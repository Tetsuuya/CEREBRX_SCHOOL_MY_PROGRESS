<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Importgraderequest_model extends CI_Model {

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
        $this->db->select('*'); 
        $this->db->from('import_grades_request');
        $this->db->join('students', 'students.id = import_grades_request.student_id');
        $this->db->join('subjects', 'subjects.id = import_grades_request.subject_id');
        if ($id != null) {
            $this->db->where('import_grades_request.id', $id);
        } else {
            $this->db->order_by('import_grades_request.id');
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
        $this->db->delete('import_grades_request');
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
            $this->db->update('import_grades_request', $data); 
        } else {
            $this->db->insert('import_grades_request', $data); 
            return $this->db->insert_id();
        }
    }

    function check_data_exists($grade_details_id) {
        $this->db->select('*'); 
        $this->db->from('import_grades_request');
        $this->db->where('import_grades_request.grade_details_id', $grade_details_id);
        $query = $this->db->get();
		
        return $query->row_array(); 
    }

     function check_id_exists($id) {
        $this->db->select('*'); 
        $this->db->from('import_grades_request');
        $this->db->where('import_grades_request.id', $id);
        $query = $this->db->get();
        
        return $query->row_array(); 
    }
	
	 public function getrequestByteacher($teacher_id = null, $session_id=null ) {
        if( empty($session_id )){
            $session_id = $this->current_session;
        }
        // $this->db->select('import_grades_request.id as id, students.id as student_id, date_requested, from,to,import_grades_request.quarter,subjects.name as subject_name,students.firstname, students.lastname,students.middlename,students.suffix,import_grades_request.status,import_grades_request.teacher_id,import_grades_request.comment_requested,import_grades_request.comment_approved, coordinator_id, academichead_id, role_requested'); 
         $this->db->select('firstname,middlename,lastname,suffix,students.id as student_id,date_requested,from,to,import_grades_request.quarter,subjects.name as subject_name,classes.*,sections.*,teacher_remarks,import_grades_request.id as id,principal_remarks,registrar_remarks,academichead_remarks,astatus,pstatus'); 
        $this->db->from('import_grades_request');
		$this->db->join('students', 'students.id = import_grades_request.student_id');
        $this->db->join('student_session', 'student_session.student_id = import_grades_request.student_id');
		$this->db->join('subjects', 'subjects.id = import_grades_request.subject_id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->where('import_grades_request.teacher_id', $teacher_id);
        $this->db->where('import_grades_request.session_id', $session_id);
        $this->db->where('student_session.session_id', $session_id);
        $this->db->order_by('import_grades_request.date_requested','DESC');
		
        $query = $this->db->get();
		
       return $query->result_array(); 
    }

    public function getrequestBydepartment($data = array(), $status = '', $session_id = null ) {
        $this->db->select('import_grades_request.id as id, students.id as student_id, date_requested, from,to,import_grades_request.quarter,subjects.name as subject_name,students.firstname, students.lastname,students.middlename,students.suffix,import_grades_request.teacher_id,import_grades_request.comment_requested,import_grades_request.comment_approved, coordinator_id, academichead_id, role_requested, import_grades_request.grade_details_id'); 
        $this->db->from('import_grades_request');
        if( $data ){
            $this->db->where_in('subjects.department_id', $data);
        }
       
       
        $this->db->join('students', 'students.id = import_grades_request.student_id');
        $this->db->join('subjects', 'subjects.id = import_grades_request.subject_id');
        $this->db->join('teachers', 'teachers.id = import_grades_request.teacher_id');
        if(empty($session_id )){
            $session_id =  $this->current_session;
        }
        $this->db->where('import_grades_request.session_id', $session_id);
        $this->db->order_by('import_grades_request.date_requested','DESC');
        
        $query = $this->db->get();
        
       return $query->result_array(); 
    }

    public function countgetrequestBydepartment($data = array(), $status = '', $session_id = '') {
        $this->db->select('import_grades_request.id as id, students.id as student_id, date_requested, from,to,import_grades_request.quarter,subjects.name as subject_name,students.firstname, students.lastname,students.middlename,students.suffix,import_grades_request.teacher_id,import_grades_request.comment_requested,import_grades_request.comment_approved, coordinator_id, academichead_id, role_requested,import_grades_request.grade_details_id'); 
        $this->db->from('import_grades_request');
        if($data){
            $this->db->where_in('subjects.department_id', $data);
        }
        
        $this->db->join('students', 'students.id = import_grades_request.student_id');
        $this->db->join('subjects', 'subjects.id = import_grades_request.subject_id');
        $this->db->join('teachers', 'teachers.id = import_grades_request.teacher_id');
        if(empty($session_id )){
            $session_id =  $this->current_session;
        }
        $this->db->where('import_grades_request.session_id', $session_id);
        $this->db->order_by('import_grades_request.date_requested','DESC');
        
        $query = $this->db->get();
        
       return $query->num_rows(); 
    }

      public function countgetrequest( $status = '', $user_id = '', $role = '', $session_id = '') {
        $this->db->select('students.firstname,students.middlename,students.lastname,students.suffix,students.id as student_id,date_requested,from,to,import_grades_request.quarter,subjects.name as subject_name,teacher_remarks,import_grades_request.id as id,principal_remarks,registrar_remarks,academichead_remarks,astatus,pstatus'); 
        $this->db->from('import_grades_request');
        if( ($status == 'approved' ||  $status == 'declined' ) && $user_id != ''){
             if( $role == 'academichead'){
                $this->db->where('import_grades_request.academichead_id', $user_id);
            } elseif( $role == 'principal'){
                $this->db->where('import_grades_request.principal_id', $user_id);
            }
        }
        // if( $role == 'academichead'){
        //     $this->db->where('import_grades_request.astatus', $status);
        // } elseif( $role == 'principal'){
        //      $this->db->where('import_grades_request.pstatus', $status);
        // }
         $this->db->where('import_grades_request.astatus', $status);

     
        $this->db->join('students', 'students.id = import_grades_request.student_id');
        $this->db->join('student_session', 'student_session.student_id = import_grades_request.student_id');
        $this->db->join('subjects', 'subjects.id = import_grades_request.subject_id');
        $this->db->join('teachers', 'teachers.id = import_grades_request.teacher_id');
        if(empty($session_id )){
            $session_id =  $this->current_session;
        }
        $this->db->where('import_grades_request.session_id', $session_id);
        $this->db->where('student_session.session_id', $session_id);
        $this->db->order_by('import_grades_request.date_requested','DESC');
        
        $query = $this->db->get();
          
       return $query->num_rows(); 
    }




     public function  getrequest( $status = '',  $role='', $user_id = '',  $session_id = '') {
        $this->db->select('students.firstname,students.middlename,students.lastname,students.suffix,students.id as student_id,date_requested,from,to,import_grades_request.quarter,subjects.name as subject_name,classes.*,sections.*,teacher_remarks,import_grades_request.id as id,principal_remarks,registrar_remarks,academichead_remarks,teachers.id as teacher_id,teachers.name as teacher_firstname, teachers.middlename as teacher_middlename,teachers.lastname as teacher_lastname,astatus,pstatus,is_INC,date_aapproved,date_adeclined,import_grades_request.subject_id,import_grades_request.session_id');         
        $this->db->from('import_grades_request');
        $this->db->join('students', 'students.id = import_grades_request.student_id');
        $this->db->join('student_session', 'student_session.student_id = import_grades_request.student_id');
        $this->db->join('subjects', 'subjects.id = import_grades_request.subject_id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('teachers', 'teachers.id = import_grades_request.teacher_id');
        if( ($status == 'approved' ||  $status == 'declined' ) && $user_id != ''){
            if( $role == 'academichead'){
                $this->db->where('import_grades_request.academichead_id', $user_id);
            } elseif( $role == 'principal'){
                $this->db->where('import_grades_request.principal_id', $user_id);
            }
        }

        // if( $role == 'academichead'){
        //     $this->db->where('import_grades_request.astatus', $status);
        // } elseif( $role == 'principal'){
        //     $this->db->where('import_grades_request.pstatus', $status);
        // }
         $this->db->where('import_grades_request.astatus', $status);


        if(empty($session_id )){
            $session_id =  $this->current_session;
        }
        $this->db->where('import_grades_request.session_id', $session_id);
        $this->db->where('student_session.session_id', $session_id);
        if( $status == 'approved' ){
             $this->db->order_by('import_grades_request.date_aapproved','DESC');
        } elseif( $status == 'declined'){
            $this->db->order_by('import_grades_request.date_adeclined','DESC'); 
        } else {
            $this->db->order_by('import_grades_request.date_requested','DESC');   
        }
       
        
        $query = $this->db->get();
      
        
       return $query->result_array(); 
    }

	
    public function  getallrequest(   $session_id = '') {
        $this->db->select('students.firstname,students.middlename,students.lastname,students.suffix,students.id as student_id,date_requested,from,to,import_grades_request.quarter,subjects.name as subject_name,classes.*,sections.*,teacher_remarks,import_grades_request.id as id,principal_remarks,registrar_remarks,academichead_remarks,teachers.id as teacher_id,teachers.name as teacher_firstname, teachers.middlename as teacher_middlename,teachers.lastname as teacher_lastname,astatus,pstatus,is_INC');         
        $this->db->from('import_grades_request');
        $this->db->join('students', 'students.id = import_grades_request.student_id');
        $this->db->join('student_session', 'student_session.student_id = import_grades_request.student_id');
        $this->db->join('subjects', 'subjects.id = import_grades_request.subject_id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('teachers', 'teachers.id = import_grades_request.teacher_id');

        if(empty($session_id )){
            $session_id =  $this->current_session;
        }
        $this->db->where('import_grades_request.session_id', $session_id);
        $this->db->where('student_session.session_id', $session_id);
        $this->db->order_by('import_grades_request.date_requested','DESC');
        
        $query = $this->db->get();
      
        
       return $query->result_array(); 
    }

	

}
