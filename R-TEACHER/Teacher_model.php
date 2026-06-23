<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Teacher_model extends CI_Model {

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
        $this->db->select()->from('teachers');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            // $this->db->order_by('id');
            $this->db->order_by('lastname');        // Sort by last name
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array(); 
        } else {
            return $query->result_array(); 
        }
    }

    public function getactive($id = null) {
        $this->db->select()->from('teachers');
            $this->db->where('is_active', 'yes');
            $this->db->where('is_teacher', 'yes');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('lastname');
            $this->db->order_by('name');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array(); 
        } else {
            return $query->result_array(); 
        }
    }
    
    public function getTeacher($id = null) {
        $this->db->select('teachers.*,users.id as `user_tbl_id`,users.username,users.password as `user_tbl_password`,users.is_active as `user_tbl_active`');
        $this->db->from('teachers');
        $this->db->join('users', 'users.user_id = teachers.id', 'left'); 
        $this->db->where('users.role', 'teacher');
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array(); 
        } else {
            return $query->result_array(); 
        }
    }


    public function getLibraryTeacher() {
        $this->db->select('teachers.*, IFNULL(libarary_members.id,0) as `libarary_member_id`, IFNULL(libarary_members.library_card_no,0) as `library_card_no`')->from('teachers');

        $this->db->join('libarary_members', 'libarary_members.member_id = teachers.id and libarary_members.member_type = "teacher"','left');

        $this->db->order_by('teachers.id');
        
        $query = $this->db->get();
        return $query->result_array(); 
    }

    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('teachers');
    }

    /**
     * This function will take the post data passed from the controller
     * If id is present, then it will do an update
     * else an insert. One function doing both add and edit.
     * @param $data
     */
    // public function add($data) {
    //     if (isset($data['id'])) {
    //         $this->db->where('id', $data['id']);
    //         $this->db->update('teachers', $data); 
    //     } else {
    //         $this->db->insert('teachers', $data);
    //         return $this->db->insert_id();
    //     }
    // }

       //new function
       public function add($data) {
        // Check if the teacher record exists
        if (isset($data['id'])) {
            // Update teacher's record
            $this->db->where('id', $data['id']);
            $this->db->update('teachers', $data);
    
            // Synchronize the employee table
            if (isset($data['is_active'])) {
                $this->syncEmployeeStatus($data['id'], $data['is_active']);
            }
        } else {
            // Insert a new teacher's record
            $this->db->insert('teachers', $data);
            $teacher_id = $this->db->insert_id();
    
            // Synchronize the employee table for the newly added teacher
            if (isset($data['is_active'])) {
                $this->syncEmployeeStatus($teacher_id, $data['is_active']);
            }
    
            return $teacher_id;
        }
    }
    
    private function syncEmployeeStatus($teacher_id, $is_active) {
        // Find the teacher's name from the teacher table
        $this->db->select('name');
        $this->db->where('id', $teacher_id);
        $teacher = $this->db->get('teachers')->row();
    
        if ($teacher) {
            // Update the employee table where the name matches
            $this->db->where('name', $teacher->name);
            $this->db->update('employee', ['is_active' => $is_active]);
        }
    }

    public function getTotalTeacher() {
        $sql = "SELECT count(*) as `total_teacher` FROM `teachers` ";
        $query = $this->db->query($sql);
        return $query->row(); 
    }

    public function getTotalTeacheractive() {
        $sql = "SELECT count(*) as `total_teacher` FROM `teachers` where `is_active` = 'yes' ";
        $query = $this->db->query($sql);
        return $query->row(); 
    }
	
	public function getTotalActiveStaff() {
        $sql = "SELECT count(*) as `total_staff` FROM `teachers` where `is_active` = 'yes' AND type_teacher != 'Advisory' ";
        $query = $this->db->query($sql);
        return $query->row(); 
    }

   public function get_teachers_number($id = null) {
        $faculty_number = array(); 

        $this->db->select('phone')->from('teachers');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
			$this->db->where('is_active', 'yes');
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        $results = $query->result_array(); 
        if( $results ){
            foreach ($results as $key => $value) {
                $phone_number = $value['phone'];
                $phone_number = str_replace(' ', '', $phone_number);
                $phone_number = preg_replace('/\s+/', '', $phone_number);
                $phone_number = str_replace("‐","",$phone_number);
                if( strlen($phone_number) >= 10 ){
                    // unset($results[ $key]);
                    $faculty_number[]['phone'] = $phone_number;
                }
            }
        }

        $this->db->select('phone')->from('principal'); 
		$this->db->where('is_active', 'yes');
        $query = $this->db->get();
        $results = $query->result_array(); 
        if( $results ){
            foreach ($results as $key => $value) {
                $phone_number = $value['phone'];
                $phone_number = str_replace(' ', '', $phone_number);
                $phone_number = preg_replace('/\s+/', '', $phone_number);
                $phone_number = str_replace("‐","",$phone_number);
                if( strlen($phone_number) >= 10 ){
                    // unset($results[ $key]);
                    $faculty_number[]['phone'] = $phone_number;
                }
            }
        }
         

        $this->db->select('phone')->from('registrar'); 
		$this->db->where('is_active', 'yes');
        $query = $this->db->get();
        $results = $query->result_array(); 
        if( $results ){
            foreach ($results as $key => $value) {
                $phone_number = $value['phone'];
                $phone_number = str_replace(' ', '', $phone_number);
                $phone_number = preg_replace('/\s+/', '', $phone_number);
                $phone_number = str_replace("‐","",$phone_number);
                if( strlen($phone_number) >= 10 ){
                    // unset($results[ $key]);
                    $faculty_number[]['phone'] = $phone_number;
                }
            }
        }

        $this->db->select('phone')->from('academic_head'); 
		$this->db->where('is_active', 'yes');
        $query = $this->db->get();
        $results = $query->result_array(); 
        if( $results ){
            foreach ($results as $key => $value) {
                $phone_number = $value['phone'];
                $phone_number = str_replace(' ', '', $phone_number);
                $phone_number = preg_replace('/\s+/', '', $phone_number);
                $phone_number = str_replace("‐","",$phone_number);
                if( strlen($phone_number) >= 10 ){
                    // unset($results[ $key]);
                    $faculty_number[]['phone'] = $phone_number;
                }
            }
        }
        $this->db->select('phone')->from('accountants'); 
		$this->db->where('is_active', 'yes');
        $query = $this->db->get();
        $results = $query->result_array(); 
        if( $results ){
            foreach ($results as $key => $value) {
                $phone_number = $value['phone'];
                $phone_number = str_replace(' ', '', $phone_number);
                $phone_number = preg_replace('/\s+/', '', $phone_number);
                $phone_number = str_replace("‐","",$phone_number);
                if( strlen($phone_number) >= 10 ){
                    // unset($results[ $key]);
                    $faculty_number[]['phone'] = $phone_number;
                }
            }
        }
         
        $this->db->select('phone')->from('administrator'); 
		$this->db->where('is_active', 'yes');
        $query = $this->db->get();
        $results = $query->result_array(); 
        if( $results ){
            foreach ($results as $key => $value) {
                $phone_number = $value['phone'];
                $phone_number = str_replace(' ', '', $phone_number);
                $phone_number = preg_replace('/\s+/', '', $phone_number);
                $phone_number = str_replace("‐","",$phone_number);
                if( strlen($phone_number) >= 10 ){
                    // unset($results[ $key]);
                    $faculty_number[]['phone'] = $phone_number;
                }
            }
        }
         
        $this->db->select('phone')->from('cafeteria_staff'); 
		$this->db->where('is_active', 'yes');
        $query = $this->db->get();
        $results = $query->result_array(); 
        if( $results ){
            foreach ($results as $key => $value) {
                $phone_number = $value['phone'];
                $phone_number = str_replace(' ', '', $phone_number);
                $phone_number = preg_replace('/\s+/', '', $phone_number);
                $phone_number = str_replace("‐","",$phone_number);
                if( strlen($phone_number) >= 10 ){
                    // unset($results[ $key]);
                    $faculty_number[]['phone'] = $phone_number;
                }
            }
        }
         
        $this->db->select('phone')->from('coordinator'); 
		$this->db->where('is_active', 'yes');
        $query = $this->db->get();
        $results = $query->result_array(); 
        if( $results ){
            foreach ($results as $key => $value) {
                $phone_number = $value['phone'];
                $phone_number = str_replace(' ', '', $phone_number);
                $phone_number = preg_replace('/\s+/', '', $phone_number);
                $phone_number = str_replace("‐","",$phone_number);
                if( strlen($phone_number) >= 10 ){
                    // unset($results[ $key]);
                    $faculty_number[]['phone'] = $phone_number;
                }
            }
        }
        
        $this->db->select('phone')->from('dormitorydean'); 
		$this->db->where('is_active', 'yes');
        $query = $this->db->get();
        $results = $query->result_array(); 
        if( $results ){
            foreach ($results as $key => $value) {
                $phone_number = $value['phone'];
                $phone_number = str_replace(' ', '', $phone_number);
                $phone_number = preg_replace('/\s+/', '', $phone_number);
                $phone_number = str_replace("‐","",$phone_number);
                if( strlen($phone_number) >= 10 ){
                    // unset($results[ $key]);
                    $faculty_number[]['phone'] = $phone_number;
                }
            }
        }
         
        $this->db->select('phone')->from('guidance'); 
		$this->db->where('is_active', 'yes');
        $query = $this->db->get();
        $results = $query->result_array(); 
        if( $results ){
            foreach ($results as $key => $value) {
                $phone_number = $value['phone'];
                $phone_number = str_replace(' ', '', $phone_number);
                $phone_number = preg_replace('/\s+/', '', $phone_number);
                $phone_number = str_replace("‐","",$phone_number);
                if( strlen($phone_number) >= 10 ){
                    // unset($results[ $key]);
                    $faculty_number[]['phone'] = $phone_number;
                }
            }
        }
         
        $this->db->select('phone')->from('librarians'); 
		$this->db->where('is_active', 'yes');
        $query = $this->db->get();
        $results = $query->result_array(); 
        if( $results ){
            foreach ($results as $key => $value) {
                $phone_number = $value['phone'];
                $phone_number = str_replace(' ', '', $phone_number);
                $phone_number = preg_replace('/\s+/', '', $phone_number);
                $phone_number = str_replace("‐","",$phone_number);
                if( strlen($phone_number) >= 10 ){
                    // unset($results[ $key]);
                    $faculty_number[]['phone'] = $phone_number;
                }
            }
        }
         
        $this->db->select('phone')->from('prefect');
		$this->db->where('is_active', 'yes');		
        $query = $this->db->get();
        $results = $query->result_array(); 
        if( $results ){
            foreach ($results as $key => $value) {
                $phone_number = $value['phone'];
                $phone_number = str_replace(' ', '', $phone_number);
                $phone_number = preg_replace('/\s+/', '', $phone_number);
                $phone_number = str_replace("‐","",$phone_number);
                if( strlen($phone_number) >= 10 ){
                    // unset($results[ $key]);
                    $faculty_number[]['phone'] = $phone_number;
                }
            }
        }
		
		$this->db->select('phone')->from('human_resource');
		$this->db->where('is_active', 'yes');		
        $query = $this->db->get();
        $results = $query->result_array(); 
        if( $results ){
            foreach ($results as $key => $value) {
                $phone_number = $value['phone'];
                $phone_number = str_replace(' ', '', $phone_number);
                $phone_number = preg_replace('/\s+/', '', $phone_number);
                $phone_number = str_replace("‐","",$phone_number);
                if( strlen($phone_number) >= 10 ){
                    // unset($results[ $key]);
                    $faculty_number[]['phone'] = $phone_number;
                }
            }
        }
		
		$this->db->select('phone')->from('clinic');
		$this->db->where('is_active', 'yes');		
        $query = $this->db->get();
        $results = $query->result_array(); 
        if( $results ){
            foreach ($results as $key => $value) {
                $phone_number = $value['phone'];
                $phone_number = str_replace(' ', '', $phone_number);
                $phone_number = preg_replace('/\s+/', '', $phone_number);
                $phone_number = str_replace("‐","",$phone_number);
                if( strlen($phone_number) >= 10 ){
                    // unset($results[ $key]);
                    $faculty_number[]['phone'] = $phone_number;
                }
            }
        }
         
        return $faculty_number;
         
    }

    public function getactiveattendance($id = null) {
        // $this->db->select('teachers.id, teachers.name, teachers.middlename, teachers.lastname, student_attendences.id as student_attendences_id, student_attendences.student_session_id, student_attendences.attendence_type_id, student_attendences.date, student_attendences.attendence_type_id, student_attendences.subject_id, subjects.name as subjects_name, subjects.code as subjects_code')->from('teachers');
        $this->db->select('teachers.id'); 
        $this->db->select('teachers.name');  
        $this->db->select('teachers.middlename');  
        $this->db->select('teachers.lastname');  
        $this->db->select('ANY_VALUE ( student_attendences.id ) as student_attendences_id');  
        $this->db->select('ANY_VALUE ( student_attendences.student_session_id ) as student_session_id'); 
        $this->db->select('ANY_VALUE ( student_attendences.attendence_type_id ) as attendence_type_id'); 
        $this->db->select('ANY_VALUE ( student_attendences.date ) as date');  
        $this->db->select('ANY_VALUE ( student_attendences.subject_id ) as subject_id');  
        $this->db->select('ANY_VALUE ( subjects.name) as subjects_name'); 
        $this->db->select('ANY_VALUE ( subjects.code) as subjects_code');
        $this->db->from('teachers');
        $this->db->join('student_attendences', 'student_attendences.teacher_id = teachers.id and student_attendences.date = "'.date('Y-m-d').'"','left');
        $this->db->join('subjects', 'student_attendences.subject_id = subjects.id','left');
        $this->db->where('teachers.is_active', 'yes'); 
        if ($id != null) {
            $this->db->where('teachers.id', $id);
        } else {
            $this->db->group_by('teachers.id');
        }

        $this->db->order_by('teachers.lastname', 'ASC'); 
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array(); 
        } else {
            return $query->result_array(); 
        }
    }

    public function getactiveattendanceteacherdate($id, $date ) {
        $this->db->select('teachers.id, teachers.name, teachers.middlename, teachers.lastname, student_attendences.id as student_attendences_id, student_attendences.student_session_id, student_attendences.attendence_type_id, student_attendences.date, student_attendences.attendence_type_id, student_attendences.subject_id, subjects.name as subjects_name, subjects.code as subjects_code')->from('teachers');

        $this->db->join('student_attendences', 'student_attendences.teacher_id = teachers.id','left');
        $this->db->join('subjects', 'student_attendences.subject_id = subjects.id','left');
        $this->db->where('teachers.is_active', 'yes'); 
        $this->db->where('student_attendences.date', $date); 
        if ($id != null) {
            $this->db->where('teachers.id', $id);
        } else {
            $this->db->group_by('teachers.id');
        }

        $this->db->order_by('teachers.lastname', 'ASC'); 
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array(); 
        } else {
            return $query->result_array(); 
        }
    }
     
    
    public function getactiveattendancenumber($todays_date = null) {
        if( empty($todays_date)){
            $todays_date = date('Y-m-d');
        } 

        $this->db->select('count(`teachers`.`id`) as teachers_id')->from('teachers');
        $this->db->where('teachers.is_active', 'yes'); 
        $query1 = $this->db->get();
        $results1 = $query1->row_array();
        $active_teachers_count = $results1['teachers_id'];
        
        $this->db->select('count(`teachers`.`id`) as teachers_id')->from('teachers');
        $this->db->join('student_attendences', 'student_attendences.teacher_id = teachers.id','left');
        $this->db->where('teachers.is_active', 'yes'); 
        $this->db->where('student_attendences.date', $todays_date); 
        $this->db->group_by('student_attendences.teacher_id');
        $query2 = $this->db->get();
        $results2 = $query2->result_array();
        // $using_teachers_count = $results2['teachers_id'];
        $using_teachers_count =  count( $results2  );
        
        $results_return = array('active_teachers_count' => $active_teachers_count , 'using_teachers_count' => $using_teachers_count  );
 
        return $results_return; 
        
    }

    public function getactiveattendanceteacher($today_date = null){
        if( empty($todays_date)){
            $todays_date = date('Y-m-d');
        }

        // var_dump($todays_date);
        // die;

        $this->db->select('`teachers`.`id` as teachers_id, teachers.name, teachers.lastname')->from('teachers');
        $this->db->join('student_attendences', 'student_attendences.teacher_id = teachers.id','left');
        $this->db->where('teachers.is_active', 'yes'); 
        $this->db->where('student_attendences.date', $todays_date); 
        $this->db->group_by('student_attendences.teacher_id');
        $query = $this->db->get();
        $results = $query->result_array();

        return $results;
    }

    public function getactiveattendanceteacherid($today_date = null){
        if( empty($todays_date)){
            $todays_date = date('Y-m-d');
        }

        // var_dump($todays_date);
        // die;

        $this->db->select('`teachers`.`id` as teachers_id')->from('teachers');
        $this->db->join('student_attendences', 'student_attendences.teacher_id = teachers.id','left');
        $this->db->where('teachers.is_active', 'yes'); 
        $this->db->where('student_attendences.date', $todays_date); 
        $this->db->group_by('student_attendences.teacher_id');
        $query = $this->db->get();
        $results = $query->result_array();

        $teachersids = array_column($results, 'teachers_id');

        return $teachersids;
    }

    public function getactiveteachersid() {
        $this->db->select('teachers.id as teacher_id')->from('teachers');
        $this->db->where('teachers.is_active', 'yes');
        $query = $this->db->get();
        $results = $query->result_array();

        $teacherIds = array_column($results, 'teacher_id');

        return $teacherIds;
    }

    public function davegetcleanteacher($id=null) {
        $this->db->select('teachers.id, teachers.name as first_name, teachers.middlename as middle_name, teachers.lastname as last_name')->from('teachers');
        $this->db->where('teachers.id', $id);
        $query = $this->db->get();
        $results = $query->row_array();

        if ($results) {
            // Capitalize the first letter of each word in first name, middle name, and last name
            $results['first_name'] = ucwords(strtolower($results['first_name']));
            $results['middle_name'] = ucwords(strtolower($results['middle_name']));
            $results['last_name'] = ucwords(strtolower($results['last_name']));
    
            // Format middle name to include only the first letter followed by a dot
            if (!empty($results['middle_name'])) {
                $results['middle_name'] = substr($results['middle_name'], 0, 1) . '.';
                $cleaned_name = $results['first_name'] . ' ' . $results['middle_name'] . ' ' . $results['last_name'];
            } else {
                $cleaned_name = $results['first_name'] . ' ' . $results['last_name'];
            }
    
            // Combine first name, middle name (with dot), and last name
            
    
            // Update results with cleaned name
            $results['cleaned_name'] = $cleaned_name;
        }
    
        return $results;    // Prince John Michael B. Dacay
    }

   
     public function getteacherswhoimport( $session_id, $quarter=1 ) {
        if( empty($session_id)){
            $selected_session_id = $this->current_session; 
        } else {
            $selected_session_id = $session_id; 
        }
        $this->load->model('examresult_model');

        /*SELECT
        teacher_subjects.*, subjects.name, subjects.code, teachers.name,  teachers.middlename,  teachers.lastname 
        FROM
        `teacher_subjects`
        INNER JOIN subjects on subjects.id = teacher_subjects.subject_id 
        INNER JOIN teachers on teachers.id = teacher_subjects.teacher_id 
        WHERE
        `teacher_subjects`.`session_id` = '13' */

        $this->db->select('teacher_subjects.*');
        $this->db->select('subjects.name as subject_name, subjects.code as subject_code');
        $this->db->select('teachers.name as firstname,  teachers.middlename,  teachers.lastname');
        $this->db->select('class_sections.class_id,  class_sections.section_id'); 
        $this->db->select('classes.class as class_name, sections.section as section_name');
        $this->db->from('teacher_subjects'); 
        $this->db->join('subjects', 'subjects.id = teacher_subjects.subject_id','INNER');
        $this->db->join('teachers', 'teachers.id = teacher_subjects.teacher_id','INNER');
        $this->db->join('class_sections', 'class_sections.id = teacher_subjects.class_section_id','INNER');
        $this->db->join('classes', 'classes.id = class_sections.class_id','INNER');
        $this->db->join('sections', 'sections.id = class_sections.section_id','INNER'); 
        $this->db->where('teacher_subjects.session_id', $selected_session_id ); 
        $query1 = $this->db->get();
        $result_array = $query1->result_array(); 
        $return_array = array();
        if( $result_array ){
            foreach ($result_array as $result_key => $result_value) {
                # code...
                $class_id = $result_value['class_id'];
                $section_id = $result_value['section_id'];
                $subject_id = $result_value['subject_id']; 
                $resultx = $this->examresult_model->get_exam_results_by_class_subject_quarter(  $class_id , $section_id, $subject_id , $quarter );
                if( $resultx ){
                    $result_value['submitted'] = 'yes';
                } else {
                    $result_value['submitted'] = 'no';
                } 

                 $return_array[] =  $result_value;
            }
        }
         
 
        return $return_array; 
        
    }
	
	 public function getactiveadvisory($id = null) {
        $this->db->select()->from('teachers');
        $this->db->where('is_active', 'yes');
        $this->db->where('type_teacher', 'Advisory');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('teachers.lastname','ASC');
            $this->db->order_by('teachers.name','ASC');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array(); 
        } else {
            return $query->result_array(); 
        }
    }

     public function getactiveteachers($id = null) {
        $this->db->select()->from('teachers');
        $this->db->where('is_active', 'yes');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('teachers.lastname','ASC');
            $this->db->order_by('teachers.name','ASC');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array(); 
        } else {
            return $query->result_array(); 
        }
    }

}
