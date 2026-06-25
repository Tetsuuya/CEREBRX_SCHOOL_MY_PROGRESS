<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class faculties extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('file');
        $this->load->library('upload');
        $this->load->library('zip'); 
        $this->lang->load('message', 'english');
        $this->role;
        $this->load->library('auth');
        $this->auth->is_logged_in_idproduction();
        $this->load->model('humanresource_model');
        $this->load->model('Events_model','events_model'); 
        $this->load->model('employee_model'); 
        date_default_timezone_set('Asia/Manila');
    }
    function index( $id=NULL, $role=NULL ) {
        $this->session->set_userdata('top_menu', 'Employee Record');
        $this->session->set_userdata('sub_menu', 'employee');
        $data['title'] = 'Employee List'; 
        $employee = $this->employee_model->getactive();  
        $data['employees'] = $employee;   
        $this->load->view('layout/idproduction/header', $data);
        $this->load->view('idproduction/employee/employeeList', $data);
        $this->load->view('layout/idproduction/footer', $data);
     }

    public function update_rfid_information() {
        $session_id = $this->setting_model->getCurrentSession();
        $employee_id = $this->input->post('employee_id');  
        $rfid_number = $this->input->post('rfid_number'); 
        
        // Validate inputs
        if (empty($employee_id) || empty($rfid_number)) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">Invalid input data.</div>');
            redirect('idproduction/faculties/');
            return;
        }
        
        $data = array(  
            'rfid_number' => $rfid_number 
        ); 
        
        $update = $this->employee_model->update_employee($data, $employee_id);  
        
        if ($update) {
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">RFID number updated successfully.</div>');
        } else {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">Failed to update RFID number.</div>');
        }
        
        redirect('idproduction/faculties/');
    }

    function inactive() {
        $this->session->set_userdata('top_menu', 'Employee Record');
        $this->session->set_userdata('sub_menu', 'employee');
        $data['title'] = 'Employee List'; 
        $employee = $this->employee_model->getincactive();  
        $data['employees'] = $employee;   
        $this->load->view('layout/idproduction/header', $data);
        $this->load->view('idproduction/employee/employeeList', $data);
        $this->load->view('layout/idproduction/footer', $data);
    }
    public function edit( $id ) {
        $this->session->set_userdata('top_menu', 'Faculties');
        $this->session->set_userdata('sub_menu', 'Faculties/index');
        $employee = $this->employee_model->get( $id );
        $data['employee'] = $employee; 
        $data['id'] = $id; 
        $this->form_validation->set_rules('name', 'Firstname', 'trim|required|xss_clean');
        $this->form_validation->set_rules('lastname', 'Lastname', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('sex', 'Gender', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('dob', 'Date of Birth', 'trim|required|xss_clean');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required|xss_clean');
        $this->form_validation->set_rules('address', 'Purok and Barangay', 'trim|required|xss_clean');
        $this->form_validation->set_rules('address2', 'City and Province', 'trim|required|xss_clean');
        $this->form_validation->set_rules('position', 'Position', 'trim|required|xss_clean');
        // $this->form_validation->set_rules('file', 'Image', 'callback_handle_upload');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/idproduction/header', $data);
            $this->load->view('idproduction/employee/employeeaddList', $data);
            $this->load->view('layout/idproduction/footer', $data);
        } else { 
            $data = array(
                'id' =>   $id  ,
                'name' => $this->input->post('name'),
                'middlename' => $this->input->post('middlename'), 
                'lastname' => $this->input->post('lastname'),  
                'email' => $this->input->post('email'),
                'sex' => $this->input->post('sex'),
                'dob' => date('Y-m-d', strtotime($this->input->post('dob'))),
                'phone' => $this->input->post('phone'),  
                'current_address' => $this->input->post('current_address'),  
                'current_address2' => $this->input->post('current_address2'),  
                'position' => $this->input->post('position'),
                'tin' => $this->input->post('tin'),
                'sss' => $this->input->post('sss'),
                'phic' => $this->input->post('phic'),
                'pagibig' => $this->input->post('pagibig'),
                'prc_no' => $this->input->post('prc_no'),
                'id_no' => $this->input->post('id_no') ,
                'date_hired' => $this->input->post('date_hired') ,
                'emergency' => $this->input->post('emergency'),
                'emergency_contact' => $this->input->post('emergency_contact')  ,
                'address' => $this->input->post('address'),
                'address2' => $this->input->post('address2'),
                'is_active' => $this->input->post('is_active')  
            );
            $id_no = $this->input->post('id_no');
            
            // e capture niyang old employee value (name,email,phone) para sa pag sync sa teacher table
            $old_employee = $this->employee_model->get( $id );

            $this->employee_model->add( $data );
            if (isset($_FILES["image"]) && !empty($_FILES['image']['name'])) {
                $fileInfo = pathinfo($_FILES["image"]["name"]);
                $img_name = $id_no. '.' . $fileInfo['extension'];
                if (file_exists( 'uploads/faculty_id/' . $img_name )) {
                   unlink( 'uploads/faculty_id/' . $img_name );
                } 
                move_uploaded_file($_FILES["image"]["tmp_name"], "./uploads/faculty_id/" . $img_name);
                $data_img = array('id' => $id, 'image' => 'uploads/faculty_id/' . $img_name);
                $this->employee_model->add($data_img); 
            }
            if (isset($_FILES["signature"]) && !empty($_FILES['signature']['name'])) {
                $fileInfo = pathinfo($_FILES["signature"]["name"]);
                $img_name = $id_no. '.' . $fileInfo['extension'];
                if (file_exists( 'uploads/faculty_signature/' . $img_name )) {
                   unlink( 'uploads/faculty_signature/' . $img_name );
                } 
                move_uploaded_file($_FILES["signature"]["tmp_name"], "./uploads/faculty_signature/" . $img_name);
                // added signature path
                $signature_path = 'uploads/faculty_signature/' . $img_name;
                $data_img = array('id' => $id, 'signature' => $signature_path);
                $this->employee_model->add($data_img); 

                // load model and sync uploaded signature path to the matching teacher in database using old employee details
                $this->load->model('idproduction_model');
                $this->idproduction_model->syncEmployeeSignatureToTeacher($id, $signature_path, $old_employee);
            }
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Employee successfully updated!</div>');
            
            // load idproduction_model and sync employee details to teachers database table using old employee details
            $this->load->model('idproduction_model');
            $this->idproduction_model->syncEmployeeToTeacher($id, $old_employee);

            // redirect('idproduction/faculties/edit/'.$id);
            redirect('idproduction/faculties/');
        }
    }
    // public function add() {
    //     $this->session->set_userdata('top_menu', 'Employee Record');
    //     $this->session->set_userdata('sub_menu', 'employee/add');
    //     $allusers = $this->facultystaffdtrlog_model->getallusers();
    //     $data['allusersList'] = $allusers; 
    //     $account_type = array( 'teacher', 'accountant',  'librarian',  'academichead',  'principal',  'registrar',  'clinic',  'guidance',  'prefect',  'coordinator',  'subadmin',  'hr',  'dormitorydean'  );
    //     $data['accounttypeList'] = $account_type;
    //     $genderList = $this->customlib->getGender();
    //     $data['genderList'] = $genderList;
    //     $this->form_validation->set_rules('name', 'Employee', 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('dob', 'Date of Birth', 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('phone', 'Phone', 'trim|required|xss_clean');
    //     // $this->form_validation->set_rules('file', 'Image', 'callback_handle_upload');
    //     if ($this->form_validation->run() == FALSE) {
    //         $this->load->view('layout/idproduction/header', $data);
    //         $this->load->view('idproduction/employee/employeeaddList', $data);
    //         $this->load->view('layout/idproduction/footer', $data);
    //     } else { 
    //         $data = array(
    //             'name' => $this->input->post('name'),
    //             'middlename' => $this->input->post('middlename'), 
    //             'lastname' => $this->input->post('lastname'),  
    //             'email' => $this->input->post('email'),
    //             'sex' => $this->input->post('gender'),
    //             'dob' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('dob'))),
    //             'address' => $this->input->post('address'),
    //             'phone' => $this->input->post('phone'), 
    //             'rfid_number' => 'requestforid', 
    //             'image' => 'uploads/'.$this->input->post('account_type').'_images/no_image.png',
    //         );
    //         if( $this->input->post('account_type')){
    //             if( $this->input->post('account_type') == "teacher"){ 
    //                 $insert_id = $this->teacher_model->add($data);  
    //                 $login_prefix = "teacher";
    //             } elseif( $this->input->post('account_type') == "accountant"){ 
    //                 $insert_id = $this->accountant_model->add($data);  
    //                 $login_prefix = "accountant";
    //             } elseif( $this->input->post('account_type') == "librarian"){ 
    //                 $insert_id = $this->librarian_model->add($data);  
    //                 $login_prefix = "librarian";
    //             } elseif( $this->input->post('account_type') == "academichead"){ 
    //                 $insert_id = $this->academichead_model->add($data);  
    //                 $login_prefix = "academichead";
    //             } elseif( $this->input->post('account_type') == "principal"){ 
    //                 $insert_id = $this->principal_model->add($data);  
    //                 $login_prefix = "principal";
    //             } elseif( $this->input->post('account_type') == "registrar"){ 
    //                 $insert_id = $this->registrar_model->add($data);  
    //                 $login_prefix = "registrar";
    //             } elseif( $this->input->post('account_type') == "clinic"){ 
    //                 $insert_id = $this->clinic_model->add($data);  
    //                 $login_prefix = "clinic";
    //             } elseif( $this->input->post('account_type') == "guidance"){ 
    //                 $insert_id = $this->guidance_model->add($data);  
    //                 $login_prefix = "guidance";
    //             } elseif( $this->input->post('account_type') == "prefect"){ 
    //                 $insert_id = $this->prefect_model->add($data);  
    //                 $login_prefix = "prefect";
    //             } elseif( $this->input->post('account_type') == "coordinator"){ 
    //                 $insert_id = $this->coordinator_model->add($data); 
    //                 $login_prefix = "coordinator"; 
    //             } elseif( $this->input->post('account_type') == "subadmin"){ 
    //                 $insert_id = $this->subadmin_model->add($data);  
    //                 $login_prefix = "subadmin";
    //             } elseif( $this->input->post('account_type') == "hr"){ 
    //                 $insert_id = $this->humanresource_model->add($data); 
    //                 $login_prefix = "hr"; 
    //             } elseif( $this->input->post('account_type') == "dormitorydean"){ 
    //                 $insert_id = $this->dormitorydean_model->add($data);  
    //                 $login_prefix = "dormitorydean";
    //             }
    //             $user_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
    //             $data_student_login = array(
    //                 'username' => $login_prefix . $insert_id,
    //                 'password' => $user_password,
    //                 'user_id' => $insert_id,
    //                 'role' => $this->input->post('account_type')
    //             );
    //             $this->user_model->add($data_student_login);
    //             if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
    //                 $fileInfo = pathinfo($_FILES["file"]["name"]);
    //                 $img_name = $insert_id . '.' . $fileInfo['extension'];
    //                 move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/".$login_prefix."_images/" . $img_name);
    //                 $data_img = array('id' => $insert_id, 'image' => 'uploads/'.$login_prefix.'_images/' . $img_name);
    //                 $this->accountant_model->add($data_img);
    //                  if( $this->input->post('account_type') == "teacher"){ 
    //                    $this->teacher_model->add($data_img);   
    //                 } elseif( $this->input->post('account_type') == "accountant"){ 
    //                    $this->accountant_model->add($data_img);   
    //                 } elseif( $this->input->post('account_type') == "librarian"){ 
    //                    $this->librarian_model->add($data_img);   
    //                 } elseif( $this->input->post('account_type') == "academichead"){ 
    //                    $this->academichead_model->add($data_img);   
    //                 } elseif( $this->input->post('account_type') == "principal"){ 
    //                    $this->principal_model->add($data_img);   
    //                 } elseif( $this->input->post('account_type') == "registrar"){ 
    //                    $this->registrar_model->add($data_img);   
    //                 } elseif( $this->input->post('account_type') == "clinic"){ 
    //                    $this->clinic_model->add($data_img);   
    //                 } elseif( $this->input->post('account_type') == "guidance"){ 
    //                    $this->guidance_model->add($data_img);   
    //                 } elseif( $this->input->post('account_type') == "prefect"){ 
    //                    $this->prefect_model->add($data_img);   
    //                 } elseif( $this->input->post('account_type') == "coordinator"){ 
    //                    $this->coordinator_model->add($data_img);  
    //                 } elseif( $this->input->post('account_type') == "subadmin"){ 
    //                    $this->subadmin_model->add($data_img);   
    //                 } elseif( $this->input->post('account_type') == "hr"){ 
    //                    $this->humanresource_model->add($data_img);  
    //                 } elseif( $this->input->post('account_type') == "dormitorydean"){ 
    //                    $this->dormitorydean_model->add($data_img);   
    //                 }
    //             }
    //             $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Accountant added successfully</div>');
    //             redirect('idproduction/employee/add');
    //         } else {
    //             $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">Employee Verification Unsuccessfull</div>');
    //             redirect('idproduction/employee/add');
    //         }
    //     }
    // }

    public function add() {
        $this->session->set_userdata('top_menu', 'Employee Record');
        $this->session->set_userdata('sub_menu', 'employee/add');

        
        $genderList = $this->customlib->getGender();
        $data['genderList'] = $genderList;

         
        $this->form_validation->set_rules('name', 'Firstname', 'trim|required|xss_clean');
        $this->form_validation->set_rules('lastname', 'Lastname', 'trim|required|xss_clean'); 
        if ($this->form_validation->run() == FALSE) {
            
            $this->load->view('layout/idproduction/header', $data);
            $this->load->view('idproduction/employee/employeeaddNew', $data);
            $this->load->view('layout/idproduction/footer', $data);
        } else { 
             
            $data = array( 
                'name' => $this->input->post('name'),
                'middlename' => $this->input->post('middlename'), 
                'lastname' => $this->input->post('lastname'),  
                'email' => $this->input->post('email'),
                'sex' => $this->input->post('sex'),
                'dob' => date('Y-m-d', strtotime($this->input->post('dob'))),
                'phone' => $this->input->post('phone'),  
                'address' => $this->input->post('address'),
                'address2' => $this->input->post('address2'),
                'position' => $this->input->post('position'),
                'tin' => $this->input->post('tin'),
                'sss' => $this->input->post('sss'),
                'phic' => $this->input->post('phic'),
                'pagibig' => $this->input->post('pagibig'),
                'prc_no' => $this->input->post('prc_no'),
                'id_no' => $this->input->post('id_no') ,
                'date_hired' => $this->input->post('date_hired') ,
                'emergency' => $this->input->post('emergency'),
                'emergency_contact' => $this->input->post('emergency_contact') ,
                'is_active' => $this->input->post('is_active') ,
            );

            
            if(  $this->input->post('id_no') ){
                $id_no = $this->input->post('id_no');
            } else {
                $lastname = $this->input->post('lastname');
                $lastname = str_replace(' ', '', $lastname);
                $lastname = strtolower( $lastname );
                $name = $this->input->post('name');
                $name = str_replace(' ', '', $name);
                $name = strtolower( $name );

                $id_no = $lastname.'_'.$name;
            }
            

            // store the generated insert ID to fix the undefined $id variable bug
            $id = $this->employee_model->add( $data );

            if (isset($_FILES["image"]) && !empty($_FILES['image']['name'])) {
                $fileInfo = pathinfo($_FILES["image"]["name"]);

                $img_name = $id_no. '.' . $fileInfo['extension'];
                if (file_exists( 'uploads/faculty_id/' . $img_name )) {
                   unlink( 'uploads/faculty_id/' . $img_name );
                } 
                move_uploaded_file($_FILES["image"]["tmp_name"], "./uploads/faculty_id/" . $img_name);
                $data_img = array('id' => $id, 'image' => 'uploads/faculty_id/' . $img_name);
                $this->employee_model->add($data_img); 
            }

            if (isset($_FILES["signature"]) && !empty($_FILES['signature']['name'])) {
                $fileInfo = pathinfo($_FILES["signature"]["name"]);

                $img_name = $id_no. '.' . $fileInfo['extension'];
                if (file_exists( 'uploads/faculty_signature/' . $img_name )) {
                   unlink( 'uploads/faculty_signature/' . $img_name );
                } 
                move_uploaded_file($_FILES["signature"]["tmp_name"], "./uploads/faculty_signature/" . $img_name);
                // added signature path
                $signature_path = 'uploads/faculty_signature/' . $img_name;
                $data_img = array('id' => $id, 'signature' => $signature_path);
                $this->employee_model->add($data_img); 

                // added sync signature path
                $this->load->model('idproduction_model');
                $this->idproduction_model->syncEmployeeSignatureToTeacher($id, $signature_path);
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Accountant added successfully</div>');
            
            // [NEW CHANGES] Load idproduction_model and sync employee details to teachers database table
            $this->load->model('idproduction_model');
            $this->idproduction_model->syncEmployeeToTeacher($id);

            redirect('idproduction/faculties/add');
 
        }
    }
    function view($id, $role ) {
        $this->session->set_userdata('top_menu', 'Employee Record');
        $this->session->set_userdata('sub_menu', 'employee/add');
        $data['title'] = 'Academic Head List';
        $userinfo = $this->facultystaffdtrlog_model->getuserinfo( $id, $role );  
        $data['userinfolist'] = $userinfo;       
        $data['id'] = $id;       
        $data['role'] = $role;       
        $this->load->view('layout/idproduction/header', $data);
        $this->load->view('idproduction/employee/employeeShow', $data);
        $this->load->view('layout/idproduction/footer', $data);
    }
   function getlogindetail() {
        $user_id = $this->input->post('user_id');
        $user_role = $this->input->post('user_role');
        if(  $user_role == "teacher"){
          $resutlt = $this->user_model->getTeacherLoginDetails($user_id);
        }  elseif(  $this->input->post('user_role')  == "accountant"){
            $resutlt = $this->user_model->getAccountantLoginDetails($user_id);
        } elseif(  $this->input->post('user_role')  == "librarian"){
            $resutlt = $this->user_model->getLibrarianLoginDetails($user_id);
        } elseif( $this->input->post('user_role')  == "academichead"){ 
            $resutlt = $this->user_model->getAcademicheadLoginDetails($user_id);
        } elseif( $this->input->post('user_role') == "principal"){ 
            $resutlt = $this->user_model->getPrincipalLoginDetails($user_id);
        } elseif( $this->input->post('user_role') == "registrar"){ 
            $resutlt = $this->user_model->getRegistrarLoginDetails($user_id);
        } elseif( $this->input->post('user_role') == "clinic"){ 
            $resutlt = $this->user_model->getClinicLoginDetails($user_id);
        } elseif( $this->input->post('user_role') == "guidance"){ 
            $resutlt = $this->user_model->getguidanceLoginDetails($user_id);
        } elseif( $this->input->post('user_role') == "prefect"){ 
            $resutlt = $this->user_model->getpreficLoginDetails($user_id);
        } elseif( $this->input->post('user_role') == "coordinator"){ 
            $resutlt = $this->user_model->getCoordinatorLoginDetails($user_id);
        } elseif( $this->input->post('user_role') == "subadmin"){ 
            $resutlt = $this->user_model->getSubadminLoginDetails($user_id);
        } elseif( $this->input->post('user_role') == "hr"){ 
            $resutlt = $this->user_model->getHrLoginDetails($user_id);
        } elseif( $this->input->post('user_role') == "dormitorydean"){ 
            $resutlt = $this->user_model->getDormitorydeanLoginDetails($user_id);
        } 
        echo json_encode($resutlt);
    }
    function faculties_generatation( ) { 
        $this->db->select('id');   
        $this->db->select('lastname');   
        $this->db->select('name');   
        $this->db->from('employee');   
        $this->db->where('employee.is_active', "yes" ); 
        $query = $this->db->get();
        $data_array = $query->result_array();  
        if( $data_array ){
            foreach ($data_array as $key => $value) {
                // code...
                $id = $value['id'];
                $lastname = $value['lastname'];
                $name = $value['name'];
                $path = 'uploads/faculty_created_id/'.$lastname.', '.$name ;
                if (!file_exists($path)) {
                    mkdir( $path , 0777, true); 
                }
                $this->generate_front($id,  "no", $path);
                $this->generate_back($id, "no", $path);
            }
        }
    }
    // public function generate_zip( $student_id ){ 
    //     $path = 'uploads/id_created/batch/'.$student_id;
    //     if (!file_exists($path)) {
    //         mkdir( $path , 0777, true); 
    //     }
    //     $this->generate_front( $student_id ,  "yes" );
    //     $this->generate_back( $student_id ,  "yes" );
    //     $this->zip->clear_data();
    //     $this->zip->read_dir($path.'/', FALSE);
    //     // Add file
    //     // $this->zip->read_file($filepath1);
    //     // $this->zip->read_file($filepath2);
    //     // Download
    //     // $filename = "ARC_ID_BATCH_".$batch.".zip"; 
    //     $filename = "MVC_Student_".$student_id."_".date("Ymd_His").".zip";
    //     $this->zip->download($filename);
    //     $this->zip->clear_data();
    //     $this->deleteDir($path);
    // }
    // function deleteDir($dirPath) {
    //     if (! is_dir($dirPath)) {
    //         throw new InvalidArgumentException("$dirPath must be a directory");
    //     }
    //     if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
    //         $dirPath .= '/';
    //     }
    //     $files = glob($dirPath . '*', GLOB_MARK);
    //     foreach ($files as $file) {
    //         if (is_dir($file)) {
    //             self::deleteDir($file);
    //         } else {
    //             unlink($file);
    //         }
    //     }
    //     rmdir($dirPath);
    // }
    function generate_front($id, $auto_redirect="yes", $path=null) { 
        $this->db->from('employee');  
        $this->db->where('employee.id', $id ); 
        $query = $this->db->get();
        $data_array = $query->row_array();
        $date_today_for_issued = date('m/d/Y');
        // printx($data_array);
        if( $data_array ){ 
            $lastname = $data_array['lastname'];
            $firstname = $data_array['name'];
            $middlename = $data_array['middlename'];
            $watermark = $data_array['image'];
            $position = $data_array['position'];
            $secondary_position = $data_array['secondary_position'];
            $id_no = $data_array['id'];
            $email= $data_array['email'];
            $id = $data_array['date_hired'];
            $base_blank =  "uploads/id_template/base_blank.png"; 
            $image =  "uploads/id_template/Faculty_ID_TEMPLATE.png"; 
            $white  =  "uploads/id_template/white.jpg"; 
            if( file_exists($watermark)){ 
               if (exif_imagetype($base_blank) == IMAGETYPE_JPEG){ 
                    $base_blank = imagecreatefromjpeg($base_blank);
                    imagesavealpha($base_blank, true);
                } else {
                    $base_blank = imagecreatefrompng($base_blank);
                    imagesavealpha($base_blank, true);
                } 
                if (exif_imagetype($watermark) == IMAGETYPE_JPEG){ 
                    $watermark = imagecreatefromjpeg($watermark);
                    imagesavealpha($watermark, true);
                } else {
                    $watermark = imagecreatefrompng($watermark);
                    imagesavealpha($watermark, true);
                } 
                if (exif_imagetype($image) == IMAGETYPE_JPEG){ 
                    $image = imagecreatefromjpeg($image);
                    imagesavealpha($image, true);
                } else {
                    $image = imagecreatefrompng($image);
                    imagesavealpha($image, true);
                } 
                if (exif_imagetype($white) == IMAGETYPE_JPEG){ 
                    $white = imagecreatefromjpeg($white);
                    imagesavealpha($white, true);
                } else {
                    $white = imagecreatefrompng($white);
                    imagesavealpha($white, true);
                } 
                if (exif_imagetype($signature) == IMAGETYPE_JPEG){ 
                    $signature = imagecreatefromjpeg($signature);
                    imagesavealpha($signature, true);
                } else {
                    $signature = imagecreatefrompng($signature);
                    imagesavealpha($signature, true);
                } 
                $wm_x = imagesx($watermark);
                $wm_y = imagesy($watermark);
                $base_x = imagesx($base_blank);
                $base_y = imagesy($base_blank);  
                $image_x = imagesx($image);
                $image_y = imagesy($image); 
                $white_x = imagesx($white);
                $white_y = imagesy($white);  
                $signature_x = imagesx($signature);
                $signature_y = imagesy($signature);  
                if( $data_array['middlename'] && $data_array['middlename'] <> "-" && $data_array['middlename'] <> " " ){
                    $middlename_data = trim($data_array['middlename'] );
                    $middlename = $middlename_data[0].'.';
                    $lastname_line =  $lastname;
                    $firstname_line =  $firstname.' '.$middlename;
                    // $complete_name = $firstname.' '.$middlename.' '.$lastname; 
                    // $complete_name = $firstname.' '.$middlename.' '.$lastname; 
                    // $complete_name = $firstname.' '.$middlename.' '.
                    // $complete_name .= "\n";
                    // $complete_name .= $lastname; 
                } else {
                    $lastname_line =  $lastname;
                    $firstname_line =  $firstname;
                    // $complete_name = $firstname.' '.$lastname;
                }
                $font = "uploads/id_template/franklin.gothic.demi.cond.ttf";
                $font2 = "uploads/id_template/oswald.bold.ttf";
                $font_bold = "uploads/id_template/franklin.gothic.demi.cond.ttf"; 
                $font_italic= "uploads/id_template/oswaldheavyItalic800.ttf"; 
                $black = imagecolorallocate($base_blank, 0, 0, 0); 
                $white = imagecolorallocate($base_blank, 255, 255, 255); 
                $maroon = imagecolorallocate($base_blank, 127, 22, 24); 
                $yellowish = imagecolorallocate($base_blank, 255, 255, 204);
                $orange = imagecolorallocate($base_blank, 255, 137, 10); // for faculty positon like "principal"
                $blue = imagecolorallocate($base_blank, 135, 206, 235); // imagecolorallocate($base_blank, 0, 51, 204)
                $magenta = imagecolorallocate($base_blank, 102, 0, 51); // new color
                $dark_green = imagecolorallocate($base_blank, 0, 51, 0); //new color


                // Adjust the coordinates here
                $watermark_x = 200;
                $watermark_y = 350;
                $image_x_dest = 0;
                $image_y_dest = 0;

                // Image copy resized with the new coordinates
                imagecopyresized($base_blank, $watermark, $watermark_x, $watermark_y, 0, 0, 500, 500, $wm_x, $wm_y);  
                imagecopyresized($base_blank, $image, $image_x_dest, $image_y_dest, 0, 0, 890, 1377, $image_x, $image_y);  
                // imagecopyresized ($base_blank, $signature, 270,990, 0, 0, 300, 150, $signature_x, $signature_y); 

                $right_margin = 80; // Adjust this value as needed for your desired right margin

                // // Position
                // $ryb0x = imagettfbbox(40, 0, $font, strtoupper($position)); 
                // $rybaX = imagesx($base_blank) - $right_margin - ($ryb0x[2] - $ryb0x[0]);
                // $this->imagettfstroketext($base_blank, 40, 0, $rybaX, 1150, $magenta, $black, $font2, strtoupper($position), 0);

                // ID Number
                // imagettftext($base_blank, 40, 0,  540, 950, $dark_green, $font2, strtoupper( $id_no ) );

                // Last name
                $font_size_lastname = 68;
                $text_box_lastname = imagettfbbox($font_size_lastname, 0, $font, strtoupper($lastname_line . ","));
                $text_width_lastname = $text_box_lastname[2] - $text_box_lastname[0];
                $x_lastname = imagesx($base_blank) - $right_margin - $text_width_lastname;
                $y_lastname = 1060; // Y-coordinate for last name

                $this->imagettfstroketext($base_blank, $font_size_lastname, 0, $x_lastname, $y_lastname, $magenta, $black, $font2, strtoupper($lastname_line), 0);

                // First name
                $font_size_firstname = 53;
                $text_box_firstname = imagettfbbox($font_size_firstname, 0, $font, strtoupper($firstname_line));
                $text_width_firstname = $text_box_firstname[2] - $text_box_firstname[0];
                $x_firstname = imagesx($base_blank) - $right_margin - $text_width_firstname;
                $y_firstname = 1140; // Y-coordinate for first name

                $this->imagettfstroketext($base_blank, $font_size_firstname, 0, $x_firstname, $y_firstname, $magenta, $black, $font2, strtoupper($firstname_line), 0);

                // Assuming $id is a valid date-time string and $id_no is a numeric value
                $date_time = new DateTime($id);
                $year = $date_time->format('Y');
                $month = $date_time->format('m');
                // Ensure $id_no is properly formatted to four digits
                $formatted_id_no = str_pad($id_no, 4, '0', STR_PAD_LEFT);

                // Combine the year, month, '00', and formatted ID number
                $formatted_string = $year . $month . '' . $formatted_id_no;
                // Calculate the bounding box of the text
                $font_size_id = 40;
                $text_box_id = imagettfbbox($font_size_id, 0, $font2, strtoupper($formatted_string));
                $text_width_id = $text_box_id[2] - $text_box_id[0];
                $x_id = imagesx($base_blank) - $right_margin - $text_width_id;
                $y_id = 960; // Y-coordinate for ID number

                // Add the text to the image
                imagettftext($base_blank, $font_size_id, 0, $x_id, $y_id, $dark_green, $font2, strtoupper($formatted_string));


                
                if( $path ){
                     $save = $path."/".$id."_front.png"; 
                } else {
                    $save = "uploads/faculty_created_id/".$id."_front_".$lastname.".png"; 
                }
                imagepng( $base_blank ,  $save);
                // imagepng( $base_blank  );
                // exit();
                // $this->download_file_generated($save);
                imagedestroy($base_blank);
                if(  $auto_redirect == "yes"){
                    redirect( $save);
                }
            }
        }
    }
    
    function generate_back($id, $auto_redirect="yes", $path=null) {
        $this->db->from('employee');  
        $this->db->where('employee.id', $id ); 
        $query = $this->db->get();
        $data_array = $query->row_array(); 
        $date_today_for_issued = date('m/d/Y');
        if( $data_array ){ 
            $lastname = $data_array['lastname'];
            $firstname = $data_array['name'];
            $middlename = $data_array['middlename'];
            $id = $data_array['id'];  
            $signature = $data_array['signature'];
            $email= $data_array['email'];
            $date_of_birth = $data_array['dob'];  
            $date_hired = $data_array['date_hired'];  
            if( $date_hired  ){ 
                if( $date_hired <> "-"){ 
                    $date_hired_display = date('F j, Y',  strtotime($date_hired));
                } elseif( $date_hired != " "){ 
                    $date_hired_display = date('F j, Y',  strtotime($date_hired));
                } else { 
                    $date_hired_display = "";
                }
            } else { 
                $date_hired_display = "";
            }
            $date_of_birth_display = date('F j, Y',  strtotime($date_of_birth));
            $address = $data_array['address'];
            $address2 = $data_array['address2'];
            $sss = $data_array['sss'];
            $pagibig = $data_array['pagibig'];
            $philhealth = $data_array['phic'];
            $tin = $data_array['tin'];
            $prc = $data_array['prc_no'];
            $member_image = $data_array['image'];
            $emergency = $data_array['emergency'];
            $phone = $data_array['emergency_contact'];
            $watermark = $data_array['signature'];
            $image =  "uploads/id_template/facutly_back_ID TEMPLATE.png"; 
            $president_sig = 'uploads/principal_signatures/PASTOR_LANTAYA_SIG.png';
            $current_address = $data_array['current_address'];
            $current_address2 = $data_array['current_address2'];
            $overlay_color = false;
            if( file_exists($member_image)){ 
                $name = $firstname.' '.$middlename.' '.$lastname; 
                $qr_data = $name." \n".$date_of_birth_display."\n ".$email; 
                // $qrcode = 'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl='.urlencode($qr_data).'%2F&choe=UTF-8&chld=M|0';
                $qrcode = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data='.urlencode($qr_data).'%2F&choe=UTF-8&chld=M|0';

                $qrcode = imagecreatefrompng($qrcode);
                imagesavealpha($qrcode, true);
                $qc_x = imagesx($qrcode);
                $qc_y = imagesy($qrcode);

               if (exif_imagetype($base_blank) == IMAGETYPE_JPEG){ 
                    $base_blank = imagecreatefromjpeg($base_blank);
                    imagesavealpha($base_blank, true);
                } else {
                    $base_blank = imagecreatefrompng($base_blank);
                    imagesavealpha($base_blank, true);
                } 
               if (exif_imagetype($image) == IMAGETYPE_JPEG){ 
                    $image = imagecreatefromjpeg($image);
                    imagesavealpha($image, true);
                } else {
                    $image = imagecreatefrompng($image);
                    imagesavealpha($image, true);
                }
               if (exif_imagetype($watermark) == IMAGETYPE_JPEG){ 
                    $watermark = imagecreatefromjpeg($watermark);
                    imagesavealpha($watermark, true);
                } else {
                    $watermark = imagecreatefrompng($watermark);
                    imagesavealpha($watermark, true);
                }
                if (exif_imagetype($signature) == IMAGETYPE_JPEG){ 
                    $signature = imagecreatefromjpeg($signature);
                    imagesavealpha($signature, true);
                } else {
                    $signature = imagecreatefrompng($signature);
                    imagesavealpha($signature, true);
                } 
                $wm_x = imagesx($watermark);
                $wm_y = imagesy($watermark);
            
                $signature_x = imagesx($signature);
                $signature_y = imagesy($signature);  
                // imagecopyresized ($image, $watermark, 150,650, 0, 0, 374, 200, $wm_x, $wm_y); 
                // imagecopyresized ($image, $watermark, 180,700, 0, 0, 280, 120, $wm_x, $wm_y); 
                $font = "uploads/id_template/oswald.regular.ttf"; 
                $font_bold = "uploads/id_template/ARIALBD.TTF"; 
                $font_italic= "uploads/id_template/oswaldheavyItalic800.ttf"; 
                $font_century_gothic = "uploads/id_template/centurygothicregular.TTF"; 
                $black = imagecolorallocate($image, 0, 0, 0);
                $white = imagecolorallocate($image, 255, 255, 255);
                // $font = imageloadfont('Oswald/Oswald-VariableFont_wght.ttf'); 
                imagecopyresized ($image, $qrcode, 380 , 590, 0, 0, 230, 240,  $qc_x, $qc_y); //777
                // imagettftext( $image, 20, 0, 280, 155, $black, $font_century_gothic, strtoupper( $date_hired_display)); 

                // imagettftext( $image, 20, 0, 265, 46, $black, $font_century_gothic, strtoupper( $date_of_birth_display));
                // if( strlen($current_address) >= "26" ){  
                //     $ryb0x = imagettfbbox(23, 0, $font, strtoupper( $current_address ));
                //     $rybaX = $ryb0x[0] + (imagesx($image) / 2) - ($ryb0x[4] / 2); 
                //     imagettftext( $image, 17, 0, 265, 83, $black, $font_century_gothic,  strtoupper( $current_address )); 
                //     $ryb0x = imagettfbbox(23, 0, $font, strtoupper( $address2 ));
                //     $rybaX = $ryb0x[0] + (imagesx($image) / 2) - ($ryb0x[4] / 2); 
                //     imagettftext( $image, 17, 0, 265, 108, $black, $font_century_gothic,  strtoupper( $current_address2 ));
                // } else {  
                //     $ryb0x = imagettfbbox(25, 0, $font, strtoupper( $current_address ));
                //     $rybaX = $ryb0x[0] + (imagesx($image) / 2) - ($ryb0x[4] / 2); 
                //     imagettftext( $image, 20, 0, 265, 83, $black, $font_century_gothic,  strtoupper( $current_address ));
                //     $ryb0x = imagettfbbox(25, 0, $font, strtoupper( $address2 ));
                //     $rybaX = $ryb0x[0] + (imagesx($image) / 2) - ($ryb0x[4] / 2); 
                //     imagettftext( $image, 20, 0, 265, 108, $black, $font_century_gothic,  strtoupper( $current_address2 ));
                // }
                
                // Get the dimensions of the signature image
                $signature_width = imagesx($signature);
                $signature_height = imagesy($signature);

                imagecopy($image, $signature, 30, 620, 0, 0, $signature_width, $signature_height);

                // imagettftext( $image, 20, 0, 280, 223, $black, $font_century_gothic, strtoupper( $prc?$prc:"N/A"));
                imagettftext( $image, 20, 0, 265, 175, $black, $font_bold, strtoupper( $tin?$tin:"N/A"));
                imagettftext( $image, 20, 0, 265, 210, $black, $font_bold, strtoupper( $sss?$sss:"N/A"));
                imagettftext( $image, 20, 0, 265, 245, $black, $font_bold, strtoupper( $pagibig?$pagibig:"N/A"));  
                imagettftext( $image, 20, 0, 265, 280, $black, $font_bold, strtoupper( $philhealth?$philhealth:"N/A"));

                // for email
            
                $text_box = imagettfbbox(19, 0, $font, strtoupper($email));
                // Width of the text
                $text_width = $text_box[2] - $text_box[0];
                // Desired X-coordinate (e.g., left-aligned within the image)
                $desired_x = 40; // Adjust this value for your specific positioning
                // Calculate the X-coordinate for left alignment
                $x = $desired_x;
                // Y-coordinate for text position
                $y = 400; // Adjust as needed
                // Draw the text on the image
                imagettftext($image, 19, 0, $x, $y, $black, $font_bold, ($email));

                $president_sig_path = 'uploads/principal_signatures/PASTOR_LANTAYA_SIG.png';
                $president_sig = imagecreatefrompng($president_sig_path);

                // Get the dimensions of the signature image
                $psignature_width = imagesx($president_sig);
                $psignature_height = imagesy($president_sig);
                imagecopy($image, $president_sig, 30, 730, 0, 0, $psignature_width, $psignature_height);

                // printx(strlen($emergency));
                // if( strlen($emergency) >= "33" ){  
                //     $ryb0x = imagettfbbox(19, 0, $font, strtoupper( $emergency ));
                //     $rybaX = $ryb0x[0] + (imagesx($image) / 2) - ($ryb0x[4] / 2); 
                //     imagettftext( $image, 19, 0, $rybaX, 390, $black, $font, strtoupper( $emergency ));  
                // } elseif( strlen($emergency) >= "29" ){   
                //     $ryb0x = imagettfbbox(20, 0, $font, strtoupper( $emergency )); 
                //     $rybaX = $ryb0x[0] + (imagesx($image) / 2) - ($ryb0x[4] / 2);  
                //     imagettftext( $image, 20, 0, $rybaX, 390, $black, $font, strtoupper( $emergency ));   
                // } elseif( strlen($emergency) >= "27" ){   
                //     $ryb0x = imagettfbbox(21, 0, $font, strtoupper( $emergency )); 
                //     $rybaX = $ryb0x[0] + (imagesx($image) / 2) - ($ryb0x[4] / 2);  
                //     imagettftext( $image, 21, 0, $rybaX, 390, $black, $font, strtoupper( $emergency ));    
                // } else { 
                //     $ryb0x = imagettfbbox(25, 0, $font, strtoupper( $emergency )); 
                //     $rybaX = $ryb0x[0] + (imagesx($image) / 2) - ($ryb0x[4] / 2);  
                //     imagettftext( $image, 25, 0, $rybaX, 390, $black, $font, strtoupper( $emergency ));   
                // } 
                // if( strlen($address) >= "30" ){  
                //     $ryb0x = imagettfbbox(23, 0, $font, strtoupper( $address ));
                //     $rybaX = $ryb0x[0] + (imagesx($image) / 2) - ($ryb0x[4] / 2); 
                //     imagettftext( $image, 23, 0, $rybaX, 430, $black, $font, strtoupper( $address )); 
                //     $ryb0x = imagettfbbox(23, 0, $font, strtoupper( $address2 ));
                //     $rybaX = $ryb0x[0] + (imagesx($image) / 2) - ($ryb0x[4] / 2); 
                //     imagettftext( $image, 23, 0, $rybaX, 470, $black, $font, strtoupper( $address2 ));
                //     $ryb0x = imagettfbbox(23, 0, $font, strtoupper( $phone ));
                //     $rybaX = $ryb0x[0] + (imagesx($image) / 2) - ($ryb0x[4] / 2); 
                //     imagettftext( $image, 23, 0, $rybaX, 510, $black, $font, strtoupper( $phone )); 
                // } else {  
                //     $ryb0x = imagettfbbox(25, 0, $font, strtoupper( $address ));
                //     $rybaX = $ryb0x[0] + (imagesx($image) / 2) - ($ryb0x[4] / 2); 
                //     imagettftext( $image, 25, 0, $rybaX, 430, $black, $font, strtoupper( $address ));
                //     $ryb0x = imagettfbbox(25, 0, $font, strtoupper( $address2 ));
                //     $rybaX = $ryb0x[0] + (imagesx($image) / 2) - ($ryb0x[4] / 2); 
                //     imagettftext( $image, 25, 0, $rybaX, 470, $black, $font, strtoupper( $address2 ));
                //     $ryb0x = imagettfbbox(25, 0, $font, strtoupper( $phone ));
                //     $rybaX = $ryb0x[0] + (imagesx($image) / 2) - ($ryb0x[4] / 2); 
                //     imagettftext( $image, 25, 0, $rybaX, 510, $black, $font, strtoupper( $phone )); 
                // }
                if( $path ){ 
                     $save = $path."/".$id."_back.png"; 
                } else {
                    $save = "uploads/faculty_created_id/".$id."_back_".$lastname.".png"; 
                } 
                imagepng( $image ,  $save);  
                imagedestroy($image);
                 if(  $auto_redirect == "yes"){
                    redirect( $save);
                }
                // exit();
            }
        }
    }
    function ImageTTFCenter($image, $text, $font, $size, $angle = 45) 
    {
        $xi = imagesx($image);
        $yi = imagesy($image);
        $box = imagettfbbox($size, $angle, $font, $text);
        $xr = abs(max($box[2], $box[4]));
        $yr = abs(max($box[5], $box[7]));
        $x = intval(($xi - $xr) / 2);
        $y = intval(($yi + $yr) / 2);
        return array($x, $y);
    }
    function download_file_generated($file){
     // printx($file);
        $ext = substr($file,-3);
        $filename =  str_replace("uploads/faculty_created_id/","", $file );
        if($ext=='jpg' || $ext=='gif' || $ext=='png' || $ext=='pdf'){
            header('Content-type: octet/stream');
            header('Content-disposition: attachment; filename='.$filename.';');
            header('Content-Length: '.filesize($file));
            readfile($file);
            exit;
        }
    }
    public function imagettfstroketext(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px) {
        for($c1 = ($x-abs($px)); $c1 <= ($x+abs($px)); $c1++)
            for($c2 = ($y-abs($px)); $c2 <= ($y+abs($px)); $c2++)
                $bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);
            return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
    }
}   
?> 