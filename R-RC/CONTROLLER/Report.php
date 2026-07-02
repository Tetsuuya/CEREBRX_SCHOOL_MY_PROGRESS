<?php

if (!defined('BASEPATH'))

    exit('No direct script access allowed');



class Report extends CI_Controller {



    function __construct() {

        parent::__construct();

        $this->load->helper('file');

        $this->time = strtotime(date('d-m-Y H:i:s'));

        $this->lang->load('message', 'english');

        $this->load->library('auth');

        $this->load->library('excel');

        $this->auth->is_logged_in_registrar();

        $this->load->model('principal_model');

		$this->load->model('gradingsetting_model');

		$this->load->model('examresult_model');

		$this->load->model('conduct_model');

		$this->load->model('conductsession_model');

		$this->load->model('conductimportbatchdetail_model');

		$this->load->model('studentform_model');

        $this->load->model('studentformsettings_model');

        $this->load->model('category_model');

        $this->load->model('categoryclass_model');

        $this->load->model('specializedsubject_model');

        $this->load->model('customsubject_model');

        $this->load->model('strand_model');

        $this->load->model('setting_model');

        $this->load->model('session_model');

        $this->load->model('stuattendencecustom_model'); 

		$this->load->model('subjectmanager_model');

        $this->load->model('subjectcombine_model');

        $this->load->model('registrar_model');

        $this->load->model('setting_model');

		$this->load->model('studentstrand_model');

        $this->load->model('conductimportsubject_model');

        date_default_timezone_set('Asia/Manila');

        $this->load->model('registrar_model');

    }



    function pdfStudentFeeRecord() {

        $data = [];

        $class_id = $this->uri->segment(4);

        $section_id = $this->uri->segment(5);

        $student_id = $this->uri->segment(6);

        $student = $this->student_model->get($student_id);

        $setting_result = $this->setting_model->get();

        $data['settinglist'] = $setting_result;

        $data['student'] = $student;

        $student_due_fee = $this->studentfee_model->getDueFeeBystudent($class_id, $section_id, $student_id);

        $data['student_due_fee'] = $student_due_fee;

        $html = $this->load->view('registrar/reports/students_detail', $data, true);

        $pdfFilePath = $this->time . ".pdf";

        $this->fontdata = array(

            "opensans" => array(

                'R' => "OpenSans-Regular.ttf",

                'B' => "OpenSans-Bold.ttf",

                'I' => "OpenSans-Italic.ttf",

                'BI' => "OpenSans-BoldItalic.ttf",

            ),

        );

        $this->load->library('m_pdf');

        $this->m_pdf->pdf->WriteHTML($html);

        $this->m_pdf->pdf->Output($pdfFilePath, "D");

    }



    function marksreport() {

        $setting_result = $this->setting_model->get();

        $data['settinglist'] = $setting_result;

        $exam_id = $this->uri->segment(4);

        $class_id = $this->uri->segment(5);

        $section_id = $this->uri->segment(6);

        $data['exam_id'] = $exam_id;

        $data['class_id'] = $class_id;

        $data['section_id'] = $section_id;

        $exam_arrylist = $this->exam_model->get($exam_id);

        $data['exam_arrylist'] = $exam_arrylist;

        $section = $this->section_model->getClassNameBySection($class_id, $section_id);

        $data['class'] = $section;

        $examSchedule = $this->examschedule_model->getDetailbyClsandSection($class_id, $section_id, $exam_id);

        $studentList = $this->student_model->searchByClassSection($class_id, $section_id);

        $data['examSchedule'] = array();

        if (!empty($examSchedule)) {

            $new_array = array();

            $data['examSchedule']['status'] = "yes";

            foreach ($studentList as $stu_key => $stu_value) {

                $array = array();

                $array['student_id'] = $stu_value['id'];

                $array['roll_no'] = $stu_value['roll_no'];

                $array['firstname'] = $stu_value['firstname'];

                $array['lastname'] = $stu_value['lastname'];

                $array['admission_no'] = $stu_value['admission_no'];

                $array['dob'] = $stu_value['dob'];

                $array['father_name'] = $stu_value['father_name'];

                $x = array();

                foreach ($examSchedule as $ex_key => $ex_value) {

                    $exam_array = array();

                    $exam_array['exam_schedule_id'] = $ex_value['id'];

                    $exam_array['exam_id'] = $ex_value['exam_id'];

                    $exam_array['full_marks'] = $ex_value['full_marks'];

                    $exam_array['passing_marks'] = $ex_value['passing_marks'];

                    $exam_array['exam_name'] = $ex_value['name'];

                    $exam_array['exam_type'] = $ex_value['type'];

                    $student_exam_result = $this->examresult_model->get_result($ex_value['id'], $stu_value['id']);

                    if (empty($student_exam_result)) {

                        $data['examSchedule']['status'] = "no";

                    } else {

                        $exam_array['attendence'] = $student_exam_result->attendence;

                        $exam_array['get_marks'] = $student_exam_result->get_marks;

                    }

                    $x[] = $exam_array;

                }

                $array['exam_array'] = $x;

                $new_array[] = $array;

            }

            $data['examSchedule']['result'] = $new_array;

        } else {

            $s = array('status' => 'no');

            $data['examSchedule'] = $s;

        }

        $html = $this->load->view('reports/marksreport', $data, true);

        $pdfFilePath = $this->time . ".pdf";

        $this->load->library('m_pdf');

        $this->m_pdf->pdf->WriteHTML($html);

        $this->m_pdf->pdf->Output($pdfFilePath, "D");

        $this->load->view('reports/marksreport', $data);

    }



    function pdfByInvoiceNo() {

        $data = [];

        $invoice_id = $this->uri->segment(3);

        $setting_result = $this->setting_model->get();

        $data['settinglist'] = $setting_result;

        $student_due_fee = $this->studentfee_model->getFeeByInvoice($invoice_id);

        $data['student_due_fee'] = $student_due_fee;

        $html = $this->load->view('registrar/reports/pdfinvoiceno', $data, true);

        $pdfFilePath = $this->time . ".pdf";

        $this->load->library('m_pdf');

        $this->m_pdf->pdf->WriteHTML($html);

        $this->m_pdf->pdf->Output($pdfFilePath, "D");

    }



    function pdfDepositeFeeByStudent($id) {

        $data = [];

        $data['title'] = 'Student Detail';

        $student = $this->student_model->get($id);

        $setting_result = $this->setting_model->get();

        $data['settinglist'] = $setting_result;

        $student_fee_history = $this->studentfee_model->getStudentFees($id);

        $data['student_fee_history'] = $student_fee_history;

        $data['student'] = $student;

        $array = array();

        $feecategory = $this->feecategory_model->get();

        foreach ($feecategory as $key => $value) {

            $dataarray = array();

            $value_id = $value['id'];

            $dataarray[$value_id] = $value['category'];

            $category = $value['category'];

            $datatype = array();

            $data_fee_type = array();

            $feetype = $this->feetype_model->getFeetypeByCategory($value['id']);

            foreach ($feetype as $feekey => $feevalue) {

                $ftype = $feevalue['id'];

                $datatype[$ftype] = $feevalue['type'];

            }

            $data_fee_type[] = $datatype;

            $dataarray[$category] = $datatype;

            $array[] = $dataarray;

        }

        $data['category_array'] = $array;

        $data['feecategory'] = $feecategory;

        $html = $this->load->view('registrar/reports/pdfStudentDeposite', $data, true);

        $pdfFilePath = $this->time . ".pdf";

        $this->load->library('m_pdf');

        $this->m_pdf->pdf->WriteHTML($html);

        $this->m_pdf->pdf->Output($pdfFilePath, "D");

    }



    function pdfStudentListByText() {

        $data = [];

        $search_text = $this->uri->segment(3);

        $setting_result = $this->setting_model->get();

        $data['settinglist'] = $setting_result;

        $resultlist = $this->student_model->searchFullText($search_text);

        $data['resultlist'] = $resultlist;

        $html = $this->load->view('registrar/reports/pdfStudentListByText', $data, true);

        $pdfFilePath = $this->time . ".pdf";

        $this->load->library('m_pdf');

        $this->m_pdf->pdf->WriteHTML($html);

        $this->m_pdf->pdf->Output($pdfFilePath, "D");

    }



    function pdfStudentListByClassSection() {

        $data = [];

        $class_id = $this->uri->segment(3);

        $section_id = $this->uri->segment(4);

        $setting_result = $this->setting_model->get();

        $section = $this->section_model->getClassNameBySection($class_id, $section_id);

        $data['class'] = $section;

        $data['settinglist'] = $setting_result;

        $resultlist = $this->student_model->searchByClassSection($class_id, $section_id);

        $data['resultlist'] = $resultlist;

        $html = $this->load->view('registrar/reports/pdfStudentListByClassSection', $data, true);

        $pdfFilePath = $this->time . ".pdf";

        $this->load->library('m_pdf');

        $this->m_pdf->pdf->WriteHTML($html);

        $this->m_pdf->pdf->Output($pdfFilePath, "D");

    }

// Added by geylou 3/11/2026
   function graduating_student_ranking(){
    $this->session->set_userdata('top_menu', 'Reports');
    $this->session->set_userdata('sub_menu', 'report/graduating_ranking');

    
    $data['classlist']       = $this->class_model->get();
    $data['getquarter']      = $this->customlib->getQuarter();
    $data['getSemester']     = $this->customlib->getSemester();
    $data['sessionlist']     = $this->session_model->getAllSession();
    $data['current_session'] = $this->setting_model->getCurrentSessionName();

    $setting_result     = $this->setting_model->get();
    $data['session_id'] = $setting_result[0]['session_id'];
    $data['class_id']   = "";
    $data['section_id'] = "";
    $data['date']       = "";
    $data['quarter']    = "";
    $data['semester']   = "";


    $class            = "15";
    $section          = "all";
    $quarter          = 'final';
    $semester         = '1';
    $combine_category = 'ga';

    $month      = $this->input->post('month');
    $student_id = $this->input->post('student_id');
    $action     = $this->input->post('action');

    $data['class_id']       = $class;
    $data['section_id']     = $section;
    $data['month_selected'] = $month;
    $data['quarter']        = $quarter;
    $data['semester']       = $semester;
    $data['is_all']         = true;


    $studentlist    = $this->student_model->getStudentIDAndStrandIDBySessionIDAndClassID($class);
    $studentranking = $this->grade_model->getG12FinalAvg($studentlist, $quarter, $semester, $combine_category);

    unset($studentranking['quarter']);
    unset($studentranking['semester']);
   
    
    $studentlist    = $this->student_model->getStudentIDAndStrandIDBySessionIDAndClassID($class);
$studentranking = $this->grade_model->getG12FinalAvg($studentlist, $quarter, $semester, $combine_category);

unset($studentranking['quarter']);
unset($studentranking['semester']);

// Extract student IDs from ranking
$student_ids = array_column(
    array_filter($studentranking, function($item) {
        return is_array($item) && isset($item['id']);
    }),
    'id'
);

$allG11Grades = $this->grade_model->getAllPreviousG11FinalAvg($student_ids, 'ga');

foreach($studentranking as &$student){
    if(!is_array($student)) continue;
    
    $sid = (int)$student['id']; // cast to int to match array key type
    
    $g11 = isset($allG11Grades[$sid]) ? (float)$allG11Grades[$sid] : null;
    
    // ✅ Save G11 grade so the view can display it in the "G11 Final Grade" column
    $student['g11_grade'] = $g11;
    
    if(!empty($g11) && $student['final_grade'] != null){
        $student['final_grade'] = round(($g11 + (float)$student['final_grade']) / 2, 2);
    }
}
unset($student);

 
    usort($studentranking, function($a, $b) {
        if($a['final_grade'] == $b['final_grade']) return 0;
        return ($a['final_grade'] < $b['final_grade']) ? 1 : -1;
    });

    $studentranking['quarter']  = $quarter;
    $studentranking['semester'] = $semester;

   
    $data['studentlist'] = $this->grade_model->getgraderanking($studentranking);
    // printx($data['studentlist']);
    // printx($data);

    $this->load->view('layout/registrar/header', $data);
    $this->load->view('registrar/reports/graduate_ranking', $data);
    $this->load->view('layout/registrar/footer', $data);
}

    function pdfStudentListDifferentCriteria() {

        $data = [];

        $class_id = $this->input->get('class_id');

        $section_id = $this->input->get('section_id');

        $category_id = $this->input->get('category_id');

        $gender = $this->input->get('gender');

        $rte = $this->input->get('rte');

        $setting_result = $this->setting_model->get();

        $class = $this->class_model->get($class_id);

        $data['class'] = $class;

        if ($section_id != "") {

            $section = $this->section_model->getClassNameBySection($class_id, $section_id);

            $data['section'] = $section;

        }

        if ($gender != "") {

            $data['gender'] = $gender;

        }

        if ($rte != "") {

            $data['rte'] = $rte;

        }

        if ($category_id != "") {

            $category = $this->category_model->get($category_id);

            $data['category'] = $category;

        }

        $data['settinglist'] = $setting_result;

        $resultlist = $this->student_model->searchByClassSectionCategoryGenderRte($class_id, $section_id, $category_id, $gender, $rte);

        $data['resultlist'] = $resultlist;

        $html = $this->load->view('registrar/reports/pdfStudentListDifferentCriteria', $data, true);

        $pdfFilePath = $this->time . ".pdf";

        $this->load->library('m_pdf');

        $this->m_pdf->pdf->WriteHTML($html);

        $this->m_pdf->pdf->Output($pdfFilePath, "D");

    }



    function pdfStudentListByClass() {

        $data = [];

        $class_id = $this->uri->segment(3);

        $section_id = "";

        $setting_result = $this->setting_model->get();

        $section = $this->class_model->get($class_id);

        $data['class'] = $section;

        $data['settinglist'] = $setting_result;

        $resultlist = $this->student_model->searchByClassSection($class_id, $section_id);

        $data['resultlist'] = $resultlist;

        $html = $this->load->view('registrar/reports/pdfStudentListByClass', $data, true);

        $pdfFilePath = $this->time . ".pdf";

        $this->load->library('m_pdf');

        $this->m_pdf->pdf->WriteHTML($html);

        $this->m_pdf->pdf->Output($pdfFilePath, "D");

    }



    function transactionSearch() {

        $data = [];

        echo "string";

        $date_from = $this->uri->segment(3);

        $date_to = $this->uri->segment(4);

        $setting_result = $this->setting_model->get();

        $data['exp_title'] = 'Transaction From ' . $date_from . " To " . $date_to;

        $date_from = date('Y-m-d', strtotime($date_from));

        $date_to = date('Y-m-d', strtotime($date_to));

        $expenseList = $this->expense_model->search("", $date_from, $date_to);

        $feeList = $this->studentfee_model->getFeeBetweenDate($date_from, $date_to);

        $data['expenseList'] = $expenseList;

        $data['feeList'] = $feeList;

        $data['settinglist'] = $setting_result;

        $html = $this->load->view('registrar/reports/transactionSearch', $data, true);

        $pdfFilePath = $this->time . ".pdf";

        $this->load->library('m_pdf');

        $this->m_pdf->pdf->WriteHTML($html);

        $this->m_pdf->pdf->Output($pdfFilePath, "D");

    }



    function pdfExamschdule() {

        $data = [];

        $setting_result = $this->setting_model->get();

        $data['settinglist'] = $setting_result;

        $exam_id = $this->uri->segment(4);

        $section_id = $this->uri->segment(5);

        $class_id = $this->uri->segment(6);

        $class = $this->class_model->get($class_id);

        $data['class'] = $class;

        $examSchedule = $this->examschedule_model->getDetailbyClsandSection($class_id, $section_id, $exam_id);

        $section = $this->section_model->getClassNameBySection($class_id, $section_id);

        $data['section'] = $section;

        $data['examSchedule'] = $examSchedule;

        $exam = $this->exam_model->get($exam_id);

        $data['exam'] = $exam;

        $html = $this->load->view('reports/examSchedule', $data, true);

        $pdfFilePath = $this->time . ".pdf";

        $this->load->library('m_pdf');

        $this->m_pdf->pdf->WriteHTML($html);

        $this->m_pdf->pdf->Output($pdfFilePath, "D");

    }



  

	

	function student_ranking(){

        $this->session->set_userdata('top_menu', 'Reports');

        $this->session->set_userdata('sub_menu', 'report/student_ranking');

		

        $class = $this->class_model->get();

        $data['classlist'] = $class;

        $data['getquarter'] = $this->customlib->getQuarter();

        $data['class_id'] = "";

        $data['section_id'] = "";

        $data['date'] = "";

        $data['quarter'] = "";

		// new

		$data['getSemester'] = $this->customlib->getSemester();

		$data['semester'] ='';

        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');

        //$this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');

        $this->form_validation->set_rules('quarter', 'Quarter', 'trim|required|xss_clean');

        

        if ($this->form_validation->run() == FALSE) {

            $this->load->view('layout/registrar/header', $data);

            $this->load->view('registrar/reports/student_ranking', $data);

            $this->load->view('layout/registrar/footer', $data);

        } else {

            $resultlist = array();

            $class = $this->input->post('class_id');

            $section = $this->input->post('section_id');

            $month = $this->input->post('month');

            $quarter = $this->input->post('quarter');



            $student_id = $this->input->post('student_id');

            $semester = $this->input->post('semester');

            $action = $this->input->post('action');



            $data['class_id'] = $class;

            $data['section_id'] = $section;

            $data['month_selected'] = $month;

            $data['quarter'] = $quarter;

            $data['semester'] = $semester;

			$combine_category = 'ga';

            // $studentlist = $this->student_model->searchByClassSection($class, $section);

            /* $studentlist = $this->student_model->getrankingclasssection( $class,  $section,  $quarter, $semester );

            $studentranking = $this->grade_model->getgraderanking( $studentlist ); */

			$studentlist = $this->student_model->searchByClassSection($class, $section);

            $studentranking = $this->grade_model->getStudentAverage( $studentlist, $quarter, $semester, $combine_category );

			$getgraderanking = $this->grade_model->getgraderanking( $studentranking );

            // var_dump($quarter, $semester, $combine_category );

            // printx($studentlist);



            $data['studentlist'] = $getgraderanking;

 

            $this->load->view('layout/registrar/header', $data);

            $this->load->view('registrar/reports/student_ranking', $data);

            $this->load->view('layout/registrar/footer', $data);

        }  

    }

    

