<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: DataPulse
Description: DataPulse for Perfex CRM: Instantly access vital charts, stats, and insights on your dashboard. Drive smarter decisions effortlessly.
Version: 1.0.0
Author: LenzCreative
Author URI: https://codecanyon.net/user/lenzcreativee/portfolio
Requires at least: 1.0.*
*/

define('DATAPULSE_MODULE_NAME', 'datapulse');

hooks()->add_action('admin_init', 'datapulse_module_init_menu_items');
hooks()->add_action('admin_init', 'datapulse_permissions');
hooks()->add_action('datapulse_init', DATAPULSE_MODULE_NAME . '_appint');
hooks()->add_action('pre_activate_module', DATAPULSE_MODULE_NAME . '_preactivate');
hooks()->add_action('pre_deactivate_module', DATAPULSE_MODULE_NAME . '_predeactivate');
hooks()->add_action('pre_uninstall_module', DATAPULSE_MODULE_NAME . '_uninstall');

/**
 * Load the module helper
 */
$CI = &get_instance();
$CI->load->helper(DATAPULSE_MODULE_NAME . '/datapulse');

function datapulse_permissions()
{}

/**
 * Register activation module hook
 */
register_activation_hook(DATAPULSE_MODULE_NAME, 'datapulse_module_activation_hook');

function datapulse_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(DATAPULSE_MODULE_NAME, [DATAPULSE_MODULE_NAME]);

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
function datapulse_module_init_menu_items()
{}

hooks()->add_action('app_admin_footer', 'datapulse_load_js');
function datapulse_load_js()
{
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];

    if (!(strpos($viewuri, 'admin') === false)) {
        echo '<script src="' . module_dir_url(DATAPULSE_MODULE_NAME, 'assets/widgets/customers_map_chart.js') . '"></script>';
        echo '<script src="' . module_dir_url(DATAPULSE_MODULE_NAME, 'assets/widgets/leads_map_chart.js') . '"></script>';
        echo '<script src="' . module_dir_url(DATAPULSE_MODULE_NAME, 'assets/widgets/staff_assigned_to_leads.js') . '"></script>';
        echo '<script src="' . module_dir_url(DATAPULSE_MODULE_NAME, 'assets/widgets/item_groups_chart.js') . '"></script>';
        echo '<script src="' . module_dir_url(DATAPULSE_MODULE_NAME, 'assets/widgets/staff_by_departments_chart.js') . '"></script>';
        echo '<script src="' . module_dir_url(DATAPULSE_MODULE_NAME, 'assets/widgets/staff_assigned_projects_chart.js') . '"></script>';
        echo '<script src="' . module_dir_url(DATAPULSE_MODULE_NAME, 'assets/widgets/customers_through_year_chart.js') . '"></script>';
        echo '<script src="' . module_dir_url(DATAPULSE_MODULE_NAME, 'assets/widgets/employee_through_year_chart.js') . '"></script>';
        echo '<script src="' . module_dir_url(DATAPULSE_MODULE_NAME, 'assets/widgets/expenses_on_categories_chart.js') . '"></script>';
        echo '<script src="' . module_dir_url(DATAPULSE_MODULE_NAME, 'assets/widgets/customers_by_group_chart.js') . '"></script>';
        echo '<script src="' . module_dir_url(DATAPULSE_MODULE_NAME, 'assets/widgets/staff_logged_time_chart.js') . '"></script>';
        echo '<script src="' . module_dir_url(DATAPULSE_MODULE_NAME, 'assets/widgets/added_tickets_by_project_chart.js') . '"></script>';
        echo '<script src="' . module_dir_url(DATAPULSE_MODULE_NAME, 'assets/widgets/projects_based_on_customers_chart.js') . '"></script>';
        echo '<script src="' . module_dir_url(DATAPULSE_MODULE_NAME, 'assets/widgets/estimate_assigned_agents_chart.js') . '"></script>';
        echo '<script src="' . module_dir_url(DATAPULSE_MODULE_NAME, 'assets/widgets/proposal_assigned_staff_chart.js') . '"></script>';
        echo '<script src="' . module_dir_url(DATAPULSE_MODULE_NAME, 'assets/widgets/invoices_stacked_by_customers_chart.js') . '"></script>';
    }
}

hooks()->add_action('app_admin_head', 'datapulse_head_load_js');
function datapulse_head_load_js()
{
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];

    if (!(strpos($viewuri, 'admin') === false)) {
        echo '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>';
        echo '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2"></script>';
    }
}

