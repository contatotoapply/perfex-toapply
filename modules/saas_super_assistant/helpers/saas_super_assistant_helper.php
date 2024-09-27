<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Checks if an assistant is set.
 *
 * @return bool Returns true if an assistant exists, false otherwise.
 */
function saas_super_assistant_is_assistant()
{
    return !empty(saas_super_assistant_get_assistant());
}

/**
 * Retrieves an assistant by staff ID.
 *
 * @param string $staff_id The staff ID (optional if empty).
 * @param string $use_cache 
 * @return mixed|null Returns the assistant or null if not found.
 * @throws \Exception Throws an exception if called within tenant context.
 */
function saas_super_assistant_get_assistant($staff_id = '', $use_cache = true)
{
    // Check if the method is called within a tenant context
    if (perfex_saas_is_tenant()) throw new \Exception("This method cannot be called within tenant context", 1);

    // Retrieve the assistant by staff ID
    $staff_id = empty($staff_id) ? get_staff_user_id() : $staff_id;
    if (!$staff_id) return null;

    $CI = &get_instance();

    // Retrieve assistant from session if available, otherwise fetch from the model
    $session_key = 'client_assistant_' . $staff_id;
    if ($CI->session->has_userdata($session_key) && $use_cache)
        return $CI->session->userdata($session_key);

    $assistant = get_instance()->saas_super_assistant_model->get_assistant_by_staff_id($staff_id);

    // Store assistant data in session and return
    $CI->session->set_userdata($session_key, $assistant);
    return $assistant;
}

/**
 * Retrieves an assistant by ID from the database.
 *
 * @param int $assistant_id The ID of the assistant.
 * @return object|null Returns the assistant object or null if not found.
 */
function saas_super_assistant_get_assistant_by_id($assistant_id)
{
    $table = perfex_saas_table('super_assistants');
    return perfex_saas_raw_query_row("SELECT * from $table WHERE `id`='$assistant_id';", [], true);
}

/**
 * Validates and updates assistant details within tenant context.
 *
 * @throws \Exception Throws an exception if called outside tenant context or on error conditions.
 */

function saas_super_assistant_validate_and_update_assistant()
{
    // Ensure the method is called within tenant context
    if (!perfex_saas_is_tenant()) throw new \Exception("This method can only be called within tenant context", 1);

    $staff_id = get_staff_user_id();
    $assistant_id = (int)get_staff_meta($staff_id, SAAS_SUPER_ASSISTANT_MODULE_NAME);
    if (!$assistant_id) return;

    $CI = get_instance();
    $CI->load->model('Authentication_model');
    $CI->load->model('staff_model');

    // Retrieve staff and assistant details for validation and updates
    $terminate = false;
    $assistant = saas_super_assistant_get_assistant_by_id($assistant_id);
    if (empty($assistant->id)) {
        $terminate = true;
    }

    // Ensure the company belongs to the assistant
    if (!$terminate) {
        $tenants = $assistant->tenants;
        $slugs = empty($tenants) ? [] : json_decode($tenants);
        if (!empty($slugs) && !in_array(perfex_saas_tenant_slug(), $slugs)) {
            $terminate = true;
        }
    }

    if ($terminate) {
        // Delete the assistant and logout
        $admin = $CI->staff_model->get('', ['admin' => '1', 'staffid !=' => $staff_id])[0] ?? [];
        if (!empty($admin)) {
            $CI->staff_model->delete($staff_id, $admin['staffid']);
        }

        // Delete meta
        delete_staff_meta($staff_id, SAAS_SUPER_ASSISTANT_MODULE_NAME);

        // destroy session 
        $CI->Authentication_model->logout();
        perfex_saas_show_tenant_error(_l('perfex_saas_permission_denied'), '');
    }

    // Update permission
    $permissions = empty($assistant->permissions) ? [] : (array)json_decode($assistant->permissions);
    $CI->staff_model->update_permissions($permissions, $staff_id);
}
