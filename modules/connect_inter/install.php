<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$get_media_path_banco_inter_invoices = get_media_path_banco_inter_invoices() . "/banco_inter/invoices";

_maybe_create_upload_path($get_media_path_banco_inter_invoices);

_maybe_create_upload_path(CONNECT_INTER_MODULE_NAME_UPLOADS_FOLDER);

if (!$CI->db->field_exists('banco_inter_dados_cobranca', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `banco_inter_dados_cobranca` TEXT NULL DEFAULT NULL;');
}

if (!$CI->db->field_exists('banco_inter_codigo_solicitacao', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `banco_inter_codigo_solicitacao` VARCHAR(50) NULL DEFAULT NULL;');
}

if (!$CI->db->field_exists('email_cobranca_enviado_dia_vencimento_at', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `email_cobranca_enviado_dia_vencimento_at` TIMESTAMP NULL DEFAULT NULL;');
}

if (!$CI->db->field_exists('banco_inter_item_adicionado', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `banco_inter_item_adicionado` tinyint(4) NULL DEFAULT 0;');
}

if (!$CI->db->field_exists('is_amount_updated', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `is_amount_updated` tinyint(1) NULL DEFAULT '0';");
}

if (!$CI->db->field_exists('is_financial_record_name_updated', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `is_financial_record_name_updated` tinyint(1) NULL DEFAULT '0';");
}

if (!$CI->db->field_exists('is_amount_name_updated', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `is_amount_name_updated` tinyint(1) DEFAULT '0';");
}

if (!$CI->db->field_exists('is_nota_fiscal_gerada', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `is_nota_fiscal_gerada` tinyint(1) DEFAULT '0';");
}

if (!$CI->db->field_exists('bi_pix', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `bi_pix` TEXT NULL DEFAULT NULL;");
}

if (!$CI->db->field_exists('bi_pix_criado_em', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `bi_pix_criado_em` TIMESTAMP NULL DEFAULT NULL;");
}

if (!$CI->db->field_exists('bi_nosso_numero', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `bi_nosso_numero` VARCHAR(45) NULL DEFAULT NULL;");
}

if (!$CI->db->field_exists('bi_boleto', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `bi_boleto` TEXT NULL DEFAULT NULL;");
}

if (!$CI->db->field_exists('banco_inter_boleto_gerado_at', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `banco_inter_boleto_gerado_at` TIMESTAMP NULL DEFAULT NULL;");
}

if (!$CI->db->field_exists('email_cobranca_enviado_recorrente_at', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `email_cobranca_enviado_recorrente_at` TIMESTAMP NULL DEFAULT NULL;");
}

if (!$CI->db->field_exists('bi_next_mailing_day', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `bi_next_mailing_day` DATE NULL DEFAULT NULL;");
}

if (!$CI->db->field_exists('fatura_created_at', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `fatura_created_at` TIMESTAMP NULL DEFAULT NULL;");
}

if (!$CI->db->field_exists('bi_tentativas_criar_boleto', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `bi_tentativas_criar_boleto` int(11) UNSIGNED NOT NULL DEFAULT '0';");
}

if (!$CI->db->field_exists('task_last_due_reminder', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `task_last_due_reminder` tinyint(1) NOT NULL DEFAULT '0';");
}

if (!$CI->db->field_exists('pagamento_atrasado_email_enviado_at', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `pagamento_atrasado_email_enviado_at` TIMESTAMP NULL DEFAULT NULL;");
}

if (!$CI->db->field_exists('updated_at', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;");
}

if (!$CI->db->field_exists('pagamento_atrasado_last_overdue_reminder', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `pagamento_atrasado_last_overdue_reminder` date DEFAULT NULL;");
}

if (!$CI->db->field_exists('aviso_suspensao_proximo_dia_util', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `aviso_suspensao_proximo_dia_util` date NULL DEFAULT NULL;");
}

if (!$CI->db->field_exists('aviso_suspensao_enviado_at', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `aviso_suspensao_enviado_at` TIMESTAMP NULL DEFAULT NULL;");
}

if (!$CI->db->field_exists('lembrete_faturas_vencida_enviado_at', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . "invoices` ADD `lembrete_faturas_vencida_enviado_at` TIMESTAMP NULL DEFAULT NULL;");
}

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

add_option('paymentmethod_connect_inter_is_installment', 0);

if (!$CI->db->field_exists('banco_inter_slip_invoice_parent_id', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `banco_inter_slip_invoice_parent_id` INT(11) NULL DEFAULT NULL;');
}
