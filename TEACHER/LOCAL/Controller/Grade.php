<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Grade extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->lang->load('message', 'english');
        $this->load->library('auth');
        $this->load->library('excel');
		$this->load->library('spout');
		$this->load->library('excelwithspout');
		$this->load->model('importgradesbatch_model');
		$this->load->model('importgradesdetails_model');
		$this->load->model('template_model');
		$this->load->model('strand_model');
		$this->load->model('customsubject_model');
		$this->load->model('importgradesperformance_model');
		$this->load->model('importgradesquarterly_model');
		$this->load->model('importgradeswritten_model');
        $this->auth->is_logged_in_teacher();
    }

    public function index() {

    
        $this->session->set_userdata('top_menu', 'Examinations');
        $this->session->set_userdata('sub_menu', 'grade/index');
        $data['title'] = 'Add Grade';
        $data['title_list'] = 'Grade Details';
        $listgrade = $this->grade_model->get();
        $data['listgrade'] = $listgrade;
        $this->load->view('layout/teacher/header');
        $this->load->view('teacher/grade/creategrade', $data);
        $this->load->view('layout/teacher/footer');
    }

    function create() {
        $data['title'] = 'Add Arade';
        $data['title_list'] = 'Grade Details';
        $this->form_validation->set_rules('name', 'Grade', 'required');
        $this->form_validation->set_rules('mark_from', 'Percentage From', 'trim|required|xss_clean');
        $this->form_validation->set_rules('mark_upto', 'Percentage Upto', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $listgrade = $this->grade_model->get();
            $data['listgrade'] = $listgrade;
            $this->load->view('layout/teacher/header');
            $this->load->view('teacher/grade/creategrade', $data);
            $this->load->view('layout/teacher/footer');
        } else {
            $data = array(
                'name' => $this->input->post('name'),
                'mark_from' => $this->input->post('mark_from'),
                'mark_upto' => $this->input->post('mark_upto'),
                'point' => $this->input->post('point'),
                'description' => $this->input->post('description')
            );
            $this->grade_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Grade added successfully</div>');
            redirect('teacher/grade/index');
        }
    }

    function edit($id) {
        $data['title'] = 'Edit Grade';
        $data['title_list'] = 'Grade Details';
        $data['id'] = $id;
        $editgrade = $this->grade_model->get($id);
        $data['editgrade'] = $editgrade;
        $this->form_validation->set_rules('name', 'Grade', 'required');
        $this->form_validation->set_rules('mark_from', 'Percentage from', 'trim|required|xss_clean');
        $this->form_validation->set_rules('mark_upto', 'Percentage upto', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $listgrade = $this->grade_model->get();
            $data['listgrade'] = $listgrade;
            $this->load->view('layout/teacher/header');
            $this->load->view('teacher/grade/editgrade', $data);
            $this->load->view('layout/teacher/footer');
        } else {
            $data = array(
                'id' => $this->input->post('id'),
                'name' => $this->input->post('name'),
                'mark_from' => $this->input->post('mark_from'),
                'mark_upto' => $this->input->post('mark_upto'),
                'point' => $this->input->post('point'),
                'description' => $this->input->post('description')
            );
            $this->grade_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Grade updated successfully</div>');
            redirect('teacher/grade/index');
        }
    }

    function delete($id) {
        $data['title'] = 'Fees Master List';
        $this->grade_model->remove($id);
        redirect('teacher/grade/index');
    }
	
    function import() {
		$student_session_data = $this->session->userdata("student");
		$teacher_id = $student_session_data['teacher_id'];
		$data['title'] = 'Import Grades';
		$data['title_list'] = 'Recently Added Grades';
		$this->session->set_userdata('top_menu', 'Examinations');
		$this->session->set_userdata('sub_menu', 'import/index');
		$session = $this->setting_model->getCurrentSession();
		// $class = $this->class_model->get();
		$student_session_data = $this->session->userdata("student"); 
		$teacher_id = $student_session_data['teacher_id']; 
		$class = $this->class_model->get_by_teacher( $teacher_id );
		$data['classlist'] = $class;
		$data['subjectlist'] = $this->subject_model->get();
		$data['getquarter'] = $this->customlib->getQuarter();
		$data['templatelist'] = $this->template_model->get();
		$setting_result = $this->setting_model->get();
        $import_grade_settings = $setting_result[0]['import_grade'];
        $data['import_grade_settings'] = $import_grade_settings;
        $data['firstqsettings'] = $setting_result[0]['import_first'];
		$data['secondqsettings'] = $setting_result[0]['import_second'];
		$data['thirdqsettings'] = $setting_result[0]['import_third'];
		$data['fourthqsettings'] = $setting_result[0]['import_fourth'];
		$this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
		$this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');
		$this->form_validation->set_rules('quarter', 'Quarter', 'trim|required|xss_clean');
		$this->form_validation->set_rules('subject_id', 'Subject', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('layout/teacher/header', $data);
			$this->load->view('teacher/grade/import', $data);
			$this->load->view('layout/teacher/footer', $data);
		} else {
		}
	}

	function getNameFromNumber($num) {
	    $numeric = $num % 26;
	    $letter = chr(65 + $numeric);
	    $num2 = intval($num / 26);
	    if ($num2 > 0) {
	        return getNameFromNumber($num2 - 1) . $letter;
	    } else {
	        return $letter;
	    }
	}
	