hooks()->add_filter('get_dashboard_widgets', 'datapulse_assign_widgets');
function datapulse_assign_widgets($widgets)
{
    $widgets[] = [
        'path'      => 'datapulse/widgets/customers_map_chart/widget',
        'container' => 'right-4',
    ];

    $widgets[] = [
        'path'      => 'datapulse/widgets/leads_map_chart/widget',
        'container' => 'right-4',
    ];

    $widgets[] = [
        'path'      => 'datapulse/widgets/staff_assigned_to_leads/widget',
        'container' => 'left-8',
    ];

    $widgets[] = [
        'path'      => 'datapulse/widgets/item_groups_chart/widget',
        'container' => 'left-8',
    ];

    $widgets[] = [
        'path'      => 'datapulse/widgets/staff_by_departments_chart/widget',
        'container' => 'left-8',
    ];

    $widgets[] = [
        'path'      => 'datapulse/widgets/staff_assigned_projects_chart/widget',
        'container' => 'left-8',
    ];

    $widgets[] = [
        'path'      => 'datapulse/widgets/customers_through_year_chart/widget',
        'container' => 'left-8',
    ];

    $widgets[] = [
        'path'      => 'datapulse/widgets/employee_through_year_chart/widget',
        'container' => 'left-8',
    ];

    $widgets[] = [
        'path'      => 'datapulse/widgets/expenses_on_categories_chart/widget',
        'container' => 'left-8',
    ];

    $widgets[] = [
        'path'      => 'datapulse/widgets/customers_by_group_chart/widget',
        'container' => 'left-8',
    ];

    $widgets[] = [
        'path'      => 'datapulse/widgets/staff_logged_time_chart/widget',
        'container' => 'left-8',
    ];

    $widgets[] = [
        'path'      => 'datapulse/widgets/added_tickets_by_project_chart/widget',
        'container' => 'left-8',
    ];

    $widgets[] = [
        'path'      => 'datapulse/widgets/projects_based_on_customers_chart/widget',
        'container' => 'left-8',
    ];

    $widgets[] = [
        'path'      => 'datapulse/widgets/estimate_assigned_agents_chart/widget',
        'container' => 'left-8',
    ];

    $widgets[] = [
        'path'      => 'datapulse/widgets/proposal_assigned_staff_chart/widget',
        'container' => 'left-8',
    ];

    $widgets[] = [
        'path'      => 'datapulse/widgets/invoices_stacked_by_customers_chart/widget',
        'container' => 'left-8',
    ];

    return $widgets;
}

function datapulse_appint()
{
    $CI = &get_instance();
    // require_once 'libraries/leclib.php';
    // $module_api = new DatapulseLic();
    // $module_leclib = $module_api->verify_license(true);
    // if (!$module_leclib || ($module_leclib && isset($module_leclib['status']) && !$module_leclib['status'])) {
    //     $CI->app_modules->deactivate(DATAPULSE_MODULE_NAME);
    //     set_alert('danger', "One of your modules failed its verification and got deactivated. Please reactivate or contact support.");
    //     redirect(admin_url('modules'));
    // }    
}

function datapulse_preactivate($module_name)
{
    if ($module_name['system_name'] == DATAPULSE_MODULE_NAME) {
        // require_once 'libraries/leclib.php';
        // $module_api = new DatapulseLic();
        // $module_leclib = $module_api->verify_license();
        // if (!$module_leclib || ($module_leclib && isset($module_leclib['status']) && !$module_leclib['status'])) {
        //     $CI = &get_instance();
        //     $data['submit_url'] = $module_name['system_name'] . '/lecverify/activate';
        //     $data['original_url'] = admin_url('modules/activate/' . DATAPULSE_MODULE_NAME);
        //     $data['module_name'] = DATAPULSE_MODULE_NAME;
        //     $data['title'] = "Module License Activation";
        //     echo $CI->load->view($module_name['system_name'] . '/activate', $data, true);
        //     exit();
        // }        
    }
}

function datapulse_predeactivate($module_name)
{
    if ($module_name['system_name'] == DATAPULSE_MODULE_NAME) {
        // require_once 'libraries/leclib.php';
        // $datapulse_api = new DatapulseLic();
        // $datapulse_api->deactivate_license();
    }
}

function datapulse_uninstall($module_name)
{
    if ($module_name['system_name'] == DATAPULSE_MODULE_NAME) {
        // require_once 'libraries/leclib.php';
        // $datapulse_api = new DatapulseLic();
        // $datapulse_api->deactivate_license();
    }
}
