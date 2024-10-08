<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(__DIR__ . '/../vendor/autoload.php');

use ctodobom\APInterPHP\BancoInter;
use ctodobom\APInterPHP\TokenRequest;

class Webhook extends ClientsController
{
    protected $gt_inter;
    protected $banco;

    const SITUACAO_PAGO      = 'PAGO';
    const SITUACAO_ABERTO    = 'ABERTO';
    const SITUACAO_VENCIDO   = 'VENCIDO';
    const SITUACAO_CANCELADO = 'CANCELADO';

    public function __construct()
    {
        parent::__construct();
        $this->load->library("banco_inter/gateways/inter_gateway");
        $this->gt_inter = $this->inter_gateway;
        $this->load->helper('banco_inter');
        $this->load->library(['inter_library', 'enviar_email_recorrente', 'pagamento_atrasado_library']);
        $this->load->model(['payments_model', 'invoices_model']);
        $this->load->library('conta_azul/api_conta_azul_library');
    }

    public function index()
    {
        log_activity('[BANCO INTER V3] - Entrou no webhook em ' . date('d/m/Y H:i:s'));

        try {

            $hash      = get_option('connect_inter_ssl_file_hash');
            $cert_file = CONNECT_INTER_MODULE_NAME_UPLOADS_FOLDER . "ssl_files/crt_{$hash}.crt";
            $key_file  = CONNECT_INTER_MODULE_NAME_UPLOADS_FOLDER . "ssl_files/key_{$hash}.key";

            if (!file_exists($key_file) || !file_exists($cert_file)) {
                return;
            }
            $this->banco = new BancoInter(
                "123456",
                $cert_file,
                $key_file,
                new TokenRequest(
                    $this->gt_inter->getSetting('inter_client_id'),
                    $this->gt_inter->getSetting('inter_client_secret'),
                    'boleto-cobranca.read boleto-cobranca.write'
                )
            );
        } catch (\Throwable $th) {
            log_activity('error web hook 1: ' . $th->getMessage());
        }

        try {

            $Post_Recebe = json_decode(file_get_contents('php://input'));

            log_activity('Posta Usando Form: ' . json_encode($_REQUEST));

            if (is_array($Post_Recebe) && count($Post_Recebe) > 0) {
                $Post_Recebe = (object) $Post_Recebe[0];
            }

            log_activity('Post_Recebe: ' . json_encode($Post_Recebe));

            $situacao = $Post_Recebe->situacao;

            switch ($situacao) {
                case self::SITUACAO_PAGO:
                    $this->pagar($Post_Recebe);
                    break;
                case self::SITUACAO_ABERTO:
                    break;
                case self::SITUACAO_VENCIDO:
                    break;
                case self::SITUACAO_CANCELADO:
                    $this->cancelar($Post_Recebe);
                    break;
                default:
                    break;
            }
        } catch (\Throwable $th) {
            log_activity('error webhook: ' . $th->getMessage());
        }
    }

    protected function cancelar($post_received)
    {
        log_activity('Entrou em cancelar: ' . $post_received->nossoNumero);

        $invoice = $this
            ->db
            ->select('id, bi_nosso_numero')
            ->where('bi_nosso_numero', $post_received->nossoNumero)
            ->get(db_prefix() . 'invoices')->row();

        if (!is_null($invoice)) {
            $invoice_id = $invoice->id;
            $this->db->where('id', $invoice_id);
            $this->db->update(db_prefix() . 'invoices', [
                'status' => 5 // Cancelado
            ]);
        }
    }

    protected function pagar($post_received)
    {
        log_activity('[BANCO INTER V3] - Entrou em pagar em ' . date('d/m/Y H:i:s'));

        $invoice = $this
            ->db
            ->select('id, bi_nosso_numero, total, banco_inter_item_adicionado')
            ->where('bi_nosso_numero', $post_received->nossoNumero)
            ->get(db_prefix() . 'invoices')->row();

        log_activity('Invoice: ' . json_encode(['invoice' => $invoice, 'post' => $post_received]));

        if (!is_null($invoice)) {
            $valorTotalRecebimento = $post_received->valorTotalRecebimento;
            $invoice_id = $invoice->id;

            $this->db->where('id', $invoice_id);
            $this->db->update(db_prefix() . 'invoices', [
                'status'   => 2,
                'total'    => $valorTotalRecebimento,
                'subtotal' => $valorTotalRecebimento
            ]);

            if ($invoice->total < $valorTotalRecebimento && !$invoice->banco_inter_item_adicionado) {

                $juros_multas =  $valorTotalRecebimento - $invoice->total;

                try {
                    // Dá baixa automaticamente no Conta Azul
                    $this->api_conta_azul_library->installments($invoice_id, $juros_multas, $valorTotalRecebimento);
                } catch (\Throwable $th) {
                }

                $this->adicionar_itens_juros_multas($invoice_id, $juros_multas);
            }

            $data = [
                'invoiceid'       => $invoice_id,
                'amount'          => $valorTotalRecebimento,
                'date'            => date('d/m/Y', strtotime($post_received->dataHoraSituacao)),
                'paymentmode'     => 'banco_inter',
                'paymentmethod'   => 'Módulo Banco Inter',
                'transactionid'   => $post_received->nossoNumero,
                'note'            => 'Baixa dada via webhook do banco Inter em ' . date('d/m/Y H:i:s'),
            ];


            if ($valorTotalRecebimento) {
                $this->db->where('id', $invoice_id)
                    ->update(db_prefix() . 'invoices', [
                        'total'    => $valorTotalRecebimento,
                        'subtotal' => $valorTotalRecebimento
                    ]);
            }

            $payment = $this->db->where('invoiceid', $invoice_id)
                ->where('transactionid', $post_received->nossoNumero)
                ->get(db_prefix() . 'invoicepaymentrecords')
                ->row();

            if (!$payment) {

                try {
                    if ($this->app_modules->is_active(CONTA_AZUL_MODULE_NAME)) {
                        if ($juros_multas > 0) {
                            // Pega o item "Multa/Juros por Atraso"
                            $this->db->select("id,description,long_description, 1 as qty, $juros_multas as rate");
                            $this->db->like('description', 'Multa');
                            $this->db->like('description', 'Juros');
                            $this->db->like('description', 'atraso');
                            $query = $this->db->get(db_prefix() . 'items');
                            $items = $query->result_array(); // É necessário que este item seja do tipo array
                            $this->api_conta_azul_library->updateSale($invoice_id, $items);
                        }
                    }
                } catch (\Throwable $th) {
                }

                try {
                    $this->payments_model_add($data);
                } catch (\Throwable $th) {
                }
            }

            if ($valorTotalRecebimento) {

                $this->db->where('id', $invoice_id)
                    ->update(db_prefix() . 'invoices', [
                        'total'    => $valorTotalRecebimento,
                        'subtotal' => $valorTotalRecebimento
                    ]);

                if ($this->db->affected_rows()) {
                    log_activity('[Webhook] Atualizado valor total da fatura [' . format_invoice_number($invoice_id) . ']');
                }
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

                // $activity_lang_key = 'invoice_activity_subscription_payment_succeeded';
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

        if ($this->db->affected_rows()) {
            log_activity('[Webhook] Adicionado itens de juros e multas à fatura [' . format_invoice_number($invoice_id) . ']');
        }
    }
}
