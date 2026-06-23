<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Idproduction_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function get($id = null) {
        $this->db->select()->from('idproduction');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array(); 
        } else {
            return $query->result_array(); 
        }
    }
    public function getidproduction($id = null) {
        $this->db->select('idproduction.*,users.id as `user_tbl_id`,users.username,users.password as `user_tbl_password`,users.is_active as `user_tbl_active`');
        $this->db->from('idproduction');
        $this->db->join('users', 'users.user_id = idproduction.id', 'left'); 
        $this->db->where('users.role', 'idproduction');
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array(); 
        } else {
            return $query->result_array(); 
        }
    }


 

    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('idproduction');
    }

    /**
     * This function will take the post data passed from the controller
     * If id is present, then it will do an update
     * else an insert. One function doing both add and edit.
     * @param $data
     */
    public function add($data) {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('idproduction', $data); 
        } else {
            $this->db->insert('idproduction', $data);
            return $this->db->insert_id();
        }
    }

    public function getTotalidproduction() {
        $sql = "SELECT count(*) as `total_idproduction` FROM `idproduction`";
        $query = $this->db->query($sql);
        return $query->row(); 
    }

}
