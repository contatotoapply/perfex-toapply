<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
$table = function_exists('perfex_saas_table') ? perfex_saas_table('super_assistants') : db_prefix() . 'perfex_saas_super_assistants';
if ($CI->db->table_exists($table)) {
    $CI->db->query("DROP TABLE $table");
}
