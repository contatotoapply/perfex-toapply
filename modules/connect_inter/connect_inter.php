<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Connect Inter
Description: Gateway de recebimento Banco Inter para Perfex CRM
Author: Nando Cardoso - Connect Designers
Author URI: https://connectdesigners.com.br
Version: 1.0.9
Requires at least: 2.3.*
*/

require FCPATH . "modules/connect_inter/vendor/autoload.php";

use chillerlan\QRCode\QRCode;

define('CONNECT_INTER_MODULE_NAME', 'connect_inter');
define('CONNECT_INTER_MODULE_NAME_UPLOADS_FOLDER', FCPATH . 'uploads/' . CONNECT_INTER_MODULE_NAME . '/');


hooks()->add_filter('module_connect_inter_action_links', 'module_connect_inter_action_links');
hooks()->add_action('before_invoice_deleted', 'bi_invoice_marked_as_cancelled_deleted');
hooks()->add_action('invoice_marked_as_cancelled', 'bi_invoice_marked_as_cancelled_deleted');
hooks()->add_action('invoice_updated', 'connect_inter_atualizar_boleto');
hooks()->add_action('after_invoice_added', 'gerar_boleto_apos_invoice_added');

$CI = &get_instance();

$CI->load->helper(CONNECT_INTER_MODULE_NAME . '/connect_inter');

register_payment_gateway('gateways/inter_gateway', CONNECT_INTER_MODULE_NAME);
register_payment_gateway('gateways/banco_interpix_gateway', CONNECT_INTER_MODULE_NAME);
register_activation_hook(CONNECT_INTER_MODULE_NAME, 'modulo_demo_module_activation_hook');
register_language_files(CONNECT_INTER_MODULE_NAME, [CONNECT_INTER_MODULE_NAME]);

function modulo_demo_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

function module_connect_inter_action_links($actions)
{
    $actions[] = '<a href="' . admin_url('settings?group=payment_gateways&tab=online_payments_connect_inter_tab') . '"> Configurações</a>';
    return $actions;
}

function gerar_boleto_apos_invoice_added($invoice_id)
{
    if (
        get_option('paymentmethod_connect_inter_gerar_boleto_apos_criar_fatura')
        && get_option('paymentmethod_connect_inter_active')
    ) {

        log_activity('[BANCO INTER V3] - Gerar boleto após criar fatura..');

        $CI = &get_instance();

        $invoice = $CI->invoices_model->get($invoice_id);

        $allowed_payment_modes = unserialize($invoice->allowed_payment_modes);

        if ((in_array(CONNECT_INTER_MODULE_NAME, $allowed_payment_modes)
            || in_array(CONNECT_INTER_MODULE_NAME . '_pix', $allowed_payment_modes)
        ) && $invoice->status != 6) {

            if (
                in_array(CONNECT_INTER_MODULE_NAME, $allowed_payment_modes) &&
                get_option('paymentmethod_connect_inter_is_installment')
            ) {
                bancoInterPrepararFatura($invoice_id);
            }

            if (get_option('paymentmethod_connect_inter_criar_pixao_criar_fatura')) {
                connect_inter_emitir_cobranca($invoice, 'criacao');
            }
        }
    }
}

function connect_inter_atualizar_boleto($data)
{
    if (
        get_option('paymentmethod_connect_inter_gerar_boleto_apos_criar_fatura')
        && get_option('paymentmethod_connect_inter_active')
    ) {

        $CI = &get_instance();

        $CI->load->library('connect_inter/inter_library');

        $invoice_id = $data['id'];

        $updated = $data['updated'];

        if (!$updated) {
            return;
        }

        $invoice               = $CI->invoices_model->get($invoice_id);

        $allowed_payment_modes = unserialize($invoice->allowed_payment_modes);

        if ((
            in_array(CONNECT_INTER_MODULE_NAME, $allowed_payment_modes)
            || in_array(CONNECT_INTER_MODULE_NAME . '_pix', $allowed_payment_modes)
        ) && $invoice->status != 6) {

            if (get_option('paymentmethod_connect_inter_criar_pixao_criar_fatura')) {

                if ($invoice->banco_inter_dados_cobranca) {

                    $cobranca = json_decode($invoice->banco_inter_dados_cobranca);

                    if (isset($cobranca->codigoSolicitacao)) {

                        $codigoSolicitacao = $cobranca->codigoSolicitacao;

                        $CI->load->library("connect_inter/banco_inter_v3_library");

                        $CI->load->library('connect_inter/notificar_contato_fatura_excluida');

                        $CI->banco_inter_v3_library->cancelarBoleto($codigoSolicitacao);

                        connect_inter_emitir_cobranca($invoice, 'criacao');
                    } else {
                        connect_inter_emitir_cobranca($invoice, 'criacao');
                    }
                } else {
                    connect_inter_emitir_cobranca($invoice, 'criacao');
                }
            }
        }
    }
}

