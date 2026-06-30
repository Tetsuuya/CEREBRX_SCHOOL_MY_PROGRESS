<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class student extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('smsgateway');
        $this->load->helper('file');
        $this->lang->load('message', 'english');
        $this->role;
        $this->load->library('auth');
        $this->auth->is_logged_in_teacher(); 
		$this->load->model('Studentincident_model','SIM'); 
		$this->load->model('specializedsubject_model');
		$this->load->model('strand_model');
		$this->load->model('customsubject_model');
        $this->load->model('Notification_API_model','notification_api_model'); 
        $this->load->model('Sms_model','SMSM'); 
		$this->load->model('gradingsetting_model');		
		$this->load->model('subjectmanager_model');		
		$this->load->model('subjectcombine_model');	
		$this->load->model('conductsession_model');	
		$this->load->model('studentstrand_model');
		$this->load->model('conductimportbatchdetail_model');	
        $this->load->model('Session_model');
        $this->load->model('Subject_model');
        $this->load->model('Grade_model');
    }

    function index() {
        $data['title'] = 'Student List';
        $student_result = $this->student_model->get();
        $data['studentlist'] = $student_result;
        $this->load->view('layout/teacher/header', $data);
        $this->load->view('teacher/student/studentList', $data);
        $this->load->view('layout/teacher/footer', $data);
    }

    function studentreport() {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'student/studentreport');
        $data['title'] = 'student fee';
        $data['title'] = 'student fee';
        $genderList = $this->customlib->getGender();
        $data['genderList'] = $genderList;
        $RTEstatusList = $this->customlib->getRteStatus();
        $data['RTEstatusList'] = $RTEstatusList;
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $category = $this->category_model->get();
        $data['categorylist'] = $category;
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/student/studentReport', $data);
            $this->load->view('layout/teacher/footer', $data);
        } else {
            $this->form_validation->set_rules('class_id', 'Class', 'required');
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('layout/teacher/header', $data);
                $this->load->view('teacher/student/studentReport', $data);
                $this->load->view('layout/teacher/footer', $data);
            } else {
                $class = $this->input->post('class_id');
                $section = $this->input->post('section_id');
                $category_id = $this->input->post('category_id');
                $gender = $this->input->post('gender');
                $rte = $this->input->post('rte');
                $search = $this->input->post('search');
                if (isset($search)) {
                    if ($search == 'search_filter') {
                        $resultlist = $this->student_model->searchByClassSectionCategoryGenderRte($class, $section, $category_id, $gender, $rte);
                        $data['resultlist'] = $resultlist;
                    }
                    $data['class_id'] = $class;
                    $data['section_id'] = $section;
                    $data['category_id'] = $category_id;
                    $data['gender'] = $gender;
                    $data['rte_status'] = $rte;
                    $this->load->view('layout/teacher/header', $data);
                    $this->load->view('teacher/student/studentReport', $data);
                    $this->load->view('layout/teacher/footer', $data);
                }
            }
        }
    }

    public function download($student_id, $doc) {
        $this->load->helper('download');
        $filepath = "./uploads/student_documents/$student_id/" . $this->uri->segment(5);
        $data = file_get_contents($filepath);
        $name = $this->uri->segment(6);
        force_download($name, $data);
    }
    
     function view($id) {
        $data['title'] = 'Student Details';
        $setting_result = $this->setting_model->get();
        $session_id = $setting_result[0]['session_id'];
        $session_session_id = $this->session->userdata('grade_set_session');
        if( $session_session_id ){
            $session_id = $session_session_id;
        }
        $data['session_id'] = $session_id;
        $student = $this->student_model->getbySession($id, $session_id);
        $gradeList = $this->grade_model->get();
       
        $guardian_barangay_id = $student['guardian_barangay_id'];
        $guardian_city_id = $student['guardian_city_id'];
        $guardian_province_id = $student['guardian_province_id']; 
        $current_barangay_id = $student['current_barangay_id'];
        $current_city_id = $student['current_city_id'];
        $current_province_id = $student['current_province_id']; 
        $permanent_barangay_id = $student['permanent_barangay_id'];
        $permanent_city_id = $student['permanent_city_id'];
        $permanent_province_id = $student['permanent_province_id'];
 
        
        $guardian_address_barangay = $this->address_model->get_barangay( $guardian_barangay_id, TRUE );
        $guardian_address_city = $this->address_model->get_city( $guardian_city_id, TRUE ); 
        $guardian_address_province = $this->address_model->get_province( $guardian_province_id, TRUE );
        $current_address_barangay = $this->address_model->get_barangay( $current_barangay_id, TRUE );
        $current_address_city = $this->address_model->get_city( $current_city_id, TRUE );
        $current_address_province = $this->address_model->get_province( $current_province_id, TRUE );
        $permanent_address_barangay = $this->address_model->get_barangay( $permanent_barangay_id, TRUE );
        $permanentt_address_city = $this->address_model->get_city( $permanent_city_id, TRUE );
        $permanent_address_province = $this->address_model->get_province( $permanent_province_id, TRUE );
       
        
        $data['new_complete_guardian_address'] = $guardian_address_barangay['name'].', '.$guardian_address_city['name'].', '.$guardian_address_province['name']; 
        $data['new_complete_current_address'] = $current_address_barangay['name'].', '.$current_address_city['name'].', '.$current_address_province['name'];
        $data['new_complete_permanent_address'] = $permanent_address_barangay['name'].', '.$permanentt_address_city['name'].', '.$permanent_address_province['name'];

        //$examList = $this->examschedule_model->getExamByClassandSection($student['class_id'], $student['section_id']);
       /*  $data['examSchedule'] = array();
        if (!empty($examList)) {
            $new_array = array();
            foreach ($examList as $ex_key => $ex_value) {
                $array = array();
                $x = array();
                $exam_id = $ex_value['exam_id'];
                $student['id'];
                $exam_subjects = $this->examschedule_model->getresultByStudentandExam($exam_id, $student['id']);
                foreach ($exam_subjects as $key => $value) {
                    $exam_array = array();
                    $exam_array['exam_schedule_id'] = $value['exam_schedule_id'];
                    $exam_array['exam_id'] = $value['exam_id'];
                    $exam_array['full_marks'] = $value['full_marks'];
                    $exam_array['passing_marks'] = $value['passing_marks'];
                    $exam_array['exam_name'] = $value['name'];
                    $exam_array['exam_type'] = $value['type'];
                    $exam_array['attendence'] = $value['attendence'];
                    $exam_array['get_marks'] = $value['get_marks'];
                    $x[] = $exam_array;
                }
                $array['exam_name'] = $ex_value['name'];
                $array['exam_result'] = $x;
                $new_array[] = $array;
            }
            $data['examSchedule'] = $new_array;
        } */
        $student_doc = $this->student_model->getstudentdoc($id);
        $data['student_doc'] = $student_doc;
        $data['student_doc_id'] = $id;
        $category_list = $this->category_model->get();
        $data['category_list'] = $category_list;
        $data['gradeList'] = $gradeList;
        $data['student'] = $student;
        $data['student_id'] = $student['id'];
        $getStudentSubject = $this->subject_model->getSubjctByClass( $student['class_id'] );
        //$getStudentSubject = $this->examresult_model->get_student_exam_result_by_quarter( $student['id'] );
        //$getStudentSubjectshs = $this->examresult_model->get_student_exam_result_by_subject_shs( $student['id'] );
        $data['list_of_subjects'] = $getStudentSubject;
        //$data['list_of_subjects_shs'] = $getStudentSubjectshs;
        $data['enableStrand'] = $this->strand_model->checkEnable( $student['class_id'] );
        $data['getStrand'] = $this->strand_model->get( $student['strand_id'] );
        $data['strand_id'] = $student['strand_id'];
        $getConductSubject = $this->conductsession_model->getDetailByclassAndSection( $student['class_id'],  $student['section_id'], $session_id );
        $data['getConductSubject'] = $getConductSubject;
        $data['section_id'] = $student['section_id'];
        $data['class_id'] = $student['class_id'];

        $teacher_id = $this->session->userdata['student']['teacher_id'];
        $teacher_result = $this->teacher_model->get($teacher_id); 
        $class_advisory = $this->classsection_model->get_individual_advisories(  $teacher_id  );
        // $adviser = $this->teacher_model->getactiveadvisory();
        $data['adviser'] = $class_advisory;
        
        $this->load->view('layout/teacher/header', $data);
        $this->load->view('teacher/student/studentShow', $data);
        $this->load->view('layout/teacher/footer', $data);
    }

    public function view_allgrades($student_id)
    {
        // Get student & session data
        $sessions = $this->Session_model->getSessionsByStudent($student_id);
        $data['student_id'] = $student_id;
        $data['student_data'] = $this->student_model->get($student_id);
        $class_id = $data['student_data']['class_id'];
        // $strand_id = $data['student_data']['strand_id'] ? $data['student_data']['strand_id'] : null;
        $level = ($class_id < 14) ? 'JHS' : 'SHS';
        $data['sessions'] = [];

        foreach ($sessions as $session) {
            $session_id = $session['session_id'];
            $session_name = $session['session_name'];

            // 🔹 Get class + section per session
            $student_class = $this->Grade_model->getClassAndSectionBySession($student_id, $session_id);
            $strand_id = isset($student_class['strand_id']) ? $student_class['strand_id'] : null;
            $class_name = isset($student_class['class']) ? $student_class['class'] : 'N/A';
            $section_name = isset($student_class['section']) ? $student_class['section'] : 'N/A';

            // Fetch subjects
            $subjects = [];

            if ($level == 'SHS') {
                for ($sem = 1; $sem <= 2; $sem++) {
                    $sem_subjects = $this->Grade_model->getByStrandSemester($strand_id, $sem, $session_id);

                    foreach ($sem_subjects as &$subject) {
                        $subject_id = $subject['subject_id'];
                        $subject['semester'] = $sem;

                        for ($q = 1; $q <= 3; $q++) {
                            $result = $this->Grade_model->getExamResultsByStudentSessionQuarter(
                                $student_id,
                                $session_id,
                                $subject_id,
                                $q
                            );
                            $subject['grades'][$q] = !empty($result) && isset($result[0]['grades']) ? $result[0]['grades'] : null;
                        }

                        $total = 0; $count = 0;
                        foreach ($subject['grades'] as $grade) {
                            if ($grade !== null && is_numeric($grade)) {
                                $total += $grade;
                                $count++;
                            }
                        }

                        $subject['final_grade'] = $count ? round($total / $count, 2) : null;
                    }

                    $subjects = array_merge($subjects, $sem_subjects);
                }
            } else {
                $subjects = $this->Grade_model->getSubjectsForStudentSession($student_id, $session_id);

                foreach ($subjects as &$subject) {
                    $subject_id = $subject['id'];

                    for ($q = 1; $q <= 3; $q++) {
                        $result = $this->Grade_model->getExamResultsByStudentSessionQuarter(
                            $student_id,
                            $session_id,
                            $subject_id,
                            $q
                        );
                        $subject['grades'][$q] = !empty($result) && isset($result[0]['grades']) ? $result[0]['grades'] : null;
                    }

                    $total = 0; $count = 0;
                    foreach ($subject['grades'] as $grade) {
                        if ($grade !== null && is_numeric($grade)) {
                            $total += $grade;
                            $count++;
                        }
                    }

                    $subject['final_grade'] = $count ? round($total / $count, 2) : null;
                }
            }

            $data['sessions'][] = [
                'session_id'   => $session_id,
                'session_name' => $session_name,
                'class_name'   => $class_name,
                'section_name' => $section_name,
                'subjects'     => $subjects
            ];
        }


        // printx($data);

        $this->load->view('teacher/grade/student_grades', $data);
    }

    function exportformat() {
       $array = array(
            array("admission_no", "lrn",
                "admission_date(dd-mm-yyyy)", "firstname",
                "lastname", "gender","dob(dd-mm-yyyy)",
                "current_address", "permanent_address",
                "father_name","father_midname","father_lastname",
                "mother_name","mother_name","mother_lastname",
                "guardian_name",
                "guardian_relation", "guardian_phone",
                "guardian_address"),
        );
        $this->load->helper('csv');
        echo array_to_csv($array, 'import_student_sample_file.csv');
    }

    function delete($id) {
        $this->student_model->remove($id);
        $this->session->set_flashdata('msg', '<i class="fa fa-check-square-o" aria-hidden="true"></i> Record Deleted Successfully');
        redirect('teacher/student/search');
    }

    function doc_delete($id, $student_id) {
        $this->student_model->doc_delete($id);
        $this->session->set_flashdata('msg', '<i class="fa fa-check-square-o" aria-hidden="true"></i> Document Deleted Successfully');
        redirect('teacher/student/view/' . $student_id);
    }

    function create() {
        $this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'student/create');
        $genderList = $this->customlib->getGender();
    	$bloodList = array( '0 -', '0 +', 'A -', 'A +',  'B -', 'B +',  'AB -', 'AB +');
    		 
    	$this->db->select_max('admission_no');
    	$result = $this->db->get('students')->row();   
    	/* First variation number of the admissio number
        $admission_no_code_1 = date('Ymd'); 
        $admission_no_code_2 = str_pad( $result->admission_no+1,6,'0', STR_PAD_LEFT);
        $admission_no_code_2 = substr($admission_no_code_2, -6);
        $admission_no_code = $admission_no_code_1.'-'.$admission_no_code_2; */ 


        /* Second variation number of the admissio number */
        $admission_no_code_1 = date('Y'); 
        $admission_no_code_2 = str_pad( $result->admission_no+1,5,'0', STR_PAD_LEFT);
        $admission_no_code_2 = substr($admission_no_code_2, -5);
        $admission_no_code = $admission_no_code_1.'-'.$admission_no_code_2;  
		   
        $data['admission_no_code'] = $admission_no_code;
        $data['bloodList'] = $bloodList;
        $data['genderList'] = $genderList;
        $data['title'] = 'Add Student';
        $data['title_list'] = 'Recently Added Student';
        $session = $this->setting_model->getCurrentSession();
        $student_result = $this->student_model->getRecentRecord();
        $data['studentlist'] = $student_result;
        $vehroute_result = $this->vehroute_model->get();
        $data['vehroutelist'] = $vehroute_result;
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $category = $this->category_model->get();
        $data['categorylist'] = $category;
        $this->form_validation->set_rules('firstname', 'First Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean');
        $this->form_validation->set_rules('dob', 'Date of Birth', 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');
        $this->form_validation->set_rules('rte', 'RTE', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_name', 'Guardian Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('father_name', 'Father Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('mother_name', 'Mother Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_phone', 'Guardian Phone', 'trim|required|xss_clean');
        $this->form_validation->set_rules('admission_no', 'Admission No', 'trim|required|xss_clean|is_unique[students.admission_no]');
        $this->form_validation->set_rules('file', 'Image', 'callback_handle_upload');
    	$this->form_validation->set_rules('government_voucher', 'Government Voucher', 'trim|required|xss_clean');
    	$this->form_validation->set_rules('father_lastname', 'Father Last Name', 'trim|required|xss_clean');
    	$this->form_validation->set_rules('mother_lastname', 'Mother Last Name', 'trim|required|xss_clean');
      	$this->form_validation->set_rules('guardian_lastname', 'Guardian Last Name', 'trim|required|xss_clean');
    	$this->form_validation->set_rules('blood_type', 'Blood Type', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('lunch_pass', 'Lunch Pass', 'trim|required|xss_clean');
        $this->form_validation->set_rules('commuter_pass', 'Commuter Pass', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {            
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/student/studentCreate', $data);
            $this->load->view('layout/teacher/footer', $data);
        } else {
            $class_id = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
           
            $fees_discount = $this->input->post('fees_discount');
            $vehroute_id = $this->input->post('vehroute_id');
            $data = array(
                'admission_no' => $this->input->post('admission_no'),
                'roll_no' => $this->input->post('roll_no'),
                'admission_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('admission_date'))),
                'firstname' => $this->input->post('firstname'),
                'lastname' => $this->input->post('lastname'),
                // 'mobileno' => $this->input->post('mobileno'),
                'government_voucher' => $this->input->post('government_voucher'),
                'blood_type' => $this->input->post('blood_type'),
                'rte' => $this->input->post('rte'),
                'email' => $this->input->post('email'),
                'state' => $this->input->post('state'),
                'city' => $this->input->post('city'),
                'guardian_is' => $this->input->post('guardian_is'),
                'pincode' => $this->input->post('pincode'),
                'religion' => $this->input->post('religion'),
                // 'cast' => $this->input->post('cast'),
                'previous_school' => $this->input->post('previous_school'),
                'dob' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('dob'))),
                'current_address' => $this->input->post('current_address'),
                'permanent_address' => $this->input->post('permanent_address'),
                'current_barangay_id' => $this->input->post('current_barangay_id'),
                'current_city_id' => $this->input->post('current_city_id'),
                'current_province_id' => $this->input->post('current_province_id'), 
                'permanent_barangay_id' => $this->input->post('current_barangay_id'),
                'permanent_city_id' => $this->input->post('current_city_id'),
                'permanent_province_id' => $this->input->post('current_province_id'), 
                'image' => 'uploads/student_images/no_image.png',
                'category_id' => $this->input->post('category_id'),
                'adhar_no' => $this->input->post('adhar_no'),
                'samagra_id' => $this->input->post('samagra_id'),
                'bank_account_no' => $this->input->post('bank_account_no'),
                'bank_name' => $this->input->post('bank_name'),
                'ifsc_code' => $this->input->post('ifsc_code'),
                'father_name' => $this->input->post('father_name'),
                'father_phone' => $this->input->post('father_phone'),
                'father_occupation' => $this->input->post('father_occupation'),
                'mother_name' => $this->input->post('mother_name'),
                'mother_phone' => $this->input->post('mother_phone'),
                'mother_occupation' => $this->input->post('mother_occupation'),
                'guardian_occupation' => $this->input->post('guardian_occupation'),
                'gender' => $this->input->post('gender'),
                'guardian_name' => $this->input->post('guardian_name'),
                'guardian_relation' => $this->input->post('guardian_relation'),
                'guardian_phone' => $this->input->post('guardian_phone'),
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
                );
            $insert_id = $this->student_model->add($data);
            $data_new = array(
                'student_id' => $insert_id,
                'class_id' => $class_id,
                'section_id' => $section_id,
                'session_id' => $session,
                'vehroute_id' => $vehroute_id,
              
                'fees_discount' => $fees_discount
                );
            $this->student_model->add_student_session($data_new);
            $user_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
            $sibling_id = $this->input->post('sibling_id');
            $data_student_login = array(
                'username' => $this->student_login_prefix . $insert_id,
                'password' => $user_password,
                'user_id' => $insert_id,
                'role' => 'student'
                );
            $this->user_model->add($data_student_login);
            if (isset($sibling_id)) {
                $countsib = 0;
                $up_record = 0;
                $record_value = "";
                $findusers = $this->user_model->read_user();
                $find = $sibling_id;
                foreach ($findusers as $key => $value) {
                    if ($value->childs != "") {
                        $childs = explode(",", $value->childs);
                        foreach ($childs as $k_child => $v_child) {
                            if ($find == $v_child) {
                                $up_record = $value->id;
                                $record_value = $value->childs;
                                $countsib = 1;
                                break;
                            }
                        }
                    }
                }
                if ($countsib != 0) {
                    $json = array($insert_id);
                    $da = array_merge((array) $record_value, (array) $json);
                    $rec = implode(",", $da);
                    $data_parent_login = array(
                        'id' => $up_record,
                        'childs' => $rec
                        );
                    $ins_id = $this->user_model->add($data_parent_login);
                } else {
                 $parent_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
                 $temp = $insert_id;
                 $data_parent_login = array(
                    'username' => $this->parent_login_prefix . $insert_id,
                    'password' => $parent_password,
                    'user_id' => $insert_id,
                    'role' => 'parent',
                    'childs' => $temp
                    );
                 $ins_id = $this->user_model->add($data_parent_login);
             }
         }
         if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $fileInfo = pathinfo($_FILES["file"]["name"]);
            $img_name = $insert_id . '.' . $fileInfo['extension'];
            move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/student_images/" . $img_name);
            $data_img = array('id' => $insert_id, 'image' => 'uploads/student_images/' . $img_name);
            $this->student_model->add($data_img);
        }
        if (isset($_FILES["first_doc"]) && !empty($_FILES['first_doc']['name'])) {
            $uploaddir = './uploads/student_documents/' . $insert_id . '/';
            if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                die("Error creating folder $uploaddir");
            }
            $fileInfo = pathinfo($_FILES["first_doc"]["name"]);
            $first_title = $this->input->post('first_title');
            $img_name = $uploaddir . basename($_FILES['first_doc']['name']);
            move_uploaded_file($_FILES["first_doc"]["tmp_name"], $img_name);
            $data_img = array('student_id' => $insert_id, 'title' => $first_title, 'doc' => basename($_FILES['first_doc']['name']));
            $this->student_model->adddoc($data_img);
        }
        if (isset($_FILES["second_doc"]) && !empty($_FILES['second_doc']['name'])) {
            $uploaddir = './uploads/student_documents/' . $insert_id . '/';
            if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                die("Error creating folder $uploaddir");
            }
            $fileInfo = pathinfo($_FILES["second_doc"]["name"]);
            $second_title = $this->input->post('second_title');
            $img_name = $uploaddir . basename($_FILES['second_doc']['name']);
            move_uploaded_file($_FILES["second_doc"]["tmp_name"], $img_name);
            $data_img = array('student_id' => $insert_id, 'title' => $second_title, 'doc' => basename($_FILES['second_doc']['name']));
            $this->student_model->adddoc($data_img);
        }
        if (isset($_FILES["third_doc"]) && !empty($_FILES['third_doc']['name'])) {
            $uploaddir = './uploads/student_documents/' . $insert_id . '/';
            if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                die("Error creating folder $uploaddir");
            }
            $fileInfo = pathinfo($_FILES["third_doc"]["name"]);
            $third_title = $this->input->post('third_title');
            $img_name = $uploaddir . basename($_FILES['third_doc']['name']);
            move_uploaded_file($_FILES["third_doc"]["tmp_name"], $img_name);
            $data_img = array('student_id' => $insert_id, 'title' => $third_title, 'doc' => basename($_FILES['third_doc']['name']));
            $this->student_model->adddoc($data_img);
        }
        if (isset($_FILES["fourth_doc"]) && !empty($_FILES['fourth_doc']['name'])) {
            $uploaddir = './uploads/student_documents/' . $insert_id . '/';
            if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                die("Error creating folder $uploaddir");
            }
            $fileInfo = pathinfo($_FILES["fourth_doc"]["name"]);
            $fourth_title = $this->input->post('fourth_title');
            $img_name = $uploaddir . basename($_FILES['fourth_doc']['name']);
            move_uploaded_file($_FILES["fourth_doc"]["tmp_name"], $img_name);
            $data_img = array('student_id' => $insert_id, 'title' => $fourth_title, 'doc' => basename($_FILES['fourth_doc']['name']));
            $this->student_model->adddoc($data_img);
        }
        if (isset($_FILES["fifth_doc"]) && !empty($_FILES['fifth_doc']['name'])) {
            $uploaddir = './uploads/student_documents/' . $insert_id . '/';
            if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                die("Error creating folder $uploaddir");
            }
            $fileInfo = pathinfo($_FILES["fifth_doc"]["name"]);
            $fifth_title = $this->input->post('fifth_title');
            $img_name = $uploaddir . basename($_FILES['fifth_doc']['name']);
            move_uploaded_file($_FILES["fifth_doc"]["tmp_name"], $img_name);
            $data_img = array('student_id' => $insert_id, 'title' => $fifth_title, 'doc' => basename($_FILES['fifth_doc']['name']));
            $this->student_model->adddoc($data_img);
        }
    $result =  $this->smsgateway->sentRegisterSMS($insert_id,$this->input->post('guardian_phone'));
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Student added Successfully</div>');
        redirect('teacher/student/create');
    }
}

