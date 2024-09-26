<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sms_notifications_zap_engine extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("sms_connect_api/ticket_library");
    }

    public function index()
    {
        $ticketid = 2;
        $ticket = $this->db->where('ticketid', $ticketid)->get(db_prefix().'tickets')->row();
        dd(1, $this->ticket_library->fn_admin_created_ticket($ticket));
    }

    public function testEnvioMensagem()
    {
        $merge_fields = [
            'contact_firstname' => 'Taffarel',
            'two_factor_auth_code' => 123,
        ];

        dd(1, $this->sms_notifications_zap_engine_library->trigger('notifications_zap_engine_faturas_atrasadas', 63999364840, $merge_fields));
    }
}
