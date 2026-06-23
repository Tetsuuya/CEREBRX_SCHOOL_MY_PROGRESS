<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Teacher extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->lang->load('message', 'english');
        $this->role;
        $this->load->library('auth');
        $this->auth->is_logged_in_idproduction();
		$this->load->model('user_model');
    }
	
	function index() {
        $this->session->set_userdata('top_menu', 'Teachers');
        $this->session->set_userdata('sub_menu', 'teacher/index');
        $data['title'] = 'Add Teacher';
        $teacher_result = $this->teacher_model->get();
        $data['teacherlist'] = $teacher_result;
        $genderList = $this->customlib->getGender();
        $data['genderList'] = $genderList;
        $class = $this->classsection_model->get();
        $data['classlist'] = $class;
        $this->load->view('layout/idproduction/header', $data);
        $this->load->view('idproduction/teacher/teacherList', $data);
        $this->load->view('layout/idproduction/footer', $data);
    }
	
	function create() {
        $this->session->set_userdata('top_menu', 'Teachers');
        $this->session->set_userdata('sub_menu', 'teacher/index');
        $data['title'] = 'Add teacher';
        $genderList = $this->customlib->getGender();
        $data['genderList'] = $genderList;
        $this->form_validation->set_rules('name', 'Teacher Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('lastname', 'Teacher Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean');
        $this->form_validation->set_rules('dob', 'Date of Birth', 'trim|required|xss_clean');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', 'Image', 'callback_handle_upload');
        if ($this->form_validation->run() == FALSE) {
            $teacher_result = $this->teacher_model->get();
            $data['teacherlist'] = $teacher_result;
            $genderList = $this->customlib->getGender();
            $data['genderList'] = $genderList;
            $class = $this->classsection_model->get();
            $data['classlist'] = $class;
           $this->load->view('layout/idproduction/header', $data);
            $this->load->view('idproduction/teacher/teacherCreate', $data);
            $this->load->view('layout/idproduction/footer', $data);
        } else {
			$type_teacher =  $this->input->post('type_teacher');
            $data = array(
                'name' => $this->input->post('name'),
                'middlename' => $this->input->post('middlename'),
                'lastname' => $this->input->post('lastname'),
                'email' => $this->input->post('email'),
                'password' => $this->input->post('password'),
                'sex' => $this->input->post('gender'),
                'dob' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('dob'))),
                'address' => $this->input->post('address'),
                'phone' => $this->input->post('phone'),
                'is_active' => 'yes',
                'type_teacher' => $type_teacher,
                'image' => 'uploads/student_images/no_image.png',
            );
			
            $insert_id = $this->teacher_model->add($data);
			/* if( $type_teacher == 'Advisory'){
				$class_id = $this->input->post('class_id');
				if( count($class_id) > 0 ){
					$this->classsection_model->blank_teacherid( $insert_id );
					for($x=0;$x<count($class_id);$x++ ){
						$get_class_id = $class_id[$x];
						$data = array(
							'id' => $get_class_id,
							'id' => $get_class_id,
							'teacher_id' => $insert_id 
						);
						$this->classsection_model->assign_advisory( $data );
					}
				}
			} else {
				$this->classsection_model->blank_teacherid( $insert_id );	
			} */
            $user_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
            $data_student_login = array(
                'username' => $this->teacher_login_prefix . $insert_id,
                'password' => $user_password,
                'user_id' => $insert_id,
                'role' => 'teacher'
            );
            $this->user_model->add($data_student_login);
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/teacher_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'image' => 'uploads/teacher_images/' . $img_name);
                $this->teacher_model->add($data_img);
            }
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Teacher added successfully</div>');
            redirect('idproduction/teacher/index');
        }
	}
	
	function view($id) {
        $this->session->set_userdata('top_menu', 'Teachers');
        $this->session->set_userdata('sub_menu', 'teacher/index');
        $data['title'] = 'Teacher List';
        $teacher = $this->teacher_model->get($id);
        $teachersubject = $this->teachersubject_model->getTeacherSubjects($id);
        $data['teacher'] = $teacher;
        $data['teachersubject'] = $teachersubject;
        $type_teacher = $teacher['type_teacher']; 
        if( $type_teacher == "Advisory"){
            /* $class = $this->classsection_model->get($teacher['class_id']);
            $data['class_advisory'] = isset($class['class'])?$class['class']:'';
            $section = $this->section_model->get($teacher['section_id']);
            $data['section_advisory'] = isset($class['section'])?$class['section']:''; */
			$get_advisory = $this->classsection_model->get_advisory( $id );
			if( $get_advisory ){
				$get_class_advisory = '';
				for( $x=0; $x<count( $get_advisory );$x++){
					$get_id = $get_advisory[$x];
					if( $get_id ){
						$class_section = $this->classsection_model->getByClassSectionID( $get_id );
						$class_name = $class_section['class_name'];
						$section_name = $class_section['section'];
						$get_class_advisory .= $class_name.' '.$section_name.'<br/>';
					}
				}
				$data['class_advisory'] = $get_class_advisory;
			} else {
				$data['class_advisory'] = 'None';
			}
        } else {
            $data['class_advisory'] = 'None';
        } 
         
        $this->load->view('layout/idproduction/header', $data);
        $this->load->view('idproduction/teacher/teacherShow', $data);
        $this->load->view('layout/idproduction/footer', $data);
    }

	
	 function edit($id) {
        $this->session->set_userdata('top_menu', 'Teachers');
        $this->session->set_userdata('sub_menu', 'teacher/index');
        $data['title'] = 'Edit Teacher';
        $data['id'] = $id;
        $genderList = $this->customlib->getGender();
        $data['genderList'] = $genderList;
        $teacher = $this->teacher_model->get($id);
        $data['teacher'] = $teacher;
        $class = $this->classsection_model->get();
        $data['classlist'] = $class;
		$get_advisory = $this->classsection_model->get_advisory($id);
		$data['get_advisory'] = $get_advisory;
	
        $this->form_validation->set_rules('name', 'Teacher Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('lastname', 'Teacher Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean');
        $this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean');
        $this->form_validation->set_rules('dob', 'Date of Birth', 'trim|required|xss_clean');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', 'Image', 'callback_handle_upload');
        if ($this->form_validation->run() == FALSE) {
            $teacher_result = $this->teacher_model->get();
            $data['teacherlist'] = $teacher_result;
            $this->load->view('layout/idproduction/header', $data);
            $this->load->view('idproduction/teacher/teacherEdit', $data);
            $this->load->view('layout/idproduction/footer', $data);
        } else {
			$type_teacher = $this->input->post('type_teacher');
			$class_id = $this->input->post('class_id');
			$is_active = $this->input->post('is_active');
            $data = array(
                'id' => $id,
                'name' => $this->input->post('name'),
                'middlename' => $this->input->post('middlename'),
                'lastname' => $this->input->post('lastname'),
                'email' => $this->input->post('email'),
                'password' => $this->input->post('password'),
                'sex' => $this->input->post('gender'),
                'dob' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('dob'))),
                'address' => $this->input->post('address'),
                'phone' => $this->input->post('phone'),
                'type_teacher' => $type_teacher,
                'is_active' => $is_active,
            );
            $insert_id = $this->teacher_model->add($data);
			
			if( $is_active == 'no'){
				$login_details = $this->user_model->getTeacherLoginDetails( $id );
				$user_id = $login_details[0]->id;
				$this->user_model->add( array('id' => $user_id, 'is_active' => 'no'));
			} else if($is_active == 'yes'){
				$login_details = $this->user_model->getTeacherLoginDetails( $id );
				$user_id = $login_details[0]->id;
				$this->user_model->add( array('id' => $user_id, 'is_active' => 'yes'));
			}
			
			
			/* if( $type_teacher == 'Advisory'){
				$class_id = $class_id;
				
				if( count($class_id) > 0 ){
					$this->classsection_model->blank_teacherid( $id	 );
					for($x=0;$x<count($class_id);$x++ ){
						$get_class_id = $class_id[$x];
						$data = array(
							'id' => $get_class_id,
							'teacher_id' => $id 
						);
						$this->classsection_model->assign_advisory( $data );
					}
				}
			} else {
				$this->classsection_model->blank_teacherid( $insert_id );	
			} */
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/teacher_images/" . $img_name);
                $data_img = array('id' => $id, 'image' => 'uploads/teacher_images/' . $img_name);
                $this->teacher_model->add($data_img);
            }
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">Teacher updated successfully</div>');
            redirect('idproduction/teacher/index');
        }
    }
	
	
	function handle_upload() {
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts = array('jpg', 'jpeg', 'png');
            $temp = explode(".", $_FILES["file"]["name"]);
            $extension = end($temp);
            if ($_FILES["file"]["error"] > 0) {
                $error .= "Error opening the file<br />";
            }
            if ($_FILES["file"]["type"] != 'image/gif' &&
                    $_FILES["file"]["type"] != 'image/jpeg' &&
                    $_FILES["file"]["type"] != 'image/png') {

                $this->form_validation->set_message('handle_upload', 'File type not allowed');
                return false;
            }
            if (!in_array($extension, $allowedExts)) {

                $this->form_validation->set_message('handle_upload', 'Extension not allowed');
                return false;
            }
            if ($_FILES["file"]["size"] > 10240000) {

                $this->form_validation->set_message('handle_upload', 'File size shoud be less than 100 kB');
                return false;
            }
            if ($error == "") {
                return true;
            }
        } else {
            return true;
        }
    }


    public function getSubjctByClassandSection() {
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $data = $this->teachersubject_model->getSubjectByClsandSection($class_id, $section_id);
        echo json_encode($data);
    }

    public function assignteacher() {
        $this->session->set_userdata('top_menu', 'Teachers');
        $this->session->set_userdata('sub_menu', 'teacher/assignTeacher');
        $data['title'] = 'Assign Teacher with Class and Subject wise';
        $teacher = $this->teacher_model->get();
        $data['teacherlist'] = $teacher;
        $subject = $this->subject_model->get();
        $data['subjectlist'] = $subject;
        $class = $this->class_model->get();
        $data['classlist'] = $class;
		$data['teacher_id'] = '';
        $this->form_validation->set_rules('teacher_id', 'Teacher', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE ) {
			$this->load->view('layout/idproduction/header', $data);
			$this->load->view('idproduction/teacher/assignTeacher', $data);
			$this->load->view('layout/idproduction/footer', $data);	
		} else {
			$id = $this->input->post('teacher_id');
			$data['teacher_id'] = $id;
            $loop = $this->input->post('i');
			$array = array();
            
			if( $loop ){
				 foreach ($loop as $key => $value) {
				
					$s = array();
					$s['session_id'] = $this->setting_model->getCurrentSession();
					
				/* 	$class_id = $this->input->post('class_id');
					$section_id = $this->input->post('section_id');
					$dt = $this->classsection_model->getDetailbyClassSection($class_id, $section_id);
					$subject = $this->subject_model->get( $class_id );
					$data['subjectlist'] = $subject; */
					$class_id = $this->input->post('class_id_' . $value);
					$section_id = $this->input->post('section_id_' . $value);
					$dt = $this->classsection_model->getDetailbyClassSection($class_id, $section_id);
					$s['class_section_id'] = $dt['id'];
					$s['teacher_id'] = $id;
					$s['subject_id'] = $this->input->post('subject_id_' . $value);
                    $s['semester'] = $this->input->post('semester_' . $value);
					$row_id = $this->input->post('row_id_' . $value);
					if ($row_id == 0) {
						$insert_id = $this->teachersubject_model->add($s);
						$array[] = $insert_id;
					} else {
						$s['id'] = $row_id;
						$array[] = $row_id;
						$this->teachersubject_model->add($s);
					}
					
					
				}
			}
          
			$ids =  $array;
			if( $ids ){
				$this->teachersubject_model->deleteBatchTeacherSubject($ids, $id );
			}
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Record updated successfully</div>');
			
			$this->load->view('layout/idproduction/header', $data);
			$this->load->view('idproduction/teacher/assignTeacher', $data);
			$this->load->view('layout/idproduction/footer', $data);	
        }
    }
	
	 function getlogindetail() {
        $teacher_id = $this->input->post('teacher_id');
        $examSchedule = $this->user_model->getTeacherLoginDetails($teacher_id);
        echo json_encode($examSchedule);
    }
	
	 function getSubjectTeachers()
    {
        $teacher_id   = $this->input->post('teacher_id');
		$teachersubject = $this->teachersubject_model->getTeacherSubjects($teacher_id);
        $data       = $teachersubject;
        echo json_encode($data);
    }
		 public function advisers() {
        $this->session->set_userdata('top_menu', 'Teachers');
        $this->session->set_userdata('sub_menu', 'teacher/advisers');
		$session_id = $this->input->get('session_id');
		$setting_result = $this->setting_model->get();
		if( empty( $session_id )){
				$session_id = $setting_result[0]['session_id'];
		}
        $data['current_session'] =  $setting_result[0]['session_id'];
        $data['title'] = 'Teacher Adviser';
        $session = $this->session_model->getAllSession();
		$teacher = $this->teacher_model->get();
        $data['teacherlist'] = $teacher;
		$data['session_id'] = $session_id;
		$data['sessionlist'] = $session;
		$vehicle_result = $this->section_model->get();
        $data['vehiclelist'] = $vehicle_result;
        $vehroute_result = $this->classsection_model->getByID( null, $session_id );
        $data['vehroutelist'] = $vehroute_result;
		$this->load->view('layout/idproduction/header', $data);
        $this->load->view('idproduction/teacher/classList', $data);
        $this->load->view('layout/idproduction/footer', $data);
    }

	function assign_advisers(){
		 $teacher_id = $this->input->post('teacher_id');
		 $section_id = $this->input->post('section_id');
		 $class_id = $this->input->post('class_id');
		 $get_session_id = $this->input->post('get_session_id');
		 $get_class_section_id = $this->classsection_model->getDetailbyClassSection( $class_id, $section_id, $get_session_id );
		 $class_section_id = $get_class_section_id['id'];
		 if( $class_section_id ){
			 $this->classsection_model->auto_add(array('id'=> $class_section_id, 'teacher_id' => $teacher_id ));
			 $this->session->set_flashdata('msg', '<div class="alert alert-success">Data updated successfully</div>');
		 } 
		 
		 redirect('idproduction/teacher/advisers/?session_id='.$get_session_id );
	}
}

?>
