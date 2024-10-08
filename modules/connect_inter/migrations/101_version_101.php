<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_101 extends App_module_migration
{

    /**
     * Add triggers, actions and last_triggered_by, active fields
     */
    public function up()
    {
        $CI = &get_instance();
        if (!$CI->db->field_exists('banco_inter_dados_cobranca', db_prefix() . 'invoices')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `banco_inter_dados_cobranca` TEXT NULL DEFAULT NULL;');
        }
    }
}
