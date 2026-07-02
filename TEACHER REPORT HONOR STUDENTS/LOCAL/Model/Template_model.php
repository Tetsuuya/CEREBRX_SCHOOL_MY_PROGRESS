<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Template_model extends CI_Model {

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
        $this->db->select()->from('template');
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

    /**
     * This function will delete the record based on the id
     * @param $id
     */
	 
    public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('template');
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
            $this->db->update('template', $data); 
        } else {
            $this->db->insert('template', $data); 
            return $this->db->insert_id();
        }
    }
	
	public function getByClass($class_id){
		$this->db->select('template.id,template.title, template.availability')->from('template');
		$this->db->join('template_class', 'template.id = template_class.template_id');
		$this->db->where('class_id', $class_id );
        $query = $this->db->get();
		return $query->result_array(); 
       
		
	}
}
