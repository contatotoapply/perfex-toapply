<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Send_email_on_invoice_update extends App_mail_template
{
    public $slug     = 'send-email-on-invoice-update';
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
