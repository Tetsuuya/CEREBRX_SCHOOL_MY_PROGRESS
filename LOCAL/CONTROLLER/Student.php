<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Student extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('smsgateway');
        $this->load->helper('file');
        $this->lang->load('message', 'english');
        $this->role;
        $this->load->library('auth');
		$this->role;
        $this->load->library('auth');
        $this->auth->is_logged_in_cafeteria_staff();
		$this->load->model('studentload_model'); 
		$this->load->model('menusettings_model');
		$this->load->model('order_model');
    }


    function getByClassAndSection() {
        $class = $this->input->get('class_id');
        $section = $this->input->get('section_id');
        $resultlist = $this->student_model->searchByClassSection($class, $section,"students.lastname");
        echo json_encode($resultlist);
    }

    public function getsearchstudent(){
		$session_details = $this->menusettings_model->get();
		$session_id = $session_details[0]['school_year'];
        $search_student = $this->input->post('search_student'); 
        $resultlist = $this->student_model->searchFullText($search_student,'yes', $session_id );
        // echo  $resultlist ;   
        echo json_encode($resultlist);  
        # code...
    }
	
	public function getsearchstudentnotallow(){
        $session_id = $this->input->post('session_id');
		if(empty($session_id)){
			$session_details = $this->menusettings_model->get();
			$session_id = $session_details[0]['school_year'];
		}
        $search_student = $this->input->post('search_student'); 
        $resultlist = $this->student_model->searchFullTextCheckAllow($search_student,'yes','no', $session_id );
        // echo  $resultlist ;   
        echo json_encode($resultlist);  
        # code...
    }
	
	public function getsearchstudentallow(){
		$session_details = $this->menusettings_model->get();
		$session_id = $session_details[0]['school_year'];
        $search_student = $this->input->post('search_student'); 
        $resultlist = $this->student_model->searchFullTextCheckAllow($search_student,'yes','yes', $session_id );
        // echo  $resultlist ;   
        echo json_encode($resultlist);  
        # code...
    }
	
	// FEATURE: Search for UNREGISTERED students (allow = 'no')
	// This method is used by the new unregistered student search panel
	// Returns list of students who are NOT registered in the cafeteria system
	// Added: [Your Date]
	public function search_unregistered_students(){
		// Get school year from AJAX POST request first, fallback to menu settings if empty
		$session_id = $this->input->post('session_id');
		if(empty($session_id)){
			$session_details = $this->menusettings_model->get();
			$session_id = $session_details[0]['school_year'];
		}
		
		// Get search term from POST request
		$search_student = $this->input->post('search_student'); 
		
		// Search for students with allow = 'no' (unregistered)
		// Parameters: search_term, is_active, allow_status, session_id
		$resultlist = $this->student_model->searchFullTextCheckAllow($search_student, 'yes', 'no', $session_id);
		
		// Return results as JSON for AJAX handling
		echo json_encode($resultlist);  
	}
	
	 public function getstd(){
        $session_id = $this->input->post('session_id'); 
        $search_student = $this->input->post('search_student'); 
		if(empty($session_id)){
			$session_details = $this->menusettings_model->get();
			$session_id = $session_details[0]['school_year'];
		}
		if( $search_student ){
			$resultlist = $this->student_model->getBySession($search_student, $session_id );
		}
        // echo  $resultlist ;   
        echo json_encode($resultlist);  
        # code...
    }
	
	public function getavailableload(){
        $session_id = $this->input->post('session_id'); 
        $student_id = $this->input->post('search_student'); 
		// $get_total_spent_list = $this->order_model->get_total_spent_list( $student_id, $session_id );
		// $get_total_spent = number_format($get_total_spent_list['get_total_amount'],2,".","," );
		// $get_total_load = number_format($get_total_spent_list['get_total_load'],2,".","," );
		// $balance = $get_total_load - $get_total_spent;
		// $balance =number_format($balance,2,".","," );
		// if( $balance > 0 ){
		// 	$resultlist['load_balance'] = $balance;
		// } else {
		// 	$resultlist['load_balance'] = 0;
		// }
        // echo  $resultlist ;   

		// Get unformatted values
		$get_total_load_details = $this->studentload_model->get_total_load($student_id, $session_id);
		$get_total_load = isset($get_total_load_details['total_amount']) ? (float)$get_total_load_details['total_amount'] : 0;

		$get_total_spent_details = $this->order_model->get_total_spent($student_id, $session_id);
		$get_total_spent = isset($get_total_spent_details['total_amount']) ? (float)$get_total_spent_details['total_amount'] : 0;

		// Compute balance
		$balance = $get_total_load - $get_total_spent;

		// Prepare result (formatted if needed)
		$resultlist['load_balance'] = $balance > 0 ? number_format($balance, 2, ".", ",") : "0.00";

        echo json_encode($resultlist);  
        # code...
    }

	public function auto_load() {
		$session_id = $this->input->post('sessionDropdown');
		$remarks = $this->input->post('remarks');
		$created_at = date('Y-m-d H:i:s');
		$sessionData = $this->customlib->getLoggedInUserData(); 
		$cafeteria_id = $sessionData['cafeteria_id'];
		$role = 'cafeteria';
	
		// Begin get session_id
		if (empty($session_id)) {
			$session_id = $this->input->get('session_id');
		}
		$session = $this->session_model->getAllSession();
		$session_details = $this->menusettings_model->get();
		$current_session_id = $session_details[0]['school_year'];
	
		if (empty($session_id)) {
			$session_id = $current_session_id;
		}
		// End get session_id
	
		// Log auto load
		$data = array(
			'session_id' => $session_id,
			'remarks' => $remarks
		);
		$this->db->insert('auto_load', $data);

	
		// Get all student's meal plan
		$this->db->select('students.id as id, student_session.id as student_session_id, students.id,students.gender,students.meal_plan,students.load_balance, students.lastname, students.suffix, students.firstname, students.middlename');
		$this->db->from('students');
		$this->db->join('student_session', 'student_session.student_id = students.id');
		$this->db->where('student_session.session_id', $session_id);
		$this->db->where('student_session.allow', 'yes');
		$this->db->order_by('students.lastname', 'asc');
		$this->db->order_by('students.firstname', 'asc');
		$students = $this->db->get()->result();
	
		foreach ($students as $student) {
			if ($student->gender == "Male") {
				$student->load = 3200;
			} else {
				$student->load = 3000;
			}
	
			 if ($student->meal_plan == "subsidized_1") {
				$student->load = 3500;
			} elseif ($student->meal_plan == "subsidized_2") {
				$student->load = 4000;
			} elseif ($student->meal_plan == "subsidized_3") {
				$student->load = 4500;
			}
	
			$array = array(
				'student_id' => $student->id,
				'current_balance' => $student->load_balance,
				'amount' => $student->load,
				'remarks' => $remarks,
				'date_load' => $created_at, 
				'user_id' => $cafeteria_id,
				'user_role' => $role,
				'session_id' => $session_id,
				'is_deleted' => 0
			);
			$id = $this->studentload_model->add($array);
		}
	
		$this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Successfuly loaded all student meal plans.</div>');
		redirect('cafeteria/student/register_student');
	}
	
	public function import_smp_excel() {
		$students = $this->input->post('jsonData');
		$students = json_decode($students);
		array_shift($students);
	
		$session_details = $this->menusettings_model->get();
		$current_session_id = $session_details[0]['school_year'];
	
		$this->db->select('students.id as id, student_session.id as student_session_id, students.id,students.gender,students.meal_plan, students.lastname, students.suffix, students.firstname, students.middlename, students.admission_no');
		$this->db->from('students');
		$this->db->join('student_session', 'student_session.student_id = students.id');
		$this->db->where('student_session.session_id', $current_session_id);
		$this->db->where('student_session.allow', 'yes');
		$this->db->order_by('students.lastname', 'asc');
		$this->db->order_by('students.firstname', 'asc');
		$allowed_students = $this->db->get()->result();
	
		foreach ($students as $student) {
			$ac = $student[0];
			$ln = $student[1];
			$fn = $student[2];
			$sn = $student[3];
			$mp = $student[4];
			$isFound = false;
	
			foreach ($allowed_students as $astudent) {
				if ($astudent->admission_no == $ac) {
					$this->db->where('id', $astudent->id);
					$this->db->update('students', array('meal_plan' => $mp));
					$isFound = true;
				}
			}
	
			if (!$isFound) {
				$this->db->select('students.*');
				$this->db->from('students');
				$this->db->join('student_chart_of_accounts', 'student_chart_of_accounts.student_id = students.id');
				$this->db->where('student_chart_of_accounts.account_number', $ac);
				$query = $this->db->get();
				$row = $query->row_array();
	
				if ($row) {
					$conditions2 = array(
						'session_id' => $current_session_id,
						'student_id' => $row['id']
					);
					$query2 = $this->db->get_where('student_session', $conditions2);
					$row2 = $query2->row_array();
	
					$this->db->where('id', $row['id']);
					$this->db->update('students', array('meal_plan' => $mp));
	
					if ($row2) {
						$this->db->where('id', $row2['id']);
						$this->db->update('student_session', array('allow' => 'yes'));
					}
				}
			}
		}
	
		$this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Successfuly updated student meal plans.</div>');
		redirect('cafeteria/student/register_student');
		if ($this->input->post('student_ids')) {
			$student_ids = json_decode($this->input->post('student_ids'));
			$meal_plans = json_decode($this->input->post('meal_plans'));
			$session_id = $this->input->post('session_id');
			
			if (!empty($student_ids)) {
				$success_count = 0;
				$error_count = 0;
				$update_array = array();
				
				foreach ($student_ids as $index => $student_id) {
					try {
						$get_details = $this->student_model->getBySession($student_id, $session_id);
						$student_session_id = $get_details['student_session_id'];
						
						if ($student_session_id) {
							// Update student_session table
							$update_array[] = array(
								'id' => $student_session_id,
								'allow' => 'yes'
							);
							
							// Update student's meal plan
							$this->db->where('id', $student_id);
							$this->db->update('students', array(
								'meal_plan' => isset($meal_plans[$index]) ? $meal_plans[$index] : 'cafeteria'
							));
							
							$success_count++;
						} else {
							$error_count++;
						}
					} catch(Exception $e) {
						$error_count++;
						log_message('error', 'Error registering student ID ' . $student_id . ': ' . $e->getMessage());
					}
				}
				
				// Batch update for better performance
				if (!empty($update_array)) {
					$this->db->update_batch('student_session', $update_array, 'id');
				}
				
				$response = array(
					'status' => 'success',
					'success_count' => $success_count,
					'error_count' => $error_count,
					'message' => "Successfully registered $success_count student(s)" . 
								($error_count > 0 ? ". Failed to register $error_count student(s)" : "")
				);
				
				echo json_encode($response);
				return;
			}
		}
	}
	
	public function fetch_all_data() {
		$session_id = $this->input->post('session_id');
		$search = $this->input->post('search');
	
		if (empty($session_id)) {
			$session_id = $this->input->get('session_id');
		}
	
		$session = $this->session_model->getAllSession();
		$session_details = $this->menusettings_model->get();
		$current_session_id = $session_details[0]['school_year'];
	
		if (empty($session_id)) {
			$session_id = $current_session_id;
		}
	
		$this->db->select('students.id as id,student_session.id as student_session_id,classes.id AS class_id,classes.class,sections.id AS section_id,sections.section,students.id,students.admission_no ,allow,students.lastname,students.firstname,students.middlename');
		$this->db->from('students');
		$this->db->join('student_session', 'student_session.student_id = students.id');
		$this->db->join('classes', 'student_session.class_id = classes.id');
		$this->db->join('sections', 'sections.id = student_session.section_id','left');
		$this->db->where('student_session.session_id', $session_id);
		$this->db->where('student_session.allow', 'yes');
		$this->db->order_by('students.lastname', 'asc');
		$this->db->order_by('students.firstname', 'asc');
		$query = $this->db->get()->result();
	
		echo json_encode(["data" => $query]);
	}
	
	
	public function register_student() {
		$data['title'] = 'Student Details';
		$per_page = $this->input->get('per_page');
		$session_id = $this->input->post('session_id');
		$search = $this->input->post('search');
		
		if(empty($session_id)){
			$session_id = $this->input->get('session_id');
		}
		
		$session = $this->session_model->getAllSession();
		$session_details = $this->menusettings_model->get();
		$current_session_id = $session_details[0]['school_year'];
		
		if(empty($session_id)){
			$session_id = $current_session_id;
		}
		
		$class_id = $this->input->post('get_class_id');
		$section_id = $this->input->post('get_section_id');
		$get_category = $this->input->post('get_category_id');
		
		$data['per_page'] = $per_page;
		$data['session_id'] = $session_id;
		$data['sessionlist'] = $session;
		$data['search'] = $search;
		
		$studentlist = $this->order_model->getallowstudents_pagination($session_id, $current_session_id, $per_page, $search);
		$data['studentlist'] = $studentlist['records'];
		$data['studentlist_pagination'] = $studentlist['pagination'];
		$data['classlist'] = $this->class_model->get();
		$data['class_id'] = $class_id;
		$data['section_id'] = $section_id;
		$data['get_category'] = $get_category;
		
		if($class_id && $section_id){
			$data['studentlistmale'] = $this->student_model->searchByClassSectionGenderAllow($class_id, $section_id, 'Male', $get_category, $session_id);
			$data['studentlistfemale'] = $this->student_model->searchByClassSectionGenderAllow($class_id, $section_id, 'Female', $get_category, $session_id);
		}

		// Get allowed students for the datalist
		$this->db->select('students.id, student_session.id as student_session_id, students.gender, students.meal_plan, students.lastname, students.suffix, students.firstname, students.middlename');
		$this->db->from('students');
		$this->db->join('student_session', 'student_session.student_id = students.id');
		$this->db->where('student_session.session_id', $session_id);
		$this->db->where('student_session.allow', 'yes');
		$this->db->order_by('students.lastname', 'asc');
		$this->db->order_by('students.firstname', 'asc');
		$allowed_students = $this->db->get()->result();
		$data['allowed_students'] = $allowed_students;

		// Updated validation rule for array of student IDs
		$this->form_validation->set_rules('student_id[]', 'Students', 'required', array('required' => 'Please select at least one student to register.'));
		
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('layout/cafeteria/header', $data);
			$this->load->view('cafeteria/student/register', $data);
			$this->load->view('layout/cafeteria/footer', $data);
		} else {
			$student_ids = $this->input->post('student_id');
			$student_meal_plans = $this->input->post('student_meal_plan');
			$session_id = $this->input->post('session_id');
			
			if(!empty($student_ids) && is_array($student_ids)) {
				$success_count = 0;
				$error_count = 0;
				$update_array = array();
				
				foreach($student_ids as $student_id) {
					if(!empty($student_id)) {
						try {
							$get_details = $this->student_model->getBySession($student_id, $session_id);
							$student_session_id = $get_details['student_session_id'];
							
							if($student_session_id) {
								$meal_plan = isset($student_meal_plans[$student_id]) ? $student_meal_plans[$student_id] : 'cafeteria';
								
								$update_array[] = array(
									'id' => $student_session_id,
									'allow' => 'yes',
								);
								
								// Update student's meal plan in students table
								$this->db->where('id', $student_id);
								$this->db->update('students', array('meal_plan' => $meal_plan));
								
								$success_count++;
							} else {
								$error_count++;
							}
						} catch(Exception $e) {
							$error_count++;
							log_message('error', 'Error registering student ID ' . $student_id . ': ' . $e->getMessage());
						}
					}
				}
				
				// Batch update for better performance
				if(!empty($update_array)) {
					$this->db->update_batch('student_session', $update_array, 'id');
				}
				
				// Set appropriate flash message
				if($success_count > 0 && $error_count == 0) {
					$this->session->set_flashdata('msg', '<div class="alert alert-success text-left"><i class="fa fa-check-circle"></i> Successfully registered ' . $success_count . ' student(s).</div>');
				} elseif($success_count > 0 && $error_count > 0) {
					$this->session->set_flashdata('msg', '<div class="alert alert-warning text-left"><i class="fa fa-exclamation-triangle"></i> Successfully registered ' . $success_count . ' student(s). ' . $error_count . ' student(s) could not be registered.</div>');
				} else {
					$this->session->set_flashdata('msg', '<div class="alert alert-danger text-left"><i class="fa fa-times-circle"></i> No students could be registered. Please try again.</div>');
				}
			} else {
				$this->session->set_flashdata('msg', '<div class="alert alert-danger text-left"><i class="fa fa-times-circle"></i> No students selected for registration.</div>');
			}
			
			redirect('cafeteria/student/register_student/?session_id=' . $session_id);
		}
	}

	// Add this new method to handle AJAX requests for student list
	public function get_students_ajax() {
		$session_id = $this->input->post('session_id');
		
		if(!$session_id) {
			echo json_encode([]);
			return;
		}
		
		$this->db->select('students.id, students.firstname, students.middlename, students.lastname, students.suffix, students.gender, students.meal_plan, classes.class, sections.section');
		$this->db->from('students');
		$this->db->join('student_session', 'student_session.student_id = students.id');
		$this->db->join('classes', 'classes.id = student_session.class_id');
		$this->db->join('sections', 'sections.id = student_session.section_id');

		$this->db->where('student_session.session_id', $session_id);
		$this->db->where('student_session.allow', 'no'); // Only show students not yet registered
		$this->db->order_by('students.lastname', 'asc');
		$this->db->order_by('students.firstname', 'asc');
		
		$students = $this->db->get()->result_array();
		// Format student names
		$formatted_students = array();
		foreach($students as $student) {
			$full_name = trim($student['lastname'] . ', ' . $student['firstname']);
			if(!empty($student['middlename'])) {
				$full_name .= ' ' . $student['middlename'];
			}
			if(!empty($student['suffix'])) {
				$full_name .= ' ' . $student['suffix'];
			}
			
			$formatted_students[] = array(
				'id' => $student['id'],
				'full_name' => $full_name,
				'gender' => $student['gender'],
				'meal_plan' => $student['meal_plan'],
				'class' => $student['class'],
				'section' => $student['section']
			);
		}
		
		echo json_encode($formatted_students);
	}

	public function register_by_student() {
		$data['title'] = 'Student Details';
		$per_page = $this->input->get('per_page');
		$session_id = $this->input->post('session_id');
		$search = $this->input->post('search');
	
		if (empty($session_id)) {
			$session_id = $this->input->get('session_id');
		}
	
		$session = $this->session_model->getAllSession();
		$session_details = $this->menusettings_model->get();
		$current_session_id = $session_details[0]['school_year'];
	
		if (empty($session_id)) {
			$session_id = $current_session_id;
		}
	
		$data['per_page'] = $per_page;
		$data['session_id'] = $session_id;
		$data['sessionlist'] = $session;
		$data['search'] = $search;
	
		$studentlist = $this->order_model->getallowstudents_pagination($session_id, $current_session_id, $per_page, $search);
		$data['studentlist'] = $studentlist['records'];
		$data['studentlist_pagination'] = $studentlist['pagination'];
	
		$this->form_validation->set_rules('student_id', 'Student', 'trim|required|xss_clean');
	
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('layout/cafeteria/header', $data);
			$this->load->view('cafeteria/student/register', $data);
			$this->load->view('layout/cafeteria/footer', $data);
		} else {
			$student_id = $this->input->post('student_id');
			$session_id = $this->input->post('session_id');
	
			if ($student_id) {
				$get_details = $this->student_model->getBySession($student_id, $session_id);
				$student_session_id = $get_details['student_session_id'];
	
				if ($student_session_id) {
					$this->studentsession_model->add(['id' => $student_session_id, 'allow' => 'yes', 'is_deactivate' => 'no']);
				}
	
				$this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Student successfully registered.</div>');
			}
	
			redirect('cafeteria/student/register_student/?session_id=' . $session_id);
		}
	}
	
	
	public function unregister( $id ) {
			$session_id = $this->input->get('session_id');
		if( $id ){
			$this->studentsession_model->add( array('id'=>$id, 'allow' => 'no') );
			$this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Student successfuly removed.</div>');
		}
		
		redirect('cafeteria/student/register_student/?session_id='.$session_id );
    }

	public function deactivate($id) {
		$session_id = $this->input->get('session_id');
		if ($id) {
			$this->studentsession_model->add(array('id' => $id, 'is_deactivate' => 'yes'));
			$this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Student successfully deactivated.</div>');
		}

		redirect('cafeteria/student/register_student/?session_id=' . $session_id);
	}

	public function activate($id) {
		$session_id = $this->input->get('session_id');
		if ($id) {
			$this->studentsession_model->add(array('id' => $id, 'is_deactivate' => 'no'));
			$this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Student successfully activated.</div>');
		}

		redirect('cafeteria/student/register_student/?session_id=' . $session_id);
	}


	

    public function register_batch(){
		$student_id = $this->input->post('student_id');
		$session_id = $this->input->post('session_id');
		if( count( $student_id ) > 0 ){
			$update_array = array();
			for ($x=0; $x<count( $student_id);$x++) {
				$get_student_id = $student_id[$x];
				if( $get_student_id ){
					$get_details = $this->student_model->getBySession( $get_student_id, $session_id );
					$student_session_id = $get_details['student_session_id'];
					if( $student_session_id ){
						$update_array[] = array(
							'id' => $student_session_id,
							'allow' => 'yes',
						);
					}
				}
			}
			if( !empty($update_array) ){
				$this->db->update_batch('student_session',$update_array,'id');
			}
		} 
			
    	redirect('cafeteria/student/register_student/?session_id='.$session_id);
    }

     public function unregister_batch(){
		$student_id = $this->input->post('student_id');
		$session_id = $this->input->post('session_id');
		if( count( $student_id ) > 0 ){
			$update_array = array();
			for ($x=0; $x<count( $student_id);$x++) {
				$get_student_id = $student_id[$x];
				if( $get_student_id ){
					$get_details = $this->student_model->getBySession( $get_student_id, $session_id );
					$student_session_id = $get_details['student_session_id'];
					if( $student_session_id ){
						$update_array[] = array(
							'id' => $student_session_id,
							'allow' => 'no',
						);
					}
				}
			}
			if( !empty($update_array) ){
				$this->db->update_batch('student_session',$update_array,'id');
			}
		} 
			
    	redirect('cafeteria/student/register_student/?session_id='.$session_id);
    }
	
	public function save_load(){
		 $created_at  = date('Y-m-d H:i:s');	
		 $sessionData = $this->customlib->getLoggedInUserData(); 
		 $cafeteria_id = $sessionData['cafeteria_id'];
		//  printx($sessionData);
		 $role = 'cafeteria';
		 $get_student_id = $this->input->post('get_student_id');
		 $load_amount = $this->input->post('load_amount');
		 $load_session_id = $this->input->post('load_session_id');
		 $remarks = $this->input->post('remarks');
		 $per_page = $this->input->post('per_page');
		if( $get_student_id ){
			$student_details = $this->student_model->get( $get_student_id );
			$load_balance = $student_details['load_balance'];
			$array = array(
				'student_id' => $get_student_id,
				'current_balance' => $load_balance,
				'amount' => $load_amount,
				'remarks' => $remarks,
				'date_load' => $created_at,
				'user_id' => $cafeteria_id,
				'user_role' => $role,
				'session_id' => $load_session_id,
				'is_deleted' => 0
			);
			
			$id = $this->studentload_model->add( $array );
			if( $id ){
				$this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Student load added successfully.</div>');
			}
		}
		
		  redirect( $_SERVER['HTTP_REFERER'] );	
	}
	
	public function load(){
		$data['title'] = 'Student Details';
        $data['studentlist'] = $this->student_model->getallowstudents();
		$this->form_validation->set_rules('student_id', 'Student', 'trim|required|xss_clean');
       
	    $this->load->view('layout/cafeteria/header', $data);
		$this->load->view('cafeteria/student/load', $data);
		$this->load->view('layout/cafeteria/footer', $data);
		
	}
	// added meal plan function
	public function update_meal_plan() {
		$student_id = $this->input->post('student_id');
		$meal_plan = $this->input->post('meal_plan');
		 
		$result = $this->student_model->update_meal_plan($student_id, $meal_plan);
		
		if($result) {
			echo json_encode(['status' => 'success']);
		} else {
			echo json_encode(['status' => 'error']);
		}
	}
	
	
	function load_history(){
		$this->session->set_userdata('top_menu', 'Orders');
        $this->session->set_userdata('sub_menu', 'orders/index');
		$daterange="";
		$data['title'] = 'Current School Year Load Transaction';
		$session_details = $this->menusettings_model->get();
		$session_id = $session_details[0]['school_year'];
		$get_order_total = $this->studentload_model->get_load_total( $session_id );
		$data['dailyamount'] = $get_order_total['dailyamount'];
		$data['monthlyamount'] = $get_order_total['monthlyamount'];
		$data['yearlyamount'] = $get_order_total['yearlyamount'];			
		$data['totalamount'] = $get_order_total['totalamount'];					
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$daterange = $this->input->post('data_range');
			$daterangepre = substr($daterange,0,10);
			$daterangepos = substr($daterange,14,24);
			$rangepre = str_replace('/', '-', $daterangepre);
			$rangepos = str_replace('/', '-', $daterangepos);
			$data['rangepre']=$daterangepre;
			$data['rangepos']=$daterangepos;
			$data['is_range']=true;
			$data['orderlist'] = $this->studentload_model->get_range( $daterangepre, $daterangepos, $session_id ); 
			$this->load->view('layout/cafeteria/header', $data);
			$this->load->view('cafeteria/student/load_history', $data);
			$this->load->view('layout/cafeteria/footer', $data);
		}
		else {	
			$daterange = date('m/d/Y')."-". date('m/d/Y');
			$daterangepre = substr($daterange,0,10);
			$daterangepos = substr($daterange,0,10);
			/* $rangepre = str_replace('/', '-', $daterangepre);
			$rangepos = str_replace('/', '-', $daterangepos); */
			$data['rangepre']= $daterangepre;
			$data['rangepos']= $daterangepos;
			/* $daterangepre = date('m/d/Y');
			$daterangepos = date('m/d/Y'); */
			$data['orderlist'] = $this->studentload_model->get_range( $daterangepre, $daterangepos, $session_id ); 
			$data['is_range']=false;
			$this->load->view('layout/cafeteria/header', $data);
			$this->load->view('cafeteria/student/load_history', $data);
			$this->load->view('layout/cafeteria/footer', $data);
		}
	}
	
	function allload_transaction(){
		$this->session->set_userdata('top_menu', 'Orders');
        $this->session->set_userdata('sub_menu', 'orders/index');
		$data['title'] = 'View Load Transaction Per School Year ';
		$session_id = $this->input->get('session_id');
		if(empty( $session_id )){
			$session_details = $this->menusettings_model->get();
			$session_id = $session_details[0]['school_year'];
		}
		$get_order_total = $this->studentload_model->get_load_total( $session_id );
		$data['totalall'] = $get_order_total['totalall'];			
		$data['totalamount'] = $get_order_total['totalamount'];		
		$data['orderlist'] = $this->studentload_model->get_load_by_sy(); 		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->load->view('layout/cafeteria/header', $data);
			$this->load->view('cafeteria/student/loadTotal', $data);
			$this->load->view('layout/cafeteria/footer', $data);
		} else {	
			$data['orderlistmonthly'] = $this->studentload_model->get_load_monthly( $session_id );
			$session_details = $this->session_model->get( $session_id );
			$get_session_name = $session_details['session'];
			$data['session_selected'] = $this->studentload_model->get_load_monthly( $session_id ); 	
			$data['get_session_name'] = $get_session_name; 	
			$data['getMonthList'] = $this->customlib->getMonthList(); 	
			$this->load->view('layout/cafeteria/header', $data);
			$this->load->view('cafeteria/student/loadTotal', $data);
			$this->load->view('layout/cafeteria/footer', $data);
		}
	}
	
	
}
?>