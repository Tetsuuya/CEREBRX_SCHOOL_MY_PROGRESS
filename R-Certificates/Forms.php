<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class forms extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->lang->load('message', 'english');
        $this->load->library('auth');
        $this->load->library('excel');
        $this->auth->is_logged_in_registrar(); 
        $this->load->model('Archiveexamresult_model');
        $this->load->model('registrar_model');
        $this->load->model('gradingsetting_model');
        $this->load->model('stuattendence_model');
        $this->load->model('stuattendencecustom_model');
        $this->load->model('subjectmanager_model');
        $this->load->model('subjectcombine_model');
        $this->load->model('archiveexamresult_model');
        $this->load->model('studentstrand_model');
    
    }

    function index() {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'report/forms');

		$data['getquarter'] = $this->customlib->getQuarter();
		$data['class_id'] = "";
        $data['section_id'] = "";
		$data['quarter'] = "";
		$data['semester_id'] = "";
		$data['getSemester'] = $this->customlib->getSemester();
		$session = $this->session_model->getAllSession();
		$setting_result = $this->setting_model->get();
		$data['session_id'] = $setting_result[0]['session_id'];
		$data['sessionlist'] = $session;
		$data['current_session'] = $this->setting_model->getCurrentSessionName();
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/reports/forms', $data);
        $this->load->view('layout/registrar/footer', $data);
    }

    function search() {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'report/junior');
        $data['title'] = 'Student Search';
		$student_id = $this->input->get('student_id');
		$data['getquarter'] = $this->customlib->getQuarter();
		$data['class_id'] = "";
        $data['section_id'] = "";
		$data['quarter'] = "";
		$data['semester_id'] = "";
		$data['getSemester'] = $this->customlib->getSemester();
		$session = $this->session_model->getAllSession();
		$setting_result = $this->setting_model->get();
		$data['session_id'] = $setting_result[0]['session_id'];
		$data['sessionlist'] = $session;
		$data['current_session'] = $this->setting_model->getCurrentSessionName();
		if( $student_id ){
			$resultlist = $this->student_model->searchAllStudentText($student_id);
			$data['resultlist'] = $resultlist;
		}
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/reports/forms', $data);
            $this->load->view('layout/registrar/footer', $data);
        } else {
			$search_text = $this->input->post('search_text');
			$session_id = $this->input->post('session_id');
			if ($search_text) {
				$resultlist = $this->student_model->searchAllStudentText($search_text );
                $data['resultlist'] = $resultlist;
               
            }
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/reports/forms', $data);
            $this->load->view('layout/registrar/footer', $data);
        }
    }
	
	function print_permanent_record(){  
		$student_id = $this->input->post('student_id');
		
		// other fields
		$lineone = $this->input->post('lineone');
		$linetwo = $this->input->post('linetwo');
		$intermediate = $this->input->post('intermediate');
		$yeear = $this->input->post('yeear');
		$total_years = $this->input->post('total_years');
		$gen_ave = $this->input->post('gen_ave');
		$eligibleto = $this->input->post('eligibleto');
		$place = $this->input->post('place');
		
		$grade_seven = $this->input->post('grade_seven');
		$grade_eight = $this->input->post('grade_eight');
		$grade_nine = $this->input->post('grade_nine');
		$grade_ten = $this->input->post('grade_ten');
		$grade_seven_days = $this->input->post('grade_seven_days');
		$grade_seven_present = $this->input->post('grade_seven_present');
		$grade_seven_total_years = $this->input->post('grade_seven_total_years');
		$grade_eight_days = $this->input->post('grade_eight_days');
		$grade_eight_present = $this->input->post('grade_eight_present');
		$grade_eight_total_years = $this->input->post('grade_eight_total_years');
		$grade_nine_days = $this->input->post('grade_nine_days');
		$grade_nine_present = $this->input->post('grade_nine_present');
		$grade_nine_total_years = $this->input->post('grade_nine_total_years');
		$grade_ten_days = $this->input->post('grade_ten_days');
		$grade_ten_present = $this->input->post('grade_ten_present');
		$grade_ten_total_years = $this->input->post('grade_ten_total_years');
		$remarks = $this->input->post('remarks');
		
		
		$getAllStudentsSession = $this->studentsession_model->getAllStudentsSession( $student_id );
		$grades_7_content = null;
		$grades_8_content = null;
		$grades_9_content = null;
		$grades_10_content = null;
		$grade_7_list_of_subjects = null;
		$grade_8_list_of_subjects = null;
		$grade_9_list_of_subjects = null;
		$grade_10_list_of_subjects = null;
		$registrar_id = $this->session->userdata['student']['registrar_id'];
		$registrar_result = $this->registrar_model->get($registrar_id);
		$registrar_name = $registrar_result['name'];
		$registrar_middlename = $registrar_result['middlename'];
		$registrar_lastname = $registrar_result['lastname'];
		if( $registrar_middlename  ){
			$registrar_middlename_first_character = substr($registrar_middlename, 0, 1);
			$registrar_middlename_display =  ucfirst(  $registrar_middlename_first_character ).'.';
		} else {
			 $registrar_middlename_display = '';
		}
		$registrar_fullname =  ucfirst( $registrar_name )." ".$registrar_middlename_display." ".ucfirst(   $registrar_lastname ); 
		$setting_result = $this->setting_model->get();
		$school_name = $setting_result[0]['name'];
		$school_name = isset($school_name)?$school_name:null;
	
		$school_address = !empty($setting_result[0]['address'])?$setting_result[0]['address']:"El Salvador City, Misamis Oriental";
		$form_data['from_school'] = null;
		$form_data['days_of_school'] = null;
		$form_data['days_present'] = null;
		$form_data['total_years'] = null;
		$set_current_7 = FALSE;
		$set_current_8 = FALSE;
		$set_current_9 = FALSE;
		$set_current_10 = FALSE;
		if( $getAllStudentsSession ){
			//$session_current = $this->setting_model->getCurrentSessionName(); 
			$student_details = $this->student_model->get( $student_id );
			$student_name  = $student_details['lastname'].', '.$student_details['firstname'].' '.$student_details['middlename'].' '.$student_details['suffix']; 
			$student_dob  = $student_details['dob']; 
			$student_gender  = $student_details['gender']; 
			$student_lrn  = $student_details['lrn']; 
			$student_admission_no  = $student_details['admission_no']; 
			
			$student_father_name  = $student_details['father_name']; 
			$student_father_midname  = $student_details['father_midname']; 
			$student_father_lastname = $student_details['father_lastname']; 
			$student_father_occupation = $student_details['father_occupation']; 

			$student_mother_name  = $student_details['mother_name']; 
			$student_mother_midname  = $student_details['mother_midname']; 
			$student_mother_lastname = $student_details['mother_lastname']; 
			$student_mother_occupation = $student_details['mother_occupation'];  
			$student_guardian_address = $student_details['guardian_address'];  
			$student_current_address = $student_details['current_address'];  
			$get_archive = $this->archiveexamresult_model->getStudentSessionClass( $student_id );
			if( $get_archive ){
				$getAllStudentsSession = array_merge( $get_archive, $getAllStudentsSession );
			}
			foreach( $getAllStudentsSession as $key => $student_result ){
				$form_data['from_school'] = null;
				$form_data['days_of_school'] = null;
				$form_data['days_present'] = null;
				$form_data['total_years'] = null;
				$form_data['session_id'] = null;
				$session_current = $student_result['session'];
				$school_year = "S. Y. ".$session_current;
				$centenary = substr($session_current, 0, 2); //2017-18 to 2017
				$class_id = $student_result['class_id'];
				$section_id = isset($student_result['section_id'])?$student_result['section_id']:'';  
				$class_array = $this->class_model->get($class_id); 
				$class_name = $class_array['class']; 
			
				/* $section_array = $this->section_model->get($section_id);
				$section_name = $section_array['section']; */
				$count = 1;
				
				if (strpos($class_name, '7') !== false) {
					$session_id = $student_result['session_id'];					
					$grade_7_list_of_subjects = $this->subject_model->getSubjctByClass( $class_id ); 
					$form_data['session_id'] = $session_id;
					$form_data['from_school'] = !empty($grade_seven)?$grade_seven:$school_name;
					$form_data['days_of_school'] = !empty($grade_seven_days)?$grade_seven_days:null;
					$form_data['days_present'] = !empty($grade_seven_present)?$grade_seven_present:null;
					$form_data['total_years'] = !empty($grade_seven_total_years)?$grade_seven_total_years:null;
					$grades_7_content = $this->generate_secondary_table_grades( $grade_7_list_of_subjects, $student_id, $session_id, $form_data  ); 	
				} 

				if (strpos($class_name, '8') !== false) { 
					$session_id = $student_result['session_id'];
					$grade_8_list_of_subjects = $this->subject_model->getSubjctByClass( $class_id ); 
					$form_data['session_id'] = $session_id;
					$form_data['from_school'] = !empty($grade_eight)?$grade_eight:$school_name;
					$form_data['days_of_school'] = !empty($grade_eight_days)?$grade_eight_days:null;
					$form_data['days_present'] =!empty($grade_eight_present)?$grade_eight_present:null;
					$form_data['total_years'] = !empty($grade_eight_total_years)?$grade_eight_total_years:null;
					$grades_8_content = $this->generate_secondary_table_grades( $grade_8_list_of_subjects, $student_id, $session_id, $form_data  );
					$set_current_8 = TRUE;
					
				} 

				if (strpos($class_name, '9') !== false) { 
					$session_id = $student_result['session_id'];
					$grade_9_list_of_subjects = $this->subject_model->getSubjctByClass( $class_id ); 
					$form_data['session_id'] = $session_id;
					$form_data['from_school'] = !empty($grade_nine)?$grade_nine:$school_name;
					$form_data['days_of_school'] = !empty($grade_nine_days)?$grade_nine_days:null;
					$form_data['days_present'] = !empty($grade_nine_present)?$grade_nine_present:null;
					$form_data['total_years'] = !empty($grade_nine_total_years)?$grade_nine_total_years:null;
					$grades_9_content = $this->generate_secondary_table_grades( $grade_9_list_of_subjects, $student_id, $session_id, $form_data );
				}
				
				if (strpos($class_name, '10') !== false) { 
					$session_id = $student_result['session_id'];
					$grade_10_list_of_subjects = $this->subject_model->getSubjctByClass( $class_id ); 
					$form_data['session_id'] = $session_id;
					$form_data['from_school'] = !empty($grade_ten)?$grade_ten:$school_name;
					$form_data['days_of_school'] = !empty($grade_ten_days)?$grade_ten_days:null;
					$form_data['days_present'] = !empty($grade_ten_present)?$grade_ten_present:null;
					$form_data['total_years'] = !empty($grade_ten_total_years)?$grade_ten_total_years:null;
					$grades_10_content = $this->generate_secondary_table_grades( $grade_10_list_of_subjects, $student_id, $session_id, $form_data );
				} 
				
				 if( empty( $grades_7_content ) ){
					$form_data['from_school'] = !empty($grade_eight)?$grade_eight:'';
					$form_data['days_of_school'] = !empty($grade_seven_days)?$grade_seven_days:null;
					$form_data['days_present'] = !empty($grade_seven_present)?$grade_seven_present:null;
					$form_data['total_years'] = !empty($grade_seven_total_years)?$grade_seven_total_years:null;
					$grades_7_content = $this->generate_secondary_table_grades( $grade_7_list_of_subjects, $student_id, null, $form_data ); 	
				}
				if( empty( $grades_8_content ) ){
					$form_data['from_school'] = !empty($grade_eight)?$grade_eight:'';
					$form_data['days_of_school'] = !empty($grade_eight_days)?$grade_eight_days:null;
					$form_data['days_present'] =!empty($grade_eight_present)?$grade_eight_present:null;
					$form_data['total_years'] = !empty($grade_eight_total_years)?$grade_eight_total_years:null;
					$grades_8_content = $this->generate_secondary_table_grades( $grade_8_list_of_subjects, $student_id, null,$form_data ); 	
				}
				if( empty( $grades_9_content ) ){
					$form_data['from_school'] = !empty($grade_nine)?$grade_nine:'';
					$form_data['days_of_school'] = !empty($grade_nine_days)?$grade_nine_days:null;
					$form_data['days_present'] = !empty($grade_nine_present)?$grade_nine_present:null;
					$form_data['total_years'] = !empty($grade_nine_total_years)?$grade_nine_total_years:null;
					$grades_9_content = $this->generate_secondary_table_grades( $grade_9_list_of_subjects, $student_id, null,$form_data ); 	
				}
				if( empty( $grades_10_content ) ){
					$form_data['from_school'] = !empty($grade_ten)?$grade_ten:'';
					$form_data['days_of_school'] = !empty($grade_ten_days)?$grade_ten_days:null;
					$form_data['days_present'] = !empty($grade_ten_present)?$grade_ten_present:null;
					$form_data['total_years'] = !empty($grade_ten_total_years)?$grade_ten_total_years:null;
					$grades_10_content = $this->generate_secondary_table_grades( $grade_10_list_of_subjects, $student_id ,null, $form_data ); 	
				}
			}
			
		}
		//printx( $grades_10_content );
        $data['grades_7_content'] = $grades_7_content; 
        $data['grades_8_content'] = $grades_8_content; 
        $data['grades_9_content'] = $grades_9_content; 
        $data['grades_10_content'] = $grades_10_content; 
		
       
        $data['school_name'] = $school_name;
        $data['school_address'] = isset($school_address)?$school_address:null;
        $data['school_year'] = isset($school_year)?$school_year:null;
        $data['class_name'] = isset($class_name)?$class_name:null;
        $data['section_name'] = isset($section_name)?$section_name:null;
        $data['student_name'] = isset($student_name)?$student_name:null;  
        $data['student_lrn'] = isset($student_lrn)?$student_lrn:null;   
        $data['student_admission_no'] = isset($student_admission_no)?$student_admission_no:null;   
        $student_dob = isset($student_dob)?$student_dob:null;   
        $data['student_dob'] = date("F d, Y", strtotime($student_dob));    
        $data['student_gender'] = isset($student_gender)?$student_gender:null;   
        $data['student_place_of_birth'] = isset($student_current_address)?$student_current_address:null;     
		$student_father_name = isset($student_father_name)?$student_father_name:null; 
		$student_father_midname = isset($student_father_midname)?$student_father_midname:null; 
		$student_father_midname = substr($student_father_midname, 0, 1); 
		$student_father_lastname = isset($student_father_lastname)?$student_father_lastname:null; 
		$student_father_lastname = isset($student_father_lastname)?$student_father_lastname:null; 
		$student_mother_name = isset($student_mother_name)?$student_mother_name:null; 
		$student_mother_midname = isset($student_mother_midname)?$student_mother_midname:null; 
		$student_mother_midname = substr($student_mother_midname, 0, 1); 
		$student_mother_lastname = isset($student_mother_lastname)?$student_mother_lastname:null; 
        $data['student_father'] = $student_father_midname != null ? $student_father_name.' '.$student_father_midname.'. '.$student_father_lastname:$student_father_name.' '.$student_father_lastname;    
        $data['student_father_occupation'] = $student_father_occupation;   
        $data['student_mother'] = $student_mother_midname != null ? $student_mother_name.' '.$student_mother_midname.'. '.$student_mother_lastname:$student_mother_name.' '.$student_mother_lastname; 
        $data['student_mother_occupation'] = isset($student_mother_occupation)?$student_mother_occupation:null;      
        $data['student_guardian_address'] = isset($student_guardian_address)?$student_guardian_address:null;      
        $data['quarter_display'] = ''; 
        $data['diplay_date'] = date('F d, Y'); 
        $data['registrar_fullname'] = isset($registrar_fullname)?$registrar_fullname:null;
		$data['lineone'] = $lineone;
		$data['linetwo'] = $linetwo;
		$data['intermediate'] = $intermediate;
		$data['yeear'] = $yeear;
		$data['total_years'] = $total_years;
		$data['gen_ave'] = $gen_ave;
		$data['eligibleto'] = $eligibleto;
		$data['place'] = $place;
		$data['remarks'] = $remarks;
        ob_start();
       
        $content = ob_get_contents();
        ob_end_clean(); 
        $data['content'] = $content;  
		
        $html = $this->load->view('template/secondary_student_permanent_record',$data, true );    
        $pdfFilePath = "Student_Permanent_Record_".$student_name.".pdf";
        $this->load->library('m_pdf'); 
        $this->m_pdf->pdf->mPDF('','LEGAL','0','','10','10','16','16','9','9', 'L');
        $this->m_pdf->pdf->AddPage();
        $this->m_pdf->pdf->use_kwt = true;
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "I");
    }

    function print_transcript_record(){ 
		$student_id = $this->input->post('student_id');
		// other fields
		$lineone = $this->input->post('lineone');
		$linetwo = $this->input->post('linetwo');
		$intermediate = $this->input->post('intermediate');
		$yeear = $this->input->post('yeear');
		$total_years = $this->input->post('total_years');
		$gen_ave = $this->input->post('gen_ave');
		$eligibleto = $this->input->post('eligibleto');
		$place = $this->input->post('place');
		
		$grade_seven = $this->input->post('grade_seven');
		$grade_eight = $this->input->post('grade_eight');
		$grade_nine = $this->input->post('grade_nine');
		$grade_ten = $this->input->post('grade_ten');
		$grade_seven_days = $this->input->post('grade_seven_days');
		$grade_seven_present = $this->input->post('grade_seven_present');
		$grade_seven_total_years = $this->input->post('grade_seven_total_years');
		$grade_eight_days = $this->input->post('grade_eight_days');
		$grade_eight_present = $this->input->post('grade_eight_present');
		$grade_eight_total_years = $this->input->post('grade_eight_total_years');
		$grade_nine_days = $this->input->post('grade_nine_days');
		$grade_nine_present = $this->input->post('grade_nine_present');
		$grade_nine_total_years = $this->input->post('grade_nine_total_years');
		$grade_ten_days = $this->input->post('grade_ten_days');
		$grade_ten_present = $this->input->post('grade_ten_present');
		$grade_ten_total_years = $this->input->post('grade_ten_total_years');
		$remarks = $this->input->post('remarks');
		
		$getAllStudentsSession = $this->studentsession_model->getAllStudentsSession( $student_id );
		$grades_7_content = null;
		$grades_8_content = null;
		$grades_9_content = null;
		$grades_10_content = null;
		$grade_7_list_of_subjects = null;
		$grade_8_list_of_subjects = null;
		$grade_9_list_of_subjects = null;
		$grade_10_list_of_subjects = null;
		$registrar_id = $this->session->userdata['student']['registrar_id'];
		$registrar_result = $this->registrar_model->get($registrar_id);
		$registrar_name = $registrar_result['name'];
		$registrar_middlename = $registrar_result['middlename'];
		$registrar_lastname = $registrar_result['lastname'];
		if( $registrar_middlename  ){
			$registrar_middlename_first_character = substr($registrar_middlename, 0, 1);
			$registrar_middlename_display =  ucfirst(  $registrar_middlename_first_character ).'.';
		} else {
			 $registrar_middlename_display = '';
		}
		$registrar_fullname =  ucfirst( $registrar_name )." ".$registrar_middlename_display." ".ucfirst(   $registrar_lastname ); 
		$setting_result = $this->setting_model->get();
		$school_name = $setting_result[0]['name'];
		$school_address = !empty($setting_result[0]['address'])?$setting_result[0]['address']:"El Salvador City, Misamis Oriental";
		$school_name = isset($school_name)?$school_name:null;
		$form_data['from_school'] = null;
		$form_data['days_of_school'] = null;
		$form_data['days_present'] = null;
		$form_data['total_years'] = null;
		
		if( $getAllStudentsSession ){
			$student_details = $this->student_model->get( $student_id );
			$student_name  = $student_details['lastname'].', '.$student_details['firstname'].' '.$student_details['middlename'].' '.$student_details['suffix']; 
			$student_dob  = $student_details['dob']; 
			$student_gender  = $student_details['gender']; 
			$student_lrn  = $student_details['lrn']; 
			$student_admission_no  = $student_details['admission_no']; 

			$student_father_name  = $student_details['father_name']; 
			$student_father_midname  = $student_details['father_midname']; 
			$student_father_lastname = $student_details['father_lastname']; 
			$student_father_occupation = $student_details['father_occupation']; 

			$student_mother_name  = $student_details['mother_name']; 
			$student_mother_midname  = $student_details['mother_midname']; 
			$student_mother_lastname = $student_details['mother_lastname']; 
			$student_mother_occupation = $student_details['mother_occupation'];  
			$get_archive = $this->archiveexamresult_model->getStudentSessionClass( $student_id );
			if( $get_archive ){
				$getAllStudentsSession = array_merge( $get_archive, $getAllStudentsSession );
			}
			/* $session_current = $this->setting_model->getCurrentSessionName(); 
			$centenary = substr($session_current, 0, 2); //2017-18 to 2017 */
			foreach( $getAllStudentsSession as $key => $student_result ){
				$session_current = $student_result['session'];
				$school_year = "S. Y. ".$session_current;
				$centenary = substr($session_current, 0, 2); //2017-18 to 2017
				//$student_result = $this->student_model->get(  $student_id );
				$session_id = $student_result['session_id'];
				$class_id = $student_result['class_id'];
				//$section_id = $student_result['section_id'];  
				
				$class_array = $this->class_model->get($class_id); 
				$class_name = $class_array['class']; 
				/* $section_array = $this->section_model->get($section_id);
				$section_name = $section_array['section']; */
				// $list_of_subjects = $this->subject_model->getSubjctByClass( $class_id );  
				// $grade_10_list_of_subjectsxx = $this->Archiveexamresult_model->getsubectbystudentclass(  $student_id, $class_id  );

				if (strpos($class_name, '7') !== false) {
					$session_id = $student_result['session_id'];					
					$grade_7_list_of_subjects = $this->subject_model->getSubjctByClass( $class_id ); 
					$form_data['session_id'] = $session_id;
					$form_data['from_school'] = !empty($grade_seven)?$grade_seven:$school_name;
					$form_data['days_of_school'] = !empty($grade_seven_days)?$grade_seven_days:null;
					$form_data['days_present'] = !empty($grade_seven_present)?$grade_seven_present:null;
					$form_data['total_years'] = !empty($grade_seven_total_years)?$grade_seven_total_years:null;
					$grades_7_content = $this->generate_secondary_table_group_grades( $grade_7_list_of_subjects, $student_id, $session_id, $form_data  ); 	
				} 

				if (strpos($class_name, '8') !== false) { 
					$session_id = $student_result['session_id'];
					$grade_8_list_of_subjects = $this->subject_model->getSubjctByClass( $class_id ); 
					$form_data['session_id'] = $session_id;
					$form_data['from_school'] = !empty($grade_eight)?$grade_eight:$school_name;
					$form_data['days_of_school'] = !empty($grade_eight_days)?$grade_eight_days:null;
					$form_data['days_present'] =!empty($grade_eight_present)?$grade_eight_present:null;
					$form_data['total_years'] = !empty($grade_eight_total_years)?$grade_eight_total_years:null;
					$grades_8_content = $this->generate_secondary_table_group_grades( $grade_8_list_of_subjects, $student_id, $session_id, $form_data  );
					$set_current_8 = TRUE;
					
				} 

				if (strpos($class_name, '9') !== false) { 
					$session_id = $student_result['session_id'];
					$grade_9_list_of_subjects = $this->subject_model->getSubjctByClass( $class_id ); 
					$form_data['session_id'] = $session_id;
					$form_data['from_school'] = !empty($grade_nine)?$grade_nine:$school_name;
					$form_data['days_of_school'] = !empty($grade_nine_days)?$grade_nine_days:null;
					$form_data['days_present'] = !empty($grade_nine_present)?$grade_nine_present:null;
					$form_data['total_years'] = !empty($grade_nine_total_years)?$grade_nine_total_years:null;
					$grades_9_content = $this->generate_secondary_table_group_grades( $grade_9_list_of_subjects, $student_id, $session_id, $form_data );
				}
				
				if (strpos($class_name, '10') !== false) { 
					$session_id = $student_result['session_id'];
					$grade_10_list_of_subjects = $this->subject_model->getSubjctByClass( $class_id ); 
					$form_data['session_id'] = $session_id;
					$form_data['from_school'] = !empty($grade_ten)?$grade_ten:$school_name;
					$form_data['days_of_school'] = !empty($grade_ten_days)?$grade_ten_days:null;
					$form_data['days_present'] = !empty($grade_ten_present)?$grade_ten_present:null;
					$form_data['total_years'] = !empty($grade_ten_total_years)?$grade_ten_total_years:null;
					$grades_10_content = $this->generate_secondary_table_group_grades( $grade_10_list_of_subjects, $student_id, $session_id, $form_data );
				} 
				
				 if( empty( $grades_7_content ) ){
					$form_data['from_school'] = !empty($grade_eight)?$grade_eight:'';
					$form_data['days_of_school'] = !empty($grade_seven_days)?$grade_seven_days:null;
					$form_data['days_present'] = !empty($grade_seven_present)?$grade_seven_present:null;
					$form_data['total_years'] = !empty($grade_seven_total_years)?$grade_seven_total_years:null;
					$grades_7_content = $this->generate_secondary_table_group_grades( $grade_7_list_of_subjects, $student_id, null, $form_data ); 	
				}
				if( empty( $grades_8_content ) ){
					$form_data['from_school'] = !empty($grade_eight)?$grade_eight:'';
					$form_data['days_of_school'] = !empty($grade_eight_days)?$grade_eight_days:null;
					$form_data['days_present'] =!empty($grade_eight_present)?$grade_eight_present:null;
					$form_data['total_years'] = !empty($grade_eight_total_years)?$grade_eight_total_years:null;
					$grades_8_content = $this->generate_secondary_table_group_grades( $grade_8_list_of_subjects, $student_id, null,$form_data ); 	
				}
				if( empty( $grades_9_content ) ){
					$form_data['from_school'] = !empty($grade_nine)?$grade_nine:'';
					$form_data['days_of_school'] = !empty($grade_nine_days)?$grade_nine_days:null;
					$form_data['days_present'] = !empty($grade_nine_present)?$grade_nine_present:null;
					$form_data['total_years'] = !empty($grade_nine_total_years)?$grade_nine_total_years:null;
					$grades_9_content = $this->generate_secondary_table_group_grades( $grade_9_list_of_subjects, $student_id, null,$form_data ); 	
				}
				if( empty( $grades_10_content ) ){
					$form_data['from_school'] = !empty($grade_ten)?$grade_ten:'';
					$form_data['days_of_school'] = !empty($grade_ten_days)?$grade_ten_days:null;
					$form_data['days_present'] = !empty($grade_ten_present)?$grade_ten_present:null;
					$form_data['total_years'] = !empty($grade_ten_total_years)?$grade_ten_total_years:null;
					$grades_10_content = $this->generate_secondary_table_group_grades( $grade_10_list_of_subjects, $student_id ,null, $form_data ); 	
				}
			}
		}
        $data['grades_7_content'] = $grades_7_content; 
        $data['grades_8_content'] = $grades_8_content; 
        $data['grades_9_content'] = $grades_9_content; 
        $data['grades_10_content'] = $grades_10_content; 
		
        $data['school_name'] = isset($school_name)?$school_name:null;
        $data['school_address'] = isset($school_address)?$school_address:null;
        $data['school_year'] = isset($school_year)?$school_year:null;
        $data['class_name'] = isset($class_name)?$class_name:null;
        $data['section_name'] = isset($section_name)?$section_name:null;
        $data['student_name'] = isset($student_name)?$student_name:null;  
        $data['student_lrn'] = isset($student_lrn)?$student_lrn:null;   
        $data['student_admission_no'] = isset($student_admission_no)?$student_admission_no:null;   
        $student_dob = isset($student_dob)?$student_dob:null;   
        $data['student_dob'] = date("F d, Y", strtotime($student_dob));    
        $data['student_gender'] = isset($student_gender)?$student_gender:null;   
        $data['student_place_of_birth'] = isset($student_current_address)?$student_current_address:null;     
		$student_father_name = isset($student_father_name)?$student_father_name:null; 
		$student_father_midname = isset($student_father_midname)?$student_father_midname:null; 
		$student_father_midname = substr($student_father_midname, 0, 1); 
		$student_father_lastname = isset($student_father_lastname)?$student_father_lastname:null; 
		$student_father_lastname = isset($student_father_lastname)?$student_father_lastname:null; 
		$student_mother_name = isset($student_mother_name)?$student_mother_name:null; 
		$student_mother_midname = isset($student_mother_midname)?$student_mother_midname:null; 
		$student_mother_midname = substr($student_mother_midname, 0, 1); 
		$student_mother_lastname = isset($student_mother_lastname)?$student_mother_lastname:null; 
        $data['student_father'] = $student_father_midname != null ? $student_father_name.' '.$student_father_midname.'. '.$student_father_lastname:$student_father_name.' '.$student_father_lastname;    
        $data['student_father_occupation'] = $student_father_occupation;   
        $data['student_mother'] = $student_mother_midname != null ? $student_mother_name.' '.$student_mother_midname.'. '.$student_mother_lastname:$student_mother_name.' '.$student_mother_lastname; 
        $data['student_mother_occupation'] = isset($student_mother_occupation)?$student_mother_occupation:null;      
        $data['student_guardian_address'] = isset($student_guardian_address)?$student_guardian_address:null;      
        $data['quarter_display'] = ''; 
        $data['diplay_date'] = date('F d, Y'); 
        $data['registrar_fullname'] = isset($registrar_fullname)?$registrar_fullname:null;
		$data['lineone'] = $lineone;
		$data['linetwo'] = $linetwo;
		$data['intermediate'] = $intermediate;
		$data['yeear'] = $yeear;
		$data['total_years'] = $total_years;
		$data['gen_ave'] = $gen_ave;
		$data['eligibleto'] = $eligibleto;
		$data['place'] = $place;
		$data['remarks'] = $remarks;
		
        ob_start();
        $content = ob_get_contents();
        ob_end_clean(); 
        $data['content'] = $content;  
        $html = $this->load->view('template/secondary_student_transcript_record',$data, true );    
        $pdfFilePath = "Student_Permanent_Record_".$student_name.".pdf";
        $this->load->library('m_pdf'); 
        $this->m_pdf->pdf->mPDF('','LEGAL','0','','10','10','16','16','9','9', 'L');
        $this->m_pdf->pdf->AddPage();
        $this->m_pdf->pdf->use_kwt = true;
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "I");
    }

    function generate_secondary_grades( $list_of_subjects,  $student_id ) {
         ob_start();
        if( $list_of_subjects  ){ 
            foreach ($list_of_subjects as $key => $value){
                $total_grades = 0;
                $subject_id = $value['id'];
                $get_subject = $this->subject_model->get( $subject_id );
                $subject_name = isset($get_subject['name'])?$get_subject['name']:'';
                ?>
                <tr>
                    <td style="border: 1px solid;"><?php echo $subject_name; ?></td>
                    <?php
                    $grade_count = 1;
                    for( $quarter=1;$quarter<=4;$quarter++ ) {
                        $examresult = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, $quarter, $subject_id   );
                        $grades = isset($examresult->grades)?$examresult->grades:'';
                         $total_grades += $grades;  
                          
                        ?>
                        <td style="border: 1px solid;"><?php echo $grades; ?></td>
                        <?php
                          $grade_count++;
                    }
                    if(   $grade_count == 4 ){

                      $final_grade =  $total_grades/4;
                    } else {
                        $final_grade = ' ';
                    }

                    if( $final_grade == '74.5'){
                        $final_grade = '75';
                    } elseif(  $final_grade > '74.4' && $final_grade <  75 ){
                        $final_grade = '75';
                    }
                    ?>
                    <td style="border: 1px solid;"><?php echo $final_grade; ?></td>
                    <td style="border: 1px solid;"></td>
                </tr>
                <?php
            }
        }
        $grades_content = ob_get_contents();
        ob_clean( );

        return $grades_content;
    }


   /*  function generate_secondary_table_grades( $list_of_subjects,  $student_id, $set_current ){
       

        if( $set_current ){ 
            $current_session = $this->setting_model->getCurrentSessionName(); 
        } else {
            if($list_of_subjects){   
                $this->db->select('session_id')->from('archive_exam_result'); 
                $this->db->where('class_id', $list_of_subjects[0]['class_id']);
                $this->db->where('subject_id', $list_of_subjects[0]['id']);
                $this->db->where('student_id', $student_id); 
                $query = $this->db->get(); 
                $result_vc = $query->row_array(); 
 
                if( $result_vc ){ 
                    $selected_session_id = $result_vc['session_id'];
                    $selected_session_array = $this->session_model->get( $selected_session_id );   
                    $current_session =  $selected_session_array['session'];
                } else { 
                    $current_session = '';
                } 
            } 
        } 

         if( $list_of_subjects ){ 
            $grade_display = strtoupper($list_of_subjects[0]['class']); 
            $school_year_display = $current_session; 
        } else {
            $grade_display = '';
            $school_year_display = '';  
        }


        $grades_content = '';
        ob_start();
        ?>
        <table width="100%" border="1" style="font-size: 10px;  border-collapse: collapse; margin-top: 5px;">
            <tr>
                <td style="text-align: center; border-top: 1px solid black; border-right: none;">School</td>
                <td colspan="7" style="text-align: right; border-top: 1px solid black; border-left: none;border-right: none;">SY: <?php echo $school_year_display; ?> Classified as <?php echo  $grade_display; ?></td>
                <td style="border-top: 1px solid black; border-right: 1px solid black; border-left: none"></td>
            </tr>
            <tr style="text-align: center;">
                <th style="border: 1px solid black;">CURR YR</th>
                <th style="border: 1px solid black;" style="border: 1px solid black;">SUBJECTS</th>
                <th style="border: 1px solid black;">1ST</th>
                <th style="border: 1px solid black;">2ND</th>
                <th style="border: 1px solid black;">3RD</th>
                <th style="border: 1px solid black;">4TH</th>
                <th style="border: 1px solid black;">AVE.</th>
                <th style="border: 1px solid black;">Action<br/>Taken</th>
                <th style="border: 1px solid black;">Credits<br/>Earned</th>
            </tr>
            <?php 
            $base_count = 1;
            if( $list_of_subjects ){ 
                if( $set_current ){ 
                    foreach ( $list_of_subjects as $list_key => $list_value){  
                        ?>
                        <tr>
                            <td style="border: 1px solid black; height:20px"></td>
                            <td style="border: 1px solid black; text-align: center;"><?php echo $list_value['name']; ?></td>
                            <?php
                            $grade_count = 1;
                            $total_grades = 0;
                            for( $quarter=1;$quarter<=4;$quarter++ ) {
                                $examresult = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, $quarter, $list_value['id']   );
                               
                                if( isset($examresult->grades) ){
                                    $grades = number_format($examresult->grades, 2, '.', '');
                                     $total_grades +=  $grades;
                                } else {
                                    $grades = '';
                                }

                                if( $grades ){ 
                                    $grades = number_format( (float)$grades,0,",","."); 
                                }
                                  
                                ?>
                                <td style="border: 1px solid black; text-align: center;"><?php echo $grades; ?></td> 
                                <?php  
                            }

                            $final_grade =  $total_grades/4;
                            if( $final_grade > 74.4 ){
                                $action_taken = "PASSED"; 
                            } else {
                                if( $final_grade > 1 ){ 
                                    $action_taken = "FAILED"; 
                                } else {
                                    $action_taken = " "; 
                                }
                               
                            } 

                            if( $final_grade > 1 ){ 
                                $display_final_grade =  number_format($final_grade, 2, '.', '');
                            } else { 
                                $display_final_grade = "";
                            } 

                            if( $display_final_grade == '74.5'){
                                $display_final_grade = '75';
                            } elseif(  $display_final_grade > '74.4' && $display_final_grade <  75 ){
                                $display_final_grade = '75';
                            }

                            ?> 
                            <td style="border: 1px solid black; text-align: center;"><?php echo $display_final_grade; ?></td> 
                            <td style="border: 1px solid black; text-align: center;"><?php echo $action_taken; ?></td>  
                            <td style="border: 1px solid black;"></td>
                        </tr>
                        <?php
                        $base_count++;
                        # code...
                    }
                } else {
                    foreach ( $list_of_subjects as $list_key => $list_value){  
 

                        $this->db->select('*')->from('archive_exam_result'); 
                        $this->db->where('class_id', $list_value['class_id']);
                        $this->db->where('subject_id', $list_value['id']);
                        $this->db->where('student_id', $student_id); 
                        $query = $this->db->get(); 
                        $result_vc = $query->row_array(); 
                          
                        $first_q = $result_vc['first'];
                        $second_q = $result_vc['second'];
                        $third_q = $result_vc['third'];
                        $fourth_q = $result_vc['fourth'];
                        $first_add =  $first_q + $second_q;
                        $second_add =  $third_q + $fourth_q;
                        $total_grades =  $first_add + $second_add;
                        $final_grade =  $total_grades/4;
                        if( $final_grade > 74.4 ){
                            $action_taken = "PASSED";
                        } else {
                            $action_taken = "FAILED";
                        } 

                        if( $final_grade == '74.5'){
                            $final_grade = '75';
                        } elseif(  $final_grade > '74.4' && $final_grade <  75 ){
                            $final_grade = '75';
                        }
                        ?>
                        <tr>
                            <td style="border: 1px solid black; height:20px"></td>
                            <td style="border: 1px solid black; text-align: center;"><?php echo $list_value['name']; ?></td>
                           
                            <td style="border: 1px solid black; text-align: center;"><?php echo number_format($first_q, 0, '.', ''); ?></td>
                            <td style="border: 1px solid black; text-align: center;"><?php echo number_format($second_q, 0, '.', ''); ?></td>
                            <td style="border: 1px solid black; text-align: center;"><?php echo number_format($third_q, 0, '.', ''); ?></td>
                            <td style="border: 1px solid black; text-align: center;"><?php echo number_format($fourth_q, 0, '.', ''); ?></td>
                            <td style="border: 1px solid black; text-align: center;"><?php echo number_format($final_grade, 2, '.', ''); ?></td> 
                            <td style="border: 1px solid black; text-align: center;"><?php echo $action_taken; ?></td>  
                            <td style="border: 1px solid black;"></td>
                        </tr>
                        <?php
                        $base_count++;
                        # code...
                    }

                }

            }
            for ($i=$base_count; $i < 15; $i++) { 
                ?>
                <tr>
                     <td style="border: 1px solid black; height:20px"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;">GENERAL AVERAGE</td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
            </tr> 
        </table> 
        <table width="100%" style="font-size: 10px;"> 
            <tr>
                 <td width="25%"><em>Days of School</em></td> 
                 <td width="25%"><em>Days Present</em></td> 
                 <td width="25%"><em>Total years in school to date</em></td>  
                 
            </tr>
        </table>  
        <?php
        $grades_contentxx = ob_get_contents();
        ob_end_clean( );

        return $grades_contentxx;
    } */
	
	 function generate_secondary_table_grades( $list_of_subjects,  $student_id, $session_id=null, $form_data=array() ){
		$selected_session_array = $this->session_model->get( $session_id );   
		$current_session =  isset($selected_session_array['session'])?$selected_session_array['session']:null;
		 if( $list_of_subjects ){ 
			$grade_display = strtoupper($list_of_subjects[0]['class']); 
			$school_year_display = $current_session; 
		} else {
			$grade_display = '';
			$school_year_display = '';  
		}
        $grades_content = '';
		$getNoSchoolDays = null;
		$gradingsettings = $this->gradingsetting_model->getbySession( $session_id );
		$decimal_grades = isset($gradingsettings->decimal_grades)?$gradingsettings->decimal_grades:0;
		$decimal_average = isset($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;
		$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:2;
		$if_gradeisnull = isset($gradingsettings->if_gradeisnull)?$gradingsettings->if_gradeisnull:'blank';
		$allow_to_pass = isset($gradingsettings->allow_to_pass)?$gradingsettings->allow_to_pass:'no';
		$combine_category = 'ga';
        ob_start();
        ?>
        <table width="100%" border="1" style="font-size: 10px;  border-collapse: collapse; margin-top: 5px;">
            <tr>
				<td style="text-align: left; border-top: 1px solid black; border-right: none;padding:5px;" colspan="3">School: <?php echo $form_data['from_school']; ?></td>
				<?php 
				if( $school_year_display ){?>
					<td colspan="4" style="text-align: left; border-top: 1px solid black; border-left: none;border-right: none;margin-left:10px;">SY: <?php echo $school_year_display; ?>&nbsp;&nbsp;Classified as&nbsp;&nbsp;<?php echo  $grade_display; ?></td>
					<?php 
				} else {
					?>
					<td colspan="4" style="text-align: left; border-top: 1px solid black; border-left: none;border-right: none;margin-left:10px;">SY: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Classified as&nbsp;&nbsp;<?php echo  $grade_display; ?></td>
					<?php
				}
				?>
				<td style="border-top: 1px solid black; border-right: 1px solid black; border-left: none"></td>
            </tr>
            <tr style="text-align: center;">
                <th style="border: 1px solid black;" style="border: 1px solid black;" width="55%" >SUBJECTS</th>
                <th style="border: 1px solid black;" width="7%">1ST</th>
                <th style="border: 1px solid black;" width="7%">2ND</th>
                <th style="border: 1px solid black;" width="7%">3RD</th>
                <th style="border: 1px solid black;" width="7%">4TH</th>
                <th style="border: 1px solid black;" width="8%">AVE.</th>
                <th style="border: 1px solid black;" width="9%">Action<br/>Taken</th>
                <!--<th style="border: 1px solid black;">Credits<br/>Earned</th> -->
            </tr>
            <?php 
            $base_count = 1;
			$gen_ave = 0;
			$count = 0;
			$total_grade = 0;
			$complete_grades = true;
            if( $list_of_subjects ){ 
			  foreach ( $list_of_subjects as $list_key => $list_value ){ 
						$complete_grades = true;
						$complete_first = true;
						$complete_second = true;
						$complete_third = true;
						$complete_fourth = true;
						$subject_id = $list_value['id'];
						$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );
						$display_card = $subject_manager_details['display_card'];
						$include_computation = $subject_manager_details['include_computation'];
						$align_right = $subject_manager_details['align_right'];
						$units = $subject_manager_details['units'];
						
						$this->db->select('*')->from('archive_exam_result'); 
                        $this->db->where('class_id', $list_value['class_id']);
                        $this->db->where('subject_id', $subject_id );
                        $this->db->where('student_id', $student_id); 
                        $this->db->where('session_id', $session_id ); 
                        $query = $this->db->get(); 
                        $result_vc = $query->row_array(); 
							
						if( $result_vc ){
							$base_count++;
							$first_q = $result_vc['first'];
							if( empty( $first_q )){
								$complete_first = false;
							} else {
								$first_q = number_format( (float)$first_q,$decimal_grades,",","."); 
							}
							$second_q = $result_vc['second'];
							if( empty( $second_q )){
								$complete_second = false;
							} else {
							$second_q = number_format( (float)$second_q,$decimal_grades,",","."); 
							}
							$third_q = $result_vc['third'];
							if( empty( $third_q )){
								$complete_third = false;
							} else {
								$third_q = number_format( (float)$third_q,$decimal_grades,",","."); 
							}
							$fourth_q = $result_vc['fourth'];
							if( empty( $fourth_q )){
								$complete_fourth = false;
							} else {
								$fourth_q = number_format( (float)$fourth_q,$decimal_grades,",","."); 
							}
							/* $first_add =  $first_q + $second_q;
							$second_add =  $third_q + $fourth_q;
							$total_grades =  $first_add + $second_add;
							$final_grade =  $total_grades/4; */
							$total_grades = $first_q + $second_q + $third_q + $fourth_q;
							$get_final = $result_vc['final'];
							if( $get_final ){
								$final_grade = $get_final;
							} else {
								$final_grade = $total_grades/4;
							}
							
							if( $allow_to_pass == 'yes'){
								if( $final_grade == '74.5'){
									$final_grade = '75';
								} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
									$final_grade = '75';
								}
							}
							if( $final_grade ){
								$count++;
								$total_grade = $total_grade + $final_grade;
							}
							 
							$final_grade = number_format((float)$final_grade, $decimal_finalgrade, '.', '');
							
							if( $final_grade > 74.4 ){
								$action_taken = "P";
							} else {
								$action_taken = "F";
							}
							/* if( $final_grade == '74.5'){
								$final_grade = '75';
							} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
								$final_grade = '75';
							} */
							?>
							<tr>
							
								<td style="border: 1px solid black; margin-left:15px;text-transform:uppercase;height:20px">&nbsp;&nbsp;<?php echo $list_value['name']; ?></td>
								<td style="border: 1px solid black; text-align: center;"><?php echo $first_q; ?></td>
								<td style="border: 1px solid black; text-align: center;"><?php echo $second_q; ?></td>
								<td style="border: 1px solid black; text-align: center;"><?php echo $third_q; ?></td>
								<td style="border: 1px solid black; text-align: center;"><?php echo $fourth_q; ?></td>
								<?php 
								if( $complete_first && $complete_second && $complete_third && $complete_fourth ){?>
									<td style="border: 1px solid black; text-align: center;"><?php echo $final_grade; ?></td> 
									<td style="border: 1px solid black; text-align: center;"><?php echo $action_taken; ?></td>  
									<?php 
								} else { 
									$complete_grades = false;
									?>
									<td style="border: 1px solid black; text-align: center;"></td> 
									<td style="border: 1px solid black; text-align: center;"></td>  
									<?php
								}
								?>
								<!--<td style="border: 1px solid black;"></td>-->
							</tr>
							<?php
							//$base_count++;
						} else {
							if( $display_card == 'yes'){
								$base_count++;
								?>
								<tr>
								<?php 
								if( $align_right == 'yes'){
									?>
									<td style="border: 1px solid black; margin-left:15px;text-transform:uppercase;height:20px">&nbsp;&nbsp;<?php echo $list_value['name']; ?></td>
									<?php
								} else {
									?>
									<td style="border: 1px solid black; margin-left:15px;text-transform:uppercase;height:20px">&nbsp;<?php echo $list_value['name']; ?></td>
									<?php
								}
							}
							$grade_count = 1;
							$total_grades = 0;
							$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
							for( $quarter=1;$quarter<=4;$quarter++ ) {
								$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id );
								//$examresult = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, $quarter, $list_value['id'],  $session_id  );
								/* if( isset($examresult->grades) ){
									$grades = number_format($examresult->grades, 2, '.', '');
									 $total_grades +=  $grades;
								} else {
									$grades = '';
								}

								if( $grades ){ 
									$grades = number_format( (float)$grades,0,",","."); 
								} */
								if( $include_computation == 'yes' ){
									if( $grade_per_quarter == null ){
										$complete_grades = false;
									}  
								}								
								if( $display_card == 'yes'){  
									if( $grade_per_quarter == null || $grade_per_quarter == 0 ){
										if( $if_gradeisnull == 'blank'){
										?>
										<td style="border: 1px solid black; text-align: center;"></td>
										<?php
										}  else {
											?>
											<td style="border: 1px solid black; text-align: center;"><?php echo number_format((float)0, $decimal_grades, '.', '');?></td>
											<?php
										}
									} else {
										?>
										<td style="border: 1px solid black; text-align: center;"><?php echo $grade_per_quarter; ?></td> 
										<?php
									}
								}
								
								$total_grades +=  $grade_per_quarter;
							}
							
							$final_grade =  $total_grades/4;
							if( $include_computation == 'yes' ){ 
								if( empty( $checkifChild ) ){
									if( $allow_to_pass == 'yes'){
										if( $final_grade == '74.5'){
											$final_grade = '75';
										} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
											$final_grade = '75';
										}
									}
								}
							}
							$final_grade = number_format((float)$final_grade, $decimal_finalgrade, '.', '');
							if( $final_grade > 74.4 ){
								$action_taken = "P";
							} else {
								$action_taken = "F";
							}
							if( $include_computation == 'yes' ){ 
								if( empty( $checkifChild ) ){
									$count++; 
									$total_grade = $total_grade +  $final_grade;
								}
							}
							/* if( $final_grade ){
								$count++;
								$total_grade = $total_grade + $final_grade;
							} 
							if( $final_grade > 74.4 ){
								$action_taken = "P"; 
							} else {
								if( $final_grade > 1 ){ 
									$action_taken = "F"; 
								} else {
									$action_taken = " "; 
								}
							   
							} */

						/* 	if( $final_grade > 1 ){ 
								$display_final_grade =  number_format($final_grade, 2, '.', '');
							} else { 
								$display_final_grade = "";
							} 

							if( $display_final_grade == '74.5'){
								$display_final_grade = '75';
							} elseif(  $display_final_grade > '74.4' && $display_final_grade <  75 ){
								$display_final_grade = '75';
							} */
								
							if( $display_card == 'yes'){
								if( $complete_grades ){
									?> 
									<td style="border: 1px solid black; text-align: center;"><?php echo $final_grade; ?></td> 
									<td style="border: 1px solid black; text-align: center;"><?php echo $action_taken; ?></td>  
									<?php
								} else {
									?>
									<td style="border: 1px solid black; text-align: center;"></td> 
									<td style="border: 1px solid black; text-align: center;"></td>  
									<?php
								}
								?>
								</tr>
								<?php
							}
							
							# code...
						}
			  }
        }
		for ($i=$base_count; $i < 15; $i++) { 
			?>
			<tr>
				 <td style="border: 1px solid black;height:20px"></td>
				 <td style="border: 1px solid black;"></td>
				 <td style="border: 1px solid black;"></td>
				 <td style="border: 1px solid black;"></td>
				 <td style="border: 1px solid black;"></td>
				 <td style="border: 1px solid black;"></td>
				 <td style="border: 1px solid black;"></td>
				<!-- <td style="border: 1px solid black;"></td>-->
			</tr>
			<?php
		}
		$gen_ave = $count!=0 ? $total_grade / $count:null;
		if( $gen_ave != null){
			$gen_ave = number_format($gen_ave, $decimal_finalgrade, '.', '');
		}
		if( $gen_ave == '74.5'){
			$gen_ave = '75';
		} elseif(  $gen_ave > '74.4' && $gen_ave <  75 ){
			$gen_ave = '75';
		}
		
		if( $gen_ave > 74.4 ){
			$action_taken = "P"; 
		} else {
			if( $gen_ave > 1 ){ 
				$action_taken = "F"; 
			} else {
				$action_taken = " "; 
			}
		   
		} 
		
		$getNoSchoolDays = $this->gradingsetting_model->getNoSchoolDays( $session_id );
		$get_total_present = $this->stuattendence_model->get_total_present( $student_id, $session_id );
		$days_of_school = $form_data['days_of_school'] != " "?$form_data['days_of_school']: $getNoSchoolDays;
		$days_present = $form_data['days_present'] != " "?$form_data['days_present']: $get_total_present;
		$total_years = $form_data['total_years'];
		
		?>
		<tr>
			<td style="border: 1px solid black;height:20px">&nbsp;GENERAL AVERAGE</td>
			<td style="border: 1px solid black;"></td>
			<td style="border: 1px solid black;"></td>
			<td style="border: 1px solid black;"></td>
			<td style="border: 1px solid black;"></td>
			<?php 
			if( $complete_grades ){
				?>
				<td style="border: 1px solid black;text-align: center;"><?php echo $gen_ave;?></td>
				<td style="border: 1px solid black;text-align: center;"><?php echo $action_taken;?></td>
				<?php 
			} else {
				?>
				<td style="border: 1px solid black;text-align: center;"></td>
				<td style="border: 1px solid black;text-align: center;"></td>
				<?php
			}?>
				
			<!--<td style="border: 1px solid black;"></td>-->
		</tr> 
		</table> 
		<table width="100%" style="font-size: 10px;"> 
			<tr>
				 <td width="25%" style="font-size:12px;"><em>Days of School</em>&nbsp;&nbsp;<?php echo $days_of_school;?></td> 
				 <td width="25%" style="font-size:12px;"><em>Days Present</em>&nbsp;&nbsp;<?php echo $days_present;?></td> 
				 <td width="30%" style="font-size:12px;"><em>Total years in school to date</em>&nbsp;&nbsp;<?php echo $total_years;?></td>  
				<!-- <td width="20%" style="font-size:12px;"><em>Total Units Earned</em></td>  -->
				 
			</tr>
		</table>  
		<?php
		$grades_contentxx = ob_get_contents();
	
		ob_end_clean( );

		return $grades_contentxx;
    }
	
	/*  function generate_secondary_table_grades( $list_of_subjects,  $student_id, $set_current ){
       

        if( $set_current ){ 
            $current_session = $this->setting_model->getCurrentSessionName(); 
        } else {
            if($list_of_subjects){   
                $this->db->select('session_id')->from('archive_exam_result'); 
                $this->db->where('class_id', $list_of_subjects[0]['class_id']);
                $this->db->where('subject_id', $list_of_subjects[0]['id']);
                $this->db->where('student_id', $student_id); 
                $query = $this->db->get(); 
                $result_vc = $query->row_array(); 
 
                if( $result_vc ){ 
                    $selected_session_id = $result_vc['session_id'];
                    $selected_session_array = $this->session_model->get( $selected_session_id );   
                    $current_session =  $selected_session_array['session'];
                } else { 
                    $current_session = '';
                } 
            } 
        } 

         if( $list_of_subjects ){ 
            $grade_display = strtoupper($list_of_subjects[0]['class']); 
            $school_year_display = $current_session; 
        } else {
            $grade_display = '';
            $school_year_display = '';  
        }


        $grades_content = '';
        ob_start();
        ?>
        <table width="100%" border="1" style="font-size: 10px;  border-collapse: collapse; margin-top: 5px;">
            <tr>
                <td style="text-align: center; border-top: 1px solid black; border-right: none;">School</td>
                <td colspan="7" style="text-align: right; border-top: 1px solid black; border-left: none;border-right: none;">SY: <?php echo $school_year_display; ?> Classified as <?php echo  $grade_display; ?></td>
                <td style="border-top: 1px solid black; border-right: 1px solid black; border-left: none"></td>
            </tr>
            <tr style="text-align: center;">
                <th style="border: 1px solid black;">CURR YR</th>
                <th style="border: 1px solid black;" style="border: 1px solid black;">SUBJECTS</th>
                <th style="border: 1px solid black;">1ST</th>
                <th style="border: 1px solid black;">2ND</th>
                <th style="border: 1px solid black;">3RD</th>
                <th style="border: 1px solid black;">4TH</th>
                <th style="border: 1px solid black;">AVE.</th>
                <th style="border: 1px solid black;">Action<br/>Taken</th>
                <th style="border: 1px solid black;">Credits<br/>Earned</th>
            </tr>
            <?php 
            $base_count = 1;
            if( $list_of_subjects ){ 
                if( $set_current ){ 
                    foreach ( $list_of_subjects as $list_key => $list_value){  
                        ?>
                        <tr>
                            <td style="border: 1px solid black; height:20px"></td>
                            <td style="border: 1px solid black; text-align: center;"><?php echo $list_value['name']; ?></td>
                            <?php
                            $grade_count = 1;
                            $total_grades = 0;
                            for( $quarter=1;$quarter<=4;$quarter++ ) {
                                $examresult = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, $quarter, $list_value['id']   );
                               
                                if( isset($examresult->grades) ){
                                    $grades = number_format($examresult->grades, 2, '.', '');
                                     $total_grades +=  $grades;
                                } else {
                                    $grades = '';
                                }

                                if( $grades ){ 
                                    $grades = number_format( (float)$grades,0,",","."); 
                                }
                                  
                                ?>
                                <td style="border: 1px solid black; text-align: center;"><?php echo $grades; ?></td> 
                                <?php  
                            }

                            $final_grade =  $total_grades/4;
                            if( $final_grade > 74.4 ){
                                $action_taken = "PASSED"; 
                            } else {
                                if( $final_grade > 1 ){ 
                                    $action_taken = "FAILED"; 
                                } else {
                                    $action_taken = " "; 
                                }
                               
                            } 

                            if( $final_grade > 1 ){ 
                                $display_final_grade =  number_format($final_grade, 2, '.', '');
                            } else { 
                                $display_final_grade = "";
                            } 

                            if( $display_final_grade == '74.5'){
                                $display_final_grade = '75';
                            } elseif(  $display_final_grade > '74.4' && $display_final_grade <  75 ){
                                $display_final_grade = '75';
                            }

                            ?> 
                            <td style="border: 1px solid black; text-align: center;"><?php echo $display_final_grade; ?></td> 
                            <td style="border: 1px solid black; text-align: center;"><?php echo $action_taken; ?></td>  
                            <td style="border: 1px solid black;"></td>
                        </tr>
                        <?php
                        $base_count++;
                        # code...
                    }
                } else {
                    foreach ( $list_of_subjects as $list_key => $list_value){  
 

                        $this->db->select('*')->from('archive_exam_result'); 
                        $this->db->where('class_id', $list_value['class_id']);
                        $this->db->where('subject_id', $list_value['id']);
                        $this->db->where('student_id', $student_id); 
                        $query = $this->db->get(); 
                        $result_vc = $query->row_array(); 
                          
                        $first_q = $result_vc['first'];
                        $second_q = $result_vc['second'];
                        $third_q = $result_vc['third'];
                        $fourth_q = $result_vc['fourth'];
                        $first_add =  $first_q + $second_q;
                        $second_add =  $third_q + $fourth_q;
                        $total_grades =  $first_add + $second_add;
                        $final_grade =  $total_grades/4;
                        if( $final_grade > 74.4 ){
                            $action_taken = "PASSED";
                        } else {
                            $action_taken = "FAILED";
                        } 

                        if( $final_grade == '74.5'){
                            $final_grade = '75';
                        } elseif(  $final_grade > '74.4' && $final_grade <  75 ){
                            $final_grade = '75';
                        }
                        ?>
                        <tr>
                            <td style="border: 1px solid black; height:20px"></td>
                            <td style="border: 1px solid black; text-align: center;"><?php echo $list_value['name']; ?></td>
                           
                            <td style="border: 1px solid black; text-align: center;"><?php echo number_format($first_q, 0, '.', ''); ?></td>
                            <td style="border: 1px solid black; text-align: center;"><?php echo number_format($second_q, 0, '.', ''); ?></td>
                            <td style="border: 1px solid black; text-align: center;"><?php echo number_format($third_q, 0, '.', ''); ?></td>
                            <td style="border: 1px solid black; text-align: center;"><?php echo number_format($fourth_q, 0, '.', ''); ?></td>
                            <td style="border: 1px solid black; text-align: center;"><?php echo number_format($final_grade, 2, '.', ''); ?></td> 
                            <td style="border: 1px solid black; text-align: center;"><?php echo $action_taken; ?></td>  
                            <td style="border: 1px solid black;"></td>
                        </tr>
                        <?php
                        $base_count++;
                        # code...
                    }

                }

            }
            for ($i=$base_count; $i < 15; $i++) { 
                ?>
                <tr>
                     <td style="border: 1px solid black; height:20px"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;">GENERAL AVERAGE</td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
            </tr> 
        </table> 
        <table width="100%" style="font-size: 10px;"> 
            <tr>
                 <td width="25%"><em>Days of School</em></td> 
                 <td width="25%"><em>Days Present</em></td> 
                 <td width="25%"><em>Total years in school to date</em></td>  
                 
            </tr>
        </table>  
        <?php
        $grades_contentxx = ob_get_contents();
        ob_end_clean( );

        return $grades_contentxx;
    } */

