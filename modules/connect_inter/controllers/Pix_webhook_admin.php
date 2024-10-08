<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pix_webhook_admin extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("connect_inter/inter_library");
    }

    public function create()
    {
        if($this->input->post()) {

            $web_hook_url = $this->input->post('web_hook_url');

            $data = ['webhookUrl' => $web_hook_url];

            $chavePix = get_option('paymentmethod_banco_inter_chave_pix');

            $this->inter_library->createWebhookPix($chavePix, $data);

            update_option('paymentmethod_banco_inter_webhook_pix', $web_hook_url);

            $webhookCreated = $this->inter_library->getWebhookPix($chavePix);

            update_option('paymentmethod_banco_inter_webhook_pix_data', $webhookCreated );

        }

        $webhook = json_decode(get_option('paymentmethod_banco_inter_webhook_pix_data'));

        $data = ['webhook'=> $webhook ];

        $this->load->view("banco_inter/pix", $data);
    }
}