	function honor_students(){

        $this->session->set_userdata('top_menu', 'Reports');

        $this->session->set_userdata('sub_menu', 'report/student_ranking');

		

        $class = $this->class_model->get();

        $data['classlist'] = $class;

        $data['getquarter'] = $this->customlib->getQuarter();

        $data['class_id'] = "";

        $data['section_id'] = "";

        $data['date'] = "";

        $data['quarter'] = "";

		// new

		$data['getSemester'] = $this->customlib->getSemester();

		$data['semester'] ='';

        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');

        //$this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');

        $this->form_validation->set_rules('quarter', 'Quarter', 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {

            $this->load->view('layout/registrar/header', $data);

            $this->load->view('registrar/reports/honor_student', $data);

            $this->load->view('layout/registrar/footer', $data);

        } else {

            $resultlist = array();

            $class = $this->input->post('class_id');

            $section = $this->input->post('section_id');

            $month = $this->input->post('month');

            $quarter = $this->input->post('quarter');



            $student_id = $this->input->post('student_id');

            $semester = $this->input->post('semester');

            $action = $this->input->post('action');



            $data['class_id'] = $class;

            $data['section_id'] = $section;

            $data['month_selected'] = $month;

            $data['quarter'] = $quarter;

			 $data['semester'] = $semester;

            $combine_category = 'ga';

            // $studentlist = $this->student_model->searchByClassSection($class, $section);

			

            $studentlist = $this->student_model->searchByClassSection($class, $section);

			$studentranking = $this->grade_model->getStudentAverage( $studentlist, $quarter, $semester, $combine_category );

			$getgraderanking = $this->grade_model->gethonorstudent( $studentranking );

            $data['studentlist'] = $getgraderanking;

 

            $this->load->view('layout/registrar/header', $data);

            $this->load->view('registrar/reports/honor_student', $data);

            $this->load->view('layout/registrar/footer', $data);

 

        } 

    

    }

	

	function report_card(){

        $this->session->set_userdata('top_menu', 'Reports');

        $this->session->set_userdata('sub_menu', 'report/report_card');



		$data['classlist'] =  $this->class_model->get();

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

        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');

        $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');

        $this->form_validation->set_rules('quarter', 'Quarter', 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {

            $this->load->view('layout/registrar/header', $data);

            $this->load->view('registrar/reports/report_card', $data);

            $this->load->view('layout/registrar/footer', $data);

        } else {

            $class = $this->input->post('class_id');

            $section = $this->input->post('section_id');

            $month = $this->input->post('month');

            $quarter = $this->input->post('quarter');

            $student_id = $this->input->post('student_id');

            $semester_id = $this->input->post('semester_id');

			$session_id = $this->input->post('session_id');

            $action = $this->input->post('action');

            $data['class_id'] = $class;

            $data['section_id'] = $section;

            $data['month_selected'] = $month;

            $data['semester_id'] = $semester_id;

            $data['quarter'] = $quarter;

			$data['session_id'] = $session_id;

			$studentlist_male = $this->student_model->searchByClassSectionGender($class, $section, 'Male', $session_id);

			$studentlist_female = $this->student_model->searchByClassSectionGender($class, $section, 'Female', $session_id );

			$studentlist = array();

			$studentlist = array_merge( $studentlist, $studentlist_male );

			$studentlist = array_merge( $studentlist, $studentlist_female );

			$data['studentlist_male'] = $studentlist_male;

			$data['studentlist_female'] = $studentlist_female;

			$data['studentlist'] = $studentlist;

 

            $this->load->view('layout/registrar/header', $data);

            $this->load->view('registrar/reports/report_card', $data);

            $this->load->view('layout/registrar/footer', $data);

        }

    }



	function master_sheet(){

        $this->session->set_userdata('top_menu', 'Reports');

        $this->session->set_userdata('sub_menu', 'report/master_sheet');

		

        $student_results = $this->student_model->get();

        $data['studentList'] = $student_results; 

        $data['classlist'] = $this->class_model->get();

        $data['getquarter'] = $this->customlib->getQuarter();

        $data['class_id'] = "";

        $data['section_id'] = "";

        $data['quarter'] = "";

		$data['getSemester'] = $this->customlib->getSemester();

		$data['enableStrand'] = false;

		$data['semester_id'] = "";

		$data['strands'] = $this->strand_model->get();

		$session = $this->session_model->getAllSession();

		$setting_result = $this->setting_model->get();

		$data['session_id'] = $setting_result[0]['session_id'];

		$data['current_session'] = $this->setting_model->getCurrentSessionName();

		$data['sessionlist'] = $session;

        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');

        $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean'); 

        $this->form_validation->set_rules('quarter', 'Quarter', 'trim|required|xss_clean'); 

        if ($this->form_validation->run() == FALSE) {

            $this->load->view('layout/registrar/header', $data);

            $this->load->view('registrar/reports/master_sheet', $data);

            $this->load->view('layout/registrar/footer', $data);

        } else {

            $resultlist = array();

            $class = $this->input->post('class_id');

            $section = $this->input->post('section_id');

            $month = $this->input->post('month');

            $quarter = $this->input->post('quarter');  

            $action = $this->input->post('action');

			$semester_id = $this->input->post('semester_id');

			$session_id = $this->input->post('session_id');

            $data['class_id'] = $class;

            $data['section_id'] = $section;

            $data['month_selected'] = $month;

            $data['quarter'] = $quarter; 

			$data['semester_id'] = $semester_id; 

			$data['session_id'] = $session_id; 

			$enableStrand =  $this->strand_model->checkEnable( $class );

           	$data['enableStrand'] = $enableStrand;

            $subjectlist = $this->subject_model->getSubjctByClass( $class ); 

          //  $studentlist = $this->student_model->searchByClassSection($class, $section, 'lastname', $session_id );

			$studentlist_male = $this->student_model->searchByClassSectionGender($class, $section, 'Male', $session_id );

			$studentlist_female = $this->student_model->searchByClassSectionGender($class, $section, 'Female', $session_id );

			$studentlist = array();

			$data['studentlist_male'] = $studentlist_male;

			$data['studentlist_female'] = $studentlist_female;

			$studentlist = array_merge( $studentlist, $studentlist_male );

			$studentlist = array_merge( $studentlist, $studentlist_female );

			$data['studentlist'] = $studentlist;

			$data['subjectlist'] = $subjectlist;

        

			$gradingsettings = $this->gradingsetting_model->getbySession( $session_id );

			$data['decimal_grades']  = isset($gradingsettings->decimal_grades)?$gradingsettings->decimal_grades:0;

			$data['decimal_average'] = isset($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;

			$data['decimal_finalgrade'] = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:2;

			$data['if_gradeisnull'] = isset($gradingsettings->if_gradeisnull)?$gradingsettings->if_gradeisnull:'blank';

			$data['allow_to_pass'] = isset($gradingsettings->allow_to_pass)?$gradingsettings->allow_to_pass:'allow_to_pass';

			$data['combine_category'] = 'ga';

 

            $this->load->view('layout/registrar/header', $data);

            $this->load->view('registrar/reports/master_sheet', $data);

            $this->load->view('layout/registrar/footer', $data);



            if( isset($action) && $action == 'print_master_sheet'){  

                $this->print_master_sheet( $class, $section, $quarter, $semester_id, $session_id );

            }

        }   

    }



    function grade_sheet(){

        $this->session->set_userdata('top_menu', 'Reports');

        $this->session->set_userdata('sub_menu', 'report/grade_sheet');

        

        $student_results = $this->student_model->get();

        $data['studentList'] = $student_results;      

        $class = $this->class_model->get();  

        $data['classlist'] = $class;

        $data['getquarter'] = $this->customlib->getQuarter();

        $data['class_id'] = "";

        $data['section_id'] = "";

        $data['subject_id'] = "";

        $data['quarter'] = "";

        $data['semester_id'] = "";

        $data['getSemester'] = $this->customlib->getSemester();

        $data['enableStrand'] = false;

		$session = $this->session_model->getAllSession();

		$setting_result = $this->setting_model->get();

		$data['session_id'] = $setting_result[0]['session_id'];

		$data['current_session'] = $this->setting_model->getCurrentSessionName();

		$data['sessionlist'] = $session;

        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');

        $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean'); 

        $this->form_validation->set_rules('subject_id', 'Subject', 'trim|required|xss_clean'); 

        if ($this->form_validation->run() == FALSE) {

            $this->load->view('layout/registrar/header', $data);

            $this->load->view('registrar/reports/grade_sheet', $data);

            $this->load->view('layout/registrar/footer', $data);

        } else {

            $resultlist = array();

            $class = $this->input->post('class_id');

            $section = $this->input->post('section_id');

            $subject = $this->input->post('subject_id');

            $month = $this->input->post('month');

            $quarter = $this->input->post('quarter');  

            $action = $this->input->post('action');

            $semester_id = $this->input->post('semester_id');

            $session_id = $this->input->post('session_id');

          

            $data['session_id'] = $session_id;

            $data['class_id'] = $class;

            $data['section_id'] = $section;

            $data['subject_id'] = $subject;

            $data['month_selected'] = $month;

            $data['quarter'] = $quarter; 

            $data['semester_id'] = $semester_id; 

            $data['enableStrand'] = $this->strand_model->checkEnable( $class );

            $subjectlist = $this->subject_model->getSubjctByClass( $class, true ); 

    

            

            $studentlist_male = $this->student_model->searchByClassSectionGender($class, $section, 'Male',$session_id);

			$studentlist_female = $this->student_model->searchByClassSectionGender($class, $section, 'Female',$session_id); 

            $data['subjectlist'] = $subjectlist;

            $data['studentlist_male'] = $studentlist_male;

            $data['studentlist_female'] = $studentlist_female;

 

            $this->load->view('layout/registrar/header', $data);

            $this->load->view('registrar/reports/grade_sheet', $data);

            $this->load->view('layout/registrar/footer', $data);



            if( isset($action) && $action == 'print_grade_sheet'){  

               $this->print_grade_sheet( $class, $section, $subject, $semester_id, $session_id );

            }

        }  

    

    } 



	 public function show_report_card(){

        ini_set('display_errors', 1);

        ini_set('display_startup_errors', 1);

        error_reporting(E_ALL);

        $student_id = $this->input->post('student_id');

    

        if( $student_id ){

            $quarter =  $this->input->post('quarter');

            $semester_id =  $this->input->post('semester_id');

            $class_id = $this->input->post('class_id');

            $section_id = $this->input->post('section_id');

            $session_id = $this->input->post('session_id');

            $gdisplay_attendance = $this->input->post('gdisplay_attendance');

            $gdisplay_signature = $this->input->post('gdisplay_signature');

            $gdisplay_psignature = $this->input->post('gdisplay_psignature');

            $gdisplay_attendance = $this->input->post('gdisplay_attendance');

            $session_id = !empty( $session_id )?$session_id:$this->setting_model->getCurrentSession();

            $data['quarter'] = $quarter;

            $data['semester_id'] = $semester_id;

            $data['class_id'] = $class_id;

            $data['section_id'] = $section_id;

            $data['session_id'] = $session_id;

            $data['gdisplay_attendance'] = $gdisplay_attendance;



            // session_id 

            $session_details = $this->session_model->get( $session_id );

            $session_current = $session_details['session'];

            //$session_current = $this->setting_model->getCurrentSessionName(); 

            $centenary = substr($session_current, 0, 2); //2017-18 to 2017

            $date_today = date("Y-m-d"); 



    // var_dump( $class_id,  $section_id, $session_id);

    // die();

            $teacher_advisory =  $this->classsection_model->get_teacher_advisory( $class_id,  $section_id, $session_id );

            $teacher_lastname = isset($teacher_advisory->lastname )?$teacher_advisory->lastname:'';

            $teacher_name = isset($teacher_advisory->name )?$teacher_advisory->name:'';

            $teacher_middlename = isset($teacher_advisory->middlename )?$teacher_advisory->middlename[0]:'';

            $t_sex =  isset($teacher_advisory->sex )?$teacher_advisory->sex:'';

            $prefix = $t_sex == 'Female'?'Ms.':'Mr.';

            $data['t_sex'] =  $t_sex;

            $t_fullname = $this->setting_model->getprincipalformat( '', $teacher_name, $teacher_middlename, $teacher_lastname );

            $data['t_fullname'] = $t_fullname; 

            $data['t_signature'] = isset($teacher_advisory->signature)?$teacher_advisory->signature:''; 

            $setting_result = $this->setting_model->get();

            $principal_id = $setting_result[0]['principal_id'];

            $session = $setting_result[0]['session'];

            $principal_result = $this->principal_model->get(  $principal_id  ); 

            $principal_result_lastname = isset($principal_result['lastname'] )?$principal_result['lastname'] :'';

            $principal_result_name = isset($principal_result['name'] )?$principal_result['name'] :'';

            $principal_result_middlename = isset($principal_result['middlename'] )?$principal_result['middlename']:'';

            $p_sex = isset($principal_result['sex'] )?$principal_result['sex'] :'';

            $prefix = $p_sex == 'Female'?'Ms.':'Mr.';

            $principal_name = $this->setting_model->getprincipalformat('', $principal_result_name, $principal_result_middlename, $principal_result_lastname );

            $data['p_fullname'] = $principal_name;

            $data['p_sex'] = $p_sex; 

            

            // SETTINGS

            $data['student_session_id'] = $session_current;

            //$sch_settings = $this->setting_model->getCurrentSessiondata();

            $data['school_name'] =  $setting_result[0]['name'];

            $data['logo'] = $setting_result[0]['image'];

            $data['email'] =  $setting_result[0]['email'];

            $address_barangay_id =  $setting_result[0]['address_barangay_id'];

            $address_barangay =$this->address_model->get_barangay($address_barangay_id);

            $address_city_id =  $setting_result[0]['address_city_id'];

            $address_city =$this->address_model->get_city($address_city_id);

            $address_province_id =  $setting_result[0]['address_province_id'];

            $province_name =$this->address_model->get_province($address_province_id);

            $data['barangay'] = $address_barangay['name'];

            $data['city'] = $address_city['name'];

            $data['province'] = $province_name['name'];

            $data['address'] = $setting_result[0]['address'];

            $data['phone'] = $setting_result[0]['phone']; 

            $data['date_created'] = date('F d, Y');



            // GET ATTENDANCE 

            $hern_data = $this->stuattendencecustom_model->get_hern_attendance( $student_id, $session_id );

            $data['hern_presents'] = $hern_data['presents'];

            $data['hern_absents'] = $hern_data['absents'];

            $data['hern_lates'] = $hern_data['lates'];

            

            $gradingsettings = $this->gradingsetting_model->getbySession( $session_id );

            $get_quarter_settings = !empty($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array('1','2','3','4');    

            $get_quarter_settings = is_serialized( $get_quarter_settings )?unserialize($get_quarter_settings):$get_quarter_settings;

             $data['get_quarter_settings'] = $get_quarter_settings;

            

            $this->load->library('m_pdf');

            //$this->mpdf = new mPDF(); 

            $this->mpdf = new mPDF('utf-8',array(216 ,330)); 

            $this->mpdf->SetDisplayMode('fullwidth'); 

            $student_print = $this->input->post('student_print');

            if( count( $student_print ) > 1 || count( $student_id ) > 1){

                for( $x=0; $x<count( $student_print );$x++){

                    $this->m_pdf->pdf->AddPage('LANDSCAPE','A4','5','5','5','5','5','5','5');

                    $data['student_id'] = $student_print[$x];

                    $html = $this->get_report_card( $data );

                    $this->m_pdf->pdf->WriteHTML($html);

                    

                    

                }

                $pdfFilePath = "Form138".$data['fullname'].".pdf";

                    $this->m_pdf->pdf->Output($pdfFilePath, "D");

            } else {

                $this->m_pdf->pdf->AddPage('LANDSCAPE','A4','5','5','5','5','5','5','5');

                $data['student_id'] = $student_id;

                $html = $this->get_report_card( $data );

                $this->m_pdf->pdf->WriteHTML($html);

                $pdfFilePath = "Form138".$data['fullname'].".pdf";

                $this->m_pdf->pdf->Output($pdfFilePath, "D");

            }

        } else {

            redirect('registrar/report/report_card');

        }



    }



    public function get_report_card( $data ){

        

        if( $data ){

            

             $quarter = $data['quarter'];

             $semester_id = $data['semester_id'];

             $class_id = $data['class_id'];

             $section_id = $data['section_id'];

             $student_id = $data['student_id'];

             $filename = $data['filename'];

             $folder = $data['folder'];

             $session_id = $data['session_id'];

            

            //$student_results = $this->student_model->get( $student_id );

            $student_results = $this->student_model->getBySession( $student_id, $session_id );

            $student_firstname = $student_results['firstname'];

            $student_lastname = $student_results['lastname'];

            $student_middlename = $student_results['middlename'];

            $student_suffix = $student_results['suffix'];

            $current_address = $student_results['current_address'];



            $student_dob = $student_results['dob']; 

            $dob = new DateTime( $student_dob );

            $today = new DateTime( $date_today ); 

            $difference = $dob->diff($today);

            $student_age = $difference->y; 

            $admission_no = $student_results['admission_no']; 

            $data['fullname'] = $student_lastname.', '.$student_firstname.' '.$student_middlename.' '.$student_suffix; 

            $data['dob'] = $student_dob; 

            $data['age'] = $student_age; 

            $data['lrn'] =  $student_results['lrn'];

            $data['gender'] = $student_results['gender'];

            $data['class'] = $student_results['class'];

            $data['section'] = $student_results['section'] == 'None' ?'':$student_results['section']; 

            $data['student_id'] = $student_id; 

            $data['admission_no'] = $student_results['admission_no']; 

            $data['strand_id'] = $student_results['strand_id']; 

            $data['current_address'] = $current_address; 

            $check_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id, $session_id ); 

            if( $check_strand ){

                $data['strand_id'] = $check_strand['strand_id'];

            }  

            $strand_data = $this->strand_model->get( $data['strand_id'] );

            if( $strand_data  ){

                $data['section'] = $data['section']." (".$strand_data['code'].")"; 

            } 







            $get_layout_grade = $this->get_layout_grade( $data );

			$data = array_merge( $data, $get_layout_grade );

			$get_layout_attendance = $this->get_layout_attendance( $data );

		

			$data = array_merge( $data, $get_layout_attendance );

			$get_layout_conduct = $this->get_layout_conduct( $data );

			$data = array_merge( $data, $get_layout_conduct );



			  /*   $this->mpdf->AddPage('L', // L - landscape, P - portrait

					'', '', '', '',

					30, // margin_left

					30, // margin right

					30, // margin top

					30, // margin bottom

					18, // margin header

					12); // margin footer */

             // printx($filename);

            $html = $this->load->view('template/form138/Form138Template',$data, true ); 

		   /* $pdfFilePath = "Form138".$data['fullname'].".pdf";

		   $this->load->library('m_pdf');

		   $this->m_pdf->pdf->AddPage($orientation,$size,'10','10','10','10','10','10','10','10');

		   //$this->m_pdf->pdf->AddPage();

		   $this->m_pdf->pdf->use_kwt = true;

		   $this->m_pdf->pdf->WriteHTML($html);

		   $this->m_pdf->pdf->Output($pdfFilePath, "I"); */



			return $html;

		} else {

			redirect('registrar/report/report_card');

		}

    }



    //         //$get_layout_grade = $this->get_layout_grade( $data );

    //         //$data = array_merge( $data, $get_layout_grade );

    //         // MARQUEZ

    //         if($class_id == 14 || $class_id == 15){

    //             $get_layout_attendance = $this->get_layout_attendance_shs( $data );

    //         }else{

    //             $get_layout_attendance = $this->get_layout_attendance( $data );



    //         }



    //         // END

            

    //         $data = array_merge( $data, $get_layout_attendance );



    //        // $get_layout_conduct = $this->get_layout_conduct( $data ); 

    //         //$data = array_merge( $data, $get_layout_conduct );

            

    //           /*   $this->mpdf->AddPage('L', // L - landscape, P - portrait

    //                 '', '', '', '',

    //                 30, // margin_left

    //                 30, // margin right

    //                 30, // margin top

    //                 30, // margin bottom

    //                 18, // margin header

    //                 12); // margin footer */

    //          // printx($filename);



    //         // MARQUEZ

    //         if($class_id == 14 || $class_id == 15){

    //             $html = $this->load->view('template/form138/Form138Template_shs',$data, true ); 

    //         }else{

    //             $html = $this->load->view('template/form138/Form138Template',$data, true );

    //         }

    //         // END



    //        // $html = $this->load->view('template/form138/Form138Template',$data, true ); 

    //        /* $pdfFilePath = "Form138".$data['fullname'].".pdf";

    //        $this->load->library('m_pdf');

    //        $this->m_pdf->pdf->AddPage($orientation,$size,'10','10','10','10','10','10','10','10');

    //        //$this->m_pdf->pdf->AddPage();

    //        $this->m_pdf->pdf->use_kwt = true;

    //        $this->m_pdf->pdf->WriteHTML($html);

    //        $this->m_pdf->pdf->Output($pdfFilePath, "I"); */



    //         return $html;

    //     } else {

    //         redirect('registrar/report/report_card');

    //     }

    // }



    // MARQUEZ

    public function show_report_card_shs(){

        

        $this->load->library('m_pdf');

        //$this->mpdf = new mPDF(); 

        $this->mpdf = new mPDF('utf-8',array(216 ,330)); 

        $this->mpdf->SetDisplayMode('fullwidth'); 

      

        $this->m_pdf->pdf->AddPage('LANDSCAPE','LEGAL','3','3','3','3','3','3','3');





        $html =  $this->load->view('template/form138/Form138Template_shs'); 

        $this->m_pdf->pdf->WriteHTML(utf8_encode($html));





    }

	

	public function getSchoolYearFormat( $school_year ){

		$format = '';

		$split = explode('-',$school_year );

		

		if( $school_year ){

			for( $x=0; $x<count($split); $x++ ){

				if($x==0){

					$key = $split[$x].'-';

				} else {

					$key = date('Y',strtotime($split[$x]));

					print_r($key);

					die();

				}

				$format .= $key;

			}

		}

		

		

		return $format;

		

	}



// MARQUEZ

    public function get_layout_attendance_shs( $data ){

        $student_id = $data['student_id'];

        $session_id = $data['session_id'];

        $semester_id = $data['semester_id'];

        $gdisplay_attendance = $data['gdisplay_attendance'];



        $gradingsetting_model = $this->gradingsetting_model->getbySession( $session_id );

        $school_days = $gradingsetting_model->school_days;

        if( $school_days ){

            $school_days = is_serialized( $school_days )?unserialize($school_days):$school_days; 

            $school_days = $school_days != '' ? $school_days:array();

        }

        $startMonthName = $gradingsetting_model->school_days_first_month;

        $endMonthName = $gradingsetting_model->school_days_last_month;

        $general_semester_start = 'August';

        $general_semester_end = 'June';

        $first_sem_start = $general_semester_start;

        $first_sem_end = $general_semester_end;

        $second_sem_start = $general_semester_start;

        $second_sem_end = $general_semester_end;



 

        $startMonth = $this->setting_model->getStartMonth();

        $endMonth = $this->setting_model->getEndMonth();



        $session_details = $this->session_model->get( $session_id );

        $session_current = $session_details['session'];

        $centenary = substr($session_current, 0, 2); //2017-18 to 2017

        $year_first_substring = substr($session_current, 2, 2); //2017-18 to 2017

        $year_second_substring = substr($session_current, 5, 2); //2017-18 to 18

        $get_first_sem_start = '';

        $get_first_sem_last = '';

        $get_second_sem_start = '';

        $get_second_sem_last = '';

        if( $semester_id ){

            if( $semester_id == 1 ){

                $startMonthName = !empty( $second_sem_start )?$second_sem_start:'November';

                $endMonthName = !empty( $second_sem_end )?$second_sem_end:'March';

                $get_second_sem_start = $startMonthName;

                $get_second_sem_last = $endMonthName;

            } else {

                $startMonthName = !empty( $second_sem_start )?$second_sem_start:'November';

                $endMonthName = !empty( $second_sem_end )?$second_sem_end:'March';

                $get_second_sem_start = $startMonthName;

                $get_second_sem_last = $endMonthName;

            }

        } else {

            $startMonthName = !empty( $startMonthName )?$startMonthName:'August';

            $endMonthName = !empty( $startMonthName )?$endMonthName:'March';

        }

       

          if( empty( $semester_id  )){

            $start_timestamp = strtotime($startMonthName.' '. $centenary.$year_first_substring);

               $end_timestamp    = strtotime($endMonthName.' '. $centenary.$year_second_substring);

        } else {

            if( $semester_id == 1 ){

                $start_timestamp = strtotime($startMonthName.' '. $centenary.$year_first_substring);



               $end_timestamp    = strtotime($endMonthName.' '. $centenary.$year_second_substring);

            } else {

                if( $startMonthName == 'Jan' || $startMonthName == 'Feb' || $startMonthName == 'Mar' || $startMonthName == 'Apr' || $startMonthName == 'May'){

                    $start_timestamp = strtotime($startMonthName.' '. $centenary.$year_second_substring);

                } else {

                    $start_timestamp = strtotime($startMonthName.' '. $centenary.$year_first_substring);

                }

                

               $end_timestamp    = strtotime($endMonthName.' '. $centenary.$year_second_substring);

            }

        }

        

       

        if( $semester_id ){

            if( $semester_id == 1 ){

                // $start_timestamp = strtotime($startMonthName.' '. $centenary.$year_first_substring);

                // $end_timestamp    = strtotime($endMonthName.' '. $centenary.$year_first_substring);

                $first_second = date('Y-m-01', $start_timestamp);

                $last_second  = date('Y-m-t', $end_timestamp); 

            } else {

                $first_second = date('Y-m-01', $start_timestamp);

                $last_second  = date('Y-m-t', $end_timestamp); 

            }

            

        } else {

            $first_second = date('Y-m-01', $start_timestamp);

            $last_second  = date('Y-m-t', $end_timestamp); 

        }

        $data['startMonth'] = $first_second;

        $data['endMonth'] = $last_second; 

        $data['first_second'] = $first_second; 

        $data['last_second'] = $last_second; 

        $data['get_first_sem_start'] = $get_first_sem_start; 

        $data['get_first_sem_last'] = $get_first_sem_last; 

        $data['get_second_sem_start'] = $get_second_sem_start; 

        $data['get_second_sem_last'] = $get_second_sem_last; 

        $end = strtotime($last_second); 

        $attendance_layout_array = array();

        ob_start(); 

        $no_of_days =  array();

        $no_of_absent =  array();

        $no_of_present =  array();

        $no_of_late =  array();

            ?>

          

            <tr>

            <td style="border: 1px solid;border-top:0;"><small>Days of School</small></td>

            <?php 



                $month_count =  0; 

                $month =  ''; 

                $total_month_count =  0; 

                $start = $month = strtotime($first_second); 

                while($month < $end){   

                    $month_count =  0; 

                    $monthly_name_fd = date('F_Y', $month);   

                    $monthly_name_fd1 = date('M_Y', $month);   

                    $month = strtotime("+1 month", $month);

                    if(isset($school_days[$monthly_name_fd])){

                        $month_count = $school_days[$monthly_name_fd];

                        $total_month_count += $month_count; 

                        $no_of_days[$monthly_name_fd1] = $month_count;



                    }

                    ?> 

                    <td style="border: 1px solid;border-top:0;border-left:0;" align="center" ><small><?php echo $month_count; ?></small></td>  

                    <?php

                } 

                ?>

                <td style="border: 1px solid;border-top:0;border-left:0;" align="center" ><small><?php echo $total_month_count; ?></small></td>

            </tr>

            <!-- check for absent -->

            <?php 

                $month_count = 0;

                $month =  ''; 

                $total_month_count =  0; 

                $start = $month = strtotime($first_second); 

                $student_attendance_absent_array = $this->stuattendencecustom_model->get_attendance( $student_id, 'Absent', $session_id );

                $studdent_absent_attendance = $student_attendance_absent_array['attendance'];

                $student_attendance_absent_serialize = is_serialized( $studdent_absent_attendance )?unserialize( $studdent_absent_attendance ): $studdent_absent_attendance ;

                    while($month < $end){  

                        $month_count =  0; 

                        $monthly_name_fd = date('M_Y', $month); 

                        if( !empty( $student_attendance_absent_serialize[$monthly_name_fd])){

                            $month_count = $student_attendance_absent_serialize[$monthly_name_fd];

                        } else {                

                            $monthly_name  = date('m', $month);   

                            $monthly_name_year = date('Y', $month);   

                            $month_count = $this->stuattendence_model->get_attendance_by_user_count_status(  $student_id, $monthly_name , $monthly_name_year, 4 , $session_id ); 

                        }

                        $no_of_absent[$monthly_name_fd] = $month_count;

                        $total_month_count += $month_count; 

                        $month = strtotime("+1 month", $month);   

                    } 

                ?>

                <!-- check for present -->

                <?php 

                if( $no_of_days ){

                    foreach ( $no_of_days as $key  => $value ) {

                        $get_no_of_days = $value;

                        if( isset( $no_of_absent[$key] )) {



                            $get_no_of_absent = $no_of_absent[$key];

                            $get_present = $get_no_of_days - $get_no_of_absent;

                            $no_of_present[$key] = $get_present;

                        }

                    }

                }



                ?>

            <tr>

            <td style="border: 1px solid;border-top:0;"><small>Days Present</small></td>

                <?php 

                $total_month_count =  0; 

                $month_count = 0;

                if( $no_of_days ){

                    foreach ( $no_of_days as $key  => $value ) {

                        $get_no_of_days = $value;

                        if( isset( $no_of_present[$key] )) {

                            $month_count = $no_of_present[$key];

                        }

                        $total_month_count = $total_month_count +  $month_count; 

                        ?>

                         <td style="border: 1px solid;border-top:0;border-left:0;" align="center" >

                           <small>

                            <?php

                            if( $month_count != 0 ){

                                if( $gdisplay_attendance){

                                    echo $month_count;

                                }

                            } else {

                            }?>

                            </small>

                        </td> 

                        <?php  

                    }

                }

                ?>

                <td style="border: 1px solid;border-top:0;border-left:0;" align="center" >

                    <small>

                        <?php

                        if( $total_month_count != 0 ){

                             if( $gdisplay_attendance){

                                 echo $total_month_count;

                             }

                        } else {

                        }?>

                    </small>

                </td>

            </tr>

              <tr>

                <td style="border: 1px solid;border-top:0;"><small>Times Tardy</small></td> 

                <?php 

                $month_count = 0;

                $month =  ''; 

                $total_month_count =  0; 

                $start = $month = strtotime($first_second); 

                $student_attendance_tardy_array = $this->stuattendencecustom_model->get_attendance( $student_id, 'Late', $session_id );

                $studdent_tardy_attendance = $student_attendance_tardy_array['attendance'];

                $student_attendance_tardy_serialize = is_serialized( $studdent_tardy_attendance )?unserialize( $studdent_tardy_attendance ): $studdent_tardy_attendance ;

                while($month < $end){  

                    $month_count =  0;  

                    $monthly_name_fd = date('M_Y', $month); 

                    if( !empty( $student_attendance_tardy_serialize[$monthly_name_fd])){

                        $month_count = $student_attendance_tardy_serialize[$monthly_name_fd];

                    } else {

                        $monthly_name  = date('m', $month);   

                        $monthly_name_year = date('Y', $month);   

                        $month_count = $this->stuattendence_model->get_attendance_by_user_count_status(  $student_id, $monthly_name , $monthly_name_year, 3 , $session_id ); 

                    }

                    $total_month_count += $month_count;    

                    $month = strtotime("+1 month", $month);  

                    ?> 

                    <td style="border: 1px solid;border-top:0;border-left:0;" align="center" >

                        <small>

                        <?php

                        if( $month_count != 0 ){

                             if( $gdisplay_attendance){

                                 echo $month_count;

                             }

                        } else {

                        }?>

                        </small>

                    </td>  

                    <?php

                } 

                ?>

                <td style="border: 1px solid;border-top:0;border-left:0;" align="center">

                    <small>

                        <?php

                        if( $total_month_count != 0 ){

                             if( $gdisplay_attendance){

                                 echo $total_month_count;

                             }

                        } else {

                        }?>

                    </small>

                </td>

            </tr> 

            

            <?php

        $attendance = ob_get_contents();

         ob_clean( ); 

         

        $data['attendance'] = $attendance; 

         

         return $data;

    }

// END

	

public function get_layout_attendance( $data ){

        $student_id = $data['student_id'];

        $session_id = $data['session_id'];

        $semester_id = $data['semester_id'];

        $gdisplay_attendance = $data['gdisplay_attendance'];



        $gradingsetting_model = $this->gradingsetting_model->getbySession( $session_id );

        $school_days = $gradingsetting_model->school_days;

        if( $school_days ){

            $school_days = is_serialized( $school_days )?unserialize($school_days):$school_days; 

            $school_days = $school_days != '' ? $school_days:array();

        }



        $startMonthName = $gradingsetting_model->school_days_first_month;

        $endMonthName = $gradingsetting_model->school_days_last_month;

        $first_sem_start = $gradingsetting_model->first_sem_start;

        $first_sem_end = $gradingsetting_model->first_sem_end;

        $second_sem_start = $gradingsetting_model->second_sem_start;

        $second_sem_end = $gradingsetting_model->second_sem_end;

 

        $startMonth = $this->setting_model->getStartMonth();

        $endMonth = $this->setting_model->getEndMonth();



        $session_details = $this->session_model->get( $session_id );

        $session_current = $session_details['session'];

        $centenary = substr($session_current, 0, 2); //2017-18 to 2017

        $year_first_substring = substr($session_current, 2, 2); //2017-18 to 2017

        $year_second_substring = substr($session_current, 5, 2); //2017-18 to 18

        $get_first_sem_start = '';

        $get_first_sem_last = '';

        $get_second_sem_start = '';

        $get_second_sem_last = '';

        if( $semester_id ){

            if( $semester_id == 1 ){

                $startMonthName = !empty( $first_sem_start )?$first_sem_start:'June';

                $endMonthName = !empty( $first_sem_end )?$first_sem_end:'October';

                $get_first_sem_start = $startMonthName;

                $get_first_sem_last = $endMonthName;

            } else {

                $startMonthName = !empty( $second_sem_start )?$second_sem_start:'November';

                $endMonthName = !empty( $second_sem_end )?$second_sem_end:'March';

                $get_second_sem_start = $startMonthName;

                $get_second_sem_last = $endMonthName;

            }

        } else {

            $startMonthName = !empty( $startMonthName )?$startMonthName:'August';

            $endMonthName = !empty( $startMonthName )?$endMonthName:'March';

        }



         

          if( empty( $semester_id  )){

            $start_timestamp = strtotime($startMonthName.' '. $centenary.$year_first_substring);

               $end_timestamp    = strtotime($endMonthName.' '. $centenary.$year_second_substring);

        } else {

            if( $semester_id == 1 ){

                $start_timestamp = strtotime($startMonthName.' '. $centenary.$year_first_substring);



               $end_timestamp    = strtotime($endMonthName.' '. $centenary.$year_second_substring);

            } else {

                if( $startMonthName == 'Jan' || $startMonthName == 'Feb' || $startMonthName == 'Mar' || $startMonthName == 'Apr' || $startMonthName == 'May'){

                    $start_timestamp = strtotime($startMonthName.' '. $centenary.$year_second_substring);

                } else {

                    $start_timestamp = strtotime($startMonthName.' '. $centenary.$year_first_substring);

                }

                

               $end_timestamp    = strtotime($endMonthName.' '. $centenary.$year_second_substring);

            }

        }

        

       

        if( $semester_id ){

            if( $semester_id == 1 ){

                $start_timestamp = strtotime($startMonthName.' '. $centenary.$year_first_substring);

                $end_timestamp    = strtotime($endMonthName.' '. $centenary.$year_first_substring);

                $first_second = date('Y-m-01', $start_timestamp);

                $last_second  = date('Y-m-t', $end_timestamp); 

            } else {

                $first_second = date('Y-m-01', $start_timestamp);

                $last_second  = date('Y-m-t', $end_timestamp); 

            }

            

        } else {

            $first_second = date('Y-m-01', $start_timestamp);

            $last_second  = date('Y-m-t', $end_timestamp); 

        }



        $data['startMonth'] = $first_second;

        $data['endMonth'] = $last_second; 

        $data['first_second'] = $first_second; 

        $data['last_second'] = $last_second; 

        $data['get_first_sem_start'] = $get_first_sem_start; 

        $data['get_first_sem_last'] = $get_first_sem_last; 

        $data['get_second_sem_start'] = $get_second_sem_start; 

        $data['get_second_sem_last'] = $get_second_sem_last; 

        $end = strtotime($last_second); 

        $attendance_layout_array = array();

        ob_start(); 

            ?>

            <tr>

            <td style="border: 1px solid;"><small>Days of School</small></td>

            <?php 



                $month_count =  0; 

                $month =  ''; 

                $total_month_count =  0; 

                $start = $month = strtotime($first_second); 

                while($month < $end){   

                    $month_count =  0; 

                    $monthly_name_fd = date('F_Y', $month);   

                    $monthkey = strtolower(date('F', $month));   

                    $month = strtotime("+1 month", $month);

                    

                    $hern_count = $data['hern_presents'][$monthkey] + $data['hern_absents'][$monthkey];

                    if(isset($school_days[$monthly_name_fd])){

                        $month_count = $school_days[$monthly_name_fd];

                        $total_month_count += ($hern_count == 0 || $hern_count > $month_count ? $month_count : $hern_count); 

                    }

                    ?> 

                    <td style="border: 1px solid;" align="center" ><small><?php echo $hern_count == 0 || $hern_count > $month_count ? $month_count : $hern_count; ?></small></td>  

                    <?php

                }

                ?>

                <td style="border: 1px solid;" align="center" ><small><?php echo $total_month_count; ?></small></td>

            </tr>

            <tr>

            <td style="border: 1px solid;"><small>Days Present</small></td>

                <?php 

                $month_count = 0;

                $month =  ''; 

                $total_month_count =  0; 

                $month_count = 0;

                $start = $month = strtotime($first_second); 

                $student_attendance_present_array = $this->stuattendencecustom_model->get_attendance( $student_id, 'Present', $session_id );

               

                $studdent_present_attendance = $student_attendance_present_array['attendance'];

                $student_attendance_present_serialize = is_serialized( $studdent_present_attendance )?unserialize( $studdent_present_attendance ): $studdent_present_attendance ;



                $student_attendance_absent_array = $this->stuattendencecustom_model->get_attendance( $student_id, 'Absent', $session_id );

                $studdent_absent_attendance = $student_attendance_absent_array['attendance'];

                $student_attendance_absent_serialize = is_serialized( $studdent_absent_attendance )?unserialize( $studdent_absent_attendance ): $studdent_absent_attendance ;



                while($month < $end){   

                    $month_count =  0; 

                    $monthly_name_fd = date('F_Y', $month);   

                    $monthkey = strtolower(date('F', $month));   

                    $month = strtotime("+1 month", $month);

                    $hern_count = $data['hern_presents'][$monthkey] + $data['hern_absents'][$monthkey];

                    //if(isset($school_days[$monthly_name_fd])){

                        $month_count = $school_days[$monthly_name_fd];

                        // $total_month_count += ($hern_count == 0 || $hern_count > $month_count ? $month_count : $hern_count); 

                    //}

                    

                    $hern_total_count = $hern_count == 0 || $hern_count > $month_count ? $month_count : $hern_count;

                    $hern_total_count -= $data['hern_absents'][$monthkey];

                    $total_month_count += $hern_total_count;

                    ?> 

                    <td style="border: 1px solid;" align="center" ><small><?php echo $hern_total_count; ?></small></td>  

                    <?php

                }

                ?>

                <td style="border: 1px solid;" align="center" >

                    <small>

                        <?php

                        if( $total_month_count != 0 ){

                             if( $gdisplay_attendance){

                                 echo $total_month_count;

                             }

                        } else {

                        }?>

                    </small>

                </td>

            </tr>

            <tr>

                <td style="border: 1px solid;"><small>Days Absent</small></td> 

                <?php 

                $month_count = 0;

                $month =  ''; 

                $total_month_count =  0; 

                $start = $month = strtotime($first_second); 

                $student_attendance_absent_array = $this->stuattendencecustom_model->get_attendance( $student_id, 'Absent', $session_id );

                $studdent_absent_attendance = $student_attendance_absent_array['attendance'];

                $student_attendance_absent_serialize = is_serialized( $studdent_absent_attendance )?unserialize( $studdent_absent_attendance ): $studdent_absent_attendance ;

                while($month < $end){   

                    $month_count =  0; 

                    $monthly_name_fd = date('F_Y', $month);   

                    $monthkey = strtolower(date('F', $month));   

                    $month = strtotime("+1 month", $month);

                    $hern_count = $data['hern_absents'][$monthkey];

                    if(isset($school_days[$monthly_name_fd])){

                        $month_count = $school_days[$monthly_name_fd];

                        $total_month_count += $hern_count; 

                    }

                    ?> 

                    <td style="border: 1px solid;" align="center" ><small><?php echo $hern_count != 0 ? $hern_count : ''; ?></small></td>  

                    <?php

                }

                ?>

                <td style="border: 1px solid;" align="center" >

                    <small>

                        <?php

                        if( $total_month_count != 0 ){

                             if( $gdisplay_attendance){

                                echo $total_month_count;

                             }

                            //echo $total_month_count;

                        } else {

                        }?>

                    </small>

                </td>

            </tr>

            <tr>

                <td style="border: 1px solid;"><small>Times Tardy</small></td> 

                <?php 

                $month_count = 0;

                $month =  ''; 

                $total_month_count =  0; 

                $start = $month = strtotime($first_second); 

                $student_attendance_tardy_array = $this->stuattendencecustom_model->get_attendance( $student_id, 'Late', $session_id );

                $studdent_tardy_attendance = $student_attendance_tardy_array['attendance'];

                $student_attendance_tardy_serialize = is_serialized( $studdent_tardy_attendance )?unserialize( $studdent_tardy_attendance ): $studdent_tardy_attendance ;

                while($month < $end){   

                    $month_count =  0; 

                    $monthly_name_fd = date('F_Y', $month);   

                    $monthkey = strtolower(date('F', $month));   

                    $month = strtotime("+1 month", $month);

                    $hern_count = $data['hern_lates'][$monthkey];

                    if(isset($school_days[$monthly_name_fd])){

                        $month_count = $school_days[$monthly_name_fd];

                        $total_month_count += $hern_count; 

                    }

                    ?> 

                    <td style="border: 1px solid;" align="center" ><small><?php echo $hern_count != 0 ? $hern_count : ''; ?></small></td>  

                    <?php

                }

                ?>

                <td style="border: 1px solid;" align="center">

                    <small>

                        <?php

                        if( $total_month_count != 0 ){

                             if( $gdisplay_attendance){

                                 echo $total_month_count;

                             }

                        } else {

                        }?>

                    </small>

                </td>

            </tr> 

            <?php

        $attendance = ob_get_contents();

         ob_clean( ); 

         

        $data['attendance'] = $attendance; 

         

         return $data;

    }

	

	public function get_layout_grade( $data ){

        $student_id = $data['student_id'];

        $class_id = $data['class_id'];

        $quarter = $data['quarter'];

        $semester = $data['semester_id'];

        $strand_id = $data['strand_id'];

        $current_class = $data['class'];

        $session_id = $data['session_id']; 

        $check_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester, $session_id );

        if( $check_strand ){

            $strand_id = $check_strand['strand_id'];

        } 

        $gradingsettings = $this->gradingsetting_model->getbySession( $session_id );

        $decimal_grades = isset($gradingsettings->decimal_grades)?$gradingsettings->decimal_grades:0;

        $decimal_average = isset($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;

        $decimal_finalgrade = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:2;

        $if_gradeisnull = isset($gradingsettings->if_gradeisnull)?$gradingsettings->if_gradeisnull:'blank';

        $allow_to_pass = isset($gradingsettings->allow_to_pass)?$gradingsettings->allow_to_pass:'no';

        $combine_category = 'ga';

        ob_start(); 

            $first = 0;

            $second = 0;

            $third = 0;

            $fourth = 0;

            $count_subjects_first = 0;

            $count_subjects_second = 0;

            $count_subjects_third = 0;

            $count_subjects_fourth = 0;

            

            $last_quarter = 3;

            $get_ave = 0;

            $get_count = 0;

            $final_grade = 0;

            $get_final_grade_result = 0;

            $get_final_remarks = '';

            $show_final_grade = false;

            $admssion = true;

            if( $semester ){    // SHS

                $gradingsettings = $this->gradingsetting_model->getbySession( $session_id );

                if( $semester == 1 ){

                    $get_quarter_settings = !empty($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array('1','2','3','4');    

                    $get_quarter_settings = is_serialized( $get_quarter_settings )?unserialize($get_quarter_settings):$get_quarter_settings;

                    $total_quarter = count( $get_quarter_settings );

                } else {

                    $get_quarter_settings = !empty($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array('1','2','3','4');  

                    $get_quarter_settings = is_serialized( $get_quarter_settings )?unserialize($get_quarter_settings):$get_quarter_settings;

                    $total_quarter = count( $get_quarter_settings );

                }

                $list_of_subjects = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester, $session_id );

            

                $custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester, $session_id );

                if( $custom_subject ){

                    $list_of_subjects = array_merge( $list_of_subjects, $custom_subject );

                }

                $list_of_subjects = $this->customsubject_model->unsetsubjectdrop( $list_of_subjects, $student_id, $semester , $session_id );

                $complete_grades = true;

                if( $quarter == 'final'){   // Final Quarter

                    foreach( $list_of_subjects as $listsubject => $subjects ){

                        $complete_grades = true;

                        $subject_id = $subjects['subject_id'];

                        $get_final_grade = 0;

                        $subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );

                        $display_card = $subject_manager_details['display_card'];

                        $include_computation = $subject_manager_details['include_computation'];

                        $align_right = $subject_manager_details['align_right'];

                        $units = $subject_manager_details['units'];

                         $subject_name = $subjects['name'];

                        if( $semester == 1 ){

                            if( $subject_name == 'Values Education 11'){

                                $subject_name = 'Values Education 11 (Life and Teachings of Jesus)';

                            } elseif( $subject_name == 'Values Education 12' ){

                                $subject_name = 'Values Education 12 (Daniel & Revelation)';

                            }

                        } elseif( $semester == 2 ){

                            if( $subject_name == 'Values Education 11'){

                                $subject_name = 'Values Education 11 (Christian Beliefs)';



                            } elseif( $subject_name == 'Values Education 12' ){

                                $subject_name = 'Values Education 12 (Christian Ethics)';

                            }

                        }

                        if( $display_card == 'yes' ){

                            ?>

                            <tr>

                            <?php

                            if( $align_right == 'yes' ){

                                ?>

                                <td style="padding-left:20px;font-size:13px;border:1px solid black;border-top:0;"><?php echo $subject_name;?></td>

                                <?php

                            } else {

                                ?>

                                <td style="padding-left:10px;font-size:13px;border:1px solid black;border-top:0;"><?php echo $subject_name;?></td>

                                <?php

                            }   

                        }   

                        $get_final_grade = 0;

                        for($y=0;$y<$total_quarter;$y++){

                            $x = $get_quarter_settings[$y];

                            if( $x ){

                                $grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $x, $combine_category, $session_id );

                                $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );

                                if( $grade_per_quarter == null && $include_computation == 'yes' ){

                                    $complete_grades = false;

                                }

                                if( $include_computation == 'yes'){

                                    if( empty($checkifChild)){

                                        $grade_per_quarter_with_unit = $grade_per_quarter * $units;

                                        if($quarter==1){ 

                                            $first = $first + $grade_per_quarter_with_unit;

                                            $count_subjects_first = $count_subjects_first + $units;

                                        } elseif($quarter==2){  

                                            $second = $second + $grade_per_quarter_with_unit; 

                                            $count_subjects_second = $count_subjects_second + $units;

                                        } elseif($quarter==3){ 

                                            $third = $third + $grade_per_quarter_with_unit; 

                                            $count_subjects_third = $count_subjects_third + $units;

                                        } elseif($quarter==4){

                                            $fourth = $fourth + $grade_per_quarter_with_unit; 

                                            $count_subjects_fourth = $count_subjects_fourth + $units;

                                        }

                                    }

                                }

                                if( $display_card == 'yes' ){

                                    if( $grade_per_quarter == null || $grade_per_quarter == 0  ){

                                        if( $if_gradeisnull == 'blank'){

                                            ?>

                                            <td style="border:1px solid black;border-top:0;border-left:0;"></td>

                                            <?php

                                        }  else {

                                            ?>

                                            <td style="font-size:13px;border:1px solid black;border-top:0;border-left:0;"><?php echo number_format((float)0, $decimal_grades, '.', '');?></td>

                                            <?php

                                        }

                                    } else {

                                        ?>

                                        <td align="center" style="font-size:13px;border:1px solid black;border-top:0;border-left:0;"><?php echo $grade_per_quarter;?></td>

                                        <?php

                                    }

                                }

                                $get_final_grade = $get_final_grade + $grade_per_quarter;



                            } 

                            

                            $get_final_grade_result = $get_final_grade / $total_quarter;

                            if( $include_computation == 'yes' ){ 

                                if( empty( $checkifChild ) ){

                                    if( $allow_to_pass == 'yes'){

                                        if( $get_final_grade_result == '74.5'){

                                            $get_final_grade_result = '75';

                                        } elseif(  $get_final_grade_result > '74.4' && $get_final_grade_result <  75 ){

                                            $get_final_grade_result = '75';

                                        }

                                    }

                                }

                            }

                            $get_final_grade_result = number_format((float)$get_final_grade_result, $decimal_finalgrade, '.', '');

                            $get_final_remarks = $get_final_grade_result <= 74.4 ?"Failed":"Passed";

                            if( $get_final_remarks != 'Passed' && $include_computation == 'yes' ){ 

                                $admssion = false;

                            }

                            $show_final_grade = true;

                                

                        } 

                        if( $display_card == 'yes' ){

                            if( $complete_grades ){

                                ?>

                                <td align="center" style="font-size:13px;border:1px solid black;border-top:0;border-left:0;"><?php echo $get_final_grade_result;?></td>

                                <td align="center" style="border:1px solid black;border-top:0;border-left:0;"><small><?php echo $get_final_remarks;?></small></td>

                                </tr>

                                <?php

                            } else {

                                ?>

                                <td align="center" style="border:1px solid black;border-top:0;border-left:0;"><small></small></td>

                                <td align="center" style="border:1px solid black;border-top:0;border-left:0;"><small></small></td>

                                </tr>

                                <?php   

                            }

                        }

                        

                        if( $include_computation == 'yes' ){ 

                            if( empty($checkifChild)){

                                $get_count = $get_count + $units; 

                                $get_final_grade_result = $get_final_grade_result * $units;

                                $get_ave = $get_ave +  $get_final_grade_result; 

                            }

                        }

                    }

                } else {    // Other Quarters

                    foreach( $list_of_subjects as $listsubject => $subjects ){

                        $subject_id = $subjects['subject_id'];

                        $get_final_grade = 0;

                        $complete_grades = true;

                        $subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );

                        $display_card = $subject_manager_details['display_card'];

                        $include_computation = $subject_manager_details['include_computation'];

                        $align_right = $subject_manager_details['align_right'];

                        $units = $subject_manager_details['units'];

                         $subject_name = $subjects['name'];

                        if( $semester == 1 ){

                            if( $subject_name == 'Values Education 11'){

                                $subject_name = 'Values Education 11 (Life and Teachings of Jesus)';

                            } elseif( $subject_name == 'Values Education 12' ){

                                $subject_name = 'Values Education 12 (Daniel & Revelation)';

                            }

                        } elseif( $semester == 2 ){

                            if( $subject_name == 'Values Education 11'){

                                $subject_name = 'Values Education 11 (Christian Beliefs)';



                            } elseif( $subject_name == 'Values Education 12' ){

                                $subject_name = 'Values Education 12 (Christian Ethics)';

                            }

                        }

                        if( $display_card == 'yes' ){

                            ?>

                            <tr>

                            <?php 

                            if( $align_right == 'yes' ){

                                ?>

                                <td style="padding-left:20px;font-size:13px;border:1px solid black;border-top:0;"><?php echo $subject_name;?></td>

                                <?php

                            } else {

                                ?>

                                <td style="padding-left:10px;font-size:13px;border:1px solid black;border-top:0;"><?php echo $subject_name;?></td>

                                <?php

                            }   

                        }   

                        $get_final_grade = 0;

                        for($y=0;$y<$total_quarter;$y++){

                            $x = $get_quarter_settings[$y];

                            if( $x <= $quarter ){

                                $grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $x, $combine_category, $session_id );

                                if( $include_computation == 'yes'){

                                    $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id  );

                                    if( empty($checkifChild)){

                                        $grade_per_quarter_with_unit = $grade_per_quarter * $units;

                                        if($quarter==1){ 

                                            $first = $first + $grade_per_quarter_with_unit;

                                            $count_subjects_first = $count_subjects_first + $units;

                                        } elseif($quarter==2){  

                                            $second = $second + $grade_per_quarter_with_unit; 

                                            $count_subjects_second = $count_subjects_second + $units;

                                        } elseif($quarter==3){ 

                                            $third = $third + $grade_per_quarter_with_unit; 

                                            $count_subjects_third = $count_subjects_third + $units;

                                        } elseif($quarter==4){

                                            $fourth = $fourth + $grade_per_quarter_with_unit; 

                                            $count_subjects_fourth = $count_subjects_fourth + $units;

                                        }

                                    }

                                }

                                if( $display_card == 'yes' ){

                                    if( $grade_per_quarter == null || $grade_per_quarter == 0  ){

                                        if( $if_gradeisnull == 'blank'){

                                            ?>

                                            <td style="border:1px solid black;border-top:0;border-left:0;"></td>

                                            <?php

                                        }  else {

                                            ?>

                                            <td style="font-size:13px;border:1px solid black;border-top:0;border-left:0;"><?php echo number_format((float)0, $decimal_grades, '.', '');?></td>

                                            <?php

                                        }

                                    } else {

                                        ?>

                                        <td align="center" style="font-size:13px;border:1px solid black;border-top:0;border-left:0;"><?php echo $grade_per_quarter;?></td>

                                        <?php

                                    }

                                }

                                $get_final_grade = $get_final_grade + $grade_per_quarter;

                            } else {

                                if( $display_card == 'yes' ){

                                    ?>

                                    <td style="border:1px solid black;border-top:0;border-left:0;"></td>

                                    <?php

                                }

                            }

                        } 

                        if( $display_card == 'yes' ){

                            ?>

                            <td style="border:1px solid black;border-top:0;border-left:0;"></td>

                            <td style="border:1px solid black;border-top:0;border-left:0;"></td>

                            </tr>

                            <?php

                        }

                    }

                }

                $data['set_quarter'] = $get_quarter_settings; 

            } else {    // JHS

                $list_of_subjects = $this->subject_model->getSubjctByClass( $class_id );

                if( $quarter == 'final'){   // Final

                    foreach( $list_of_subjects as $listsubject => $subjects ){

                        $subject_id = $subjects['id'];

                        $get_final_grade = 0;

                        $complete_grades = true; 

                        $complete_first = true;

                        $complete_second = true;

                        $complete_third = true;

                        $complete_fourth = true;

                        $subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );

                        $display_card = $subject_manager_details['display_card'];

                        $include_computation = $subject_manager_details['include_computation'];

                        $align_right = $subject_manager_details['align_right'];

                        if( $display_card == 'yes' ){ 

                            ?>

                            <tr>

                                <?php 

                                if( $align_right == 'yes' ){

                                    ?>

                                    <td style="padding-left:20px;font-size:13px;border:1px solid black;border-top:0;"><?php echo $subjects['name'];?></td>

                                    <?php

                                } else {

                                    ?>

                                    <td style="padding-left:10px;font-size:13px;border:1px solid black;border-top:0;"><?php echo $subjects['name'];?></td>

                                    <?php

                                }

                        }

                        for($x=1;$x<=$last_quarter;$x++){

                            $grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $x, $combine_category, $session_id );

                            $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );

                            if( $grade_per_quarter == null && $include_computation == 'yes' ){

                                $complete_grades = false;

                            } 

                            if( $include_computation == 'yes' ){

                                if( empty( $checkifChild ) ){

                                    if($x==1){ 

                                        $first = $first + $grade_per_quarter;

                                        $grade_per_quarter != null?$count_subjects_first ++:'';

                                        if( empty( $grade_per_quarter )){

                                            $complete_first = false;

                                        }

                                    } elseif($x==2){ 

                                        $second = $second + $grade_per_quarter; 

                                        $grade_per_quarter != null?$count_subjects_second++:'';

                                        if( empty( $grade_per_quarter )){

                                            $complete_second = false;

                                        }

                                    } elseif($x==3){ 

                                        $third = $third + $grade_per_quarter; 

                                        $grade_per_quarter != null?$count_subjects_third++:'';

                                        if( empty( $grade_per_quarter )){

                                            $complete_third = false;

                                        }

                                    } elseif($x==4){ 

                                        $fourth = $fourth + $grade_per_quarter;

                                        $grade_per_quarter != null?$count_subjects_fourth++:'';     

                                        if( empty( $grade_per_quarter )){

                                            $complete_fourth = false;

                                        }                                                                       

                                    }

                                }

                            }

                            if( $display_card == 'yes' ){ 

                                if( $grade_per_quarter == null || $grade_per_quarter == 0 ){

                                    if( $if_gradeisnull == 'blank'){

                                    ?>

                                    <td  style="font-size:13px;border:1px solid black;border-left:0;border-top:0;"></td>

                                    <?php

                                    }  else {

                                        ?>

                                        <td style="font-size:13px;border:1px solid black;border-left:0;border-top:0;"><?php echo number_format((float)0, $decimal_grades, '.', '');?></td>

                                        <?php

                                    }

                                } else {

                                    ?>

                                    <td align="center" style="font-size:13px;border:1px solid black;border-left:0;border-top:0;"><?php echo $grade_per_quarter;?></td>

                                    <?php

                                }

                            }

                            $get_final_grade = $get_final_grade + $grade_per_quarter;

                            if( $x == $last_quarter ){

                                $get_final_grade_result = $get_final_grade / $last_quarter;

                                $get_final_grade_result = number_format($get_final_grade_result,$decimal_finalgrade,".","");

                            

                                

                                if( $include_computation == 'yes'){

                                    if( empty( $checkifChild ) ){

                                        if( $allow_to_pass == 'yes'){

                                            if( $get_final_grade_result == '74.5'){

                                                $get_final_grade_result = '75';

                                            } elseif(  $get_final_grade_result > '74.4' && $get_final_grade_result <  75 ){

                                                $get_final_grade_result = '75';

                                            }   

                                        }   

                                    }

                                }                               

                                $get_final_remarks = $get_final_grade_result < 74.4 ?"Failed":"Passed";

                                if( $get_final_remarks != 'Passed' && $include_computation == 'yes' ){ 

                                    $admssion = false;

                                }

                                $show_final_grade = true;

                                if( $include_computation == 'yes' ){ 

                                    if( empty( $checkifChild ) ){

                                        $get_count++; 

                                        $get_ave = $get_ave +  $get_final_grade_result;

                                    }

                                }

                            }

                        } 

                        

                        if( $display_card == 'yes' ){ 

                            if( $complete_grades ){

                                ?>

                                <td align="center"  style="font-size:13px;border:1px solid black;border-left:0;border-top:0;"><?php echo $get_final_grade_result;?></td>    

                                <td align="center"  style="font-size:13px;border:1px solid black;border-left:0;border-top:0;"><small><?php echo $get_final_remarks;?></small></td>              

                                </tr>

                                <?php

                            } else {

                                ?>

                                <td align="center"  style="font-size:13px;border:1px solid black;border-left:0;border-top:0;"></td> 

                                <td align="center"  style="font-size:13px;border:1px solid black;border-left:0;border-top:0;"></td>             

                                </tr>

                                <?php

                            }

                        }

                    }

                } else {    // Other Quarters

                    foreach( $list_of_subjects as $listsubject => $subjects ){

                        $subject_id = $subjects['id'];

                        $get_final_grade = 0;

                        $complete_grade = true; 

                        $subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );

                        $display_card = $subject_manager_details['display_card'];

                        $include_computation = $subject_manager_details['include_computation'];

                        $align_right = $subject_manager_details['align_right'];

                        if( $display_card == 'yes' ){ 

                            ?>

                            <tr>

                                <?php 

                                if( $align_right == 'yes' ){

                                    ?>

                                    <td style="padding-left:20px;font-size:13px;border:1px solid black;border-top:0;"><?php echo $subjects['name'];?></td>

                                    <?php

                                } else {

                                    ?>

                                    <td style="padding-left:10px;font-size:13px;border:1px solid black;border-top:0;"><?php echo $subjects['name'];?></td>

                                    <?php

                                }

                        }

                        for($x=1;$x<=$last_quarter;$x++){

                            if( $x <= $quarter ){

                                $grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $x, $combine_category, $session_id );

                                $grade_per_quarter = isset($grade_per_quarter)?$grade_per_quarter:null;

                                    if( $grade_per_quarter == null ){

                                        $complete_grade = false;

                                    }

                                    $grade_per_quarter = number_format((float)$grade_per_quarter, $decimal_grades, '.', '');

                                    if( $include_computation == 'yes' ){

                                        $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category );

                                        if( empty( $checkifChild ) ){

                                            if($x==1){ 

                                                $first = $first + $grade_per_quarter;

                                                $grade_per_quarter != null?$count_subjects_first ++:'';

                                            } elseif($x==2){ 

                                                $second = $second + $grade_per_quarter; 

                                                $grade_per_quarter != null?$count_subjects_second++:'';

                                            } elseif($x==3){ 

                                                $third = $third + $grade_per_quarter; 

                                                $grade_per_quarter != null?$count_subjects_third++:'';

                                            } elseif($x==4){ 

                                                $fourth = $fourth + $grade_per_quarter;

                                                $grade_per_quarter != null?$count_subjects_fourth++:'';                                                                         

                                            }

                                        }

                                    }

                                    if( $display_card == 'yes' ){ 

                                        if( $grade_per_quarter == null || $grade_per_quarter == 0 ){

                                            if( $if_gradeisnull == 'blank'){

                                            ?>

                                            <td style="font-size:13px;border:1px solid black;border-left:0;border-top:0;"></td>

                                            <?php

                                            }  else {

                                                ?>

                                                <td style="font-size:13px;border:1px solid black;border-left:0;border-top:0;"><?php echo number_format((float)0, $decimal_grades, '.', '');?></td>

                                                <?php

                                            }

                                        } else {

                                            ?>

                                            <td align="center" style="font-size:13px;border:1px solid black;border-left:0;border-top:0;"><?php echo $grade_per_quarter;?></td>

                                            <?php

                                        }

                                    }

                                    $get_final_grade = $get_final_grade + $grade_per_quarter;

                            } else {

                                if( $display_card == 'yes' ){ 

                                    ?>

                                    <td  style="font-size:13px;border:1px solid black;border-left:0;border-top:0;"></td>

                                    <?php

                                }

                            }

                        } 

                        if( $display_card == 'yes' ){ 

                            ?>

                            <td  style="font-size:13px;border:1px solid black;border-left:0;border-top:0;"></td>

                            <td  style="font-size:13px;border:1px solid black;border-left:0;border-top:0;"></td>

                            </tr>

                            <?php

                        }

                    }

                }

                $get_quarter_settings = array('1','2','3'); 

                $get_quarter_settings = is_serialized( $get_quarter_settings )?unserialize($get_quarter_settings):$get_quarter_settings;

                $data['set_quarter'] = $get_quarter_settings; 

            }



        $get_grades = ob_get_contents();

        ob_clean( ); 

        $get_ave = $get_count != 0 ?$get_ave / $get_count:'';

        if( $get_ave  ){

            $get_ave = number_format($get_ave,2,".","");

        } 

        $get_ave = $show_final_grade == true ? $get_ave:'';

        $get_ave = $complete_grades == true ? $get_ave:'';

        $data['get_grades'] = $get_grades;   

        $data['gen_ave'] = $get_ave; 



        if( $admssion == true && $quarter == 'final' ){

            $next_class_details = $this->class_model->getnextClassID( $class_id );

            $data['admssion'] =  $next_class_details['class'];

        } else {

            $data['admssion'] = " ";

        }

        return $data;

    }

	

	

	

	

	public function get_layout_conduct( $data ){

		$class_id = $data['class_id'];

		$section_id = $data['section_id'];

		$student_id = $data['student_id'];

		$quarter = $data['quarter'];

		$session_id = $data['session_id'];		

		$getConductSubject = $this->conductsession_model->getDetailByclassAndSection( $class_id, $section_id, $session_id );

		$data['getConductSubject'] = $getConductSubject;

		$set_quarter = $data['set_quarter'];

		ob_start(); 

		?>		

				<?php 

				if( $getConductSubject ){

					foreach( $getConductSubject as $getConduct => $conduct){

						$conduct_id = $conduct['id'];

						if( $conduct_id ){

							?> 

							<tr>

								<td  style="padding:2px;border:1px solid;">&nbsp;&nbsp;<?php echo $conduct['name'];?></td>

								<?php 

								for($x=0;$x<count($set_quarter);$x++){

									$get_quarter = $set_quarter[$x];

									if( $get_quarter <= $quarter  ){

										$conduct_details = $this->conductimportbatchdetail_model->getAllDetailsConductByStudentPerQuarter( $conduct_id, $student_id, $class_id, $section_id, $get_quarter, $session_id );

										if( !empty( $conduct_details ) && $conduct_details ){

											?>

										<td align="center" style="padding:2px;border:1px solid;"><?php echo $conduct_details['conduct'];?></td>

											<?php

										} else {

											?>

											<td align="center"  style="padding:2px;border:1px solid;"></td>

											<?php

										}

									} else {

										?>

										<td align="center"  style="padding:2px;border:1px solid;"></td>

										<?php

									}

									

								}

								?>

								<td align="center"  style="padding:2px;border:1px solid;"></td>

								 <td align="center"  style="padding:2px;border:1px solid;"></td>

							</tr>

								<?php

						}/*  else {

							?>

								<td align="center"  style="padding:2px;border:1px solid;"></td>

							<?php

						} */

					}

				}

		?>

			

		<?php

		$conduct_contents = ob_get_contents();

		$data['conduct'] = $conduct_contents; 

		

		ob_clean( ); 



		return $data;

	}

	

	public function get_quarterly_display( $quarter ){

		

        if( $quarter == 1 ){

            $quarter_display = "First";

        }elseif( $quarter == 2 ){

            $quarter_display = "Second";

        }elseif( $quarter == 3 ){

            $quarter_display = "Third";

        }elseif( $quarter == 4 ){

            $quarter_display = "Fourth";

        }

		

		return $quarter_display;

	}



	

    public function print_master_sheet( $class_id, $section_id, $quarter, $semester_id=null ){  

        $setting_result = $this->setting_model->get();

        $session_id = $setting_result[0]['session_id'];

        $session_details = $this->session_model->get( $session_id );

        $session_current = $session_details['session'];

        $centenary = substr($session_current, 0, 2); //2017-18 to 2017 

        $setting_result = $this->setting_model->get();

        $school_year = "S. Y. ".$session_current;



        $class_array = $this->class_model->get($class_id);

        $class_name = $class_array['class'];

        $section_array = $this->section_model->get($section_id);

        $section_name = $section_array['section'];

        

        $teacher_details = $this->classsection_model->get_teacher_advisory($class_id, $section_id, $session_id );

        $teacher_lastname = isset($teacher_details->lastname )?$teacher_details->lastname:'';

        $teacher_name = isset($teacher_details->name )?$teacher_details->name:'';

        $teacher_middlename = isset($teacher_details->middlename )?$teacher_details->middlename:'';



        $data['school_year'] = $school_year;

        $data['class_name'] = $class_name;

        $data['section_name'] = $section_name;  

        $data['teacher_name'] = $teacher_lastname.', '.$teacher_name.' '.$teacher_middlename;

        if( $quarter == 'final' ||  $quarter == 'Final' ){

            $data['quarter_display'] = "Final";

        } else { 

            $data['quarter_display'] = $quarter.substr(date('jS', mktime(0,0,0,1,($quarter%10==0?9:($quarter%100>20?$quarter%10:$quarter%100)),2000)),-2);

        } 

        $enableStrand =  $this->strand_model->checkEnable( $class_id );     

         $subjectlist = $this->subject_model->getSubjctByClass( $class_id ); 

        

         // SETTINGS

        $data['student_session_id'] = $session_current;

        $setting_result = $this->setting_model->get();

        $data['school_name'] =  $setting_result[0]['name'];

        $data['logo'] = $setting_result[0]['image'];

        $data['email'] =  $setting_result[0]['email'];

        $address_barangay_id =  $setting_result[0]['address_barangay_id'];

        $address_barangay =$this->address_model->get_barangay($address_barangay_id);

        $address_city_id =  $setting_result[0]['address_city_id'];

        $address_city =$this->address_model->get_city($address_city_id);

        $address_province_id =  $setting_result[0]['address_province_id'];

        $province_name =$this->address_model->get_province($address_province_id);

        $data['barangay'] = $address_barangay['name'];

        $data['city'] = $address_city['name'];

        $data['province'] = $province_name['name'];

        $data['address'] = $setting_result[0]['address'];

        $data['phone'] = $setting_result[0]['phone'];

        $school_year = "S. Y. ".$session_current;

        $principal_id = $setting_result[0]['principal_id'];

        $principal_details = $this->principal_model->get(  $principal_id );

        $principal_sex = $principal_details['sex'];

        if(  $principal_sex == 'Female'){

            $principal_sex_display = "Ms.";

        } else {

            $principal_sex_display =  "Mr.";

        }

        $principal_firstname = $principal_details['name'];

        $principal_midname = $principal_details['middlename'];

        $principal_lastname = $principal_details['lastname'];

        $principal_fullname = $this->setting_model->getprincipalformat( '', $principal_firstname, $principal_midname, $principal_lastname );

        

        $gradingsettings = $this->gradingsetting_model->getbySession( $session_id );

        $decimal_grades  = !empty($gradingsettings->decimal_grades)?$gradingsettings->decimal_grades:0;

        $decimal_average = !empty($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;

        $decimal_finalgrade = !empty($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:0;

        $if_gradeisnull = !empty($gradingsettings->if_gradeisnull)?$gradingsettings->if_gradeisnull:'blank';

        $allow_to_pass = !empty($gradingsettings->allow_to_pass)?$gradingsettings->allow_to_pass:'no';

        $mastersheet_order = isset($gradingsettings->mastersheet_order)?$gradingsettings->mastersheet_order:'lastname';

        $studentlist_male = $this->student_model->searchByClassSectionGender($class_id, $section_id, 'Male', $session_id );

        $studentlist_female = $this->student_model->searchByClassSectionGender($class_id, $section_id, 'Female', $session_id );

        $if_gradeisnull = isset($gradingsettings->if_gradeisnull)?$gradingsettings->if_gradeisnull:'blank';

        $studentlist = array();

        $data['studentlist_male'] = $studentlist_male;

        $data['studentlist_female'] = $studentlist_female;

        $studentlist = array_merge( $studentlist, $studentlist_male );

        $studentlist = array_merge( $studentlist, $studentlist_female );

        $data['studentlist'] = $studentlist;

        $combine_category = 'ga';

         ob_start();

        if( $enableStrand ){

          ?>

            <table class="tobe_bordered" style=" border: 1px solid black;  border-collapse: collapse; width: 100%;">

                <?php 

                $row_count = 1;

                if( $semester_id == 1 ){

                    $get_quarter = !empty($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array(); 

                } else {

                    $get_quarter = !empty($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();   

                }

                $get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;

                $sql_quarter = $this->student_model->sql_array_to_string( $get_quarter );  

                $total_quarter = count($get_quarter); 

                $getsubjectlist = array();

                $getstrandlist = array();

                $getsubjectperstrand = array();

                ?>

                    <tr> 

                    <th style=" border: 1px solid black" height="179" style="white-space:nowrap;">Names</th> 

                    <?php

                        $xy=0;

                    

                        foreach ($studentlist as  $student_value) {

                            $dont_check = TRUE;

                            $student_id = $student_value["id"]; 

                            $strand_id = $student_value["strand_id"];

                            $check_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id, $session_id );

                            if( $check_strand ){

                                $strand_id = $check_strand['strand_id'];

                            }

                            $subjectlist = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id, $session_id  );

                            $custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id , $semester_id, $session_id  );

                            if( $custom_subject ){

                                foreach( $custom_subject as $key => $value ){

                                    $get_cs_id = $value['subject_id'];

                                    $custom_subject_student[ $get_cs_id ][] = $student_id;

                                }

                                $dont_check = FALSE;

                                $subjectlist = array_merge( $subjectlist, $custom_subject );

                            } 

                            if(!in_array( $strand_id, $getstrandlist) || $dont_check == FALSE ){

                                $getstrandlist[] = $strand_id;

                                //$subjectlist = $this->customsubject_model->unsetsubjectdrop( $subjectlist, $student_id, $semester_id, $session_id );    

                                if( $subjectlist ){

                                    foreach ($subjectlist as $subject_key => $subject_value) { 

                                        $subject_id = $subject_value['subject_id'];

                                        $subject_name = $subject_value['name'];

                                        $sm_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id  );

                                        $display_card = $sm_details['display_card'];

                                        $include_computation = $sm_details['include_computation'];

                                        $getsubjectperstrand[ $strand_id ][] = $subject_id;

                                        if(!in_array( $subject_id, $getsubjectlist )){

                                            $getsubjectlist[] = $subject_id;

                                            if( $display_card == 'yes'){

                                                $xy++;

                                                echo '<th text-rotate="90" height="179" style="line-height: 13px; padding-bottom:6px;vertical-align: bottom; border: 1px solid black; white-space:nowrap;"><div style=" width: 215px;word-wrap: break-word;height:100px; " >'.$subject_name.'</div></th>'; 

                                            }

                                        }

                                        

                                        

                                    }

                                }

                            }

                        }

                        if( $quarter != 'final'){

                          echo '<th text-rotate="90" height="179" style="line-height: 13px; padding-bottom:6px;vertical-align: bottom;  border: 1px solid black;white-space:nowrap;"><div style=" width: 215px; " >Conduct</div></th>';

                        }

                         echo '<th text-rotate="90" height="179" style="line-height: 13px; padding-bottom:6px;vertical-align: bottom;  border: 1px solid black;white-space:nowrap;"><div style=" width: 215px; " >Gen Ave</div></th>';

                        ?>

                        </tr>

                    <tbody>

                    <?php

                    if (!empty( $studentlist_male )) { 

                        ?>

                        <tr>

                            <td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;text-transform:uppercase;width:300px;border-right:0;"><b>Male</b></td>

                            <?php 

                            for( $jo=1;$jo<=$xy;$jo++){

                                ?>

                                <td class="mailbox-name"></td>

                                <?php

                            }

                            ?>

                            <td class="mailbox-name"></td>

                            <?php 

                            if( $quarter != 'final'){

                                ?>

                                <td class="mailbox-name"></td>

                                <?php 

                            }

                            ?>

                        <tr>

                        <?php

                    

                        foreach ($studentlist_male as  $student_value) { 

                            $total_grade = 0;

                            $total_subject_list = 0;

                            $count_subject = 0;

                            $display_total_grade = 0;

                            $student_id = $student_value["id"]; 

                            $lastname = $student_value['lastname'];

                            $firstname = $student_value['firstname'];

                            $suffix = $student_value['suffix'];

                            $strand_id = $student_value["strand_id"];

                            $check_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id, $session_id );

                            if( $check_strand ){

                                $strand_id = $check_strand['strand_id'];

                            }

                            $middlename = !empty($student_value['middlename'])?$student_value['middlename'][0].'.':'';$subjectlist = array_merge( $subjectlist, $custom_subject );

                            ?>  

                            <tr>

                            <td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;text-transform:uppercase;width:300px;"><?php echo $lastname.', '.$firstname.' '.$suffix.' '.$middlename; ?></td> 

                            <?php

                        

                            foreach( $getsubjectlist as $subject_value ) {

                                $complete_first = true;

                                $complete_second = true;

                                $complete_third = true;

                                $complete_fourth = true;

                                $complete_quarter = true;

                                $complete_grades =true;

                                $subject_id = $subject_value;

                                $sm_details = $this->subjectmanager_model->getBySubjectSession( $subject_id , $session_id );

                                $display_card = $sm_details['display_card'];

                                if(isset( $getsubjectperstrand[$strand_id])){

                                if(in_array( $subject_id,$getsubjectperstrand[$strand_id])){

                                    if( isset($custom_subject_student[ $subject_id ]) && !in_array( $student_id, $custom_subject_student[ $subject_id ] )){

                                        if( $display_card == 'yes'){

                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">N/A</td>'; 

                                        }

                                    } else {

                                    $complete_grades = true;

                                    $checkifdrop = $this->customsubject_model->getStudentSubjectSemesterDrop($student_id, $subject_id, $semester_id, $session_id  );

                                    if( count($checkifdrop) == 0 ){ 

                                        

                                        $display_number ='yes';

                                        $include_computation = $sm_details['include_computation'];

                                        $units =  !empty($sm_details['units'])?$sm_details['units']:1;

                                        if( $quarter == "final"){

                                             $get_final_grade = 0;

                                                for ($q_i=0; $q_i < $total_quarter ; $q_i++) { 

                                                    $set_quarter = $get_quarter[$q_i];

                                                    $grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $set_quarter, $combine_category, $session_id  );

                                                    $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id , $session_id );

                                                    if( empty($checkifChild)){

                                                        if( $set_quarter == 1 ){

                                                            if( empty( $grade_per_quarter ) && $include_computation == 'yes' ){

                                                                $complete_first = false;

                                                            }

                                                        }

                                                        if( $set_quarter == 2 ){

                                                            if( empty( $grade_per_quarter ) && $include_computation == 'yes' ){

                                                                $complete_second = false;

                                                            }

                                                        }

                                                        if( $set_quarter == 3 ){

                                                            if( empty( $grade_per_quarter ) && $include_computation == 'yes' ){

                                                                $complete_third = false;

                                                            }

                                                        }

                                                        if( $set_quarter == 4 ){

                                                            if( empty( $grade_per_quarter ) && $include_computation == 'yes' ){

                                                                $complete_fourth = false;

                                                            }

                                                        }

                                                    }

                                                    

                                                    if( $display_number == 'yes'){

                                                        $get_final_grade +=  $grade_per_quarter; 

                                                    }

                                                }

                                            if( $display_number == 'yes'){

                                                $display_grade =  $get_final_grade / $total_quarter;

                                            }

                                            if( $include_computation == 'yes' ){ 

                                                $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id  );

                                            }

                                            if( $display_number == 'yes'){

                                                $display_grade =number_format( $display_grade, $decimal_finalgrade,".","");

                                            }

                                            if( $display_card == 'yes'){

                                                if( ( $complete_first && $complete_second )|| ($complete_third && $complete_fourth )){

                                                    if( $display_number == 'yes'){

                                                        if( $display_grade == null || $display_grade == 0 || $display_grade == 0.00 ){

                                                            $complete_grades = false;

                                                            if( $if_gradeisnull == 'blank'){

                                                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>'; 

                                                            }  else {

                                                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.number_format(0, $decimal_grades, '.', '').'</td>'; 

                                                            }

                                                        } else {

                                                            if( $display_grade < 75 ){

                                                                echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_grade.'</td>'; 

                                                            } else {

                                                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_grade.'</td>'; 

                                                            }

                                                        }

                                                    } else {

                                                        echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_grade.'</td>'; 

                                                    }

                                                } else {

                                                    $complete_grades = false;

                                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>'; 

                                                }

                                            }

                                            if( $include_computation == 'yes'){

                                                $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id  );

                                                if( empty($checkifChild)){

                                                    $display_grade = $display_grade * $units;

                                                    $total_grade += $display_grade;

                                                    $total_subject_list=$total_subject_list + $units;

                                                }

                                            }

                                        } else { 

                                            $grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id  );

                                            if( $include_computation == 'yes'){

                                                if( $grade_per_quarter == null ){

                                                    $complete_grades = false;

                                                    $complete_quarter = false;

                                                }

                                                $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id  );

                                                if( empty($checkifChild)){

                                                    $grade_per_quarter_with_unit = $grade_per_quarter * $units;

                                                    $display_total_grade = $display_total_grade + $grade_per_quarter_with_unit;

                                                    $count_subject = $count_subject + $units;

                                                }

                                            }

                                            if( $display_card == 'yes'){

                                                if( $display_number == 'yes'  ){

                                                    if( $grade_per_quarter == null || $grade_per_quarter == 0  ){

                                                        if( $if_gradeisnull == 'blank'){

                                                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>'; 

                                                        }  else {

                                                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.number_format(0, $decimal_grades, '.', '').'</td>'; 

                                                        }

                                                    } else {

                                                        if( $grade_per_quarter < 75  ){

                                                            echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$grade_per_quarter.'</td>'; 

                                                        } else {

                                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$grade_per_quarter.'</td>'; 

                                                        }

                                                    }

                                                } else {

                                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$grade_per_quarter.'</td>'; 

                                                }

                                            }

                                        }

                                    } else {

                                        if( $display_card == 'yes'){

                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">N/A</td>'; 

                                        }

                                    } 

                                  } 

                                } else {

                                    if( $display_card == 'yes'){

                                        echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">N/A</td>'; 

                                    }

                                }

                                } else {

                                    if( $display_card == 'yes'){

                                        echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">N/A</td>'; 

                                    }

                                }

                            }

                           if( $semester_id ){

                                $getConductSubject = $this->conductimportsubject_model->getStudentConduct( $student_id, $quarter, $class_id, $semester_id, $strand_id  );

                            } else {

                                $getConductSubject = $this->conductimportsubject_model->getStudentConduct( $student_id, $quarter, $class_id );

                            }

                            if( $getConductSubject ){

                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.round($getConductSubject).'</td>';

                            } else {

                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>';

                            }

                            

                            if( $quarter == "final"){

                                if( $complete_grades ){

                                    $display_total_grade =  $total_subject_list != 0 ? $total_grade / $total_subject_list:0;

                                    $display_total_grade =number_format( (float)$display_total_grade,$decimal_average,".",".");

                                    if( $display_total_grade == null || $display_total_grade == 0 || $display_total_grade == '0.00' ){

                                        if( $if_gradeisnull == 'blank'  ){

                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>'; 

                                        } else {

                                            echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_total_grade.'</td>'; 

                                        }

                                    } else {

                                        if( $display_total_grade < 75  ){

                                            echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_total_grade.'</td>'; 

                                        } else {

                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_total_grade.'</td>'; 

                                        }

                                    }

                                } else {

                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>'; 

                                }

                            } else {

                                if( $complete_quarter ){

                                    $average_grade =  $count_subject != 0?$display_total_grade/$count_subject:0;

                                    $average_grade =  number_format($average_grade, $decimal_average, '.', '');

                                    if( $average_grade == null || $average_grade == 0 || $average_grade == '0.00' ){

                                        if( $if_gradeisnull == 'blank'  ){

                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>'; 

                                        } else {

                                            echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$average_grade.'</td>'; 

                                        }

                                    } else {

                                        if( $average_grade < 75 ){

                                            echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$average_grade.'</td>'; 

                                        } else {

                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$average_grade.'</td>'; 

                                        }

                                    }

                                } else {

                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>'; 

                                }

                            }

                            ?>

                            </tr>

                            <?php

                            $row_count++;

                    }

                }

                if (!empty( $studentlist_female )) { 

                        ?>

                        <tr>

                            <td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;text-transform:uppercase;width:300px;border-right:0;"><b>Female</b></td>

                            <?php 

                            for( $jo=1;$jo<=$xy;$jo++){

                                ?>

                                <td class="mailbox-name"></td>

                                <?php

                            }

                            ?>

                            <td class="mailbox-name"></td>

                            <?php 

                            if( $quarter != 'final'){

                                ?>

                                <td class="mailbox-name"></td>

                                <?php 

                            }

                            ?>

                        <tr>

                        <?php

                        foreach ($studentlist_female as  $student_value) { 

                            $total_grade = 0;

                            $total_subject_list = 0;

                            $count_subject = 0;

                            $display_total_grade = 0;

                            $student_id = $student_value["id"]; 

                            $lastname = $student_value['lastname'];

                            $firstname = $student_value['firstname'];

                            $suffix = $student_value['suffix'];

                            $strand_id = $student_value["strand_id"];

                            $check_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id, $session_id );

                            if( $check_strand ){

                                $strand_id = $check_strand['strand_id'];

                            }

                            $middlename = !empty($student_value['middlename'])?$student_value['middlename'][0].'.':'';$subjectlist = array_merge( $subjectlist, $custom_subject );

                            ?>  

                            <tr>

                            <td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;text-transform:uppercase;width:300px;"><?php echo $lastname.', '.$firstname.' '.$suffix.' '.$middlename; ?></td> 

                            <?php

                        

                            foreach( $getsubjectlist as $subject_value ) {

                                $complete_first = true;

                                $complete_second = true;

                                $complete_third = true;

                                $complete_fourth = true;

                                $complete_quarter = true;

                                $subject_id = $subject_value;

                                $sm_details = $this->subjectmanager_model->getBySubjectSession( $subject_id , $session_id );

                                $display_card = $sm_details['display_card'];

                                if(isset( $getsubjectperstrand[$strand_id])){

                                if(in_array( $subject_id,$getsubjectperstrand[$strand_id])){

                                    if( isset($custom_subject_student[ $subject_id ]) && !in_array( $student_id, $custom_subject_student[ $subject_id ] )){

                                        if( $display_card == 'yes'){

                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">N/A</td>'; 

                                        }

                                    } else {

                                    $complete_grades = true;

                                    $checkifdrop = $this->customsubject_model->getStudentSubjectSemesterDrop($student_id, $subject_id, $semester_id, $session_id  );

                                    if( count($checkifdrop) == 0 ){ 

                                        

                                        $display_number ='yes';

                                        $include_computation = $sm_details['include_computation'];

                                        $units =  !empty($sm_details['units'])?$sm_details['units']:1;

                                        if( $quarter == "final"){

                                             $get_final_grade = 0;

                                                for ($q_i=0; $q_i < $total_quarter ; $q_i++) { 

                                                    $set_quarter = $get_quarter[$q_i];

                                                    $grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $set_quarter, $combine_category, $session_id  );

                                                    $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id , $session_id );

                                                    if( empty($checkifChild)){

                                                        if( $set_quarter == 1 ){

                                                            if( empty( $grade_per_quarter ) && $include_computation == 'yes' ){

                                                                $complete_first = false;

                                                            }

                                                        }

                                                        if( $set_quarter == 2 ){

                                                            if( empty( $grade_per_quarter ) && $include_computation == 'yes' ){

                                                                $complete_second = false;

                                                            }

                                                        }

                                                        if( $set_quarter == 3 ){

                                                            if( empty( $grade_per_quarter ) && $include_computation == 'yes' ){

                                                                $complete_third = false;

                                                            }

                                                        }

                                                        if( $set_quarter == 4 ){

                                                            if( empty( $grade_per_quarter ) && $include_computation == 'yes' ){

                                                                $complete_fourth = false;

                                                            }

                                                        }

                                                    }

                                                    

                                                    if( $display_number == 'yes'){

                                                        $get_final_grade +=  $grade_per_quarter; 

                                                    }

                                                }

                                            if( $display_number == 'yes'){

                                                $display_grade =  $get_final_grade / $total_quarter;

                                            }

                                            if( $include_computation == 'yes' ){ 

                                                $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id  );

                                            }

                                            if( $display_number == 'yes'){

                                                $display_grade =number_format( $display_grade, $decimal_finalgrade,".","");

                                            }

                                            if( $display_card == 'yes'){

                                                if( ( $complete_first && $complete_second ) || ( $complete_third && $complete_fourth ) ){

                                                    if( $display_number == 'yes'){

                                                        if( $display_grade == null || $display_grade == 0 || $display_grade == 0.00 ){

                                                            $complete_grades = false;

                                                            if( $if_gradeisnull == 'blank'){

                                                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>'; 

                                                            }  else {

                                                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.number_format(0, $decimal_grades, '.', '').'</td>'; 

                                                            }

                                                        } else {

                                                            if( $display_grade < 75 ){

                                                                echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_grade.'</td>'; 

                                                            } else {

                                                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_grade.'</td>'; 

                                                            }

                                                        }

                                                    } else {

                                                        echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_grade.'</td>'; 

                                                    }

                                                } else {

                                                    $complete_grades = false;

                                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>'; 

                                                }

                                            }

                                            if( $include_computation == 'yes'){

                                                $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id  );

                                                if( empty($checkifChild)){

                                                    $display_grade = $display_grade * $units;

                                                    $total_grade += $display_grade;

                                                    $total_subject_list=$total_subject_list + $units;

                                                }

                                            }

                                        } else { 

                                            $grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id  );

                                            if( $include_computation == 'yes'){

                                                if( $grade_per_quarter == null ){

                                                    $complete_grades = false;

                                                    $complete_quarter = false;

                                                }

                                                $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id  );

                                                if( empty($checkifChild)){

                                                    $grade_per_quarter_with_unit = $grade_per_quarter * $units;

                                                    $display_total_grade = $display_total_grade + $grade_per_quarter_with_unit;

                                                    $count_subject = $count_subject + $units;

                                                }

                                            }

                                            if( $display_card == 'yes'){

                                                if( $display_number == 'yes'  ){

                                                    if( $grade_per_quarter == null || $grade_per_quarter == 0  ){

                                                        if( $if_gradeisnull == 'blank'){

                                                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>'; 

                                                        }  else {

                                                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.number_format(0, $decimal_grades, '.', '').'</td>'; 

                                                        }

                                                    } else {

                                                        if( $grade_per_quarter < 75 ){

                                                            echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$grade_per_quarter.'</td>'; 

                                                        } else {

                                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$grade_per_quarter.'</td>'; 

                                                        }

                                                    }

                                                } else {

                                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$grade_per_quarter.'</td>'; 

                                                }

                                            }

                                        }

                                    } else {

                                        if( $display_card == 'yes'){

                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">N/A</td>'; 

                                        }

                                    } 

                                  } 

                                } else {

                                    if( $display_card == 'yes'){

                                        echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">N/A</td>'; 

                                    }

                                }

                                } else {

                                    if( $display_card == 'yes'){

                                        echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">N/A</td>'; 

                                    }

                                }

                            }

                            if( $semester_id ){

                                $getConductSubject = $this->conductimportsubject_model->getStudentConduct( $student_id, $quarter, $class_id, $semester_id, $strand_id  );

                            } else {

                                $getConductSubject = $this->conductimportsubject_model->getStudentConduct( $student_id, $quarter, $class_id );

                            }

                            if( $getConductSubject ){

                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.round($getConductSubject).'</td>';

                            } else {

                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>';

                            }

                            

                            if( $quarter == "final"){

                                if( $complete_grades ){

                                    $display_total_grade =  $total_subject_list != 0 ? $total_grade / $total_subject_list:0;

                                    $display_total_grade =number_format( (float)$display_total_grade,$decimal_average,".",".");

                                    if( $display_total_grade == null || $display_total_grade == 0 || $display_total_grade == '0.00' ){

                                        if( $if_gradeisnull == 'blank'  ){

                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>'; 

                                        } else {

                                            echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_total_grade.'</td>'; 

                                        }

                                    } else {

                                        if( $display_total_grade < 75  ){

                                            echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_total_grade.'</td>'; 

                                        } else {

                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_total_grade.'</td>'; 

                                        }

                                    }

                                } else {

                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>'; 

                                }

                            } else {

                                if( $complete_quarter ){

                                    $average_grade =  $count_subject != 0?$display_total_grade/$count_subject:0;

                                    $average_grade =  number_format($average_grade, $decimal_average, '.', '');

                                    if( $average_grade == null || $average_grade == 0 || $average_grade == '0.00' ){

                                        if( $if_gradeisnull == 'blank'  ){

                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>'; 

                                        } else {

                                            echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$average_grade.'</td>'; 

                                        }

                                    } else {

                                        if( $average_grade < 75 ){

                                            echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$average_grade.'</td>'; 

                                        } else {

                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$average_grade.'</td>'; 

                                        }

                                    }

                                } else {

                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>'; 

                                }

                            }

                            ?>

                            </tr>

                            <?php

                            $row_count++;

                    }

                }

                ?>  

                </tbody>

            </table>

            <?php 

        } else {

            ?>

            <table class="tobe_bordered" style=" border: 1px solid black;  border-collapse: collapse; width: 100%;">

                   <tr>

                        <th style=" border: 1px solid black" height="179">Names</th> 

                        <?php

                         if( $subjectlist ){

                             $xy=0;

                            foreach ($subjectlist as $subject_key => $subject_value) { 

                                $subject_id = $subject_value['id'];

                                $subject_name = $subject_value['name'];

                                $sm_details = $this->subjectmanager_model->getBySubjectSession( $subject_id , $session_id );

                                $display_card = $sm_details['display_card'];

                                $display_number = $sm_details['display_number'];

                                if( $display_card == 'yes'){    

                                    $xy++;                              

                                    echo '<th text-rotate="90" style="height: 179px; line-height: 13px; vertical-align: bottom; padding-bottom:6px; border: 1px solid black;word-wrap: break-word;"><div style=" width: 215px; " >'.$subject_name.'</div></th>'; 

                                }

                            }

                        }

                        if( $quarter != 'final'){   

                            echo '<th text-rotate="90" style="height: 179px; line-height: 13px; vertical-align: bottom; padding-bottom:6px; border: 1px solid black;word-wrap: break-word;"><div style=" width: 215px; " >Conduct</div></th>';

                        }

                        echo '<th text-rotate="90" style="height: 179px; line-height: 13px; vertical-align: bottom; padding-bottom:6px; border: 1px solid black;word-wrap: break-word;"><div style=" width: 215px; " >Gen Ave</div></th>';

                        ?>

                    </tr>

                <tbody>

                    <?php if (empty($studentlist)) {

                        ?>

                        <tr>

                            <td colspan="32" class="text-danger text-center" style= "border: 1px solid black"><?php echo $this->lang->line('no_record_found'); ?></td>



                        </tr>

                        <?php

                    } else {

                        $row_count = 1;

                        if (!empty( $studentlist_male )) { 

                            ?>

                            <tr>

                                <td style= "border-left: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;text-transform:uppercase;width:300px;border-right:0;"><b>Male</b></td>

                                <?php 

                                for( $jo=1;$jo<=$xy;$jo++){

                                    ?>

                                    <td class="mailbox-name"></td>

                                    <?php

                                }

                                ?>

                                <td class="mailbox-name"></td>

                                <?php 

                                if( $quarter != 'final'){   

                                    ?>

                                    <td class="mailbox-name"></td>

                                    <?php 

                                }

                                ?>

                            <tr>

                            <?php

                            foreach ($studentlist_male as  $student_value) { 

                                $student_id = $student_value["id"];

                                $lastname = $student_value['lastname'];

                                $firstname = $student_value['firstname'];

                                $suffix = $student_value['suffix'];

                                $middlename = !empty($student_value['middlename'])?$student_value['middlename'][0].'.':'';

                                $url = base_url().'teacher/report/show_report_card/'.$student_id; 

                                ?>

                                <tr> 

                                    <td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;text-transform:uppercase;width:300px;"><?php echo $lastname.', '.$firstname.' '.$suffix.' '.$middlename; ?></td> 

                                    <?php

                                        if( $subjectlist ){

                                            $total_grade = 0;

                                            $total_subject_list = 0;

                                            $total_grade = 0;

                                            $total_subject_list = 0;

                                            $display_total_grade = 0; 

                                            $average_grade = 0; 

                                            $count_subject = 0;

                                            $get_count = 3;

                                            $last_quarter = 3;

                                            $complete_first =true;

                                            $complete_second =true;

                                            $complete_third =true;

                                            $complete_fourth =true; 

                                            $complete_quarter = true;

                                            foreach ($subjectlist as $subject_key => $subject_value) {

                                                $complete_grades =true;

                                                $get_final_grade = 0;

                                                $subject_id = $subject_value['id'];

                                                $sm_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id  );

                                                $display_card = $sm_details['display_card'];

                                                $display_number = 'yes';

                                                $include_computation = $sm_details['include_computation'];

                                                if( $quarter == "final"){    

                                                    for($xy=1;$xy<=$last_quarter;$xy++){

                                                        $grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $xy, $combine_category, $session_id  );

                                                        if( $display_number == 'yes'){

                                                            $get_final_grade = $get_final_grade + $grade_per_quarter;

                                                        }

                                                        if( $xy == 1 ){

                                                            if( $grade_per_quarter == null && $include_computation == 'yes' ){

                                                                $complete_first = false;

                                                            }

                                                        }

                                                        if( $xy == 2 ){

                                                            if( $grade_per_quarter == null && $include_computation == 'yes' ){

                                                                $complete_second = false;

                                                            }

                                                        }

                                                        if( $xy == 3 ){

                                                            if( $grade_per_quarter == null && $include_computation == 'yes' ){

                                                                $complete_third = false;

                                                            }

                                                        }

                                                        if( $xy == 4 ){

                                                            if( $grade_per_quarter == null && $include_computation == 'yes' ){

                                                                $complete_fourth = false;

                                                            }

                                                        }

                                                    }

                                                    if( $display_number == 'yes' ){

                                                        $display_grade = $get_final_grade / $last_quarter;

                                                    }

                                                    if( $include_computation == 'yes' ){ 

                                                        $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id  );

                                                    }

                                                    if( $display_number == 'yes' ){

                                                        $display_grade = number_format((float)$display_grade, $decimal_finalgrade, '.', '');

                                                    }

                                                    if( $display_card == 'yes'  ){

                                                        if( $complete_first && $complete_second && $complete_third && $complete_fourth ){

                                                            if( $display_number == 'yes' ){

                                                                if( $display_grade == 0 ){

                                                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>';  

                                                                } else {

                                                                    if( $display_grade < 75 ){

                                                                        echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_grade.'</td>';  

                                                                    } else {

                                                                        echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_grade.'</td>';  

                                                                    }

                                                                }

                                                            } else {

                                                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>';  

                                                            }

                                                        } else {

                                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>';  

                                                            $complete_grades = false;   

                                                        }

                                                    }

                                                    if( $include_computation == 'yes'){

                                                        $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id  );

                                                        if( empty($checkifChild)){

                                                            $total_grade += $display_grade;

                                                             $total_subject_list++;

                                                        }

                                                    }

                                                } else {

                                                

                                                    $grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id  );

                                                    if( $include_computation == 'yes'){

                                                        $units = 1;

                                                        if( $grade_per_quarter == null ){

                                                            $complete_grades = false;

                                                            $complete_quarter = false;

                                                        }

                                                        $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id  );

                                                        if( empty($checkifChild)){

                                                            $grade_per_quarter_with_unit = $grade_per_quarter * $units;

                                                            $display_total_grade = $display_total_grade + $grade_per_quarter_with_unit;

                                                            $count_subject = $count_subject + $units;

                                                        }

                                                    }

                                                    if( $display_card == 'yes'  ){

                                                        if( $display_number == 'yes'  ){

                                                            if( $grade_per_quarter == null || $grade_per_quarter == 0  ){

                                                                if( $if_gradeisnull == 'blank'){

                                                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>'; 

                                                                }  else {

                                                                    echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.number_format(0, $decimal_grades, '.', '').'</td>'; 

                                                                }

                                                            } else {

                                                                if( $grade_per_quarter < 75 ){

                                                                    echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$grade_per_quarter.'</td>'; 

                                                                } else {

                                                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$grade_per_quarter.'</td>'; 

                                                                }

                                                            }

                                                        } else {

                                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$grade_per_quarter.'</td>'; 

                                                        }

                                                    }



                                                  }

                                            }

                                            if( $semester_id ){

                                                $getConductSubject = $this->conductimportsubject_model->getStudentConduct( $student_id, $quarter, $class_id, $semester_id, $strand_id  );

                                            } else {

                                                $getConductSubject = $this->conductimportsubject_model->getStudentConduct( $student_id, $quarter, $class_id );

                                            }

                                            if( $getConductSubject ){

                                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.round($getConductSubject).'</td>';

                                            } else {

                                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>';

                                            }

                                            if( $quarter == "final"){

                                                if( $complete_grades ){

                                                    $display_total_grade =  $total_grade / $total_subject_list;

                                                    $display_total_grade =number_format( (float)$display_total_grade,2,".",".");

                                                    if( $display_total_grade < 75 ){

                                                        echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_total_grade.'</td>'; 

                                                    } else {

                                                        echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_total_grade.'</td>';   

                                                    }

                                                } else {

                                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>';   

                                                }

                                            } else {

                                                if( $complete_quarter ){

                                                    $average_grade =  $count_subject != 0?$display_total_grade/$count_subject:0;

                                                    $average_grade =  number_format($average_grade, $decimal_average, '.', '');

                                                    if( $average_grade < 75 ){

                                                        echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$average_grade.'</td>'; 

                                                    } else {

                                                        echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$average_grade.'</td>'; 

                                                        

                                                    }

                                                } else {

                                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>';   

                                                }

                                            }



                                        }

                                    ?>  

                                </tr>

                                <?php

                                $row_count++;

                            }

                        }

                        if (!empty( $studentlist_female )) { 

                            ?>

                            <tr>

                                <td style= "border-left: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;text-transform:uppercase;width:300px;"><b>Female</b></td>

                                <?php 

                                for( $jo=1;$jo<=$xy;$jo++){

                                    ?>

                                    <td class="mailbox-name"></td>

                                    <?php

                                }

                                ?>

                                <td class="mailbox-name"></td>

                                <?php 

                                if( $quarter != 'final'){   

                                    ?>

                                    <td class="mailbox-name"></td>

                                    <?php 

                                }

                                ?>

                            <tr>

                            <?php

                            foreach ($studentlist_female as  $student_value) { 

                                $student_id = $student_value["id"];

                                $lastname = $student_value['lastname'];

                                $firstname = $student_value['firstname'];

                                $suffix = $student_value['suffix'];

                                $middlename = !empty($student_value['middlename'])?$student_value['middlename'][0].'.':'';

                                $url = base_url().'teacher/report/show_report_card/'.$student_id; 

                                ?>

                                <tr> 

                                    <td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;text-transform:uppercase;width:300px;"><?php echo $lastname.', '.$firstname.' '.$suffix.' '.$middlename; ?></td> 

                                    <?php

                                        if( $subjectlist ){

                                            $total_grade = 0;

                                            $total_subject_list = 0;

                                            $total_grade = 0;

                                            $total_subject_list = 0;

                                            $display_total_grade = 0; 

                                            $average_grade = 0; 

                                            $count_subject = 0;

                                            $get_count = 3;

                                            $last_quarter = 3;

                                            $complete_first =true;

                                            $complete_second =true;

                                            $complete_third =true;

                                            $complete_fourth =true; 

                                            $complete_quarter = true;

                                            foreach ($subjectlist as $subject_key => $subject_value) {

                                                $complete_grades =true;

                                                $get_final_grade = 0;

                                                $subject_id = $subject_value['id'];

                                                $sm_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id  );

                                                $display_card = $sm_details['display_card'];

                                                $display_number = 'yes';

                                                $include_computation = $sm_details['include_computation'];

                                                if( $quarter == "final"){    

                                                    for($xy=1;$xy<=$last_quarter;$xy++){

                                                        $grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $xy, $combine_category, $session_id  );

                                                        if( $display_number == 'yes'){

                                                            $get_final_grade = $get_final_grade + $grade_per_quarter;

                                                        }

                                                        if( $xy == 1 ){

                                                            if( $grade_per_quarter == null && $include_computation == 'yes' ){

                                                                $complete_first = false;

                                                            }

                                                        }

                                                        if( $xy == 2 ){

                                                            if( $grade_per_quarter == null && $include_computation == 'yes' ){

                                                                $complete_second = false;

                                                            }

                                                        }

                                                        if( $xy == 3 ){

                                                            if( $grade_per_quarter == null && $include_computation == 'yes' ){

                                                                $complete_third = false;

                                                            }

                                                        }

                                                        if( $xy == 4 ){

                                                            if( $grade_per_quarter == null && $include_computation == 'yes' ){

                                                                $complete_fourth = false;

                                                            }

                                                        }

                                                    }

                                                    if( $display_number == 'yes' ){

                                                        $display_grade = $get_final_grade / $last_quarter;

                                                    }

                                                    if( $include_computation == 'yes' ){ 

                                                        $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id  );

                                                    }

                                                    if( $display_number == 'yes' ){

                                                        $display_grade = number_format((float)$display_grade, $decimal_finalgrade, '.', '');

                                                    }

                                                    if( $display_card == 'yes'  ){

                                                        if( $complete_first && $complete_second && $complete_third && $complete_fourth ){

                                                            if( $display_number == 'yes' ){

                                                                if( $display_grade == 0 ){

                                                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>';  

                                                                } else {

                                                                    if( $display_grade < 75 ){

                                                                        echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_grade.'</td>';  

                                                                    } else {

                                                                        echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_grade.'</td>';  

                                                                    }

                                                                }

                                                            } else {

                                                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>';  

                                                            }

                                                        } else {

                                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>';  

                                                            $complete_grades = false;   

                                                        }

                                                    }

                                                    if( $include_computation == 'yes'){

                                                        $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id  );

                                                        if( empty($checkifChild)){

                                                            $total_grade += $display_grade;

                                                             $total_subject_list++;

                                                        }

                                                    }

                                                } else {

                                                

                                                    $grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id  );

                                                    if( $include_computation == 'yes'){

                                                        $units = 1;

                                                        if( $grade_per_quarter == null ){

                                                            $complete_grades = false;

                                                            $complete_quarter = false;

                                                        }

                                                        $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id  );

                                                        if( empty($checkifChild)){

                                                            $grade_per_quarter_with_unit = $grade_per_quarter * $units;

                                                            $display_total_grade = $display_total_grade + $grade_per_quarter_with_unit;

                                                            $count_subject = $count_subject + $units;

                                                        }

                                                    }

                                                    if( $display_card == 'yes'  ){

                                                        if( $display_number == 'yes'  ){

                                                            if( $grade_per_quarter == null || $grade_per_quarter == 0  ){

                                                                if( $if_gradeisnull == 'blank'){

                                                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>'; 

                                                                }  else {

                                                                    echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.number_format(0, $decimal_grades, '.', '').'</td>'; 

                                                                }

                                                            } else {

                                                                if( $grade_per_quarter < 75 ){

                                                                    echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$grade_per_quarter.'</td>'; 

                                                                } else {

                                                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$grade_per_quarter.'</td>'; 

                                                                }

                                                            }

                                                        } else {

                                                            echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$grade_per_quarter.'</td>'; 

                                                        }

                                                    }



                                                  }

                                            }

                                            if( $semester_id ){

                                                $getConductSubject = $this->conductimportsubject_model->getStudentConduct( $student_id, $quarter, $class_id, $semester_id, $strand_id  );

                                            } else {

                                                $getConductSubject = $this->conductimportsubject_model->getStudentConduct( $student_id, $quarter, $class_id );

                                            }

                                            if( $getConductSubject ){

                                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.round($getConductSubject).'</td>';

                                            } else {

                                                echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>';

                                            }

                                            if( $quarter == "final"){

                                                if( $complete_grades ){

                                                    $display_total_grade =  $total_grade / $total_subject_list;

                                                    $display_total_grade =number_format( (float)$display_total_grade,2,".",".");

                                                    if( $display_total_grade < 75 ){

                                                        echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_total_grade.'</td>'; 

                                                    } else {

                                                        echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$display_total_grade.'</td>'; 

                                                    }

                                                } else {

                                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>';   

                                                }

                                            } else {

                                                if( $complete_quarter ){

                                                    $average_grade =  $count_subject != 0?$display_total_grade/$count_subject:0;

                                                    $average_grade =  number_format($average_grade, $decimal_average, '.', '');

                                                    if( $average_grade < 75  ){

                                                        echo '<td style= "color:red;border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$average_grade.'</td>'; 

                                                    } else {

                                                        echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;">'.$average_grade.'</td>';     

                                                    }

                                                } else {

                                                    echo '<td style= "border: 1px solid black; font-size: 12px;  padding-left: 6px;  padding-right: 6px;"></td>';   

                                                }

                                            }



                                        }

                                    ?>  

                                </tr>

                                <?php

                                $row_count++;

                            }

                        }

                    }

                    ?>

                </tbody>

            </table>

            <?php

        }

        ?>

        <br/> 

        <table width="100%">

            <tr>

                <td width="60%">I hereby certify that the entries are true and correct</td>

                <td width="35%" style=" border-bottom: 1px solid black;"></td>

                <td width="5%"></td>

            </tr>

            <tr>

                <td width="60%"></td>

                <td width="35%" style="text-align: center;">Signature</td>

                <td width="5%"></td>

            </tr>

        </table>

        <br/>

        <br/> 

        <table width="100%">

            <tr>

                <td width="60%" align="right">Approved by:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                <?php 

                 if( $session_id == '13'){

                    $principal_fullname = 'Jesreel D. Mercader';

                } ?>

                <td width="35%" style=" border-bottom: 1px solid black;text-transform:uppercase;text-align: center;"><?php echo $principal_fullname;?></td>

                <td width="5%"></td>

            </tr>

            <tr>

                <td width="60%"></td>

                <td width="35%" style="text-align: center;">Principal</td>

                <td width="5%"></td>

            </tr>

        </table>

        <?php

        $content = ob_get_contents();

        ob_end_clean(); 

        $data['content'] = $content;

        $html = $this->load->view('template/master_sheet',$data, true ); 

        $pdfFilePath = "Master_Sheet_".$class_name."_".$section_name.".pdf";

        $pdfFilePath = str_replace(' ','_',$pdfFilePath );

        $this->load->library('m_pdf');

        $this->mpdf = new mPDF('utf-8',array(210,160)); 

        $this->m_pdf->pdf->SetDisplayMode('fullwidth');

        $this->m_pdf->pdf->shrink_tables_to_fit = 1;

        $this->m_pdf->pdf->AddPage('P','LEGAL','5','5','5','5','5','5','5','5');

        $this->m_pdf->pdf->WriteHTML($html);

        $this->m_pdf->pdf->Output($pdfFilePath, "D");

    }



    public function print_master_sheet_sf5_old(){  

        

        $class_id = $this->input->post('class_id');

        $section_id = $this->input->post('section_id'); 

        $quarter = $this->input->post('quarter');  

        $action = $this->input->post('action');

        $semester_id = $this->input->post('semester_id');

        $strand_id = $this->input->post('strand_id');



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

        $principal_id = $setting_result[0]['principal_id']; 

        $principal_details = $this->principal_model->get(  $principal_id );

        $principal_name = $principal_details['name'];

        $principal_middlename = $principal_details['middlename'];

        $principal_lastname = $principal_details['lastname']; 

        if( $principal_middlename  ){

            $principal_middlename_first_character_length =  strlen($principal_middlename);

            if( $principal_middlename_first_character_length == 2 ){ 

                $principal_middlename_display =  ucfirst(  $principal_middlename ).'.';

            } else { 

                $principal_middlename_first_character = substr($principal_middlename, 0, 1);

                $principal_middlename_display =  ucfirst(  $principal_middlename_first_character ).'.';

            }

            

        } else {

             $principal_middlename_display = '';

        } 

        $principal_fullname =  ucfirst( $principal_name )." ".$principal_middlename_display." ".ucfirst(   $principal_lastname );  

 

        $this->excel->generate_sf5( $class_id, $section_id, $quarter, $semester_id, $strand_id, $registrar_fullname, $principal_fullname );

          

    }

	

	public function print_master_sheet_sf5(){  

        

        $class_id = $this->input->post('class_id');

        $section_id = $this->input->post('section_id'); 

        $quarter = $this->input->post('quarter');  

        $action = $this->input->post('action');

        $semester_id = $this->input->post('semester_id');

        $session_id = $this->input->post('session_id');

		

		$teacher_advisory =  $this->classsection_model->get_teacher_advisory( $class_id,  $section_id, $session_id );

		$teacher_lastname = !empty($teacher_advisory->lastname )?$teacher_advisory->lastname:'';

		$teacher_name = !empty($teacher_advisory->name )?$teacher_advisory->name:'';

		$teacher_middlename = !empty($teacher_advisory->middlename )?$teacher_advisory->middlename[0]:'';

		$registrar_fullname =  ucfirst( $teacher_name )." ".$teacher_middlename." ".ucfirst(   $teacher_lastname ); 

		

        $setting_result = $this->setting_model->get();

        $principal_id = $setting_result[0]['principal_id']; 

        $principal_details = $this->principal_model->get(  $principal_id );

        $principal_name = $principal_details['name'];

        $principal_middlename = $principal_details['middlename'];

        $principal_lastname = $principal_details['lastname']; 

        if( $principal_middlename  ){

            $principal_middlename_first_character_length =  strlen($principal_middlename);

            if( $principal_middlename_first_character_length == 2 ){ 

                $principal_middlename_display =  ucfirst(  $principal_middlename ).'.';

            } else { 

                $principal_middlename_first_character = substr($principal_middlename, 0, 1);

                $principal_middlename_display =  ucfirst(  $principal_middlename_first_character ).'.';

            }

            

        } else {

             $principal_middlename_display = '';

        } 

        $principal_fullname =  ucfirst( $principal_name )." ".$principal_middlename_display." ".ucfirst(   $principal_lastname ); 

		if( $session_id == '13'){

			$principal_fullname = 'Jesreel D. Mercader';

		} elseif( $session_id == '14'){

            $principal_fullname == 'ALBERTO B. PONSICA, PH. D.';

		} else {

            $principal_fullname = $principal_fullname;

        }

 

        $this->excel->generate_sf5_new( $class_id, $section_id, $quarter, $semester_id, $registrar_fullname, $principal_fullname, $session_id );



    }

	

	

	 public function print_grade_sheet( $class_id, $section_id, $subject_id, $semester_id=NULL, $session_id ){  

       if(empty($session_id)){

			$setting_result = $this->setting_model->get();

			$session_id = $setting_result[0]['session_id'];

		}

		

		

		$session_details = $this->session_model->get( $session_id );

       	$session_current = $session_details['session'];

		

        $centenary = substr($session_current, 0, 2); //2017-18 to 2017

        // SETTINGS

        $data['student_session_id'] = $session_current;

        $setting_result = $this->setting_model->get();

        $data['school_name'] =  $setting_result[0]['name'];

        $data['logo'] = $setting_result[0]['image'];

        $data['email'] =  $setting_result[0]['email'];

        $address_barangay_id =  $setting_result[0]['address_barangay_id'];

        $address_barangay =$this->address_model->get_barangay($address_barangay_id);

        $address_city_id =  $setting_result[0]['address_city_id'];

        $address_city =$this->address_model->get_city($address_city_id);

        $address_province_id =  $setting_result[0]['address_province_id'];

        $province_name =$this->address_model->get_province($address_province_id);

        $data['barangay'] = $address_barangay['name'];

        $data['city'] = $address_city['name'];

        $data['province'] = $province_name['name'];

        $data['address'] = $setting_result[0]['address'];

        $data['phone'] = $setting_result[0]['phone'];

        $school_year = "S. Y. ".$session_current;

            

        $class_array = $this->class_model->get($class_id);

        $subject_array = $this->subject_model->get($subject_id);

        $class_name = $class_array['class'];

        $subject_name = $subject_array['name'];

        $section_array = $this->section_model->get($section_id);

        $section_name = $section_array['section'];

		

		$principal_id = $setting_result[0]['principal_id'];

		$principal_details = $this->principal_model->get(  $principal_id );

		$principal_sex = isset($principal_details['sex'])?$principal_details['sex']:'Male';

        if(  $principal_sex == 'Female'){

            $principal_sex_display = "Ms.";

        } else {

            $principal_sex_display =  "Mr.";

        }

		$principal_firstname = isset($principal_details['name'])?$principal_details['name']:'';

		$principal_midname = isset($principal_details['middlename'])?$principal_details['middlename']:'';

		$principal_lastname = isset($principal_details['lastname'])?$principal_details['lastname']:'';

        $principal_fullname = $this->setting_model->getprincipalformat( '', $principal_firstname, $principal_midname, $principal_lastname );

		

		$gradingsettings = $this->gradingsetting_model->getbySession( $session_id );

		$decimal_grades  = isset($gradingsettings->decimal_grades)?$gradingsettings->decimal_grades:0;

		$decimal_average = isset($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;

		$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:2;

		$if_gradeisnull = isset($gradingsettings->if_gradeisnull)?$gradingsettings->if_gradeisnull:'blank';

		$combine_category = 'ga';

        $subjectlist = $this->subject_model->getSubjctByClass( $class_id ); 

        // $studentlist = $this->student_model->searchByClassSection($class_id, $section_id);

        $studentlistMale = $this->student_model->getstudentsByClassSectionGender($class_id, $section_id, 'Male', $session_id ); 

        $studentlistFemale = $this->student_model->getstudentsByClassSectionGender($class_id, $section_id, 'Female', $session_id );

	

		//$teacher_details = $this->teachersubject_model->getteacherbyClsandSectionSubject($class_id, $section_id, $subject_id);

        

        $enableStrand = $this->strand_model->checkEnable( $class_id );



        $names_male = array(); 

        foreach ($studentlistMale as $userMale) {

            $names_male[] =  $userMale['lastname'];

        }  

        array_multisort($names_male, SORT_ASC, $studentlistMale); 



        $names_female = array(); 

        foreach ($studentlistFemale as $userFemale) {

            $names_female[] =  $userFemale['lastname'];

        }  

        array_multisort($names_female, SORT_ASC, $studentlistFemale); 

           



        //$data['school_name'] = $school_name;

        //$data['school_address'] = $school_address;

        $data['school_year'] = $school_year;

        $data['class_name'] = $class_name;

        $data['section_name'] = $section_name == 'None'?'':$section_name;  

        $data['subject_name'] = $subject_name;  

		$get_teacher_advisory = $this->classsection_model->get_teacher_advisory($class_id, $section_id, $session_id );

        $teachers_advisory_name = !empty($get_teacher_advisory->name)?$get_teacher_advisory->name:'';

		$teacher_advisory_midname = !empty($get_teacher_advisory->middlename)?$get_teacher_advisory->middlename[0].'.':' ';

		$teacher_advisory_lastname = !empty($get_teacher_advisory->lastname)?$get_teacher_advisory->lastname:'';

		

		$teacher_details = $this->teachersubject_model->getteacherbyClsandSectionSubject($class_id, $section_id, $subject_id, $session_id );

		$teacher_lastname =  !empty($teacher_details['teacher_lastname'])?$teacher_details['teacher_lastname']:'';

		$teacher_firstname =  !empty($teacher_details['teacher_name'])?$teacher_details['teacher_name']:'';

		$teacher_middlename = !empty( $teacher_details['teacher_midname'])?$teacher_details['teacher_midname'][0].'.':' ';

		if(empty( $teacher_details )){

			if( !empty( $get_teacher_advisory )){

				$data['teacher_name'] = $teacher_advisory_lastname.', '.$teachers_advisory_name.' '.$teacher_advisory_midname;

			} else {

				$data['teacher_name'] = '';

			}

		} else {

			$data['teacher_name'] = $teacher_lastname.', '.$teacher_firstname.' '.$teacher_middlename;

		}

		$get_teacher_advisory_gender = isset($get_teacher_advisory->sex )?$get_teacher_advisory->sex:'Male';

		if( $get_teacher_advisory_gender ){

            $prefix = 'Ms.';

        } else {

            $prefix = 'Mr.';

        }

		if( !empty( $get_teacher_advisory )){

			$data['teacher_name_upper'] = $teachers_advisory_name.' '.$teacher_advisory_midname.' '.$teacher_advisory_lastname;

		} else {

			$data['teacher_name_upper'] = '';

		}

	   $data['quarter_display'] = ''; 

	   $total_quarter = 4;

        ob_start();

        ?> 

        <table width="100%">

            <tr>

                <td width="48%" style="vertical-align: top"> 

                    <?php 

                    if( $studentlistMale ) {

                        ?>

                        <table class="tobe_bordered" width="100%" style=" border: 1px solid black;  border-collapse: collapse;">

                            <thead>

                                <tr> 

                                    <th style=" border: 1px solid black" colspan="2">Names</th> 

                                    <?php

                                    if( $enableStrand ){

                                        $gradingsettings = $this->gradingsetting_model->getBySession( $session_id );

                                        $get_quarter = array();

                                        $total_quarter =0;

                                        if( $semester_id == 1 ){

                                            $get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array(); 

                                            $get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;

                                            $total_quarter = count( $get_quarter );

                                        } else {

                                            $get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();   

                                            $get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;

                                            $total_quarter = count( $get_quarter );

                                        }

                                        

                                        if( $get_quarter ){

                                            foreach( $get_quarter as $key => $value ){

                                                ?>

                                                <th style=" border: 1px solid black;padding:2px 5px;" ><?php echo ordinal_suffix_roman($value);?></th>

                                                <?php

                                            }

                                           /*  ?><th>F</th><?php */

                                        }

                                    } else {

                                        for($x=1;$x<=4;$x++){

                                            ?>

                                            <th style=" border: 1px solid black;padding:2px 5px;" ><?php echo ordinal_suffix_roman($x);?></th>  

                                            <?php

                                        }

                                    }   

                                    ?>

									<th style=" border: 1px solid black;padding:2px 5px;" ><small>Final Grade</small></th>  

                                </tr>

                            </thead>

                            <tbody>

                                <?php if (empty($studentlistMale)) {

                                    ?>

                                    <tr>

                                        <td colspan="32" class="text-danger text-center" style= "border: 1px solid black"><?php echo $this->lang->line('no_record_found'); ?></td>



                                    </tr>

                                    <?php

                                } else {

                                    $row_count = 1;

                                    foreach ($studentlistMale as  $student_value) {  

											$complete_grades = true;

											$student_id = $student_value["id"]; 

											$lastname = $student_value['lastname'];

											$firstname = $student_value['firstname'];

											$suffix = $student_value['suffix'];

											$middlename = !empty($student_value['middlename'])?$student_value['middlename'][0].'.':'';

											?>

                                        <tr> 

                                            <td style= "border: 1px solid black; text-align: right; font-size: 11px;"><?php echo $row_count;?></td>

                                            <td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;text-transform:uppercase;"><?php echo $lastname.', '.$firstname.' '.$suffix.' '.$middlename; ?></td> 

                                            <?php 

                                            if( $enableStrand ){

                                                $final_grade = 0;

												$not_available = false;

                                                for( $qtr=0;$qtr<$total_quarter;$qtr++ ) {

                                                    $y = $get_quarter[$qtr];

													if( $y == 1 || $y == 2 ){

														$semester_id = 1;

													} elseif( $y == 3 || $y == '4'){

														$semester_id = 2;

													}

													$getStudentSubjectSemesterDrop = $this->customsubject_model->getStudentSubjectSemesterDrop( $student_id, $subject_id, $semester_id , $session_id );

													if( empty( $getStudentSubjectSemesterDrop)){

														$not_available = false;

														$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $y, $combine_category, $session_id  );

														$grade_per_quarter = isset($grade_per_quarter)?$grade_per_quarter:null;

														$grade_per_quarter = number_format($grade_per_quarter, $decimal_grades, '.', '');

														if( $grade_per_quarter == null || $grade_per_quarter == 0 ){

															$complete_grades = false;

															if( $if_gradeisnull == 'blank'){

																 echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;"></td>';

															}  else {

																echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;">'.number_format((float)0, $decimal_grades, '.', '').'</td>';

															}

														} else {

															$final_grade = $final_grade + $grade_per_quarter;

															 echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >'.$grade_per_quarter.'</td>';

														}

													} else {

														$not_available = true;

														 echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >N/A</td>';

													}

                                                }

											   	if( $not_available ){

													 echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >N/A</td>';

												} else {

													if( $complete_grades && $final_grade != 0 ){

														$average = $final_grade / $total_quarter;

														$average = number_format( $average, $decimal_finalgrade, '.',' ');

														echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >'.$average.'</td>';

													} else {

														echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;"></td>';

													}

												}

														

                                               

                                            } else {

												$final_grade= 0;

                                                for( $x=1;$x<=4;$x++){

													$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $x, $combine_category, $session_id );

													$grade_per_quarter = isset($grade_per_quarter)?$grade_per_quarter:null;

													$grade_per_quarter = number_format($grade_per_quarter, $decimal_grades, '.', '');

													if( $grade_per_quarter == null || $grade_per_quarter == 0 ){

														$complete_grades = false;

														if( $if_gradeisnull == 'blank'){

															echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;"></td>';

														}  else {

															echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;">'.number_format((float)0, $decimal_grades, '.', '').'</td>';

														}

													} else {

														$final_grade = $final_grade + $grade_per_quarter;

														echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;">'.$grade_per_quarter.'</td>';

													}

                                                }

												if( $complete_grades && $final_grade != 0 ){

													$average = $final_grade / $total_quarter;

													$average = number_format( $average, $decimal_finalgrade, '.',' ');

													 echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >'.$average.'</td>';

												} else {

													 echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;"></td>';

												}

                                            }

                                            ?>  

                                        </tr>

                                        <?php

                                        $row_count++; 

                                    }

                                }

                                ?>

                            </tbody> 

                        </table>  

                        <?php

                    }

                    ?>

                </td>

                <td width="4%"> &nbsp;</td>

                <td align="right" width="48%" style="vertical-align: top">

                    <?php 

                    if( $studentlistFemale ) {

                        ?>

                        <table class="tobe_bordered" width="100%" style=" border: 1px solid black;  border-collapse: collapse;" align="left">

                            <thead>

                                <tr> 

                                    <th style=" border: 1px solid black" colspan="2">Names</th> 

                                     <?php

                                    if( $enableStrand ){

                                        $gradingsettings = $this->gradingsetting_model->getBySession( $session_id );

                                        $get_quarter = array();

                                        $total_quarter =0;

                                        if( $semester_id == 1 ){

                                            $get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array(); 

                                            $get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;

                                            $total_quarter = count( $get_quarter );

                                        } else {

                                            $get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();   

                                            $get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;

                                            $total_quarter = count( $get_quarter );

                                        }

                                        

                                        if( $get_quarter ){

                                            foreach( $get_quarter as $key => $value ){

                                                ?>

                                                <th style=" border: 1px solid black;padding:2px 5px;" ><?php echo ordinal_suffix_roman($value);?></th>

                                                <?php

                                            }

                                          /*   ?><th>F</th><?php */

                                        }

                                    } else {

                                        for($x=1;$x<=4;$x++){

                                            ?>

                                            <th style=" border: 1px solid black;padding:2px 5px;" ><?php echo ordinal_suffix_roman($x);?></th>  

                                            <?php

                                        }

                                    }   

                                    ?>

									<th style=" border: 1px solid black;padding:2px 5px;" ><small>Final Grade</small></th>  

                                </tr>

                            </thead>

                            <tbody>

                                <?php if (empty($studentlistFemale)) {

                                    ?>

                                    <tr>

                                        <td colspan="32" class="text-danger text-center" style= "border: 1px solid black"><?php echo $this->lang->line('no_record_found'); ?></td>



                                    </tr>

                                    <?php

                                } else {

                                    $row_count = 1;

                                    foreach ($studentlistFemale as  $student_value) {  

										

										$complete_grades = true;

										$student_id = $student_value["id"]; 

										$lastname = $student_value['lastname'];

										$firstname = $student_value['firstname'];

										$suffix = $student_value['suffix'];

										$middlename = !empty($student_value['middlename'])?$student_value['middlename'][0].'.':'';

                                        ?>

                                         <tr> 

                                            <td style= "border: 1px solid black; text-align: right; font-size: 11px;"><?php echo $row_count;?></td>

                                            <td style= "border: 1px solid black; font-size: 11px; text-align: left; padding-left: 6px;  padding-right: 6px;text-transform:uppercase;"><?php echo $lastname.', '.$firstname.' '.$suffix.' '.$middlename; ?></td> 

                                            <?php 

                                            if( $enableStrand ){

                                                $final_grade = 0;

												$not_available = false;

                                                for( $qtr=0;$qtr<$total_quarter;$qtr++ ) {

                                                    $y = $get_quarter[$qtr];

													if( $y == 1 || $y == 2 ){

														$semester_id = 1;

													} elseif( $y == 3 || $y == '4'){

														$semester_id = 2;

													}

													$getStudentSubjectSemesterDrop = $this->customsubject_model->getStudentSubjectSemesterDrop( $student_id, $subject_id, $semester_id , $session_id );

													if( empty( $getStudentSubjectSemesterDrop)){

														 $not_available = false;

														$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $y, $combine_category, $session_id  );

														$grade_per_quarter = isset($grade_per_quarter)?$grade_per_quarter:null;

														$grade_per_quarter = number_format($grade_per_quarter, $decimal_grades, '.', '');

														if( $grade_per_quarter == null || $grade_per_quarter == 0 ){

															$complete_grades = false;

															if( $if_gradeisnull == 'blank'){

																 echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;"></td>';

															}  else {

																echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;">'.number_format((float)0, $decimal_grades, '.', '').'</td>';

															}

														} else {

															$final_grade = $final_grade + $grade_per_quarter;

															 echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >'.$grade_per_quarter.'</td>';

														}

													} else {

														 $not_available = true;

														 echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >N/A</td>';

													}

                                                }

												if( $not_available ){

													 echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >N/A</td>';

												} else {

													if( $complete_grades && $final_grade != 0 ){

														$average = $final_grade / $total_quarter;

														$average = number_format( $average, $decimal_finalgrade, '.',' ');

														echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >'.$average.'</td>';

													} else {

														echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;"></td>';

													}

												}

                                            } else {

												

												$final_grade= 0;

                                                for( $x=1;$x<=4;$x++){

													$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $x, $combine_category, $session_id  );

													$grade_per_quarter = isset($grade_per_quarter)?$grade_per_quarter:null;

													$grade_per_quarter = number_format($grade_per_quarter, $decimal_grades, '.', '');

													if( $grade_per_quarter == null || $grade_per_quarter == 0 ){

														$complete_grades = false;

														if( $if_gradeisnull == 'blank'){

															echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;"></td>';

														}  else {

															echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;">'.number_format((float)0, $decimal_grades, '.', '').'</td>';

														}

													} else {

														$final_grade = $final_grade + $grade_per_quarter;

														echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;">'.$grade_per_quarter.'</td>';

													}

                                                }

												if( $complete_grades && $final_grade != 0 ){

													$average = $final_grade / $total_quarter;

													$average = number_format( $average, $decimal_finalgrade, '.',' ');

													 echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;text-align:left;" >'.$average.'</td>';

												} else {

													 echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;"></td>';

												}

                                            }

                                            ?>  

                                        </tr>

                                        <?php

                                        $row_count++; 

                                    }

                                }

                                ?>

                            </tbody> 

                        </table>  

                        <?php

                    }

                    ?>

                </td>



            </tr>

        </table>

        <br /><br /><br />

		

		<br/>

		<br/>

		<br/>

        <table width="100%">

            <tr>

                <td width="60%">I hereby certify that the entries are true and correct</td>

                <td width="35%" style=" border-bottom: 1px solid black;"></td>

                <td width="5%"></td>

            </tr>

            <tr>

                <td width="60%"></td>

                <td width="35%" style="text-align: center;">Signature</td>

                <td width="5%"></td>

            </tr>

        </table>

		<br/>

		<br/>

		<br/>

		<table width="100%">

            <tr>

                <td width="60%" align="right">Approved by:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

				<?php 

				 if( $session_id == '13'){

					$principal_fullname = 'Jesreel D. Mercader';

				} elseif($session_id == '14'){

                    $principal_fullname == 'ALBERTO B. PONSICA, PH. D.';

                } ?>

                <td width="35%" style=" border-bottom: 1px solid black;text-transform:uppercase;text-align: center;"><?php echo $principal_fullname;?></td>

                <td width="5%"></td>

            </tr>

            <tr>

                <td width="60%"></td>

                <td width="35%" style="text-align: center;">Principal</td>

                <td width="5%"></td>

            </tr>

        </table>

         <?php

        $content = ob_get_contents();

        ob_end_clean(); 

        $data['content'] = $content;  

        $html = $this->load->view('template/grade_sheet',$data, true );   

        $pdfFilePath = "Grade_Sheet_".$class_name."_".$section_name.".pdf";

		$this->load->library('m_pdf');

		$this->mpdf = new mPDF('utf-8',array(210,160)); 

		$this->mpdf->SetDisplayMode('fullwidth');

		$this->m_pdf->pdf->AddPage('P','LEGAL','5','5','5','5','5','5','5','5');

		$this->m_pdf->pdf->WriteHTML($html);

		$this->m_pdf->pdf->Output($pdfFilePath, "D");

    }

	

	

	 public function show_grade_slip(){

        $student_id = $this->input->post('student_id');



        if( $student_id ){

            $quarter =  $this->input->post('quarter');

            $semester_id =  $this->input->post('semester_id');

            $class_id = $this->input->post('class_id');

            $section_id = $this->input->post('section_id');

			$session_id = $this->input->post('session_id');

			$session_id = !empty( $session_id )?$session_id:$this->setting_model->getCurrentSession();

            $data['quarter'] = $quarter;

            $data['semester_id'] = $semester_id;

            $data['class_id'] = $class_id;

            $data['section_id'] = $section_id;

            $data['session_id'] = $session_id;

		

            $category_id = $this->categoryclass_model->getStudentCategoryByClass( $class_id );

            $getFormIDByCode = $this->studentform_model->getFormIDByCode('form138');

            

            $getFormSettings = $this->studentformsettings_model->getSettingsByClass($getFormIDByCode,$class_id);

            

            if(empty( $getFormSettings ) ){

                $getFormSettings = $this->studentformsettings_model->getSettingsByCategory($getFormIDByCode,$category_id);

            }

            $orientation = !empty($getFormSettings['orientation'])?$getFormSettings['orientation']:'P';

            $size = !empty($getFormSettings['size'])?$getFormSettings['size']:'LETTER';

            $side = !empty($getFormSettings['side'])?$getFormSettings['side']:'front';

            $folder = !empty($getFormSettings['folder'])?$getFormSettings['folder']:'form138';

            $default_file = !empty($getFormSettings['default_file'])?$getFormSettings['default_file']:'grade_slip';

            $filename = !empty($getFormSettings['filename'])?$getFormSettings['filename']:$default_file;

			

			$session_details = $this->session_model->get( $session_id );

			$session_current = $session_details['session'];

            $centenary = substr($session_current, 0, 2); //2017-18 to 2017

            $date_today = date("Y-m-d"); 

            $data['orientation'] = $orientation;

            $data['size'] = $size;

            $data['side'] = $side;

            $data['folder'] = $folder;

            $data['default_file'] = $default_file;

            $data['filename'] = $filename;



            $teacher_advisory =  $this->classsection_model->get_teacher_advisory( $class_id,  $section_id, $session_id );

            $teacher_lastname = !empty($teacher_advisory->lastname )?$teacher_advisory->lastname:'';

            $teacher_name = !empty($teacher_advisory->name )?$teacher_advisory->name:'';

            $teacher_middlename = !empty($teacher_advisory->middlename )?$teacher_advisory->middlename:'';

            

            $data['t_fullname'] = $teacher_lastname.', '.$teacher_name.' '.$teacher_middlename; 

            $data['t_sex'] =  !empty($teacher_advisory->sex )?$teacher_advisory->sex:'';

            

            $setting_result = $this->setting_model->get();

            $principal_id = $setting_result[0]['principal_id'];

            $school_address = $setting_result[0]['address'];

            $session = $setting_result[0]['session'];

            $principal_result = $this->principal_model->get(  $principal_id  ); 

            $principal_result_lastname = !empty($principal_result['lastname'] )?$principal_result['lastname'] :'';

            $principal_result_name = !empty($principal_result['name'] )?$principal_result['name'] :'';

            $principal_result_middlename = !empty($principal_result['middlename'] )?$principal_result['middlename'] :'';

            $p_sex = !empty($principal_result['sex'] )?$principal_result['sex'] :'';

            $data['p_fullname'] = $principal_result_lastname.', '.$principal_result_name.' '.$principal_result_middlename;

            $data['p_sex'] = $p_sex; 

            

            // SETTINGS

            $data['student_session_id'] = $session_current;

           // $sch_settings = $this->setting_model->getCurrentSessiondata();

            $data['school_name'] =  $setting_result[0]['name'];

            $data['logo'] = $setting_result[0]['image'];

            $data['email'] =  $setting_result[0]['email'];

            $address_barangay_id =  $setting_result[0]['address_barangay_id'];

            $address_barangay =$this->address_model->get_barangay($address_barangay_id);

            $address_city_id =  $setting_result[0]['address_city_id'];

            $address_city =$this->address_model->get_city($address_city_id);

            $address_province_id =  $setting_result[0]['address_province_id'];

            $province_name =$this->address_model->get_province($address_province_id);

            $data['barangay'] = $address_barangay['name'];

            $data['city'] = $address_city['name'];

            $data['province'] = $province_name['name'];

            $data['address'] = $setting_result[0]['address'];

            $data['slogan'] = $setting_result[0]['slogan'];

            $data['phone'] = $setting_result[0]['phone']; 

            $data['date_created'] = date('F d, Y');

           

            

            $this->load->library('m_pdf');

            $this->mpdf = new mPDF(); 

            $this->mpdf = new mPDF('utf-8',array(210,160)); 

            $this->mpdf->SetDisplayMode('fullwidth'); 

            $this->mpdf->shrink_tables_to_fit=0;

           

            if( !empty( $student_id ) && count( $student_id ) > 1 ){



                for( $x=0; $x<count( $student_id );$x++){

                    $data['student_id'] = $student_id[$x] ;

                    $get_batch_grade_slip_html[] = $this->get_batch_grade_slip( $data ); 

                }  



                ob_start();

              



                if( isset($get_batch_grade_slip_html) && is_array($get_batch_grade_slip_html) && $get_batch_grade_slip_html ){

                    $counter = 1;

                    $begin = 1;

                    for ($htmli=0; $htmli < count( $get_batch_grade_slip_html ); $htmli++) {  

                         



                        if( $counter == 1 && $begin == 1 ){

                            ?>

                            <div class="row">

                                <div style="background:none; ">

                                    <div style="width: 100%; padding-left: 10px; padding-right: 10px;">

                                        <?php 

                                        if(isset($get_batch_grade_slip_html[$htmli]) &&  $get_batch_grade_slip_html[$htmli] ){ echo $get_batch_grade_slip_html[$htmli]; }

                                        ?>

                                    </div>

                                </div>  

                            <?php

                             

                        } elseif ($counter == 1 && $begin == FALSE ) {

                            ?>

                            <div class="row">

                                <div style="background:none; ">

                                    <div style="width: 100%; padding-left: 10px; padding-right: 10px;">

                                        <?php 

                                        if(isset($get_batch_grade_slip_html[$htmli]) &&  $get_batch_grade_slip_html[$htmli] ){ echo $get_batch_grade_slip_html[$htmli]; }

                                        ?>

                                    </div>

                                </div>  

                            <?php

                        } elseif ( $counter == 2 ) {

                            ?> 

                                <div style="background:none; ">

                                    <div style="width: 100%; padding-left: 10px; padding-right: 10px;">

                                        <?php 

                                        if(isset($get_batch_grade_slip_html[$htmli]) &&  $get_batch_grade_slip_html[0] ){ echo $get_batch_grade_slip_html[$htmli]; }

                                        ?>

                                    </div>

                                </div> 

                            </div>  

                            <?php

                        } elseif ( $counter == 3 ) {

                             ?>

                            <div class="row">

                                <div style="background:none; ">

                                    <div style="width: 100%; padding-left: 10px; padding-right: 10px;">

                                        <?php 

                                        if(isset($get_batch_grade_slip_html[$htmli]) &&  $get_batch_grade_slip_html[$htmli] ){ echo $get_batch_grade_slip_html[$htmli]; }

                                        ?>

                                    </div>

                                </div>  

                            <?php

                           

                        } elseif ( $counter == 4 ) {

                            ?> 

                                <div style="background:none; ">

                                    <div style="width: 100%; padding-left: 10px; padding-right: 10px;">

                                        <?php 

                                        if(isset($get_batch_grade_slip_html[$htmli]) &&  $get_batch_grade_slip_html[0] ){ echo $get_batch_grade_slip_html[$htmli]; }

                                        ?>

                                    </div>

                                </div> 

                            </div>  

                            <pagebreak />

                            <?php

                            

                        }



                        if( $counter == 4 ){

                            $counter = 1; 

                            $begin = 1; 

                        } else {  

                            $counter++;  

                            $begin = FALSE;

                        }

                        

                    }

                } 



 

				$get_batch_grade_slip_html = ob_get_contents();

				ob_clean(); 

                // $pdf_content['get_batch_grade_slip_html'] = $get_batch_grade_slip_html;*/

                $pdf_content['comments'] = $get_batch_grade_slip_html; 

                $this->m_pdf->pdf->AddPage($orientation,$size,'5','5','2','5','5','5','5','5'); 

                $html = $this->load->view('template/form138/grade_slip_batch',$pdf_content, true );  

                $this->m_pdf->pdf->WriteHTML($html);

                $pdfFilePath = "GradeSlip".$data['fullname'].".pdf";



                $this->m_pdf->pdf->Output($pdfFilePath, "I");

 

        

        

            } else {

                

                $this->m_pdf->pdf->AddPage($orientation,$size,'5','5','2','5','5','5','5','5');

                $data['student_id'] = $student_id;

                $html = $this->get_grade_slip( $data );

                $this->m_pdf->pdf->WriteHTML($html);

                $pdfFilePath = "GradeSlip".$data['fullname'].".pdf";



                $this->m_pdf->pdf->Output($pdfFilePath, "I");

            }

        } else {

            redirect('registrar/report/report_card');

        }

    }

    

    public function get_grade_slip( $data ){ 

        

        if( $data ){            

             $quarter = $data['quarter'];

             $semester_id = $data['semester_id'];

             $class_id = $data['class_id'];

             $section_id = $data['section_id'];

             $student_id = $data['student_id'];

             $filename = $data['filename'];

             $folder = $data['folder']; 

             $session_id = $data['session_id']; 



            $student_results = $this->student_model->getBySession( $student_id, $session_id ); 

            $student_firstname = $student_results['firstname'];

            $student_lastname = $student_results['lastname'];

            $student_middlename = $student_results['middlename'];

            $student_suffix = $student_results['suffix'];

    

            $student_dob = $student_results['dob']; 

            $dob = new DateTime( $student_dob );

            $today = new DateTime( $date_today ); 

            $difference = $dob->diff($today);

            $student_age = $difference->y; 

            $admission_no = $student_results['admission_no']; 

            $data['fullname'] = $student_lastname.', '.$student_firstname.' '.$student_middlename.' '.$student_suffix; 

            $data['dob'] = $student_dob; 

            $data['age'] = $student_age; 

            $data['lrn'] =  $student_results['lrn'];

            $data['gender'] = $student_results['gender'];

            $data['class'] = $student_results['class'];

            $data['section'] = $student_results['section'] == 'None' ?'':$student_results['section']; 

            $data['student_id'] = $student_id; 

            $data['admission_no'] = $student_results['admission_no']; 

            $data['strand_id'] = $student_results['strand_id']; 

            $check_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id, $session_id ); 

            if( $check_strand ){

                $data['strand_id'] = $check_strand['strand_id'];

            }  

            $strand_data = $this->strand_model->get( $data['strand_id'] );

            if( $strand_data  ){

                $data['section'] = $data['section']." (".$strand_data['code'].")"; 

            } 

			$session_current = $this->setting_model->get(); 	

			

			$registrar_id = $session_current[0]['registrar_id'];

			$registrar_details = $this->registrar_model->get( $registrar_id );

			$registrar_name = isset($registrar_details['name'])?$registrar_details['name']:'';

            $registrar_name_explode = explode( ' ',$registrar_name);

            if( count($registrar_name_explode) > 1 ){

                $registrar_name = '';

                foreach ($registrar_name_explode as $key1 => $value1) {

                   $registrar_name .= $value1[0];

                }

            } else {

                 $registrar_name = isset($registrar_details['name'][0])?$registrar_details['name'][0]:'';

            }

			$registrar_middlename = isset($registrar_details['middlename'][0])?$registrar_details['middlename'][0]:'';

			$registrar_lastname= isset($registrar_details['lastname'])?$registrar_details['lastname']:'';

			$data['prepared_by'] = $registrar_lastname.', '.$registrar_name.$registrar_middlename;

			

            $get_layout_grade = $this->get_layout_grade_slip( $data );

            $data = array_merge( $data, $get_layout_grade );

            $get_layout_conduct = $this->get_layout_conduct_grade_slip( $data ); 

            $data = array_merge( $data, $get_layout_conduct );

            $html = $this->load->view('template/form138/grade_slip',$data, true ); // dani rubin to naai problem..kay padulong cxa sa dili mao na template..



            return $html;

        } else {

            redirect('registrar/report/report_card');

        }

    }

    

    public function get_batch_grade_slip( $data ){

            

        if( $data ){            

             $quarter = $data['quarter'];

             $semester_id = $data['semester_id'];

             $class_id = $data['class_id'];

             $section_id = $data['section_id'];

             $student_id = $data['student_id'];

             $filename = $data['filename'];

             $folder = $data['folder'];

             $session_id = $data['session_id'];



            $student_results = $this->student_model->getBySession( $student_id, $session_id ); 

            $student_firstname = $student_results['firstname'];

            $student_lastname = $student_results['lastname'];

            $student_middlename = $student_results['middlename'];

            $student_suffix = $student_results['suffix'];

    

            // $student_dob = $student_results['dob']; 

            // $dob = new DateTime( $student_dob );

            // $today = new DateTime( $date_today ); 

            // $difference = $dob->diff($today);

            // $student_age = $difference->y; 

            $admission_no = $student_results['admission_no']; 

            $data['fullname'] = $student_lastname.', '.$student_firstname.' '.$student_middlename.' '.$student_suffix; 

            $data['dob'] = $student_dob; 

            $data['age'] = $student_age; 

            $data['lrn'] =  $student_results['lrn'];

            $data['gender'] = $student_results['gender'];

            $data['class'] = $student_results['class'];

            $data['section'] = $student_results['section'] == 'None' ?'':$student_results['section']; 

            $data['student_id'] = $student_id; 

            $data['admission_no'] = $student_results['admission_no']; 

            $data['strand_id'] = $student_results['strand_id']; 

            $check_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id, $session_id ); 

            if( $check_strand ){

                $data['strand_id'] = $check_strand['strand_id'];

            }  

            $strand_data = $this->strand_model->get( $data['strand_id'] );

            if( $strand_data  ){

                $data['section'] = $data['section']." (".$strand_data['code'].")"; 

            } 

			

			$session_current = $this->setting_model->get(); 	

			$registrar_id =  '2';

			$registrar_details = $this->registrar_model->get( $registrar_id ); 

			$registrar_name = isset($registrar_details['name'])?$registrar_details['name']:'';

            $registrar_name_explode = explode( ' ',$registrar_name);

            if( count($registrar_name_explode) > 1 ){

                $registrar_name = '';

                foreach ($registrar_name_explode as $key1 => $value1) {

                   $registrar_name .= $value1[0];

                }

            } else {

                 $registrar_name = isset($registrar_details['name'])?$registrar_details['name']:'';

            }

			$registrar_middlename= isset($registrar_details['middlename'])?$registrar_details['middlename']:'';

			$registrar_lastname= isset($registrar_details['lastname'])?$registrar_details['lastname']:'';

			$data['prepared_by'] = $registrar_lastname.', '.$registrar_name.' '.$registrar_middlename;

            $get_layout_grade = $this->get_layout_grade_slip( $data );

            $data = array_merge( $data, $get_layout_grade );

            $get_layout_conduct_grade_slip = $this->get_layout_conduct_grade_slip( $data );

            $data = array_merge( $data, $get_layout_conduct_grade_slip );

            $generated_batch_slip_data =  $this->generate_batch_slip_data( $data ); 

            

           // $html = $this->load->view('template/form138/grade_slip_batch',$data, true ); // dani rubin to naai problem..kay padulong cxa sa dili mao na template..

            

            return $generated_batch_slip_data;

        } else {

            redirect('registrar/report/report_card');

        }

    }



    function generate_batch_slip_data( $data ){

        $session_id = $data['session_id'];

        // if( $session_id == '14'){

        $prepared_by = 'Dulana, Darlene Caspe';

        // }

        $prepared_by = $data['prepared_by'];

        $session_id =  $data['session_id'];

		$gradingsettings = $this->gradingsetting_model->getbySession( $session_id );

        $display_conduct = $gradingsettings->display_conduct;

         ob_start();

            ?>

            <table width="100%" style="">

                <tr>

					<td style="text-align: left;">

						<img src="<?php echo base_url() ?>uploads/school_content/logo/<?php echo $data['logo']; ?>"/ height="50" width="50">

					</td>

                    <td style="text-align: center;">

                        <h4><strong><?php echo $data['school_name']; ?></strong></h4> 

                        <h5><?php echo $data['address']; ?></h5>                         

                        <h5><?php echo $data['slogan']; ?></h5>

                    </td>

                </tr>

                <tr>

                   <td colspan="2" style="text-align: center;">

                        <h6><strong>GRADE SLIP</strong></h6>

                    </td>

                </tr>

                <tr>

                    <td colspan="2"  style="text-align: center;">

                        <h6><strong>SY <?php echo $data['school_year'] .' '. $data['student_session_id']; ?></strong></h6>

                    </td>           

                </tr>

				<?php 

				if( !empty( $data['semester_id'] ) ){

					if( $data['semester_id'] == 1 ){

						?>

						<tr>

							<td colspan="2"  style="text-align: center;">

								<h6><strong>FIRST SEMESTER</strong></h6>

							</td>           

						</tr>

						<?php

					} elseif($data['semester_id'] == 2) {

						?>

						<tr>

							<td colspan="2"  style="text-align: center;">

								<h6><strong>SECOND SEMESTER</strong></h6>

							</td>           

						</tr>

						<?php

					}

					?>

					

					<?php 

				}

				?>

            </table>

            <table width="100%" style="border-collapse: collapse;" border="1"> 

                <tr>

                    <td style="font-size: 11px;text-transform:uppercase;"><strong> Name: </strong><?php echo $data['fullname']; ?></td>

                </tr>

                <tr>

                    <td style="font-size: 11px;"><strong> Grade/Section: </strong><?php echo $data['class'].'-'.$data['section']; ?></td>

                </tr> 

            </table>

            <table width="100%" border="1" style="border-collapse: collapse;" border="1"> 

					<?php echo $data['get_grades']; ?>

                <?php 

                if( $display_conduct == 'yes'){

                    ?>

			     	 <tr>

                     <?php echo $data['conduct']; ?>

				    </tr>

                    <?php 

                }

                ?>

                <tr>

                    <td colspan="6">&nbsp;</td> 

                </tr>

            </table>



            <table width="100%" border="1" style="border-collapse: collapse;" border="1"> 



                <tr> 

                    <td width="45%" style="border-right: 0;" align="center"><span style="font-size: 11px;"> <?php echo $data['t_fullname']; ?></span> </td> 

                    <td width="5%" style="border-left:0;border-right:0;"></td>

                    <td width="45%" style="border-left: 0;" align="center"> <span style="font-size: 11px; "><?php echo $prepared_by;?></span></td> 

                </tr>

                <tr> 

                    <td  style="border-right: 0;border-top:0;" align="center"><span style="font-size: 11px;">Adviser</span> </td> 

                    <td width="5%" style="border-top:0;border-left:0;border-right:0;"></td>

                    <td  style="border-left: 0;border-top:0;" align="center"> <span style="font-size: 11px; ">Registrar</span></td> 

                </tr>

            </table>

            <?php

            $content = ob_get_contents();

            ob_end_clean(); 

          

        return $content;

    }

	

	public function get_layout_grade_slip( $data ){

		$student_id = $data['student_id'];

		$class_id = $data['class_id'];

		$quarter = $data['quarter'];

		$semester = $data['semester_id'];

		$strand_id = $data['strand_id'];

		$session_id = $data['session_id'];

		$check_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester, $session_id );

		if( $check_strand ){

			$strand_id = $check_strand['strand_id'];

		} 

		ob_start(); 

			//$list_of_subjects = $this->examresult_model->get_student_exam_result_by_subject( $student_id );

			//$get_master_sheet = $this->grade_model->get_master_sheet( $list_of_subjects, $student_id );

			

			$first = 0;

			$second = 0;

			$third = 0;

			$fourth = 0;

			$count_subjects_first = 0;

			$count_subjects_second = 0;

			$count_subjects_third = 0;

			$count_subjects_fourth = 0;

			

			$last_quarter = 3;

			$get_ave = 0;

			$get_count = 0;

			$final_grade = 0;

			$get_final_grade_result = 0;

			$get_final_remarks = '';

			$show_final_grade = false;

            $admssion = true;

			$combine_category = 'ga';

			$gradingsettings = $this->gradingsetting_model->getBySession( $session_id );

			$decimal_grades = !empty($gradingsettings->decimal_grades )?$gradingsettings->decimal_grades:'0';	

			$decimal_average = !empty($gradingsettings->decimal_average )?$gradingsettings->decimal_average:'0';	

			$decimal_finalgrade = !empty($gradingsettings->decimal_finalgrade )?$gradingsettings->decimal_finalgrade:'0';

			$if_gradeisnull = !empty($gradingsettings->if_gradeisnull )?$gradingsettings->if_gradeisnull:'blank';

			$allow_to_pass = isset($gradingsettings->allow_to_pass)?$gradingsettings->allow_to_pass:'no';

			if( $semester ){

				$gradingsettings = $this->gradingsetting_model->getbySession( $session_id );

				if( $semester == 1 ){

					$get_quarter_settings = !empty($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array('1','2','3','4');	

					$get_quarter_settings = is_serialized( $get_quarter_settings )?unserialize($get_quarter_settings):$get_quarter_settings;

					$total_quarter = count( $get_quarter_settings );

				} else {

					$get_quarter_settings = !empty($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array('1','2','3','4');	

					$get_quarter_settings = is_serialized( $get_quarter_settings )?unserialize($get_quarter_settings):$get_quarter_settings;

					$total_quarter = count( $get_quarter_settings );

				}

				$list_of_subjects = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester, $session_id );

				$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester, $session_id );

				if( $custom_subject ){

					$list_of_subjects = array_merge( $list_of_subjects, $custom_subject );

				}

				$list_of_subjects = $this->customsubject_model->unsetsubjectdrop( $list_of_subjects, $student_id, $semester, $session_id );

				$data['get_quarter'] = $total_quarter;

				?>

				<tr>

				<td align="center" style="font-size: 11px;">SUBJECTS</td>

				<?php

				for( $y=0; $y<$total_quarter;$y++ ){

					$get_x = $get_quarter_settings[$y];

					if( $get_x == 1 ){

						?>

						<td align="center" style="font-size: 11px;padding:2px;">1</td>

						<?php

					} elseif( $get_x == 2 ){

						?>

						<td align="center" style="font-size: 11px;padding:2px;">2</td>

						<?php

					} elseif( $get_x == 3 ){

						?>

						<td align="center" style="font-size: 11px;padding:2px;">3</td>

						<?php

					} elseif( $get_x == 4 ){ 

						?>

						<td align="center" style="font-size: 11px;padding:2px;">4</td>

						<?php 

					}

				}

				?>

				<td align="center" style="font-size: 11px;">FR</td>

				<td align="center" style="font-size: 11px;"><small>Remark</small></td>

				</tr>

				<?php

				if( $quarter == 'final'){

					foreach( $list_of_subjects as $listsubject => $subjects ){

						$subject_id = $subjects['subject_id'];

						$subject_kind = $subjects['kind'];

						$get_final_grade = 0;

						$complete_grade = true;

						$subject_manager = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );

						$display_card = $subject_manager['display_card'];

						$align_right = $subject_manager['align_right'];

                         $subject_name = $subjects['name'];

                        if( $semester == 1 ){

                            if( $subject_name == 'Values Education 11'){

                                $subject_name = 'Values Education 11 (Life and Teachings of Jesus)';

                            } elseif( $subject_name == 'Values Education 12' ){

                                $subject_name = 'Values Education 12 (Daniel & Revelation)';

                            }

                        } elseif( $semester == 2 ){

                            if( $subject_name == 'Values Education 11'){

                                $subject_name = 'Values Education 11 (Christian Beliefs)';



                            } elseif( $subject_name == 'Values Education 12' ){

                                $subject_name = 'Values Education 12 (Christian Ethics)';

                            }

                        }

						if( $display_card == 'yes'){

							?>

							<tr>

							<?php 

							if( $align_right == 'yes' ){

								?>

								<td style="padding-left:10px; font-size: 11px;"><?php echo $subject_name;?></td>

								<?php 

							} else {

								?>

								<td style="padding-left:5px; font-size: 11px;"><?php echo $subject_name;?></td>

								<?php

							}

						}

						for($y=0;$y<$total_quarter;$y++){

							$x = $get_quarter_settings[$y];

							//$getgradeperquarter = $this->grade_model->getgradeperquarter( $student_id, $subject_id, $x, $semester );

							$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $x, $combine_category, $session_id  );

							if( $grade_per_quarter == null ){

								$complete_grade = false;

							} 

							if($x==1){ 

								$first = $first + $grade_per_quarter;

							} elseif($x==2){ 

								$second = $second + $grade_per_quarter; 

							} elseif($x==3){ 

								$third = $third + $grade_per_quarter; 

							} elseif($x==4){ 

								$fourth = $fourth + $grade_per_quarter; 

							}

							

							if( $display_card == 'yes'){

								if( $grade_per_quarter == null || $grade_per_quarter == 0 ){

									if( $if_gradeisnull == 'blank'){

										?>

										<td></td>

										<?php

									}  else {

										?>

										<td align="center" style="font-size: 11px;padding:2px;"><small><?php echo number_format((float)0, $decimal_grades, '.', '');?></small></td>

										<?php

									} 

								} else {

									?>

									<td align="center" style="font-size: 11px;padding:2px;"><small><?php echo $grade_per_quarter;?></small></td>

									<?php

								}

							}	

							$get_final_grade = $get_final_grade + $grade_per_quarter;

						

						} 

						//if( $x == $total_quarter ){

							$get_final_grade_result = $get_final_grade / $total_quarter;

							if( $include_computation == 'yes' ){ 

								$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id  );

								if( empty( $checkifChild ) ){

									if( $allow_to_pass == 'yes'){

										if( $get_final_grade_result == '74.5'){

											$get_final_grade_result = '75';

										} elseif(  $get_final_grade_result > '74.4' && $get_final_grade_result <  75 ){

											$get_final_grade_result = '75';

										}

									}

								}

							}

							$get_final_grade_result =  number_format((float)$get_final_grade_result, $decimal_finalgrade, '.', '');

							

							$get_final_remarks = $get_final_grade_result < 74.4 ?"Failed":"Passed";

							if( $get_final_remarks != 'Passed' ){ 

								$admssion = false;

							}

							$show_final_grade = true;

							

							

						//}

						if( $display_card == 'yes'){

							if( $complete_grade ){

								?>

								<td align="center" style="font-size: 11px;padding:2px;"><small><?php echo $get_final_grade_result;?></small></td>

								<td align="center" style="font-size: 11px;padding:2px;"><small><?php echo $get_final_remarks;?></small></td>

								</tr>

								<?php

							} else {

								?>

								<td></td>

								<td></td>

								</tr>

								<?php

							}

						}

						

						if( $include_computation == 'yes' ){ 

							$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id,  $combine_category, $session_id );

							if( empty($checkifChild)){

								$final_grade = $get_final_grade_result * $units;

								$total_ave = $total_ave + $final_grade;

								$get_count = $get_count + $units;

							}

						}

					}

				}	else {

					foreach( $list_of_subjects as $listsubject => $subjects ){

						$subject_id = $subjects['subject_id'];

						$subject_kind = $subjects['kind'];

						$subject_manager = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );

						$display_card = $subject_manager['display_card'];

						$align_right = $subject_manager['align_right'];

						$include_computation = $subject_manager['include_computation'];

                         $subject_name = $subjects['name'];

                        if( $semester == 1 ){

                            if( $subject_name == 'Values Education 11'){

                                $subject_name = 'Values Education 11 (Life and Teachings of Jesus)';

                            } elseif( $subject_name == 'Values Education 12' ){

                                $subject_name = 'Values Education 12 (Daniel & Revelation)';

                            }

                        } elseif( $semester == 2 ){

                            if( $subject_name == 'Values Education 11'){

                                $subject_name = 'Values Education 11 (Christian Beliefs)';



                            } elseif( $subject_name == 'Values Education 12' ){

                                $subject_name = 'Values Education 12 (Christian Ethics)';

                            }

                        }

						if( $display_card == 'yes'){

							?>

							<tr>

							<?php 

							if( $align_right == 'yes' ){

								?>

								<td style="padding-left:10px; font-size: 11px;padding:2px;"><?php echo $subject_name;?></td>

								<?php 

							} else {

								?>

								<td style="padding-left:5px; font-size: 11px;padding:2px;"><?php echo $subject_name;?></td>

								<?php

							}

						}

						for($y=0;$y<$total_quarter;$y++){

							$x = $get_quarter_settings[$y];

							if( $x <= $quarter ){

								//$getgradeperquarter = $this->grade_model->getgradeperquarter( $student_id, $subject_id, $x, $semester );

								$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $x, $combine_category, $session_id  );

								if($x==1){ 

									$first = $first + $grade_per_quarter;

								} elseif($x==2){ 

									$second = $second + $grade_per_quarter; 

								} elseif($x==3){ 

									$third = $third + $grade_per_quarter; 

								} elseif($x==4){ 

									$fourth = $fourth + $grade_per_quarter; 

								}

								if( $display_card == 'yes'){

									if( $grade_per_quarter == null || $grade_per_quarter == 0 ){

										if( $if_gradeisnull == 'blank'){

										?>

										<td></td>

										<?php

										}  else {

										?>

										<td align="center" style="font-size: 11px;padding:2px;"><small><td><?php echo number_format((float)0, $decimal_grades, '.', '');?></small></td>

										<?php

										} 

									} else {

										?>

										<td align="center" style="font-size: 11px;padding:2px;"><small><?php echo $grade_per_quarter;?></small></td>

										<?php

									}

								}	

									

							} else {

								if( $display_card == 'yes' ){

									?>

									<td></td>

									<?php

								}

							}

						} 

						if( $display_card == 'yes' ){

							?>

							<td></td>

							<td></td>

							</tr>

							<?php

						}

					}

				}

				$data['set_quarter'] = $get_quarter_settings; 

			} else {

				$data['get_quarter'] = $last_quarter;

				$list_of_subjects = $this->subject_model->getSubjctByClass( $class_id );

				?>

				<tr>

					<td align="center" style="font-size: 11px;">SUBJECTS</td>

					<td align="center" style="font-size: 11px;padding:2px;">1</td>

					<td align="center" style="font-size: 11px;padding:2px;">2</td>

					<td align="center" style="font-size: 11px;padding:2px;">3</td>

					<td align="center" style="font-size: 11px;padding:2px;">FR</td>

					<td align="center" style="font-size: 11px;padding:2px;"><small>Remark</small></td>

				</tr>

				<?php

				if( $quarter == 'final' ){

					foreach( $list_of_subjects as $listsubject => $subjects ){

						$subject_id = $subjects['id'];

						$get_final_grade = 0;

						$complete_grade = true;

						$subject_manager = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );

						$display_card = $subject_manager['display_card'];

						$align_right = $subject_manager['align_right'];

						if( $display_card == 'yes'){

							?>

							<tr>

							<?php 

							if( $align_right == 'yes' ){

								?>

								<td style="padding-left:10px; font-size: 11px;"><?php echo $subjects['name'];?></td>

								<?php 

							} else {

								?>

								<td style="padding-left:5px; font-size: 11px;"><?php echo $subjects['name'];?></td>

								<?php

							}?>

							<?php 

						}

						for($x=1;$x<=$last_quarter;$x++){

							if( $x ){

								//$getgradeperquarter = $this->grade_model->getgradeperquarter( $student_id, $subject_id, $x );

								$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $x, $combine_category, $session_id );

								if( $grade_per_quarter == null ){

									$complete_grade = false;

								} 

								if($x==1){ 

									$first = $first + $grade_per_quarter;

								} elseif($x==2){ 

									$second = $second + $grade_per_quarter; 

								} elseif($x==3){ 

									$third = $third + $grade_per_quarter; 

								} elseif($x==4){ 

									$fourth = $fourth + $grade_per_quarter; 

								}	

								if( $display_card == 'yes'){

									if( $grade_per_quarter == null || $grade_per_quarter == 0  ){

										if( $if_gradeisnull == 'blank'){

											?>

											<td></td>

											<?php

										}  else {

											?>

											<td align="center" style="font-size: 11px;padding:2px;"><?php echo number_format(0, $decimal_grades, '.', '');?></td>

											<?php

										}

									} else {

										?>

										<td align="center" style="font-size: 11px;padding:2px;"><small><?php echo $grade_per_quarter;?></small></td>

										<?php

									}

								}

								$get_final_grade = $get_final_grade + $grade_per_quarter;

							} 

							if( $x == $last_quarter ){

								$get_final_grade_result = $get_final_grade / $last_quarter;

								if( $include_computation == 'yes' ){ 

									$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id,  $combine_category, $session_id  );

									if( empty( $checkifChild ) ){

										if( $allow_to_pass == 'yes'){

											if( $get_final_grade_result == '74.5'){

												$get_final_grade_result = '75';

											} elseif(  $get_final_grade_result > '74.4' && $get_final_grade_result <  75 ){

												$get_final_grade_result = '75';

											}

										}

									}

								}

								$get_final_grade_result =  number_format((float)$get_final_grade_result, $decimal_finalgrade, '.', '');

								$get_final_remarks = $get_final_grade_result < 74.4 ?"Failed":"Passed";

								if( $get_final_remarks != 'Passed' ){ 

									$admssion = false;

								}

								$show_final_grade = true;

								

							}

						} 

						if( $display_card == 'yes' ){

							if( $complete_grade ){

								?>

								<td align="center" style="font-size: 11px;padding:2px;"><small><?php echo $get_final_grade_result;?></small></td>	

								<td align="center" style="font-size: 11px;padding:2px;"><small><?php echo $get_final_remarks;?></small></td>				

								</tr>

								<?php

							} else {

								?>

								<td></td>

								<td></td>

								</tr>

								<?php

							}

						}

						if( $include_computation == 'yes' ){ 

							$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id ,  $combine_category, $session_id );

							if( empty($checkifChild)){

								$get_final_grade_result = $get_final_grade_result * $units;

								$get_ave = $get_ave +  $get_final_grade_result;

								$get_count = $get_count + $units; 

							}

						}

					}

				} else {

					foreach( $list_of_subjects as $listsubject => $subjects ){

						$subject_id = $subjects['id'];

						$subject_manager = $this->subjectmanager_model->getBySubjectSession( $subject_id , $session_id );

						$display_card = $subject_manager['display_card'];

						$align_right = $subject_manager['align_right'];

						if( $display_card == 'yes'){

							?>

							<tr>

							<?php 

							if( $align_right == 'yes' ){

								?>

								<td style="padding-left:10px; font-size: 11px;"><?php echo $subjects['name'];?></td>

								<?php 

							} else {

								?>

								<td style="padding-left:5px;font-size: 11px;"><?php echo $subjects['name'];?></td>

								<?php

							}

						}

						for($x=1;$x<=$last_quarter;$x++){

							if( $x <= $quarter ){

								$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $x, $combine_category, $session_id );

								if($x==1){ 

									$first = $first + $grade_per_quarter;

								} elseif($x==2){ 

									$second = $second + $grade_per_quarter; 

								} elseif($x==3){ 

									$third = $third + $grade_per_quarter; 

								} elseif($x==4){ 

									$fourth = $fourth + $grade_per_quarter; 

								}

								if( $display_card == 'yes'){

									if( $grade_per_quarter == null || $grade_per_quarter == 0  ){

										if( $if_gradeisnull == 'blank'){

											?>

											<td></td>

											<?php

										}  else {

											?>

											<td align="center" style="font-size: 11px;padding:2px;"><?php echo number_format(0, $decimal_grades, '.', '');?></td>

											<?php

										}

									} else {

										?>

										<td align="center" style="font-size: 11px;padding:2px;"><small><?php echo $grade_per_quarter;?></small></td>

										<?php

									}

								}								

							} else {

								if( $display_card == 'yes'){

								?>

								<td></td>

								<?php

								}

							}

						} 

						if( $display_card == 'yes' ){

							?>

							<td></td>

							<td></td>

							</tr>

							<?php

						}

					}

				}

				$get_quarter_settings = array('1','2','3');	

				$get_quarter_settings = is_serialized( $get_quarter_settings )?unserialize($get_quarter_settings):$get_quarter_settings;

				$data['set_quarter'] = $get_quarter_settings;

			}



		$get_grades = ob_get_contents();

		ob_clean( ); 



		$get_ave = $get_count != 0 ?$get_ave / $get_count:'';

		if( $get_ave  ){

			$get_ave = number_format($get_ave, $decimal_average,".","");

		} 

		$get_ave = $show_final_grade == true ? $get_ave:'';

		$data['get_grades'] = $get_grades;   

		$data['gen_ave'] = $get_ave;



         if( $admssion == true && $quarter == 4 ){

         	$next_class_details = $this->class_model->getnextClassID( $class_id );

			$data['admssion'] =  $next_class_details['class'];

        } else {

            $data['admssion'] = " ";

        }

		

		return $data;

	}

	

	public function get_layout_conduct_grade_slip( $data ){

		$class_id = $data['class_id'];

		$section_id = $data['section_id'];

		$student_id = $data['student_id'];

		$quarter = $data['quarter'];

		$session_id = $data['session_id'];

		$getConductSubject = $this->conductsession_model->getDetailByclassAndSection( $class_id, $section_id, $session_id );

		$data['getConductSubject'] = $getConductSubject;

		$set_quarter = $data['set_quarter'];

        $strand_id = $data['strand_id'];

        $semester_id = $data['semester_id'];

        $enableStrand = $this->strand_model->checkEnable( $class_id );

        $gradingsettings = $this->gradingsetting_model->getbySession( $session_id );

        if( $semester_id ){

            if($semester_id  == '1'){

                $get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array(); 

            } else {

                $get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array(); 

          

            }

              $get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;

        } else {

             $get_quarter = array(1,2,3);

        }

        

        $total_quarter = count( $get_quarter );

		ob_start(); 

		?>		

				 <td style="padding-left:5px;font-size:11px;">Conduct</td>

                <?php 

                    if( $quarter != 'final'){

                              for( $qtr=0;$qtr<$total_quarter;$qtr++ ) {

                                 $x = $get_quarter[$qtr];

                                if( $enableStrand ){

                                    if( $x==1 || $x==2 ){

                                         $semester_id = 1;

                                         $get_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id, $session_id );

                                        if( $get_strand ){

                                            $get_strand_per_sem = $get_strand['strand_id'];

                                        } else {

                                            $get_strand_per_sem = $strand_id; 

                                        }

                                    } elseif( $x==3 || $x==4 ){

                                        $semester_id = 2;

                                        $get_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id, $session_id );

                                        if( $get_strand ){

                                            $get_strand_per_sem = $get_strand['strand_id'];

                                        } else {

                                            $get_strand_per_sem = $strand_id; 

                                        }

                                    }

                                }

                               

                             

                               if( $semester_id ){

                                    $student_conduct = $this->conductimportsubject_model->getStudentConduct( $student_id, $x, $student['class_id']  , $semester_id, $get_strand_per_sem, $session_id  );

                                } else {

                                    $student_conduct = $this->conductimportsubject_model->getStudentConduct( $student_id, $x, $class_id, null,null, $session_id );

                                } 



                                if( $x == $quarter && $student_conduct ){

                                    ?>

                                    <td align="center" style="font-size: 11px;padding:2px;"><small><?php echo round($student_conduct) ?></small></td>

                                  <?php

                                } else {

                                    ?>

                                    <td></td>

                                    <?php

                                }

                                

                            }

                            ?>

                            <td></td>

                           <td></td>

                        <?php

                    } else {

                        $complete_conduct = true;

                        $count_conduct_number = 0;

                         for( $qtr=0;$qtr<$total_quarter;$qtr++ ) {

                                 $x = $get_quarter[$qtr];

                                if( $enableStrand ){

                                    if( $x==1 || $x==2 ){

                                         $semester_id = 1;

                                         $get_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id, $session_id );

                                        if( $get_strand ){

                                            $get_strand_per_sem = $get_strand['strand_id'];

                                        } else {

                                            $get_strand_per_sem = $strand_id; 

                                        }

                                    } elseif( $x==3 || $x==4 ){

                                        $semester_id = 2;

                                        $get_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id, $session_id );

                                        if( $get_strand ){

                                            $get_strand_per_sem = $get_strand['strand_id'];

                                        } else {

                                            $get_strand_per_sem = $strand_id; 

                                        }

                                    }

                                }

                               

                             

                               if( $semester_id ){

                                    $count_conduct_number++;

                                    $student_conduct = $this->conductimportsubject_model->getStudentConduct( $student_id, $x, $student['class_id']  , $semester_id, $get_strand_per_sem, $session_id  );

                                } else {

                                    $count_conduct_number++;

                                    $student_conduct = $this->conductimportsubject_model->getStudentConduct( $student_id, $x, $class_id, null,null, $session_id );

                                } 



                                if( $student_conduct ){

                                     $total_conduct_number = $student_conduct + $total_conduct_number;

                                    ?>

                                    <td align="center" style="font-size: 11px;padding:2px;"><small><?php echo round($student_conduct) ?></small></td>

                                  <?php

                                } else {

                                    $complete_conduct = false;

                                    ?>

                                    <td></td>

                                    <?php

                                }

                                

                            }

                             if( $total_conduct_number > 0 ){

                                $conduct_average = $total_conduct_number / $count_conduct_number;

                                $conduct_average = number_format( $conduct_average,'0','.','');

                                   

                             }

                             $remarks = $conduct_average >= 75?'Passed':'Failed';

                             if( $complete_conduct ){

                                ?>

                                 <td align="center" style="font-size: 11px;padding:2px;"><small><?php echo $conduct_average;?></small></td>

                                  <td align="center" style="font-size: 11px;padding:2px;"><small><?php  echo $remarks; ?></small></td>

                                

                                <?php

                             } else {

                                ?>

                                <td align="center" style="font-size: 11px;padding:2px;"></td>

                                 <td align="center" style="font-size: 11px;padding:2px;"></td>

                               <?php

                             }

                    }

                    

                    ?>

                   

                    
		?>

			

		<?php

		$conduct_contents = ob_get_contents();

		$data['conduct'] = $conduct_contents; 

		ob_clean( ); 



		return $data;

	}



    function failing_grades_summary(){

        $this->session->set_userdata('top_menu', 'Reports');

        $this->session->set_userdata('sub_menu', 'report/failing_grades_summary');

        

        $student_results = $this->student_model->get();

        $data['studentList'] = $student_results;      

        $class = $this->class_model->get();  

        $data['classlist'] = $class;

        $data['getquarter'] = $this->customlib->getQuarter();

        $data['class_id'] = "";

        $data['section_id'] = "";

        $data['subject_id'] = "";

        $data['quarter'] = "";

        $data['semester_id'] = "";

        $data['getSemester'] = $this->customlib->getSemester();

        $data['enableStrand'] = false;

		$session = $this->session_model->getAllSession();

		$setting_result = $this->setting_model->get();

		$data['session_id'] = $setting_result[0]['session_id'];

		$data['current_session'] = $this->setting_model->getCurrentSessionName();

		$data['sessionlist'] = $session;

        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');

        $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean'); 

        // subject validation removed

        if ($this->form_validation->run() == FALSE) {

            $this->load->view('layout/registrar/header', $data);

            $this->load->view('registrar/reports/failing_grades_summary', $data);

            $this->load->view('layout/registrar/footer', $data);

        } else {

            $resultlist = array();

            $class = $this->input->post('class_id');

            $section = $this->input->post('section_id');

            $subject = $this->input->post('subject_id');

            $month = $this->input->post('month');

            $quarter = $this->input->post('quarter');  

            $action = $this->input->post('action');

            $semester_id = $this->input->post('semester_id');

            $session_id = $this->input->post('session_id');

          

            $data['session_id'] = $session_id;

            $data['class_id'] = $class;

            $data['section_id'] = $section;

            $data['subject_id'] = $subject;

            $data['month_selected'] = $month;

            $data['quarter'] = $quarter; 

            $data['semester_id'] = $semester_id; 

            $data['enableStrand'] = $this->strand_model->checkEnable( $class );

            $subjectlist = $this->subject_model->getSubjctByClass( $class, true ); 

    

            

            $studentlist_male = $this->student_model->searchByClassSectionGender($class, $section, 'Male',$session_id);

			$studentlist_female = $this->student_model->searchByClassSectionGender($class, $section, 'Female',$session_id); 

            $data['subjectlist'] = $subjectlist;

            $data['studentlist_male'] = $studentlist_male;

            $data['studentlist_female'] = $studentlist_female;

 

            $this->load->view('layout/registrar/header', $data);

            $this->load->view('registrar/reports/failing_grades_summary', $data);

            $this->load->view('layout/registrar/footer', $data);



            if( isset($action) && $action == 'print_failing_grades_summary'){  

               $this->print_failing_grades_summary( $class, $section, $subject, $semester_id, $session_id );

            }

        }  

    

    } 



	 
    public function print_failing_grades_summary( $class_id, $section_id, $subject_id, $semester_id=NULL, $session_id ){  

       if(empty($session_id)){

			$setting_result = $this->setting_model->get();

			$session_id = $setting_result[0]['session_id'];

		}

		

		

		$session_details = $this->session_model->get( $session_id );

       	$session_current = $session_details['session'];

		

        $centenary = substr($session_current, 0, 2); //2017-18 to 2017

        // SETTINGS

        $data['student_session_id'] = $session_current;

        $setting_result = $this->setting_model->get();

        $data['school_name'] =  $setting_result[0]['name'];

        $data['logo'] = $setting_result[0]['image'];

        $data['email'] =  $setting_result[0]['email'];

        $address_barangay_id =  $setting_result[0]['address_barangay_id'];

        $address_barangay =$this->address_model->get_barangay($address_barangay_id);

        $address_city_id =  $setting_result[0]['address_city_id'];

        $address_city =$this->address_model->get_city($address_city_id);

        $address_province_id =  $setting_result[0]['address_province_id'];

        $province_name =$this->address_model->get_province($address_province_id);

        $data['barangay'] = $address_barangay['name'];

        $data['city'] = $address_city['name'];

        $data['province'] = $province_name['name'];

        $data['address'] = $setting_result[0]['address'];

        $data['phone'] = $setting_result[0]['phone'];

        $school_year = "S. Y. ".$session_current;

            

        $class_array = $this->class_model->get($class_id);

        $subject_array = $this->subject_model->get($subject_id);

        $class_name = $class_array['class'];

        $subject_name = $subject_array['name'];

        $section_array = $this->section_model->get($section_id);

        $section_name = $section_array['section'];

		

		$principal_id = $setting_result[0]['principal_id'];

		$principal_details = $this->principal_model->get(  $principal_id );

		$principal_sex = isset($principal_details['sex'])?$principal_details['sex']:'Male';

        if(  $principal_sex == 'Female'){

            $principal_sex_display = "Ms.";

        } else {

            $principal_sex_display =  "Mr.";

        }

		$principal_firstname = isset($principal_details['name'])?$principal_details['name']:'';

		$principal_midname = isset($principal_details['middlename'])?$principal_details['middlename']:'';

		$principal_lastname = isset($principal_details['lastname'])?$principal_details['lastname']:'';

        $principal_fullname = $this->setting_model->getprincipalformat( '', $principal_firstname, $principal_midname, $principal_lastname );

		

		$gradingsettings = $this->gradingsetting_model->getbySession( $session_id );

		$decimal_grades  = isset($gradingsettings->decimal_grades)?$gradingsettings->decimal_grades:0;

		$decimal_average = isset($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;

		$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:2;

		$if_gradeisnull = isset($gradingsettings->if_gradeisnull)?$gradingsettings->if_gradeisnull:'blank';

		$combine_category = 'ga';

        $subjectlist = $this->subject_model->getSubjctByClass( $class_id ); 

        // $studentlist = $this->student_model->searchByClassSection($class_id, $section_id);

        $studentlistMale = $this->student_model->getstudentsByClassSectionGender($class_id, $section_id, 'Male', $session_id ); 

        $studentlistFemale = $this->student_model->getstudentsByClassSectionGender($class_id, $section_id, 'Female', $session_id );

	

		//$teacher_details = $this->teachersubject_model->getteacherbyClsandSectionSubject($class_id, $section_id, $subject_id);

        

        $enableStrand = $this->strand_model->checkEnable( $class_id );



        $names_male = array(); 

        foreach ($studentlistMale as $userMale) {

            $names_male[] =  $userMale['lastname'];

        }  

        array_multisort($names_male, SORT_ASC, $studentlistMale); 



        $names_female = array(); 

        foreach ($studentlistFemale as $userFemale) {

            $names_female[] =  $userFemale['lastname'];

        }  

        array_multisort($names_female, SORT_ASC, $studentlistFemale); 

           



        //$data['school_name'] = $school_name;

        //$data['school_address'] = $school_address;

        $data['school_year'] = $school_year;

        $data['class_name'] = $class_name;

        $data['section_name'] = $section_name == 'None'?'':$section_name;  

        $data['subject_name'] = $subject_name;  

		$get_teacher_advisory = $this->classsection_model->get_teacher_advisory($class_id, $section_id, $session_id );

        $teachers_advisory_name = !empty($get_teacher_advisory->name)?$get_teacher_advisory->name:'';

		$teacher_advisory_midname = !empty($get_teacher_advisory->middlename)?$get_teacher_advisory->middlename[0].'.':' ';

		$teacher_advisory_lastname = !empty($get_teacher_advisory->lastname)?$get_teacher_advisory->lastname:'';

		

		$teacher_details = $this->teachersubject_model->getteacherbyClsandSectionSubject($class_id, $section_id, $subject_id, $session_id );

		$teacher_lastname =  !empty($teacher_details['teacher_lastname'])?$teacher_details['teacher_lastname']:'';

		$teacher_firstname =  !empty($teacher_details['teacher_name'])?$teacher_details['teacher_name']:'';

		$teacher_middlename = !empty( $teacher_details['teacher_midname'])?$teacher_details['teacher_midname'][0].'.':' ';

		if(empty( $teacher_details )){

			if( !empty( $get_teacher_advisory )){

				$data['teacher_name'] = $teacher_advisory_lastname.', '.$teachers_advisory_name.' '.$teacher_advisory_midname;

			} else {

				$data['teacher_name'] = '';

			}

		} else {

			$data['teacher_name'] = $teacher_lastname.', '.$teacher_firstname.' '.$teacher_middlename;

		}

		$get_teacher_advisory_gender = isset($get_teacher_advisory->sex )?$get_teacher_advisory->sex:'Male';

		if( $get_teacher_advisory_gender ){

            $prefix = 'Ms.';

        } else {

            $prefix = 'Mr.';

        }

		if( !empty( $get_teacher_advisory )){

			$data['teacher_name_upper'] = $teachers_advisory_name.' '.$teacher_advisory_midname.' '.$teacher_advisory_lastname;

		} else {

			$data['teacher_name_upper'] = '';

		}

	   $data['quarter_display'] = ''; 

	   $total_quarter = 4;

        ob_start();

        ?> 

        <table width="100%">

            <tr>

                <td width="48%" style="vertical-align: top"> 

                    <?php 

                    if( $studentlistMale ) {

                        ?>

                        <table class="tobe_bordered" width="100%" style=" border: 1px solid black;  border-collapse: collapse;">

                            <thead>

                                <tr> 

                                    <th style=" border: 1px solid black" colspan="2">Names</th> 

                                    <?php

                                    if( $enableStrand ){

                                        $gradingsettings = $this->gradingsetting_model->getBySession( $session_id );

                                        $get_quarter = array();

                                        $total_quarter =0;

                                        if( $semester_id == 1 ){

                                            $get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array(); 

                                            $get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;

                                            $total_quarter = count( $get_quarter );

                                        } else {

                                            $get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();   

                                            $get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;

                                            $total_quarter = count( $get_quarter );

                                        }

                                        

                                        if( $get_quarter ){

                                            foreach( $get_quarter as $key => $value ){

                                                ?>

                                                <th style=" border: 1px solid black;padding:2px 5px;" ><?php echo ordinal_suffix_roman($value);?></th>

                                                <?php

                                            }

                                           /*  ?><th>F</th><?php */

                                        }

                                    } else {

                                        for($x=1;$x<=4;$x++){

                                            ?>

                                            <th style=" border: 1px solid black;padding:2px 5px;" ><?php echo ordinal_suffix_roman($x);?></th>  

                                            <?php

                                        }

                                    }   

                                    ?>

									<th style=" border: 1px solid black;padding:2px 5px;" ><small>Final Grade</small></th>  

                                </tr>

                            </thead>

                            <tbody>

                                <?php if (empty($studentlistMale)) {

                                    ?>

                                    <tr>

                                        <td colspan="32" class="text-danger text-center" style= "border: 1px solid black"><?php echo $this->lang->line('no_record_found'); ?></td>



                                    </tr>

                                    <?php

                                } else {

                                    $row_count = 1;

                                    foreach ($studentlistMale as  $student_value) {  

											$complete_grades = true;

											$student_id = $student_value["id"]; 

											$lastname = $student_value['lastname'];

											$firstname = $student_value['firstname'];

											$suffix = $student_value['suffix'];

											$middlename = !empty($student_value['middlename'])?$student_value['middlename'][0].'.':'';

											?>

                                        <tr> 

                                            <td style= "border: 1px solid black; text-align: right; font-size: 11px;"><?php echo $row_count;?></td>

                                            <td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;text-transform:uppercase;"><?php echo $lastname.', '.$firstname.' '.$suffix.' '.$middlename; ?></td> 

                                            <?php 

                                            if( $enableStrand ){

                                                $final_grade = 0;

												$not_available = false;

                                                for( $qtr=0;$qtr<$total_quarter;$qtr++ ) {

                                                    $y = $get_quarter[$qtr];

													if( $y == 1 || $y == 2 ){

														$semester_id = 1;

													} elseif( $y == 3 || $y == '4'){

														$semester_id = 2;

													}

													$getStudentSubjectSemesterDrop = $this->customsubject_model->getStudentSubjectSemesterDrop( $student_id, $subject_id, $semester_id , $session_id );

													if( empty( $getStudentSubjectSemesterDrop)){

														$not_available = false;

														$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $y, $combine_category, $session_id  );

														$grade_per_quarter = isset($grade_per_quarter)?$grade_per_quarter:null;

														$grade_per_quarter = number_format($grade_per_quarter, $decimal_grades, '.', '');

														if( $grade_per_quarter == null || $grade_per_quarter == 0 ){

															$complete_grades = false;

															if( $if_gradeisnull == 'blank'){

																 echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;"></td>';

															}  else {

																echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;">'.number_format((float)0, $decimal_grades, '.', '').'</td>';

															}

														} else {

															$final_grade = $final_grade + $grade_per_quarter;

															 echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >'.$grade_per_quarter.'</td>';

														}

													} else {

														$not_available = true;

														 echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >N/A</td>';

													}

                                                }

											   	if( $not_available ){

													 echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >N/A</td>';

												} else {

													if( $complete_grades && $final_grade != 0 ){

														$average = $final_grade / $total_quarter;

														$average = number_format( $average, $decimal_finalgrade, '.',' ');

														echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >'.$average.'</td>';

													} else {

														echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;"></td>';

													}

												}

														

                                               

                                            } else {

												$final_grade= 0;

                                                for( $x=1;$x<=4;$x++){

													$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $x, $combine_category, $session_id );

													$grade_per_quarter = isset($grade_per_quarter)?$grade_per_quarter:null;

													$grade_per_quarter = number_format($grade_per_quarter, $decimal_grades, '.', '');

													if( $grade_per_quarter == null || $grade_per_quarter == 0 ){

														$complete_grades = false;

														if( $if_gradeisnull == 'blank'){

															echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;"></td>';

														}  else {

															echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;">'.number_format((float)0, $decimal_grades, '.', '').'</td>';

														}

													} else {

														$final_grade = $final_grade + $grade_per_quarter;

														echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;">'.$grade_per_quarter.'</td>';

													}

                                                }

												if( $complete_grades && $final_grade != 0 ){

													$average = $final_grade / $total_quarter;

													$average = number_format( $average, $decimal_finalgrade, '.',' ');

													 echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >'.$average.'</td>';

												} else {

													 echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;"></td>';

												}

                                            }

                                            ?>  

                                        </tr>

                                        <?php

                                        $row_count++; 

                                    }

                                }

                                ?>

                            </tbody> 

                        </table>  

                        <?php

                    }

                    ?>

                </td>

                <td width="4%"> &nbsp;</td>

                <td align="right" width="48%" style="vertical-align: top">

                    <?php 

                    if( $studentlistFemale ) {

                        ?>

                        <table class="tobe_bordered" width="100%" style=" border: 1px solid black;  border-collapse: collapse;" align="left">

                            <thead>

                                <tr> 

                                    <th style=" border: 1px solid black" colspan="2">Names</th> 

                                     <?php

                                    if( $enableStrand ){

                                        $gradingsettings = $this->gradingsetting_model->getBySession( $session_id );

                                        $get_quarter = array();

                                        $total_quarter =0;

                                        if( $semester_id == 1 ){

                                            $get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array(); 

                                            $get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;

                                            $total_quarter = count( $get_quarter );

                                        } else {

                                            $get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();   

                                            $get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;

                                            $total_quarter = count( $get_quarter );

                                        }

                                        

                                        if( $get_quarter ){

                                            foreach( $get_quarter as $key => $value ){

                                                ?>

                                                <th style=" border: 1px solid black;padding:2px 5px;" ><?php echo ordinal_suffix_roman($value);?></th>

                                                <?php

                                            }

                                          /*   ?><th>F</th><?php */

                                        }

                                    } else {

                                        for($x=1;$x<=4;$x++){

                                            ?>

                                            <th style=" border: 1px solid black;padding:2px 5px;" ><?php echo ordinal_suffix_roman($x);?></th>  

                                            <?php

                                        }

                                    }   

                                    ?>

									<th style=" border: 1px solid black;padding:2px 5px;" ><small>Final Grade</small></th>  

                                </tr>

                            </thead>

                            <tbody>

                                <?php if (empty($studentlistFemale)) {

                                    ?>

                                    <tr>

                                        <td colspan="32" class="text-danger text-center" style= "border: 1px solid black"><?php echo $this->lang->line('no_record_found'); ?></td>



                                    </tr>

                                    <?php

                                } else {

                                    $row_count = 1;

                                    foreach ($studentlistFemale as  $student_value) {  

										

										$complete_grades = true;

										$student_id = $student_value["id"]; 

										$lastname = $student_value['lastname'];

										$firstname = $student_value['firstname'];

										$suffix = $student_value['suffix'];

										$middlename = !empty($student_value['middlename'])?$student_value['middlename'][0].'.':'';

                                        ?>

                                         <tr> 

                                            <td style= "border: 1px solid black; text-align: right; font-size: 11px;"><?php echo $row_count;?></td>

                                            <td style= "border: 1px solid black; font-size: 11px; text-align: left; padding-left: 6px;  padding-right: 6px;text-transform:uppercase;"><?php echo $lastname.', '.$firstname.' '.$suffix.' '.$middlename; ?></td> 

                                            <?php 

                                            if( $enableStrand ){

                                                $final_grade = 0;

												$not_available = false;

                                                for( $qtr=0;$qtr<$total_quarter;$qtr++ ) {

                                                    $y = $get_quarter[$qtr];

													if( $y == 1 || $y == 2 ){

														$semester_id = 1;

													} elseif( $y == 3 || $y == '4'){

														$semester_id = 2;

													}

													$getStudentSubjectSemesterDrop = $this->customsubject_model->getStudentSubjectSemesterDrop( $student_id, $subject_id, $semester_id , $session_id );

													if( empty( $getStudentSubjectSemesterDrop)){

														 $not_available = false;

														$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $y, $combine_category, $session_id  );

														$grade_per_quarter = isset($grade_per_quarter)?$grade_per_quarter:null;

														$grade_per_quarter = number_format($grade_per_quarter, $decimal_grades, '.', '');

														if( $grade_per_quarter == null || $grade_per_quarter == 0 ){

															$complete_grades = false;

															if( $if_gradeisnull == 'blank'){

																 echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;"></td>';

															}  else {

																echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;">'.number_format((float)0, $decimal_grades, '.', '').'</td>';

															}

														} else {

															$final_grade = $final_grade + $grade_per_quarter;

															 echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >'.$grade_per_quarter.'</td>';

														}

													} else {

														 $not_available = true;

														 echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >N/A</td>';

													}

                                                }

												if( $not_available ){

													 echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >N/A</td>';

												} else {

													if( $complete_grades && $final_grade != 0 ){

														$average = $final_grade / $total_quarter;

														$average = number_format( $average, $decimal_finalgrade, '.',' ');

														echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;" >'.$average.'</td>';

													} else {

														echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;"></td>';

													}

												}

                                            } else {

												

												$final_grade= 0;

                                                for( $x=1;$x<=4;$x++){

													$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $x, $combine_category, $session_id  );

													$grade_per_quarter = isset($grade_per_quarter)?$grade_per_quarter:null;

													$grade_per_quarter = number_format($grade_per_quarter, $decimal_grades, '.', '');

													if( $grade_per_quarter == null || $grade_per_quarter == 0 ){

														$complete_grades = false;

														if( $if_gradeisnull == 'blank'){

															echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;"></td>';

														}  else {

															echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;">'.number_format((float)0, $decimal_grades, '.', '').'</td>';

														}

													} else {

														$final_grade = $final_grade + $grade_per_quarter;

														echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;">'.$grade_per_quarter.'</td>';

													}

                                                }

												if( $complete_grades && $final_grade != 0 ){

													$average = $final_grade / $total_quarter;

													$average = number_format( $average, $decimal_finalgrade, '.',' ');

													 echo '<td style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;text-align:left;" >'.$average.'</td>';

												} else {

													 echo '<td  style= "border: 1px solid black; font-size: 11px;  padding-left: 6px;  padding-right: 6px;"></td>';

												}

                                            }

                                            ?>  

                                        </tr>

                                        <?php

                                        $row_count++; 

                                    }

                                }

                                ?>

                            </tbody> 

                        </table>  

                        <?php

                    }

                    ?>

                </td>



            </tr>

        </table>

        <br /><br /><br />

		

		<br/>

		<br/>

		<br/>

        <table width="100%">

            <tr>

                <td width="60%">I hereby certify that the entries are true and correct</td>

                <td width="35%" style=" border-bottom: 1px solid black;"></td>

                <td width="5%"></td>

            </tr>

            <tr>

                <td width="60%"></td>

                <td width="35%" style="text-align: center;">Signature</td>

                <td width="5%"></td>

            </tr>

        </table>

		<br/>

		<br/>

		<br/>

		<table width="100%">

            <tr>

                <td width="60%" align="right">Approved by:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

				<?php 

				 if( $session_id == '13'){

					$principal_fullname = 'Jesreel D. Mercader';

				} elseif($session_id == '14'){

                    $principal_fullname == 'ALBERTO B. PONSICA, PH. D.';

                } ?>

                <td width="35%" style=" border-bottom: 1px solid black;text-transform:uppercase;text-align: center;"><?php echo $principal_fullname;?></td>

                <td width="5%"></td>

            </tr>

            <tr>

                <td width="60%"></td>

                <td width="35%" style="text-align: center;">Principal</td>

                <td width="5%"></td>

            </tr>

        </table>

         <?php

        $content = ob_get_contents();

        ob_end_clean(); 

        $data['content'] = $content;  

        $html = $this->load->view('template/grade_sheet',$data, true );   

        $pdfFilePath = "Grade_Sheet_".$class_name."_".$section_name.".pdf";

		$this->load->library('m_pdf');

		$this->mpdf = new mPDF('utf-8',array(210,160)); 

		$this->mpdf->SetDisplayMode('fullwidth');

		$this->m_pdf->pdf->AddPage('P','LEGAL','5','5','5','5','5','5','5','5');

		$this->m_pdf->pdf->WriteHTML($html);

		$this->m_pdf->pdf->Output($pdfFilePath, "D");

    }

	

	

	 
}

?>