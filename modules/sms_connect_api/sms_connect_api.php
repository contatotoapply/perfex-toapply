<?php

/**
 * Ensures that the module init file can't be accessed directly, only within the application.
 */

defined('BASEPATH') or exit('No direct script access allowed');

require FCPATH . "modules/sms_connect_api/libraries/Abstract_lembrete.php";

/*
Module Name: Connect API
Description: Módulo para enviar Whatsapp via API
Author: Connect Designers / Nando Cardoso
Author URI: https://connectdesigners.com.br
Version: 1.0.3
Requires at least: 3.0.*
*/

define('SMS_CONNECT_API_MODULE_NAME', 'sms_connect_api');

hooks()->add_action('admin_init', 'sms_notifications_zap_engine_permissions');
hooks()->add_action('after_contact_modal_content_loaded', 'add_option_enviar_mensage_whatsapp');
hooks()->add_action('admin_init', 'smsZapEnginePerfexModuleInitMenuItems');
hooks()->add_action('admin_init', 'sms_notifications_zap_engine_module_init_menu_items');

register_cron_task('task_enviar_lembrete_faturas_a_vencer');
register_cron_task('task_enviar_no_dia_do_vencimento');
register_cron_task('task_enviar_lembretes_faturas_vencidas');
register_cron_task('task_enviar_aviso_suspensao');
register_cron_task('task_servicos_suspensos');

hooks()->add_filter('sms_gateways', 'notifications_zap_engine_get_sms_gateways');
hooks()->add_filter('sms_gateway_available_triggers', 'mds_sms_trigger_other');
hooks()->add_filter('before_update_contact', 'update_before_update_contact');
hooks()->add_filter('before_create_contact', 'update_before_update_contact');
hooks()->add_filter('module_notifications_zap_engine_action_links', 'module_notifications_zap_engine_action_links');
hooks()->add_filter('module_sms_connect_api_action_links', 'module_sms_connect_api_action_links');


register_activation_hook(SMS_CONNECT_API_MODULE_NAME, 'notifications_zap_engine_module_activation_hook');

// Register language files, must be registered if the module is using languages
register_language_files(SMS_CONNECT_API_MODULE_NAME, [SMS_CONNECT_API_MODULE_NAME]);

// Load module helper file
$CI = &get_instance();
$CI->load->helper(SMS_CONNECT_API_MODULE_NAME . '/sms_connect_api');

/**
 * @return [type]
 */
function task_enviar_lembrete_faturas_a_vencer()
{
    $CI = &get_instance();
    $CI->load->library("sms_connect_api/enviar_lembrete_faturas_a_vencer");
    $CI->enviar_lembrete_faturas_a_vencer->enviar_lembrete();
}

/**
 * @return [type]
 */
function task_enviar_no_dia_do_vencimento()
{
    $CI = &get_instance();
    $CI->load->library("sms_connect_api/enviar_no_dia_do_vencimento");
    $CI->enviar_no_dia_do_vencimento->enviar_lembrete();
}


/**
 * @return [type]
 */
function task_enviar_lembretes_faturas_vencidas()
{
    $CI = &get_instance();
    $CI->load->library("sms_connect_api/enviar_lembrete_faturas_vencidas");
    $CI->enviar_lembrete_faturas_vencidas->enviar_lembrete();
}

/**
 * @return [type]
 */
function task_enviar_aviso_suspensao()
{
    $CI = &get_instance();
    $CI->load->library("sms_connect_api/enviar_aviso_suspensao");
    $CI->enviar_aviso_suspensao->enviar_lembrete();
}

/**
 * @return [type]
 */
function task_servicos_suspensos()
{
    $CI = &get_instance();
    $CI->load->library("sms_connect_api/enviar_servico_suspenso");
    $CI->enviar_servico_suspenso->enviar_lembrete();
}

function notifications_zap_engine_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}


/**
 * Add additional settings for this module in the module list area
 * @param  array $actions current actions
 * @return array
 */
function module_notifications_zap_engine_action_links($actions)
{
    $actions[] = '<a href="' . admin_url('settings?group=sms') . '">Configurações</a>';
    return $actions;
}