/* 	function import_grades() {
		ini_set('memory_limit', '-1');
		$student_session_data = $this->session->userdata("student");
		$teacher_id = $student_session_data['teacher_id'];
		$data['title'] = 'Import Grades';
		$data['title_list'] = 'Recently Added Grades';
		$this->session->set_userdata('top_menu', 'Examinations');
		$this->session->set_userdata('sub_menu', 'import/index');
		$session = $this->setting_model->getCurrentSession();
		// $class = $this->class_model->get();
		$student_session_data = $this->session->userdata("student"); 
		$teacher_id = $student_session_data['teacher_id']; 
		$class = $this->class_model->get_by_teacher( $teacher_id );
		$data['classlist'] = $class;
		$data['subjectlist'] = $this->subject_model->get();
		$data['getquarter'] = $this->customlib->getQuarter();
		$data['templatelist'] = $this->template_model->get();
		$this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
		$this->form_validation->set_rules('section_id', 'Section', 'trim|xss_clean');
		$this->form_validation->set_rules('quarter', 'quarter', 'trim|required|xss_clean');
		$this->form_validation->set_rules('subject_id', 'Subject', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('layout/teacher/header', $data);
			$this->load->view('teacher/grade/import', $data);
			$this->load->view('layout/teacher/footer', $data);
		} else {
			$class_id = $this->input->post('class_id');
			$section_id = $this->input->post('section_id');
			$subject_id = $this->input->post('subject_id');
			$quarter = $this->input->post('quarter');
			$getquartersheet = $quarter - 1;
			$session = $this->setting_model->getCurrentSession();
			
			if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
				include ("application/third_party/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
				
				$inputFileName = $_FILES['file']['tmp_name'];
				//  Read your Excel workbook
				try {
					$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($inputFileName);
				} 
				
				catch(Exception $e) {
					die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
				}
				$objPHPExcel->setActiveSheetIndex($getquartersheet);
				$sheetInsertData = $objPHPExcel->getActiveSheet();
				//$sheetInsertData = $sheetInsertData->getTitle();
				//$sheetInsertData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
				
			 	$foundInCells = $this->spout->search_cells_with_pt_wt( $inputFileName );
				
				$grade_list = isset($foundInCells['{grade}'][$getquartersheet])?$foundInCells['{grade}'][$getquartersheet]:null;
				if( $grade_list != null ){
					$grade_list_letters = $grade_list['columnnumber'];
					$grade_list_number = $grade_list['rownumber'];
				}
				
				$start_boys_list =  isset($foundInCells['{start_boys}'][$getquartersheet])?$foundInCells['{start_boys}'][$getquartersheet]:null;
				if( $start_boys_list != null ){
					$start_boys_letters = $start_boys_list['columnnumber'];
					$start_boys_number = $start_boys_list['rownumber'];
				}
					
				$end_boys_list = isset($foundInCells['{end_boys}'][$getquartersheet])?$foundInCells['{end_boys}'][$getquartersheet]:null;
				if( $end_boys_list != null ){
					$end_boys_letters = $end_boys_list['columnnumber'];
					$end_boys_number = $end_boys_list['rownumber'];
				}
				
				$boys_start_id_list = isset($foundInCells['{boys_start_id}'][$getquartersheet])?$foundInCells['{boys_start_id}'][$getquartersheet]:null;
				if( $boys_start_id_list != null ){
					$boys_start_id_letters = $boys_start_id_list['columnnumber'];
					$boys_start_id_number = $boys_start_id_list['rownumber'];
				}

				$start_girls_list = isset($foundInCells['{start_girls}'][$getquartersheet])?$foundInCells['{start_girls}'][$getquartersheet]:null;
				if( $start_girls_list != null ){
					$start_girls_letters = $start_girls_list['columnnumber'];
					$start_girls_number =  $start_girls_list['rownumber'];
				}

				$end_girls_list = isset($foundInCells['{end_girls}'][$getquartersheet])?$foundInCells['{end_girls}'][$getquartersheet]:null;
				if( $end_girls_list != null ){
					$end_girls_letters = $end_girls_list['columnnumber'];
					$end_girls_number = $end_girls_list['rownumber'];
				}
				
				$girls_start_id_list = isset($foundInCells['{girls_start_id}'][$getquartersheet])?$foundInCells['{girls_start_id}'][$getquartersheet]:null;
				if( $girls_start_id_list != null ){
					$girls_start_id_letters = $girls_start_id_list['columnnumber'];
					$girls_start_id_number = $girls_start_id_list['rownumber'];
				}
				
				
				
				if( $grade_list == null ){
					$this->session->set_flashdata('msg', '<div student="alert alert-warning text-center">{grade} is missing !</div>');		
				}elseif( $start_boys_list == null ){
					$this->session->set_flashdata('msg', '<div student="alert alert-warning text-center">{start_boys} is missing !</div>');		
				}elseif( $end_boys_list == null ){
					$this->session->set_flashdata('msg', '<div student="alert alert-warning text-center">{end_boys} is missing !</div>');		
				}elseif( $start_girls_list == null ){
					$this->session->set_flashdata('msg', '<div student="alert alert-warning text-center">{start_girls} is missing !</div>');		
				} elseif( $end_girls_list == null ){
					$this->session->set_flashdata('msg', '<div student="alert alert-warning text-center">{end_girls} is missing !</div>');		
				} elseif( $boys_start_id_list == null ){
					$this->session->set_flashdata('msg', '<div student="alert alert-warning text-center">{boys_starts_id} is missing !</div>');		
				} elseif( $girls_start_id_list == null ){
					$this->session->set_flashdata('msg', '<div student="alert alert-warning text-center">{girls_start_id} is missing !</div>');		
				} else {
					$ww_data = array();
					$pa_data = array();
					$ini_grade_list = isset($foundInCells['{ini-grade}'][$getquartersheet])?$foundInCells['{ini-grade}'][$getquartersheet]:null;
					if( $ini_grade_list != null ){
						$ini_grade_letters = $ini_grade_list['columnnumber'];
						$ini_grade_number = $ini_grade_list['rownumber'];
					}
					
					$qaws_list = isset($foundInCells['{qaws}'][$getquartersheet])?$foundInCells['{qaws}'][$getquartersheet]:null;
					if( $qaws_list != null ){
						$qaws_letters = $qaws_list['columnnumber'];
						$qaws_number = $qaws_list['rownumber'];
					}

					$qaps_list = isset($foundInCells['{qaps}'][$getquartersheet])?$foundInCells['{qaps}'][$getquartersheet]:null;
					if( $qaps_list != null ){
						$qaps_letters = $qaps_list['columnnumber'];
						$qaps_number = $qaps_list['rownumber'];
					}
					
					$qa_list = isset($foundInCells['{qa}'][$getquartersheet])?$foundInCells['{qa}'][$getquartersheet]:null;
					if( $qa_list != null ){
						$qa_letters = $qa_list['columnnumber'];
						$qa_number = $qa_list['rownumber'];
					}
					
					$ptws_list = isset($foundInCells['{ptws}'][$getquartersheet])?$foundInCells['{ptws}'][$getquartersheet]:null;
					if( $ptws_list != null ){
						$ptws_letters = $ptws_list['columnnumber'];
						$ptws_number = $ptws_list['rownumber'];
					}
					
					$ptps_list = isset($foundInCells['{ptps}'][$getquartersheet])?$foundInCells['{ptps}'][$getquartersheet]:null;
					if( $ptps_list != null ){
						$ptps_letters = $ptps_list['columnnumber'];
						$ptps_number = $ptps_list['rownumber'];
					}
					
					$ptt_list = isset($foundInCells['{ptt}'][$getquartersheet])?$foundInCells['{ptt}'][$getquartersheet]:null;
					if( $ptt_list != null ){
						$ptt_letters = $ptt_list['columnnumber'];
						$ptt_number = $ptt_list['rownumber'];
					}
					$pt_list = isset($foundInCells['{pt}'][$getquartersheet])?$foundInCells['{pt}'][$getquartersheet]:null;
					if( $pt_list != null ){
						$pt_letters = $pt_list['columnnumber'];
						$pt_number = $pt_list['rownumber'];
					}
					$wwws_list = isset($foundInCells['{wwws}'][$getquartersheet])?$foundInCells['{wwws}'][$getquartersheet]:null;
					if( $wwws_list != null ){
						$wwws_letters = $wwws_list['columnnumber'];
						$wwws_number = $wwws_list['rownumber'];
					}
					
					$wwps_list = isset($foundInCells['{wwps}'][$getquartersheet])?$foundInCells['{wwps}'][$getquartersheet]:null;
					if( $wwps_list != null ){
						$wwps_letters = $wwps_list['columnnumber'];
						$wwps_number = $wwps_list['rownumber'];
					}
					$wwt_list = isset($foundInCells['{wwt}'][$getquartersheet])?$foundInCells['{wwt}'][$getquartersheet]:null;
					if( $wwt_list != null ){
						$wwt_letters = $wwt_list['columnnumber'];
						$wwt_number = $wwt_list['rownumber'];
					}
					$ww_list = isset($foundInCells['{wwt}'][$getquartersheet])?$foundInCells['{wwt}'][$getquartersheet]:null;
					if( $ww_list != null ){
						$ww_letters = $ww_list['columnnumber'];
						$ww_number = $ww_list['rownumber'];
					}
					
					if( $getquartersheet == 0 ){
						$highestRow = $end_boys_number - 1;
						
						$startRow = $start_boys_number + 1;
						$grades_f[]= null;
						for($row = $startRow;$row <= $highestRow;$row++)
						{
							//$grades['id']  = $sheetInsertData->getCellByColumnAndRow(1,$row)->getOldCalculatedValue();
							$get_id  = $sheetInsertData->getCellByColumnAndRow($boys_start_id_letters,$row)->getValue();
							
							if( $get_id ){
								$grades['id']  = $get_id;
									// $grades['grade'] = $sheetInsertData->getCellByColumnAndRow($grade_list_number,$row)->getValue();
								// $grades['initial_grade'] = $sheetInsertData->getCellByColumnAndRow($ini_grade_number,$row)->getOldCalculatedValue();
								$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getOldCalculatedValue();
								if( $get_grades == 0 ){
									$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
								}
								$grades['grade'] = $get_grades;
								if( $ini_grade_list != null ){
									$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $ini_grade_letters,$row)->getOldCalculatedValue();
									if( $get_initial_grade == 0 ){
										$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
									}
									$grades['initial_grade'] = $get_initial_grade;
								}
								if( $qa_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['qa'] =$check_1;
									} else {
										$grades['qa'] = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getValue();
									}
									$check_1 = FALSE;

								}
								if( $qaws_list != null ){  
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['qaws'] =$check_1;
									} else {
										$grades['qaws'] = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getValue();
									}
									$check_1 = FALSE;

								}
								if( $qaps_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['qaps'] =$check_1;
									} else {
										$grades['qaps'] = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								
								if( $pt_list != null){
									$pa_data = array();
									$start_pi = $wwws_letters + 1; 
									for ($pi = $start_pi; $pi !== $ptt_letters; $pi++){
										$pa_data_score = $sheetInsertData->getCellByColumnAndRow( $pi,$row)->getValue();
										$pa_data_highest_score = $sheetInsertData->getCellByColumnAndRow( $pi,$pt_number-1)->getValue();

										$pa_data[]  = $pa_data_score.'/'.$pa_data_highest_score;
									}  
									$grades['pt'] =   implode(",", $pa_data); 
								}
								if( $ww_list != null){  
									$ww_data = array();
									$start_ww = $start_boys_letters + 2;
									for ($wi = $start_ww; $wi !== $wwt_letters; $wi++){
										$ww_data_score = $sheetInsertData->getCellByColumnAndRow( $wi,$row)->getValue();
										$ww_data_highest_score = $sheetInsertData->getCellByColumnAndRow( $wi,$ww_number-1)->getValue();
										$ww_data[] = $ww_data_score.'/'.$ww_data_highest_score;
									}
									$grades['ww'] = implode(",", $ww_data); 
								}
								
								if( $ptt_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['ptt'] =$check_1;
									} else {
										$grades['ptt'] = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $ptws_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['ptws'] =$check_1;
									} else {
										$grades['ptws'] = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $ptps_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['ptps'] =$check_1;
									} else {
										$grades['ptps'] = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $wwt_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['wwt'] =$check_1;
									} else {
										$grades['wwt'] = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $wwt_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['wwws'] =$check_1;
									} else {
										$grades['wwws'] = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $wwps_list != null ){
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['wwps'] =$check_1;
									} else {
										$grades['wwps'] = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getValue();
									}
									$check_1 = FALSE;
									
								}
								
								
								if($grades['id'] == null ){
									break;
									
								} else {
									$grades_f[] = $grades;
								}
							
							}
						
						}
						
						$highestRow = $end_girls_number - 1;
						$startRow = $start_girls_number + 1; 
						for($row = $startRow;$row <= $highestRow;$row++)
						{
							$get_id = $sheetInsertData->getCellByColumnAndRow($girls_start_id_letters,$row)->getValue();
							
							if( $get_id ){
								$grades['id']  = $get_id;
								// $grades['grade'] = $sheetInsertData->getCellByColumnAndRow($grade_list_number,$row)->getValue();
								// $grades['initial_grade'] = $sheetInsertData->getCellByColumnAndRow($ini_grade_number,$row)->getOldCalculatedValue();
								$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getOldCalculatedValue();
								if( $get_grades == 0 ){
									$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
								}
								$grades['grade'] = $get_grades;
								if( $ini_grade_list != null ){
									$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $ini_grade_letters,$row)->getOldCalculatedValue();
									if( $get_initial_grade == 0 ){
										$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
									}
									$grades['initial_grade'] = $get_initial_grade;
								}
								if( $qa_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['qa'] =$check_1;
									} else {
										$grades['qa'] = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getValue();
									}
									$check_1 = FALSE; 
								}
								if( $qaws_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['qaws'] =$check_1;
									} else {
										$grades['qaws'] = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $qaps_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['qaps'] =$check_1;
									} else {
										$grades['qaps'] = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								 
								if( $pt_list != null){
									$pa_data = array();
									$start_pi = $wwws_letters + 1; 
									for ($pi = $start_pi; $pi !== $ptt_letters; $pi++){
										$pa_data_score = $sheetInsertData->getCellByColumnAndRow( $pi,$row)->getValue();
										$pa_data_highest_score = $sheetInsertData->getCellByColumnAndRow( $pi,$pt_number-1)->getValue();

										$pa_data[]  = $pa_data_score.'/'.$pa_data_highest_score;
									}  
									$grades['pt'] =   implode(",", $pa_data); 
								}
								if( $ww_list != null){ 
									$ww_data = array(); 
									$start_ww = $start_boys_letters + 2;
									for ($wi = $start_ww; $wi !== $wwt_letters; $wi++){
										$ww_data_score = $sheetInsertData->getCellByColumnAndRow( $wi,$row)->getValue();
										$ww_data_highest_score = $sheetInsertData->getCellByColumnAndRow( $wi,$ww_number-1)->getValue();
										$ww_data[] = $ww_data_score.'/'.$ww_data_highest_score;
									}
									$grades['ww'] = implode(",", $ww_data); 
								} 
								
								if( $ptt_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['ptt'] =$check_1;
									} else {
										$grades['ptt'] = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $ptws_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['ptws'] =$check_1;
									} else {
										$grades['ptws'] = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $ptps_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['ptps'] =$check_1;
									} else {
										$grades['ptps'] = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $wwt_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['wwt'] =$check_1;
									} else {
										$grades['wwt'] = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $wwt_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['wwws'] =$check_1;
									} else {
										$grades['wwws'] = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $wwps_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['wwps'] =$check_1;
									} else {
										$grades['wwps'] = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if($grades['id'] == null ){
									break;
								} else {
									$grades_f[] = $grades;
								}
							}
						}
					} else {
						// $highestRow = 38;
						$grades_f[]= null;
						$highestRow = $end_boys_number - 1;
						$startRow = $start_boys_number + 1;
						for($row = $startRow;$row <= $highestRow;$row++)
						{
							$get_id = $sheetInsertData->getCellByColumnAndRow($boys_start_id_letters,$row)->getOldCalculatedValue();
							if( $get_id != 0  ){
								//$grades['id']  = $sheetInsertData->getCellByColumnAndRow(1,$row)->getOldCalculatedValue();
								$grades['id']  = $get_id;
								// $grades['grade'] = $sheetInsertData->getCellByColumnAndRow($grade_list_number,$row)->getValue();
								// $grades['initial_grade'] = $sheetInsertData->getCellByColumnAndRow($ini_grade_number,$row)->getOldCalculatedValue();
								$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getOldCalculatedValue();
								if( $get_grades == 0 ){
									$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
								}
								$grades['grade'] = $get_grades;
								if( $ini_grade_list != null ){
									$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $ini_grade_letters,$row)->getOldCalculatedValue();
									if( $get_initial_grade == 0 ){
										$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
									}
									$grades['initial_grade'] = $get_initial_grade;
								}
								if( $qa_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['qa'] =$check_1;
									} else {
										$grades['qa'] = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $qaws_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['qaws'] =$check_1;
									} else {
										$grades['qaws'] = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $qaps_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['qaps'] =$check_1;
									} else {
										$grades['qaps'] = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								
								 if( $pt_list != null){
									$pa_data = array();
									$start_pi = $wwws_letters + 1; 
									for ($pi = $start_pi; $pi !== $ptt_letters; $pi++){
										$pa_data_score = $sheetInsertData->getCellByColumnAndRow( $pi,$row)->getValue();
										$pa_data_highest_score = $sheetInsertData->getCellByColumnAndRow( $pi,$pt_number-1)->getValue();

										$pa_data[]  = $pa_data_score.'/'.$pa_data_highest_score;
									}  
									$grades['pt'] =   implode(",", $pa_data); 
								}
								if( $ww_list != null){ 
									$ww_data = array(); 
									$start_ww = $start_boys_letters + 2;
									for ($wi = $start_ww; $wi !== $wwt_letters; $wi++){
										$ww_data_score = $sheetInsertData->getCellByColumnAndRow( $wi,$row)->getValue();
										$ww_data_highest_score = $sheetInsertData->getCellByColumnAndRow( $wi,$ww_number-1)->getValue();
										$ww_data[] = $ww_data_score.'/'.$ww_data_highest_score;
									}
									$grades['ww'] = implode(",", $ww_data); 
								}
								
								if( $ptt_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['ptt'] =$check_1;
									} else {
										$grades['ptt'] = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $ptws_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['ptws'] =$check_1;
									} else {
										$grades['ptws'] = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $ptps_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['ptps'] =$check_1;
									} else {
										$grades['ptps'] = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $wwt_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['wwt'] =$check_1;
									} else {
										$grades['wwt'] = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $wwt_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['wwws'] =$check_1;
									} else {
										$grades['wwws'] = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $wwps_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['wwps'] =$check_1;
									} else {
										$grades['wwps'] = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								
								if($grades['id'] == null ){
									break;
									
								} else {
									$grades_f[] = $grades;
								}
							}
							
							
						}
						// $highestRow = 71;
						$highestRow = $end_girls_number - 1;
						$startRow = $start_girls_number + 1; 
						for($row = $startRow;$row <= $highestRow;$row++)
						{
							$get_id = $sheetInsertData->getCellByColumnAndRow($girls_start_id_letters,$row)->getOldCalculatedValue();
							if( $get_id != 0  ){
								$grades['id']  = $get_id;
								// $grades['grade'] = $sheetInsertData->getCellByColumnAndRow($grade_list_number,$row)->getValue();
								// $grades['initial_grade'] = $sheetInsertData->getCellByColumnAndRow($ini_grade_number,$row)-> getOldCalculatedValue();
								$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getOldCalculatedValue();
								if( $get_grades == 0 ){
									$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
								}
								$grades['grade'] = $get_grades;
								if( $ini_grade_list != null ){
									$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $ini_grade_letters,$row)->getOldCalculatedValue();
									if( $get_initial_grade == 0 ){
										$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
									}
									$grades['initial_grade'] = $get_initial_grade;
								}
								if( $qa_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['qa'] =$check_1;
									} else {
										$grades['qa'] = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $qaws_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['qaws'] =$check_1;
									} else {
										$grades['qaws'] = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $qaps_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['qaps'] =$check_1;
									} else {
										$grades['qaps'] = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								
								if( $pt_list != null){
									$pa_data = array();
									$start_pi = $wwws_letters + 1; 
									for ($pi = $start_pi; $pi !== $ptt_letters; $pi++){
										$pa_data_score = $sheetInsertData->getCellByColumnAndRow( $pi,$row)->getValue();
										$pa_data_highest_score = $sheetInsertData->getCellByColumnAndRow( $pi,$pt_number-1)->getValue();

										$pa_data[]  = $pa_data_score.'/'.$pa_data_highest_score;
									}  
									$grades['pt'] =   implode(",", $pa_data); 
									
								}
								if( $ww_list != null){ 
									$ww_data = array();  
									$start_ww = $start_boys_letters + 2;
									for ($wi = $start_ww; $wi !== $wwt_letters; $wi++){
										$ww_data_score = $sheetInsertData->getCellByColumnAndRow( $wi,$row)->getValue();
										$ww_data_highest_score = $sheetInsertData->getCellByColumnAndRow( $wi,$ww_number-1)->getValue();
										$ww_data[] = $ww_data_score.'/'.$ww_data_highest_score;
									}
									$grades['ww'] = implode(",", $ww_data);
								}
								
								if( $ptt_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['ptt'] =$check_1;
									} else {
										$grades['ptt'] = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $ptws_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['ptws'] =$check_1;
									} else {
										$grades['ptws'] = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getValue();
									}
									$check_1 = FALSE;
								}
								if( $ptps_list != null ){
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['ptps'] =$check_1;
									} else {
										$grades['ptps'] = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getValue();
									}
									$check_1 = FALSE; 
								}
								if( $wwt_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['wwt'] =$check_1;
									} else {
										$grades['wwt'] = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getValue();
									}
									$check_1 = FALSE; 
								}
								if( $wwt_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['wwws'] =$check_1;
									} else {
										$grades['wwws'] = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getValue();
									}
									$check_1 = FALSE; 

								}
								if( $wwps_list != null ){ 
									$check_1 = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getOldCalculatedValue();
									if( is_numeric ( $check_1 ) ){
										$grades['wwps'] =$check_1;
									} else {
										$grades['wwps'] = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getValue();
									}
									$check_1 = FALSE; 
								}
								 
								if($grades['id'] == null ){
									
									break;
									
								} else {
									$grades_f[] = $grades;
								}
							}
						}
					}
					$insert_data_array = array(
							'created_at'=> date('Y-m-d h:i:s'),
							'subject_id'=> $subject_id,
							'class_id'=> $class_id,
							'section_id'=> $section_id,
							'quarter'=> $quarter,
							'teacher_id'=> $teacher_id
						);
					$this->db->insert('import_grades_batch', $insert_data_array );
					$get_batch_id = $this->db->insert_id();
					
					$import_data[] = null;
					$import_quarterly_assessment_data[] = null;
					$import_performance_task_data[] = null;
					$import_written_work_data[] = null;
					$x = 0;
					if( $grades_f ){
						foreach( $grades_f as $key => $value ){
							$x++;
							$id = $this->student_model->getIDByClassSection($value['id'], $class_id, $section_id );
							if( $id ){
								$import_data[] = array(
								'batch_id' => $get_batch_id,
								'class_id' => $class_id,
								'section_id' => $section_id,
								'quarter' => $quarter,
								'subject_id' => $subject_id,
								'student_id' => $id,
								'grades' => isset($value['grade'])?$value['grade']:0,
								'initial_grade' => isset($value['initial_grade'])?$value['initial_grade']:0,
								);	
								$import_quarterly_assessment_data[] = array(
									'batch_id' => $get_batch_id,
									'class_id' => $class_id,
									'section_id' => $section_id,
									'quarter' => $quarter,
									'subject_id' => $subject_id,
									'student_id' => $id,
									'scores' => isset($value['qa'])?$value['qa']:0, 
									'ps' => isset($value['qaps'])?$value['qaps']:0, 
									'ws' => isset($value['qaws'])?$value['qaws']:0, 
								);	 

								$import_performance_task_data[] = array(
									'batch_id' => $get_batch_id,
									'class_id' => $class_id,
									'section_id' => $section_id,
									'quarter' => $quarter,
									'subject_id' => $subject_id,
									'student_id' => $id,
									'scores' => isset($value['pt'])?$value['pt']:0, 
									'total' => isset($value['ptt'])?$value['ptt']:0, 
									'ps' =>  isset($value['ptps'])?$value['ptps']:0, 
									'ws' =>  isset($value['ptws'])?$value['ptws']:0,
								);	 

								$import_written_work_data[] = array(
									'batch_id' => $get_batch_id,
									'class_id' => $class_id,
									'section_id' => $section_id,
									'quarter' => $quarter,
									'subject_id' => $subject_id,
									'student_id' => $id,
									'scores' => isset($value['ww'])?$value['ww']:0,
									'total' => isset($value['wwt'])?$value['wwt']:0,
									'ps' => isset($value['wwps'])?$value['wwps']:0,
									'ws' => isset($value['wwws'])?$value['wwws']:0,
								);	
							} else {
								$this->session->set_flashdata('msg', '<div student="alert alert-warning text-center">In Row '.$x.' student does not exist'.'</div>');	
							}
						}
					} 
					if( count($import_data) > 1 ){
						unset($import_data[0]);
						unset($import_quarterly_assessment_data[0]);
						unset($import_performance_task_data[0]);
						unset($import_written_work_data[0]);
						$this->db->insert_batch('import_grades_details', $import_data );
						$this->db->insert_batch('import_grades_quarterly_assessment', $import_quarterly_assessment_data );
						$this->db->insert_batch('import_grades_performance_task', $import_performance_task_data );
						$this->db->insert_batch('import_grades_written_work', $import_written_work_data );
						$this->session->set_flashdata('msg', '<div student="alert alert-success text-center">Students imported successfully</div>');	
					} else {
						$this->session->set_flashdata('msg', '<div student="alert alert-success text-center">Students imported unsuccessfully</div>');		
					}
				}
				redirect('teacher/grade/import'); 				
			}	
			
		}
	} */
	function import_grades() {
		ini_set('memory_limit', '-1');
		$student_session_data = $this->session->userdata("student");
		$teacher_id = $student_session_data['teacher_id'];
		$data['title'] = 'Import Grades';
		$data['title_list'] = 'Recently Added Grades';
		$this->session->set_userdata('top_menu', 'Examinations');
		$this->session->set_userdata('sub_menu', 'import/index');
		$session = $this->setting_model->getCurrentSession();
		// $class = $this->class_model->get();
		$student_session_data = $this->session->userdata("student"); 
		$teacher_id = $student_session_data['teacher_id']; 
		$class = $this->class_model->get_by_teacher( $teacher_id );
		$data['classlist'] = $class;
		$data['subjectlist'] = $this->subject_model->get();
		$data['getquarter'] = $this->customlib->getQuarter();
		$data['templatelist'] = $this->template_model->get();
		$setting_result = $this->setting_model->get();
        $import_grade_settings = $setting_result[0]['import_grade'];
        $data['import_grade_settings'] = $import_grade_settings;
        $data['firstqsettings'] = $setting_result[0]['import_first'];
		$data['secondqsettings'] = $setting_result[0]['import_second'];
		$data['thirdqsettings'] = $setting_result[0]['import_third'];
		$data['fourthqsettings'] = $setting_result[0]['import_fourth'];
		$this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
		$this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');
		$this->form_validation->set_rules('quarter', 'quarter', 'trim|required|xss_clean');
		$this->form_validation->set_rules('subject_id', 'Subject', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('layout/teacher/header', $data);
			$this->load->view('teacher/grade/import', $data);
			$this->load->view('layout/teacher/footer', $data);
		} else {
			$class_id = $this->input->post('class_id');
			$section_id = $this->input->post('section_id');
			$subject_id = $this->input->post('subject_id');
			$quarter = $this->input->post('quarter');
			//$getquartersheet = $quarter - 1;
			$session = $this->setting_model->getCurrentSession();
			$session_id = $this->setting_model->getCurrentSession();
			
			if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
				include ("application/third_party/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
				
				$inputFileName = $_FILES['file']['tmp_name'];
				//  Read your Excel workbook
				try {
					$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($inputFileName);
				} 
				
				catch(Exception $e) {
					die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
				}
				$getSheetNames = $objPHPExcel->getSheetNames();
				$getCurrentSheet = $this->get_assign_sheet_name( $quarter );
				$termSheetMap = array(1 => 'TERM1', 2 => 'TERM2', 3 => 'TERM3');
				$targetTermSheet = isset($termSheetMap[$quarter]) ? $termSheetMap[$quarter] : '';
				
				if(in_array( $getCurrentSheet, $getSheetNames )){
					$getquartersheet = array_search( $getCurrentSheet, $getSheetNames);
					$objPHPExcel->setActiveSheetIndex($getquartersheet);
				} elseif($targetTermSheet !== '' && in_array( $targetTermSheet, $getSheetNames )){
					$getquartersheet = array_search( $targetTermSheet, $getSheetNames);
					$objPHPExcel->setActiveSheetIndex($getquartersheet);
				} else {
					$this->session->set_flashdata('msg', '<div class="alert alert-warning">Sheet "'.$getCurrentSheet.'" or "'.$targetTermSheet.'" not found. Please import correct template.</div>');
					redirect('teacher/grade/import');
				}
				$sheetInsertData = $objPHPExcel->getActiveSheet();
					//$sheetInsertData = $sheetInsertData->getTitle();
					//$sheetInsertData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
					$foundInCells = $this->spout->search_cells_with_pt_wt( $inputFileName );
					if( !array_key_exists('{grade}', $foundInCells ) ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{grade} is missing !</div>');		
						redirect('teacher/grade/import'); 	
					}elseif( !array_key_exists('{start_boys}', $foundInCells ) ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{start_boys} is missing !</div>');		
						redirect('teacher/grade/import'); 
					}elseif( !array_key_exists('{end_boys}', $foundInCells ) ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{end_boys} is missing !</div>');		
						redirect('teacher/grade/import'); 
					}elseif( !array_key_exists('{start_girls}', $foundInCells ) ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{start_girls} is missing !</div>');		
						redirect('teacher/grade/import'); 	
					} elseif( !array_key_exists('{end_girls}', $foundInCells ) ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{end_girls} is missing !</div>');		
						redirect('teacher/grade/import'); 
					} elseif( !array_key_exists('{boys_start_id}', $foundInCells ) ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{boys_start_id} is missing !</div>');		
						redirect('teacher/grade/import'); 
					} elseif( !array_key_exists('{girls_start_id}', $foundInCells ) ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{girls_start_id} is missing !</div>');		
						redirect('teacher/grade/import'); 	
					} else {	
							$notification_array = array();
							$grade_list = isset($foundInCells['{grade}'][$getquartersheet])?$foundInCells['{grade}'][$getquartersheet]:null;
							if( $grade_list != null ){
								$grade_list_letters = $grade_list['columnnumber'];
								$grade_list_number = $grade_list['rownumber'];
							}
							
							$start_boys_list =  isset($foundInCells['{start_boys}'][$getquartersheet])?$foundInCells['{start_boys}'][$getquartersheet]:null;
							if( $start_boys_list != null ){
								$start_boys_letters = $start_boys_list['columnnumber'];
								$start_boys_number = $start_boys_list['rownumber'];
							}
								
							$end_boys_list = isset($foundInCells['{end_boys}'][$getquartersheet])?$foundInCells['{end_boys}'][$getquartersheet]:null;
							if( $end_boys_list != null ){
								$end_boys_letters = $end_boys_list['columnnumber'];
								$end_boys_number = $end_boys_list['rownumber'];
							}
							
							$boys_start_id_list = isset($foundInCells['{boys_start_id}'][$getquartersheet])?$foundInCells['{boys_start_id}'][$getquartersheet]:null;
							if( $boys_start_id_list != null ){
								$boys_start_id_letters = $boys_start_id_list['columnnumber'];
								$boys_start_id_number = $boys_start_id_list['rownumber'];
							}

							$start_girls_list = isset($foundInCells['{start_girls}'][$getquartersheet])?$foundInCells['{start_girls}'][$getquartersheet]:null;
							if( $start_girls_list != null ){
								$start_girls_letters = $start_girls_list['columnnumber'];
								$start_girls_number =  $start_girls_list['rownumber'];
							}

							$end_girls_list = isset($foundInCells['{end_girls}'][$getquartersheet])?$foundInCells['{end_girls}'][$getquartersheet]:null;
							if( $end_girls_list != null ){
								$end_girls_letters = $end_girls_list['columnnumber'];
								$end_girls_number = $end_girls_list['rownumber'];
							}
							
							$girls_start_id_list = isset($foundInCells['{girls_start_id}'][$getquartersheet])?$foundInCells['{girls_start_id}'][$getquartersheet]:null;
							if( $girls_start_id_list != null ){
								$girls_start_id_letters = $girls_start_id_list['columnnumber'];
								$girls_start_id_number = $girls_start_id_list['rownumber'];
							}
							
							$ww_data = array();
							$pa_data = array();
							$ini_grade_list = isset($foundInCells['{ini-grade}'][$getquartersheet])?$foundInCells['{ini-grade}'][$getquartersheet]:null;
							if( $ini_grade_list != null ){
								$ini_grade_letters = $ini_grade_list['columnnumber'];
								$ini_grade_number = $ini_grade_list['rownumber'];
							}
							
							/*$qaws_list = isset($foundInCells['{qaws}'][$getquartersheet])?$foundInCells['{qaws}'][$getquartersheet]:null;
							if( $qaws_list != null ){
								$qaws_letters = $qaws_list['columnnumber'];
								$qaws_number = $qaws_list['rownumber'];
							}

							$qaps_list = isset($foundInCells['{qaps}'][$getquartersheet])?$foundInCells['{qaps}'][$getquartersheet]:null;
							if( $qaps_list != null ){
								$qaps_letters = $qaps_list['columnnumber'];
								$qaps_number = $qaps_list['rownumber'];
							}
							
							$qa_list = isset($foundInCells['{qa}'][$getquartersheet])?$foundInCells['{qa}'][$getquartersheet]:null;
							if( $qa_list != null ){
								$qa_letters = $qa_list['columnnumber'];
								$qa_number = $qa_list['rownumber'];
							}
							
							$ptws_list = isset($foundInCells['{ptws}'][$getquartersheet])?$foundInCells['{ptws}'][$getquartersheet]:null;
							if( $ptws_list != null ){
								$ptws_letters = $ptws_list['columnnumber'];
								$ptws_number = $ptws_list['rownumber'];
							}
							
							$ptps_list = isset($foundInCells['{ptps}'][$getquartersheet])?$foundInCells['{ptps}'][$getquartersheet]:null;
							if( $ptps_list != null ){
								$ptps_letters = $ptps_list['columnnumber'];
								$ptps_number = $ptps_list['rownumber'];
							}
							
							$ptt_list = isset($foundInCells['{ptt}'][$getquartersheet])?$foundInCells['{ptt}'][$getquartersheet]:null;
							if( $ptt_list != null ){
								$ptt_letters = $ptt_list['columnnumber'];
								$ptt_number = $ptt_list['rownumber'];
							}
							$pt_list = isset($foundInCells['{pt}'][$getquartersheet])?$foundInCells['{pt}'][$getquartersheet]:null;
							if( $pt_list != null ){
								$pt_letters = $pt_list['columnnumber'];
								$pt_number = $pt_list['rownumber'];
							}
							$wwws_list = isset($foundInCells['{wwws}'][$getquartersheet])?$foundInCells['{wwws}'][$getquartersheet]:null;
							if( $wwws_list != null ){
								$wwws_letters = $wwws_list['columnnumber'];
								$wwws_number = $wwws_list['rownumber'];
							}
							
							$wwps_list = isset($foundInCells['{wwps}'][$getquartersheet])?$foundInCells['{wwps}'][$getquartersheet]:null;
							if( $wwps_list != null ){
								$wwps_letters = $wwps_list['columnnumber'];
								$wwps_number = $wwps_list['rownumber'];
							}
							$wwt_list = isset($foundInCells['{wwt}'][$getquartersheet])?$foundInCells['{wwt}'][$getquartersheet]:null;
							if( $wwt_list != null ){
								$wwt_letters = $wwt_list['columnnumber'];
								$wwt_number = $wwt_list['rownumber'];
							}
							$ww_list = isset($foundInCells['{wwt}'][$getquartersheet])?$foundInCells['{wwt}'][$getquartersheet]:null;
							if( $ww_list != null ){
								$ww_letters = $ww_list['columnnumber'];
								$ww_number = $ww_list['rownumber'];
							}*/
				
							if( isset($getquartersheet) ){
								$grades = array();
								$highestRow = $end_boys_number - 1;
								$startRow = $start_boys_number + 1;
								//$grades_f[]= null;
								for($row = $startRow;$row <= $highestRow;$row++){
									//$grades['id']  = $sheetInsertData->getCellByColumnAndRow(1,$row)->getOldCalculatedValue();
									$get_id  = $sheetInsertData->getCellByColumnAndRow($boys_start_id_letters,$row)->getValue();
									if( $get_id == 0 ){
										$get_id  = $sheetInsertData->getCellByColumnAndRow($boys_start_id_letters,$row)->getOldCalculatedValue();
									}
									if( $get_id ){
										$check_exist = $this->student_model->getIDByClassSection($get_id, $class_id );
										if( $check_exist ){
											$grades['id']  = $check_exist;
											$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getOldCalculatedValue();
											if( $get_grades == 0 ){
												$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
												$get_grades = is_numeric( $get_grades )?$get_grades:'';	
											}
											$grades['grade'] = $get_grades;
											
											/*if( $ini_grade_list != null ){
												$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $ini_grade_letters,$row)->getOldCalculatedValue();
												if( $get_initial_grade == 0 ){
													$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
												}
												$grades['initial_grade'] = $get_initial_grade;
											}
										
											if( $qa_list != null ){
												$qa = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getOldCalculatedValue();
												if( $qa == 0 ){
													$qa = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getValue();
												}
												$grades['qa'] = $qa;
											}
											if( $qaws_list != null ){
												$qaws = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getOldCalculatedValue();
												if( $qaws == 0 ){
													$qaws = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getValue();
												}
												$grades['qaws'] = $qaws;
											}
										
											
											if( $qaps_list != null ){
												$qaps = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getOldCalculatedValue();
												if( $qaps == 0 ){
													$qaps = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getValue();
												}
												$grades['qaps'] = $qaps;
											}
												
											if( $pt_list != null){
												for ($pi = $pt_letters; $pi !== $wwws_letters ; $pi--){
													$get_pa_data = $sheetInsertData->getCellByColumnAndRow( $pi,$row)->getOldCalculatedValue();
													if( $get_pa_data == 0 ){
														$get_pa_data = $sheetInsertData->getCellByColumnAndRow( $pi,$row)->getValue();
													}
													$pa_data[] = $get_pa_data;
												}
												$grades['pt'] =   implode(",", $pa_data);
												
											}
											
											if( $ww_list != null){
												for ($wi = $ww_letters-1; $wi !== $start_boys_letters + 1 ; $wi--){
													$get_ww_data = $sheetInsertData->getCellByColumnAndRow( $wi,$row)->getOldCalculatedValue();
													if( $get_ww_data == 0 ){
														$get_ww_data = $sheetInsertData->getCellByColumnAndRow( $wi,$row)->getValue();
													}
													$ww_data[] = $get_ww_data;
												}
												$grades['ww'] = implode(",", $ww_data);
											}
											if( $ptt_list != null ){
												$ptt = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getOldCalculatedValue();
												if( $ptt == 0 ){
													$ptt = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getValue();
												}
												$grades['ptt'] = $ptt;
											}
											if( $ptws_list != null ){
												$ptws = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getOldCalculatedValue();
												if( $ptws == 0 ){
													$ptws = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getValue();
												}
												$grades['ptws'] = $ptws;
											}
											if( $ptps_list != null ){
												$ptps = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getOldCalculatedValue();
												if( $ptps == 0 ){
													$ptps = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getValue();
												}
												$grades['ptps'] = $ptps;
											}
											if( $wwt_list != null ){
												$wwt = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getOldCalculatedValue();
												if( $wwt == 0 ){
													$wwt = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getValue();
												}
												$grades['wwt'] = $wwt;
												
											}
											if( $wwt_list != null ){
												$wwws = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getOldCalculatedValue();
												if( $wwws == 0 ){
													$wwws = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getValue();
												}
												$grades['wwws'] = $wwws;
											}
											if( $wwps_list != null ){
												$wwps = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getOldCalculatedValue();
												if( $wwps == 0 ){
													$wwps = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getValue();
												}
												$grades['wwps'] = $wwps;
											}
											*/
											
											if($grades['id'] == null ){
												break;
												
											} else {
												$grades_f[] = $grades;
											}
											
											/*unset( $pa_data );
											unset( $ww_data );
											unset( $ww );
											unset( $pt );*/
										} else {
											$notification_array[] = '<div class="alert alert-warning">In Row '.$row.' student does not exist/does not belong to this grade level.'.'</div>';	
										}
									}
									
								}
							
							
								$highestRow = $end_girls_number - 1;
								$startRow = $start_girls_number + 1; 
								for($row = $startRow;$row <= $highestRow;$row++)
								{
									$get_id = $sheetInsertData->getCellByColumnAndRow($girls_start_id_letters,$row)->getValue();
									if( $get_id == 0 ){
										$get_id = $sheetInsertData->getCellByColumnAndRow($girls_start_id_letters,$row)->getOldCalculatedValue();
									}
									if( $get_id ){
										$check_exist = $this->student_model->getIDByClassSection($get_id, $class_id );
										if( $check_exist ){
											$grades['id']  = $check_exist;
											$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getOldCalculatedValue();
											if( $get_grades == 0 ){
												$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
											}
											$grades['grade'] = $get_grades;
											
											if( $ini_grade_list != null ){
												$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $ini_grade_letters,$row)->getOldCalculatedValue();
												if( $get_initial_grade == 0 ){
													$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
												}
												$grades['initial_grade'] = $get_initial_grade;
											}
										
											/*if( $qa_list != null ){
												$qa = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getOldCalculatedValue();
												if( $qa == 0 ){
													$qa = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getValue();
												}
												$grades['qa'] = $qa;
											}
											if( $qaws_list != null ){
												$qaws = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getOldCalculatedValue();
												if( $qaws == 0 ){
													$qaws = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getValue();
												}
												$grades['qaws'] = $qaws;
											}
										
											
											if( $qaps_list != null ){
												$qaps = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getOldCalculatedValue();
												if( $qaps == 0 ){
													$qaps = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getValue();
												}
												$grades['qaps'] = $qaps;
											}
												
											if( $pt_list != null){
												for ($pi = $pt_letters; $pi !== $wwws_letters ; $pi--){
													$get_pa_data = $sheetInsertData->getCellByColumnAndRow( $pi,$row)->getOldCalculatedValue();
													if( $get_pa_data == 0 ){
														$get_pa_data = $sheetInsertData->getCellByColumnAndRow( $pi,$row)->getValue();
													}
													$pa_data[] = $get_pa_data;
												}
												$grades['pt'] =   implode(",", $pa_data);
											}
											
											if( $ww_list != null){
												for ($wi = $ww_letters-1; $wi !== $start_boys_letters + 1 ; $wi--){
													$get_ww_data = $sheetInsertData->getCellByColumnAndRow( $wi,$row)->getOldCalculatedValue();
													if( $get_ww_data == 0 ){
														$get_ww_data = $sheetInsertData->getCellByColumnAndRow( $wi,$row)->getValue();
													}
													$ww_data[] = $get_ww_data;
												}
												$grades['ww'] = implode(",", $ww_data);
											}
											if( $ptt_list != null ){
												$ptt = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getOldCalculatedValue();
												if( $ptt == 0 ){
													$ptt = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getValue();
												}
												$grades['ptt'] = $ptt;
											}
											if( $ptws_list != null ){
												$ptws = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getOldCalculatedValue();
												if( $ptws == 0 ){
													$ptws = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getValue();
												}
												$grades['ptws'] = $ptws;
											}
											if( $ptps_list != null ){
												$ptps = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getOldCalculatedValue();
												if( $ptps == 0 ){
													$ptps = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getValue();
												}
												$grades['ptps'] = $ptps;
											}
											if( $wwt_list != null ){
												$wwt = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getOldCalculatedValue();
												if( $wwt == 0 ){
													$wwt = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getValue();
												}
												$grades['wwt'] = $wwt;
											}
											if( $wwt_list != null ){
												$wwws = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getOldCalculatedValue();
												if( $wwws == 0 ){
													$wwws = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getValue();
												}
												$grades['wwws'] = $wwws;
											}
											if( $wwps_list != null ){
												$wwps = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getOldCalculatedValue();
												if( $wwps == 0 ){
													$wwps = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getValue();
												}
												$grades['wwps'] = $wwps;
											}
											*/
											
											if($grades['id'] == null ){
												break;
											} else {
												$grades_f[] = $grades;
											}
											
											/*unset( $pa_data );
											unset( $ww_data );
											unset( $ww );
											unset( $pt );*/
										} else {
											$notification_array[] = '<div class="alert alert-warning">In Row '.$row.' student does not exist/does not belong to this grade level.'.'</div>';	
										}
										
									}
								}
							}
							
							$insert_data_array = array(
								'created_at'=> date('Y-m-d h:i:s'),
								'subject_id'=> $subject_id,
								'class_id'=> $class_id,
								'section_id'=> $section_id,
								'session_id'=> $session_id,
								'quarter'=> $quarter,
								'teacher_id'=> $teacher_id
							);
							$this->db->insert('import_grades_batch', $insert_data_array );
							$get_batch_id = $this->db->insert_id();
								
							$import_data[] = null;
							$import_quarterly_assessment_data[] = null;
							$import_performance_task_data[] = null;
							$import_written_work_data[] = null;
							$x = 0;
						
							if(isset($grades_f) )
							{
								foreach( $grades_f as $key[0] => $value ){
									if( isset( $value['id']) && $value['id'] != 0 ){
										$x++;
										$id = $value['id'];
										$getByStudentSubjectQuarter = $this->importgradesdetails_model->getByStudentSubjectQuarter( $id, $subject_id, $quarter );
										if( empty($getByStudentSubjectQuarter )){
												$import_data[] = array(
												'batch_id' => $get_batch_id,
												'class_id' => $class_id,
												'section_id' => $section_id,
												'quarter' => $quarter,
												'subject_id' => $subject_id,
												'student_id' => $id,
												'grades' => isset($value['grade'])?$value['grade']:0,
												'initial_grade' => isset($value['initial_grade'])?$value['initial_grade']:0,
												);	
												/*$import_quarterly_assessment_data[] = array(
													'batch_id' => $get_batch_id,
													'class_id' => $class_id,
													'section_id' => $section_id,
													'quarter' => $quarter,
													'subject_id' => $subject_id,
													'student_id' => $id,
													'scores' => isset($value['qa'])?$value['qa']:0, 
													'ps' => isset($value['qaps'])?$value['qaps']:0, 
													'ws' => isset($value['qaws'])?$value['qaws']:0, 
												);	 

												$import_performance_task_data[] = array(
													'batch_id' => $get_batch_id,
													'class_id' => $class_id,
													'section_id' => $section_id,
													'quarter' => $quarter,
													'subject_id' => $subject_id,
													'student_id' => $id,
													'scores' => isset($value['pt'])?$value['pt']:0, 
													'total' => isset($value['ptt'])?$value['ptt']:0, 
													'ps' =>  isset($value['ptps'])?$value['ptps']:0, 
													'ws' =>  isset($value['ptws'])?$value['ptws']:0,
												);	 

												$import_written_work_data[] = array(
													'batch_id' => $get_batch_id,
													'class_id' => $class_id,
													'section_id' => $section_id,
													'quarter' => $quarter,
													'subject_id' => $subject_id,
													'student_id' => $id,
													'scores' => isset($value['ww'])?$value['ww']:0,
													'total' => isset($value['wwt'])?$value['wwt']:0,
													'ps' => isset($value['wwps'])?$value['wwps']:0,
													'ws' => isset($value['wwws'])?$value['wwws']:0,
												);	*/
										 } else {
											 $published_grade = $getByStudentSubjectQuarter['published'];
											 $student_details = $this->student_model->get( $id );
											 $student_firstname = $student_details['firstname'];
											 $student_lastname = $student_details['lastname'];
											 if( $published_grade ){
												$notification_array[] = '<div class="alert alert-warning">In Row '.$x.' student <b>'.$student_lastname.', '.$student_firstname.'</b> submitted grade already. Please create a request to update it.</div>';	 
											 } else {
												$notification_array[] = '<div class="alert alert-danger">In Row '.$x.' student <b>'.$student_lastname.', '.$student_firstname.'</b> duplicate grade entry. Please delete it first before you import it again.</div>';	  
											 }
												 
										 }
									}
								}
							} 
							
							if( count($import_data) > 1 ){
								unset($import_data[0]);
								/*unset($import_quarterly_assessment_data[0]);
								unset($import_performance_task_data[0]);
								unset($import_written_work_data[0]);*/
								$this->db->insert_batch('import_grades_details', $import_data );
								/*$this->db->insert_batch('import_grades_quarterly_assessment', $import_quarterly_assessment_data );
								$this->db->insert_batch('import_grades_performance_task', $import_performance_task_data );
								$this->db->insert_batch('import_grades_written_work', $import_written_work_data );*/
								$notification_array[] = '<div class="alert alert-success">Students grade imported successfully.</div>';	
								if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
									$uploaddir = './uploads/template_documents/excel/';
									if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
										die("Error creating folder $uploaddir");
									}
									$filename = 'b'.$get_batch_id.'-'.$_FILES['file']['name'];
									$filename = str_replace(" ", "-", $filename );
									$img_name = $uploaddir . basename($filename);
									move_uploaded_file($_FILES["file"]["tmp_name"], $img_name);
									$this->importgradesbatch_model->add( array( 'id' => $get_batch_id, 'template' => $img_name ));
								} 
							} else {
								$notification_array[] = '<div class="alert alert-warning">Students grade imported unsuccessfully.</div>';		
								$this->importgradesbatch_model->remove( $get_batch_id );
							}
							
							if( count($notification_array) > 0 ){
								$this->session->set_flashdata('msg', $notification_array);		
							}
						} 
					

				redirect('teacher/grade/import'); 				
			} else {
				$this->session->set_flashdata('msg', '<div class="alert alert-warning">No file attached!</div>');		
				redirect('teacher/grade/import'); 	 	
			}
			
		}
	}
	
	
	public function imported() {
		$student_session_data = $this->session->userdata("student");
        $teacher_id = $student_session_data['teacher_id'];
        $this->session->set_userdata('top_menu', 'Examinations');
        $this->session->set_userdata('sub_menu', 'grades/imported');
        $importgradesbatch = $this->importgradesbatch_model->get( null, $teacher_id );
        $data['importgradesbatch'] = $importgradesbatch;
        $this->load->view('layout/teacher/header');
        $this->load->view('teacher/grade/imported', $data);
        $this->load->view('layout/teacher/footer');
    }
	
	public function view_batch( $id ){
		$data['title'] = 'Fees Master List';
		$student_session_data = $this->session->userdata("student");
        $teacher_id = $student_session_data['teacher_id'];
        $importgradesdetails = $this->importgradesdetails_model->getAllDetailsByBatch( $id );
		$importgrades = $this->importgradesbatch_model->get( $id, $teacher_id );
		
        $data['id'] = $id;
        $data['importgrades'] = $importgrades;
       // $data['importgradesdetails'] = $importgradesdetails;
		$importgradesdetails = array();
		$studentlist_male = $this->importgradesdetails_model->getAllDetailsByBatchGender( $id, 'Male' );
		$studentlist_female = $this->importgradesdetails_model->getAllDetailsByBatchGender( $id, 'Female' );
		$data['studentlist_male'] = $studentlist_male;
		$data['studentlist_female'] = $studentlist_female;
			 
        $this->load->view('layout/teacher/header');
        $this->load->view('teacher/grade/viewbatch', $data);
        $this->load->view('layout/teacher/footer');	
	}
	
	public function delete_batch( $id ){
		$data['title'] = 'Fees Master List';
		$batch_details = $this->importgradesbatch_model->getbyID( $id );
		$template = $batch_details['template'];
		if( file_exists($template) ){
			unlink($template);
		}
        $this->importgradesbatch_model->remove($id);
        $this->importgradesdetails_model->removeByBatch($id);
		$this->importgradesperformance_model->deleteByBatch($id);
        $this->importgradesquarterly_model->deleteByBatch($id);
        $this->importgradeswritten_model->deleteByBatch($id); 
		
		$this->session->set_flashdata('msg', '<div class="alert alert-success">Delete data successfully</div>');
        redirect('teacher/grade/imported');	
	}
	
	public function generate_spreadsheet(){
		$class = $this->class_model->get();
		$data['classlist'] = $class;
		$data['subjectlist'] = $this->subject_model->get();
		$data['getquarter'] = $this->customlib->getQuarter();
		$data['templatelist'] = $this->template_model->get();
		$setting_result = $this->setting_model->get();
		$import_grade_settings = $setting_result[0]['import_grade'];
		$data['import_grade_settings'] = $import_grade_settings;
		$data['firstqsettings'] = $setting_result[0]['import_first'];
		$data['secondqsettings'] = $setting_result[0]['import_second'];
		$data['thirdqsettings'] = $setting_result[0]['import_third'];
		$data['fourthqsettings'] = $setting_result[0]['import_fourth'];
		$this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
		$this->form_validation->set_rules('section_id', 'Section', 'trim|xss_clean');
		$this->form_validation->set_rules('subject_id', 'Subject', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('layout/teacher/header', $data);
			$this->load->view('teacher/grade/import', $data);
			$this->load->view('layout/teacher/footer', $data);
		} else {
			// Check if this is an AJAX request
			if ($this->input->is_ajax_request()) {
				// AJAX request - start generation and return job ID immediately
				$class_id = $this->input->post('class_id');
				$section_id = $this->input->post('section_id');
				$subject_id = $this->input->post('subject_id');
				$template_id = $this->input->post('template_id');
				
				// Generate unique job ID
				$job_id = 'job_' . date('Ymdhis') . '_' . uniqid();
				
				// Create job status file in non-public cache directory
				$status_dir = APPPATH . 'cache/grade_jobs';
				if (!is_dir($status_dir)) {
					mkdir($status_dir, 0755, true);
				}
				
				$status_file = $status_dir . '/' . $job_id . '.json';
				file_put_contents($status_file, json_encode([
					'status' => 'processing',
					'progress' => 0,
					'started_at' => date('Y-m-d H:i:s')
				]));
				
				// Return job ID immediately
				header('Content-Type: application/json');
				echo json_encode([
					'status' => 'started',
					'job_id' => $job_id,
					'message' => 'Generation started in background'
				]);
				
				// Close connection to browser but keep PHP running
				if (function_exists('fastcgi_finish_request')) {
					fastcgi_finish_request();
				} else {
					ignore_user_abort(true);
					ob_end_flush();
					flush();
				}
				
				// Now generate the file in background
				$parameters = array(
					'class_id' => $class_id,
					'section_id' => $section_id,
					'subject_id' => $subject_id,
					'template_id' => $template_id,
					'job_id' => $job_id,
					'status_file' => $status_file
				);
				$this->excelwithspout->generate_spreadsheet($parameters);
				
			} else {
				// Regular form submission (fallback)
				$class_id = $this->input->post('class_id');
				$section_id = $this->input->post('section_id');
				$subject_id = $this->input->post('subject_id');
				$template_id = $this->input->post('template_id');
				$parameters = array(
					'class_id' => $class_id,
					'section_id' => $section_id,
					'subject_id' => $subject_id,
					'template_id' => $template_id
				);
				$this->excelwithspout->generate_spreadsheet($parameters);
			}
		}
	}
	
	// AJAX endpoint to check job status
	public function check_job_status() {
		$job_id = $this->input->get('job_id');
		if (empty($job_id)) {
			header('Content-Type: application/json');
			echo json_encode(['status' => 'error', 'message' => 'No job ID provided']);
			return;
		}
		
		// Status files are stored in non-public app cache directory
		$status_file = APPPATH . 'cache/grade_jobs/' . $job_id . '.json';
		
		header('Content-Type: application/json');
		if (file_exists($status_file)) {
			$status_data = json_decode(file_get_contents($status_file), true);
			// NEVER expose temp_file server path to the browser
			unset($status_data['temp_file']);
			echo json_encode($status_data);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Job not found']);
		}
	}

	// Stream the generated file directly to browser, then delete it
	// This endpoint is hit once — it reads temp file, streams it, deletes it
	public function download_file($job_id = '') {
		$job_id = trim($job_id);
		if (empty($job_id)) {
			show_error('No job ID provided.', 400);
			return;
		}

		$status_file = APPPATH . 'cache/grade_jobs/' . $job_id . '.json';

		if (!file_exists($status_file)) {
			show_error('Download expired or not found. Please generate the file again.', 404);
			return;
		}

		$status_data = json_decode(file_get_contents($status_file), true);

		if (!isset($status_data['status']) || $status_data['status'] !== 'complete') {
			show_error('File is not ready yet. Please wait for generation to complete.', 409);
			return;
		}

		$temp_file = isset($status_data['temp_file']) ? $status_data['temp_file'] : '';
		$filename  = isset($status_data['filename'])  ? $status_data['filename']  : 'grade_spreadsheet.xlsx';

		if (empty($temp_file) || !file_exists($temp_file)) {
			// Clean up stale status file
			@unlink($status_file);
			show_error('Generated file no longer exists. Please generate again.', 410);
			return;
		}

		$file_size = filesize($temp_file);

		// Stream the file to browser
		while (ob_get_level()) {
			ob_end_clean();
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Length: ' . $file_size);
		header('Cache-Control: max-age=0');
		readfile($temp_file);

		// Delete both files immediately after streaming — no storage accumulation
		@unlink($temp_file);
		@unlink($status_file);
		exit;
	}
	
	public function submit($id) {
        $data['title'] = 'Fees Master List';
		$update_data = array(
			'id' => $id, 
			'published' => 1
		);
        $update = $this->importgradesbatch_model->add($update_data);
		$this->session->set_flashdata('msg', '<div class="alert alert-success">Grades submitted successfully.</div>');	
        redirect('teacher/grade/imported');
    }
	
	public function generate_spreadsheet_custom(){
		$class = $this->class_model->get();
		$data['classlist'] = $class;
		$data['subjectlist'] = $this->subject_model->get();
		$data['getquarter'] = $this->customlib->getQuarter();
		$data['templatelist'] = $this->template_model->get();
		$setting_result = $this->setting_model->get();
        $import_grade_settings = $setting_result[0]['import_grade'];
        $data['import_grade_settings'] = $import_grade_settings;
        $data['firstqsettings'] = $setting_result[0]['import_first'];
		$data['secondqsettings'] = $setting_result[0]['import_second'];
		$data['thirdqsettings'] = $setting_result[0]['import_third'];
		$data['fourthqsettings'] = $setting_result[0]['import_fourth'];
		$this->form_validation->set_rules('student_id', 'Student', 'trim|required|xss_clean');
		$this->form_validation->set_rules('semester_id', 'Semester', 'trim|required|xss_clean');
		$this->form_validation->set_rules('quarter', 'Quarter', 'trim|required|xss_clean');
		$this->form_validation->set_rules('subject_id', 'Subject', 'trim|required|xss_clean');
		$this->form_validation->set_rules('template_id', 'Template', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('layout/teacher/header', $data);
			$this->load->view('teacher/grade/import', $data);
			$this->load->view('layout/teacher/footer', $data);
		} else {
			$student_id = $this->input->post('student_id');
			$subject_id = $this->input->post('subject_id');	
			$template_id = $this->input->post('template_id');	
			$parameters = array(
				'student_id' => $student_id,
				'subject_id' => $subject_id,
				'template_id' => $template_id
			);
			while (ob_get_level()) {
				ob_end_clean();
			}
			$this->excelwithspout->generate_spreadsheet_custom( $parameters );
			exit;
		}
	}
	
	public function importcustomgrade() {
		ini_set('memory_limit', '-1');
		$student_session_data = $this->session->userdata("student");
		$teacher_id = $student_session_data['teacher_id'];
		$data['title'] = 'Import Custom Grades';
		$data['title_list'] = 'Recently Import Custom Grades';
		$this->session->set_userdata('top_menu', 'Examinations');
       	$this->session->set_userdata('sub_menu', 'importcustom/index');
		$session = $this->setting_model->getCurrentSession();
		// $class = $this->class_model->get();
		$student_session_data = $this->session->userdata("student"); 
		$teacher_id = $student_session_data['teacher_id']; 
		$class = $this->class_model->get_by_teacher( $teacher_id );
		$session = $this->setting_model->getCurrentSession();
		$data['getSemester'] = $this->customlib->getSemester();
		$data['studentList'] = $this->customsubject_model->getStudent( null,$class);
		$data['subjectlist'] = $this->subject_model->get();
		$data['templatelist'] = $this->template_model->get();
		$data['getquarter'] = $this->customlib->getQuarter();
		$this->form_validation->set_rules('student_id', 'Student', 'trim|required|xss_clean');
		$this->form_validation->set_rules('semester_id', 'Semester', 'trim|required|xss_clean');
		$this->form_validation->set_rules('quarter', 'Quarter', 'trim|required|xss_clean');
		$this->form_validation->set_rules('subject_id', 'Subject', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('layout/teacher/header', $data);
			$this->load->view('teacher/grade/importcustomgrade', $data);
			$this->load->view('layout/teacher/footer', $data);
		} else {
			$student_id = $this->input->post('student_id');
			$student_details = $this->student_model->get($student_id);
			$class_id = $student_details['class_id'];
			$section_id = $student_details['section_id'];
			$subject_id = $this->input->post('subject_id');
			$quarter = $this->input->post('quarter');
			//$getquartersheet = $quarter - 1;
			$session = $this->setting_model->getCurrentSession();
			
			if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
				include ("application/third_party/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
				
				$inputFileName = $_FILES['file']['tmp_name'];
				//  Read your Excel workbook
				try {
					$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($inputFileName);
				} 
				
				catch(Exception $e) {
					die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
				}
				$getSheetNames = $objPHPExcel->getSheetNames();
				$getCurrentSheet = $this->get_assign_sheet_name( $quarter );
				$termSheetMap = array(1 => 'TERM1', 2 => 'TERM2', 3 => 'TERM3');
				$targetTermSheet = isset($termSheetMap[$quarter]) ? $termSheetMap[$quarter] : '';
				
				if(in_array( $getCurrentSheet, $getSheetNames )){
					$getquartersheet = array_search( $getCurrentSheet, $getSheetNames);
					$objPHPExcel->setActiveSheetIndex($getquartersheet);
				} elseif($targetTermSheet !== '' && in_array( $targetTermSheet, $getSheetNames )){
					$getquartersheet = array_search( $targetTermSheet, $getSheetNames);
					$objPHPExcel->setActiveSheetIndex($getquartersheet);
				} else {
					$this->session->set_flashdata('msg', '<div class="alert alert-warning">Sheet "'.$getCurrentSheet.'" or "'.$targetTermSheet.'" not found. Please import correct template.</div>');
					redirect('admin/grade/importcustomgrade');
				}
				$sheetInsertData = $objPHPExcel->getActiveSheet();
					//$sheetInsertData = $sheetInsertData->getTitle();
					//$sheetInsertData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
					
					$foundInCells = $this->spout->search_cells_with_pt_wt( $inputFileName );
					$grade_list = isset($foundInCells['{grade}'][$getquartersheet])?$foundInCells['{grade}'][$getquartersheet]:null;
					
					if( $grade_list != null ){
						$grade_list_letters = $grade_list['columnnumber'];
						$grade_list_number = $grade_list['rownumber'];
					}
					
					$start_boys_list =  isset($foundInCells['{start_boys}'][$getquartersheet])?$foundInCells['{start_boys}'][$getquartersheet]:null;
					if( $start_boys_list != null ){
						$start_boys_letters = $start_boys_list['columnnumber'];
						$start_boys_number = $start_boys_list['rownumber'];
					}
						
					$end_boys_list = isset($foundInCells['{end_boys}'][$getquartersheet])?$foundInCells['{end_boys}'][$getquartersheet]:null;
					if( $end_boys_list != null ){
						$end_boys_letters = $end_boys_list['columnnumber'];
						$end_boys_number = $end_boys_list['rownumber'];
					}
					
					$boys_start_id_list = isset($foundInCells['{boys_start_id}'][$getquartersheet])?$foundInCells['{boys_start_id}'][$getquartersheet]:null;
					if( $boys_start_id_list != null ){
						$boys_start_id_letters = $boys_start_id_list['columnnumber'];
						$boys_start_id_number = $boys_start_id_list['rownumber'];
					}

					$start_girls_list = isset($foundInCells['{start_girls}'][$getquartersheet])?$foundInCells['{start_girls}'][$getquartersheet]:null;
					if( $start_girls_list != null ){
						$start_girls_letters = $start_girls_list['columnnumber'];
						$start_girls_number =  $start_girls_list['rownumber'];
					}

					$end_girls_list = isset($foundInCells['{end_girls}'][$getquartersheet])?$foundInCells['{end_girls}'][$getquartersheet]:null;
					if( $end_girls_list != null ){
						$end_girls_letters = $end_girls_list['columnnumber'];
						$end_girls_number = $end_girls_list['rownumber'];
					}
					
					$girls_start_id_list = isset($foundInCells['{girls_start_id}'][$getquartersheet])?$foundInCells['{girls_start_id}'][$getquartersheet]:null;
					if( $girls_start_id_list != null ){
						$girls_start_id_letters = $girls_start_id_list['columnnumber'];
						$girls_start_id_number = $girls_start_id_list['rownumber'];
					}
					
					
					
					if( $grade_list == null ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{grade} is missing !</div>');		
						redirect('admin/grade/importcustomgrade');
					}elseif( $start_boys_list == null ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{start_boys} is missing !</div>');		
						redirect('admin/grade/importcustomgrade');
					}elseif( $end_boys_list == null ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{end_boys} is missing !</div>');		
						redirect('admin/grade/importcustomgrade');
					}elseif( $start_girls_list == null ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{start_girls} is missing !</div>');		
						redirect('admin/grade/importcustomgrade');
					} elseif( $end_girls_list == null ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{end_girls} is missing !</div>');		
						redirect('admin/grade/importcustomgrade');
					} elseif( $boys_start_id_list == null ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{boys_starts_id} is missing !</div>');		
					} elseif( $girls_start_id_list == null ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{girls_start_id} is missing !</div>');		
						redirect('admin/grade/importcustomgrade');
					} else {
						$ww_data = array();
						$pa_data = array();
						$ini_grade_list = isset($foundInCells['{ini-grade}'][$getquartersheet])?$foundInCells['{ini-grade}'][$getquartersheet]:null;
						if( $ini_grade_list != null ){
							$ini_grade_letters = $ini_grade_list['columnnumber'];
							$ini_grade_number = $ini_grade_list['rownumber'];
						}
						
						$qaws_list = isset($foundInCells['{qaws}'][$getquartersheet])?$foundInCells['{qaws}'][$getquartersheet]:null;
						if( $qaws_list != null ){
							$qaws_letters = $qaws_list['columnnumber'];
							$qaws_number = $qaws_list['rownumber'];
						}

						$qaps_list = isset($foundInCells['{qaps}'][$getquartersheet])?$foundInCells['{qaps}'][$getquartersheet]:null;
						if( $qaps_list != null ){
							$qaps_letters = $qaps_list['columnnumber'];
							$qaps_number = $qaps_list['rownumber'];
						}
						
						$qa_list = isset($foundInCells['{qa}'][$getquartersheet])?$foundInCells['{qa}'][$getquartersheet]:null;
						if( $qa_list != null ){
							$qa_letters = $qa_list['columnnumber'];
							$qa_number = $qa_list['rownumber'];
						}
						
						$ptws_list = isset($foundInCells['{ptws}'][$getquartersheet])?$foundInCells['{ptws}'][$getquartersheet]:null;
						if( $ptws_list != null ){
							$ptws_letters = $ptws_list['columnnumber'];
							$ptws_number = $ptws_list['rownumber'];
						}
						
						$ptps_list = isset($foundInCells['{ptps}'][$getquartersheet])?$foundInCells['{ptps}'][$getquartersheet]:null;
						if( $ptps_list != null ){
							$ptps_letters = $ptps_list['columnnumber'];
							$ptps_number = $ptps_list['rownumber'];
						}
						
						$ptt_list = isset($foundInCells['{ptt}'][$getquartersheet])?$foundInCells['{ptt}'][$getquartersheet]:null;
						if( $ptt_list != null ){
							$ptt_letters = $ptt_list['columnnumber'];
							$ptt_number = $ptt_list['rownumber'];
						}
						$pt_list = isset($foundInCells['{pt}'][$getquartersheet])?$foundInCells['{pt}'][$getquartersheet]:null;
						if( $pt_list != null ){
							$pt_letters = $pt_list['columnnumber'];
							$pt_number = $pt_list['rownumber'];
						}
						$wwws_list = isset($foundInCells['{wwws}'][$getquartersheet])?$foundInCells['{wwws}'][$getquartersheet]:null;
						if( $wwws_list != null ){
							$wwws_letters = $wwws_list['columnnumber'];
							$wwws_number = $wwws_list['rownumber'];
						}
						
						$wwps_list = isset($foundInCells['{wwps}'][$getquartersheet])?$foundInCells['{wwps}'][$getquartersheet]:null;
						if( $wwps_list != null ){
							$wwps_letters = $wwps_list['columnnumber'];
							$wwps_number = $wwps_list['rownumber'];
						}
						$wwt_list = isset($foundInCells['{wwt}'][$getquartersheet])?$foundInCells['{wwt}'][$getquartersheet]:null;
						if( $wwt_list != null ){
							$wwt_letters = $wwt_list['columnnumber'];
							$wwt_number = $wwt_list['rownumber'];
						}
						$ww_list = isset($foundInCells['{wwt}'][$getquartersheet])?$foundInCells['{wwt}'][$getquartersheet]:null;
						if( $ww_list != null ){
							$ww_letters = $ww_list['columnnumber'];
							$ww_number = $ww_list['rownumber'];
						}
					
						if( isset($getquartersheet) ){
							$grades = array();
							$highestRow = $end_boys_number - 1;
							$startRow = $start_boys_number + 1;
							//$grades_f[]= null;
							for($row = $startRow;$row <= $highestRow;$row++)
							{
								//$grades['id']  = $sheetInsertData->getCellByColumnAndRow(1,$row)->getOldCalculatedValue();
								$get_id  = $sheetInsertData->getCellByColumnAndRow($boys_start_id_letters,$row)->getValue();
								if( $get_id == 0 ){
									$get_id  = $sheetInsertData->getCellByColumnAndRow($boys_start_id_letters,$row)->getOldCalculatedValue();
								}
								if( $get_id ){
									$grades['id']  = $get_id;
									$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getOldCalculatedValue();
									if( $get_grades == 0 ){
										$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
										$get_grades = is_numeric( $get_grades )?$get_grades:'';	
									}
									$grades['grade'] = $get_grades;
									
									if( $ini_grade_list != null ){
										$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $ini_grade_letters,$row)->getOldCalculatedValue();
										if( $get_initial_grade == 0 ){
											$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
										}
										$grades['initial_grade'] = $get_initial_grade;
									}
								
									if( $qa_list != null ){
										$qa = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getOldCalculatedValue();
										if( $qa == 0 ){
											$qa = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getValue();
										}
										$grades['qa'] = $qa;
									}
									if( $qaws_list != null ){
										$qaws = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getOldCalculatedValue();
										if( $qaws == 0 ){
											$qaws = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getValue();
										}
										$grades['qaws'] = $qaws;
									}
								
									
									if( $qaps_list != null ){
										$qaps = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getOldCalculatedValue();
										if( $qaps == 0 ){
											$qaps = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getValue();
										}
										$grades['qaps'] = $qaps;
									}
										
									if( $pt_list != null){
										for ($pi = $pt_letters; $pi !== $wwws_letters ; $pi--){
											$get_pa_data = $sheetInsertData->getCellByColumnAndRow( $pi,$row)->getOldCalculatedValue();
											if( $get_pa_data == 0 ){
												$get_pa_data = $sheetInsertData->getCellByColumnAndRow( $pi,$row)->getValue();
											}
											$pa_data[] = $get_pa_data;
										}
										$grades['pt'] =   implode(",", $pa_data);
										
									}
									
									if( $ww_list != null){
										for ($wi = $ww_letters-1; $wi !== $start_boys_letters + 1 ; $wi--){
											$get_ww_data = $sheetInsertData->getCellByColumnAndRow( $wi,$row)->getOldCalculatedValue();
											if( $get_ww_data == 0 ){
												$get_ww_data = $sheetInsertData->getCellByColumnAndRow( $wi,$row)->getValue();
											}
											$ww_data[] = $get_ww_data;
										}
										$grades['ww'] = implode(",", $ww_data);
									}
									if( $ptt_list != null ){
										$ptt = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getOldCalculatedValue();
										if( $ptt == 0 ){
											$ptt = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getValue();
										}
										$grades['ptt'] = $ptt;
									}
									if( $ptws_list != null ){
										$ptws = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getOldCalculatedValue();
										if( $ptws == 0 ){
											$ptws = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getValue();
										}
										$grades['ptws'] = $ptws;
									}
									if( $ptps_list != null ){
										$ptps = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getOldCalculatedValue();
										if( $ptps == 0 ){
											$ptps = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getValue();
										}
										$grades['ptps'] = $ptps;
									}
									if( $wwt_list != null ){
										$wwt = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getOldCalculatedValue();
										if( $wwt == 0 ){
											$wwt = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getValue();
										}
										$grades['wwt'] = $wwt;
										
									}
									if( $wwt_list != null ){
										$wwws = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getOldCalculatedValue();
										if( $wwws == 0 ){
											$wwws = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getValue();
										}
										$grades['wwws'] = $wwws;
									}
									if( $wwps_list != null ){
										$wwps = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getOldCalculatedValue();
										if( $wwps == 0 ){
											$wwps = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getValue();
										}
										$grades['wwps'] = $wwps;
									}
									
									
									if($grades['id'] == null ){
										break;
										
									} else {
										$grades_f[] = $grades;
									}
								
								}
								unset( $pa_data );
								unset( $ww_data );
								unset( $ww );
								unset( $pt );
							}
							
							
							$highestRow = $end_girls_number - 1;
							$startRow = $start_girls_number + 1; 
							for($row = $startRow;$row <= $highestRow;$row++)
							{
								$get_id = $sheetInsertData->getCellByColumnAndRow($girls_start_id_letters,$row)->getValue();
								if( $get_id == 0 ){
									$get_id = $sheetInsertData->getCellByColumnAndRow($girls_start_id_letters,$row)->getOldCalculatedValue();
								}
								if( $get_id ){
									$grades['id']  = $get_id;
									$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getOldCalculatedValue();
									if( $get_grades == 0 ){
										$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
									}
									$grades['grade'] = $get_grades;
									
									if( $ini_grade_list != null ){
										$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $ini_grade_letters,$row)->getOldCalculatedValue();
										if( $get_initial_grade == 0 ){
											$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
										}
										$grades['initial_grade'] = $get_initial_grade;
									}
								
									if( $qa_list != null ){
										$qa = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getOldCalculatedValue();
										if( $qa == 0 ){
											$qa = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getValue();
										}
										$grades['qa'] = $qa;
									}
									if( $qaws_list != null ){
										$qaws = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getOldCalculatedValue();
										if( $qaws == 0 ){
											$qaws = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getValue();
										}
										$grades['qaws'] = $qaws;
									}
								
									
									if( $qaps_list != null ){
										$qaps = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getOldCalculatedValue();
										if( $qaps == 0 ){
											$qaps = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getValue();
										}
										$grades['qaps'] = $qaps;
									}
										
									if( $pt_list != null){
										for ($pi = $pt_letters; $pi !== $wwws_letters ; $pi--){
											$get_pa_data = $sheetInsertData->getCellByColumnAndRow( $pi,$row)->getOldCalculatedValue();
											if( $get_pa_data == 0 ){
												$get_pa_data = $sheetInsertData->getCellByColumnAndRow( $pi,$row)->getValue();
											}
											$pa_data[] = $get_pa_data;
										}
										$grades['pt'] =   implode(",", $pa_data);
									}
									
									if( $ww_list != null){
										for ($wi = $ww_letters-1; $wi !== $start_boys_letters + 1 ; $wi--){
											$get_ww_data = $sheetInsertData->getCellByColumnAndRow( $wi,$row)->getOldCalculatedValue();
											if( $get_ww_data == 0 ){
												$get_ww_data = $sheetInsertData->getCellByColumnAndRow( $wi,$row)->getValue();
											}
											$ww_data[] = $get_ww_data;
										}
										$grades['ww'] = implode(",", $ww_data);
									}
									if( $ptt_list != null ){
										$ptt = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getOldCalculatedValue();
										if( $ptt == 0 ){
											$ptt = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getValue();
										}
										$grades['ptt'] = $ptt;
									}
									if( $ptws_list != null ){
										$ptws = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getOldCalculatedValue();
										if( $ptws == 0 ){
											$ptws = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getValue();
										}
										$grades['ptws'] = $ptws;
									}
									if( $ptps_list != null ){
										$ptps = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getOldCalculatedValue();
										if( $ptps == 0 ){
											$ptps = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getValue();
										}
										$grades['ptps'] = $ptps;
									}
									if( $wwt_list != null ){
										$wwt = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getOldCalculatedValue();
										if( $wwt == 0 ){
											$wwt = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getValue();
										}
										$grades['wwt'] = $wwt;
									}
									if( $wwt_list != null ){
										$wwws = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getOldCalculatedValue();
										if( $wwws == 0 ){
											$wwws = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getValue();
										}
										$grades['wwws'] = $wwws;
									}
									if( $wwps_list != null ){
										$wwps = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getOldCalculatedValue();
										if( $wwps == 0 ){
											$wwps = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getValue();
										}
										$grades['wwps'] = $wwps;
									}
									
									
									if($grades['id'] == null ){
										break;
									} else {
										$grades_f[] = $grades;
									}
									
									unset( $pa_data );
									unset( $ww_data );
									unset( $ww );
									unset( $pt );
								}
							}
						}
					

							$insert_data_array = array(
							'created_at'=> date('Y-m-d h:i:s'),
							'subject_id'=> $subject_id,
							'class_id'=> $class_id,
							'section_id'=> $section_id,
							'quarter'=> $quarter,
							'published'=> 1,
						);
						$this->db->insert('import_grades_batch', $insert_data_array );
						$get_batch_id = $this->db->insert_id();
							
						$import_data[] = null;
						$import_quarterly_assessment_data[] = null;
						$import_performance_task_data[] = null;
						$import_written_work_data[] = null;
						$x = 0;
						
							if(isset($grades_f) )
							{
								foreach( $grades_f as $key[0] => $value ){
									
								$x++;
								
								$id = $this->student_model->getIDByClassSection($value['id'], $class_id, $section_id );
								
								if( $id ){
									$import_data[] = array(
									'batch_id' => $get_batch_id,
									'class_id' => $class_id,
									'section_id' => $section_id,
									'quarter' => $quarter,
									'subject_id' => $subject_id,
									'student_id' => $id,
									'grades' => isset($value['grade'])?$value['grade']:0,
									'initial_grade' => isset($value['initial_grade'])?$value['initial_grade']:0,
									);	
									$import_quarterly_assessment_data[] = array(
										'batch_id' => $get_batch_id,
										'class_id' => $class_id,
										'section_id' => $section_id,
										'quarter' => $quarter,
										'subject_id' => $subject_id,
										'student_id' => $id,
										'scores' => isset($value['qa'])?$value['qa']:0, 
										'ps' => isset($value['qaps'])?$value['qaps']:0, 
										'ws' => isset($value['qaws'])?$value['qaws']:0, 
									);	 

										$import_performance_task_data[] = array(
											'batch_id' => $get_batch_id,
											'class_id' => $class_id,
											'section_id' => $section_id,
											'quarter' => $quarter,
											'subject_id' => $subject_id,
											'student_id' => $id,
											'scores' => isset($value['pt'])?$value['pt']:0, 
											'total' => isset($value['ptt'])?$value['ptt']:0, 
											'ps' =>  isset($value['ptps'])?$value['ptps']:0, 
											'ws' =>  isset($value['ptws'])?$value['ptws']:0,
										);	 

										$import_written_work_data[] = array(
											'batch_id' => $get_batch_id,
											'class_id' => $class_id,
											'section_id' => $section_id,
											'quarter' => $quarter,
											'subject_id' => $subject_id,
											'student_id' => $id,
											'scores' => isset($value['ww'])?$value['ww']:0,
											'total' => isset($value['wwt'])?$value['wwt']:0,
											'ps' => isset($value['wwps'])?$value['wwps']:0,
											'ws' => isset($value['wwws'])?$value['wwws']:0,
										);	
									} else {
										$this->session->set_flashdata('msg', '<div class="alert alert-warning">In Row '.$x.' student does not exist'.'</div>');	
									}
								}
							} 

							if( count($import_data) > 0 ){
								unset($import_data[0]);
								unset($import_quarterly_assessment_data[0]);
								unset($import_performance_task_data[0]);
								unset($import_written_work_data[0]);
								$this->db->insert_batch('import_grades_details', $import_data );
								$this->db->insert_batch('import_grades_quarterly_assessment', $import_quarterly_assessment_data );
								$this->db->insert_batch('import_grades_performance_task', $import_performance_task_data );
								$this->db->insert_batch('import_grades_written_work', $import_written_work_data );
								$this->session->set_flashdata('msg', '<div class="alert alert-success">Students grade imported successfully</div>');	
							} else {
								$this->session->set_flashdata('msg', '<div class="alert alert-warning">Students grade imported unsuccessfully</div>');		
								$this->importgradesbatch_model->remove( $get_batch_id );
							}
						} 

				redirect('admin/grade/importcustomgrade'); 				
			}	else {
				$this->session->set_flashdata('msg', '<div class="alert alert-warning">No file attached!</div>');	
				redirect('admin/grade/importcustomgrade');
			}
		}
	}
	
	function importcustomgrade1() {
		ini_set('memory_limit', '-1');
		$data['title'] = 'Import Custom Grades';
		$data['title_list'] = 'Recently Import Custom Grades';
		$this->session->set_userdata('top_menu', 'Examinations');
       	$this->session->set_userdata('sub_menu', 'import/index');
		$session = $this->setting_model->getCurrentSession();
		$data['getSemester'] = $this->customlib->getSemester();
		$data['studentList'] = $this->customsubject_model->getStudent();
		$data['subjectlist'] = $this->subject_model->get();
		$data['templatelist'] = $this->template_model->get();
		$data['getquarter'] = $this->customlib->getQuarter();
		$this->form_validation->set_rules('student_id', 'Student', 'trim|required|xss_clean');
		$this->form_validation->set_rules('semester_id', 'Semester', 'trim|required|xss_clean');
		$this->form_validation->set_rules('quarter', 'Quarter', 'trim|required|xss_clean');
		$this->form_validation->set_rules('subject_id', 'Subject', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('layout/teacher/header', $data);
			$this->load->view('teacher/grade/importcustomgrade', $data);
			$this->load->view('layout/teacher/footer', $data);
		} else {
			$student_id = $this->input->post('student_id');
			$student_details = $this->student_model->get($student_id);
			$class_id = $student_details['class_id'];
			$section_id = $student_details['section_id'];
			$subject_id = $this->input->post('subject_id');
			$quarter = $this->input->post('quarter');
			$getquartersheet = $quarter - 1;
			$session = $this->setting_model->getCurrentSession();
			
		if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
				include ("application/third_party/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
				
				$inputFileName = $_FILES['file']['tmp_name'];
				//  Read your Excel workbook
				try {
					$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($inputFileName);
				} 
				
				catch(Exception $e) {
					die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
				}
				$getSheetNames = $objPHPExcel->getSheetNames();
				$getCurrentSheet = $this->get_assign_sheet_name( $quarter );
				$termSheetMap = array(1 => 'TERM1', 2 => 'TERM2', 3 => 'TERM3');
				$targetTermSheet = isset($termSheetMap[$quarter]) ? $termSheetMap[$quarter] : '';
				
				if(in_array( $getCurrentSheet, $getSheetNames )){
					$getquartersheet = array_search( $getCurrentSheet, $getSheetNames);
					$objPHPExcel->setActiveSheetIndex($getquartersheet);
				} elseif($targetTermSheet !== '' && in_array( $targetTermSheet, $getSheetNames )){
					$getquartersheet = array_search( $targetTermSheet, $getSheetNames);
					$objPHPExcel->setActiveSheetIndex($getquartersheet);
				} else {
					$this->session->set_flashdata('msg', '<div class="alert alert-warning">Sheet "'.$getCurrentSheet.'" or "'.$targetTermSheet.'" not found. Please import correct template.</div>');
					redirect('admin/grade/importcustomgrade');
				}
				$sheetInsertData = $objPHPExcel->getActiveSheet();
					//$sheetInsertData = $sheetInsertData->getTitle();
					//$sheetInsertData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
					
					$foundInCells = $this->spout->search_cells_with_pt_wt( $inputFileName );
					$grade_list = isset($foundInCells['{grade}'][$getquartersheet])?$foundInCells['{grade}'][$getquartersheet]:null;
					
					if( $grade_list != null ){
						$grade_list_letters = $grade_list['columnnumber'];
						$grade_list_number = $grade_list['rownumber'];
					}
					
					$start_boys_list =  isset($foundInCells['{start_boys}'][$getquartersheet])?$foundInCells['{start_boys}'][$getquartersheet]:null;
					if( $start_boys_list != null ){
						$start_boys_letters = $start_boys_list['columnnumber'];
						$start_boys_number = $start_boys_list['rownumber'];
					}
						
					$end_boys_list = isset($foundInCells['{end_boys}'][$getquartersheet])?$foundInCells['{end_boys}'][$getquartersheet]:null;
					if( $end_boys_list != null ){
						$end_boys_letters = $end_boys_list['columnnumber'];
						$end_boys_number = $end_boys_list['rownumber'];
					}
					
					$boys_start_id_list = isset($foundInCells['{boys_start_id}'][$getquartersheet])?$foundInCells['{boys_start_id}'][$getquartersheet]:null;
					if( $boys_start_id_list != null ){
						$boys_start_id_letters = $boys_start_id_list['columnnumber'];
						$boys_start_id_number = $boys_start_id_list['rownumber'];
					}

					$start_girls_list = isset($foundInCells['{start_girls}'][$getquartersheet])?$foundInCells['{start_girls}'][$getquartersheet]:null;
					if( $start_girls_list != null ){
						$start_girls_letters = $start_girls_list['columnnumber'];
						$start_girls_number =  $start_girls_list['rownumber'];
					}

					$end_girls_list = isset($foundInCells['{end_girls}'][$getquartersheet])?$foundInCells['{end_girls}'][$getquartersheet]:null;
					if( $end_girls_list != null ){
						$end_girls_letters = $end_girls_list['columnnumber'];
						$end_girls_number = $end_girls_list['rownumber'];
					}
					
					$girls_start_id_list = isset($foundInCells['{girls_start_id}'][$getquartersheet])?$foundInCells['{girls_start_id}'][$getquartersheet]:null;
					if( $girls_start_id_list != null ){
						$girls_start_id_letters = $girls_start_id_list['columnnumber'];
						$girls_start_id_number = $girls_start_id_list['rownumber'];
					}
					
					
					
					if( $grade_list == null ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{grade} is missing !</div>');		
						redirect('teacher/grade/importcustomgrade');
					}elseif( $start_boys_list == null ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{start_boys} is missing !</div>');		
						redirect('teacher/grade/importcustomgrade');
					}elseif( $end_boys_list == null ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{end_boys} is missing !</div>');		
						redirect('teacher/grade/importcustomgrade');
					}elseif( $start_girls_list == null ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{start_girls} is missing !</div>');		
						redirect('teacher/grade/importcustomgrade');
					} elseif( $end_girls_list == null ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{end_girls} is missing !</div>');		
						redirect('teacher/grade/importcustomgrade');
					} elseif( $boys_start_id_list == null ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{boys_starts_id} is missing !</div>');		
					} elseif( $girls_start_id_list == null ){
						$this->session->set_flashdata('msg', '<div class="alert alert-warning">{girls_start_id} is missing !</div>');		
						redirect('teacher/grade/importcustomgrade');
					} else {
						$ww_data = array();
						$pa_data = array();
						$ini_grade_list = isset($foundInCells['{ini-grade}'][$getquartersheet])?$foundInCells['{ini-grade}'][$getquartersheet]:null;
						if( $ini_grade_list != null ){
							$ini_grade_letters = $ini_grade_list['columnnumber'];
							$ini_grade_number = $ini_grade_list['rownumber'];
						}
						
						$qaws_list = isset($foundInCells['{qaws}'][$getquartersheet])?$foundInCells['{qaws}'][$getquartersheet]:null;
						if( $qaws_list != null ){
							$qaws_letters = $qaws_list['columnnumber'];
							$qaws_number = $qaws_list['rownumber'];
						}

						$qaps_list = isset($foundInCells['{qaps}'][$getquartersheet])?$foundInCells['{qaps}'][$getquartersheet]:null;
						if( $qaps_list != null ){
							$qaps_letters = $qaps_list['columnnumber'];
							$qaps_number = $qaps_list['rownumber'];
						}
						
						$qa_list = isset($foundInCells['{qa}'][$getquartersheet])?$foundInCells['{qa}'][$getquartersheet]:null;
						if( $qa_list != null ){
							$qa_letters = $qa_list['columnnumber'];
							$qa_number = $qa_list['rownumber'];
						}
						
						$ptws_list = isset($foundInCells['{ptws}'][$getquartersheet])?$foundInCells['{ptws}'][$getquartersheet]:null;
						if( $ptws_list != null ){
							$ptws_letters = $ptws_list['columnnumber'];
							$ptws_number = $ptws_list['rownumber'];
						}
						
						$ptps_list = isset($foundInCells['{ptps}'][$getquartersheet])?$foundInCells['{ptps}'][$getquartersheet]:null;
						if( $ptps_list != null ){
							$ptps_letters = $ptps_list['columnnumber'];
							$ptps_number = $ptps_list['rownumber'];
						}
						
						$ptt_list = isset($foundInCells['{ptt}'][$getquartersheet])?$foundInCells['{ptt}'][$getquartersheet]:null;
						if( $ptt_list != null ){
							$ptt_letters = $ptt_list['columnnumber'];
							$ptt_number = $ptt_list['rownumber'];
						}
						$pt_list = isset($foundInCells['{pt}'][$getquartersheet])?$foundInCells['{pt}'][$getquartersheet]:null;
						if( $pt_list != null ){
							$pt_letters = $pt_list['columnnumber'];
							$pt_number = $pt_list['rownumber'];
						}
						$wwws_list = isset($foundInCells['{wwws}'][$getquartersheet])?$foundInCells['{wwws}'][$getquartersheet]:null;
						if( $wwws_list != null ){
							$wwws_letters = $wwws_list['columnnumber'];
							$wwws_number = $wwws_list['rownumber'];
						}
						
						$wwps_list = isset($foundInCells['{wwps}'][$getquartersheet])?$foundInCells['{wwps}'][$getquartersheet]:null;
						if( $wwps_list != null ){
							$wwps_letters = $wwps_list['columnnumber'];
							$wwps_number = $wwps_list['rownumber'];
						}
						$wwt_list = isset($foundInCells['{wwt}'][$getquartersheet])?$foundInCells['{wwt}'][$getquartersheet]:null;
						if( $wwt_list != null ){
							$wwt_letters = $wwt_list['columnnumber'];
							$wwt_number = $wwt_list['rownumber'];
						}
						$ww_list = isset($foundInCells['{wwt}'][$getquartersheet])?$foundInCells['{wwt}'][$getquartersheet]:null;
						if( $ww_list != null ){
							$ww_letters = $ww_list['columnnumber'];
							$ww_number = $ww_list['rownumber'];
						}
					
						if( isset($getquartersheet) ){
							$grades = array();
							$highestRow = $end_boys_number - 1;
							$startRow = $start_boys_number + 1;
							//$grades_f[]= null;
							for($row = $startRow;$row <= $highestRow;$row++)
							{
								//$grades['id']  = $sheetInsertData->getCellByColumnAndRow(1,$row)->getOldCalculatedValue();
								$get_id  = $sheetInsertData->getCellByColumnAndRow($boys_start_id_letters,$row)->getValue();
								if( $get_id == 0 ){
									$get_id  = $sheetInsertData->getCellByColumnAndRow($boys_start_id_letters,$row)->getOldCalculatedValue();
								}
								if( $get_id ){
									$grades['id']  = $get_id;
									$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getOldCalculatedValue();
									if( $get_grades == 0 ){
										$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
										$get_grades = is_numeric( $get_grades )?$get_grades:'';	
									}
									$grades['grade'] = $get_grades;
									
									if( $ini_grade_list != null ){
										$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $ini_grade_letters,$row)->getOldCalculatedValue();
										if( $get_initial_grade == 0 ){
											$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
										}
										$grades['initial_grade'] = $get_initial_grade;
									}
								
									if( $qa_list != null ){
										$qa = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getOldCalculatedValue();
										if( $qa == 0 ){
											$qa = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getValue();
										}
										$grades['qa'] = $qa;
									}
									if( $qaws_list != null ){
										$qaws = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getOldCalculatedValue();
										if( $qaws == 0 ){
											$qaws = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getValue();
										}
										$grades['qaws'] = $qaws;
									}
								
									
									if( $qaps_list != null ){
										$qaps = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getOldCalculatedValue();
										if( $qaps == 0 ){
											$qaps = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getValue();
										}
										$grades['qaps'] = $qaps;
									}
										
									if( $pt_list != null){
										for ($pi = $pt_letters; $pi !== $wwws_letters ; $pi--){
											$get_pa_data = $sheetInsertData->getCellByColumnAndRow( $pi,$row)->getOldCalculatedValue();
											if( $get_pa_data == 0 ){
												$get_pa_data = $sheetInsertData->getCellByColumnAndRow( $pi,$row)->getValue();
											}
											$pa_data[] = $get_pa_data;
										}
										$grades['pt'] =   implode(",", $pa_data);
										
									}
									
									if( $ww_list != null){
										for ($wi = $ww_letters-1; $wi !== $start_boys_letters + 1 ; $wi--){
											$get_ww_data = $sheetInsertData->getCellByColumnAndRow( $wi,$row)->getOldCalculatedValue();
											if( $get_ww_data == 0 ){
												$get_ww_data = $sheetInsertData->getCellByColumnAndRow( $wi,$row)->getValue();
											}
											$ww_data[] = $get_ww_data;
										}
										$grades['ww'] = implode(",", $ww_data);
									}
									if( $ptt_list != null ){
										$ptt = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getOldCalculatedValue();
										if( $ptt == 0 ){
											$ptt = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getValue();
										}
										$grades['ptt'] = $ptt;
									}
									if( $ptws_list != null ){
										$ptws = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getOldCalculatedValue();
										if( $ptws == 0 ){
											$ptws = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getValue();
										}
										$grades['ptws'] = $ptws;
									}
									if( $ptps_list != null ){
										$ptps = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getOldCalculatedValue();
										if( $ptps == 0 ){
											$ptps = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getValue();
										}
										$grades['ptps'] = $ptps;
									}
									if( $wwt_list != null ){
										$wwt = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getOldCalculatedValue();
										if( $wwt == 0 ){
											$wwt = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getValue();
										}
										$grades['wwt'] = $wwt;
										
									}
									if( $wwt_list != null ){
										$wwws = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getOldCalculatedValue();
										if( $wwws == 0 ){
											$wwws = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getValue();
										}
										$grades['wwws'] = $wwws;
									}
									if( $wwps_list != null ){
										$wwps = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getOldCalculatedValue();
										if( $wwps == 0 ){
											$wwps = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getValue();
										}
										$grades['wwps'] = $wwps;
									}
									
									
									if($grades['id'] == null ){
										break;
										
									} else {
										$grades_f[] = $grades;
									}
								
								}
								unset( $pa_data );
								unset( $ww_data );
								unset( $ww );
								unset( $pt );
							}
							
							
							$highestRow = $end_girls_number - 1;
							$startRow = $start_girls_number + 1; 
							for($row = $startRow;$row <= $highestRow;$row++)
							{
								$get_id = $sheetInsertData->getCellByColumnAndRow($girls_start_id_letters,$row)->getValue();
								if( $get_id == 0 ){
									$get_id = $sheetInsertData->getCellByColumnAndRow($girls_start_id_letters,$row)->getOldCalculatedValue();
								}
								if( $get_id ){
									$grades['id']  = $get_id;
									$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getOldCalculatedValue();
									if( $get_grades == 0 ){
										$get_grades = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
									}
									$grades['grade'] = $get_grades;
									
									if( $ini_grade_list != null ){
										$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $ini_grade_letters,$row)->getOldCalculatedValue();
										if( $get_initial_grade == 0 ){
											$get_initial_grade = $sheetInsertData->getCellByColumnAndRow( $grade_list_letters,$row)->getValue();
										}
										$grades['initial_grade'] = $get_initial_grade;
									}
								
									if( $qa_list != null ){
										$qa = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getOldCalculatedValue();
										if( $qa == 0 ){
											$qa = $sheetInsertData->getCellByColumnAndRow( $qa_letters,$row)->getValue();
										}
										$grades['qa'] = $qa;
									}
									if( $qaws_list != null ){
										$qaws = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getOldCalculatedValue();
										if( $qaws == 0 ){
											$qaws = $sheetInsertData->getCellByColumnAndRow( $qaws_letters,$row)->getValue();
										}
										$grades['qaws'] = $qaws;
									}
								
									
									if( $qaps_list != null ){
										$qaps = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getOldCalculatedValue();
										if( $qaps == 0 ){
											$qaps = $sheetInsertData->getCellByColumnAndRow( $qaps_letters,$row)->getValue();
										}
										$grades['qaps'] = $qaps;
									}
										
									if( $pt_list != null){
										for ($pi = $pt_letters; $pi !== $wwws_letters ; $pi--){
											$get_pa_data = $sheetInsertData->getCellByColumnAndRow( $pi,$row)->getOldCalculatedValue();
											if( $get_pa_data == 0 ){
												$get_pa_data = $sheetInsertData->getCellByColumnAndRow( $pi,$row)->getValue();
											}
											$pa_data[] = $get_pa_data;
										}
										$grades['pt'] =   implode(",", $pa_data);
									}
									
									if( $ww_list != null){
										for ($wi = $ww_letters-1; $wi !== $start_boys_letters + 1 ; $wi--){
											$get_ww_data = $sheetInsertData->getCellByColumnAndRow( $wi,$row)->getOldCalculatedValue();
											if( $get_ww_data == 0 ){
												$get_ww_data = $sheetInsertData->getCellByColumnAndRow( $wi,$row)->getValue();
											}
											$ww_data[] = $get_ww_data;
										}
										$grades['ww'] = implode(",", $ww_data);
									}
									if( $ptt_list != null ){
										$ptt = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getOldCalculatedValue();
										if( $ptt == 0 ){
											$ptt = $sheetInsertData->getCellByColumnAndRow( $ptt_letters,$row)->getValue();
										}
										$grades['ptt'] = $ptt;
									}
									if( $ptws_list != null ){
										$ptws = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getOldCalculatedValue();
										if( $ptws == 0 ){
											$ptws = $sheetInsertData->getCellByColumnAndRow( $ptws_letters,$row)->getValue();
										}
										$grades['ptws'] = $ptws;
									}
									if( $ptps_list != null ){
										$ptps = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getOldCalculatedValue();
										if( $ptps == 0 ){
											$ptps = $sheetInsertData->getCellByColumnAndRow( $ptps_letters,$row)->getValue();
										}
										$grades['ptps'] = $ptps;
									}
									if( $wwt_list != null ){
										$wwt = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getOldCalculatedValue();
										if( $wwt == 0 ){
											$wwt = $sheetInsertData->getCellByColumnAndRow( $wwt_letters,$row)->getValue();
										}
										$grades['wwt'] = $wwt;
									}
									if( $wwt_list != null ){
										$wwws = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getOldCalculatedValue();
										if( $wwws == 0 ){
											$wwws = $sheetInsertData->getCellByColumnAndRow( $wwws_letters,$row)->getValue();
										}
										$grades['wwws'] = $wwws;
									}
									if( $wwps_list != null ){
										$wwps = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getOldCalculatedValue();
										if( $wwps == 0 ){
											$wwps = $sheetInsertData->getCellByColumnAndRow( $wwps_letters,$row)->getValue();
										}
										$grades['wwps'] = $wwps;
									}
									
									
									if($grades['id'] == null ){
										break;
									} else {
										$grades_f[] = $grades;
									}
									
									unset( $pa_data );
									unset( $ww_data );
									unset( $ww );
									unset( $pt );
								}
							}
						}
					

						$insert_data_array = array(
							'created_at'=> date('Y-m-d h:i:s'),
							'subject_id'=> $subject_id,
							'class_id'=> $class_id,
							'section_id'=> $section_id,
							'quarter'=> $quarter,
							'teacher_id'=> $teacher_id
						);
						$this->db->insert('import_grades_batch', $insert_data_array );
						$get_batch_id = $this->db->insert_id();

						$import_data[] = null;
						$import_quarterly_assessment_data[] = null;
						$import_performance_task_data[] = null;
						$import_written_work_data[] = null;
						$x = 0;
						
							if(isset($grades_f) )
							{
								foreach( $grades_f as $key[0] => $value ){
									
								$x++;
								
								$id = $this->student_model->getIDByClassSection($value['id'], $class_id, $section_id );
								
								if( $id ){
									$import_data[] = array(
									'batch_id' => $get_batch_id,
									'class_id' => $class_id,
									'section_id' => $section_id,
									'quarter' => $quarter,
									'subject_id' => $subject_id,
									'student_id' => $id,
									'grades' => isset($value['grade'])?$value['grade']:0,
									'initial_grade' => isset($value['initial_grade'])?$value['initial_grade']:0,
									);	
									$import_quarterly_assessment_data[] = array(
										'batch_id' => $get_batch_id,
										'class_id' => $class_id,
										'section_id' => $section_id,
										'quarter' => $quarter,
										'subject_id' => $subject_id,
										'student_id' => $id,
										'scores' => isset($value['qa'])?$value['qa']:0, 
										'ps' => isset($value['qaps'])?$value['qaps']:0, 
										'ws' => isset($value['qaws'])?$value['qaws']:0, 
									);	 

										$import_performance_task_data[] = array(
											'batch_id' => $get_batch_id,
											'class_id' => $class_id,
											'section_id' => $section_id,
											'quarter' => $quarter,
											'subject_id' => $subject_id,
											'student_id' => $id,
											'scores' => isset($value['pt'])?$value['pt']:0, 
											'total' => isset($value['ptt'])?$value['ptt']:0, 
											'ps' =>  isset($value['ptps'])?$value['ptps']:0, 
											'ws' =>  isset($value['ptws'])?$value['ptws']:0,
										);	 

										$import_written_work_data[] = array(
											'batch_id' => $get_batch_id,
											'class_id' => $class_id,
											'section_id' => $section_id,
											'quarter' => $quarter,
											'subject_id' => $subject_id,
											'student_id' => $id,
											'scores' => isset($value['ww'])?$value['ww']:0,
											'total' => isset($value['wwt'])?$value['wwt']:0,
											'ps' => isset($value['wwps'])?$value['wwps']:0,
											'ws' => isset($value['wwws'])?$value['wwws']:0,
										);	
									} else {
										$this->session->set_flashdata('msg', '<div class="alert alert-warning">In Row '.$x.' student does not exist'.'</div>');	
									}
								}
							} 

							if( count($import_data) > 0 ){
								unset($import_data[0]);
								unset($import_quarterly_assessment_data[0]);
								unset($import_performance_task_data[0]);
								unset($import_written_work_data[0]);
								$this->db->insert_batch('import_grades_details', $import_data );
								$this->db->insert_batch('import_grades_quarterly_assessment', $import_quarterly_assessment_data );
								$this->db->insert_batch('import_grades_performance_task', $import_performance_task_data );
								$this->db->insert_batch('import_grades_written_work', $import_written_work_data );
								$this->session->set_flashdata('msg', '<div class="alert alert-success">Students grade imported successfully</div>');	
							} else {
								$this->session->set_flashdata('msg', '<div class="alert alert-warning">Students grade imported unsuccessfully</div>');		
								$this->importgradesbatch_model->remove( $get_batch_id );
							}
						} 

				redirect('teacher/grade/importcustomgrade'); 				
			}	else {
				$this->session->set_flashdata('msg', '<div class="alert alert-warning">No file attached!</div>');	
				redirect('teacher/grade/importcustomgrade');
			}			
		}
	}
	
	function get_assign_sheet_name( $quarter ){
		
		if( $quarter == 1 ){
			return "1st Quarter";
		} elseif( $quarter == 2 ){
			return "2nd Quarter";
		} elseif( $quarter == 3 ){
			return "3rd Quarter";
		} elseif( $quarter == 4 ){
			return "4th Quarter";
		}
		
	}

	public function deleted_all(){
		$approved_grades = $this->input->post('approved_grades');
		$batch_id = $this->input->post('batch_id');
		$count_approved_grades = count( $approved_grades );
		$is_deleted = false;
		if( $count_approved_grades > 0 ){
			$import_grades_details = $this->importgradesdetails_model->delete_batch( $approved_grades );
			if( $import_grades_details ){
				$is_deleted = true;
				$this->session->set_flashdata('msg', '<div class="alert alert-success"> Delete data successfully </div>');	
				for ($i=0; $i <= count($approved_grades); $i++) {
					if(isset($approved_grades[$i])){
						$get_id = $approved_grades[$i]; 

						$session_details = $this->session->userdata('student');
						$userid = $session_details['id'];

						$savelogs = array(
				        	'user_id' => $userid,
				        	'action' => 'delete',
				        	'module_id' => $get_id,
				        	'module' => 'import_grades_details',
				    	);

			    		$this->gradelogs_model->add( $savelogs );
					}
					

					# code...
				}
			} else {
				$is_deleted = false;
				$this->session->set_flashdata('msg', '<div class="alert alert-success"> Delete data unsuccessfully </div>');		
			}

			$session_details = $this->session->userdata('student');
			$userid = $session_details['id'];
			$description = $is_deleted == true?'data save successfully.':'data save unsuccessfully.';

			$savelogs = array(
	        	'user_id' => $userid,
	        	'action' => 'delete',
	        	'module_id' => $batch_id,
	        	'module' => 'import_grades_batch',
	        	'description' => $description,
	    	);

    		$this->gradelogs_model->add( $savelogs );
		} else {
			$this->session->set_flashdata('msg', '<div class="alert alert-warning"> Please select data. </div>');			
		}
		
		redirect( $_SERVER['HTTP_REFERER'] );	
	}
}
