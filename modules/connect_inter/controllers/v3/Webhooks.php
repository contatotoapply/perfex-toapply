<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Webhooks extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("connect_inter/banco_inter_v3_library");
        if (!is_admin()) {
            access_denied('Webhooks');
        }
    }

    public function create()
    {
        $data                       = ['title' => _l('Webhooks')];

        $data['webhook']            = get_option('paymentmethod_connect_inter_webhook');

        $data['webhook_created_at'] = get_option('paymentmethod_connect_inter_webhook_date');

        try {
            //code...
            $data['webhook_cadastrado'] = $this->banco_inter_v3_library->getWebhookCadastrado();
        } catch (\Throwable $th) {
        //    set_alert('danger', '[BANCO INTER V3] - Erro ao buscar Webhook: '. $th->getMessage());
        }

        $this->load->view('connect_inter/webhooks', $data);
    }


    public function update()
    {
        if ($this->input->method() == 'post') {

            $post = $this->input->post();

            $web_hook_url = $post['web_hook_url'];

            if (!$web_hook_url) {
                set_alert('danger', 'Informe a URL do Webhook');
                redirect('connect_inter/v3/webhooks/create');
            }

            try {
                $this->banco_inter_v3_library->createWebhook($web_hook_url);
                update_option('paymentmethod_connect_inter_webhook', $web_hook_url);
                update_option('paymentmethod_connect_inter_webhook_date', date('Y-m-d H:i:s'));
                set_alert('success', 'Webhook atualizado com sucesso');
                redirect('connect_inter/v3/webhooks/create');
            } catch (\Throwable $th) {
                set_alert('danger', 'Erro ao atualizar Webhook');
                redirect('connect_inter/v3/webhooks/create');
            }
        }
    }
}
