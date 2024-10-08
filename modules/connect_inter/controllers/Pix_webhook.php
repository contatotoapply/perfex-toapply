<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pix_webhook extends ClientsController
{
    public $db;
    public $invoices_model;
    public $clients_model;
    public $payments_model;
    public $app_sms;

    public function __construct()
    {
        parent::__construct();
        $this->load->library("connect_inter/gateways/inter_gateway");
        $this->load->library('conta_azul/api_conta_azul_library');
    }

    public function index()
    {
        $request_body = json_decode(file_get_contents('php://input'), true);
        $request_body = $request_body;

        log_activity("Webhook fez a request do pix. Corpo da Requisição: " . json_encode($request_body));

        if (isset($request_body['pix'])) {
            $data = $request_body['pix'][0];
            $this->pagar($data);
        }
    }

    protected function pagar($payload)
    {
        $invoice = $this
            ->db
            ->select('id, bi_nosso_numero, total, banco_inter_item_adicionado')
            ->where('hash', $payload['txid'])
            ->where('banco_inter_item_adicionado', 0)
            ->get(db_prefix() . 'invoices')->row();

        if (!is_null($invoice)) {

            $invoice_id = $invoice->id;

            $this->db->where('id', $invoice_id);
            $this->db->update(db_prefix() . 'invoices', [
                'total'    => $payload['valor'],
                'subtotal' => $payload['valor'],
                'status'   => 2
            ]);

            if ($this->inter_gateway->getSetting('debug')) {
                var_dump($invoice->total);
                var_dump($payload['valor']);
            }

            if ($invoice->total < $payload['valor'] && !$invoice->banco_inter_item_adicionado) {
                $juros_multas =  $payload['valor'] - $invoice->total;
                try {
                    // Dá baixa automaticamente no Conta Azul
                    $this->api_conta_azul_library->installments($invoice_id, $juros_multas, $invoice->total);
                } catch (\Throwable $th) {
                }
                $this->adicionar_itens_juros_multas($invoice_id, $juros_multas);
            }

            $data = [
                'invoiceid'       => $invoice_id,
                'amount'          => $payload['valor'],
                'date'            => date('d/m/Y', strtotime($payload['horario'])),
                'paymentmode'     => 'banco_inter',
                'paymentmethod'   => 'Módulo Banco Inter PIX',
                'transactionid'   => $payload['endToEndId'],
                'note'            => 'Baixa dada via webhook do Banco Inter em ' . date('d/m/Y H:i:s'),
            ];

            $payment = $this->db->where('invoiceid', $invoice_id)
                ->where('transactionid', $payload['endToEndId'])
                ->get(db_prefix() . 'invoicepaymentrecords')
                ->row();

            if (!$payment) {
                $this->payments_model_add($data);
            }

            if ($payload['valor']) {
                $this->db->where('id', $invoice_id)
                    ->update(db_prefix() . 'invoices', [
                        'total'    => $payload['valor'],
                        'subtotal' => $payload['valor']
                    ]);
            }

            return true;
        }
    }

    private function payments_model_add($data)
    {

        $data['date'] = to_sql_date($data['date']);

        $data['daterecorded'] = date('Y-m-d H:i:s');

        $data                 = hooks()->apply_filters('before_payment_recorded', $data);

        $this->db->insert(db_prefix() . 'invoicepaymentrecords', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            $invoice      = $this->invoices_model->get($data['invoiceid']);
            $force_update = false;

            if (!class_exists('Invoices_model', false)) {
                $this->load->model('invoices_model');
            }

            if ($invoice->status == Invoices_model::STATUS_DRAFT) {
                $force_update = true;
                $this->invoices_model->change_invoice_number_when_status_draft($invoice->id);
            }

            update_invoice_status($data['invoiceid'], $force_update);

            $activity_lang_key = 'invoice_activity_payment_made_by_staff';
            if (!is_staff_logged_in()) {
                $activity_lang_key = 'invoice_activity_payment_made_by_client';
            }

            $this->invoices_model->log_invoice_activity($data['invoiceid'], $activity_lang_key, !is_staff_logged_in() ? true : false, serialize([
                app_format_money($data['amount'], $invoice->currency_name),
                '<a href="' . admin_url('payments/payment/' . $insert_id) . '" target="_blank">#' . $insert_id . '</a>',
            ]));

            log_activity('Payment Recorded [ID:' . $insert_id . ', Invoice Number: ' . format_invoice_number($invoice->id) . ', Total: ' . app_format_money($data['amount'], $invoice->currency_name) . ']');

            // Send email to the client that the payment is recorded
            $payment               = $this->payments_model->get($insert_id);
            $payment->invoice_data = $this->invoices_model->get($payment->invoiceid);
            set_mailing_constant();
            $paymentpdf           = payment_pdf($payment);
            $payment_pdf_filename = mb_strtoupper(slug_it(_l('payment') . '-' . $payment->paymentid), 'UTF-8') . '.pdf';
            $attach               = $paymentpdf->Output($payment_pdf_filename, 'S');


            // emails
            $template_name        = 'invoice_payment_recorded_to_customer';
            $pdfInvoiceAttachment = false;
            $attachPaymentReceipt = true;
            $emails_sent          = [];

            $where = ['active' => 1, 'invoice_emails' => 1];

            if (get_option('attach_invoice_to_payment_receipt_email') == 1) {
                $invoice_number = format_invoice_number($payment->invoiceid);
                set_mailing_constant();
                $pdfInvoice           = invoice_pdf($payment->invoice_data);
                $pdfInvoiceAttachment = $pdfInvoice->Output($invoice_number . '.pdf', 'S');
            }

            $contacts = $this->clients_model->get_contacts($invoice->clientid, $where);

            foreach ($contacts as $contact) {
                $template = mail_template(
                    $template_name,
                    $contact,
                    $invoice,
                    null,
                    $payment->paymentid
                );

                if ($attachPaymentReceipt) {
                    $template->add_attachment([
                        'attachment' => $attach,
                        'filename'   => $payment_pdf_filename,
                        'type'       => 'application/pdf',
                    ]);
                }

                if ($pdfInvoiceAttachment) {
                    $template->add_attachment([
                        'attachment' => $pdfInvoiceAttachment,
                        'filename'   => str_replace('/', '-', $invoice_number) . '.pdf',
                        'type'       => 'application/pdf',
                    ]);
                }
                $merge_fields = $template->get_merge_fields();

                if ($template->send()) {
                    array_push($emails_sent, $contact['email']);
                }

                $this->app_sms->trigger(SMS_TRIGGER_PAYMENT_RECORDED, $contact['phonenumber'], $merge_fields);
            }

            if (count($emails_sent) > 0) {

                $additional_activity_data = serialize([
                    implode(', ', $emails_sent),
                ]);

                $activity_lang_key = 'invoice_activity_record_payment_email_to_customer';

                $this->invoices_model->log_invoice_activity($invoice->id, $activity_lang_key, false, $additional_activity_data);
            }


            $this->db->where('staffid', $invoice->addedfrom);
            $this->db->or_where('staffid', $invoice->sale_agent);

            $staff_invoice = $this->db->get(db_prefix() . 'staff')->result_array();

            $notifiedUsers = [];

            foreach ($staff_invoice as $member) {
                if (get_option('notification_when_customer_pay_invoice') == 1) {
                    if (is_staff_logged_in() && $member['staffid'] == get_staff_user_id()) {
                        continue;
                    }
                    // E.q. had permissions create not don't have, so we must re-check this
                    if (user_can_view_invoice($invoice->id, $member['staffid'])) {
                        $notified = add_notification([
                            'fromcompany'     => true,
                            'touserid'        => $member['staffid'],
                            'description'     => 'not_invoice_payment_recorded',
                            'link'            => 'invoices/list_invoices/' . $invoice->id,
                            'additional_data' => serialize([
                                format_invoice_number($invoice->id),
                            ]),
                        ]);
                        if ($notified) {
                            array_push($notifiedUsers, $member['staffid']);
                        }
                        send_mail_template(
                            'invoice_payment_recorded_to_staff',
                            $member['email'],
                            $member['staffid'],
                            $invoice,
                            $attach,
                            $payment->id
                        );
                    }
                }
            }

            pusher_trigger_notification($notifiedUsers);

            hooks()->do_action('after_payment_added', $insert_id);

            return $insert_id;
        }

        return false;
    }

    public function create()
    {
        $this->load->view("banco_inter/pix");
    }

    public function adicionar_itens_juros_multas($invoice_id, $valor)
    {
        $newitems = [
            [
                'order'            => 2,
                'description'      => 'Multa/Juros por Atraso',
                'long_description' => '2% de multa legal por atraso/pagamento após o vencimento, e juros de mora de 1% ao mês',
                'qty'              => 1,
                'unit'             => null,
                'rate'             => $valor,
            ]
        ];

        foreach ($newitems as $item) {
            if ($new_item_added = add_new_sales_item_post($item, $invoice_id, 'invoice')) {
                _maybe_insert_post_item_tax($new_item_added, $item, $invoice_id, 'invoice');
            }
        }

        $this->db->where('id', $invoice_id)
            ->update(db_prefix() . 'invoices', [
                'banco_inter_item_adicionado' => 1
            ]);
    }
}
