<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_109 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();          
        
        if (!$CI->db->field_exists('receipt_for_a_specific_agent', db_prefix() . 'commission_receipt')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . "commission_receipt`
            ADD COLUMN `receipt_for_a_specific_agent` TINYINT(1) NOT NULL DEFAULT 0,
            ADD COLUMN `agent` VARCHAR(25) NULL;");
        }
    }
}

