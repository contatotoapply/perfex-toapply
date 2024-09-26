<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_108 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();          
        if (!$CI->db->field_exists('percentage_of_product_type', db_prefix() . 'commission_policy')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . "commission_policy`
            ADD COLUMN `percentage_of_product_type` VARCHAR(45) NOT NULL DEFAULT 'number';");
        }

        add_option('calculate_recurring_invoice', 'latest_program');
    }
}

