<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Grade_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function get($id = null) {
        $this->db->select()->from('grades');
        if ($id != null) {
            $this->db->where('grades.id', $id);
        } else {
            $this->db->order_by('grades.category');
            $this->db->order_by('grades.grade_order');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array(); 
        }
    }
	
	public function getSortbyNoSubjectBelow($id = null, $sort_by='desc') {
        $this->db->select()->from('grades');
        if ($id != null) {
            $this->db->where('grades.id', $id);
        } else {
            $this->db->order_by('grades.id');
        }
		$this->db->order_by("no_subject_below",$sort_by);
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array(); 
        }
    }
	
	public function getOrderBySubject($id = null) {
        $this->db->select()->from('grades');
        if ($id != null) {
            $this->db->where('grades.id', $id);
        } else {
            $this->db->order_by('grades.no_subject_below');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array(); 
        }
    }

    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('grades');
    }

    /**
     * This function will take the post data passed from the controller
     * If id is present, then it will do an update
     * else an insert. One function doing both add and edit.
     * @param $data
     */
    public function add($data) {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('grades', $data); 
        } else {
            $this->db->insert('grades', $data); 
            return $this->db->insert_id();
        }
    }

    public function check_Exits_group($data) {
        $this->db->select('*');
        $this->db->from('feemasters');
        $this->db->where('session_id', $this->current_session);
        $this->db->where('feetype_id', $data['feetype_id']);
        $this->db->where('class_id', $data['class_id']);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return false;
        } else {
            return true;
        }
    }
	
	public function get_Category($category) {
        $this->db->select()->from('grades');
		$this->db->where('grades.category', $category);
		$this->db->order_by('grades.grade_order');
        $query = $this->db->get();
       
        return $query->result_array(); 
    }
	
	public function get_master_sheet( $list_of_subjects, $student_id ){
		$parent_subjects = array();
		$subjects_id_array = array();
		$get_mastersheet = array();
		foreach ($list_of_subjects as $key => $value){
			$subject_id = $value['id'];
			$subject_name = isset($value['name'])?$value['name']:'';
			$kind = !empty($value['kind'])?$value['kind']:'parent';
			/* $get_subject = $this->subject_model->get( $subject_id );
			$subject_name = isset($get_subject['name'])?$get_subject['name']:'';
			$kind = !empty($get_subject['kind'])?$get_subject['kind']:'parent'; */
			if( $kind == 'parent'){
				for( $quarter=1;$quarter<=4;$quarter++ ) {
					$examresult = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, $quarter, $subject_id   );
					$grades = isset($examresult->grades)?$examresult->grades:null;
					$get_mastersheet[$subject_name][$quarter]= $grades;
				}
			} else {
				$parent_id = $value['parent_id'];
				$subjects_id_array[] = $subject_id;
				if(!in_array( $parent_id, $parent_subjects )){
					$parent_subjects[] = $parent_id;
				}
				
			}
		} 
		
		if( count($parent_subjects) > 0 ){
			for( $x=0;$x<count($parent_subjects);$x++){
				$quarter_grades_array = array();
				$get_parent_id = $parent_subjects[$x];
				$get_child_subject = $this->subject_model->getChildSubject( $get_parent_id );
				$y=0;
				foreach( $get_child_subject as $key => $value ){
					$get_subject_id =  $value['id'];
					if(in_array( $get_subject_id,$subjects_id_array )){
						$y++;
						for( $quarter=1;$quarter<=4;$quarter++ ) {
							$examresult = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, $quarter, $get_subject_id   );
							$grades = isset($examresult->grades)?$examresult->grades:null;
							$quarter_grades_array[$y][$quarter] = $grades;
						}
					}
				}
				$get_subject_details = $this->subject_model->get( $get_parent_id );
				$get_subject_name = $get_subject_details['name'];
				if( count($quarter_grades_array) > 0 ){
					for( $quarter=1;$quarter<=4;$quarter++ ) {
						$get_total_child = null;
						$add_grade = 0;
						$get_total_grade = 0;
						for( $xyz=1;$xyz <= count($quarter_grades_array); $xyz++ ){
							$get_grade = $quarter_grades_array[$xyz][$quarter];
							if( $get_grade != null ){
								$get_total_child++;
								$add_grade = $add_grade + $get_grade;
							}
						}
						if( $get_total_child  ){
							$total_grade =  $add_grade / $get_total_child;
						} else  {
							$total_grade = null;
						}
						$get_mastersheet[$get_subject_name][$quarter]= $total_grade;
					}
				}
				
			}
		}
		
		return $get_mastersheet;
	}
	
	public function get_master_sheet_shs( $list_of_subjects, $student_id ){
		$parent_subjects = array();
		$subjects_id_array = array();
		$get_mastersheet = array();
	
		foreach ($list_of_subjects as $key => $value){
			$subject_id = $value['subject_id'];
			$get_subject = $this->subject_model->get( $subject_id );
			$subject_name = isset($get_subject['name'])?$get_subject['name']:'';
			$kind = !empty($get_subject['kind'])?$get_subject['kind']:'parent';
			if( $kind == 'parent'){
				for( $quarter=1;$quarter<=4;$quarter++ ) {
					$examresult = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, $quarter, $subject_id   );
					$grades = isset($examresult->grades)?$examresult->grades:null;
					$get_mastersheet[$subject_name][$quarter]= $grades;
				}
			} else {
				$parent_id = $get_subject['parent_id'];
				$subjects_id_array[] = $subject_id;
				if(!in_array( $parent_id, $parent_subjects )){
					$parent_subjects[] = $parent_id;
				}
				
			}
		} 
		
		if( count($parent_subjects) > 0 ){
			for( $x=0;$x<count($parent_subjects);$x++){
				$quarter_grades_array = array();
				$get_parent_id = $parent_subjects[$x];
				$get_child_subject = $this->subject_model->getChildSubject( $get_parent_id );
				$y=0;
				foreach( $get_child_subject as $key => $value ){
					$get_subject_id =  $value['id'];
					if(in_array( $get_subject_id,$subjects_id_array )){
						$y++;
						for( $quarter=1;$quarter<=4;$quarter++ ) {
							$examresult = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, $quarter, $get_subject_id   );
							$grades = isset($examresult->grades)?$examresult->grades:null;
							$quarter_grades_array[$y][$quarter] = $grades;
						}
					}
				}
				$get_subject_details = $this->subject_model->get( $get_parent_id );
				$get_subject_name = $get_subject_details['name'];
				if( count($quarter_grades_array) > 0 ){
					for( $quarter=1;$quarter<=4;$quarter++ ) {
						$get_total_child = null;
						$add_grade = 0;
						$get_total_grade = 0;
						for( $xyz=1;$xyz <= count($quarter_grades_array); $xyz++ ){
							$get_grade = $quarter_grades_array[$xyz][$quarter];
							if( $get_grade != null ){
								$get_total_child++;
								$add_grade = $add_grade + $get_grade;
							}
						}
						if( $get_total_child  ){
							$total_grade =  $add_grade / $get_total_child;
						} else  {
							$total_grade = null;
						}
						$get_mastersheet[$get_subject_name][$quarter]= $total_grade;
					}
				}
				
			}
		}
		
		return $get_mastersheet;
	}
	
	public function getgraderanking2( $studentlist ){
		$this->load->model('gradingsetting_model');
		$this->load->model('conductimportbatchdetail_model');
		$gradingsettings = $this->gradingsetting_model->get();
		$based_criteria = isset($gradingsettings->based_criteria)?$gradingsettings->based_criteria:'genaverage';
		$decimal_grades = isset($gradingsettings->decimal_grades )?$gradingsettings->decimal_grades:'2';
		$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade )?$gradingsettings->decimal_finalgrade:'2';
		$honor_based = isset($gradingsettings->honor_based )?$gradingsettings->honor_based:'average';
		
	/* 		if( $result ){
				for($y=0;$y<count($studentlist);$y++){
					$student_id = $studentlist[$y]['student_id'];
					$quarter = $studentlist[$y]['quarter'];
					$grade = $studentlist[$y]['final_grade'];
					$grade = round(  $studentlist[$y]['final_grade'], 2);
					$remove = false;
					$data = array();
					if( count( $result ) > 0 ){
						if( $based_criteria == 'subject'){
							$result = $this->getOrderBySubject(); 
							for( $x=0;$x<count($result);$x++){
								if($result[$x]){
									$from = (double)$result[$x]['mark_from'];		
									$to = (double)$result[$x]['mark_upto'];	
									$no_subject_below = $result[$x]['no_subject_below'];	
									$no_conduct_equal = $result[$x]['no_conduct_equal'];	
									$number_conduct = $result[$x]['number_conduct'];
									if( $no_subject_below ){
										$has_subject_below = $this->examresult_model->get_examresult_below( $student_id, $quarter, $no_subject_below );
										if( $has_subject_below ){
											$remove = true;
										}
									} 
									if( $no_conduct_equal && $number_conduct ){
										$check_conduct_ranking = $this->conductimportbatchdetail_model->check_conduct_ranking( $student_id, $quarter, $no_conduct_equal, $number_conduct );
										if( $check_conduct_ranking ){
											$remove = true;
										}
									} 
								
									if( $remove == false ){
										$studentlist[$y]['background_color'] = $result[$x]['background_color'];		
										$studentlist[$y]['text_color'] = $result[$x]['text_color'];	
										$studentlist[$y]['name'] = $result[$x]['name'];	
									} 
								}
							}
						} elseif( $based_criteria == 'both'){
							$result = $this->get(); 
							for( $x=0;$x<count($result);$x++){
								if($result[$x]){
									$from = (double)$result[$x]['mark_from'];		
									$to = (double)$result[$x]['mark_upto'];	
									$no_subject_below = $result[$x]['no_subject_below'];	
									$no_conduct_equal = $result[$x]['no_conduct_equal'];	
									$number_conduct = $result[$x]['number_conduct'];
									if( $from <= $grade && $grade <= $to  ){
										if( $no_subject_below ){
											$has_subject_below = $this->examresult_model->get_examresult_below( $student_id, $quarter, $no_subject_below );
											if( $has_subject_below ){
												$remove = true;
											}
										}  
										if( $no_conduct_equal && $number_conduct ){
											$check_conduct_ranking = $this->conductimportbatchdetail_model->check_conduct_ranking( $student_id, $quarter, $no_conduct_equal, $number_conduct );
											if( $check_conduct_ranking ){
												$remove = true;
											}
										} 
									
										if( $remove == false ){
											$studentlist[$y]['background_color'] = $result[$x]['background_color'];		
											$studentlist[$y]['text_color'] = $result[$x]['text_color'];	
											$studentlist[$y]['name'] = $result[$x]['name'];	
										} else {
											$next = ++$x;
											$studentlist[$y]['background_color'] = $result[$next]['background_color'];		
											$studentlist[$y]['text_color'] = $result[$next]['text_color'];	
											$studentlist[$y]['name'] = $result[$next]['name'];	
										}
									} 
								}
							}
						} else {
							$result = $this->get(); 
							for( $x=0;$x<count($result);$x++){
								if($result[$x]){
									$from = (double)$result[$x]['mark_from'];		
									$to = (double)$result[$x]['mark_upto'];	
									$no_subject_below = $result[$x]['no_subject_below'];	
									$no_conduct_equal = $result[$x]['no_conduct_equal'];	
									$number_conduct = $result[$x]['number_conduct'];

									//if( $from >= $grade && $grade >= $to ){
									if( $from <= $grade && $grade <= $to  ){
										if( $no_conduct_equal && $number_conduct ){
											$check_conduct_ranking = $this->conductimportbatchdetail_model->check_conduct_ranking( $student_id, $quarter, $no_conduct_equal, $number_conduct );
											if( $check_conduct_ranking ){
												$remove = true;
											}
										} 
									
										if( $remove == false ){
											$studentlist[$y]['background_color'] = $result[$x]['background_color'];		
											$studentlist[$y]['text_color'] = $result[$x]['text_color'];	
											$studentlist[$y]['name'] = $result[$x]['name'];	
										} 
									} 
								}
							}
						}
					} 
				}
			} */
		if( $honor_based == 'average'){
			$result = $this->get();
			$quarter = $studentlist['quarter'];
			$semester = $studentlist['semester'];
			if( $result ){
				for($y=0;$y<count($studentlist);$y++){
					if( isset( $studentlist[$y]['student_id'])){
						$student_id = $studentlist[$y]['student_id'];
						$grade = $studentlist[$y]['final_grade'];
						$grade = number_format($studentlist[$y]['final_grade'], $decimal_finalgrade,'.','');
						$remove = false;
						$data = array();
						if( count( $result ) > 0 ){
							for( $x=0;$x<count($result);$x++){
								if($result[$x]){
									$from = (double)$result[$x]['mark_from'];		
									$to = (double)$result[$x]['mark_upto'];	
									$no_subject_below = $result[$x]['no_subject_below'];	
									$no_conduct_equal = $result[$x]['no_conduct_equal'];	
									$number_conduct = $result[$x]['number_conduct'];
									if( $from <= $grade && $grade <= $to  ){
										if( $no_subject_below ){
											$has_subject_below = $this->examresult_model->get_examresult_below( $student_id, $quarter, $no_subject_below, $semester );
											if( $has_subject_below ){
												$remove = true;
											}
										}  
										if( $no_conduct_equal && $number_conduct ){
											$check_conduct_ranking = $this->conductimportbatchdetail_model->check_conduct_ranking( $student_id, $quarter, $no_conduct_equal, $number_conduct );
											if( $check_conduct_ranking ){
												$remove = true;
											}
										} 
									
										if( $remove == false ){
											$studentlist[$y]['background_color'] = $result[$x]['background_color'];		
											$studentlist[$y]['text_color'] = $result[$x]['text_color'];	
											$studentlist[$y]['name'] = $result[$x]['name'];	
										} else {
											$next = ++$x;
											$studentlist[$y]['background_color'] = $result[$next]['background_color'];		
											$studentlist[$y]['text_color'] = $result[$next]['text_color'];	
											$studentlist[$y]['name'] = $result[$next]['name'];	
										}
									} 
								}
							}
						}
					}
				}
			unset($studentlist['quarter']);
			unset($studentlist['semester']);				
			} 
		} else {
			$result = $this->getSortbyNoSubjectBelow(); 
			$quarter = $studentlist['quarter'];
			$semester = $studentlist['semester'];
			if( $result ){
				for($y=0;$y<count($studentlist);$y++){
					if( isset( $studentlist[$y]['student_id'])){
						$student_id = $studentlist[$y]['student_id'];
						$grade = $studentlist[$y]['final_grade'];
						$grade = number_format($studentlist[$y]['final_grade'], $decimal_finalgrade,'.','');
						$remove = false;
						$data = array();
						$remove_counter = 0;
						if( count( $result ) > 0 ){
							for( $x=0;$x<count($result);$x++){
								if($result[$x]){
									$from = (double)$result[$x]['mark_from'];		
									$to = (double)$result[$x]['mark_upto'];	
									$no_subject_below = $result[$x]['no_subject_below'];	
									$no_conduct_equal = $result[$x]['no_conduct_equal'];	
									$number_conduct = $result[$x]['number_conduct'];
									if( $no_subject_below ){
										$has_subject_below = $this->examresult_model->get_examresult_below( $student_id, $quarter, $no_subject_below, $semester );
										if( $has_subject_below  ){
											$remove = true;
											$remove_counter++;
										} 
										
										if( empty( $has_subject_below )){
											if( $no_conduct_equal && $number_conduct ){
												$check_conduct_ranking = $this->conductimportbatchdetail_model->check_conduct_ranking( $student_id, $quarter, $no_conduct_equal, $number_conduct );
												if( $check_conduct_ranking ){
													$remove_counter++;
												}
											} 
										}
									}  
									
									
										if( $remove == false ){
											$studentlist[$y]['background_color'] = $result[$x]['background_color'];		
											$studentlist[$y]['text_color'] = $result[$x]['text_color'];	
											$studentlist[$y]['name'] = $result[$x]['name'];	
										} else {
											if( $remove_counter < count( $result )){
												$next = $remove_counter;
												$studentlist[$y]['background_color'] = $result[$next]['background_color'];		
												$studentlist[$y]['text_color'] = $result[$next]['text_color'];	
												$studentlist[$y]['name'] = $result[$next]['name'];	
											} else {
												$studentlist[$y]['background_color'] = '';		
												$studentlist[$y]['text_color'] = '';		
												$studentlist[$y]['name'] = '';			
											}
										} 
									
								}
									
							}
						}
					}
						
				} 
			} 
			unset($studentlist['quarter']);
			unset($studentlist['semester']);	
		}
		
		
		return $studentlist;
	}
	
	/*
	public function gethonorstudent( $studentlist ){
		$this->load->model('conductimportbatchdetail_model');
		$this->load->model('gradingsetting_model');
		$gradingsettings = $this->gradingsetting_model->get();
		$based_criteria = isset($gradingsettings->based_criteria)?$gradingsettings->based_criteria:'genaverage';
		$decimal_grades = isset($gradingsettings->decimal_grades )?$gradingsettings->decimal_grades:'2';
		$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade )?$gradingsettings->decimal_finalgrade:'2';
		$honor_based = isset($gradingsettings->honor_based )?$gradingsettings->honor_based:'average';
		$result = $this->getSortbyNoSubjectBelow(); 
		/*if( $result ){
			for($y=0;$y<count($studentlist);$y++){
				$student_id = $studentlist[$y]['student_id'];
				$quarter = $studentlist[$y]['quarter'];
				$grade = $studentlist[$y]['final_grade'];
				$grade = round(  $studentlist[$y]['final_grade'], 2);
				$remove = false;
				$data = array();
				if( count( $result ) > 0 ){
					if( $based_criteria == 'subject'){
						$result = $this->getOrderBySubject(); 
						for( $x=0;$x<count($result);$x++){
							if($result[$x]){
								$from = (double)$result[$x]['mark_from'];		
								$to = (double)$result[$x]['mark_upto'];	
								$no_subject_below = $result[$x]['no_subject_below'];	
								$no_conduct_equal = $result[$x]['no_conduct_equal'];	
								$number_conduct = $result[$x]['number_conduct'];
								if( $no_subject_below ){
									$has_subject_below = $this->examresult_model->get_examresult_below( $student_id, $quarter, $no_subject_below );
									if( $has_subject_below ){
										$remove = true;
									}
								} 
								if( $no_conduct_equal && $number_conduct ){
									$check_conduct_ranking = $this->conductimportbatchdetail_model->check_conduct_ranking( $student_id, $quarter, $no_conduct_equal, $number_conduct );
									if( $check_conduct_ranking ){
										$remove = true;
									}
								} 
							
								if( $remove == false ){
									$studentlist[$y]['name'] = $result[$x]['name'];
								} 
							}
						}
					} elseif( $based_criteria == 'both'){
						$result = $this->get(); 
						for( $x=0;$x<count($result);$x++){
							if($result[$x]){
								$from = (double)$result[$x]['mark_from'];		
								$to = (double)$result[$x]['mark_upto'];	
								$no_subject_below = $result[$x]['no_subject_below'];	
								$no_conduct_equal = $result[$x]['no_conduct_equal'];	
								$number_conduct = $result[$x]['number_conduct'];
								if( $from <= $grade && $grade <= $to  ){
									if( $no_subject_below ){
										$has_subject_below = $this->examresult_model->get_examresult_below( $student_id, $quarter, $no_subject_below );
										if( $has_subject_below ){
											$remove = true;
										}
									}  
									if( $no_conduct_equal && $number_conduct ){
										$check_conduct_ranking = $this->conductimportbatchdetail_model->check_conduct_ranking( $student_id, $quarter, $no_conduct_equal, $number_conduct );
										if( $check_conduct_ranking ){
											$remove = true;
										}
									} 
								
									if( $remove == false ){
										$studentlist[$y]['name'] = $result[$x]['name'];
									} else {
										$next = ++$x;
										$studentlist[$y]['name'] = $result[$next]['name'];
									}
								} 
							}
						}
					} else {
						$result = $this->get(); 
						for( $x=0;$x<count($result);$x++){
							if($result[$x]){
								$from = (double)$result[$x]['mark_from'];		
								$to = (double)$result[$x]['mark_upto'];	
								$no_subject_below = $result[$x]['no_subject_below'];	
								$no_conduct_equal = $result[$x]['no_conduct_equal'];	
								$number_conduct = $result[$x]['number_conduct'];

								//if( $from >= $grade && $grade >= $to ){
								if( $from <= $grade && $grade <= $to  ){
									if( $no_conduct_equal && $number_conduct ){
										$check_conduct_ranking = $this->conductimportbatchdetail_model->check_conduct_ranking( $student_id, $quarter, $no_conduct_equal, $number_conduct );
										if( $check_conduct_ranking ){
											$remove = true;
										}
									} 
								
									if( $remove == false ){
										$studentlist[$y]['name'] = $result[$x]['name'];
									} 
								} 
							}
						}
					}
				} 
			}
			
			$get_student = $studentlist;
			for($z=0; $z<count($get_student);$z++){
				$exist = array_key_exists('name', $studentlist[$z]);
				if(!$exist){
					unset($studentlist[$z]);
				}
				
			}
			
		}
				
		if( $honor_based == 'average'){
			$result = $this->get();
			$quarter = $studentlist['quarter'];
			$semester = $studentlist['semester'];
			if( $result ){
				for($y=0;$y<count($studentlist);$y++){
					if( isset( $studentlist[$y]['student_id'])){
						$student_id = $studentlist[$y]['student_id'];
						//$quarter = $studentlist[$y]['quarter'];
						$grade = $studentlist[$y]['final_grade'];
						$grade = number_format($studentlist[$y]['final_grade'], $decimal_finalgrade,'.','');
						$remove = false;
						$data = array();
						if( count( $result ) > 0 ){
							$result = $this->get(); 
							for( $x=0;$x<count($result);$x++){
								if($result[$x]){
									$from = (double)$result[$x]['mark_from'];		
									$to = (double)$result[$x]['mark_upto'];	
									$no_subject_below = $result[$x]['no_subject_below'];	
									$no_conduct_equal = $result[$x]['no_conduct_equal'];	
									$number_conduct = $result[$x]['number_conduct'];
									if( $from <= $grade && $grade <= $to  ){
										if( $no_subject_below ){
											$has_subject_below = $this->examresult_model->get_examresult_below( $student_id, $quarter, $no_subject_below, $semester );
											if( $has_subject_below ){
												$remove = true;
											}
										}  
										if( $no_conduct_equal && $number_conduct ){
											$check_conduct_ranking = $this->conductimportbatchdetail_model->check_conduct_ranking( $student_id, $quarter, $no_conduct_equal, $number_conduct );
											if( $check_conduct_ranking ){
												$remove = true;
											}
										} 
									
										if( $remove == false ){
											$studentlist[$y]['name'] = $result[$x]['name'];
										} else {
											$next = ++$x;
											$studentlist[$y]['name'] = $result[$next]['name'];
										}
									} 
								}
							}
						}
					}				
				}
			} 
		} else {
			$quarter = $studentlist['quarter'];
			$semester = $studentlist['semester'];
			$result = $this->getSortbyNoSubjectBelow(); 
				if( $result ){
				for($y=0;$y<count($studentlist);$y++){
					if( isset( $studentlist[$y]['student_id'])){
						$student_id = $studentlist[$y]['student_id'];
						//$quarter = $studentlist[$y]['quarter'];
						$grade = $studentlist[$y]['final_grade'];
						$grade = number_format($studentlist[$y]['final_grade'], $decimal_finalgrade,'.','');
						$remove = false;
						$remove_counter = 0;
						$data = array();
						if( count( $result ) > 0 ){
							$result = $this->get(); 
							for( $x=0;$x<count($result);$x++){
								if($result[$x]){
									$from = (double)$result[$x]['mark_from'];		
									$to = (double)$result[$x]['mark_upto'];	
									$no_subject_below = $result[$x]['no_subject_below'];	
									$no_conduct_equal = $result[$x]['no_conduct_equal'];	
									$number_conduct = $result[$x]['number_conduct'];
									if( $no_subject_below ){
										$has_subject_below = $this->examresult_model->get_examresult_below( $student_id, $quarter, $no_subject_below, $semester );
										if( $has_subject_below ){
											$remove = true;
											$remove_counter++;
										}
										if( empty( $has_subject_below )){
											if( $no_conduct_equal && $number_conduct ){
												$check_conduct_ranking = $this->conductimportbatchdetail_model->check_conduct_ranking( $student_id, $quarter, $no_conduct_equal, $number_conduct );
												if( $check_conduct_ranking ){
													$remove = true;
													$remove_counter++;
												}
											} 
										}
									}  
									if( $remove_counter < count( $result )){
										if( $remove == false ){
											$studentlist[$y]['name'] = $result[$x]['name'];
										} else {
											$next = $remove_counter;
											$studentlist[$y]['name'] = $result[$next]['name'];
										}
									} else {
										unset($studentlist[$y]['name']);
									}
								
								}
							}
						}
					}				
				}
			}
		}	
		unset($studentlist['quarter']);	
		unset($studentlist['semester']);	
		$get_student = $studentlist;
		for($z=0; $z<count($get_student);$z++){
			$exist = array_key_exists('name', $studentlist[$z]);
			if(!$exist){
				unset($studentlist[$z]);
			}
			
		}
		
		return $studentlist;
	
	}
	 */
	 
	 	public function gethonorstudent( $studentlist ){
			
		$this->load->model('conductimportbatchdetail_model');
		$this->load->model('gradingsetting_model');
		$this->load->model('marksgrademanager_model');
		$gradingsettings = $this->gradingsetting_model->get();
		$decimal_grades = isset($gradingsettings->decimal_grades )?$gradingsettings->decimal_grades:'2';

		$quarter = $studentlist['quarter'];
		$semester = $studentlist['semester'];
		$set_category = 'ga';
		$grade_list = $this->get_Category( $set_category );
		
		if( $grade_list ){
			for($y=0;$y<count($studentlist);$y++){
				if( isset( $studentlist[$y]['id'])){
					$student_id =  $studentlist[$y]['id'];
					$final_grade =  $studentlist[$y]['final_grade'];
					$class_id =  $studentlist[$y]['class_id'];
					//if( $student_id == 12 ){
					$get_final_status = '';
					$get_final_priority = '';
					$current_status = array();
					$exam_result = $this->examresult_model->get_examresult( $student_id, $quarter, $semester );
					$get_subject_grade=array();
					$get_min_subject_grade = 0;
					foreach( $exam_result as $exam => $result ){
						$grade_result = number_format($result['final_grade'],$decimal_grades,'.','');
						$get_subject_grade[] = $grade_result;
					}
					if( $get_subject_grade ){
						$get_min_subject_grade = min( $get_subject_grade );
					}
					for( $x=0;$x<count($grade_list);$x++){
						if( isset( $grade_list[$x]['id'])){
							$grade_id = $grade_list[$x]['id'];
							$name = $grade_list[$x]['name'];
							$marksgrade = $this->marksgrademanager_model->getMarksGrade( $grade_id );
							if( $marksgrade ){
								for( $z=0; $z<count($marksgrade);$z++ ){
									$based = $marksgrade[$z]['based'];
									$type = $marksgrade[$z]['type'];
									$type_id = $marksgrade[$z]['type_id'];
									$operation = $marksgrade[$z]['operation'];
									$value = $marksgrade[$z]['value'];
									if( $based == 'average' ){
										if( $type == 'subject'){
											if( $type_id ){
												$checkSubjectByClass = $this->subject_model->checkSubjectByClass( $class_id, $type_id );
												if( $checkSubjectByClass ){
													$check_manager = $this->subjectmanager_model->getBySubjectSession( $type_id );
													$display_card = $check_manager['display_card'];
													
													if( $display_card == 'yes'){
														$get_exam_by_subject = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, $quarter,  $type_id, $semester );
														if( $get_exam_by_subject ){
															$get_grade = number_format($get_exam_by_subject->grades, $decimal_grades,'.',' ');
															$statement = $this->operation( $operation, $get_grade, $value );
															if( $statement ){
																$current_status[$grade_id][] = 'true';
																
															} else {
																$current_status[$grade_id][] = 'false';
															}	
														} else {
															$current_status[$grade_id][] = 'false';
														}
													}
												}
											} else {
												$statement = $this->operation( $operation, $final_grade, $value );
												if( $statement ){
													$current_status[$grade_id][] = 'true';
													
												} else {
													$current_status[$grade_id][] = 'false';
												}
											}
										} elseif( $type == 'conduct'){
											
										}
									} elseif( $based == 'subject_grade'){
										if( $type == 'subject'){
											$exam_result = $this->examresult_model->get_examresult( $student_id, $quarter, $semester );
											if( $type_id ){
												
											} else {
											/* 	echo "<br/>";
												echo $get_min_subject_grade .' '.$operation.' '. $value.' '.$grade_id.'['.$name.']'; */
												$statement = $this->operation( $operation, $get_min_subject_grade, $value );
												if( $statement ){
													//print_r('true');
													$current_status[$grade_id][] = 'true';
													
												} else {
													//print_r('false');
													$current_status[$grade_id][] = 'false';
												}
											}
										} elseif( $type == 'conduct'){
											
										}
									}
								}
								
							}
						}
					}
					
					if( $current_status ){
						/* printr( $current_status );
						echo "-------"; */
						//printx( $current_status );
						foreach( $current_status as $key => $value ){
							$check_if_true_exist ='';
							$check_if_false_exist='';
							$get_status_id = $key;
							for( $xyz=0; $xyz <count($value);$xyz++){
								if(isset( $value )){
									$get_value = $value[ $xyz];
									if( $get_value == 'true' ){
										//printr( 'true' );
										$check_if_true_exist = 'true';
									} elseif( $get_value == 'false' ) {
										//printr( 'false' );
										$check_if_false_exist ='true';
									}
								}
							}
							
							if( ( $check_if_true_exist == true && $check_if_false_exist != true ) || ( $check_if_true_exist == true && $check_if_false_exist == ' ' ) ){
								$get_final_status = $get_status_id;
							}
						}
					} 
					if( $get_final_status ){
						$details = $this->get( $get_final_status );
						$background_color = $details['background_color'];
						$text_color = $details['text_color'];
						$name = $details['name'];
						$studentlist[$y]['background_color'] = $background_color;		
						$studentlist[$y]['text_color'] = $text_color;	
						$studentlist[$y]['name'] = $name;	
					}
				}
			}	
		}
	//	die();
		unset($studentlist['quarter']);	
		unset($studentlist['semester']);	
		$get_student = $studentlist;
		for($z=0; $z<count($get_student);$z++){
			$exist = array_key_exists('name', $studentlist[$z]);
			if(!$exist){
				unset($studentlist[$z]);
			}
			
		}
		return $studentlist;
	}
		

		public function getgraderanking( $studentlist ){
			$this->load->model('conductimportbatchdetail_model');
			$this->load->model('gradingsetting_model');
			$this->load->model('marksgrademanager_model');
			$gradingsettings = $this->gradingsetting_model->get();
			$decimal_grades = isset($gradingsettings->decimal_grades )?$gradingsettings->decimal_grades:'2';

			$quarter = $studentlist['quarter'];
			$semester = $studentlist['semester'];
			$set_category = 'ga';
			$grade_list = $this->get_Category( $set_category );
			
			if( $grade_list ){
				for($y=0;$y<count($studentlist);$y++){
					if( isset( $studentlist[$y]['id'])){
						$student_id =  $studentlist[$y]['id'];
						$final_grade =  $studentlist[$y]['final_grade'];
						$class_id =  $studentlist[$y]['class_id'];
						//if( $student_id == 12 ){
						$get_final_status = '';
						$get_final_priority = '';
						$current_status = array();
						$exam_result = $this->examresult_model->get_examresult( $student_id, $quarter, $semester );
						$get_subject_grade=array();
						$get_min_subject_grade = 0;
						foreach( $exam_result as $exam => $result ){
							$grade_result = number_format($result['final_grade'],$decimal_grades,'.','');
							$get_subject_grade[] = $grade_result;
						}
						if( $get_subject_grade ){
							$get_min_subject_grade = min( $get_subject_grade );
						}
						for( $x=0;$x<count($grade_list);$x++){
							if( isset( $grade_list[$x]['id'])){
								$grade_id = $grade_list[$x]['id'];
								$name = $grade_list[$x]['name'];
								$marksgrade = $this->marksgrademanager_model->getMarksGrade( $grade_id );
								if( $marksgrade ){
									for( $z=0; $z<count($marksgrade);$z++ ){
										$based = $marksgrade[$z]['based'];
										$type = $marksgrade[$z]['type'];
										$type_id = $marksgrade[$z]['type_id'];
										$operation = $marksgrade[$z]['operation'];
										$value = $marksgrade[$z]['value'];
										if( $based == 'average' ){
											if( $type == 'subject'){
												if( $type_id ){
													$checkSubjectByClass = $this->subject_model->checkSubjectByClass( $class_id, $type_id );
													if( $checkSubjectByClass ){
														$check_manager = $this->subjectmanager_model->getBySubjectSession( $type_id );
														$display_card = $check_manager['display_card'];
														
														if( $display_card == 'yes'){
															$get_exam_by_subject = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, $quarter,  $type_id, $semester );
															if( $get_exam_by_subject ){
																$get_grade = number_format($get_exam_by_subject->grades, $decimal_grades,'.',' ');
																$statement = $this->operation( $operation, $get_grade, $value );
																if( $statement ){
																	$current_status[$grade_id][] = 'true';
																	
																} else {
																	$current_status[$grade_id][] = 'false';
																}	
															} else {
																$current_status[$grade_id][] = 'false';
															}
														}
													}
												} else {
													$statement = $this->operation( $operation, $final_grade, $value );
													if( $statement ){
														$current_status[$grade_id][] = 'true';
														
													} else {
														$current_status[$grade_id][] = 'false';
													}
												}
											} elseif( $type == 'conduct'){
												
											}
										} elseif( $based == 'subject_grade'){
											if( $type == 'subject'){
												$exam_result = $this->examresult_model->get_examresult( $student_id, $quarter, $semester );
												if( $type_id ){
													
												} else {
												/* 	echo "<br/>";
													echo $get_min_subject_grade .' '.$operation.' '. $value.' '.$grade_id.'['.$name.']'; */
													$statement = $this->operation( $operation, $get_min_subject_grade, $value );
													if( $statement ){
														//print_r('true');
														$current_status[$grade_id][] = 'true';
														
													} else {
														//print_r('false');
														$current_status[$grade_id][] = 'false';
													}
												}
											} elseif( $type == 'conduct'){
												
											}
										}
									}
									
								}
							}
						}
						
						if( $current_status ){
							/* printr( $current_status );
							echo "-------"; */
							//printx( $current_status );
							foreach( $current_status as $key => $value ){
								$check_if_true_exist ='';
								$check_if_false_exist='';
								$get_status_id = $key;
								for( $xyz=0; $xyz <count($value);$xyz++){
									if(isset( $value )){
										$get_value = $value[ $xyz];
										if( $get_value == 'true' ){
											//printr( 'true' );
											$check_if_true_exist = 'true';
										} elseif( $get_value == 'false' ) {
											//printr( 'false' );
											$check_if_false_exist ='true';
										}
									}
								}
								
								if( ( $check_if_true_exist == true && $check_if_false_exist != true ) || ( $check_if_true_exist == true && $check_if_false_exist == ' ' ) ){
									$get_final_status = $get_status_id;
								}
							}
						} 
						if( $get_final_status ){
							$details = $this->get( $get_final_status );
							$background_color = $details['background_color'];
							$text_color = $details['text_color'];
							$name = $details['name'];
							$studentlist[$y]['background_color'] = $background_color;		
							$studentlist[$y]['text_color'] = $text_color;	
							$studentlist[$y]['name'] = $name;	
						} else {
							$studentlist[$y]['background_color'] = '';		
							$studentlist[$y]['text_color'] = '';
							$studentlist[$y]['name'] = '';
						}
					}
				}	
			}
		//	die();
				
			return $studentlist;
		}
		
 // 	public function getgraderanking( $studentlist ){
	// 	$this->load->model('conductimportbatchdetail_model');
	// 	$this->load->model('gradingsetting_model');
	// 	$this->load->model('marksgrademanager_model');
	// 	$gradingsettings = $this->gradingsetting_model->get();
	// 	$decimal_grades = isset($gradingsettings->decimal_grades )?$gradingsettings->decimal_grades:'2';

	// 	$quarter = $studentlist['quarter'];
	// 	$semester = $studentlist['semester'];
	// 	$set_category = 'ga';
	// 	$grade_list = $this->get_Category( $set_category );
		
	// 	if( $grade_list ){
	// 		for($y=0;$y<count($studentlist);$y++){
	// 			if( isset( $studentlist[$y]['id'])){
	// 				$student_id =  $studentlist[$y]['id'];
	// 				$final_grade =  $studentlist[$y]['final_grade'];
	// 				$class_id =  $studentlist[$y]['class_id'];
	// 				//if( $student_id == 12 ){
	// 				$get_final_status = '';
	// 				$get_final_priority = '';
	// 				$current_status = array();
	// 				$exam_result = $this->examresult_model->get_examresult( $student_id, $quarter, $semester );
	// 				$get_subject_grade=array();
	// 				$get_min_subject_grade = 0;
	// 				foreach( $exam_result as $exam => $result ){
	// 					$grade_result = number_format($result['final_grade'],$decimal_grades,'.','');
	// 					$get_subject_grade[] = $grade_result;
	// 				}
	// 				if( $get_subject_grade ){
	// 					$get_min_subject_grade = min( $get_subject_grade );
	// 				}
	// 				for( $x=0;$x<count($grade_list);$x++){
	// 					if( isset( $grade_list[$x]['id'])){
	// 						$grade_id = $grade_list[$x]['id'];
	// 						$name = $grade_list[$x]['name'];
	// 						$marksgrade = $this->marksgrademanager_model->getMarksGrade( $grade_id );
	// 						if( $marksgrade ){
	// 							for( $z=0; $z<count($marksgrade);$z++ ){
	// 								$based = $marksgrade[$z]['based'];
	// 								$type = $marksgrade[$z]['type'];
	// 								$type_id = $marksgrade[$z]['type_id'];
	// 								$operation = $marksgrade[$z]['operation'];
	// 								$value = $marksgrade[$z]['value'];
	// 								if( $based == 'average' ){
	// 									if( $type == 'subject'){
	// 										if( $type_id ){
	// 											$checkSubjectByClass = $this->subject_model->checkSubjectByClass( $class_id, $type_id );
	// 											if( $checkSubjectByClass ){
	// 												$check_manager = $this->subjectmanager_model->getBySubjectSession( $type_id );
	// 												$display_card = $check_manager['display_card'];
													
	// 												if( $display_card == 'yes'){
	// 													$get_exam_by_subject = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, $quarter,  $type_id, $semester );
	// 													if( $get_exam_by_subject ){
	// 														$get_grade = number_format($get_exam_by_subject->grades, $decimal_grades,'.',' ');
	// 														$statement = $this->operation( $operation, $get_grade, $value );
	// 														if( $statement ){
	// 															$current_status[$grade_id][] = 'true';
																
	// 														} else {
	// 															$current_status[$grade_id][] = 'false';
	// 														}	
	// 													} else {
	// 														$current_status[$grade_id][] = 'false';
	// 													}
	// 												}
	// 											}
	// 										} else {
	// 											$statement = $this->operation( $operation, $final_grade, $value );
	// 											if( $statement ){
	// 												$current_status[$grade_id][] = 'true';
													
	// 											} else {
	// 												$current_status[$grade_id][] = 'false';
	// 											}
	// 										}
	// 									} elseif( $type == 'conduct'){
											
	// 									}
	// 								} elseif( $based == 'subject_grade'){
	// 									if( $type == 'subject'){
	// 										$exam_result = $this->examresult_model->get_examresult( $student_id, $quarter, $semester );
	// 										if( $type_id ){
												
	// 										} else {
	// 										/* 	echo "<br/>";
	// 											echo $get_min_subject_grade .' '.$operation.' '. $value.' '.$grade_id.'['.$name.']'; */
	// 											$statement = $this->operation( $operation, $get_min_subject_grade, $value );
	// 											if( $statement ){
	// 												//print_r('true');
	// 												$current_status[$grade_id][] = 'true';
													
	// 											} else {
	// 												//print_r('false');
	// 												$current_status[$grade_id][] = 'false';
	// 											}
	// 										}
	// 									} elseif( $type == 'conduct'){
											
	// 									}
	// 								}
	// 							}
								
	// 						}
	// 					}
	// 				}
					
	// 				if( $current_status ){
	// 					/* printr( $current_status );
	// 					echo "-------"; */
	// 					//printx( $current_status );
	// 					foreach( $current_status as $key => $value ){
	// 						$check_if_true_exist ='';
	// 						$check_if_false_exist='';
	// 						$get_status_id = $key;
	// 						for( $xyz=0; $xyz <count($value);$xyz++){
	// 							if(isset( $value )){
	// 								$get_value = $value[ $xyz];
	// 								if( $get_value == 'true' ){
	// 									//printr( 'true' );
	// 									$check_if_true_exist = 'true';
	// 								} elseif( $get_value == 'false' ) {
	// 									//printr( 'false' );
	// 									$check_if_false_exist ='true';
	// 								}
	// 							}
	// 						}
							
	// 						if( ( $check_if_true_exist == true && $check_if_false_exist != true ) || ( $check_if_true_exist == true && $check_if_false_exist == ' ' ) ){
	// 							$get_final_status = $get_status_id;
	// 						}
	// 					}
	// 				} 
	// 				if( $get_final_status ){
	// 					$details = $this->get( $get_final_status );
	// 					$background_color = $details['background_color'];
	// 					$text_color = $details['text_color'];
	// 					$name = $details['name'];
	// 					$studentlist[$y]['background_color'] = $background_color;		
	// 					$studentlist[$y]['text_color'] = $text_color;	
	// 					$studentlist[$y]['name'] = $name;	
	// 				} else {
	// 					$studentlist[$y]['background_color'] = '';		
	// 					$studentlist[$y]['text_color'] = '';
	// 					$studentlist[$y]['name'] = '';
	// 				}
	// 			}
	// 		}	
	// 	}
	// //	die();
	// 	unset($studentlist['quarter']);	
	// 	unset($studentlist['semester']);	
	// 	return $studentlist;
	// }*/
	
	// public function getgraderanking( $studentlist ){
	// 	$this->load->model('conductimportbatchdetail_model');
	// 	$this->load->model('gradingsetting_model');
	// 	$this->load->model('marksgrademanager_model');
	// 	$gradingsettings = $this->gradingsetting_model->get();
	// 	$decimal_grades = isset($gradingsettings->decimal_grades )?$gradingsettings->decimal_grades:'2';

	// 	$quarter = $studentlist['quarter'];
	// 	$semester = $studentlist['semester'];
	// 	$set_category = 'ga';
	// 	$grade_list = $this->get_Category( $set_category );
		
	// 	if( $grade_list ){
	// 		for($y=0;$y<count($studentlist);$y++){
	// 			if( isset( $studentlist[$y]['id'])){
	// 				$student_id =  $studentlist[$y]['id'];
	// 				$final_grade =  $studentlist[$y]['final_grade'];
	// 				$class_id =  $studentlist[$y]['class_id'];
	// 				//if( $student_id == 12 ){
	// 				$get_final_status = '';
	// 				$get_final_priority = '';
	// 				$current_status = array();
	// 				$exam_result = $this->examresult_model->get_examresult( $student_id, $quarter, $semester );
	// 				$get_subject_grade=array();
	// 				$get_min_subject_grade = 0;
	// 				foreach( $exam_result as $exam => $result ){
	// 					$grade_result = number_format($result['final_grade'],$decimal_grades,'.','');
	// 					$get_subject_grade[] = $grade_result;
	// 				}
	// 				if( $get_subject_grade ){
	// 					$get_min_subject_grade = min( $get_subject_grade );
	// 				}
	// 				for( $x=0;$x<count($grade_list);$x++){
	// 					if( isset( $grade_list[$x]['id'])){
	// 						$grade_id = $grade_list[$x]['id'];
	// 						$name = $grade_list[$x]['name'];
	// 						$marksgrade = $this->marksgrademanager_model->getMarksGrade( $grade_id );
	// 						if( $marksgrade ){
	// 							for( $z=0; $z<count($marksgrade);$z++ ){
	// 								$based = $marksgrade[$z]['based'];
	// 								$type = $marksgrade[$z]['type'];
	// 								$type_id = $marksgrade[$z]['type_id'];
	// 								$operation = $marksgrade[$z]['operation'];
	// 								$value = $marksgrade[$z]['value'];
	// 								if( $based == 'average' ){
	// 									if( $type == 'subject'){
	// 										if( $type_id ){
	// 											$checkSubjectByClass = $this->subject_model->checkSubjectByClass( $class_id, $type_id );
	// 											if( $checkSubjectByClass ){
	// 												$check_manager = $this->subjectmanager_model->getBySubjectSession( $type_id );
	// 												$display_card = $check_manager['display_card'];
													
	// 												if( $display_card == 'yes'){
	// 													$get_exam_by_subject = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, $quarter,  $type_id, $semester );
	// 													if( $get_exam_by_subject ){
	// 														$get_grade = number_format($get_exam_by_subject->grades, $decimal_grades,'.',' ');
	// 														$statement = $this->operation( $operation, $get_grade, $value );
	// 														if( $statement ){
	// 															$current_status[$grade_id][] = 'true';
																
	// 														} else {
	// 															$current_status[$grade_id][] = 'false';
	// 														}	
	// 													} else {
	// 														$current_status[$grade_id][] = 'false';
	// 													}
	// 												}
	// 											}
	// 										} else {
	// 											$statement = $this->operation( $operation, $final_grade, $value );
	// 											if( $statement ){
	// 												$current_status[$grade_id][] = 'true';
													
	// 											} else {
	// 												$current_status[$grade_id][] = 'false';
	// 											}
	// 										}
	// 									} elseif( $type == 'conduct'){
											
	// 									}
	// 								} elseif( $based == 'subject_grade'){
	// 									if( $type == 'subject'){
	// 										$exam_result = $this->examresult_model->get_examresult( $student_id, $quarter, $semester );
	// 										if( $type_id ){
												
	// 										} else {
	// 										/* 	echo "<br/>";
	// 											echo $get_min_subject_grade .' '.$operation.' '. $value.' '.$grade_id.'['.$name.']'; */
	// 											$statement = $this->operation( $operation, $get_min_subject_grade, $value );
	// 											if( $statement ){
	// 												//print_r('true');
	// 												$current_status[$grade_id][] = 'true';
													
	// 											} else {
	// 												//print_r('false');
	// 												$current_status[$grade_id][] = 'false';
	// 											}
	// 										}
	// 									} elseif( $type == 'conduct'){
											
	// 									}
	// 								}
	// 							}
								
	// 						}
	// 					}
	// 				}
					
	// 				if( $current_status ){
	// 					/* printr( $current_status );
	// 					echo "-------"; */
	// 					//printx( $current_status );
	// 					foreach( $current_status as $key => $value ){
	// 						$check_if_true_exist ='';
	// 						$check_if_false_exist='';
	// 						$get_status_id = $key;
	// 						for( $xyz=0; $xyz <count($value);$xyz++){
	// 							if(isset( $value )){
	// 								$get_value = $value[ $xyz];
	// 								if( $get_value == 'true' ){
	// 									//printr( 'true' );
	// 									$check_if_true_exist = 'true';
	// 								} elseif( $get_value == 'false' ) {
	// 									//printr( 'false' );
	// 									$check_if_false_exist ='true';
	// 								}
	// 							}
	// 						}
							
	// 						if( ( $check_if_true_exist == true && $check_if_false_exist != true ) || ( $check_if_true_exist == true && $check_if_false_exist == ' ' ) ){
	// 							$get_final_status = $get_status_id;
	// 						}
	// 					}
	// 				} 
	// 				if( $get_final_status ){
	// 					$details = $this->get( $get_final_status );
	// 					$background_color = $details['background_color'];
	// 					$text_color = $details['text_color'];
	// 					$name = $details['name'];
	// 					$studentlist[$y]['background_color'] = $background_color;		
	// 					$studentlist[$y]['text_color'] = $text_color;	
	// 					$studentlist[$y]['name'] = $name;	
	// 				} else {
	// 					$studentlist[$y]['background_color'] = '';		
	// 					$studentlist[$y]['text_color'] = '';
	// 					$studentlist[$y]['name'] = '';
	// 				}
	// 			}
	// 		}	
	// 	}
	// //	die();
	// 	unset($studentlist['quarter']);	
	// 	unset($studentlist['semester']);	
	// 	return $studentlist;
	// }
	
	
	public function getgraderanking_gpa( $studentlist ){
		$this->load->model('conductimportbatchdetail_model');
		$this->load->model('gradingsetting_model');
		$this->load->model('marksgrademanager_model');
		$gradingsettings = $this->gradingsetting_model->get();
		$decimal_grades = isset($gradingsettings->decimal_grades )?$gradingsettings->decimal_grades:'2';

		$quarter = $studentlist['quarter'];
		$semester = $studentlist['semester'];
		$set_category = 'gpa';
		$grade_list = $this->get_Category( $set_category );
		if( $grade_list ){
			for($y=0;$y<count($studentlist);$y++){
				if( isset( $studentlist[$y]['id'])){
					$student_id =  $studentlist[$y]['id'];
					$final_grade =  $studentlist[$y]['final_grade'];
					$class_id =  $studentlist[$y]['class_id'];
					//if( $student_id == 12 ){
					$get_final_status = '';
					$get_final_priority = '';
					$current_status = array();
					$exam_result = $this->examresult_model->get_examresult( $student_id, $quarter, $semester );
					$get_subject_grade=array();
					$get_min_subject_grade = 0;
					foreach( $exam_result as $exam => $result ){
						$grade_result = number_format($result['final_grade'],$decimal_grades,'.','');
						$get_subject_grade[] = $grade_result;
					}
					if( $get_subject_grade ){
						$get_min_subject_grade = min( $get_subject_grade );
					}
					for( $x=0;$x<count($grade_list);$x++){
						if( isset( $grade_list[$x]['id'])){
							$grade_id = $grade_list[$x]['id'];
							$name = $grade_list[$x]['name'];
							$marksgrade = $this->marksgrademanager_model->getMarksGrade( $grade_id );
							if( $marksgrade ){
								for( $z=0; $z<count($marksgrade);$z++ ){
									$based = $marksgrade[$z]['based'];
									$type = $marksgrade[$z]['type'];
									$type_id = $marksgrade[$z]['type_id'];
									$operation = $marksgrade[$z]['operation'];
									$value = $marksgrade[$z]['value'];
									if( $based == 'average' ){
										if( $type == 'subject'){
											if( $type_id ){
												$checkSubjectByClass = $this->subject_model->checkSubjectByClass( $class_id, $type_id );
												if( $checkSubjectByClass ){
													$check_manager = $this->subjectmanager_model->getBySubjectSession( $type_id );
													$display_card = $check_manager['display_card'];
													if( $display_card == 'yes'){
														$get_exam_by_subject = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, $quarter,  $type_id, $semester );
														if( $get_exam_by_subject ){
															$get_grade = number_format($get_exam_by_subject->grades, $decimal_grades,'.',' ');
															$statement = $this->operation( $operation, $get_grade, $value );
															if( $statement ){
																$current_status[$grade_id][] = 'true';
																
															} else {
																$current_status[$grade_id][] = 'false';
															}	
														} else {
															$current_status[$grade_id][] = 'false';
														}
													}
												}
											} else {
												$statement = $this->operation( $operation, $final_grade, $value );
												if( $statement ){
													$current_status[$grade_id][] = 'true';
													
												} else {
													$current_status[$grade_id][] = 'false';
												}
											}
										} elseif( $type == 'conduct'){
											
										}
									} elseif( $based == 'subject_grade'){
										if( $type == 'subject'){
											$exam_result = $this->examresult_model->get_examresult( $student_id, $quarter, $semester );
											if( $type_id ){
												
											} else {
											/* 	echo "<br/>";
												echo $get_min_subject_grade .' '.$operation.' '. $value.' '.$grade_id.'['.$name.']'; */
												$statement = $this->operation( $operation, $get_min_subject_grade, $value );
												if( $statement ){
													//print_r('true');
													$current_status[$grade_id][] = 'true';
													
												} else {
													//print_r('false');
													$current_status[$grade_id][] = 'false';
												}
											}
										} elseif( $type == 'conduct'){
											
										}
									}
								}
								
							}
						}
					}
					
					if( $current_status ){
						/* printr( $current_status );
						echo "-------"; */
						//printx( $current_status );
						foreach( $current_status as $key => $value ){
							$check_if_true_exist ='';
							$check_if_false_exist='';
							$get_status_id = $key;
							for( $xyz=0; $xyz <count($value);$xyz++){
								if(isset( $value )){
									$get_value = $value[ $xyz];
									if( $get_value == 'true' ){
										//printr( 'true' );
										$check_if_true_exist = 'true';
									} elseif( $get_value == 'false' ) {
										//printr( 'false' );
										$check_if_false_exist ='true';
									}
								}
							}
							
							if( ( $check_if_true_exist == true && $check_if_false_exist != true ) || ( $check_if_true_exist == true && $check_if_false_exist == ' ' ) ){
								$get_final_status = $get_status_id;
							}
						}
					} 
					if( $get_final_status ){
						$details = $this->get( $get_final_status );
						$background_color = $details['background_color'];
						$text_color = $details['text_color'];
						$name = $details['name'];
						$studentlist[$y]['background_color'] = $background_color;		
						$studentlist[$y]['text_color'] = $text_color;	
						$studentlist[$y]['name'] = $name;	
					} else {
						$studentlist[$y]['background_color'] = '';		
						$studentlist[$y]['text_color'] = '';
						$studentlist[$y]['name'] = '';
					}
				}
			}	
		}
	//	die();
		unset($studentlist['quarter']);	
		unset($studentlist['semester']);	
		return $studentlist;
	}

	
	public function getgradeperquarter(  $student_id,  $subject_id,  $quarter ,  $semester=NULL, $session_id = null ){
		$this->load->model('gradingsetting_model');	
		$gradingsettings = $this->gradingsetting_model->get();
		if( $session_id == null ){
			$session_id = $this->current_session;
		}
	/* 	if( $semester ){
			if( $semester == 1 ){
				$get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array();	
			} else {
				$get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();	
			}
			$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
			$sql_quarter = $this->sql_array_to_string( $get_quarter );
			$total_quarter = count($get_quarter);
			
		}
	 */
		if( $semester ){
				/* if( $quarter == $total_quarter ){
				$result = $this->db->query("SELECT AVG(get_final_grade)  as final_grade,  AE.`student_id`, AE.`name` as subject_name,  AE.`quarter`,AE.`student_id` as id
					FROM ( SELECT SUM(get_marks)/".$total_quarter." AS get_final_grade, `exam_results`.`student_id`, `subjects`.`name`, `quarter`
					FROM `exam_results`
					JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`
					JOIN `students` ON `students`.`id` = `exam_results`.`student_id`
					JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`
					JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`
					JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`
					JOIN `strand` ON `strand`.`id` = `students`.`strand_id`
					JOIN `specialized_subjects` ON `specialized_subjects`.`strand_id` = `strand`.`id`
					WHERE `student_session`.`class_id` = '".$class."'
					AND `student_session`.`section_id` = '".$section."'
					AND `exam_results`.`session_id` = '".$this->current_session."'
					AND `subjects`.`kind` <> 'child'
					AND `exam_results`.`subject_id` NOT IN ( SELECT parent_id FROM subjects WHERE `exam_results`.`subject_id` =  `subjects`.`parent_id` AND `subjects`.`kind` = 'child' )
					AND `specialized_subjects`.`semester` = '".$semester."'
					AND `specialized_subjects`.`subject_id` <=> `exam_results`.`subject_id`
					AND `quarter` IN (".$sql_quarter.")
					GROUP BY `exam_results`.`student_id`, `exam_results`.`subject_id`
					UNION  ALL
					SELECT SUM(get_final_grade_per_quarter ) / ".$total_quarter." AS get_final_grade, AC.`student_id`, AC.`name` , AC.`quarter`
					FROM ( SELECT (SUM(get_marks) / COUNT(get_marks)) AS `get_final_grade_per_quarter`, `exam_results`.`student_id`, `subjects`.`name`, `subjects`.`id`  as subject_id, `quarter`
					FROM `exam_results`
					JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`
					JOIN `students` ON `students`.`id` = `exam_results`.`student_id`
					JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`
					JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`
					JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`
					JOIN `strand` ON `strand`.`id` = `students`.`strand_id`
					JOIN `specialized_subjects` ON `specialized_subjects`.`strand_id` = `strand`.`id`
					WHERE `student_session`.`class_id` = '".$class."'
					AND `student_session`.`section_id` = '".$section."'
					AND `exam_results`.`session_id` = '".$this->current_session."'
					AND `subjects`.`kind` = 'child'
					AND `specialized_subjects`.`semester` = '".$semester."'
					AND `specialized_subjects`.`subject_id` <=> `subjects`.`parent_id`
					AND `quarter` IN (".$sql_quarter.")
					GROUP BY `exam_results`.`student_id`, `subjects`.`parent_id`, `exam_results`.`quarter` 
					) as AC  group by `student_id`  ) as AE  group by `student_id` order by final_grade DESC 
				"); 
				

				} else { */
				$result = $this->db->query("SELECT get_final_grade AS `final_grade`
				FROM ( SELECT get_marks AS `get_final_grade`
				FROM `exam_results`
				JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`
				JOIN `students` ON `students`.`id` = `exam_results`.`student_id`
				JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`
				JOIN `strand` ON `strand`.`id` = `student_session`.`strand_id`
				JOIN `specialized_subjects` ON `specialized_subjects`.`strand_id` = `strand`.`id`
				WHERE `quarter` = '".$quarter."'
				AND `exam_results`.`student_id` = '".$student_id."'
				AND `exam_results`.`session_id` = '".$session_id."'
				AND `exam_results`.`subject_id` = '".$subject_id."'
				/*AND `subjects`.`kind` <> 'child'*/
				AND `exam_results`.`subject_id` NOT IN ( SELECT parent_id FROM subjects WHERE `exam_results`.`subject_id` =  `subjects`.`parent_id` AND `subjects`.`kind` = 'child' )
				AND `specialized_subjects`.`semester` = '".$semester."'
				AND `specialized_subjects`.`subject_id` <=> `exam_results`.`subject_id`
				UNION ALL SELECT ( SUM(get_marks) / COUNT(get_marks) ) AS `get_final_grade`
				FROM `exam_results`
				JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`
				JOIN `students` ON `students`.`id` = `exam_results`.`student_id`
				JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`
				JOIN `strand` ON `strand`.`id` = `student_session`.`strand_id`
				JOIN `specialized_subjects` ON `specialized_subjects`.`strand_id` = `strand`.`id`
				WHERE `quarter` = '".$quarter."'
				AND `exam_results`.`student_id` = '".$student_id."'
				AND `exam_results`.`session_id` = '".$session_id."'
				AND `subjects`.`kind` = 'child'
				AND `specialized_subjects`.`semester` = '".$semester."'
				AND `specialized_subjects`.`subject_id` <=> `subjects`.`parent_id`
				GROUP BY `subjects`.`parent_id` ) as grade ");
			//}
		} else {
				/* 	if( $quarter == 4 ){
				$result = $this->db->query("SELECT AVG(get_final_grade)  as final_grade,  AE.`student_id`, AE.`name` as subject_name,  AE.`quarter`,AE.`student_id` as id
				FROM ( SELECT SUM(get_marks) / 4 AS get_final_grade, `exam_results`.`student_id`, `subjects`.`name`, `quarter`
					FROM `exam_results`
					JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`
					JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`
					LEFT JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`
					JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`
					WHERE `student_session`.`class_id` = '".$class."'
					AND `student_session`.`section_id` = '".$section."'
					AND `exam_results`.`session_id` = '".$this->current_session."'
					AND `subjects`.`kind` <> 'child'
					AND `exam_results`.`subject_id` NOT IN ( SELECT parent_id FROM subjects WHERE `exam_results`.`subject_id` =  `subjects`.`parent_id` AND `subjects`.`kind` = 'child' )
					GROUP BY `exam_results`.`student_id`, `exam_results`.`subject_id`
					UNION  ALL
					SELECT SUM(get_final_grade_per_quarter ) / 4 AS get_final_grade, AC.`student_id`, AC.`name` , AC.`quarter`
					FROM ( SELECT (SUM(get_marks) / COUNT(get_marks)) AS `get_final_grade_per_quarter`, `exam_results`.`student_id`, `subjects`.`name`, `subjects`.`id`  as subject_id, `quarter`
					FROM `exam_results`
					JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`
					JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`
					LEFT JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`
					JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`
					WHERE `student_session`.`class_id` = '".$class."'
					AND `student_session`.`section_id` = '".$section."'
					AND `exam_results`.`session_id` = '".$this->current_session."'
					AND `subjects`.`kind` = 'child'
					GROUP BY `exam_results`.`student_id`, `subjects`.`parent_id`, `exam_results`.`quarter` 
					) as AC  group by `student_id`  ) as AE   group by `student_id`  order by final_grade DESC 
				"); 
			} else { */
				$result = $this->db->query("SELECT get_final_grade AS `final_grade`
				FROM ( SELECT get_marks AS `get_final_grade`
				FROM `exam_results`
				JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`
				JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`
				WHERE `quarter` = '".$quarter."'
				AND `exam_results`.`student_id` = '".$student_id."'
				AND `exam_results`.`session_id` = '".$session_id."'
				AND `exam_results`.`subject_id` = '".$subject_id."'
				/*AND `subjects`.`kind` <> 'child'*/
				AND `exam_results`.`subject_id` NOT IN ( SELECT parent_id FROM subjects WHERE `exam_results`.`subject_id` =  `subjects`.`parent_id` AND `subjects`.`kind` = 'child' )
				GROUP BY `exam_results`.`student_id`
				UNION ALL SELECT (SUM(get_marks) / COUNT(get_marks)) AS `get_final_grade`
				FROM `exam_results`
				JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`
				JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`
				WHERE `quarter` = '".$quarter."'
				AND `exam_results`.`student_id` = '".$student_id."'
				AND `exam_results`.`session_id` = '".$session_id."'
				AND `subjects`.`kind` = 'child'
				GROUP BY  `subjects`.`parent_id` ) as grade");
			//}
		}
		
		return $result->row_array(); 	
	}
	

	function getstudentgradestatus( $student_id, $subject_id, $quarter, $grade ){
		$this->db->select('post')->from('import_grades_details');
		$this->db->join('import_grades_batch', 'import_grades_details.batch_id = import_grades_batch.id '); 
        $this->db->where('import_grades_details.student_id', $student_id);
        $this->db->where('import_grades_details.subject_id', $subject_id);
        $this->db->where('import_grades_details.quarter', $quarter);
        $this->db->where( 'ROUND(import_grades_details.grades,2)', $grade);
        $this->db->where('import_grades_batch.session_id', $this->current_session ); 
        $this->db->order_by('post', 'DESC' ); 
        $this->db->limit('1');  
        
        $query = $this->db->get();
        return $query->row_array(); 
	}
	
	function operation( $operation, $x, $y ){
		 switch ( $operation )
		{
			case ">": return $x > $y;
			case "<": return $x < $y;
			case "<=": return $x <= $y;
			case ">=": return $x >= $y;
			case "!=": return $x != $y;
			case "=": return $x == $y;
			case "==": return $x === $y;
			default: return false;
		}
	}
	
	function getStudentAverage( $student_list, $quarter, $semester_id=null, $combine_category, $session_id=null ){
		$gradingsettings = $this->gradingsetting_model->getbySession();
		$decimal_average = isset($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;
		$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:0;
		$allow_to_pass = isset($gradingsettings->allow_to_pass)?$gradingsettings->allow_to_pass:'no';
		if( $student_list ){
			if( $combine_category == 'ga'){
				if( $semester_id ){
					if( $semester_id == '1'){ 
						$get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array();	
						$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
						$total_quarter = count( $get_quarter );
					} else {
						$get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();	
						$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
						$total_quarter = count( $get_quarter );
					}
					if( $quarter == 'final'){
						$x=0;
						foreach( $student_list as $list => $student ){
							$count_subjects_first = 0;
							$count_subjects_second = 0;
							$count_subjects_third = 0;
							$count_subjects_fourth = 0;
							$first = 0;
							$second = 0;
							$third = 0;
							$fourth = 0;
							$total_ave = 0;
							$subject_count = 0;
							$get_final_grade = 0;
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$strand_id = $student['strand_id'];
							$getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id );
							$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester_id );
							if( $custom_subject ){
								$getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
							}
							$average_grade = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$get_final_grade = 0;
									$subject_id = $subjects['subject_id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation'];
									$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											for($y=0;$y<$total_quarter;$y++){
												$set_quarter = $get_quarter[$y];
												$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $set_quarter, $combine_category, $session_id=null );
												/* $grade_per_quarter_with_unit = $getComputedCombinedGrade * $units;
												if($set_quarter==1){ 
													$first = $first + $grade_per_quarter_with_unit;
													$count_subjects_first = $count_subjects_first + $units;
												} elseif($set_quarter==2){ 	
													$second = $second + $grade_per_quarter_with_unit; 
													$count_subjects_second = $count_subjects_second + $units;
												} elseif($set_quarter==3){ 
													$third = $third + $grade_per_quarter_with_unit; 
													$count_subjects_third = $count_subjects_third + $units;
												} elseif($set_quarter==4){
													$fourth = $fourth + $grade_per_quarter_with_unit; 
													$count_subjects_fourth = $count_subjects_fourth + $units;
												} */
												
												$get_final_grade = $get_final_grade + $getComputedCombinedGrade;
											}
										}
									}
								
								
									$final_grade = $get_final_grade / $total_quarter;
									if( $include_computation == 'yes' ){ 
										if( empty( $checkifChild ) ){
											if( $allow_to_pass == 'yes'){
												if( $final_grade == '74.5'){
													$final_grade = '75';
												} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
													$final_grade = '75';
												}
											}
										}
									}
									
									$final_grade =  number_format((float)$final_grade, $decimal_finalgrade, '.', '');
									if( $include_computation == 'yes' ){ 
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id );
										if( empty($checkifChild)){
											$final_grade = $final_grade * $units;
											$total_ave = $total_ave + $final_grade;
											$subject_count = $subject_count + $units;
										}
									}
								}
								/* $first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
								$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
								$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
								$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
								$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
								$second_ave = number_format((float)$second_ave, $decimal_average, '.', '');
								$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
								$fourth_ave = number_format((float)$fourth_ave, $decimal_average, '.', '');
								 
								
								$count_quarter = count( $get_quarter );*/
								$total_ave =   $subject_count != 0 ? $total_ave / $subject_count:0;
								$total_ave =  number_format((float)$total_ave, $decimal_finalgrade, '.', '');
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $total_ave ));
							$x++;
						}
					} else {
						$x=0;
						foreach( $student_list as $list => $student ){
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$student_firstname = $student['firstname'];
							$strand_id = $student['strand_id'];
							$getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id );
							$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester_id );
							if( $custom_subject ){
								$getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
							}
							$average_grade = 0;
							$subject_count = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$subject_id = $subjects['subject_id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation'];
									$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id=null );
											$getComputedCombinedGrade = $getComputedCombinedGrade * $units;
											$average_grade = $getComputedCombinedGrade + $average_grade;
											$subject_count = $subject_count + $units;
										}
									}
								}
								if( $subject_count != 0 ){
									$average_grade = $average_grade / $subject_count;
									$average_grade = number_format( $average_grade, $decimal_average,'.',',');
								}
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $average_grade ));
							$x++;
						}
					}
		
				} else {
					if( $quarter == 'final'){
						$x=0;
						$total_quarter = 4;
						foreach( $student_list as $list => $student ){
							$first = 0;
							$second = 0;
							$third = 0;
							$fourth = 0;
							$count_subjects_first = 0;
							$count_subjects_second = 0;
							$count_subjects_third = 0;
							$count_subjects_fourth = 0;
							$get_final_grade = 0;
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$student_firstname = $student['firstname'];
							$getStudentSubject = $this->subject_model->getSubjctByClass( $class_id );
							$average_grade = 0;
							$subject_count = 0;
							$total_ave = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$get_final_grade = 0;
									$subject_id = $subjects['id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation'];
									if( $include_computation == 'yes' ){
										for( $y=1;$y<=$total_quarter;$y++ ){
											$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
											if( empty( $checkifChild )){
												$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $y, $combine_category, $session_id );
												/* if($y==1){ 
													$first = $first + $getComputedCombinedGrade;
													$getComputedCombinedGrade != null?$count_subjects_first ++:'';
												} elseif($y==2){ 
													$second = $second + $getComputedCombinedGrade; 
													$getComputedCombinedGrade != null?$count_subjects_second++:'';
												} elseif($y==3){ 
													$third = $third + $getComputedCombinedGrade; 
													$getComputedCombinedGrade != null?$count_subjects_third++:'';
												} elseif($y==4){ 
													$fourth = $fourth + $getComputedCombinedGrade;
													$getComputedCombinedGrade != null?$count_subjects_fourth++:'';																			
												} */
											}
											$get_final_grade = $get_final_grade + $getComputedCombinedGrade;
										}
									}
									$final_grade = $get_final_grade / $total_quarter;
									if( $include_computation == 'yes' ){ 
										if( empty( $checkifChild ) ){
											if( $allow_to_pass == 'yes'){
												if( $final_grade == '74.5'){
													$final_grade = '75';
												} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
													$final_grade = '75';
												}
											}
										}
									}
									$final_grade =  number_format((float)$final_grade, $decimal_finalgrade, '.', '');
									if( $include_computation == 'yes' ){ 
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id );
										if( empty($checkifChild  )){
											$total_ave = $total_ave + $final_grade;
											$subject_count++;
										}
									}
								}
								
								/* $first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
								$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
								$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
								$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
								$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
								$second_ave =  number_format((float)$second_ave, $decimal_average, '.', '');
								$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
								$fourth_ave =  number_format((float)$fourth_ave,$decimal_average, '.', '');
								$get_ave = $first_ave + $second_ave + $third_ave + $fourth_ave; 
								$total_ave =  $get_ave / $total_quarter;*/
								$total_ave =   $subject_count != 0 ? $total_ave / $subject_count:0;
								$total_ave =  number_format((float)$total_ave, $decimal_finalgrade, '.', '');
								$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $total_ave ));
							}
							$x++;
						}
					} else {
						$x=0;
						foreach( $student_list as $list => $student ){
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$getStudentSubject = $this->subject_model->getSubjctByClass( $class_id );
							$average_grade = 0;
							$subject_count = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$subject_id = $subjects['id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation'];
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											$subject_count++;
											$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id );
											$average_grade = $getComputedCombinedGrade + $average_grade;
											
										}
									}
								}
								if( $subject_count != 0 ){
									$average_grade = $average_grade / $subject_count;
									$average_grade = number_format( $average_grade, $decimal_average,'.',',');
								}
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $average_grade ));
							$x++;
						}
					}
				
				}
			} elseif( $combine_category == 'gpa'){
				if( $semester_id ){
					if( $semester_id == '1'){ 
						$get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array();	
						$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
						$total_quarter = count( $get_quarter );
					} else {
						$get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();	
						$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
						$total_quarter = count( $get_quarter );
					}
					if( $quarter == 'final'){
						$x=0;
						foreach( $student_list as $list => $student ){
							$count_subjects_first = 0;
							$count_subjects_second = 0;
							$count_subjects_third = 0;
							$count_subjects_fourth = 0;
							$first = 0;
							$second = 0;
							$third = 0;
							$fourth = 0;
							$total_ave = 0;
							$subject_count = 0;
							$get_final_grade = 0;
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$strand_id = $student['strand_id'];
							$getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id );
							$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester_id );
							if( $custom_subject ){
								$getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
							}
							$average_grade = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$get_final_grade = 0;
									$subject_id = $subjects['subject_id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation_gpa'];
									$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											for($y=0;$y<$total_quarter;$y++){
												$set_quarter = $get_quarter[$y];
												$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $set_quarter, $combine_category, $session_id=null );
												/* $grade_per_quarter_with_unit = $getComputedCombinedGrade * $units;
												if($set_quarter==1){ 
													$first = $first + $grade_per_quarter_with_unit;
													$count_subjects_first = $count_subjects_first + $units;
												} elseif($set_quarter==2){ 	
													$second = $second + $grade_per_quarter_with_unit; 
													$count_subjects_second = $count_subjects_second + $units;
												} elseif($set_quarter==3){ 
													$third = $third + $grade_per_quarter_with_unit; 
													$count_subjects_third = $count_subjects_third + $units;
												} elseif($set_quarter==4){
													$fourth = $fourth + $grade_per_quarter_with_unit; 
													$count_subjects_fourth = $count_subjects_fourth + $units;
												} */
												
												$get_final_grade = $get_final_grade + $getComputedCombinedGrade;
											}
										}
									}
								
								
									$final_grade = $get_final_grade / $total_quarter;
									if( $include_computation == 'yes' ){ 
										if( empty( $checkifChild ) ){
											if( $allow_to_pass == 'yes'){
												if( $final_grade == '74.5'){
													$final_grade = '75';
												} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
													$final_grade = '75';
												}
											}
										}
									}
									
									$final_grade =  number_format((float)$final_grade, $decimal_finalgrade, '.', '');
									if( $include_computation == 'yes' ){ 
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id );
										if( empty($checkifChild)){
											$final_grade = $final_grade * $units;
											$total_ave = $total_ave + $final_grade;
											$subject_count = $subject_count + $units;
										}
									}
								}
								/* $first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
								$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
								$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
								$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
								$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
								$second_ave = number_format((float)$second_ave, $decimal_average, '.', '');
								$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
								$fourth_ave = number_format((float)$fourth_ave, $decimal_average, '.', '');
								 
								
								$count_quarter = count( $get_quarter );*/
								$total_ave =   $subject_count != 0 ? $total_ave / $subject_count:0;
								$total_ave =  number_format((float)$total_ave, $decimal_finalgrade, '.', '');
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $total_ave ));
							$x++;
						}
					} else {
						$x=0;
						foreach( $student_list as $list => $student ){
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$strand_id = $student['strand_id'];
							$getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id );
							$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester_id );
							if( $custom_subject ){
								$getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
							}
							$average_grade = 0;
							$subject_count = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$subject_id = $subjects['subject_id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation_gpa'];
									$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id=null );
											$getComputedCombinedGrade = $getComputedCombinedGrade * $units;
											$average_grade = $getComputedCombinedGrade + $average_grade;
											$subject_count = $subject_count + $units;
										}
									}
								}
								if( $subject_count != 0 ){
									$average_grade = $average_grade / $subject_count;
									$average_grade = number_format( $average_grade, $decimal_average,'.',',');
								}
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $average_grade ));
							$x++;
						}
					}
		
				} else {
					if( $quarter == 'final'){
						$x=0;
						$total_quarter = 4;
						foreach( $student_list as $list => $student ){
							$first = 0;
							$second = 0;
							$third = 0;
							$fourth = 0;
							$count_subjects_first = 0;
							$count_subjects_second = 0;
							$count_subjects_third = 0;
							$count_subjects_fourth = 0;
							$get_final_grade = 0;
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$getStudentSubject = $this->subject_model->getSubjctByClass( $class_id );
							$average_grade = 0;
							$subject_count = 0;
							$total_ave = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$get_final_grade = 0;
									$subject_id = $subjects['id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation_gpa'];
									if( $include_computation == 'yes' ){
										for( $y=1;$y<=$total_quarter;$y++ ){
											$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
											if( empty( $checkifChild )){
												$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $y, $combine_category, $session_id );
												/* if($y==1){ 
													$first = $first + $getComputedCombinedGrade;
													$getComputedCombinedGrade != null?$count_subjects_first ++:'';
												} elseif($y==2){ 
													$second = $second + $getComputedCombinedGrade; 
													$getComputedCombinedGrade != null?$count_subjects_second++:'';
												} elseif($y==3){ 
													$third = $third + $getComputedCombinedGrade; 
													$getComputedCombinedGrade != null?$count_subjects_third++:'';
												} elseif($y==4){ 
													$fourth = $fourth + $getComputedCombinedGrade;
													$getComputedCombinedGrade != null?$count_subjects_fourth++:'';																			
												} */
											}
											$get_final_grade = $get_final_grade + $getComputedCombinedGrade;
										}
									}
									$final_grade = $get_final_grade / $total_quarter;
									if( $include_computation == 'yes' ){ 
										if( empty( $checkifChild ) ){
											if( $allow_to_pass == 'yes'){
												if( $final_grade == '74.5'){
													$final_grade = '75';
												} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
													$final_grade = '75';
												}
											}
										}
									}
									
									$final_grade =  number_format((float)$final_grade, $decimal_finalgrade, '.', '');
									if( $include_computation == 'yes' ){ 
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id );
										if( empty($checkifChild  )){
											$total_ave = $total_ave + $final_grade;
											$subject_count++;
										}
									}
								}
								
								/* $first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
								$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
								$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
								$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
								$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
								$second_ave =  number_format((float)$second_ave, $decimal_average, '.', '');
								$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
								$fourth_ave =  number_format((float)$fourth_ave,$decimal_average, '.', '');
								$get_ave = $first_ave + $second_ave + $third_ave + $fourth_ave; 
								$total_ave =  $get_ave / $total_quarter;*/
								$total_ave =   $subject_count != 0 ? $total_ave / $subject_count:0;
								$total_ave =  number_format((float)$total_ave, $decimal_finalgrade, '.', '');
								$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $total_ave ));
							}
							$x++;
						}
					} else {
						$x=0;
						foreach( $student_list as $list => $student ){
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$getStudentSubject = $this->subject_model->getSubjctByClass( $class_id );
							$average_grade = 0;
							$subject_count = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$subject_id = $subjects['id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation_gpa'];
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											$subject_count++;
											$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id );
											$average_grade = $getComputedCombinedGrade + $average_grade;
											
										}
									}
								}
								if( $subject_count != 0 ){
									$average_grade = $average_grade / $subject_count;
									$average_grade = number_format( $average_grade, $decimal_average,'.',',');
								}
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $average_grade ));
							$x++;
						}
					}
				
				}
			}
			$student_list = $this->orderBy( $student_list, 'final_grade');
		}
		$student_list['quarter'] = $quarter;
		$student_list['semester'] = $semester_id;
		
		return $student_list;
	}
	
		

	function getStudentAveragemma( $student_list, $quarter, $semester_id=null, $combine_category, $session_id=null ){
		$gradingsettings = $this->gradingsetting_model->getbySession();
		$decimal_average = isset($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;
		$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:0;
		$allow_to_pass = isset($gradingsettings->allow_to_pass)?$gradingsettings->allow_to_pass:'no';
		
		if( $student_list ){
			if( $combine_category == 'ga'){
				if( $semester_id ){
					if( $semester_id == '1'){ 
						$get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array();	
						$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
						$total_quarter = count( $get_quarter );
					} else {
						$get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();	
						$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
						$total_quarter = count( $get_quarter );
					}
					if( $quarter == 'final'){
						$x=0;
						foreach( $student_list as $list => $student ){
							$count_subjects_first = 0;
							$count_subjects_second = 0;
							$count_subjects_third = 0;
							$count_subjects_fourth = 0;
							$first = 0;
							$second = 0;
							$third = 0;
							$fourth = 0;
							$total_ave = 0;
							$subject_count = 0;
							$get_final_grade = 0;
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$strand_id = $student['strand_id'];
							$getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id );
							$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester_id );
							if( $custom_subject ){
								$getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
							}
							$average_grade = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$get_final_grade = 0;
									$subject_id = $subjects['subject_id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation'];
									$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											for($y=0;$y<$total_quarter;$y++){
												$set_quarter = $get_quarter[$y];
												$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $set_quarter, $combine_category, $session_id=null );
												/* $grade_per_quarter_with_unit = $getComputedCombinedGrade * $units;
												if($set_quarter==1){ 
													$first = $first + $grade_per_quarter_with_unit;
													$count_subjects_first = $count_subjects_first + $units;
												} elseif($set_quarter==2){ 	
													$second = $second + $grade_per_quarter_with_unit; 
													$count_subjects_second = $count_subjects_second + $units;
												} elseif($set_quarter==3){ 
													$third = $third + $grade_per_quarter_with_unit; 
													$count_subjects_third = $count_subjects_third + $units;
												} elseif($set_quarter==4){
													$fourth = $fourth + $grade_per_quarter_with_unit; 
													$count_subjects_fourth = $count_subjects_fourth + $units;
												} */
												
												$get_final_grade = $get_final_grade + $getComputedCombinedGrade;
											}
										}
									}
								
								
									$final_grade = $get_final_grade / $total_quarter;
									if( $include_computation == 'yes' ){ 
										if( empty( $checkifChild ) ){
											if( $allow_to_pass == 'yes'){
												if( $final_grade == '74.5'){
													$final_grade = '75';
												} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
													$final_grade = '75';
												}
											}
										}
									}
									
									$final_grade =  number_format((float)$final_grade, $decimal_finalgrade, '.', '');
									if( $include_computation == 'yes' ){ 
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id );
										if( empty($checkifChild)){
											$final_grade = $final_grade * $units;
											$total_ave = $total_ave + $final_grade;
											$subject_count = $subject_count + $units;
										}
									}
								}
								/* $first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
								$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
								$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
								$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
								$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
								$second_ave = number_format((float)$second_ave, $decimal_average, '.', '');
								$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
								$fourth_ave = number_format((float)$fourth_ave, $decimal_average, '.', '');
								 
								
								$count_quarter = count( $get_quarter );*/
								$total_ave =   $subject_count != 0 ? $total_ave / $subject_count:0;
								$total_ave =  number_format((float)$total_ave, $decimal_finalgrade, '.', '');
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $total_ave ));
							$x++;
						}
					} else {
						$x=0;
						foreach( $student_list as $list => $student ){
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$strand_id = $student['strand_id'];
							$getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id );
							$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester_id );
							if( $custom_subject ){
								$getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
							}
							$average_grade = 0;
							$subject_count = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$subject_id = $subjects['subject_id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation'];
									$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id=null );
											$getComputedCombinedGrade = $getComputedCombinedGrade * $units;
											$average_grade = $getComputedCombinedGrade + $average_grade;
											$subject_count = $subject_count + $units;
										}
									}
								}
								if( $subject_count != 0 ){
									$average_grade = $average_grade / $subject_count;
									$average_grade = number_format( $average_grade, $decimal_average,'.',',');
								}
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $average_grade ));
							$x++;
						}
					}
		
				} else {
					if( $quarter == 'final'){
						$x=0;
						$total_quarter = 4;
						foreach( $student_list as $list => $student ){
							$first = 0;
							$second = 0;
							$third = 0;
							$fourth = 0;
							$count_subjects_first = 0;
							$count_subjects_second = 0;
							$count_subjects_third = 0;
							$count_subjects_fourth = 0;
							$get_final_grade = 0;
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$getStudentSubject = $this->subject_model->getSubjctByClass( $class_id );
							$average_grade = 0;
							$subject_count = 0;
							$total_ave = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$get_final_grade = 0;
									$subject_id = $subjects['id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation'];
									if( $include_computation == 'yes' ){
										for( $y=1;$y<=$total_quarter;$y++ ){
											$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
											if( empty( $checkifChild )){
												$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $y, $combine_category, $session_id );
												/* if($y==1){ 
													$first = $first + $getComputedCombinedGrade;
													$getComputedCombinedGrade != null?$count_subjects_first ++:'';
												} elseif($y==2){ 
													$second = $second + $getComputedCombinedGrade; 
													$getComputedCombinedGrade != null?$count_subjects_second++:'';
												} elseif($y==3){ 
													$third = $third + $getComputedCombinedGrade; 
													$getComputedCombinedGrade != null?$count_subjects_third++:'';
												} elseif($y==4){ 
													$fourth = $fourth + $getComputedCombinedGrade;
													$getComputedCombinedGrade != null?$count_subjects_fourth++:'';																			
												} */
											}
											$get_final_grade = $get_final_grade + $getComputedCombinedGrade;
										}
									}
									$final_grade = $get_final_grade / $total_quarter;
									if( $include_computation == 'yes' ){ 
										if( empty( $checkifChild ) ){
											if( $allow_to_pass == 'yes'){
												if( $final_grade == '74.5'){
													$final_grade = '75';
												} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
													$final_grade = '75';
												}
											}
										}
									}
									
									$final_grade =  number_format((float)$final_grade, $decimal_finalgrade, '.', '');
									if( $include_computation == 'yes' ){ 
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id );
										if( empty($checkifChild  )){
											$total_ave = $total_ave + $final_grade;
											$subject_count++;
										}
									}
								}
								
								/* $first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
								$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
								$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
								$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
								$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
								$second_ave =  number_format((float)$second_ave, $decimal_average, '.', '');
								$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
								$fourth_ave =  number_format((float)$fourth_ave,$decimal_average, '.', '');
								$get_ave = $first_ave + $second_ave + $third_ave + $fourth_ave; 
								$total_ave =  $get_ave / $total_quarter;*/
								$total_ave =   $subject_count != 0 ? $total_ave / $subject_count:0;
								$total_ave =  number_format((float)$total_ave, $decimal_finalgrade, '.', '');
								$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $total_ave ));
							}
							$x++;
						}
					} else {
						$x=0;
						foreach( $student_list as $list => $student ){
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$getStudentSubject = $this->subject_model->getSubjctByClass( $class_id );
							$average_grade = 0;
							$subject_count = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$subject_id = $subjects['id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation'];
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											$subject_count++;
											$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id );
											$average_grade = $getComputedCombinedGrade + $average_grade;
											
										}
									}
								}
								if( $subject_count != 0 ){
									$average_grade = $average_grade / $subject_count;
									$average_grade = number_format( $average_grade, $decimal_average,'.',',');
								}
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $average_grade ));
							$x++;
						}
					}
				
				}
			} elseif( $combine_category == 'gpa'){
				if( $semester_id ){
					if( $semester_id == '1'){ 
						$get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array();	
						$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
						$total_quarter = count( $get_quarter );
					} else {
						$get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();	
						$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
						$total_quarter = count( $get_quarter );
					}
					if( $quarter == 'final'){
						$x=0;
						foreach( $student_list as $list => $student ){
							$count_subjects_first = 0;
							$count_subjects_second = 0;
							$count_subjects_third = 0;
							$count_subjects_fourth = 0;
							$first = 0;
							$second = 0;
							$third = 0;
							$fourth = 0;
							$total_ave = 0;
							$subject_count = 0;
							$get_final_grade = 0;
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$strand_id = $student['strand_id'];
							$getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id );
							$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester_id );
							if( $custom_subject ){
								$getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
							}
							$average_grade = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$get_final_grade = 0;
									$subject_id = $subjects['subject_id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation_gpa'];
									$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											for($y=0;$y<$total_quarter;$y++){
												$set_quarter = $get_quarter[$y];
												$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $set_quarter, $combine_category, $session_id=null );
												/* $grade_per_quarter_with_unit = $getComputedCombinedGrade * $units;
												if($set_quarter==1){ 
													$first = $first + $grade_per_quarter_with_unit;
													$count_subjects_first = $count_subjects_first + $units;
												} elseif($set_quarter==2){ 	
													$second = $second + $grade_per_quarter_with_unit; 
													$count_subjects_second = $count_subjects_second + $units;
												} elseif($set_quarter==3){ 
													$third = $third + $grade_per_quarter_with_unit; 
													$count_subjects_third = $count_subjects_third + $units;
												} elseif($set_quarter==4){
													$fourth = $fourth + $grade_per_quarter_with_unit; 
													$count_subjects_fourth = $count_subjects_fourth + $units;
												} */
												
												$get_final_grade = $get_final_grade + $getComputedCombinedGrade;
											}
										}
									}
								
								
									$final_grade = $get_final_grade / $total_quarter;
									if( $include_computation == 'yes' ){ 
										if( empty( $checkifChild ) ){
											if( $allow_to_pass == 'yes'){
												if( $final_grade == '74.5'){
													$final_grade = '75';
												} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
													$final_grade = '75';
												}
											}
										}
									}
									
									$final_grade =  number_format((float)$final_grade, $decimal_finalgrade, '.', '');
									if( $include_computation == 'yes' ){ 
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id );
										if( empty($checkifChild)){
											$final_grade = $final_grade * $units;
											$total_ave = $total_ave + $final_grade;
											$subject_count = $subject_count + $units;
										}
									}
								}
								/* $first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
								$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
								$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
								$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
								$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
								$second_ave = number_format((float)$second_ave, $decimal_average, '.', '');
								$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
								$fourth_ave = number_format((float)$fourth_ave, $decimal_average, '.', '');
								 
								
								$count_quarter = count( $get_quarter );*/
								$total_ave =   $subject_count != 0 ? $total_ave / $subject_count:0;
								$total_ave =  number_format((float)$total_ave, $decimal_finalgrade, '.', '');
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $total_ave ));
							$x++;
						}
					} else {
						$x=0;
						foreach( $student_list as $list => $student ){
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$strand_id = $student['strand_id'];
							$getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id );
							$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester_id );
							if( $custom_subject ){
								$getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
							}
							$average_grade = 0;
							$subject_count = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$subject_id = $subjects['subject_id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation_gpa'];
									$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id=null );
											$getComputedCombinedGrade = $getComputedCombinedGrade * $units;
											$average_grade = $getComputedCombinedGrade + $average_grade;
											$subject_count = $subject_count + $units;
										}
									}
								}
								if( $subject_count != 0 ){
									$average_grade = $average_grade / $subject_count;
									$average_grade = number_format( $average_grade, $decimal_average,'.',',');
								}
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $average_grade ));
							$x++;
						}
					}
		
				} else {
					if( $quarter == 'final'){
						$x=0;
						$total_quarter = 4;
						foreach( $student_list as $list => $student ){
							$first = 0;
							$second = 0;
							$third = 0;
							$fourth = 0;
							$count_subjects_first = 0;
							$count_subjects_second = 0;
							$count_subjects_third = 0;
							$count_subjects_fourth = 0;
							$get_final_grade = 0;
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$getStudentSubject = $this->subject_model->getSubjctByClass( $class_id );
							$average_grade = 0;
							$subject_count = 0;
							$total_ave = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$get_final_grade = 0;
									$subject_id = $subjects['id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation_gpa'];
									if( $include_computation == 'yes' ){
										for( $y=1;$y<=$total_quarter;$y++ ){
											$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
											if( empty( $checkifChild )){
												$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $y, $combine_category, $session_id );
												/* if($y==1){ 
													$first = $first + $getComputedCombinedGrade;
													$getComputedCombinedGrade != null?$count_subjects_first ++:'';
												} elseif($y==2){ 
													$second = $second + $getComputedCombinedGrade; 
													$getComputedCombinedGrade != null?$count_subjects_second++:'';
												} elseif($y==3){ 
													$third = $third + $getComputedCombinedGrade; 
													$getComputedCombinedGrade != null?$count_subjects_third++:'';
												} elseif($y==4){ 
													$fourth = $fourth + $getComputedCombinedGrade;
													$getComputedCombinedGrade != null?$count_subjects_fourth++:'';																			
												} */
											}
											$get_final_grade = $get_final_grade + $getComputedCombinedGrade;
										}
									}
									$final_grade = $get_final_grade / $total_quarter;
									if( $include_computation == 'yes' ){ 
										if( empty( $checkifChild ) ){
											if( $allow_to_pass == 'yes'){
												if( $final_grade == '74.5'){
													$final_grade = '75';
												} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
													$final_grade = '75';
												}
											}
										}
									}
									
									$final_grade =  number_format((float)$final_grade, $decimal_finalgrade, '.', '');
									if( $include_computation == 'yes' ){ 
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id );
										if( empty($checkifChild  )){
											$total_ave = $total_ave + $final_grade;
											$subject_count++;
										}
									}
								}
								
								/* $first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
								$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
								$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
								$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
								$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
								$second_ave =  number_format((float)$second_ave, $decimal_average, '.', '');
								$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
								$fourth_ave =  number_format((float)$fourth_ave,$decimal_average, '.', '');
								$get_ave = $first_ave + $second_ave + $third_ave + $fourth_ave; 
								$total_ave =  $get_ave / $total_quarter;*/
								$total_ave =   $subject_count != 0 ? $total_ave / $subject_count:0;
								$total_ave =  number_format((float)$total_ave, $decimal_finalgrade, '.', '');
								$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $total_ave ));
							}
							$x++;
						}
					} else {
						$x=0;
						foreach( $student_list as $list => $student ){
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$getStudentSubject = $this->subject_model->getSubjctByClass( $class_id );
							$average_grade = 0;
							$subject_count = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$subject_id = $subjects['id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation_gpa'];
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											$subject_count++;
											$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id );
											$average_grade = $getComputedCombinedGrade + $average_grade;
											
										}
									}
								}
								if( $subject_count != 0 ){
									$average_grade = $average_grade / $subject_count;
									$average_grade = number_format( $average_grade, $decimal_average,'.',',');
								}
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $average_grade ));
							$x++;
						}
					}
				
				}
			}
			$student_list = $this->orderBy( $student_list, 'final_grade');
		}
		$student_list['quarter'] = $quarter;
		$student_list['semester'] = $semester_id;
		
		return $student_list;
	}
	

	
