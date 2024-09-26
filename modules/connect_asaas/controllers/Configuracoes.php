<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Configuracoes extends AdminController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $opcoes_selecionadas = [
            'asaas_campo_personalisado_bairro_padrao' => get_option('asaas_campo_personalisado_bairro_padrao'),
            'asaas_campo_personalisado_numero_endereco_padrao' => get_option('asaas_campo_personalisado_numero_endereco_padrao'),
            'asaas_campo_personalisado_email_padrao' => get_option('asaas_campo_personalisado_email_padrao'),
        ];

        $campos_personalizados = $this->db->select('id,name,slug')->get(db_prefix() . 'customfields')->result();

        $data = ['title' => 'Configurações', 'campos_personalizados' => $campos_personalizados, 'opcoes_selecionadas' => $opcoes_selecionadas];

        $this->load->view('connect_asaas/admin/configuracoes/index', $data);
    }

    public function update()
    {
        $settings = $this->input->post('settings');
        foreach ($settings as $key => $value) {
            update_option($key, $value);
        }
        set_alert('success', _l('updated_successfully'));
        redirect(admin_url('connect_asaas/configuracoes'));
    }
}
