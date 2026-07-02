<?php



if (!defined('BASEPATH')) exit('No direct script access allowed'); 







require_once APPPATH."/third_party/PHPExcel-1.8/Classes/PHPExcel.php";







class Excel extends PHPExcel {







    public function __construct() { 



    	 $CI =& get_instance();



		$CI->load->database();



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







		$class_id = $parameters['class_id'];



		$section_id = $parameters['section_id'];



		$subject_id = $parameters['subject_id'];



		$template_id = $parameters['template_id'];



		$subject_name = null;



		$class_name = null;



		$teachers_name = null;



		$section_name = null;



		$get_school_name = $CI->Setting_model->getCurrentSchoolName();



		$boys_students = $CI->Student_model->getstudentsByClassSectionGender( $class_id, $section_id, 'Male');



		$girl_students = $CI->Student_model->getstudentsByClassSectionGender( $class_id, $section_id, 'Female'); 	







		$template_details = $CI->Template_model->get( $template_id ); 	



		$template_file = isset($template_details['file'])?$template_details['file']:null;



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



		if( $template_file != null  ){



			$inputFileName = $template_file;



		} else {



			$inputFileName = 'uploads/GradingSheetTemplate.xlsx';



		}







		try {



			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);



			$objReader = PHPExcel_IOFactory::createReader($inputFileType);



			$objPHPExcel = $objReader->load($inputFileName);



		} 



		