function notifications_zap_engine_get_sms_gateways($gateways)
{

    $CI = &get_instance();

    $CI->app_sms->add_gateway('notifications_zap_engine_library', [
        'name' => 'Configuração Evolution API (Whatsapp)',
        'info' => '',
        'options' => [
            [
                'name' => 'zap_engine_url',
                'label' => 'URL do Servidor',
            ],
            [
                'name' => 'zap_engine_token',
                'label' => 'Api Key Global',
            ],
            [
                'name'  => 'whatsapp_api_instance_name_selected',
                'label' => 'sms_zap_instance_name'
            ],
            [
                'name' => 'qtd_dias_para_avisar_lembrete_vencimento',
                'label' => 'Enviar lembrete X dias antes do vencimento. <span class="label label-warning"></span>  <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Será enviao o lembrete quando o número for a diferença entre duedate e a data atual." data-original-title="" title=""></i>',
            ],
            [
                'name' => 'qtd_dias_de_lembretes',
                'label' => 'Enviar lembrete X dias após o vencimento. <span class="label label-warning"></span> <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Será enviado lembrete X dias após o vencimento. Por exemplo: se for 3, enviará três vezes para o usuário." data-original-title="" title=""></i>',
            ],
            [
                'name' => 'qtd_dias_para_enviar_lembrete_suspensao',
                'label' => 'Enviar lembrete X dias APÓS O vencimento sobre serviço suspenso. <span class="label label-warning"></span>  <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Será enviao o lembrete quando o número for a diferença entre duedate e a data atual." data-original-title="" title=""></i>',
            ],
            [
                'name' => 'qtd_dias_para_suspender_servicos',
                'label' => 'Enviar lembrete e suspender serviços após X dias.  <span class="label label-warning"></span>  <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Será enviao o lembrete quando o número for a diferença entre duedate e a data atual." data-original-title="" title=""></i>',
            ]
        ]
    ]);

    $CI->app_sms->add_gateway('mpwa_gateway', [
        'name' => _l('mpwa_gateway_api_name'),
        'info' => '',
        'options' => [
            [
                'name' => 'mpwa_gateway_url_base',
                'label' => _l('mpwa_gateway_url_base')
            ],
            [
                'name' => 'mpwa_gateway_api_key',
                'label' => _l('mpwa_gateway_api_key')
            ],
            [
                'name' => 'mpwa_gateway_api_sender',
                'label' => _l('mpwa_gateway_api_sender'),
            ]
        ]
    ]);

    $CI->app_sms->add_gateway('connect_whaticket', [
        'name' => _l('connect_whaticket_api_name'),
        'info'    => '<p>OS CONTACTOS devem estar no formato internacional sem o +</p><hr class="hr-10" />',
        'options' => [

            [
                'name'  => 'endpoint',
                'label' => 'URL da API',
            ],

            [
                'name'  => 'authorized',
                'label' => 'API Key',
            ]
        ],
    ]);


    array_push($gateways, "sms_connect_api/sms_notifications_zap_engine_library");
    array_push($gateways, "sms_connect_api/sms_mpwa_gateway");
    array_push($gateways, "sms_connect_api/sms_connect_whaticket");
    return $gateways;
}

function update_before_update_contact($data)
{
    $data['central_notificacao_contact_whatsapp']   = isset($_POST['central_notificacao_contact_whatsapp']) ? 1 : 0;
    return $data;
}


function add_option_enviar_mensage_whatsapp()
{
?>
    <script>
        window.contactId = $('input[name="contactid"]').val();

        $(document).ready(function() {

            const html = `<div class="col-md-6 row">
            <div class="row">
                <div class="col-md-6 mtop10 border-right">
                    <span>Whatsapp</span>
                </div>
                <div class="col-md-6 mtop10">
                    <div class="onoffswitch">
                        <input type="checkbox" id="central_notificacao_contact_whatsapp" class="onoffswitch-checkbox" name="central_notificacao_contact_whatsapp">
                        <label class="onoffswitch-label" for="central_notificacao_contact_whatsapp"></label>
                    </div>
                </div>
            </div>
        </div>`;

            $("#contact_email_notifications").append(html);

            $('#contact').on('shown.bs.modal', function() {
                if (window.contactId) {
                    // Ação a ser executada ao abrir o modal
                    $.ajax(
                        admin_url + 'sms_connect_api/contact/' + window.contactId
                    ).done(function(data) {
                        if (data.central_notificacao_contact_whatsapp == 1) {
                            $('#central_notificacao_contact_whatsapp').prop('checked', true);
                        }
                    })
                }
            });
        })
    </script>
    <?php
}

