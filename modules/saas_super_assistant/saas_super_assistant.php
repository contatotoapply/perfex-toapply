<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: SaaS Super Assistant Module
Description: Assistant module that allow you to set staff assistant for tenants.
Version: 1.0.1
Requires at least: 3.0.*
Author: ulutfa
Author URI: https://codecanyon.net/user/ulutfa
*/

defined('SAAS_SUPER_ASSISTANT_MODULE_NAME') or define('SAAS_SUPER_ASSISTANT_MODULE_NAME', 'saas_super_assistant');

if (!defined('PERFEX_SAAS_MODULE_NAME') || !function_exists('perfex_saas_is_tenant')) return;

$CI = &get_instance();

/**
 * Load the models
 */
$CI->load->model(SAAS_SUPER_ASSISTANT_MODULE_NAME . '/' . SAAS_SUPER_ASSISTANT_MODULE_NAME . '_model');

/**
 * Load the helpers
 */
$CI->load->helper(SAAS_SUPER_ASSISTANT_MODULE_NAME . '/' . SAAS_SUPER_ASSISTANT_MODULE_NAME);


/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(SAAS_SUPER_ASSISTANT_MODULE_NAME, [SAAS_SUPER_ASSISTANT_MODULE_NAME]);


/********************** EXIT IF TENANT *****************************/

if (perfex_saas_is_tenant()) {
    require_once(APP_MODULES_PATH . SAAS_SUPER_ASSISTANT_MODULE_NAME . '/hooks/assistant_instance_switch.php');
    return;
}




/**
 * Register activation module hook
 */
register_activation_hook(SAAS_SUPER_ASSISTANT_MODULE_NAME, function () {
    if (!defined('PERFEX_SAAS_MODULE_NAME'))
        show_error(SAAS_SUPER_ASSISTANT_MODULE_NAME . ' module requires Perfex SaaS module to be installed', 500, 'Module requirement not met');

    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
});

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
hooks()->add_action('admin_init', function () use ($CI) {

    // Add super assistance menu to the saas dropdown for admin
    if (is_admin()) {

        $CI->app_menu->add_sidebar_children_item(PERFEX_SAAS_MODULE_NAME, [
            'slug' => 'saas_super_assistant',
            'name' => _l('saas_super_assistant_menu'),
            'icon' => 'fa fa-user-tie',
            'href' => admin_url('saas_super_assistant'),
            'position' => 100,
        ]);
    }

    // Add tenant menu item for non admin staff with super assistance role
    if (saas_super_assistant_is_assistant()) {
        // @todo check for assistance role
        $CI->app_menu->add_sidebar_menu_item(SAAS_SUPER_ASSISTANT_MODULE_NAME, [
            'slug' => 'saas_super_assistant_client',
            'name' => _l('saas_super_assistant_staff_menu'),
            'icon' => 'fa fa-university',
            'href' => admin_url('saas_super_assistant/tenants'),
            'position' => 2,
        ]);
    }
}, PHP_INT_MAX);