function create_doc() {
    $student_id = $this->input->post('student_id');
    if (isset($_FILES["first_doc"]) && !empty($_FILES['first_doc']['name'])) {
        $uploaddir = './uploads/student_documents/' . $student_id . '/';
        if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
            die("Error creating folder $uploaddir");
        }
        $fileInfo = pathinfo($_FILES["first_doc"]["name"]);
        $first_title = $this->input->post('first_title');
        $img_name = $uploaddir . basename($_FILES['first_doc']['name']);
        move_uploaded_file($_FILES["first_doc"]["tmp_name"], $img_name);
        $data_img = array('student_id' => $student_id, 'title' => $first_title, 'doc' => basename($_FILES['first_doc']['name']));
        $this->student_model->adddoc($data_img);
    }
    redirect('teacher/student/view/' . $student_id);
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
    if ($_FILES["file"]["size"] > 102400) {
        $this->form_validation->set_message('handle_upload', 'File size shoud be less than 100 kB');
        return false;
    }
    return true;
} else {
    return true;
}
}

function import() {
    $data['title'] = 'Import Student';
    $data['title_list'] = 'Recently Added Student';
    $session = $this->setting_model->getCurrentSession();
    $class = $this->class_model->get();
    $data['classlist'] = $class;
    $category = $this->category_model->get();
    $data['categorylist'] = $category;
    $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
    $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');
    $this->form_validation->set_rules('file', 'Image', 'callback_handle_csv_upload');
    if ($this->form_validation->run() == FALSE) {
        $this->load->view('layout/teacher/header', $data);
        $this->load->view('teacher/student/import', $data);
        $this->load->view('layout/teacher/footer', $data);
    } else {
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $session = $this->setting_model->getCurrentSession();
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $file = $_FILES['file']['tmp_name'];
            $this->load->library('CSVReader');
            $result = $this->csvreader->parse_file($file);
            for ($i = 1; $i <= count($result); $i++) {
                $insert_id = $this->student_model->add($result[$i]);
                $data_new = array(
                    'student_id' => $insert_id,
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'session_id' => $session
                    );
                $this->student_model->add_student_session($data_new);
            }
            $data['csvData'] = $result;
        }
   $this->session->set_flashdata('msg', '<div student="alert alert-success text-center">Students imported successfully</div>');
        redirect('teacher/student/search');
    }
}

