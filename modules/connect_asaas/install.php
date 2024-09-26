<?php
if (!$CI->db->field_exists('asaas_customer_id', db_prefix() . 'clients')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'clients` ADD `asaas_customer_id` VARCHAR(25) NULL DEFAULT NULL;');
}

if (!$CI->db->field_exists('asaas_cobranca_id', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `asaas_cobranca_id` VARCHAR(25) NULL DEFAULT NULL;');
}


if (!$CI->db->field_exists('asaas_added_by', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `asaas_added_by` VARCHAR(70) NULL DEFAULT NULL;');
}

if (!$CI->db->table_exists(db_prefix() . 'asaas_invoice_services')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "asaas_invoice_services` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `cityTaxes` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `countryTaxes` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `ibgeCode` varchar(255) DEFAULT NULL,
  `service_id` varchar(255) DEFAULT NULL,
  `issTax` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
   `stateTaxes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=" . $CI->db->char_set . ";");
}

if (!$CI->db->field_exists('asaas_subscription_id', db_prefix() . 'subscriptions')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'subscriptions` ADD `asaas_subscription_id` VARCHAR(25) NULL DEFAULT NULL;');
}

if (!$CI->db->field_exists('asaas_subscription_link', db_prefix() . 'subscriptions')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'subscriptions` ADD `asaas_subscription_link` VARCHAR(255) NULL DEFAULT NULL;');
}

if (!$CI->db->field_exists('asaas_item_id', db_prefix() . 'subscriptions')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'subscriptions` ADD `asaas_item_id` VARCHAR(25) NULL DEFAULT NULL;');
}

if (!$CI->db->field_exists('asaas_added_by', db_prefix() . 'invoicepaymentrecords')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoicepaymentrecords` ADD `asaas_added_by` VARCHAR(70) NULL DEFAULT NULL;');
}

$CI = &get_instance();

$customfields = [
    [
        'fieldto'   => 'items',
        'name'      => 'Código do Serviço',
        'slug'      => 'items_codigo_servico',
        'type'      => 'input',
        'active'    => 1,
        'bs_column' => 12
    ],
    [
        'fieldto'   => 'items',
        'name'      => 'Descrição do Serviço',
        'slug'      => 'items_servico_descricao',
        'type'      => 'input',
        'active'    => 1,
        'bs_column' => 12
    ],
    [
        'fieldto'   => 'customers',
        'name'      => 'Bairro',
        'slug'      => 'customers_bairro',
        'type'      => 'input',
        'active'    => 1,
        'bs_column' => 12
    ],
    [
        'fieldto'   => 'customers',
        'name'      => 'Email principal do cliente',
        'slug'      => 'customers_email_principal',
        'type'      => 'input',
        'active'    => 1,
        'bs_column' => 12
    ],
    [
        'fieldto'   => 'customers',
        'name'      => 'Número do endereço',
        'slug'      => 'customers_numero',
        'type'      => 'input',
        'active'    => 1,
        'bs_column' => 12
    ],
    [
        'fieldto'   => 'customers',
        'name'      => 'Configuração da emissão',
        'slug'      => 'customers_configuracao_da_emissao',
        'type'      => 'select',
        'options'   => 'Emissão avulsa,Emitir na criação da fatura,Emitir na confirmação de pagamento',
        'active'    => 1,
        'bs_column' => 12
    ],
    [
        'fieldto'   => 'invoice',
        'name'      => 'Método de Pagamento',
        'slug'      => 'invoice_metodo_de_pagamento',
        'type'      => 'multiselect',
        'options'   => 'Boleto,PIX,Cartão de Crédito',
        'active'    => 1,
        'bs_column' => 6
    ],
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


if (!$CI->db->table_exists(db_prefix() . 'asaas_invoice_files')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "asaas_invoice_files` (
    `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `invoice_id` varchar(250) null default null,
    `status` varchar(250) null default null,
    `customer` varchar(250) null default null,
    `type` varchar(250) null default null,
    `statusDescription` varchar(250) null default null,
    `serviceDescription` varchar(250) null default null,
    `pdfUrl` varchar(250) null default null,
    `xmlUrl` varchar(250) null default null,
    `rpsSerie` varchar(250) null default null,
    `rpsNumber` varchar(250) null default null,
    `number` varchar(250) null default null,
    `validationCode` varchar(250) null default null,
    `value` varchar(250) null default null,
    `deductions` varchar(250) null default null,
    `effectiveDate` varchar(250) null default null,
    `observations` varchar(250) null default null,
    `estimatedTaxesDescription` varchar(250) null default null,
    `payment` varchar(250) null default null,
    `installment` varchar(250) null default null,
    `externalReference`  varchar(250) null default null
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=" . $CI->db->char_set . ";");
}

update_option('asaas_campo_personalisado_email_padrao', 'customers_email_principal');
update_option('asaas_campo_personalisado_numero_endereco_padrao', 'customers_numero');
update_option('asaas_campo_personalisado_bairro_padrao', 'customers_bairro');

// // Criação da Tabela: asaas_carne_carnes
// if (!$CI->db->table_exists(db_prefix() . 'asaas_carne_carnes')) {
// $CI->db->query('CREATE TABLE `'.db_prefix()."asaas_carne_carnes` (
//   `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
//   `date` DATE NULL DEFAULT NULL,
//   `duedate` DATE NULL DEFAULT NULL,
//   `item_id` INT(11) NULL DEFAULT NULL,
//   `installment_quantity` INT(11) NULL DEFAULT NULL,
//   `total` DECIMAL(16,2) NULL DEFAULT NULL,
//   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
//   `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//   ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
// }

add_option('paymentmethod_connect_asaas_is_installment', 0);

if (!$CI->db->field_exists('asaas_slip_invoice_parent_id', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `asaas_slip_invoice_parent_id` INT(11) NULL DEFAULT NULL;');
}
