<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Inter_gateway extends App_gateway
{
    public function __construct()
    {
        /**
         * Call App_gateway __construct function
         */
        parent::__construct();

        $this->ci = &get_instance();

        /**
         * Gateway unique id - REQUIRED
         */
        $this->setId('connect_inter');

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('Banco V3 Inter Boleto');

        /**
         * Add gateway settings
         */
        $this->setSettings([
            [
                'name'              => 'inter_client_id',
                'label'             => 'Client ID',
                'type'              => 'input'
            ],
            [
                'name'              => 'inter_client_secret',
                'label'             => 'Client Secret',
                'type'              => 'input'
            ],
            [
                'name'              => 'currencies',
                'label'             => 'settings_paymentmethod_currencies',
                'default_value'     => 'BRL'
            ],
            [
                'name'              => 'test_mode_enabled',
                'type'              => 'yes_no',
                'default_value'     => 0,
                'label'             => 'settings_paymentmethod_testing_mode',
            ],
            [
                'name'              => 'debug',
                'type'              => 'yes_no',
                'default_value'     => 0,
                'label'             => 'debug',
            ],
            [
                'name'              => 'b_inter_multa',
                'label'             => 'Multa',
                'type'              => 'input',
                'default_value'     => '0.02',
            ],
            [
                'name'              => 'b_inter_juros',
                'label'             => 'Juros',
                'type'              => 'input',
                'default_value'     => '1',
            ],
            [
                'name'              => 'juros_modalidade',
                'label'             => 'Modalidade de Juros',
                'type'              => 'input',
                'default_value'     => 'TAXAMENSAL'
            ],
            [
                'name'              => 'multa_modalidade',
                'label'             => 'Modalidade de Multa',
                'type'              => 'input',
                'default_value'     => 'PERCENTUAL'
            ],
            [
                'name'              => 'b_inter_mostrar_linhas',
                'label'             => 'Mostrar textos no boleto (5 linhas)',
                'type'              => 'yes_no',
                'default_value'     => 1
            ],
            [
                'name'              => 'gerar_boleto_apos_criar_fatura',
                'label'             => 'Gerar boleto após criar ou atualizar uma fatura',
                'type'              => 'yes_no',
                'default_value'     => 1
            ],
            [
                'name'              => 'permitir_cancelar_boleto_na_api',
                'label'             => 'Permitir cancelar boleto na API',
                'type'              => 'yes_no',
                'default_value'     => 1
            ],
            [
                'name'              => 'pix',
                'label'             => 'PIX',
                'type'              => 'yes_no',
                'default_value'     => 1
            ],
            [
                'name'              => 'criar_pixao_criar_fatura',
                'label'             => 'Criar Pix ao Criar a Fatura',
                'type'              => 'yes_no',
                'default_value'     => 1
            ],
            [
                'name'              => 'desativar_visualizacao_boleto_tela_pagamento',
                'label'             => 'Mostrar boleto na tela de pagamento',
                'type'              => 'yes_no',
                'default_value'     => 0
            ],
            [
                'name'          => 'is_installment',
                'label'         => _l('Habilitar Carnê'),
                'type'          => 'yes_no',
                'default_value' => 0,
            ]
        ]);
    }

    public function process_payment($data)
    {
        $invoice = $data['invoice'];
        $id = $invoice->id;
        $hash = $invoice->hash;
        return redirect("connect_inter/invoice/{$id}/{$hash}");
    }
}