hooks()->add_action('after_invoice_preview_template_rendered', function ($invoice) {

    $CI = &get_instance();
    // Get the REQUEST_URI from $_SERVER
    $requestUri = $_SERVER['REQUEST_URI'];

    // Parse the URL
    $parse = parse_url($requestUri);

    // Define the regular expression pattern to match the format
    $pattern = "/^\/admin\/invoices\/get_invoice_data_ajax\/\d+$/";

    if (preg_match($pattern, $parse['path']) && isset($invoice->status) && $invoice->status != 2) {

        $invoices = $CI->db
            ->select('reminder_type')
            ->where('rel_id', $invoice->id)
            ->where('rel_type', 'invoice')->get(db_prefix() . 'central_notificacoes_lembretes')
            ->result_array();

        $invoices         = array_column($invoices, 'reminder_type');
        $pagamentoAVencer = in_array('pagamento_a_vencer', $invoices);
        $faturaVencida    = in_array('fatura_vencida', $invoices);
        $servicoSuspenso  = in_array('servico_suspenso', $invoices);

    ?>
        <script>
            var isPagamentoAVencer = +'<?= $pagamentoAVencer ?? false ?>';
            var isFaturaVencida = +'<?= $faturaVencida  ?? false ?>';
            var isServicoSuspenso = +'<?= $servicoSuspenso ?? false ?>';

            var avisoLembretePagamento = `<li>
      <a
        href="${admin_url}sms_connect_api/lembretes/enviarLembreteDePagamento?invoice_id=${<?= $invoice->id ?>}"
        >Enviar Lembrete de Pagamento</a
      >
    </li>`;

            var avisoFaturaVencida = `<li>
      <a
        href="${admin_url}sms_connect_api/lembretes/enviarFaturaVencida?invoice_id=${<?= $invoice->id ?>}"
        >Enviar Fatura Vencida</a
      >
    </li>`;

            var avisoServicoSuspenso = `<li>
      <a href="${admin_url}sms_connect_api/lembretes/enviarServicoSuspenso?invoice_id=${<?= $invoice->id ?>}">Enviar Serviço Suspenso</a>
    </li>`;

            var $buttons = `<div class="btn-group">
  <button
    type="button"
    class="btn btn-default pull-left dropdown-toggle"
    data-toggle="dropdown"
    aria-haspopup="true"
    aria-expanded="false"
  >
    Mais <span class="caret"></span>
  </button>
  <ul class="dropdown-menu dropdown-menu-right">`;
            $buttons += avisoLembretePagamento;
            $buttons += avisoFaturaVencida;
            $buttons += avisoServicoSuspenso;
            $buttons += `</ul>
</div>
`;

            $('._buttons').prepend($buttons);
        </script>
    <?php
    }
});


/**
 * Add additional settings for this module in the module list area
 * @param  array $actions current actions
 * @return array
 */
function mds_sms_trigger_other($data)
{

    $proposal_merge_fields = [
        '{invoice_id}',
        '{contact_firstname}',
        '{invoice_link}',
        '{invoice_number}',
        '{invoice_duedate}',
        '{invoice_total}',
        '{phonenumber}',
        '{companyname}',
        '{empresa}',
        '{cliente_email}',
        '{qtd_dias_vencida}',
        '{total_dias_ate_vencimento}'
    ];

    $data['notifications_zap_engine_lembrete_pagamento'] = [
        'merge_fields' => $proposal_merge_fields,
        'label'        => 'Lembrete de Faturas a Vencer (ZAP)',
        'info'         => 'Este texto é enviado para o cliente quando a fatura ainda não está vencida. <span class="label label-warning"></span>',
    ];

    $data['notifications_zap_engine_enviar_no_dia_do_vencimento'] = [
        'merge_fields' => $proposal_merge_fields,
        'label'        => 'Lembrete no dia do vencimento (ZAP)',
        'info'         => 'Este texto é enviado para o cliente do dia que a fatura venceu. <span class="label label-warning"></span>',
    ];

    $data['notifications_zap_engine_faturas_atrasadas'] = [
        'merge_fields' => $proposal_merge_fields,
        'label'        => 'Lembrete de faturas Vencidas (ZAP)',
        'info'         => 'Este texto é enviado para o cliente no próximo dia após o vencimento. <span class="label label-warning"></span> ',
    ];

    $data['notifications_zap_engine_servico_suspenso'] = [
        'merge_fields' => $proposal_merge_fields,
        'label'        => 'Lembrete de Suspensão de Serviços (ZAP)',
        'info'         => 'Este texto é enviado para o cliente quando avisando que o serviço <b>SERÁ</b> suspenso. <span class="label label-warning"></span>',
    ];

    $data['notifications_zap_engine_servicos_suspensos'] = [
        'merge_fields' => $proposal_merge_fields,
        'label'        => 'Enviar no dia da suspensão',
        'info'         => 'Este texto é enviado para o cliente no ato da suspensão. <span class="label label-warning"></span>',
    ];

    return $data;
}


hooks()->add_filter('client_filtered_visible_tabs', function ($tab) {

    $CI = &get_instance();

    $requestUri = $_SERVER['REQUEST_URI'];

    $parse = parse_url($requestUri);

    $url = $parse['path'];

    if (preg_match('/(\d+)$/', $url, $matches)) {
        $cientid = $matches[1]; // O ID estará em $matches[1]
        $total_lembretes = $CI->db->select('t1.*')
            ->from(db_prefix() . 'central_notificacoes_lembretes t1')
            ->where('reminder_type', 'servico_pronto')
            ->where('t1.rel_id', $cientid)->count_all_results();
        // $tab['servico-pronto'] = [
        //     'name' => _l('Serviço Pronto'),
        //       'badge' => [
        //           'value' => $total_lembretes,
        //           'color' => 'bg-primary',
        //           'type'  => 'bg-primary',
        //       ],
        //     'icon' => 'fa fa-tasks',
        //   ];
    }

    return $tab;
});

