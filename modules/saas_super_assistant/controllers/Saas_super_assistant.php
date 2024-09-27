<?php defined('BASEPATH') or exit('No direct script access allowed');

class Saas_super_assistant extends AdminController
{
    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();
        if (!defined('PERFEX_SAAS_MODULE_NAME')) show_404();

        if (perfex_saas_is_tenant()) show_404();
    }

    /**
     * Show list of assistants
     *
     * @return void
     */
    public function index()
    {
        if (!is_admin()) {
            return show_error(_l('perfex_saas_permission_denied'));
        }

        // Return the table data for ajax request
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path(SAAS_SUPER_ASSISTANT_MODULE_NAME, 'assistants/table'));
        }

        // Show list of super assistants
        $data['title'] = _l('saas_super_assistants');
        $this->load->view('assistants/manage', $data);
    }

    /**
     * Manage an assistant
     *
     * @param string $action Optional
     * @param string $id Optional
     * @return void
     */
    public function manage($action = 'create', $id = '')
    {
        if (!is_admin()) {
            return show_error(_l('perfex_saas_permission_denied'));
        }

        // Save assistant data
        if ($this->input->post()) {

            // Delete
            if ($action === 'delete') {

                $id = (int)$this->input->post('id', true);
                if ($this->saas_super_assistant_model->delete('super_assistants', $id))
                    set_alert('success', _l('deleted', _l('saas_super_assistant')));
                return redirect(admin_url(SAAS_SUPER_ASSISTANT_MODULE_NAME));
            } else {

                // Create or edit
                // Perform validation
                $this->load->library('form_validation');
                $this->form_validation->set_rules('staff_id', _l('staff'), ['required']);
                if ($this->form_validation->run() !== false) {

                    try {

                        $tenants = json_encode($this->input->post('tenants', true) ?? []);
                        $permissions = json_encode($this->input->post('permissions', true));

                        $form_data = [
                            'staff_id' => $this->input->post('staff_id', true),
                            'tenants' => $tenants,
                            'permissions' => $permissions
                        ];
                        if ($id)
                            $form_data['id'] = $id;


                        $_id = $this->saas_super_assistant_model->create_or_update_assistant($form_data);
                        if ($_id) {

                            set_alert('success', _l('added_successfully', _l('saas_super_assistant')));
                            return redirect(admin_url(SAAS_SUPER_ASSISTANT_MODULE_NAME));
                        }
                    } catch (\Exception $e) {

                        set_alert('danger', $e->getMessage());
                    }
                }
            }
        }

        // Show form to create/edit an assistant
        if (!empty($id)) {
            $data['assistant'] = $this->saas_super_assistant_model->get_assistant($id);
            $data['member'] = $this->staff_model->get($data['assistant']->staff_id);
        }

        $data['title'] = _l('perfex_saas_packages');
        $data['tenants'] = $this->saas_super_assistant_model->tenants();
        $data['staff']     = $this->staff_model->get('', ['active' => 1]);

        $this->load->view('assistants/form', $data);
    }

    /**
     * Display tenants that an assistant can assist
     *
     * @return void
     */
    public function tenants()
    {
        if (!saas_super_assistant_is_assistant()) {
            return show_error(_l('perfex_saas_permission_denied'));
        }

        // Return the table data for ajax request
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path(SAAS_SUPER_ASSISTANT_MODULE_NAME, 'companies/table'));
        }

        // Show list of tenant that can be assisted by the assistants
        $data['title'] = _l('saas_super_assistants');
        $this->load->view('companies/manage', $data);
    }
}
