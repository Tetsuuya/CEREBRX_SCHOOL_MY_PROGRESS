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
		$this->auth->is_logged_in_registrar();
    } 
	
	public function index() {

    
        $this->session->set_userdata('top_menu', 'Academics');
        $this->session->set_userdata('sub_menu', 'request/grade');
		$student_session_data = $this->session->userdata("student"); 
		$registrar_id = $student_session_data['registrar_id']; 	
		$setting_result = $this->setting_model->get();
		$session_id = $setting_result[0]['session_id'];
		$update_grade = $setting_result[0]['update_grade'];
		$data['update_grade'] = $update_grade;
        $data['title'] = 'Add Grade';
        $data['title_list'] = 'Grade Details';
        $listgrade = $this->importgraderequest_model->getallrequest();
        $data['listgrade'] = $listgrade;
        $this->load->view('layout/registrar/header');
        $this->load->view('registrar/grade/request', $data);
        $this->load->view('layout/registrar/footer');
    }
	
	
}


?>
