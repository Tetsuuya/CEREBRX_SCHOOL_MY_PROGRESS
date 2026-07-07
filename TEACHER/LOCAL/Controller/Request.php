<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Request extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->lang->load('message', 'english');
        $this->load->library('auth');
        $this->load->library('excel');
		$this->load->library('spout');
		$this->load->library('excelwithspout');
		$this->load->model('specializedsubject_model');
		$this->load->model('customsubject_model');
		$this->load->model('importgradesdetails_model');
		$this->load->model('importgraderequest_model');
		$this->load->model('setting_model');
		$this->auth->is_logged_in_teacher();
    } 
	
	public function index() {

    
        $this->session->set_userdata('top_menu', 'Examinations');
        $this->session->set_userdata('sub_menu', 'request/index');
		$student_session_data = $this->session->userdata("student"); 
		$teacher_id = $student_session_data['teacher_id']; 	
		$setting_result = $this->setting_model->get();
		$update_grade = $setting_result[0]['update_grade'];
		$data['firstqsettings'] = $setting_result[0]['first_quarter'];
		$data['secondqsettings'] = $setting_result[0]['second_quarter'];
		$data['thirdqsettings'] = $setting_result[0]['third_quarter'];
		$data['fourthqsettings'] = $setting_result[0]['fourth_quarter'];
		$data['update_grade'] = $update_grade;
        $data['title'] = 'Add Grade';
        $data['title_list'] = 'Grade Details';
       
        $data['getquarter'] = $this->customlib->getQuarter();
        $session = $this->session_model->getAllSession();
		$setting_result = $this->setting_model->get();
		$session_id = $setting_result[0]['session_id'];
		$session_session_id = $this->session->userdata('grade_set_session');
		if( $session_session_id ){
			$session_id = $session_session_id;
		}
		$data['current_session_id'] =  $setting_result[0]['session_id'];
		$data['previous'] = '16';
		$data['session_id'] = $session_id;
		$data['sessionlist'] = $session;
		$listgrade = $this->importgraderequest_model->getrequestByteacher( $teacher_id, $session_id );
        $data['listgrade'] = $listgrade;
        $data['listsubjects'] = $this->teachersubject_model->getTeacherSubjectsGroupID($teacher_id,null, $session_id);
        $this->form_validation->set_rules('session_id', 'Session', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
	        $this->load->view('layout/teacher/header');
	        $this->load->view('teacher/grade/request', $data);
	        $this->load->view('layout/teacher/footer');
   		 } else {
   		 	$session_id = $this->input->post('session_id');
   		 	$this->session->set_userdata(array('grade_set_session'=>$session_id));
			$data['session_id'] = $session_id;
			$listgrade = $this->importgraderequest_model->getrequestByteacher( $teacher_id, $session_id );
        	$data['listgrade'] = $listgrade;
        	$data['listsubjects'] = $this->teachersubject_model->getTeacherSubjectsGroupID($teacher_id,null, $session_id);
   		 	$this->load->view('layout/teacher/header');
	        $this->load->view('teacher/grade/request', $data);
	        $this->load->view('layout/teacher/footer');
   		 }
    }
	
	public function request_grade(){
		$student_id = $this->input->post('student_id');
		$session_id = $this->input->post('set_session_id');
		$quarter = $this->input->post('quarter');
		$subject_id = $this->input->post('subject_id');
		$teacher_remarks = $this->input->post('teacher_remarks');
		$to_grade = $this->input->post('to_grade');
		$is_INC = $this->input->post('is_INC');

		$student_session_data = $this->session->userdata("student"); 
		$teacher_id = $student_session_data['teacher_id']; 
		$current_datetime = $this->setting_model->getDatetime();
		//$session_id = $this->setting_model->getCurrentSession();

		if( empty($student_id) ){
			$this->session->set_flashdata('msg', '<div class="alert alert-warning text-left">No student selected.</div>');
		 	redirect('teacher/request/'); 	 	
		}
		if( empty($subject_id) ){
			$this->session->set_flashdata('msg', '<div class="alert alert-warning text-left">No Subject selected.</div>');
		 	redirect('teacher/request/'); 	 	
		}
		if( empty($quarter) ){
			$this->session->set_flashdata('msg', '<div class="alert alert-warning text-left">No Quarter selected.</div>');
		 	redirect('teacher/request/'); 	 	
		}

		if( empty($to_grade) ){
			$this->session->set_flashdata('msg', '<div class="alert alert-warning text-left">No Quarter selected.</div>');
		 	redirect('teacher/request/'); 	 	
		}

		$get_exam_results = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, $quarter, $subject_id, $session_id );
		$grades = (int)$get_exam_results->grades;
		$exam_results_id = $get_exam_results->exam_results_id;

		$get_import_results = $this->importgradesdetails_model->getApprovedUploadedGradeSubjectQuarter($student_id, $subject_id, $quarter, $session_id );
		$get_import_grades = $get_import_results['grades'];
		$import_grades_details_id = $get_import_results['import_grades_details_id'];
		$batch_id = $get_import_results['batch_id'];
		
		if( empty( $get_exam_results ) && $get_import_results ){
			$grades = $get_import_grades;
		}


  		if( $grades ){
			
			$details = array(
				'student_id' => $student_id,
				'subject_id' => $subject_id,
				'session_id' => $session_id,
				'quarter' => $quarter,
				'from' => $grades,
				'to' => $to_grade,
				'date_requested' => $current_datetime,
				'teacher_remarks' => $teacher_remarks,
				'teacher_id' => $teacher_id,
				'is_INC' => $is_INC,
				
			 );

			if( $batch_id ){
				$details1 = array(
					'grade_details_id' => $import_grades_details_id,
					'batch_id' => $batch_id,
				);
				$details = array_merge( $details, $details1 );
			}

			if( $exam_results_id ){
				$details = array_merge( $details, array('exam_results_id' => $exam_results_id ) );
			}
		} else {
			$details = array(
				'student_id' => $student_id,
				'subject_id' => $subject_id,
				'session_id' => $session_id,
				'quarter' => $quarter,
				'to' => $to_grade,
				'date_requested' => $current_datetime,
				'teacher_remarks' => $teacher_remarks,
				'teacher_id' => $teacher_id,
				'is_INC' => $is_INC,
				'is_empty' => 'yes',
			 );

			
			if( $exam_results_id ){
				$details = array_merge( $details, array('exam_results_id' => $exam_results_id ) );
			}
		}


		// if( $grade_request_id ){
		// 	$details = array_merge( $details, array('id'=>$grade_request_id));
		// }
		$this->importgraderequest_model->add( $details );
		$this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Submit request successfully.</div>');
		
			redirect('teacher/request'); 	 	
	}
	
	function getsearchrequest(){
        $grade_details_id = $this->input->post('grade_details_id'); 
        $resultlist = $this->importgraderequest_model->check_data_exists($grade_details_id);
        // echo  $resultlist ;   
        echo json_encode($resultlist);  
        # code...
    }
	
	function getsearchid(){
        $grade_request_id = $this->input->post('grade_request_id'); 
        $resultlist = $this->importgraderequest_model->get($grade_request_id);
		
		if( $resultlist ){
			// $coordinator_id = $resultlist['coordinator_id'];
			// if( $coordinator_id ){
			// 	$coordinator_details = $this->coordinator_model->get( $coordinator_id );
			// 	$resultlist['coordinator_firstname'] = $coordinator_details['name'];
			// 	$resultlist['coordinator_lastname'] = $coordinator_details['lastname'];
			// }
		}
        // echo  $resultlist ;   
        echo json_encode($resultlist);  
        # code...
    }
	
	function remove($id) {
        $this->importgraderequest_model->remove($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left"><i class="fa fa-check-square-o" aria-hidden="true"></i> Request Deleted Successfully.</div>');
        redirect('teacher/request/index');
    }
	
	public function edit(){
		$grade_request_id = $this->input->post('grade_request_id');
		$comment_request = $this->input->post('comment_request');
		$to_grade = $this->input->post('to_grade');
		$current_datetime = $this->setting_model->getDatetime();
		if( $grade_request_id ){
			 $details = array(
				'to' => $to_grade,
				'status' => 'pending',
				'role_requested' => 'teacher',
				'date_requested' => $current_datetime,
				'comment_requested' => $comment_request,
			 );
			if( $grade_request_id ){
				$details = array_merge( $details, array('id'=>$grade_request_id));
			} 	
			$this->importgraderequest_model->add( $details );
		}
		 $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Submit request successfully.</div>');
		redirect('teacher/request/index'); 	 	
	}


	function search_grade(){
        $student_id = $this->input->post('student_id'); 
        $subject_id = $this->input->post('subject_id'); 
        $quarter = $this->input->post('quarter'); 
        $resultlist = $this->importgraderequest_model->getByStudentSubjectQuarter($student_id, $subject_id, $quarter );
		
		if( $resultlist ){
			$result = '';
			ob_start();
			?>

			<?php
			$result = ob_get_contents();
			ob_clean( ); 
			?>
			<?php
			$coordinator_id = $resultlist['coordinator_id'];
			if( $coordinator_id ){
				$coordinator_details = $this->coordinator_model->get( $coordinator_id );
				$resultlist['coordinator_firstname'] = $coordinator_details['name'];
				$resultlist['result'] = $coordinator_details['lastname'];
			}
		} else {
			ob_start();
			?>
			<div class="alert alert-info">No Grade Submitted!</div>
			<?php
			$result = ob_get_contents();
			ob_clean( ); 
		}
        // echo  $resultlist ;   
        echo json_encode($resultlist);  
        # code...
    }
}


?>