function bi_invoice_marked_as_cancelled_deleted($invoiceid)
{
    if (
        get_option('paymentmethod_connect_inter_permitir_cancelar_boleto_na_api')
        && get_option('paymentmethod_connect_inter_active')
    ) {
        $CI = &get_instance();

        $CI->load->model("invoices_model");

        $invoice = $CI->invoices_model->get($invoiceid);

        $allowed_payment_modes = unserialize($invoice->allowed_payment_modes);

        if (!in_array(CONNECT_INTER_MODULE_NAME, $allowed_payment_modes)) {
            return;
        }

        if ($invoice->banco_inter_dados_cobranca) {

            $cobranca = json_decode($invoice->banco_inter_dados_cobranca);

            if (isset($cobranca->codigoSolicitacao) && !$invoice->status != 5) {

                $codigoSolicitacao = $cobranca->codigoSolicitacao;

                $CI->load->library("connect_inter/banco_inter_v3_library");

                $response = $CI->banco_inter_v3_library->cancelarBoleto($codigoSolicitacao);

                if ($response) {
                    $invoice = $CI->invoices_model->log_invoice_activity($invoiceid, '[BANCO INTER V3] - Boleto cancelado com sucesso.');
                }
            }
        }
    }
}

hooks()->add_action('connect_inter_invoice_credits_applied', 'connect_inter_invoice_credits_applied');

function connect_inter_invoice_credits_applied($data)
{
    $CI = &get_instance();

    $CI->load->model('invoices_model');

    $CI->load->library('connect_inter/inter_library');

    $invoice_id        = $data['invoice_id'];

    $total_left_amount = $data['total_amount'];

    $invoice           = $CI->invoices_model->get($invoice_id);

    $total             = floatval($invoice->total - $total_left_amount);

    $hash              = app_generate_hash();

    $data_updated = [
        'hash'                           => $hash,
        'banco_inter_codigo_solicitacao' => null,
    ];

    $allowed_payment_modes = unserialize($invoice->allowed_payment_modes);

    if (in_array(CONNECT_INTER_MODULE_NAME, $allowed_payment_modes)) {

        if ($total >= 5) { // 5, porque é o valor mínimo para gerar um boleto

            $CI->db->where('id', $invoice_id)
                ->update(db_prefix() . 'invoices', $data_updated);

            if ($invoice->banco_inter_codigo_solicitacao) {
                $CI->load->library("connect_inter/banco_inter_v3_library");
                $CI->banco_inter_v3_library->cancelarBoleto($invoice->codigoSolicitacao);
            }

            $invoice->hash     = $hash;

            $invoice->total    = number_format($total, 2, '.', '');

            $invoice->total           = $total;

            $invoice->banco_inter_codigo_solicitacao = null;

            if (get_option('paymentmethod_connect_inter_criar_pixao_criar_fatura')) {
                banco_inter_emitir_cobranca($invoice, 'criacao');
            }
        }

        log_activity('[BANCO INTER V3] - Créditos aplicados: ' . json_encode($data));
    }
}

