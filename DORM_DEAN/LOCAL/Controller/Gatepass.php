<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Gatepass extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('smsgateway');
        $this->load->helper('file');
        $this->lang->load('message', 'english');
        $this->role;
        $this->load->library('auth');
        $this->load->model('gatepass_model');
        $this->load->model('dormitorydean_model');
        $this->load->model('Sms_model','SMSM'); 
        $this->auth->is_logged_in_dormitorydean();
    }

    function records() {
        $this->session->set_userdata('top_menu', 'GatePass');
        $this->session->set_userdata('sub_menu', 'gatepass/records');
        $data['title'] = 'Student List';

        $dormitorydean_id = $this->session->userdata('student')['dormitorydean_id'];
        $session_id = $this->setting_model->getCurrentSession();
        
        // Get search parameter
        $search = $this->input->get('search');
        
        // Trim search to remove extra spaces
        if ($search !== null && $search !== '') {
            $search = trim($search);
        }
        
        // Pagination
        $per_page = 15;
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $offset = ($page - 1) * $per_page;
        
        $total_records = $this->gatepass_model->getactiverecords_count($dormitorydean_id, $session_id, $search);
        $total_pages = ceil($total_records / $per_page);
        
        $student_result = $this->dormitorydean_model->getstudentsunderthedean($dormitorydean_id, $session_id);
        $listofrequest = $this->gatepass_model->getactiverecords($dormitorydean_id, $session_id, $per_page, $offset, $search);
        
        $data['studentlist'] = $student_result;
        $data['listofrequest'] = $listofrequest;
        $data['current_page'] = $page;
        $data['total_pages'] = $total_pages;
        $data['total_records'] = $total_records;
        $data['search'] = $search;
        
        $this->load->view('layout/dormitorydean/header', $data);
        $this->load->view('dormitorydean/gatepass/records', $data);
        $this->load->view('layout/dormitorydean/footer', $data);
    }

    function save_data() {
        $dormitorydean_id = $this->session->userdata('student')['dormitorydean_id'];
        $session_id = $this->setting_model->getCurrentSession();
        $student_id =  $this->input->post('student_id');  
        $exit_date =  $this->input->post('exit_date');
        $exit_time =  $this->input->post('exit_time');
        $return_date =  $this->input->post('return_date');
        $return_time =  $this->input->post('return_time');
        $purpose =  $this->input->post('purpose');
        $destination =  $this->input->post('destination');
        $type_of_gatepass =  $this->input->post('type_of_gatepass'); 
        $student_result = $this->dormitorydean_model->getstudentsdetails( $student_id , $session_id); 
        $room_id =  $student_result['room_id'];
        $dorm_id =  $student_result['dorm_id'];
        $complete_name =  $student_result['firstname'].' '.$student_result['middlename'].' '.$student_result['lastname']; 

        if( $student_id ){
            $data = array(
                'student_id' => $student_id,
                'room_id' => $room_id,
                'student' => $complete_name, 
                'approve' => '0',
                'exit_date' => $exit_date, 
                'exit_time' => $exit_time, 
                'return_date' => $return_date, 
                'return_time' => $return_time, 
                'purpose' => $purpose, 
                'destination' => $destination, 
                'type' => $type_of_gatepass, 
                'status' =>  "pending", 
                'dorm_id' => $dorm_id, 
                'requestor_id' => $dormitorydean_id, 
                'session_id' => $session_id, 
                'created_at' => date("Y-m-d")
            );


            $this->gatepass_model->add($data); 

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Student Gatepass Added successfully</div>');
            redirect('dormitorydean/gatepass/records');
            
        }else {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">Student Gatepass Not Added please re-entry again!</div>');
            redirect('dormitorydean/gatepass/records');
        }
        
    }

    function declined_data( $id ) {
        $dormitorydean_id = $this->session->userdata('student')['dormitorydean_id'];
        $session_id = $this->setting_model->getCurrentSession();

        $request_datails = $this->gatepass_model->get( $id );   
        $student_id =  $request_datails['student_id'];
        $student_result = $this->dormitorydean_model->getstudentsdetails( $student_id , $session_id); 
        $firstname = $student_result['firstname'];
        $mobileno = $student_result['mobileno'];
        $guardian_phone = $student_result['guardian_phone'];


        $exit_date =  $request_datails['exit_date'];
        $return_date =  $request_datails['return_date'];  

        if( $request_datails ){
            $data = array(
                'id' => $id, 
                'status' =>  "denied",  
            );


            $this->gatepass_model->add($data); 

            $sms_message =  'Hi '.$firstname.', your gatepass request with exit date of '.$exit_date.' and return date of '.$return_date.' has been Denied. This is an autogenerated SMS by Bridgette. Please do not reply.'; 
            $data_sms  = array(
                'dorm_attendance_id' => $id,
                'sms_msg' => $sms_message,
                'sms_number' => '09666608651', 
                'sms_status' => 'idle',
                'sms_teacher' => 'no',
                'sms_parent' => 'yes' 
            );
            $data_sms2  = array(
                'dorm_attendance_id' => $id,
                'sms_msg' => $sms_message,
                'sms_number' => '09751096978', 
                'sms_status' => 'idle',
                'sms_teacher' => 'no',
                'sms_parent' => 'yes' 
            );
            // $this->SMSM->add($data_sms); 
            // $this->SMSM->add($data_sms2); 

            $mobileno = str_replace(' ', '', $mobileno);
            $mobileno = str_replace('-', '', $mobileno);
            if (strlen($mobileno)>10) { 
                $data_sms3  = array(
                    'dorm_attendance_id' => $id,
                    'sms_msg' => $sms_message,
                    'sms_number' => $mobileno, 
                    'sms_status' => 'idle',
                    'sms_teacher' => 'no',
                    'sms_parent' => 'yes' 
                );

                $this->SMSM->add($data_sms3); 
            }

            $guardian_phone = str_replace(' ', '', $guardian_phone);
            $guardian_phone = str_replace('-', '', $guardian_phone);
            if (strlen($guardian_phone)>10) { 
                $data_sms4 = array(
                    'dorm_attendance_id' => $id,
                    'sms_msg' => $sms_message,
                    'sms_number' => $guardian_phone, 
                    'sms_status' => 'idle',
                    'sms_teacher' => 'no',
                    'sms_parent' => 'yes' 
                );

                $this->SMSM->add($data_sms4); 
            }
           

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Student Gatepass Denied successfully</div>');
            redirect('dormitorydean/gatepass/records');
            
        }else {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">Student Gatepass Not Denied please re-try again!</div>');
            redirect('dormitorydean/gatepass/records');
        }
        
    }

    function delete_data( $id ) {
        $dormitorydean_id = $this->session->userdata('student')['dormitorydean_id'];
        $session_id = $this->setting_model->getCurrentSession();

        $request_datails = $this->gatepass_model->get( $id );   
        // $student_result = $this->dormitorydean_model->getstudentsdetails( $student_id , $session_id); 
        // $room_id =  $student_result['room_id'];
        // $dorm_id =  $student_result['dorm_id'];
        // $complete_name =  $student_result['firstname'].' '.$student_result['middlename'].' '.$student_result['lastname']; 

        if( $request_datails ){
            $data = array(
                'id' => $id, 
                'deleted' =>  "1",  
            );


            $this->gatepass_model->add($data); 

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Student Gatepass Deleted successfully</div>');
            redirect('dormitorydean/gatepass/records');
            
        }else {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">Student Gatepass Not Deleted please re-try again!</div>');
            redirect('dormitorydean/gatepass/records');
        }
        
    }

    function approved_data( $id ) {
        $dormitorydean_id = $this->session->userdata('student')['dormitorydean_id'];
        $session_id = $this->setting_model->getCurrentSession();

        $request_datails = $this->gatepass_model->get( $id );   
        $student_id =  $request_datails['student_id'];
        $student_result = $this->dormitorydean_model->getstudentsdetails( $student_id , $session_id); 
        $firstname = $student_result['firstname'];
        $mobileno = $student_result['mobileno'];
        $guardian_phone = $student_result['guardian_phone'];


        $exit_date =  $request_datails['exit_date'];
        $return_date =  $request_datails['return_date']; 

        if( $request_datails ){
            $data = array(
                'id' => $id, 
                'approve' =>  "1",  
                'status' =>  "approve",  
            );


            $this->gatepass_model->add($data); 

            $sms_message =  'Hi '.$firstname.', your gatepass request with exit date of '.$exit_date.' and return date of '.$return_date.' has been Approved. This is an autogenerated SMS by Bridgette. Please do not reply.'; 
            $data_sms  = array(
                'dorm_attendance_id' => $id,
                'sms_msg' => $sms_message,
                'sms_number' => '09666608651', 
                'sms_status' => 'idle',
                'sms_teacher' => 'no',
                'sms_parent' => 'yes' 
            );
            $data_sms2  = array(
                'dorm_attendance_id' => $id,
                'sms_msg' => $sms_message,
                'sms_number' => '09751096978', 
                'sms_status' => 'idle',
                'sms_teacher' => 'no',
                'sms_parent' => 'yes' 
            );

            // $this->SMSM->add($data_sms); 
            // $this->SMSM->add($data_sms2); 
 
           

            $mobileno = str_replace(' ', '', $mobileno);
            $mobileno = str_replace('-', '', $mobileno);
            if (strlen($mobileno)>10) { 
                $data_sms3  = array(
                    'dorm_attendance_id' => $id,
                    'sms_msg' => $sms_message,
                    'sms_number' => $mobileno, 
                    'sms_status' => 'idle',
                    'sms_teacher' => 'no',
                    'sms_parent' => 'yes' 
                );

                $this->SMSM->add($data_sms3); 
            }

            $guardian_phone = str_replace(' ', '', $guardian_phone);
            $guardian_phone = str_replace('-', '', $guardian_phone);
            if (strlen($guardian_phone)>10) { 
                $data_sms4 = array(
                    'dorm_attendance_id' => $id,
                    'sms_msg' => $sms_message,
                    'sms_number' => $guardian_phone, 
                    'sms_status' => 'idle',
                    'sms_teacher' => 'no',
                    'sms_parent' => 'yes' 
                );

                $this->SMSM->add($data_sms4); 
            }
             

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Student Gatepass Approved successfully</div>');
            redirect('dormitorydean/gatepass/records');
            
        }else {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">Student Gatepass Not Approved please re-try again!</div>');
            redirect('dormitorydean/gatepass/records');
        }
        
    }

    function tapreport() {
        $this->session->set_userdata('top_menu', 'GatePass');
        $this->session->set_userdata('sub_menu', 'gatepass/tapreport');
        $data['title'] = 'Student List';


        $dormitorydean_id = $this->session->userdata('student')['dormitorydean_id'];
          
        $session_id = $this->setting_model->getCurrentSession();
      
 
        $student_result = $this->gatepass_model->getapproverecords( $dormitorydean_id , $session_id );
    
        $data['studentlist'] = $student_result;
        $this->load->view('layout/dormitorydean/header', $data);
        $this->load->view('dormitorydean/gatepass/tapreport', $data);
        $this->load->view('layout/dormitorydean/footer', $data);
    }
    
    // AJAX search for students with live suggestions
    function search_students() {
        $dormitorydean_id = $this->session->userdata('student')['dormitorydean_id'];
        $session_id = $this->setting_model->getCurrentSession();
        $search_term = $this->input->post('search_term');
        
        if (empty($search_term)) {
            echo json_encode([]);
            return;
        }
        
        $search_term = trim($search_term);
        
        // Search in gatepass records for this dorm dean
        $this->db->select('gatepass.*, students.firstname, students.middlename, students.lastname');
        $this->db->from('gatepass');
        $this->db->join('students', 'gatepass.student_id = students.id');
        $this->db->join('hostel_rooms', 'gatepass.room_id = hostel_rooms.id');
        $this->db->join('hostel', 'hostel_rooms.hostel_id = hostel.id');
        $this->db->where('hostel.dormdean_id', $dormitorydean_id);
        $this->db->where('gatepass.session_id', $session_id);
        $this->db->where('gatepass.deleted', '0');
        
        // Search by LASTNAME or FIRSTNAME (starts with OR contains)
        $starts_with = $this->db->escape_like_str($search_term) . '%';
        $contains = '%' . $this->db->escape_like_str($search_term) . '%';
        
        $this->db->group_start();
        $this->db->where("students.lastname LIKE '$starts_with'");
        $this->db->or_where("students.firstname LIKE '$starts_with'");
        $this->db->or_where("students.lastname LIKE '$contains'");
        $this->db->or_where("students.firstname LIKE '$contains'");
        $this->db->group_end();
        
        // Order by priority: lastname starts with > firstname starts with > lastname contains > firstname contains
        $this->db->order_by("CASE 
            WHEN students.lastname LIKE '$starts_with' THEN 1 
            WHEN students.firstname LIKE '$starts_with' THEN 2
            WHEN students.lastname LIKE '$contains' THEN 3
            WHEN students.firstname LIKE '$contains' THEN 4
            ELSE 5 
        END", '', FALSE);
        $this->db->order_by('students.lastname');
        $this->db->limit(10);
        
        $results = $this->db->get()->result_array();
        
        echo json_encode($results);
    }

     
}

?>
