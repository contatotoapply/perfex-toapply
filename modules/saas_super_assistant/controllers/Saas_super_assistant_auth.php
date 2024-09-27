<?php defined('BASEPATH') or exit('No direct script access allowed');

class Saas_super_assistant_auth extends App_Controller
{
    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();
        if (!defined('PERFEX_SAAS_MODULE_NAME')) show_404();

        $this->load->model('staff_model');
        $this->load->helper('security');
        $this->load->helper('cookie');
    }

    /**
     * Methd to handling generation of magic auth code for signing into a tenant as assistant.
     * Can be used from superadmin context or tenant context.
     *
     * @param [type] $slug
     * @return void ved_aicnega
     */
    public function login_as_assistant($slug)
    {

        $slug = xss_clean($slug);
        if (empty($slug)) show_404();

        if (!is_staff_logged_in()) perfex_saas_show_tenant_error(_l(SAAS_SUPER_ASSISTANT_MODULE_NAME), _l('perfex_saas_permission_denied'));

        $staff_id = get_staff_user_id();
        $is_tenant = perfex_saas_is_tenant();

        $assistant = null;
        $company = null;

        // If signing in from assistant portal from super admin
        if (!$is_tenant) {
            $assistant = saas_super_assistant_get_assistant();
            if ($assistant)
                $company = $this->perfex_saas_model->get_entity_by_slug('companies', $slug, 'parse_company');
        }

        if ($is_tenant) {
            // Ensure the current user is an assistant
            $assistant_id = (int)get_staff_meta($staff_id, SAAS_SUPER_ASSISTANT_MODULE_NAME);
            $assistant = saas_super_assistant_get_assistant_by_id($assistant_id);

            $table = perfex_saas_table('companies');
            $company = perfex_saas_raw_query_row("SELECT * from $table WHERE `slug`='$slug';", [], true);
            if ($company)
                $company = $this->perfex_saas_model->parse_company($company);
        }

        // Generate a code
        if (empty($company->slug) || empty($assistant->id)) {
            perfex_saas_show_tenant_error(_l('perfex_saas_permission_denied'), _l('perfex_saas_page_not_found'), 403);
        }

        // Ensure the company belongs to the assistant
        $tenants = $assistant->tenants;
        $slugs = empty($tenants) ? [] : json_decode($tenants);
        if (!empty($slugs) && !in_array($slug, $slugs)) {
            perfex_saas_show_tenant_error(_l('perfex_saas_permission_denied'), '');
        }

        //Ensure current instance also belong to the assistant
        if ($is_tenant && !empty($slugs) && !in_array(perfex_saas_tenant()->slug, $slugs)) {
            perfex_saas_show_tenant_error(_l('perfex_saas_permission_denied'), '');
        }

        $auth_code = perfex_saas_generate_magic_auth_code($company->clientid);
        $url = perfex_saas_tenant_base_url($company, SAAS_SUPER_ASSISTANT_MODULE_NAME . '/saas_super_assistant_auth/magic_auth_as_assistant', 'path') . '?auth_code=' . urlencode($auth_code) . '&staff_id=' . $assistant->staff_id;
        return redirect($url);
    }

    /**
     * Validate code and sign into a tenaant as assistant
     *
     * @return void ved_aicnega
     */
    public function magic_auth_as_assistant()
    {
        try {
            // Check if the user is not a tenant or if instance switching is not enabled
            if (!perfex_saas_is_tenant()) {
                throw new \Exception(_l('perfex_saas_permission_denied'), 1);
            }

            // If the user is already an admin, redirect to the admin dashboard
            if (is_staff_logged_in()) {
                return redirect(admin_url());
            }

            // Validate and authorize the magic authentication code
            $clientid = perfex_saas_validate_and_authorize_magic_auth_code();
            $tenant = perfex_saas_tenant();

            // Ensure that the client matches the current tenant instance
            if ((int)$tenant->clientid !== $clientid) {
                throw new \Exception(_l('perfex_saas_permission_denied'), 1);
            }

            // Add the assistant to the instance with neccessary permission
            $_staff_id = (int)$this->input->get('staff_id');
            $assistant_table = perfex_saas_table('super_assistants');
            $super_staff_table = perfex_saas_master_db_prefix() . 'staff';

            $query =  "SELECT $super_staff_table.*, permissions, $assistant_table.id as assistant_id, tenants FROM $super_staff_table JOIN $assistant_table on $super_staff_table.staffid=$assistant_table.staff_id WHERE `staffid`='$_staff_id'";

            // Get staff from super table with assigned permissions
            $staff = perfex_saas_raw_query_row($query, [], true);
            if (!$staff) show_404();

            // Ensure its truly assistant 
            if (empty($staff->assistant_id)) show_404();

            $tenants = empty($staff->tenants) || $staff->tenants === '[]' ? [] : json_decode($staff->tenants);
            // Ensure assistant have right to access
            if (!empty($tenants) && !in_array($tenant->slug, $tenants)) {
                throw new \Exception(_l('perfex_saas_permission_denied'), 1);
            }

            $this->load->helper('string');
            $data = [
                'email' => $staff->email,
                'firstname' => $staff->firstname,
                'lastname' => $staff->lastname,
                'password' => random_string('alnum', 20), // We dont want to know it and wont ever work for direct login since we are using direct db insert (i.e not hashed)
                'admin' => 0,
                'is_not_staff' => 0
            ];

            $permissions = empty($staff->permissions) ? [] : (array)json_decode($staff->permissions);

            $staff_table = db_prefix() . 'staff';
            $local_staff = $this->staff_model->db->where('email', $staff->email)->get($staff_table)->row();

            $staff_id = null;

            // Add assistant as staff with permission
            if (!$local_staff) {

                $data['datecreated'] = date('Y-m-d H:i:s');

                $this->staff_model->db->insert($staff_table, $data);
                $staff_id = $this->db->insert_id();
                $this->staff_model->update_permissions($permissions, $staff_id);
            }

            // Update assistant information
            if ($local_staff) {
                $staff_id = $local_staff->staffid;
                $this->staff_model->db->where('staffid', $staff_id)->update($staff_table, $data);
                $this->staff_model->update_permissions($permissions, $staff_id);
            }

            // Sign in into the current tenant as the staff - assistant
            if (!perfex_saas_tenant_admin_autologin($staff_id))
                throw new \Exception("Unkown", 1);

            update_staff_meta($staff_id, SAAS_SUPER_ASSISTANT_MODULE_NAME, $staff->assistant_id);

            // Set the supporting tenants as cookie for switching accross instances
            $cookie_path = '/';
            if ($tenant->http_identification_type === PERFEX_SAAS_TENANT_MODE_PATH) {
                $tenant_url_sig = perfex_saas_tenant_url_signature($tenant->slug);
                $cookie_path = '/' . $tenant_url_sig . '/';
            }
            set_cookie([
                'name'  => SAAS_SUPER_ASSISTANT_MODULE_NAME . '_tenants',
                'value' => $this->encryption->encrypt(json_encode($tenants)),
                'expire' => 60 * 60 * 24 * 31 * 3,
                'path' => $cookie_path,
                'httponly' => true,
            ]);

            return redirect(admin_url());
        } catch (\Throwable $th) {

            perfex_saas_show_tenant_error(_l('perfex_saas_authentication_error'), $th->getMessage());
        }
    }
}
