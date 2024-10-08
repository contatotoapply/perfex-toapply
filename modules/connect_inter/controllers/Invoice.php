<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Invoice extends ClientsController
{
    public $invoices_model;
    public $banco_inter_v3_library;
    public $db;

    public function __construct()
    {
        parent::__construct();
        $this->load->library("connect_inter/banco_inter_v3_library");
    }

    public function index($id, $hash)
    {
        $hash = $hash[0];

        check_invoice_restrictions($id, $hash);

        $logo = $pix = $boleto = null;

        $company_logo = get_option('company_logo_dark');

        $invoice = $this->invoices_model->get($id);

        if ($company_logo != '') {
            $logo = base_url('uploads/company/' . $company_logo);
        }

        $invoice->total = get_invoice_total_left_to_pay($id);

        $invoice->cobranca =  json_decode($invoice->banco_inter_dados_cobranca);

        if (!$invoice->cobranca->codigoSolicitacao) {
            set_alert('warning', "Boleto não encontrado ou não foi gerado.");
            return redirect(base_url("invoice/$id/$hash"));
        }

        $data = ['invoice' => $invoice, 'logo' => $logo];

        $this->load->view("invoice", $data);
    }

    public function baixar($id, $hash)
    {
        if (!invoice_exists($id, $hash)) {
            set_alert('danger', "Boleto não encontrado.");
            return redirect(base_url());
        }

        $invoice = $this->invoices_model->get($id);

        $nome_arquivo = str_replace("/", "-", format_invoice_number($invoice->id));

        header('Content-Type: application/pdf');

        header('Content-Disposition: attachment; filename="BOLETO_' . $nome_arquivo . '.pdf"');

        if ($invoice) {
            $cobranca = json_decode($invoice->banco_inter_dados_cobranca);
            if(!$cobranca->codigoSolicitacao){
                set_alert('warning', "Boleto não encontrado ou não foi gerado.");
                redirect(base_url("invoice/$id/$hash"));
            }
            echo $this->banco_inter_v3_library->baixarPdf($cobranca->codigoSolicitacao);
        }
    }

    public function imprimir($id, $hash)
    {
        check_invoice_restrictions($id, $hash);

        $invoice = $this->invoices_model->get($id);

        $cobranca = json_decode($invoice->banco_inter_dados_cobranca);

        if (!$cobranca->codigoSolicitacao) {
            set_alert('warning', "Boleto não encontrado ou não foi gerado.");
            redirect(base_url("invoice/$id/$hash"));
        }

        echo $this->banco_inter_v3_library->mostraPdfBrowser($cobranca->codigoSolicitacao);
    }

    /**
     * Verifica se o pagamento foi efetuado
     * @return [type]
     */
    public  function verificar_pagamento_efetuado($id, $hash)
    {
        check_invoice_restrictions($id, $hash);

        $invoice = $this->db->select('id,hash,status')->where('id', $id)
            ->where('hash', $hash)->get(db_prefix() . 'invoices')->row();

        echo $invoice->status == 2 ? '1' : '0';
    }


    // create function _remap ci3
    public function _remap($method, $params = array())
    {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        } else {
            $params_count = count($params);

            if ($params_count == 1) {
                $this->index($method, $params);
            } elseif ($params_count == 2) {
                $id = $method;
                $this->{$params[1]}($id, $params[0]);
            }
        }
    }
}