function smsZapEnginePerfexModuleInitMenuItems()
{
    /*
    $CI = &get_instance();
    if (is_admin()) {
        $data = [
                   'slug' => 'servico-pronto-slug',
                   'name' => _l('Serviço Pronto'),
                   'view' => SMS_CONNECT_API_MODULE_NAME . '/servico_pronto',
                   'position' => 5,
                   'icon' => 'fa fa-tasks',
               ];
        $CI->app_tabs->add_customer_profile_tab('servico-pronto', $data);
    }
    */
}

hooks()->add_action('app_admin_head', function () {
    ?>
    <style>
        [app-field-wrapper="settings[sms_notifications_zap_engine_library_zap_engine_token]"] {
            display: none;
        }
    </style>

    <?php
});

hooks()->add_action('app_admin_footer', function () {
    if (is_admin()) {
        $CI = &get_instance();
        $CI->load->model('sms_connect_api/instance_model');
        $all_instances = $CI->instance_model->all('id, instanceName');
    ?>
        <div class="modal fade" id="modalSelectInstance" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <?php echo form_open(admin_url('leads/source')); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            <span class="edit-title"><?php echo _l('Selecionar Instância Ativa'); ?></span>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <select id="mainInstance" name="mainInstance" data-live-search="true" data-width="100%" class="selectpicker ajax-search">
                                    <option>Selecione</option>
                                    <?php
                                    foreach ($all_instances as $instance) {
                                    ?>
                                        <option value="<?php echo $instance->id; ?>"><?php echo $instance->instanceName; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    </div>
                </div>
                <!-- /.modal-content -->
                <?php echo form_close(); ?>
            </div>
        </div>
        <script>
            const adminUrlGerenciarInstancias = admin_url + "sms_connect_api/instances";
            let contentHtmlWhatsapp = `<br/><div><a href="${adminUrlGerenciarInstancias}" class="btn btn-default" >Gerenciar Instâncias</a> `;
            $('#sms_test_response').after(contentHtmlWhatsapp);

            // to save database change instance
            $('#mainInstance').on('change', function() {
                const instanceId = $(this).val();

                const instanceName = $(this).find('option:selected').text();

                $.ajax({
                    url: admin_url + 'sms_connect_api/instances/update_instance_name',
                    type: 'POST',
                    data: {
                        id: instanceId,
                        instance_name: instanceName
                    },
                    success: function(data) {
                        if (data.success) {
                            window.location.reload();
                        }
                    }
                })
            });

            $(document).ready(function() {
                var campo = document.querySelector("input[name='settings[sms_notifications_zap_engine_library_zap_engine_token]']");
                campo.type = 'password';

                var campo = document.querySelector("input[name='settings[sms_notifications_zap_engine_library_whatsapp_api_instance_name_selected]']");
                campo.disabled = true;

                var divApiKey = document.querySelector('[app-field-wrapper="settings[sms_notifications_zap_engine_library_zap_engine_token]"]');
                divApiKey.style.display = 'block';
            })
        </script>
<?php
    }
});


hooks()->add_action('module_activated', function ($module) {
    $CI = &get_instance();
    if ($module['system_name'] == 'sms_connect_api') {
        redirect('admin/settings?group=sms');
    }
});


function sms_notifications_zap_engine_permissions()
{
    $capabilities = [];
    $capabilities['capabilities'] = [
        'view_own' => _l('permission_view_own'),
        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    if (function_exists('register_staff_capabilities')) {
        register_staff_capabilities('sms_connect_api', $capabilities, _l('sms_connect_api'));
    }
}

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function sms_notifications_zap_engine_module_init_menu_items()
{
    $CI = &get_instance();

    if (
        staff_can('view_own', 'sms_connect_api')
        || staff_can('view', 'sms_connect_api')
    ) {

        $CI->app_menu->add_sidebar_menu_item('gerador_documentos_id', [
            'slug' => 'central_documentos_estaduais_novodocumento',
            'name' => 'sms_notifications_zap_engine_short',
            'position' => 10,
            'icon' => 'fa fa-envelope'
        ]);

        $CI->app_menu->add_sidebar_children_item('gerador_documentos_id', [
            'slug' => 'central_documentos_estaduais_listagem',
            'name' => 'sms_zap_instances',
            'href' => admin_url('sms_connect_api/instances'),
            'position' => 24,
        ]);
    }
}

function module_sms_connect_api_action_links($actions)
{
    $actions[] = '<a href="' . admin_url('settings?group=sms') . '">' . _l('mpwa_gateway_api_settings') . '</a>';
    return $actions;
}
