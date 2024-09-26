<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Zap_engine extends ClientsController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function update_instance_name()
    {
        $instance_name = $this->input->post('instance_name');
        update_option('sms_notifications_zap_engine_library_instance_name_selected', $instance_name);
        echo json_encode(['success' => true]);
    }
}
