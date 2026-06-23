<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Importgradeswritten_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function getByBatch( $id) {
        $this->db->select('*')->from('import_grades_written_work');
        $this->db->join('import_grades_batch', 'import_grades_batch.id = import_grades_written_work.batch_id ');
        $this->db->where('import_grades_written_work.batch_id', $id);
        $query = $this->db->get();
        
		return $query->row_array();
    }
     
	public function deleteByBatch( $id ) {
        $this->db->where('batch_id', $id);
        $this->db->delete('import_grades_written_work');
    }
	
    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('import_grades_written_work');
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
            $this->db->update('import_grades_written_work', $data); 
        } else {
            $this->db->insert('import_grades_written_work', $data); 
            return $this->db->insert_id();
        }
    }
	
	public function getByStudentBatch( $student_id, $batch_id ){
		$this->db->select('*')->from('import_grades_written_work');
        $this->db->join('import_grades_batch', 'import_grades_batch.id = import_grades_written_work.batch_id ');
        $this->db->where('import_grades_written_work.batch_id', $batch_id);
        $this->db->where('import_grades_written_work.student_id', $student_id);
        $query = $this->db->get();
        
		return $query->row_array();
	}
}
