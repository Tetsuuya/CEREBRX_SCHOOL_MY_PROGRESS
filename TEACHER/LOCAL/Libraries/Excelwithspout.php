<?php

if (!defined('BASEPATH')) exit('No direct script access allowed'); 



require_once APPPATH."/third_party/PHPExcel-1.8/Classes/PHPExcel.php";



class Excelwithspout extends PHPExcel {

	

    public function __construct() {

        parent::__construct();

		

    }

	


	/*
	// LEGACY PHPEXCEL APPROACH (from BACKUP)
	public function generate_spreadsheet_OLD( $parameters ){
		
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
		// print_r( $foundInCells );
		// die();
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
	*/

	public function generate_spreadsheet( $parameters ){
		
		// Clear the debug log for a fresh run so it does not grow indefinitely
		@file_put_contents(APPPATH . 'logs/excel_generation_debug.txt', '');

		// Buffer all output. On success we wipe it and send a clean file download.
		// On any error/crash, the buffer is flushed automatically → error shows in browser.
		ob_start();
		
		ini_set('memory_limit', '2048M');
		set_time_limit(0);
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

		$generationStartTime = microtime(true);
		$this->log_debug("--- START EXCEL GENERATION ---");
		$this->log_debug("Params: Class ID: {$class_id}, Section ID: {$section_id}, Subject ID: {$subject_id}, Template ID: {$template_id}");
		$this->log_debug("Template File in DB: " . ($template_file ? $template_file : "NULL"));

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

		$this->log_debug("Loading template file from disk: {$inputFileName}");
		$this->log_debug("Initial memory usage: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB");
		
		if (!file_exists($inputFileName)) {
			$this->log_debug("ERROR: File does not exist at path: {$inputFileName}");
			die('Error: File does not exist at path: ' . $inputFileName);
		}
		
		$date = date('Ymdhis');
		$filename = $subject_name.'-'.$class_name.' '.$section_name.'-'.$date;
		$filename= $filename.'.xlsx';
		
		$temp_dir = sys_get_temp_dir();
		$public_file = $temp_dir . DIRECTORY_SEPARATOR . $filename;
		$this->log_debug("Target file path (temp): {$public_file}");
		
		// Copy original template to target output path
		if (!copy($inputFileName, $public_file)) {
			$this->log_debug("  ERROR: Failed to copy template file to target output path.");
			die('Error: Could not copy template file.');
		}
		
		$zip = new ZipArchive();
		if ($zip->open($public_file) !== TRUE) {
			$this->log_debug("  ERROR: Could not open output Excel file.");
			die('Error: Could not open output Excel file.');
		}
		
		// 1. Read and modify sharedStrings.xml
		$this->log_debug("Reading sharedStrings.xml...");
		$sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
		if ($sharedStringsXml === false) {
			$zip->close();
			$this->log_debug("  ERROR: Could not read sharedStrings.xml from template.");
			die('Error: Could not read sharedStrings.xml from template.');
		}
		
		// Prepare metadata replacements
		$fullname = '';
		if ($teacher_result) {
			$firstname = $teacher_result['name'];
			$lastname = $teacher_result['lastname'];
			$middlename = $teacher_result['middlename'];
			if ($middlename) {
				$fullname = $lastname.', '.$firstname.' '.$middlename;
			} else {
				$fullname = $lastname.', '.$firstname;
			}
		}
		
		$replacements = array(
			'{school_name}' => $get_school_name,
			'{class}' => $class_name . ' ' . $section_name,
			'{subject_name}' => $subject_name,
			'{subject_teacher}' => $fullname,
			'{school_year}' => date('Y') . '-' . (date('Y') + 1),
			'{written_work}' => $written_work ? ($written_work / 100) : '',
			'{performance_tasks}' => $performance_task ? ($performance_task / 100) : '',
			'{quarterly_assessment}' => $quarterly_assesment ? ($quarterly_assesment / 100) : ''
		);
		
		foreach ($replacements as $key => $val) {
			$sharedStringsXml = str_replace($key, htmlspecialchars($val), $sharedStringsXml);
		}
		
		// Parse shared strings to find placeholder indices
		preg_match_all('/<si[^>]*>.*?<\/si>/s', $sharedStringsXml, $si_matches);
		$si_items = $si_matches[0];
		
		$placeholder_indices = array();
		foreach ($si_items as $idx => $si) {
			if (preg_match('/<t[^>]*>(.*?)<\/t>/s', $si, $t_match)) {
				$txt = trim($t_match[1]);
				if (strpos($txt, '{') !== false) {
					$placeholder_indices[$txt] = $idx;
				}
			}
		}
		
		// 2. Read and modify sheet1.xml
		$this->log_debug("Reading xl/worksheets/sheet1.xml...");
		$sheet1Xml = $zip->getFromName('xl/worksheets/sheet1.xml');
		if ($sheet1Xml === false) {
			$zip->close();
			$this->log_debug("  ERROR: Could not read sheet1.xml from template.");
			die('Error: Could not read sheet1.xml from template.');
		}
		
		// Helper function to find cell coordinate for placeholder
		$find_cell = function($xml, $idx) {
			$pattern = '/<c\s+r="([A-Z]+)(\d+)"[^>]*t="s"[^>]*>\s*<v>' . $idx . '<\/v>\s*<\/c>/s';
			if (preg_match($pattern, $xml, $match)) {
				return array('col' => $match[1], 'row' => (int)$match[2]);
			}
			return null;
		};
		
		$boys_name_cell = $find_cell($sheet1Xml, isset($placeholder_indices['{start_boys}']) ? $placeholder_indices['{start_boys}'] : -1);
		$boys_num_cell = $find_cell($sheet1Xml, isset($placeholder_indices['{start_boys_numbering}']) ? $placeholder_indices['{start_boys_numbering}'] : -1);
		$boys_id_cell = $find_cell($sheet1Xml, isset($placeholder_indices['{boys_start_id}']) ? $placeholder_indices['{boys_start_id}'] : -1);
		$girls_name_cell = $find_cell($sheet1Xml, isset($placeholder_indices['{start_girls}']) ? $placeholder_indices['{start_girls}'] : -1);
		
		$boys_end_cell = $find_cell($sheet1Xml, isset($placeholder_indices['{end_boys}']) ? $placeholder_indices['{end_boys}'] : -1);
		$girls_end_cell = $find_cell($sheet1Xml, isset($placeholder_indices['{end_girls}']) ? $placeholder_indices['{end_girls}'] : -1);
		
		$boys_col = $boys_name_cell ? $boys_name_cell['col'] : 'C';
		$boys_start_row = $boys_name_cell ? $boys_name_cell['row'] : 11;
		$num_col = $boys_num_cell ? $boys_num_cell['col'] : 'A';
		$id_col = $boys_id_cell ? $boys_id_cell['col'] : 'B';
		$boys_end_row = $boys_end_cell ? $boys_end_cell['row'] : 61;
		
		$girls_col = $girls_name_cell ? $girls_name_cell['col'] : 'C';
		$girls_start_row = $girls_name_cell ? $girls_name_cell['row'] : 63;
		$girls_end_row = $girls_end_cell ? $girls_end_cell['row'] : 112;
		
		$this->log_debug("Columns: NameCol={$boys_col}, NumCol={$num_col}, IdCol={$id_col}");
		$this->log_debug("Boys Row range: {$boys_start_row} to {$boys_end_row}");
		$this->log_debug("Girls Row range: {$girls_start_row} to {$girls_end_row}");
		
		$inject_students = function($xml, $start_row, $end_row, $students, $col_name, $col_num, $col_id, $is_boy) use (&$total_written) {
			$count = 0;
			$total = count($students);
			
			// 1. Populate students
			for ($i = 0; $i < $total; $i++) {
				$row_num = $start_row + $i + 1;
				$num_val = $i + 1;
				$student = $students[$i];
				$name = htmlspecialchars($student['lastname'] . ', ' . $student['firstname']);
				$adm_no = htmlspecialchars($student['admission_no']);
				
				$old_name_tag = '<c r="' . $col_name . $row_num . '" s="232"/>';
				$new_name_tag = '<c r="' . $col_name . $row_num . '" s="232" t="inlineStr"><is><t>' . $name . '</t></is></c>';
				
				$old_num_tag = '<c r="' . $col_num . $row_num . '" s="33"/>';
				$new_num_tag = '<c r="' . $col_num . $row_num . '" s="33"><v>' . $num_val . '</v></c>';
				
				$old_id_tag = '<c r="' . $col_id . $row_num . '" s="231"/>';
				$new_id_tag = '<c r="' . $col_id . $row_num . '" s="231" t="inlineStr"><is><t>' . $adm_no . '</t></is></c>';
				
				if (strpos($xml, $old_name_tag) !== false) {
					$xml = str_replace($old_name_tag, $new_name_tag, $xml);
					$xml = str_replace($old_num_tag, $new_num_tag, $xml);
					$xml = str_replace($old_id_tag, $new_id_tag, $xml);
					$count++;
				} else {
					// Regex fallback
					$pattern_name = '/<c\s+r="' . $col_name . $row_num . '"\s+s="(\d+)"\/>/s';
					if (preg_match($pattern_name, $xml, $m)) {
						$style_name = $m[1];
						$xml = preg_replace($pattern_name, '<c r="' . $col_name . $row_num . '" s="' . $style_name . '" t="inlineStr"><is><t>' . $name . '</t></is></c>', $xml);
						
						$pattern_num = '/<c\s+r="' . $col_num . $row_num . '"\s+s="(\d+)"\/>/s';
						if (preg_match($pattern_num, $xml, $m_num)) {
							$xml = preg_replace($pattern_num, '<c r="' . $col_num . $row_num . '" s="' . $m_num[1] . '"><v>' . $num_val . '</v></c>', $xml);
						}
						
						$pattern_id = '/<c\s+r="' . $col_id . $row_num . '"\s+s="(\d+)"\/>/s';
						if (preg_match($pattern_id, $xml, $m_id)) {
							$xml = preg_replace($pattern_id, '<c r="' . $col_id . $row_num . '" s="' . $m_id[1] . '" t="inlineStr"><is><t>' . $adm_no . '</t></is></c>', $xml);
						}
						$count++;
					}
				}
			}
			
			// 2. Hide unused rows
			$unused_start = $start_row + $total + 1;
			$unused_end = $end_row - 1;
			
			for ($r = $unused_start; $r <= $unused_end; $r++) {
				$xml = preg_replace_callback('/<row r="' . $r . '"\b[^>]*?>/s', function($m) {
					if (strpos($m[0], 'hidden=') === false) {
						return str_replace('>', ' hidden="1">', $m[0]);
					}
					return $m[0];
				}, $xml);
			}
			
			return array('xml' => $xml, 'count' => $count);
		};
		
		$total_boys_written = 0;
		$total_girls_written = 0;
		
		$res_boys = $inject_students($sheet1Xml, $boys_start_row, $boys_end_row, $boys_students, $boys_col, $num_col, $id_col, true);
		$sheet1Xml = $res_boys['xml'];
		$total_boys_written = $res_boys['count'];
		
		$res_girls = $inject_students($sheet1Xml, $girls_start_row, $girls_end_row, $girl_students, $girls_col, $num_col, $id_col, false);
		$sheet1Xml = $res_girls['xml'];
		$total_girls_written = $res_girls['count'];
		
		$this->log_debug("XML Generation: Injected {$total_boys_written} boys, {$total_girls_written} girls.");
		
		// Update job status if provided
		if (isset($parameters['status_file'])) {
			file_put_contents($parameters['status_file'], json_encode([
				'status' => 'processing',
				'progress' => 80,
				'message' => 'Saving Excel file...'
			]));
		}
		
		// Save modified files back to output ZIP
		$zip->addFromString('xl/sharedStrings.xml', $sharedStringsXml);
		$zip->addFromString('xl/worksheets/sheet1.xml', $sheet1Xml);
		$zip->close();
		
		// Check file was created
		if (!file_exists($public_file)) {
			$this->log_debug("ERROR: File not created!");
			if (isset($parameters['status_file'])) {
				file_put_contents($parameters['status_file'], json_encode([
					'status' => 'error',
					'message' => 'Could not create Excel file'
				]));
			}
			die('Error: Could not create Excel file.');
		}
		
		$file_size = filesize($public_file);
		$download_url = base_url() . 'teacher/grade/download_file/' . urlencode(isset($parameters['job_id']) ? $parameters['job_id'] : '');
		$totalTime = microtime(true) - $generationStartTime;
		
		$this->log_debug("=== GENERATION COMPLETE - SUMMARY ===");
		$this->log_debug("Template: " . basename($inputFileName));
		$this->log_debug("Generated file (temp): {$filename}");
		$this->log_debug("Download endpoint: {$download_url}");
		$this->log_debug("Total execution time: " . round($totalTime, 2) . " seconds");
		$this->log_debug("Final memory usage: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB");
		$this->log_debug("Peak memory usage: " . round(memory_get_peak_usage(true) / 1024 / 1024, 2) . " MB");
		$this->log_debug("Students: {$total_boys_written} boys + {$total_girls_written} girls = " . ($total_boys_written + $total_girls_written) . " total");
		$this->log_debug("--- EXCEL GENERATION SUCCESS ---");
		
		// Update job status to complete
		if (isset($parameters['status_file'])) {
			file_put_contents($parameters['status_file'], json_encode([
				'status' => 'complete',
				'progress' => 100,
				'filename' => $filename,
				'temp_file' => $public_file,
				'download_url' => $download_url,
				'file_size' => round($file_size / 1024 / 1024, 2) . ' MB',
				'completed_at' => date('Y-m-d H:i:s')
			]));
			exit;
		}
		
		// Fallback for non-AJAX requests: stream directly
		while (ob_get_level()) {
			ob_end_clean();
		}
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Length: ' . $file_size);
		header('Cache-Control: max-age=0');
		readfile($public_file);
		@unlink($public_file);
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

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type

		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name

		header('Cache-Control: max-age=0'); //no cache

					

		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)

