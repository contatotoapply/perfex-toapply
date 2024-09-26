<?php

/**
 * Criado pelo : Taffarel Dev Module Creator
 * Autor       : Taffarel Xavier
 * Email       : contato@taffarel.dev
 * Site        : https://taffarel.dev?t=1724331201
 * GitHub      : https://github.com/TaffarelXavier
 * ID          : 46d331c1cfa8f024c5917663c8e8cbc3
 **/

defined('BASEPATH') or exit('No direct script access allowed');

class Carne_model extends App_model
{
    private $table;
    public static $primary_key = 'id';
    public static $fillable = [];

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'asaas_carnes';
    }

    public function all($select = '*', $where = [])
    {
        $this->db->select($select);
        if (!empty($where)) {
            $this->db->where($where);
        }
        return $this->db->get($this->table)->result();
    }

    public function create($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function create_many($data)
    {
        $this->db->insert_batch($this->table, $data);
        return $this->db->affected_rows();
    }

    public function find($select = null, $where = null)
    {
        if ($select == null) {
            $select = '*';
        }
        $this->db->select($select);
        if (!empty($where)) {
            $this->db->where($where);
        }

        return $this->db->get($this->table)->row();
    }

    public function find_by_id($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    public function update($data, $where = [])
    {
        if (!empty($where)) {
            $this->db->where($where);
        }

        $this->db->update($this->table, $data);
        return $this->db->affected_rows();
    }

    public function destroy($where)
    {
        $this->db->where($where);
        $this->db->delete($this->table);
        return $this->db->affected_rows();
    }
}
