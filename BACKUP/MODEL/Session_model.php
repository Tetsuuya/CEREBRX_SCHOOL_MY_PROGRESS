<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Session_model extends CI_Model {

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
        $this->db->select()->from('sessions');
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

    public function getAllSession() {
        $sql = "SELECT sessions.*, IFNULL(sch_settings.session_id, 0) as `active` FROM `sessions` LEFT JOIN sch_settings ON sessions.id=sch_settings.session_id";
        $query = $this->db->query($sql);
        return $query->result_array(); 
    }

    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('sessions');
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
            $this->db->update('sessions', $data); 
        } else {
            $this->db->insert('sessions', $data); 
        }
    }

    public function getSessionsByStudent($student_id) {
        return $this->db
            ->select('DISTINCT s.id as session_id, s.session as session_name', false) // ← false disables escaping
            ->from('exam_results er')
            ->join('sessions s', 'er.session_id = s.id')
            ->where('er.student_id', $student_id)
            ->order_by('s.id', 'ASC')
            ->get()
            ->result_array();
    }

    public function convertSessionIDtoSchoolYearForID($session_id) {
        $this->db->select('session');
        $this->db->from('sessions');
        $this->db->where('id', $session_id);

        $query = $this->db->get();
        $result = $query->row()->session;

        $result = explode('-', $result);
        $result = $result[0] . ' - ' . $result[1];

        return $result;
    }
}
