<?php

add_option('central_notificacoes_softpowerne_url_base_do_servidor', '');
add_option('central_notificacoes_softpowerne_hora_para_enviar_notificacao_para_faturas_em_atraso', '7');
add_option('central_notificacoes_softpowerne_hora_para_enviar_notificacao_para_faturas_no_dia_do_vencimento', '8');
add_option('central_notificacoes_softpowerne_reenviar_automaticamente_um_lembrete_depois_de_dias', '1');
add_option('central_notificacoes_softpowerne_enviar_lembrete_x_dias_antes_da_data_de_vencimento', '2');

add_option('sms_notifications_zap_engine_library_qtd_dias_para_avisar_lembrete_vencimento', '3,2,1');
add_option('sms_notifications_zap_engine_library_qtd_dias_de_lembretes', '1,2');
add_option('sms_notifications_zap_engine_library_qtd_dias_para_enviar_lembrete_suspensao', '3,4');
add_option('sms_notifications_zap_engine_library_qtd_dias_para_suspender_servicos', '5');

add_option('sms_trigger_notifications_zap_engine_lembrete_pagamento', 'Prezado(a) {contact_firstname},

Gostaríamos de lembrá-lo(a) sobre a iminente data de vencimento da fatura associada à sua conta. Agradecemos sua atenção a este assunto.

Detalhes da Fatura:
- Número da Fatura: {invoice_number}
- Data de Vencimento: {invoice_duedate}
- Valor: {invoice_total}

Para acessar a fatura completa e efetuar o pagamento, por favor, utilize o seguinte link: {invoice_link}

Agradecemos pela sua cooperação.

Atenciosamente,
{companyname}');

add_option('sms_trigger_notifications_zap_engine_enviar_no_dia_do_vencimento','Assunto: *Lembrete de Vencimento*

Prezado(a)  {contact_firstname},

Esperamos que esteja bem! Queremos lembrar que hoje é o dia do vencimento referente à fatura número
*{invoice_number}*. Para garantir que sua conta permaneça em dia, solicitamos que realize o pagamento até o final do dia.

Link da fatura: {invoice_link}

Se você já efetuou o pagamento, por favor, ignore este lembrete.

Agradecemos por sua parceria contínua e ficamos à disposição para qualquer dúvida ou assistência.

Atenciosamente,
{companyname}');

add_option('sms_trigger_notifications_zap_engine_faturas_atrasadas','Lembrete Importante: *Faturas Vencidas*
*Prezado(a) cliente,*

Total de Dias Vencidos: {qtd_dias_vencida}

Esperamos que esteja tudo bem com você. Gostaríamos de lembrar que a sua cobrança referente à fatura
*{invoice_number}* ainda não foi quitada. Pedimos que, por gentileza, regularize o pagamento o mais breve possível.

Valorizamos a sua parceria e entendemos que imprevistos acontecem. Caso haja alguma dificuldade, estamos disponíveis para ajudar a encontrar uma solução adequada. Não deixe de entrar em contato conosco.

Agradecemos a sua atenção e contamos com sua colaboração para manter nosso relacionamento em dia.

Atenciosamente,

Link: {invoice_link}

*{companyname} | CRM*');

add_option('sms_trigger_notifications_zap_engine_servico_suspenso','
Lembrete Importante: *Suspensão de Serviços em Breve*

Prezado(a) {contact_firstname},

Esperamos que esta mensagem o(a) encontre bem. Gostaríamos de lembrar sobre a pendência de pagamento associada à sua conta em nome de {companyname}, referente à fatura de número {invoice_number}.

Detalhes da Fatura:

Número da Fatura: {invoice_number}
Valor Total: {invoice_total}
Data de Vencimento: {invoice_duedate}
Dias até o Vencimento: {qtd_dias_vencida}
A fatura encontra-se pendente há {qtd_dias_vencida} dias, e o prazo para pagamento está prestes a expirar em {invoice_duedate}. Caso a fatura não seja quitada até essa data, lamentavelmente, seremos forçados a suspender temporariamente os seus serviços.

Para evitar qualquer interrupção indesejada, por favor, considere efetuar o pagamento o quanto antes. Você pode visualizar e pagar a fatura de maneira conveniente através deste link: {invoice_link}.

Estamos à disposição para esclarecer qualquer dúvida ou fornecer assistência adicional. Por favor, entre em contato conosco pelo número {phonenumber} ou pelo e-mail {cliente_email}.

Agradecemos a sua atenção a esta questão e esperamos continuar a fornecer serviços de qualidade.

Atenciosamente,

Equipe {companyname}');

add_option('sms_trigger_notifications_zap_engine_servicos_suspensos','Assunto: *Aviso de Suspensão de Serviço - Ação Necessária*

Prezado(a) {contact_firstname},

Olá, esperamos que esteja bem. Gostaríamos de informar que a sua conta em nome de {companyname} está prestes a ser suspensa devido a um pagamento pendente.

Detalhes da Fatura:

Número da Fatura: {invoice_number}
Valor Total: {invoice_total}
Data de Vencimento: {invoice_duedate}
Dias até o Vencimento: {qtd_dias_vencida}
A fatura em questão está pendente há {qtd_dias_vencida} dias e precisa ser paga até {invoice_duedate} para evitar a suspensão dos serviços. Você pode visualizar e pagar a fatura através deste link: {invoice_link}.

Caso já tenha efetuado o pagamento, por favor, desconsidere este aviso.

Pedimos que entre em contato conosco o mais breve possível pelo número {phonenumber} ou pelo e-mail {cliente_email} para confirmar o pagamento ou discutir alternativas.

Agradecemos a sua compreensão e esperamos resolver essa questão prontamente para que você possa continuar a desfrutar dos nossos serviços.

Atenciosamente,

Equipe {companyname}');




if (!$CI->db->field_exists('cn_aviso_faturas_vencida_enviado_at', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `cn_aviso_faturas_vencida_enviado_at` TIMESTAMP NULL DEFAULT NULL AFTER id;');
}


if (!$CI->db->table_exists(db_prefix() . 'notifications_central_invoices_history')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "notifications_central_invoices_history` (
  `id` BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `invoice_id` int(11) NULL DEFAULT NULL,
  `status` 	tinyint(1) NULL DEFAULT 0,
  `date` DATE NULL DEFAULT NULL,
  `rel_id` varchar(255) DEFAULT NULL,
  `rel_type` varchar(255) DEFAULT NULL,
  `phonenumber` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=" . $CI->db->char_set . ";");
}


if (!$CI->db->table_exists(db_prefix() . 'central_notificacoes_mensagens_enviadas')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "central_notificacoes_mensagens_enviadas` (
  `id` BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `rel_id` varchar(255) DEFAULT NULL,
  `rel_type` varchar(255) DEFAULT NULL,
  `phonenumber` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `date` DATE NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=" . $CI->db->char_set . ";");
}

if (!$CI->db->table_exists(db_prefix() . 'central_notificacoes_lembretes')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "central_notificacoes_lembretes` (
  `id` BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `date` DATE NULL DEFAULT NULL,
  `created_by_staff_id` int(11) NULL DEFAULT NULL,
  `rel_id` varchar(255) NULL DEFAULT NULL,
  `rel_type` varchar(255) NULL DEFAULT NULL,
  `reminder_type` enum('pagamento_a_vencer','fatura_vencida','servico_suspenso','aviso_servico_suspenso','dia_do_vencimento','servico_pronto') NULL DEFAULT NULL,
  `status` 	tinyint(1) NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=" . $CI->db->char_set . ";");
}



if (!$CI->db->field_exists('cn_aviso_faturas_vencida_enviado_at', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `cn_aviso_faturas_vencida_enviado_at` TIMESTAMP NULL DEFAULT NULL;');
}

if (!$CI->db->field_exists('cn_aviso_faturas_vencida_qtd_tentativas', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `cn_aviso_faturas_vencida_qtd_tentativas` INT(11) NULL DEFAULT 0;');
}

if (!$CI->db->field_exists('cn_aviso_fatura_a_vencer_enviado_at', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `cn_aviso_fatura_a_vencer_enviado_at` TIMESTAMP NULL DEFAULT NULL;');
}

if (!$CI->db->field_exists('cn_aviso_fatura_a_vencer_qtd_tentativas', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `cn_aviso_fatura_a_vencer_qtd_tentativas` INT(11) NULL DEFAULT 0;');
}

if (!$CI->db->field_exists('cn_aviso_suspensao_enviado_at', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `cn_aviso_suspensao_enviado_at` TIMESTAMP NULL DEFAULT NULL;');
}

if (!$CI->db->field_exists('cn_aviso_suspensao_qtd_tentativas', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `cn_aviso_suspensao_qtd_tentativas` INT(11) NULL DEFAULT 0;');
}

if (!$CI->db->field_exists('cn_aviso_dia_do_vencimento_enviado_at', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `cn_aviso_dia_do_vencimento_enviado_at` TIMESTAMP NULL DEFAULT NULL;');
}

if (!$CI->db->field_exists('cn_aviso_dia_do_vencimento_qtd_tentativas', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `cn_aviso_dia_do_vencimento_qtd_tentativas` INT(11) NULL DEFAULT 0;');
}

if (!$CI->db->field_exists('cn_suspenso_enviado_at', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `cn_suspenso_enviado_at` TIMESTAMP NULL DEFAULT NULL;');
}

if (!$CI->db->field_exists('cn_suspenso_qtd_tentativas', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices` ADD `cn_suspenso_qtd_tentativas` INT(11) NULL DEFAULT 0;');
}

// Contacts
if (!$CI->db->field_exists('central_notificacao_contact_whatsapp', db_prefix() . 'contacts')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'contacts` ADD `central_notificacao_contact_whatsapp` tinyint(1) NULL DEFAULT 1;');
}

if (!$CI->db->table_exists(db_prefix() . 'notifications_zap_engine_instances')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "notifications_zap_engine_instances` (
  `id` BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `created_by_staff_id` int(11) NULL DEFAULT NULL,
  `instanceName` varchar(255) NULL DEFAULT NULL,
  `base_url` varchar(255) NULL DEFAULT NULL,
  `api_key` varchar(255) NULL DEFAULT NULL,
  `its_primary_server` TINYINT(1) NULL DEFAULT '0',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=" . $CI->db->char_set . ";");
}