hooks()->add_filter('invoice_merge_fields', function ($fields, $args) {
    $CI = &get_instance();
    $invoice = $args['invoice'];
    $invoiceid                  = $invoice->id;
    $hash                       = $invoice->hash;
    $media_folder               = $CI->app->get_media_folder();
    $cora_image_qrcode          = base_url($media_folder . "/connect_inter/invoices/invoice_{$invoiceid}_{$hash}_qrcode.png");
    $fields['{pix_qrcode}']     = $cora_image_qrcode;
    $fields['%7Bpix_qrcode%7D'] = $cora_image_qrcode;
    return $fields;
}, 8, 2);


hooks()->add_action('admin_init', 'modulo_connect_inter_module_init_menu_items');

function modulo_connect_inter_module_init_menu_items()
{
    if (is_admin()) {
        $CI = &get_instance();

        $CI->app_menu->add_setup_menu_item('connect-inter-id', [
            'slug' => 'connect_inter_settings',
            'name' => _l('connect_inter'),
            'position' => 10,
            'icon' => 'fa fa-cogs',
        ]);

        $CI->app_menu->add_setup_children_item('connect-inter-id', [
            'slug' => 'connect_inter_settings',
            'name' => 'Configurações de arquivos',
            'href' => admin_url('connect_inter/v3/settings'),
            'position' => 24,
        ]);

        $CI->app_menu->add_setup_children_item('connect-inter-id', [
            'slug' => 'connect_inter_settings_webhooks',
            'name' => 'Configurações de webhooks',
            'href' => admin_url('connect_inter/v3/webhooks/create'),
            'position' => 24,
        ]);
    }
}

hooks()->add_action('before_render_payment_gateway_settings', function ($gateway) {
    if ($gateway['id'] == CONNECT_INTER_MODULE_NAME) {
        connect_inter_certs_exists();
        echo '<a href="' . admin_url('connect_inter/v3/webhooks/create') . '">Configurar Webhook.</a>';
    }
});