function handle_csv_upload() {
    $error = "";
    if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
        $allowedExts = array('csv');
        $mimes = array('text/csv',
        'text/plain',
        'application/csv',
        'text/comma-separated-values',
        'application/excel',
        'application/vnd.ms-excel',
        'application/vnd.msexcel',
        'text/anytext',
        'application/octet-stream',
        'application/txt');
        $temp = explode(".", $_FILES["file"]["name"]);
        $extension = end($temp);
        if ($_FILES["file"]["error"] > 0) {
            $error .= "Error opening the file<br />";
        }
        if (!in_array($_FILES['file']['type'], $mimes)) {
            $error .= "Error opening the file<br />";
            $this->form_validation->set_message('handle_csv_upload', 'File type not allowed');
            return false;
        }
        if (!in_array($extension, $allowedExts)) {
            $error .= "Error opening the file<br />";
            $this->form_validation->set_message('handle_csv_upload', 'Extension not allowed');
            return false;
        }
        if ($error == "") {
            return true;
        }
    } else {
        $this->form_validation->set_message('handle_csv_upload', 'Please Select file');
        return false;
    }
}



    function edit($id) {

        $teacher_id = $this->session->userdata['student']['teacher_id'];
        $teacher_result = $this->teacher_model->get($teacher_id); 
        $class_advisory = $this->classsection_model->get_individual_advisories(  $teacher_id  );
        $data['class_advisory'] = $class_advisory;
        $data['title'] = 'Edit Student';
        $data['id'] = $id;
        $data['teacher_id'] = $teacher_id;
        $student = $this->student_model->get($id); 
        /* print_r( $student );
        die(); */
        $genderList = $this->customlib->getGender();
        $bloodList = array( '0','0 -', '0 +','A','A -', 'A +',  'B', 'B -', 'B +',  'AB -', 'AB +');
        $religionList = array( 'Roman Catholic', 'Islam', 'Evangelical', 'INC',  'SDA','Protestant','Bible Baptist Church','United Church of Christ in the Philippines','Jehovahs Witnesses');
        $data['student'] = $student;
        $data['genderList'] = $genderList;
        $data['bloodList'] = $bloodList;
        $data['religionList'] = $religionList;
        $session = $this->setting_model->getCurrentSession();
        $vehroute_result = $this->vehroute_model->get();
        $data['vehroutelist'] = $vehroute_result;
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $category = $this->category_model->get();
        $data['categorylist'] = $category;
 
        $barangay = $this->address_model->get_barangay();
        $city = $this->address_model->get_city();
        $province = $this->address_model->get_province();

        $guardian_barangay_id = $student['guardian_barangay_id'];
        $guardian_city_id = $student['guardian_city_id'];
        $guardian_province_id = $student['guardian_province_id']; 
        $current_barangay_id = $student['current_barangay_id'];
        $current_city_id = $student['current_city_id'];
        $current_province_id = $student['current_province_id']; 
        $permanent_barangay_id = $student['permanent_barangay_id'];
        $permanent_city_id = $student['permanent_city_id'];
        $permanent_province_id = $student['permanent_province_id'];
         
        $data['barangaylist'] = $barangay;
        $data['citylist'] = $city;
        $data['provincelist'] = $province;
        $data['strands'] = $this->strand_model->get();
        $data['studentstatuslist'] = $this->customlib->getStudentStatus();
        $data['hostelroomlist'] = $this->hostelroom_model->get(); 
        $this->form_validation->set_rules('firstname', 'First Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('dob', 'Date of Birth', 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('guardian_name', 'Guardian Name', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('government_voucher', 'Government Voucher', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('guardian_phone', 'Guardian Phone', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('guardian_lastname', 'Guardian Last Name', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('lunch_pass', 'Lunch Pass', 'trim|required|xss_clean');
        $this->form_validation->set_rules('commuter_pass', 'Commuter Pass', 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == FALSE) { 
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/student/studentEdit', $data);
            $this->load->view('layout/teacher/footer', $data);
        } else {
            $class_id = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');

            $fees_discount = $this->input->post('fees_discount');
            $vehroute_id = $this->input->post('vehroute_id');
            
            $admission_no = $this->input->post('admission_no');
            $father_phone = $this->input->post('father_phone');
            $father_phone = str_replace(' ', '', $father_phone);
            $mother_phone = $this->input->post('mother_phone');
            $mother_phone = str_replace(' ', '', $mother_phone);
            $guardian_phone = $this->input->post('guardian_phone');
            $guardian_phone = str_replace(' ', '', $guardian_phone);
            $data = array(
                'id' => $id,
                'admission_no' => $this->input->post('admission_no'),
                'roll_no' => $this->input->post('roll_no'),
                'admission_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('admission_date'))),
                'firstname' => $this->input->post('firstname'),
                'lastname' => $this->input->post('lastname'),
                'suffix' => $this->input->post('suffix'),
                'rte' => $this->input->post('rte'),
                'mobileno' => $this->input->post('mobileno'),
                'government_voucher' => $this->input->post('government_voucher'),
                'blood_type' => $this->input->post('blood_type'),
                'email' => $this->input->post('email'),
                'state' => $this->input->post('state'),
                'city' => $this->input->post('city'), 
                'previous_school' => $this->input->post('previous_school'),
                'guardian_is' => $this->input->post('guardian_is'),
                'pincode' => $this->input->post('pincode'),
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
                'adhar_no' => $this->input->post('adhar_no'),
                'samagra_id' => $this->input->post('samagra_id'),
                'bank_account_no' => $this->input->post('bank_account_no'),
                'bank_name' => $this->input->post('bank_name'),
                'ifsc_code' => $this->input->post('ifsc_code'),
                // 'cast' => $this->input->post('cast'),
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
                //'strand_id' => $this->input->post('strand_id'),
                'status' => $this->input->post('studentstatus'),
                'dormitory' => $this->input->post('dormitory'),
                'hostel_id' => $this->input->post('dormitory_room'),
                'guardian_address2' => $this->input->post('guardian_address2'),
                // 'rfid_number' => $this->input->post('rfid_number')
            
            );
            $this->student_model->add($data);
            $data_new = array(
                'student_id' => $id,
                'class_id' => $class_id,
                'section_id' => $section_id,
                'session_id' => $session,
                'vehroute_id' => $vehroute_id,
                'fees_discount' => $fees_discount,
                'strand_id' =>  $this->input->post('strand_id')
            );
            $insert_id = $this->student_model->add_student_session($data_new);
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                // $img_name = $id . '.' . $fileInfo['extension'];
                $last_image = $student['image'];
                $full_image_directory =  'uploads/student_images/' . $last_image;
                if( file_exists(FCPATH.$full_image_directory) ){
                        unlink($full_image_directory);
                }
                
                $img_name = 'ST-'.$admission_no . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/student_images/" . $img_name);
                $data_img = array('id' => $id, 'image' => 'uploads/student_images/' . $img_name);
                $this->student_model->add($data_img);
                $get_image = 'uploads/student_images/'. $img_name;
                $this->imageresize->load( $get_image );
                $get_height = $this->imageresize->getHeight();
                $get_width = $this->imageresize->getWidth();
                // if( $get_height != '624' && $get_width != '464'){
                //   $this->imageresize->resize(464, 624);
               /* if( $get_height != '500' && $get_width != '375'){
                    $this->imageresize->resize(375, 500); 
                     $this->imageresize->save( $get_image);
                 }*/
            }
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Student Record Updated successfully</div>');
            redirect('teacher/student/edit/'.$id);
        }
    }

    function search() {
        $this->session->set_userdata('top_menu', 'Student');
        $this->session->set_userdata('sub_menu', 'student/search');
        $data['title'] = 'Student Search';

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


        $student_session_data = $this->session->userdata("student");
        $teacher_id = $student_session_data['teacher_id'];
        $class = $this->class_model->get_calss_section_subject( $teacher_id, $session_id );
        
        $data['classlist'] = $class;
        $data['section_id'] = '';
        $data['searchby'] = '';
        $data['class_id'] = '';
        $data['section_id'] ='';
    

        $button = $this->input->post('search');
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/student/studentSearch', $data);
            $this->load->view('layout/teacher/footer', $data);
        } else {
            $session_id = $this->input->post('hide_session_id');
            $this->session->set_userdata(array('grade_set_session'=>$session_id));
            $data['session_id'] = $session_id;
            $class = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $search = $this->input->post('search');
            $search_text = $this->input->post('search_text');
            if (isset($search)) {
                if ($search == 'search_filter') {
                    $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
                    if ($this->form_validation->run() == FALSE) {
                    } else {
                        $data['searchby'] = "filter";
                        $data['class_id'] = $this->input->post('class_id');
                        $data['section_id'] = $this->input->post('section_id');
                        $data['search_text'] = $this->input->post('search_text');
                        $resultlist_male = $this->student_model->searchByClassSectionGender($class, $section, 'Male', $session_id);
                        $resultlist_female = $this->student_model->searchByClassSectionGender($class, $section, 'Female', $session_id);
                        $data['resultlist_male'] = $resultlist_male;
                        $data['resultlist_female'] = $resultlist_female;
                    }
                } else if ($search == 'search_full') {
                    $data['searchby'] = "text";
                    $data['class_id'] = $this->input->post('class_id');
                    $data['section_id'] = $this->input->post('section_id');
                    $data['search_text'] = trim($this->input->post('search_text'));
                    $resultlist = $this->student_model->searchFullText($search_text,'yes',$session_id);
                    $data['resultlist'] = $resultlist;
                }
            }
            $data['enableStrand'] = $this->strand_model->checkEnable( $class );
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/student/studentSearch', $data);
            $this->load->view('layout/teacher/footer', $data);
        }
    }

    function getByClassSession() {
        $student_session_data = $this->session->userdata("student");
        $teacher_id = $student_session_data['teacher_id'];
        $session_id = $this->input->get('session_id');
        $resultlist = $this->class_model->get_calss_section_subject( $teacher_id, $session_id );
        echo json_encode($resultlist);
    }

    function getByClassAndSection() {
        $class = $this->input->get('class_id');
        $section = $this->input->get('section_id');
        $resultlist = $this->student_model->searchByClassSection($class, $section);
        echo json_encode($resultlist);
    }

    function getStudentRecordByID() {
        $student_id = $this->input->get('student_id');
        $resultlist = $this->student_model->get($student_id);
        echo json_encode($resultlist);
    }

    function uploadimage($id) {
        $data['title'] = 'Add Image';
        $data['id'] = $id;
        $this->load->view('layout/teacher/header', $data);
        $this->load->view('teacher/student/uploadimage', $data);
        $this->load->view('layout/teacher/footer', $data);
    }

    public function doupload($id) {
        $config = array(
            'upload_path' => "./uploads/student_images/",
            'allowed_types' => "gif|jpg|png|jpeg|df",
            'overwrite' => TRUE,
            );
        $config['file_name'] = $id . ".jpg";
        $this->upload->initialize($config);
        $this->load->library('upload', $config);
        if ($this->upload->do_upload()) {
            $data = array('upload_data' => $this->upload->data());
            $upload_data = $this->upload->data();
            $data_record = array('id' => $id, 'image' => $upload_data['file_name']);
            $this->setting_model->add($data_record);
            $this->load->view('upload_success', $data);
        } else {
            $error = array('error' => $this->upload->display_errors());
    
            $this->load->view('file_view', $error);
        }
    }

    function getlogindetail() {
        $student_id = $this->input->post('student_id');
        $examSchedule = $this->user_model->getLoginDetails($student_id);
        echo json_encode($examSchedule);
    }

    public function getsearchstudent(){
        $search_student = $this->input->post('search_student'); 
        $resultlist = $this->student_model->searchFullText($search_student);
        // echo  $resultlist ;   
        echo json_encode($resultlist);  
        # code...
    }

     public function getsearchstudentactivewithtransferee(){
        $search_student = $this->input->post('search_student'); 
        $session_id = $this->input->post('session_id'); 
        $resultlist = $this->student_model->searchFullTextTranferee($search_student,'yes',$session_id,TRUE);
        // echo  $resultlist ;   
        echo json_encode($resultlist);  
        # code...
    }

     public function getsearchstudentactive(){
        $search_student = $this->input->post('search_student'); 
        $session_id = $this->input->post('session_id'); 
        $resultlist = $this->student_model->searchFullText($search_student,'yes',$session_id,TRUE);
        // echo  $resultlist ;   
        echo json_encode($resultlist);  
        # code...
    }

     function incident() {
        $this->session->set_userdata('top_menu', 'Student');
        $this->session->set_userdata('sub_menu', 'student/incident');
        $data['title'] = 'Student Incident';
 
        $student_session_data = $this->session->userdata("student");
        $teacher_id = $student_session_data['teacher_id'];
        

        $teacher_details = $this->teacher_model->get( $teacher_id );
        $phone = $teacher_details['phone'];

        $incident_result = $this->SIM->get( null, $teacher_id); 
        $data['incidentlist'] = $incident_result;
        $data['phone'] = $phone;
        // $this->form_validation->set_rules('doa', 'Date of Admission', 'trim|required|xss_clean');
        $this->form_validation->set_rules('note', 'Note', 'trim|required|xss_clean');
        $this->form_validation->set_rules('student_id', 'Student', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/student/incidentList', $data);
            $this->load->view('layout/teacher/footer', $data);
        } else {
            date_default_timezone_set('Asia/Manila');
            $todays_date = date('Y-m-d');
            $data = array(
                // 'quarter' => $this->input->post('quarter'),
                'doa' => $todays_date,
                'student_id' => $this->input->post('student_id'), 
                'teacher_id' => $teacher_id,
                'type' => $this->input->post('case_type'),
                'note' => $this->input->post('note')   
            ); 
            $insert_id = $this->SIM->add($data); 

            if( $insert_id  ){  
                $student_id = $this->input->post('student_id');
                $message_to_teacher = $this->input->post('message_to_teacher');
                $case_type = $this->input->post('case_type');
                $note = $this->input->post('note');
                $student_details = $this->student_model->getStudentbySession( $student_id); 
                $guardian_phone =  $student_details->guardian_phone;
                $firstname =  $student_details->firstname;
                $lastname =  $student_details->lastname; 
                $case_date_display =  date('F d, Y', strtotime($todays_date));
                $sms_message =  'From LVAA: '.$firstname.' has a '.$case_type.' incident: '.$note.' at '.$case_date_display.'. This is an autogenerated SMS by Bridgette. Please do not reply.'; 
                if( $guardian_phone ){
                     $data_sms  = array(
                        'guidance_id' => $insert_id,
                        'sms_msg' => $sms_message,
                        'sms_number' => $guardian_phone, 
                        'sms_status' => 'idle',
                        'sms_teacher' => 'no',
                        'sms_parent' => 'yes' 
                    );
                     $this->SMSM->add($data_sms); 

                      if( $message_to_teacher == 'yes'){
                         $data_sms  = array(
                            'guidance_id' => $insert_id,
                            'sms_msg' => $sms_message,
                            'sms_number' => $phone, 
                            'sms_status' => 'idle',
                            'sms_teacher' => 'yes',
                            'sms_parent' => 'no' 
                        );
                         $this->SMSM->add($data_sms); 

                     } 
                } 

               
            } 



            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Student Incident added successfully</div>');
            redirect('teacher/student/incident');
        }

    }

    function incident_edit( $id ) {
        $this->session->set_userdata('top_menu', 'Student');
        $this->session->set_userdata('sub_menu', 'student/incident');
        $data['title'] = 'Student Incident';
 
        $student_session_data = $this->session->userdata("student");
        $teacher_id = $student_session_data['teacher_id'];

        $incident_result = $this->SIM->get(  $id  ); 
        $data['incident'] = $incident_result;
        $data['id'] = $id;

        $incident_result = $this->SIM->get( null, $teacher_id); 
        $data['incidentlist'] = $incident_result;
        // $this->form_validation->set_rules('doa', 'Date of Admission', 'trim|required|xss_clean');
        $this->form_validation->set_rules('note', 'Note', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/student/incidentEdit', $data);
            $this->load->view('layout/teacher/footer', $data);
        } else {
            date_default_timezone_set('Asia/Manila');
            $todays_date = date('Y-m-d');
            $data = array(
                // 'quarter' => $this->input->post('quarter'),
                'id' => $id, 
                'student_id' => $this->input->post('student_id'), 
                'teacher_id' => $teacher_id,
                'type' => $this->input->post('case_type'),
                'note' => $this->input->post('note')   
            ); 
            $insert_id = $this->SIM->add($data);  

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Student Incident added successfully</div>');
            redirect('teacher/student/incident');
        }

    }

     function incident_delete($id) {
        $this->SIM->remove($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left"><i class="fa fa-check-square-o" aria-hidden="true"></i> Record Deleted Successfully</div>');
        redirect('teacher/student/incident');
    }
	
	 function getStudentClassID() {
		$class_id = null;
        $student_id = $this->input->get('student_id');
        $resultlist = $this->student_model->get($student_id);

		if( $resultlist ){
			$class_id = $resultlist['class_id'];
		}
		echo json_encode($class_id);
    }
	
	function update_strand(){
        $this->session->set_userdata('top_menu', 'Student');
        $this->session->set_userdata('sub_menu', 'student/student_strand');

        $class = $this->class_model->get();
        $data['classlist'] =  $class;
        $data['getquarter'] = $this->customlib->getQuarter();
        $data['class_id'] = "";
        $data['section_id'] = "";
        $data['quarter'] = "";
        $data['semester_id'] = "";
        $data['getSemester'] = $this->customlib->getSemester();
        $session = $this->session_model->getAllSession();
        $setting_result = $this->setting_model->get();
        $session_id = $setting_result[0]['session_id'];
        $data['session_id'] = $session_id;
        $data['sessionlist'] = $session;
          $data['set_department'] = 'senior';
        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');
        $this->form_validation->set_rules('semester_id', 'Semester', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/student/updateStrand', $data);
            $this->load->view('layout/teacher/footer', $data);
        } else {
            $resultlist = array();
            $class = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $semester_id = $this->input->post('semester_id');
            $session_id = $this->input->post('session_id');
            $data['class_id'] = $class;
            $data['session_id'] = $session_id;
            $data['section_id'] = $section;
            $data['semester_id'] = $semester_id;
            $data['strandlist'] = $this->strand_model->getStrandByClass( $class );
             $studentlist_male = $this->student_model->searchByClassSectionGender($class, $section, 'Male', $session_id );
            $studentlist_female = $this->student_model->searchByClassSectionGender($class, $section, 'Female', $session_id );
            $studentlist = array();
            $studentlist = array_merge( $studentlist, $studentlist_male );
            $studentlist = array_merge( $studentlist, $studentlist_female );
            $data['studentlist_male'] = $studentlist_male;
            $data['studentlist_female'] = $studentlist_female;
            $data['studentlist'] = $studentlist;
            $data['set_department'] = 'senior';
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/student/updateStrand', $data);
            $this->load->view('layout/teacher/footer', $data);
        }
    }
    
    function save_strand(){
        $session_id = $this->input->post('session_id');
        $student_id = $this->input->post('student_id');
        $strand_id = $this->input->post('strand_id'); 
        $semester_id = $this->input->post('semester_id');
        
        if( $student_id ){
            $data = array(
                'student_id' => $student_id,
                'strand_id' => $strand_id,
                'semester_id' => $semester_id,
                'session_id' => $session_id
            );
            
            $this->studentstrand_model->add( $data );
            
            
        }
        
        $get_latest_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id , $session_id );
        if( $get_latest_strand ){
            $strand_id = $get_latest_strand['strand_id'];
        }
        $strand_details = $this->strand_model->get( $strand_id );
        
        echo json_encode($strand_details);
    }
    
    function view_grade_coordinator() {
        $setting_result = $this->setting_model->get();
        $current_session = $setting_result[0]['session_id'];
        $session_name = $setting_result[0]['session'];
        $data['session_name'] = $session_name;
        $id = $this->input->post('student_id');
        $session_id = $this->input->post('session_id');
        $semester_id = $this->input->post('semester');
           $deparment = $this->input->post('deparment');
        $data['session_id'] = isset( $session_id )?$session_id: $current_session ;
        $data['title'] = 'Student List';
        $student = $this->student_model->getbySession($id, $session_id);
        $data['class_id'] = $student['class_id'];
        $data['student'] = $student;
        $data['student_id'] = $id;
        $data['semester_id'] = $semester_id;
        $getStudentSubject = $this->subject_model->getSubjctByClass( $student['class_id'] );
        $data['list_of_subjects'] = $getStudentSubject;
        $data['enableStrand'] = $this->strand_model->checkEnable( $student['class_id'] );
        $data['getStrand'] = $this->strand_model->get( $student['strand_id'] );
        $data['strand_id'] = $student['strand_id'];
        $getConductSubject = $this->conductsession_model->getDetailByclassAndSection( $student['class_id'],  $student['section_id'], $session_id );
        $data['getConductSubject'] = $getConductSubject;
        $data['section_id'] = $student['section_id'];
        $data['class_id'] = $student['class_id'];
        $data['sessionlist'] = $this->studentsession_model->getAllStudentsSession( $id );
         $data['deparment'] = $deparment;

        $this->load->view('layout/teacher/header', $data);
        $this->load->view('teacher/student/studentGrade', $data);
        $this->load->view('layout/teacher/footer', $data);
    }

     function junior(){
        $this->session->set_userdata('top_menu', 'Student');
        $this->session->set_userdata('sub_menu', 'student/junior');

        $class = $this->class_model->get();
        $data['classlist'] =  $class;
        $data['getquarter'] = $this->customlib->getQuarter();
        $data['class_id'] = "";
        $data['section_id'] = "";
        $data['quarter'] = "";
        $data['semester_id'] = "";
        $session = $this->session_model->getAllSession();
        $setting_result = $this->setting_model->get();
        $session_id = $setting_result[0]['session_id'];
        $data['session_id'] = $session_id;
        $data['sessionlist'] = $session;
        $data['set_department'] = 'junior';
        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/student/junior', $data);
            $this->load->view('layout/teacher/footer', $data);
        } else {
            $resultlist = array();
            $class = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $session_id = $this->input->post('session_id');
            $data['class_id'] = $class;
            $data['session_id'] = $session_id;
            $data['section_id'] = $section;
               $data['semester_id'] = "";
             $studentlist_male = $this->student_model->searchByClassSectionGender($class, $section, 'Male', $session_id );
            $studentlist_female = $this->student_model->searchByClassSectionGender($class, $section, 'Female', $session_id );
            $studentlist = array();
            $studentlist = array_merge( $studentlist, $studentlist_male );
            $studentlist = array_merge( $studentlist, $studentlist_female );
            $data['studentlist_male'] = $studentlist_male;
            $data['studentlist_female'] = $studentlist_female;
            $data['studentlist'] = $studentlist;
             $data['set_department'] = 'junior';
            
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/student/junior', $data);
            $this->load->view('layout/teacher/footer', $data);
        }
    }


}

?>