/* 	function getStudentAverage( $student_list, $quarter, $semester_id=null, $combine_category, $session_id=null ){
		$gradingsettings = $this->gradingsetting_model->getbySession();
		$decimal_average = isset($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;
		$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:0;
		
		if( $student_list ){
			if( $combine_category == 'ga'){
				if( $semester_id ){
					if( $semester_id == '1'){ 
						$get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array();	
						$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
						$total_quarter = count( $get_quarter );
					} else {
						$get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();	
						$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
						$total_quarter = count( $get_quarter );
					}
					if( $quarter == 'final'){
						$x=0;
						foreach( $student_list as $list => $student ){
							$count_subjects_first = 0;
							$count_subjects_second = 0;
							$count_subjects_third = 0;
							$count_subjects_fourth = 0;
							$first = 0;
							$second = 0;
							$third = 0;
							$fourth = 0;
							$total_ave = 0;
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$strand_id = $student['strand_id'];
							$getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id );
							$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester_id );
							if( $custom_subject ){
								$getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
							}
							$average_grade = 0;
							$subject_count = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$subject_id = $subjects['subject_id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation'];
									$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											for($y=0;$y<$total_quarter;$y++){
												$set_quarter = $get_quarter[$y];
												$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $set_quarter, $combine_category, $session_id=null );
												$grade_per_quarter_with_unit = $getComputedCombinedGrade * $units;
												if($set_quarter==1){ 
													$first = $first + $grade_per_quarter_with_unit;
													$count_subjects_first = $count_subjects_first + $units;
												} elseif($set_quarter==2){ 	
													$second = $second + $grade_per_quarter_with_unit; 
													$count_subjects_second = $count_subjects_second + $units;
												} elseif($set_quarter==3){ 
													$third = $third + $grade_per_quarter_with_unit; 
													$count_subjects_third = $count_subjects_third + $units;
												} elseif($set_quarter==4){
													$fourth = $fourth + $grade_per_quarter_with_unit; 
													$count_subjects_fourth = $count_subjects_fourth + $units;
												}
											}
										}
									}
								}
								$first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
								$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
								$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
								$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
								$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
								$second_ave = number_format((float)$second_ave, $decimal_average, '.', '');
								$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
								$fourth_ave = number_format((float)$fourth_ave, $decimal_average, '.', '');
								
								$total_average = 0;
								$complete_average = 0;
								if( $get_quarter ){
									foreach( $get_quarter as $key => $value ){
										if($value==1){ 
											if( $first_ave != 0  ){
												$complete_average++;
											}
											$total_average = $total_average + $first_ave;
											
										} elseif($value==2){ 	
											if( $second_ave != 0  ){
												$complete_average++;
											}																
											$total_average = $total_average + $second_ave;
										} elseif($value==3){
											if( $third_ave != 0  ){
												$complete_average++;
											}
											$total_average = $total_average + $third_ave;
										} elseif($value==4){
											if( $fourth_ave != 0  ){
												$complete_average++;
											}
											$total_average = $total_average + $fourth_ave;
										}
									}
								}
								
								$count_quarter = count( $get_quarter );
								$total_ave =   $count_quarter != 0 ? $total_average / $count_quarter:0;
								$final_grade =  number_format((float)$total_ave, $decimal_finalgrade, '.', '');
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $final_grade ));
							$x++;
						}
					} else {
						$x=0;
						foreach( $student_list as $list => $student ){
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$strand_id = $student['strand_id'];
							$getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id );
							$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester_id );
							if( $custom_subject ){
								$getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
							}
							$average_grade = 0;
							$subject_count = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$subject_id = $subjects['subject_id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation'];
									$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id=null );
											$getComputedCombinedGrade = $getComputedCombinedGrade * $units;
											$average_grade = $getComputedCombinedGrade + $average_grade;
											$subject_count = $subject_count + $units;
										}
									}
								}
								if( $subject_count != 0 ){
									$average_grade = $average_grade / $subject_count;
									$average_grade = number_format( $average_grade, $decimal_average,'.',',');
								}
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $average_grade ));
							$x++;
						}
					}
		
				} else {
					if( $quarter == 'final'){
						$x=0;
						$total_quarter = 4;
						foreach( $student_list as $list => $student ){
							$first = 0;
							$second = 0;
							$third = 0;
							$fourth = 0;
							$count_subjects_first = 0;
							$count_subjects_second = 0;
							$count_subjects_third = 0;
							$count_subjects_fourth = 0;
							$total_ave = 0;
							$get_final_grade = 0;
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$getStudentSubject = $this->subject_model->getSubjctByClass( $class_id );
							$average_grade = 0;
							$subject_count = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$subject_id = $subjects['id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation'];
									if( $include_computation == 'yes' ){
										for( $y=1;$y<=$total_quarter;$y++ ){
											$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
											if( empty( $checkifChild )){
												$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $y, $combine_category, $session_id );
												if($y==1){ 
													$first = $first + $getComputedCombinedGrade;
													$getComputedCombinedGrade != null?$count_subjects_first ++:'';
												} elseif($y==2){ 
													$second = $second + $getComputedCombinedGrade; 
													$getComputedCombinedGrade != null?$count_subjects_second++:'';
												} elseif($y==3){ 
													$third = $third + $getComputedCombinedGrade; 
													$getComputedCombinedGrade != null?$count_subjects_third++:'';
												} elseif($y==4){ 
													$fourth = $fourth + $getComputedCombinedGrade;
													$getComputedCombinedGrade != null?$count_subjects_fourth++:'';																			
												}
											}
										}
									}
								}
								
								$first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
								$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
								$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
								$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
								$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
								$second_ave =  number_format((float)$second_ave, $decimal_average, '.', '');
								$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
								$fourth_ave =  number_format((float)$fourth_ave,$decimal_average, '.', '');
								$get_ave = $first_ave + $second_ave + $third_ave + $fourth_ave;
								$total_ave =  $get_ave / $total_quarter;
								$get_final_grade = number_format((float)$total_ave, $decimal_average, '.', '');
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $get_final_grade ));
							$x++;
						}
					} else {
						$x=0;
						foreach( $student_list as $list => $student ){
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$getStudentSubject = $this->subject_model->getSubjctByClass( $class_id );
							$average_grade = 0;
							$subject_count = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$subject_id = $subjects['id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation'];
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											$subject_count++;
											$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id );
											$average_grade = $getComputedCombinedGrade + $average_grade;
											
										}
									}
								}
								if( $subject_count != 0 ){
									$average_grade = $average_grade / $subject_count;
									$average_grade = number_format( $average_grade, $decimal_average,'.',',');
								}
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $average_grade ));
							$x++;
						}
					}
				
				}
			} elseif( $combine_category == 'gpa'){
			if( $semester_id ){
					if( $semester_id == '1'){ 
						$get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array();	
						$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
						$total_quarter = count( $get_quarter );
					} else {
						$get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();	
						$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
						$total_quarter = count( $get_quarter );
					}
					if( $quarter == 'final'){
						$x=0;
						foreach( $student_list as $list => $student ){
							$count_subjects_first = 0;
							$count_subjects_second = 0;
							$count_subjects_third = 0;
							$count_subjects_fourth = 0;
							$first = 0;
							$second = 0;
							$third = 0;
							$fourth = 0;
							$total_ave = 0;
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$strand_id = $student['strand_id'];
							$getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id );
							$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester_id );
							if( $custom_subject ){
								$getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
							}
							$average_grade = 0;
							$subject_count = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$subject_id = $subjects['subject_id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation_gpa'];
									$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											for($y=0;$y<$total_quarter;$y++){
												$set_quarter = $get_quarter[$y];
												$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $set_quarter, $combine_category, $session_id=null );
												$grade_per_quarter_with_unit = $getComputedCombinedGrade * $units;
												if($set_quarter==1){ 
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
									}
								}
								$first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
								$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
								$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
								$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
								$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
								$second_ave =  number_format((float)$second_ave, $decimal_average, '.', '');
								$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
								$fourth_ave =  number_format((float)$fourth_ave, $decimal_average, '.', '');
								
								$total_average = 0;
								$complete_average = 0;
								if( $get_quarter ){
									foreach( $get_quarter as $key => $value ){
										if($value==1){ 
											if( $first_ave != 0  ){
												$complete_average++;
											}
											$total_average = $total_average + $first_ave;
											
										} elseif($value==2){ 	
											if( $second_ave != 0  ){
												$complete_average++;
											}																
											$total_average = $total_average + $second_ave;
										} elseif($value==3){
											if( $third_ave != 0  ){
												$complete_average++;
											}
											$total_average = $total_average + $third_ave;
										} elseif($value==4){
											if( $fourth_ave != 0  ){
												$complete_average++;
											}
											$total_average = $total_average + $fourth_ave;
										}
									}
								}
								
								$count_quarter = count( $get_quarter );
								$total_ave =   $count_quarter != 0 ? $total_average / $count_quarter:0;
								$final_grade =  number_format((float)$total_ave, $decimal_finalgrade, '.', '');
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $total_ave ));
							$x++;
						}
					} else {
						$x=0;
						foreach( $student_list as $list => $student ){
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$strand_id = $student['strand_id'];
							$getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id );
							$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester_id );
							if( $custom_subject ){
								$getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
							}
							$average_grade = 0;
							$subject_count = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$subject_id = $subjects['subject_id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation_gpa'];
									$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id=null );
											$getComputedCombinedGrade = $getComputedCombinedGrade * $units;
											$average_grade = $getComputedCombinedGrade + $average_grade;
											$subject_count = $subject_count + $units;
										}
									}
								}
								if( $subject_count != 0 ){
									$average_grade = $average_grade / $subject_count;
									$average_grade = number_format( $average_grade, $decimal_average,'.',',');
								}
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $average_grade ));
							$x++;
						}
					}
		
				} else {
					if( $quarter == 'final'){
						$x=0;
						$total_quarter = 4;
						foreach( $student_list as $list => $student ){
							$first = 0;
							$second = 0;
							$third = 0;
							$fourth = 0;
							$count_subjects_first = 0;
							$count_subjects_second = 0;
							$count_subjects_third = 0;
							$count_subjects_fourth = 0;
							$total_ave = 0;
							$get_final_grade = 0;
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$getStudentSubject = $this->subject_model->getSubjctByClass( $class_id );
							$average_grade = 0;
							$subject_count = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$subject_id = $subjects['id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation_gpa'];
									if( $include_computation == 'yes' ){
										for( $y=1;$y<=$total_quarter;$y++ ){
											$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
											if( empty( $checkifChild )){
												$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $y, $combine_category, $session_id );
												if($y==1){ 
													$first = $first + $getComputedCombinedGrade;
													$getComputedCombinedGrade != null?$count_subjects_first ++:'';
												} elseif($y==2){ 
													$second = $second + $getComputedCombinedGrade; 
													$getComputedCombinedGrade != null?$count_subjects_second++:'';
												} elseif($y==3){ 
													$third = $third + $getComputedCombinedGrade; 
													$getComputedCombinedGrade != null?$count_subjects_third++:'';
												} elseif($y==4){ 
													$fourth = $fourth + $getComputedCombinedGrade;
													$getComputedCombinedGrade != null?$count_subjects_fourth++:'';																			
												}
											}
										}
									}
								}
								
								$first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
								$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
								$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
								$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
								$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
								$second_ave =  number_format((float)$second_ave, $decimal_average, '.', '');
								$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
								$fourth_ave =  number_format((float)$fourth_ave,$decimal_average, '.', '');
								$get_ave = $first_ave + $second_ave + $third_ave + $fourth_ave;
								$total_ave =  $get_ave / $total_quarter;
								$get_final_grade = number_format((float)$total_ave, $decimal_finalgrade, '.', '');
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $get_final_grade ));
							$x++;
						}
					} else {
						$x=0;
						foreach( $student_list as $list => $student ){
							$class_id = $student['class_id'];
							$student_id = $student['id'];
							$getStudentSubject = $this->subject_model->getSubjctByClass( $class_id );
							$average_grade = 0;
							$subject_count = 0;
							if( $getStudentSubject){
								foreach( $getStudentSubject as $listsubject => $subjects ){
									$subject_id = $subjects['id'];
									$subject_name = $subjects['name'];
									$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
									$include_computation = $subject_manager_details['include_computation_gpa'];
									if( $include_computation == 'yes' ){
										$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
										if( empty( $checkifChild )){
											$subject_count++;
											$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id );
											$average_grade = $getComputedCombinedGrade + $average_grade;
											
										}
									}
								}
								if( $subject_count != 0 ){
									$average_grade = $average_grade / $subject_count;
									$average_grade = number_format( $average_grade, $decimal_average,'.',',');
								}
							}
							$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $average_grade ));
							$x++;
						}
					}
				
				}
			}
			$student_list = $this->orderBy( $student_list, 'final_grade');
		}
		$student_list['quarter'] = $quarter;
		$student_list['semester'] = $semester_id;
		return $student_list;
	}
	 */
	
	
	function orderBy($data, $field)
	{
		/* usort($data, function($a, $b) {
			return $a['final_grade'] - $b['final_grade'];
		});
		$data = array_reverse($data); */
		$sort = array();
		foreach($data as $k=>$v) {
			$sort[$field][$k] = $v[$field];
		}

		array_multisort($sort[$field], SORT_DESC, $data);

		return $data;
	}
	

	//added by gels
	function getG12FinalAvg( $student_list, $quarter, $semester_id=null, $combine_category, $session_id=null ){
		$this->load->model('studentstrand_model');
		$gradingsettings = $this->gradingsetting_model->getbySession(19);
		$decimal_average = isset($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;
		$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:0;
		$allow_to_pass = isset($gradingsettings->allow_to_pass)?$gradingsettings->allow_to_pass:'no';
		if( $student_list ){
			$semester_ids = array('1', '2');
			foreach ($semester_ids as $semester_id) {
				if( $semester_id == '1'){ 
					$get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array();	
					$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
					$total_quarter = count( $get_quarter );
	
					$x=0;
					foreach( $student_list as $list => $student ){
						$count_subjects_first = 0;
						$count_subjects_second = 0;
						$count_subjects_third = 0;
						$count_subjects_fourth = 0;
						$first = 0;
						$second = 0;
						$third = 0;
						$fourth = 0;
						$first_sem_total_ave = 0;
						$subject_count = 0;
						$get_final_grade = 0;
						$student_id = $student['id'];
						$strand_id = $student['strand_id'];
						$check_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id, 19 );
						if( $check_strand ){
							$strand_id = $check_strand['strand_id'];
						} 
						
						$getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id, 19 );
						$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester_id, 19 );
						if( $custom_subject ){
							$getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
						}
						$getStudentSubject = $this->customsubject_model->unsetsubjectdrop( $getStudentSubject, $student_id, $semester_id, 19 );	
						
						$average_grade = 0;
						if( $getStudentSubject){
							foreach( $getStudentSubject as $listsubject => $subjects ){
								$get_final_grade = 0;
								$subject_id = $subjects['subject_id'];
								$subject_name = $subjects['name'];
								$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, 19 );
								$include_computation = $subject_manager_details['include_computation'];
								$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
								if( $include_computation == 'yes' ){
									// $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
									$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, 19 );
									if( empty( $checkifChild )){
										for($y=0;$y<$total_quarter;$y++){
											$set_quarter = $get_quarter[$y];
											// $getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $set_quarter, $combine_category, $session_id=null );
											$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $set_quarter, $combine_category, 19 );
											/* $grade_per_quarter_with_unit = $getComputedCombinedGrade * $units;
											if($set_quarter==1){ 
												$first = $first + $grade_per_quarter_with_unit;
												$count_subjects_first = $count_subjects_first + $units;
											} elseif($set_quarter==2){ 	
												$second = $second + $grade_per_quarter_with_unit; 
												$count_subjects_second = $count_subjects_second + $units;
											} elseif($set_quarter==3){ 
												$third = $third + $grade_per_quarter_with_unit; 
												$count_subjects_third = $count_subjects_third + $units;
											} elseif($set_quarter==4){
												$fourth = $fourth + $grade_per_quarter_with_unit; 
												$count_subjects_fourth = $count_subjects_fourth + $units;
											} */
											
											$get_final_grade = $get_final_grade + $getComputedCombinedGrade;
										}
									}
								}
							
							
								$final_grade = $get_final_grade / $total_quarter;
								if( $include_computation == 'yes' ){ 
									if( empty( $checkifChild ) ){
										if( $allow_to_pass == 'yes'){
											if( $final_grade == '74.5'){
												$final_grade = '75';
											} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
												$final_grade = '75';
											}
										}
									}
								}
								
								$final_grade =  number_format((float)$final_grade, $decimal_finalgrade, '.', '');
								if( $include_computation == 'yes' ){ 
									$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, 'ga', 19 );
									if( empty($checkifChild)){
										$final_grade = $final_grade * $units;
										$first_sem_total_ave = $first_sem_total_ave + $final_grade;
										$subject_count = $subject_count + $units;
									}
								}
							}
							/* $first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
							$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
							$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
							$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
							$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
							$second_ave = number_format((float)$second_ave, $decimal_average, '.', '');
							$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
							$fourth_ave = number_format((float)$fourth_ave, $decimal_average, '.', '');
							
							
							$count_quarter = count( $get_quarter );*/
							$first_sem_total_ave =   $subject_count != 0 ? $first_sem_total_ave / $subject_count:0;
							$first_sem_total_ave =  number_format((float)$first_sem_total_ave, $decimal_average, '.', '');
						}
						$student_list[$x] = array_merge( $student_list[$x], array( 'first_sem_final_grade' => $first_sem_total_ave ));
						$x++;
					}
				} else {
					$get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();	
					$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
					$total_quarter = count( $get_quarter );
	
					$x=0;
					foreach( $student_list as $list => $student ){
						$count_subjects_first = 0;
						$count_subjects_second = 0;
						$count_subjects_third = 0;
						$count_subjects_fourth = 0;
						$first = 0;
						$second = 0;
						$third = 0;
						$fourth = 0;
						$second_sem_total_ave = 0;
						$subject_count = 0;
						$get_final_grade = 0;
						$student_id = $student['id'];
						$strand_id = $student['strand_id'];
						$check_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id, 19 );
						if( $check_strand ){
							$strand_id = $check_strand['strand_id'];
						} 
						
						$getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id, 19 );
						$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester_id, 19 );
						if( $custom_subject ){
							$getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
						}
						$getStudentSubject = $this->customsubject_model->unsetsubjectdrop( $getStudentSubject, $student_id, $semester_id, 19 );	
						
						$average_grade = 0;
						if( $getStudentSubject){
							foreach( $getStudentSubject as $listsubject => $subjects ){
								$get_final_grade = 0;
								$subject_id = $subjects['subject_id'];
								$subject_name = $subjects['name'];
								$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, 19 );
								$include_computation = $subject_manager_details['include_computation'];
								$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
								if( $include_computation == 'yes' ){
									$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, 19 );
									if( empty( $checkifChild )){
										for($y=0;$y<$total_quarter;$y++){
											$set_quarter = $get_quarter[$y];
											$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $set_quarter, $combine_category, 19 );
											/* $grade_per_quarter_with_unit = $getComputedCombinedGrade * $units;
											if($set_quarter==1){ 
												$first = $first + $grade_per_quarter_with_unit;
												$count_subjects_first = $count_subjects_first + $units;
											} elseif($set_quarter==2){ 	
												$second = $second + $grade_per_quarter_with_unit; 
												$count_subjects_second = $count_subjects_second + $units;
											} elseif($set_quarter==3){ 
												$third = $third + $grade_per_quarter_with_unit; 
												$count_subjects_third = $count_subjects_third + $units;
											} elseif($set_quarter==4){
												$fourth = $fourth + $grade_per_quarter_with_unit; 
												$count_subjects_fourth = $count_subjects_fourth + $units;
											} */
											
											$get_final_grade = $get_final_grade + $getComputedCombinedGrade;
										}
									}
								}
							
							
								$final_grade = $get_final_grade / $total_quarter;
								if( $include_computation == 'yes' ){ 
									if( empty( $checkifChild ) ){
										if( $allow_to_pass == 'yes'){
											if( $final_grade == '74.5'){
												$final_grade = '75';
											} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
												$final_grade = '75';
											}
										}
									}
								}
								
								$final_grade =  number_format((float)$final_grade, $decimal_finalgrade, '.', '');
								if( $include_computation == 'yes' ){ 
									$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, 'ga', 19 );
									if( empty($checkifChild)){
										$final_grade = $final_grade * $units;
										$second_sem_total_ave = $second_sem_total_ave + $final_grade;
										$subject_count = $subject_count + $units;
									}
								}
							}
							/* $first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
							$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
							$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
							$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
							$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
							$second_ave = number_format((float)$second_ave, $decimal_average, '.', '');
							$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
							$fourth_ave = number_format((float)$fourth_ave, $decimal_average, '.', '');
							
							
							$count_quarter = count( $get_quarter );*/
							$second_sem_total_ave =   $subject_count != 0 ? $second_sem_total_ave / $subject_count:0;
							$second_sem_total_ave =  number_format((float)$second_sem_total_ave, $decimal_average, '.', '');
						}
						$student_list[$x] = array_merge( $student_list[$x], array( 'second_sem_final_grade' => $second_sem_total_ave ));
						$x++;
					}
				}
			}

			$i = 0;
			foreach ($student_list as $list => $student) {
				$total_ave = 0;
				$total_ave = ($student['first_sem_final_grade'] + $student['second_sem_final_grade']) / 2;
				$student_list[$i] = array_merge( $student_list[$i], array( 'final_grade' => $total_ave ));
				$i++;
			}

			$student_list = $this->orderBy( $student_list, 'final_grade');
		}
		$student_list['quarter'] = $quarter;
		$student_list['semester'] = $semester_id;
		
		return $student_list;
	}

	// function getStudentAverage_G11( $student_list, $quarter, $semester_id=null, $combine_category, $session_id=null ){
	// 	$this->load->model('studentstrand_model');
	// 	$gradingsettings = $this->gradingsetting_model->getbySession($session_id);
	// 	$decimal_average = isset($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;
	// 	$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:0;
	// 	$allow_to_pass = isset($gradingsettings->allow_to_pass)?$gradingsettings->allow_to_pass:'no';
	// 	$semester_array =array('1','2');
	// 	$x=0;

	// 	if(empty($session_id)) {
	// 		$session_id = $this->current_session - 1; // only subtract when falling back
	// 	}
	// 	// Do NOT subtract when session_id is explicitly passed

	// 	if( $semester_id ){
	// 		$semester_array = array( $semester_id );
	// 	}
	// 	if( $student_list ){
	// 		foreach( $student_list as $list => $student ){
				
	// 			$class_id = $student['class_id'];
	// 			$student_id = $student['id'];
	// 			$strand_id = $student['strand_id'];
				
				
	// 			$total_semester_average = 0;
	// 			for($zy=0;$zy<count($semester_array);$zy++){
	// 				$semester_id = $semester_array[$zy];
	// 				if( $semester_id == '1'){ 
	// 					$get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array();	
	// 					$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
	// 					$total_quarter = count( $get_quarter );
	// 				} else {
	// 					$get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();	
	// 					$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
	// 					$total_quarter = count( $get_quarter );
	// 				}
	// 				$count_subjects_first = 0;
	// 				$count_subjects_second = 0;
	// 				$count_subjects_third = 0;
	// 				$count_subjects_fourth = 0;
	// 				$first = 0;
	// 				$second = 0;
	// 				$third = 0;
	// 				$fourth = 0;
	// 				$total_ave = 0;
	// 				$subject_count = 0;
	// 				$get_final_grade = 0;
					

	// 				$check_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id );
	// 				if( $check_strand ){
	// 					$strand_id = $check_strand['strand_id'];
	// 				} 
	// 				$getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id );
	// 				$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester_id );
	// 				if( $custom_subject ){
	// 					$getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
	// 				}
	// 				$getStudentSubject = $this->customsubject_model->unsetsubjectdrop( $getStudentSubject, $student_id, $semester_id );			
	// 				$average_grade = 0;
	// 				if( $getStudentSubject){

	// 					foreach( $getStudentSubject as $listsubject => $subjects ){
	// 						$get_final_grade = 0;
	// 						$subject_id = $subjects['subject_id'];
	// 						$subject_name = $subjects['name'];
	// 						$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
	// 						$include_computation = $subject_manager_details['include_computation'];
	// 						$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
	// 						if( $include_computation == 'yes' ){
	// 							$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
	// 							if( empty( $checkifChild )){
	// 								for($y=0;$y<$total_quarter;$y++){
	// 									$set_quarter = $get_quarter[$y];
	// 									$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $set_quarter, $combine_category, $session_id );
	// 									//echo $getComputedCombinedGrade.'<br/>';
	// 									/* $grade_per_quarter_with_unit = $getComputedCombinedGrade * $units;
	// 									if($set_quarter==1){ 
	// 										$first = $first + $grade_per_quarter_with_unit;
	// 										$count_subjects_first = $count_subjects_first + $units;
	// 									} elseif($set_quarter==2){ 	
	// 										$second = $second + $grade_per_quarter_with_unit; 
	// 										$count_subjects_second = $count_subjects_second + $units;
	// 									} elseif($set_quarter==3){ 
	// 										$third = $third + $grade_per_quarter_with_unit; 
	// 										$count_subjects_third = $count_subjects_third + $units;
	// 									} elseif($set_quarter==4){
	// 										$fourth = $fourth + $grade_per_quarter_with_unit; 
	// 										$count_subjects_fourth = $count_subjects_fourth + $units;
	// 									} */
										
	// 									$get_final_grade = $get_final_grade + $getComputedCombinedGrade;

	// 								}
	// 							}
	// 						}
						
						
	// 						$final_grade = $get_final_grade / $total_quarter;
	// 						if( $include_computation == 'yes' ){ 
	// 							if( empty( $checkifChild ) ){
	// 								if( $allow_to_pass == 'yes'){
	// 									if( $final_grade == '74.5'){
	// 										$final_grade = '75';
	// 									} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
	// 										$final_grade = '75';
	// 									}
	// 								}
	// 							}
	// 						}
							
	// 						$final_grade =  number_format((float)$final_grade, $decimal_finalgrade, '.', '');
	// 						if( $include_computation == 'yes' ){ 
	// 							$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
	// 							if( empty($checkifChild)){
	// 								$final_grade = $final_grade * $units;
	// 								$total_ave = $total_ave + $final_grade;
	// 								$subject_count = $subject_count + $units;
	// 							}
	// 						}
	// 					}
	// 					/* $first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
	// 					$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
	// 					$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
	// 					$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
	// 					$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
	// 					$second_ave = number_format((float)$second_ave, $decimal_average, '.', '');
	// 					$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
	// 					$fourth_ave = number_format((float)$fourth_ave, $decimal_average, '.', '');
						 
						
	// 					$count_quarter = count( $get_quarter );*/
	// 					$total_ave =   $subject_count != 0 ? $total_ave / $subject_count:0;
	// 					$total_ave =  number_format((float)$total_ave, $decimal_average, '.', '');
	// 					$total_semester_average = $total_semester_average + $total_ave;	
	// 					$student_list[$x] = array_merge( $student_list[$x], array( $semester_id.'_sem' => $total_ave ));
						
						
	// 					//echo $total_semester_average.''.$semester_id.'<br/>';

	// 				}
					
	// 			}
	// 			$final_average = $total_semester_average / count( $semester_array );	
	// 			$student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $final_average ));
	// 			$x++;
	// 		}
			
	// 		$student_list = $this->orderBy( $student_list, 'final_grade');
	// 	}
	// 	$student_list['quarter'] = $quarter;
	// 	$student_list['semester'] = $semester_id;
		
	// 	printx($student_list);
	// 	return $student_list;
	// }

	//new function
	function getStudentAverage_G11( $student_list, $quarter, $semester_id=null, $combine_category, $session_id=null ){
    $this->load->model('studentstrand_model');

    // Bug 1 & 2 Fix: use correct session_id before loading settings
    if(empty($session_id)) {
        $session_id = $this->current_session - 1;
    }

    // Bug 1 Fix: load grading settings for the correct G11 session
    $gradingsettings    = $this->gradingsetting_model->getbySession($session_id);
    $decimal_average    = isset($gradingsettings->decimal_average)    ? $gradingsettings->decimal_average    : 0;
    $decimal_finalgrade = isset($gradingsettings->decimal_finalgrade) ? $gradingsettings->decimal_finalgrade : 0;
    $allow_to_pass      = isset($gradingsettings->allow_to_pass)      ? $gradingsettings->allow_to_pass      : 'no';
    $semester_array     = array('1', '2');
    $x = 0;

    if( $semester_id ){
        $semester_array = array( $semester_id );
    }

    if( $student_list ){
        foreach( $student_list as $list => $student ){
            $class_id   = $student['class_id'];
            $student_id = $student['id'];
            $strand_id  = $student['strand_id'];

            $total_semester_average = 0;
            for($zy=0; $zy<count($semester_array); $zy++){
                $semester_id = $semester_array[$zy];
                if( $semester_id == '1' ){
                    $get_quarter   = isset($gradingsettings->qtr_first_sem) ? $gradingsettings->qtr_first_sem : array();
                    $get_quarter   = is_serialized($get_quarter) ? unserialize($get_quarter) : $get_quarter;
                    $total_quarter = count($get_quarter);
                } else {
                    $get_quarter   = isset($gradingsettings->qtr_second_sem) ? $gradingsettings->qtr_second_sem : array();
                    $get_quarter   = is_serialized($get_quarter) ? unserialize($get_quarter) : $get_quarter;
                    $total_quarter = count($get_quarter);
                }

                $total_ave     = 0;
                $subject_count = 0;
                $get_final_grade = 0;

                $check_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id );
                if( $check_strand ){
                    $strand_id = $check_strand['strand_id'];
                }

                $getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id );
                $custom_subject    = $this->customsubject_model->getSubjectbySemester( $student_id, $semester_id );
                if( $custom_subject ){
                    $getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
                }
                $getStudentSubject = $this->customsubject_model->unsetsubjectdrop( $getStudentSubject, $student_id, $semester_id );

                if( $getStudentSubject ){
                    foreach( $getStudentSubject as $listsubject => $subjects ){
                        $get_final_grade = 0;
                        $subject_id      = $subjects['subject_id'];
                        $subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
                        $include_computation     = $subject_manager_details['include_computation'];
                        $units = !empty($subject_manager_details['units']) ? $subject_manager_details['units'] : 1;

                        if( $include_computation == 'yes' ){
                            $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
                            if( empty($checkifChild) ){
                                for($y=0; $y<$total_quarter; $y++){
                                    $set_quarter = $get_quarter[$y];
                                    // Bug 3 Fix: pass $session_id correctly
                                    $getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade(
                                        $student_id, $subject_id, $set_quarter, $combine_category, $session_id
                                    );
                                    $get_final_grade = $get_final_grade + $getComputedCombinedGrade;
                                }
                            }
                        }

                        $final_grade = $total_quarter > 0 ? $get_final_grade / $total_quarter : 0;
                        if( $include_computation == 'yes' ){
                            if( empty($checkifChild) ){
                                if( $allow_to_pass == 'yes' ){
                                    if( $final_grade == '74.5' ){
                                        $final_grade = '75';
                                    } elseif( $final_grade > '74.4' && $final_grade < 75 ){
                                        $final_grade = '75';
                                    }
                                }
                            }
                        }

                        $final_grade = number_format((float)$final_grade, $decimal_finalgrade, '.', '');
                        if( $include_computation == 'yes' ){
                            // Bug 4 Fix: pass session_id to checkifChild
                            $checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );
                            if( empty($checkifChild) ){
                                $final_grade   = $final_grade * $units;
                                $total_ave     = $total_ave + $final_grade;
                                $subject_count = $subject_count + $units;
                            }
                        }
                    }

                    $total_ave = $subject_count != 0 ? $total_ave / $subject_count : 0;
                    $total_ave = number_format((float)$total_ave, $decimal_average, '.', '');
                    $total_semester_average = $total_semester_average + $total_ave;
                    $student_list[$x] = array_merge( $student_list[$x], array( $semester_id.'_sem' => $total_ave ));
                }
            }

            $final_average    = $total_semester_average / count($semester_array);
            $student_list[$x] = array_merge( $student_list[$x], array( 'final_grade' => $final_average ));
            $x++;
        }

        $student_list = $this->orderBy( $student_list, 'final_grade');
    }

    $student_list['quarter']  = $quarter;
    $student_list['semester'] = $semester_id;

	// printx($student_list);
    return $student_list;
}
//END

	
	function getAllPreviousG11FinalAvg($student_ids, $combine_category) {
    if (empty($student_ids)) return [];

    $ids = implode("','", $student_ids);

    $result = $this->db->query("
        SELECT 
            s.id AS student_id,
            ROUND(SUM(subj.final_grade * subj.units) / SUM(subj.units), 2) AS final_grade
        FROM students s
        JOIN student_session ss ON ss.student_id = s.id AND ss.class_id = 14
        JOIN (
            SELECT 
                sp.subject_id,
                sp.strand_id,
                sp.session_id,
                er.student_id,
                sm.units,
                ROUND(
                    SUM(er.get_marks) / COUNT(er.get_marks)
                , 2) AS final_grade
            FROM specialized_subjects sp
            JOIN subject_manager sm ON sm.subject_id = sp.subject_id
                AND sm.session_id = sp.session_id
                AND sm.include_computation = 'yes'
                AND sm.include_computation_gpa = 'yes'
            LEFT JOIN exam_results er ON er.subject_id = sp.subject_id
                AND er.session_id = sp.session_id
            WHERE sp.semester IN (1, 2)
            AND NOT EXISTS (
                SELECT 1 FROM subject_manager sm2
                WHERE sm2.subject_id = sp.subject_id
                AND sm2.session_id = sp.session_id
                AND (sm2.include_computation = 'no' OR sm2.include_computation_gpa = 'no')
            )
            GROUP BY sp.subject_id, sp.strand_id, sp.session_id, sm.units, er.student_id
        ) subj ON subj.session_id = ss.session_id
            AND subj.student_id = s.id
        WHERE s.id IN ('$ids')
        GROUP BY s.id
    ");

    $rows = $result->result_array();

    if (empty($rows)) return [];

    $g11Map = [];
    foreach ($rows as $row) {
        if (isset($row['student_id']) && isset($row['final_grade'])) {
            $g11Map[$row['student_id']] = $row['final_grade'];
        }
    }
	// printx($g11Map);
    return $g11Map;
}
//NEW FUNCTION FOR AVERAGE 03-23-2026
function getStudentAverage_final($student_list, $quarter, $semester_id = null, $combine_category, $session_id = null)
		{
			$this->load->model('studentstrand_model');

			if (empty($session_id)) {
				$session_id = $this->current_session;
			}

			$gradingsettings = $this->gradingsetting_model->getbySession($session_id);
			$decimal_average = isset($gradingsettings->decimal_average) ? $gradingsettings->decimal_average : 0;
			$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade) ? $gradingsettings->decimal_finalgrade : 0;
			$allow_to_pass = isset($gradingsettings->allow_to_pass) ? $gradingsettings->allow_to_pass : 'no';

			if ($student_list) {

				if ($combine_category == 'ga') {

					if ($semester_id || $quarter == 'final') {

						$first_sem_quarters = isset($gradingsettings->qtr_first_sem) ? $gradingsettings->qtr_first_sem : array();
						$second_sem_quarters = isset($gradingsettings->qtr_second_sem) ? $gradingsettings->qtr_second_sem : array();

						$first_sem_quarters = is_serialized($first_sem_quarters) ? unserialize($first_sem_quarters) : $first_sem_quarters;
						$second_sem_quarters = is_serialized($second_sem_quarters) ? unserialize($second_sem_quarters) : $second_sem_quarters;

						if ($quarter == 'final') {
							$x = 0;

							foreach ($student_list as $list => $student) {
								$count_subjects_first  = 0;
								$count_subjects_second = 0;
								$count_subjects_third  = 0;
								$count_subjects_fourth = 0;

								$first  = 0;
								$second = 0;
								$third  = 0;
								$fourth = 0;

								$total_ave = 0;
								$subject_count = 0;

								$student_id = $student['id'];
								$class_id = $student['class_id'];
								$default_strand_id = isset($student['strand_id']) ? $student['strand_id'] : '';

								$first_check  = false;
								$second_check = false;
								$third_check  = false;
								$fourth_check = false;

								$is_shs = ($class_id == 14 || $class_id == 15);

								$merged_subjects = array();

								if ($is_shs) {
									$sem1_strand_id = $default_strand_id;
									$check_strand_sem1 = $this->studentstrand_model->get_latest_strand($student_id, 1, $session_id);
									if ($check_strand_sem1) {
										$sem1_strand_id = $check_strand_sem1['strand_id'];
									}

									$getStudentSubject_sem1 = $this->specializedsubject_model->getByStrandSemester($sem1_strand_id, 1, $session_id);
									$custom_subject_sem1 = $this->customsubject_model->getSubjectbySemester($student_id, 1, $session_id);

									if ($custom_subject_sem1) {
										$getStudentSubject_sem1 = array_merge($getStudentSubject_sem1, $custom_subject_sem1);
									}

									$getStudentSubject_sem1 = $this->customsubject_model->unsetsubjectdrop($getStudentSubject_sem1, $student_id, 1, $session_id);

									$sem2_strand_id = $default_strand_id;
									$check_strand_sem2 = $this->studentstrand_model->get_latest_strand($student_id, 2, $session_id);
									if ($check_strand_sem2) {
										$sem2_strand_id = $check_strand_sem2['strand_id'];
									}

									$getStudentSubject_sem2 = $this->specializedsubject_model->getByStrandSemester($sem2_strand_id, 2, $session_id);
									$custom_subject_sem2 = $this->customsubject_model->getSubjectbySemester($student_id, 2, $session_id);

									if ($custom_subject_sem2) {
										$getStudentSubject_sem2 = array_merge($getStudentSubject_sem2, $custom_subject_sem2);
									}

									$getStudentSubject_sem2 = $this->customsubject_model->unsetsubjectdrop($getStudentSubject_sem2, $student_id, 2, $session_id);

									if ($getStudentSubject_sem1) {
										foreach ($getStudentSubject_sem1 as $subj) {
											$subj['semester_source'] = 1;
											$merged_subjects[] = $subj;
										}
									}

									if ($getStudentSubject_sem2) {
										foreach ($getStudentSubject_sem2 as $subj) {
											$subj['semester_source'] = 2;
											$merged_subjects[] = $subj;
										}
									}
								} else {
									$getStudentSubject = $this->subject_model->getSubjctByClass($class_id);

									if ($getStudentSubject) {
										foreach ($getStudentSubject as $subj) {
											$subj['subject_id'] = $subj['id'];
											$subj['semester_source'] = 0;
											$merged_subjects[] = $subj;
										}
									}
								}

								if ($merged_subjects) {
									/*
									|------------------------------------------------------
									| FIX: Use subject_id + semester_source as unique key
									| so same subject in sem1 and sem2 are BOTH counted
									|------------------------------------------------------
									*/
									$processed_subject_keys = array();

									foreach ($merged_subjects as $subjects) {
										$get_final_grade = 0;
										$subject_id = isset($subjects['subject_id']) ? $subjects['subject_id'] : $subjects['id'];
										$semester_source = isset($subjects['semester_source']) ? $subjects['semester_source'] : 0;

										if (empty($subject_id)) {
											continue;
										}

										// FIX: key includes semester_source so sem1 and sem2
										// instances of the same subject are both processed
										$unique_key = $subject_id . '_' . $semester_source;
										if (in_array($unique_key, $processed_subject_keys)) {
											continue;
										}
										$processed_subject_keys[] = $unique_key;

										$subject_manager_details = $this->subjectmanager_model->getBySubjectSession($subject_id, $session_id);
										$include_computation = isset($subject_manager_details['include_computation']) ? $subject_manager_details['include_computation'] : 'no';
										// $units = !empty($subject_manager_details['units']) ? $subject_manager_details['units'] : 1;
										$units = 1;

										if ($include_computation == 'yes') {
											$checkifChild = $this->subjectcombine_model->checkifChild($subject_id, $combine_category, $session_id);

											if (empty($checkifChild)) {
												if ($semester_source == 1) {
													$subject_quarters = $first_sem_quarters;
												} elseif ($semester_source == 2) {
													$subject_quarters = $second_sem_quarters;
												} else {
													$subject_quarters = array(1, 2, 3, 4);
												}

												$valid_quarter_count = 0;

												foreach ($subject_quarters as $set_quarter) {
													$grade = $this->subjectcombine_model->getComputedCombinedGrade(
														$student_id,
														$subject_id,
														$set_quarter,
														$combine_category,
														$session_id
													);

													if ($grade !== null && $grade !== '' && $grade > 0) {
														$grade_per_quarter_with_unit = $grade * $units;

														if ($set_quarter == 1) {
															$first += $grade_per_quarter_with_unit;
															$count_subjects_first += $units;
															$first_check = true;
														} elseif ($set_quarter == 2) {
															$second += $grade_per_quarter_with_unit;
															$count_subjects_second += $units;
															$second_check = true;
														} elseif ($set_quarter == 3) {
															$third += $grade_per_quarter_with_unit;
															$count_subjects_third += $units;
															$third_check = true;
														} elseif ($set_quarter == 4) {
															$fourth += $grade_per_quarter_with_unit;
															$count_subjects_fourth += $units;
															$fourth_check = true;
														}

														$get_final_grade += $grade;
														$valid_quarter_count++;
													}
												}

												$final_grade = ($valid_quarter_count > 0) ? ($get_final_grade / $valid_quarter_count) : 0;

												if ($allow_to_pass == 'yes') {
													if ($final_grade == 74.5 || ($final_grade > 74.4 && $final_grade < 75)) {
														$final_grade = 75;
													}
												}

												$final_grade = (int) round($final_grade);

												if ($valid_quarter_count > 0) {
													// $total_ave += ($final_grade * $units);
													$total_ave += $final_grade;
													$subject_count++; // count number of subjects
												}
											}
										}
									}

									$first_ave  = $count_subjects_first  != 0 ? $first  / $count_subjects_first  : 0;
									$second_ave = $count_subjects_second != 0 ? $second / $count_subjects_second : 0;
									$third_ave  = $count_subjects_third  != 0 ? $third  / $count_subjects_third  : 0;
									$fourth_ave = $count_subjects_fourth != 0 ? $fourth / $count_subjects_fourth : 0;

									$first_ave  = number_format((float)$first_ave, $decimal_average, '.', '');
									$second_ave = number_format((float)$second_ave, $decimal_average, '.', '');
									$third_ave  = number_format((float)$third_ave, $decimal_average, '.', '');
									$fourth_ave = number_format((float)$fourth_ave, $decimal_average, '.', '');

									if ($first_check) {
										$student_list[$x] = array_merge($student_list[$x], array('1' => $first_ave));
									}
									if ($second_check) {
										$student_list[$x] = array_merge($student_list[$x], array('2' => $second_ave));
									}
									if ($third_check) {
										$student_list[$x] = array_merge($student_list[$x], array('3' => $third_ave));
									}
									if ($fourth_check) {
										$student_list[$x] = array_merge($student_list[$x], array('4' => $fourth_ave));
									}

									$final_average = $subject_count != 0 ? ($total_ave / $subject_count) : 0;
									$final_average = number_format((float)$final_average, $decimal_average, '.', '');
								} else {
									$final_average = '';
								}

								$student_list[$x] = array_merge($student_list[$x], array('final_grade' => $final_average));
								$x++;
							}
						} else {
							$x = 0;

							foreach ($student_list as $list => $student) {
								$student_id = $student['id'];
								$class_id = $student['class_id'];
								$strand_id = isset($student['strand_id']) ? $student['strand_id'] : '';
								$is_shs = ($class_id == 14 || $class_id == 15);

								if ($is_shs) {
									$check_strand = $this->studentstrand_model->get_latest_strand($student_id, $semester_id, $session_id);
									if ($check_strand) {
										$strand_id = $check_strand['strand_id'];
									}

									$getStudentSubject = $this->specializedsubject_model->getByStrandSemester($strand_id, $semester_id, $session_id);
									$custom_subject = $this->customsubject_model->getSubjectbySemester($student_id, $semester_id, $session_id);

									if ($custom_subject) {
										$getStudentSubject = array_merge($getStudentSubject, $custom_subject);
									}

									$getStudentSubject = $this->customsubject_model->unsetsubjectdrop($getStudentSubject, $student_id, $semester_id, $session_id);
								} else {
									$getStudentSubject = $this->subject_model->getSubjctByClass($class_id);
								}

								$average_grade = 0;
								$subject_count = 0;
								$processed_subject_ids = array();

								if ($getStudentSubject) {
									foreach ($getStudentSubject as $listsubject => $subjects) {
										$subject_id = $is_shs ? $subjects['subject_id'] : $subjects['id'];

										if (empty($subject_id)) {
											continue;
										}

										if (in_array($subject_id, $processed_subject_ids)) {
											continue;
										}
										$processed_subject_ids[] = $subject_id;

										$subject_name = '';
										$get_subject_info = $this->subject_model->get($subject_id);
										if ($get_subject_info && isset($get_subject_info['name'])) {
											$subject_name = trim($get_subject_info['name']);
										}

										$normalized_subject_name = strtolower(trim(preg_replace('/\s+/', ' ', $subject_name)));

										$excluded_subject_names = array(
											'homeroom 11',
											'homeroom 12',
											'work education 11',
											'work education 12',
											'elective - singing group',
											'elective-singing group',
											'elective'
										);

										if ($is_shs && in_array($normalized_subject_name, $excluded_subject_names)) {
											continue;
										}

										$subject_manager_details = $this->subjectmanager_model->getBySubjectSession($subject_id, $session_id);
										$include_computation = isset($subject_manager_details['include_computation']) ? $subject_manager_details['include_computation'] : 'no';
										// $units = !empty($subject_manager_details['units']) ? $subject_manager_details['units'] : 1;
										$units = 1;

										if ($include_computation == 'yes') {
											$checkifChild = $this->subjectcombine_model->checkifChild($subject_id, $combine_category, $session_id);

											if (empty($checkifChild)) {
												$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade(
													$student_id,
													$subject_id,
													$quarter,
													$combine_category,
													$session_id
												);

												if ($getComputedCombinedGrade !== null && $getComputedCombinedGrade !== '' && $getComputedCombinedGrade > 0) {
													$getComputedCombinedGrade = $getComputedCombinedGrade * $units;
													$average_grade += $getComputedCombinedGrade;
													$subject_count += $units;
												}
											}
										}
									}

									if ($subject_count != 0) {
										$average_grade = $average_grade / $subject_count;
										$average_grade = number_format($average_grade, $decimal_average, '.', '');
									} else {
										$average_grade = '';
									}
								} else {
									$average_grade = '';
								}

								$student_list[$x] = array_merge($student_list[$x], array('final_grade' => $average_grade));
								$x++;
							}
						}
					} else {

						if ($quarter == 'final') {
							$x = 0;
							$total_quarter = 4;

							foreach ($student_list as $list => $student) {
								$first  = 0;
								$second = 0;
								$third  = 0;
								$fourth = 0;

								$count_subjects_first  = 0;
								$count_subjects_second = 0;
								$count_subjects_third  = 0;
								$count_subjects_fourth = 0;

								$student_id = $student['id'];
								$class_id   = $student['class_id'];

								$getStudentSubject = $this->subject_model->getSubjctByClass($class_id);

								$total_ave = 0;
								$subject_count = 0;

								$first_check  = false;
								$second_check = false;
								$third_check  = false;
								$fourth_check = false;

								if ($getStudentSubject) {
									$processed_subject_ids = array();

									foreach ($getStudentSubject as $listsubject => $subjects) {
										$get_final_grade = 0;
										$subject_id = $subjects['id'];

										if (empty($subject_id)) {
											continue;
										}

										if (in_array($subject_id, $processed_subject_ids)) {
											continue;
										}
										$processed_subject_ids[] = $subject_id;

										$subject_manager_details = $this->subjectmanager_model->getBySubjectSession($subject_id, $session_id);
										$include_computation = isset($subject_manager_details['include_computation']) ? $subject_manager_details['include_computation'] : 'no';

										if ($include_computation == 'yes') {
											$checkifChild = $this->subjectcombine_model->checkifChild($subject_id, $combine_category, $session_id);

											if (empty($checkifChild)) {
												$valid_quarter_count = 0;

												for ($q = 1; $q <= $total_quarter; $q++) {
													$grade = $this->subjectcombine_model->getComputedCombinedGrade(
														$student_id,
														$subject_id,
														$q,
														$combine_category,
														$session_id
													);

													if ($grade !== null && $grade !== '' && $grade > 0) {
														if ($q == 1) {
															$first += $grade;
															$count_subjects_first++;
															$first_check = true;
														} elseif ($q == 2) {
															$second += $grade;
															$count_subjects_second++;
															$second_check = true;
														} elseif ($q == 3) {
															$third += $grade;
															$count_subjects_third++;
															$third_check = true;
														} elseif ($q == 4) {
															$fourth += $grade;
															$count_subjects_fourth++;
															$fourth_check = true;
														}

														$get_final_grade += $grade;
														$valid_quarter_count++;
													}
												}

												$final_grade = ($valid_quarter_count > 0) ? ($get_final_grade / $valid_quarter_count) : 0;

												if ($allow_to_pass == 'yes') {
													if ($final_grade == 74.5 || ($final_grade > 74.4 && $final_grade < 75)) {
														$final_grade = 75;
													}
												}

												$final_grade = (int) round($final_grade);

												if ($valid_quarter_count > 0) {
													$total_ave += $final_grade;
													$subject_count++;
												}
											}
										}
									}

									$first_ave  = $count_subjects_first  != 0 ? $first  / $count_subjects_first  : 0;
									$second_ave = $count_subjects_second != 0 ? $second / $count_subjects_second : 0;
									$third_ave  = $count_subjects_third  != 0 ? $third  / $count_subjects_third  : 0;
									$fourth_ave = $count_subjects_fourth != 0 ? $fourth / $count_subjects_fourth : 0;

									$first_ave  = number_format((float)$first_ave, $decimal_average, '.', '');
									$second_ave = number_format((float)$second_ave, $decimal_average, '.', '');
									$third_ave  = number_format((float)$third_ave, $decimal_average, '.', '');
									$fourth_ave = number_format((float)$fourth_ave, $decimal_average, '.', '');

									if ($first_check) {
										$student_list[$x] = array_merge($student_list[$x], array('1' => $first_ave));
									}
									if ($second_check) {
										$student_list[$x] = array_merge($student_list[$x], array('2' => $second_ave));
									}
									if ($third_check) {
										$student_list[$x] = array_merge($student_list[$x], array('3' => $third_ave));
									}
									if ($fourth_check) {
										$student_list[$x] = array_merge($student_list[$x], array('4' => $fourth_ave));
									}

									$final_average = $subject_count != 0 ? ($total_ave / $subject_count) : 0;
									$final_average = number_format((float)$final_average, $decimal_average, '.', '');

									$student_list[$x] = array_merge($student_list[$x], array('final_grade' => $final_average));
								} else {
									$student_list[$x] = array_merge($student_list[$x], array('final_grade' => ''));
								}

								$x++;
							}
						} else {
							$x = 0;

							foreach ($student_list as $list => $student) {
								$student_id = $student['id'];
								$class_id   = $student['class_id'];

								$getStudentSubject = $this->subject_model->getSubjctByClass($class_id);

								$average_grade = 0;
								$subject_count = 0;
								$processed_subject_ids = array();

								if ($getStudentSubject) {
									foreach ($getStudentSubject as $listsubject => $subjects) {
										$subject_id = $subjects['id'];

										if (empty($subject_id)) {
											continue;
										}

										if (in_array($subject_id, $processed_subject_ids)) {
											continue;
										}
										$processed_subject_ids[] = $subject_id;

										$subject_manager_details = $this->subjectmanager_model->getBySubjectSession($subject_id, $session_id);
										$include_computation = isset($subject_manager_details['include_computation']) ? $subject_manager_details['include_computation'] : 'no';

										if ($include_computation == 'yes') {
											$checkifChild = $this->subjectcombine_model->checkifChild($subject_id, $combine_category, $session_id);

											if (empty($checkifChild)) {
												$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade(
													$student_id,
													$subject_id,
													$quarter,
													$combine_category,
													$session_id
												);

												if ($getComputedCombinedGrade !== null && $getComputedCombinedGrade !== '' && $getComputedCombinedGrade > 0) {
													$subject_count++;
													$average_grade += $getComputedCombinedGrade;
												}
											}
										}
									}

									if ($subject_count != 0) {
										$average_grade = $average_grade / $subject_count;
										$average_grade = number_format($average_grade, $decimal_average, '.', '');
									} else {
										$average_grade = '';
									}
								} else {
									$average_grade = '';
								}

								$student_list[$x] = array_merge($student_list[$x], array('final_grade' => $average_grade));
								$x++;
							}
						}
					}

				} elseif ($combine_category == 'gpa') {

					// =====================================================================
					// SHARED HELPER: excluded subject names for SHS
					// =====================================================================
					$excluded_subject_names = array(
						'homeroom 11',
						'homeroom 12',
						'work education 11',
						'work education 12',
						'elective - singing group',
						'elective-singing group',
						'elective'
					);

					if ($semester_id) {

						if ($semester_id == '1') {
							$get_quarter = isset($gradingsettings->qtr_first_sem) ? $gradingsettings->qtr_first_sem : array();
						} else {
							$get_quarter = isset($gradingsettings->qtr_second_sem) ? $gradingsettings->qtr_second_sem : array();
						}

						$get_quarter = is_serialized($get_quarter) ? unserialize($get_quarter) : $get_quarter;
						$total_quarter = count($get_quarter);

						if ($quarter == 'final') {
							$x = 0;

							foreach ($student_list as $list => $student) {
								$student_id = $student['id'];
								$strand_id  = $student['strand_id'];
								$class_id   = isset($student['class_id']) ? $student['class_id'] : '';
								$is_shs     = ($class_id == 14 || $class_id == 15);

								$check_strand = $this->studentstrand_model->get_latest_strand($student_id, $semester_id, $session_id);
								if ($check_strand) {
									$strand_id = $check_strand['strand_id'];
								}

								$getStudentSubject = $this->specializedsubject_model->getByStrandSemester($strand_id, $semester_id, $session_id);
								$custom_subject = $this->customsubject_model->getSubjectbySemester($student_id, $semester_id, $session_id);

								if ($custom_subject) {
									$getStudentSubject = array_merge($getStudentSubject, $custom_subject);
								}

								$getStudentSubject = $this->customsubject_model->unsetsubjectdrop($getStudentSubject, $student_id, $semester_id, $session_id);

								$total_ave = 0;
								$subject_count = 0;

								if ($getStudentSubject) {
									$processed_subject_ids = array();

									foreach ($getStudentSubject as $listsubject => $subjects) {
										$get_final_grade = 0;
										$subject_id = $subjects['subject_id'];

										if (empty($subject_id)) {
											continue;
										}

										if (in_array($subject_id, $processed_subject_ids)) {
											continue;
										}
										$processed_subject_ids[] = $subject_id;

										$subject_name = '';
										$get_subject_info = $this->subject_model->get($subject_id);
										if ($get_subject_info && isset($get_subject_info['name'])) {
											$subject_name = trim($get_subject_info['name']);
										}
										$normalized_subject_name = strtolower(trim(preg_replace('/\s+/', ' ', $subject_name)));
										if ($is_shs && in_array($normalized_subject_name, $excluded_subject_names)) {
											continue;
										}

										$subject_manager_details = $this->subjectmanager_model->getBySubjectSession($subject_id, $session_id);
										$include_computation = isset($subject_manager_details['include_computation_gpa']) ? $subject_manager_details['include_computation_gpa'] : 'no';
										// $units = !empty($subject_manager_details['units']) ? $subject_manager_details['units'] : 1;
										$units = 1;

										if ($include_computation == 'yes') {
											$checkifChild = $this->subjectcombine_model->checkifChild($subject_id, $combine_category, $session_id);

											if (empty($checkifChild)) {
												$valid_quarter_count = 0;

												foreach ($get_quarter as $set_quarter) {
													$grade = $this->subjectcombine_model->getComputedCombinedGrade(
														$student_id,
														$subject_id,
														$set_quarter,
														$combine_category,
														$session_id
													);

													if ($grade !== null && $grade !== '' && $grade > 0) {
														$get_final_grade += $grade;
														$valid_quarter_count++;
													}
												}

												$final_grade = ($valid_quarter_count > 0) ? ($get_final_grade / $valid_quarter_count) : 0;

												if ($allow_to_pass == 'yes') {
													if ($final_grade == 74.5 || ($final_grade > 74.4 && $final_grade < 75)) {
														$final_grade = 75;
													}
												}

												$final_grade = number_format((float)$final_grade, $decimal_finalgrade, '.', '');

												if ($valid_quarter_count > 0) {
													// $total_ave += ($final_grade * $units);
													$total_ave += $final_grade;
													$subject_count++; // count number of subjects
												}
											}
										}
									}

									$final_average = $subject_count != 0 ? ($total_ave / $subject_count) : 0;
									$final_average = number_format((float)$final_average, $decimal_average, '.', '');
								} else {
									$final_average = '';
								}

								$student_list[$x] = array_merge($student_list[$x], array('final_grade' => $final_average));
								$x++;
							}
						} else {
							$x = 0;

							foreach ($student_list as $list => $student) {
								$student_id = $student['id'];
								$strand_id  = $student['strand_id'];
								$class_id   = isset($student['class_id']) ? $student['class_id'] : '';
								$is_shs     = ($class_id == 14 || $class_id == 15);

								$check_strand = $this->studentstrand_model->get_latest_strand($student_id, $semester_id, $session_id);
								if ($check_strand) {
									$strand_id = $check_strand['strand_id'];
								}

								$getStudentSubject = $this->specializedsubject_model->getByStrandSemester($strand_id, $semester_id, $session_id);
								$custom_subject = $this->customsubject_model->getSubjectbySemester($student_id, $semester_id, $session_id);

								if ($custom_subject) {
									$getStudentSubject = array_merge($getStudentSubject, $custom_subject);
								}

								$getStudentSubject = $this->customsubject_model->unsetsubjectdrop($getStudentSubject, $student_id, $semester_id, $session_id);

								$average_grade = 0;
								$subject_count = 0;
								$processed_subject_ids = array();

								if ($getStudentSubject) {
									foreach ($getStudentSubject as $listsubject => $subjects) {
										$subject_id = $subjects['subject_id'];

										if (empty($subject_id)) {
											continue;
										}

										if (in_array($subject_id, $processed_subject_ids)) {
											continue;
										}
										$processed_subject_ids[] = $subject_id;

										$subject_name = '';
										$get_subject_info = $this->subject_model->get($subject_id);
										if ($get_subject_info && isset($get_subject_info['name'])) {
											$subject_name = trim($get_subject_info['name']);
										}
										$normalized_subject_name = strtolower(trim(preg_replace('/\s+/', ' ', $subject_name)));
										if ($is_shs && in_array($normalized_subject_name, $excluded_subject_names)) {
											continue;
										}

										$subject_manager_details = $this->subjectmanager_model->getBySubjectSession($subject_id, $session_id);
										$include_computation = isset($subject_manager_details['include_computation_gpa']) ? $subject_manager_details['include_computation_gpa'] : 'no';
										// $units = !empty($subject_manager_details['units']) ? $subject_manager_details['units'] : 1;
										$units = 1;

										if ($include_computation == 'yes') {
											$checkifChild = $this->subjectcombine_model->checkifChild($subject_id, $combine_category, $session_id);

											if (empty($checkifChild)) {
												$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade(
													$student_id,
													$subject_id,
													$quarter,
													$combine_category,
													$session_id
												);

												if ($getComputedCombinedGrade !== null && $getComputedCombinedGrade !== '' && $getComputedCombinedGrade > 0) {
													$average_grade += ($getComputedCombinedGrade * $units);
													$subject_count += $units;
												}
											}
										}
									}

									if ($subject_count != 0) {
										$average_grade = $average_grade / $subject_count;
										$average_grade = number_format($average_grade, $decimal_average, '.', '');
									} else {
										$average_grade = '';
									}
								} else {
									$average_grade = '';
								}

								$student_list[$x] = array_merge($student_list[$x], array('final_grade' => $average_grade));
								$x++;
							}
						}
					} else {

						if ($quarter == 'final') {
							$x = 0;
							$total_quarter = 4;

							foreach ($student_list as $list => $student) {
								$student_id = $student['id'];
								$class_id   = $student['class_id'];
								$is_shs     = ($class_id == 14 || $class_id == 15);

								$getStudentSubject = $this->subject_model->getSubjctByClass($class_id);

								$total_ave = 0;
								$subject_count = 0;

								if ($getStudentSubject) {
									$processed_subject_ids = array();

									foreach ($getStudentSubject as $listsubject => $subjects) {
										$get_final_grade = 0;
										$subject_id = $subjects['id'];

										if (empty($subject_id)) {
											continue;
										}

										if (in_array($subject_id, $processed_subject_ids)) {
											continue;
										}
										$processed_subject_ids[] = $subject_id;

										$subject_name = '';
										$get_subject_info = $this->subject_model->get($subject_id);
										if ($get_subject_info && isset($get_subject_info['name'])) {
											$subject_name = trim($get_subject_info['name']);
										}
										$normalized_subject_name = strtolower(trim(preg_replace('/\s+/', ' ', $subject_name)));
										if ($is_shs && in_array($normalized_subject_name, $excluded_subject_names)) {
											continue;
										}

										$subject_manager_details = $this->subjectmanager_model->getBySubjectSession($subject_id, $session_id);
										$include_computation = isset($subject_manager_details['include_computation_gpa']) ? $subject_manager_details['include_computation_gpa'] : 'no';

										if ($include_computation == 'yes') {
											$checkifChild = $this->subjectcombine_model->checkifChild($subject_id, $combine_category, $session_id);

											if (empty($checkifChild)) {
												$valid_quarter_count = 0;

												for ($q = 1; $q <= $total_quarter; $q++) {
													$grade = $this->subjectcombine_model->getComputedCombinedGrade(
														$student_id,
														$subject_id,
														$q,
														$combine_category,
														$session_id
													);

													if ($grade !== null && $grade !== '' && $grade > 0) {
														$get_final_grade += $grade;
														$valid_quarter_count++;
													}
												}

												$final_grade = ($valid_quarter_count > 0) ? ($get_final_grade / $valid_quarter_count) : 0;

												if ($allow_to_pass == 'yes') {
													if ($final_grade == 74.5 || ($final_grade > 74.4 && $final_grade < 75)) {
														$final_grade = 75;
													}
												}

												$final_grade = number_format((float)$final_grade, $decimal_finalgrade, '.', '');

												if ($valid_quarter_count > 0) {
													$total_ave += $final_grade;
													$subject_count++;
												}
											}
										}
									}

									$final_average = $subject_count != 0 ? ($total_ave / $subject_count) : 0;
									$final_average = number_format((float)$final_average, $decimal_average, '.', '');
								} else {
									$final_average = '';
								}

								$student_list[$x] = array_merge($student_list[$x], array('final_grade' => $final_average));
								$x++;
							}
						} else {
							$x = 0;

							foreach ($student_list as $list => $student) {
								$student_id = $student['id'];
								$class_id   = $student['class_id'];
								$is_shs     = ($class_id == 14 || $class_id == 15);

								$getStudentSubject = $this->subject_model->getSubjctByClass($class_id);

								$average_grade = 0;
								$subject_count = 0;
								$processed_subject_ids = array();

								if ($getStudentSubject) {
									foreach ($getStudentSubject as $listsubject => $subjects) {
										$subject_id = $subjects['id'];

										if (empty($subject_id)) {
											continue;
										}

										if (in_array($subject_id, $processed_subject_ids)) {
											continue;
										}
										$processed_subject_ids[] = $subject_id;

										$subject_name = '';
										$get_subject_info = $this->subject_model->get($subject_id);
										if ($get_subject_info && isset($get_subject_info['name'])) {
											$subject_name = trim($get_subject_info['name']);
										}
										$normalized_subject_name = strtolower(trim(preg_replace('/\s+/', ' ', $subject_name)));
										if ($is_shs && in_array($normalized_subject_name, $excluded_subject_names)) {
											continue;
										}

										$subject_manager_details = $this->subjectmanager_model->getBySubjectSession($subject_id, $session_id);
										$include_computation = isset($subject_manager_details['include_computation_gpa']) ? $subject_manager_details['include_computation_gpa'] : 'no';

										if ($include_computation == 'yes') {
											$checkifChild = $this->subjectcombine_model->checkifChild($subject_id, $combine_category, $session_id);

											if (empty($checkifChild)) {
												$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade(
													$student_id,
													$subject_id,
													$quarter,
													$combine_category,
													$session_id
												);

												if ($getComputedCombinedGrade !== null && $getComputedCombinedGrade !== '' && $getComputedCombinedGrade > 0) {
													$subject_count++;
													$average_grade += $getComputedCombinedGrade;
												}
											}
										}
									}

									if ($subject_count != 0) {
										$average_grade = $average_grade / $subject_count;
										$average_grade = number_format($average_grade, $decimal_average, '.', '');
									} else {
										$average_grade = '';
									}
								} else {
									$average_grade = '';
								}

								$student_list[$x] = array_merge($student_list[$x], array('final_grade' => $average_grade));
								$x++;
							}
						}
					}
				}

				$student_list = $this->orderBy($student_list, 'final_grade');
			}

			$student_list['quarter'] = $quarter;
			$student_list['semester'] = $semester_id;

			return $student_list;
		}

