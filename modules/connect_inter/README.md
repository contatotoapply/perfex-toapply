# basic_module


# Guia de instalação do módulo

### Passo 1) Altere o nome da pasta principal

- Por exemplo: mv basic_module central_notificacao


### Passo 2) Dentro da raiz do projeto, existe um arquivo com o mesmo nome da pasta do módulo. <br>Altere para o mesmo nome que foi copiado.

Por exemplo: central_notificacao.php

### Passo 3)

Altere os dados de dentro do arquivo `central_notificacao.php`.

Conforme necessidade.

Detalhe: o nome desta função segue o padrão, isto é, o nome da pasta seguido de _module_action_links.
Além disso, a função deve iniciar, obrigatoriamente, com a palavra module_
Por exemplo:
```php

function module_central_notificacao_module_action_links($actions) {
    $actions[] = '<a href="' . admin_url(BASIC_MODULE_MODULE_NAME) . '"> Configurações</a>';
    return $actions;
}

```

Dúvida: o conteúdo do email, isto é, o emailtemplate, será o mesmo para todas as faturas? Tanto
para as faturas cujo método de pagamento é o banco inter, quanto para as demais faturas?

Verificar essa questão:
invoice_overdue em application/models/Cron_model.php
É esta função (invoice_overdue) que envia o email de cobrança para os clientes quando a fatura
não recorrente está atrasada.

1. Faturas NÃO recorrentes: application/models/Cron_model.php Linhas 1037 (método: invoice_overdue)


Problemas:
O problema era o limite.

## Sua fatura vence em 2 dias: # {invoice_number} / {invoice_duedate}
Slug: `invoice-due-notice`

# Alterar status de uma fatura para vencido
O modelo responsável por alterar o status de uma fatura para vencido, isto é, 4, é o model
Cron_model.php.
O método responsável por alterar o status de uma fatura para vencido é a `invoice_overdue`.
A função `update_invoice_status` altera o status para vencido, que é chamada pelo cronjob `application/helpers/invoices_helper.php`.

# Enviar email 1 dia após o vencimento nas funções core.
O método `invoice_overdue` é chamado pelo cronjob `application/models/Cron_model.php`.
Esse método é responsável por enviar o email (invoice_overdue_notice) 1 dia após o vencimento e a cada (verificar as opções:
automatically_send_invoice_overdue_reminder_after e automatically_resend_invoice_overdue_reminder_after)


# Faturas Recorrentes
Verificar por que eu desativei essa opção:
__(CRON) Enviar email dia vencimento dois dias após__

# Não está enviando
1. A coluna `email_cobranca_enviado_recorrente_at`
2. (CRON) Agendar e enviar email no dia vencimento
3. Sua fatura INV-0000401/03/2023 vence hoje

Os itens 1, 2 e 3 estão todos relacionados ao envio de email para faturas recorrentes.


# Email enviado no dia do vencimento:
1. Assunto do Email: Sua fatura INV-0000401/03/2023 vence hoje
2. Coluna `email_cobranca_enviado_dia_vencimento_at`
3. Função na raiz do projeto: `enviar_email_dia_vencimento_hoje`
5. Configurações do módulo: (CRON) Enviar email no dia vencimento (hoje)
6. `$CI->inter_library->enviarEmailCobrancaDiaVencimento();`

# Email enviado avisando suspensão:
1. Assunto: Pagamento Atrasado #Fatura INV-0000401/03/2023
2. Coluna: `pagamento_atrasado_email_enviado_at`
4. Configurações do módulo: Enviar email em caso de faturas recorrentes atrasadas
5. `$CI->pagamento_atrasado_faturas_recorrentes->agendarEmailFaturasRecorrentes();`


# Sua Fatura está vencida desde 21/03/2023 - #INV-0000184/03/2023
1. Sua Fatura está vencida desde 21/03/2023 - #INV-0000184/03/2023
2. Coluna: `last_overdue_reminder`
3. SQL Teste: SELECT * FROM `tblemailtemplates` WHERE subject LIKE "%Sua%Fatura%está%";
4. O método `invoice_overdue` é chamado pelo cronjob `application/models/Cron_model.php`
4. Função que envia o email: `$this->invoices_model->send_invoice_overdue_notice($invoice['id']);`

