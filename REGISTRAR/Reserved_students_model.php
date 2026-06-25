<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reserved_Students_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_date = $this->setting_model->getDateYmd();
    }

	
     public function get($id = null, $type = null ) {
        $this->db->select('reserved_students.*,reserved_students.firstname,reserved_students.lastname,reserved_students.middlename, reserved_students.type,reserved_students.session_id,reserved_students.class_id,classes.class,sessions.session,students.id as student_id, reserved_students.id as id,reserved_students.created_at,reserved_students.status,parent_id,reserved_students.dob,reserved_students.gender, reserved_students.guardian_name,reserved_students.guardian_midname, reserved_students.guardian_lastname, reserved_students.guardian_phone,reserved_students.status as student_status, reserved_students.lrn, reserved_students.section_id, reserved_students.strand_id,students.lrn as student_lrn,students.admission_no as student_admission_no,reserved_students.modality, reserved_students.email_address')->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id','LEFT');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        if ($id != null) {
            $this->db->where('reserved_students.id', $id);
        } else {
            $this->db->order_by('reserved_students.id', 'desc');
        }
        if( $type != null && $type != 'all' ){
            $this->db->where('reserved_students.process_type', $type); 
        }
        $query = $this->db->get();
 
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }

    }
	
	 public function getonreserved($id = null) { 
        $this->db->select('reserved_students.*, students.admission_no');
        $this->db->select('sessions.session');
        $this->db->select('classes.class');
        $this->db->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id','LEFT');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        if ($id != null) {
            $this->db->where('reserved_students.id', $id);
        } else {
            $this->db->order_by('reserved_students.id', 'desc');
        }
        $query = $this->db->get();
 
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }

    }
	
	public function get_confirmed($id = null){
		$this->db->select('reserved_students.*,reserved_students.firstname,reserved_students.lastname,reserved_students.middlename, reserved_students.type,reserved_students.session_id,reserved_students.class_id,classes.class,sessions.session,students.id as student_id, reserved_students.id as id,reserved_students.created_at,reserved_students.status,parent_id,reserved_students.dob,reserved_students.gender, reserved_students.guardian_name,reserved_students.guardian_midname, reserved_students.guardian_lastname, reserved_students.guardian_phone,reserved_students.status as student_status, reserved_students.lrn, reserved_students.section_id, reserved_students.strand_id,enrollment_status,date_scheduled')->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id','LEFT');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        if ($id != null) {
            $this->db->where('reserved_students.id', $id);
        } else {
            $this->db->order_by('reserved_students.id', 'desc');
        }
		$this->db->where('reserved_students.type <>', 'unconfirmed' );
        $query = $this->db->get();
 
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        } 
	}
	
	public function get_unconfirmed($id = null){
	    $this->db->select('students.firstname,students.lastname,students.middlename, reserved_students.type,reserved_students.session_id,reserved_students.class_id,classes.class,sessions.session,students.id as student_id, reserved_students.id as id,reserved_students.created_at,reserved_students.status,parent_id,students.dob,students.gender, students.guardian_name,students.guardian_midname, students.guardian_lastname, students.guardian_phone, students.status as student_status')->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        if ($id != null) {
            $this->db->where('reserved_students.id', $id);
        } else {
            $this->db->order_by('reserved_students.id', 'desc');
        }
		$this->db->where('reserved_students.type', 'unconfirmed' );
        $query = $this->db->get();
 
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        } 
	}
	
	

    public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('reserved_students');
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
            $this->db->update('reserved_students', $data);
            // Log update result
            if ($this->db->affected_rows() > 0) {
                log_message('error', 'Reserved student updated successfully. ID: ' . $data['id']);
                return $data['id'];
            } else {
                log_message('error', 'Failed to update reserved student. ID: ' . $data['id'] . ' DB Error: ' . $this->db->error()['message']);
                return false;
            }
        } else {
            // Log the data being inserted
            log_message('error', 'Attempting to insert reserved student data: ' . print_r($data, true));
            
            $this->db->insert('reserved_students', $data);
            $insert_id = $this->db->insert_id();
            
            if ($insert_id) {
                log_message('error', 'Reserved student inserted successfully. Insert ID: ' . $insert_id);
                return $insert_id;
            } else {
                $db_error = $this->db->error();
                log_message('error', 'Failed to insert reserved student. DB Error Code: ' . $db_error['code'] . ' Message: ' . $db_error['message']);
                log_message('error', 'Last query: ' . $this->db->last_query());
                return false;
            }
        }
    }
	
	public function getByStudent($student_id, $session_id = null ) {
        $this->db->select('students.firstname,students.lastname,students.middlename, reserved_students.type,reserved_students.session_id,reserved_students.class_id,classes.class,sessions.session,students.id as student_id, reserved_students.id as id,reserved_students.created_at,reserved_students.status,parent_id,students.dob,students.gender, students.guardian_name,students.guardian_midname, students.guardian_lastname, students.guardian_phone,students.status as student_status, students.lrn, students.roll_no, reserved_students.section_id, reserved_students.strand_id')->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id','LEFT');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
		if( $session_id ){
			$this->db->where('reserved_students.session_id', $session_id);
		}
        $this->db->where('reserved_students.student_id', $student_id);
        $this->db->where('reserved_students.status', 'reserved');
        $query = $this->db->get();
 
        return $query->row_array();
    }
	
	public function countReservedStudents() {
        $this->db->select('reserved_students.id')->from('reserved_students');
		 $this->db->where('reserved_students.status', 'reserved');
		 $this->db->join('students', 'students.id = reserved_students.student_id','LEFT');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        $query = $this->db->get();
		
		return $query->num_rows();

    }
	
	public function countUnconfirmedStudents() {
        $this->db->select('reserved_students.id')->from('reserved_students');
		$this->db->where('reserved_students.type', 'unconfirmed');
		$this->db->join('students', 'students.id = reserved_students.student_id','LEFT');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        $query = $this->db->get();
		
		return $query->num_rows();

    }
	
	 public function get_with_limit($limit = 10) {
        $this->db->select('reserved_students.*,reserved_students.firstname,reserved_students.lastname,students.middlename,reserved_students.type, reserved_students.session_id,reserved_students.class_id, classes.class,sessions.session,students.id as student_id, reserved_students.id as id,reserved_students.created_at,reserved_students.status,parent_id,enrollment_status,date_scheduled,reserved_students.guardian_phone')->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id','LEFT');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        $this->db->order_by('reserved_students.id', 'desc');
        $this->db->limit($limit);
        $query = $this->db->get();
        
		return $query->result_array();
    }

    public function get_with_limit_coordinator($limit = 10, $user_id ) {
        $setting_result = $this->setting_model->get();
        $sy_enrollment = $setting_result[0]['sy_enrollment'];
        $this->db->select('reserved_students.id as id ,reserved_students.firstname,reserved_students.lastname,reserved_students.middlename,reserved_students.type, reserved_students.session_id,reserved_students.class_id, classes.class,reserved_students.created_at,reserved_students.status,parent_id,enrollment_status,date_scheduled,reserved_students.guardian_phone,reserved_students.suffix')->from('reserved_students');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('coordinator_grade', 'classes.id = coordinator_grade.class_id');
        $this->db->where('coordinator_grade.user_id', $user_id);
        $this->db->where('coordinator_grade.session_id', $sy_enrollment);
        $this->db->where('reserved_students.session_id', $sy_enrollment);
        $this->db->where('reserved_students.review_cashier', 'yes');
        //  $this->db->where('reserved_students.is_active', 'yes');
        $this->db->order_by('reserved_students.id', 'desc');
        $this->db->limit($limit);
        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_reserved_coordinator_count( $user_id, $class_id='', $gender = '' ) {
        $setting_result = $this->setting_model->get();
        $sy_enrollment = $setting_result[0]['sy_enrollment'];
        $this->db->select('reserved_students.id as id ,reserved_students.firstname,reserved_students.lastname,reserved_students.middlename,reserved_students.type, reserved_students.session_id,reserved_students.class_id, classes.class,reserved_students.created_at,reserved_students.status,parent_id,enrollment_status,date_scheduled,reserved_students.guardian_phone,reserved_students.suffix,reserved_students.strand_id,reserved_students.gender,reserved_students.dob,reserved_students.guardian_lastname,reserved_students.guardian_name,reserved_students.lrn')->from('reserved_students');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('coordinator_grade', 'classes.id = coordinator_grade.class_id');
        //  $this->db->where('reserved_students.is_active', 'yes');
        $this->db->where('coordinator_grade.user_id', $user_id);
       // $this->db->where('reserved_students.review_dsf', 'yes');
        if( $class_id ){
            $this->db->where('coordinator_grade.class_id', $class_id);
        }
         if( $gender ){
            $this->db->where('reserved_students.gender', $gender );
        }
        $this->db->where('coordinator_grade.session_id', $sy_enrollment);
        $this->db->where('reserved_students.session_id', $sy_enrollment);
        $this->db->order_by('reserved_students.id', 'asc');
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function get_reserved_coordinator_count_to_activate( $user_id, $class_id='', $gender = '' ) {
        $setting_result = $this->setting_model->get();
        $sy_enrollment = $setting_result[0]['sy_enrollment'];
        $this->db->select('reserved_students.id as id ,reserved_students.firstname,reserved_students.lastname,reserved_students.middlename,reserved_students.type, reserved_students.session_id,reserved_students.class_id, classes.class,reserved_students.created_at,reserved_students.status,parent_id,enrollment_status,date_scheduled,reserved_students.guardian_phone,reserved_students.suffix,reserved_students.strand_id,reserved_students.gender,reserved_students.dob,reserved_students.guardian_lastname,reserved_students.guardian_name,reserved_students.lrn')->from('reserved_students');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('coordinator_grade', 'classes.id = coordinator_grade.class_id');
        //  $this->db->where('reserved_students.is_active', 'yes');
        $this->db->where('coordinator_grade.user_id', $user_id);
        $this->db->where('reserved_students.review_cashier', 'yes');
        if( $class_id ){
            $this->db->where('coordinator_grade.class_id', $class_id);
        }
         if( $gender ){
            $this->db->where('reserved_students.gender', $gender );
        }
        $this->db->where('coordinator_grade.session_id', $sy_enrollment);
        $this->db->where('reserved_students.session_id', $sy_enrollment);
        $this->db->order_by('reserved_students.id', 'asc');
        $query = $this->db->get();

        return $query->num_rows();
    }
	
	public function get_reserved_child($parent_id, $session_id = '') {
		$this->db->select('students.firstname,students.lastname,students.middlename, reserved_students.type,reserved_students.session_id,reserved_students.class_id,classes.class,sessions.session,students.id as student_id, reserved_students.id as id,reserved_students.created_at,reserved_students.status,parent_id')->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        $this->db->where('reserved_students.parent_id', $parent_id);
		if( $session_id ){
			 $this->db->where('reserved_students.session_id', $session_id);
		}
		$this->db->order_by('reserved_students.id', 'desc');
        $query = $this->db->get();
 
       return $query->result_array();;
	}
	
	public function get_reserved_information($id = null) {
        $this->db->select('reserved_students.firstname,reserved_students.lastname,reserved_students.middlename, reserved_students.type,reserved_students.session_id,reserved_students.class_id,classes.class,sessions.session,students.id as student_id, reserved_students.id as id,reserved_students.created_at,reserved_students.status,reserved_students.parent_id,reserved_students.dob,reserved_students.gender, reserved_students.guardian_name,reserved_students.guardian_midname, reserved_students.guardian_lastname, reserved_students.guardian_phone,students.status as student_status, reserved_students.lrn, reserved_students.suffix, reserved_students.guardian_address, reserved_students.guardian_address2')->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        if ($id != null) {
            $this->db->where('reserved_students.id', $id);
        } else {
            $this->db->order_by('reserved_students.id', 'desc');
        }
        $query = $this->db->get();
 
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }

    }
	
	public function get_reserved_status(){
        $setting_result = $this->setting_model->get();
        $session_id = isset($setting_result[0]['sy_enrollment'])?$setting_result[0]['sy_enrollment']:$setting_result[0]['session_id'];
        $query = $this->db->query('
                SELECT 
                COUNT(CASE WHEN `class_id` = "10" AND `session_id` = '.$session_id.' THEN 1 END ) AS grade_7,
                COUNT(CASE WHEN `class_id` = "11" AND `session_id` = '.$session_id.' THEN 1 END ) AS grade_8,
                COUNT(CASE WHEN `class_id` = "12" AND `session_id` = '.$session_id.' THEN 1 END ) AS grade_9,
                COUNT(CASE WHEN `class_id` = "13" AND `session_id` = '.$session_id.' THEN 1 END ) AS grade_10,
                COUNT(CASE WHEN `class_id` = "14" AND `session_id` = '.$session_id.' THEN 1 END ) AS grade_11,
                COUNT(CASE WHEN `class_id` = "15" AND `session_id` = '.$session_id.' THEN 1 END ) AS grade_12,
                COUNT(CASE WHEN `class_id` = "10"  AND `session_id` = '.$session_id.' AND `gender` = "Male" THEN 1 END ) AS grade_7_male,
                COUNT(CASE WHEN `class_id` = "10"  AND `session_id` = '.$session_id.' AND `gender` = "Female" THEN 1 END ) AS grade_7_female,
                COUNT(CASE WHEN `class_id` = "11"  AND `session_id` = '.$session_id.' AND `gender` = "Male" THEN 1 END ) AS grade_8_male,
                COUNT(CASE WHEN `class_id` = "11"  AND `session_id` = '.$session_id.' AND `gender` = "Female" THEN 1 END ) AS grade_8_female,
                COUNT(CASE WHEN `class_id` = "12"  AND `session_id` = '.$session_id.' AND `gender` = "Male" THEN 1 END ) AS grade_9_male,
                COUNT(CASE WHEN `class_id` = "12"  AND `session_id` = '.$session_id.' AND `gender` = "Female" THEN 1 END ) AS grade_9_female,
                COUNT(CASE WHEN `class_id` = "13"  AND `session_id` = '.$session_id.' AND `gender` = "Male" THEN 1 END ) AS grade_10_male,
                COUNT(CASE WHEN `class_id` = "13"  AND `session_id` = '.$session_id.' AND `gender` = "Female" THEN 1 END ) AS grade_10_female,
                COUNT(CASE WHEN `class_id` = "14"  AND `session_id` = '.$session_id.' AND `gender` = "Male" THEN 1 END ) AS grade_11_male,
                COUNT(CASE WHEN `class_id` = "14"  AND `session_id` = '.$session_id.' AND `gender` = "Female" THEN 1 END ) AS grade_11_female,
                COUNT(CASE WHEN `class_id` = "15"  AND `session_id` = '.$session_id.' AND `gender` = "Male" THEN 1 END ) AS grade_12_male,
                COUNT(CASE WHEN `class_id` = "15"  AND `session_id` = '.$session_id.' AND `gender` = "Female" THEN 1 END ) AS grade_12_female
                FROM reserved_students
                '); 
         $result = $query->row_array();     
    
         
        return $result;
    }

    public function online_get_with_limit($limit = 10, $session_id = null) {
           if( empty( $session_id) ){
          $session_id = $this->current_session;
        }
        $this->db->select('reserved_students.*,reserved_students.firstname,reserved_students.lastname,students.middlename,reserved_students.type, reserved_students.session_id,reserved_students.class_id, classes.class,sessions.session,students.id as student_id, reserved_students.id as id,reserved_students.created_at,reserved_students.status,parent_id,enrollment_status,date_scheduled,reserved_students.guardian_phone')->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id','LEFT');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        $this->db->where('reserved_students.process_type', 'online');
        $this->db->where('reserved_students.session_id', $session_id);
        $this->db->order_by('reserved_students.id', 'desc');
        $this->db->limit($limit);
        $query = $this->db->get();
        
        return $query->result_array();
    }

    public function pre_get_with_limit($limit = 10, $session_id = null) {
        $setting_result = $this->setting_model->get();
       
        if( empty( $session_id) ){
          $session_id = $setting_result[0]['sy_enrollment'];
        }
        $this->db->select('reserved_students.*,reserved_students.firstname,reserved_students.lastname,students.middlename,reserved_students.type, reserved_students.session_id,reserved_students.class_id, classes.class,sessions.session,students.id as student_id, reserved_students.id as id,reserved_students.created_at,reserved_students.status,parent_id,enrollment_status,date_scheduled,reserved_students.guardian_phone')->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id','LEFT');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        $this->db->where('reserved_students.process_type', 'school');
        $this->db->order_by('reserved_students.id', 'desc');
        $this->db->where('reserved_students.session_id', $session_id);
        $this->db->limit($limit);
        $query = $this->db->get();
        
        return $query->result_array();
    }

    public function not_review_cashier_get_with_limit($session_id = null, $limit = 10 ) {
        $setting_result = $this->setting_model->get();
       
        if( empty( $session_id) ){
          $session_id = $setting_result[0]['sy_enrollment'];
        }
        $this->db->select('reserved_students.*,reserved_students.firstname,reserved_students.lastname,students.middlename,reserved_students.type, reserved_students.session_id,reserved_students.class_id, classes.class,sessions.session,students.id as student_id, reserved_students.id as id,reserved_students.created_at,reserved_students.status,parent_id,enrollment_status,date_scheduled,reserved_students.guardian_phone')->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id','LEFT');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        $this->db->where('reserved_students.review_cashier', 'no');
        $this->db->where('reserved_students.paid_status', 'no');
        $this->db->where('reserved_students.session_id', $session_id);
        $this->db->order_by('reserved_students.id', 'desc');
        $this->db->limit($limit);
        $query = $this->db->get();
        
        return $query->result_array();
    }



    public function not_review_cashier($session_id = null){
         $setting_result = $this->setting_model->get();
       
        if( empty( $session_id) ){
          $session_id = $setting_result[0]['sy_enrollment'];
        }
        $this->db->select('reserved_students.*,reserved_students.firstname,reserved_students.lastname,students.middlename,reserved_students.type, reserved_students.session_id,reserved_students.class_id, classes.class,sessions.session,students.id as student_id, reserved_students.id as id,reserved_students.created_at,reserved_students.status,parent_id,enrollment_status,date_scheduled,reserved_students.guardian_phone')->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id','LEFT');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        $this->db->where('reserved_students.review_cashier', 'no');
        $this->db->where('reserved_students.paid_status', 'no');
        $this->db->where('reserved_students.session_id', $session_id);
        $this->db->order_by('reserved_students.id', 'desc');
        $query = $this->db->get();
        
        return $query->result_array();
    }

      public function online_students( $session_id = null) {
        if( empty( $session_id) ){
          $session_id = $this->current_session;
        }
        $this->db->select('reserved_students.*,reserved_students.firstname,reserved_students.lastname,students.middlename,reserved_students.type, reserved_students.session_id,reserved_students.class_id, classes.class,sessions.session,students.id as student_id, reserved_students.id as id,reserved_students.created_at,reserved_students.status,parent_id,enrollment_status,date_scheduled,reserved_students.guardian_phone')->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id','LEFT');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        $this->db->where('reserved_students.process_type', 'online');
        $this->db->where('reserved_students.session_id', $session_id);
        $this->db->order_by('reserved_students.id', 'desc');
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
     public function pending_review_get_with_limit($limit = 10) {
        $setting_result = $this->setting_model->get();
        $sy_enrollment = $setting_result[0]['sy_enrollment']; 
        $this->db->select('reserved_students.*,reserved_students.firstname,reserved_students.lastname,students.middlename,reserved_students.type, reserved_students.session_id,reserved_students.class_id, classes.class,sessions.session,students.id as student_id, reserved_students.id as id,reserved_students.created_at,reserved_students.status,parent_id,enrollment_status,date_scheduled,reserved_students.guardian_phone')->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id','LEFT');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        $this->db->where('reserved_students.session_id', $sy_enrollment);
        $this->db->where('reserved_students.review_cashier <>', 'yes');
        $this->db->order_by('reserved_students.id', 'desc');
        $this->db->limit($limit);
        $query = $this->db->get();
        
        return $query->result_array();
    }

    public function approved_online_get_with_limit($limit = 10, $session_id = null) {
        if( empty( $session_id) ){
          $session_id = $this->current_session;
        }
        $this->db->select('reserved_students.*,reserved_students.firstname,reserved_students.lastname,students.middlename,reserved_students.type, reserved_students.session_id,reserved_students.class_id, classes.class,sessions.session,students.id as student_id, reserved_students.id as id,reserved_students.created_at,reserved_students.status,parent_id,enrollment_status,date_scheduled,reserved_students.guardian_phone')->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id','LEFT');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        $this->db->where('reserved_students.process_type', 'online');
        $this->db->where('reserved_students.review_cashier', 'yes');
        $this->db->where('reserved_students.session_id', $session_id);
        $this->db->order_by('reserved_students.id', 'desc');
        $this->db->limit($limit);
        $query = $this->db->get();
        
        return $query->result_array();
    }

     public function approved_pre_get_with_limit($limit = 10, $session_id = null) {
        if( empty( $session_id) ){
          $session_id = $this->current_session;
        }
        $this->db->select('reserved_students.*,reserved_students.firstname,reserved_students.lastname,students.middlename,reserved_students.type, reserved_students.session_id,reserved_students.class_id, classes.class,sessions.session,students.id as student_id, reserved_students.id as id,reserved_students.created_at,reserved_students.status,parent_id,enrollment_status,date_scheduled,reserved_students.guardian_phone')->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id','LEFT');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        $this->db->where('reserved_students.process_type', 'school');
        $this->db->where('reserved_students.review_cashier', 'yes');
        $this->db->order_by('reserved_students.id', 'desc');
        $this->db->where('reserved_students.session_id', $session_id);
        $this->db->limit($limit);
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
     public function pending_review() {
        $setting_result = $this->setting_model->get();
        $sy_enrollment = $setting_result[0]['sy_enrollment']; 
        $this->db->select('reserved_students.*,reserved_students.firstname,reserved_students.lastname,students.middlename,reserved_students.type, reserved_students.session_id,reserved_students.class_id, classes.class,sessions.session,students.id as student_id, reserved_students.id as id,reserved_students.created_at,reserved_students.status,parent_id,enrollment_status,date_scheduled,reserved_students.guardian_phone')->from('reserved_students');
        $this->db->join('students', 'students.id = reserved_students.student_id','LEFT');
        $this->db->join('classes', 'classes.id = reserved_students.class_id');
        $this->db->join('sessions', 'sessions.id = reserved_students.session_id');
        $this->db->where('reserved_students.session_id', $sy_enrollment);
        $this->db->where('reserved_students.review_cashier <>', 'yes');
        $this->db->where('reserved_students.paid_status <>', 'promissory');
        $this->db->order_by('reserved_students.id', 'desc');
        $query = $this->db->get();
        
        return $query->result_array();
    }

    // ── NEW ── for student storing on the reserved student table and update the paid status to promissory or paid
    public function update_paid_status($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('reserved_students', $data);
    }
    // ── END NEW ──
    
	

}
