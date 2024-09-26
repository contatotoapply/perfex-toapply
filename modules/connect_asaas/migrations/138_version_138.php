<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Added at: 2024-08-22 10:43:36
class Migration_Version_138 extends App_module_migration
{

    public function up()
    {

        $CI = &get_instance();

        $customfields = [
            [
                'fieldto'   => 'invoice',
                'name'      => 'Total de Parcelas',
                'slug'      => 'invoice_quantidade_de_parcelas_para_gerar',
                'type'      => 'number',
                'active'    => 1,
                'bs_column' => 6
            ]

        ];

        foreach ($customfields as $field) {
            $CI->db->where('fieldto', $field['fieldto']);
            $CI->db->where('slug', $field['slug']);
            $CI->db->from(db_prefix() . 'customfields');
            if ($CI->db->count_all_results() == 0) {
                $CI->db->insert(db_prefix() . 'customfields', $field);
            }
        }

        add_option('paymentmethod_connect_asaas_is_installment', 0);

        if (!$CI->db->field_exists('asaas_slip_invoice_parent_id', db_prefix() . 'invoices')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `asaas_slip_invoice_parent_id` INT(11) NULL DEFAULT NULL;');
        }
    }
}
