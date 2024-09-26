<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Instances extends AdminController
{
    protected $base_url;

    public function __construct()
    {
        parent::__construct();
        $this->load->library("sms_connect_api/sms_notifications_zap_engine_library");
        $this->load->model("sms_connect_api/instance_model");
        $this->base_url = get_option('sms_notifications_zap_engine_library_zap_engine_url');
    }

    public function index()
    {

        $instances = [];

        $instances = $this->sms_notifications_zap_engine_library->list_instances();

        if (is_admin()) {
            $data = ['title' => _l('sms_zap_instances'), 'instances' => $instances, 'is_api_global' => true];
        } else {
            if (!staff_can('view_own', 'sms_connect_api') && !staff_can('view', 'sms_connect_api')) {
                // redirect
                set_alert('danger', _l('access_denied'));
                redirect(admin_url());
            }

            if (staff_can('view_own', 'sms_connect_api')) {
                $my_instances             = $this->instance_model->all('*', ['created_by_staff_id' => get_staff_user_id()]);

                // Extrair os valores de instanceName de arr_1
                $arr_1_instance_names = array_map(function ($item) {
                    return $item->instanceName;
                }, $my_instances);

                // Filtrar arr_2 com base em arr_1_instance_names
                $result = array_filter($instances, function ($item) use ($arr_1_instance_names) {
                    return in_array($item->instance->instanceName, $arr_1_instance_names);
                });

                $data['instances']     = array_values($result);
                $data['is_api_global'] = false;
                return $this->load->view('instances/manage', $data);
            }

            if (staff_can('view', 'sms_connect_api')) {
                $my_instances             = $this->instance_model->all();

                // Extrair os valores de instanceName de arr_1
                $arr_1_instance_names = array_map(function ($item) {
                    return $item->instanceName;
                }, $my_instances);

                // Filtrar arr_2 com base em arr_1_instance_names
                $result = array_filter($instances, function ($item) use ($arr_1_instance_names) {
                    return in_array($item->instance->instanceName, $arr_1_instance_names);
                });

                $data['instances']     = array_values($result);
                $data['is_api_global'] = false;
                return $this->load->view('instances/manage', $data);
            }
        }

        $this->load->view('instances/manage', $data);
    }

    public function delete()
    {
        $instanteName = $this->input->get('instanteName');
        if ($instanteName) {
            $response = $this->sms_notifications_zap_engine_library->delete_instance($instanteName);

            if ($response->status == 404) {
                set_alert('danger', _l('sms_zap_instance_not_found'));
            }

            if ($response->status == 'SUCCESS') {
                set_alert('success', _l('sms_zap_instance_deleted'));
            }

            redirect(admin_url('sms_connect_api/instances'));
        }
    }

    public function create_instance()
    {

        $instanceName = $this->input->post('instanceName');

        $data         = [
            "instanceName" => $instanceName,
            "qrcode"       => true
        ];

        try {

            $response = $this->sms_notifications_zap_engine_library->create_instance($data);

            if (isset($response->status)) {
                $statusCode = $response->status;
                echo json_encode(['message' => $response->response->message[0], 'code' => $statusCode, 'status' => 'ERROR']);
                die;
            }

            if (isset($response->qrcode)) {

                $data = [
                    'created_by_staff_id' => get_staff_user_id(),
                    'instanceName'        => $instanceName,
                    'base_url'            => $this->base_url,
                    'api_key'             => $response->hash->apikey,
                    'its_primary_server'  => isset($isPrimary) ? 1 : 0
                ];

                $this->instance_model->create($data);

                echo json_encode(['data' => $response->qrcode, 'status' => 'SUCCESS']);
            }
        } catch (\Throwable $th) {
            print_r($th->getMessage());
        }
    }

    public function get_status_connection_instance()
    {

        $instanceName = $this->input->get('instanceName');

        $response = $this->sms_notifications_zap_engine_library->status_connection_instance($instanceName);

        if (isset($response->state)) {
            echo json_encode(['status' => 'success', 'state' => $response->state]);
            die;
        }

        echo json_encode(['status' => 'error', 'state' => null]);
        die;
    }

    public function update_instance_name()
    {
        $instance_name = $this->input->post('instance_name');
        $id            = $this->input->post('id');
        if (isset($id)) {
            $row = $this->instance_model->find($id);
            update_option('sms_notifications_zap_engine_library_whatsapp_api_instance_name_selected', $instance_name);
            update_option('sms_notifications_zap_engine_library_base_url', $row->base_url);
            update_option('sms_notifications_zap_engine_library_api_key', $row->api_key);
            $this->instance_model->update(['its_primary_server' => 0]);
            $this->instance_model->update(['its_primary_server' => 1], ['id' => $id]);
        } else {
            $base_url = get_option('sms_notifications_zap_engine_library_zap_engine_url');
            $api_key  = get_option('sms_notifications_zap_engine_library_zap_engine_token');
            update_option('sms_notifications_zap_engine_library_base_url', $base_url);
            update_option('sms_notifications_zap_engine_library_api_key', $api_key);
            update_option('sms_notifications_zap_engine_library_whatsapp_api_instance_name_selected', $instance_name);
        }
        echo json_encode(['status' => 'success']);
    }

    public function logout()
    {
        $instance_name = $this->input->get('instanteName');

        if (!$instance_name) {
            set_alert('danger', _l('sms_zap_instance_not_found'));
            redirect(admin_url('sms_connect_api/instances'));
        }

        $this->sms_notifications_zap_engine_library->logout_instance($instance_name);

        set_alert('success', _l('notifications_zap_engine_logout'));

        redirect(admin_url('sms_connect_api/instances'));
    }

    public function restart()
    {
        $instance_name = $this->input->get('instanteName');

        if (!$instance_name) {
            set_alert('danger', _l('sms_zap_instance_not_found'));
            redirect(admin_url('sms_connect_api/instances'));
        }

        $this->sms_notifications_zap_engine_library->restart_instance($instance_name);

        set_alert('success', _l('notifications_zap_engine_logout'));

        redirect(admin_url('sms_connect_api/instances'));
    }

    public function connect()
    {
        $instance_name = $this->input->get('instanteName');


        if (!$instance_name) {
            echo json_encode(['data' => _l('sms_zap_instance_not_found'), 'status' => 'FAIL']);
            die;
        }
        $response = $this->sms_notifications_zap_engine_library->connect_instance($instance_name);
        if (isset($response->base64)) {
            echo json_encode(['data' => ['base64' => $response->base64], 'status' => 'SUCCESS']);
        }
    }

    // Funções para API Não Global

    public function store()
    {

        $instanceName = $this->input->post('instanceName');

        $baseUrl      = $this->base_url;

        $isPrimary    = $this->input->post('its_primary_server');

        try {

            $data = [
                "instanceName" => $instanceName,
                "qrcode"       => true
            ];


            $response = $this->sms_notifications_zap_engine_library->create_instance($data);

            if (isset($response->status)) {
                $statusCode = $response->status;
                echo json_encode(['message' => $response->response->message[0], 'code' => $statusCode, 'status' => 'ERROR']);
                die;
            }

            if (isset($response->qrcode)) {

                $payload = [
                    'created_by_staff_id' => get_staff_user_id(),
                    'instanceName'        => $instanceName,
                    'base_url'            => $baseUrl,
                    'its_primary_server'  => isset($isPrimary) ? 1 : 0,
                    'api_key'             => $response->hash->apikey
                ];

                if ($payload['its_primary_server']) {
                    update_option('sms_notifications_zap_engine_library_whatsapp_api_instance_name_selected', $instanceName);
                    update_option('sms_notifications_zap_engine_library_base_url', $baseUrl);
                    update_option('sms_notifications_zap_engine_library_api_key', $response->hash->apikey);
                    $this->instance_model->update(['its_primary_server' => 0]);
                }


                $this->instance_model->create($payload);

                echo json_encode(['data' => $response->qrcode, 'status' => 'SUCCESS']);
            }
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public function destroy()
    {
        $id = $this->input->get('id');

        if ($id) {
            $response = $this->instance_model->destroy($id);
            if ($response) {
                set_alert('success', _l('sms_zap_instance_deleted'));
            }

            redirect(admin_url('sms_connect_api/instances'));
        }
    }
}
