<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Carbon\Carbon;

class Mindmap extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mindmap_model');
    }

    /* List all mindmap */
    public function index()
    {
        if (!has_permission('mindmap', '', 'view')) {
            access_denied('mindmap');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('mindmap', 'table'));
        }

        $data['switch_grid'] = false;

        if ($this->session->userdata('mindmap_grid_view') == 'true') {
            $data['switch_grid'] = true;
        }

        $this->load->model('staff_model');
        $data['staffs'] = $this->staff_model->get();
        $data['groups'] = $this->mindmap_model->get_groups();

        $data['title'] = _l('mindmaps');
        $this->app_scripts->add('mindmap-js','modules/mindmap/assets/js/mindmap.js');
        $this->load->view('manage', $data);
    }

    public function table()
    {
        if (!has_permission('mindmap', '', 'view')) {
            access_denied('mindmap');
        }

        $this->app->get_table_data(module_views_path('mindmap', 'table'));
    }


    public function grid()
    {
        echo $this->load->view('mindmap/grid', [], true);
    }

    /**
     * Task ajax request modal
     * @param  mixed $id
     * @return mixed
     */
    public function get_mindmap_data($id)
    {
        $mindmap = $this->mindmap_model->get($id);

        if (!$mindmap) {
            header('HTTP/1.0 404 Not Found');
            echo 'Mindmap not found';
            die();
        }
        $this->load->model('staff_model');

        $data['mindmap']               = $mindmap;
        $data['staff'] = $this->staff_model->get($data['mindmap']->staffid);
        $data['group'] = $this->mindmap_model->get_groups($data['mindmap']->mindmap_group_id);


        $html =  $this->load->view('view_mindmap_template', $data, true);
        echo $html;
    }

    public function mindmap_create($id = '')
    {
        if (!has_permission('mindmap', '', 'view')) {
            access_denied('mindmap');
        }
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('mindmap', '', 'create')) {
                    access_denied('mindmap');
                }
                $id = $this->mindmap_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('mindmap')));
                    redirect(admin_url('mindmap'));
                }
            } else {
                if (!has_permission('mindmap', '', 'edit')) {
                    access_denied('mindmap');
                }
                $success = $this->mindmap_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('mindmap')));
                }
                redirect(admin_url('mindmap/mindmap_create/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('mindmap_add_new', _l('mindmap'));
        } else {
            $data['mindmap']        = $this->mindmap_model->get($id);

            $title = _l('mindmap_edit', _l('mindmap'));
        }

        $data['mindmap_groups']    = $this->mindmap_model->get_groups();
        $data['title']                 = $title;
        
        $this->load->view('mindmap', $data);
    }


    /* Mindmap function to handle preview views. */
    public function preview($id = 0)
    {
        if (!has_permission('mindmap', '', 'view')) {
            access_denied('mindmap');
        }
        $data['mindmap']        = $this->mindmap_model->get($id);

        if (!$data['mindmap']) {
            blank_page(_l('mindmap_not_found'), 'danger');
        }

        $title = _l('preview_mindmap');
        $data['title']                 = $title;
        $data['mindmap_group']    = $this->mindmap_model->get_groups($data['mindmap']->mindmap_group_id);
        $this->load->view('preview', $data);
    }


    /* Delete from database */
    public function delete($id)
    {
        if (!has_permission('mindmap', '', 'delete')) {
            access_denied('mindmap');
        }
        if (!$id) {
            redirect(admin_url('mindmap'));
        }
        $response = $this->mindmap_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('mindmap_deleted', _l('mindmap')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('mindmap_lowercase')));
        }
        redirect(admin_url('mindmap'));
    }

    public function switch_grid($set = 0, $manual = false)
    {
        if ($set == 1) {
            $set = 'false';
        } else {
            $set = 'true';
        }

        $this->session->set_userdata([
            'mindmap_grid_view' => $set,
        ]);
        if ($manual == false) {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /*********Mindmap group**********/
    public function groups(){
        if (!is_admin()) {
            access_denied('Mindmap');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('mindmap', 'admin/groups_table'));
        }
        $data['title'] = _l('mindmap_group');
        $this->load->view('mindmap/admin/groups_manage', $data);
    }

    public function group()
    {
        if (!is_admin() && get_option('staff_members_create_inline_mindmap_group') == '0') {
            access_denied('mindmap');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->mindmap_model->add_group($this->input->post());
                echo json_encode([
                    'success' => $id ? true : false,
                    'message' => $id ? _l('added_successfully', _l('mindmap_group')) : '',
                    'id'      => $id,
                    'name'    => $this->input->post('name'),
                ]);
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->mindmap_model->update_group($data, $id);
                $message = _l('updated_successfully', _l('mindmap_group'));
                echo json_encode(['success' => $success, 'message' => $message]);
            }
        }
    }


    public function delete_group($id)
    {
        if (!$id) {
            redirect(admin_url('mindmap'));
        }
        $response = $this->mindmap_model->delete_group($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('mindmap_group')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('mindmap_group')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('mindmap_group')));
        }
        redirect(admin_url('mindmap/groups'));
    }
}
