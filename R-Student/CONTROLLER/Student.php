<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class student extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('smsgateway');
        $this->load->library('imageresize');
        $this->load->library('excel');
        $this->load->helper('file');
        $this->lang->load('message', 'english');
        $this->role;
        $this->load->library('auth');
		$this->load->model('specializedsubject_model');
		$this->load->model('strand_model');
		$this->load->model('customsubject_model');
        $this->load->model('gradingsetting_model'); 
		$this->load->model('archiveexamresult_model');	
		$this->load->model('subjectmanager_model');	
		$this->load->model('subjectcombine_model');	
		$this->load->model('conductsession_model');	
		$this->load->model('conductimportbatchdetail_model');	
		$this->load->model('studentstrand_model');	
		$this->load->model('customsubject_model');
        $this->load->model('conductimportsubject_model');
        $this->auth->is_logged_in_registrar();
    }

    function index() {
        $data['title'] = 'Student List';
        $student_result = $this->student_model->get();
        $data['studentlist'] = $student_result;
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/student/studentList', $data);
        $this->load->view('layout/registrar/footer', $data);
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
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/studentReport', $data);
            $this->load->view('layout/registrar/footer', $data);
        } else {
            $this->form_validation->set_rules('class_id', 'Class', 'required');
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('layout/registrar/header', $data);
                $this->load->view('registrar/student/studentReport', $data);
                $this->load->view('layout/registrar/footer', $data);
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
                    $this->load->view('layout/registrar/header', $data);
                    $this->load->view('registrar/student/studentReport', $data);
                    $this->load->view('layout/registrar/footer', $data);
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
       
     /*   $examList = $this->examschedule_model->getExamByClassandSection($student['class_id'], $student['section_id']);
        $data['examSchedule'] = array();
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
        }*/
        $student_doc = $this->student_model->getstudentdoc($id);
        $data['student_doc'] = $student_doc;
        $data['student_doc_id'] = $id;
        $category_list = $this->category_model->get();
        $data['category_list'] = $category_list;
        $data['gradeList'] = $gradeList;
        $data['student'] = $student;
		$data['student_id'] = $student['id'];
		//$getStudentSubject = $this->examresult_model->get_student_exam_result_by_subject( $student['id'] );
		$getStudentSubject = $this->subject_model->getSubjctByClass( $student['class_id'] );
		//$getStudentSubject = $this->examresult_model->get_student_exam_result_by_quarter( $student['id'] );
		//$getStudentSubjectshs = $this->examresult_model->get_student_exam_result_by_subject_shs( $student['id'] );
		$data['list_of_subjects'] = $getStudentSubject;
		//$data['list_of_subjects_shs'] = $getStudentSubjectshs;
		$data['enableStrand'] = $this->strand_model->checkEnable( $student['class_id'] );
		$data['strand_id'] = $student['strand_id'];
		$data['getStrand'] = $this->strand_model->get( $student['strand_id'] );
		$getConductSubject = $this->conductsession_model->getDetailByclassAndSection( $student['class_id'],  $student['section_id'], $session_id );
		$data['getConductSubject'] = $getConductSubject;
		$data['section_id'] = $student['section_id'];
		$data['class_id'] = $student['class_id'];
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/student/studentShow', $data);
        $this->load->view('layout/registrar/footer', $data);
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

    /*function delete($id) {
        $this->student_model->remove($id);
        $this->session->set_flashdata('msg', '<i class="fa fa-check-square-o" aria-hidden="true"></i> Record Deleted Successfully');
        redirect('registrar/student/search');
    }*/

    function doc_delete($id, $student_id) {
        $this->student_model->doc_delete($id);
        $this->session->set_flashdata('msg', '<i class="fa fa-check-square-o" aria-hidden="true"></i> Document Deleted Successfully');
        redirect('registrar/student/view/' . $student_id);
    }

    function create() {

        $this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'student/create');
        $genderList = $this->customlib->getGender();
        
         $bloodList = array( '0','0 -', '0 +','A','A -', 'A +',  'B', 'B -', 'B +',  'AB -', 'AB +');
       $religionList = array( 'Roman Catholic', 'Islam', 'Evangelical', 'INC',  'SDA','Protestant','Bible Baptist Church','United Church of Christ in the Philippines','Jehovahs Witnesses');
         
        /* First variation number of the admissio number
         $this->db->select_max('admission_no');
        $result = $this->db->get('students')->row(); 
        $admission_no_code_1 = date('Ymd'); 
        $admission_no_code_2 = str_pad( $result->admission_no+1,6,'0', STR_PAD_LEFT);
        $admission_no_code_2 = substr($admission_no_code_2, -6);
        $admission_no_code = $admission_no_code_1.'-'.$admission_no_code_2; */ 

        $result = $this->db->select('admission_no')->order_by('admission_no','desc')->limit(1)->get('students')->row('admission_no');
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
  
        $data['genderList'] = $genderList;
        $data['admission_no_code'] = $admission_no_code;
        $data['bloodList'] = $bloodList;
        $data['religionList'] = $religionList;
        $data['title'] = 'Add Student';
        $data['title_list'] = 'Recently Added Student';
        $session = $this->setting_model->getCurrentSession();
        $student_result = $this->student_model->getRecentRecord();
        $data['studentlist'] = $student_result;

        $barangay = $this->address_model->get_barangay();
        $city = $this->address_model->get_city();
        $province = $this->address_model->get_province();

        $data['barangaylist'] = $barangay;
        $data['citylist'] = $city;
        $data['provincelist'] = $province;
 
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $category = $this->category_model->get();
        $data['categorylist'] = $category;
        $vehroute_result = $this->vehroute_model->get();
 
        $data['vehroutelist'] = $vehroute_result;
		$data['strands'] = $this->strand_model->get();
        $data['studentstatuslist'] = $this->customlib->getStudentStatus();
        $data['hostelroomlist'] = $this->hostelroom_model->get();
        $data['gender'] = 'Male';
        $data['status'] = 'Active';
        /*$this->form_validation->set_rules('firstname', 'First Name', 'trim|required|xss_clean');
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
        $this->form_validation->set_rules('guardian_lastname', 'Guardian Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('father_lastname', 'Father Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('mother_lastname', 'Mother Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('blood_type', 'Blood Type', 'trim|required|xss_clean');

        $this->form_validation->set_rules('guardian_barangay_id', 'Barangay', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_city_id', 'City', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_province_id', 'Province', 'trim|required|xss_clean');
        $this->form_validation->set_rules('lunch_pass', 'Lunch Pass', 'trim|required|xss_clean');
        $this->form_validation->set_rules('commuter_pass', 'Commuter Pass', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_is', 'Guardian Is', 'trim|required|xss_clean');*/

        $this->form_validation->set_rules('firstname', 'First Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('dob', 'Date of Birth', 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('guardian_name', 'Guardian Name', 'trim|required|xss_clean'); 
        //$this->form_validation->set_rules('government_voucher', 'Government Voucher', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('guardian_phone', 'Guardian Phone', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('guardian_lastname', 'Guardian Last Name', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('lunch_pass', 'Lunch Pass', 'trim|required|xss_clean');
        $this->form_validation->set_rules('commuter_pass', 'Commuter Pass', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/studentCreate', $data);
            $this->load->view('layout/registrar/footer', $data);
        } else {
            $class_id = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');

            $fees_discount = $this->input->post('fees_discount');
            $vehroute_id = $this->input->post('vehroute_id');

            $father_phone = $this->input->post('father_phone');
            $admission_no = $this->input->post('admission_no');
            
            // Fix race condition: check if admission_no is already taken, if so generate the next one
            while (!empty($this->student_model->check_unique_admission($admission_no))) {
                $latest_admission = $this->db->select('admission_no')->order_by('admission_no','desc')->limit(1)->get('students')->row('admission_no');
                $pieces = explode('-', $latest_admission);
                $part2 = isset($pieces[1]) ? (int)$pieces[1] : 0; 
                $admission_no = date('Y') . '-' . str_pad($part2 + 1, 5, '0', STR_PAD_LEFT);
            }
            
            $father_phone = str_replace(' ', '', $father_phone);
            $mother_phone = $this->input->post('mother_phone');
            $mother_phone = str_replace(' ', '', $mother_phone);
            $guardian_phone = $this->input->post('guardian_phone');
            $guardian_phone = str_replace(' ', '', $guardian_phone);
            $data = array(
                'admission_no' => $admission_no,
                'roll_no' => $this->input->post('roll_no'), 
                'admission_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('admission_date'))),
                'firstname' => $this->input->post('firstname'),
                'lastname' => $this->input->post('lastname'),
                'mobileno' => $this->input->post('mobileno'),
                //'rte' => $this->input->post('rte'),
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
                //'government_voucher' => $this->input->post('government_voucher'),
                'blood_type' => $this->input->post('blood_type'),
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
                //'esc_number' => $this->input->post('esc_number'),
                //'government_number' => $this->input->post('government_number'),
				//'strand_id' => $this->input->post('strand_id'),
                'status' => $this->input->post('studentstatus'),
                'dormitory' => $this->input->post('dormitory'),
                'hostel_id' => $this->input->post('dormitory_room'),
                'guardian_address2' => $this->input->post('guardian_address2'),
                'rfid_number' => $this->input->post('rfid_number'),
            );
            $insert_id = $this->student_model->add($data);
            $data_new = array(
                'student_id' => $insert_id,
                'class_id' => $class_id,
                'section_id' => $section_id,
                'session_id' => $session,
                'vehroute_id' => $vehroute_id,
                'fees_discount' => $fees_discount,
                'strand_id' => $this->input->post('strand_id')
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
                // $img_name = $insert_id . '.' . $fileInfo['extension'];
                $img_name = 'ST-'.$admission_no . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/student_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'image' => 'uploads/student_images/' . $img_name);
                $this->student_model->add($data_img);
				$get_image = 'uploads/student_images/'. $img_name;
				$this->imageresize->load( $get_image );
				$get_height = $this->imageresize->getHeight();
				$get_width = $this->imageresize->getWidth();
				// if( $get_height != '624' && $get_width != '464'){
				// 	 $this->imageresize->resize(464, 624);
               /* if( $get_height != '500' && $get_width != '375'){
                    $this->imageresize->resize(375, 500); 
					 $this->imageresize->save( $get_image);
				 }*/
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


            $result = $this->smsgateway->sentRegisterSMS($insert_id, $this->input->post('guardian_phone'));
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Student added Successfully</div>');
            redirect('registrar/student/create');
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
        redirect('registrar/student/view/' . $student_id);
    }

    function handle_upload() {
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts = array('jpg', 'jpeg', 'png');
            $temp = explode(".", $_FILES["file"]["name"]);
            $extension = end($temp);
			$extension = strtolower( $extension );
            if ($_FILES["file"]["error"] > 0) {
                $error .= "Error opening the file<br />";
            }
            if ($_FILES["file"]["type"] != 'image/gif' &&
                $_FILES["file"]["type"] != 'image/jpeg' &&
                $_FILES["file"]["type"] != 'image/png'  ) {
                $this->form_validation->set_message('handle_upload', 'File type not allowed');
                return false;
            }
            if (!in_array($extension, $allowedExts)) {
                $this->form_validation->set_message('handle_upload', 'Extension not allowed');
                return false;
            }
           /*  if ($_FILES["file"]["size"] > 102400) {
                $this->form_validation->set_message('handle_upload', 'File size shoud be less than 100 kB');
                return false;
            } */
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
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/import', $data);
            $this->load->view('layout/registrar/footer', $data);
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
            redirect('registrar/student/search');
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
        $data['title'] = 'Edit Student';
        $data['id'] = $id;
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
       /* $this->form_validation->set_rules('firstname', 'First Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('dob', 'Date of Birth', 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_name', 'Guardian Name', 'trim|required|xss_clean');
        // $this->form_validation->set_rules('rte', 'RTE', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('government_voucher', 'Government Voucher', 'trim|required|xss_clean');
        $this->form_validation->set_rules('blood_type', 'Blood Type', 'trim|required|xss_clean');
        $this->form_validation->set_rules('father_name', 'Father Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('mother_name', 'Mother Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_phone', 'Guardian Phone', 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', 'Image', 'callback_handle_upload');
        $this->form_validation->set_rules('guardian_lastname', 'Guardian Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('father_lastname', 'Father Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('mother_lastname', 'Mother Last Name', 'trim|required|xss_clean');
         $this->form_validation->set_rules('lunch_pass', 'Lunch Pass', 'trim|required|xss_clean');
        $this->form_validation->set_rules('commuter_pass', 'Commuter Pass', 'trim|required|xss_clean');
		$this->form_validation->set_rules('guardian_is', 'Guardian Is', 'trim|required|xss_clean');*/

        $this->form_validation->set_rules('firstname', 'First Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('dob', 'Date of Birth', 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('guardian_name', 'Guardian Name', 'trim|required|xss_clean'); 
       // $this->form_validation->set_rules('government_voucher', 'Government Voucher', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('guardian_phone', 'Guardian Phone', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('guardian_lastname', 'Guardian Last Name', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('lunch_pass', 'Lunch Pass', 'trim|required|xss_clean');
        $this->form_validation->set_rules('commuter_pass', 'Commuter Pass', 'trim|required|xss_clean');
$this->form_validation->set_rules('subsidized', 'Subsidized', 'trim|required|xss_clean');
$this->form_validation->set_rules('if_adventist', 'If Adventist', 'trim|required|xss_clean');
if ($this->input->post('subsidized') == 'yes') {
    $this->form_validation->set_rules('institution', 'Institution', 'trim|required|xss_clean');
}
if ($this->input->post('if_adventist') == 'yes') {
    $this->form_validation->set_rules('year_baptism', 'Year of Baptism', 'trim|required|xss_clean');
}
        
        if ($this->form_validation->run() == FALSE) { 
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/studentEdit', $data);
            $this->load->view('layout/registrar/footer', $data);
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
               // 'rte' => $this->input->post('rte'),
                'mobileno' => $this->input->post('mobileno'),
               // 'government_voucher' => $this->input->post('government_voucher'),
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
                'subsidized' => $this->input->post('subsidized'),
                'if_adventist' => $this->input->post('if_adventist'),
                'institution' => $this->input->post('institution'),
                'year_baptism' => $this->input->post('year_baptism'),
               // 'esc_number' => $this->input->post('esc_number'),
               // 'government_number' => $this->input->post('government_number'),
				//'strand_id' => $this->input->post('strand_id'),
                'status' => $this->input->post('studentstatus'),
                'dormitory' => $this->input->post('dormitory'),
                'hostel_id' => $this->input->post('dormitory_room'),
                'guardian_address2' => $this->input->post('guardian_address2'),
                'rfid_number' => $this->input->post('rfid_number'),
                 'school_name' => $this->input->post('school_name'),
                'school_address' => $this->input->post('school_address')
            
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
				// 	 $this->imageresize->resize(464, 624);
               /* if( $get_height != '500' && $get_width != '375'){
                    $this->imageresize->resize(375, 500); 
					 $this->imageresize->save( $get_image);
				 }*/
            }
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Student Record Updated successfully</div>');
            redirect('registrar/student/edit/'.$id);
        }
    }

     function search() {
		$this->session->set_userdata('top_menu', 'Student Information');
		$this->session->set_userdata('sub_menu', 'student/search');
		$data['title'] = 'Student Search';
        $session = $this->session_model->getAllSession();
        $setting_result = $this->setting_model->get();
        $session_id = $setting_result[0]['session_id'];
        $session_session_id = $this->session->userdata('grade_set_session');
        if( $session_session_id ){
            $session_id = $session_session_id;
        }
        $data['session_id'] = $session_id;
        $data['sessionlist'] = $session;

		$class = $this->class_model->get();
		$data['classlist'] = $class;
		$data['section_id'] = '';
        $data['class_id'] = '';
		$data['searchby'] = '';
		$button = $this->input->post('search');
		if ($this->input->server('REQUEST_METHOD') == "GET") {
			$this->load->view('layout/registrar/header', $data);
			$this->load->view('registrar/student/studentSearch', $data);
			$this->load->view('layout/registrar/footer', $data);
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
						$resultlist_female = $this->student_model->searchByClassSectionGender($class, $section, 'Female', $session_id );
						$data['resultlist_male'] = $resultlist_male;
						$data['resultlist_female'] = $resultlist_female;
					}
				} else if ($search == 'search_full') {
					$data['searchby'] = "text";
					$data['class_id'] = $this->input->post('class_id');
					$data['section_id'] = $this->input->post('section_id');
					$data['search_text'] = trim($this->input->post('search_text'));
					$resultlist = $this->student_model->searchFullText($search_text,'yes', $session_id);
					$data['resultlist'] = $resultlist;
				}
			}
			$data['enableStrand'] = $this->strand_model->checkEnable( $class );
			$this->load->view('layout/registrar/header', $data);
			$this->load->view('registrar/student/studentSearch', $data);
			$this->load->view('layout/registrar/footer', $data);
		}
    }
    
    // MARQUEZ
    function number() {
        $this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'student/number');
        
        $student = $this->student_model->getStudents();
        $data['students'] = $student;   
        $data['student_count'] = count($student); 

           
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/student/studentNumber', $data);
        $this->load->view('layout/registrar/footer', $data);
    
    }

    // END MARQUEZ


    function searchphoto() {
		$this->session->set_userdata('top_menu', 'Student Information');
		$this->session->set_userdata('sub_menu', 'student/searchphoto');
		$data['title'] = 'Student Search Photo';
		$class = $this->class_model->get();
		$data['classlist'] = $class;
		$data['section_id'] = '';
		$data['searchby'] = '';
		$button = $this->input->post('search');
		if ($this->input->server('REQUEST_METHOD') == "GET") {
			$this->load->view('layout/registrar/header', $data);
			$this->load->view('registrar/student/studentSearchPhoto', $data);
			$this->load->view('layout/registrar/footer', $data);
		} else {
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
						$resultlist_male = $this->student_model->searchByClassSectionGender($class, $section, 'Male');
						$resultlist_female = $this->student_model->searchByClassSectionGender($class, $section, 'Female');
						$data['resultlist_male'] = $resultlist_male;
						$data['resultlist_female'] = $resultlist_female;
					}
				} else if ($search == 'search_full') {
					$data['searchby'] = "text";
					$data['class_id'] = $this->input->post('class_id');
					$data['section_id'] = $this->input->post('section_id');
					$data['search_text'] = trim($this->input->post('search_text'));
					$resultlist = $this->student_model->searchFullTextPhoto($search_text);
					$data['resultlist'] = $resultlist;
				}
			}
			$data['enableStrand'] = $this->strand_model->checkEnable( $class );
			$this->load->view('layout/registrar/header', $data);
			$this->load->view('registrar/student/studentSearchPhoto', $data);
			$this->load->view('layout/registrar/footer', $data);
		}
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
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/student/uploadimage', $data);
        $this->load->view('layout/registrar/footer', $data);
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

    function junior() {
        $this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'student/junior');
        $data['title'] = 'Student Search';
        $class = $this->class_model->get();  
        $session_result = $this->session_model->getAllSession();
        $subjectlist = $this->subject_model->get();  
              
        $data['subjectlist'] = $subjectlist;
        $data['sessionlist'] = $session_result;
        $data['classlist'] = $class;
         
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/student/junioradd', $data);
        $this->load->view('layout/registrar/footer', $data);
         
    }   

  

    function generate_junior_grades() {
        $data['title'] = 'Import Student';
        $data['title_list'] = 'Recently Added Student';
        $class = $this->class_model->get();  
        $session_result = $this->session_model->getAllSession();
        $subjectlist = $this->subject_model->get();  
              
        $data['subjectlist'] = $subjectlist;
        $data['sessionlist'] = $session_result;
        $data['classlist'] = $class;
 
        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
        $this->form_validation->set_rules('session_id', 'Session', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('subject_id', 'Subject', 'trim|required|xss_clean'); 
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/junioradd', $data);
            $this->load->view('layout/registrar/footer', $data);
        } else {
            $class_id = $this->input->post('class_id');
            $session_id = $this->input->post('session_id'); 
            $subject_id = $this->input->post('subject_id'); 
            $parameters = array(
                'class_id' => $class_id, 
                'session_id' => $session_id, 
                'subject_id' => $subject_id 
            );
            
            $this->excel->generate_spreadsheet_secondary( $parameters ); 


        }


    }

    function import_junior_grades() {
        $data['title'] = 'Import Student';
        $data['title_list'] = 'Recently Added Student';
        $class = $this->class_model->get();  
        $session_result = $this->session_model->getAllSession();
        $data['sessionlist'] = $session_result;
        $data['classlist'] = $class;
 
        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
        $this->form_validation->set_rules('session_id', 'Session', 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject_id', 'Subject', 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', 'File', 'callback_handle_excel_upload');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/junioradd', $data);
            $this->load->view('layout/registrar/footer', $data);
        } else {
            $class_id = $this->input->post('class_id');
            $session_id = $this->input->post('session_id'); 
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $file = $_FILES['file']['tmp_name'];
 
                //load the excel library
                $this->load->library('excel'); 
                //read file from path
                $objPHPExcel = PHPExcel_IOFactory::load($file); 
                //get only the Cell Collection
                $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection(); 
                //extract to a PHP readable array format
                foreach ($cell_collection as $cell) {
                    $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                    $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                    $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue(); 
                    //The header will/should be in row 1 only. of course, this can be modified to suit your need.
                    if ($row == 1) {
                        $header[$row][$column] = $data_value;
                    } elseif ($row == 2) {
                        $sub_header[$row][$column] = $data_value;
                    } 
                     else {
                        $arr_data[$row][$column] = $data_value; 
                    }
                }
                 
                //send the data in an array format
                $import_student_data_header = $header;

                $class_text = $import_student_data_header[1]['A']; 
                $class_name = $import_student_data_header[1]['B'];
                $subject_text = $import_student_data_header[1]['C'];
                $subject_name = $import_student_data_header[1]['D']; 

                $subject_data = $this->subject_model->getbyname( $subject_name ); 
                $import_student_data_sub_header = $sub_header;
                $import_student_data = $arr_data; 

                if(  empty( $subject_data ) ){
                    $this->session->set_flashdata('msg', '<div class="alert alert-danger ">Students imported unsuccessfully. Subject does not exist.</div>');  
                } else { 
                    if(  $import_student_data ){
                        foreach ($import_student_data as $import_student_key => $import_student_value) {

                            $number = $import_student_value['A'];
                            $lastname = $import_student_value['B'];
                            $firstname = $import_student_value['C'];
                            $middlename = $import_student_value['D'];
                            $gender = $import_student_value['E'];
                            $first_quarter = isset($import_student_value['F']) ? $import_student_value['F'] : null;
                            $second_quarter = isset($import_student_value['G']) ? $import_student_value['G'] : null;
                            $third_quarter = isset($import_student_value['H']) ? $import_student_value['H'] : null;
                            $student_data = $this->student_model->searchbylastnameandfirstname( $lastname, $firstname, $number );
 
                            if(  (isset($first_quarter) || isset($second_quarter) || isset($third_quarter)) && !empty($student_data->id) ){  
                                $student_existing_data = $this->archiveexamresult_model->checkifexist($session_id, $class_id, $student_data->id, $subject_data->id );   
                                $data_new = array( 
                                    'class_id' => $class_id, 
                                    'session_id' => $session_id,
                                    'student_id' => $student_data->id,
                                    'subject_id' => $subject_data->id, 
                                    'subject_name' => $subject_name,
                                    'first' => $first_quarter,
                                    'second' => $second_quarter,
                                    'third' => $third_quarter,
                                    'fourth' => null,
                                );

                                if( $student_existing_data ){
                                    $existing_id = $student_existing_data->id;
                                    $data_new_with_id = array( 
                                        'id' => $existing_id 
                                    );
                                    $data_new = array_merge($data_new, $data_new_with_id);
                                }
                                    
                                $this->archiveexamresult_model->add($data_new);
                                # code...
                            }
                        }
                      
                    }
                     $this->session->set_flashdata('msg', '<div class="alert alert-success ">Students imported successfully</div>'); 
                }

                
            } else {
                 $this->session->set_flashdata('msg', '<div class="alert alert-danger">Students imported unsuccessfully. File error please check the file uploaded file again.</div>');
           
            } 

            redirect('registrar/student/junior');
        }


    }

    function handle_excel_upload() {
        $error = "";
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
           
            $allowedExts = array('xls', 'xlsx');
            $mimes = array('text/xls','text/xlsx',
            'text/plain',
            'application/csv',
            'application/xls',
            'application/xlsx',
            'text/comma-separated-values', 
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
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
                $this->form_validation->set_message('handle_excel_upload', 'File type not allowed');
                return false;
            }
            if (!in_array($extension, $allowedExts)) {
                $error .= "Error opening the file<br />";
                $this->form_validation->set_message('handle_excel_upload', 'Extension not allowed');
                return false;
            }
            if ($error == "") {
                return true;
            }
        } else {
            $this->form_validation->set_message('handle_excel_upload', 'Please Select file');
            return false;
        }
    }
	
	function getsearchstudent(){
        $search_student = $this->input->post('search_student'); 
        $resultlist = $this->student_model->searchFullText($search_student);
        // echo  $resultlist ;   
        echo json_encode($resultlist);  
        # code...
    }
	
	function getsearchstudentname(){
        $search_student = $this->input->post('search_student'); 
        $include_inactive = $this->input->post('include_inactive'); 
        $resultlist = $this->student_model->searchFullStudentText($search_student, $include_inactive );
        // echo  $resultlist ;   
        echo json_encode($resultlist);  
        # code...
    }
	
	function getcurrentgradebystudentid(){
        $search_student = $this->input->post('search_student'); 
        $resultlist = $this->student_model->get($search_student);
		$data['get_section'] = $resultlist['section'];
		$data['get_class'] = $resultlist['class'];
        // echo  $resultlist ;   
        echo json_encode($resultlist);  
        # code...
    }
	
	function getmatchfirstlast(){
        $firstname = $this->input->post('firstname'); 
        $lastname = $this->input->post('lastname'); 
        $resultlist = $this->student_model->getmatchfirstlast($firstname,$lastname);
        // echo  $resultlist ;   
        echo json_encode($resultlist);  
        # code...
    }
	
	function enrolled() {
        $this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'student/enrolled'); 
		$session_id = $this->input->post('session_id');
		
        $data['title'] = 'Student List';
		$session_result = $this->session_model->getAllSession();
        $data['sessionlist'] = $session_result;$setting_result = $this->setting_model->get();
		$current_session_id = $setting_result[0]['session_id'];
		if(empty( $session_id )){
			$session_id = $current_session_id;
		}
        $student_result = $this->student_model->getBySession(null,$session_id); 
        $data['studentlist'] = $student_result;
		
		$data['session_id'] = $session_id;
	
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/student/studentEnrolled', $data);
        $this->load->view('layout/registrar/footer', $data);
    }
	
	
	function enrolled_today() {
        $this->session->set_userdata('top_menu', 'Student Information'); 
        $this->session->set_userdata('sub_menu', 'student/enrolled_today');
        $data['title'] = 'Student List';
        $student_result = $this->student_model->getenrolledtoday(); 
        $data['studentlist'] = $student_result;
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/student/studentEnrolledtoday', $data);
        $this->load->view('layout/registrar/footer', $data);
    }
	
	 function remove_session($id) {
		$this->studentsession_model->remove($id);
		$this->session->set_flashdata('msg', '<div class="alert alert-success"><i class="fa fa-check-square-o" aria-hidden="true"></i> Data Deleted Successfully</div>');
		redirect('registrar/student/enrolled');
	}
	
	 function get_student() {
        $data['title'] = 'Student Search';
        $class = $this->class_model->get();
		$class_id = $this->input->get('class');
        $data['classlist'] = $class;
        $data['class_id'] = $class_id;
        $data['gender'] = '';
        $data['section_id'] = '';
        if ($this->input->server('REQUEST_METHOD') == "GET") {
			$resultlist = $this->student_model->searchByClassSectionGender($class_id, '', 'Male', null, 'TRUE');
			$resultlist1 = $this->student_model->searchByClassSectionGender($class_id, '', 'Female',null, 'TRUE');
			$resultlist = array_merge( $resultlist, $resultlist1);
			$data['resultlist'] = $resultlist;
			
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/studentFilter', $data);
            $this->load->view('layout/registrar/footer', $data);
        } else {
            $class_id = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $gender = $this->input->post('gender');
			if( $gender ){
				$resultlist = $this->student_model->searchByClassSectionGender($class_id, $section_id, $gender,null, 'TRUE');
			} else {
				$resultlist = $this->student_model->searchByClassSectionGender($class_id, $section_id, 'Male',null,'TRUE');
				$resultlist1 = $this->student_model->searchByClassSectionGender($class_id, $section_id, 'Female',null, 'TRUE');
				$resultlist = array_merge( $resultlist, $resultlist1);
			}
			$data['resultlist'] = $resultlist;
			$data['class_id'] = $class_id;
			$data['section_id'] = $section_id;
			$data['gender'] = $gender;
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/studentFilter', $data);
            $this->load->view('layout/registrar/footer', $data);
        }
    }
	
	 function getStudentBySession() {
        $session_id = $this->input->get('session_id');
        $resultlist = $this->student_model->getStudentFullnameBySession($session_id);
        echo json_encode($resultlist);
	 }

      function getLatestStudentInformation(){
        $student_id = $this->input->get('student_id');
        $resultlist = $this->student_model->getLatestStudentInformation($student_id);
        echo json_encode($resultlist);
     }

      function lrn() {
        $this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'student/lrn');
        $data['title'] = 'Student LRN';
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $data['section_id'] = '';
        $data['searchby'] = '';
        $button = $this->input->post('search');
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/studentLRN', $data);
            $this->load->view('layout/registrar/footer', $data);

        } else {

            $class = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $search = $this->input->post('search');
            $search_text = $this->input->post('search_text');
            $lrn = $this->input->post('lrn');
            $student_id = $this->input->post('student_id');
            if( count( $student_id ) > 0 ){
                for( $x=0;$x<count( $student_id);$x++){
                    $get_student_id = $student_id[$x];
                    $get_lrn = $lrn[$x];

                    $update_array[] = array(
                        'id' => $get_student_id,
                        'lrn' => $get_lrn
                    );
                }
                if( count($update_array) > 0 ){
                    $this->db->update_batch('students', $update_array, 'id');
                    $this->session->set_flashdata('msg', '<i class="fa fa-check-square-o" aria-hidden="true"></i> Data Updated Successfully');
                }
               
            }
            $data['searchby'] = "filter";
            $data['class_id'] = $this->input->post('class_id');
            $data['section_id'] = $this->input->post('section_id');
            $resultlist_male = $this->student_model->searchByClassSectionGender($class, $section, 'Male');
            $resultlist_female = $this->student_model->searchByClassSectionGender($class, $section, 'Female');
            $data['resultlist_male'] = $resultlist_male;
            $data['resultlist_female'] = $resultlist_female;
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/studentLRN', $data);
            $this->load->view('layout/registrar/footer', $data);
        }
    }


      
       function modality() {
        $this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'student/modality');
        $data['title'] = 'Student LRN';
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $data['section_id'] = '';
        $data['searchby'] = '';
        $data['modalityList'] = array( 'online'=>'Online','modular_a'=>'Modular A','modular_b'=>'Modular B',
            'face_to_face' => 'Face to Face');
        $session = $this->session_model->getAllSession();
        $setting_result = $this->setting_model->get();
        $data['session_id'] = $setting_result[0]['session_id'];
        $data['sessionlist'] = $session;
        $data['class_id'] = '';
        $button = $this->input->post('search');
         $this->form_validation->set_rules('class_id', 'Incoming Grade', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) { 
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/studentModality', $data);
            $this->load->view('layout/registrar/footer', $data);

        } else {

            $class = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $search = $this->input->post('search');
            $session_id = $this->input->post('session_id');
            $search_text = $this->input->post('search_text');
            $modality_1 = $this->input->post('modality_1');
            $modality_2 = $this->input->post('modality_2');
            $modality_3 = $this->input->post('modality_3');
            $modality_4 = $this->input->post('modality_4');
            $student_id = $this->input->post('student_id');
            $student_session_id = $this->input->post('student_session_id');
            $email = $this->input->post('email');
            $guardian_phone = $this->input->post('guardian_phone');
            if( count( $student_id ) > 0 ){
                foreach( $student_id as $key => $value ){
                    $get_student_id = $value;
                    $get_student_session_id = $student_session_id[$get_student_id];
                    $get_modality_1 = $modality_1[$get_student_id];
                    $get_modality_2 = $modality_2[$get_student_id];
                    $get_modality_3 = $modality_3[$get_student_id];
                    $get_modality_4 = $modality_4[$get_student_id];
                    $get_email = $email[$get_student_id];
                    $get_guardian_phone = $guardian_phone[$get_student_id];

                    $update_array_session[] = array(
                        'id' => $get_student_session_id,
                        'modality' => serialize(array('1'=> $get_modality_1, '2'=>$get_modality_2,'3'=> $get_modality_3, '4' => $get_modality_4 )),
                    );

                    $update_array_student[] = array(
                        'id' => $get_student_id,
                        'email' => $get_email,
                        'guardian_phone' => $get_guardian_phone,
                    );
                }
                if( count($update_array_session) > 0 || count($update_array_student) > 0 ){
                    if( count($update_array_session) > 0 ){
                         $this->db->update_batch('student_session', $update_array_session, 'id');
                    }
                    if( count($update_array_student) > 0 ){
                        $this->db->update_batch('students', $update_array_student, 'id');
                    }
                   
                    
                    $this->session->set_flashdata('msg', '<i class="fa fa-check-square-o" aria-hidden="true"></i> Data Updated Successfully');
                }
               
            }
            $data['searchby'] = "filter";
            $data['class_id'] = $this->input->post('class_id');
            $data['section_id'] = $this->input->post('section_id');
            $data['session_id'] = $this->input->post('session_id');

            $resultlist_male = $this->student_model->searchByClassSectionGender($class, $section, 'Male', $session_id );
            $resultlist_female = $this->student_model->searchByClassSectionGender($class, $section, 'Female',$session_id );
            $data['resultlist_male'] = $resultlist_male;
            $data['resultlist_female'] = $resultlist_female;
             $data['modalityList'] = array( 'online'=>'Online','modular_a'=>'Modular A','modular_b'=>'Modular B',
            'face_to_face' => 'Face to Face');
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/studentModality', $data);
            $this->load->view('layout/registrar/footer', $data);
        }
    }


     function esc() {
        $this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'student/esc');
        $data['title'] = 'Student ESC Grantee/Government Voucher';
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $data['section_id'] = '';
        $data['searchby'] = '';
        $session = $this->session_model->getAllSession();
        $setting_result = $this->setting_model->get();
        $data['session_id'] = $setting_result[0]['session_id'];
        $data['sessionlist'] = $session;
        $data['class_id'] = '';
        $button = $this->input->post('search');
         $this->form_validation->set_rules('class_id', 'Incoming Grade', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) { 

            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/updateEsc', $data);
            $this->load->view('layout/registrar/footer', $data);

        } else {
            $class = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $search = $this->input->post('search');
            $session_id = $this->input->post('session_id');
            $search_text = $this->input->post('search_text');
            $student_id = $this->input->post('student_id');
            $student_session_id = $this->input->post('student_session_id');
            $esc_array = $this->input->post('esc_array');
            $gov_array = $this->input->post('gov_array');
            $esc_no_array = $this->input->post('esc_no_array');
            $gov_no_array = $this->input->post('gov_no_array');
            if( count( $student_id ) > 0 ){
                foreach( $student_id as $key => $value ){
                    $get_student_id = $value;
                    $get_student_session_id = $student_session_id[$get_student_id];
                    $esc_grantee = $esc_array[$get_student_id];
                    $gov_voucher = $gov_array[$get_student_id];
                    $esc_no = $esc_no_array[$get_student_id];
                    $gov_no = $gov_no_array[$get_student_id];
                   

                    $update_array_session[] = array(
                        'id' => $get_student_session_id,
                        'esc_grantee' => $esc_grantee,
                        'gov_voucher' => $gov_voucher,
                        'esc_no' => $esc_no,
                        'gov_no' => $gov_no,
                    );

                }
                if( count($update_array_session) > 0 ){
                    if( count($update_array_session) > 0 ){
                         $this->db->update_batch('student_session', $update_array_session, 'id');
                    }
                    
                    $this->session->set_flashdata('msg', '<i class="fa fa-check-square-o" aria-hidden="true"></i> Data Updated Successfully');
                }
               
            }
            $data['searchby'] = "filter";
            $data['class_id'] = $this->input->post('class_id');
            $data['section_id'] = $this->input->post('section_id');
            $data['session_id'] = $this->input->post('session_id');

            $resultlist_male = $this->student_model->searchByClassSectionGender($class, $section, 'Male', $session_id );
            $resultlist_female = $this->student_model->searchByClassSectionGender($class, $section, 'Female',$session_id );
            $data['resultlist_male'] = $resultlist_male;
            $data['resultlist_female'] = $resultlist_female;
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/updateEsc', $data);
            $this->load->view('layout/registrar/footer', $data);
        }
    }

     function get_sda_student() {
        $data['title'] = 'Student Search';
        $class = $this->class_model->get();
        $class_id = $this->input->get('class');
        $data['classlist'] = $class;
        $data['class_id'] = $class_id;
        $data['gender'] = '';
        $data['section_id'] = '';
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $resultlist = $this->student_model->getSDAbyGender($class_id, '', 'Male', null, 'TRUE');
            $resultlist1 = $this->student_model->getSDAbyGender($class_id, '', 'Female',null, 'TRUE');
            $resultlist = array_merge( $resultlist, $resultlist1);
            $data['resultlist'] = $resultlist;
            
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/studentSDA', $data);
            $this->load->view('layout/registrar/footer', $data);
        } else {
            $class_id = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $gender = $this->input->post('gender');
            if( $gender ){
                $resultlist = $this->student_model->getSDAbyGender($class_id, $section_id, $gender,null, 'TRUE');
            } else {
                $resultlist = $this->student_model->getSDAbyGender($class_id, $section_id, 'Male',null,'TRUE');
                $resultlist1 = $this->student_model->getSDAbyGender($class_id, $section_id, 'Female',null, 'TRUE');
                $resultlist = array_merge( $resultlist, $resultlist1);
            }
            $data['resultlist'] = $resultlist;
            $data['class_id'] = $class_id;
            $data['section_id'] = $section_id;
            $data['gender'] = $gender;
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/studentSDA', $data);
            $this->load->view('layout/registrar/footer', $data);
        }
    }

    function promote() {
        $this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'student/search');
        $data['title'] = 'Student Search';
        $session = $this->session_model->getAllSession();
        $setting_result = $this->setting_model->get();
        $session_id = $setting_result[0]['session_id'];
        $session_session_id = $this->session->userdata('grade_set_session');
        if( $session_session_id ){
            $session_id = $session_session_id;
        }
        $data['session_id'] = $session_id;
        $data['sessionlist'] = $session;

        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $data['section_id'] = '';
        $data['class_id'] = '';
        $data['searchby'] = '';
        $button = $this->input->post('search');
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/studentPromote', $data);
            $this->load->view('layout/registrar/footer', $data);
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
                        $resultlist_female = $this->student_model->searchByClassSectionGender($class, $section, 'Female', $session_id );
                        $data['resultlist_male'] = $resultlist_male;
                        $data['resultlist_female'] = $resultlist_female;
                    }
                } else if ($search == 'search_full') {
                    $data['searchby'] = "text";
                    $data['class_id'] = $this->input->post('class_id');
                    $data['section_id'] = $this->input->post('section_id');
                    $data['search_text'] = trim($this->input->post('search_text'));
                    $resultlist = $this->student_model->searchFullText($search_text,'yes', $session_id);
                    $data['resultlist'] = $resultlist;
                }
            }
            $data['enableStrand'] = $this->strand_model->checkEnable( $class );
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/student/studentPromote', $data);
            $this->load->view('layout/registrar/footer', $data);
        }
    }

}

?>
