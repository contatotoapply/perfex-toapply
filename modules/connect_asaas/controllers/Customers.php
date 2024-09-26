<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Customers extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('connect_asaas/asaas_gateway');
        $this->load->library('connect_asaas/customer');
        $this->apiKey  = $this->asaas_gateway->getApiKey();
        $this->apiUrl  = $this->asaas_gateway->getUrlBase();
    }

    /**
     * @return [type]
     */
    public function index()
    {
        $response = $this->asaas_gateway->get_customers($this->apiKey, $this->apiUrl);

        $result_data = $response["data"] ?? [];

        $data = [
            "response" => $result_data,
            'title' => 'Clientes no Asaas',
        ];

        $data['clients'] = $this->db->select('userid,company,vat,asaas_customer_id')->get(db_prefix() . 'clients')->result();


        $this->load->view('connect_asaas/admin/customers/index', $data);
    }

    /**
     * @param mixed $customer_id
     *
     * @return [type]
     */
    public function show($customer_id)
    {
        $customer = (array) $this->customer->get_by_id($customer_id);

        $data = ['title' => 'Detalhes do Cliente', 'customer' => $customer];

        $row = $this->db->select('*')->where('asaas_customer_id', $customer_id)->get(db_prefix() . 'clients')->row();

        $data['client'] = $row;

        $this->load->view("connect_asaas/admin/customers/show", $data);
    }

    public function delete($customer_id)
    {
        if (!is_admin()) {
            set_alert('danger', _l('Não é administrador'));
            redirect(admin_url());
        }
        $response = $this->customer->delete($customer_id);
        if ($response) {
            set_alert('success', _l('Cliente removido com sucesso no Asaas.'));
        } else {
            set_alert('danger', _l('Houve algum erro na tentativa de remover o cliente no asaas.'));
        }
        redirect(admin_url('connect_asaas/customers'));
    }

    public function set_customer_id($customer_id, $cpf_cnpj)
    {
        if (!is_admin()) {
            set_alert('danger', _l('Não é administrador'));
            redirect(admin_url());
        }

        $client = $this->db->select('*')
            ->where("TRIM(REPLACE(REPLACE(REPLACE(vat, '.', ''), '/', ''), '-', '')) =", $cpf_cnpj, true)
            ->get(db_prefix() . 'clients', 1)
            ->row();

        if ($client->asaas_customer_id) {
            if ($client->asaas_customer_id != $customer_id) {
                $this->db->where('userid', $client->userid)
                ->update(db_prefix() . 'clients', ['asaas_customer_id' => $customer_id]);
                set_alert('success', _l('Cliente atualizado com sucesso no Perfex.'));
            }
        } else {
            $this->db->where('userid', $client->userid)->update(db_prefix() . 'clients', ['asaas_customer_id' => $customer_id]);
        }
        redirect(admin_url('connect_asaas/customers'));
    }
}
