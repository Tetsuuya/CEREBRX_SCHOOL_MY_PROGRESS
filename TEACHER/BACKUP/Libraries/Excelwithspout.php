<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); 

require_once APPPATH."/third_party/PHPExcel-1.8/Classes/PHPExcel.php";

class Excelwithspout extends PHPExcel {
	
    public function __construct() {
        parent::__construct();
		
    }
	
	public function generate_spreadsheet( $parameters ){
		
		ini_set('memory_limit', '-1');
		$CI =& get_instance();
		$CI->load->model('Student_model');
		$CI->load->model('Class_model');
		$CI->load->model('Section_model');
		$CI->load->model('Subject_model');
		$CI->load->model('Setting_model');
		$CI->load->model('Template_model');
		$CI->load->model('Teacher_model');
		$CI->load->library('excel');
		$CI->load->library('spout');

		$class_id = $parameters['class_id'];
		$section_id = $parameters['section_id'];
		$subject_id = $parameters['subject_id'];
		$template_id = $parameters['template_id'];
		$subject_name = null;
		$class_name = null;
		$teachers_name = null;
		$section_name = null;
		$written_work = null;
		$performance_task = null;
		$quarterly_assesment = null;
		$teacher_result = null;
		$get_school_name = $CI->Setting_model->getCurrentSchoolName();
		$boys_students = $CI->Student_model->getstudentsByClassSectionGender( $class_id, $section_id, 'Male', '', TRUE );
		$girl_students = $CI->Student_model->getstudentsByClassSectionGender( $class_id, $section_id, 'Female', '', TRUE );
		$template_details = $CI->Template_model->get( $template_id ); 	
		$template_file = isset($template_details['file'])?$template_details['file']:null;
		$foundInCells = $CI->spout->search_cells( $template_file );  
		/* print_r( $foundInCells );
		die(); */
		if( $class_id != null ){
			$class_details = $CI->Class_model->get( $class_id ); 	
			$class_name = $class_details['class'];
		}
		
		if( $section_id != null ){
			$section_details = $CI->Section_model->get( $section_id ); 	
			$section_name = $section_details['section'];
		}
		
		if( $subject_id != null ){
			$subject_details = $CI->Subject_model->get( $subject_id ); 	
	
			$subject_name = $subject_details['name']; 	
			$written_work = $subject_details['written_work']; 	
			$performance_task = $subject_details['performance_task']; 	
			$quarterly_assesment = $subject_details['quarterly_assessment']; 	
		}
		
		$teacher_id = isset($CI->session->userdata['student']['teacher_id'])?$CI->session->userdata['student']['teacher_id']:null; 
		if( $teacher_id ){
			$teacher_result = $CI->Teacher_model->get($teacher_id);
		}
		
		include ("application/third_party/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
		
		if( $template_file!= null ){
			$inputFileName = $template_file;
		} else {
			$inputFileName = 'uploads/GradingTemplate.xlsx';
		}
	
		try {
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFileName);
		} 
		
		catch(Exception $e) {
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}
		
		// BOYS
		$objPHPExcel->setActiveSheetIndex(0);
		$sheetInsertData = $objPHPExcel->getActiveSheet();
		
		$school_name_cell = isset($foundInCells['{school_name}'])?$foundInCells['{school_name}']:null;
		$subject_name_cell = isset($foundInCells['{subject_name}'])?$foundInCells['{subject_name}']:null;
		$class_cell = isset($foundInCells['{class}'])?$foundInCells['{class}']:null; 
		$subject_teacher_cell = isset($foundInCells['{subject_teacher}'])?$foundInCells['{subject_teacher}']:null;
		$written_work_cell = isset($foundInCells['{written_work}'])?$foundInCells['{written_work}']:null;
		$performance_tasks_cell = isset($foundInCells['{performance_tasks}'])?$foundInCells['{performance_tasks}']:null;
		$quarterly_assesment_cell = isset($foundInCells['{quarterly_assessment}'])?$foundInCells['{quarterly_assessment}']:null;
		
		if( $school_name_cell != null ){
			$school_name_cell_x = $school_name_cell['rownumber'];
			$school_name_cell_y = $school_name_cell['columnnumber'];
			$sheetInsertData->setCellValueByColumnAndRow($school_name_cell_y,$school_name_cell_x, $get_school_name );
		}
		
		if( $class_cell != null ){
			$class_cell_x = $class_cell['rownumber'];
			$class_cell_y = $class_cell['columnnumber'];
			$sheetInsertData->setCellValueByColumnAndRow($class_cell_y,$class_cell_x, $class_name.' '.$section_name );
		}
		
		if( $class_cell != null ){
			$class_cell_x = $class_cell['rownumber'];
			$class_cell_y = $class_cell['columnnumber'];
			$sheetInsertData->setCellValueByColumnAndRow($class_cell_y,$class_cell_x, $class_name.' '.$section_name );
		}
		
		if( $subject_name_cell != null ){
			$subject_name_cell_x = $subject_name_cell['rownumber'];
			$subject_name_cell_y = $subject_name_cell['columnnumber'];
			$sheetInsertData->setCellValueByColumnAndRow($subject_name_cell_y,$subject_name_cell_x, $subject_name );
		}
		
		if( $written_work_cell != null && $written_work  ){
			$written_work = $written_work / 100;
			$written_work_cell_x = $written_work_cell['rownumber'];
			$written_work_cell_y = $written_work_cell['columnnumber'];
			$sheetInsertData->setCellValueByColumnAndRow($written_work_cell_y,$written_work_cell_x, $written_work );
		}
		
		if( $performance_tasks_cell != null && $performance_task ){
			$performance_task = $performance_task / 100;
			$performance_tasks_cell_x = $performance_tasks_cell['rownumber'];
			$performance_tasks_cell_y = $performance_tasks_cell['columnnumber'];
			$sheetInsertData->setCellValueByColumnAndRow($performance_tasks_cell_y,$performance_tasks_cell_x, $performance_task );
		}
		
		if( $quarterly_assesment_cell != null && $quarterly_assesment ){
			$quarterly_assesment = $quarterly_assesment / 100;
			$quarterly_assesment_cell_x = $quarterly_assesment_cell['rownumber'];
			$quarterly_assesment_cell_y = $quarterly_assesment_cell['columnnumber'];
			$sheetInsertData->setCellValueByColumnAndRow($quarterly_assesment_cell_y,$quarterly_assesment_cell_x, $quarterly_assesment );
		}
		
		if( $subject_teacher_cell != null  ){
			$subject_teacher_cell_x = $subject_teacher_cell['rownumber'];
			$subject_teacher_cell_y = $subject_teacher_cell['columnnumber'];
			if( $teacher_result ){
				$firstname = $teacher_result['name'];
				$lastname = $teacher_result['lastname'];
				$middlename = $teacher_result['middlename'];
				if( $middlename ){
				  $fullname = $lastname.', '.$firstname.' '.$middlename;	
				} else {
					$fullname = $lastname.', '.$firstname;	
				}
				$sheetInsertData->setCellValueByColumnAndRow($subject_teacher_cell_y,$subject_teacher_cell_x, $fullname );
			}
		}
	
	
		$start_boys_numbering = isset($foundInCells['{start_boys_numbering}'])?$foundInCells['{start_boys_numbering}']:null;
		$end_boys_numbering = isset($foundInCells['{end_boys_numbering}'])?$foundInCells['{end_boys_numbering}']:null;
		$start_boys = isset($foundInCells['{start_boys}'])?$foundInCells['{start_boys}']:null;
		$end_boys = isset($foundInCells['{end_boys}'])?$foundInCells['{end_boys}']:null;
		$boys_start_id = isset($foundInCells['{boys_start_id}'])?$foundInCells['{boys_start_id}']:null;
		$start_boys_numbering_x = $start_boys_numbering['rownumber'];
		$start_boys_numbering_y = $start_boys_numbering['columnnumber'];
		$end_boys_numbering_x = $end_boys_numbering['rownumber'];
		$start_boys_x = $start_boys['rownumber'];
		$start_boys_y = $start_boys['columnnumber'];
		$end_boys_x = $end_boys['rownumber'];
		$end_boys_y = $end_boys['columnnumber'];
		$boys_start_id_x = $boys_start_id['rownumber'];
		$boys_start_id_y = $boys_start_id['columnnumber'];
		$n=1;
		$start_boys_numbering_x++;
		$boys_start_id_x++;
		$start_boys_x++;
		for( $b=0; $start_boys_numbering_x < $end_boys_numbering_x ; $b++ )
		{
			set_time_limit(0);
			if( !empty( $boys_students[$b] ) ){
				$boy_details  = $boys_students[$b];
				$sheetInsertData->setCellValueByColumnAndRow($start_boys_numbering_y,$start_boys_numbering_x, $n );
				$sheetInsertData->setCellValueByColumnAndRow($boys_start_id_y,$boys_start_id_x, $boy_details['admission_no'] ); 
				$sheetInsertData->setCellValueByColumnAndRow($start_boys_y,$start_boys_x, $boy_details['lastname'].", ".$boy_details['firstname'] );
			}
			
			$start_boys_numbering_x++;
			$boys_start_id_x++;
			$start_boys_x++;
			$n++;
		}  
		// GIRLS
		$start_girls_numbering = isset($foundInCells['{start_girls_numbering}'])?$foundInCells['{start_girls_numbering}']:null;
		$end_girls_numbering = isset($foundInCells['{end_girls_numbering}'])?$foundInCells['{end_girls_numbering}']:null;
		$start_girls = isset($foundInCells['{start_girls}'])?$foundInCells['{start_girls}']:null;
		$end_girls = isset($foundInCells['{end_girls}'])?$foundInCells['{end_girls}']:null;
		$girls_start_id = isset($foundInCells['{girls_start_id}'])?$foundInCells['{girls_start_id}']:null;

		$start_girls_numbering_x = $start_girls_numbering['rownumber'];
		$start_girls_numbering_y = $start_girls_numbering['columnnumber'];
		$end_girls_numbering_x = $end_girls_numbering['rownumber'];
		$start_girls_x = $start_girls['rownumber'];
		$start_girls_y = $start_girls['columnnumber'];
		$end_girls_x = $end_girls['rownumber'];
		$end_girls_y = $end_girls['columnnumber'];
		$girls_start_id_x = $girls_start_id['rownumber'];
		$girls_start_id_y = $girls_start_id['columnnumber'];
		
		$n=1;
		$start_girls_numbering_x++;
		$girls_start_id_x++;
		$start_girls_x++;
		for( $b=0; $start_girls_numbering_x < $end_girls_numbering_x ; $b++ )
		{
			set_time_limit(0);
			if( !empty( $girl_students[$b] ) ){
				$girl_details  = $girl_students[$b];
				$sheetInsertData->setCellValueByColumnAndRow($start_girls_numbering_y,$start_girls_numbering_x, $n );
				$sheetInsertData->setCellValueByColumnAndRow($girls_start_id_y,$girls_start_id_x, $girl_details['admission_no'] ); 
				$sheetInsertData->setCellValueByColumnAndRow($start_girls_y,$start_girls_x, $girl_details['lastname'].", ".$girl_details['firstname'] );
			}
			
			$start_girls_numbering_x++;
			$girls_start_id_x++;
			$start_girls_x++;
			$n++;
		}  
				
	
		$date = date('Ymdhis');
		$filename = $subject_name.'-'.$class_name.' '.$section_name.'-'.$date;
		$filename= $filename.'.xlsx';
		//header('Content-Type: application/vnd.ms-excel'); //mime type
		// Clear all output buffers to prevent download issues
		while (ob_get_level()) {
			ob_end_clean();
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
					
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
		$objPHPExcel->disconnectWorksheets();
		unset($objPHPExcel);
		unset($objWriter);
		exit;

	}	

	public function generate_spreadsheet_conduct( $parameters ){
		ini_set('memory_limit', '-1');
		$CI =& get_instance();
		$CI->load->model('Student_model');
		$CI->load->model('Class_model');
		$CI->load->model('Section_model');
		$CI->load->model('Subject_model');
		$CI->load->model('Setting_model');
		$CI->load->model('Template_model');
		$CI->load->model('Conduct_model');
		$CI->load->library('excel');
		$CI->load->library('spout');

		$class_id = $parameters['class_id'];
		$section_id = $parameters['section_id']; 
		$assign_conduct_array = $parameters['assign_conduct_array']; 
		$quarter = $parameters['quarter']; 
		$subject_name = null;
		$class_name = null;
		$teachers_name = null;
		$section_name = null;
		$get_school_name = $CI->Setting_model->getCurrentSchoolName();
		$boys_students = $CI->Student_model->getstudentsByClassSectionGender( $class_id, $section_id, 'Male');
		$girl_students = $CI->Student_model->getstudentsByClassSectionGender( $class_id, $section_id, 'Female'); 	 
		$template_file = 'uploads/template_documents/conducts/conduct_template.xlsx';
		$foundInCells = $CI->spout->search_cells( $template_file );  
		/* print_r( $foundInCells );
		die(); */
		if( $class_id != null ){
			$class_details = $CI->Class_model->get( $class_id ); 	
			$class_name = $class_details['class'];
		}
		
		if( $section_id != null ){
			$section_details = $CI->Section_model->get( $section_id ); 	
			$section_name = $section_details['section'];
		}

		include ("application/third_party/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
		 
		$inputFileName = $template_file; 
	
		try {
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFileName);
		} 
		
		catch(Exception $e) {
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}
		
		// BOYS
		$objPHPExcel->setActiveSheetIndex(0);
		$sheetInsertData = $objPHPExcel->getActiveSheet();
		$get_quarter = $this->get_quarter( $quarter );
		$sheetInsertData->setTitle($get_quarter );
		
		
		$school_name_cell = isset($foundInCells['{school_name}'])?$foundInCells['{school_name}']:null;
		$class_cell = isset($foundInCells['{class}'])?$foundInCells['{class}']:null;
		$quarter_cell = isset($foundInCells['{quarter}'])?$foundInCells['{quarter}']:null; 

		if( $school_name_cell != null ){
			$school_name_cell_x = $school_name_cell['rownumber'];
			$school_name_cell_y = $school_name_cell['columnnumber'];
			$sheetInsertData->setCellValueByColumnAndRow($school_name_cell_y,$school_name_cell_x, $get_school_name );
		}
		
		if( $class_cell != null ){
			$class_cell_x = $class_cell['rownumber'];
			$class_cell_y = $class_cell['columnnumber'];
			$sheetInsertData->setCellValueByColumnAndRow($class_cell_y,$class_cell_x, $class_name.' '.$section_name );
		}
		
		if( $quarter_cell != null ){
			$quarter_cell_x = $quarter_cell['rownumber'];
			$quarter_cell_y = $quarter_cell['columnnumber'];
			$sheetInsertData->setCellValueByColumnAndRow($quarter_cell_y,$quarter_cell_x, $get_quarter );
		} 
		//$assign_conduct_array = $CI->Conduct_model->getDetailByclassAndSection($class_id, $section_id);
		if( $assign_conduct_array ){
			$start_conduct_column = 4; 
			$start_conduct_row = 10;
			$start_conduct_row_code = 11;
			foreach( $assign_conduct_array as $key => $value) { 
				$conduct_id = $value['conduct_id'];
				$conduct_assign_id = $value['id'];
				$conduct_code = $value['code'];
				$conduct_name = $value['name'];

				$sheetInsertData->setCellValueByColumnAndRow( $start_conduct_column,$start_conduct_row, $conduct_name );
				$sheetInsertData->setCellValueByColumnAndRow( $start_conduct_column,$start_conduct_row_code, $conduct_code );  
				$start_conduct_column++;  
			}
		} 
		$start_boys_numbering = isset($foundInCells['{start_boys_numbering}'])?$foundInCells['{start_boys_numbering}']:null;
		$end_boys_numbering = isset($foundInCells['{end_boys_numbering}'])?$foundInCells['{end_boys_numbering}']:null;
		$start_boys = isset($foundInCells['{start_boys}'])?$foundInCells['{start_boys}']:null;
		$end_boys = isset($foundInCells['{end_boys}'])?$foundInCells['{end_boys}']:null;
		$boys_start_id = isset($foundInCells['{boys_start_id}'])?$foundInCells['{boys_start_id}']:null;
		$start_boys_numbering_x = $start_boys_numbering['rownumber'];
		$start_boys_numbering_y = $start_boys_numbering['columnnumber'];
		$end_boys_numbering_x = $end_boys_numbering['rownumber'];
		$start_boys_x = $start_boys['rownumber'];
		$start_boys_y = $start_boys['columnnumber'];
		$end_boys_x = $end_boys['rownumber'];
		$end_boys_y = $end_boys['columnnumber'];
		$boys_start_id_x = $boys_start_id['rownumber'];
		$boys_start_id_y = $boys_start_id['columnnumber'];
		$n=1;
		$start_boys_numbering_x++;
		$boys_start_id_x++;
		$start_boys_x++;
		
		for( $b=0; $start_boys_numbering_x < $end_boys_numbering_x ; $b++ )
		{
			set_time_limit(0);
			if( !empty( $boys_students[$b] ) ){
				$boy_details  = $boys_students[$b];
				$sheetInsertData->setCellValueByColumnAndRow($start_boys_numbering_y,$start_boys_numbering_x, $n );
				$sheetInsertData->setCellValueByColumnAndRow($boys_start_id_y,$boys_start_id_x, $boy_details['admission_no'] ); 
				$sheetInsertData->setCellValueByColumnAndRow($start_boys_y,$start_boys_x, $boy_details['lastname'].", ".$boy_details['firstname'] );
			}
			
			$start_boys_numbering_x++;
			$boys_start_id_x++;
			$start_boys_x++;
			$n++;
		}  
		// GIRLS
		$start_girls_numbering = isset($foundInCells['{start_girls_numbering}'])?$foundInCells['{start_girls_numbering}']:null;
		$end_girls_numbering = isset($foundInCells['{end_girls_numbering}'])?$foundInCells['{end_girls_numbering}']:null;
		$start_girls = isset($foundInCells['{start_girls}'])?$foundInCells['{start_girls}']:null;
		$end_girls = isset($foundInCells['{end_girls}'])?$foundInCells['{end_girls}']:null;
		$girls_start_id = isset($foundInCells['{girls_start_id}'])?$foundInCells['{girls_start_id}']:null;

		$start_girls_numbering_x = $start_girls_numbering['rownumber'];
		$start_girls_numbering_y = $start_girls_numbering['columnnumber'];
		$end_girls_numbering_x = $end_girls_numbering['rownumber'];
		$start_girls_x = $start_girls['rownumber'];
		$start_girls_y = $start_girls['columnnumber'];
		$end_girls_x = $end_girls['rownumber'];
		$end_girls_y = $end_girls['columnnumber'];
		$girls_start_id_x = $girls_start_id['rownumber'];
		$girls_start_id_y = $girls_start_id['columnnumber'];
		
		$n=1;
		$start_girls_numbering_x++;
		$girls_start_id_x++;
		$start_girls_x++;
		for( $b=0; $start_girls_numbering_x < $end_girls_numbering_x ; $b++ )
		{
			set_time_limit(0);
			if( !empty( $girl_students[$b] ) ){
				$girl_details  = $girl_students[$b];
				$sheetInsertData->setCellValueByColumnAndRow($start_girls_numbering_y,$start_girls_numbering_x, $n );
				$sheetInsertData->setCellValueByColumnAndRow($girls_start_id_y,$girls_start_id_x, $girl_details['admission_no'] ); 
				$sheetInsertData->setCellValueByColumnAndRow($start_girls_y,$start_girls_x, $girl_details['lastname'].", ".$girl_details['firstname'] );
			}
			
			$start_girls_numbering_x++;
			$girls_start_id_x++;
			$start_girls_x++;
			$n++;
		}  
		
		$date = date('Ymdhis');
		$filename = $class_name.' '.$section_name.'- Conduct - '.$date;
		$filename = $filename.'.xlsx';
		//header('Content-Type: application/vnd.ms-excel'); //mime type
		// Clear all output buffers to prevent download issues
		while (ob_get_level()) {
			ob_end_clean();
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
					
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
		$objPHPExcel->disconnectWorksheets();
		unset($objPHPExcel);
		unset($objWriter);
		exit;
	}

	public function generate_spreadsheet_custom( $parameters ){

		ini_set('memory_limit', '-1');
		$CI =& get_instance();
		$CI->load->model('Student_model');
		$CI->load->model('Class_model');
		$CI->load->model('Section_model');
		$CI->load->model('Subject_model');
		$CI->load->model('Setting_model');
		$CI->load->model('Template_model');
		$CI->load->library('excel');
		$CI->load->library('spout');

		$student_id = $parameters['student_id'];
		$subject_id = $parameters['subject_id'];
		$template_id = $parameters['template_id'];
		$subject_name = null;
		$class_name = null;
		$teachers_name = null;
		$section_name = null;
		
		$student_details = $CI->student_model->get( $student_id );
		$class_id = $student_details['class_id'];
		$section_id = $student_details['section_id'];
		$gender = isset($student_details['gender'])?$student_details['gender']:'Male';
		$get_school_name = $CI->Setting_model->getCurrentSchoolName();
		$get_details[] = $student_details;
		$boys_students = $gender == 'Male'?$get_details:array();
		$girl_students = $gender == 'Female'?$get_details:array();

		$template_details = $CI->Template_model->get( $template_id ); 	
		$template_file = isset($template_details['file'])?$template_details['file']:null;
		$foundInCells = $CI->spout->search_cells( $template_file );  
		
		if( $class_id != null ){
			$class_details = $CI->Class_model->get( $class_id ); 	
			$class_name = $class_details['class'];
		}
		
		if( $section_id != null ){
			$section_details = $CI->Section_model->get( $section_id ); 	
			$section_name = $section_details['section'];
		}
		
		if( $subject_id != null ){
			$subject_details = $CI->Subject_model->get( $subject_id ); 	
			$subject_name = $subject_details['name']; 	
		}

		include ("application/third_party/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
		
		if( $template_file!= null ){
			$inputFileName = $template_file;
		} else {
			$inputFileName = 'uploads/GradingTemplate.xlsx';
		}

		try {
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFileName);
		} 
		
		catch(Exception $e) {
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

		// BOYS
		$objPHPExcel->setActiveSheetIndex(0);
		$sheetInsertData = $objPHPExcel->getActiveSheet();
		
		$school_name_cell = isset($foundInCells['{school_name}'])?$foundInCells['{school_name}']:null;
		$subject_name_cell = isset($foundInCells['{subject_name}'])?$foundInCells['{subject_name}']:null;
		$class_cell = isset($foundInCells['{class}'])?$foundInCells['{class}']:null; 
		$subject_teacher_cell = isset($foundInCells['{subject_teacher}'])?$foundInCells['{subject_teacher}']:null;

		if( $school_name_cell != null ){
			$school_name_cell_x = $school_name_cell['rownumber'];
			$school_name_cell_y = $school_name_cell['columnnumber'];
			$sheetInsertData->setCellValueByColumnAndRow($school_name_cell_y,$school_name_cell_x, $get_school_name );
		}
		
		if( $class_cell != null ){
			$class_cell_x = $class_cell['rownumber'];
			$class_cell_y = $class_cell['columnnumber'];
			$sheetInsertData->setCellValueByColumnAndRow($class_cell_y,$class_cell_x, $class_name.' '.$section_name );
		}
		
		
		if( $subject_name_cell != null ){
			$subject_name_cell_x = $subject_name_cell['rownumber'];
			$subject_name_cell_y = $subject_name_cell['columnnumber'];
			$sheetInsertData->setCellValueByColumnAndRow($subject_name_cell_y,$subject_name_cell_x, $subject_name );
		}

		$start_boys_numbering = isset($foundInCells['{start_boys_numbering}'])?$foundInCells['{start_boys_numbering}']:null;
		$end_boys_numbering = isset($foundInCells['{end_boys_numbering}'])?$foundInCells['{end_boys_numbering}']:null;
		$start_boys = isset($foundInCells['{start_boys}'])?$foundInCells['{start_boys}']:null;
		$end_boys = isset($foundInCells['{end_boys}'])?$foundInCells['{end_boys}']:null;
		$boys_start_id = isset($foundInCells['{boys_start_id}'])?$foundInCells['{boys_start_id}']:null;
		$start_boys_numbering_x = $start_boys_numbering['rownumber'];
		$start_boys_numbering_y = $start_boys_numbering['columnnumber'];
		$end_boys_numbering_x = $end_boys_numbering['rownumber'];
		$start_boys_x = $start_boys['rownumber'];
		$start_boys_y = $start_boys['columnnumber'];
		$end_boys_x = $end_boys['rownumber'];
		$end_boys_y = $end_boys['columnnumber'];
		$boys_start_id_x = $boys_start_id['rownumber'];
		$boys_start_id_y = $boys_start_id['columnnumber'];
		$n=1;
		$start_boys_numbering_x++;
		$boys_start_id_x++;
		$start_boys_x++;
		for( $b=0; $start_boys_numbering_x < $end_boys_numbering_x ; $b++ )
		{
			set_time_limit(0);
			if( !empty( $boys_students[$b] ) ){
				$boy_details  = $boys_students[$b];
				$sheetInsertData->setCellValueByColumnAndRow($start_boys_numbering_y,$start_boys_numbering_x, $n );
				$sheetInsertData->setCellValueByColumnAndRow($boys_start_id_y,$boys_start_id_x, $boy_details['admission_no'] ); 
				$sheetInsertData->setCellValueByColumnAndRow($start_boys_y,$start_boys_x, $boy_details['lastname'].", ".$boy_details['firstname'] );
			}
			
			$start_boys_numbering_x++;
			$boys_start_id_x++;
			$start_boys_x++;
			$n++;
		}  
	// GIRLS
		$start_girls_numbering = isset($foundInCells['{start_girls_numbering}'])?$foundInCells['{start_girls_numbering}']:null;
		$end_girls_numbering = isset($foundInCells['{end_girls_numbering}'])?$foundInCells['{end_girls_numbering}']:null;
		$start_girls = isset($foundInCells['{start_girls}'])?$foundInCells['{start_girls}']:null;
		$end_girls = isset($foundInCells['{end_girls}'])?$foundInCells['{end_girls}']:null;
		$girls_start_id = isset($foundInCells['{girls_start_id}'])?$foundInCells['{girls_start_id}']:null;

		$start_girls_numbering_x = $start_girls_numbering['rownumber'];
		$start_girls_numbering_y = $start_girls_numbering['columnnumber'];
		$end_girls_numbering_x = $end_girls_numbering['rownumber'];
		$start_girls_x = $start_girls['rownumber'];
		$start_girls_y = $start_girls['columnnumber'];
		$end_girls_x = $end_girls['rownumber'];
		$end_girls_y = $end_girls['columnnumber'];
		$girls_start_id_x = $girls_start_id['rownumber'];
		$girls_start_id_y = $girls_start_id['columnnumber'];

		$n=1;
		$start_girls_numbering_x++;
		$girls_start_id_x++;
		$start_girls_x++;
		for( $b=0; $start_girls_numbering_x < $end_girls_numbering_x ; $b++ )
		{
			set_time_limit(0);
			if( !empty( $girl_students[$b] ) ){
				$girl_details  = $girl_students[$b];
				$sheetInsertData->setCellValueByColumnAndRow($start_girls_numbering_y,$start_girls_numbering_x, $n );
				$sheetInsertData->setCellValueByColumnAndRow($girls_start_id_y,$girls_start_id_x, $girl_details['admission_no'] ); 
				$sheetInsertData->setCellValueByColumnAndRow($start_girls_y,$start_girls_x, $girl_details['lastname'].", ".$girl_details['firstname'] );
			}
			
			$start_girls_numbering_x++;
			$girls_start_id_x++;
			$start_girls_x++;
			$n++;
		}  
				

		$date = date('Ymdhis');
		$filename = $subject_name.'-'.$class_name.' '.$section_name.'-'.$date;
		$filename= $filename.'.xlsx';
		//header('Content-Type: application/vnd.ms-excel'); //mime type
		// Clear all output buffers to prevent download issues
		while (ob_get_level()) {
			ob_end_clean();
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
					
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
		$objPHPExcel->disconnectWorksheets();
		unset($objPHPExcel);
		unset($objWriter);
		exit;
	}
	
	public function get_quarter( $quarter ){
		
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
}

