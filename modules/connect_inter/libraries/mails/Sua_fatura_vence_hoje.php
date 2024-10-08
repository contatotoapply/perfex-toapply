<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sua_fatura_vence_hoje extends App_mail_template
{
    public $slug     = 'invoice-send-email-duedate';
    public $rel_type = 'invoice';
    protected $data;
    protected $email;

    public function __construct($data)
    {
        parent::__construct();
        $this->data = $data;
    }

    public function build()
    {
        $this->to($this->data['email'])
        ->set_merge_fields('sua_fatura_vence_hoje_merge_fields', $this->data)
        ->set_rel_id($this->data['invoice_id']);
    }

}
