<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Contact extends AdminController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id)
    {
        $contact = $this->db->select('central_notificacao_contact_whatsapp')
            ->where('id', $id)
            ->get(db_prefix() . 'contacts')
            ->row();

        $this->output->set_content_type('application/json')->set_output(json_encode($contact));
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
