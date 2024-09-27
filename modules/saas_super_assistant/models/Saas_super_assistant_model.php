<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APP_MODULES_PATH . PERFEX_SAAS_MODULE_NAME . '/models/Perfex_saas_model.php');

class Saas_super_assistant_model extends Perfex_saas_model
{
    public $table_res_name = 'super_assistants';
    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function assistants()
    {
        return $this->get(perfex_saas_table($this->table_res_name));
    }

    public function get_assistant($id)
    {
        return $this->get(perfex_saas_table($this->table_res_name), $id);
    }

    public function create_or_update_assistant($data)
    {
        return $this->add_or_update($this->table_res_name, $data);
    }

    public function tenants($where = [])
    {
        if (!empty($where))
            $this->db->where($where);
        return $this->db->get(perfex_saas_table('companies'))->result_array();
    }

    public function get_assistant_by_staff_id($staff_id)
    {
        $this->db->where('staff_id', (int)$staff_id);
        return $this->db->get(perfex_saas_table($this->table_res_name))->row();
    }
}