# Boleto sendo enviado no email
1. Sua fatura INV-0000184/03/2023 vence hoje - Okay
2. Pagamento Atrasado #Fatura INV-0000186/03/2023 - Okay

# Email enviado quando clicando em "Salvar e Enviar" na Fatura
1. Assunto: `Sua Fatura vence em 24/03/2023 - #INV-0000403/03/2023`
2. Método: `send_invoice_to_client` (application/models/Invoices_model.php)

# Sua fatura vence em 2 dias: # {invoice_number} / {invoice_duedate}
1. SQL ```SELECT * FROM `tblemailtemplates` WHERE slug = 'invoice-due-notice' AND language = 'portuguese_br';```
2. Método: `send_invoice_due_notice` | Arquivo:`(application/models/Invoices_model.php)`
3. O método `send_invoice_due_notice` é chamado em `application/models/Cron_model.php` na linha 1254 no método `invoice_due`

# TODO: 2023-07-03 15:02:20
- [x] - Alterar layout
- [x] - Criar pix com vencimento
- [x] - Preencher a chave pix nas configurações do módulo
- [x] - Criar lógica para verificar se foi pago pelo pix
- [x] - Criar chave nas configurações do módulo denomnada Criar Pix ao Criar a Fatura
- [x] - Criar pix ao criar a fatura
- [ ] - Enviar por email


# Configuração: Alterar o conteúdo do email, adicionando a chave {pix_copia_e_cola} e {pix_qrcode}
- Send Invoice to Customer
- Send Invoice to Customer
- Pagamento em Atraso
- Invoice Due Notice
- Invoice Overdue Notice

# 2023-11-22 09:28:50

UPDATE `tblcreditnotes` SET `status` = '1' WHERE `tblcreditnotes`.`id` = 1;
UPDATE `tblcreditnotes` SET `status` = '1' WHERE `tblcreditnotes`.`id` = 2;
UPDATE `tblinvoices` SET `status` = '1', subtotal = 10, total = 10 WHERE `tblinvoices`.`id` = 140;
truncate tblcredits;

$valor_float = 6;
$valor_formatado = number_format($valor_float, 2, '.', '');

echo $valor_formatado;  // Saída: 6.00


UPDATE `tblinvoices` SET `pagamento_atrasado_email_enviado_at` = NULL,
`email_cobranca_enviado_dia_vencimento_at` = NULL,
`email_cobranca_enviado_recorrente_at`=null,
`bi_next_mailing_day` = null,
`pagamento_atrasado_last_overdue_reminder` = null,
`aviso_suspensao_proximo_dia_util`=null,
`aviso_suspensao_enviado_at` = null;


UPDATE `tblinvoices` SET `status` = '1';

SELECT id, number, recurring, pagamento_atrasado_email_enviado_at,
pagamento_atrasado_last_overdue_reminder FROM `tblinvoices`;

SELECT * FROM `tbltracked_mails`;

```json
{
  "codigoSolicitacao": "8f8ad139-fbcc-4bad-8e16-58080f4a6016",
  "seuNumero": "FAT-001704",
  "situacao": "A_RECEBER",
  "dataHoraSituacao": "2024-09-30T19:29:22.684Z",
  "nossoNumero": "90071264603",
  "codigoBarras": "07794985700000009500001112095331990071264603",
  "linhaDigitavel": "07790001161209533199200712646033498570000000950",
  "txid": "131665491727713762000U35jsDoeGaqVIa",
  "pixCopiaECola": "00020101021226980014BR.GOV.BCB.PIX2576spi-qrcode.bancointer.com.br/spi/pj/v2/cobv/aa99da6d187a4b648943f8e6f2b01f6852040000530398654049.505802BR5901*6008ROLANDIA61088660152862070503***6304EE2E"
}
```


