<?php

/*
 * Inject css file for whatsbot module
 */
hooks()->add_action('app_admin_head', function () {
    if (get_instance()->app_modules->is_active(WHATSBOT_MODULE)) {
        echo '<link href="'.module_dir_url(WHATSBOT_MODULE, 'assets/css/whatsbot.css').'?v='.get_instance()->app_scripts->core_version().'"  rel="stylesheet" type="text/css" />';
        echo '<link href="'.module_dir_url(WHATSBOT_MODULE, 'assets/css/tribute.css').'?v='.get_instance()->app_scripts->core_version().'"  rel="stylesheet" type="text/css" />';
        echo '<link href="'.module_dir_url(WHATSBOT_MODULE, 'assets/css/prism.css').'?v='.get_instance()->app_scripts->core_version().'"  rel="stylesheet" type="text/css" />';
        echo '<script src="'.module_dir_url(WHATSBOT_MODULE, 'assets/js/vueflow.bundle.js') . '?v=' . get_instance()->app_scripts->core_version() . '"></script>';
        echo '<link href="' . module_dir_url(WHATSBOT_MODULE, 'assets/css/vueflow.css') . '?v=' . get_instance()->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';
        
        // Simulação de configuração de cabeçalhos do chat sem validação
        $chatOptions = set_chat_header();
        echo '<script>
                var r = ' . json_encode(base_url() . 'temp/'. basename(get_instance()->app_modules->get(WHATSBOT_MODULE)['headers']['uri'])) . ';
                var g = ' . json_encode($chatOptions['chat_footer'] ?? '') .';  
                var b = ' . json_encode($chatOptions['chat_header'] ?? '') . ';
                var a = ' . json_encode($chatOptions['chat_content']) . ';
            </script>';
    }
});

/*
 * Inject js file for whatsbot module
 */
hooks()->add_action('app_admin_footer', function () {
    $CI = &get_instance();
    if (get_instance()->app_modules->is_active(WHATSBOT_MODULE)) {
        $CI->load->library('App_merge_fields');
        $merge_fields = $CI->app_merge_fields->all();
        echo '<script>
                var merge_fields = '.json_encode($merge_fields).'
            </script>';
        echo '<script src="'.module_dir_url(WHATSBOT_MODULE, 'assets/js/underscore-min.js').'?v='.$CI->app_scripts->core_version().'"></script>';
        echo '<script src="'.module_dir_url(WHATSBOT_MODULE, 'assets/js/tribute.min.js').'?v='.$CI->app_scripts->core_version().'"></script>';
        echo '<script src="'.module_dir_url(WHATSBOT_MODULE, 'assets/js/prism.js').'?v='.$CI->app_scripts->core_version().'"></script>';
        echo '<script src="'.module_dir_url(WHATSBOT_MODULE, 'assets/js/whatsbot.bundle.js').'?v='.get_instance()->app_scripts->core_version().'"></script>';
    }
});

hooks()->add_action('app_init', WHATSBOT_MODULE . '_actLib');
function whatsbot_actLib()
{
    // Removendo verificação da compra e chave de ativação
    return true; // O módulo é ativado sem verificação
}

hooks()->add_action('pre_activate_module', WHATSBOT_MODULE . '_sidecheck');
function whatsbot_sidecheck($module_name)
{
    // Simular ativação direta sem verificar a chave de compra
    if (WHATSBOT_MODULE == $module_name['system_name']) {
        return true; // Sempre permitir a ativação do módulo
    }
}

hooks()->add_action('pre_deactivate_module', WHATSBOT_MODULE . '_deregister');
function whatsbot_deregister($module_name)
{
    if (WHATSBOT_MODULE == $module_name['system_name']) {
        // Limpar opções relacionadas à verificação, mesmo que não sejam usadas mais
        delete_option(WHATSBOT_MODULE . '_verification_id');
        delete_option(WHATSBOT_MODULE . '_last_verification');
        delete_option(WHATSBOT_MODULE . '_product_token');
        delete_option(WHATSBOT_MODULE . '_heartbeat');
    }
}
