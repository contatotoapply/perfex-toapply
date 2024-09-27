<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Register as global Perfex crm extension
perfex_saas_register_global_extension('saas_super_assistant');

// Create tables
$table = perfex_saas_table('super_assistants');
$staff_table = db_prefix() . 'staff';
if (!$CI->db->table_exists($table)) {
    $CI->db->query(
        "CREATE TABLE IF NOT EXISTS `" . $table . "` (
            `id` int NOT NULL AUTO_INCREMENT,
            `staff_id` int NOT NULL,
            `permissions` text NOT NULL,
            `tenants` text,
            `metadata` text,
            `last_signed_in` varchar(255) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_staff_id` (`staff_id`),
            CONSTRAINT `fk_assistant_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `$staff_table` (`staffid`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";"
    );
}