//NEW FUNCTION FOR FINAL GRADING RANKING 03-23-2026
public function getgraderanking_final($studentlist)
		{
			$this->load->model('conductimportbatchdetail_model');
			$this->load->model('gradingsetting_model');
			$this->load->model('marksgrademanager_model');

			$gradingsettings = $this->gradingsetting_model->get();
			$decimal_grades = isset($gradingsettings->decimal_grades) ? $gradingsettings->decimal_grades : 2;
			$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade) ? $gradingsettings->decimal_finalgrade : 2;
			$allow_to_pass = isset($gradingsettings->allow_to_pass) ? $gradingsettings->allow_to_pass : 'no';

			$quarter = isset($studentlist['quarter']) ? $studentlist['quarter'] : '';
			$semester = isset($studentlist['semester']) ? $studentlist['semester'] : '';
			$set_category = 'ga';
			$grade_list = $this->get_Category($set_category);

			$first_sem_quarters = isset($gradingsettings->qtr_first_sem) ? $gradingsettings->qtr_first_sem : array();
			$second_sem_quarters = isset($gradingsettings->qtr_second_sem) ? $gradingsettings->qtr_second_sem : array();

			$first_sem_quarters = is_serialized($first_sem_quarters) ? unserialize($first_sem_quarters) : $first_sem_quarters;
			$second_sem_quarters = is_serialized($second_sem_quarters) ? unserialize($second_sem_quarters) : $second_sem_quarters;

			if ($semester) {
				if ($semester == '1') {
					$get_quarter = $first_sem_quarters;
				} else {
					$get_quarter = $second_sem_quarters;
				}
				$total_quarter = count($get_quarter);
			} else {
				$get_quarter = array(1, 2, 3, 4);
				$total_quarter = 4;
			}

			if ($grade_list) {
				for ($y = 0; $y < count($studentlist); $y++) {

					if (!isset($studentlist[$y]['id'])) {
						continue;
					}

					$student_id  = $studentlist[$y]['id'];
					$final_grade = isset($studentlist[$y]['final_grade']) ? (float)$studentlist[$y]['final_grade'] : 0;
					$class_id    = isset($studentlist[$y]['class_id']) ? $studentlist[$y]['class_id'] : '';
					$strand_id   = isset($studentlist[$y]['strand_id']) ? $studentlist[$y]['strand_id'] : '';
					$is_shs      = ($class_id == 14 || $class_id == 15);

					$get_final_status = '';
					$current_status = array();
					$get_min_subject_grade = 0;
					$overall_min_subject_grade = null;

					/*
					|--------------------------------------------------------------------------
					| GLOBAL SUBJECT CHECK FOR FINAL AWARDS (SHS ONLY)
					| If any final subject grade is below 80, SHS student is not awardee
					|--------------------------------------------------------------------------
					*/
					if ($quarter == 'final' && $is_shs) {
						$overall_subjects = array();

						if (empty($semester)) {
							// semester 1
							$sem1_strand_id = $strand_id;
							$check_strand_sem1 = $this->studentstrand_model->get_latest_strand($student_id, 1);
							if ($check_strand_sem1) {
								$sem1_strand_id = $check_strand_sem1['strand_id'];
							}

							$get_subjects_sem1 = $this->specializedsubject_model->getByStrandSemester($sem1_strand_id, 1);
							$custom_subject_sem1 = $this->customsubject_model->getSubjectbySemester($student_id, 1);

							if ($custom_subject_sem1) {
								$get_subjects_sem1 = array_merge($get_subjects_sem1, $custom_subject_sem1);
							}

							$get_subjects_sem1 = $this->customsubject_model->unsetsubjectdrop($get_subjects_sem1, $student_id, 1);

							if ($get_subjects_sem1) {
								foreach ($get_subjects_sem1 as $subj) {
									$subj['semester_source'] = 1;
									$overall_subjects[] = $subj;
								}
							}

							// semester 2
							$sem2_strand_id = $strand_id;
							$check_strand_sem2 = $this->studentstrand_model->get_latest_strand($student_id, 2);
							if ($check_strand_sem2) {
								$sem2_strand_id = $check_strand_sem2['strand_id'];
							}

							$get_subjects_sem2 = $this->specializedsubject_model->getByStrandSemester($sem2_strand_id, 2);
							$custom_subject_sem2 = $this->customsubject_model->getSubjectbySemester($student_id, 2);

							if ($custom_subject_sem2) {
								$get_subjects_sem2 = array_merge($get_subjects_sem2, $custom_subject_sem2);
							}

							$get_subjects_sem2 = $this->customsubject_model->unsetsubjectdrop($get_subjects_sem2, $student_id, 2);

							if ($get_subjects_sem2) {
								foreach ($get_subjects_sem2 as $subj) {
									$subj['semester_source'] = 2;
									$overall_subjects[] = $subj;
								}
							}

						} else {
							// Single semester
							$check_strand = $this->studentstrand_model->get_latest_strand($student_id, $semester);
							if ($check_strand) {
								$get_strand = $check_strand['strand_id'];
							} else {
								$get_strand = $strand_id;
							}

							$overall_subjects = $this->specializedsubject_model->getByStrandSemester($get_strand, $semester);
							$custom_subject = $this->customsubject_model->getSubjectbySemester($student_id, $semester);

							if ($custom_subject) {
								$overall_subjects = array_merge($overall_subjects, $custom_subject);
							}

							$overall_subjects = $this->customsubject_model->unsetsubjectdrop($overall_subjects, $student_id, $semester);
						}

						$overall_subject_grade = array();

						if ($overall_subjects) {
							foreach ($overall_subjects as $subject_row) {
								$subject_id = isset($subject_row['subject_id']) ? $subject_row['subject_id'] : $subject_row['id'];
								$semester_source = isset($subject_row['semester_source']) ? $subject_row['semester_source'] : 0;

								if (!$subject_id) {
									continue;
								}

								$subject_manager_details = $this->subjectmanager_model->getBySubjectSession($subject_id);
								$display_card = isset($subject_manager_details['display_card']) ? $subject_manager_details['display_card'] : 'no';
								$include_computation = isset($subject_manager_details['include_computation']) ? $subject_manager_details['include_computation'] : 'no';

								if ($display_card != 'yes' || $include_computation != 'yes') {
									continue;
								}

								$checkifChild = $this->subjectcombine_model->checkifChild($subject_id);
								if (!empty($checkifChild)) {
									continue;
								}

								if (empty($semester)) {
									if ($semester_source == 1) {
										$subject_quarters = $first_sem_quarters;
									} elseif ($semester_source == 2) {
										$subject_quarters = $second_sem_quarters;
									} else {
										$subject_quarters = array(1, 2, 3, 4);
									}
								} else {
									$subject_quarters = $get_quarter;
								}

								$subject_total = 0;
								$subject_qtr_count = 0;

								foreach ($subject_quarters as $set_quarter) {
									$get_exam_by_subject = $this->examresult_model->get_exam_results_by_subject_quarter(
										$student_id,
										$set_quarter,
										$subject_id,
										$semester
									);

									if ($get_exam_by_subject && isset($get_exam_by_subject->grades) && $get_exam_by_subject->grades !== '') {
										$grade_result = (float)$get_exam_by_subject->grades;
									} else {
										$grade_result = $this->subjectcombine_model->getComputedCombinedGrade(
											$student_id,
											$subject_id,
											$set_quarter
										);
									}

									// FIX: updated
									if ($grade_result !== null && $grade_result !== '') {
										$subject_total += (float)$grade_result;
										$subject_qtr_count++;
									}
								}

								if ($subject_qtr_count > 0) {
									$subject_final_grade = $subject_total / $subject_qtr_count;

									if ($allow_to_pass == 'yes') {
										if ($subject_final_grade == 74.5 || ($subject_final_grade > 74.4 && $subject_final_grade < 75)) {
											$subject_final_grade = 75;
										}
									}

									$subject_final_grade = (int) round($subject_final_grade);
									$overall_subject_grade[] = (float)$subject_final_grade;
								}
							}
						}

						if (!empty($overall_subject_grade)) {
							$overall_min_subject_grade = min($overall_subject_grade);
						}
					}

					for ($x = 0; $x < count($grade_list); $x++) {

						if (!isset($grade_list[$x]['id'])) {
							continue;
						}

						$grade_id = $grade_list[$x]['id'];
						$marksgrade = $this->marksgrademanager_model->getMarksGrade($grade_id);

						if (!$marksgrade) {
							continue;
						}

						for ($z = 0; $z < count($marksgrade); $z++) {
							$based     = $marksgrade[$z]['based'];
							$type      = $marksgrade[$z]['type'];
							$type_id   = $marksgrade[$z]['type_id'];
							$operation = $marksgrade[$z]['operation'];
							$value     = $marksgrade[$z]['value'];

							if ($based == 'average') {

								if ($type == 'subject') {

									if ($type_id) {
										$checkSubjectByClass = $this->subject_model->checkSubjectByClass($class_id, $type_id);

										if ($checkSubjectByClass) {
											$check_manager = $this->subjectmanager_model->getBySubjectSession($type_id);
											$display_card = isset($check_manager['display_card']) ? $check_manager['display_card'] : 'no';

											if ($display_card == 'yes') {

												if ($quarter == 'final') {
													$subject_total = 0;
													$subject_qtr_count = 0;

													if ($is_shs && empty($semester)) {
														$specific_subject_quarters = array(1, 2, 3, 4);
													} else {
														$specific_subject_quarters = $get_quarter;
													}

													foreach ($specific_subject_quarters as $set_quarter) {
														$get_exam_by_subject = $this->examresult_model->get_exam_results_by_subject_quarter(
															$student_id,
															$set_quarter,
															$type_id,
															$semester
														);

														if ($get_exam_by_subject && isset($get_exam_by_subject->grades) && $get_exam_by_subject->grades !== '') {
															$grade_result = (float)$get_exam_by_subject->grades;
														} else {
															$checkifChild = $this->subjectcombine_model->checkifChild($type_id);
															if (empty($checkifChild)) {
																$grade_result = $this->subjectcombine_model->getComputedCombinedGrade(
																	$student_id,
																	$type_id,
																	$set_quarter
																);
															} else {
																$grade_result = null;
															}
														}

														if ($grade_result !== null && $grade_result !== '' && $grade_result > 0) {
															$subject_total += (float)$grade_result;
															$subject_qtr_count++;
														}
													}

													if ($subject_qtr_count > 0) {
														$subject_final_grade = $subject_total / $subject_qtr_count;

														if ($allow_to_pass == 'yes') {
															if ($subject_final_grade == 74.5 || ($subject_final_grade > 74.4 && $subject_final_grade < 75)) {
																$subject_final_grade = 75;
															}
														}

														$subject_final_grade = (float) number_format($subject_final_grade, $decimal_finalgrade, '.', '');
														$statement = $this->operation($operation, $subject_final_grade, $value);
														$current_status[$grade_id][] = $statement ? 'true' : 'false';
													} else {
														$current_status[$grade_id][] = 'false';
													}
												} else {
													$get_exam_by_subject = $this->examresult_model->get_exam_results_by_subject_quarter(
														$student_id,
														$quarter,
														$type_id,
														$semester
													);

													if ($get_exam_by_subject && isset($get_exam_by_subject->grades)) {
														$get_grade = (float) number_format($get_exam_by_subject->grades, $decimal_grades, '.', '');
														$statement = $this->operation($operation, $get_grade, $value);
														$current_status[$grade_id][] = $statement ? 'true' : 'false';
													} else {
														$current_status[$grade_id][] = 'false';
													}
												}
											}
										}
									} else {
										$grade_to_check = (float)$final_grade;
										$statement = $this->operation($operation, $grade_to_check, $value);
										$current_status[$grade_id][] = $statement ? 'true' : 'false';
									}

								} elseif ($type == 'conduct') {
									// add conduct logic here if needed
								}

							} elseif ($based == 'subject_grade') {

								$get_subjects = array();

								if ($is_shs && $quarter == 'final' && empty($semester)) {
									$merged_subjects = array();

									$sem1_strand_id = $strand_id;
									$check_strand_sem1 = $this->studentstrand_model->get_latest_strand($student_id, 1);
									if ($check_strand_sem1) {
										$sem1_strand_id = $check_strand_sem1['strand_id'];
									}

									$get_subjects_sem1 = $this->specializedsubject_model->getByStrandSemester($sem1_strand_id, 1);
									$custom_subject_sem1 = $this->customsubject_model->getSubjectbySemester($student_id, 1);

									if ($custom_subject_sem1) {
										$get_subjects_sem1 = array_merge($get_subjects_sem1, $custom_subject_sem1);
									}

									$get_subjects_sem1 = $this->customsubject_model->unsetsubjectdrop($get_subjects_sem1, $student_id, 1);

									if ($get_subjects_sem1) {
										foreach ($get_subjects_sem1 as $subj) {
											$subj['semester_source'] = 1;
											$merged_subjects[] = $subj;
										}
									}

									$sem2_strand_id = $strand_id;
									$check_strand_sem2 = $this->studentstrand_model->get_latest_strand($student_id, 2);
									if ($check_strand_sem2) {
										$sem2_strand_id = $check_strand_sem2['strand_id'];
									}

									$get_subjects_sem2 = $this->specializedsubject_model->getByStrandSemester($sem2_strand_id, 2);
									$custom_subject_sem2 = $this->customsubject_model->getSubjectbySemester($student_id, 2);

									if ($custom_subject_sem2) {
										$get_subjects_sem2 = array_merge($get_subjects_sem2, $custom_subject_sem2);
									}

									$get_subjects_sem2 = $this->customsubject_model->unsetsubjectdrop($get_subjects_sem2, $student_id, 2);

									if ($get_subjects_sem2) {
										foreach ($get_subjects_sem2 as $subj) {
											$subj['semester_source'] = 2;
											$merged_subjects[] = $subj;
										}
									}

									$get_subjects = $merged_subjects;

								} else {
									$enableStrand = $this->strand_model->checkEnable($class_id);

									if ($enableStrand) {
										$check_strand = $this->studentstrand_model->get_latest_strand($student_id, $semester);
										if ($check_strand) {
											$get_strand = $check_strand['strand_id'];
										} else {
											$get_strand = $strand_id;
										}

										$get_subjects = $this->specializedsubject_model->getByStrandSemester($get_strand, $semester);
										$custom_subject = $this->customsubject_model->getSubjectbySemester($student_id, $semester);

										if ($custom_subject) {
											$get_subjects = array_merge($get_subjects, $custom_subject);
										}

										$get_subjects = $this->customsubject_model->unsetsubjectdrop($get_subjects, $student_id, $semester);
									} else {
										$get_subjects = $this->subject_model->getSubjctByClass($class_id);
									}
								}

								$get_subject_grade = array();

								if ($get_subjects) {
									foreach ($get_subjects as $key_subject => $value_subject) {

										$check_subject_id = isset($value_subject['subject_id']) ? $value_subject['subject_id'] : $value_subject['id'];
										$semester_source = isset($value_subject['semester_source']) ? $value_subject['semester_source'] : 0;

										if (!$check_subject_id) {
											continue;
										}

										$subject_manager_details = $this->subjectmanager_model->getBySubjectSession($check_subject_id);
										$display_card = isset($subject_manager_details['display_card']) ? $subject_manager_details['display_card'] : 'no';
										$include_computation = isset($subject_manager_details['include_computation']) ? $subject_manager_details['include_computation'] : 'no';

										if ($display_card != 'yes' || $include_computation != 'yes') {
											continue;
										}

										$checkifChild = $this->subjectcombine_model->checkifChild($check_subject_id);
										if (!empty($checkifChild)) {
											continue;
										}

										if ($quarter == 'final') {
											$subject_total = 0;
											$subject_qtr_count = 0;

											if ($is_shs && empty($semester)) {
												if ($semester_source == 1) {
													$subject_quarters = $first_sem_quarters;
												} elseif ($semester_source == 2) {
													$subject_quarters = $second_sem_quarters;
												} else {
													$subject_quarters = array(1, 2, 3, 4);
												}
											} else {
												$subject_quarters = $get_quarter;
											}

											foreach ($subject_quarters as $set_quarter) {
												$get_exam_by_subject = $this->examresult_model->get_exam_results_by_subject_quarter(
													$student_id,
													$set_quarter,
													$check_subject_id,
													$semester
												);

												if ($get_exam_by_subject && isset($get_exam_by_subject->grades) && $get_exam_by_subject->grades !== '') {
													$grade_result = (float)$get_exam_by_subject->grades;
												} else {
													$grade_result = $this->subjectcombine_model->getComputedCombinedGrade(
														$student_id,
														$check_subject_id,
														$set_quarter
													);
												}

												if ($grade_result !== null && $grade_result !== '' && $grade_result > 0) {
													$subject_total += (float)$grade_result;
													$subject_qtr_count++;
												}
											}

											if ($subject_qtr_count > 0) {
												$subject_final_grade = $subject_total / $subject_qtr_count;

												if ($allow_to_pass == 'yes') {
													if ($subject_final_grade == 74.5 || ($subject_final_grade > 74.4 && $subject_final_grade < 75)) {
														$subject_final_grade = 75;
													}
												}

												$subject_final_grade = (int) round($subject_final_grade);
												$get_subject_grade[] = (float)$subject_final_grade;
											}
										} else {
											$get_exam_by_subject = $this->examresult_model->get_exam_results_by_subject_quarter(
												$student_id,
												$quarter,
												$check_subject_id,
												$semester
											);

											if ($get_exam_by_subject && isset($get_exam_by_subject->grades) && $get_exam_by_subject->grades !== '') {
												$grade_result = (float)$get_exam_by_subject->grades;
											} else {
												$grade_result = $this->subjectcombine_model->getComputedCombinedGrade(
													$student_id,
													$check_subject_id,
													$quarter
												);
											}

											if ($grade_result !== null && $grade_result !== '' && $grade_result > 0) {
												$get_subject_grade[] = (float)$grade_result;
											}
										}
									}

									if ($get_subject_grade) {
										$get_subject_grade = array_map('floatval', $get_subject_grade);
										$get_min_subject_grade = min($get_subject_grade);
									} else {
										$get_min_subject_grade = 0;
									}
								}

								if ($type == 'subject') {
									if (!$type_id) {
										$statement = $this->operation($operation, (float)$get_min_subject_grade, $value);
										$current_status[$grade_id][] = $statement ? 'true' : 'false';
									}
								} elseif ($type == 'conduct') {
									// add conduct logic here if needed
								}
							}
						}
					}

					if ($current_status) {
						foreach ($current_status as $key1 => $value1) {
							$check_if_true_exist = '';
							$check_if_false_exist = '';
							$get_status_id = $key1;

							for ($xyz = 0; $xyz < count($value1); $xyz++) {
								$get_value = $value1[$xyz];

								if ($get_value == 'true') {
									$check_if_true_exist = 'true';
								} elseif ($get_value == 'false') {
									$check_if_false_exist = 'true';
								}
							}

							if ($check_if_true_exist == 'true' && $check_if_false_exist != 'true') {
								$get_final_status = $get_status_id;
								break;
							}
						}
					}

					/*
					|--------------------------------------------------------------------------
					| HARD SAFEGUARD: SHS ONLY
					| If any final subject grade is below 80, strip the award
					|--------------------------------------------------------------------------
					*/
					if ($quarter == 'final' && $is_shs && $overall_min_subject_grade !== null && $overall_min_subject_grade < 80) {
						$get_final_status = '';
					}

					if ($get_final_status) {
						$details = $this->get($get_final_status);
						$background_color = isset($details['background_color']) ? $details['background_color'] : '';
						$text_color = isset($details['text_color']) ? $details['text_color'] : '';
						$name = isset($details['name']) ? $details['name'] : '';

						$studentlist[$y]['background_color'] = $background_color;
						$studentlist[$y]['text_color'] = $text_color;
						$studentlist[$y]['name'] = $name;
					} else {
						$studentlist[$y]['background_color'] = '';
						$studentlist[$y]['text_color'] = '';
						$studentlist[$y]['name'] = '';
					}
				}
			}

			if (isset($studentlist['quarter'])) {
				unset($studentlist['quarter']);
			}

			if (isset($studentlist['semester'])) {
				unset($studentlist['semester']);
			}

			return $studentlist;
		}

	public function get_student_grade($student_id, $semester_id = null, $quarter = null, $session_id = null) {
        if ($session_id == null) {
            $session_id = $this->current_session;
        }

        $this->load->model('gradingsetting_model');	
        $gradingsettings = $this->gradingsetting_model->get();

        $get_quarter = array();
        if ($semester_id == 1) {
            $get_quarter = !empty($gradingsettings->qtr_first_sem) ? $gradingsettings->qtr_first_sem : array('1', '2');
        } elseif ($semester_id == 2) {
            $get_quarter = !empty($gradingsettings->qtr_second_sem) ? $gradingsettings->qtr_second_sem : array('3', '4');
        } else {
            $get_quarter = array('1', '2', '3', '4');
        }
        $get_quarter = is_serialized($get_quarter) ? unserialize($get_quarter) : $get_quarter;

        $this->db->select('exam_results.subject_id, subjects.name as subject_name, exam_results.quarter, exam_results.get_marks');
        $this->db->from('exam_results');
        $this->db->join('subjects', 'subjects.id = exam_results.subject_id');
        $this->db->where('exam_results.student_id', $student_id);
        if ($session_id) {
            $this->db->where('exam_results.session_id', $session_id);
        }
        $this->db->where_in('exam_results.quarter', $get_quarter);
        $this->db->order_by('subjects.id', 'asc');
        $this->db->order_by('exam_results.quarter', 'asc');
        $results = $this->db->get()->result_array();

        $subjects_data = array();
        foreach ($results as $row) {
            $subject_id = $row['subject_id'];
            if (!isset($subjects_data[$subject_id])) {
                $subjects_data[$subject_id] = array(
                    'display_card' => 'yes', 
                    'subject_name' => $row['subject_name'],
                    'grades' => array()
                );
            }
            
            $q = $row['quarter'];
            $subjects_data[$subject_id]['grades'][$q] = array('quarterly_grade' => $row['get_marks']);
        }

        foreach ($subjects_data as $sub_id => &$sub_data) {
            $total = 0;
            $count = 0;
            foreach ($get_quarter as $q) {
                if (isset($sub_data['grades'][$q]['quarterly_grade'])) {
                    $total += $sub_data['grades'][$q]['quarterly_grade'];
                    $count++;
                }
            }
            if ($count > 0 && $count == count($get_quarter)) {
                $sub_data['grades']['final_grade'] = round($total / count($get_quarter));
            } else {
                $sub_data['grades']['final_grade'] = ''; 
            }
        }

        $formatted_subjects = array_values($subjects_data);

        return array(
            $student_id => array(
                'get_quarter' => $get_quarter,
                'subjects' => $formatted_subjects
            )
        );
    }
	
    public function get_letter_grade_special($grade, $subject_name) {
        $subject_name = strtolower(trim($subject_name));
        if (strpos($subject_name, 'homeroom') !== false || strpos($subject_name, 'work education') !== false) {
            if ($grade == null || $grade == '' || $grade == 0) {
                return $grade;
            }
            if ($grade >= 90) {
                return 'O';
            } elseif ($grade >= 85) {
                return 'VS';
            } elseif ($grade >= 80) {
                return 'S';
            } elseif ($grade >= 75) {
                return 'FS';
            } else {
                return 'DNME';
            }
        }
        return $grade;
    }
}