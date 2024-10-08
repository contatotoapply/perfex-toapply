<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Carbon\Carbon;

class Mindmap_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_staff_counts($staffid){
        $count = 0;

        $sql = "SELECT count(`staffid`) as total_count
                from ".db_prefix()."mindmap where staffid= '".$staffid."' " ;
        $query = $this->db->query($sql);
        $row = $query->row();
        if (isset($row)){
            $count = $row->total_count;
        }

        return $count;
    }

    /**
     * Get groups
     * @param  mixed $id group id (Optional)
     * @return mixed     object or array
     */
    public function get_groups($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'mindmap_groups')->row();
        }
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'mindmap_groups')->result_array();
    }

    /**
     * Add new group
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_group($data)
    {
        $data['description'] = nl2br($data['description']);
        $this->db->insert(db_prefix() . 'mindmap_groups', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('Mindmap Group Added [ID: ' . $insert_id . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Update group
     * @param  mixed $data All $_POST data
     * @param  mixed $id   group id to update
     * @return boolean
     */
    public function update_group($data, $id)
    {
        $data['description'] = nl2br($data['description']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'mindmap_groups', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Mindmap Group Updated [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete type from database, if used return array with key referenced
     */
    public function delete_group($id)
    {
        if (is_reference_in_table('mindmap_group_id', db_prefix() . 'mindmap', $id)) {
            return [
                'referenced' => true,
            ];
        }
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'mindmap_groups');
        if ($this->db->affected_rows() > 0) {
            log_activity('Group Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer (optional)
     * @return object
     * Get single
     */
    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'mindmap')->row();
        }
        return $this->db->get(db_prefix() . 'mindmap')->result_array();
    }

    /**
     * Add new
     * @param mixed $data All $_POST dat
     * @return mixed
     */
    public function add($data)
    {
        $data['staffid']      = $data['staffid'] == '' ? 0 : $data['staffid'];
        $data['dateadded'] = date('Y-m-d H:i:s');

        $this->db->insert(db_prefix() . 'mindmap', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Mindmap Added [ID:' . $insert_id . ']');

            return $insert_id;
        }
        return false;
    }

    /**
     * Update
     * @param  mixed $data All $_POST data
     * @param  mixed $id    id
     * @return boolean
     */
    public function update($data, $id)
    {
        $data['staffid']      = $data['staffid'] == '' ? 0 : $data['staffid'];
        $data['dateaupdated'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'mindmap', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Mindmap Updated [ID:' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete
     * @param  mixed $id id
     * @return boolean
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'mindmap');
        if ($this->db->affected_rows() > 0) {
            log_activity('Mindmap Deleted [ID:' . $id . ']');
            return true;
        }
        return false;
    }
}
