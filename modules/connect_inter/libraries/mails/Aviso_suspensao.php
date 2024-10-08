<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Aviso_suspensao extends App_mail_template
{
    public $slug     = 'email-suspension-notice';
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
        ->set_merge_fields('pagamento_em_atraso_merge_fields', $this->data)
        ->set_rel_id($this->data['invoice_id']);
    }
}