		//if you want to save it as .XLSX Excel 2007 format

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  

		//force user to download the Excel file without writing it to server's HD

		$objWriter->save('php://output');

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

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type

		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name

		header('Cache-Control: max-age=0'); //no cache

					

		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)

		//if you want to save it as .XLSX Excel 2007 format

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  

		//force user to download the Excel file without writing it to server's HD

		$objWriter->save('php://output');

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


	
	/**
	 * Fix hidden rows in XML (PHPExcel bug workaround)
	 * PHPExcel doesn't reliably save hidden="1" to XML even after setVisible(false) + setRowHeight()
	 * This function manually injects hidden="1" attributes into the sheet XML files
	 * 
	 * @param string $excelFile Path to the saved Excel file
	 * @param array $allSheetsHiddenRows Array of hidden rows per sheet index
	 */
	private function fix_hidden_rows_in_xml($excelFile, $allSheetsHiddenRows) {
		if (!file_exists($excelFile)) {
			$this->log_debug("  ERROR: File not found for post-processing: {$excelFile}");
			return;
		}
		
		// Excel files are ZIP archives - we'll extract, modify XML, and re-zip
		$tempDir = sys_get_temp_dir() . '/excel_fix_' . uniqid();
		mkdir($tempDir, 0755, true);
		
		$this->log_debug("  Extracting Excel ZIP to temp dir: {$tempDir}");
		
		// Extract the ZIP
		$zip = new ZipArchive();
		if ($zip->open($excelFile) !== TRUE) {
			$this->log_debug("  ERROR: Could not open Excel file as ZIP");
			return;
		}
		
		$zip->extractTo($tempDir);
		$zip->close();
		
		$fixedCount = 0;
		
		// Process each sheet
		foreach ($allSheetsHiddenRows as $sheetIndex => $hiddenRows) {
			if (empty($hiddenRows)) {
				continue;
			}
			
			// Sheet XML files are named sheet1.xml, sheet2.xml, etc. (1-indexed)
			$sheetNum = $sheetIndex + 1;
			$sheetXmlPath = $tempDir . '/xl/worksheets/sheet' . $sheetNum . '.xml';
			
			if (!file_exists($sheetXmlPath)) {
				$this->log_debug("  WARNING: Sheet XML not found: {$sheetXmlPath}");
				continue;
			}
			
			// Read the XML
			$xmlContent = file_get_contents($sheetXmlPath);
			if ($xmlContent === false) {
				$this->log_debug("  ERROR: Could not read sheet XML: {$sheetXmlPath}");
				continue;
			}
			
			// Parse XML to ensure it's valid
			$xml = simplexml_load_string($xmlContent);
			if ($xml === false) {
				$this->log_debug("  ERROR: Invalid XML in sheet: {$sheetXmlPath}");
				continue;
			}
			
			$sheetFixed = 0;
			
			// For each hidden row, inject hidden="1" attribute
			foreach ($hiddenRows as $rowNum) {
				// Check if row element exists
				// Pattern: <row r="12" ...> or <row r="12"/>
				$pattern = '/<row\s+r="' . $rowNum . '"([^>]*)>/';
				
				if (preg_match($pattern, $xmlContent, $matches)) {
					$attributes = $matches[1];
					
					// Check if hidden="1" already exists
					if (strpos($attributes, 'hidden=') !== false) {
						// Already has hidden attribute - skip
						continue;
					}
					
					// Inject hidden="1" attribute
					// Replace: <row r="12" ...> with <row r="12" hidden="1" ...>
					$newAttributes = ' hidden="1"' . $attributes;
					$replacement = '<row r="' . $rowNum . '"' . $newAttributes . '>';
					$xmlContent = preg_replace($pattern, $replacement, $xmlContent);
					
					$sheetFixed++;
				} else {
					// Row element doesn't exist - create it
					// This is rare but possible if PHPExcel skipped the row entirely
					$this->log_debug("    WARNING: Row {$rowNum} not found in XML - cannot inject hidden attribute");
				}
			}
			
			if ($sheetFixed > 0) {
				// Write modified XML back to file
				file_put_contents($sheetXmlPath, $xmlContent);
				$fixedCount += $sheetFixed;
				$this->log_debug("  Sheet {$sheetNum}: Injected hidden=\"1\" for {$sheetFixed} rows");
			}
		}
		
		if ($fixedCount > 0) {
			$this->log_debug("  Total rows fixed: {$fixedCount}");
			$this->log_debug("  Re-zipping Excel file...");
			
			// Re-create the ZIP file
			$zip = new ZipArchive();
			if ($zip->open($excelFile, ZipArchive::OVERWRITE) !== TRUE) {
				$this->log_debug("  ERROR: Could not re-open Excel file for writing");
				$this->cleanup_temp_dir($tempDir);
				return;
			}
			
			// Add all files back to ZIP
			$files = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($tempDir),
				RecursiveIteratorIterator::LEAVES_ONLY
			);
			
			foreach ($files as $file) {
				if (!$file->isDir()) {
					$filePath = $file->getRealPath();
					$relativePath = substr($filePath, strlen($tempDir) + 1);
					
					// Use forward slashes for ZIP paths (Windows uses backslashes)
					$relativePath = str_replace('\\', '/', $relativePath);
					
					$zip->addFile($filePath, $relativePath);
				}
			}
			
			$zip->close();
			$this->log_debug("  Excel file re-zipped successfully");
		} else {
			$this->log_debug("  No rows needed fixing (all were already correct in XML)");
		}
		
		// Cleanup temp directory
		$this->cleanup_temp_dir($tempDir);
	}
	
	/**
	 * Recursively delete a directory and all its contents
	 */
	private function cleanup_temp_dir($dir) {
		if (!is_dir($dir)) {
			return;
		}
		
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
			RecursiveIteratorIterator::CHILD_FIRST
		);
		
		foreach ($files as $file) {
			if ($file->isDir()) {
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}
		
		rmdir($dir);
		$this->log_debug("  Temp directory cleaned up: {$dir}");
	}
	
	private function set_cell_value($sheet, $col, $row, $value, $is_string = false) {
		if ($is_string) {
			$sheet->setCellValueExplicitByColumnAndRow($col, $row, $value, PHPExcel_Cell_DataType::TYPE_STRING);
		} else {
			$sheet->setCellValueByColumnAndRow($col, $row, $value);
		}
	}
	
	private function merge_modified_sheet($targetZipFile, $sourceZipFile) {
		$this->log_debug("  Opening target zip file: {$targetZipFile}");
		$zipTarget = new ZipArchive();
		if ($zipTarget->open($targetZipFile) !== TRUE) {
			$this->log_debug("  ERROR: Could not open target zip file: {$targetZipFile}");
			return false;
		}
		
		$this->log_debug("  Opening source zip file: {$sourceZipFile}");
		$zipSource = new ZipArchive();
		if ($zipSource->open($sourceZipFile) !== TRUE) {
			$this->log_debug("  ERROR: Could not open source zip file: {$sourceZipFile}");
			$zipTarget->close();
			return false;
		}
		
		// Read sheet1.xml from source ZIP
		$this->log_debug("  Reading xl/worksheets/sheet1.xml from source...");
		$sheetXml = $zipSource->getFromName('xl/worksheets/sheet1.xml');
		if ($sheetXml === false) {
			$this->log_debug("  ERROR: Could not read xl/worksheets/sheet1.xml from source.");
			$zipSource->close();
			$zipTarget->close();
			return false;
		}
		
		// Read shared strings from target (template)
		$this->log_debug("  Reading shared strings from target...");
		$targetSharedStringsXml = $zipTarget->getFromName('xl/sharedStrings.xml');
		$targetStrings = array();
		if ($targetSharedStringsXml !== false) {
			preg_match_all('/<si\b[^>]*>.*?<\/si>/s', $targetSharedStringsXml, $matches);
			$targetStrings = $matches[0];
		}
		
		$targetLookup = array();
		foreach ($targetStrings as $idx => $si) {
			$cleanSi = preg_replace('/\s+/', ' ', $si);
			$targetLookup[$cleanSi] = $idx;
		}
		
		// Read shared strings from source
		$this->log_debug("  Reading shared strings from source...");
		$sourceSharedStringsXml = $zipSource->getFromName('xl/sharedStrings.xml');
		$sourceStrings = array();
		if ($sourceSharedStringsXml !== false) {
			preg_match_all('/<si\b[^>]*>.*?<\/si>/s', $sourceSharedStringsXml, $matches);
			$sourceStrings = $matches[0];
		}
		
		$zipSource->close();
		
		// Map shared string indices
		$this->log_debug("  Mapping shared string indices in sheet1.xml...");
		$pattern = '/(<c[^>]*\bt="s"[^>]*>.*?<v>)(\d+)(<\/v>)/s';
		
		$sheetXml = preg_replace_callback($pattern, function($matches) use ($sourceStrings, &$targetStrings, &$targetLookup) {
			$sourceIndex = (int)$matches[2];
			if (!isset($sourceStrings[$sourceIndex])) {
				return $matches[0];
			}
			
			$siElement = $sourceStrings[$sourceIndex];
			$cleanSi = preg_replace('/\s+/', ' ', $siElement);
			
			if (!isset($targetLookup[$cleanSi])) {
				$newIndex = count($targetStrings);
				$targetStrings[] = $siElement;
				$targetLookup[$cleanSi] = $newIndex;
			}
			
			$targetIndex = $targetLookup[$cleanSi];
			return $matches[1] . $targetIndex . $matches[3];
		}, $sheetXml);
		
		// Write merged shared strings back to target ZIP
		$this->log_debug("  Writing merged shared strings back to target ZIP...");
		$newSharedStringsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
			. '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($targetStrings) . '" uniqueCount="' . count($targetStrings) . '">'
			. implode('', $targetStrings)
			. '</sst>';
		
		$zipTarget->addFromString('xl/sharedStrings.xml', $newSharedStringsXml);
		
		// Write modified sheet1.xml to target ZIP
		$this->log_debug("  Replacing xl/worksheets/sheet1.xml in target ZIP...");
		if ($zipTarget->addFromString('xl/worksheets/sheet1.xml', $sheetXml)) {
			$zipTarget->close();
			$this->log_debug("  Merge successful.");
			return true;
		} else {
			$this->log_debug("  ERROR: Failed to add sheet1.xml to target ZIP.");
			$zipTarget->close();
			return false;
		}
	}
	
	private function log_debug($message) {
		$log_file = APPPATH . 'logs/excel_generation_debug.txt';
		$timestamp = date('Y-m-d H:i:s');
		file_put_contents($log_file, "[{$timestamp}] {$message}" . PHP_EOL, FILE_APPEND);
		// Echo only if we're in debug mode (check for ?debug=1 in URL)
		// This way you can see live output when testing but normal users get clean downloads
		if (isset($_GET['debug']) && $_GET['debug'] == '1') {
			echo "[{$timestamp}] " . htmlspecialchars($message) . "<br>\n";
		}
	}
}



