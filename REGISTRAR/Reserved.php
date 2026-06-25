<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class reserved extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('smsgateway');
        $this->load->library('imageresize');
        $this->load->library('excel');
        $this->load->helper('file');
        $this->lang->load('message', 'english');
        $this->role;
        $this->load->library('auth');
		$this->load->model('reserved_students_model');
		$this->load->model('student_model');
		$this->load->model('studentsession_model');
		$this->load->model('enrollmentsettings_model');
		$this->load->model('Notification_API_model','notification_api_model');
        $this->load->model('Sms_model','SMSM'); 
        $this->load->model('strand_model'); 
        $this->load->model('enrollmentonline_model'); 
         $this->load->model('reserved_payment_model'); 
        $this->auth->is_logged_in_registrar();
    }

    function index() {
		$this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'student/reserved');
        $data['title'] = 'Student List';
        $student_result = $this->reserved_students_model->get_confirmed();

        $data['studentlist'] = $student_result;
        $data['count_unconfirmed'] = $this->reserved_students_model->countUnconfirmedStudents();
		$genderList = $this->customlib->getGender();
		$data['genderList'] = $genderList;
		$class = $this->class_model->get();
        $data['classlist'] = $class;
		$session_result = $this->session_model->getAllSession();
        $data['sessionlist'] = $session_result;
		$setting_result = $this->setting_model->get();
		$data['session_id'] = $setting_result[0]['sy_enrollment'];
		$data['gender'] = 'Male';
		$religionList = $this->customlib->getReligion();
		$data['religionList'] = $religionList;
		$data['modalityList'] = array( 'online'=>'Online', 'face_to_face' => 'Face to Face Classes');
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/student/studentReserved', $data);
        $this->load->view('layout/registrar/footer', $data);
    }

    function delete($id) {
        $this->reserved_students_model->remove($id);
    }

    function save_new() {

        $student_result = $this->reserved_students_model->get();
        $data['studentlist'] = $student_result;
		$genderList = $this->customlib->getGender();
		$data['genderList'] = $genderList;
		$class = $this->class_model->get();
        $data['classlist'] = $class;
		$session_result = $this->session_model->getAllSession();
        $data['sessionlist'] = $session_result;
		$setting_result = $this->setting_model->get();
		$data['session_id'] = $setting_result[0]['sy_enrollment'];
		$status = 'reserved';
		$religionList = $this->customlib->getReligion();
		$data['religionList'] = $religionList;

        $this->form_validation->set_rules('firstname', 'First Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean');
        $this->form_validation->set_rules('dob', 'Date of Birth', 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', 'Incoming Grade', 'trim|required|xss_clean');
        $this->form_validation->set_rules('session_id', 'School Year', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_phone', 'Guardian Phone', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_name', 'Guardian First Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_lastname', 'Guardian Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_relation', 'Guardian Relation', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_street', 'Guardian Street', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_barangay', 'Guardian Barangay', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_city_municipality', 'Guardian City/Municipality', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_province', 'Guardian Province', 'trim|required|xss_clean');
        $this->form_validation->set_rules('subsidized', 'Subsidized', 'trim|required|xss_clean');
        $this->form_validation->set_rules('if_adventist', 'If Baptized', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/registrar/header', $data);
			$this->load->view('registrar/student/studentReserved', $data);
			$this->load->view('layout/registrar/footer', $data);
        } else {
        	$check_data = array(
				'search_firstname' => $this->input->post('firstname'),
				'search_lastname' => $this->input->post('lastname'),
				'search_dob' => date('Y-m-d', strtotime($this->input->post('dob'))),
				'search_gender' => $this->input->post('gender'),
			);
			$check_student_name = $this->student_model->checkStudentExist( $check_data );
			if( $check_student_name ){
				$this->session->set_flashdata('msg', '<div class="alert alert-danger">Your information existed on the database. Please use the old student option.</div>');
			} else {
				
				// required fields
				$firstname =  $this->input->post('firstname');
				$lastname =  $this->input->post('lastname');
				$middlename =  $this->input->post('middlename');
				$suffix =  $this->input->post('suffix');
				$guardian_street =  $this->input->post('guardian_street');
				$guardian_barangay =  $this->input->post('guardian_barangay');
				$guardian_city_municipality =  $this->input->post('guardian_city_municipality');
				$guardian_province =  $this->input->post('guardian_province');
				$dob = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('dob')));
				$gender = $this->input->post('gender');
				$guardian_name = $this->input->post('guardian_name');
				$guardian_lastname = $this->input->post('guardian_lastname');
				$guardian_relation = $this->input->post('guardian_relation');
				$guardian_phone = $this->input->post('guardian_phone');
				$subsidized =  $this->input->post('subsidized');
				$class_id = $this->input->post('class_id');
				$session_id = $this->input->post('session_id');
			

				$religion =  $this->input->post('religion');
				$other_religion = $this->input->post('other_religion');
				if( $religion ){
					if( $religion == 'other'){
						$other_religion = $this->input->post('other_religion');
						if( $other_religion ){
							$religion = $other_religion;
						}
					}
				}

				$address1 = $guardian_street.' '.$guardian_barangay;
				$address2 = $guardian_city_municipality.' '.$guardian_province;

				$modality =  $this->input->post('modality');
				$modality_array = array('1'=>$modality,'2'=>$modality,'3'=> $modality,'4'=>$modality);
			

				

	            $data_array = array(
	                'type' => 'new',
	                'class_id' => $class_id,
	                'session_id' => $session_id,
	                'firstname' => $firstname,
	                'lastname' => $lastname,
	                'middlename' => $middlename,
	                'suffix' => $suffix,
	                'dob' => $dob,
	                'gender' => $gender,
	                'guardian_name' => $guardian_name,
	                'guardian_lastname' => $guardian_lastname,
	                'guardian_relation' => $guardian_relation,
	                'guardian_phone' => $guardian_phone,
					'guardian_address' => $address1,
					'guardian_address2' => $address2,
					'subsidized' => $subsidized,
					'religion' => $religion,
	                'status' => 'reserved',
	                'subsidized' => $subsidized,
	                'modality' =>  serialize( $modality_array )
	            );
				
				
				// optional 
				$lrn = $this->input->post('lrn');
				$middlename = $this->input->post('middlename');
				$guardian_midname = $this->input->post('guardian_midname');
				$suffix = $this->input->post('suffix');
				$school_address = $this->input->post('school_address');
				$school_name = $this->input->post('school_name');
				$institution =  $this->input->post('institution');
				$strand_id =  $this->input->post('strand_id');
				$semester = $this->input->post('semester');
				$if_adventist = $this->input->post('if_adventist');
				$year_baptism = $this->input->post('year_baptism');

				if( $if_adventist ){
					$data_array = array_merge( $data_array, array('if_adventist' => $if_adventist));
					if( $if_adventist == 'yes' && $year_baptism ){
						$data_array = array_merge( $data_array, array('year_baptism' => $year_baptism));
					}
				}
			

				if( $subsidized == 'yes'){
					$data_array = array_merge( $data_array, array('institution' => $institution ) );
				}
				if( $lrn ){
					$data_array = array_merge( $data_array, array('lrn' => $lrn ) );
				}
				
				if( $middlename ){
					$data_array =  array_merge( $data_array, array('middlename' => $middlename ) );
				}
				
				if( $guardian_midname ){
					$data_array = array_merge( $data_array, array('guardian_midname' => $guardian_midname ) );
				}
				
				if( $school_name ){
					$data_array = array_merge( $data_array, array('school_name' => $school_name ) );
				}
				
				if( $school_address ){
					$data_array = array_merge( $data_array, array('school_address' => $school_address ) );
				}

				if( $suffix ){
					$data_array = array_merge( $data_array, array('suffix' => $suffix ) );
				}
				
				if( $strand_id ){
					$data_array = array_merge( $data_array, array('strand_id' => $strand_id ));
				}
				if( $semester ){
					$data_array = array_merge( $data_array, array('semester' => $semester ));
				}


				$payment_type = $this->input->post('payment_type');
				if( $payment_type ){
					$data_array = array_merge( $data_array, array('payment_type' => $payment_type ));
				}
				$payment_fees = $this->enrollmentonline_model->get_enrollment_fees( $class_id, $payment_type );
				if( $payment_fees ){
					$data_array = array_merge( $data_array, array('payment_fees' => $payment_fees ));
				}

				/*if( $guardian_relation ){
					$data_array = array_merge( $data_array, array('guardian_relation' => $guardian_relation ));
					if( $guardian_relation == 'Mother' || $guardian_relation == 'mother'){
						$data_stable_only = array_merge( $data_stable_only, array('mother_name' => $guardian_name ));
						$data_stable_only = array_merge( $data_stable_only, array('mother_lastname' => $guardian_lastname ));
						$data_stable_only = array_merge( $data_stable_only, array('mother_phone' => $guardian_phone ));
						$data_stable_only = array_merge( $data_stable_only, array('guardian_is' => 'mother' ));
						if( $guardian_midname ){
							$data_stable_only = array_merge( $data_stable_only, array('mother_midname' => $guardian_midname ));
						}
					} elseif($guardian_relation == 'Father' || $guardian_relation == 'father'){
						$data_stable_only = array_merge( $data_stable_only, array('father_name' => $guardian_name ));
						$data_stable_only = array_merge( $data_stable_only, array('father_lastname' => $guardian_lastname ));
						$data_stable_only = array_merge( $data_stable_only, array('father_phone' => $guardian_phone ));
						$data_stable_only = array_merge( $data_stable_only, array('guardian_is' => 'father' ));
						if( $guardian_midname ){
							$data_stable_only = array_merge( $data_stable_only, array('father_midname' => $guardian_midname ));
						}
					}
				}
				$data_stable_only = array_merge( $data_array, $data_stable_only );
				$data_stable_only = array_merge( $data_stable_only, array('image' => 'uploads/student_images/no_image.png' )  );
				//$insert_id = $this->student_model->add($data_stable_only);*/
				
				//if( $insert_id ){
					/*$class_id = $this->input->post('class_id');
					$session_id = $this->input->post('session_id');
					$strand_id = $this->input->post('strand_id');
					$data1 = array(
					//	'student_id' => $insert_id,
						'session_id' => $session_id,
						'class_id' => $class_id,
						'type' => 'new',
						'status' => 'reserved',
					);	
					
					
					
					$data1 = array_merge( $data_array, $data1 );*/
					$save_data = $this->reserved_students_model->add($data_array);
					if( $save_data ){
					 $this->session->set_flashdata('msg', '<div class="alert alert-success">Reserved Student added Successfully.</div>');
					}
				//}
			}
        }

		redirect('registrar/reserved/index');
    }
	
     function save_old() {
        
		$student_result = $this->reserved_students_model->get();
        $data['studentlist'] = $student_result;
		$genderList = $this->customlib->getGender();
		$data['genderList'] = $genderList;
		$class = $this->class_model->get();
        $data['classlist'] = $class;
		$session_result = $this->session_model->getAllSession();
        $data['sessionlist'] = $session_result;
		$setting_result = $this->setting_model->get();
		$data['session_id'] = $setting_result[0]['sy_enrollment'];
       
        $this->form_validation->set_rules('class_id', 'Incoming Grade', 'trim|required|xss_clean');
        $this->form_validation->set_rules('session_id', 'School Year', 'trim|required|xss_clean');
		$this->form_validation->set_rules('guardian_phone', 'Guardian Phone', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_name', 'Guardian First Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_lastname', 'Guardian Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_relation', 'Guardian Relation', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) { 
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/studentEdit', $data);
            $this->load->view('layout/registrar/footer', $data);
        } else {
       
			$sy_id = isset($setting_result[0]['sy_enrollment'])?$setting_result[0]['sy_enrollment']:$setting_result[0]['session_id'];
			$session_details = $this->session_model->get($sy_id);
			$sy_enrollment =  $session_details['session'];
			
			$student_id = $this->input->post('student_id');
			if( $student_id ){
				$check_exist = $this->reserved_students_model->getByStudent( $student_id, $sy_id );
				$check_if_enrolled = $this->student_model->check_if_enrolled( $student_id, $sy_id );
				if($check_exist){
					$this->session->set_flashdata('msg', '<div class="alert alert-danger">Student registered already for SY '.$sy_enrollment.'.</div>');
				} elseif( $check_if_enrolled ){
					$this->session->set_flashdata('msg', '<div class="alert alert-danger">Student enrolled already for SY '.$sy_enrollment.'.</div>');
				}else { 
					//$student_details = $this->student_model->getStudentBasicInformation( $student_id );
					// required fields
					$class_id = $this->input->post('class_id');
					$session_id = $this->input->post('session_id');

					$firstname = $this->input->post('firstname');
					$middlename = $this->input->post('middlename');
					$lastname = $this->input->post('lastname');
					$suffix = $this->input->post('suffix');
					$guardian_name = $this->input->post('guardian_name');
					$guardian_lastname = $this->input->post('guardian_lastname');
					$guardian_phone = $this->input->post('guardian_phone');
					$guardian_relation = $this->input->post('guardian_relation');
					$religion = $this->input->post('religion');
					$subsidized = $this->input->post('subsidized');
					$other_religion = $this->input->post('other_religion');
					
					if( $religion ){
						if( $religion == 'other'){
							$other_religion = $this->input->post('other_religion');
							if( $other_religion ){
								$religion = $other_religion;
							}
						}
					}

					$modality =  $this->input->post('modality');
					$modality_array = array('1'=>$modality,'2'=>$modality,'3'=> $modality,'4'=>$modality);


					$data1 = array(
						'student_id' => $student_id,
						'session_id' => $session_id,
						'class_id' => $class_id,
						'type' => 'old',
						'status' => 'reserved',
						'firstname' => $firstname,
						'lastname' => $lastname,
						'religion' => $religion,
						'modality' => serialize( $modality_array ),
						'subsidized' => $subsidized,
						'guardian_name' => $guardian_name,
						'guardian_lastname' => $guardian_lastname,
						'guardian_relation' => $guardian_relation,
						'guardian_phone' => $guardian_phone
					);	
					

					// optional
					$guardian_midname = $this->input->post('guardian_midname');
					$mobileno = $this->input->post('mobileno');
					$strand_id = $this->input->post('strand_id');
					$semester = $this->input->post('semester');
					$institution = $this->input->post('institution');
					$if_adventist = $this->input->post('if_adventist');
					$year_baptism = $this->input->post('year_baptism');

					if( $if_adventist ){
						$data1 = array_merge( $data1, array('if_adventist' => $if_adventist));
						if( $if_adventist == 'yes' && $year_baptism ){
							$data1 = array_merge( $data1, array('year_baptism' => $year_baptism));
						}
					}
			
					
					if( $subsidized == 'yes'){
						$data1 = array_merge( $data1, array('institution' => $institution ) );
					}

					$payment_type = $this->input->post('payment_type');
					if( $payment_type ){
						$data1 = array_merge( $data1, array('payment_type' => $payment_type ));
					}
					$payment_fees = $this->enrollmentonline_model->get_enrollment_fees( $class_id, $payment_type );
					if( $payment_fees ){
						$data1 = array_merge( $data1, array('payment_fees' => $payment_fees ));
					}
					$payment_link = $this->enrollmentonline_model->get_payment_link( $class_id, $payment_type );
					if( $payment_link ){
						$data1 = array_merge( $data1, array('payment_link' => $payment_link ));
					}
			
					if( $middlename ){
						$data1 = array_merge($data1, array('middlename' => $middlename ) );
					} 
					if( $suffix ){
						$data1 = array_merge($data1, array('suffix' => $suffix ) );
					} 
					if( $guardian_midname ){
						$data1 = array_merge($data1, array('guardian_midname' => $guardian_midname ) );
					} 
					if( $mobileno ){
						$data1 = array_merge($data1, array('mobileno' => $mobileno ) );
					} 
					if( $strand_id ){
						$data1 = array_merge($data1, array('strand_id' => $strand_id ) );
					} 

					if( $semester ){
						$data1 = array_merge($data1, array('semester' => $semester ) );
					} 
					$insert = $this->reserved_students_model->add($data1);
					if( $insert ){
						$this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Reserved Student added successfully.</div>');
					}
				}
				
			} else {
				$this->session->set_flashdata('msg', '<div class="alert alert-warning text-left">Student Not Found.</div>');
			}
         
         
        }
		 redirect('registrar/reserved/index');
    }
	
	public function activate(){
		$notification_array = array();
		$notification_array1 = array();
		$get_id = $this->input->post('get_id');
		$class_id_array = array();
	/* 	$enrollment_settings = $this->enrollmentsettings_model->get();
		if( $enrollment_settings ){ */
			if(count( $get_id ) > 0 ){
				for( $x=0; $x<count( $get_id );$x++ ){
					if(isset($get_id[$x])){
						$student_get_id = $get_id[$x];
						$reserved_details = $this->reserved_students_model->get( $student_get_id );
						
						// e skip ang student nga wala ang button nga "Activate" sa action column
						$enrollment_status = $reserved_details['enrollment_status'];
						$review_cashier = $reserved_details['review_cashier'];
						$paid_status = $reserved_details['paid_status'];
						$can_activate = ($review_cashier == 'yes' && $enrollment_status == 'passed') || ($paid_status == 'promissory' && $enrollment_status == 'passed');
						
						if (!$can_activate) {
							continue; // e skip and mag continue sa next check
						}
						$student_id = $reserved_details['student_id'];
						$session_id = $reserved_details['session_id'];
						$class_id = $reserved_details['class_id'];
						$status = $reserved_details['status'];
						$parent_id = $reserved_details['parent_id'];
						
						// data will be updated if old
						$dob = $reserved_details['dob'];
						$gender = $reserved_details['gender'];
						$guardian_name = $reserved_details['guardian_name'];
						$guardian_midname = $reserved_details['guardian_midname'];
						$guardian_lastname = $reserved_details['guardian_lastname'];
						$guardian_phone = $reserved_details['guardian_phone'];
						
						$firstname = $reserved_details['firstname'];
						$lastname = $reserved_details['lastname'];
						$dob = $reserved_details['dob'];
						$gender = $reserved_details['gender'];
						$guardian_name = $reserved_details['guardian_name'];
						$guardian_lastname = $reserved_details['guardian_lastname'];
						$guardian_phone = $reserved_details['guardian_phone'];
						$guardian_relation = $reserved_details['guardian_relation'];
						$guardian_address = $reserved_details['guardian_address'];
						$guardian_address2 = $reserved_details['guardian_address2'];
						$middlename = $reserved_details['middlename'];
						$suffix = $reserved_details['suffix'];
						$guardian_is = $reserved_details['guardian_is'];
						$guardian_midname = $reserved_details['guardian_midname'];
						$mobileno = $reserved_details['mobileno'];
						$doc1_title = $reserved_details['doc1_title'];
						$doc1_upload = $reserved_details['doc1_upload'];
						$doc2_title = $reserved_details['doc2_title'];
						$doc2_upload = $reserved_details['doc2_upload'];
						$doc3_title = $reserved_details['doc3_title'];
						$doc3_upload = $reserved_details['doc3_upload'];
						$school_name = $reserved_details['school_name'];
						$strand_id = $reserved_details['strand_id'];
						$semester = $reserved_details['semester'];
						$doc4_title = $reserved_details['doc4_title'];
						$doc4_upload = $reserved_details['doc4_upload'];
						$school_address = $reserved_details['school_address'];
						$religion = $reserved_details['religion'];
						$payment_type = $reserved_details['payment_type'];
						$payment_fees = $reserved_details['payment_fees'];
						$payment_link = $reserved_details['payment_link'];
						$email_address = $reserved_details['email_address'];
						$modality = $reserved_details['modality'];
						// missing fields
						$subsidized = $reserved_details['subsidized'];
						$institution = $reserved_details['institution'];
						$if_adventist = $reserved_details['if_adventist'];
						$year_baptism = $reserved_details['year_baptism'];
						$lrn = $reserved_details['lrn'];
						
						if( $status == 'reserved'){	
							$type = $reserved_details['type'];
							if( empty( $student_id ) ){
									$data1 = array(
									'firstname' => $firstname,
									'lastname' => $lastname,
									'dob' => $dob,
									'gender' => $gender,
									'guardian_name' => $guardian_name,
									'guardian_lastname' => $guardian_lastname,
									'guardian_phone' => $guardian_phone,
									'guardian_address' => $guardian_address,
									'guardian_address2' => $guardian_address2,
									'image' => 'uploads/student_images/no_image.png',
									'status' => 'active',
								);
								if( $middlename ){
									$data1 = array_merge( $data1, array('middlename' => $middlename));
								}
								if( $suffix ){
									$data1 = array_merge( $data1, array('suffix' => $suffix));
								}
								
								if( $guardian_is ){
									$data1 = array_merge( $data1, array('guardian_is' => $guardian_is ));
								}
								if( $guardian_midname ){
									$data1 = array_merge( $data1, array('guardian_midname' => $guardian_midname ));
								}
								$check_admission = $this->check_admission();
								if( $check_admission ){
									$data1 = array_merge( $data1, array('admission_no' => $check_admission ));
								}
								if( $guardian_relation ){
									$data1 = array_merge( $data1, array('guardian_relation' => $guardian_relation ));
									if( $guardian_relation == 'Mother' || $guardian_relation == 'mother'){
										$data1 = array_merge( $data1, array('mother_name' => $guardian_name ));
										$data1 = array_merge( $data1, array('mother_lastname' => $guardian_lastname ));
										$data1 = array_merge( $data1, array('mother_phone' => $guardian_phone ));
										if( $guardian_midname ){
											$data1 = array_merge( $data1, array('mother_midname' => $guardian_midname ));
										}
									} elseif($guardian_relation == 'Father' || $guardian_relation == 'father'){
										$data1 = array_merge( $data1, array('father_name' => $guardian_name ));
										$data1 = array_merge( $data1, array('father_lastname' => $guardian_lastname ));
										$data1 = array_merge( $data1, array('father_phone' => $guardian_phone ));
										if( $guardian_midname ){
											$data1 = array_merge( $data1, array('father_midname' => $guardian_midname ));
										}
									}
								}
								if( $school_name ){
									$data1 = array_merge( $data1, array('school_name' => $school_name ));
								}
								if( $school_address ){
									$data1 = array_merge( $data1, array('school_address' => $school_address ));
								}
								if( $religion ){
									$data1 = array_merge( $data1, array('religion' => $religion ));
								}
								if( $email_address ){
									$data1 = array_merge( $data1, array('email' => $email_address ));
								}
								// missing fields
								if( $subsidized ){
									$data1 = array_merge( $data1, array('subsidized' => $subsidized ));
								}
								if( $institution ){
									$data1 = array_merge( $data1, array('institution' => $institution ));
								}
								if( $if_adventist ){
									$data1 = array_merge( $data1, array('if_adventist' => $if_adventist ));
								}
								if( $year_baptism ){
									$data1 = array_merge( $data1, array('year_baptism' => $year_baptism ));
								}
								if( $lrn ){
									$data1 = array_merge( $data1, array('lrn' => $lrn ));
								}
								
								$student_id = $this->student_model->add($data1);
								
								// ── Backfill paymongo: user_id = new students.id ──────────────────
								$this->_backfill_paymongo($student_get_id, $student_id);
								// ─────────────────────────────────────────────────────────────────

								$result = $this->smsgateway->sentRegisterSMS($student_id, $guardian_phone );
							}
							$details = $this->studentsession_model->getStudentsBySession( $session_id, null, null,$student_id );
							if( $details ){
								// $firstname = $details['firstname'];
								// $lastname = $details['lastname'];
								$session = $details['session'];
								$class_name = $details['class'];
								array_push(  $notification_array, '<div class="alert alert-warning">Student <b>'.$firstname.'</b> <b>'.$lastname.'</b> ( '.$class_name.' ) already enrolled for SY '.$session.'.</div>');	
							} else {
								if( $student_id ){
									$report_card = './uploads/student_documents/r' . $student_get_id . '/'.$doc1_upload;
									$good_moral = './uploads/student_documents/r' . $student_get_id . '/'.$doc2_upload;
									$birth_certicate = './uploads/student_documents/r' . $student_get_id . '/'.$doc3_upload;
									$esc_certificate = './uploads/student_documents/r' . $student_get_id . '/'.$doc4_upload;
									
									if ( file_exists($report_card)) {
										$uploaddir = './uploads/student_documents/' . $student_id . '/';
										if (!file_exists($uploaddir)) {
											
											if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
												die("Error creating folder $uploaddir");
											}
										}
										if (file_exists($uploaddir)) {
											copy($report_card, $uploaddir.'/'.$doc1_upload );
											//move_uploaded_file($ruploaddir, $uploaddir.'/'.$doc );
											$data_img = array('student_id' => $student_id, 'title' => $doc1_title, 'doc' => $doc1_upload );
											$this->student_model->adddoc($data_img);
										}
									}
									if ( file_exists($good_moral)) {
										$uploaddir = './uploads/student_documents/' . $student_id . '/';
										if (!file_exists($uploaddir)) {
											if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
												die("Error creating folder $uploaddir");
											}
										}
										if (file_exists($uploaddir)) {
											copy($good_moral, $uploaddir.'/'.$doc2_upload );
											//move_uploaded_file($ruploaddir, $uploaddir.'/'.$doc );
											$data_img = array('student_id' => $student_id, 'title' => $doc2_title, 'doc' => $doc2_upload );
											$this->student_model->adddoc($data_img);
										}
									}
									if ( file_exists($birth_certicate)) {
											
										$uploaddir = './uploads/student_documents/' . $student_id . '/';
										if (!file_exists($uploaddir)) {
											
											if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
												die("Error creating folder $uploaddir");
											}
										}
										if (file_exists($uploaddir)) {
											copy($birth_certicate, $uploaddir.'/'.$doc3_upload );
											//move_uploaded_file($ruploaddir, $uploaddir.'/'.$doc );
											$data_img = array('student_id' => $student_id, 'title' => $doc3_title, 'doc' => $doc3_upload );
											$this->student_model->adddoc($data_img);
										}
									}
									if ( file_exists($esc_certificate)) {
											
										$uploaddir = './uploads/student_documents/' . $student_id . '/';
										if (!file_exists($uploaddir)) {
											
											if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
												die("Error creating folder $uploaddir");
											}
										}
										if (file_exists($uploaddir)) {
											copy($esc_certificate, $uploaddir.'/'.$doc4_upload );
											//move_uploaded_file($ruploaddir, $uploaddir.'/'.$doc );
											$data_img = array('student_id' => $student_id, 'title' => $doc4_title, 'doc' => $doc4_upload );
											$this->student_model->adddoc($data_img);
										}
									}
									
								}
								
								$section_details = $this->section_model->getbyname('none');
								$section_id = $section_details['id'];
								$getDatetime = $this->setting_model->getDatetime();
								$data_new = array(
									'student_id' => $student_id,
									'strand_id' => $strand_id,
									'semester' => $semester,
									'class_id' => $class_id,
									'session_id' => $session_id,
									'enrolled_at' => $getDatetime,
								);

								if( $payment_type ){
									$data_new = array_merge( $data_new, array('payment_type' => $payment_type ));
								}
								if( $payment_link ){
									$data_new = array_merge( $data_new, array('payment_link' => $payment_link ));
								}
								if( $payment_fees ){
									$data_new = array_merge( $data_new, array('payment_fees' => $payment_fees ));
								}
								
								if( $modality ){
									$data_new = array_merge( $data_new, array('modality' => $modality ));
								}

								if( $section_id ){
									$class_section_details = $this->section_model->getClassSectionBySession( $class_id, $section_id, $session_id );
									
									if( empty( $class_section_details) ){
										$auto_add = array(
											'class_id' => $class_id,
											'session_id' => $session_id,
											'section_id' => $section_id,
											'is_active' => 'no',
										);
										$id = $this->classsection_model->auto_add( $auto_add );
									}
									$data_new = array_merge( $data_new, array('section_id' => $section_id ));
								}
								
							
									
								$success = $this->student_model->add_student_session($data_new);
								if( $type == 'old' ){
									$update_old_data = array(
										'id' => $student_id,
										'firstname' => $firstname,
										'lastname' => $lastname,
										'middlename' => $middlename,
										'suffix' => $suffix,
										'status' => 'active'
									);
									if( $dob ){
										$update_old_data = array_merge( $update_old_data, array('dob' => $dob ) );
									}
									if( $gender ){
										$update_old_data = array_merge( $update_old_data, array('gender' => $gender ) );
									}
									if( $guardian_name ){
										$update_old_data = array_merge( $update_old_data, array('guardian_name' => $guardian_name ) );
									}
									if( $guardian_lastname ){
										$update_old_data = array_merge( $update_old_data, array('guardian_lastname' => $guardian_lastname ) );
									}
									if( $guardian_phone ){
										$update_old_data = array_merge( $update_old_data, array('guardian_phone' => $guardian_phone ) );
									}
									if( $guardian_midname ){
										$update_old_data = array_merge( $update_old_data, array('guardian_midname' => $guardian_midname ) );
									}
									if( $mobileno ){
										$update_old_data = array_merge( $update_old_data, array('mobileno' => $mobileno ) );
									}
									if( $religion ){
										$update_old_data = array_merge( $update_old_data, array('religion' => $religion ) );
									}
									if( $email_address ){
										$update_old_data = array_merge( $update_old_data, array('email' => $email_address ) );
									}
									// missing fields
									if( $subsidized ){
										$update_old_data = array_merge( $update_old_data, array('subsidized' => $subsidized ));
									}
									if( $institution ){
										$update_old_data = array_merge( $update_old_data, array('institution' => $institution ));
									}
									if( $if_adventist ){
										$update_old_data = array_merge( $update_old_data, array('if_adventist' => $if_adventist ));
									}
									if( $year_baptism ){
										$update_old_data = array_merge( $update_old_data, array('year_baptism' => $year_baptism ));
									}
									if( $lrn ){
										$update_old_data = array_merge( $update_old_data, array('lrn' => $lrn ));
									}
									$update_student = $this->student_model->add($update_old_data);
									$this->delete( $student_get_id );

									// ── Backfill paymongo: user_id = existing students.id ─────────────
									$this->_backfill_paymongo($student_get_id, $student_id);
									// ─────────────────────────────────────────────────────────────────

									//$student_details = $this->student_model->get( $student_id );
									$student_details = $this->studentsession_model->getStudentsBySession( $session_id, null, null,$student_id );
									$firstname = $student_details['firstname'];
									$lastname = $student_details['lastname'];
									$session = $student_details['session'];
									$class_name = $student_details['class'];
									if( $success ){
										//$this->enrollmentsettings_model->insert_preenrollment_fee( $success,  $session_id );
										$enrollment_settings = $this->enrollmentsettings_model->getByClass( $session_id , $class_id );
										if( $enrollment_settings ){
											$this->enrollmentsettings_model->get_pre_enrollment( $success,  $enrollment_settings, $session_id );
										} else {
											if(!in_array( $class_id, $class_id_array, TRUE )){
												$class_id_array[] = $class_id;
												array_push( $notification_array1, '<div class="alert alert-warning">Enrollment Fee for <b>'.$class_name.'</b> is not yet set in Enrollment Settings for SY '.$session.'. Kindly contact the cashier.</div>');	
											}
										}
										
										array_push( $notification_array , '<div class="alert alert-success">Student <b>'.$firstname.'</b> <b>'.$lastname.'</b> ( '.$class_name.' ) enrolled for SY '.$session.' successfully.</div>');	
										
										$this->db->select('users.*, students.guardian_name, students.guardian_name, students.guardian_midname, students.guardian_lastname, students.guardian_phone');
										$this->db->from('users');   
										$this->db->join('students', 'students.id = users.user_id'); 
										$this->db->where('users.user_id', $student_id);
										$this->db->where('users.role', 'parent');
										$query = $this->db->get();
										$student_details =  $query->row_array(); 
										 
										$username = $student_details['username']; 
										$password = $student_details['password'];     
										$sessionschoolname = $this->setting_model->getCurrentSchoolName();
										// $guardian_phone = "09451299241"; 
										// $guardian_phone = "09365320333"; 
										//$message2 = "You can browse your childs progress through: ".site_url().". Login: ".$username." Password: ".$password.". SMS is Autogenerated. Please do not reply.";
										//$message2 = "Check your childs school performance via Parent Portal app for Android and iOS devices to be available soon.-- Bridgette Generated SMS. Do Not Reply. --";
										$message1 = "Good Day Parent! ".$lastname.", ".$firstname." is now officially enrolled at ".$sessionschoolname."..-- Bridgette Generated SMS. Do Not Reply. --";


										$guardian_phone = str_replace(' ', '', $guardian_phone);
										$guardian_phone = str_replace('-', '', $guardian_phone);
										if (strlen($guardian_phone)>10) {
											$data_sms_parent1 = array(
												'notification_id' => 0,
												'sms_msg' => $message1,
												'sms_number' => $guardian_phone, 
												'sms_status' => 'idle',
												'sms_teacher' => 'no',
												'sms_parent' => 'yes' 
											);   
											if( isset($data_sms_parent1) ){ 
												 $this->SMSM->add($data_sms_parent1);
											}   

										}
										/*$guardian_phone = str_replace(' ', '', $guardian_phone);
										$guardian_phone = str_replace('-', '', $guardian_phone);
										if (strlen($guardian_phone)>10) {
											$data_sms_parent = array(
												'notification_id' => 0,
												'sms_msg' => $message2,
												'sms_number' => $guardian_phone, 
												'sms_status' => 'idle',
												'sms_teacher' => 'no',
												'sms_parent' => 'yes' 
											);  
						 
											if( isset($data_sms_parent) ){ 
												 $this->SMSM->add($data_sms_parent);
											}   

										}*/
									}
								} else {
									$admission_no_code = $this->check_admission();
									$this->student_model->add(array('id' => $student_id, 'admission_no'=>$admission_no_code, 'admission_date' => date('Y-m-d'),'status' => 'active'));
									
									
									// check if student user is exist
									$get_student_account = $this->user_model->get_student_account( $student_id );
									if( empty($get_student_account) ){
										$user_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
										$data_student_login = array(
											'username' => $this->student_login_prefix . $student_id,
											'password' => $user_password,
											'user_id' => $student_id,
											'role' => 'student'
										);
										$this->user_model->add($data_student_login);
										if( $parent_id ){
											$user_details = $this->parent_model->getChildByParentID( $parent_id );
											$get_childs = $user_details['childs'];
											if( $get_childs ){
												$childs = $get_childs.','.$student_id;
											} else {
												$childs = $student_id;
											}
											
											$this->user_model->add(array('id'=>$parent_id,'childs'=>$childs));
										} else {
											$parent_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
											 $data_parent_login = array(
												'username' => $this->parent_login_prefix . $student_id,
												'password' => $parent_password,
												'user_id' => $student_id,
												'role' => 'parent',
												'childs' => $student_id
											);
											$ins_id = $this->user_model->add($data_parent_login);
										}
									}
						
				
									$this->delete( $student_get_id );
									//$student_details = $this->student_model->get( $student_id );

									// ── Backfill paymongo: user_id = students.id ──────────────────────
									$this->_backfill_paymongo($student_get_id, $student_id);
									// ────────────────────────────────────────────────────────────────

									$student_details = $this->studentsession_model->getStudentsBySession( $session_id, null, null,$student_id );
									$firstname = $student_details['firstname'];
									$lastname = $student_details['lastname'];
									$session = $student_details['session'];
									$class_name = $student_details['class'];
									if( $success ){
										$enrollment_settings = $this->enrollmentsettings_model->getByClass( $session_id , $class_id );
										if( $enrollment_settings ){
											$this->enrollmentsettings_model->get_pre_enrollment( $success,  $enrollment_settings, $session_id );
										} else {
											if(!in_array( $class_id, $class_id_array, TRUE )){
												$class_id_array[] = $class_id;
												array_push( $notification_array1, '<div class="alert alert-warning">Enrollment Fee for <b>'.$class_name.'</b> is not yet set in Enrollment Settings for SY '.$session.'. Kindly contact the cashier.</div>');	
											}
										}
										//$this->enrollmentsettings_model->get_pre_enrollment( $success,  $class_id, $session_id );
										//$this->enrollmentsettings_model->insert_preenrollment_fee( $success,  $session_id );
										array_push( $notification_array, '<div class="alert alert-success">Student <b>'.$firstname.'</b> <b>'.$lastname.'</b> ( '.$class_name.' ) enrolled for SY '.$session.' successfully.</div>');	
										if( $parent_id ){
											$user_details = $this->parent_model->getChildByParentID( $parent_id );
											$get_childs = $user_details['childs'];
											$split = explode(",",$get_childs);
											for( $x=0;$x<count( $split );$x++){
												$get_childs_id = $split[$x];
												$this->db->select('users.*, students.guardian_name, students.guardian_name, students.guardian_midname, students.guardian_lastname, students.guardian_phone');
												$this->db->from('users');   
												$this->db->join('students', 'students.id = users.user_id'); 
												$this->db->where('users.user_id', $get_childs_id);
												$this->db->where('users.role', 'parent');
												$query = $this->db->get();
												$student_details =  $query->row_array(); 
												if( $student_details ){
													break;
												}
											}
										} else {
											$this->db->select('users.*, students.guardian_name, students.guardian_name, students.guardian_midname, students.guardian_lastname, students.guardian_phone');
											$this->db->from('users');   
											$this->db->join('students', 'students.id = users.user_id'); 
											$this->db->where('users.user_id', $student_id);
											$this->db->where('users.role', 'parent');
											$query = $this->db->get();
											$student_details =  $query->row_array(); 
										}
								
										 
										$username = $student_details['username']; 
										$password = $student_details['password'];     
										$sessionschoolname = $this->setting_model->getCurrentSchoolName();
										// $guardian_phone = "09451299241"; 
										// $guardian_phone = "09365320333"; 
										//$message2 = "You can browse your childs progress through: ".site_url().". Login: ".$username." Password: ".$password.". SMS is Autogenerated. Please do not reply.";
										//$message2 = "Check your childs school performance via Parent Portal app for Android and iOS devices to be available soon..-- Bridgette Generated SMS. Do Not Reply. --";
										$message1 = "Good Day Parent! ".$lastname.", ".$firstname." is now officially enrolled at ".$sessionschoolname."..-- Bridgette Generated SMS. Do Not Reply. --";


										$guardian_phone = str_replace(' ', '', $guardian_phone);
										$guardian_phone = str_replace('-', '', $guardian_phone);
										if (strlen($guardian_phone)>10) {
											$data_sms_parent1 = array(
												'notification_id' => 0,
												'sms_msg' => $message1,
												'sms_number' => $guardian_phone, 
												'sms_status' => 'idle',
												'sms_teacher' => 'no',
												'sms_parent' => 'yes' 
											);   
											if( isset($data_sms_parent1) ){ 
												 $this->SMSM->add($data_sms_parent1);
											}   

										}
										/*$guardian_phone = str_replace(' ', '', $guardian_phone);
										$guardian_phone = str_replace('-', '', $guardian_phone);
										if (strlen($guardian_phone)>10) {
											$data_sms_parent = array(
												'notification_id' => 0,
												'sms_msg' => $message2,
												'sms_number' => $guardian_phone, 
												'sms_status' => 'idle',
												'sms_teacher' => 'no',
												'sms_parent' => 'yes' 
											);  
						 
											if( isset($data_sms_parent) ){ 
												 $this->SMSM->add($data_sms_parent);
											}   

										}*/
									}
								
									
								}
								
							}
							
						}
						
					}
				}
				if( count($notification_array1) > 0 ){
						$notification_array = array_merge( $notification_array1, $notification_array );
				}
				$this->session->set_flashdata('msg',$notification_array);				
			} else {
					$this->session->set_flashdata('msg', '<div class="alert alert-warning text-left">Please select data.</div>');
				
			}
	/* 	} else {
			$this->session->set_flashdata('msg', '<div class="alert alert-warning text-left">Settings for Enrollment Fee is not yet setup. Kindly contact the cashier. </div>');
		} */
		
		redirect('registrar/reserved/index');
	}
	

	
	public function delete_all(){
		$get_id = $this->input->post('get_id');
		if(count( $get_id ) > 0 ){
			for( $x=0; $x< count( $get_id );$x++ ){
				if(isset($get_id[$x])){
					$student_get_id = $get_id[$x];
					$reserved_details = $this->reserved_students_model->get( $student_get_id );
					$type = $reserved_details['type'];
					$student_id = $reserved_details['student_id'];
					$this->reserved_students_model->remove( $student_get_id );
					if( $type == 'new' ){
						$this->student_model->remove( $student_id );
					}
				}
			}
			$this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Reserved Student(s) deleted successfully.</div>');
		} else {
			$this->session->set_flashdata('msg', '<div class="alert alert-warning text-left">Please select data.</div>');
		}
		
		 redirect('registrar/reserved/index');
	}	
	
	function unconfirmed() {
		$this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'student/reserved');
        $data['title'] = 'Student List';
        $student_result = $this->reserved_students_model->get_unconfirmed();
        $data['studentlist'] = $student_result;
		$genderList = $this->customlib->getGender();
		$data['genderList'] = $genderList;
		$class = $this->class_model->get();
        $data['classlist'] = $class;
		$session_result = $this->session_model->getAllSession();
        $data['sessionlist'] = $session_result;
		$setting_result = $this->setting_model->get();
		$data['session_id'] = $setting_result[0]['session_id'];
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/student/studentUReserved', $data);
        $this->load->view('layout/registrar/footer', $data);
    }
	
	
	
	function confirm_new() {
		
		$reserved_id = $this->input->post('reserved_id');
		if( $reserved_id ){
			$details = $this->reserved_students_model->get( $reserved_id );
			$student_id = $details['student_id'];
			$status = 'reserved';
			$dob = date('m/d/Y', strtotime($this->input->post('dob')));
			$data = array(
                'id' => $student_id,
                'firstname' => $this->input->post('firstname'),
                'lastname' => $this->input->post('lastname'),
                'dob' => date('Y-m-d', $this->customlib->datetostrtotime($dob)),
                'gender' => $this->input->post('gender'),
                'guardian_name' => $this->input->post('guardian_name'),
                'guardian_midname' => $this->input->post('guardian_midname'),
                'guardian_lastname' => $this->input->post('guardian_lastname'),
                'guardian_phone' => $this->input->post('guardian_phone'),
                'status' => $status,
            );
			$this->student_model->add($data);
			if( $reserved_id ){
				$class_id = $this->input->post('class_id');
				$session_id = $this->input->post('session_id');
				$data1 = array(
					'id' => $reserved_id,
					'session_id' => $session_id,
					'class_id' => $class_id,
					'type' => 'new',
				);	
				
				$this->reserved_students_model->add($data1);
			}
			
			$this->session->set_flashdata('msg', '<div class="alert alert-success">Reserved Student confirmed successfully.</div>');
		} else {
			$this->session->set_flashdata('msg', '<div class="alert alert-warning">Reserved Student confirmed unsuccessfully.</div>');
		}

		redirect('registrar/reserved/unconfirmed');
    }
	
    function confirm_old() {
        
		$reserved_id = $this->input->post('reserved_id');
		if( $reserved_id ){
			$student_id = $this->input->post('student_id');
			if( $student_id ){
				$check_exist = $this->reserved_students_model->getByStudent( $student_id );
				if($check_exist){
					$this->session->set_flashdata('msg', '<div class="alert alert-warning text-left">This student reserved already.</div>');
				} else {
					$details = $this->reserved_students_model->get( $reserved_id );
					$reserved_student_id = $details['student_id'];
					$student_status = $details['student_status'];
					$class_id = $this->input->post('class_id');
					$session_id = $this->input->post('session_id');
					$data1 = array(
						'id' => $reserved_id,
						'student_id' => $student_id,
						'session_id' => $session_id,
						'class_id' => $class_id,
						'type' => 'old'
					);	
				
					$this->reserved_students_model->add( $data1 );
					if( $reserved_student_id && $student_status == 'unconfirmed'){
						$this->student_model->remove( $reserved_student_id );
					}
					$this->session->set_flashdata('msg', '<div class="alert alert-success">Reserved Student confirmed successfully.</div>');
				}
			} else {
				$this->session->set_flashdata('msg', '<div class="alert alert-warning">Student not found.</div>');	
			}
		} else {
			$this->session->set_flashdata('msg', '<div class="alert alert-warning">Reserved Student confirmed unsuccessfully.</div>');
		}
		redirect('registrar/reserved/unconfirmed');
    }
	
	function getReservedStudentByID(){
		$id = $this->input->post('id');
        $resultlist = $this->reserved_students_model->get($id);
        echo json_encode($resultlist);
	}
	
	function getNextClassID(){
		$id = $this->input->post('student_id');
        $resultlist = $this->student_model->getLatestStudentInformation($id);
		$data = array();
		if( $resultlist ){
		
			$class_name = $resultlist['class'];
			$session =  $resultlist['session'];
			//$data['guardian_phone'] =  $resultlist['guardian_phone'];
			$data['msg'] = 'Recent: '.$class_name.' for S.Y. '.$session;
			$class_id = $resultlist['class_id'];
			
			$next_class_details = $this->class_model->getnextClassID( $class_id );
			$data['next_class'] =  $next_class_details['id'];
		}
        echo json_encode($data);
	}
	
	function unconfirmed_all(){
		$get_id = $this->input->post('get_id');
		if(count( $get_id ) > 0 ){
			for( $x=0; $x< count( $get_id );$x++ ){
				if(isset($get_id[$x])){
					$student_get_id = $get_id[$x];
					$this->reserved_students_model->add( array('id' => $student_get_id, 'type' => 'unconfirmed') 	);
					/* $reserved_details = $this->reserved_students_model->get( $student_get_id );
					$type = $reserved_details['type'];
					$student_id = $reserved_details['student_id'];
					$this->reserved_students_model->add( $student_get_id );
					if( $type == 'new' ){
						$this->student_model->remove( $student_id );
					} */
				}
			}
			$this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Reserved Student(s) unconfirmed successfully.</div>');
		} else {
			$this->session->set_flashdata('msg', '<div class="alert alert-warning text-left">Please select data.</div>');
		}
		
		 redirect('registrar/reserved/index');
	}
	
	public function delete_unconfirmed(){
		$get_id = $this->input->post('get_id');
		if(count( $get_id ) > 0 ){
			for( $x=0; $x< count( $get_id );$x++ ){
				if(isset($get_id[$x])){
					$student_get_id = $get_id[$x];
					$reserved_details = $this->reserved_students_model->get( $student_get_id );
					$type = $reserved_details['type'];
					$student_status = $reserved_details['student_status'];
					$student_id = $reserved_details['student_id'];
					$this->reserved_students_model->remove( $student_get_id );
					if( $type == 'new' && $student_status == 'unconfirmed'){
						$this->student_model->remove( $student_id );
					}
				}
			}
			$this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Unconfirmed Reserved Student(s) deleted successfully.</div>');
		} else {
			$this->session->set_flashdata('msg', '<div class="alert alert-warning text-left">Please select data.</div>');
		}
		
		redirect('registrar/reserved/unconfirmed');
	}	
	
	// backfill paymongo user_id in payments and payments_enrollment tables when reserved student is confirmed
	private function _backfill_paymongo($reserved_id, $new_student_id)
	{
		$this->load->model('paymongo_payments_enrollment_model');
		$this->load->model('paymongo_payment_model');
	
		// paymongo_payments_enrollment: SET user_id = students.id, reserved_id = NULL
		$this->paymongo_payments_enrollment_model
			->backfill_user_id_from_reserved($reserved_id, $new_student_id);
	
		// paymongo_payments: SET user_id = students.id  (matched via order_id)
		$this->paymongo_payment_model
			->backfill_user_id_from_reserved($reserved_id, $new_student_id);
	
		log_message('info',
			'[reserved::_backfill_paymongo]'
			. ' reserved_id='    . $reserved_id
			. ' new_student_id=' . $new_student_id
		);
	}
		
	function check_admission( $student_id=null ){
		
		$unique_admission = FALSE;
		$admission_no_code = '';
		while( $unique_admission == FALSE ){
			// $result = $this->db->query('admission_no')->order_by('admission_no','desc')->limit(1)->get('students')->row('admission_no');
			$result = $this->db->query("SELECT admission_no FROM students where admission_no > 0 order by admission_no DESC limit 1")->row('admission_no');

			/* Second variation number of the admissio number */
			$n = 1; //nth dash  
			$pieces = explode('-', $result);
			$part1 = implode('-', array_slice($pieces, 0, $n)); 
			 if(isset( $pieces[$n])){
				$part2 = $pieces[$n]; 
			} else {
				 $part2 = 0; 
			} 
			$admission_no_code_1 = date('Y'); 
			$admission_no_code_2 = str_pad( $part2+1,5,'0', STR_PAD_LEFT);
			$admission_no_code_2 = substr($admission_no_code_2, -5);
			$admission_no_code = $admission_no_code_1.'-'.$admission_no_code_2;  2;  
			$check_admission = $this->student_model->check_unique_admission( $admission_no_code );

			if( $check_admission ){
				$unique_admission =  FALSE;
			} else {
				$unique_admission =  TRUE;
			}
		}
		
		
		return $admission_no_code;
		
	}
	
	public function interview( $id ){
		$link = "https://www.facebook.com/Bridgette-Smart-School-112746257066849/";
		$reserved_details = $this->reserved_students_model->get( $id );
		$firstname = $reserved_details['firstname'];
		$lastname = $reserved_details['lastname'];
		$guardian_phone = $reserved_details['guardian_phone'];
		$sms_message = "You are halfway to being officially enrolled. Please visit our FB page ".$link." and book for a video interview with the registrar. Thank you.-- Bridgette Generated SMS. Do Not Reply. --";
		$date_scheduled = date("y-m-d h:i:sa");
		if( $guardian_phone ){
			$data_sms  = array(
				'sms_msg' => $sms_message,
				'sms_number' => $guardian_phone, 
				'sms_status' => 'idle',
				'sms_teacher' => 'no',
				'sms_parent' => 'yes' 
			);
			$message = $this->SMSM->add($data_sms); 
				
		}
		if( $message ){
			$this->reserved_students_model->add( array('id'=>$id,'enrollment_status'=> 'scheduled', 'date_scheduled' => $date_scheduled ) );
			$this->session->set_flashdata("enrollment_msg", "<div class=\"alert alert-success\">Message for interview sent to <b>".$firstname." ".$lastname.".</b></div>");
		}
		redirect($_SERVER['HTTP_REFERER']);
		
	}
	
	public function passed1( $id ){
		$link = "https://bit.ly/2VAYFE2";
		if( $id ){
			$reserved_details = $this->reserved_students_model->get( $id );
			$student_id = $reserved_details['student_id'];
			$guardian_phone = $reserved_details['guardian_phone'];
			$firstname = $reserved_details['firstname'];

			$lastname = $reserved_details['lastname'];
			$payment_link = $reserved_details['payment_link'];
			if(empty( $student_id)){
				$firstname = $reserved_details['firstname'];
				$lastname = $reserved_details['lastname'];
				$dob = $reserved_details['dob'];
				$gender = $reserved_details['gender'];
				$guardian_name = $reserved_details['guardian_name'];
				$guardian_lastname = $reserved_details['guardian_lastname'];
				$guardian_phone = $reserved_details['guardian_phone'];
				$guardian_relation = $reserved_details['guardian_relation'];
				$guardian_address = $reserved_details['guardian_address'];
				$guardian_address2 = $reserved_details['guardian_address2'];
				$middlename = $reserved_details['middlename'];
				$suffix = $reserved_details['suffix'];
				$guardian_is = $reserved_details['guardian_is'];
				$guardian_midname = $reserved_details['guardian_midname'];
				$school_name = $reserved_details['school_name'];
				$school_address = $reserved_details['school_address'];
				$religion = $reserved_details['religion'];
				$email_address = $reserved_details['email_address'];
				
				$data1 = array(
					'firstname' => $firstname,
					'lastname' => $lastname,
					'dob' => $dob,
					'gender' => $gender,
					'guardian_name' => $guardian_name,
					'guardian_lastname' => $guardian_lastname,
					'guardian_phone' => $guardian_phone,
					'guardian_address' => $guardian_address,
					'guardian_address2' => $guardian_address2,
					'status' => 'reserved',
				);
				if( $middlename ){
					$data1 = array_merge( $data1, array('middlename' => $middlename));
				}
				if( $suffix ){
					$data1 = array_merge( $data1, array('suffix' => $suffix));
				}
				
				if( $guardian_is ){
					$data1 = array_merge( $data1, array('guardian_is' => $guardian_is ));
				}
				if( $guardian_midname ){
					$data1 = array_merge( $data1, array('guardian_midname' => $guardian_midname ));
				}
				$check_admission = $this->check_admission();
				if( $check_admission ){
					$data1 = array_merge( $data1, array('admission_no' => $check_admission ));
				}
				if( $school_name ){
					$data1 = array_merge( $data1, array('school_name' => $school_name ));
				}
				if( $school_address ){
					$data1 = array_merge( $data1, array('school_address' => $school_address ));
				}
				if( $religion ){
					$data1 = array_merge( $data1, array('religion' => $religion ));
				}
				if( $email_address ){
					$data1 = array_merge( $data1, array('email' => $email_address ));
				}
				$student_id = $this->student_model->add($data1);
				if( $student_id ){
					$this->reserved_students_model->add( array('id'=>$id,'student_id'=> $student_id ) );
					$user_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
					$sibling_id = $this->input->post('sibling_id');
					$data_student_login = array(
						'username' => $this->student_login_prefix . $student_id,
						'password' => $user_password,
						'user_id' => $student_id,
						'role' => 'student'
					);
					$this->user_model->add($data_student_login);
					
					$parent_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
                    $temp = $student_id;
                    $data_parent_login = array(
                        'username' => $this->parent_login_prefix . $student_id,
                        'password' => $parent_password,
                        'user_id' => $student_id,
                        'role' => 'parent',
                        'childs' => $temp
                    );
                    $ins_id = $this->user_model->add($data_parent_login);
				}
			}
			if( $student_id ){	
				$student_details = $this->student_model->get( $student_id );
				$get_guardian_phone = $student_details['guardian_phone'];
				if( empty( $guardian_phone )){
					$guardian_phone = $get_guardian_phone;
				}
				
				$student_details = $this->user_model->getLoginDetails( $student_id );
				foreach( $student_details as $key => $value ){
					$get_role =  $value->role;
					if( $get_role == 'parent'){
						$username = $value->username;
						$password = $value->password;
						break;
					}
				}
				if( $guardian_phone  ){
					$get_count = strlen($guardian_phone);
					if( $get_count > 10  ){
						/* $get_left = 4 - $get_count;
						$get_right = $get_count -  4;
						$get_first = substr( $guardian_phone, 0, $get_left );
						$get_last = substr( $guardian_phone, -4 );
						$get_count_first = strlen( $get_first );
						$get_count_second = strlen( $get_last );
						$get_star = $get_count - ($get_count_first + $get_count_second );
						$star  = '';
						for( $x=1;$x<=$get_star;$x++){
							$star =  $star.'*';
						} */
						$school_code = 'MMA';
						$enrollmentonline_result = $this->enrollmentonline_model->get();
						$passed_evaluation = isset($enrollmentonline_result[0]['passed_evaluation'])?$enrollmentonline_result[0]['passed_evaluation']:"";
						if( $passed_evaluation ){
							$firstname = strtolower( $firstname );
							$firstname = ucwords( $firstname );
							$lastname = strtolower( $lastname );
							$lastname = ucwords( $lastname );
							$data_msg['firstname'] = $firstname;
							$data_msg['lastname'] = $lastname;
							$data_msg['payment_link'] = $payment_link;
							$data_msg['school_code'] = $school_code;
							$data_msg['guardian_phone'] = $guardian_phone;
							$sms_message = $this->enrollmentonline_model->replace_msg( $passed_evaluation, $data_msg );
						} else {
							$sms_message = 'FROM '.$school_code.': Hi '.$firstname.' '.$lastname.'. Your enrollment is almost complete. Please pay the enrollment fee by clicking '.$payment_link.'. Thank you.-- Bridgette Generated SMS. Do Not Reply. --';
							//$sms_message = 'FROM MMA: Congratulations '.$lastname.'! You passed the final evaluation. Please pay ENROLLMENT FEE - PHP2,500.00 by clicking '.$link.' to be officially enrolled. See You Soon!-- Bridgette Generated SMS. Do Not Reply. --';
						}
						// $sms_message = 'You can now pay enrollment online. Visit '.$link.' and login using your Username:'.$username.'and Password:'.$password;
						if( $guardian_phone ){
							$get_count = strlen($guardian_phone);
							if( $get_count > 10   ){
								$data_sms  = array(
									'sms_msg' => $sms_message,
									'sms_number' => $guardian_phone, 
									'sms_status' => 'idle',
									'sms_teacher' => 'no',
									'sms_parent' => 'yes' 
								);
								$message = $this->SMSM->add($data_sms); 
							}
						}
					
					} else {
						$data['message'] = 'Incorrect number registered. Please contact the registrar.';
					}
				} else {
					$data['message'] = 'No number registered. Please contact the registrar.';
				}

				$this->reserved_students_model->add( array('id'=>$id,'enrollment_status'=> 'passed' ) );
			}
			
			
			$this->session->set_flashdata("enrollment_msg", "<div class=\"alert alert-success\"><b>".$firstname." ".$lastname."</b> successfully pass the interview.</div>");
		}
		redirect($_SERVER['HTTP_REFERER']);
		
	}

	public function passed( $id ){
		if( $id ){
			$reserved_details = $this->reserved_students_model->get( $id );
			$student_id = $reserved_details['student_id'];
			$guardian_phone = $reserved_details['guardian_phone'];
			$firstname = $reserved_details['firstname'];
			$lastname = $reserved_details['lastname'];
			$payment_link = $reserved_details['payment_link'];
			$process_type = $reserved_details['process_type'];
			
			if( $guardian_phone  ){
				$get_count = strlen($guardian_phone);
				if( $get_count > 10  ){
					if( $process_type == 'school'){
						$send_sms = $this->input->get('sms');
						if( $send_sms == 'yes' ){
							$school_code = 'MMA';
							$firstname = strtolower( $firstname );
							$firstname = ucwords( $firstname );
							$lastname = strtolower( $lastname );
							$lastname = ucwords( $lastname );
							$sms_message = 'FROM '.$school_code.': Hi '.$firstname.' '.$lastname.'. Your enrollment is almost complete. You can now pay to the cashier. Thank you.-- Bridgette Generated SMS. Do Not Reply. --';
							if( $guardian_phone ){
								$get_count = strlen($guardian_phone);
								if( $get_count > 10   ){
									$data_sms  = array(
										'sms_msg' => $sms_message,
										'sms_number' => $guardian_phone, 
										'sms_status' => 'idle',
										'sms_teacher' => 'no',
										'sms_parent' => 'yes' 
									);
									$message = $this->SMSM->add($data_sms); 
								}
							}
						}
					} else {
						$school_code = 'MMA';
						$firstname = strtolower( $firstname );
						$firstname = ucwords( $firstname );
						$lastname = strtolower( $lastname );
						$lastname = ucwords( $lastname );
						$sms_message = 'FROM '.$school_code.': Hi '.$firstname.' '.$lastname.'. Your enrollment is almost complete. Please pay the enrollment fee by clicking '.$payment_link.'. Thank you.-- Bridgette Generated SMS. Do Not Reply. --';
						if( $guardian_phone ){
							$get_count = strlen($guardian_phone);
							if( $get_count > 10   ){
								$data_sms  = array(
									'sms_msg' => $sms_message,
									'sms_number' => $guardian_phone, 
									'sms_status' => 'idle',
									'sms_teacher' => 'no',
									'sms_parent' => 'yes' 
								);
								$message = $this->SMSM->add($data_sms); 
							}
						}
					}
					
					
				} else {
					$data['message'] = 'Incorrect number registered. Please update the Guardian Phone Number.';
				}
			} else {
				$data['message'] = 'No number registered. Please update the Guardian Phone Number.';
			}

				$this->reserved_students_model->add( array('id'=>$id,'enrollment_status'=> 'passed', 'passed_role' => 'registrar') );

				$this->session->set_flashdata("enrollment_msg", "<div class=\"alert alert-success\"><b>".$firstname." ".$lastname."</b> successfully pass the interview.</div>");
			}
			
		
		redirect($_SERVER['HTTP_REFERER']);
		
	}
	
	
	public function failed( $id ){
		if( $id ){
			$enrollmentonline_result = $this->enrollmentonline_model->get();
			$getCurrentSchoolName = $this->setting_model->getCurrentSchoolName();
			$failed_evaluation = isset($enrollmentonline_result[0]['failed_evaluation'])?$enrollmentonline_result[0]['failed_evaluation']:"";
			$school_code = 'MMA';		
			$reserved_details = $this->reserved_students_model->get( $id );
			$firstname = $reserved_details['firstname'];
			$lastname = $reserved_details['lastname'];
			$guardian_phone = $reserved_details['guardian_phone'];
			
			if( $failed_evaluation ){
				$firstname = strtolower( $firstname );
				$firstname = ucwords( $firstname );
				$lastname = strtolower( $lastname );
				$lastname = ucwords( $lastname );
				$data_msg['firstname'] = $firstname;
				$data_msg['lastname'] = $lastname;
				$data_msg['school_code'] = $school_code;
				$data_msg['guardian_phone'] = $guardian_phone;
				$data_msg['school_name'] = $getCurrentSchoolName;
				$sms_message = $this->enrollmentonline_model->replace_msg( $failed_evaluation, $data_msg );
			} else {
				$sms_message = 'FROM '.$school_code.': Hi '.$firstname.' '.$lastname.'. After thorough evaluation of your credentials, we regret to inform you that you can not be enrolled at '.$getCurrentSchoolName.' this school year. Thank you.-- Bridgette Generated SMS. Do Not Reply. --';
			}
			if( $guardian_phone ){
				$get_count = strlen($guardian_phone);
				if( $get_count > 10   ){
					$data_sms  = array(
						'sms_msg' => $sms_message,
						'sms_number' => $guardian_phone, 
						'sms_status' => 'idle',
						'sms_teacher' => 'no',
						'sms_parent' => 'yes' 
					);
					$message = $this->SMSM->add($data_sms); 
				}
			}
			$this->reserved_students_model->add( array('id'=>$id,'enrollment_status'=> 'failed','failed_role' => 'registrar' ) );
			$this->session->set_flashdata("enrollment_msg", "<div class=\"alert alert-warning\"><b>".$firstname." ".$lastname."</b> failed to pass the interview.</div>");
		}
		redirect($_SERVER['HTTP_REFERER']);
		
	}
	
	public function getinfo(){
		$id = $this->input->post('id');
		$get_data = array();
		$reserved_details = $this->reserved_students_model->getonreserved( $id );
		// printx($reserved_details);
		if( $reserved_details  ){
			if( isset( $reserved_details['student_id'] ) && $reserved_details['student_id'] ){
				$reserved_details = $this->reserved_students_model->get( $id );
				$student_basic = $this->student_model->get( $reserved_details['student_id'] );
				if( $student_basic ){
					if( empty( $reserved_details['lrn'] ) || $reserved_details['lrn'] == 'N/A' ) {
						$reserved_details['lrn'] = $student_basic['lrn'];
					}
					if( empty( $reserved_details['religion'] ) ) {
						$reserved_details['religion'] = $student_basic['religion'];
					}
					if( empty( $reserved_details['email_address'] ) || $reserved_details['email_address'] == 'N/A' ) {
						$reserved_details['email_address'] = $student_basic['email'];
					}
					if( empty( $reserved_details['guardian_name'] ) ) {
						$reserved_details['guardian_name'] = $student_basic['guardian_name'];
					}
					if( empty( $reserved_details['guardian_midname'] ) ) {
						$reserved_details['guardian_midname'] = $student_basic['guardian_midname'];
					}
					if( empty( $reserved_details['guardian_lastname'] ) ) {
						$reserved_details['guardian_lastname'] = $student_basic['guardian_lastname'];
					}
					if( empty( $reserved_details['guardian_phone'] ) ) {
						$reserved_details['guardian_phone'] = $student_basic['guardian_phone'];
					}
					if( empty( $reserved_details['guardian_relation'] ) ) {
						$reserved_details['guardian_relation'] = $student_basic['guardian_relation'];
					}
					if( empty( $reserved_details['guardian_address'] ) ) {
						$reserved_details['guardian_address'] = $student_basic['guardian_address'];
					}
				}
			} 
		}

		// $get_modality = $reserved_details['modality'];
		// if( $get_modality ){
		// 	$modality = is_serialiZed($get_modality)?unserialize($get_modality):''; 
		// 	if( $modality ){
		// 		$modality = $modality[1];
		// 		$modality = ucwords(str_replace( '_', ' ', $modality ));
		// 	}
		// 	$reserved_details = array_merge( $reserved_details, array('get_modality' => $modality ));
		// }

		$get_modality = isset($reserved_details['modality']) ? $reserved_details['modality'] : null;

		if ($get_modality) {
			$modality = is_serialized($get_modality) ? unserialize($get_modality) : '';

			if (!empty($modality) && isset($modality[1])) {
				$modality = ucwords(str_replace('_', ' ', $modality[1]));
			} else {
				$modality = 'Not Available'; // Default value if modality is empty
			}

			$reserved_details = array_merge($reserved_details, ['get_modality' => $modality]);
		}
		
		echo json_encode( $reserved_details );
	}
	
	public function get_activate( $id ){
		$notification_array = array();
		$notification_array1 = array();
		$class_id_array = array();
		if(isset($id)){
			$student_get_id = $id;
			$reserved_details = $this->reserved_students_model->get( $student_get_id );
			$student_id = $reserved_details['student_id'];
			$session_id = $reserved_details['session_id'];
			$class_id = $reserved_details['class_id'];
			$status = $reserved_details['status'];
			$parent_id = $reserved_details['parent_id'];
			$student_type = $reserved_details['type'];
			
			// data will be updated if old
			$dob = $reserved_details['dob'];
			$gender = $reserved_details['gender'];
			$guardian_name = $reserved_details['guardian_name'];
			$guardian_midname = $reserved_details['guardian_midname'];
			$guardian_lastname = $reserved_details['guardian_lastname'];
			$guardian_phone = $reserved_details['guardian_phone'];
			
			$firstname = $reserved_details['firstname'];
			$lastname = $reserved_details['lastname'];
			$dob = $reserved_details['dob'];
			$gender = $reserved_details['gender'];
			$guardian_name = $reserved_details['guardian_name'];
			$guardian_lastname = $reserved_details['guardian_lastname'];
			$guardian_phone = $reserved_details['guardian_phone'];
			$guardian_relation = $reserved_details['guardian_relation'];
			$guardian_address = $reserved_details['guardian_address'];
			$guardian_address2 = $reserved_details['guardian_address2'];
			$middlename = $reserved_details['middlename'];
			$suffix = $reserved_details['suffix'];
			$guardian_is = $reserved_details['guardian_is'];
			$guardian_midname = $reserved_details['guardian_midname'];
			$mobileno = $reserved_details['mobileno'];
			$doc1_title = $reserved_details['doc1_title'];
			$doc1_upload = $reserved_details['doc1_upload'];
			$doc2_title = $reserved_details['doc2_title'];
			$doc2_upload = $reserved_details['doc2_upload'];
			$doc3_title = $reserved_details['doc3_title'];
			$doc3_upload = $reserved_details['doc3_upload'];
			$doc4_title = $reserved_details['doc4_title'];
			$doc4_upload = $reserved_details['doc4_upload'];
			$school_name = $reserved_details['school_name'];
			$school_address = $reserved_details['school_address'];
			$religion = $reserved_details['religion'];
			$strand_id = $reserved_details['strand_id'];
			$semester = $reserved_details['semester'];
			$payment_type = $reserved_details['payment_type'];
			$payment_fees = $reserved_details['payment_fees'];
			$payment_link = $reserved_details['payment_link'];
			$email_address = $reserved_details['email_address'];
			$modality = $reserved_details['modality'];
			$subsidized = $reserved_details['subsidized'];
			$institution = $reserved_details['institution'];
			$if_adventist = $reserved_details['if_adventist'];
			$year_baptism = $reserved_details['year_baptism'];
			$lrn = $reserved_details['lrn'];
			
			if( $status == 'reserved'){	
				$type = $reserved_details['type'];
				if( empty( $student_id ) ){
						$data1 = array(
						'firstname' => $firstname,
						'lastname' => $lastname,
						'dob' => $dob,
						'gender' => $gender,
						'guardian_name' => $guardian_name,
						'guardian_lastname' => $guardian_lastname,
						'guardian_phone' => $guardian_phone,
						'guardian_address' => $guardian_address,
						'guardian_address2' => $guardian_address2,
						'school_name' => $school_name,
						'image' => 'uploads/student_images/no_image.png',
						'status' => 'active',
					);
					if( $middlename ){
						$data1 = array_merge( $data1, array('middlename' => $middlename));
					}
					if( $suffix ){
						$data1 = array_merge( $data1, array('suffix' => $suffix));
					}
					
					if( $guardian_is ){
						$data1 = array_merge( $data1, array('guardian_is' => $guardian_is ));
					}
					if( $guardian_midname ){
						$data1 = array_merge( $data1, array('guardian_midname' => $guardian_midname ));
					}
					$check_admission = $this->check_admission();
					if( $check_admission ){
						$data1 = array_merge( $data1, array('admission_no' => $check_admission ));
					}
					if( $guardian_relation ){
						$data1 = array_merge( $data1, array('guardian_relation' => $guardian_relation ));
						if( $guardian_relation == 'Mother' || $guardian_relation == 'mother'){
							$data1 = array_merge( $data1, array('mother_name' => $guardian_name ));
							$data1 = array_merge( $data1, array('mother_lastname' => $guardian_lastname ));
							$data1 = array_merge( $data1, array('mother_phone' => $guardian_phone ));
							$data1 = array_merge( $data1, array('guardian_is' => 'mother' ));
							if( $guardian_midname ){
								$data1 = array_merge( $data1, array('mother_midname' => $guardian_midname ));
							}
						} elseif($guardian_relation == 'Father' || $guardian_relation == 'father'){
							$data1 = array_merge( $data1, array('father_name' => $guardian_name ));
							$data1 = array_merge( $data1, array('father_lastname' => $guardian_lastname ));
							$data1 = array_merge( $data1, array('father_phone' => $guardian_phone ));
							$data1 = array_merge( $data1, array('guardian_is' => 'father' ));
							if( $guardian_midname ){
								$data1 = array_merge( $data1, array('father_midname' => $guardian_midname ));
							}
						} else {
							$data1 = array_merge( $data1, array('guardian_is' => 'other' ));
						}
					}
					if( $school_name ){
						$data1 = array_merge( $data1, array('school_name' => $school_name ));
					}
					if( $school_address ){
						$data1 = array_merge( $data1, array('school_address' => $school_address ));
					}
					if( $religion ){
						$data1 = array_merge( $data1, array('religion' => $religion ));
					}
					if( $email_address ){
						$data1 = array_merge( $data1, array('email' => $email_address ));
					}
					if( $subsidized ){
						$data1 = array_merge( $data1, array('subsidized' => $subsidized ));
					}
					if( $institution ){
						$data1 = array_merge( $data1, array('institution' => $institution ));
					}
					if( $if_adventist ){
						$data1 = array_merge( $data1, array('if_adventist' => $if_adventist ));
					}
					if( $year_baptism ){
						$data1 = array_merge( $data1, array('year_baptism' => $year_baptism ));
					}
					if( $lrn ){
						$data1 = array_merge( $data1, array('lrn' => $lrn ));
					}
					$student_id = $this->student_model->add($data1);

					// ── Backfill paymongo: user_id = new students.id ──────────────────
					$this->_backfill_paymongo($student_get_id, $student_id);
					// ──────────────────────────────────────────────────────────────────
					
					$result = $this->smsgateway->sentRegisterSMS($student_id, $guardian_phone );
				}
				$details = $this->studentsession_model->getStudentsBySession( $session_id, null, null,$student_id );
				if( $details ){
					// $firstname = $details['firstname'];
					// $lastname = $details['lastname'];
					$session = $details['session'];
					$class_name = $details['class'];
					array_push(  $notification_array, '<div class="alert alert-warning">Student <b>'.$firstname.'</b> <b>'.$lastname.'</b> ( '.$class_name.' ) already enrolled for SY '.$session.'.</div>');	
				} else {
					if( $student_id ){
						$report_card = './uploads/student_documents/r' . $student_get_id . '/'.$doc1_upload;
						$good_moral = './uploads/student_documents/r' . $student_get_id . '/'.$doc2_upload;
						$birth_certicate = './uploads/student_documents/r' . $student_get_id . '/'.$doc3_upload;
						$esc_certificate = './uploads/student_documents/r' . $student_get_id . '/'.$doc4_upload;
						
						if ( file_exists($report_card) && $doc1_upload ) {
							$uploaddir = './uploads/student_documents/' . $student_id . '/';
							if (!file_exists($uploaddir)) {
								
								if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
									die("Error creating folder $uploaddir");
								}
							}
							if (file_exists($uploaddir)) {
								copy($report_card, $uploaddir.'/'.$doc1_upload );
								//move_uploaded_file($ruploaddir, $uploaddir.'/'.$doc );
								$data_img = array('student_id' => $student_id, 'title' => $doc1_title, 'doc' => $doc1_upload );
								$this->student_model->adddoc($data_img);
							}
						}
						if ( file_exists($good_moral) && $doc2_upload ) {
							$uploaddir = './uploads/student_documents/' . $student_id . '/';
							if (!file_exists($uploaddir)) {
								if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
									die("Error creating folder $uploaddir");
								}
							}
							if (file_exists($uploaddir)) {
								copy($good_moral, $uploaddir.'/'.$doc2_upload );
								//move_uploaded_file($ruploaddir, $uploaddir.'/'.$doc );
								$data_img = array('student_id' => $student_id, 'title' => $doc2_title, 'doc' => $doc2_upload );
								$this->student_model->adddoc($data_img);
							}
						}
						if ( file_exists($birth_certicate) && $doc3_upload ) {
								
							$uploaddir = './uploads/student_documents/' . $student_id . '/';
							if (!file_exists($uploaddir)) {
								
								if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
									die("Error creating folder $uploaddir");
								}
							}
							if (file_exists($uploaddir)) {
								copy($birth_certicate, $uploaddir.'/'.$doc3_upload );
								//move_uploaded_file($ruploaddir, $uploaddir.'/'.$doc );
								$data_img = array('student_id' => $student_id, 'title' => $doc3_title, 'doc' => $doc3_upload );
								$this->student_model->adddoc($data_img);
							}
						}
						if ( file_exists($esc_certificate) && $doc4_upload ) {
								
							$uploaddir = './uploads/student_documents/' . $student_id . '/';
							if (!file_exists($uploaddir)) {
								
								if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
									die("Error creating folder $uploaddir");
								}
							}
							if (file_exists($uploaddir)) {
								copy($esc_certificate, $uploaddir.'/'.$doc4_upload );
								//move_uploaded_file($ruploaddir, $uploaddir.'/'.$doc );
								$data_img = array('student_id' => $student_id, 'title' => $doc4_title, 'doc' => $doc4_upload );
								$this->student_model->adddoc($data_img);
							}
						}
						
					}
					
					$section_details = $this->section_model->getbyname('none');
					$section_id = $section_details['id'];
					$getDatetime = $this->setting_model->getDatetime();
					$data_new = array(
						'student_id' => $student_id,
						'strand_id' => $strand_id,
						'semester' => $semester,
						'class_id' => $class_id,
						'session_id' => $session_id,
						'enrolled_at' => $getDatetime,
					);
					if( $payment_type ){
						$data_new = array_merge( $data_new, array('payment_type' => $payment_type ));
					}
					if( $payment_link ){
						$data_new = array_merge( $data_new, array('payment_link' => $payment_link ));
					}
					if( $payment_fees ){
						$data_new = array_merge( $data_new, array('payment_fees' => $payment_fees ));
					}
					if( $modality ){
						$data_new = array_merge( $data_new, array('modality' => $modality ));
					}
					if( $student_type ){
						$data_new = array_merge( $data_new, array('student_type' => $student_type ));
					}

					if( $section_id ){
						$class_section_details = $this->section_model->getClassSectionBySession( $class_id, $section_id, $session_id );
						
						if( empty( $class_section_details) ){
							$auto_add = array(
								'class_id' => $class_id,
								'session_id' => $session_id,
								'section_id' => $section_id,
								'is_active' => 'no',
							);
							$id = $this->classsection_model->auto_add( $auto_add );
						}
						$data_new = array_merge( $data_new, array('section_id' => $section_id ));
					}
					
				
						
					$success = $this->student_model->add_student_session($data_new);

					$update_full_name = array(
						'id' => $student_id,
						'firstname' => $firstname,
						'lastname' => $lastname,
						'middlename' => $middlename,
						'suffix' => $suffix,
						'guardian_phone	' => $guardian_phone
					);

					$this->student_model->add( $update_full_name );

					if( $type == 'old' ){
						$update_old_data = array(
							'id' => $student_id,
							'firstname' => $firstname,
							'lastname' => $lastname,
							'middlename' => $middlename,
							'suffix' => $suffix,
							'status' => 'active'
						);
						if( $dob ){
							$update_old_data = array_merge( $update_old_data, array('dob' => $dob ) );
						}
						if( $gender ){
							$update_old_data = array_merge( $update_old_data, array('gender' => $gender ) );
						}
						if( $guardian_name ){
							$update_old_data = array_merge( $update_old_data, array('guardian_name' => $guardian_name ) );
						}
						if( $guardian_lastname ){
							$update_old_data = array_merge( $update_old_data, array('guardian_lastname' => $guardian_lastname ) );
						}
						if( $guardian_phone ){
							$update_old_data = array_merge( $update_old_data, array('guardian_phone' => $guardian_phone ) );
						}
						if( $guardian_midname ){
							$update_old_data = array_merge( $update_old_data, array('guardian_midname' => $guardian_midname ) );
						}
						if( $mobileno ){
							$update_old_data = array_merge( $update_old_data, array('mobileno' => $mobileno ) );
						}
						if( $religion ){
							$update_old_data = array_merge( $update_old_data, array('religion' => $religion ) );
						}
						if( $email_address ){
							$update_old_data = array_merge( $update_old_data, array('email' => $email_address ) );
						}
						if( $subsidized ){
							$update_old_data = array_merge( $update_old_data, array('subsidized' => $subsidized ) );
						}
						if( $institution ){
							$update_old_data = array_merge( $update_old_data, array('institution' => $institution ) );
						}
						if( $if_adventist ){
							$update_old_data = array_merge( $update_old_data, array('if_adventist' => $if_adventist ));
						}
						if( $year_baptism ){
							$update_old_data = array_merge( $update_old_data, array('year_baptism' => $year_baptism ));
						}
						if( $lrn ){
							$update_old_data = array_merge( $update_old_data, array('lrn' => $lrn ));
						}
						$update_student = $this->student_model->add($update_old_data);
						$this->delete( $student_get_id );
						//$student_details = $this->student_model->get( $student_id );

						// ── Backfill paymongo: user_id = existing students.id ─────────────
						$this->_backfill_paymongo($student_get_id, $student_id);
						// ──────────────────────────────────────────────────────────────────

						$student_details = $this->studentsession_model->getStudentsBySession( $session_id, null, null,$student_id );
						// $firstname = $student_details['firstname'];
						// $lastname = $student_details['lastname'];
						$session = $student_details['session'];
						$class_name = $student_details['class'];
						if( $success ){
							//$this->enrollmentsettings_model->insert_preenrollment_fee( $success,  $session_id );
							$enrollment_settings = $this->enrollmentsettings_model->getByClass( $session_id , $class_id );
							if( $enrollment_settings ){
								$this->enrollmentsettings_model->get_pre_enrollment( $success,  $enrollment_settings, $session_id );
							} else {
								/* if(!in_array( $class_id, $class_id_array, TRUE )){
									$class_id_array[] = $class_id;
									array_push( $notification_array1, '<div class="alert alert-warning">Enrollment Fee for <b>'.$class_name.'</b> is not yet set in Enrollment Settings for SY '.$session.'. Kindly contact the cashier.</div>');	
								} */
							}
							
							$this->session->set_flashdata("msg", '<div class="alert alert-success">Student <b>'.$firstname.'</b> <b>'.$lastname.'</b> ( '.$class_name.' ) enrolled for SY '.$session.' successfully.</div>');
							$this->session->set_flashdata("enrollment_msg", '<div class="alert alert-success">Student <b>'.$firstname.'</b> <b>'.$lastname.'</b> ( '.$class_name.' ) enrolled for SY '.$session.' successfully.</div>');
							//array_push( $notification_array , '<div class="alert alert-success">Student <b>'.$firstname.'</b> <b>'.$lastname.'</b> ( '.$class_name.' ) enrolled for SY '.$session.' successfully.</div>');	
							
							$this->db->select('users.*, students.guardian_name, students.guardian_name, students.guardian_midname, students.guardian_lastname, students.guardian_phone');
							$this->db->from('users');   
							$this->db->join('students', 'students.id = users.user_id'); 
							$this->db->where('users.user_id', $student_id);
							$this->db->where('users.role', 'parent');
							$query = $this->db->get();
							$student_details =  $query->row_array(); 
							 
							$username = $student_details['username']; 
							$password = $student_details['password'];     
							$sessionschoolname = $this->setting_model->getCurrentSchoolName();
							// $guardian_phone = "09451299241"; 
							// $guardian_phone = "09365320333"; 
							//$message2 = "You can browse your childs progress through: ".site_url().". Login: ".$username." Password: ".$password.". SMS is Autogenerated. Please do not reply.";
							//$message2 = "Check your childs school performance via Parent Portal app for Android and iOS devices to be available soon. SMS is Autogenerated. Please do not reply.";
							//$message1 = "Good Day Parent! ".$lastname.", ".$firstname." is now officially enrolled at ".$sessionschoolname.". SMS is Autogenerated. Please do not reply.";
							$school_code = "MMA";
							$firstname = strtolower( $firstname );
							$firstname = ucwords( $firstname );
							$lastname = strtolower( $lastname );
							$lastname = ucwords( $lastname );
							$message = "FROM ".$school_code.": Good day ".$firstname." ".$lastname.". Thank you for complying all enrollment requirements. You are now officially enrolled at ".$school_code.".-- Bridgette Generated SMS. Do Not Reply. --";
						

							$guardian_phone = str_replace('-', '', $guardian_phone);
							if (strlen($guardian_phone)>10) {
								$data_sms_parent1 = array(
									'notification_id' => 0,
									'sms_msg' => $message,
									'sms_number' => $guardian_phone, 
									'sms_status' => 'idle',
									'sms_teacher' => 'no',
									'sms_parent' => 'yes' 
								);   
								if( isset($data_sms_parent1) ){ 
									 $this->SMSM->add($data_sms_parent1);
								}   

							}

							$getStudentPaymentbySession = $this->reserved_payment_model->getStudentPaymentbySession( $student_id, $session_id );
							if( $getStudentPaymentbySession ){
								$get_payment_id = $getStudentPaymentbySession['id'];
								$update_payment = array( 'id'=>$get_payment_id, 'is_activated' => 'yes' );
								$this->reserved_payment_model->add( $update_payment );
							}
						}
					} else {
						$admission_no_code = $this->check_admission();
						$this->student_model->add(array('id' => $student_id, 'admission_no'=>$admission_no_code, 'admission_date' => date('Y-m-d'),'status' => 'active'));
						
						
						// check if student user is exist
						$get_student_account = $this->user_model->get_student_account( $student_id );
						if( empty($get_student_account) ){
							$user_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
							$data_student_login = array(
								'username' => $this->student_login_prefix . $student_id,
								'password' => $user_password,
								'user_id' => $student_id,
								'role' => 'student'
							);
							$this->user_model->add($data_student_login);
							if( $parent_id ){
								$user_details = $this->parent_model->getChildByParentID( $parent_id );
								$get_childs = $user_details['childs'];
								if( $get_childs ){
									$childs = $get_childs.','.$student_id;
								} else {
									$childs = $student_id;
								}
								
								$this->user_model->add(array('id'=>$parent_id,'childs'=>$childs));
							} else {
								$parent_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
								 $data_parent_login = array(
									'username' => $this->parent_login_prefix . $student_id,
									'password' => $parent_password,
									'user_id' => $student_id,
									'role' => 'parent',
									'childs' => $student_id
								);
								$ins_id = $this->user_model->add($data_parent_login);
							}
						}
			
	
						$this->delete( $student_get_id );
						//$student_details = $this->student_model->get( $student_id );

						// ── Backfill paymongo: user_id = students.id ──────────────────────
						$this->_backfill_paymongo($student_get_id, $student_id);
						// ─────────────────────────────────────────────────────────────────
						
						$student_details = $this->studentsession_model->getStudentsBySession( $session_id, null, null,$student_id );
						$firstname = $student_details['firstname'];
						$lastname = $student_details['lastname'];
						$session = $student_details['session'];
						$class_name = $student_details['class'];
						if( $success ){
							$enrollment_settings = $this->enrollmentsettings_model->getByClass( $session_id , $class_id );
							if( $enrollment_settings ){
								$this->enrollmentsettings_model->get_pre_enrollment( $success,  $enrollment_settings, $session_id );
							} else {
								/* if(!in_array( $class_id, $class_id_array, TRUE )){
									$class_id_array[] = $class_id;
									array_push( $notification_array1, '<div class="alert alert-warning">Enrollment Fee for <b>'.$class_name.'</b> is not yet set in Enrollment Settings for SY '.$session.'. Kindly contact the cashier.</div>');	
								} */
							}
							//$this->enrollmentsettings_model->get_pre_enrollment( $success,  $class_id, $session_id );
							//$this->enrollmentsettings_model->insert_preenrollment_fee( $success,  $session_id );
							//array_push( $notification_array, '<div class="alert alert-success">Student <b>'.$firstname.'</b> <b>'.$lastname.'</b> ( '.$class_name.' ) enrolled for SY '.$session.' successfully.</div>');	
							$this->session->set_flashdata("msg", '<div class="alert alert-success">Student <b>'.$firstname.'</b> <b>'.$lastname.'</b> ( '.$class_name.' ) enrolled for SY '.$session.' successfully.</div>');
							$this->session->set_flashdata("enrollment_msg", '<div class="alert alert-success">Student <b>'.$firstname.'</b> <b>'.$lastname.'</b> ( '.$class_name.' ) enrolled for SY '.$session.' successfully.</div>');
							if( $parent_id ){
								$user_details = $this->parent_model->getChildByParentID( $parent_id );
								$get_childs = $user_details['childs'];
								$split = explode(",",$get_childs);
								for( $x=0;$x<count( $split );$x++){
									$get_childs_id = $split[$x];
									$this->db->select('users.*, students.guardian_name, students.guardian_name, students.guardian_midname, students.guardian_lastname, students.guardian_phone');
									$this->db->from('users');   
									$this->db->join('students', 'students.id = users.user_id'); 
									$this->db->where('users.user_id', $get_childs_id);
									$this->db->where('users.role', 'parent');
									$query = $this->db->get();
									$student_details =  $query->row_array(); 
									if( $student_details ){
										break;
									}
								}
							} else {
								$this->db->select('users.*, students.guardian_name, students.guardian_name, students.guardian_midname, students.guardian_lastname, students.guardian_phone');
								$this->db->from('users');   
								$this->db->join('students', 'students.id = users.user_id'); 
								$this->db->where('users.user_id', $student_id);
								$this->db->where('users.role', 'parent');
								$query = $this->db->get();
								$student_details =  $query->row_array(); 
							}
					
							 
							$username = $student_details['username']; 
							$password = $student_details['password'];     
							$sessionschoolname = $this->setting_model->getCurrentSchoolName();
							// $guardian_phone = "09451299241"; 
							// $guardian_phone = "09365320333"; 
							//$message2 = "You can browse your childs progress through: ".site_url().". Login: ".$username." Password: ".$password.". SMS is Autogenerated. Please do not reply.";
							//$message2 = "Check your childs school performance via Parent Portal app for Android and iOS devices to be available soon. SMS is Autogenerated. Please do not reply.";
							//$message1 = "Good Day Parent! ".$lastname.", ".$firstname." is now officially enrolled at ".$sessionschoolname.". SMS is Autogenerated. Please do not reply.";
							$school_code = "MMA";
							$firstname = strtolower( $firstname );
							$firstname = ucwords( $firstname );
							$lastname = strtolower( $lastname );
							$lastname = ucwords( $lastname );
							$message = "FROM ".$school_code.": Good day ".$firstname." ".$lastname.". Thank you for complying all enrollment requirements. You are now officially enrolled at ".$school_code.".-- Bridgette Generated SMS. Do Not Reply. --";
						

							$guardian_phone = str_replace(' ', '', $guardian_phone);
							$guardian_phone = str_replace('-', '', $guardian_phone);
							if (strlen($guardian_phone)>10) {
								$data_sms_parent1 = array(
									'notification_id' => 0,
									'sms_msg' => $message,
									'sms_number' => $guardian_phone, 
									'sms_status' => 'idle',
									'sms_teacher' => 'no',
									'sms_parent' => 'yes' 
								);   
								if( isset($data_sms_parent1) ){ 
									 $this->SMSM->add($data_sms_parent1);
								}   

							}
							/* $guardian_phone = str_replace(' ', '', $guardian_phone);
							$guardian_phone = str_replace('-', '', $guardian_phone);
							if (strlen($guardian_phone)>10) {
								$data_sms_parent = array(
									'notification_id' => 0,
									'sms_msg' => $message2,
									'sms_number' => $guardian_phone, 
									'sms_status' => 'idle',
									'sms_teacher' => 'no',
									'sms_parent' => 'yes' 
								);  
			 
								if( isset($data_sms_parent) ){ 
									 $this->SMSM->add($data_sms_parent);
								}   

							} */
							// payment is activated
							$getStudentPaymentbySession = $this->reserved_payment_model->getStudentPaymentbySession( $student_id, $session_id );
							if( $getStudentPaymentbySession ){
								$get_payment_id = $getStudentPaymentbySession['id'];
								$update_payment = array( 'id'=>$get_payment_id, 'is_activated' => 'yes' );
								$this->reserved_payment_model->add( $update_payment );
							}
						}
					
						
					}
					
				}
				
			}
			/* if( count($notification_array1) > 0 ){
			$notification_array = array_merge( $notification_array1, $notification_array );
			}
			$this->session->set_flashdata('msg',$notification_array); */		
			
		}
		
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function getStrand(){
		$strand_id = $this->input->post('id');
		$strand_details = $this->strand_model->get( $strand_id );
		
		echo json_encode( $strand_details );
	}

	public function pay_on_site( $id ){
		if( $id ){
			$reserved_details = $this->reserved_students_model->get( $id );
			$firstname = $reserved_details['firstname'];
			$lastname = $reserved_details['lastname'];
			$firstname = strtolower( $firstname );
			$firstname = ucwords( $firstname );
			$lastname = strtolower( $lastname );
			$lastname = ucwords( $lastname );
			
			$this->reserved_students_model->add( array('id'=>$id,'process_type' => 'school' ) );
			$this->session->set_flashdata("enrollment_msg", "<div class=\"alert alert-success\"><b>".$firstname." ".$lastname."</b> updated to Pre-Enroll process.</div>");
		}

		redirect($_SERVER['HTTP_REFERER']);
		
	}

	public function getLatestStudentInformation(){
		$id = $this->input->post('id');
		$student_details = $this->student_model->getLatestStudentInformation( $id );

		echo json_decode($student_details);
	}

	public function update_modal(){
		$id = $this->input->post('display_rid');
		if( $id ){
			$display_first = $this->input->post('display_first');
			$display_middlename = $this->input->post('display_middlename');
			$display_lastname = $this->input->post('display_lastname');
			$display_suffix = $this->input->post('display_suffix');
			$display_guardiannumber = $this->input->post('display_guardiannumber');
			
			$display_religion = $this->input->post('display_religion');
			$display_baptized = $this->input->post('display_baptized');
			$display_year_baptized = $this->input->post
			('display_year_baptized');
			
			$display_email = $this->input->post('display_email');
			$display_lrn = $this->input->post('display_lrn');
			$display_guardianfirst = $this->input->post('display_guardianfirst');
			
			$display_guardianmiddlename = $this->input->post('display_guardianmiddlename');
			
			$display_guardianlastname = $this->input->post('display_guardianlastname');
			
			$display_relation = $this->input->post('display_relation');
			$display_address = $this->input->post('display_address');
			$display_institution = $this->input->post('display_institution');

			$existing_reserved = $this->reserved_students_model->get( $id );
			$existing_year = !empty($existing_reserved['year_baptism']) ? $existing_reserved['year_baptism'] : NULL;

			$year_baptism = $display_year_baptized;
			if ($display_baptized === 'no') {
				$year_baptism = $existing_year;
			} else {
				if ($year_baptism === 'N/A' || empty($year_baptism)) {
					$year_baptism = NULL;
				}
			}

			$data  = array(
				'id' => $id,
				'firstname' => $display_first,
				'middlename' => $display_middlename,
				'lastname' => $display_lastname,
				'suffix' => $display_suffix,
				'guardian_phone' => $display_guardiannumber,
				'religion' => $display_religion,
				'if_adventist' => $display_baptized,
				'year_baptism' => $year_baptism,
				'email_address' => $display_email === 'N/A' ? NULL : $display_email,
				'lrn' => $display_lrn === 'N/A' ? NULL : $display_lrn,
				'guardian_name' => $display_guardianfirst,
				'guardian_midname' => $display_guardianmiddlename,
				'guardian_lastname' => $display_guardianlastname,
				'guardian_relation' => $display_relation,
				'guardian_address' => $display_address,
				'institution' => $display_institution === 'N/A' ? NULL : $display_institution
			);

			$this->reserved_students_model->add( $data );
			$reserved_student = $this->reserved_students_model->get( $id );
			if ( !empty($reserved_student['student_id']) ) {
				$student_id = $reserved_student['student_id'];
				$student_data = array(
					'id' => $student_id,
					'firstname' => $display_first,
					'middlename' => $display_middlename,
					'lastname' => $display_lastname,
					'suffix' => $display_suffix,
					'guardian_name' => $display_guardianfirst,
					'guardian_midname' => $display_guardianmiddlename,
					'guardian_lastname' => $display_guardianlastname,
					'guardian_phone' => $display_guardiannumber,
					'guardian_relation' => $display_relation,
					'guardian_address' => $display_address,
					'religion' => $display_religion,
					'email' => $display_email === 'N/A' ? NULL : $display_email, 
					'lrn' => $display_lrn === 'N/A' ? NULL : $display_lrn,
					'if_adventist' => $display_baptized,
					'year_baptism' => $year_baptism,
					'institution' => $display_institution === 'N/A' ? NULL : $display_institution
				);
				$this->student_model->add( $student_data );
			}
			$this->session->set_flashdata("enrollment_msg", "<div class=\"alert alert-success\"><b>".$display_first." ".$display_lastname."</b> updated data successfully.</div>");

		}
	
		redirect($_SERVER['HTTP_REFERER']);		
	}

	     function paid() {
        $this->session->set_userdata('top_menu', 'reserved');
        $this->session->set_userdata('sub_menu', 'reserved/paidonline');
        $data['title'] = 'Student List';
        $setting_result = $this->setting_model->get();
        $sy_enrollment = $setting_result[0]['sy_enrollment']; 
      
        $session_id = $this->input->post('session_id');
		$session = $this->session_model->getAllSession();
		if(empty( $session_id )){
			$session_id = $setting_result[0]['session_id'];
		}

		$data['session_id'] = $session_id;
		$data['sessionlist'] = $session;
		$student_result = $this->reserved_payment_model->getStudentPaidbySession(null,$session_id);
		$data['studentlist'] = $student_result;
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/student/studentPaid', $data);
        $this->load->view('layout/registrar/footer', $data);
    }


     function promissory() {
        $this->session->set_userdata('top_menu', 'reserved');
        $this->session->set_userdata('sub_menu', 'reserved/promissory');
        $data['title'] = 'Student List';
        $setting_result = $this->setting_model->get();
        $sy_enrollment = $setting_result[0]['sy_enrollment']; 
      
        $session_id = $this->input->post('session_id');
		$session = $this->session_model->getAllSession();
		if(empty( $session_id )){
			$session_id = $setting_result[0]['session_id'];
		}

		$data['session_id'] = $session_id;
		$data['sessionlist'] = $session;
		$student_result =  $this->reserved_payment_model->getStudentPromissorybySession(null, $session_id);
		$data['studentlist'] = $student_result;
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/student/studentPromissory', $data);
        $this->load->view('layout/registrar/footer', $data);
    }
	//kench
	// public function generate_enrollment_form($student_id) {
	// // 	// Load mPDF library
	// 	$this->load->library('m_pdf');
		
	// 	// Get student information from database using the correct model
	// 	$student_info = $this->reserved_students_model->get($student_id);
		
	// 	// Get additional information if needed
	// 	$setting_result = $this->setting_model->get();
	// 	$session_id = $setting_result[0]['session_id'];
		
	// 	// Prepare data for PDF
	// 	$data['student'] = $student_info;
	// 	$data['session_id'] = $session_id;
		

	// 	$html = $this->load->view('registrar/student/enrollment_form_pdf', $data, true);
		
	// 	$this->m_pdf->pdf->AddPage('','Legal','10','10','10','10','5','10','10');

	// 	// Set PDF metadata
	// 	$this->m_pdf->pdf->SetTitle('Enrollment Form');
		
	// 	// Add content to PDF
	// 	$this->m_pdf->pdf->WriteHTML($html);
		
	// 	// Output PDF
	// 	$this->m_pdf->pdf->Output('Enrollment_Form_' . $student_id . '.pdf', 'I');
	// }

	public function generate_enrollment_form($student_id) {
		$this->load->library('m_pdf');
		$student_info = $this->reserved_students_model->get($student_id);
		$setting_result = $this->setting_model->get();
		$session_id = $setting_result[0]['session_id'];
		$data['student'] = $student_info;
		$data['session_id'] = $session_id;
		$html = $this->load->view('registrar/student/generate_enrollment_form', $data, true);
		$this->m_pdf->pdf = new mPDF('','Legal','0','','0','0','0','0','0','0','P');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFileName, "I");
	}

	public function generate_esc_form($student_id) {
		$this->load->library('m_pdf');
		$student_info = $this->reserved_students_model->get($student_id);
		$setting_result = $this->setting_model->get();
		$session_id = $setting_result[0]['session_id'];
		$data['student'] = $student_info;
		$data['session_id'] = $session_id;
		$html = $this->load->view('registrar/student/generate_esc_form', $data, true);
		$this->m_pdf->pdf = new mPDF('','A4','0','','0','0','0','0','0','0','P');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFileName, "I");
	}
	

}

?>