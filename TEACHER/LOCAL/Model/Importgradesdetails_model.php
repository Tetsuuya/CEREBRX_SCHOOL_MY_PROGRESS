<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Importgradesdetails_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
		 $this->current_date = $this->setting_model->getDateYmd();
		
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function get($id = null) {
        $this->db->select('import_grades_details.id as import_grades_details_id, classes.id, classes.class as "class_name",sections.id, sections.section as "section_name",subjects.id, subjects.name as "subject_name", import_grades_details.quarter, import_grades_details.grades, import_grades_details.student_id, approved, student_id, import_grades_details.subject_id, import_grades_batch.session_id,post')->from('import_grades_details');
		$this->db->join('classes', 'import_grades_details.class_id = classes.id ');
		$this->db->join('sections', 'import_grades_details.section_id = sections.id ', 'LEFT');
		$this->db->join('subjects', 'import_grades_details.subject_id = subjects.id ');
		$this->db->join('import_grades_batch', 'import_grades_details.batch_id = import_grades_batch.id ');
        if ($id != null) {
            $this->db->where('import_grades_details.id', $id);
        } else {
            $this->db->order_by('import_grades_details.id');
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
        $this->db->delete('import_grades_details');
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
            $this->db->update('import_grades_details', $data); 
        } else {
            $this->db->insert('import_grades_details', $data); 
            return $this->db->insert_id();
        }
    }
	
	public function removeByBatch($id) {
        $this->db->where('batch_id', $id);
        $this->db->delete('import_grades_details');
    }
	
	public function getAllDetailsByBatch( $id ) {
        $this->db->select('import_grades_details.batch_id, import_grades_details.id as import_grades_details_id, classes.id, classes.class as "class_name",sections.id, sections.section as "section_name",subjects.id, subjects.name as "subject_name", import_grades_details.quarter, import_grades_details.grades, import_grades_details.student_id, approved , students.admission_no,students.firstname,students.lastname, lrn')->from('import_grades_details');
		$this->db->join('classes', 'import_grades_details.class_id = classes.id ');
		$this->db->join('sections', 'import_grades_details.section_id = sections.id ', 'LEFT');
		$this->db->join('subjects', 'import_grades_details.subject_id = subjects.id ');
		$this->db->join('students', 'import_grades_details.student_id = students.id ');
        $this->db->where('import_grades_details.batch_id', $id);
        $this->db->order_by('import_grades_details.id');
		
        $query = $this->db->get();
        return $query->result_array(); 
    }
	
	public function approved_details( $id ){
			
		$details = $this->importgradesdetails_model->get( $id );
		$student_id = $details['student_id'];
		$subject_id = $details['subject_id'];
		$quarter = $details['quarter'];	
		$grades = $details['grades'];	
		$session_id = $details['session_id'];	
		
		if( empty($session_id) ){
			 $session_id = $this->current_session;
		}
		
		$check_details = $this->examresult_model->ifexist( $student_id, $subject_id, $quarter, $session_id );
		if( $check_details ){
			return false;
		} else {
			$data = array(
				'student_id' => $student_id,
				'session_id' => $session_id,
				'subject_id' => $subject_id,
				'quarter' => $quarter,
				'get_marks' => $grades,
				'is_active' => 'yes',
				'created_at' => $this->current_date
			);
			
			$insert_id = $this->examresult_model->add( $data );
			
			return $insert_id;
		}
	}
	
	public function approved_batch( $approved_grades, $subject_id, $quarter, $batch_id=null ){
		$count_approved_grades = count( $approved_grades );
		$approved_grades_update_list = array();
		$approved_grades_added_list = array();
		$notification = array();
		$get_quarter = $this->setting_model->getquarter( $quarter );
		$subject_details = $this->subject_model->get(  $subject_id );
		$subject_name = $subject_details['name'];
		$save_data = false;
		for( $x=0; $x<$count_approved_grades;$x++ ){
			$approved_grades_id = $approved_grades[$x];
			$details = $this->importgradesdetails_model->get( $approved_grades_id );
			$student_id = $details['student_id'];
			$grades = $details['grades'];	
			$session_id = $details['session_id'];	
			if( empty($session_id) ){
				$session_id = $this->current_session;
			}
			$check_details = $this->examresult_model->ifexist( $student_id, $subject_id, $quarter, $session_id );
			if( empty( $check_details ) ){
				$approved_grades_update_list[] = array(
					'id' => $approved_grades_id,
					'approved' => 1
				);
				$approved_grades_added_list[] = array(
				'student_id' => $student_id,
				'subject_id' => $subject_id,
				'quarter' => $quarter,
				'get_marks' => $grades,
				'session_id' => $session_id,
				'is_active' => 'yes',
				'created_at' => $this->current_date
				);
			} else {
				$student_details = $this->student_model->get( $student_id );
				$firstname = $student_details['firstname'];
				$lastname = $student_details['lastname'];

				$notification[] =  '<div class="alert alert-warning"> Student <strong>'.$firstname.' '.$lastname.'</strong> grade(<strong>'.$subject_name.'</strong>) for <strong>'.$get_quarter.'</strong> approved already. </div>';	
			}
		}		
		
		if( count( $approved_grades_added_list ) > 0 ){
			$this->db->insert_batch( 'exam_results', $approved_grades_added_list );
			$this->db->update_batch( 'import_grades_details',$approved_grades_update_list, 'id' );
			$notification[] = '<div class="alert alert-success"> Approved data successfully. </div>';
			$save_data = true;
		} else {
			$notification[] = '<div class="alert alert-warning"> Approved data unsuccessfully. </div>';
			$save_data = false;
		}

		$session_details = $this->session->userdata('student');
		$userid = $session_details['id'];
		$description = $save_data == true?'data save successfully.':'data save unsuccessfully.';
		$savelogs = array(
        	'user_id' => $userid,
        	'action' => 'approve',
        	'module' => 'import_grades_batch',
        	'description' => $description,
    	);
    	if( $batch_id ){
    		$savelogs = array_merge($savelogs, array('module_id' => $batch_id ));
    	}

    	$this->gradelogs_model->add( $savelogs );
		
		return $notification;
		
	}	
		
	public function delete_batch( $approved_grades ){
		
		$this->db->where_in('id', $approved_grades);
		$this->db->delete('import_grades_details');
		
		return true;
	}	
	
	/* public function get_pending_status( $batch_id ){
		$status = '';
		$query = $this->db->query('
				SELECT COUNT(*) AS pending
				FROM   import_grades_details WHERE `approved` = 0 AND `batch_id`= '.$batch_id.'
				'); 
		 $result = $query->row_array(); 	
		 
		 if( $result ){
			 foreach( $result as $key => $value ){
				 if( $key == 'pending' && $value != 0 ){
					$status .= "&nbsp;<span class=\"alert-warning\">".$key."(".$value.")</span>";
				 } elseif( $key == 'pending' && $value == 0  ){
					$query = $this->db->query('
							SELECT COUNT(*) AS COUNTROW
							FROM   import_grades_details WHERE `batch_id`= '.$batch_id.'
							'); 
					$result1 = $query->row_array(); 	
					$status .= "&nbsp;<span class=\"alert-success\">approved (".$result1['COUNTROW'].")</span>";
				 }
			 }
		 }
		 
		return $status;
	} */
	public function get_pending_status( $batch_id ){
		if( is_array( $batch_id ) ){
			$result  = $batch_id;
		} else {
			$result = $this->pending_status( $batch_id );
		}
		
		$status = '';
		 if( $result ){
			 if( $result['total'] == 0 ){
				$status .= "&nbsp;<span style='font-size:12px;' class=\"label label-danger\">Empty</span>"; 
			 } elseif( $result['approved'] != 0 && $result['pending'] != 0  ){
				  $status .= "&nbsp;<span style='font-size:12px;' class=\"label label-success\">Approved ( ".$result['approved']." ) </span>  &nbsp;<span style='font-size:12px;' class=\"label label-warning\">Pending ( ".$result['pending']." ) </span>"; 
			 } elseif( $result['pending'] != 0 ){
				 $status .= "&nbsp;<span style='font-size:12px;' class=\"label label-warning\">Pending ( ".$result['pending']." )</span>"; 
			 } elseif( $result['total'] == $result['approved'] || $result['total'] != 0 && $result['pending'] != 0  ){
				  $status .= "&nbsp;<span style='font-size:12px;' class=\"label label-success\">Approved</span>"; 
			 } 
		 }

		 
		return $status;
	}
	// academichead workflow
	public function pending_status( $batch_id ){
		$query = $this->db->query('
				SELECT 
				COUNT(CASE WHEN `approved` = "0" AND `batch_id`= '.$batch_id.' THEN 1 END ) AS pending,
				COUNT(CASE WHEN `batch_id`= '.$batch_id.' THEN 1 END ) AS total,
				COUNT(CASE WHEN `approved` = "1" AND `batch_id`= '.$batch_id.' THEN 1 END ) AS approved
				FROM import_grades_details
				'); 
		 $result = $query->row_array(); 	
	
		 
		return $result;
	}
	
	
	public function getByStudentSubjectQuarter($student_id, $subject_id, $quarter, $session_id=null ) {
        $this->db->select('*')->from('import_grades_details');
		$this->db->join('import_grades_batch', 'import_grades_details.batch_id = import_grades_batch.id ');
        $this->db->where('import_grades_details.student_id', $student_id);
        $this->db->where('import_grades_details.subject_id', $subject_id);
        $this->db->where('import_grades_details.quarter', $quarter);
		if(empty( $session_id )){
		 $session_id = $this->current_session; 	
		}
        $this->db->where('import_grades_batch.session_id', $session_id );
        $query = $this->db->get();
		
        return $query->row_array();
    }
	
	public function getAllDetailsByBatchGender( $id, $gender='Male') {
        $this->db->select('import_grades_details.batch_id, import_grades_details.id as import_grades_details_id, classes.id, classes.class as "class_name",sections.id, sections.section as "section_name",subjects.id, subjects.name as "subject_name", import_grades_details.quarter, import_grades_details.grades, import_grades_details.student_id, approved , import_grades_details.post,  students.admission_no,students.firstname,students.lastname, lrn, import_grades_details.final_approved')->from('import_grades_details');
		$this->db->join('classes', 'import_grades_details.class_id = classes.id ');
		$this->db->join('sections', 'import_grades_details.section_id = sections.id ', 'LEFT');
		$this->db->join('subjects', 'import_grades_details.subject_id = subjects.id ');
		$this->db->join('students', 'import_grades_details.student_id = students.id ');
        $this->db->where('import_grades_details.batch_id', $id);
        $this->db->where('students.gender', $gender);
		$this->db->order_by('students.lastname', 'ASC');
        $this->db->order_by('students.firstname', 'ASC');
		
        $query = $this->db->get();
		//printx( $this->db->last_query());
        return $query->result_array(); 
    }
	
	public function getApprovedDetails($student_id, $subject_id, $quarter, $session_id=null ) {
        $this->db->select('*')->from('import_grades_details');
		$this->db->join('import_grades_batch', 'import_grades_details.batch_id = import_grades_batch.id ');
        $this->db->where('import_grades_details.student_id', $student_id);
        $this->db->where('import_grades_details.subject_id', $subject_id);
        $this->db->where('import_grades_details.quarter', $quarter);
		if(empty( $session_id )){
		 $session_id = $this->current_session; 	
		}
        $this->db->where('import_grades_batch.session_id', $session_id );
        $this->db->where('import_grades_details.approved', 1 );
        $query = $this->db->get();
		
        return $query->row_array();
    }

        public function getSubjectTeacherUpload($teacher_id, $session_id = null ){
    	if( empty(  $session_id )){
    		$session_id = $this->current_session;
    	}
        $this->db->select('*')->from('import_grades_details');
		$this->db->join('import_grades_batch', 'import_grades_details.batch_id = import_grades_batch.id ');
		$this->db->join('classes', 'import_grades_details.class_id = classes.id ');
		$this->db->join('subjects', 'import_grades_details.subject_id = subjects.id ');
        $this->db->where('import_grades_batch.teacher_id', $teacher_id);
        $this->db->where('import_grades_batch.session_id', $session_id );
        $this->db->where('import_grades_batch.published', 1 );
        $this->db->group_by('import_grades_batch.subject_id' );

        $query = $this->db->get();
		
        return $query->result_array();
    }

    public function getUploadedGradeSubjectQuarter($student_id, $subject_id, $quarter, $session_id=null ) {
        $this->db->select('*,import_grades_details.id as import_grades_details_id')->from('import_grades_details');
		$this->db->join('import_grades_batch', 'import_grades_details.batch_id = import_grades_batch.id ');
        $this->db->where('import_grades_details.student_id', $student_id);
        $this->db->where('import_grades_details.subject_id', $subject_id);
        $this->db->where('import_grades_details.quarter', $quarter);
		if(empty( $session_id )){
		 $session_id = $this->current_session; 	
		}
        $this->db->where('import_grades_batch.session_id', $session_id );
        $query = $this->db->get();
		
        return $query->row_array();
    }

    public function getlastestotherrequest($session_id = null, $teacher_id = null ) {
        if( empty( $session_id )){
            $session_id = $this->current_session;
        }
        $this->db->select('students.id as id,firstname,middlename,lastname,suffix,class,section,created_by,requestor_role,requestor,id_printing.status,id_printing.type,students.image,students.signature_image as signature,students.lrn,students.admission_no,students.dob,students.gender,students.blood_type,id_printing.id as printing_id,id_printing.status as printing_status');
        $this->db->from('id_printing');
        $this->db->join('students', 'id_printing.student_id = students.id', 'left'); 
        $this->db->join('student_session', 'student_session.student_id = students.id', 'left'); 
        $this->db->join('classes', 'student_session.class_id = classes.id', 'left'); 
        $this->db->join('sections', 'student_session.section_id = sections.id', 'left'); 
        $this->db->where('id_printing.session_id', $session_id);
        $this->db->where('student_session.session_id', $session_id);
        $this->db->where('id_printing.requestor_id <>', $teacher_id);
        $this->db->where('id_printing.status <>', 'delivered');
        $this->db->where('students.status', 'active');
        $this->db->order_by('created_by', 'DESC');
        $this->db->group_by('id_printing.student_id');
        $query = $this->db->get();
        
        return $query->result_array(); 
         
    }

     public function getApprovedUploadedGradeSubjectQuarter($student_id, $subject_id, $quarter, $session_id=null ) {
        $this->db->select('*,import_grades_details.id as import_grades_details_id')->from('import_grades_details');
		$this->db->join('import_grades_batch', 'import_grades_details.batch_id = import_grades_batch.id ');
        $this->db->where('import_grades_details.student_id', $student_id);
        $this->db->where('import_grades_details.subject_id', $subject_id);
        $this->db->where('import_grades_details.quarter', $quarter);
        $this->db->where('import_grades_details.approved', '1');
		if(empty( $session_id )){
		 $session_id = $this->current_session; 	
		}
        $this->db->where('import_grades_batch.session_id', $session_id );
        $query = $this->db->get();
		
        return $query->row_array();
    }

    public function pending_status_check( $batch_id ){
		$query = $this->db->query("
				SELECT import_grades_details.id from import_grades_details where batch_id = ".$batch_id."
				and import_grades_details.approved = 0;
				"); 
		 $result = $query->num_rows(); 	
	
		 
		return $result;
	}
}
