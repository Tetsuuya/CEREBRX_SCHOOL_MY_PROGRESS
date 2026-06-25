<?php

if (!defined('BASEPATH')) exit('No direct script access allowed'); 



require_once APPPATH."/third_party/PHPExcel-1.8/Classes/PHPExcel.php";



class Excelwithspout extends PHPExcel {

	

    public function __construct() {

        parent::__construct();

		

    }

	

	public function generate_spreadsheet( $parameters ){
		
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
		
		$fsize = filesize($inputFileName);
		$this->log_debug("File size: {$fsize} bytes (" . round($fsize / 1024 / 1024, 2) . " MB)");
		
		// Check magic header (first 4 bytes)
		$fh = fopen($inputFileName, 'r');
		if ($fh) {
			$magic = fread($fh, 4);
			fclose($fh);
			$hexMagic = bin2hex($magic);
			$this->log_debug("File magic bytes (hex): {$hexMagic}");
		} else {
			$this->log_debug("Warning: Could not open file to read magic bytes.");
		}
		
		$temp_dir = sys_get_temp_dir();
		$this->log_debug("System temp dir: {$temp_dir} | Writable: " . (is_writable($temp_dir) ? "YES" : "NO"));
		
		// Set cache storage method (disabled to prevent /tmp write locks/hangs on the server)
		/*
		try {
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
			$cacheSettings = array('memoryCacheSize' => '256MB');
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
			$this->log_debug("Cell caching (phpTemp) initialized.");
		} catch (Exception $e) {
			$this->log_debug("Failed to set cell caching: " . $e->getMessage());
		}
		*/
	
		try {
			$this->log_debug("Identifying Excel format...");
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$this->log_debug("Excel format identified: {$inputFileType}. Creating reader...");
			
			// Update job status
			if (isset($parameters['status_file'])) {
				file_put_contents($parameters['status_file'], json_encode([
					'status' => 'processing',
					'progress' => 10,
					'message' => 'Loading template file...'
				]));
			}
			
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$this->log_debug("Loading workbook into memory...");
			$objPHPExcel = $objReader->load($inputFileName);
			$this->log_debug("PHPExcel loaded template successfully.");
			$this->log_debug("Memory usage after load: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB");
			
			// Update job status
			if (isset($parameters['status_file'])) {
				file_put_contents($parameters['status_file'], json_encode([
					'status' => 'processing',
					'progress' => 30,
					'message' => 'Template loaded, processing sheets...'
				]));
			}
		} 
		catch(Exception $e) {
			$this->log_debug("Error loading template file: " . $e->getMessage() . "\n" . $e->getTraceAsString());
			if (isset($parameters['status_file'])) {
				file_put_contents($parameters['status_file'], json_encode([
					'status' => 'error',
					'message' => 'Error loading template: ' . $e->getMessage()
				]));
			}
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

		// Calculate decimal representations once outside the loop
		$written_work_pct = $written_work ? $written_work / 100 : null;
		$performance_task_pct = $performance_task ? $performance_task / 100 : null;
		$quarterly_assesment_pct = $quarterly_assesment ? $quarterly_assesment / 100 : null;

		$totalSheets = $objPHPExcel->getSheetCount();
		$this->log_debug("Total worksheets in workbook: {$totalSheets}");
		$this->log_debug("Initial file size on disk: " . round(filesize($inputFileName) / 1024 / 1024, 2) . " MB (" . filesize($inputFileName) . " bytes)");
		$this->log_debug("Memory after load: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB");

		// Store hidden rows for ALL sheets before processing
		$allSheetsHiddenRows = array();
		
		$this->log_debug("=== PHASE 1: DETECTING HIDDEN ROWS IN ALL SHEETS ===");
		for ($sheetIndex = 0; $sheetIndex < $totalSheets; $sheetIndex++) {
			$objPHPExcel->setActiveSheetIndex($sheetIndex);
			$sheetInsertData = $objPHPExcel->getActiveSheet();
			$sheetTitle = $sheetInsertData->getTitle();
			
			// CRITICAL: Preserve ONLY rows that were hidden in the original template
			// We do NOT want to hide additional rows after generation
			// We only want to keep hidden the rows that were hidden by the template designer
			$originallyHiddenRows = array();
			for ($row = 1; $row <= 150; $row++) {
				$rowDimension = $sheetInsertData->getRowDimension($row);
				// Check if row is explicitly hidden in template
				if (!$rowDimension->getVisible()) {
					$originallyHiddenRows[] = $row;
					$this->log_debug("  Sheet '{$sheetTitle}': Row {$row} is hidden in template");
				}
			}
			
			// Store hidden rows for this sheet
			$allSheetsHiddenRows[$sheetIndex] = $originallyHiddenRows;
			$this->log_debug("  Sheet '{$sheetTitle}': Stored " . count($originallyHiddenRows) . " hidden rows for later use");
		}
		
		$this->log_debug("Memory after Phase 1: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB");
		
		$this->log_debug("=== PHASE 2: DATA INSERTION ===");
		$total_boys_written = 0;
		$total_girls_written = 0;
		for ($sheetIndex = 0; $sheetIndex < $totalSheets; $sheetIndex++) {
			$objPHPExcel->setActiveSheetIndex($sheetIndex);
			$sheetInsertData = $objPHPExcel->getActiveSheet();
			$sheetTitle = $sheetInsertData->getTitle();
			
			// Scan the active sheet for placeholders dynamically (up to 150 rows and 60 columns)
			$sheetFoundCells = array();
			
			for ($row = 1; $row <= 150; $row++) {
				for ($col = 0; $col < 60; $col++) {
					$cell = $sheetInsertData->getCellByColumnAndRow($col, $row);
					$cellValue = $cell->getValue();
					if ($cellValue !== null && $cellValue !== '') {
						$cellValueClean = trim((string)$cellValue);
						if (strpos($cellValueClean, '{') === 0 && strpos($cellValueClean, '}') === (strlen($cellValueClean) - 1)) {
							$sheetFoundCells[$cellValueClean] = array(
								'rownumber' => $row,
								'columnnumber' => $col
							);
						}
					}
				}
			}

			// Check if this sheet is a student grade sheet (contains `{start_boys}` and `{start_girls}` placeholders)
			$start_boys = isset($sheetFoundCells['{start_boys}'])?$sheetFoundCells['{start_boys}']:null;
			$start_girls = isset($sheetFoundCells['{start_girls}'])?$sheetFoundCells['{start_girls}']:null;

			$has_boys = ($start_boys != null);
			$has_girls = ($start_girls != null);

			if ($has_boys || $has_girls) {
				$this->log_debug("Processing worksheet Index {$sheetIndex}: '{$sheetTitle}' (Grade Sheet detected)");
				
				$school_name_cell = isset($sheetFoundCells['{school_name}'])?$sheetFoundCells['{school_name}']:null;
				$subject_name_cell = isset($sheetFoundCells['{subject_name}'])?$sheetFoundCells['{subject_name}']:null;
				$class_cell = isset($sheetFoundCells['{class}'])?$sheetFoundCells['{class}']:null; 
				$subject_teacher_cell = isset($sheetFoundCells['{subject_teacher}'])?$sheetFoundCells['{subject_teacher}']:null;
				$written_work_cell = isset($sheetFoundCells['{written_work}'])?$sheetFoundCells['{written_work}']:null;
				$performance_tasks_cell = isset($sheetFoundCells['{performance_tasks}'])?$sheetFoundCells['{performance_tasks}']:null;
				$quarterly_assesment_cell = isset($sheetFoundCells['{quarterly_assessment}'])?$sheetFoundCells['{quarterly_assessment}']:null;

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
				
				if( $written_work_cell != null && $written_work_pct !== null  ){
					$written_work_cell_x = $written_work_cell['rownumber'];
					$written_work_cell_y = $written_work_cell['columnnumber'];
					$sheetInsertData->setCellValueByColumnAndRow($written_work_cell_y,$written_work_cell_x, $written_work_pct );
				}
				
				if( $performance_tasks_cell != null && $performance_task_pct !== null ){
					$performance_tasks_cell_x = $performance_tasks_cell['rownumber'];
					$performance_tasks_cell_y = $performance_tasks_cell['columnnumber'];
					$sheetInsertData->setCellValueByColumnAndRow($performance_tasks_cell_y,$performance_tasks_cell_x, $performance_task_pct );
				}
				
				if( $quarterly_assesment_cell != null && $quarterly_assesment_pct !== null ){
					$quarterly_assesment_cell_x = $quarterly_assesment_cell['rownumber'];
					$quarterly_assesment_cell_y = $quarterly_assesment_cell['columnnumber'];
					$sheetInsertData->setCellValueByColumnAndRow($quarterly_assesment_cell_y,$quarterly_assesment_cell_x, $quarterly_assesment_pct );
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

				$boys_written_count = 0;
				$start_boys_numbering = isset($sheetFoundCells['{start_boys_numbering}'])?$sheetFoundCells['{start_boys_numbering}']:null;
				$end_boys_numbering = isset($sheetFoundCells['{end_boys_numbering}'])?$sheetFoundCells['{end_boys_numbering}']:null;
				$boys_start_id = isset($sheetFoundCells['{boys_start_id}'])?$sheetFoundCells['{boys_start_id}']:null;
				$end_boys = isset($sheetFoundCells['{end_boys}'])?$sheetFoundCells['{end_boys}']:null;

				if ($start_boys_numbering && $end_boys_numbering && $start_boys && $end_boys) {
					$start_boys_numbering_x = $start_boys_numbering['rownumber'];
					$start_boys_numbering_y = $start_boys_numbering['columnnumber'];
					$end_boys_numbering_x = $end_boys_numbering['rownumber'];
					$start_boys_x = $start_boys['rownumber'];
					$start_boys_y = $start_boys['columnnumber'];
					$end_boys_x = $end_boys['rownumber'];
					$end_boys_y = $end_boys['columnnumber'];
					$boys_start_id_x = $boys_start_id ? $boys_start_id['rownumber'] : null;
					$boys_start_id_y = $boys_start_id ? $boys_start_id['columnnumber'] : null;
					$n=1;
					$start_boys_numbering_x++;
					if ($boys_start_id_x !== null) {
						$boys_start_id_x++;
					}
					$start_boys_x++;
					for( $b=0; $start_boys_numbering_x < $end_boys_numbering_x ; $b++ )
					{
						set_time_limit(0);
						if( !empty( $boys_students[$b] ) ){
							$boy_details  = $boys_students[$b];
							$sheetInsertData->setCellValueByColumnAndRow($start_boys_numbering_y,$start_boys_numbering_x, $n );
							if ($boys_start_id_y !== null && $boys_start_id_x !== null) {
								$sheetInsertData->setCellValueByColumnAndRow($boys_start_id_y,$boys_start_id_x, $boy_details['admission_no'] ); 
							}
							$sheetInsertData->setCellValueByColumnAndRow($start_boys_y,$start_boys_x, $boy_details['lastname'].", ".$boy_details['firstname'] );
							$boys_written_count++;
						}
						
						$start_boys_numbering_x++;
						if ($boys_start_id_x !== null) {
							$boys_start_id_x++;
						}
						$start_boys_x++;
						$n++;
					}
				}

				$girls_written_count = 0;
				$start_girls_numbering = isset($sheetFoundCells['{start_girls_numbering}'])?$sheetFoundCells['{start_girls_numbering}']:null;
				$end_girls_numbering = isset($sheetFoundCells['{end_girls_numbering}'])?$sheetFoundCells['{end_girls_numbering}']:null;
				$girls_start_id = isset($sheetFoundCells['{girls_start_id}'])?$sheetFoundCells['{girls_start_id}']:null;
				$end_girls = isset($sheetFoundCells['{end_girls}'])?$sheetFoundCells['{end_girls}']:null;

				if ($start_girls_numbering && $end_girls_numbering && $start_girls && $end_girls) {
					$start_girls_numbering_x = $start_girls_numbering['rownumber'];
					$start_girls_numbering_y = $start_girls_numbering['columnnumber'];
					$end_girls_numbering_x = $end_girls_numbering['rownumber'];
					$start_girls_x = $start_girls['rownumber'];
					$start_girls_y = $start_girls['columnnumber'];
					$end_girls_x = $end_girls['rownumber'];
					$end_girls_y = $end_girls['columnnumber'];
					$girls_start_id_x = $girls_start_id ? $girls_start_id['rownumber'] : null;
					$girls_start_id_y = $girls_start_id ? $girls_start_id['columnnumber'] : null;
					
					$n=1;
					$start_girls_numbering_x++;
					if ($girls_start_id_x !== null) {
						$girls_start_id_x++;
					}
					$start_girls_x++;
					for( $b=0; $start_girls_numbering_x < $end_girls_numbering_x ; $b++ )
					{
						set_time_limit(0);
						if( !empty( $girl_students[$b] ) ){
							$girl_details  = $girl_students[$b];
							$sheetInsertData->setCellValueByColumnAndRow($start_girls_numbering_y,$start_girls_numbering_x, $n );
							if ($girls_start_id_y !== null && $girls_start_id_x !== null) {
								$sheetInsertData->setCellValueByColumnAndRow($girls_start_id_y,$girls_start_id_x, $girl_details['admission_no'] ); 
							}
							$sheetInsertData->setCellValueByColumnAndRow($start_girls_y,$start_girls_x, $girl_details['lastname'].", ".$girl_details['firstname'] );
							$girls_written_count++;
						}
						
						$start_girls_numbering_x++;
						if ($girls_start_id_x !== null) {
							$girls_start_id_x++;
						}
						$start_girls_x++;
						$n++;
					}
				}
				$this->log_debug("Worksheet '{$sheetTitle}' populated: {$boys_written_count} boys, {$girls_written_count} girls.");
				$total_boys_written += $boys_written_count;
				$total_girls_written += $girls_written_count;
			} else {
				$this->log_debug("Skipping worksheet Index {$sheetIndex}: '{$sheetTitle}' (No student grade placeholders found)");
			}
			
			// CRITICAL FIX: Re-hide rows that were originally hidden in the template
			// This must be done AFTER all data insertion to ensure PHPExcel doesn't lose the hidden state
			$originallyHiddenRows = isset($allSheetsHiddenRows[$sheetIndex]) ? $allSheetsHiddenRows[$sheetIndex] : array();
			if (!empty($originallyHiddenRows)) {
				$this->log_debug("  Sheet '{$sheetTitle}': Re-hiding " . count($originallyHiddenRows) . " originally hidden rows");
				foreach ($originallyHiddenRows as $hideRow) {
					$sheetInsertData->getRowDimension($hideRow)->setVisible(false);
					$this->log_debug("    Row {$hideRow}: setVisible(false) called");
				}
			} else {
				$this->log_debug("  Sheet '{$sheetTitle}': No hidden rows to restore");
			}

			if (function_exists('gc_collect_cycles')) {
				gc_collect_cycles();
			}
			
			// Log memory after each sheet
			$this->log_debug("  Memory after sheet {$sheetIndex}: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB");
		}
		
		$this->log_debug("Memory after Phase 2: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB");
		
		// FINAL PASS: Re-apply hidden rows to ALL sheets before saving
		// PHPExcel sometimes loses row visibility when switching between sheets
		$this->log_debug("=== PHASE 3: FINAL PASS - ENSURING ALL HIDDEN ROWS ARE PRESERVED ===");
		for ($sheetIndex = 0; $sheetIndex < $totalSheets; $sheetIndex++) {
			$objPHPExcel->setActiveSheetIndex($sheetIndex);
			$sheet = $objPHPExcel->getActiveSheet();
			$sheetTitle = $sheet->getTitle();
			
			$hiddenRows = isset($allSheetsHiddenRows[$sheetIndex]) ? $allSheetsHiddenRows[$sheetIndex] : array();
			if (!empty($hiddenRows)) {
				$this->log_debug("  Sheet '{$sheetTitle}': Re-applying " . count($hiddenRows) . " hidden rows");
				foreach ($hiddenRows as $row) {
					// Check if row is currently visible (PHPExcel may have reset it)
					$rowDimension = $sheet->getRowDimension($row);
					$isCurrentlyHidden = !$rowDimension->getVisible();
					$currentHeight = $rowDimension->getRowHeight();
					
					if (!$isCurrentlyHidden) {
						$this->log_debug("    WARNING: Row {$row} was hidden but is now VISIBLE! Re-hiding...");
					}
					
					// CRITICAL FIX: PHPExcel bug - ALWAYS set row height to force dimension creation
					// Without explicit height, PHPExcel won't save hidden attribute to XML
					// Must use a non-zero height, otherwise Excel ignores it
					if ($currentHeight == -1 || $currentHeight === null || $currentHeight == 0) {
						// Row has default/auto height - set to Excel default (15)
						$rowDimension->setRowHeight(15);
						$this->log_debug("    Row {$row}: Set height to 15 (was: " . ($currentHeight == -1 ? 'auto' : $currentHeight) . ")");
					} else {
						// Row already has explicit height - keep it but re-set to force dimension creation
						$rowDimension->setRowHeight($currentHeight);
						$this->log_debug("    Row {$row}: Re-set height to {$currentHeight} to force dimension creation");
					}
					
					// Now set hidden - PHPExcel will save it to XML because row has explicit height
					$rowDimension->setVisible(false);
					$this->log_debug("    Row {$row}: setVisible(false) applied in final pass");
				}
			} else {
				$this->log_debug("  Sheet '{$sheetTitle}': No hidden rows to re-apply");
			}
		}
		
		$this->log_debug("Memory after Phase 3: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB");
				
		$date = date('Ymdhis');
		$filename = $subject_name.'-'.$class_name.' '.$section_name.'-'.$date;
		$filename= $filename.'.xlsx';
		
		$this->log_debug("=== PHASE 4: SAVING FILE ===");
		$this->log_debug("Filename: {$filename}");
		$this->log_debug("Peak memory usage: " . round(memory_get_peak_usage(true) / 1024 / 1024, 2) . " MB");
		
		// VERIFICATION: Check if hidden rows are still hidden right before save
		$this->log_debug("VERIFICATION: Checking hidden row state immediately before save:");
		for ($sheetIndex = 0; $sheetIndex < $totalSheets; $sheetIndex++) {
			$objPHPExcel->setActiveSheetIndex($sheetIndex);
			$sheet = $objPHPExcel->getActiveSheet();
			$sheetTitle = $sheet->getTitle();
			$hiddenRows = isset($allSheetsHiddenRows[$sheetIndex]) ? $allSheetsHiddenRows[$sheetIndex] : array();
			
			if (!empty($hiddenRows)) {
				$stillHidden = array();
				$becameVisible = array();
				foreach ($hiddenRows as $row) {
					if (!$sheet->getRowDimension($row)->getVisible()) {
						$stillHidden[] = $row;
					} else {
						$becameVisible[] = $row;
					}
				}
				
				if (!empty($stillHidden)) {
					$this->log_debug("  Sheet '{$sheetTitle}': " . count($stillHidden) . " rows STILL HIDDEN: " . implode(', ', $stillHidden));
				}
				if (!empty($becameVisible)) {
					$this->log_debug("  Sheet '{$sheetTitle}': ERROR! " . count($becameVisible) . " rows BECAME VISIBLE: " . implode(', ', $becameVisible));
				}
			}
		}
		
		// Update job status if provided
		if (isset($parameters['status_file'])) {
			file_put_contents($parameters['status_file'], json_encode([
				'status' => 'processing',
				'progress' => 80,
				'message' => 'Saving Excel file...'
			]));
		}
		
		// Reset timeout for the save operation
		set_time_limit(600);
		
		// Save to public downloads folder
		$downloads_dir = 'downloads/generated_grades';
		if (!is_dir($downloads_dir)) {
			mkdir($downloads_dir, 0755, true);
		}
		
		$public_file = $downloads_dir . '/' . $filename;
		$this->log_debug("Target file path: {$public_file}");
		
		// Get current object state before creating writer
		$this->log_debug("PHPExcel object state before save:");
		$this->log_debug("  - Sheet count: " . $objPHPExcel->getSheetCount());
		$this->log_debug("  - Memory usage: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB");
		
		$this->log_debug("Creating Excel2007 writer...");
		$startTime = microtime(true);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$writerCreateTime = microtime(true) - $startTime;
		$this->log_debug("Writer object created in " . round($writerCreateTime, 2) . " seconds");
		
		// Set pre-calculation to FALSE for speed (6min vs 20+min)
		// This creates larger files (3.6MB vs 1MB) but generation is 4x faster
		// Trade-off: File size vs generation speed - we choose speed
		$objWriter->setPreCalculateFormulas(false);
		
		// Check writer settings
		$preCalcSetting = $objWriter->getPreCalculateFormulas() ? "YES (formulas will be calculated and cached)" : "NO (Excel will calculate on open)";
		$this->log_debug("Writer setting - Pre-calculate formulas: {$preCalcSetting}");
		
		// Don't disable pre-calculation - it causes file bloat
		// Modern Excel can handle pre-calculated formulas just fine
		// $objWriter->setPreCalculateFormulas(false); // REMOVED - causes 4x file size increase
		
		$this->log_debug("Starting file save operation...");
		$saveStartTime = microtime(true);
		$objWriter->save($public_file);
		$saveDuration = microtime(true) - $saveStartTime;
		$this->log_debug("File saved successfully in " . round($saveDuration, 2) . " seconds");
		
		// POST-PROCESSING: Fix hidden rows in XML (PHPExcel bug workaround)
		// PHPExcel doesn't reliably save hidden="1" to XML even after setVisible(false) + setRowHeight()
		// So we manually inject hidden="1" attributes into the XML
		$this->log_debug("POST-PROCESSING: Fixing hidden rows in XML...");
		$this->fix_hidden_rows_in_xml($public_file, $allSheetsHiddenRows);
		
		// Clean up PHPExcel objects
		$this->log_debug("Cleaning up PHPExcel objects...");
		$objPHPExcel->disconnectWorksheets();
		unset($objPHPExcel);
		unset($objWriter);
		$this->log_debug("PHPExcel objects cleaned up.");
		$this->log_debug("Memory after cleanup: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB");
		
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
		$file_size_mb = round($file_size / 1024 / 1024, 2);
		$original_size = filesize($inputFileName);
		$original_size_mb = round($original_size / 1024 / 1024, 2);
		$size_increase = $file_size - $original_size;
		$size_increase_mb = round($size_increase / 1024 / 1024, 2);
		$size_increase_pct = round(($size_increase / $original_size) * 100, 1);
		
		$this->log_debug("=== FILE SIZE ANALYSIS ===");
		$this->log_debug("Original template size: {$original_size_mb} MB ({$original_size} bytes)");
		$this->log_debug("Generated file size: {$file_size_mb} MB ({$file_size} bytes)");
		$this->log_debug("Size increase: {$size_increase_mb} MB ({$size_increase} bytes)");
		$this->log_debug("Percentage increase: {$size_increase_pct}%");
		
		if ($size_increase_pct > 200) {
			$this->log_debug("WARNING: File size increased by more than 200%! This indicates bloat.");
			$this->log_debug("Possible causes:");
			$this->log_debug("  - setPreCalculateFormulas(false) was used (adds ~300% bloat)");
			$this->log_debug("  - Too many cell styles/formats being created");
			$this->log_debug("  - PHPExcel adding unnecessary XML data");
		} else if ($size_increase_pct > 50) {
			$this->log_debug("NOTE: File size increased by {$size_increase_pct}%, which is acceptable for data insertion.");
		} else {
			$this->log_debug("GOOD: File size increase is minimal ({$size_increase_pct}%).");
		}
		
		// Build download URL
		$download_url = base_url() . $public_file;
		
		// Calculate total execution time
		$totalTime = microtime(true) - $generationStartTime;
		
		// Log final summary
		$this->log_debug("=== GENERATION COMPLETE - SUMMARY ===");
		$this->log_debug("Template: " . basename($inputFileName));
		$this->log_debug("Generated file: {$filename}");
		$this->log_debug("Download URL: {$download_url}");
		$this->log_debug("Total execution time: " . round($totalTime, 2) . " seconds");
		$this->log_debug("Final memory usage: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB");
		$this->log_debug("Peak memory usage: " . round(memory_get_peak_usage(true) / 1024 / 1024, 2) . " MB");
		$this->log_debug("Students: {$total_boys_written} boys + {$total_girls_written} girls = " . ($total_boys_written + $total_girls_written) . " total");
		
		// Count hidden rows preserved
		$totalHiddenRows = 0;
		foreach ($allSheetsHiddenRows as $rows) {
			$totalHiddenRows += count($rows);
		}
		$this->log_debug("Hidden rows preserved: {$totalHiddenRows} total across " . count($allSheetsHiddenRows) . " sheets");
		$this->log_debug("--- EXCEL GENERATION SUCCESS ---");
		
		// Update job status to complete
		if (isset($parameters['status_file'])) {
			file_put_contents($parameters['status_file'], json_encode([
				'status' => 'complete',
				'progress' => 100,
				'filename' => $filename,
				'download_url' => $download_url,
				'file_size' => round($file_size / 1024 / 1024, 2) . ' MB',
				'completed_at' => date('Y-m-d H:i:s')
			]));
			// Exit silently - browser already disconnected
			exit;
		}
		
		// Fallback for non-AJAX requests: return JSON
		while (ob_get_level()) {
			ob_end_clean();
		}
		
		header('Content-Type: application/json');
		echo json_encode([
			'status' => 'success',
			'filename' => $filename,
			'download_url' => $download_url,
			'file_path' => $public_file,
			'file_size' => round($file_size / 1024 / 1024, 2) . ' MB'
		]);
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



