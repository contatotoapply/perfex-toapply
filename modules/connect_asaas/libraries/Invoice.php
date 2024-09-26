<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Invoice
{
    protected $apiKey;
    protected $apiUrl;
    protected $ci;
    protected $user_agent;
    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->library('connect_asaas/asaas_gateway');
        $this->ci->load->library('connect_asaas/base_api');
        $this->apiKey  = $this->ci->base_api->getApiKey();
        $this->apiUrl  = $this->ci->base_api->getUrlBase();
        $this->user_agent = 'Perfex CRM';
    }

    public function get_customers()
    {
        $api_key = $this->apiKey;
        $api_url =  $this->apiUrl;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/customers");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, TRUE);
    }

    /**
     *
     */
    public function search_cliente(string $cpfCnpj)
    {
        $api_key = $this->apiKey;
        $api_url =  $this->apiUrl;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url . "/v3/customers?cpfCnpj=" . $cpfCnpj,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "access_token: " . $api_key,
                "User-Agent: " . $this->user_agent
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $customers = json_decode($response, TRUE);
        return $customers;
    }

    public function my_account()
    {
        $api_key = $this->apiKey;
        $api_url =  $this->apiUrl;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/myAccount");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /*
    * SCHEDULED - Agendada
    * SYNCHRONIZED - Enviada para prefeitura
    * AUTHORIZED - Emitida
    * PROCESSING_CANCELLATION - Processando cancelamento
    * CANCELED - Cancelada
    * CANCELLATION_DENIED - Cancelamento negado
    * ERROR - Erro na emiss�o
    */
    public function authorize_invoice($id)
    {
        $api_key = $this->apiKey;
        $api_url =  $this->apiUrl;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/invoices/{$id}/authorize");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent
        ));

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function create_invoice($body)
    {
        $api_key = $this->apiKey;
        $api_url =  $this->apiUrl;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/invoices");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /*
    * SCHEDULED - Agendada
    * SYNCHRONIZED - Enviada para prefeitura
    * AUTHORIZED - Emitida
    * PROCESSING_CANCELLATION - Processando cancelamento
    * CANCELED - Cancelada
    * CANCELLATION_DENIED - Cancelamento negado
    * ERROR - Erro na emiss�o
    */

    public function update_invoice($body, $id)
    {
        $api_key = $this->apiKey;
        $api_url =  $this->apiUrl;
        $body = json_encode([
            "customer" => "cus_000004825422",
            "serviceDescription" =>  "Nota fiscal da Fatura 101940. nDescri��o dos Servi�os:  AN�LISE E DESENVOLVIMENTO DE SISTEMAS",
            "observations" =>  "Mensal referente aos trabalhos de Junho.",
            "value" =>  300,
            "deductions" =>  10,
            "effectiveDate" =>  "2018-07-03",
            "externalReference" =>  null,
            "taxes" =>  [
                "retainIss" =>  false,
                "iss" =>  3,
                "cofins" =>  3,
                "csll" =>  1,
                "inss" =>  0,
                "ir" =>  1.5,
                "pis" =>  0.65
            ]
        ]);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/invoices/" . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent
        ));

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    public function get_invoices(array $params) : array
    {
        $api_key = $this->apiKey;
        $api_url =  $this->apiUrl;

        $offset = $params['offset'] ?? 0;
        $limit = $params['limit']   ?? 10;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/invoices?offset=$offset&limit=$limit");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, TRUE) ?? [];
    }

    public function get_invoice($id)
    {
        $api_key = $this->apiKey;
        $api_url =  $this->apiUrl;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/invoices/{$id}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent
        ));

        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }

    public function customer_fiscal_info()
    {
        $api_key = $this->apiKey;
        $api_url =  $this->apiUrl;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/customerFiscalInfo");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent
        ));

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    public function services()
    {
        $api_key = $this->apiKey;
        $api_url =  $this->apiUrl;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/invoices/municipalServices");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent
        ));

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function seach_services($description)
    {
        $api_key = $this->apiKey;
        $api_url =  $this->apiUrl;
        $ch = curl_init();
        // ?description=
        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/invoices/municipalServices?description=" . $description);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent
        ));

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    public function get_webhook_invoice()
    {
        $api_key = $this->apiKey;
        $api_url =  $this->apiUrl;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/webhook/invoice");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, TRUE);
    }

    public function create_webhook_invoice($post_data)
    {
        $api_key = $this->apiKey;
        $api_url =  $this->apiUrl;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/webhook/invoice");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function get_webhook_transfer()
    {
        $api_key = $this->apiKey;
        $api_url =  $this->apiUrl;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/webhook/transfer");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function create_webhook_transfer($post_data)
    {
        $api_key = $this->apiKey;
        $api_url =  $this->apiUrl;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/webhook/transfer");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
