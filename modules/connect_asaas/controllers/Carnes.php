<?php

/**
 * Criado pelo : Taffarel Dev Module Creator
 * Autor       : Taffarel Xavier
 * Email       : contato@taffarel.dev
 * Site        : https://taffarel.dev?t=1724331201
 * GitHub      : https://github.com/TaffarelXavier
 * ID          : 57f07f4c80e7413add64265ff2763338
 **/

defined('BASEPATH') or exit('No direct script access allowed');

class Carnes extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('carne_model');
    }

    public function index()
    {
        $data = ['title' => 'carnes'];
        $result = $this->carne_model->all();
        $data['carnes'] = $result;
        $this->load->view("connect_asaas/admin/carnes/index", $data);
    }

    // Feito por: Taffarel Dev
    public function store()
    {
        $data = $this->input->post();
        $result = $this->carne_model->create($data);
        if ($result) {
            set_alert('success', _l('asaas_carne_created_successfull', 'carnes'));
        } else {
            set_alert('danger', _l('asaas_carne_error_when_trying_create'));
        }
        redirect(admin_url('connect_asaas/carnes'));
    }

    public function edit($id)
    {
        $result = $this->carne_model->find(null, ['id' => $id]);
        if (!$result) {
            set_alert('danger', _l('asaas_carne_not_found', 'carnes'));
            redirect(admin_url('carnes'));
        }
        $this->load->view("connect_asaas/admin/carnes/edit", ['row' => $result]);
    }

    public function update(int $id)
    {
        $data = $this->input->post();
        $result = $this->carne_model->find(null, ['id' => $id]);
        if (!$result) {
            set_alert('danger', _l('asaas_carne_not_found', 'carnes'));
            redirect(admin_url('carnes'));
        }
        $result = $this->carne_model->update($data, ['id' => $id]);
        if ($result) {
            set_alert('success', _l('asaas_carne_updated_successfull', 'carnes'));
        } else {
            set_alert('danger', _l('asaas_carne_updated_error_successfull', 'carnes'));
        }
        redirect(admin_url("connect_asaas/admin/carnes/{$id}"));
    }

    public function delete(int $id)
    {
        $result = $this->carne_model->find(null, ['id' => $id]);
        if (!$result) {
            set_alert('danger', _l('asaas_carne_not_found', 'carnes'));
            redirect(admin_url('carnes'));
        }
        $result = $this->carne_model->destroy(['id' => $id]);
        if ($result) {
            set_alert('success', _l('asaas_carne_record_successfully_deleted', 'carnes'));
        } else {
            set_alert('danger', _l('asaas_carne_error_by_excluding_registration', 'carnes'));
        }
        redirect(admin_url('connect_asaas/carnes'));
    }

    public function show($id)
    {
        $result = $this->carne_model->find(null, ['id' => $id]);
        if (!$result) {
            echo json_encode(['error' => true, 'message' => _l('asaas_carne_not_found', 'carnes')]);
            die;
        }

        echo json_encode(['error' => false, 'message' => _l('asaas_carne_success'), 'data' => $result]);
    }

    public function _remap($method, $params = array())
    {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        } else {
            $this->index($method);
        }
    }
}
