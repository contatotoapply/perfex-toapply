<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Callback extends ClientsController
{
    public const EVENT_ASAAS_SUBSCRIPTION_PAYMENT_CONFIRMED = 'PAYMENT_CONFIRMED';
    public const EVENT_ASAAS_SUBSCRIPTION_PAYMENT_RECEIVED  = 'PAYMENT_RECEIVED';


    public function __construct()
    {
        parent::__construct();
        $this->load->library('asaas_gateway');
        $this->load->model('connect_asaas/invoice_model');
    }

    public function getSubscriptionByAsaasSubscriptionId($asaasSubscriptionId)
    {
        $this->db->where('asaas_subscription_id', $asaasSubscriptionId);
        return $this->db->get(db_prefix() . 'subscriptions')->row();
    }

    public function index()
    {
        if ($this->input->method() == 'post') {
            $post = $this->input->post();

            $response = trim(file_get_contents("php://input"));

            $content  = json_decode($response);

            log_activity('Asaas: Recebendo callback: ' . json_encode($post));

            if (!$content) {
                echo 'Asaas: Falha ao receber callback';
                return;
            }

            $externalReference = $content->payment->externalReference;
            $status            = $content->payment->status;

            $this->db->where('hash', $externalReference);
            $invoice = $this->db->get(db_prefix() . 'invoices')->row();

            if ($invoice) {

                if ($invoice->status !== "2") {

                    if ($status == "RECEIVED" || $status == "CONFIRMED" || $status == "RECEIVED_IN_CASH") {

                        $total     = $content->payment->value;// TODO: 2024-05-04 11:55:12
                        $invoiceid = $invoice->id;

                        $this->asaas_gateway->addPayment([
                            'amount'         => $total,
                            'invoiceid'      => $invoiceid,
                            'paymentmode'    => CONNECT_ASAAS_MODULE_NAME,
                            'paymentmethod'  => $content->payment->billingType,
                            'transactionid'  => $content->payment->id,
                            'asaas_added_by' => 'CALLBACK_ASAAS_' . strtoupper($status)
                        ]);

                        $this->db->where('id', $invoiceid)->update(
                            db_prefix() . 'invoices',
                            ['total' => $total, 'subtotal' => $total]
                        );

                        if ($total > $invoice->total) {
                            $diff = $total - $invoice->total;
                            $this->db->insert(db_prefix() . 'itemable', array(
                                'rel_id' => $invoiceid, 'rel_type' => 'invoice', 'description' => 'Juros e multa',
                                'long_description' => '', 'qty' => '1.00', 'rate' => $diff, 'unit' => '', 'item_order' => '1',
                            ));
                        }

                        log_activity('Asaas: Confirmação de pagamento para a fatura ' . $invoice->id . ', com o ID: ' . $externalReference);
                        echo 'Asaas: Confirmação de pagamento para a fatura ' . $invoice->id . ', com o ID: ' . $externalReference;
                    } else {
                        log_activity('Asaas: Estado do pagamento da fatura ' . $invoice->id . ', com o ID: ' . $externalReference . ', Status: ' . $status);
                        echo 'Asaas: Estado do pagamento da fatura ' . $invoice->id . ', com o ID: ' . $externalReference . ', Status: ' . $status;
                    }
                } else {
                    log_activity('Asaas: Falha ao receber callback para a fatura ' . $invoice->id . ', com o ID: ' . $externalReference . ' ');
                    echo 'Asaas: Falha ao receber callback para a fatura ' . $invoice->id . ', com o ID: ' . $externalReference;
                }
            }

            if (isset($content->event)) {

                switch ($content->event) {

                    case self::EVENT_ASAAS_SUBSCRIPTION_PAYMENT_CONFIRMED:

                        log_activity('Entrou no webhoook (1)');

                        $payment            = $content->payment;
                        $date               = $payment->dateCreated;
                        $transactionId      = $payment->id;
                        $costumer           = $payment->customer;
                        $subscriptionId     = isset($payment->subscription) ? $payment->subscription : null;
                        $dueDate            = $payment->dueDate;
                        $clientPaymentDate  = $payment->clientPaymentDate;
                        $total              = $payment->value;
                        $sub                = $this->getSubscriptionByAsaasSubscriptionId($subscriptionId);

                        if (!$subscriptionId) {
                            log_activity('Asaas: Assinatura não encontrada (1). [' . $transactionId . ']');
                        } else {

                            if (!$sub) {
                                log_activity('Asaas: Assinatura não encontrada (2). [' . $transactionId . ']');
                                return;
                            }

                            // Atualizar a inscrição
                            log_activity('A data é esta: ' . $clientPaymentDate . ' ' . date("H:i:s"));

                            $select                         = 'userid,billing_street,billing_city,billing_state,billing_zip,billing_country';
                            $client                         = $this->db->select($select)->where('asaas_customer_id', $costumer)
                                ->get(db_prefix() . 'clients')->row();

                            $invoiceExist = $this->db->select('id')
                                ->where('clientid', $client->userid)
                                ->where('subscription_id', $sub->id)
                                ->where('date', date('Y-m-d'))
                                ->where('asaas_added_by', 'CALLBACK_ASAAS_PGTO_CONFIRMADO_LINHA_109')
                                ->get(db_prefix() . 'invoices')
                                ->row();

                            if (is_null($invoiceExist)) {

                                $this->db->where('id', $sub->id)->update(
                                    db_prefix() . 'subscriptions',
                                    [
                                        'date'               => $date,
                                        'next_billing_cycle' => strtotime('+1 month', strtotime($dueDate . ' ' . date('H:i:s'))),
                                        'date_subscribed'    => $clientPaymentDate . ' ' . date("H:i:s"),
                                        'status'             => 'active'
                                    ]
                                );


                                $invoice_data = [];

                                $invoice_data['total']           = $total;
                                $invoice_data['subtotal']        = $total;
                                $invoice_data['status']          = 2;
                                $invoice_data['duedate']         = $dueDate;
                                $invoice_data['clientid']        = $client->userid;
                                $invoice_data['billing_street']  = $client->billing_street;
                                $invoice_data['billing_city']    = $client->billing_city;
                                $invoice_data['billing_state']   = $client->billing_state;
                                $invoice_data['billing_zip']     = $client->billing_zip;
                                $invoice_data['billing_country'] = $client->billing_country;
                                $invoice_data['number']          = get_option('next_invoice_number');
                                $invoice_data['date']            = $date;
                                $invoice_data['currency']        = 1;
                                $invoice_data['subscription_id'] = $sub->id;
                                $invoice_data['asaas_added_by']  = 'CALLBACK_ASAAS_PGTO_CONFIRMADO_LINHA_142';

                                $invoiceid                       = $this->invoice_model->add($invoice_data);

                                $this->db->where('id', $invoiceid)->update(
                                    db_prefix() . 'invoices',
                                    [
                                        'status'    => 2,
                                        'addedfrom' => $sub->created_from
                                    ]
                                );

                                $this->asaas_gateway->addPayment([
                                    'amount'         => $total,
                                    'invoiceid'      => $invoiceid,
                                    'paymentmode'    => CONNECT_ASAAS_MODULE_NAME,
                                    'paymentmethod'  => $payment->billingType,
                                    'transactionid'  => $payment->id,
                                    'date'           => $clientPaymentDate,
                                    'note'           => 'Asaas: Pagamento recebido para a fatura ' . $invoiceid . ' | Hash: ' . $externalReference,
                                    'asaas_added_by' => 'ASS_ASAAS_WEBHOOK_' . self::EVENT_ASAAS_SUBSCRIPTION_PAYMENT_CONFIRMED
                                ]);

                                echo "OK";
                            }
                        }

                        break;
                    case self::EVENT_ASAAS_SUBSCRIPTION_PAYMENT_RECEIVED:

                        log_activity('[ASAAS] - Entrou no webhoook (2): ' . json_encode($content));

                        $payment           = $content->payment;
                        $date              = $payment->dateCreated;
                        $transactionId     = $payment->id;
                        $costumer          = $payment->customer;
                        $subscriptionId    = isset($payment->subscription) ? $payment->subscription : null;
                        $dueDate           = $payment->dueDate;
                        $clientPaymentDate = $payment->clientPaymentDate;
                        $total             = $payment->value;

                        // Se for assinatura:
                        if (isset($payment->subscription)) {

                            $sub = $this->getSubscriptionByAsaasSubscriptionId($subscriptionId);

                            if (!$sub) {
                                log_activity('Asaas: Falha ao receber callback para a inscrição ' . $transactionId);
                                return;
                            }

                            log_activity('A data é esta (1):' . $clientPaymentDate . ' ' . date("H:i:s"));

                            // Atualizar a inscrição
                            $this->db->where('id', $sub->id)->update(
                                db_prefix() . 'subscriptions',
                                [
                                    'date'               => $date,
                                    'next_billing_cycle' => strtotime('+1 month', strtotime($dueDate . ' ' . date('H:i:s'))),
                                    'date_subscribed'    => $clientPaymentDate . ' ' . date("H:i:s"),
                                    'status'             => 'active'
                                ]
                            );

                            $select                         = 'userid,billing_street,billing_city,billing_state,billing_zip,billing_country';

                            $client                         = $this->db->select($select)
                                ->where('asaas_customer_id', $costumer)
                                ->get(db_prefix() . 'clients')->row();

                            $invoice_data = [];

                            $invoice_data['total']           = $total;
                            $invoice_data['subtotal']        = $total;
                            $invoice_data['status']          = 2;
                            $invoice_data['duedate']         = $dueDate;
                            $invoice_data['clientid']        = $client->userid;
                            $invoice_data['billing_street']  = $client->billing_street;
                            $invoice_data['billing_city']    = $client->billing_city;
                            $invoice_data['billing_state']   = $client->billing_state;
                            $invoice_data['billing_zip']     = $client->billing_zip;
                            $invoice_data['billing_country'] = $client->billing_country;
                            $invoice_data['number']          = get_option('next_invoice_number');
                            $invoice_data['date']            = $date;
                            $invoice_data['currency']        = 1;
                            $invoice_data['subscription_id'] = $sub->id;
                            $invoice_data['asaas_added_by']  = 'CALLBACK_ASAAS_PGTO_CONFIRMADO_LINHA_228';

                            $invoiceid                       = $this->invoice_model->add($invoice_data);

                            $this->db->where('id', $invoiceid)->update(
                                db_prefix() . 'invoices',
                                ['status' => 2, 'addedfrom' => 'ASAAS_PGTO_RECEBIDO_1']
                            );

                            $rowExists = $this->db->select('*')
                                ->where('transactionId', $payment->id)->get(db_prefix() . 'invoicepaymentrecords')->row();

                            if (is_null($rowExists)) {
                                $this->asaas_gateway->addPayment([
                                    'amount'         => $total,
                                    'invoiceid'      => $invoiceid,
                                    'paymentmode'    => CONNECT_ASAAS_MODULE_NAME, // TODO: verificar se é número ou string
                                    'paymentmethod'  => $payment->billingType,
                                    'transactionid'  => $payment->id,
                                    'date'           => $clientPaymentDate,
                                    'note'           => 'Asaas: Pagamento recebido para a fatura ' . $invoiceid . ' | Hash: ' . $externalReference,
                                    'asaas_added_by' => 'ASS_ASAAS_WEBHOOK_' . self::EVENT_ASAAS_SUBSCRIPTION_PAYMENT_RECEIVED
                                ]);
                            }
                        } else {

                            $this->db->select('id')->where('hash', $externalReference);

                            $invoice = $this->db->get(db_prefix() . 'invoices')->row();

                            if (!$invoice) {
                                log_activity('Asaas: Falha ao receber callback para a inscrição. Fatura não encontrada. ' . $payment->id);
                                return;
                            }

                            $rowExists = $this->db->select('count(transactionId) as total')
                                ->where('transactionId', $payment->id)->get(db_prefix() . 'invoicepaymentrecords')->row();

                            if (is_null($rowExists)) {
                                $this->asaas_gateway->addPayment([
                                    'amount'         => $total,
                                    'invoiceid'      => $invoice->id,
                                    'paymentmode'    => 4,
                                    'paymentmethod'  => $payment->billingType,
                                    'transactionid'  => $payment->id,
                                    'date'           => $clientPaymentDate,
                                    'note'           => 'Asaas: Pagamento recebido para a fatura ' . $invoice->id . ' | Hash: ' . $externalReference,
                                    'asaas_added_by' => 'FAT_ASAAS_WEBHOOK_' . self::EVENT_ASAAS_SUBSCRIPTION_PAYMENT_RECEIVED
                                ]);

                                echo 'Asaas: Pagamento recebido para a fatura ' . $invoice->id . ', Hash: ' . $externalReference;

                                log_activity('Asaas: Pagamento recebido para a fatura ' . $invoice->id . ', Hash: ' . $externalReference);
                            }
                        }

                        break;
                    default:
                        log_activity('Outro tipo de evento vindo do Asaas: ' . json_encode($content));
                        break;
                }
            }
        }

        echo 'OK: GET';
    }
}
