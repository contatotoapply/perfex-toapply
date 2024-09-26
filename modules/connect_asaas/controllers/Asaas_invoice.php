<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Asaas_invoice extends AdminController
{
    protected  $api_key;
    protected  $api_url;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('connect_asaas/asaas_gateway');
        $this->load->model('clients_model');
        $this->load->model('invoices_model');
        $this->load->model('invoice_items_model');
        $this->load->library('connect_asaas/base_api');
        $this->load->library('connect_asaas/invoice');
        $this->load->library('connect_asaas/customer');
        $this->api_url = $this->base_api->getUrlBase();
        $this->api_key = $this->base_api->getApiKey();
    }

    public function index()
    {
        $offset = $this->input->get('offset');

        $limit  = $this->input->get('limit');

        $params = ['offset' => $offset, 'limit' => $limit];

        $response = $this->invoice->get_invoices($params);

        if (!empty($response['data'])) {
            foreach ($response['data'] as $key => $nota_fiscal) {
                $response['data'][$key]['client'] = $this->customer->get_by_id($nota_fiscal['customer']);
            }
        }

        $data = [
            'title'    => 'Notas Fiscais',
            'params'   => $params,
            'response' => $response
        ];

        $this->load->view('connect_asaas/invoice/invoices', $data);
    }

    public function invoices($id)
    {
        $invoice = $this->invoice->get_invoice($id);
        $customer = $this->customer->get_by_id($invoice->customer);

        $client = $this->db->where('asaas_customer_id', $customer->id)
            ->get(db_prefix() . 'clients')->row();

        $data = [
            'title'      => 'Notas Fiscal Detalhada ' . $id,
            'invoice'    => $invoice,
            'customer'   => $customer,
            'client_url' => admin_url('clients/client/' . $client->userid),
        ];

        $this->load->view('connect_asaas/invoice/invoice', $data);
    }


    public function files()
    {
        $data = [];
        $this->load->view('connect_asaas/invoice/files', $data);
    }


    public function setup_webhook()
    {

        if ($this->input->post()) {

            $email = $this->input->post('email', TRUE);

            $this->set_webhook_invoice($email);

            set_alert('success', 'Alterado.');

            redirect(admin_url('connect_asaas/setup_webhook'));
        }

        $webhook = $this->invoice->get_webhook_invoice();

        $data = [
            'webhook' => $webhook,
        ];
        $this->load->view('connect_asaas/invoice/setup_webhook', $data);
    }


    public function set_webhook_invoice($email)
    {
        $webhook = $this->invoice->get_webhook_invoice();
        if ($webhook["url"] !== site_url('connect_asaas/gateways/asaas_invoice_callback/index')) {
            $post_data = json_encode([
                "url"         => site_url('connect_asaas/gateways/callback'),
                "email"       => $email,
                "interrupted" => false,
                "enabled"     => true,
                "apiVersion"  => 3
            ]);
            $create_webhook = $this->invoice->create_webhook_invoice($this->api_key, $this->api_url, $post_data);
            return $create_webhook;
        }
    }

    public function fiscal_info()
    {

        $response = $this->invoice->customer_fiscal_info();

        $my_account = $this->invoice->my_account();

        $data = [
            'my_account' => json_decode($my_account, TRUE),
            'response' => json_decode($response, TRUE),
        ];
        $this->load->view('connect_asaas/invoice/fiscal_info', $data);
    }

    public function issue($id)
    {

        $asaas_invoice_on_event = get_option('asaas_invoice_on_event');

        $asaas_invoice_municipal_service    = get_option('municipal_service_default');
        $asaas_invoice_ir                   = get_option('asaas_invoice_ir');
        $asaas_invoice_inss                 = get_option('asaas_invoice_inss');
        $asaas_invoice_csll                 = get_option('asaas_invoice_csll');
        $asaas_invoice_cofins               = get_option('asaas_invoice_cofins');
        $asaas_invoice_iss                  = get_option('asaas_invoice_iss');
        $asaas_invoice_retainIss            = get_option('asaas_invoice_retainIss');
        $asaas_invoice_pis                  = get_option('asaas_invoice_pis');


        if ($asaas_invoice_on_event == '0') {

            $invoice = $this->invoices_model->get($id);
            $client  = $invoice->client;
            $clientid = $client->userid;

            if (!$asaas_invoice_municipal_service) {
                $mensagem =  "Não foi possível criar a nota fiscal, pois o campo Código de Serviço Municipal não está preenchido. Vá até a página " . admin_url("settings?group=asaasconnect_asaas-nf-settings") . " e na aba 'Opções', configure o serviço padrão.";
                set_alert('warning', $mensagem);
                redirect(admin_url('invoices/list_invoices/' . $invoice->id));
            }

            if (!$asaas_invoice_iss) {
                $mensagem =  "Não foi possível criar a nota fiscal, pois o campo ISS não está preenchido. Vá até a página " . admin_url("settings?group=connect_asaas-nf-settings") . " e defina o valor para o campo Alíquota ISS.";
                set_alert('warning', $mensagem);
                redirect(admin_url('invoices/list_invoices/' . $invoice->id));
            }

            $asaas_invoice_municipal_service = json_decode($asaas_invoice_municipal_service);
            $parts                      = explode(' - ', $asaas_invoice_municipal_service->service_name, 2);
            $municipalServiceCode       = trim($parts[0]);
            $municipalServiceName       = trim($parts[1]);
            $pipePosition = strpos($municipalServiceCode, '|');
            if ($pipePosition !== false) {
                $municipalServiceCode = trim(substr($municipalServiceCode, 0, $pipePosition));
            } else {
                $municipalServiceCode = trim($municipalServiceCode);
            }

            $disable_charge_notification = $this->asaas_gateway->getSetting('disable_charge_notification');

            if ($disable_charge_notification == '1') {
                $notificationDisabled = true;
            } else {
                $notificationDisabled = false;
            }

            $email = $this->asaas_gateway->get_customer_customfields($clientid, 'customers', 'customers_email_principal');
            $document = str_replace('/', '', str_replace('-', '', str_replace('.', '', $client->vat)));
            $postalCode = str_replace('-', '', str_replace('.', '', $client->zip));
            $address_number = $this->asaas_gateway->get_customer_customfields($clientid, 'customers', 'customers_numero');

            if (!$client->asaas_customer_id) {
                $post_data = json_encode([
                    "name"                 => $client->company,
                    "email"                => $email,
                    "cpfCnpj"              => $document,
                    "postalCode"           => $postalCode,
                    "address"              => $client->address,
                    "addressNumber"        => $address_number,
                    "complement"           => "",
                    "phone"                => $client->phonenumber,
                    "mobilePhone"          => $client->phonenumber,
                    "externalReference"    => $client->userid,
                    "notificationDisabled" => $notificationDisabled,
                ]);

                $cliente_create = $this->asaas_gateway->create_customer($this->api_url, $this->api_key, $post_data);
                $cliente_id = $cliente_create['id'];

                log_activity('Cliente cadastrado no Asaas [Cliente ID: ' . $cliente_id . ']');
            } else {
                // se existir recupera os dados para cobranca
                $cliente_id = $client->asaas_customer_id;
            }

            $post_data = json_encode([
                "customer" => $cliente_id,
                "serviceDescription" => $municipalServiceName,
                "value" => $invoice->total,
                "effectiveDate" => date('Y-m-d'),
                "externalReference" =>  $invoice->hash,
                "taxes" => [
                    "retainIss" => $asaas_invoice_retainIss,
                    "iss"       => $asaas_invoice_iss,
                    "cofins"    => $asaas_invoice_cofins,
                    "csll"      => $asaas_invoice_csll,
                    "inss"      => $asaas_invoice_inss,
                    "ir"        => $asaas_invoice_ir,
                    "pis"       => $asaas_invoice_pis
                ],
                "municipalServiceId" => $municipalServiceCode,
                "municipalServiceName" => $municipalServiceName
            ]);

            $create_invoice = $this->invoice->create_invoice($post_data);

            if ($create_invoice) {
                set_alert('success', 'Emissão agendada');
            } else {
                set_alert('warning', 'Falha ao agendar emissão');
            }

            redirect(admin_url('invoices/list_invoices/' . $id));
        }
    }

    public function create()
    {

        if ($this->input->post()) {

            $effectiveNow       = $this->input->post('effectiveNow', TRUE);
            $clientid           = $this->input->post('clientid', TRUE);
            $effectiveDate      = $this->input->post('effectiveDate', TRUE);
            $clientid           = $this->input->post('clientid', TRUE);
            $serviceDescription = $this->input->post('serviceDescription', TRUE);
            $observations       = $this->input->post('observations', TRUE);
            $value              = $this->input->post('amount', TRUE);
            $deductions         = $this->input->post('deductions', TRUE);
            $invoice_retainIss  = $this->input->post('retainIss', TRUE);
            $retainIss          = false;

            if ($invoice_retainIss == '1') {
                $retainIss = true;
            }

            $iss    = $this->input->post('iss', TRUE);
            $cofins = $this->input->post('cofins', TRUE);
            $csll   = $this->input->post('csll', TRUE);
            $inss   = $this->input->post('inss', TRUE);
            $ir     = $this->input->post('ir', TRUE);
            $pis    = $this->input->post('pis', TRUE);
            $municipalServiceDescription = $this->input->post('municipal_service_description', TRUE);


            $dataAtual = date("Y-m-d");

            if (strtotime($effectiveDate) < strtotime($dataAtual)) {
                set_alert('warning', 'Cliente não encontrado');
                redirect(admin_url('connect_asaas/invoice/create'));
            }

            if (!$clientid) {
                set_alert('warning', 'Cliente não encontrado');
                redirect(admin_url('connect_asaas/invoice/create'));
            }

            $client = $this->clients_model->get($clientid);

            $disable_charge_notification = $this->asaas_gateway->getSetting('disable_charge_notification');

            if ($disable_charge_notification == '1') {
                $notificationDisabled = true;
            } else {
                $notificationDisabled = false;
            }

            $email          = $this->asaas_gateway->get_customer_customfields($clientid, 'customers', 'customers_email_principal');
            $document       = str_replace('/', '', str_replace('-', '', str_replace('.', '', $client->vat)));
            $postalCode     = str_replace('-', '', str_replace('.', '', $client->zip));
            $address_number = $this->asaas_gateway->get_customer_customfields($clientid, 'customers', 'customers_numero');

            if (!$client->asaas_customer_id) {

                $post_data = json_encode([
                    "name"                 => $client->company,
                    "email"                => $email,
                    "cpfCnpj"              => $document,
                    "postalCode"           => $postalCode,
                    "address"              => $client->address,
                    "addressNumber"        => $address_number,
                    "complement"           => "",
                    "phone"                => $client->phonenumber,
                    "mobilePhone"          => $client->phonenumber,
                    "externalReference"    => $client->userid,
                    "notificationDisabled" => $notificationDisabled,
                ]);

                $cliente_create = $this->asaas_gateway->create_customer($this->api_url, $this->api_key, $post_data);

                $cliente_id = $cliente_create['id'];

                $this->db->where('userid', $clientid)->update(db_prefix() . 'clients', ['asaas_customer_id' => $cliente_id]);

                log_activity('Cliente cadastrado no Asaas [Cliente ID: ' . $cliente_id . ']');
            } else {
                $cliente_id = $client->asaas_customer_id;
            }

            $parts                      = explode(' - ', $municipalServiceDescription, 2);
            $municipalServiceCode       = preg_replace("/\D+/", "", trim($parts[0])); // TODO
            $municipalServiceName       = trim($parts[1]);

            $pipePosition = strpos($municipalServiceCode, '|');
            if ($pipePosition !== false) {
                $municipalServiceCode = trim(substr($municipalServiceCode, 0, $pipePosition));
            } else {
                $municipalServiceCode = trim($municipalServiceCode);
            }

            $post_request = [
                "customer"           => $cliente_id,
                "serviceDescription" => $serviceDescription,
                "observations"       => $observations,
                "value"              => moeda2float($value),
                "deductions"         => $deductions,
                "effectiveDate"      => $effectiveDate,
                "taxes" => [
                    "retainIss" => $retainIss,
                    "iss"       => $iss,
                    "cofins"    => $cofins,
                    "csll"      => $csll,
                    "inss"      => $inss,
                    "ir"        => $ir,
                    "pis"       => $pis
                ],
                "municipalServiceCode" => $municipalServiceCode,
                "municipalServiceName" => $municipalServiceName
            ];

            $post_data = json_encode($post_request);

            $create_invoice = $this->invoice->create_invoice($post_data);

            $create_invoice = json_decode($create_invoice, TRUE);

            if ($effectiveNow == '1') {
                echo $create_invoice["id"];
                echo "<hr>";
                $this->invoice->authorize_invoice($create_invoice["id"]);
                echo "<hr>";
            }
            if ($effectiveNow == '1') {
                $success_message = "Nota emitida";
            } else {
                $success_message = "Nota agendada";
            }
            if ($create_invoice) {
                log_activity($success_message . ' com sucesso. [Cliente ID: ' . $cliente_id . ']');
                set_alert('success', $success_message);
            } else {
                log_activity('Falha ao emitir nota. [Cliente ID: ' . $cliente_id . ']');
                set_alert('warning', 'Falha ao emitir nota');
            }

            redirect(admin_url('connect_asaas/asaas_invoice'));

        }

        $municipal_services = $this->db->get(db_prefix().'asaas_invoice_services')->result_array();

        $data = [
            'municipal_services' => $municipal_services,
            'title'              => 'Emitir Nota Fiscal'
        ];

        $this->load->view('connect_asaas/invoice/create', $data);
    }

    public function authorize($id)
    {
        $response = $this->invoice->authorize_invoice($id);

        var_dump($response);

        die();
    }

    public function services()
    {

        $response = $this->invoice->services();

        $data = [
            'response' => json_decode($response, TRUE),
        ];
        $this->load->view('connect_asaas/invoice/services', $data);
    }

    public function customers()
    {
        $get_customers = $this->invoice->get_customers();
    }

    public function teste()
    {
        $this->load->library('connect_asaas/invoice');

        $municipal_services = $this->invoice->services();

        $municipal_services = json_decode($municipal_services, TRUE);

        file_get_contents(FCPATH . 'modules/connect_asaas/listOfMunicipalService.json');
    }

}
