<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Studentsession_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function searchStudents($class_id = null, $section_id = null, $key = null) {
        $this->db->select('student_session.id,student_session.student_id,classes.class,sections.section,
            students.firstname,students.lastname,students.admission_no,students.roll_no,students.lrn,students.dob,students.guardian_name,
            ')->from('student_session');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('students', 'students.id = student_session.student_id');
        $this->db->where('student_session.class_id', $class_id);
        $this->db->where('student_session.section_id', $section_id);
        $this->db->order_by('student_session.id');
        $query = $this->db->get();
        return $query->result_array(); 
    }

    public function searchStudentsBySession($student_session_id = null) {
        $this->db->select('students.admission_no,students.roll_no,student_session.session_id, student_session.class_id, student_session.section_id,student_session.id,student_session.student_id,classes.class,sections.section,
            students.firstname,students.lastname,students.admission_no,students.roll_no,students.lrn,students.dob,students.guardian_name,students.guardian_phone,students.father_name')->from('student_session');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('students', 'students.id = student_session.student_id');
        $this->db->where('student_session.id', $student_session_id);
        $this->db->order_by('id');
        $query = $this->db->get();
        return $query->row_array(); 
    }

    public function getStudentClass($id) {
        $this->db->select('students.admission_no,students.roll_no,student_session.session_id, student_session.class_id, student_session.section_id,student_session.id,student_session.student_id,classes.class,sections.section,
            students.firstname,students.lastname,students.admission_no,students.roll_no,students.lrn,students.dob,students.guardian_name,
            ')->from('student_session');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('students', 'students.id = student_session.student_id');
        $this->db->where('student_id', $id);
        $this->db->where('session_id', $this->current_session);
        $this->db->order_by('id');
        $query = $this->db->get();
        return $query->row_array(); 
    }

    public function getStudentByStudentId($id) {
        $this->db->select()->from('student_session');
        $this->db->where('student_id', $id);
        $this->db->where('session_id', $this->current_session);
        $this->db->order_by('id');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getTotalStudentBySession() {
       $query = "SELECT count(*) as `total_student` FROM `student_session` INNER JOIN students on students.id=student_session.student_id where student_session.session_id=".$this->db->escape($this->current_session);
		$query = $this->db->query($query);
		return $query->row(); 
	}
	
	public function getStudentsBySession( $student_session_id = null, $class_id = null, $section_id = null, $student_id = null ) {
        $this->db->select('students.admission_no,students.roll_no,students.lrn,student_session.session_id, student_session.class_id, student_session.section_id,student_session.id,student_session.student_id,classes.class,sections.section,
            students.firstname,students.middlename,students.lastname,students.admission_no,students.roll_no,students.lrn,students.dob,students.guardian_name,students.guardian_phone,students.father_name,session,students.gender,students.status')->from('student_session');
        $this->db->join('classes', 'student_session.class_id = classes.id','left');
        $this->db->join('sections', 'sections.id = student_session.section_id','left');
        $this->db->join('students', 'students.id = student_session.student_id');
        $this->db->join('sessions', 'sessions.id = student_session.session_id');
        $this->db->where('student_session.session_id', $student_session_id);
		if( $student_id ){
			$this->db->where('student_session.student_id', $student_id );
		}
		if( $class_id ){
			 $this->db->where('student_session.class_id', $class_id);
		}
		if( $section_id ){
			 $this->db->where('student_session.section_id', $section_id);
		} 
        $this->db->order_by('classes.id');
		$query = $this->db->get();
		if( $student_id ){
			return $query->row_array();  
		} else {
			return $query->result_array();  
		}
    }
	
	public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('student_session');
    }
	
	public function insert_batch( $data ){
        $this->db->insert_batch('student_session', $data );
		
		return true;
	}
	
	// public function add($data) {
    //     if (isset($data['id'])) {
    //         $this->db->where('id', $data['id']);
    //         $this->db->update('student_session', $data); 
    //     } else {
    //         $this->db->insert('student_session', $data); 
    //         return $this->db->insert_id();
    //     }
    // }

    public function add($data) {
    if (isset($data['id'])) {
        $this->db->where('id', $data['id']);
        return $this->db->update('student_session', $data); 
    } else {
        $this->db->insert('student_session', $data); 
        return $this->db->insert_id();
    }
}


	
	public function getAllStudentsSession( $student_id ) {
        $this->db->select('students.admission_no,students.roll_no,students.lrn,student_session.session_id, student_session.class_id, student_session.section_id,student_session.id,student_session.student_id,classes.class,sections.section,
            students.firstname,students.middlename,students.lastname,students.admission_no,students.roll_no,students.lrn,students.dob,students.guardian_name,students.guardian_phone,students.father_name,session,students.gender, students.father_midname,students.father_lastname,students.father_occupation,students.mother_name, students.mother_midname, students.mother_lastname, students.mother_occupation, students.current_address, students.guardian_address, students.suffix ')->from('student_session');
        $this->db->join('classes', 'student_session.class_id = classes.id','left');
        $this->db->join('sections', 'sections.id = student_session.section_id','left');
        $this->db->join('students', 'students.id = student_session.student_id');
        $this->db->join('sessions', 'sessions.id = student_session.session_id');
		$this->db->where('student_session.student_id', $student_id );
		$this->db->order_by('classes.id');
		$query = $this->db->get();
		
		return $query->result_array();  
    }
	
	public function lastschoolyearattended( $student_id ) {
        $this->db->select('student_session.session_id, student_session.class_id, student_session.section_id,student_session.id,student_session.student_id,classes.class,sections.section,sessions.session as session_name,students.firstname,students.lastname,students.middlename,students.suffix,student_session.strand_id,students.image,students.admission_no,students.gender,students.load_balance')->from('student_session');
        $this->db->join('classes', 'student_session.class_id = classes.id','left');
        $this->db->join('sections', 'sections.id = student_session.section_id','left');
        $this->db->join('students', 'students.id = student_session.student_id');
        $this->db->join('sessions', 'sessions.id = student_session.session_id');
		$this->db->where('student_session.student_id', $student_id );
		$this->db->order_by('sessions.session','DESC');
		$query = $this->db->get();
		
		return $query->row_array();  
    }
	
    public function get_enrolled_status($session_id='' ){
         if( empty( $session_id )){
            $session_id = $this->current_session;
        }
        $query = $this->db->query('
                SELECT 
                COUNT(CASE WHEN `class_id` = "10" AND `status` = "active" AND `session_id` = '.$session_id.' THEN 1 END ) AS grade_7,
                COUNT(CASE WHEN `class_id` = "11" AND `status` = "active" AND `session_id` = '.$session_id.' THEN 1 END ) AS grade_8,
                COUNT(CASE WHEN `class_id` = "12" AND `status` = "active" AND `session_id` = '.$session_id.' THEN 1 END ) AS grade_9,
                COUNT(CASE WHEN `class_id` = "13" AND `status` = "active" AND `session_id` = '.$session_id.' THEN 1 END ) AS grade_10,
                COUNT(CASE WHEN `class_id` = "14" AND `status` = "active" AND `session_id` = '.$session_id.' THEN 1 END ) AS grade_11,
                COUNT(CASE WHEN `class_id` = "15" AND `status` = "active" AND `session_id` = '.$session_id.' THEN 1 END ) AS grade_12,
                COUNT(CASE WHEN `class_id` = "10" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gender` = "Male" THEN 1 END ) AS grade_7_male,
                COUNT(CASE WHEN `class_id` = "10" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gender` = "Female" THEN 1 END ) AS grade_7_female,
                COUNT(CASE WHEN `class_id` = "11" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gender` = "Male" THEN 1 END ) AS grade_8_male,
                COUNT(CASE WHEN `class_id` = "11" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gender` = "Female" THEN 1 END ) AS grade_8_female,
                COUNT(CASE WHEN `class_id` = "12" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gender` = "Male" THEN 1 END ) AS grade_9_male,
                COUNT(CASE WHEN `class_id` = "12" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gender` = "Female" THEN 1 END ) AS grade_9_female,
                COUNT(CASE WHEN `class_id` = "13" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gender` = "Male" THEN 1 END ) AS grade_10_male,
                COUNT(CASE WHEN `class_id` = "13" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gender` = "Female" THEN 1 END ) AS grade_10_female,
                COUNT(CASE WHEN `class_id` = "14" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gender` = "Male" THEN 1 END ) AS grade_11_male,
                COUNT(CASE WHEN `class_id` = "14" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gender` = "Female" THEN 1 END ) AS grade_11_female,
                COUNT(CASE WHEN `class_id` = "15" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gender` = "Male" THEN 1 END ) AS grade_12_male,
                COUNT(CASE WHEN `class_id` = "15" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gender` = "Female" THEN 1 END ) AS grade_12_female,
                COUNT(CASE WHEN `class_id` = "10" AND `status` = "active" AND `session_id` = '.$session_id.' AND `rte` = "yes" THEN 1 END ) AS grade_7_esc,
                COUNT(CASE WHEN `class_id` = "11" AND `status` = "active" AND `session_id` = '.$session_id.' AND `rte` = "yes" THEN 1 END ) AS grade_8_esc,
                COUNT(CASE WHEN `class_id` = "12" AND `status` = "active" AND `session_id` = '.$session_id.' AND `rte` = "yes" THEN 1 END ) AS grade_9_esc,
                COUNT(CASE WHEN `class_id` = "13" AND `status` = "active" AND `session_id` = '.$session_id.' AND `rte` = "yes" THEN 1 END ) AS grade_10_esc,
                COUNT(CASE WHEN `class_id` = "14" AND `status` = "active" AND `session_id` = '.$session_id.' AND `rte` = "yes" THEN 1 END ) AS grade_11_esc,
                COUNT(CASE WHEN `class_id` = "15" AND `status` = "active" AND `session_id` = '.$session_id.' AND `rte` = "yes" THEN 1 END ) AS grade_12_esc,
                COUNT(CASE WHEN `class_id` = "10" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gov_voucher` = "yes" THEN 1 END ) AS grade_7_gov,
                COUNT(CASE WHEN `class_id` = "11" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gov_voucher` = "yes" THEN 1 END ) AS grade_8_gov,
                COUNT(CASE WHEN `class_id` = "12" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gov_voucher` = "yes" THEN 1 END ) AS grade_9_gov,
                COUNT(CASE WHEN `class_id` = "13" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gov_voucher` = "yes" THEN 1 END ) AS grade_10_gov,
                COUNT(CASE WHEN `class_id` = "14" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gov_voucher` = "yes" THEN 1 END ) AS grade_11_gov,
                COUNT(CASE WHEN `class_id` = "15" AND `status` = "active" AND `session_id` = '.$session_id.' AND `gov_voucher` = "yes" THEN 1 END ) AS grade_12_gov,
                COUNT(CASE WHEN `class_id` = "10" AND `status` = "active" AND `session_id` = '.$session_id.' AND `religion` = "SDA" THEN 1 END ) AS grade_7_sda,
                COUNT(CASE WHEN `class_id` = "11" AND `status` = "active" AND `session_id` = '.$session_id.' AND `religion` = "SDA" THEN 1 END ) AS grade_8_sda,
                COUNT(CASE WHEN `class_id` = "12" AND `status` = "active" AND `session_id` = '.$session_id.' AND `religion` = "SDA" THEN 1 END ) AS grade_9_sda,
                COUNT(CASE WHEN `class_id` = "13" AND `status` = "active" AND `session_id` = '.$session_id.' AND `religion` = "SDA" THEN 1 END ) AS grade_10_sda,
                COUNT(CASE WHEN `class_id` = "14" AND `status` = "active" AND `session_id` = '.$session_id.' AND `religion` = "SDA" THEN 1 END ) AS grade_11_sda,
                COUNT(CASE WHEN `class_id` = "15" AND `status` = "active" AND `session_id` = '.$session_id.' AND `religion` = "SDA" THEN 1 END ) AS grade_12_sda
                FROM students as l
                inner join student_session as m on l.id = m.student_id
                '); 
         $result = $query->row_array();     
    
         
        return $result;
    }
	
	public function get_student_allow_by_sy( $student_id ) {
        $this->db->select('session_id,student_id,session')->from('student_session');
		$this->db->where('student_session.allow', 'yes' );
		$this->db->where('student_session.student_id', $student_id );
		$this->db->join('sessions', 'student_session.session_id = sessions.id');		
		$this->db->order_by('session_id');
        $query = $this->db->get();
		
        return $query->result_array(); 
    }

     public function get_modality_value_abbrv( $modality ){
        if( $modality == 'online'){
            return 'OL';
        } elseif( $modality == 'modular_a'){
            return 'MOD A';
        } elseif( $modality == 'modular_b'){
            return 'MOD B';
        } else {
            return '';
        }
    }
}
