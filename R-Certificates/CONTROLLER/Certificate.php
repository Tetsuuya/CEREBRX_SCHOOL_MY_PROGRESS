<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class certificate extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->lang->load('message', 'english');
        $this->load->library('auth');
      	$this->load->library('m_pdf');
        $this->auth->is_logged_in_registrar();
        $this->load->model('Principal_model','principal_model');
    }

    function index() {
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'certificate');
        $data['title'] = 'Add Certificate';
		 
		$student_session_data = $this->session->userdata("student");
		$registrar_id = $student_session_data['registrar_id'];

        // $class = $this->class_model->get_by_teacher( $registrar_id );
        $class = $this->class_model->get();
        
		$awards = $this->awards_model->get();
		$certificate_result = $this->certificate_model->get_by_teacher( $registrar_id );
	     
        $data['classlist'] = $class;
        $data['awardslist'] = $awards; 
        $data['certificatelist'] = $certificate_result; 
        $data['getquarter'] = $this->customlib->getQuarter();
        $data['quarter'] = $this->input->post('quarter');
        $data['section_id'] = $this->input->post('section_id');
        $data['class_id'] = $this->input->post('class_id');
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/certificate/certificateList', $data);
        $this->load->view('layout/registrar/footer', $data);
    }

    function view($id) {
        $data['title'] = 'Certificate List';
        $certificate = $this->certificate_model->get($id);
        $data['certificate'] = $certificate;
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/certificate/certificatelist', $data);
        $this->load->view('layout/registrar/footer', $data);
    }

    function delete($id) {
        $data['title'] = 'Certificate List';
        $this->certificate_model->remove($id);
        redirect('registrar/certificate/index');
    }

    function create() {
		 
		
		$student_id = $this->input->post('student_id');
		$award_id = $this->input->post('award_id');
		$student_name = $this->input->post('student_name');
		$award_name = $this->input->post('award_name');
		$award_date = $this->input->post('award_date');
        $quarter_id = $this->input->post('quarter_id');
        $quarter_student_grade = $this->input->post('grade');
		$student_session_data = $this->session->userdata("student");
		$registrar_id = $student_session_data['registrar_id'];
		 
		$data = array(
			'student_id' => $student_id, 
			'award_id' => $award_id,
			'teacher_id' => $registrar_id, 
			'date' => $award_date,
            'quarter' => $quarter_id, 
            'grade' => $quarter_student_grade  
		);
			
		$certificate_id = $this->certificate_model->add($data);
		$this->print_pdf( $certificate_id );
		$this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Certificate added successfully</div>');
		redirect('registrar/certificate/index');
   
    }

    function _check_name_exists() {
        $data['name'] = $this->security->xss_clean($this->input->post('name'));
        $data['type'] = $this->security->xss_clean($this->input->post('type'));
        if ($this->awards_model->check_data_exists($data)) {
            $this->form_validation->set_message('_check_name_exists', 'Record already exists');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function edit($id) {
        $awards_result = $this->certificate_model->get();
        $data['awardslist'] = $awards_result;
        $data['title'] = 'Edit Awards';
        $data['id'] = $id;
        $awards = $this->certificate_model->get($id);
        $data['awards'] = $awards;
        $this->form_validation->set_rules('name', 'Award', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/registrar/header', $data);
            $this->load->view('registrar/awards/awardsEdit', $data);
            $this->load->view('layout/registrar/footer', $data);
        } else {
            $data = array(
                'id' => $id,
                'name' => $this->input->post('name'), 
                'type' => $this->input->post('type'),
            );
            $this->certificate_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Award updated successfully</div>');
            redirect('registrar/awards/index');
        }
    }

    function getSubjctByClassandSection() {
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $date = $this->subject_model->getSubjectByClsandSection($class_id, $section_id);
        echo json_encode($data);
    }
	
	function get_student_random() { 
        $data = $this->student_model->get_with_limit();
		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
        echo json_encode($data);
    }
	
    function get_student_awardee() { 
        $class_id = $this->input->post('class_id');  /*2*/
        $section_id = $this->input->post('section_id'); /*16*/
        $quarter = $this->input->post('quarter'); /*1*/
        $awards_id = $this->input->post('awards_id'); /*1*/
         
        $awards_result = $this->awards_model->get( $awards_id ); 
        $subject_id = $awards_result['subject_id'];

        $class_section = $this->student_model->searchByClassSection($class_id, $section_id); 
        $list_id = array();
        foreach ($class_section as $class_section_key => $class_section_value) {
            $list_id[] = $class_section_value['id'];
          
        }


        $this->db->select()->from('exam_results');  
        $this->db->where('subject_id', $subject_id ); 
        $this->db->where('quarter',  $quarter ); 
        $this->db->where_in('student_id', $list_id ); 
        $this->db->order_by('get_marks', 'DESC'); 
        $this->db->limit(10); 
        $query = $this->db->get();
        $exam_results = $query->result_array();
          
        $final_data = array();
        foreach ($exam_results as $exam_results_key => $exam_results_value) {
            $student_id = $exam_results_value['student_id'];
            $exam_id = $exam_results_value['id'];
            $exam_results_value['exam_id'] = $exam_id;
            $student_data = $this->student_model->get($student_id); 
            $final_data[] = array_merge($exam_results_value, $student_data);
             
        }
        
        echo json_encode($final_data);
    }

	function print_pdf( $certificate_id=null ) {   

        if( $certificate_id == null ){
            $certificate_id = $this->uri->slash_segment(3);
        } 
            
             
        $student_session_data = $this->session->userdata("student");
     
        // $teacher_id = $student_session_data['teacher_id']; 
        $teacher = $student_session_data['username']; 
        $school_name = $student_session_data['sch_name']; 
        $certificate_result = $this->certificate_model->get_by_id( $certificate_id );
        $student_result = $this->student_model->get( $certificate_result['student_id'] );
        if( $student_result['middlename'] ){
            $middlename = substr($student_result['middlename'], 0, 1).".";
        } else {
            $middlename = '';
        }     
       
        $student_teacher_advisory =  $this->classsection_model->get_teacher_advisory( $student_result['class_id'],  $student_result['section_id']);
     
        $setting_result = $this->setting_model->get();
        
        $principal_id = $setting_result[0]['principal_id'];
        $session = $setting_result[0]['session'];
        $principal_result = $this->principal_model->get(  $principal_id  ); 
        $data['principal_name'] = $principal_result['lastname'].', '.$principal_result['name'].' '.$principal_result['middlename'];
       
        $date_presented = $certificate_result['date']; 
        $data[ 'date'] = date("F/d/Y", strtotime($date_presented)); 
        $data[ 'date_year'] = date("Y", strtotime($date_presented)); 
        $data[ 'date_month'] = date("F", strtotime($date_presented)); 
        $data[ 'date_day'] = date("dS", strtotime($date_presented)); 
        $data[ 'award_name'] = $certificate_result['name'];
        $data[ 'student_name'] = $certificate_result['lastname'].', '.$certificate_result['firstname'].' '.$certificate_result['middlename'];

        $data[ 'student_name'] = $certificate_result['firstname'].' '.$middlename.' '.$certificate_result['lastname'];
         if( $student_teacher_advisory ){
            // $data[ 'teacher_name'] = $student_teacher_advisory->lastname.', '.$student_teacher_advisory->name.' '.$student_teacher_advisory->middlename;
            $student_teacher_advisory_middlename = isset($student_teacher_advisory->middlename) != '' ?substr($student_teacher_advisory->middlename, 0, 1).".":'';
            $data[ 'teacher_name'] = $student_teacher_advisory->name.' '.$student_teacher_advisory_middlename.' '.$student_teacher_advisory->lastname;  
        } else {
             $data[ 'teacher_name'] = '';
        }
        
        $data[ 'school_name'] = $school_name;
        $data[ 'class_name'] = $student_result['class'];
        $data[ 'grade'] = $certificate_result['grade'];
        $data[ 'quarter'] = $certificate_result['quarter'].$this->ordinal_suffix($certificate_result['quarter']); 
        $data[ 'school_year'] = $session; 
        $data[ 'body_image'] = base_url()."uploads/template_documents/certificate/certificate_blank.png";
         
        $file_name = "Certificate_".$data['award_name']."_".$certificate_result['lastname'].".pdf";
        
        $this->load->library('m_pdf');
        
        $html =  $this->load->view('template/certificate/certificate_awards', $data, true);   

        $this->mpdf = new mPDF();
        // $this->stylesheet = file_get_contents('css/style.css');
     

       /* $this->mpdf->AddPage('L', // L - landscape, P - portrait
                'LETTER', '', '', '',
                0, // margin_left
                0, // margin right
                30, // margin top
                30, // margin bottom
                18, // margin header
                12); // margin footer*/
                $this->mpdf->AddPage('P', // L - landscape, P - portrait
                'LETTER', '', '', '',
                15, // margin_left
                15, // margin right
                15, // margin top
                15, // margin bottom
                15, // margin header
                15); // margin footer
        $this->mpdf->WriteHTML($html);
        $this->mpdf->Output($file_name, 'D'); // download force
        
        redirect('registrar/certificate/index');
    }

    function diploma() {
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'certificate/diploma');
        $data['title'] = 'Add Diploma';

        $session = $this->session_model->getAllSession();
        $session_list = $this->session_model->getAllSession();
        $setting_result = $this->setting_model->get();
        $session = $setting_result[0]['session'];
        //printx($session);

		$class_id = $this->input->post('class_id');
        $student_id = $this->input->post('student_id');
        $section_id = $this->input->post('section_id');
		$year = $this->input->post('year');
        $date = $this->input->post('date_input');
        $action = $this->input->post('action');

        $student_session_data = $this->session->userdata("student");
        $registrar_id = $student_session_data['registrar_id'];
        $class = $this->class_model->get( ); 
        // $student_result = $this->certificate_model->get_student_class( $class_id );
        if( $class_id && $section_id ){
            $student_result = $this->student_model->searchByClassSection($class_id, $section_id) ;
        } else {
            $student_result = array();
        }

        $data['school_year'] = $session_list;
        $data['year_id'] = $year;
        $data['date'] = $date;
        $data['classlist'] = $class; 
        $data['class_id'] = $class_id; 
        $data['section_id'] = $section_id;
        $data['student_result'] = $student_result;
		
		if( $class_id &&  $student_id || $class_id &&  $section_id && $action ){
			$this->print_diploma_pdf(  $class_id, $student_id,  $section_id, $year, $date, $action );
		}
		
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/certificate/diplomaList', $data);
        $this->load->view('layout/registrar/footer', $data);
    }
	
	function print_diploma_pdf(  $class_id, $student_id, $section_id=null , $action=null ) {  
		  
        $this->load->library('m_pdf');
        $this->mpdf = new mPDF();
		$certificate_id = $this->uri->slash_segment(3); 
		$student_session_data = $this->session->userdata("student");

        $setting_result = $this->setting_model->get();
        
        $principal_id = $setting_result[0]['principal_id']; 
        $principal_result = $this->principal_model->get(  $principal_id  );  
        $data['principal_name'] =  $principal_result['name'].' '.$principal_result['middlename'].' '.$principal_result['lastname'];
        $data[ 'image_src'] = base_url()."backend/images/s_logo.png";
        $data[ 'image_seal'] = base_url()."backend/images/s_seal.png";

	   
        if( $action == 'print_batch'){
            $student_array =  $this->student_model->searchByClassSection($class_id, $section_id);

            foreach ($student_array as $student_key => $student_value) {
                $data['class'] = $student_value['class'];   
                if( empty($student_value['lrn'])){
                    $data['lrn'] = $student_value['roll_no']; 
                } else {
                    $data['lrn'] = $student_value['lrn']; 
                } 
                $data['firstname'] = $student_value['firstname'];
                $data['lastname'] = $student_value['lastname']; 
                $data['middlename'] = $student_value['middlename']; 
                $data['date'] = date('F/d/Y');  
                $data[ 'body_image'] =  base_url()."uploads/template_documents/certificate/template_diploma.jpg"; 
                $html =  $this->load->view('template/certificate/template_diploma', $data, true);  


                $pdfFilePath = "Diploma ".$student_value['class'].", ".$certificate_result['section'].".pdf";

                 $this->mpdf->AddPage('L', // L - landscape, P - portrait
                        '', '', '', '',
                        15, // margin_left
                        15, // margin right
                        10, // margin top
                        10, // margin bottom
                        10, // margin header
                        10); // margin footer
                $this->mpdf->WriteHTML($html);
            }
         
            $this->mpdf->Output($pdfFilePath, 'I'); // download force*/
          
        } else { 
    		$certificate_result = $this->student_model->get( $student_id ); 
    		$data['class'] = $certificate_result['class'];  
            $data['lrn'] = $certificate_result['lrn']; 
            $data['firstname'] = $certificate_result['firstname'];
            $data['lastname'] = $certificate_result['lastname']; 
    		$data['middlename'] = $certificate_result['middlename']; 
    		$data['date'] = date('F/d/Y');  
            $data[ 'body_image'] =  base_url()."uploads/template_documents/certificate/template_diploma.jpg"; 
    		$html =  $this->load->view('template/certificate/template_diploma', $data, true);  
            $pdfFilePath = "Diploma ".$certificate_result['lastname'].", ".$certificate_result['firstname'].".pdf";
     
     
            $this->mpdf->AddPage('L', // L - landscape, P - portrait
                    '', '', '', '',
                    15, // margin_left
                    15, // margin right
                    10, // margin top
                    10, // margin bottom
                    10, // margin header
                    10); // margin footer
            $this->mpdf->WriteHTML($html);
            $this->mpdf->Output($pdfFilePath, 'I'); // download force*/

    		 
        }
    }
 
    // function good_moral() {
    //     $this->session->set_userdata('top_menu', 'Certificate');
    //     $this->session->set_userdata('sub_menu', 'certificate/good_moral');
    //     $data['title'] = 'Add good_moral';

    //     //get the session
    //     $session = $this->session_model->getAllSession();
    //     $session_list = $this->session_model->getAllSession(); 
	// 	$setting_result = $this->setting_model->get();
    //     $session = $setting_result[0]['session'];

	// 	$class_id = $this->input->post('class_id');
    //     $student_id = $this->input->post('student_id');
    //     $section_id = $this->input->post('section_id');
    //     $year = $this->input->post("year");
    //     $date = $this->input->post("date_input");
    
	// 	$action = $this->input->post('action');
    //     $student_session_data = $this->session->userdata("student");
    //     $registrar_id = $student_session_data['registrar_id'];
    //     $class = $this->class_model->get( );
    //     // $student_result = $this->certificate_model->get_student_class( $class_id );
    //     if( $class_id && $section_id ){
    //         $student_result = $this->student_model->searchByClassSection($class_id, $section_id, null, $year) ; // updated model
    //     } else {
    //         $student_result = array();
    //     }

    //     $data[ 'school_year'] = $session_list;
    //     $data["year_id"] = $year;
    //     $data['date'] = $date;
    //     $data['classlist'] = $class;
    //     $data['class_id'] = $class_id;
    //     $data['section_id'] = $section_id;
    //     $data['student_result'] = $student_result;
	// 	if( $class_id && $student_id || $class_id && $section_id && $action ){
	// 		$this->print_good_moral_pdf($class_id, $student_id,  $section_id, $action, $year, $date);
	// 	}

    //     $this->load->view('layout/registrar/header', $data);
    //     $this->load->view('registrar/certificate/good_moral_List', $data);
    //     $this->load->view('layout/registrar/footer', $data);
    // }

	// function print_good_moral_pdf(  $class_id, $student_id, $section_id=null, $action=null, $year=null, $date) {

    //     $this->load->library('m_pdf');
    //     $this->mpdf = new mPDF();
	// 	$certificate_id = $this->uri->slash_segment(3);
	// 	$student_session_data = $this->session->userdata("student");
    //     $setting_result = $this->setting_model->get();
    //     $session = $setting_result[0]['session'];

    //     $principal_id = $setting_result[0]['principal_id'];
    //     $principal_result = $this->principal_model->get(  $principal_id  );
    //     $data['principal_name'] =  $principal_result['name'].' '.$principal_result['middlename'].' '.$principal_result['lastname'];
    //     $data[ 'image_src'] = base_url()."backend/images/s_logo.png";
    //     $data[ 'image_seal'] = base_url()."backend/images/s_seal.png";

    //     if( $action == 'print_batch'){
    //         $student_array =  $this->student_model->searchByClassSection($class_id, $section_id, null, $year);
    //         foreach ($student_array as $student_key => $student_value) {
    //             $data['class'] = $student_value['class'];
    //             if( empty($student_value['lrn'])){
    //                 $data['lrn'] = $student_value['roll_no'];
                    
    //             } else {
    //                 $data['lrn'] = $student_value['lrn'];
    //             }
                
    //             // $data[ 'school_year'] = $session;

    //             $session_list = $this->session_model->getAllSession();
    //             $data['date_today'] = trim($date);
            
    //             foreach ($session_list as $session_item) {
    //                 if ($session_item['id'] == $year) {
    //                     // Split the session string (e.g., 2023-24) to modify it
    //                     $parts = explode('-', $session_item['session']); // Split the session year into parts

    //                     if (count($parts) === 2) {
    //                         // Reconstruct the year in the "2023-2024" format
    //                         $data['year'] = $parts[0] . '-' . (intval($parts[0]) + 1);
    //                     } else {
    //                         // Fallback in case the format is unexpected
    //                         $data['year'] = $session_item['session'];
    //                     }
    //                     break; // Stop once the match is found
    //                 }
    //             }

    //             $data['class'] = $student_value['class'];
    //             $data['section'] = $student_value['section'];
    //             $data['strand_id'] = $student_value['strand_id'];
    //             $data['class_id'] = $student_value['class_id'];

    //             $data['firstname'] = $student_value['firstname'];
    //             $data['lastname'] = $student_value['lastname'];
    //             $data['middlename'] = $student_value['middlename'];
    //             $data['gender'] = $student_value['gender'];
    //             $data['date'] = date('F/d/Y');
    //             // $data[ 'body_image'] =  base_url()."uploads/template_documents/certificate/template_diploma.jpg";
    //             $html =  $this->load->view('template/certificate/template_goodmoral', $data, true);


    //             $pdfFilePath = "Good Moral ".$student_value['class'].", ".$student_value['section'].".pdf";

    //             $this->mpdf->AddPage('P', // P - portrait, L - landscape
    //                 'Letter', // Paper size (8.5 x 11 inches)
    //                 '', '', '',
    //                 15, // margin_left
    //                 15, // margin_right
    //                 10, // margin_top
    //                 10, // margin_bottom
    //                 10, // margin_header
    //                 10  // margin_footer
    //             );
    //             $this->mpdf->WriteHTML($html);
                
    //             }

    //         $this->mpdf->Output($pdfFilePath, 'I'); // download force*/

    //     } else {
    //         $session_list = $this->session_model->getAllSession();

    //         $data['date_today'] = trim($date);
        

    //         foreach ($session_list as $session_item) {
    //             if ($session_item['id'] == $year) {
    //                 // Split the session string (e.g., 2023-24) to modify it
    //                 $parts = explode('-', $session_item['session']); // Split the session year into parts

    //                 if (count($parts) === 2) {
    //                     // Reconstruct the year in the "2023-2024" format
    //                     $data['year'] = $parts[0] . '-' . (intval($parts[0]) + 1);
    //                 } else {
    //                     // Fallback in case the format is unexpected
    //                     $data['year'] = $session_item['session'];
    //                 }
    //                 break; // Stop once the match is found
    //             }
    //         }
    //         // printx($data['year']);
            
            
    // 		$certificate_result = $this->student_model->getBySession( $student_id,$year);
    //         // printx($certificate_result);
    // 		$data['class'] = $certificate_result['class'];
    //         $data['section'] = $certificate_result['section'];
    //         $data['strand_id'] = $certificate_result['strand_id'];
    //         $data['class_id'] = $certificate_result['class_id'];
    //         $data['gender'] = $certificate_result['gender'];

    //         // printx($certificate_result);
    //         // printx($certificate_result['class_id']); 
    //         $data['lrn'] = $certificate_result['lrn'];
    //         $data['firstname'] = $certificate_result['firstname'];
    //         $data['lastname'] = $certificate_result['lastname'];
    // 		$data['middlename'] = $certificate_result['middlename'];
    // 		$data['date'] = date('F/d/Y');
            
    //         $data[ 'school_year'] = $session;
    //         // $data[ 'body_image'] =  base_url()."uploads/template_documents/certificate/template_diploma.jpg";
    // 		$html =  $this->load->view('template/certificate/template_goodmoral', $data, true);
    //         $pdfFilePath = "Good Moral ".$certificate_result['lastname'].", ".$certificate_result['firstname'].".pdf";

    //         // printx($data);  

    //         $this->mpdf->AddPage('P', // P - portrait, L - landscape
    //             'Letter', // Paper size (8.5 x 11 inches)
    //             '', '', '',
    //             15, // margin_left
    //             15, // margin_right
    //             10, // margin_top
    //             10, // margin_bottom
    //             10, // margin_header
    //             10  // margin_footer
    //         );
    //         $this->mpdf->WriteHTML($html);
    //         $this->mpdf->Output($pdfFilePath, 'I'); // download force*/


    //     }
    // }

    function good_moral() {
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'certificate/good_moral');
        $data['title'] = 'Add good_moral';
        //get the session

        $session = $this->session_model->getAllSession();
        $session_list = $this->session_model->getAllSession();
		$setting_result = $this->setting_model->get();
        $session = $setting_result[0]['session'];


		$class_id = $this->input->post('class_id');
        $student_id = $this->input->post('student_id');
        $section_id = $this->input->post('section_id');
        $year = $this->input->post("year");
        $date = $this->input->post("date_input");
        $template = $this->input->post('template'); // Capture the template parameter


        // printx($template);
		$action = $this->input->post('action');
        $student_session_data = $this->session->userdata("student");
        $principal_id = $student_session_data['principal_id'];
        $class = $this->class_model->get( );
        // $student_result = $this->certificate_model->get_student_class( $class_id );
        if( $class_id && $section_id ){
            $student_result = $this->student_model->searchByClassSection($class_id, $section_id, null, $year) ; // updated model
        } else {
            $student_result = array();
        }


        $data[ 'school_year'] = $session_list;
        $data["year_id"] = $year;
        $data['date'] = $date;
        $data['classlist'] = $class;
        $data['class_id'] = $class_id;
        $data['section_id'] = $section_id;
        $data['student_result'] = $student_result;
		if( $class_id && $student_id || $class_id && $section_id && $action ){
			$this->print_good_moral_pdf($class_id, $student_id,  $section_id, $action, $year, $date, $template);
		}

        // printx($data);

        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/certificate/good_moral_List', $data);
        $this->load->view('layout/registrar/footer', $data);
    }

	function print_good_moral_pdf(  $class_id, $student_id, $section_id=null, $action=null, $year=null, $date, $template) {

        $this->load->library('m_pdf');
        $this->mpdf = new mPDF();
		$certificate_id = $this->uri->slash_segment(3);
		$student_session_data = $this->session->userdata("student");
        $setting_result = $this->setting_model->get();
        $session = $setting_result[0]['session'];

        $principal_id = $setting_result[0]['principal_id'];
        $principal_result = $this->principal_model->get(  $principal_id  );
        $data['principal_name'] = $principal_result['name'] . ' ' .
        strtoupper(substr($principal_result['middlename'], 0, 1)) . '. ' .
        $principal_result['lastname'];
        $data[ 'image_src'] = base_url()."backend/images/s_logo.png";
        $data[ 'image_seal'] = base_url()."backend/images/s_seal.png";

        if ($action == 'print_batch') {
            $this->mpdf = new mPDF('', 'LETTER', '0', '', '0', '0', '0', '0', '0', '0', 'P');
        
            $student_array = $this->student_model->searchByClassSection($class_id, $section_id, null, $year);
            
            foreach ($student_array as $student_key => $student_value) {
                $data['class'] = $student_value['class'];
                $data['lrn'] = !empty($student_value['lrn']) ? $student_value['lrn'] : $student_value['roll_no'];
        
                // $Deped_SID = $this->student_model->get_SID();
                    $Deped_SID ='Cere1090909';
                $data['SID'] = $Deped_SID[0]['dise_code'];
                
                $data['school_year'] = $session;
                $data['section'] = $student_value['section'];
                $data['strand_id'] = $student_value['strand_id'];
                $data['class_id'] = $student_value['class_id'];
                $data['firstname'] = $student_value['firstname'];
                $data['lastname'] = $student_value['lastname'];
                $data['middlename'] = $student_value['middlename'];
                $data['gender'] = $student_value['gender'];
                $data['date'] = date('F j, Y');
                $data['body_image'] = base_url() . "uploads/template_documents/certificate/template_diploma.jpg";
        
                if ($template == 1) {
                    $html = $this->load->view('template/certificate/template_goodmoral', $data, true);
                } elseif ($template == 2) {
                    $html = $this->load->view('template/certificate/template_goodmoral2', $data, true);
                } else {
                    show_error('Invalid template selected.');
                }
        
                $this->mpdf->WriteHTML($html);
                if ($student_key < count($student_array) - 1) {
                    $this->mpdf->AddPage();
                }
            }
        
            $pdfFilePath = "Good Moral " . $student_array[0]['class'] . ", " . $student_array[0]['section'] . ".pdf";
            $this->mpdf->Output($pdfFilePath, 'D');
        }else {
            $session_list = $this->session_model->getAllSession();

            $data['date_today'] = trim($date);


            foreach ($session_list as $session_item) {
                if ($session_item['id'] == $year) {
                    // Split the session string (e.g., 2023-24) to modify it
                    $parts = explode('-', $session_item['session']); // Split the session year into parts

                    if (count($parts) === 2) {
                        // Reconstruct the year in the "2023-2024" format
                        $data['year'] = $parts[0] . '-' . (intval($parts[0]) + 1);
                    } else {
                        // Fallback in case the format is unexpected
                        $data['year'] = $session_item['session'];
                    }
                    break; // Stop once the match is found
                }
            }


    		$certificate_result = $this->student_model->get( $student_id );
    		$data['class'] = $certificate_result['class'];

            // $Deped_SID = $this->student_model->get_SID();
            $Deped_SID ='Cere1090909';
            $data['SID'] = $Deped_SID[0]['dise_code'];


            $grade_number = intval(preg_replace('/[^0-9]/', '', $data['class'])); // Extract the numeric part
            $next_class = "Grade " . ($grade_number + 1); // Increment the grade
            $current_grade = "Grades " . ($grade_number);

            $data['next_class'] = $next_class; // Assign to the array



            $data['section'] = $certificate_result['section'];
            $data['strand_id'] = $certificate_result['strand_id'];
            $data['class_id'] = $certificate_result['class_id'];
            $data['gender'] = $certificate_result['gender'];
            // printx($data);

            // printx($certificate_result);

            // printx($certificate_result['class_id']);

            $data['lrn'] = !empty($certificate_result['lrn']) ? $certificate_result['lrn'] : "No LRN was found.";

            // $data['lrn'] = $certificate_result['lrn'];

            // printx($certificate_result['lrn']);
            $data['firstname'] = $certificate_result['firstname'];
            $data['lastname'] = $certificate_result['lastname'];
    		$data['middlename'] = $certificate_result['middlename'];
    		$data['date'] = date('F j, Y');

            $data[ 'school_year'] = $session;
            $data[ 'body_image'] =  base_url()."uploads/template_documents/certificate/template_diploma.jpg";
    		// $html =  $this->load->view('template/certificate/template_goodmoral', $data, true);
            if ($template == 1) {
                $html = $this->load->view('template/certificate/template_goodmoral', $data, true); // Template 1
            } elseif ($template == 2) {
                $html = $this->load->view('template/certificate/template_goodmoral2', $data, true); // Template 2
            } else {
                show_error('Invalid template selected.');
            }
            $pdfFilePath = "Good Moral ".$certificate_result['lastname'].", ".$certificate_result['firstname'].".pdf";


            // $this->mpdf->AddPage('P', // P - portrait, L - landscape
            //     'Letter', // Paper size (8.5 x 11 inches)
            //     '', '', '',
            //     15, // margin_left
            //     15, // margin_right
            //     10, // margin_top
            //     10, // margin_bottom
            //     10, // margin_header
            //     10  // margin_footer
            // );

            $this->m_pdf->pdf = new mPDF('','LETTER','0','','0','0','0','0','0','0', 'P');
            $this->m_pdf->pdf->WriteHTML($html);
            // $this->mpdf->WriteHTML($html);
            $this->m_pdf->pdf->Output($pdfFilePath, 'D'); // download force*/


        }
    }

    function certificate_of_enrollment() {
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'certificate/coe');
        $data['title'] = 'Add Certificate of Enrollment';

        //get the session
        $session = $this->session_model->getAllSession();
        $session_list = $this->session_model->getAllSession(); 
		$setting_result = $this->setting_model->get();
        $session = $setting_result[0]['session'];

		$class_id = $this->input->post('class_id');
        $student_id = $this->input->post('student_id');
        $section_id = $this->input->post('section_id');
        $year = $this->input->post("year");
        $date = $this->input->post("date_input");
    
		$action = $this->input->post('action');
        $student_session_data = $this->session->userdata("student");
        $registrar_id = $student_session_data['registrar_id'];
        $class = $this->class_model->get( );
        // $student_result = $this->certificate_model->get_student_class( $class_id );
        if( $class_id && $section_id ){
            $student_result = $this->student_model->searchByClassSection($class_id, $section_id, null, $year) ; // updated model
        } else {
            $student_result = array();
        }

        $data[ 'school_year'] = $session_list;
        $data["year_id"] = $year;
        $data['date'] = $date;
        $data['classlist'] = $class;
        $data['class_id'] = $class_id;
        $data['section_id'] = $section_id;
        $data['student_result'] = $student_result;
		if( $class_id && $student_id || $class_id && $section_id && $action ){
			$this->print_coe_pdf($class_id, $student_id,  $section_id, $action, $year, $date);
		}

        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/certificate/coe_list', $data);
        $this->load->view('layout/registrar/footer', $data);
    }

	function print_coe_pdf(  $class_id, $student_id, $section_id=null, $action=null, $year=null, $date) {

        $this->load->library('m_pdf');
        $this->mpdf = new mPDF();
		$certificate_id = $this->uri->slash_segment(3);
		$student_session_data = $this->session->userdata("student");
        $setting_result = $this->setting_model->get();
        $session = $setting_result[0]['session'];

        $principal_id = $setting_result[0]['principal_id'];
        $principal_result = $this->principal_model->get(  $principal_id  );
        $data['principal_name'] =  $principal_result['name'].' '.$principal_result['middlename'].' '.$principal_result['lastname'];
        $data[ 'image_src'] = base_url()."backend/images/s_logo.png";
        $data[ 'image_seal'] = base_url()."backend/images/s_seal.png";

        if( $action == 'print_batch'){
            $student_array =  $this->student_model->searchByClassSection($class_id, $section_id, null, $year);
            foreach ($student_array as $student_key => $student_value) {
                $data['class'] = $student_value['class'];
                if( empty($student_value['lrn'])){
                    $data['lrn'] = $student_value['roll_no'];
                    
                } else {
                    $data['lrn'] = $student_value['lrn'];
                }
                
                // $data[ 'school_year'] = $session;

                $session_list = $this->session_model->getAllSession();

                $data['date_today'] = trim($date);
            
                foreach ($session_list as $session_item) {
                    if ($session_item['id'] == $year) {
                        // Split the session string (e.g., 2023-24) to modify it
                        $parts = explode('-', $session_item['session']); // Split the session year into parts

                        if (count($parts) === 2) {
                            // Reconstruct the year in the "2023-2024" format
                            $data['year'] = $parts[0] . '-' . (intval($parts[0]) + 1);
                        } else {
                            // Fallback in case the format is unexpected
                            $data['year'] = $session_item['session'];
                        }
                        break; // Stop once the match is found
                    }
                }
                // printx($student_array);
                $data['class'] = $student_value['class'];
                $data['section'] = $student_value['section'];
                $data['strand_id'] = $student_value['strand_id'];
                $data['class_id'] = $student_value['class_id'];
                $data['lrn'] = $student_value['lrn'];
                $data['strand'] = $student_value['strand_code'];

                $data['firstname'] = $student_value['firstname'];
                $data['lastname'] = $student_value['lastname'];
                $data['middlename'] = $student_value['middlename'];
                $data['gender'] = $student_value['gender'];
                $data['date'] = date('F/d/Y');
                $data[ 'body_image'] =  base_url()."uploads/template_documents/certificate/template_diploma.jpg";
                $html =  $this->load->view('template/certificate/template_coe', $data, true);


                $pdfFilePath = "COE ".$student_value['class'].", ".$student_value['section'].".pdf";

                $this->mpdf->AddPage('P', // P - portrait, L - landscape
                    'Letter', // Paper size (8.5 x 11 inches)
                    '', '', '',
                    15, // margin_left
                    15, // margin_right
                    10, // margin_top
                    10, // margin_bottom
                    10, // margin_header
                    10  // margin_footer
                );
                $this->mpdf->WriteHTML($html);
                
                }

            $this->mpdf->Output($pdfFilePath, 'I'); // download force*/

        } else {
            $session_list = $this->session_model->getAllSession();

            $data['date_today'] = trim($date);
        

            foreach ($session_list as $session_item) {
                if ($session_item['id'] == $year) {
                    // Split the session string (e.g., 2023-24) to modify it
                    $parts = explode('-', $session_item['session']); // Split the session year into parts

                    if (count($parts) === 2) {
                        // Reconstruct the year in the "2023-2024" format
                        $data['year'] = $parts[0] . '-' . (intval($parts[0]) + 1);
                    } else {
                        // Fallback in case the format is unexpected
                        $data['year'] = $session_item['session'];
                    }
                    break; // Stop once the match is found
                }
            }
            // printx($data['year']);
            
            
    		$certificate_result = $this->student_model->getBySession( $student_id,$year);
            // printx($certificate_result);
    		$data['class'] = $certificate_result['class'];
            $data['section'] = $certificate_result['section'];
            $data['strand_id'] = $certificate_result['strand_id'];
            $data['class_id'] = $certificate_result['class_id'];
            $data['gender'] = $certificate_result['gender'];
            $data['strand'] = $certificate_result['strand_code'];

            // printx($certificate_result);
            // printx($certificate_result['class_id']); 
            $data['lrn'] = $certificate_result['lrn'];
            $data['firstname'] = $certificate_result['firstname'];
            $data['lastname'] = $certificate_result['lastname'];
    		$data['middlename'] = $certificate_result['middlename'];
    		$data['date'] = date('F/d/Y');
            
            $data[ 'school_year'] = $session;
            $data[ 'body_image'] =  base_url()."uploads/template_documents/certificate/template_diploma.jpg";
    		$html =  $this->load->view('template/certificate/template_coe', $data, true);
            $pdfFilePath = "COE ".$certificate_result['lastname'].", ".$certificate_result['firstname'].".pdf";

            // printx($data);  

            $this->mpdf->AddPage('P', // P - portrait, L - landscape
                'Letter', // Paper size (8.5 x 11 inches)
                '', '', '',
                15, // margin_left
                15, // margin_right
                10, // margin_top
                10, // margin_bottom
                10, // margin_header
                10  // margin_footer
            );
            $this->mpdf->WriteHTML($html);
            $this->mpdf->Output($pdfFilePath, 'I'); // download force*/


        }
    }

    function request_form() {
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'certificate/request_form');
        $data['title'] = 'Add good_moral';

        //get the session
        $session = $this->session_model->getAllSession();
        $session_list = $this->session_model->getAllSession(); 
		$setting_result = $this->setting_model->get();
        $session = $setting_result[0]['session'];

		$class_id = $this->input->post('class_id');
        $student_id = $this->input->post('student_id');
        $section_id = $this->input->post('section_id');
        $year = $this->input->post("year");
        $date = $this->input->post("date_input");

         // NEW: Get recipient school name, Remarks, Request options
        $recipient_school = $this->input->post("recipient_school"); 
        $school_address = $this->input->post("school_address"); 
        $remarks = $this->input->post("remarks"); 
        $request_options = $this->input->post("request_options"); 
        $authorized_hand_carry = $this->input->post("authorized_hand_carry"); 
    
		$action = $this->input->post('action');
        $student_session_data = $this->session->userdata("student");
        $registrar_id = $student_session_data['registrar_id'];
        $class = $this->class_model->get( );
        // $student_result = $this->certificate_model->get_student_class( $class_id );
        if( $class_id && $section_id ){
            $student_result = $this->student_model->searchByClassSection($class_id, $section_id, null, $year) ; // updated model
        } else {
            $student_result = array();
        }

        $data[ 'school_year'] = $session_list;
        $data["year_id"] = $year;
        $data['date'] = $date;
        $data['classlist'] = $class;
        $data['class_id'] = $class_id;
        $data['section_id'] = $section_id;
        $data['student_result'] = $student_result;
        // printx($student_result);

        // save school name to database and print (supports both single student and batch print)
        if ($class_id && ($action == 'print_batch' || ($student_id && $recipient_school && $school_address))) {

            if ($action == 'print_batch') {
                $batch_ids = $this->input->post('batch_id');
                if (!empty($batch_ids)) {
                    foreach ($batch_ids as $sid) {
                        $s_school = isset($recipient_school[$sid]) ? $recipient_school[$sid] : '';
                        $s_address = isset($school_address[$sid]) ? $school_address[$sid] : '';
                        if ($s_school && $s_address) {
                            $this->student_model->update_school_name($sid, $s_school, $s_address);
                        }
                    }
                }
            } else {
                $single_school = is_array($recipient_school) && isset($recipient_school[$student_id]) ? $recipient_school[$student_id] : (!is_array($recipient_school) ? $recipient_school : '');
                $single_address = is_array($school_address) && isset($school_address[$student_id]) ? $school_address[$student_id] : (!is_array($school_address) ? $school_address : '');
                $this->student_model->update_school_name($student_id, $single_school, $single_address);
            }

            // Proceed to print
            $this->print_request_form_pdf($class_id, $student_id, $section_id, $action, $year, $date, $recipient_school, $school_address, $remarks, $request_options, $authorized_hand_carry);
        }
        
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/certificate/request_form', $data);
        $this->load->view('layout/registrar/footer', $data);
    }

	function print_request_form_pdf(  $class_id, $student_id, $section_id=null, $action=null, $year=null, $date, $recipient_school, $school_address ,$remarks, $request_options,$authorized_hand_carry ) {

        $this->load->library('m_pdf');
        $this->mpdf = new mPDF();
		$certificate_id = $this->uri->slash_segment(3);
		$student_session_data = $this->session->userdata("student");
        $setting_result = $this->setting_model->get();
        $session = $setting_result[0]['session'];

        // define school year and date today for both single and batch prints
        $data['school_year'] = $session;
        $data['date_today'] = trim($date);

        $principal_id = $setting_result[0]['principal_id'];
        $principal_result = $this->principal_model->get(  $principal_id  );
        $data['principal_name'] =  $principal_result['name'].' '.$principal_result['middlename'].' '.$principal_result['lastname'];
        $data[ 'image_src'] = base_url()."backend/images/s_logo.png";
        $data[ 'image_seal'] = base_url()."backend/images/s_seal.png";

        // Add recipient school to data
        $data['recipient_school'] = is_array($recipient_school) && isset($recipient_school[$student_id]) ? $recipient_school[$student_id] : (!is_array($recipient_school) ? $recipient_school : '');
        $data['school_address'] = is_array($school_address) && isset($school_address[$student_id]) ? $school_address[$student_id] : (!is_array($school_address) ? $school_address : '');
        $data['remarks'] = is_array($remarks) && isset($remarks[$student_id]) ? $remarks[$student_id] : (!is_array($remarks) ? $remarks : ''); 
        $data['request_options'] = $request_options; 
        $data['authorized_hand_carry'] = $authorized_hand_carry;

        if( $action == 'print_batch'){
            $batch_ids = $this->input->post('batch_id');
            $student_array =  $this->student_model->searchByClassSection($class_id, $section_id, null, $year);
            
            // If batch_ids is provided, filter the student array to only include selected students
            if (!empty($batch_ids)) {
                $filtered_array = [];
                foreach ($student_array as $student_val) {
                    if (in_array($student_val['id'], $batch_ids)) {
                        $filtered_array[] = $student_val;
                    }
                }
                $student_array = $filtered_array;
            }

            foreach ($student_array as $student_key => $student_value) {
                $data['class'] = $student_value['class'];
                if( empty($student_value['lrn'])){
                    $data['lrn'] = $student_value['roll_no'];
                    
                } else {
                    $data['lrn'] = $student_value['lrn'];
                }
                
                $session_list = $this->session_model->getAllSession();
            
                foreach ($session_list as $session_item) {
                    if ($session_item['id'] == $year) {
                        // Split the session string (e.g., 2023-24) to modify it
                        $parts = explode('-', $session_item['session']); // Split the session year into parts

                        if (count($parts) === 2) {
                            // Reconstruct the year in the "2023-2024" format
                            $data['year'] = $parts[0] . '-' . (intval($parts[0]) + 1);
                        } else {
                            // Fallback in case the format is unexpected
                            $data['year'] = $session_item['session'];
                        }
                        break; // Stop once the match is found
                    }
                }

                $data['class'] = $student_value['class'];
                $data['section'] = $student_value['section'];
                $data['strand_id'] = $student_value['strand_id'];
                $data['class_id'] = $student_value['class_id'];

                $data['firstname'] = $student_value['firstname'];
                $data['lastname'] = $student_value['lastname'];
                $data['middlename'] = $student_value['middlename'];
                $data['gender'] = $student_value['gender'];
                $data['date'] = date('F/d/Y');

                // Pass recipient school per student dynamically
                $data['recipient_school'] = isset($recipient_school[$student_value['id']]) ? $recipient_school[$student_value['id']] : '';
                $data['school_address'] = isset($school_address[$student_value['id']]) ? $school_address[$student_value['id']] : '';
                $data['remarks'] = isset($remarks[$student_value['id']]) ? $remarks[$student_value['id']] : '';

                $data[ 'body_image'] =  base_url()."uploads/template_documents/certificate/template_diploma.jpg";
                $html =  $this->load->view('template/certificate/template_request_form', $data, true);

                $pdfFilePath = "Request Form Batch.pdf";

                $this->mpdf->AddPage('P', // P - portrait, L - landscape
                    'Letter', // Paper size (8.5 x 11 inches)
                    '', '', '',
                    15, // margin_left
                    15, // margin_right
                    10, // margin_top
                    10, // margin_bottom
                    10, // margin_header
                    10  // margin_footer
                );
                $this->mpdf->WriteHTML($html);
            }

            $this->mpdf->Output($pdfFilePath, 'I');

        } else {
            $session_list = $this->session_model->getAllSession();

            $data['date_today'] = trim($date);
        
            foreach ($session_list as $session_item) {
                if ($session_item['id'] == $year) {
                    // Split the session string (e.g., 2023-24) to modify it
                    $parts = explode('-', $session_item['session']); // Split the session year into parts

                    if (count($parts) === 2) {
                        // Reconstruct the year in the "2023-2024" format
                        $data['year'] = $parts[0] . '-' . (intval($parts[0]) + 1);
                    } else {
                        // Fallback in case the format is unexpected
                        $data['year'] = $session_item['session'];
                    }
                    break; // Stop once the match is found
                }
            }
            
    		$certificate_result = $this->student_model->getBySession( $student_id,$year);
    		$data['class'] = $certificate_result['class'];
            $data['section'] = $certificate_result['section'];
            $data['strand_id'] = $certificate_result['strand_id'];
            $data['class_id'] = $certificate_result['class_id'];
            $data['gender'] = $certificate_result['gender'];

            $data['lrn'] = $certificate_result['lrn'];
            $data['firstname'] = $certificate_result['firstname'];
            $data['lastname'] = $certificate_result['lastname'];
    		$data['middlename'] = $certificate_result['middlename'];
    		$data['date'] = date('F/d/Y');
            
            $data[ 'school_year'] = $session;
            $data[ 'body_image'] =  base_url()."uploads/template_documents/certificate/template_diploma.jpg";
    		$html =  $this->load->view('template/certificate/template_request_form', $data, true);
            $pdfFilePath = "Request Form ".$certificate_result['lastname'].", ".$certificate_result['firstname'].".pdf";

            $this->mpdf->AddPage('P', // P - portrait, L - landscape
                'Letter', // Paper size (8.5 x 11 inches)
                '', '', '',
                15, // margin_left
                15, // margin_right
                10, // margin_top
                10, // margin_bottom
                10, // margin_header
                10  // margin_footer
            );
            $this->mpdf->WriteHTML($html);
            $this->mpdf->Output($pdfFilePath, 'I');
        }
    }




   function ordinal_suffix($num){
       // protect against large numbers
		if( $num == 'final'){
			return ' ';
		} else {
			$num = $num % 100; 
			if($num < 11 || $num > 13){
				switch($num % 10){
				case 1: return 'st';
				case 2: return 'nd';
				case 3: return 'rd';
				}
				return 'th';	
			}
		}
       
    }
	
	function print_certificate() {  
		 
		$student_id = $this->input->post('student_id');
		$student_name = $this->input->post('student_name');
		$award_name = $this->input->post('award_name');
		$award_date = $this->input->post('award_date');
        $quarter_id = $this->input->post('quarter_id');
        $with_sig = $this->input->post('with_sig');
        $quarter_student_grade = $this->input->post('grade');
		
		$student_session_data = $this->session->userdata("student");
        $school_name = $student_session_data['sch_name']; 
        $student_result = $this->student_model->get( $student_id ); 
        if( $student_result['middlename'] ){
            $middlename = substr($student_result['middlename'], 0, 1).".";
			$middlename = strtoupper( $middlename );
        } else {
            $middlename = '';
        }
        
        $student_teacher_advisory =  $this->classsection_model->get_teacher_advisory( $student_result['class_id'],  $student_result['section_id']);
		$student_teacher_advisory_lastname = isset($student_teacher_advisory->lastname )?$student_teacher_advisory->lastname:'';
        $student_teacher_advisory_name = isset($student_teacher_advisory->name )?$student_teacher_advisory->name:'';
        $student_teacher_advisory_middlename = isset($student_teacher_advisory->middlename )?$student_teacher_advisory->middlename:'';
		$setting_result = $this->setting_model->get();
        
        $principal_id = $setting_result[0]['principal_id'];
        $session = $setting_result[0]['session'];
        $principal_result = $this->principal_model->get(  $principal_id  ); 
		$principal_result_lastname = isset($principal_result['lastname'] )?$principal_result['lastname'] :'';
		$principal_result_name = isset($principal_result['name'] )?$principal_result['name'] :'';
		$principal_result_middlename = isset($principal_result['middlename'] )?$principal_result['middlename'] :'';
        $data['principal_name'] = $principal_result_lastname.', '.$principal_result_name.' '.$principal_result_middlename;
       
        $date_presented = $award_date; 
        $data[ 'date'] = date("F/d/Y", strtotime($date_presented)); 
        $data[ 'date_year'] = date("Y", strtotime($date_presented)); 
        $data[ 'date_month'] = date("F", strtotime($date_presented)); 
        $data[ 'date_day'] = date("dS", strtotime($date_presented)); 
        $data[ 'award_name'] = $award_name;
        // $data[ 'student_name'] = $student_result['lastname'].', '.$student_result['firstname'].' '.$middlename; 
        $data[ 'student_name'] = $student_result['firstname'].' '.$middlename.' '.$student_result['lastname'];
        // $data[ 'teacher_name'] = $student_teacher_advisory_lastname.', '.$student_teacher_advisory_name.' '.$student_teacher_advisory_middlename;
        $student_teacher_advisory_middlename = !empty($student_teacher_advisory_middlename)?substr($student_teacher_advisory_middlename, 0, 1).".":'';
        $data[ 'teacher_name'] = $student_teacher_advisory_name.' '.$student_teacher_advisory_middlename.' '.$student_teacher_advisory_lastname;
        $data[ 'school_name'] = $school_name;
        $data[ 'class_name'] = $student_result['class'];
        $data[ 'grade'] = $quarter_student_grade;
        $data[ 'quarter'] = ucfirst($quarter_id).$this->ordinal_suffix($quarter_id );
        $data[ 'with_sig'] = $with_sig;
        $data[ 'school_year'] = $session; 
		if( $award_name == 'Highest Honor'){
			$data[ 'body_image']          = base_url()."uploads/template_documents/certificate/certificate_highest.png";
			$data[ 'body_image_portrait'] = base_url()."uploads/template_documents/certificate/certificate_highest_portrait.png";
		} elseif( $award_name == 'High Honor'){
			$data[ 'body_image']          = base_url()."uploads/template_documents/certificate/certificate_high.png";
			$data[ 'body_image_portrait'] = base_url()."uploads/template_documents/certificate/certificate_high_portrait.png";
		} elseif( $award_name == 'With Honor'){
			$data[ 'body_image']          = base_url()."uploads/template_documents/certificate/certificate_with.png";
			$data[ 'body_image_portrait'] = base_url()."uploads/template_documents/certificate/certificate_with_portrait.png";
		} else {
			$data[ 'body_image']          = base_url()."uploads/template_documents/certificate/certificate_blank.png";
			$data[ 'body_image_portrait'] = base_url()."uploads/template_documents/certificate/certificate_blank_portrait.png";
		}
       /* $data[ 'body_image'] = base_url()."uploads/template_documents/certificate/certificate3.png"; */
        $file_name = "Certificate_".$data['award_name']."_".$student_result['lastname'].".pdf";

        // set orientation and body_image_portrait in $data BEFORE loading the view
        $orientation = $this->input->post('orientation');
        $data['orientation'] = $orientation;

        $html = $this->load->view('template/certificate/certificate_awards', $data, true);

        $page_orientation = ($orientation == 'portrait') ? 'P' : 'L';
        $this->mpdf = new mPDF($page_orientation . '-LETTER');
        $this->mpdf->AddPage(
            $page_orientation,
            'LETTER', '', '', '',
            0, // margin_left
            0, // margin_right
            0, // margin_top
            0, // margin_bottom
            0, // margin_header
            0  // margin_footer
        );

        $this->mpdf->WriteHTML($html);
        $this->mpdf->Output($file_name, 'D'); // download force
    }
	
	function print_certificate_batch() {

        // Safety check to ensure batch_id is provided
        $batch_id = $this->input->post('batch_id');

        if (empty($batch_id)) {
            echo "No students selected.";
            return;
        }

        $certificate_data = array();

        $batch_id        = $this->input->post('batch_id');
        $batch_awards    = $this->input->post('awards');
        $batch_grade     = $this->input->post('grade');
        $class_id        = $this->input->post('class_id');
        $section_id      = $this->input->post('section_id');
        $award_date      = $this->input->post('award_date');
        $quarter_id      = $this->input->post('quarter');
        $with_sig        = $this->input->post('with_sig');
        $orientation     = $this->input->post('orientation');

        $section_details = $this->section_model->get($section_id);
        $section_name    = $section_details['section'];

        $class_details = $this->class_model->get($class_id);
        $class_name    = $class_details['class'];

        $student_session_data = $this->session->userdata("student");
        $school_name = $student_session_data['sch_name'];

        $setting_result = $this->setting_model->get();
        $principal_id   = $setting_result[0]['principal_id'];
        $session        = $setting_result[0]['session'];

        $principal_result = $this->principal_model->get($principal_id);

        $principal_result_lastname   = isset($principal_result['lastname']) ? $principal_result['lastname'] : '';
        $principal_result_name       = isset($principal_result['name']) ? $principal_result['name'] : '';
        $principal_result_middlename = isset($principal_result['middlename']) ? $principal_result['middlename'] : '';

        $date_presented = $award_date;
        $certificate_data['date']       = date("F/d/Y", strtotime($date_presented));
        $certificate_data['date_year']  = date("Y", strtotime($date_presented));
        $certificate_data['date_month'] = date("F", strtotime($date_presented));
        $certificate_data['date_day']   = date("dS", strtotime($date_presented));
        $certificate_data['with_sig']   = $with_sig;

        $this->mpdf = new mPDF();
        $this->mpdf->SetDisplayMode('fullpage');

        // ── Build a flat list of all student certificate data first ──────────
        $all_certificates = array();

        for ($x = 0; $x < count($batch_id); $x++) {

            if (!isset($batch_id[$x])) continue;

            $student_id            = $batch_id[$x];
            $award_name            = $batch_awards[$x];
            $quarter_student_grade = $batch_grade[$x];

            $student_result = $this->student_model->get($student_id);

            // Format middlename
            if ($student_result['middlename']) {
                $middlename = strtoupper(substr($student_result['middlename'], 0, 1) . ".");
            } else {
                $middlename = '';
            }

            // Get adviser per student
            $student_teacher_advisory = $this->classsection_model
                ->get_teacher_advisory($student_result['class_id'], $student_result['section_id']);

            $adv_lastname   = isset($student_teacher_advisory->lastname)   ? $student_teacher_advisory->lastname   : '';
            $adv_name       = isset($student_teacher_advisory->name)       ? $student_teacher_advisory->name       : '';
            $adv_middlename = isset($student_teacher_advisory->middlename) ? $student_teacher_advisory->middlename : '';

            $teacher_middle_abbr = !empty($adv_middlename) ? substr($adv_middlename, 0, 1) . "." : '';

            // Resolve certificate background images
            if ($award_name == 'Highest Honor') {
                $body_image          = base_url() . "uploads/template_documents/certificate/certificate_highest.png";
                $body_image_portrait = base_url() . "uploads/template_documents/certificate/certificate_highest_portrait.png";
            } elseif ($award_name == 'High Honor') {
                $body_image          = base_url() . "uploads/template_documents/certificate/certificate_high.png";
                $body_image_portrait = base_url() . "uploads/template_documents/certificate/certificate_high_portrait.png";
            } elseif ($award_name == 'With Honor') {
                $body_image          = base_url() . "uploads/template_documents/certificate/certificate_with.png";
                $body_image_portrait = base_url() . "uploads/template_documents/certificate/certificate_with_portrait.png";
            } else {
                $body_image          = base_url() . "uploads/template_documents/certificate/certificate_blank.png";
                $body_image_portrait = base_url() . "uploads/template_documents/certificate/certificate_blank_portrait.png";
            }

            $all_certificates[] = array(
                'principal_name'     => $principal_result_lastname . ', ' . $principal_result_name . ' ' . $principal_result_middlename,
                'award_name'         => $award_name,
                'student_name'       => $student_result['firstname'] . ' ' . $middlename . ' ' . $student_result['lastname'],
                'teacher_name'       => $adv_name . ' ' . $teacher_middle_abbr . ' ' . $adv_lastname,
                'school_name'        => $school_name,
                'class_name'         => $class_name,
                'grade'              => $quarter_student_grade,
                'quarter'            => ucfirst($quarter_id) . $this->ordinal_suffix($quarter_id),
                'school_year'        => $session,
                'date'               => $certificate_data['date'],
                'date_year'          => $certificate_data['date_year'],
                'date_month'         => $certificate_data['date_month'],
                'date_day'           => $certificate_data['date_day'],
                'with_sig'           => $with_sig,
                'body_image'         => $body_image,
                'body_image_portrait'=> $body_image_portrait,
                'orientation'        => $orientation,
            );
        }

        // ── Render pages ─────────────────────────────────────────────────────
        if ($orientation == 'portrait') {

            // Portrait: 2 students per page (top + bottom)
            $total = count($all_certificates);
            for ($i = 0; $i < $total; $i += 2) {

                // New page for every pair
                $this->mpdf->AddPage('P', 'LETTER', '', '', '', 0, 0, 0, 0, 0, 0);

                // Top certificate (first student of the pair)
                $top_data                  = $all_certificates[$i];
                $top_data['cert_position'] = 'top';
                $html = $this->load->view('template/certificate/certificate_awards_batch', $top_data, true);
                $this->mpdf->WriteHTML($html);

                // Bottom certificate (second student of the pair, if exists)
                if (isset($all_certificates[$i + 1])) {
                    $bottom_data                  = $all_certificates[$i + 1];
                    $bottom_data['cert_position'] = 'bottom';
                    $html = $this->load->view('template/certificate/certificate_awards_batch', $bottom_data, true);
                    $this->mpdf->WriteHTML($html);
                }
            }

        } else {

            // Landscape: 1 student per page (original behaviour)
            foreach ($all_certificates as $cert) {
                $this->mpdf->AddPage('L', 'LETTER', '', '', '', 0, 0, 0, 0, 0, 0);
                $html = $this->load->view('template/certificate/certificate_awards_batch', $cert, true);
                $this->mpdf->WriteHTML($html);
            }
        }

        $file_name = "Certificate_Batch_" . $class_name . '_' . $section_name . ".pdf";
        $this->mpdf->Output($file_name, 'D'); // download force
    }
    function tutorial_form() {
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'certificate/tutorial_form');
        $data['title'] = 'Tutorial Form';

        //get the session
        $session = $this->session_model->getAllSession();
        $session_list = $this->session_model->getAllSession(); 
        $setting_result = $this->setting_model->get();
        $session = $setting_result[0]['session'];

        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $year = $this->input->post("year");
        
        $class = $this->class_model->get();
        
        if( $class_id && $section_id ){
            $student_result = $this->student_model->searchByClassSection($class_id, $section_id, null, $year) ;
        } else {
            $student_result = array();
        }

        $data['school_year'] = $session_list;
        $data["year_id"] = $year;
        $data['classlist'] = $class;
        $data['class_id'] = $class_id;
        $data['section_id'] = $section_id;
        $data['student_result'] = $student_result;
        
        $this->load->view('layout/registrar/header', $data);
        $this->load->view('registrar/certificate/tutorial_form_list', $data);
        $this->load->view('layout/registrar/footer', $data);
    }

    function print_tutorial_form_pdf() {
        $student_id = $this->input->post('student_id');
        if (!$student_id) {
            redirect('registrar/certificate/tutorial_form');
        }

        $this->load->library('m_pdf');
        $this->mpdf = new mPDF();
        
        $setting_result = $this->setting_model->get();
        $session = $setting_result[0]['session'];

        $certificate_result = $this->student_model->get($student_id);
        
        // Pass all POST data directly to the template
        $data = $this->input->post();
        $data['class'] = $certificate_result['class'];
        $data['firstname'] = $certificate_result['firstname'];
        $data['lastname'] = $certificate_result['lastname'];
        $data['middlename'] = $certificate_result['middlename'];
        $data['student_name'] = $certificate_result['firstname'] . ' ' . (isset($certificate_result['middlename'][0]) ? substr($certificate_result['middlename'], 0, 1) . '.' : '') . ' ' . $certificate_result['lastname'];
        
        // Determine Grade Level for title (Junior vs Senior)
        $grade_number = intval(preg_replace('/[^0-9]/', '', $data['class']));
        if ($grade_number >= 11) {
            $data['form_title'] = 'Form 4: PETITION FORM FOR TUTORIAL STUDY &ndash; SENIOR HIGH SCHOOL';
        } else {
            $data['form_title'] = 'Form 4: PETITION FORM FOR TUTORIAL STUDY &ndash; JUNIOR HIGH SCHOOL';
        }

        $html = $this->load->view('template/certificate/template_tutorial_form', $data, true);
        $pdfFilePath = "Tutorial_Form_".$certificate_result['lastname']."_".$certificate_result['firstname'].".pdf";

        $this->m_pdf->pdf = new mPDF('','LETTER','0','','0','0','0','0','0','0', 'L');
        $this->m_pdf->pdf->AddPage('L', 'LETTER', '', '', '', 15, 15, 8, 0, 8, 0);
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, 'D'); // force download
    }
}
?>