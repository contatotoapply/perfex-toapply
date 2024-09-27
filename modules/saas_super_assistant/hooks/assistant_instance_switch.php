<?php

defined('BASEPATH') or exit('No direct script access allowed');


// Add tenant switch to top nav
if (perfex_saas_is_tenant()) {

    $staff_id = get_staff_user_id();
    $is_assistant = (int)get_staff_meta($staff_id, SAAS_SUPER_ASSISTANT_MODULE_NAME);

    hooks()->add_action('admin_navbar_start', function () {
        $staff_id = get_staff_user_id();
        $is_assistant = (int)get_staff_meta($staff_id, SAAS_SUPER_ASSISTANT_MODULE_NAME);

        if (is_staff_member() && $is_assistant) {

            $tenant = perfex_saas_tenant();
            $clientid = $tenant->clientid;
            $table = perfex_saas_table('companies');

            $CI   = &get_instance();
            $meta_key = SAAS_SUPER_ASSISTANT_MODULE_NAME . 'tenants' . $clientid;
            $meta = $CI->app_object_cache->get($meta_key);
            if ($meta !== false) {
                $instances = (array)$meta;
            } else {
                $where = [
                    "`status`='active'",
                ];

                if (!function_exists('get_cookie')) $CI->load->helper('cookie');
                $slugs = get_cookie(SAAS_SUPER_ASSISTANT_MODULE_NAME . '_tenants');
                if (empty($slugs)) return;

                $slugs = $CI->encryption->decrypt($slugs);
                if (!$slugs) return;

                $slugs = json_decode($slugs);

                if (!empty($slugs))
                    $where[] = "`slug` IN ('" . implode("', '", $slugs) . "')";

                $where = ' WHERE ' . implode(" AND ", $where);
                $instances = perfex_saas_raw_query("SELECT `name`,`slug`,`custom_domain` from `$table` $where LIMIT 1000;", [], true);
                if (!empty($instances))
                    $CI->app_object_cache->add($meta_key, $instances);
            }

            if ($instances && count($instances) > 1) {
                echo '<li class="icon header-assistant-tenant-switch tw-h-full tw-relative ltr:tw-mr-1.5 rtl:tw-ml-1.5" data-toggle="tooltip" data-title="' . _l('perfex_saas_switch_app') . '">
                        <a href="#" id="tenant-switch" class="!tw-px-0 tw-group" data-toggle="dropdown" aria-expanded="true">
                            <span class="tw-px-3 tw-py-2 tw-inline-block tw-border tw-border-solid tw-border-neutral-200 tw-rounded-lg -tw-mt-2" style="border-color:red;"> <i class="fa fa-random tw-mr-1"></i>' . $tenant->name . '</span>
                        </a>    
                        <ul class="dropdown-menu animated fadeIn" id="tenant-switch-list">
                        <li class="tw-px-2"><input class="form-control" name="tenant-switch-list-filter"/></li>
                        ';
                foreach ($instances as $key => $instance) {
                    $url = $instance->slug === $tenant->slug ? '#' : admin_url(SAAS_SUPER_ASSISTANT_MODULE_NAME . '/saas_super_assistant_auth/login_as_assistant/' . $instance->slug);
                    echo "<li><a href='$url'>$instance->name</a></li>";
                }

                echo '
                        </ul>
                    </li>';

                hooks()->add_action('before_js_scripts_render', function () {
                    require_once(APP_MODULES_PATH . SAAS_SUPER_ASSISTANT_MODULE_NAME . '/views/includes/switch-scripts.php');
                });
            }
        }
    });

    if ($is_assistant) {
        $current_url = uri_string();
        $is_post_and_not_datatable = !empty($_POST) && empty($_POST['columns']) && empty($_POST['draw']);
        if ($is_post_and_not_datatable || stripos($current_url, 'admin/dashboard') !== false || str_ends_with($current_url, 'admin')) {
            saas_super_assistant_validate_and_update_assistant();
        }
    }
}