		catch(Exception $e) {



			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());



		}



		



		$objPHPExcel->setActiveSheetIndex(0);



		$sheetInsertData = $objPHPExcel->getActiveSheet();



		//$sheetInsertData = $sheetInsertData->getTitle();



		



		$foundInCells = array();



		$searchValueArray = array(



						'{start_boys_numbering}',



						'{boys_start_id}',



						'{start_boys}',



						'{end_boys}',



						'{end_boys_numbering}',



						'{start_girls_numbering}',



						'{girls_start_id}',



						'{start_girls}',



						'{end_girls}',



						'{end_girls_numbering}', 



						'{class}' ,



						'{subject_name}',



						'{subject_teacher}',



						'{school_name}' 



						);



			for( $x=0; $x<count($searchValueArray);$x++){



				set_time_limit(0);



				$searchValue = $searchValueArray[$x];



				foreach ($sheetInsertData->getRowIterator() as $row) {



					$cellIterator = $row->getCellIterator();



					$cellIterator->setIterateOnlyExistingCells(false);



					foreach ($cellIterator as $cell) {



						if ($cell->getValue() === $searchValue) {



							$coordinate = $cell->getCoordinate();



							$foundInCells[$searchValue] = $coordinate;



							break;



						}



					}



				}



			}



			//setCellValueByColumnAndRow



			// GENERAL



			$school_name = isset($foundInCells['{school_name}'])?$foundInCells['{school_name}']:null;



			$class_cell = isset($foundInCells['{class}'])?$foundInCells['{class}']:null;



			$subject_name_cell = isset($foundInCells['{subject_name}'])?$foundInCells['{subject_name}']:null; 



			$subject_teacher_cell = isset($foundInCells['{subject_teacher}'])?$foundInCells['{subject_teacher}']:null;



			



			$school_name_letters = preg_replace('/[^a-zA-Z]/', '', $school_name);



			$school_name_numbers = preg_replace('/[^0-9]/', '', $school_name);



			$class_letters = preg_replace('/[^a-zA-Z]/', '', $class_cell);



			$class_numbers = preg_replace('/[^0-9]/', '', $class_cell);



			$subject_name_letters = preg_replace('/[^a-zA-Z]/', '', $subject_name_cell);



			$subject_name_numbers = preg_replace('/[^0-9]/', '', $subject_name_cell);



			$subject_teacher_letters = preg_replace('/[^a-zA-Z]/', '', $subject_teacher_cell);



			$subject_teacher_numbers = preg_replace('/[^0-9]/', '', $subject_teacher_cell);  



		



			if( $school_name != null ){



				$sheetInsertData->setCellValue($school_name_letters.$school_name_numbers, $get_school_name );



			}



			if( $class_cell != null ){



				$sheetInsertData->setCellValue($class_letters.$class_numbers, $class_name.' '.$section_name );



			}



			if( $subject_name_cell != null ){



				$sheetInsertData->setCellValue($subject_name_letters.$subject_name_numbers, $subject_name );	



			}



			



			



		



			// BOYS



			$start_boys_numbering = isset($foundInCells['{start_boys_numbering}'])?$foundInCells['{start_boys_numbering}']:null;



			$end_boys_numbering = isset($foundInCells['{end_boys_numbering}'])?$foundInCells['{end_boys_numbering}']:null;



			$start_boys = isset($foundInCells['{start_boys}'])?$foundInCells['{start_boys}']:null;



			$end_boys = isset($foundInCells['{end_boys}'])?$foundInCells['{end_boys}']:null;



			$boys_start_id = isset($foundInCells['{boys_start_id}'])?$foundInCells['{boys_start_id}']:null;



			



			$start_boys_numbering_letters = preg_replace('/[^a-zA-Z]/', '', $start_boys_numbering);



			$end_boys_numbers = preg_replace('/[^0-9]/', '', $end_boys);



			$start_boys_letters = preg_replace('/[^a-zA-Z]/', '', $start_boys);



			$start_boys_numbers = preg_replace('/[^0-9]/', '', $start_boys);



			$boys_start_id_letters = preg_replace('/[^a-zA-Z]/', '', $boys_start_id);



			$start_boys_letters_merge = $start_boys_letters;



			$start_boys_letters_merge++;



			$n=1;



			$start_boys_numbers++;



			for( $b=0; $start_boys_numbers <= $end_boys_numbers ; $b++ )



			{



				set_time_limit(0);



				if( !empty( $boys_students[$b] ) ){



					$boy_details  = $boys_students[$b];



					$sheetInsertData->setCellValue($start_boys_numbering_letters.$start_boys_numbers, $n );



					$sheetInsertData->setCellValue($boys_start_id_letters.$start_boys_numbers, $boy_details['admission_no'] ); 



					$sheetInsertData->setCellValue($start_boys_letters.$start_boys_numbers, $boy_details['lastname'].", ".$boy_details['firstname'] );



				}



				



				$start_boys_numbers++;



				$n++;



			}  



			



			// GIRLS



			$start_girls_numbering = isset($foundInCells['{start_girls_numbering}'])?$foundInCells['{start_girls_numbering}']:null;



			$end_girls_numbering = isset($foundInCells['{end_girls_numbering}'])?$foundInCells['{end_girls_numbering}']:null;



			$start_girls = isset($foundInCells['{start_girls}'])?$foundInCells['{start_girls}']:null;



			$end_girls = isset($foundInCells['{end_girls}'])?$foundInCells['{end_girls}']:null;



			$girls_start_id = isset($foundInCells['{girls_start_id}'])?$foundInCells['{girls_start_id}']:null;







			



			$start_girls_numbering_letters = preg_replace('/[^a-zA-Z]/', '', $start_girls_numbering);



			$end_girls_numbers = preg_replace('/[^0-9]/', '', $end_girls);



			$start_girls_letters = preg_replace('/[^a-zA-Z]/', '', $start_girls);



			$start_girls_numbers = preg_replace('/[^0-9]/', '', $start_girls);



			$girls_start_id_letters = preg_replace('/[^a-zA-Z]/', '', $girls_start_id); 



			$n=1;



			$start_girls_numbers++;



			for( $b=0; $start_girls_numbers <= $end_girls_numbers ; $b++ )



			{



				set_time_limit(0);



				if( !empty( $girl_students[$b] ) ){



					$girl_details  = $girl_students[$b];



					$sheetInsertData->setCellValue($start_girls_numbering_letters.$start_girls_numbers, $n );



					$sheetInsertData->setCellValue($girls_start_id_letters.$start_girls_numbers, $girl_details['lrn'] ); 



					$sheetInsertData->setCellValue($start_girls_letters.$start_girls_numbers, $girl_details['lastname'].", ".$girl_details['firstname'] );



				}



				



				$start_girls_numbers++;



				$n++;



			}  







		/* print_r( $foundInCells );



		die(); */



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



	




	public function generate_attendance( $class_id, $section_id, $month_selected, $session_id = null ){ 


     	ini_set('memory_limit', '-1');
		$CI =& get_instance();
		$CI->load->model('Student_model');
		$CI->load->model('Class_model');
		$CI->load->model('Section_model');
		$CI->load->model('Subject_model');
		$CI->load->model('Setting_model');
		$CI->load->model('Stuattendence_model');
		$CI->load->model('Session_model');

		if( empty( $session_id )){
			$session_id =  $CI->Setting_model->getCurrentSession();
		}

		$class_name = null;
		$teachers_name = null;
		$section_name = null;
		$get_school_name = $CI->Setting_model->getCurrentSchoolName();
		$getCurrentSessionName = $CI->Setting_model->getCurrentSessionName();
		$getCurrentSchoolId = $CI->Setting_model->getCurrentSchoolId();
		$boys_students = $CI->Student_model->getstudentsByClassSectionGender( $class_id, $section_id, 'Male', $session_id );
		$girl_students = $CI->Student_model->getstudentsByClassSectionGender( $class_id, $section_id, 'Female', $session_id );
		if( $class_id != null ){
			$class_details = $CI->Class_model->get( $class_id ); 	
			$class_name = $class_details['class'];
		}

		if( $section_id != null ){
			$section_details = $CI->Section_model->get( $section_id ); 	
			$section_name = $section_details['section'];
		}

		$opendoc = "././uploads/SF2AttendanceTemplate.xlsx";
		$objReader   = new PHPExcel_Reader_Excel2007();
		$objReader->setIncludeCharts(TRUE);
		$objPHPExcel = $objReader->load($opendoc); 

    
	 	$be = 0;
	 	$bf = 0;
	 	$bg = 0;
	 	$bh = 0;
	 	$bi = 0;
	 	$bj = 0;
	 	$bk = 0;
	 	$bl = 0;
	 	$bm = 0;
	 	$bn = 0;
	 	$bo = 0;
	 	$bp = 0;
	 	$bq = 0;
	 	$br = 0;
	 	$bs = 0;
	 	$bt = 0;
	 	$bu = 0;
	 	$bv = 0;
	 	$bw = 0;
	 	$bx = 0;
	 	$by = 0;
	 	$bz = 0;
	 	$baa = 0;
	 	$bab = 0;
	 	$bac = 0;
	 	$ge = 0;
		$gf = 0;
		$gg = 0;
		$gh = 0;
		$gi = 0;
		$gj = 0;
		$gk = 0;
		$gl = 0;
		$gm = 0;
		$gn = 0;
		$go = 0;
		$gp = 0;
		$gq = 0;
		$gr = 0;
		$gs = 0;
		$gt = 0;
		$gu = 0;
		$gv = 0;
		$gw = 0;
		$gx = 0;
		$gy = 0;
		$gz = 0;
		$gaa = 0;
		$gab = 0;
		$gac = 0;
 		$n=1;

		$start_boys_numbers = 15;
		$end_boys_numbers =  37;	// not dynamic (if students exceed this number, they won't be displayed)
		$start_boys_numbering_letters = "A";
		$boys_start_id_letters = "B";
		$start_boys_letters = "C";
		$start_boys_numbers2 = $start_boys_numbers - 4;

		for( $b=0; $start_boys_numbers <= $end_boys_numbers ; $b++ )
		{
			set_time_limit(0);
			if( !empty( $boys_students[$b] ) ){
				$boy_details  = $boys_students[$b];
				$objPHPExcel->getActiveSheet()->setCellValue($start_boys_numbering_letters.$start_boys_numbers, $n );
				$objPHPExcel->getActiveSheet()->setCellValue($boys_start_id_letters.$start_boys_numbers, $boy_details['admission_no'] ); 
				$objPHPExcel->getActiveSheet()->setCellValue($start_boys_letters.$start_boys_numbers, $boy_details['lastname'].", ".$boy_details['firstname'] );
				$session_details = $CI->Session_model->get( $session_id );
				$session_current = $session_details['session'];
				$startMonth = $CI->Setting_model->getStartMonth();
				$centenary = substr($session_current, 0, 2); //2017-18 to 2017
				$year_first_substring = substr($session_current, 2, 2); //2017-18 to 2017
				$year_second_substring = substr($session_current, 5, 2); //2017-18 to 18
				$month_number = date("m", strtotime($month_selected));

				if ($month_number >= $startMonth && $month_number <= 12) {
					$year = $centenary . $year_first_substring;
				} else {
					$year = $centenary . $year_second_substring;
				}

				//$year = date("Y");
		        $month = date("m", strtotime($month_selected));  
		        $type = CAL_GREGORIAN; 
		        $day_count = cal_days_in_month($type, $month, $year); // Get the amount of days
		        //loop through all days

		        $ba = 0;
				$list_late = 0;
				$list_absent = 0;
				$str_letter = 'E';
		        for ($i = 1; $i <= $day_count; $i++){ 
		        	$value = str_pad($i,2,"0",STR_PAD_LEFT);
		            $date = $year.'/'.$month.'/'.$value; //format date
		            $get_name = date('l', strtotime($date)); //get week day
		            $day_name = substr($get_name, 0, 3); // Trim day name to 3 chars
	             	$time = mktime(0, 0, 0, $month, $value, $year); 
	                $weeknumber = $this->weekofmonth($time); 
	               	$student_session_id = $boy_details['student_session_id']; 
	               	$student_specific_detail = $CI->student_model->getStudentbySession( $student_session_id );  
	               	if( 	$student_specific_detail == FALSE || empty(	$student_specific_detail)){

	               	}

	               	// $ret = $CI->stuattendence_model->get_attendance_by_date_user($student_specific_detail->id,  $date  ); 
	               	$ret = $CI->stuattendence_model->get_attendance_by_date_user($student_session_id,  $date  ); 
	               	/*if( $date == '2017/11/06'){







	               	 	echo '<pre>';echo ' student_session_id : '.$student_session_id.'                   student_specific_detail : '; echo  $student_specific_detail->id;



	               	}*/







               	  	// $ret = $CI->stuattendence_model->get_attendance_by_date_user( 42,  '2017-10-23' );



	               	 







               	 			/*$objPHPExcel->getActiveSheet()->getStyle('F12')->applyFromArray(



               	 	                    array(



               	 	                        'fill' => array(



               	 	                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



               	 	                            'rotation'   => 235,



               	 	                            'startcolor' => array(



               	 	                                'argb' => '272822'



               	 	                            ),



               	 	                            'endcolor'   => array(



               	 	                                'argb' => 'FFFFFFFF'



               	 	                            )



               	 	                        )



               	 	                    )



               	 	            );*/



	              	







		            if( $day_name == 'Mon' && $weeknumber == 1){







		        	 	$objPHPExcel->getActiveSheet()->setCellValue('E'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('E'.$start_boys_numbers,  'X' );



	            			// $list_absent++; 



	            			



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'E'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



			               			$list_late++; 	



			               			$be++; 	



			               		} elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('E'.$start_boys_numbers,  'X' );



			               		}  elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('E'.$start_boys_numbers,  '' );



			               			$be++;  



			               		} 



			               	} 



		               	}  



		            } elseif( $day_name == 'Tue' && $weeknumber == 1 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('F'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			//$objPHPExcel->getActiveSheet()->setCellValue('F'.$start_boys_numbers,  'X' ); 



		            		



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'F'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



					          	  	); 



			               			$list_late++; 		



			               			$bf++; 		



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('F'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('F'.$start_boys_numbers,  '' );



			               			$bf++;  



			               		} 



			               	}  



		            	}  



		            } elseif( $day_name == 'Wed' && $weeknumber == 1 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('G'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            		// 	$objPHPExcel->getActiveSheet()->setCellValue('G'.$start_boys_numbers,  'X' );



		            	// 	$list_absent++; 



		            		



		            	} else {



	            		 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'G'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 		



				               		$bg++; 		



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('G'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		}elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('G'.$start_boys_numbers,  '' );



			               			$bg++;  



			               		} 



			               	}  



		            	}  







		            } elseif( $day_name == 'Thu' && $weeknumber == 1 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('H'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('H'.$start_boys_numbers,  'X' );



		            		// $list_absent++; 



		            		 



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'H'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



						            $list_late++; 	



				               		$bh++; 		



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('H'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('H'.$start_boys_numbers,  '' );



			               			$bh++; 



			               		} 



			               	}   



		            	}







		            } elseif( $day_name == 'Fri' && $weeknumber == 1 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('I'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('I'.$start_boys_numbers,  'X' );



		            		// $list_absent++; 



		            		 



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'I'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



			               			$list_late++; 	



			               			$bi++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('I'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('I'.$start_boys_numbers,  '' );



			               			$bi++;



			               		} 



			               	}   



		            	}  







		            } elseif( $day_name == 'Mon' && $weeknumber == 2){



		            	$objPHPExcel->getActiveSheet()->setCellValue('J'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('J'.$start_boys_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'J'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



			               			$list_late++; 		



			               			$bj++; 		



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('J'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		}elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('J'.$start_boys_numbers,  '' );



			               			$bj++;



			               		} 



			               	}   



		            	}  



		            } elseif( $day_name == 'Tue' && $weeknumber == 2 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('K'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('K'.$start_boys_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'K'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 		



				               		$bk++; 		



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('K'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		}  elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('K'.$start_boys_numbers,  '' );



			               			$bk++;



			               		} 



			               	}   



		            	}  







		            } elseif( $day_name == 'Wed' && $weeknumber == 2 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('L'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('L'.$start_boys_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'L'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 		



				               		$bl++; 		



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('L'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('L'.$start_boys_numbers,  '' );



			               			$bl++; 



			               		} 



			               	}   



		            	}







		            } elseif( $day_name == 'Thu' && $weeknumber == 2 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('M'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('M'.$start_boys_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'M'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 		



				               		$bm++; 		



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('M'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('M'.$start_boys_numbers,  '' );



			               			$bm++; 



			               		} 



			               	}  



		            		



		            	}  







		            } elseif( $day_name == 'Fri' && $weeknumber == 2 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('N'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('N'.$start_boys_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'N'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 		



				               		$bn++; 		



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('N'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('N'.$start_boys_numbers,  '' );



			               			$bn++; 



			               		} 



			               	}   



		            	}  



		            } elseif( $day_name == 'Mon' && $weeknumber == 3){



		            	$objPHPExcel->getActiveSheet()->setCellValue('O'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('O'.$start_boys_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'O'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 		



				               		$bo++; 		



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('O'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('O'.$start_boys_numbers,  '' );



			               			$bo++; 



			               		} 



			               	}   



		            	}    



		            } elseif( $day_name == 'Tue' && $weeknumber == 3 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('P'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('P'.$start_boys_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'P'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 		



				               		$bp++; 		



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('P'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		}elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('P'.$start_boys_numbers,  '' );



			               			$bp++; 



			               		} 



			               	}   



		            	}  







		            } elseif( $day_name == 'Wed' && $weeknumber == 3 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('Q'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('Q'.$start_boys_numbers,  'X' );



		            		// $list_absent++;  



		            		



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'Q'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



			               			$list_late++; 	



			               			$bq++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$start_boys_numbers,  '' );



			               			$bq++; 



			               		} 



			               	}  



		            	}    







		            } elseif( $day_name == 'Thu' && $weeknumber == 3 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('R'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('R'.$start_boys_numbers,  'X' );



		            		// $list_absent++;  



		            		



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'R'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



			               			$list_late++; 	



			               			$br++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('R'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('R'.$start_boys_numbers,  '' );



			               			$br++; 



			               		} 



			               	}  



		            	}  



		            } elseif( $day_name == 'Fri' && $weeknumber == 3 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('S'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('S'.$start_boys_numbers,  'X' );



		            		// $list_absent++;  



		            		



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'S'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



			               			$list_late++; 	



			               			$bs++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('S'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('S'.$start_boys_numbers,  '' );



			               			$bs++; 



			               		} 



			               	}  



		            	}    







		            }  elseif( $day_name == 'Mon' && $weeknumber == 4){



		            	$objPHPExcel->getActiveSheet()->setCellValue('T'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('T'.$start_boys_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'T'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



			               			$list_late++; 	



			               			$bt++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('T'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('T'.$start_boys_numbers,  '' );



			               			$bt++;



			               		} 



			               	}  



		            		 



		            	}    



		            } elseif( $day_name == 'Tue' && $weeknumber == 4 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('U'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('U'.$start_boys_numbers,  'X' );



		            		// $list_absent++; 



		            		 



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'U'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



			               			$list_late++; 	



			               			$bu++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('U'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('U'.$start_boys_numbers,  '' );



			               			$bu++;



			               		} 



			               	}   



		            	}     



		            } elseif( $day_name == 'Wed' && $weeknumber == 4 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('V'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('V'.$start_boys_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'V'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



			               			$list_late++; 	



			               			$bv++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('V'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('V'.$start_boys_numbers,  '' );



			               			$bv++; 



			               		} 



			               	}   



		            	}    



		            } elseif( $day_name == 'Thu' && $weeknumber == 4 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('W'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('W'.$start_boys_numbers,  'X' );



		            		// $list_absent++; 



	            		} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'W'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



			               			$list_late++; 	



			               			$bw++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('W'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('W'.$start_boys_numbers,  '' );



			               			$bw++;



			               		} 



			               	}   



		            	}    



		            } elseif( $day_name == 'Fri' && $weeknumber == 4 ) {  



		            	$objPHPExcel->getActiveSheet()->setCellValue('X'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('X'.$start_boys_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'X'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 		



				               		$bx++; 		



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('X'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('X'.$start_boys_numbers,  '' );



			               			$bx++;



			               		} 



			               	}    



		            	}       



		            }  elseif( $day_name == 'Mon' && $weeknumber == 5){



		            	$objPHPExcel->getActiveSheet()->setCellValue('Y'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('Y'.$start_boys_numbers,  'X' );



		            		// $list_absent++;  



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'Y'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



			               			$list_late++; 	



			               			$by++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('Y'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('Y'.$start_boys_numbers,  '' );



			               			$by++; 



			               		} 



			               	}  



		            	}     



		            } elseif( $day_name == 'Tue' && $weeknumber == 5 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('Z'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('Z'.$start_boys_numbers,  'X' );



		            		// $list_absent++;  



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'Z'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



			               			$list_late++; 	



			               			$bz++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('Z'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('Z'.$start_boys_numbers,  '' );



			               			$bz++; 



			               		} 



			               	}  	



		            	}  



		            } elseif( $day_name == 'Wed' && $weeknumber == 5 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('AA'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('AA'.$start_boys_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'AA'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



			               			$list_late++; 		



			               			$baa++; 		



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('AA'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('AA'.$start_boys_numbers,  '' );



			               			$baa++; 



			               		} 



			               	}  



		            	}      







		            } elseif( $day_name == 'Thu' && $weeknumber == 5 ) {



		            	$objPHPExcel->getActiveSheet()->setCellValue('AB'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('AB'.$start_boys_numbers,  'X' );



		            		// $list_absent++; 



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'AB'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



			               			$list_late++; 	



			               			$bab++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('AB'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('AB'.$start_boys_numbers,  '' ); 



		            				$bab++; 



			               		} 



			               	}  



		            	}      



		            } elseif( $day_name == 'Fri' && $weeknumber == 5 ) { 



		            	$objPHPExcel->getActiveSheet()->setCellValue('AC'.$start_boys_numbers2,   $value );



		            	if( $ret == FALSE ){



	            			/*$objPHPExcel->getActiveSheet()->setCellValue('AC'.$start_boys_numbers,  'X' );



		            		$list_absent++; */ 



		            	} else { 



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'AC'.$start_boys_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$bac++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('AC'.$start_boys_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('AC'.$start_boys_numbers,  '' );



			               			$bac++; 



			               		} 



			               	}  



		            	}   



		            }







		            $objPHPExcel->getActiveSheet()->setCellValue('AD'.$start_boys_numbers,  $list_absent );



		            $objPHPExcel->getActiveSheet()->setCellValue('AE'.$start_boys_numbers,  $list_late );



			           



		        }  



				



			} 



			$start_boys_numbers++;



			$n++;



		}  







		$gnn=1;



		$start_girls_numbers = 41;



		$end_girls_numbers = 65;



		$start_girls_numbering_letters = "A";



		$girls_start_id_letters = "B";



		$start_girls_letters = "C";



		for( $g=0; $start_girls_numbers <= $end_girls_numbers ; $g++ )



		{



			set_time_limit(0);



			if( !empty( $girl_students[$g] ) ){



				$girl_details  = $girl_students[$g];



				$objPHPExcel->getActiveSheet()->setCellValue($start_girls_numbering_letters.$start_girls_numbers, $gnn );



				$objPHPExcel->getActiveSheet()->setCellValue($girls_start_id_letters.$start_girls_numbers, $girl_details['admission_no'] ); 



				$objPHPExcel->getActiveSheet()->setCellValue($start_girls_letters.$start_girls_numbers, $girl_details['lastname'].", ".$girl_details['firstname'] );






				$session_details = $CI->session_model->get( $session_id );
				$session_current = $session_details['session'];
				$startMonth = $CI->setting_model->getStartMonth();
				$centenary = substr($session_current, 0, 2); //2017-18 to 2017
				$year_first_substring = substr($session_current, 2, 2); //2017-18 to 2017
				$year_second_substring = substr($session_current, 5, 2); //2017-18 to 18
				$month_number = date("m", strtotime($month_selected));

				if ($month_number >= $startMonth && $month_number <= 12) {
					$year = $centenary . $year_first_substring;
				} else {
					$year = $centenary . $year_second_substring;
				}
				//$year = date("Y");



		        $month = date("m", strtotime($month_selected));  



		        $type = CAL_GREGORIAN; 



		        $day_count = cal_days_in_month($type, $month, $year); // Get the amount of days



		 



		       	$ga = 0;



		       	$list_late = 0;



				$list_absent = 0;



				for ($i = 1; $i <= $day_count; $i++){ 



				    	 



					$value = str_pad($i,2,"0",STR_PAD_LEFT);



					 



				    $date = $year.'/'.$month.'/'.$value; //format date



				    $get_name = date('l', strtotime($date)); //get week day



				    $day_name = substr($get_name, 0, 3); // Trim day name to 3 chars







				 	$time = mktime(0, 0, 0, $month, $value, $year); 



				    $weeknumber = $this->weekofmonth($time); 



				   	$student_session_id = $girl_details['student_session_id']; 







				   	$ret = $CI->stuattendence_model->get_attendance_by_date_user($student_session_id, $date  );



				    



				   						if( $day_name == 'Mon' && $weeknumber == 1){



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('E'.$start_girls_numbers,  'X' );



	            			// $list_absent++;  



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'E'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$ge++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('E'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		}  elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('E'.$start_girls_numbers,  '' );



			               			$ge++; 



			               		} 



			               	}  



		            	} 



		            } elseif( $day_name == 'Tue' && $weeknumber == 1 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('F'.$start_girls_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'F'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gf++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('F'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		}elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('F'.$start_girls_numbers,  '' );



			               			$gf++; 



			               		} 



			               	}



		            	}  



		            } elseif( $day_name == 'Wed' && $weeknumber == 1 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('G'.$start_girls_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'G'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gg++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('G'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('G'.$start_girls_numbers,  '' );



			               			$gg++; 



			               		} 



			               	}



		            	}  







		            } elseif( $day_name == 'Thu' && $weeknumber == 1 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('H'.$start_girls_numbers,  'X' );



		            		// $list_absent++;  



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'H'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gh++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('H'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('H'.$start_girls_numbers,  '' );



			               			$gh++; 



			               		} 



			               	}



		            	}







		            } elseif( $day_name == 'Fri' && $weeknumber == 1 ) {







		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('I'.$start_girls_numbers,  'X' );



		            		// $list_absent++;  



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'I'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gi++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('I'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('I'.$start_girls_numbers,  '' );



			               			$gi++; 



			               		} 



			               	}



		            	}  







		            } elseif( $day_name == 'Mon' && $weeknumber == 2){



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('J'.$start_girls_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'J'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gj++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('J'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('J'.$start_girls_numbers,  '' );



			               			$gj++; 



			               		} 



			               	}



		            	}  



		            } elseif( $day_name == 'Tue' && $weeknumber == 2 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('K'.$start_girls_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'K'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gk++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('K'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('K'.$start_girls_numbers,  '' );



			               			$gk++; 



			               		} 



			               	}



		            	}  







		            } elseif( $day_name == 'Wed' && $weeknumber == 2 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('L'.$start_girls_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'L'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gl++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('L'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('L'.$start_girls_numbers,  '' );



			               			$gl++; 



			               		} 



			               	}



		            	}







		            } elseif( $day_name == 'Thu' && $weeknumber == 2 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('M'.$start_girls_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'M'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gm++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('M'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('M'.$start_girls_numbers,  '' );



			               			$gm++; 



			               		} 



			               	}



		            	}  







		            } elseif( $day_name == 'Fri' && $weeknumber == 2 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('N'.$start_girls_numbers,  'X' );



		            		// $list_absent++;  



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'N'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gn++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('N'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		}elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('N'.$start_girls_numbers,  '' );



			               			$gn++; 



			               		} 



			               	}



		            	}  



		            } elseif( $day_name == 'Mon' && $weeknumber == 3){



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('O'.$start_girls_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'O'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$go++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('O'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('O'.$start_girls_numbers,  '' );



			               			$go++; 



			               		} 



			               	}



		            	}    



		            } elseif( $day_name == 'Tue' && $weeknumber == 3 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('P'.$start_girls_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'P'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gp++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('P'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('P'.$start_girls_numbers,  '' );



			               			$gp++; 



			               		} 



			               	}



		            	}  







		            } elseif( $day_name == 'Wed' && $weeknumber == 3 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('Q'.$start_girls_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'Q'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gq++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$start_girls_numbers,  '' );



			               			$gq++; 



			               		} 



			               	}



		            	}    







		            } elseif( $day_name == 'Thu' && $weeknumber == 3 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('R'.$start_girls_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'R'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gr++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('R'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('R'.$start_girls_numbers,  '' );



			               			$gr++; 



			               		} 



			               	}



		            	}  



		            } elseif( $day_name == 'Fri' && $weeknumber == 3 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('S'.$start_girls_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'S'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gs++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('S'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('S'.$start_girls_numbers,  '' );



			               			$gs++; 



			               		} 



			               	}



		            	}    







		            }  elseif( $day_name == 'Mon' && $weeknumber == 4){



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('T'.$start_girls_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'T'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gt++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('T'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('T'.$start_girls_numbers,  '' );



			               			$gt++; 



			               		} 



			               	}



		            	}    



		            } elseif( $day_name == 'Tue' && $weeknumber == 4 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('U'.$start_girls_numbers,  'X' );



		            		// $list_absent++;  



		            		



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'U'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gu++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('U'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		}  elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('U'.$start_girls_numbers,  '' );



			               			$gu++; 



			               		} 



			               	}



		            	}     



		            } elseif( $day_name == 'Wed' && $weeknumber == 4 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('V'.$start_girls_numbers,  'X' );



		            		// $list_absent++; 



		            	



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'V'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gv++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('V'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('V'.$start_girls_numbers,  '' );



		               				$gv++; 



			               		} 



			               	}



		            	}    



		            } elseif( $day_name == 'Thu' && $weeknumber == 4 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('W'.$start_girls_numbers,  'X' );



		            		// $list_absent++;  



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'W'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gw++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('W'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('W'.$start_girls_numbers,  '' );



			               			$gw++; 



			               		} 



			               	}



		            	}    



		            } elseif( $day_name == 'Fri' && $weeknumber == 4 ) {  



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('X'.$start_girls_numbers,  'X' );



		            		// $list_absent++;  



		            		 



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'X'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gx++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('X'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('X'.$start_girls_numbers,  '' );



			               			$gx++; 



			               		} 



			               	}



		            	}       



		            }  elseif( $day_name == 'Mon' && $weeknumber == 5){



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('Y'.$start_girls_numbers,  'X' );



		            		// $list_absent++;  



		            		 



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'Y'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gy++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('Y'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('Y'.$start_girls_numbers,  '' );



			               			$gy++;



			               		} 



			               	}



		            	}     



		            } elseif( $day_name == 'Tue' && $weeknumber == 5 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('Z'.$start_girls_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'Z'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gz++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('Z'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('Z'.$start_girls_numbers,  '' );



			               			$gz++; 



			               		} 



			               	}



		            	}  



		            } elseif( $day_name == 'Wed' && $weeknumber == 5 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('AA'.$start_girls_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'AA'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gaa++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('AA'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('AA'.$start_girls_numbers,  '' );



			               			$gaa++; 



			               		} 



			               	}



		            	}      







		            } elseif( $day_name == 'Thu' && $weeknumber == 5 ) {



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('AB'.$start_girls_numbers,  'X' );



		            		// $list_absent++; 



		            		



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'AB'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gab++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('AB'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		} elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('AB'.$start_girls_numbers,  '' );



			               			$gab++; 



			               		} 



			               	}



		            	}      



		            } elseif( $day_name == 'Fri' && $weeknumber == 5 ) { 



		            	if( $ret == FALSE ){



	            			// $objPHPExcel->getActiveSheet()->setCellValue('AC'.$start_girls_numbers,  'X' );



		            		// $list_absent++;  



		            	} else {



		            		if( $ret == TRUE ){



			               		if( $ret['type'] == 'Late'){



			               			$objPHPExcel->getActiveSheet()->getStyle( 'AC'.$start_girls_numbers )->applyFromArray(



					                    array(



					                        'fill' => array(



					                            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,



					                            'rotation'   => 90,



					                            'startcolor' => array(



					                                'argb' => '272822'



					                            ),



					                            'endcolor'   => array(



					                                'argb' => 'FFFFFFFF'



					                            )



					                        )



					                    )



						            ); 



				               		$list_late++; 	



				               		$gac++; 	



			               		}elseif( $ret['type'] == 'Absent'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('AC'.$start_girls_numbers,  'X' );



			               			$list_absent++; 



			               		}elseif( $ret['type'] == 'Present'){



			               			$objPHPExcel->getActiveSheet()->setCellValue('AC'.$start_girls_numbers,  '' );



			               			$gac++; 



			               		} 



			               	}



		            	}   



		            }







		            $objPHPExcel->getActiveSheet()->setCellValue('AD'.$start_girls_numbers,  $list_absent );



		            $objPHPExcel->getActiveSheet()->setCellValue('AE'.$start_girls_numbers,  $list_late );



				     



				       



				}   



		          



			}



			



			$start_girls_numbers++;



			$gnn++;



		}  



 



 



		/*$full_shade = imagecreatefromjpeg("././backend/images/full.jpg");



 	    $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();



        $objDrawing->setName("name");



        $objDrawing->setDescription("Description"); 



        $objDrawing->setImageResource($full_shade);



		$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);



		$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);



		$objDrawing->setHeight(100);



		$objDrawing->setCoordinates('E15');



		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());*/



  



		$objPHPExcel->getActiveSheet()->setCellValue('E39', $be);



		$objPHPExcel->getActiveSheet()->setCellValue('F39', $bf);



		$objPHPExcel->getActiveSheet()->setCellValue('G39', $bg);



		$objPHPExcel->getActiveSheet()->setCellValue('H39', $bh);



		$objPHPExcel->getActiveSheet()->setCellValue('I39', $bi);



		$objPHPExcel->getActiveSheet()->setCellValue('J39', $bj);



		$objPHPExcel->getActiveSheet()->setCellValue('K39', $bk);



		$objPHPExcel->getActiveSheet()->setCellValue('L39', $bl);



		$objPHPExcel->getActiveSheet()->setCellValue('M39', $bm);



		$objPHPExcel->getActiveSheet()->setCellValue('N39', $bn);



		$objPHPExcel->getActiveSheet()->setCellValue('O39', $bo);



		$objPHPExcel->getActiveSheet()->setCellValue('P39', $bp);



		$objPHPExcel->getActiveSheet()->setCellValue('Q39', $bq);



		$objPHPExcel->getActiveSheet()->setCellValue('R39', $br);



		$objPHPExcel->getActiveSheet()->setCellValue('S39', $bs);



		$objPHPExcel->getActiveSheet()->setCellValue('T39', $bt);



		$objPHPExcel->getActiveSheet()->setCellValue('U39', $bu);



		$objPHPExcel->getActiveSheet()->setCellValue('V39', $bv);



	 	$objPHPExcel->getActiveSheet()->setCellValue('W39', $bw);



		$objPHPExcel->getActiveSheet()->setCellValue('X39', $bx);



		$objPHPExcel->getActiveSheet()->setCellValue('Y39', $by);



		$objPHPExcel->getActiveSheet()->setCellValue('Z39', $bz);



		$objPHPExcel->getActiveSheet()->setCellValue('AA39', $baa);



		$objPHPExcel->getActiveSheet()->setCellValue('AB39', $bab);



		$objPHPExcel->getActiveSheet()->setCellValue('AC39', $bac);



  



		$objPHPExcel->getActiveSheet()->setCellValue('E69', $ge);



		$objPHPExcel->getActiveSheet()->setCellValue('F69', $gf);



		$objPHPExcel->getActiveSheet()->setCellValue('G69', $gg);



		$objPHPExcel->getActiveSheet()->setCellValue('H69', $gh);



		$objPHPExcel->getActiveSheet()->setCellValue('I69', $gi);



		$objPHPExcel->getActiveSheet()->setCellValue('J69', $gj);



		$objPHPExcel->getActiveSheet()->setCellValue('K69', $gk);



		$objPHPExcel->getActiveSheet()->setCellValue('L69', $gl);



		$objPHPExcel->getActiveSheet()->setCellValue('M69', $gm);



		$objPHPExcel->getActiveSheet()->setCellValue('N69', $gn);



		$objPHPExcel->getActiveSheet()->setCellValue('O69', $go);



		$objPHPExcel->getActiveSheet()->setCellValue('P69', $gp);



		$objPHPExcel->getActiveSheet()->setCellValue('Q69', $gq);



		$objPHPExcel->getActiveSheet()->setCellValue('R69', $gr);



		$objPHPExcel->getActiveSheet()->setCellValue('S69', $gs);



		$objPHPExcel->getActiveSheet()->setCellValue('T69', $gt);



		$objPHPExcel->getActiveSheet()->setCellValue('U69', $gu);



		$objPHPExcel->getActiveSheet()->setCellValue('V69', $gv);



	 	$objPHPExcel->getActiveSheet()->setCellValue('W69', $gw);



		$objPHPExcel->getActiveSheet()->setCellValue('X69', $gx);



		$objPHPExcel->getActiveSheet()->setCellValue('Y69', $gy);



		$objPHPExcel->getActiveSheet()->setCellValue('Z69', $gz);



		$objPHPExcel->getActiveSheet()->setCellValue('AA69', $gaa);



		$objPHPExcel->getActiveSheet()->setCellValue('AB69', $gab);



		$objPHPExcel->getActiveSheet()->setCellValue('AC69', $gac);







		$objPHPExcel->getActiveSheet()->setCellValue('E70', $be+$ge);



		$objPHPExcel->getActiveSheet()->setCellValue('F70', $bf+$gf);



		$objPHPExcel->getActiveSheet()->setCellValue('G70', $bg+$gg);



		$objPHPExcel->getActiveSheet()->setCellValue('H70', $bh+$gh);



		$objPHPExcel->getActiveSheet()->setCellValue('I70', $bi+$gi);



		$objPHPExcel->getActiveSheet()->setCellValue('J70', $bj+$gj);



		$objPHPExcel->getActiveSheet()->setCellValue('K70', $bk+$gk);



		$objPHPExcel->getActiveSheet()->setCellValue('L70', $bl+$gl);



		$objPHPExcel->getActiveSheet()->setCellValue('M70', $bm+$gm);



		$objPHPExcel->getActiveSheet()->setCellValue('N70', $bn+$gn);



		$objPHPExcel->getActiveSheet()->setCellValue('O70', $bo+$go);



		$objPHPExcel->getActiveSheet()->setCellValue('P70', $bp+$gp);



		$objPHPExcel->getActiveSheet()->setCellValue('Q70', $bq+$gq);



		$objPHPExcel->getActiveSheet()->setCellValue('R70', $br+$gr);



		$objPHPExcel->getActiveSheet()->setCellValue('S70', $bs+$gs);



		$objPHPExcel->getActiveSheet()->setCellValue('T70', $bt+$gt);



		$objPHPExcel->getActiveSheet()->setCellValue('U70', $bu+$gu);



		$objPHPExcel->getActiveSheet()->setCellValue('V70', $bv+$gv);



	 	$objPHPExcel->getActiveSheet()->setCellValue('W70', $bw+$gw);



		$objPHPExcel->getActiveSheet()->setCellValue('X70', $bx+$gx);



		$objPHPExcel->getActiveSheet()->setCellValue('Y70', $by+$gy);



		$objPHPExcel->getActiveSheet()->setCellValue('Z70', $bz+$gz);



		$objPHPExcel->getActiveSheet()->setCellValue('AA70', $baa+$gaa);



		$objPHPExcel->getActiveSheet()->setCellValue('AB70', $bab+$gab);



		$objPHPExcel->getActiveSheet()->setCellValue('AC70', $bac+$gac);







		/*	



		*/



		







        // $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(12);



        $objRichText = new PHPExcel_RichText();



        $objRichText->createText("SF2 Attendance");



        $objRichText = new PHPExcel_RichText();



        $objRichText->createText("Student Details");



        // $students = $this->student->getstudents;



        $students = array();



        $objPHPExcel->getActiveSheet()->setCellValue('D6', $getCurrentSchoolId);



        $objPHPExcel->getActiveSheet()->setCellValue('L6', $getCurrentSessionName);



        $objPHPExcel->getActiveSheet()->setCellValue('Y6', $month_selected);



        $objPHPExcel->getActiveSheet()->setCellValue('D8', $get_school_name);



        $objPHPExcel->getActiveSheet()->setCellValue('Y8', $class_name);



        $objPHPExcel->getActiveSheet()->setCellValue('AD8', $section_name);



        // $objPHPExcel->getActiveSheet()->fromArray($students); 


        $filename = "SF2 Attendance ".$month_selected.".xls";



        $objPHPExcel->getActiveSheet()->setTitle( $month_selected );



        $objPHPExcel->setActiveSheetIndex(0);



        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");



        header("Content-Disposition: attachment;filename=\"".$filename."\";");



        header("Cache-Control: max-age=0");



        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



        $objWriter->save('php://output');











	}










 	function weekofmonth($time) {







        $firstday       = 1;



        $lastday        = date('j',$time);



        $lastdayweek = 6; //Saturday







        $week = 1;



        for ($day=1;$day<=$lastday;$day++) {



            $timetmp = mktime(0, 0, 0, date('n',$time), $day, date('Y',$time));



            if (date('N',$timetmp) == $lastdayweek) {



                $week++;



            }



        }



        if (date('N',$time)==$lastdayweek) {



            $week--;



        }







        return $week;



    }	







    public function generate_spreadsheet_secondary( $parameters ){







    	$CI =& get_instance();



		$CI->load->model('Student_model');



		$CI->load->model('Classsection_model');



		$CI->load->model('Class_model');



		$CI->load->model('Section_model');



		$CI->load->model('Subject_model');



		$CI->load->model('Setting_model');



		$CI->load->model('Session_model');



	   	$class_id = $parameters['class_id'];



	   	$session_id = $parameters['session_id'];



	   	$subject_id = $parameters['subject_id'];







		$class_details = $CI->Class_model->get( $class_id ); 	



		$class_name = $class_details['class'];







		$session_details = $CI->Session_model->get( $session_id ); 	



		$session_name = $session_details['session'];







		$subject_details = $CI->Subject_model->get( $subject_id ); 	



		$subject_name = $subject_details['name'];











		$class_list_details = $CI->Student_model->getstudentsByClassSectionGender( $class_id, null, null ); 	 







		include ("application/third_party/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");



		$class_id = $parameters['class_id'];



		$session_id = $parameters['session_id'];



		$file = 'Secondary_archive_'.$class_name.'.xls';



		$filePath = "././uploads/ArchiveSecondayTemplate.xlsx";  







		$objReader   = new PHPExcel_Reader_Excel2007();



		$objPHPExcel = $objReader->load($filePath);







		$objPHPExcel->getActiveSheet()->setCellValue('B1', $class_name );



		$objPHPExcel->getActiveSheet()->setCellValue('D1', $subject_name );



		$objPHPExcel->getActiveSheet()->setCellValue('F1', 'SY: '.$session_name );







		if( $class_list_details ){ 



			$start = 3;



			$numnber = 1;



			 $styleArray = array(



			      'borders' => array(



			          'allborders' => array(



			              'style' => PHPExcel_Style_Border::BORDER_THIN



			          )



			      )



			  );



			



			foreach ($class_list_details as $key => $value) {



				 $objPHPExcel->getActiveSheet()->setCellValue('A'.$start, $value['admission_no']);



				 $objPHPExcel->getActiveSheet()->setCellValue('B'.$start, $value['lastname'] );



				 $objPHPExcel->getActiveSheet()->setCellValue('C'.$start, $value['firstname'] );



				 $objPHPExcel->getActiveSheet()->setCellValue('D'.$start, $value['middlename'] );



				 $objPHPExcel->getActiveSheet()->setCellValue('E'.$start, $value['gender'] );



				 $objPHPExcel->getActiveSheet()->getStyle('A'.$start.':I'.$start)->applyFromArray($styleArray);



				 $numnber++;



				 $start++;



			}



		}







		$objPHPExcel->setActiveSheetIndex(0);  



		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');



		header('Content-type: application/vnd.ms-excel');



		header('Content-Disposition: attachment; filename='.$file);



		$objWriter->save('php://output');



 



	}



	



	public function generate_spreadsheet_attendance( $parameters ){







    	$CI =& get_instance();



		$CI->load->model('Student_model');



		$CI->load->model('Classsection_model');



		$CI->load->model('Class_model');



		$CI->load->model('Section_model');



		$CI->load->model('Subject_model');



		$CI->load->model('Setting_model');



		$CI->load->model('Session_model');



	   	$class_id = $parameters['class_id'];



	   	$section_id = $parameters['section_id']; 







		$class_details = $CI->Class_model->get( $class_id ); 	



		$class_name = $class_details['class'];







		$section_details = $CI->Section_model->get( $section_id ); 	



		$section_name = $section_details['section'];



 











		$class_list_details = $CI->Student_model->getstudentsByClassSectionGender( $class_id, null, null ); 	 







		include ("application/third_party/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");



		$class_id = $parameters['class_id']; 



		$file = 'Monthly_Attendance'.$class_name.'_'.$section_name.'.xls';



		$filePath = "././uploads/MonthlyAttendanceTemplate.xlsx";  







		$objReader   = new PHPExcel_Reader_Excel2007();



		$objPHPExcel = $objReader->load($filePath);







		$objPHPExcel->getActiveSheet()->setCellValue('B1', $class_name ); 



		$objPHPExcel->getActiveSheet()->setCellValue('D1', $section_name );  



		if( $class_list_details ){ 



			$start = 3;



			$numnber = 1;



			 $styleArray = array(



			      'borders' => array(



			          'allborders' => array(



			              'style' => PHPExcel_Style_Border::BORDER_THIN



			          )



			      )



			  );



			



			foreach ($class_list_details as $key => $value) {



				 $objPHPExcel->getActiveSheet()->setCellValue('A'.$start, $value['admission_no']);



				 $objPHPExcel->getActiveSheet()->setCellValue('B'.$start, $value['lastname'] );



				 $objPHPExcel->getActiveSheet()->setCellValue('C'.$start, $value['firstname'] );



				 $objPHPExcel->getActiveSheet()->setCellValue('D'.$start, $value['middlename'] );



				 $objPHPExcel->getActiveSheet()->setCellValue('E'.$start, $value['gender'] );



				 $objPHPExcel->getActiveSheet()->getStyle('A'.$start.':AJ'.$start)->applyFromArray($styleArray);



				 $numnber++;



				 $start++;



			}



		}







		$objPHPExcel->setActiveSheetIndex(0);  



		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');



		header('Content-type: application/vnd.ms-excel');



		header('Content-Disposition: attachment; filename='.$file);



		$objWriter->save('php://output');



 



	}







	public function generate_sf5( $class_id, $section_id, $quarter, $semester_id, $strand_id, $registrar_fullname, $principal_fullname ){ 



        // $objPHPExcel = new PHPExcel();







		ini_set('memory_limit', '-1');



		$CI =& get_instance();



		$CI->load->model('Student_model');



		$CI->load->model('Class_model');



		$CI->load->model('Section_model');



		$CI->load->model('Subject_model');



		$CI->load->model('Setting_model');



		$CI->load->model('Stuattendence_model');



		$CI->load->model('Examresult_model');



		$CI->load->model('Gradingsetting_model');



		$CI->load->model('Subjectmanager_model');



		$CI->load->model('Gradingsetting_model');



		$CI->load->model('Subjectcombine_model');



		$CI->load->model('Specializedsubject_model');



		$CI->load->model('Customsubject_model');



 



		$class_name = null;



		$teachers_name = null;



		$section_name = null;



		$get_school_name = $CI->Setting_model->getCurrentSchoolName();



		$getCurrentSessionName = $CI->Setting_model->getCurrentSessionName();



		$getCurrentSchoolId = $CI->Setting_model->getCurrentSchoolId();



		$getCurrentSchoolAddress = $CI->Setting_model->getCurrentSchoolAddress();



		$boys_students = $CI->Student_model->getstudentsByClassSectionGender( $class_id, $section_id, 'Male');



		$girl_students = $CI->Student_model->getstudentsByClassSectionGender( $class_id, $section_id, 'Female'); 	



	 	



		if( $class_id != null ){



			$class_details = $CI->Class_model->get( $class_id ); 	



			$class_name = $class_details['class'];



		}



		



		if( $section_id != null ){



			$section_details = $CI->Section_model->get( $section_id ); 	



			$section_name = $section_details['section'];



		}



		 







		$opendoc = "././uploads/SF5Template.xlsx";



		$objReader   = new PHPExcel_Reader_Excel2007();







		$objReader->setIncludeCharts(TRUE);







		$objPHPExcel = $objReader->load($opendoc); 



		  



		$start_boys_numbers = 13;



		$end_boys_numbers =  count(	$boys_students );



 



		$boys_start_id_letters = "A";



		$start_boys_letters = "B";  



		$boys_start_grade_letters = "F";  



		$boys_start_action_letters = "G";  



		$boys_start_comment_letters = "I";  



		$total_male_promoted = 0;



		$total_female_promoted = 0;



		$total_male_conditional = 0;



		$total_female_conditional = 0;



		$total_male_retained = 0;



		$total_female_retained = 0;



		$total_number_male = 0;



		$total_number_female = 0;



		$gradingsettings = $CI->gradingsetting_model->getbySession();



		$allow_to_pass = isset($gradingsettings->allow_to_pass)?$gradingsettings->allow_to_pass:'no';



		$complete_grades = true;



		if( $boys_students ){ 



			set_time_limit(0);



			foreach ($boys_students as $boys_students_key => $boys_students_value) {



				$student_id = $boys_students_value['id'];



				$strand_id = $boys_students_value['strand_id'];



				$boys_lrn = $boys_students_value['lrn']?$boys_students_value['lrn']:$boys_students_value['roll_no'];



				$boys_lrn = str_replace(' ', '', $boys_lrn); 



				$boys_lrn = preg_replace('/\s+/', '', $boys_lrn); 



				$objPHPExcel->getActiveSheet()->setCellValue($boys_start_id_letters.$start_boys_numbers, $boys_lrn ); 



				$objPHPExcel->getActiveSheet()->setCellValue($start_boys_letters.$start_boys_numbers, strtoupper($boys_students_value['lastname'].", ".$boys_students_value['firstname'] ." ".$boys_students_value['middlename']) ); 



                $total_grade = 0;



                $total_subject_list = 0; 



                if( $semester_id  ){ 



	                $gradingsettings = $CI->Gradingsetting_model->get(); 



	                if( $semester_id == 1 ){



	                    $get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array(); 



	                } else {



	                    $get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();   



	                }



	                $get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;



	                $sql_quarter = $CI->Student_model->sql_array_to_string( $get_quarter );  



	                $total_quarter = count($get_quarter); 







					$subjectlist = $CI->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id );



					$custom_subject = $CI->customsubject_model->getSubjectbySemester( $student_id, $semester_id );



					if( $custom_subject ){



						$subjectlist = array_merge( $subjectlist, $custom_subject );



					}	



					foreach ($subjectlist as $subject_key => $subject_value) {



						if(isset($subject_value['subject_id'])){



							$subject_manager_details = $CI->subjectmanager_model->getBySubjectSession( $subject_value['subject_id'] );



							$include_computation = $subject_manager_details['include_computation'];



							$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;



							$total_current_grade = 0;



							if( $include_computation == 'yes'){



								for ($q_i=1; $q_i <= $total_quarter ; $q_i++) { 



									//$examresult = $CI->examresult_model->get_exam_results_by_subject_quarter( $student_id, $q_i,  $subject_value['id']   );



									$grade_per_quarter = $CI->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_value['subject_id'], $q_i );



									if( $grade_per_quarter == null ){



										$complete_grades = false;



									}



									$total_current_grade +=  $grade_per_quarter; 



								}







								$display_grade =  $total_current_grade / $total_quarter;



								$checkifChild = $CI->subjectcombine_model->checkifChild( $subject_value['subject_id'] );



								if( empty( $checkifChild ) ){



									if( $allow_to_pass == 'yes'){



										if( $display_grade == '74.5'){



											$display_grade = '75';



										} elseif(  $display_grade > '74.4' && $display_grade <  75 ){



											$display_grade = '75';



										}



									}



								}



															



								$display_grade =number_format( (float)$display_grade,2,".",".");



								



								if( empty( $checkifChild ) ){



									$display_grade = $display_grade * $units;



									$total_grade += $display_grade; 



									$total_subject_list = $total_subject_list + $units;



								}



							}



						}



					}



                } else {



					$subjectlist = $CI->Subject_model->getSubjctByClass( $class_id );



					$total_quarter = 4;



					$complete_grades = true;



					foreach ($subjectlist as $subject_key => $subject_value) { 



						if( $subject_value['id']){



							$subject_manager_details = $CI->subjectmanager_model->getBySubjectSession( $subject_value['id'] );



							$include_computation = $subject_manager_details['include_computation'];



							$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;



							$total_current_grade = 0;



							if( $include_computation == 'yes'){



								for($xy=1;$xy<=$total_quarter;$xy++){



									$grade_per_quarter = $CI->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_value['id'], $xy );



									if( $grade_per_quarter == null ){



										$complete_grades = false;



									}



									$total_current_grade +=  $grade_per_quarter;



								}



							



								$display_grade =  $total_current_grade / $total_quarter;



								$checkifChild = $CI->subjectcombine_model->checkifChild( $subject_value['id'] );



								if( empty( $checkifChild ) ){



									if( $allow_to_pass == 'yes'){



										if( $display_grade == '74.5'){



											$display_grade = '75';



										} elseif(  $display_grade > '74.4' && $display_grade <  75 ){



											$display_grade = '75';



										}



									}



								}



															



								$display_grade =number_format( (float)$display_grade,2,".",".");



								



								if( empty( $checkifChild ) ){



									$total_grade += $display_grade; 



									$total_subject_list++;



								}



							}



						}



	                }



                }







                $display_total_grade =  $total_grade / $total_subject_list;



       		 	$display_total_grade =number_format( (float)$display_total_grade,2,".",".");



                $get_final_remarks = $display_total_grade <= 74.4 ?"RETAINED":"PROMOTED";



                $display_total_grade = round( $display_total_grade, 0 ); 



       		 	$display_total_grade =number_format( (float)$display_total_grade,0,".",".");



                if( $get_final_remarks == 'PROMOTED'){



            	 	$get_final_comment = 'Meets Expectation';



            	 	$total_male_promoted++; 



                }elseif( $get_final_remarks == 'RETAINED'){



            	 	$get_final_comment = 'Did Not Meet Expectation';  



					$total_male_retained++; 



                } 



				if( $complete_grades  ){



					$objPHPExcel->getActiveSheet()->setCellValue($boys_start_grade_letters.$start_boys_numbers, number_format( (float)$display_total_grade,0 ) ); 



					$objPHPExcel->getActiveSheet()->setCellValue($boys_start_action_letters.$start_boys_numbers, $get_final_remarks ); 



					$objPHPExcel->getActiveSheet()->setCellValue($boys_start_comment_letters.$start_boys_numbers, $get_final_comment ); 



                } else {



					$objPHPExcel->getActiveSheet()->setCellValue($boys_start_grade_letters.$start_boys_numbers, ''); 



					$objPHPExcel->getActiveSheet()->setCellValue($boys_start_action_letters.$start_boys_numbers, ''); 



					$objPHPExcel->getActiveSheet()->setCellValue($boys_start_comment_letters.$start_boys_numbers, ''); 



				}



				$total_number_male++;



				$start_boys_numbers++;



			}



		}



		$start_girls_numbers = 41;



		if( $girl_students ){



			set_time_limit(0);



			foreach ($girl_students as $girl_students_key => $girl_students_value) { 



				$student_id = $boys_students_value['id'];



				$strand_id = $boys_students_value['strand_id'];



				$girl_lrn = $girl_students_value['lrn']?$girl_students_value['lrn']:$girl_students_value['roll_no'];



				$girl_lrn = str_replace(' ', '', $girl_lrn); 



				$girl_lrn = preg_replace('/\s+/', '', $girl_lrn); 



				$objPHPExcel->getActiveSheet()->setCellValue($boys_start_id_letters.$start_girls_numbers, $girl_lrn );  



				$objPHPExcel->getActiveSheet()->setCellValue($start_boys_letters.$start_girls_numbers, strtoupper($girl_students_value['lastname'].", ".$girl_students_value['firstname'] ." ".$girl_students_value['middlename'] ));







				$total_grade = 0;



                $total_subject_list = 0;



                if( $semester_id  ){ 



	                $gradingsettings = $CI->Gradingsetting_model->get(); 



	                if( $semester_id == 1 ){



	                    $get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array(); 



	                } else {



	                    $get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();   



	                }



	                $get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;



	                $sql_quarter = $CI->Student_model->sql_array_to_string( $get_quarter );  



	                $total_quarter = count($get_quarter);  



					$subjectlist = $CI->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id );



					$custom_subject = $CI->customsubject_model->getSubjectbySemester( $student_id, $semester_id );



					if( $custom_subject ){



						$subjectlist = array_merge( $subjectlist, $custom_subject );



					}					



					$complete_grades = true;



					foreach ($subjectlist as $subject_key => $subject_value) {



						if(isset($subject_value['subject_id'])){



							$subject_manager_details = $CI->subjectmanager_model->getBySubjectSession( $subject_value['subject_id'] );



							$include_computation = $subject_manager_details['include_computation'];



							$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;



							$total_current_grade = 0;



							if( $include_computation == 'yes'){



								for ($q_i=1; $q_i <= $total_quarter ; $q_i++) { 



									//$examresult = $CI->examresult_model->get_exam_results_by_subject_quarter( $student_id, $q_i,  $subject_value['id']   );



									$grade_per_quarter = $CI->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_value['subject_id'], $q_i );



									if( $grade_per_quarter == null ){



										$complete_grades = false;



									}



									$total_current_grade +=  $grade_per_quarter; 



								}







								$display_grade =  $total_current_grade / $total_quarter;



								$checkifChild = $CI->subjectcombine_model->checkifChild( $subject_value['subject_id'] );



								if( empty( $checkifChild ) ){



									if( $allow_to_pass == 'yes'){



										if( $display_grade == '74.5'){



											$display_grade = '75';



										} elseif(  $display_grade > '74.4' && $display_grade <  75 ){



											$display_grade = '75';



										}



									}



								}



															



								$display_grade =number_format( (float)$display_grade,2,".",".");



								



								if( empty( $checkifChild ) ){



									$display_grade = $display_grade * $units;



									$total_grade += $display_grade; 



									$total_subject_list = $total_subject_list + $units;



								}



							}



						}



					}



	                    



                } else { 



					$subjectlist = $CI->Subject_model->getSubjctByClass( $class_id ); 	



					$total_quarter = 4;					



					foreach ($subjectlist as $subject_key => $subject_value) { 



					$complete_grades = true;



						if( $subject_value['id']){



							$subject_manager_details = $CI->subjectmanager_model->getBySubjectSession( $subject_value['id'] );



							$include_computation = $subject_manager_details['include_computation'];



							$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;



							$total_current_grade = 0;



							if( $include_computation == 'yes'){



								for($xy=1;$xy<=$total_quarter;$xy++){



									$grade_per_quarter = $CI->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_value['id'], $xy );



									if( $grade_per_quarter == null ){



										$complete_grades = false;



									}



									$total_current_grade +=  $grade_per_quarter;



								}







							   $display_grade =  $total_current_grade / $total_quarter;



								$checkifChild = $CI->subjectcombine_model->checkifChild( $subject_value['id'] );



								if( empty( $checkifChild ) ){



									if( $allow_to_pass == 'yes'){



										if( $display_grade == '74.5'){



											$display_grade = '75';



										} elseif(  $display_grade > '74.4' && $display_grade <  75 ){



											$display_grade = '75';



										}



									}



								}



															



								$display_grade =number_format( (float)$display_grade,2,".",".");



								



								if( empty( $checkifChild ) ){



									$total_grade += $display_grade; 



									$total_subject_list++;



								}



							}



						}



	                }



                }







                $display_total_grade =  $total_grade / $total_subject_list;



       		 	$display_total_grade =number_format( (float)$display_total_grade,2,".",".");



                $get_final_remarks = $display_total_grade <= 74.4 ?"RETAINED":"PROMOTED";



                $display_total_grade = round( $display_total_grade, 0 );   



                if( $get_final_remarks == 'PROMOTED'){



            	 	$get_final_comment = 'Meets Expectation'; 



					$total_female_promoted++; 



                }elseif( $get_final_remarks == 'RETAINED'){



            	 	$get_final_comment = 'Did Not Meet Expectation';  



					$total_female_retained++;



                }



				if( $complete_grades  ){



					$objPHPExcel->getActiveSheet()->setCellValue($boys_start_grade_letters.$start_girls_numbers, number_format( (float)$display_total_grade,0 ) ); 



					$objPHPExcel->getActiveSheet()->setCellValue($boys_start_action_letters.$start_girls_numbers, $get_final_remarks ); 



					$objPHPExcel->getActiveSheet()->setCellValue($boys_start_comment_letters.$start_girls_numbers, $get_final_comment ); 



				} else {



					$objPHPExcel->getActiveSheet()->setCellValue($boys_start_grade_letters.$start_girls_numbers, '' ); 



					$objPHPExcel->getActiveSheet()->setCellValue($boys_start_action_letters.$start_girls_numbers, '' ); 



					$objPHPExcel->getActiveSheet()->setCellValue($boys_start_comment_letters.$start_girls_numbers, '' ); 



				}



				$total_number_female++;



				$start_girls_numbers++;



			}



		}



		/*for( $b=0; $start_boys_numbers <= $end_boys_numbers ; $b++ ){



		 	set_time_limit(0);



			if( !empty( $boys_students[$b] ) ){



				$boy_details  = $boys_students[$b]; 



				$objPHPExcel->getActiveSheet()->setCellValue($boys_start_id_letters.$start_boys_numbers, $boy_details['lrn'] ); 



				$objPHPExcel->getActiveSheet()->setCellValue($start_boys_letters.$start_boys_numbers, $boy_details['lastname'].", ".$boy_details['firstname'] .", ".$boy_details['middlename'] );



  			}



		}*/



        // $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(12);



        $objRichText = new PHPExcel_RichText();



        $objRichText->createText("School Form 5");



        $objRichText = new PHPExcel_RichText();



        $objRichText->createText("Student Details");



        // $students = $this->student->getstudents;



        $students = array();



        $objPHPExcel->getActiveSheet()->setCellValue('C5', $getCurrentSchoolId);



        $objPHPExcel->getActiveSheet()->setCellValue('G5', $getCurrentSessionName); 



        $objPHPExcel->getActiveSheet()->setCellValue('C7', $get_school_name);



        $objPHPExcel->getActiveSheet()->setCellValue('E3', $getCurrentSchoolAddress);



        $objPHPExcel->getActiveSheet()->setCellValue('J7', $class_name);



        $objPHPExcel->getActiveSheet()->setCellValue('M7', $section_name);



        $objPHPExcel->getActiveSheet()->setCellValue('M15', $total_male_promoted);



        $objPHPExcel->getActiveSheet()->setCellValue('N15', $total_female_promoted);



        $objPHPExcel->getActiveSheet()->setCellValue('O15', $total_male_promoted + $total_female_promoted );



        $objPHPExcel->getActiveSheet()->setCellValue('M17', $total_male_conditional);



        $objPHPExcel->getActiveSheet()->setCellValue('N17', $total_female_conditional);



        $objPHPExcel->getActiveSheet()->setCellValue('O17', $total_male_conditional + $total_female_conditional );



        $objPHPExcel->getActiveSheet()->setCellValue('M19', $total_male_retained);



        $objPHPExcel->getActiveSheet()->setCellValue('N19', $total_female_retained); 



        $objPHPExcel->getActiveSheet()->setCellValue('O19', $total_male_retained + $total_female_retained );



        $objPHPExcel->getActiveSheet()->setCellValue('L43', $registrar_fullname );



        $objPHPExcel->getActiveSheet()->setCellValue('L48', $principal_fullname ); 



        $objPHPExcel->getActiveSheet()->setCellValue('F40', $total_number_male ); 



        $objPHPExcel->getActiveSheet()->setCellValue('F68', $total_number_female ); 



        $objPHPExcel->getActiveSheet()->setCellValue('F69', $total_number_male + $total_number_female ); 



        // $objPHPExcel->getActiveSheet()->fromArray($students); 



        $filename = "School Form 5 ".$class_name." ".$section_name.".xls";



        $objPHPExcel->getActiveSheet()->setTitle( $class_name );



        $objPHPExcel->setActiveSheetIndex(0);



        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");



        header("Content-Disposition: attachment;filename=\"".$filename."\";");



        header("Cache-Control: max-age=0");



        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



        $objWriter->save('php://output');











	}



	

	public function generate_sf5_new( $class_id, $section_id, $quarter, $semester_id, $strand_id, $registrar_fullname, $principal_fullname, $session_id=null ){ 
        // $objPHPExcel = new PHPExcel();

		
		ini_set('memory_limit', '-1');
		$CI =& get_instance();
		$CI->load->model('Student_model');
		$CI->load->model('Class_model');
		$CI->load->model('Section_model');
		$CI->load->model('Subject_model');
		$CI->load->model('Setting_model');
		$CI->load->model('Stuattendence_model');
		$CI->load->model('Examresult_model');
		$CI->load->model('Gradingsetting_model');
		$CI->load->model('Subjectmanager_model');
		$CI->load->model('Gradingsetting_model');
		$CI->load->model('Subjectcombine_model');
		$CI->load->model('Specializedsubject_model');
		$CI->load->model('Customsubject_model');
		$CI->load->model('session_model');
		$CI->load->model('Grade_model');
		$CI->load->model('Studentstrand_model');
 
		$class_name = null;
		$teachers_name = null;
		$section_name = null;
		if( empty( $session_id ) ){
			$session_id =  $CI->Setting_model->getCurrentSession();
		}
		$session_details = $CI->session_model->get( $session_id );
		$getCurrentSessionName = $session_details['session'];
		//$getCurrentSessionName = $CI->Setting_model->getCurrentSessionName();
		$get_school_name = $CI->Setting_model->getCurrentSchoolName();
		$getCurrentSchoolId = $CI->Setting_model->getCurrentSchoolId();
		$getCurrentSchoolAddress = $CI->Setting_model->getCurrentSchoolAddress();
		$boys_students = $CI->Student_model->getstudentsByClassSectionGender( $class_id, $section_id, 'Male', $session_id );
		$girl_students = $CI->Student_model->getstudentsByClassSectionGender( $class_id, $section_id, 'Female', $session_id); 	
		
	 	$girl_students_count = count( $girl_students );
	 	$boys_students_count = count( $boys_students );
		if( $class_id != null ){
			$class_details = $CI->Class_model->get( $class_id ); 	
			$class_name = $class_details['class'];
		}
		
		if( $section_id != null ){
			$section_details = $CI->Section_model->get( $section_id ); 	
			$section_name = $section_details['section'];
		}
		 

		$opendoc = "././uploads/SF5TemplateLatest.xlsx";
		$objReader   = new PHPExcel_Reader_Excel2007();

		$objReader->setIncludeCharts(TRUE);

		$objPHPExcel = $objReader->load($opendoc); 
		  
		$start_boys_numbers = 13;
		$end_boys_numbers =  count(	$boys_students );
 
		$boys_start_id_letters = "A";
		$start_boys_letters = "B";  
		$boys_start_grade_letters = "F";  
		$boys_start_action_letters = "G";  
		$boys_start_comment_letters = "I";  
		$total_male_promoted = 0;
		$total_female_promoted = 0;
		$total_male_conditional = 0;
		$total_female_conditional = 0;
		$total_male_retained = 0;
		$total_female_retained = 0;
		$total_number_male = 0;
		$total_number_female = 0;
		$gradingsettings = $CI->gradingsetting_model->getbySession( $session_id );
		$allow_to_pass = isset($gradingsettings->allow_to_pass)?$gradingsettings->allow_to_pass:'no';
		$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:2;
		$combine_category = 'ga';
		$complete_grades = true;
		if( $boys_students ){ 
			set_time_limit(0);
			foreach ($boys_students as $boys_students_key => $boys_students_value) {
				$student_id = $boys_students_value['id'];
				$strand_id = $boys_students_value['strand_id'];
				$check_strand = $CI->Studentstrand_model->get_latest_strand( $student_id, $semester_id, $session_id );
				if( $check_strand ){
					$strand_id = $check_strand['strand_id'];
				}
				$boys_lrn = $boys_students_value['lrn']?$boys_students_value['lrn']:$boys_students_value['roll_no'];
				$boys_lrn = str_replace(' ', '', $boys_lrn); 
				$boys_lrn = preg_replace('/\s+/', '', $boys_lrn); 
				$objPHPExcel->getActiveSheet()->setCellValue($boys_start_id_letters.$start_boys_numbers, $boys_lrn ); 
				$objPHPExcel->getActiveSheet()->setCellValue($start_boys_letters.$start_boys_numbers, strtoupper($boys_students_value['lastname'].", ".$boys_students_value['firstname'] ." ".$boys_students_value['middlename']) ); 
                $total_grade = 0;
                $total_subject_list = 0; 
                if( $semester_id  ){ 
	                if( $semester_id == 1 ){
	                    $get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array(); 
	                } else {
	                    $get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();   
	                }
	                $get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
	                $sql_quarter = $CI->Student_model->sql_array_to_string( $get_quarter );  
	                $total_quarter = count($get_quarter); 

					$subjectlist = $CI->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id, $session_id );
					$custom_subject = $CI->customsubject_model->getSubjectbySemester( $student_id, $semester_id, $session_id );
					if( $custom_subject ){
						$subjectlist = array_merge( $subjectlist, $custom_subject );
					}	
					$subjectlist = $CI->customsubject_model->unsetsubjectdrop( $subjectlist, $student_id, $semester_id );
					$complete_grades = true;
					foreach ($subjectlist as $subject_key => $subject_value) {
						if(isset($subject_value['subject_id'])){
							$complete_grades = true;
							$subject_manager_details = $CI->subjectmanager_model->getBySubjectSession( $subject_value['subject_id'], $session_id );
							$include_computation = $subject_manager_details['include_computation'];
							$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
							$total_current_grade = 0;
							if( $include_computation == 'yes'){
								for ($q_i=0; $q_i < $total_quarter ; $q_i++) { 
									$set_quarter = $get_quarter[ $q_i ];
									//$examresult = $CI->examresult_model->get_exam_results_by_subject_quarter( $student_id, $q_i,  $subject_value['id']   );
									$grade_per_quarter = $CI->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_value['subject_id'], $set_quarter, $combine_category, $session_id );
									if( $grade_per_quarter == null ){
										$complete_grades = false;
									}
									$total_current_grade +=  $grade_per_quarter; 
								}

								$display_grade =  $total_current_grade / $total_quarter;
								$checkifChild = $CI->subjectcombine_model->checkifChild( $subject_value['subject_id'], $combine_category, $session_id );
								if( empty( $checkifChild ) ){
									if( $allow_to_pass == 'yes'){
										if( $display_grade == '74.5'){
											$display_grade = '75';
										} elseif(  $display_grade > '74.4' && $display_grade <  75 ){
											$display_grade = '75';
										}
									}
								}
															
								$display_grade =number_format( (float)$display_grade, $decimal_finalgrade ,".",".");
								
								if( empty( $checkifChild ) ){
									$display_grade = $display_grade * $units;
									$total_grade += $display_grade; 
									$total_subject_list = $total_subject_list + $units;
								}
							}
						}
					}
                } else {
					$subjectlist = $CI->Subject_model->getSubjctByClass( $class_id );
					$total_quarter = 4;
					$complete_grades = true;
					$complete_first = true;
					$complete_second = true;
					$complete_third = true;
					$complete_fourth = true;
					$first = 0;
					$second = 0;
					$third = 0;
					$fourth = 0;
					$count_subjects_first = 0;
					$count_subjects_second = 0;
					$count_subjects_third = 0;
					$count_subjects_fourth = 0;	
					foreach ($subjectlist as $subject_key => $subject_value) { 
						if( $subject_value['id']){
							$complete_grades = true;
							$subject_manager_details = $CI->subjectmanager_model->getBySubjectSession( $subject_value['id'], $session_id  );
							$include_computation = $subject_manager_details['include_computation'];
							$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
							
							$total_current_grade = 0;
							if( $include_computation == 'yes'){
								for($xy=1;$xy<=$total_quarter;$xy++){
									$grade_per_quarter = $CI->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_value['id'], $xy, $combine_category, $session_id  );
									if( $grade_per_quarter == null ){
										$complete_grades = false;
									}
									
									$checkifChild = $CI->subjectcombine_model->checkifChild( $subject_value['id'], $combine_category, $session_id );
									if( empty( $checkifChild ) ){
										if($xy==1){ 
											$first = $first + $grade_per_quarter;
											//$grade_per_quarter != null?$count_subjects_first ++:'';
											$count_subjects_first ++;
											if( empty( $grade_per_quarter )){
												$complete_first = false;
											}
										
										} elseif($xy==2){ 
											$second = $second + $grade_per_quarter; 
											//$grade_per_quarter != null?$count_subjects_second++:'';
											$count_subjects_second++;
											if( empty( $grade_per_quarter )){
												$complete_second = false;
											}
										} elseif($xy==3){ 
											$third = $third + $grade_per_quarter; 
											//$grade_per_quarter != null?$count_subjects_third++:'';
											$count_subjects_third++;
											if( empty( $grade_per_quarter )){
												$complete_third = false;
											}
										} elseif($xy==4){ 
											$fourth = $fourth + $grade_per_quarter;
											//$grade_per_quarter != null?$count_subjects_fourth++:'';		
											$count_subjects_fourth++;		
											if( empty( $grade_per_quarter )){
												$complete_fourth = false;
											}																		
										}
									}
																	
									$total_current_grade +=  $grade_per_quarter;
								}
							
								$display_grade =  $total_current_grade / $total_quarter;
								$checkifChild = $CI->subjectcombine_model->checkifChild( $subject_value['id'], $combine_category, $session_id );
								if( empty( $checkifChild ) ){
									if( $allow_to_pass == 'yes'){
										if( $display_grade == '74.5'){
											$display_grade = '75';
										} elseif(  $display_grade > '74.4' && $display_grade <  75 ){
											$display_grade = '75';
										}
									}
								}
															
								$display_grade =number_format( (float)$display_grade, $decimal_finalgrade,".",".");
								if( empty( $checkifChild ) ){
									$total_grade += $display_grade; 
									$total_subject_list++;
								}
							}
						}
	                }
                }
							
                $display_total_grade =  $total_subject_list != 0 ? $total_grade / $total_subject_list:'';
				if( $display_total_grade ){
					$getrankingstatus = $CI->Grade_model->getrankingstatus( $student_id, $display_total_grade );
					if( $getrankingstatus ){
						$display_total_grade =number_format( (float)$display_total_grade,3,".",".");
					} else {
						$display_total_grade =number_format( (float)$display_total_grade,2,".",".");
					}
				}
                $get_final_remarks = $display_total_grade <= 74.4 ?"RETAINED":"PROMOTED";
                if( $get_final_remarks == 'PROMOTED'){
            	 	$get_final_comment = 'Meets Expectation';
            	 	$total_male_promoted++; 
                }elseif( $get_final_remarks == 'RETAINED'){
            	 	$get_final_comment = 'Did Not Meet Expectation';  
					$total_male_retained++; 
                } 
				if( $semester_id ){
					if( $complete_grades  ){
						$objPHPExcel->getActiveSheet()->setCellValue($boys_start_grade_letters.$start_boys_numbers, $display_total_grade ); 
						$objPHPExcel->getActiveSheet()->setCellValue($boys_start_action_letters.$start_boys_numbers, $get_final_remarks ); 
						//$objPHPExcel->getActiveSheet()->setCellValue($boys_start_comment_letters.$start_boys_numbers, $get_final_comment ); 
					} else {
						$objPHPExcel->getActiveSheet()->setCellValue($boys_start_grade_letters.$start_boys_numbers, ' '); 
						$objPHPExcel->getActiveSheet()->setCellValue($boys_start_action_letters.$start_boys_numbers, ' '); 
						//$objPHPExcel->getActiveSheet()->setCellValue($boys_start_comment_letters.$start_boys_numbers, ' '); 
					}
				} else {
					if( $complete_first && $complete_second && $complete_third && $complete_fourth  ){
						$objPHPExcel->getActiveSheet()->setCellValue($boys_start_grade_letters.$start_boys_numbers, $display_total_grade ); 
						$objPHPExcel->getActiveSheet()->setCellValue($boys_start_action_letters.$start_boys_numbers, $get_final_remarks ); 
						//$objPHPExcel->getActiveSheet()->setCellValue($boys_start_comment_letters.$start_boys_numbers, $get_final_comment ); 
					} else {
						$objPHPExcel->getActiveSheet()->setCellValue($boys_start_grade_letters.$start_boys_numbers, ' '); 
						$objPHPExcel->getActiveSheet()->setCellValue($boys_start_action_letters.$start_boys_numbers, ' '); 
						//$objPHPExcel->getActiveSheet()->setCellValue($boys_start_comment_letters.$start_boys_numbers, ' '); 
					}
				}
				
				$total_number_male++;
				$start_boys_numbers++;
			}
		}
		$start_girls_numbers = 46;
	
		if( $girl_students ){
			set_time_limit(0);
			foreach ($girl_students as $girl_students_key => $girl_students_value) { 
				$student_id = $girl_students_value['id'];
				$strand_id = $girl_students_value['strand_id'];
				$check_strand = $CI->Studentstrand_model->get_latest_strand( $student_id, $semester_id, $session_id );
				if( $check_strand ){
					$strand_id = $check_strand['strand_id'];
				}
				$girl_lrn = $girl_students_value['lrn']?$girl_students_value['lrn']:$girl_students_value['roll_no'];
				$girl_lrn = str_replace(' ', '', $girl_lrn); 
				$girl_lrn = preg_replace('/\s+/', '', $girl_lrn); 
				$objPHPExcel->getActiveSheet()->setCellValue($boys_start_id_letters.$start_girls_numbers, $girl_lrn );  
				$objPHPExcel->getActiveSheet()->setCellValue($start_boys_letters.$start_girls_numbers, strtoupper($girl_students_value['lastname'].", ".$girl_students_value['firstname'] ." ".$girl_students_value['middlename'] ));

				$total_grade = 0;
                $total_subject_list = 0;
                if( $semester_id  ){ 
	                if( $semester_id == 1 ){
	                    $get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array(); 
	                } else {
	                    $get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();   
	                }
	                $get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
	                $sql_quarter = $CI->Student_model->sql_array_to_string( $get_quarter );  
	                $total_quarter = count($get_quarter);  
					$subjectlist = $CI->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id, $session_id );
					$custom_subject = $CI->customsubject_model->getSubjectbySemester( $student_id, $semester_id, $session_id );
					if( $custom_subject ){
						$subjectlist = array_merge( $subjectlist, $custom_subject );
					}		
					$subjectlist = $CI->customsubject_model->unsetsubjectdrop( $subjectlist, $student_id, $semester_id );					
					$complete_grades = true;
					$complete_first = true;
					$complete_second = true;
					$complete_third = true;
					$complete_fourth = true;
					foreach ($subjectlist as $subject_key => $subject_value) {
						if(isset($subject_value['subject_id'])){
							$complete_grades = true;
							$subject_manager_details = $CI->subjectmanager_model->getBySubjectSession( $subject_value['subject_id'], $session_id );
							$include_computation = $subject_manager_details['include_computation'];
							$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
							$total_current_grade = 0;
							if( $include_computation == 'yes'){
								for ($q_i=0; $q_i < $total_quarter ; $q_i++) { 
									$set_quarter = $get_quarter[ $q_i ];
									//$examresult = $CI->examresult_model->get_exam_results_by_subject_quarter( $student_id, $q_i,  $subject_value['id']   );
									$grade_per_quarter = $CI->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_value['subject_id'], $set_quarter, $combine_category, $session_id );
									if( $grade_per_quarter == null ){
										$complete_grades = false;
									}
									$total_current_grade +=  $grade_per_quarter; 
								}

								$display_grade =  $total_current_grade / $total_quarter;
								$checkifChild = $CI->subjectcombine_model->checkifChild( $subject_value['subject_id'], $combine_category, $session_id );
								if( empty( $checkifChild ) ){
									if( $allow_to_pass == 'yes'){
										if( $display_grade == '74.5'){
											$display_grade = '75';
										} elseif(  $display_grade > '74.4' && $display_grade <  75 ){
											$display_grade = '75';
										}
									}
								}
															
								$display_grade =number_format( (float)$display_grade, $decimal_finalgrade,".",".");
								
								if( empty( $checkifChild ) ){
									$display_grade = $display_grade * $units;
									$total_grade += $display_grade; 
									$total_subject_list = $total_subject_list + $units;
								}
							}
						}
					}
	                    
                } else { 
					$subjectlist = $CI->Subject_model->getSubjctByClass( $class_id ); 	
					$total_quarter = 4;		
					$complete_grades = true;
					$complete_first = true;
					$complete_second = true;
					$complete_third = true;
					$complete_fourth = true;
					$first = 0;
					$second = 0;
					$third = 0;
					$fourth = 0;
					$count_subjects_first = 0;
					$count_subjects_second = 0;
					$count_subjects_third = 0;
					$count_subjects_fourth = 0;					
					foreach ($subjectlist as $subject_key => $subject_value) { 
						if( $subject_value['id']){
							$complete_grades = true;
							$subject_manager_details = $CI->subjectmanager_model->getBySubjectSession( $subject_value['id'], $session_id );
							$include_computation = $subject_manager_details['include_computation'];
							$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
							$total_current_grade = 0;
								if( $include_computation == 'yes'){
								for($xy=1;$xy<=$total_quarter;$xy++){
									$grade_per_quarter = $CI->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_value['id'], $xy, $combine_category, $session_id  );
									if( $grade_per_quarter == null ){
										$complete_grades = false;
									}
									
									$checkifChild = $CI->subjectcombine_model->checkifChild( $subject_value['id'], $combine_category, $session_id );
									if( empty( $checkifChild ) ){
										if($xy==1){ 
											$first = $first + $grade_per_quarter;
											//$grade_per_quarter != null?$count_subjects_first ++:'';
											$count_subjects_first ++;
											if( empty( $grade_per_quarter )){
												$complete_first = false;
											}
										
										} elseif($xy==2){ 
											$second = $second + $grade_per_quarter; 
											//$grade_per_quarter != null?$count_subjects_second++:'';
											$count_subjects_second++;
											if( empty( $grade_per_quarter )){
												$complete_second = false;
											}
										} elseif($xy==3){ 
											$third = $third + $grade_per_quarter; 
											//$grade_per_quarter != null?$count_subjects_third++:'';
											$count_subjects_third++;
											if( empty( $grade_per_quarter )){
												$complete_third = false;
											}
										} elseif($xy==4){ 
											$fourth = $fourth + $grade_per_quarter;
											//$grade_per_quarter != null?$count_subjects_fourth++:'';		
											$count_subjects_fourth++;		
											if( empty( $grade_per_quarter )){
												$complete_fourth = false;
											}																		
										}
									}
													
									$total_current_grade +=  $grade_per_quarter;
								}
							
								$display_grade =  $total_current_grade / $total_quarter;
							
								if( empty( $checkifChild ) ){
									if( $allow_to_pass == 'yes'){
										if( $display_grade == '74.5'){
											$display_grade = '75';
										} elseif(  $display_grade > '74.4' && $display_grade <  75 ){
											$display_grade = '75';
										}
									}
								}
															
								$display_grade =number_format( (float)$display_grade, $decimal_finalgrade,".",".");
								
								if( empty( $checkifChild ) ){
									$total_grade += $display_grade; 
									$total_subject_list++;
								}
							}
						}
	                }
                }
				
                $display_total_grade =  $total_subject_list != 0 ? $total_grade / $total_subject_list:'';
				if( $display_total_grade ){
					$getrankingstatus = $CI->Grade_model->getrankingstatus( $student_id, $display_total_grade );
					if( $getrankingstatus ){
						$display_total_grade =number_format( (float)$display_total_grade,3,".",".");
					} else {
						$display_total_grade =number_format( (float)$display_total_grade,2,".",".");
					}
				}
       		 	
                $get_final_remarks = $display_total_grade <= 74.4 ?"RETAINED":"PROMOTED";
                if( $get_final_remarks == 'PROMOTED'){
            	 	$get_final_comment = 'Meets Expectation'; 
					$total_female_promoted++; 
                } elseif( $get_final_remarks == 'RETAINED'){
            	 	$get_final_comment = 'Did Not Meet Expectation';  
					$total_female_retained++;
                }
			
				
				if( $semester_id ){
					if( $complete_grades  ){
						$objPHPExcel->getActiveSheet()->setCellValue($boys_start_grade_letters.$start_girls_numbers, $display_total_grade ); 
						$objPHPExcel->getActiveSheet()->setCellValue($boys_start_action_letters.$start_girls_numbers, $get_final_remarks ); 
						//$objPHPExcel->getActiveSheet()->setCellValue($boys_start_comment_letters.$start_girls_numbers, $get_final_comment ); 
					} else {
						$objPHPExcel->getActiveSheet()->setCellValue($boys_start_grade_letters.$start_girls_numbers, ''); 
						$objPHPExcel->getActiveSheet()->setCellValue($boys_start_action_letters.$start_girls_numbers, ''); 
						//$objPHPExcel->getActiveSheet()->setCellValue($boys_start_comment_letters.$start_girls_numbers, ''); 
					}
				} else {
					
					if( $complete_first && $complete_second && $complete_third && $complete_fourth  ){
						$objPHPExcel->getActiveSheet()->setCellValue($boys_start_grade_letters.$start_girls_numbers, $display_total_grade ); 
						$objPHPExcel->getActiveSheet()->setCellValue($boys_start_action_letters.$start_girls_numbers, $get_final_remarks ); 
						//$objPHPExcel->getActiveSheet()->setCellValue($boys_start_comment_letters.$start_girls_numbers, $get_final_comment ); 
					} else {
						$objPHPExcel->getActiveSheet()->setCellValue($boys_start_grade_letters.$start_girls_numbers, ''); 
						$objPHPExcel->getActiveSheet()->setCellValue($boys_start_action_letters.$start_girls_numbers, ''); 
						//$objPHPExcel->getActiveSheet()->setCellValue($boys_start_comment_letters.$start_girls_numbers, ''); 
					}
				}
				$total_number_female++;
				$start_girls_numbers++;
			}
		}

        $objRichText = new PHPExcel_RichText();
        $objRichText->createText("School Form 5");
        $objRichText = new PHPExcel_RichText();
        $objRichText->createText("Student Details");
        // $students = $this->student->getstudents;
        $students = array();
         $objPHPExcel->getActiveSheet()->setCellValue('C5', $getCurrentSchoolId);
        $objPHPExcel->getActiveSheet()->setCellValue('G5', $getCurrentSessionName); 
        $objPHPExcel->getActiveSheet()->setCellValue('C7', $get_school_name);
        $objPHPExcel->getActiveSheet()->setCellValue('E3', $getCurrentSchoolAddress);
        $objPHPExcel->getActiveSheet()->setCellValue('J7', $class_name);
        $objPHPExcel->getActiveSheet()->setCellValue('L7', $section_name);
		$objPHPExcel->getActiveSheet()->setCellValue('N15', $total_male_promoted);
        $objPHPExcel->getActiveSheet()->setCellValue('O15', $total_female_promoted);
        $objPHPExcel->getActiveSheet()->setCellValue('P15', $total_male_promoted + $total_female_promoted );
        $objPHPExcel->getActiveSheet()->setCellValue('N17', $total_male_conditional);
        $objPHPExcel->getActiveSheet()->setCellValue('O17', $total_female_conditional);
        $objPHPExcel->getActiveSheet()->setCellValue('p17', $total_male_conditional + $total_female_conditional );
        $objPHPExcel->getActiveSheet()->setCellValue('N19', $total_male_retained);
        $objPHPExcel->getActiveSheet()->setCellValue('O19', $total_female_retained); 
        $objPHPExcel->getActiveSheet()->setCellValue('P19', $total_male_retained + $total_female_retained );		
        $objPHPExcel->getActiveSheet()->setCellValue('N45', $registrar_fullname );
        $objPHPExcel->getActiveSheet()->setCellValue('N50', $principal_fullname ); 
        $objPHPExcel->getActiveSheet()->setCellValue('F45', $total_number_male ); 
        $objPHPExcel->getActiveSheet()->setCellValue('F73', $total_number_female ); 
        $objPHPExcel->getActiveSheet()->setCellValue('F74', $total_number_male + $total_number_female ); 
        // $objPHPExcel->getActiveSheet()->fromArray($students); 
        $filename = "School Form 5 ".$class_name." ".$section_name.".xls";
        $objPHPExcel->getActiveSheet()->setTitle( $class_name );
        $objPHPExcel->setActiveSheetIndex(0);
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment;filename=\"".$filename."\";");
        header("Cache-Control: max-age=0");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');


	}
	

	public function generate_school_budget( $session_id, $group_id,  $registrar_fullname, $principal_fullname ){ 



        // $objPHPExcel = new PHPExcel();







     ini_set('memory_limit', '-1');



		$CI =& get_instance(); 



		$CI->load->model('Class_model'); 



		$CI->load->model('Setting_model'); 



        $CI->load->model('session_model');



        $CI->load->model('budgetgroup_model');



		$CI->load->model('budgetrevenue_model');



        $CI->load->model('budgetesc_model');



        $CI->load->model('expensebudget_model');



 		$CI->load->model('budgettrustfund_model');



        $CI->load->model('budgettrustfundgroup_model');



        $CI->load->model('budgettrustfundgroupdetails_model');



        $CI->load->model('budgettrustfundtype_model');  



        $CI->load->model('budgettrustfundgroupdetailexpense_model');



        $CI->load->model('budgetauxillaryincometype_model'); 



        $CI->load->model('budgetauxillaryincomegroup_model');  



        $CI->load->model('budgetauxillaryincomegroupdetails_model');



        $CI->load->model('budgetauxillaryincomegroupdetailexpense_model');



        $CI->load->model('budgetoperationcoststype_model'); 



        $CI->load->model('budgetoperationcostsgroup_model');  



        $CI->load->model('budgetoperationcostsgroupdetails_model');



        $CI->load->model('budgetoperationcostsgroupdetailexpense_model');



        $CI->load->model('budgeteducationalsharingtype_model'); 



        $CI->load->model('budgeteducationalsharinggroup_model');  



        $CI->load->model('budgeteducationalsharinggroupdetails_model');



        $CI->load->model('budgeteducationalsharinggroupdetailexpense_model');







 		$session_result = $CI->session_model->get( $session_id );



 		$budgetgroup_result = $CI->budgetgroup_model->get( $group_id );



 		$selected_class = $budgetgroup_result['selected_class'];



        if( $selected_class ){



            $selected_class = unserialize( $selected_class );   



        } 



 	



	 



		$get_school_name = $CI->Setting_model->getCurrentSchoolName();



		$getCurrentSessionName = $CI->Setting_model->getCurrentSessionName();



		$getCurrentSchoolId = $CI->Setting_model->getCurrentSchoolId();



		 



		/*Excel Style*/



	 	$center_style = array(



	        'alignment' => array(



	            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,



	        )



	    );



		 



		$opendoc = "././uploads/SchoolBudgetTemplate.xlsx";



		$objReader   = new PHPExcel_Reader_Excel2007();







		$objReader->setIncludeCharts(TRUE);







		$objPHPExcel = $objReader->load($opendoc); 







		$esc_group_data = FALSE;



		$current_data_row_number =  11;



		if( is_array($selected_class )){



			$revenue_grade_letter = 'B';



			$revenue_student_letter = 'C';



			$revenue_amount_letter = 'D';



			$revenue_total_letter = 'E';



			$revenue_number = $current_data_row_number;



			$revenue_budgetgroup_student = 0;



			foreach ($selected_class as $selected_class_key => $selected_class_value) { 



				$budgetgroup_result = $CI->budgetrevenue_model->getbysessionclass($session_id, $selected_class_key );



				$budgetescgroup_result = $CI->budgetesc_model->getbysessionclass($session_id, $selected_class_key );



				if( $budgetescgroup_result ){



					$esc_group_data = TRUE;



				}



				$budgetgroup_student = $budgetgroup_result['student_number'];



				$revenue_budgetgroup_student += $budgetgroup_result['student_number'];



				$budgetgroup_amount = $budgetgroup_result['amount'];



				$budgetgroup_total = $budgetgroup_amount * $budgetgroup_student;



				



				$objPHPExcel->getActiveSheet()->setCellValue($revenue_grade_letter.$revenue_number, $selected_class_value  );  



				$objPHPExcel->getActiveSheet()->setCellValue($revenue_student_letter.$revenue_number, $budgetgroup_student  );  



				$objPHPExcel->getActiveSheet()->setCellValue($revenue_amount_letter.$revenue_number, $budgetgroup_amount  );  



				$objPHPExcel->getActiveSheet()->getStyle($revenue_amount_letter.$revenue_number)->getNumberFormat()->setFormatCode('###,###,##0.00'); 



				$objPHPExcel->getActiveSheet()->setCellValue($revenue_total_letter.$revenue_number, $budgetgroup_total );   



				$objPHPExcel->getActiveSheet()->getStyle($revenue_total_letter.$revenue_number)->getNumberFormat()->setFormatCode('###,###,##0.00');    



				$revenue_number++;



			}



			$objPHPExcel->getActiveSheet()->setCellValue($revenue_student_letter.$revenue_number, $revenue_budgetgroup_student  );  



			$revenue_number++;



			$objPHPExcel->getActiveSheet()->setCellValue($revenue_amount_letter.$revenue_number, 'Less 10% from regular students'  );  



			$revenue_number++;



			$current_data_row_number = $revenue_number;



		}







	 	







	 	if( $esc_group_data == TRUE){



	 		$esc_title_letter = 'A';



	 	 	$esc_title_number = $current_data_row_number + 1 ;



	 		$esc_group_title_number = $esc_title_number + 1;



	 		$esc_number = $esc_group_title_number + 1; 



	 



		  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$esc_title_number.':C'.$esc_title_number); 



		  	$objPHPExcel->getActiveSheet()->getStyle('A'.$esc_title_number.':C'.$esc_title_number)->applyFromArray($center_style);



		  	$objPHPExcel->getActiveSheet()->getStyle('B'.$esc_group_title_number.':E'.$esc_group_title_number)->applyFromArray($center_style);



			$objPHPExcel->getActiveSheet()->setCellValue($esc_title_letter.$esc_title_number,  'ESC STUDENTS' ); 



			$objPHPExcel->getActiveSheet()->setCellValue('B'.$esc_group_title_number, 'Grade'  );   



			$objPHPExcel->getActiveSheet()->setCellValue('C'.$esc_group_title_number, 'Students'  );   



			$objPHPExcel->getActiveSheet()->setCellValue('D'.$esc_group_title_number, 'Amount'  );   



			$objPHPExcel->getActiveSheet()->setCellValue('E'.$esc_group_title_number, 'Total'  );  







			$esc_grade_letter = 'B';



			$esc_student_letter = 'C';



			$esc_amount_letter = 'D';



			$esc_total_letter = 'E'; 



			$esc_budgetgroup_student = 0;







			foreach ($selected_class as $selected_class_key => $selected_class_value) {  



				$budgetescgroup_result = $CI->budgetesc_model->getbysessionclass($session_id, $selected_class_key );



				 



				$budgetgroup_student = $budgetescgroup_result['student_number'];



				$esc_budgetgroup_student += $budgetescgroup_result['student_number'];



				$budgetgroup_amount = $budgetescgroup_result['amount'];



				$budgetgroup_total = $budgetgroup_amount * $budgetgroup_student;



				



				$objPHPExcel->getActiveSheet()->setCellValue($esc_grade_letter.$esc_number, $selected_class_value  );  



				$objPHPExcel->getActiveSheet()->setCellValue($esc_student_letter.$esc_number, $budgetgroup_student  );  



				$objPHPExcel->getActiveSheet()->setCellValue($esc_amount_letter.$esc_number, $budgetgroup_amount  );  



				$objPHPExcel->getActiveSheet()->getStyle($esc_amount_letter.$esc_number)->getNumberFormat()->setFormatCode('###,###,##0.00');  



				$objPHPExcel->getActiveSheet()->setCellValue($esc_total_letter.$esc_number, $budgetgroup_total );  



				$objPHPExcel->getActiveSheet()->getStyle($esc_total_letter.$esc_number)->getNumberFormat()->setFormatCode('###,###,##0.00');   



				$esc_number++;



			}  



			$objPHPExcel->getActiveSheet()->setCellValue($esc_student_letter.$esc_number, $esc_budgetgroup_student  );  



			$esc_number++;



			$current_data_row_number = $esc_number;



	 	}



 	 	 







 	 	$educational_reveneu_title_number = $current_data_row_number + 3 ;



 		$educational_reveneu_title_letter = 'A'; 



 	 	$objPHPExcel->getActiveSheet()->mergeCells('A'.$educational_reveneu_title_number.':C'.$educational_reveneu_title_number); 



	  	// $objPHPExcel->getActiveSheet()->getStyle('A'.$educational_reveneu_title_number.':C'.$educational_reveneu_title_number)->applyFromArray($center_style); 



		$objPHPExcel->getActiveSheet()->setCellValue($educational_reveneu_title_letter.$educational_reveneu_title_number,  'Total Educational Revenues:' ); 







  		$current_data_row_number = $educational_reveneu_title_number; 



  		$budgettrustfundgroup_result = $CI->budgettrustfundgroup_model->getbysessionclass($session_id, $group_id); 



	 	if( is_array( $budgettrustfundgroup_result )){  



	 		$trustfund_title_letter = 'A';



	 	 	$trustfund_title_number = $current_data_row_number + 1 ;



	 		$trustfund_group_title_number = $trustfund_title_number + 1;



	 		$trustfund_number = $trustfund_group_title_number + 1; 



	 



		  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$trustfund_title_number.':C'.$trustfund_title_number); 



		  	// $objPHPExcel->getActiveSheet()->getStyle('A'.$trustfund_title_number.':C'.$trustfund_title_number)->applyFromArray($center_style);



		  	$objPHPExcel->getActiveSheet()->getStyle('B'.$trustfund_group_title_number.':E'.$trustfund_group_title_number)->applyFromArray($center_style);



			$objPHPExcel->getActiveSheet()->setCellValue($trustfund_title_letter.$trustfund_title_number,  'TRUST FUNDS' ); 







		  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$trustfund_group_title_number.':D'.$trustfund_group_title_number);  



			$objPHPExcel->getActiveSheet()->setCellValue('A'.$trustfund_group_title_number, 'Type'  );    



			$objPHPExcel->getActiveSheet()->setCellValue('E'.$trustfund_group_title_number, 'Amount'  );   



			$objPHPExcel->getActiveSheet()->setCellValue('F'.$trustfund_group_title_number, 'Students'  );  



			$objPHPExcel->getActiveSheet()->setCellValue('G'.$trustfund_group_title_number, 'Income'  );  



			$objPHPExcel->getActiveSheet()->setCellValue('H'.$trustfund_group_title_number, 'Expense'  );  



			$objPHPExcel->getActiveSheet()->setCellValue('I'.$trustfund_group_title_number, 'Actual Income'  );   



		  	$objPHPExcel->getActiveSheet()->getStyle('A'.$trustfund_group_title_number.':I'.$trustfund_group_title_number)->applyFromArray($center_style);







			$trustfund_type_letter = 'A';



			$trustfund_amount_letter = 'E';



			$trustfund_student_letter = 'F';



			$trustfund_income_letter = 'G'; 



			$trustfund_expense_letter = 'H'; 



			$trustfund_actual_income_letter = 'I';







			$trustfund_total_amount = 0;    



			$trustfund_total_income_amount = 0;    



			$trustfund_total_expense_amount = 0;    



			$trustfund_total_actual_income = 0;  







			$budgettrustfundgroupdetails_result = $CI->budgettrustfundgroupdetails_model->getbygroupid( $budgettrustfundgroup_result['id'], 'ASC' );







		 	foreach ($budgettrustfundgroupdetails_result as $budgettrustfundgroupdetails_result_key => $budgettrustfundgroupdetails_result_value) {  



				// $budgettrustfundgroup_result = $CI->budgettrustfund_model->getbysessionclass($session_id, $selected_class_key );



			 	$total_expense_amount = $CI->budgettrustfundgroupdetailexpense_model->getdetailstotalamount($budgettrustfundgroupdetails_result_value['id']); 



			 	$total_expense_amount = $total_expense_amount?$total_expense_amount:'0.00';



			 	$total_actual_income = 0;



			 	$total_actual_income = $total_actual_income?$total_actual_income:'0.00';



				$budgetgrouptrustfund_student = $budgettrustfundgroupdetails_result_value['student_number']; 



				$budgetgrouptrustfund_amount = $budgettrustfundgroupdetails_result_value['amount'];



				$budgetgrouptrustfund_name = $budgettrustfundgroupdetails_result_value['name'];



				$budgetgrouptrustfund_income = $budgetgrouptrustfund_amount * $budgetgrouptrustfund_student;







				$trustfund_total_amount += $budgetgrouptrustfund_amount;    



				$trustfund_total_income_amount += $budgetgrouptrustfund_income;    



				$trustfund_total_expense_amount += $total_expense_amount;    



				$trustfund_total_actual_income += $total_actual_income; 







				$objPHPExcel->getActiveSheet()->mergeCells('A'.$trustfund_number.':D'.$trustfund_number); 



				$objPHPExcel->getActiveSheet()->setCellValue($trustfund_type_letter.$trustfund_number, strtoupper( $budgetgrouptrustfund_name )  );  



				$objPHPExcel->getActiveSheet()->setCellValue($trustfund_amount_letter.$trustfund_number, $budgettrustfundgroupdetails_result_value['amount'] );  



				$objPHPExcel->getActiveSheet()->getStyle($trustfund_amount_letter.$trustfund_number)->getNumberFormat()->setFormatCode('###,###,##0.00');  



				$objPHPExcel->getActiveSheet()->setCellValue($trustfund_student_letter.$trustfund_number, $budgetgrouptrustfund_student  );  



				$objPHPExcel->getActiveSheet()->setCellValue($trustfund_income_letter.$trustfund_number,  $budgetgrouptrustfund_income  );  



				$objPHPExcel->getActiveSheet()->getStyle($trustfund_income_letter.$trustfund_number)->getNumberFormat()->setFormatCode('###,###,##0.00');      



				$objPHPExcel->getActiveSheet()->setCellValue($trustfund_expense_letter.$trustfund_number, $total_expense_amount );  



				$objPHPExcel->getActiveSheet()->getStyle($trustfund_expense_letter.$trustfund_number)->getNumberFormat()->setFormatCode('###,###,##0.00');  



				$objPHPExcel->getActiveSheet()->setCellValue($trustfund_actual_income_letter.$trustfund_number, $total_actual_income  );  



				$objPHPExcel->getActiveSheet()->getStyle($trustfund_actual_income_letter.$trustfund_number)->getNumberFormat()->setFormatCode('###,###,##0.00');  



				$trustfund_number++;



			}   







			$objPHPExcel->getActiveSheet()->setCellValue($trustfund_amount_letter.$trustfund_number, $trustfund_total_amount );  



			$objPHPExcel->getActiveSheet()->getStyle($trustfund_amount_letter.$trustfund_number)->getNumberFormat()->setFormatCode('###,###,##0.00');    



			$objPHPExcel->getActiveSheet()->setCellValue($trustfund_income_letter.$trustfund_number,  $trustfund_total_income_amount  );  



			$objPHPExcel->getActiveSheet()->getStyle($trustfund_income_letter.$trustfund_number)->getNumberFormat()->setFormatCode('###,###,##0.00');      



			$objPHPExcel->getActiveSheet()->setCellValue($trustfund_expense_letter.$trustfund_number, $trustfund_total_expense_amount );  



			$objPHPExcel->getActiveSheet()->getStyle($trustfund_expense_letter.$trustfund_number)->getNumberFormat()->setFormatCode('###,###,##0.00');  



			$objPHPExcel->getActiveSheet()->setCellValue($trustfund_actual_income_letter.$trustfund_number, $trustfund_total_actual_income  );  



			$objPHPExcel->getActiveSheet()->getStyle($trustfund_actual_income_letter.$trustfund_number)->getNumberFormat()->setFormatCode('###,###,##0.00'); 



			$trustfund_number++;







			$budget_excess_trust_funds_number = $trustfund_number + 1;



			$objPHPExcel->getActiveSheet()->mergeCells('A'.$budget_excess_trust_funds_number.':D'.$budget_excess_trust_funds_number);  



			$objPHPExcel->getActiveSheet()->setCellValue('A'.$budget_excess_trust_funds_number,  'Budget Excess Trust Funds:' ); 



			$objPHPExcel->getActiveSheet()->setCellValue('I'.$budget_excess_trust_funds_number,  $trustfund_total_income_amount ); 



			$objPHPExcel->getActiveSheet()->getStyle('I'.$budget_excess_trust_funds_number)->getNumberFormat()->setFormatCode('###,###,##0.00'); 



			$trustfund_number++;



			$current_data_row_number = $trustfund_number;



		}



 	 



		







		$budgetauxillaryincomegroup_result = $CI->budgetauxillaryincomegroup_model->getbysessionclass($session_id, $group_id);



		if( is_array( $budgetauxillaryincomegroup_result )){  



			$auxillary_income_title_letter = 'A';



	 	 	$auxillary_income_title_number = $current_data_row_number + 3 ;



	 		$auxillary_income_group_title_number = $auxillary_income_title_number + 1;



	 		$auxillaryincome_number = $auxillary_income_group_title_number + 1; 



	 



		  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$auxillary_income_title_number.':C'.$auxillary_income_title_number); 



		  	// $objPHPExcel->getActiveSheet()->getStyle('A'.$auxillary_income_title_number.':C'.$auxillary_income_title_number)->applyFromArray($center_style);



		  	$objPHPExcel->getActiveSheet()->getStyle('B'.$auxillary_income_group_title_number.':E'.$auxillary_income_group_title_number)->applyFromArray($center_style);



			$objPHPExcel->getActiveSheet()->setCellValue($auxillary_income_title_letter.$auxillary_income_title_number,  'AUXILLARY INCOME' );  



		  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$auxillary_income_group_title_number.':D'.$auxillary_income_group_title_number);  



			$objPHPExcel->getActiveSheet()->setCellValue('A'.$auxillary_income_group_title_number, 'Type'  );    



			$objPHPExcel->getActiveSheet()->setCellValue('E'.$auxillary_income_group_title_number, 'Amount'  );    



			$objPHPExcel->getActiveSheet()->setCellValue('F'.$auxillary_income_group_title_number, 'Income'  );   



			$objPHPExcel->getActiveSheet()->setCellValue('G'.$auxillary_income_group_title_number, 'Actual Income'  );   



		  	$objPHPExcel->getActiveSheet()->getStyle('A'.$auxillary_income_group_title_number.':G'.$auxillary_income_group_title_number)->applyFromArray($center_style); 



		  	$auxillaryincome_type_letter = 'A';



			$auxillaryincome_amount_letter = 'E'; 



			$auxillaryincome_income_letter = 'F';  



			$auxillaryincome_actual_income_letter = 'G'; 



			$auxillaryincome_total_amount = 0;    



			$auxillaryincome_total_income_amount = 0;      



			$auxillaryincome_total_actual_income = 0;   



			$budgetauxillaryincomegroupdetails_result = $CI->budgetauxillaryincomegroupdetails_model->getbygroupidordername( $budgetauxillaryincomegroup_result['id'] ); 



		 	foreach ($budgetauxillaryincomegroupdetails_result as $budgetauxillaryincomegroupdetails_result_key => $budgetauxillaryincomegroupdetails_result_value) {  



			 	$total_actual_income = 0;



			 	$total_actual_income = $total_actual_income?$total_actual_income:'0.00';



				$budgetgroupauxillaryincome_multiplier = $budgetauxillaryincomegroupdetails_result_value['multiplier']; 



				$budgetgroupauxillaryincome_amount = $budgetauxillaryincomegroupdetails_result_value['amount'];



				$budgetgroupauxillaryincome_name = $budgetauxillaryincomegroupdetails_result_value['name'];



				$budgetgroupauxillaryincome_income = $budgetgroupauxillaryincome_amount * $budgetgroupauxillaryincome_multiplier; 



				$auxillaryincome_total_amount += $budgetgroupauxillaryincome_amount;    



				$auxillaryincome_total_income_amount += $budgetgroupauxillaryincome_income;  



				$auxillaryincome_total_actual_income += $total_actual_income;  



				$objPHPExcel->getActiveSheet()->mergeCells('A'.$auxillaryincome_number.':D'.$auxillaryincome_number); 



				$objPHPExcel->getActiveSheet()->setCellValue($auxillaryincome_type_letter.$auxillaryincome_number, strtoupper( $budgetgroupauxillaryincome_name )  );  



				$objPHPExcel->getActiveSheet()->setCellValue($auxillaryincome_amount_letter.$auxillaryincome_number, $budgetauxillaryincomegroupdetails_result_value['amount'] );  



				$objPHPExcel->getActiveSheet()->getStyle($auxillaryincome_amount_letter.$auxillaryincome_number)->getNumberFormat()->setFormatCode('###,###,##0.00');   



				$objPHPExcel->getActiveSheet()->setCellValue($auxillaryincome_income_letter.$auxillaryincome_number,  $budgetgroupauxillaryincome_income  );  



				$objPHPExcel->getActiveSheet()->getStyle($auxillaryincome_income_letter.$auxillaryincome_number)->getNumberFormat()->setFormatCode('###,###,##0.00');    



				$objPHPExcel->getActiveSheet()->setCellValue($auxillaryincome_actual_income_letter.$auxillaryincome_number, $total_actual_income  );  



				$objPHPExcel->getActiveSheet()->getStyle($auxillaryincome_actual_income_letter.$auxillaryincome_number)->getNumberFormat()->setFormatCode('###,###,##0.00');  



				$auxillaryincome_number++;



			}   



			$objPHPExcel->getActiveSheet()->setCellValue($auxillaryincome_income_letter.$auxillaryincome_number, $auxillaryincome_total_income_amount  );  



			$objPHPExcel->getActiveSheet()->getStyle($auxillaryincome_income_letter.$auxillaryincome_number)->getNumberFormat()->setFormatCode('###,###,##0.00');



			$auxillaryincome_number++;







	  		$current_data_row_number = $auxillaryincome_number;



	  	}







	  	$budgetoperationcostgroup_result = $CI->budgetoperationcostsgroup_model->getbysessionclass($session_id, $group_id); 



		if( is_array( $budgetoperationcostgroup_result )){   



		  	$budget_excess_amount = $budgetoperationcostgroup_result['budget_excess_amount'];



		  	$operation_costs_title_letter = 'A';



	 	 	$operation_costs_title_number = $current_data_row_number + 2 ; 



	 		$auxillary_budget_excess_title_number = $operation_costs_title_number + 1;



	 		$operation_costs_group_title_number = $auxillary_budget_excess_title_number + 1;



	 		$operation_costs_number = $operation_costs_group_title_number + 1; 



		  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$operation_costs_title_number.':C'.$operation_costs_title_number); 



		  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$auxillary_budget_excess_title_number.':C'.$auxillary_budget_excess_title_number);  



		  	$objPHPExcel->getActiveSheet()->getStyle('B'.$operation_costs_group_title_number.':E'.$operation_costs_group_title_number)->applyFromArray($center_style); 



			$objPHPExcel->getActiveSheet()->setCellValue($operation_costs_title_letter.$operation_costs_title_number,  'AUXILLARY BUDGET EXCESS' );  



			$objPHPExcel->getActiveSheet()->setCellValue($operation_costs_title_letter.$auxillary_budget_excess_title_number,  'OPERATION COST' );   



			$objPHPExcel->getActiveSheet()->mergeCells('A'.$operation_costs_group_title_number.':D'.$operation_costs_group_title_number);  



			$objPHPExcel->getActiveSheet()->setCellValue('A'.$operation_costs_group_title_number, 'Type'  );    



			$objPHPExcel->getActiveSheet()->setCellValue('E'.$operation_costs_group_title_number, 'Amount'  );     



			$objPHPExcel->getActiveSheet()->setCellValue('F'.$operation_costs_group_title_number, 'Actual Expenses'  );   



			$objPHPExcel->getActiveSheet()->getStyle('A'.$operation_costs_group_title_number.':F'.$operation_costs_group_title_number)->applyFromArray($center_style); 







			$operation_costs_type_letter = 'A';



			$operation_costs_amount_letter = 'E'; 



			$operation_costs_expense_letter = 'F';  







			$operation_costs_total_amount = 0;     



			$budgetoperation_costsgroupdetails_result = $CI->budgetoperationcostsgroupdetails_model-> getbygroupidwithorder($budgetoperationcostgroup_result['id'], 'ASC', 'no');



			foreach ($budgetoperation_costsgroupdetails_result as $budgetoperation_costsgroupdetails_result_key => $budgetoperation_costsgroupdetails_result_value) {  



			 	$total_actual_expense = 0;



			 	$total_actual_expense = $total_actual_expense?$total_actual_expense:'0.00'; 



				$budgetgroupoperation_costs_amount = $budgetoperation_costsgroupdetails_result_value['amount'];



				$operation_costs_total_amount += $budgetgroupoperation_costs_amount;



				$budgetgroupoperation_costs_name = $budgetoperation_costsgroupdetails_result_value['name']; 



				$objPHPExcel->getActiveSheet()->mergeCells('A'.$operation_costs_number.':D'.$operation_costs_number); 



				$objPHPExcel->getActiveSheet()->setCellValue($operation_costs_type_letter.$operation_costs_number, strtoupper( $budgetgroupoperation_costs_name )  );  



				$objPHPExcel->getActiveSheet()->setCellValue($operation_costs_amount_letter.$operation_costs_number, $budgetoperation_costsgroupdetails_result_value['amount'] );  



				$objPHPExcel->getActiveSheet()->getStyle($operation_costs_amount_letter.$operation_costs_number)->getNumberFormat()->setFormatCode('###,###,##0.00');      



				$objPHPExcel->getActiveSheet()->setCellValue($operation_costs_expense_letter.$operation_costs_number, $total_actual_expense  );  



				$objPHPExcel->getActiveSheet()->getStyle($operation_costs_expense_letter.$operation_costs_number)->getNumberFormat()->setFormatCode('###,###,##0.00');  



				$operation_costs_number++;



			}  



			$operation_costs_number++;



			$objPHPExcel->getActiveSheet()->setCellValue($operation_costs_title_letter.$operation_costs_number,  'CAPITAL OUTLAY' );   



			$operation_costs_number++;



			$budgetoperation_costsgroupdetails_result = $CI->budgetoperationcostsgroupdetails_model-> getbygroupidwithorder($budgetoperationcostgroup_result['id'], 'ASC', 'yes');



			foreach ($budgetoperation_costsgroupdetails_result as $budgetoperation_costsgroupdetails_result_key => $budgetoperation_costsgroupdetails_result_value) {  



			 	$total_actual_expense = 0;



			 	$total_actual_expense = $total_actual_expense?$total_actual_expense:'0.00'; 



				$budgetgroupoperation_costs_amount = $budgetoperation_costsgroupdetails_result_value['amount'];



				$operation_costs_total_amount += $budgetgroupoperation_costs_amount;



				$budgetgroupoperation_costs_name = $budgetoperation_costsgroupdetails_result_value['name']; 



				$objPHPExcel->getActiveSheet()->mergeCells('A'.$operation_costs_number.':D'.$operation_costs_number); 



				$objPHPExcel->getActiveSheet()->setCellValue($operation_costs_type_letter.$operation_costs_number, strtoupper( $budgetgroupoperation_costs_name )  );  



				$objPHPExcel->getActiveSheet()->setCellValue($operation_costs_amount_letter.$operation_costs_number, $budgetoperation_costsgroupdetails_result_value['amount'] );  



				$objPHPExcel->getActiveSheet()->getStyle($operation_costs_amount_letter.$operation_costs_number)->getNumberFormat()->setFormatCode('###,###,##0.00');      



				$objPHPExcel->getActiveSheet()->setCellValue($operation_costs_expense_letter.$operation_costs_number, $total_actual_expense  );  



				$objPHPExcel->getActiveSheet()->getStyle($operation_costs_expense_letter.$operation_costs_number)->getNumberFormat()->setFormatCode('###,###,##0.00');  



				$operation_costs_number++;



			}   



 			$operation_costs_number++; 



		  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$operation_costs_number.':C'.$operation_costs_number);



			$objPHPExcel->getActiveSheet()->setCellValue($operation_costs_title_letter.$operation_costs_number,  'TOTAL OPERATION' );    



			$objPHPExcel->getActiveSheet()->setCellValue($operation_costs_amount_letter.$operation_costs_number,  $operation_costs_total_amount); 



			$objPHPExcel->getActiveSheet()->getStyle($operation_costs_amount_letter.$operation_costs_number)->getNumberFormat()->setFormatCode('###,###,##0.00');         



 			$operation_costs_number++; 



		  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$operation_costs_number.':C'.$operation_costs_number);



			$objPHPExcel->getActiveSheet()->setCellValue($operation_costs_title_letter.$operation_costs_number,  'Budget Excess for Operations' );  



			$objPHPExcel->getActiveSheet()->setCellValue($operation_costs_amount_letter.$operation_costs_number,  $budget_excess_amount );  



			$objPHPExcel->getActiveSheet()->getStyle($operation_costs_amount_letter.$operation_costs_number)->getNumberFormat()->setFormatCode('###,###,##0.00');  



			$operation_costs_number++;  



			$current_data_row_number = $operation_costs_number;



		}











	  	$cash_ending_title_letter = 'A';



 	 	$cash_ending_title_number = $current_data_row_number + 2 ;  



	  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cash_ending_title_number.':C'.$cash_ending_title_number);    



		$objPHPExcel->getActiveSheet()->setCellValue($cash_ending_title_letter.$cash_ending_title_number,  'CASH ENDING of SY:'.$session_result['session'] ); 



		$objPHPExcel->getActiveSheet()->setCellValue('E'.$cash_ending_title_number,  'PROPOSED' ); 



		$objPHPExcel->getActiveSheet()->setCellValue('F'.$cash_ending_title_number,  'ACTUAL' ); 



 	 	$cash_ending_trust_fund_title_number = $cash_ending_title_number + 2 ;  



	  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cash_ending_trust_fund_title_number.':C'.$cash_ending_trust_fund_title_number);    



		$objPHPExcel->getActiveSheet()->setCellValue($cash_ending_title_letter.$cash_ending_trust_fund_title_number,  'TRUST FUNDS' );   



		$cash_ending_trust_fund_title_number++;   



	  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cash_ending_trust_fund_title_number.':C'.$cash_ending_trust_fund_title_number);    



		$objPHPExcel->getActiveSheet()->setCellValue($cash_ending_title_letter.$cash_ending_trust_fund_title_number,  'AUXILLARY INCOME' ); 



		$cash_ending_trust_fund_title_number++;   



	  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cash_ending_trust_fund_title_number.':C'.$cash_ending_trust_fund_title_number);    



		$objPHPExcel->getActiveSheet()->setCellValue($cash_ending_title_letter.$cash_ending_trust_fund_title_number,  'OPERATION BUDGET EXCESS' );  



		$cash_ending_trust_fund_title_number++;   



	  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cash_ending_trust_fund_title_number.':C'.$cash_ending_trust_fund_title_number);    



		$objPHPExcel->getActiveSheet()->setCellValue($cash_ending_title_letter.$cash_ending_trust_fund_title_number,  'COMPUTER BUDGET EXCESS' );  



		$cash_ending_trust_fund_title_number = $cash_ending_trust_fund_title_number + 3; 



	  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cash_ending_trust_fund_title_number.':C'.$cash_ending_trust_fund_title_number);    



		$objPHPExcel->getActiveSheet()->setCellValue($cash_ending_title_letter.$cash_ending_trust_fund_title_number,  'RETURN ON EQUITY' ); 



		$cash_ending_trust_fund_title_number++;



	  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cash_ending_trust_fund_title_number.':D'.$cash_ending_trust_fund_title_number);    



		$objPHPExcel->getActiveSheet()->setCellValue($cash_ending_title_letter.$cash_ending_trust_fund_title_number,  'CASH BEGINNING BALANCE '.$session_result['session'] ); 



		$cash_ending_trust_fund_title_number++;



	  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cash_ending_trust_fund_title_number.':D'.$cash_ending_trust_fund_title_number);    



		$objPHPExcel->getActiveSheet()->setCellValue($cash_ending_title_letter.$cash_ending_trust_fund_title_number,  'CASH ON HAND AT THE END OF SY'.$session_result['session'] ); 



		$cash_ending_trust_fund_title_number++;



	  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cash_ending_trust_fund_title_number.':D'.$cash_ending_trust_fund_title_number);    



		$objPHPExcel->getActiveSheet()->setCellValue($cash_ending_title_letter.$cash_ending_trust_fund_title_number,  'BUDGET PROPOSED BY:' ); 



		$cash_ending_trust_fund_title_number++;



	  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cash_ending_trust_fund_title_number.':D'.$cash_ending_trust_fund_title_number);  



	  	$objPHPExcel->getActiveSheet()->getStyle('A'.$cash_ending_trust_fund_title_number.':D'.$cash_ending_trust_fund_title_number)->applyFromArray($center_style);  



		$objPHPExcel->getActiveSheet()->setCellValue($cash_ending_title_letter.$cash_ending_trust_fund_title_number, $registrar_fullname );  



		$cash_ending_trust_fund_title_number = $cash_ending_trust_fund_title_number + 3 ;



	  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cash_ending_trust_fund_title_number.':D'.$cash_ending_trust_fund_title_number);    



		$objPHPExcel->getActiveSheet()->setCellValue($cash_ending_title_letter.$cash_ending_trust_fund_title_number,  'APPROVED BY:' ); 



		$cash_ending_trust_fund_title_number++;



	  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cash_ending_trust_fund_title_number.':D'.$cash_ending_trust_fund_title_number);  



	  	$objPHPExcel->getActiveSheet()->getStyle('A'.$cash_ending_trust_fund_title_number.':D'.$cash_ending_trust_fund_title_number)->applyFromArray($center_style);  



		$objPHPExcel->getActiveSheet()->setCellValue($cash_ending_title_letter.$cash_ending_trust_fund_title_number, '' );   



		$cash_ending_trust_fund_title_number = $cash_ending_trust_fund_title_number + 3 ;



	  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cash_ending_trust_fund_title_number.':D'.$cash_ending_trust_fund_title_number);   



		$objPHPExcel->getActiveSheet()->setCellValue($cash_ending_title_letter.$cash_ending_trust_fund_title_number, 'EDUCATIONAL SHARING' );  



		$objPHPExcel->getActiveSheet()->setCellValue('E'.$cash_ending_trust_fund_title_number, 'Actual Share' );  



		$cash_ending_trust_fund_title_number++;











	  	$budgeteducationalsharinggroup_result = $CI->budgeteducationalsharinggroup_model->getbysessionclass($session_id, $group_id); 



	  	if( is_array( $budgeteducationalsharinggroup_result )){ 



			$budgetoperation_costsgroupdetails_employee_result = $CI->budgeteducationalsharinggroupdetails_model->getbygroupidwithtype($budgeteducationalsharinggroup_result['id'], 'employee' );



			$budgetoperation_costsgroupdetails_operation_result = $CI->budgeteducationalsharinggroupdetails_model->getbygroupidwithtype($budgeteducationalsharinggroup_result['id'], 'operation' );



			$budgetoperation_costsgroupdetails_roe_result = $CI->budgeteducationalsharinggroupdetails_model->getbygroupidwithtype($budgeteducationalsharinggroup_result['id'], 'roe' );







		 







		  	$objPHPExcel->getActiveSheet()->mergeCells('B'.$cash_ending_trust_fund_title_number.':C'.$cash_ending_trust_fund_title_number);   



			$objPHPExcel->getActiveSheet()->setCellValue('B'.$cash_ending_trust_fund_title_number, 'Employees' );



			$objPHPExcel->getActiveSheet()->setCellValue('D'.$cash_ending_trust_fund_title_number, '=' );



			$objPHPExcel->getActiveSheet()->setCellValue('E'.$cash_ending_trust_fund_title_number, $budgetoperation_costsgroupdetails_employee_result['amount'] );   



			$objPHPExcel->getActiveSheet()->getStyle('E'.$cash_ending_trust_fund_title_number)->getNumberFormat()->setFormatCode('###,###,##0.00');  



			$cash_ending_trust_fund_title_number++;



		  	$objPHPExcel->getActiveSheet()->mergeCells('B'.$cash_ending_trust_fund_title_number.':C'.$cash_ending_trust_fund_title_number);  



			$objPHPExcel->getActiveSheet()->setCellValue('B'.$cash_ending_trust_fund_title_number, 'Operation' );



			$objPHPExcel->getActiveSheet()->setCellValue('D'.$cash_ending_trust_fund_title_number, '=' );



			$objPHPExcel->getActiveSheet()->setCellValue('E'.$cash_ending_trust_fund_title_number, $budgetoperation_costsgroupdetails_operation_result['amount'] );



			$objPHPExcel->getActiveSheet()->getStyle('E'.$cash_ending_trust_fund_title_number)->getNumberFormat()->setFormatCode('###,###,##0.00');  



			$cash_ending_trust_fund_title_number++;



		  	$objPHPExcel->getActiveSheet()->mergeCells('B'.$cash_ending_trust_fund_title_number.':C'.$cash_ending_trust_fund_title_number);  



			$objPHPExcel->getActiveSheet()->setCellValue('B'.$cash_ending_trust_fund_title_number, 'ROE' );



			$objPHPExcel->getActiveSheet()->setCellValue('D'.$cash_ending_trust_fund_title_number, '=' );



			$objPHPExcel->getActiveSheet()->setCellValue('E'.$cash_ending_trust_fund_title_number, $budgetoperation_costsgroupdetails_roe_result['amount'] );



			$objPHPExcel->getActiveSheet()->getStyle('E'.$cash_ending_trust_fund_title_number)->getNumberFormat()->setFormatCode('###,###,##0.00');  



			



	 	 	$cash_ending_trust_fund_title_number = $cash_ending_trust_fund_title_number + 2 ;  



		  	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cash_ending_trust_fund_title_number.':D'.$cash_ending_trust_fund_title_number);  



			$objPHPExcel->getActiveSheet()->setCellValue('A'.$cash_ending_trust_fund_title_number, 'Employee Share + Janitorial, Security, Medical, Guidance' ); 



			$objPHPExcel->getActiveSheet()->setCellValue('E'.$cash_ending_trust_fund_title_number, $budgeteducationalsharinggroup_result['employee_share'] );



			$objPHPExcel->getActiveSheet()->getStyle('E'.$cash_ending_trust_fund_title_number)->getNumberFormat()->setFormatCode('###,###,##0.00');  



		}







	  	











        $objRichText = new PHPExcel_RichText();



        $objRichText->createText("School Budget");



        $objRichText = new PHPExcel_RichText();



        $objRichText->createText("School Budget");



        // $students = $this->student->getstudents;



        $students = array();



        $getCurrentSchoolId = 'HUHU';



        $objPHPExcel->getActiveSheet()->setCellValue('D5', 'SCHOOL YEAR: '.$session_result['session']); 



        $objPHPExcel->getActiveSheet()->setCellValue('D2', strtoupper( $get_school_name )); 



        $filename = "School Budget ".$session_result['session'].".xls";



        $objPHPExcel->getActiveSheet()->setTitle( 'School Budget' );



        $objPHPExcel->setActiveSheetIndex(0);



        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");



        header("Content-Disposition: attachment;filename=\"".$filename."\";");



        header("Cache-Control: max-age=0");



        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



        $objWriter->save('php://output');











	}



    



    public function generate_dtr_summary( $date_dtr, $allusers, $start_date, $end_date ){







		ini_set('memory_limit', '-1');



		$CI =& get_instance(); 



        $CI->load->model('facultystaffdtrlog_model');







        $display_start_date = date("M d, Y", strtotime( $start_date ));



        $display_end_date = date("M d, Y", strtotime( $end_date ));











	 



		 



		$opendoc = "././uploads/DTRSummaryTemplate.xlsx";



		



		include ("application/third_party/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");



		 



		$file = 'DTRSummary.xls';



		$filePath = "././uploads/DTRSummaryTemplate.xlsx";  







		$objReader   = new PHPExcel_Reader_Excel2007();



		$objPHPExcel = $objReader->load($filePath);







		$objPHPExcel->getActiveSheet()->setCellValue('B3', $display_start_date );    



		$objPHPExcel->getActiveSheet()->setCellValue('B4', $display_end_date );    



		$start = 7;



		foreach ($date_dtr as $date_dtr_key => $date_dtr_value) {  



			foreach ($allusers as $allusers_key => $allusers_value) {



				$faculty_id =  $allusers_value['user_id'];



	            $faculty_role =  $allusers_value['role']; 



	            $faculty_name =  $allusers_value['firstname']; 



	            $faculty_middlename =  $allusers_value['middlename']; 



	            $faculty_lastname =  $allusers_value['lastname']; 



	            if( $faculty_middlename ){



	                if (strpos($faculty_middlename, ".") !== false) {



	                   $faculty_display =  $faculty_name.' '.$faculty_middlename.' '.$faculty_lastname; 



	                } else {



	                    $faculty_display =  $faculty_name.' '.$faculty_middlename.'. '.$faculty_lastname; 



	                } 



	            } else {



	                 $faculty_display =  $faculty_name.' '.$faculty_lastname; 



	            }



	            



	            $main_date_display = date("m/d/Y", strtotime( $date_dtr_value ));



                $faculty_login_morning_result = $CI->facultystaffdtrlog_model->getdtrdashboard($faculty_id, $faculty_role, $date_dtr_value, 'IN', '00:00:00', '12:59:00'  );



                 



                $faculty_logout_morning_result = $CI->facultystaffdtrlog_model->getdtrdashboard($faculty_id, $faculty_role, $date_dtr_value, 'OUT', '00:00:00', '12:59:00', 'DESC' );







                $faculty_login_afternoon_result = $CI->facultystaffdtrlog_model->getdtrdashboard($faculty_id, $faculty_role, $date_dtr_value, 'IN', '13:00:00', '24:59:00'  );



                $faculty_logout_afternoon_result = $CI->facultystaffdtrlog_model->getdtrdashboard($faculty_id, $faculty_role, $date_dtr_value, 'OUT', '13:00:00', '24:59:00', 'DESC'  );







                if( isset( $faculty_login_morning_result['time'] ) &&  $faculty_login_morning_result['time'] ){



                     $morning_main_time_in_display = date("h:i:s a", strtotime( $faculty_login_morning_result['time'] ));



                } else {



                    $morning_main_time_in_display = '';



                }











                if( isset( $faculty_logout_morning_result['time'] ) &&  $faculty_logout_morning_result['time'] ){



                     $morning_main_time_out_display = date("h:i:s a", strtotime( $faculty_logout_morning_result['time'] ));



                } else {



                    $morning_main_time_out_display = '';



                }







                $morning_datetime1 = date_create( $faculty_login_morning_result['time']);



                $morning_datetime2 = date_create( $faculty_logout_morning_result['time']);



                $morning_interval = date_diff($morning_datetime1, $morning_datetime2); 







                $morning_interval_hour = $morning_interval->h;



                $morning_interval_minutes = $morning_interval->i;



                $morning_interval_seconds = $morning_interval->s;



                // $morning_display_interval =  $morning_interval_hour.'h '. $morning_interval_minutes.'m '. $morning_interval_seconds.'s ';



                $morning_display_interval =  $morning_interval_hour.'.'. $morning_interval_minutes;







                if(  empty($faculty_login_morning_result['time']) ||  empty($faculty_logout_morning_result['time'])  ){



                    $morning_display_interval = '0';



                }







                if( isset( $faculty_login_afternoon_result['time'] ) &&  $faculty_login_afternoon_result['time'] ){



                     $afternoon_main_time_in_display = date("h:i:s a", strtotime( $faculty_login_afternoon_result['time'] ));



                } else {



                    $afternoon_main_time_in_display = '';



                }











                if( isset( $faculty_logout_afternoon_result['time'] ) &&  $faculty_logout_afternoon_result['time'] ){



                     $afternoon_main_time_out_display = date("h:i:s a", strtotime( $faculty_logout_afternoon_result['time'] ));



                } else {



                    $afternoon_main_time_out_display = '';



                }







                $afternoon_datetime1 = date_create( $faculty_login_afternoon_result['time']);



                $afternoon_datetime2 = date_create( $faculty_logout_afternoon_result['time']);



                $afternoon_interval = date_diff($afternoon_datetime1, $afternoon_datetime2); 







                $afternoon_interval_hour = $afternoon_interval->h;



                $afternoon_interval_minutes = $afternoon_interval->i;



                $afternoon_interval_seconds = $afternoon_interval->s;



                // $afternoon_display_interval =  $afternoon_interval_hour.'h '. $afternoon_interval_minutes.'m '. $afternoon_interval_seconds.'s ';



                $afternoon_display_interval =  $afternoon_interval_hour.'.'. $afternoon_interval_minutes;







                if(  empty($faculty_login_afternoon_result['time']) ||  empty($faculty_logout_afternoon_result['time'])  ){



                    $afternoon_display_interval = '0';



                }



                 $total_time = $morning_display_interval + $afternoon_display_interval;



                if( isset( $faculty_login_morning_result['time']) &&  $faculty_login_morning_result['time'] && isset( $faculty_logout_afternoon_result['time']) &&  $faculty_logout_afternoon_result['time'] && $total_time == 0 ){







                    $custom_datetime1 = date_create( $faculty_login_morning_result['time']);



                    $custom_datetime2 = date_create( $faculty_logout_afternoon_result['time']);



                    $custom_interval = date_diff($custom_datetime1, $custom_datetime2); 







                    $custom_interval_hour = $custom_interval->h;



                    $custom_interval_minutes = $custom_interval->i;



                    $custom_interval_seconds = $custom_interval->s; 







                    $total_time = $custom_interval_hour.'.'.$custom_interval_minutes;



                    



                } 







               



                $total_time_spend +=  $total_time;















				$objPHPExcel->getActiveSheet()->setCellValue('A'.$start, $faculty_display );



				$objPHPExcel->getActiveSheet()->setCellValue('B'.$start, strtoupper( $faculty_role  )); 



				$objPHPExcel->getActiveSheet()->setCellValue('C'.$start, $main_date_display );



				$objPHPExcel->getActiveSheet()->setCellValue('D'.$start, $morning_main_time_in_display );



				$objPHPExcel->getActiveSheet()->setCellValue('E'.$start, $morning_main_time_out_display );



				$objPHPExcel->getActiveSheet()->setCellValue('F'.$start, $afternoon_main_time_in_display );



				$objPHPExcel->getActiveSheet()->setCellValue('G'.$start, $afternoon_main_time_out_display );



				$objPHPExcel->getActiveSheet()->setCellValue('H'.$start, $total_time );



				$start++;











			}



		}



		 



		$objPHPExcel->setActiveSheetIndex(0);  



		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');



		header('Content-type: application/vnd.ms-excel');



		header('Content-Disposition: attachment; filename='.$file);



		$objWriter->save('php://output');



	}







	public function generatepaymentchartofaccountsbydate( $resultList, $date_selected ){ 



		ini_set('memory_limit', '-1');



		$CI =& get_instance(); 



        $CI->load->model('payment_model');



        $CI->load->model('studentchartofaccounts_model');







		 



		$opendoc = "././uploads/PaymentReportTemplate.xlsx";



		



		include ("application/third_party/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");



		 



		// $file = 'Chart of Accout.xls';



		$file = date("j F Ymd ", strtotime($date_selected)).'Cash.xls';



		$filePath = "././uploads/ChartofAccoutTemplate.xlsx";  







		$objReader   = new PHPExcel_Reader_Excel2007();



		$objPHPExcel = $objReader->load($filePath); 



		$displaydate = date('Ymd', strtotime($date_selected)); 



		$displaydate = date('Ymd', strtotime($date_selected)); 







		$objPHPExcel->getActiveSheet()->setCellValue('D1', $displaydate );



  



		$start = 2; 



		foreach ($resultList as $resultList_key => $resultList_value) {   



				$student_id = $resultList_value['student_id'];



                $name = $resultList_value['name'];



                $note = $resultList_value['note'];



                $total_amount = $resultList_value['total_amount'];



                if( $student_id > 1){

                	$charofaccounts = $CI->studentchartofaccounts_model->getbystudent($student_id);
                } else {
                    $charofaccounts = FALSE;
                }


                if( $charofaccounts ){



                    $displaycharofaccounts = $charofaccounts['account_number'];



                } else {







                    $charofaccountssearch = $CI->studentchartofaccounts_model->getbysearch($name);



                    if( $charofaccountssearch ){



                        $displaycharofaccounts = $charofaccountssearch['account_number'];



                    } else {



                        $displaycharofaccounts = $name;



                        $objPHPExcel->getActiveSheet()->getStyle('A'.$start)->applyFromArray(



						    array(



						        'fill' => array(



						            'type' => PHPExcel_Style_Fill::FILL_SOLID,



						            'color' => array('rgb' => 'FF0000')



						        )



						    )



						);



                    } 



                    



                }



                                            



				$objPHPExcel->getActiveSheet()->setCellValue('A'.$start, $name );



				$objPHPExcel->getActiveSheet()->setCellValue('B'.$start, $displaycharofaccounts );



				$objPHPExcel->getActiveSheet()->setCellValue('C'.$start, $note );



				$objPHPExcel->getActiveSheet()->setCellValue('D'.$start, 'c');  



				$objPHPExcel->getActiveSheet()->setCellValue('E'.$start, $total_amount );     







 







				



				$start++;







 



		}



		 



		$objPHPExcel->setActiveSheetIndex(0);   



		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');



		header('Content-type: application/vnd.ms-excel');



		header('Content-Disposition: attachment; filename='.$file);



		$objWriter->save('php://output');



	}



	public function generate_tapping_report( $class_id, $section_id, $tap_type, $date_from, $date_to ){
	 	ini_set('memory_limit', '-1');
		$CI =& get_instance();
		$CI->load->model('Student_model');
		$CI->load->model('Class_model');
		$CI->load->model('Section_model');
		$CI->load->model('Subject_model');
		$CI->load->model('Setting_model');
		$CI->load->model('Template_model');
 
        $date_from = date("Y-m-d", strtotime( $date_from )); 
        $date_to = date("Y-m-d", strtotime( $date_to ));   
        $tapping_tbody_data = array();

        $results = $this->callAPI('GET', 'http://157.230.43.105/smsgateway/report/gettodays/SJA/'.$date_from.'/'.$date_to, false); 
        $tapping_data = json_decode($results, true);

        if( is_array( $tapping_data ) && $tapping_data  ){
            foreach ($tapping_data as $tapping_data_key => $tapping_data_value){
                $student_id = $tapping_data_value['student_id'];
                $date_created = $tapping_data_value['date_created'];
                $tapped_type = $tapping_data_value['tapped_type'];
                $student_info = $CI->student_model->get($student_id); 
                $date_to_display = date("F j, Y, g:i a", strtotime( $date_created ));
 

                if( $student_info['class_id'] == $class_id && $student_info['section_id'] == $section_id  && $tapped_type === $tap_type ){ 
                    $tapping_tbody_data[] = array(
                      'date_created' => $date_to_display, 
                      'class' =>  $student_info['class'], 
                      'section' =>  $student_info['section'], 
                      'lastname' =>  $student_info['lastname'], 
                      'firstname' =>  $student_info['firstname'], 
                      'middlename' =>  $student_info['middlename'],  
                      'tapped_type' =>  $tapped_type, 
                    ); 
                } elseif( $student_info['class_id'] == $class_id && $student_info['section_id'] == $section_id  && empty( $tap_type ) ){
                     $tapping_tbody_data[] = array(
                      'date_created' => $date_to_display, 
                      'class' =>  $student_info['class'], 
                      'section' =>  $student_info['section'], 
                      'lastname' =>  $student_info['lastname'], 
                      'firstname' =>  $student_info['firstname'], 
                      'middlename' =>  $student_info['middlename'],  
                      'tapped_type' =>  $tapped_type, 
                    ); 
                } elseif( empty( $class_id ) && empty( $section_id )  && empty( $tap_type ) ){
                     $tapping_tbody_data[] = array(
                      'date_created' => $date_to_display, 
                      'class' =>  $student_info['class'], 
                      'section' =>  $student_info['section'], 
                      'lastname' =>  $student_info['lastname'], 
                      'firstname' =>  $student_info['firstname'], 
                      'middlename' =>  $student_info['middlename'],  
                      'tapped_type' =>  $tapped_type, 
                    ); 
                }  elseif(  $class_id  && empty( $section_id )  && empty( $tap_type ) ){
                   if( $student_info['class_id'] == $class_id ){ 
                     $tapping_tbody_data[] = array(
                      'date_created' => $date_to_display, 
                      'class' =>  $student_info['class'], 
                      'section' =>  $student_info['section'], 
                      'lastname' =>  $student_info['lastname'], 
                      'firstname' =>  $student_info['firstname'], 
                      'middlename' =>  $student_info['middlename'],  
                      'tapped_type' =>  $tapped_type, 
                    ); 
                   }
                }  
            }
        }
 

		 
		$opendoc = "././uploads/TappingReportTemplate.xlsx";
		
		include ("application/third_party/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
		 
		$file = 'Tapping Report '.date("m-d-Y").'.xls';
		$filePath = "././uploads/TappingReportTemplate.xlsx";  

	 	$objReader   = new PHPExcel_Reader_Excel2007();
		$objPHPExcel = $objReader->load($filePath);
    	
    	$styleArray = array(
	      	'borders' => array(
	          	'allborders' => array(
	              	'style' => PHPExcel_Style_Border::BORDER_THIN
	          	)
	      	)
	  	);
		$start = 2;
		$count = 1;
		$objPHPExcel->getActiveSheet()->getStyle('A1:H1'.$start)->applyFromArray($styleArray);
		foreach ($tapping_tbody_data as $tapping_tbody_data_key => $tapping_tbody_data_value) {   

			$date_created = $tapping_tbody_data_value['date_created'];  
            $tapped_type = $tapping_tbody_data_value['tapped_type'];
           	$lastname = $tapping_tbody_data_value['lastname'];
            $firstname = $tapping_tbody_data_value['firstname'];
            $middlename = $tapping_tbody_data_value['middlename'];;
            $class_name = $tapping_tbody_data_value['class'];
            $section_name = $tapping_tbody_data_value['section'];


			$objPHPExcel->getActiveSheet()->setCellValue('A'.$start, $count );
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$start, $date_created ); 
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$start, strtoupper( $class_name  ));
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$start, strtoupper( $section_name  ));  
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$start, strtoupper( $lastname  ));  
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$start, strtoupper( $firstname  ));  
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$start, strtoupper( $middlename  ));  
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$start, strtoupper( $tapped_type  ));  
		 	$objPHPExcel->getActiveSheet()->getStyle('A'.$start.':H'.$start)->applyFromArray($styleArray);
			$start++;
			$count++;

 
		}
		 
		$objPHPExcel->setActiveSheetIndex(0);  
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename='.$file);
		$objWriter->save('php://output');

	}	

	function callAPI($method, $url, $data){   
        $curl = curl_init();

        switch ($method){
          case "POST":
             curl_setopt($curl, CURLOPT_POST, 1);
             if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
             break;
          case "PUT":
             curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
             if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);                              
             break;
          default:
             if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
       }

       // OPTIONS:
       curl_setopt($curl, CURLOPT_URL, $url);
       curl_setopt($curl, CURLOPT_HTTPHEADER, array(
          'APIKEY: 111111111111111111111',
          'Content-Type: application/json',
       ));
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

       // EXECUTE:
       $result = curl_exec($curl);
       if(!$result){die("Connection Failure");}
       curl_close($curl);
       return $result;
    }



}







