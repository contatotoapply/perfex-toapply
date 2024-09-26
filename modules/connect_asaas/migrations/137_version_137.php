<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Added at: 2024-08-14 16:02:47
class Migration_Version_137 extends App_module_migration
{

    public function up()
    {

        $CI = &get_instance();

        $customfields = [
            [
                'fieldto'   => 'invoice',
                'name'      => 'Método de Pagamento',
                'slug'      => 'invoice_metodo_de_pagamento',
                'type'      => 'multiselect',
                'options'   => 'Boleto,PIX,Cartão de Crédito',
                'active'    => 1,
                'bs_column' => 6
            ],

        ];

        foreach ($customfields as $field) {
            $CI->db->where('fieldto', $field['fieldto']);
            $CI->db->where('slug', $field['slug']);
            $CI->db->from(db_prefix() . 'customfields');
            if ($CI->db->count_all_results() == 0) {
                $CI->db->insert(db_prefix() . 'customfields', $field);
            }
        }
    }
}
