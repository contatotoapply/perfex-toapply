<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Service
{
    protected $apiKey;
    protected $apiUrl;
    protected $tbl_services;
    protected $ci;
    protected $user_agent;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->library('connect_asaas/base_api');
        $this->apiKey       = $this->ci->base_api->getApiKey();
        $this->apiUrl       = $this->ci->base_api->getUrlBase();
        $this->tbl_services = db_prefix() . 'asaas_invoice_services';
        $this->user_agent = 'Perfex CRM';
    }

    public function services($description = null)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl . "/v3/invoices/municipalServices?limit=500&description=$description",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "access_token: " . $this->apiKey,
                "User-Agent: " . $this->user_agent,
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $customer = json_decode($response);
        return $customer;
    }

    public function atualizarBaseServicos()
    {
        try {
            if (get_option('paymentmethod_connect_asaas_ultima_atualizacao_servicos_municipais') != date('Y-m-d')) {

                $response = $this->services();

                $insertArray = $response->data;

                $new_array = [];

                foreach ($insertArray as $key => $value) {
                    $row = $this->ci->db->where('service_id', $value->id)->get(db_prefix() . 'asaas_invoice_services')->row();
                    if (!$row) {
                        $new_array[] = [
                            'service_id' => $value->id,
                            'description' => $value->description,
                            'issTax' => $value->issTax
                        ];
                    }
                }

                update_option('paymentmethod_connect_asaas_ultima_atualizacao_servicos_municipais', date('Y-m-d'));

                if (!empty($new_array)) {
                    $this->ci->db->insert_batch(db_prefix() . 'asaas_invoice_services', $new_array);
                }

                if ($total = $this->ci->db->affected_rows()) {
                    log_activity('Base de Serviços Municipais atualizada com sucesso. ' . $total . ' registros inseridos.');
                }
            }
        } catch (\Throwable $th) {
            log_activity('Erro ao atualizar a base de serviços municipais.');
        }
    }

    /**
     * @param mixed $data
     *
     * @return [type]
     */
    public function createService($data)
    {

        $service_id       = $data['cnae_service_id'];
        $cnae_code        = $data['cnae_code'];
        $cnae_description = $data['cnae_description'];

        $query = $this->ci->db->select('*')
            ->from($this->tbl_services)
            ->where('description LIKE "' . $cnae_code . '%"')
            ->get();

        $rows = $query->result();

        if ($rows) {
            return (object)[
                'errors' => [
                    'message' => 'Serviço já cadastrado'
                ]
            ];
        }

        $data = ['service_id' => $service_id, 'service_name' => $cnae_code . " - " . $cnae_description];
        update_option('municipal_service_default', json_encode($data));

        $data = [
            'service_id' => $service_id,
            'description' => $cnae_code . " - " . $cnae_description,
        ];
        $this->ci->db->insert($this->tbl_services, $data);

        $affected_rows = $this->ci->db->affected_rows();
        if ($affected_rows) {
            $data['id'] = $this->ci->db->insert_id();
            return (object)[
                'data' => $data,
                'success' => true
            ];
        }
    }
}
