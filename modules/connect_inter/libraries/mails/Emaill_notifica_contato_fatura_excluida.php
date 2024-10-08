<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Emaill_notifica_contato_fatura_excluida extends App_mail_template
{
    public $slug     = 'notificar-contato-fatura-excluida';
    public $rel_type = 'invoice';
    protected $data;

    public function __construct($data)
    {
        parent::__construct();
        $this->data = $data;
    }

    public function build()
    {
        $this->to($this->data['email'])
            ->set_rel_id($this->data['invoice']['id'])
            ->set_merge_fields('emaill_notifica_contato_fatura_excluida_merge_fields', $this->data);
    }
}