/*     function generate_secondary_table_group_grades( $list_of_subjects,  $student_id, $set_current ){
       

        if( $set_current ){ 
            $current_session = $this->setting_model->getCurrentSessionName(); 
        } else {
            if($list_of_subjects){   
                $this->db->select('session_id')->from('archive_exam_result'); 
                $this->db->where('class_id', $list_of_subjects[0]['class_id']);
                $this->db->where('subject_id', $list_of_subjects[0]['id']);
                $this->db->where('student_id', $student_id); 
                $query = $this->db->get(); 
                $result_vc = $query->row_array(); 
 
                if( $result_vc ){ 
                    $selected_session_id = $result_vc['session_id'];
                    $selected_session_array = $this->session_model->get( $selected_session_id );   
                    $current_session =  $selected_session_array['session'];
                } else { 
                    $current_session = '';
                } 
            } 
        } 

         if( $list_of_subjects ){ 
            $grade_display = strtoupper($list_of_subjects[0]['class']); 
            $school_year_display = $current_session; 
        } else {
            $grade_display = '';
            $school_year_display = '';  
        }


        $grades_content = '';
        ob_start();
        ?>
        <table width="100%" border="1" style="font-size: 10px;  border-collapse: collapse; margin-top: 5px;">
            <tr>
                <td colspan="4" style="padding-left: 5px;padding-right: 5px">
                    <table width="100%">
                        <tr>
                            <td style="text-align: left;">Classified as <?php echo  $grade_display; ?></td>
                            <td style="text-align: right;">SY <?php echo  $school_year_display; ?></td>
                        </tr>
                        <tr>
                            <td colspan="2">School</td>
                            
                        </tr>
                    </table> 
                </td>  
            </tr>
            <tr style="text-align: center;"> 
                <th style="border: 1px solid black;" style="border: 1px solid black;">SUBJECTS</th>
                <th style="border: 1px solid black;">Final <br /> Rating</th> 
                <th style="border: 1px solid black;">Action<br/>Taken</th>
                <th style="border: 1px solid black;">Credits<br/>Earned</th>
            </tr>
            <?php 
            $base_count = 3;
            $base_total_units = 0;
            if( $list_of_subjects ){ 
                if( $set_current ){ 
                    foreach ( $list_of_subjects as $list_key => $list_value){  
                        ?>
                        <tr> 
                            <td style="border: 1px solid black; text-align: left;height:20px"><?php echo $list_value['name']; ?></td>
                            <?php
                            $grade_count = 1;
                            $total_grades = 0;
                            for( $quarter=1;$quarter<=4;$quarter++ ) {
                                $examresult = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, $quarter, $list_value['id']   );
                               
                                if( isset($examresult->grades) ){
                                    $grades = number_format($examresult->grades, 2, '.', '');
                                     $total_grades +=  $grades;
                                } else {
                                    $grades = '';
                                }
                                   
                            }

                            $final_grade =  $total_grades/4;
                            if( $final_grade > 74.4 ){
                                $action_taken = "P"; 
                            } else {
                                if( $final_grade > 1 ){ 
                                    $action_taken = "F"; 
                                } else {
                                    $action_taken = " "; 
                                }
                               
                            } 

                            if( $final_grade > 1 ){ 
                                $display_final_grade =  number_format($final_grade, 2, '.', '');
                            } else { 
                                $display_final_grade = "";
                            } 

                            if( $display_final_grade == '74.5'){
                                $display_final_grade = '75';
                            } elseif(  $display_final_grade > '74.4' && $display_final_grade <  75 ){
                                $display_final_grade = '75';
                            }


                            ?> 
                            <td style="border: 1px solid black; text-align: right;"><?php echo $display_final_grade; ?></td> 
                            <td style="border: 1px solid black; text-align: center;"><?php echo $action_taken; ?></td>  
                            <td style="border: 1px solid black; text-align: right;">1.0</td>
                        </tr>
                        <?php
                        $base_count++;
                        $base_total_units += 1.0;
                        # code...
                    }
                } else {
                    foreach ( $list_of_subjects as $list_key => $list_value){  
 

                        $this->db->select('*')->from('archive_exam_result'); 
                        $this->db->where('class_id', $list_value['class_id']);
                        $this->db->where('subject_id', $list_value['id']);
                        $this->db->where('student_id', $student_id); 
                        $query = $this->db->get(); 
                        $result_vc = $query->row_array(); 
                          
                        $first_q = $result_vc['first'];
                        $second_q = $result_vc['second'];
                        $third_q = $result_vc['third'];
                        $fourth_q = $result_vc['fourth'];
                        $first_add =  $first_q + $second_q;
                        $second_add =  $third_q + $fourth_q;
                        $total_grades =  $first_add + $second_add;
                        $final_grade =  $total_grades/4;
                        if( $final_grade > 74.4 ){
                            $action_taken = "P";
                        } else {
                            $action_taken = "F";
                        } 

                        if( $final_grade == '74.5'){
                            $final_grade = '75';
                        } elseif(  $final_grade > '74.4' && $final_grade <  75 ){
                            $final_grade = '75';
                        }
                        ?>
                        <tr> 
                            <td style="border: 1px solid black; text-align: left;height:20px"><?php echo $list_value['name']; ?></td>
                            
                            <td style="border: 1px solid black; text-align: right;"><?php echo number_format($final_grade, 2, '.', ''); ?></td> 
                            <td style="border: 1px solid black; text-align: center;"><?php echo $action_taken; ?></td>  
                            <td style="border: 1px solid black; text-align: right;">1.0</td>
                        </tr>
                        <?php
                        $base_count++;
                        $base_total_units += 1.0;
                        # code...
                    }

                }

            }
            for ($i=$base_count; $i < 15; $i++) { 
                ?>
                <tr>
                     <td style="border: 1px solid black; height:20px"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td> 
                </tr>
                <?php
            }
            ?>
            <tr>
                <td style="border: 1px solid black;">GENERAL AVERAGE</td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td> 
            </tr> 
        </table> 
        <table width="100%" style="font-size: 10px;"> 
            <tr>
                 <td width="25%"><em>Days of School</em></td>  
                 <td width="65%" style="text-align: right;"><em>Total Units Earned </em></td>  
                 <td width="10%" style="border: 1px solid black;"><em> &nbsp; <?php if(   $base_total_units > 0 ){ echo number_format($base_total_units, 1, '.', ''); } ?></em></td>  
                 
            </tr>
            <tr> 
                <td colspan="3"><em>Days Present</em></td>   
            </tr>
            <tr> 
                <td colspan="3"><em>Total years in school to date</em></td>   
            </tr>
        </table>  
        <?php
        $grades_contentxx = ob_get_contents();
        ob_end_clean( );

        return $grades_contentxx;
    } */
	
	
	 function generate_secondary_table_group_grades( $list_of_subjects,  $student_id, $session_id=null, $form_data=array() ){
       

		$selected_session_array = $this->session_model->get( $session_id );   
		$current_session =  isset($selected_session_array['session'])?$selected_session_array['session']:null;
		 if( $list_of_subjects ){ 
			$grade_display = strtoupper($list_of_subjects[0]['class']); 
			$school_year_display = $current_session; 
		} else {
			$grade_display = '';
			$school_year_display = '';  
		}
        $grades_content = '';
		$gradingsettings = $this->gradingsetting_model->getbySession( $session_id );
		$decimal_grades = isset($gradingsettings->decimal_grades)?$gradingsettings->decimal_grades:0;
		$decimal_average = isset($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;
		$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:2;
		$if_gradeisnull = isset($gradingsettings->if_gradeisnull)?$gradingsettings->if_gradeisnull:'blank';
		$allow_to_pass = isset($gradingsettings->allow_to_pass)?$gradingsettings->allow_to_pass:'no';
		$combine_category = 'ga';
        ob_start();
        ?>
        <table width="100%" border="1" style="font-size: 10px;  border-collapse: collapse; margin-top: 5px;">
            <tr>
                <td colspan="4" style="padding-left: 5px;padding-right: 5px">
                    <table width="100%">
                        <tr>
                            <td style="text-align: left;">Classified as <?php echo  $grade_display; ?></td>
							<?php 
							if($school_year_display ){
								?>
								<td style="text-align: right;">SY <?php echo  $school_year_display; ?></td>
								<?php 
							} else {
								?>
								<td style="text-align: right;">SY &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
								<?php
							}?>
						</tr>
                        <tr>
                            <td colspan="2">School: <?php echo $form_data['from_school']; ?></td>
                            
                        </tr>
                    </table> 
                </td>  
            </tr>
            <tr style="text-align: center;"> 
                <th style="border: 1px solid black;" style="border: 1px solid black;" colspan="2" width="55%"  >SUBJECTS</th>
                <th style="border: 1px solid black;">Final <br /> Rating</th> 
                <th style="border: 1px solid black;">Action<br/>Taken</th>
                <!--<th style="border: 1px solid black;">Credits<br/>Earned</th>-->
            </tr>
            <?php 
            $base_count = 1;
            $base_total_units = 0;
			$gen_ave = 0;
			$count = 0;
			$total_grade = 0;
			$final_grade = 0;
			$complete_grades = true;
            if( $list_of_subjects ){ 
               foreach ( $list_of_subjects as $list_key => $list_value){  
					$complete_grades = true;
					$complete_first = true;
					$complete_second = true;
					$complete_third = true;
					$complete_fourth = true;
					$subject_id = $list_value['id'];
					$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );
					$display_card = $subject_manager_details['display_card'];
					$include_computation = $subject_manager_details['include_computation'];
					$align_right = $subject_manager_details['align_right'];
					$units = $subject_manager_details['units'];
					$this->db->select('*')->from('archive_exam_result'); 
					$this->db->where('class_id', $list_value['class_id']);
					$this->db->where('subject_id', $subject_id );
					$this->db->where('student_id', $student_id); 
					$query = $this->db->get(); 
					$result_vc = $query->row_array(); 
			
					if( $result_vc ){
						$base_count++;
						/* $first_q = $result_vc['first'];
						$second_q = $result_vc['second'];
						$third_q = $result_vc['third'];
						$fourth_q = $result_vc['fourth']; */
						/* $first_add =  $first_q + $second_q;
						$second_add =  $third_q + $fourth_q;
						$total_grades =  $first_add + $second_add;
						$final_grade =  $total_grades/4; */
						$first_q = $result_vc['first'];
						if( empty( $first_q )){
							$complete_first = false;
						} else {
							$first_q = number_format( (float)$first_q,$decimal_grades,",","."); 
						}
						$second_q = $result_vc['second'];
						if( empty( $second_q )){
							$complete_second = false;
						} else {
						$second_q = number_format( (float)$second_q,$decimal_grades,",","."); 
						}
						$third_q = $result_vc['third'];
						if( empty( $third_q )){
							$complete_third = false;
						} else {
							$third_q = number_format( (float)$third_q,$decimal_grades,",","."); 
						}
						$fourth_q = $result_vc['fourth'];
						if( empty( $fourth_q )){
							$complete_fourth = false;
						} else {
							$fourth_q = number_format( (float)$fourth_q,$decimal_grades,",","."); 
						}
						$total_grades = $first_q + $second_q + $third_q + $fourth_q;
						$get_final = $result_vc['final'];
						if( $get_final ){
							$final_grade = $get_final;
						} else {
							$final_grade = $total_grades/4;
						}
					
						/* if( $final_grade ){
							$count++;
							$total_grade = $total_grade + $final_grade;
						}
						if( $final_grade > 74.4 ){
							$action_taken = "P";
						} else {
							$action_taken = "F";
						} 

						if( $final_grade == '74.5'){
							$final_grade = '75';
						} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
							$final_grade = '75';
						} */
						if( $allow_to_pass == 'yes'){
							if( $final_grade == '74.5'){
								$final_grade = '75';
							} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
								$final_grade = '75';
							}
						}
						if( $final_grade ){
							$count++;
							$total_grade = $total_grade + $final_grade;
						}
						$final_grade = number_format((float)$final_grade, $decimal_finalgrade, '.', '');
						if( $final_grade > 74.4 ){
							$action_taken = "P";
						} else {
							$action_taken = "F";
						} 
						
						?>
						<tr> 
							<td style="border: 1px solid black; text-align: left;height:20px;text-transform:uppercase;" colspan="2" width="55%" >&nbsp;<?php echo $list_value['name']; ?></td>
							<?php 
							if( $complete_first && $complete_second && $complete_third && $complete_fourth ){?>
								<td style="border: 1px solid black; text-align: center;"><?php echo $final_grade; ?></td> 
								<td style="border: 1px solid black; text-align: center;"><?php echo $action_taken; ?></td>  
								<?php 
							} else {
								?>
								<td style="border: 1px solid black; text-align: center;"></td> 
								<td style="border: 1px solid black; text-align: center;"></td>  
								<?php
							}
							?>
							<!--<td style="border: 1px solid black; text-align: right;">1.0</td>-->
						</tr>
						<?php
						//$base_total_units += 1.0;
						# code...
					} else {
						if( $display_card == 'yes' ){
							$base_count++;
							?>
						    <tr> 
							<?php 
							if( $align_right == 'yes'){?>
								<td style="border: 1px solid black; text-align: left;height:20px;text-transform:uppercase;" colspan="2" width="55%" >&nbsp;<?php echo $list_value['name']; ?></td>
								<?php
							} else {
								?>
								<td style="border: 1px solid black; text-align: left;height:20px;text-transform:uppercase;" colspan="2">&nbsp;&nbsp;<?php echo $list_value['name']; ?></td>
								<?php
							}
						}
                        //$grade_count = 1;
						$total_grades = 0;
						$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
						for( $quarter=1;$quarter<=4;$quarter++ ) {
							$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id );
							/* 	$examresult = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, $quarter, $list_value['id'], $session_id   );
							if( isset($examresult->grades) ){
								$grades = number_format($examresult->grades, 2, '.', '');
								 $total_grades +=  $grades;
							} else {
								$grades = '';
							} */
							if( $include_computation == 'yes' ){
								if( $grade_per_quarter == null ){
									$complete_grades = false;
								}  
							}	
							$total_grades +=  $grade_per_quarter;
						}
						/* if( $final_grade ){
							$count++;
							$total_grade = $total_grade + $final_grade;
						} */
						$final_grade =  $total_grades/4;
						
						if( $include_computation == 'yes' ){ 
							if( empty( $checkifChild ) ){
								if( $allow_to_pass == 'yes'){
									if( $final_grade == '74.5'){
										$final_grade = '75';
									} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
										$final_grade = '75';
									}
								}
							}
						}
						
						$final_grade = number_format((float)$final_grade, $decimal_finalgrade, '.', '');
						if( $final_grade > 74.4 ){
							$action_taken = "P";
						} else {
							$action_taken = "F";
						}
						if( $include_computation == 'yes' ){ 
							if( empty( $checkifChild ) ){
								$count++; 
								$total_grade = $total_grade +  $final_grade;
							}
						}

						/* if( $final_grade > 1 ){ 
							$display_final_grade =  number_format($final_grade, 2, '.', '');
						} else { 
							$display_final_grade = "";
						} 

						if( $display_final_grade == '74.5'){
							$display_final_grade = '75';
						} elseif(  $display_final_grade > '74.4' && $display_final_grade <  75 ){
							$display_final_grade = '75';
						}
						
						if( $final_grade > 74.4 ){
							$action_taken = "P"; 
						} else {
							if( $final_grade > 1 ){ 
								$action_taken = "F"; 
							} else {
								$action_taken = " "; 
							}
						   
						}  */
						
						if( $display_card == 'yes' ){
							if( $complete_grades ){
								?> 
								<td style="border: 1px solid black; text-align: center;"><?php echo $final_grade; ?></td> 
								<td style="border: 1px solid black; text-align: center;"><?php echo $action_taken; ?></td>  
                            <!--<td style="border: 1px solid black; text-align: right;">1.0</td>-->
								<?php 
							} else {
								?>
								<td style="border: 1px solid black; text-align: center;"></td> 
								<td style="border: 1px solid black; text-align: center;"></td>  
								<?php
							}?>
							</tr>
							<?php 
						}
						?>
                        <?php
                        //$base_total_units += 1.0;
                        # code...
					}
					  
		
				}

			}
            for ($i=$base_count; $i < 15; $i++) { 
                ?>
                <tr>
                     <td style="border: 1px solid black; height:20px" colspan="2"></td>
                     <td style="border: 1px solid black;"></td>
                     <td style="border: 1px solid black;"></td>
                     <!--<td style="border: 1px solid black;"></td> -->
                </tr>
                <?php
            }
			/* $gen_ave = $count!=0 ? $total_grade / $count:null;
			if( $gen_ave != null){
				$gen_ave = number_format($gen_ave, 2, '.', '');
			}
			if( $gen_ave == '74.5'){
				$gen_ave = '75';
			} elseif(  $gen_ave > '74.4' && $gen_ave <  75 ){
				$gen_ave = '75';
			}
			
			if( $gen_ave > 74.4 ){
				$action_taken = "P"; 
			} else {
				if( $gen_ave > 1 ){ 
					$action_taken = "F"; 
				} else {
					$action_taken = " "; 
				}
			   
			} */ 
			$gen_ave = $count!=0 ? $total_grade / $count:null;
			if( $gen_ave != null){
				$gen_ave = number_format($gen_ave, $decimal_finalgrade, '.', '');
			}
			if( $gen_ave == '74.5'){
				$gen_ave = '75';
			} elseif(  $gen_ave > '74.4' && $gen_ave <  75 ){
				$gen_ave = '75';
			}
			
			if( $gen_ave > 74.4 ){
				$action_taken = "P"; 
			} else {
				if( $gen_ave > 1 ){ 
					$action_taken = "F"; 
				} else {
					$action_taken = " "; 
				}
			   
			} 
		
			$getNoSchoolDays = $this->gradingsetting_model->getNoSchoolDays( $session_id );
			$get_total_present = $this->stuattendence_model->get_total_present( $student_id, $session_id );
			$days_of_school = $form_data['days_of_school'] != " "?$form_data['days_of_school']: $getNoSchoolDays;
			$days_present = $form_data['days_present'] != " "?$form_data['days_present']: $get_total_present;
			$total_years = $form_data['total_years'];
            ?>
            <tr>
                <td style="border: 1px solid black;padding:2px;" colspan="2">&nbsp;GENERAL AVERAGE</td>
				<?php 
				if( $complete_grades ){
					?>
					<td style="border: 1px solid black;text-align: center;"><?php echo $gen_ave;?></td>
					<td style="border: 1px solid black;text-align: center;"><?php echo $action_taken;?></td>
					<?php 
				} else {
					?>
					<td style="border: 1px solid black;text-align: center;"></td>
					<td style="border: 1px solid black;text-align: center;"></td>
					<?php
				}?>
			   <!--<td style="border: 1px solid black;"></td> -->
            </tr> 
        </table> 
        <table width="100%" style="font-size: 10px;"> 
			<tr><td></td></tr>
            <tr>
                 <td><em>Days of School</em>&nbsp;&nbsp;<?php echo $days_of_school;?></td>  
                 <!--<td width="65%" style="text-align: right;"><em>Total Units Earned </em></td> 
                 <td width="10%" style="border: 1px solid black;"><em> &nbsp; <?php //if(   $base_total_units > 0 ){ echo number_format($base_total_units, 1, '.', ''); } ?></em></td>   -->
                 
            </tr>
            <tr> 
                <td><em>Days Present</em>&nbsp;&nbsp;<?php echo $days_present;?></td>   
            </tr>
            <tr> 
                <td><em>Total years in school to date</em>&nbsp;&nbsp;<?php echo $total_years;?></td>   
            </tr>
        </table>  
        <?php
		
        $grades_contentxx = ob_get_contents();
        ob_end_clean( );

        return $grades_contentxx;
    }
	
	public function getStudentSchoolYear() {
        $student_id = $this->input->get('student_id');
        $data = $this->studentsession_model->getAllStudentsSession($student_id);
        echo json_encode($data);
    }
	
	public function checkifSemester() {
        $session_id = $this->input->get('session_id');
        $student_id = $this->input->get('student_id');
        $data = $this->student_model->getBySession($student_id, $session_id );
		$get_data = array();
		if( $data ){
			$class_id = $data['class_id'];
			$section_id = $data['section_id'];
			$class_name = $data['class'];
			$section = $data['section'];
			$class_name = trim( $class_name);
			$get_data['class_name'] = $class_name;
			$get_data['section'] = $section;
			if( $class_name == 'Grade 11' || $class_name == 'Grade 12'){
				$semester = 'true';
			} else {
				$semester = 'false';
			}
		} else {
			$semester = 'false';
		}
		$get_data['semester'] = $semester;
		$get_data['class_id'] = $class_id;
		$get_data['section_id'] = $section_id;
		
		echo json_encode($get_data);
    }
	
	
	 function edit_student( $id ) {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'report/forms');

		$data['student'] = $this->student_model->getStudentBasicInformation( $id );
		$data['id'] = $id;
		$genderList = $this->customlib->getGender();
	    $bloodList = array( '0 -', '0 +', 'A -', 'A +',  'B -', 'B +',  'AB -', 'AB +');
        $religionList = array( 'Roman Catholic', 'Islam', 'Evangelical', 'INC',  'SDA');
        $data['genderList'] = $genderList;
        $data['bloodList'] = $bloodList;
        $data['religionList'] = $religionList;
		 $this->form_validation->set_rules('firstname', 'First Name', 'trim|required|xss_clean');
		 $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|xss_clean');
		 if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/reports/studentEdit', $data);
            $this->load->view('layout/registrar/footer', $data);
        } else {
			 $father_phone = $this->input->post('father_phone');
            $father_phone = str_replace(' ', '', $father_phone);
            $mother_phone = $this->input->post('mother_phone');
            $mother_phone = str_replace(' ', '', $mother_phone);
            $guardian_phone = $this->input->post('guardian_phone');
            $guardian_phone = str_replace(' ', '', $guardian_phone);
			$admission_no = $this->input->post('admission_no');
			$studentstatus = $this->input->post('studentstatus') != '' ?$this->input->post('studentstatus'):'active';
            $data = array(
                'id' => $id,
                'admission_no' => $this->input->post('admission_no'),
                'admission_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('admission_date'))),
                'firstname' => $this->input->post('firstname'),
                'lastname' => $this->input->post('lastname'),
                'middlename' => $this->input->post('middlename'),
                'rte' => $this->input->post('rte'),
                'mobileno' => $this->input->post('mobileno'),
                'government_voucher' => $this->input->post('government_voucher'),
                'blood_type' => $this->input->post('blood_type'),
                'email' => $this->input->post('email'),
                'state' => $this->input->post('state'),
                'city' => $this->input->post('city'), 
                'guardian_is' => $this->input->post('guardian_is'),
                'religion' => $this->input->post('religion'),
                'dob' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('dob'))),
                'current_address' => $this->input->post('current_address'),
                'permanent_address' => $this->input->post('permanent_address'),
                'current_barangay_id' => $this->input->post('current_barangay_id'),
                'current_city_id' => $this->input->post('current_city_id'),
                'current_province_id' => $this->input->post('current_province_id'), 
                'permanent_barangay_id' => $this->input->post('current_barangay_id'),
                'permanent_city_id' => $this->input->post('current_city_id'),
                'permanent_province_id' => $this->input->post('current_province_id'), 
                'category_id' => $this->input->post('category_id'),
                'father_name' => $this->input->post('father_name'),
                'father_phone' => $father_phone,
                'father_occupation' => $this->input->post('father_occupation'),
                'mother_name' => $this->input->post('mother_name'),
                'mother_phone' => $mother_phone,
                'mother_occupation' => $this->input->post('mother_occupation'),
                'guardian_occupation' => $this->input->post('guardian_occupation'),
                'gender' => $this->input->post('gender'),
                'guardian_name' => $this->input->post('guardian_name'),
                'guardian_relation' => $this->input->post('guardian_relation'),
                'guardian_phone' => $guardian_phone,
                'guardian_address' => $this->input->post('guardian_address'),
                'guardian_barangay_id' => $this->input->post('guardian_barangay_id'),
                'guardian_city_id' => $this->input->post('guardian_city_id'),
                'guardian_province_id' => $this->input->post('guardian_province_id'),
        		'lrn' => $this->input->post('lrn'),
        		'middlename' => $this->input->post('middlename'),
        		'father_midname' => $this->input->post('father_midname'),
        		'father_lastname' => $this->input->post('father_lastname'),
        		'mother_midname' => $this->input->post('mother_midname'),
        		'mother_lastname' => $this->input->post('mother_lastname'),
        		'guardian_midname' => $this->input->post('guardian_midname'),
        		'guardian_lastname' => $this->input->post('guardian_lastname'),
                'lunch_pass' => $this->input->post('lunch_pass'),
                'commuter_pass' => $this->input->post('commuter_pass'),
                'esc_number' => $this->input->post('esc_number'),
                'government_number' => $this->input->post('government_number'),
                'suffix' => $this->input->post('suffix'),
                 'school_address' => $this->input->post('school_address'),
                'school_name' => $this->input->post('school_name'),
            );
			
			$this->student_model->add( $data );
			 if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'ST-'.$admission_no. '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/student_images/" . $img_name);
                $data_img = array('id' => $id, 'image' => 'uploads/student_images/' . $img_name);
                $this->student_model->add($data_img);
				$get_image = './uploads/student_images/'. $img_name;
				$this->imageresize->load( $get_image );
				$get_height = $this->imageresize->getHeight();
				$get_width = $this->imageresize->getWidth();
				// if( $get_height != '624' && $get_width != '464'){
				// 	 $this->imageresize->resize(464, 624);
                if( $get_height != '753' && $get_width != '661'){
                    $this->imageresize->resize(661, 753);
					 $this->imageresize->save( $get_image);
				 }
            }
			$this->session->set_flashdata('msg', '<div student="alert alert-success text-left">Student Record Updated successfully</div>');
			
			redirect('registrar/forms/edit_student/'.$id);
		}
    }
	
	public function updatesection() {
        $session_id = $this->input->post('session_id');
        $student_id = $this->input->post('student_id');
		$section_id = $this->input->post('section_id');
		if( $student_id ){
			$get_student_id = $this->student_model->getBySession( $student_id, $session_id );
			if( $get_student_id ){
				$student_session_id = $get_student_id['student_session_id'];
			}
			$update_array = array(
				'id' => $student_session_id,
				'section_id' => $section_id
			);
			$this->studentsession_model->add($update_array);
			$data = $this->student_model->getBySession($student_id, $session_id );
			
			$get_data = array();
			if( $data ){
				$class_id = $data['class_id'];
				$section_id = $data['section_id'];
				$class_name = $data['class'];
				$section = $data['section'];
				$class_name = trim( $class_name);
				$get_data['class_name'] = $class_name;
				$get_data['section'] = $section;
				if( $class_name == 'Grade 11' || $class_name == 'Grade 12'){
					$semester = 'true';
					
				} else {
					$semester = 'false';
				}
			} else {
				$semester = 'false';
			}
			$get_data['semester'] = $semester;
			$get_data['class_id'] = $class_id;
			$get_data['section_id'] = $section_id;
			
			echo json_encode($get_data);
		}
    }
	
}

?>