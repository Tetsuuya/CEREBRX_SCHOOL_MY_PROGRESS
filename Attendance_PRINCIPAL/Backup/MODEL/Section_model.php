<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Section_model extends CI_Model {

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
    public function get($id = null) {
        $this->db->select()->from('sections');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array(); // single row
        } else {
            return $query->result_array(); // array of result
        }
    }

    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('sections');
    }

    public function getClassBySection($classid) {
        $this->db->select('class_sections.id,class_sections.section_id,sections.section');
        $this->db->from('class_sections');
        $this->db->join('sections', 'sections.id = class_sections.section_id');
        $this->db->where('class_sections.class_id', $classid);
        $this->db->where('class_sections.session_id', $this->current_session);
        $this->db->order_by('class_sections.id');
        $query = $this->db->get();
        return $query->result_array(); 
    }
	
	public function getClassBySectionBySession($classid, $session_id) {
        $this->db->select('class_sections.id,class_sections.section_id,sections.section');
        $this->db->from('class_sections');
        $this->db->join('sections', 'sections.id = class_sections.section_id');
        $this->db->where('class_sections.class_id', $classid);
        $this->db->where('class_sections.session_id', $session_id);
        $this->db->order_by('class_sections.id');
        $query = $this->db->get();
        return $query->result_array();
    }
	

    public function getClassNameBySection($classid, $sectionid) {
        $this->db->select('class_sections.id,class_sections.section_id,sections.section,classes.class');
        $this->db->from('class_sections');
        $this->db->join('sections', 'sections.id = class_sections.section_id');
        $this->db->join('classes', 'classes.id = class_sections.class_id');
        $this->db->where('class_sections.class_id', $classid);
        $this->db->where('class_sections.section_id', $sectionid);
        $this->db->order_by('class_sections.id');
        $query = $this->db->get();
        return $query->result_array(); 
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
            $this->db->update('sections', $data); 
            } else {
            $this->db->insert('sections', $data); 
        }
    }
	
	public function getbyname($name = null) {
		$this->db->select()->from('sections'); 
		$this->db->where('section', $name); 
		$this->db->order_by('id'); 
		$query = $this->db->get(); 
		
		if( $query->num_rows() == 0 && $name == 'none' ){
			$this->add(array('section' => 'None'));
			$this->getbyname('name');
		} else {
			return $query->row_array(); 
		}
	}
	
	 public function getClassSectionBySession($classid, $sectionid, $session_id='' ) {
        $this->db->select('class_sections.id,class_sections.section_id,sections.section,classes.class');
        $this->db->from('class_sections');
        $this->db->join('sections', 'sections.id = class_sections.section_id');
        $this->db->join('classes', 'classes.id = class_sections.class_id');
        $this->db->where('class_sections.class_id', $classid);
        $this->db->where('class_sections.section_id', $sectionid);
		if( $session_id ){
			$this->db->where('class_sections.session_id', $session_id);
		} else {
			$this->db->where('class_sections.session_id', $this->current_session );
		}
        $this->db->order_by('class_sections.id');
        $query = $this->db->get();
        return $query->row_array(); 
    }
	
	public function getClassBySectionTeacher($classid, $teacher_id ) {
        // $this->db->select('class_sections.id,class_sections.section_id,sections.section');
         $this->db->select('ANY_VALUE ( class_sections.id ) AS id');
         $this->db->select('ANY_VALUE ( class_sections.section_id ) AS section_id');
         $this->db->select('ANY_VALUE ( sections.section ) AS section');

        $this->db->from('class_sections');
        $this->db->join('sections', 'sections.id = class_sections.section_id' , 'left');
        $this->db->join('teacher_subjects', 'class_sections.id = teacher_subjects.class_section_id', 'left');
        $this->db->where('class_sections.class_id', $classid);
        $this->db->where('teacher_subjects.teacher_id', $teacher_id);
        $this->db->where('teacher_subjects.session_id', $this->current_session);
        $this->db->where('class_sections.session_id', $this->current_session);
        $this->db->order_by('id');
        $this->db->group_by('section_id');
        $query = $this->db->get();
        return $query->result_array(); 
    }

    public function checkifused( $section_id ){
        $this->db->select('sections.id');
        $this->db->from('sections');
        $this->db->join('student_session', 'sections.id = student_session.section_id');
        $this->db->where('student_session.section_id', $section_id );
        $query = $this->db->get();
        return $query->row_array(); 
    }


}
