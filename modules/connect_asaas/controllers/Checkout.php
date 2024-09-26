<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Checkout extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('asaas_gateway');
        $this->load->helper('general');
        $this->load->model('invoices_model');
    }

    public function index($hash)
    {
        $this->db->where('hash', $hash);

        $invoice = $this->db->get(db_prefix() . 'invoices')->row();

        $billet_only = $this->asaas_gateway->getSetting('billet_only');
        $card_only   = $this->asaas_gateway->getSetting('card_only');
        $pix_only    = $this->asaas_gateway->getSetting('pix_only');

        $metodos_pagamento = get_custom_field_value($invoice->id, 'invoice_metodo_de_pagamento', 'invoice');

        if ($metodos_pagamento) {
            $metodos_pagamento = explode(',', $metodos_pagamento);
            $metodos_pagamento = array_map(function ($item) {
                return mb_strtolower(trim($item));
            }, $metodos_pagamento);
            $billet_only = in_array('boleto', $metodos_pagamento);
            $card_only   = in_array('cartão de crédito', $metodos_pagamento);
            $pix_only    = in_array('pix', $metodos_pagamento);
        }

        $data = [
            'billet_only' => $billet_only,
            'card_only'   => $card_only,
            'pix_only'    => $pix_only,
            'invoice'     => $invoice,
            'hash'        => $hash,
            'title'       => 'Asaas'
        ];

        $this->data($data);
        $this->view('connect_asaas/payment');
        $this->layout();
    }

    public function boleto($hash)
    {
        $this->db->where('hash', $hash);
        $invoice = $this->db->get(db_prefix() . 'invoices')->row();

        $data = [];
        $data['invoice'] = $invoice;

        $charge = $this->asaas_gateway->get_charge($hash);

        if ($charge) {
            redirect($charge->bankSlipUrl);
        } else {
            $this->session->set_flashdata('error', 'Não foi possível gerar a cobrança, Fale com o financeiro');
            set_alert('warning', 'Não foi possível gerar a cobrança, Fale com o financeiro');
            redirect(site_url('invoice/' . $invoice->id . '/' . $invoice->hash), 'refresh');
        }
    }

    public function cartao($hash)
    {
        $status = 'NAOPAGO';
        $mensagem_erro = '';
        if ($this->input->post()) {
            $this->db->where('hash', $hash);
            $invoice1 = $this->db->get(db_prefix() . 'invoices')->row();
            $data = [];
            $data['invoice'] = $invoice1;


            $data['card'] = [
                'holderName'       => $this->input->post('holderName', TRUE),
                'number'           => $this->input->post('cardNumber', TRUE),
                'expiryMonth'      => $this->input->post('expirationMonth', TRUE),
                'expiryYear'       => $this->input->post('expirationYear', TRUE),
                'cvv'              => $this->input->post('securityCode', TRUE),
                'installmentCount' => $this->input->post('installmentCount', TRUE),
            ];

            $charge = $this->asaas_gateway->charge_credit_card($data);

            $content = $charge;

            $charge = json_decode($charge, TRUE);

            if (isset($charge["errors"])) {
                $mensagem_erro = $charge["errors"][0]["description"];
            } else {

                $content = json_decode($content);

                if ($content->status == "RECEIVED" || $content->status == "CONFIRMED" || $content->status == "RECEIVED_IN_CASH") {
                    $externalReference = $content->externalReference;
                    $status = $content->status;
                    $this->db->where('hash', $externalReference);
                    $invoice = $this->db->get(db_prefix() . 'invoices')->row();

                    // check_invoice_restrictions($invoice->id, $invoiceid->hash);
                    if ($invoice) {
                        //echo "LINHA 103";
                        //echo "<hr>";
                        if ($invoice->status !== "2") {
                            //echo "LINHA 106";
                            //echo "<hr>";
                            if ($status == "RECEIVED" || $status == "CONFIRMED" || $status == "RECEIVED_IN_CASH") {
                                //echo "LINHA 109";
                                //echo "<hr>";
                                $this->asaas_gateway->addPayment([
                                    'amount' => $invoice->total,
                                    'invoiceid' => $invoice->id,
                                    'paymentmode' => 'Asaas',
                                    'paymentmethod' => $content->billingType,
                                    'transactionid' => $content->id,
                                ]);

                                log_activity('Asaas: Confirmação de pagamento para a fatura ' . $invoice->id . ', com o ID: ' . $externalReference);
                                //echo 'Asaas: Confirmação de pagamento para a fatura ' . $invoice->id . ', com o ID: ' . $externalReference;

                                redirect(site_url('invoice/' . $invoice1->id . '/' . $invoice1->hash), 'refresh');
                            } else {
                                log_activity('Asaas: Estado do pagamento da fatura ' . $invoice->id . ', com o ID: ' . $externalReference . ', Status: ' . $status);
                                //echo 'Asaas: Estado do pagamento da fatura ' . $invoice->id . ', com o ID: ' . $externalReference . ', Status: ' . $status;
                            }
                        } else {
                            log_activity('Asaas: Falha ao receber callback para a fatura ' . $invoice->id . ', com o ID: ' . $externalReference . ' ');
                            //echo 'Asaas: Falha ao receber callback para a fatura ' . $invoice->id . ', com o ID: ' . $externalReference;
                        }
                    }
                }
            }
        }

        $installmentCount = $this->asaas_gateway->getSetting('installmentCount');

        $this->db->select('id, hash')->where('hash', $hash);
        $invoice1 = $this->db->get(db_prefix() . 'invoices')->row();
        $invoice =  $this->invoices_model->get($invoice1->id);

        $invoice1->total_left_to_pay = get_invoice_total_left_to_pay($invoice->id, $invoice->total);

        $data = [
            'hash' => $hash,
            'invoice' => $invoice,
            'installmentCount' => $installmentCount,
            'mensagem_erro' => $mensagem_erro,
            'title' => 'Asaas'
        ];
        $this->data($data);
        $this->view('connect_asaas/cartao');
        $this->layout();
    }

    public function qrcode($hash)
    {
        $this->db->where('hash', $hash);

        $invoice = $this->db->get(db_prefix() . 'invoices')->row();

        $invoice->total_left_to_pay = get_invoice_total_left_to_pay($invoice->id, $invoice->total);

        $data = [];

        $data['invoice'] = $invoice;

        $billet = $this->asaas_gateway->get_charge($hash);

        $data = ['invoice' => $invoice];

        if ($billet) {
            $qrcode = $this->asaas_gateway->create_qrcode($billet->id);
            $data['response'] = json_decode($qrcode);
        }
        $this->title('Asaas');
        $this->data($data);
        $this->data($data);
        $this->view('connect_asaas/qrcode');
        $this->layout();
    }

    public function error($hash)
    {
        $this->title('Connect Asaas');
        $this->disableNavigation();
        $this->disableSubMenu();
        $this->view('connect_asaas/error');
        $this->layout();
    }
}
