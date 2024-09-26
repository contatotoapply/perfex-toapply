<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Instance_model extends App_Model
{

    private $table;
    public static $primary_key = 'id';
    public static $fillable = [];

    public function __construct()
    {
        $this->table = db_prefix() . 'notifications_zap_engine_instances';
    }

    public function all($select = '*', $where = null)
    {
        $this->db->select($select);
        if (is_array($where)) {
            $this->db->where($where);
        }
        return $this->db->get($this->table)->result();
    }

    public function create($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function find($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    /**
     * @param mixed $data
     * @param null $where
     *
     * @return [type]
     */
    public function update($data, $where = null)
    {
        if (is_array($where)) {
            $this->db->where($where);
        }
        $this->db->update($this->table, $data);
        return $this->db->affected_rows();
    }

    public function destroy($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        return $this->db->affected_rows();
    }
}