hooks()->add_action('after_right_panel_invoicehtml', function ($invoice) {

    $allowed_payment_modes = unserialize($invoice->allowed_payment_modes);


    if (in_array(CONNECT_INTER_MODULE_NAME . '_pix', $allowed_payment_modes)) {

        if ($invoice->banco_inter_dados_cobranca  && $invoice->status != 2) {
            $dados_cobranca = json_decode($invoice->banco_inter_dados_cobranca);
            $pix            = $dados_cobranca->pix;
            $pixCopiaECola  = $pix->pixCopiaECola;
            $qrcode_image   = (new QRCode())->render($pixCopiaECola);
?>
            <style>
                /*Esconder na versão mobile o elemento: #versao-mobile */
                @media (max-width: 768px) {
                    #versao-mobile {
                        display: none !important;
                    }
                }
            </style>

            <script>
                function verificarPagamento() {
                    const invoiceId = "<?= $invoice->id ?>";
                    const invoiceHash = "<?= $invoice->hash ?>";
                    let invoiceStatus = "<?= $invoice->status ?>";
                    let intervalId;
                    if (invoiceStatus != 2) {
                        intervalId = setInterval(() => {
                            $.ajax({
                                'url': `/connect_inter/invoice/${invoiceId}/${invoiceHash}/verificar_pagamento_efetuado`
                            }).done(function(data) {
                                if (data == 1) {
                                    window.location.reload();
                                }
                            });
                        }, 3000);
                    }
                }

                document.addEventListener("DOMContentLoaded", (event) => {

                    const qrCodeImage = '<?= $qrcode_image ?>';

                    $(".col-md-12.invoice-html-payments").append(`
                    <div class="row">
                        <div class="col-md-12">
                            <h3><b>Pagamento por PIX</b></h3>
                            <div class="copy-link input-group pt-5" style="margin-top: 15px;" id="pix-copy">
                                <input type="text" class="copy-link-input form-control" value="<?= $pixCopiaECola ?>" readonly>
                                <span class="copy-link-button input-group-addon" style="cursor: pointer;">
                                <i class="fa-regular fa-copy"></i> Copiar
                                </span>
                            </div>
                             <div style="margin-top:15px;"></div>
                        </div>

                        <div class="row" id="versao-mobile" style="display: flex; align-items: center;">
                            <div class="col-md-6 instrucoes-img"><img height="250" src="${window.location.origin + '/modules/connect_inter/assets/img/instrucao-pix.png'}"></div>
                            <div class="col-md-6 text-right"><img  height="250" src="${qrCodeImage}"></div>
                        </div>

                    </div> <br/><br/>`);

                    $('.copy-link-button').on('click', function() {
                        const inputField = $(".copy-link-input");
                        const copy = document.createElement("textarea");
                        document.body.appendChild(copy);
                        copy.value = inputField.val()
                        copy.select();
                        document.execCommand("copy");
                        copy.setSelectionRange(0, 0, "none");
                        document.body.removeChild(copy);
                        alert_float("success", 'Copiado com sucesso!');
                    });

                });

                verificarPagamento();
            </script>
        <?php
        }
    }

    if (in_array(CONNECT_INTER_MODULE_NAME, $allowed_payment_modes)) {

        if ($invoice->banco_inter_dados_cobranca && $invoice->status != 2) {

            $dados_cobranca = json_decode($invoice->banco_inter_dados_cobranca);

        ?>

            <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>

            <script>
                function copyLinhaDigital(linhaDigitavel) {
                    const copy = document.createElement("textarea");
                    document.body.appendChild(copy);
                    copy.value = linhaDigitavel;
                    copy.select();
                    document.execCommand("copy");
                    copy.setSelectionRange(0, 0, "none");
                    document.body.removeChild(copy);
                    alert_float("success", 'Copiado com sucesso!');
                }

                function verificarPagamento() {
                    const invoiceId = "<?= $invoice->id ?>";
                    const invoiceHash = "<?= $invoice->hash ?>";
                    let invoiceStatus = "<?= $invoice->status ?>";
                    let intervalId;
                    if (invoiceStatus != 2) {
                        intervalId = setInterval(() => {
                            $.ajax({
                                'url': `/connect_inter/invoice/${invoiceId}/${invoiceHash}/verificar_pagamento_efetuado`
                            }).done(function(data) {
                                if (data == '1') {
                                    window.location.reload();
                                }
                            });
                        }, 3000);
                    }
                }

                document.addEventListener("DOMContentLoaded", (event) => {

                    const linhaDigitavel = '<?= $dados_cobranca->boleto->linhaDigitavel ?>';
                    $(".col-md-12.invoice-html-payments").append(`<div class="boleto-dados"
                    style="display:flex;mn;flex-direction: column;align-items:center">

                    <h4>Dados do boleto</h4>

                    <div style="display:flex"><canvas id="barcode" style="margin:0px auto;"></canvas></div></div>

                    <div>
                        <div class="text-center">
                            <div class="form-group" app-field-wrapper="name-input">
                            <label class="field-label">Linha Digitável</label>
                                <div class="input-group">
                                    <input type="number" value="${linhaDigitavel}"
                                     name="amount" class="form-control text-center" readonly  />
                                    <span class="btn btn-sm input-group-addon" onclick="copyLinhaDigital('${linhaDigitavel}')"><i class="fa fa-copy"></i> Copiar código</span>
                                </div>
                            </div>
                        </div>
                    </div>

                     <div class="row">
                        <div class="col-md-12">
                            <div class="mtop15 mbot15" style="display:flex;gap:10px; flex-direction:row;">
                                <a style="width:100%;" href="<?= base_url("connect_inter/invoice/{$invoice->id}/{$invoice->hash}/baixar"); ?>"
                                    class="btn btn-primary">Baixar boleto</a>
                                <a style="width:100%;" href="<?= base_url("connect_inter/invoice/{$invoice->id}/{$invoice->hash}/imprimir"); ?>"
                                    class="btn btn-warning">Visualizar boleto</a>
                            </div>
                        </div>
                    </div>
                    `);

                    $("#barcode").JsBarcode("<?= $dados_cobranca->boleto->codigoBarras ?>", {
                        format: "ITF",
                        displayValue: false
                    });
                });

                verificarPagamento();
            </script>
<?php
        }
    }
});
