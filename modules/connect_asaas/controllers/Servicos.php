<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Servicos extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('connect_asaas/service');
    }


    public function index()
    {
        $params = $this->input->post();

        if ($q = $params['q']) {

            $q = replace_accents($q);

            $this->db->select('id, description as name');

            $this->db->like('description', $q, 'both');

            $query = $this->db->get(db_prefix().'asaas_invoice_services');

            $result = $query->result_array();

            if ($result) {
                echo json_encode($result);
            } else {
                $response = $this->service->services($q);

                if (!empty($response->data)) {

                    $services = $response->data;

                    $new_data = array_map(function ($service) {
                        return ['id' => $service->id, 'name' => $service->description];
                    }, $services);

                    echo json_encode($new_data);
                }
            }
        }
    }
    public function store()
    {
        $post_request = $this->input->post();

        $response = $this->service->createService($post_request);

        if (isset($response->errors)) {
            echo json_encode($response->errors);
        } else {
            echo json_encode($response);
        }
    }
}
