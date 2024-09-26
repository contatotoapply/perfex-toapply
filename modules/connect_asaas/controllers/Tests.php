<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tests extends ClientsController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        $this->load->library('connect_asaas/asaas_gateway');
        $this->load->library('connect_asaas/base_api');

        $update_payment_invoice_asaas = $this->asaas_gateway->getSetting('update_payment_invoice_asaas');
        if ($update_payment_invoice_asaas) {
            $body_params = ['paymentDate' => date('Y-m-d'), 'value' => 91, 'notifyCustomer' => true];
            $response = $this->asaas_gateway->receive_in_cash(5100745, $body_params);
            dd(1, isset($response['status']) && $response['status'] == 'RECEIVED_IN_CASH');
        }
    }

    public function testar_opcoes_nfse_cliente()
    {
        $clientid = 5;

        dd(1, is_emissao_avulsa($clientid), is_criacao_fatura($clientid), is_confirmacao_pagamento($clientid));
    }

    public function testar_nfse_criacao_fatura()
    {
        $clientid = 5;

        $isCriacaoFatura = is_criacao_fatura($clientid);
        $asaasInvoiceOnEvent = get_option('asaas_invoice_on_event');
        $isCriarNotaFiscal = false;

        if (is_emissao_empty($clientid)) {
            $isCriarNotaFiscal = $asaasInvoiceOnEvent == 1;
        } else {
            if ($asaasInvoiceOnEvent == 1) {
                $isCriarNotaFiscal = $isCriacaoFatura && !is_emissao_avulsa($clientid) && !is_confirmacao_pagamento($clientid);
            } else {
                $isCriarNotaFiscal = $isCriacaoFatura;
            }
        }

        dd(1, $isCriarNotaFiscal);
    }

    public function testar_nfse_no_confirmacao_pagamento()
    {
        $clientid = 5;

        $isCriacaoFatura = is_confirmacao_pagamento($clientid);
        $asaasInvoiceOnEvent = get_option('asaas_invoice_on_event');
        $isCriarNotaFiscal = false;

        if (is_emissao_empty($clientid)) {
            $isCriarNotaFiscal = $asaasInvoiceOnEvent == 2;
        } else {
            if ($asaasInvoiceOnEvent == 2) {
                $isCriarNotaFiscal = $isCriacaoFatura && !is_emissao_avulsa($clientid) && !is_criacao_fatura($clientid);
            } else {
                $isCriarNotaFiscal = $isCriacaoFatura;
            }
        }

        dd(1, $isCriarNotaFiscal);
    }

    public function testar_nfse_is_emissao_empty()
    {
        $clientid = 5;
        dd(1, is_emissao_empty($clientid));
    }

    public function test_get_customers()
    {
        // load library Invoice
        $this->load->library('connect_asaas/invoice');
        dd(1, $this->invoice->get_customers());
    }

    function asaas_after_invoice_added($invoice_id)
    {
        if (get_option('paymentmethod_connect_asaas_billet_only')) {

            $CI = &get_instance();
            $CI->load->library('asaas_gateway');
            $CI->load->model('invoices_model');
            $invoice = $CI->invoices_model->get($invoice_id);

            if ($invoice) {

                if ($invoice->duedate) {

                    $allowed_payment_modes = unserialize($invoice->allowed_payment_modes);

                    if (in_array(CONNECT_ASAAS_MODULE_NAME, $allowed_payment_modes)) {

                        // Remover este comentário
                        // $billet = $CI->asaas_gateway->charge_billet($invoice);
                        // if (isset($billet['id'])) {
                        //     $data = array(
                        //         'asaas_cobranca_id' => $billet['id'],
                        //     );
                        //     $CI->db->where('id', $invoice_id);
                        //     $CI->db->update(db_prefix() . 'invoices', $data);
                        //     $CI->invoices_model->log_invoice_activity($invoice->id, 'Cobrança adicionada com sucesso ao Asaas. Asaas ID: ' . $billet['id']);
                        // }

                        $CI->load->library('connect_asaas/invoice');
                        $CI->load->library('connect_asaas/base_api');
                        $CI->invoices_model->log_invoice_activity($invoice->id, 'Início da criação da nota fiscal');
                        // Início Criação da Nota Fiscal
                        $clientid                           = $invoice->clientid;

                        $asaas_invoice_municipalServiceCode = get_option('asaas_invoice_municipalServiceCode');

                        $asaas_invoice_ir                   = get_option('asaas_invoice_ir');

                        $asaas_invoice_inss                 = get_option('asaas_invoice_inss');

                        $asaas_invoice_csll                 = get_option('asaas_invoice_csll');

                        $asaas_invoice_cofins               = get_option('asaas_invoice_cofins');

                        $asaas_invoice_iss                  = get_option('asaas_invoice_iss');

                        $api_key                            = $CI->base_api->getApiKey();

                        $api_url                            = $CI->base_api->getUrlBase();


                        // Se is_criacao_fatura for falso, então, deve verificar o status global de asaas_invoice_on_event
                        $isCriacaoFatura     = is_criacao_fatura($clientid);
                        $asaasInvoiceOnEvent = get_option('asaas_invoice_on_event');
                        $isCriarNotaFiscal   = false;

                        if (is_emissao_empty($clientid)) {
                            $isCriarNotaFiscal = $asaasInvoiceOnEvent == 1;
                        } else {
                            if ($asaasInvoiceOnEvent == 1) {
                                $isCriarNotaFiscal = $isCriacaoFatura && !is_emissao_avulsa($clientid) && !is_confirmacao_pagamento($clientid);
                            } else {
                                $isCriarNotaFiscal = $isCriacaoFatura;
                            }
                        }

                        $CI->invoices_model->log_invoice_activity($invoice->id, 'Pode criar NFSe na criação:' . $isCriarNotaFiscal);

                        if ($isCriarNotaFiscal) {

                            $client = $CI->clients_model->get($clientid);

                            $description    = $CI->asaas_gateway->getSetting('description');

                            $invoice_number = $invoice->prefix . str_pad($invoice->number, 6, "0", STR_PAD_LEFT);

                            $description    = str_replace("{invoice_number}", $invoice_number, $description);

                            $document       = str_replace('/', '', str_replace('-', '', str_replace('.', '', $client->vat)));


                            $email = get_custom_field_value($client->userid, 'customers_email_principal', 'customers') ??
                                get_custom_field_value($client->userid, 'customers_e_mail_financeiro', 'customers');

                            $addressNumber = get_custom_field_value($client->userid, 'customers_numero', 'customers') ??
                                get_custom_field_value($client->userid, 'customers_numero_endereco', 'customers');

                            $document       = str_replace('/', '', str_replace('-', '', str_replace('.', '', $client->vat)));

                            $postalCode     = str_replace('-', '', str_replace('.', '', $client->zip));

                            $customer       = $CI->invoice->search_cliente($document);

                            if ($customer['totalCount'] == "0") {

                                $post_data = json_encode([
                                    "name"                 => $client->company,
                                    "email"                => $email,
                                    "cpfCnpj"              => $document,
                                    "postalCode"           => $postalCode,
                                    "address"              => $client->address,
                                    "addressNumber"        => $addressNumber,
                                    "complement"           => "",
                                    "phone"                => $client->phonenumber,
                                    "mobilePhone"          => $client->phonenumber,
                                    "externalReference"    => $client->userid,
                                    "notificationDisabled" => false,
                                ]);

                                $cliente_create = $CI->asaas_gateway->create_customer($api_url, $api_key, $post_data);
                                $cliente_id = $cliente_create['id'];
                                log_activity('Cliente cadastrado no Asaas [Cliente ID: ' . $cliente_id . ']');
                            } else {
                                // se existir recupera os dados para cobranca
                                $cliente_id = $customer['data'][0]['id'];
                            }

                            $municipal_service_default = json_decode(get_option('municipal_service_default')) ?? '';

                            $parts                      = explode(' - ', $municipal_service_default->service_name, 2);
                            $municipalServiceCode       = preg_replace("/\D+/", "", trim($parts[0])); // TODO
                            $municipalServiceName       = trim($parts[1]);

                            $pipePosition = strpos($municipalServiceCode, '|');
                            if ($pipePosition !== false) {
                                $municipalServiceCode = trim(substr($municipalServiceCode, 0, $pipePosition));
                            } else {
                                $municipalServiceCode = trim($municipalServiceCode);
                            }

                            $post_data = json_encode([
                                "customer"           => $cliente_id,
                                "serviceDescription" => $description,
                                "value"              => $invoice->total,
                                "effectiveDate"      => date('Y-m-d'),
                                "externalReference"  => $invoice->hash,
                                "taxes"              => [
                                    "retainIss" => null,
                                    "iss"       => $asaas_invoice_iss,
                                    "cofins"    => $asaas_invoice_cofins,
                                    "csll"      => $asaas_invoice_csll,
                                    "inss"      => $asaas_invoice_inss,
                                    "ir"        => $asaas_invoice_ir,
                                    "pis"       => null
                                ],
                                "municipalServiceCode" => $municipalServiceCode,
                                "municipalServiceName" => $municipalServiceName
                            ]);

                            $CI->invoices_model->log_invoice_activity($invoice->id, '[Asaas - NFSe] - Corpo da requisição para criação:' . $post_data);

                            $create_invoice = $CI->invoice->create_invoice($post_data);

                            $CI->invoices_model->log_invoice_activity($invoice->id, '[Asaas - NFSe] - NF criada com sucesso:' . json_encode($create_invoice));

                            $create_invoice = json_decode($create_invoice, true);
                        }

                        // Fim
                    }
                }
            }
            return $invoice_id;
        }
    }

    public function test_delete_invoice()
    {
        $insert_id = 20;
        $tags      = get_tags_in($insert_id, 'invoice');
        $prefix    = get_option('invoice_prefix');
        $tags[]    = $prefix . 'MÃE';
        handle_tags_save($tags, $insert_id, 'invoice');
    }

    public function test_pesquisar_cliente_asaas()
    {
        $this->load->library("connect_asaas/customer");

        $client = $this->clients_model->get(5);

        $document      = soNumeros($client->vat);

        $email         = get_custom_field_value($client->userid, get_option('asaas_campo_personalisado_email_padrao'), 'customers');

        $addressNumber = get_custom_field_value($client->userid,  get_option('asaas_campo_personalisado_numero_endereco_padrao'), 'customers');

        $bairro        = get_custom_field_value($client->userid, get_option('asaas_campo_personalisado_bairro_padrao'), 'customers');

        $userid        = $client->userid;

        $search_customer = $this->customer->search_customer('47011485805');

        if (!$search_customer) {

            $post_data = [
                "name"                 => $client->company,
                "company"              => $client->company,
                "cpfCnpj"              => $document,
                "email"                => $email,
                "phone"                => $client->phonenumber,
                "postalCode"           => $client->zip,
                "address"              => $client->address,
                "addressNumber"        => $addressNumber,
                "complement"           => "",
                "province"             => $bairro,
                "mobilePhone"          => $client->phonenumber,
                "externalReference"    => $userid,
                "notificationDisabled" => false,
            ];

            $cliente_create = $this->customer->create($post_data);
            dd(1, $cliente_create);
            if (isset($cliente_create['id'])) {
                $cliente_id = $cliente_create['id'];
                log_activity('[ASAAS] - Cliente cadastrado no Asaas [Cliente ID: ' . $cliente_id . ']');
            }
        }

    }
}
