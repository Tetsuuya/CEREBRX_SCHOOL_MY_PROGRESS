<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Stuattendence_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_date = $this->setting_model->getDateYmd();
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function get($id = null) {
        $subject = $this->input->post('subject_id');
        $this->db->select()->from('student_attendences');
        if ($subject != null) {
            $this->db->where('subject_id', $subject);
        }
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
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
    public function add($data) { 

        $this->db->select("COUNT(id) as Counter");
        $this->db->from('student_attendences');
        $this->db->where("student_session_id", $data['student_session_id']);
        $this->db->where("DATE(date)", $data['date']);
        $this->db->where("subject_id", $data['subject_id']);
        $this->db->where("teacher_id", $data['teacher_id']);
        $query = $this->db->get();
        $row = $query->row();
        $counter = $row->Counter;   
   
        if( $counter == FALSE ){
             unset($data['id']);
        } 
        if (isset($data['id']) ) {
            $this->db->where('id', $data['id']);
            $this->db->update('student_attendences', $data);  
            return $data['id'];
        } else {

            date_default_timezone_set('Asia/Manila');
            $current_date = date('Y-m-d H:i:s');
            $data_time = array(
                'created_at' => $current_date 
            );

            $data_save = array_merge($data,$data_time ); 
            $this->db->insert('student_attendences', $data_save);
            return $this->db->insert_id(); 
        } 
    }
//     public function add($data) {
//     $this->db->select("COUNT(id) as Counter");
//     $this->db->from('student_attendences');
//     $this->db->where("student_session_id", $data['student_session_id']);
//     $this->db->where("DATE(date)", $data['date']);
//     $this->db->where("subject_id", $data['subject_id']);
//     $this->db->where("teacher_id", $data['teacher_id']);
//     $query = $this->db->get();
//     $row = $query->row();
//     $counter = $row->Counter;   
    
//     if ($counter == FALSE) {
//         unset($data['id']);
//     } 
    
//     if (isset($data['id'])) {
//         // Get existing excuse note if not being updated
//         if (!isset($data['excuse_note'])) {
//             $existing = $this->db->select('excuse_note')->where('id', $data['id'])->get('student_attendences')->row();
//             if ($existing) {
//                 $data['excuse_note'] = $existing->excuse_note;
//             }
//         }
        
//         $this->db->where('id', $data['id']);
//         $this->db->update('student_attendences', $data);  
//         return $data['id'];
//     } else {
//         date_default_timezone_set('Asia/Manila');
//         $current_date = date('Y-m-d H:i:s');
//         $data_time = array(
//             'created_at' => $current_date 
//         );

//         $data_save = array_merge($data, $data_time); 
//         $this->db->insert('student_attendences', $data_save);
//         return $this->db->insert_id(); 
//     } 
// }


    public function searchAttendenceClassSection($class_id, $section_id, $date) {

        $sql = "SELECT 
            ANY_VALUE(student_sessions.attendence_id) AS attendence_id,
            ANY_VALUE(students.status) AS status,
            ANY_VALUE(students.firstname) AS firstname,
            ANY_VALUE(student_sessions.date) AS date,
            ANY_VALUE(students.roll_no) AS roll_no,
            ANY_VALUE(students.lrn) AS lrn,
            ANY_VALUE(students.admission_no) AS admission_no,
            ANY_VALUE(students.lastname) AS lastname,
            ANY_VALUE(student_sessions.attendence_type_id) AS attendence_type_id,
            ANY_VALUE(student_sessions.id) AS student_session_id,
            ANY_VALUE(attendence_type.type) AS att_type,
            ANY_VALUE(attendence_type.key_value) AS `key`,
            ANY_VALUE(student_sessions.excuse_note) AS excuse_note,
            ANY_VALUE(student_sessions.exempted_note) AS exempted_note
        FROM 
            students, 
            (
                SELECT 
                    ANY_VALUE(student_session.id) AS id,
                    ANY_VALUE(student_session.student_id) AS student_id,
                    IFNULL(ANY_VALUE(student_attendences.date), 'xxx') AS date,
                    IFNULL(ANY_VALUE(student_attendences.id), '0') AS attendence_id,
                    ANY_VALUE(student_attendences.attendence_type_id) AS attendence_type_id,
                    ANY_VALUE(student_attendences.excuse_note) AS excuse_note,
                    ANY_VALUE(student_attendences.exempted_note)AS exempted_note
                FROM 
                    student_session 
                LEFT JOIN 
                    student_attendences 
                    ON student_attendences.student_session_id = student_session.id  
                    AND student_attendences.date = " . $this->db->escape($date) . "
                WHERE  
                    student_session.session_id = " . $this->db->escape($this->current_session) . " 
                    AND student_session.class_id = " . $this->db->escape($class_id) . " 
                    AND student_session.section_id = " . $this->db->escape($section_id) . " 
                GROUP BY 
                    student_session.student_id
            ) AS student_sessions 
        LEFT JOIN 
            attendence_type 
            ON attendence_type.id = student_sessions.attendence_type_id 
        WHERE 
            student_sessions.student_id = students.id";


     /*   student_sessions.attendence_id,students.status,students.firstname,student_sessions.date,students.roll_no,students.lrn,students.admission_no,students.lastname,student_sessions.attendence_type_id,student_sessions.id as student_session_id, attendence_type.type as `att_type`,attendence_type.key_value as `key` from students ,(SELECT student_session.id,student_session.student_id ,IFNULL(student_attendences.date, 'xxx') as date,IFNULL(student_attendences.id, 0) as attendence_id,student_attendences.attendence_type_id FROM `student_session` LEFT JOIN student_attendences ON student_attendences.student_session_id=student_session.id  and student_attendences.date=" . $this->db->escape($date) . " where  student_session.session_id=" . $this->db->escape($this->current_session) . " and student_session.class_id=" . $this->db->escape($class_id) . " and student_session.section_id=" . $this->db->escape($section_id) . " GROUP BY student_session.student_id ) as student_sessions LEFT JOIN attendence_type ON attendence_type.id=student_sessions.attendence_type_id where student_sessions.student_id=students.id";*/

  
        $query = $this->db->query($sql);
        return $query->result_array(); 
    }

    public function searchAttendenceClassSectionSubject($class_id, $section_id, $date, $subject) {

       /* $sql="select student_sessions.attendence_id,students.status,students.firstname,student_sessions.date,students.roll_no,students.lrn,students.admission_no,students.lastname,student_sessions.attendence_type_id,student_sessions.id as student_session_id, attendence_type.type as `att_type`,attendence_type.key_value as `key` from students ,(SELECT student_session.id,student_session.student_id ,IFNULL(student_attendences.date, 'xxx') as date,IFNULL(student_attendences.id, 0) as attendence_id,student_attendences.attendence_type_id FROM `student_session` LEFT JOIN student_attendences ON student_attendences.student_session_id=student_session.id  and student_attendences.date=" . $this->db->escape($date) . " where  student_session.session_id=" . $this->db->escape($this->current_session) . " and student_session.class_id=" . $this->db->escape($class_id) . " and student_session.section_id=" . $this->db->escape($section_id) . " AND student_attendences.subject_id=" . $this->db->escape($subject) . " GROUP BY student_session.student_id ) as student_sessions LEFT JOIN attendence_type ON attendence_type.id=student_sessions.attendence_type_id where student_sessions.student_id=students.id";*/
        $sql="select 
        ANY_VALUE(student_sessions.attendence_id ) as attendence_id,
        ANY_VALUE(students.status ) as status,
        ANY_VALUE(students.firstname ) as firstname,
        ANY_VALUE(student_sessions.date ) as date,
        ANY_VALUE(students.roll_no ) as roll_no,
        ANY_VALUE(students.lrn ) as lrn,
        ANY_VALUE(students.admission_no ) as admission_no,
        ANY_VALUE(students.lastname ) as lastname,
        ANY_VALUE(student_sessions.attendence_type_id ) as attendence_type_id,
        ANY_VALUE(student_sessions.id ) as student_session_id,
        ANY_VALUE(attendence_type.type ) as att_type,
        ANY_VALUE(attendence_type.key_value ) as `key`,
        ANY_VALUE(student_sessions.excuse_note ) as excuse_note,
        ANY_VALUE(student_sessions.exempted_note ) as exempted_note from students, 
        (SELECT 
             ANY_VALUE(student_session.id ) as id,
             ANY_VALUE(student_session.student_id ) as student_id,
             IFNULL(ANY_VALUE(student_attendences.date), 'xxx') as date,
             IFNULL(ANY_VALUE(student_attendences.id), '0') as attendence_id,
             ANY_VALUE(student_attendences.attendence_type_id) as attendence_type_id,
             ANY_VALUE(student_attendences.excuse_note) as excuse_note,
             ANY_VALUE(student_attendences.exempted_note) as exempted_note
             FROM `student_session` LEFT JOIN student_attendences ON student_attendences.student_session_id=student_session.id  and student_attendences.date=" . $this->db->escape($date) . " AND student_attendences.subject_id=" . $this->db->escape($subject) . " where  student_session.session_id=" . $this->db->escape($this->current_session) . " and student_session.class_id=" . $this->db->escape($class_id) . " and student_session.section_id=" . $this->db->escape($section_id) . " GROUP BY student_session.student_id ) as student_sessions LEFT JOIN attendence_type ON attendence_type.id=student_sessions.attendence_type_id where student_sessions.student_id=students.id";

  
        $query = $this->db->query($sql);
        return $query->result_array(); 
    }
    

    public function searchAttendenceClassSectionPrepare($class_id, $section_id, $date) {
        $query = $this->db->query("select student_sessions.attendence_id,students.firstname,students.admission_no,student_sessions.date,students.roll_no,students.lrn,students.lastname,student_sessions.attendence_type_id,student_sessions.id as student_session_id from students ,(SELECT student_session.id,student_session.student_id ,IFNULL(student_attendences.date, 'xxx') as date,IFNULL(student_attendences.id, 0) as attendence_id,student_attendences.attendence_type_id FROM `student_session` RIGHT JOIN student_attendences ON student_attendences.student_session_id=student_session.id  and student_attendences.date=". $this->db->escape($date) ." where  student_session.session_id=". $this->db->escape($this->current_session) ." and student_session.class_id=". $this->db->escape($class_id) ." and student_session.section_id=". $this->db->escape($section_id) .") as student_sessions where student_sessions.student_id=students.id");
        return $query->result_array(); 
    }

    public function get_attendance_by_date_user($user_id, $date  ) {
         
        $ret = FALSE;
        $this->db->select("student_attendences.*, attendence_type.type, attendence_type.key_value");
        $this->db->from('student_attendences');

        $this->db->join('attendence_type', 'student_attendences.attendence_type_id = attendence_type.id', 'left'); 
        $this->db->where("DATE(date)", $date);
        $this->db->where("student_session_id", $user_id);

        $query = $this->db->get(); 
        $query_result = $query->result_array();  
        $type = array();
        $new_array = array();
        if( is_array($query_result) ){
            foreach ($query_result as $key => $value) {
                $type[$key] = $value['type'];  
            } 
            if (in_array('Absent', $type, true)) {
                $key = array_search('Absent', $type );
                  $ret =  $query_result[ $key ];
 
            } elseif ( in_array('Late', $type, true) ) { 
                $key = array_search('Late', $type );
                  $ret =  $query_result[ $key ];
            } elseif ( in_array('Present', $type, true) ) { 
                $key = array_search('Present', $type );
                  $ret =  $query_result[ $key ];
            } elseif ( in_array('Late with excuse', $type, true) ) { 
                $key = array_search('Late with excuse', $type );
                  $ret =  $query_result[ $key ];
            }
          
             return $ret;
        } else {
            if( isset( $query_result['id'] ) && !empty( $query_result['id'] )){
                $ret =  $query_result;
            } 
            return $ret; 
        }
        // echo '<pre>'; print_r( $ret);exit();
        
    } 

    public function get_attendance_by_between_date_user($user_id, $start_date, $end_date ) {
         
        $ret = FALSE;
        $this->db->select("student_attendences.*, attendence_type.type, attendence_type.key_value");
        $this->db->from('student_attendences');

        $this->db->join('attendence_type', 'student_attendences.attendence_type_id = attendence_type.id', 'left');  
        $this->db->where('DATE(date) >=', $start_date);
        $this->db->where('DATE(date) <=', $end_date);

        $this->db->where("student_session_id", $user_id);

        $query = $this->db->get(); 
        $query_result = $query->result_array();  
         
        return $query_result;  
        
    } 

    public function get_attendance_by_date_user_display($user_id, $date, $subject = null ) {
          
        $ret = FALSE;
        $this->db->select("student_attendences.*, attendence_type.type, attendence_type.key_value");
        $this->db->from('student_attendences');
        $this->db->join('attendence_type', 'student_attendences.attendence_type_id = attendence_type.id', 'left'); 
        $this->db->where("DATE(date)", $date);
        $this->db->where("student_session_id", $user_id);
        if (!empty($subject)) {
            $this->db->where("subject_id", $subject);
        }

        $query = $this->db->get(); 
        $query_result = $query->result_array();  
        $display_array = array();
        $display  = '';
        if(  $query_result  ){
            foreach ($query_result as $value) {
                 $display_array[] = $this->format_attendance_key_value($value);
            } 
            $display_array = array_unique($display_array);
            $display = implode (", ", $display_array);
        } 

        return $display;
        
    }

    private function format_attendance_key_value($attendance) {
        $key_value = isset($attendance['key_value']) ? $attendance['key_value'] : '';
        $key_text = trim(strip_tags($key_value));
        $note = '';
        $title = '';

        if ($key_text === 'Ex') {
            $note = isset($attendance['excuse_note']) ? trim($attendance['excuse_note']) : '';
            $title = 'Excuse note';
        } elseif ($key_text === 'Exm') {
            $note = isset($attendance['exempted_note']) ? trim($attendance['exempted_note']) : '';
            $title = 'Exempted note';
        }

        if ($note === '') {
            return $key_value;
        }

        return '<button type="button" class="btn btn-xs btn-link attendance-note-btn" data-note-title="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" data-note="' . htmlspecialchars($note, ENT_QUOTES, 'UTF-8') . '" onclick="alert(this.getAttribute(\'data-note-title\') + \'\n\n\' + this.getAttribute(\'data-note\')); return false;">' . $key_value . '</button>';
    }

   /*  public function get_attendance_by_user_count_status($user_id, $month , $year, $type=FALSE  ) {
         
        $ret = FALSE;
        $this->db->select("COUNT(id) as Counter");
        $this->db->from('student_attendences');
        $this->db->where("Month(date)", $month);
        $this->db->where("Year(date)", $year);
        $this->db->where("student_session_id", $user_id);
        if( $type == TRUE ){
             $this->db->where("attendence_type_id", $type);
        }
        $query = $this->db->get(); 
        $query_result = $query->row_array();  
        $counter = $query_result['Counter'];
 
        return $counter; 
    } */
	
	/*  public function get_attendance_by_user_count_status($user_id, $month , $year, $type=''  ) {
         
        $ret = FALSE;
        $this->db->select("student_attendences.id as Counter");
        $this->db->from('student_attendences');
		$this->db->join('student_session', 'student_attendences.student_session_id = student_session.id'); 
        $this->db->where("Month(date)", $month);
        $this->db->where("Year(date)", $year);
        $this->db->where("student_id", $user_id);
        $this->db->where("student_session.session_id", $this->current_session);
        if( $type ){
             $this->db->where("attendence_type_id", $type);
        }
		$this->db->group_by("date");
        $query = $this->db->get(); 
        $query_result = $query->num_rows();  

        $counter = $query_result;
		
        return $counter; 
    } */
	
	public function check_date_attendance( $user_id, $day, $month, $year , $session_id ){
		if( empty( $session_id )){
			$session_id = $this->current_session;
		}
		$query = $this->db->query('
			SELECT 
			COUNT(CASE WHEN `attendence_type_id`= "1" THEN 1 END ) AS present,
			COUNT(CASE WHEN `attendence_type_id`= "4" THEN 1 END ) AS absent,
			COUNT(CASE WHEN `attendence_type_id`= "2" THEN 1 END ) AS holiday,
			COUNT(CASE WHEN `attendence_type_id`= "3" OR `attendence_type_id` = "2"  THEN 1 END ) AS late
			FROM student_attendences
			JOIN student_session ON student_attendences.student_session_id = student_session.id 
			WHERE DAY(`date`) = '.$day.' AND MONTH(`date`) = '.$month.' AND YEAR(`date`) = '.$year.' AND student_id = '.$user_id.' AND student_session.session_id = '. $session_id.'
			GROUP by date'); 
		  $result = $query->row_array(); 

		  return $result;
	}
	
	
	public function get_attendance_by_user_count_status($user_id, $month , $year, $type='', $session_id = null  ) {
		if( empty( $session_id )){
			$session_id = $this->current_session;
		}
	
		$total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year ); // 31
		$total_absent = 0;
		$total_late = 0;
		$total_present = 0;
		$score = 0;
		$consecutive_days = 0;
		for( $x=1;$x<=$total_days;$x++){
			 $check_attendance = $this->check_date_attendance( $user_id, $x, $month, $year, $session_id );
			 $check_if_present = $check_attendance['present'];
			 $check_if_absent = $check_attendance['absent'];
			 $check_if_late = $check_attendance['late'];
			 $check_if_holiday = $check_attendance['holiday'];
			if( $check_if_absent != 0  && $check_if_present != 0 ){
				$total_present = $total_present + .5 ;
				$total_absent = $total_absent + .5 ;
			} elseif($check_if_absent != 0 && $check_if_present == 0 ){
				$total_absent = $total_absent + 1 ;
			} elseif( $check_if_present != 0 && $check_if_absent == 0 && $check_if_holiday == 0  ){
				$total_present = $total_present + 1 ;
			} /* elseif( $check_if_present == 0 && $check_if_late != 0 ){
				$total_present = $total_present + 1 ;
			} */
			
			if( $check_if_late != 0 ){
				$total_late = $total_late + 1 ;
				 $consecutive_days++;
				 if( $consecutive_days == 3){
					$total_absent = $total_absent + 1 ;
					$consecutive_days=0;
				 }
			} else {
				 $consecutive_days = 0;
			}
			
		}

		if( $type == 1 ){
			return $total_present;
		} elseif( $type == 4 ){
			return $total_absent;
		} elseif( $type == 3 ){
			return $total_late;
		}
		

		if( $type == 1 ){
			return $total_present;
		} elseif( $type == 4 ){
			return $total_absent;
		} elseif( $type == 3 ){
			return $total_late;
		}
    }
	
	public function get_total_present( $student_id, $session_id=null){
		if( $session_id == null ){
			$session_id = $this->current_session;
		}
		$gradingsetting_model = $this->gradingsetting_model->getbySession( $session_id );
		$school_days = $gradingsetting_model->school_days;
		if( $school_days ){
			$school_days = is_serialized( $school_days )?unserialize($school_days):$school_days; 
			$school_days = $school_days != '' ? $school_days:array();
		}
        $startMonthName = $gradingsetting_model->school_days_first_month;
        $endMonthName = $gradingsetting_model->school_days_last_month;
 
        $startMonth = $this->setting_model->getStartMonth();
        $endMonth = $this->setting_model->getEndMonth();

       // $session_current = $this->setting_model->getCurrentSessionName(); 
	   $session_details = $this->session_model->get( $session_id );
		$session_current = $session_details['session'];
        $centenary = substr($session_current, 0, 2); //2017-18 to 2017
        $year_first_substring = substr($session_current, 2, 2); //2017-18 to 2017
        $year_second_substring = substr($session_current, 5, 2); //2017-18 to 18
        
         $dateObj   = DateTime::createFromFormat('!m', $startMonth);
        // $startMonthName = $dateObj->format('F'); // March
        $startMonthName = isset( $startMonthName )?$startMonthName:'August';
         $dateObj2   = DateTime::createFromFormat('!m', $endMonth);
        // $endMonthName = $dateObj2->format('F'); // March
        $endMonthName = isset( $startMonthName )?$endMonthName:'March';
        
        $start_timestamp = strtotime($startMonthName.' '. $centenary.$year_first_substring);
        $end_timestamp    = strtotime($endMonthName.' '. $centenary.$year_second_substring);
        $first_second = date('Y-m-01', $start_timestamp);
        $last_second  = date('Y-m-t', $end_timestamp); // A leap year!
 
        $data['startMonth'] = $first_second;
        $data['endMonth'] = $last_second; 
        $data['first_second'] = $first_second; 
        $data['last_second'] = $last_second; 
		$end = strtotime($last_second); 
		$month =  ''; 
		$total_month_count =  0; 
		$month_count = 0;
		$start = $month = strtotime($first_second); 
		$student_attendance_present_array = $this->stuattendencecustom_model->get_attendance( $student_id, 'Present', $session_id );
		$studdent_present_attendance = $student_attendance_present_array['attendance'];
		$student_attendance_present_serialize = is_serialized( $studdent_present_attendance )?unserialize( $studdent_present_attendance ): $studdent_present_attendance ;
		//printx( $student_attendance_present_serialize ); 
		while($month < $end){   
			$monthly_name_fd = date('M_Y', $month); 
			if( isset( $student_attendance_present_serialize[$monthly_name_fd])){
				$month_count = $student_attendance_present_serialize[$monthly_name_fd];
			} else {
				$monthly_name  = date('m', $month);   
				$monthly_name_year = date('Y', $month);  
				$month_count = $this->stuattendence_model->get_attendance_by_user_count_status(  $student_id, $monthly_name , $monthly_name_year, 1  ); 
					
			}
			$total_month_count += $month_count;   
			$month = strtotime("+1 month", $month);    
		} 
		
		return $total_month_count;
	}
	
	public function get_total_absent( $student_id, $session_id=null){
		if( $session_id == null ){
			$session_id = $this->current_session;
		}
		$gradingsetting_model = $this->gradingsetting_model->getbySession( $session_id );
		$school_days = $gradingsetting_model->school_days;
		if( $school_days ){
			$school_days = is_serialized( $school_days )?unserialize($school_days):$school_days; 
			$school_days = $school_days != '' ? $school_days:array();
		}
        $startMonthName = $gradingsetting_model->school_days_first_month;
        $endMonthName = $gradingsetting_model->school_days_last_month;
 
        $startMonth = $this->setting_model->getStartMonth();
        $endMonth = $this->setting_model->getEndMonth();

       // $session_current = $this->setting_model->getCurrentSessionName(); 
	   $session_details = $this->session_model->get( $session_id );
		$session_current = $session_details['session'];
        $centenary = substr($session_current, 0, 2); //2017-18 to 2017
        $year_first_substring = substr($session_current, 2, 2); //2017-18 to 2017
        $year_second_substring = substr($session_current, 5, 2); //2017-18 to 18
        
         $dateObj   = DateTime::createFromFormat('!m', $startMonth);
        // $startMonthName = $dateObj->format('F'); // March
        $startMonthName = isset( $startMonthName )?$startMonthName:'August';
         $dateObj2   = DateTime::createFromFormat('!m', $endMonth);
        // $endMonthName = $dateObj2->format('F'); // March
        $endMonthName = isset( $startMonthName )?$endMonthName:'March';
        
        $start_timestamp = strtotime($startMonthName.' '. $centenary.$year_first_substring);
        $end_timestamp    = strtotime($endMonthName.' '. $centenary.$year_second_substring);
        $first_second = date('Y-m-01', $start_timestamp);
        $last_second  = date('Y-m-t', $end_timestamp); // A leap year!
 
        $data['startMonth'] = $first_second;
        $data['endMonth'] = $last_second; 
        $data['first_second'] = $first_second; 
        $data['last_second'] = $last_second; 
		$end = strtotime($last_second); 
		$month =  ''; 
		$total_month_count =  0; 
		$month_count = 0;
		$start = $month = strtotime($first_second); 
		$student_attendance_present_array = $this->stuattendencecustom_model->get_attendance( $student_id, 'Absent', $session_id );
		$studdent_present_attendance = $student_attendance_present_array['attendance'];
		$student_attendance_present_serialize = is_serialized( $studdent_present_attendance )?unserialize( $studdent_present_attendance ): $studdent_present_attendance ;
		//printx( $student_attendance_present_serialize ); 
		while($month < $end){   
			$monthly_name_fd = date('M_Y', $month); 
			if( isset( $student_attendance_present_serialize[$monthly_name_fd])){
				$month_count = $student_attendance_present_serialize[$monthly_name_fd];
			} else {
				$monthly_name  = date('m', $month);   
				$monthly_name_year = date('Y', $month);  
				$month_count = $this->stuattendence_model->get_attendance_by_user_count_status(  $student_id, $monthly_name , $monthly_name_year, 4  ); 
					
			}
			$total_month_count += $month_count;   
			$month = strtotime("+1 month", $month);    
		} 
		
		return $total_month_count;
	}
	
	public function get_total_late( $student_id, $session_id=null){
		if( $session_id == null ){
			$session_id = $this->current_session;
		}
		$gradingsetting_model = $this->gradingsetting_model->getbySession( $session_id );
		$school_days = $gradingsetting_model->school_days;
		if( $school_days ){
			$school_days = is_serialized( $school_days )?unserialize($school_days):$school_days; 
			$school_days = $school_days != '' ? $school_days:array();
		}
        $startMonthName = $gradingsetting_model->school_days_first_month;
        $endMonthName = $gradingsetting_model->school_days_last_month;
 
        $startMonth = $this->setting_model->getStartMonth();
        $endMonth = $this->setting_model->getEndMonth();

       // $session_current = $this->setting_model->getCurrentSessionName(); 
	   $session_details = $this->session_model->get( $session_id );
		$session_current = $session_details['session'];
        $centenary = substr($session_current, 0, 2); //2017-18 to 2017
        $year_first_substring = substr($session_current, 2, 2); //2017-18 to 2017
        $year_second_substring = substr($session_current, 5, 2); //2017-18 to 18
        
         $dateObj   = DateTime::createFromFormat('!m', $startMonth);
        // $startMonthName = $dateObj->format('F'); // March
        $startMonthName = isset( $startMonthName )?$startMonthName:'August';
         $dateObj2   = DateTime::createFromFormat('!m', $endMonth);
        // $endMonthName = $dateObj2->format('F'); // March
        $endMonthName = isset( $startMonthName )?$endMonthName:'March';
        
        $start_timestamp = strtotime($startMonthName.' '. $centenary.$year_first_substring);
        $end_timestamp    = strtotime($endMonthName.' '. $centenary.$year_second_substring);
        $first_second = date('Y-m-01', $start_timestamp);
        $last_second  = date('Y-m-t', $end_timestamp); // A leap year!
 
        $data['startMonth'] = $first_second;
        $data['endMonth'] = $last_second; 
        $data['first_second'] = $first_second; 
        $data['last_second'] = $last_second; 
		$end = strtotime($last_second); 
		$month =  ''; 
		$total_month_count =  0; 
		$month_count = 0;
		$start = $month = strtotime($first_second); 
		$student_attendance_present_array = $this->stuattendencecustom_model->get_attendance( $student_id, 'Late', $session_id );
		$studdent_present_attendance = $student_attendance_present_array['attendance'];
		$student_attendance_present_serialize = is_serialized( $studdent_present_attendance )?unserialize( $studdent_present_attendance ): $studdent_present_attendance ;
		//printx( $student_attendance_present_serialize ); 
		while($month < $end){   
			$monthly_name_fd = date('M_Y', $month); 
			if( isset( $student_attendance_present_serialize[$monthly_name_fd])){
				$month_count = $student_attendance_present_serialize[$monthly_name_fd];
			} else {
				$monthly_name  = date('m', $month);   
				$monthly_name_year = date('Y', $month);  
				$month_count = $this->stuattendence_model->get_attendance_by_user_count_status(  $student_id, $monthly_name , $monthly_name_year, 3  ); 
					
			}
			$total_month_count += $month_count;   
			$month = strtotime("+1 month", $month);    
		} 
		
		return $total_month_count;
	}

    public function get_top_10_late_absent(){ 
        $todays_month = date("m");

        $this->db->select("COUNT(student_attendences.attendence_type_id) AS 'counter'");
        $this->db->select("ANY_VALUE (student_attendences.student_session_id) as student_session_id");
        $this->db->select("ANY_VALUE (student_attendences.attendence_type_id) as attendence_type_id");
        $this->db->select("ANY_VALUE (students.lastname) as lastname");
        $this->db->select("ANY_VALUE (students.firstname) as firstname");
        $this->db->select('ANY_VALUE (student_session.class_id) as class_id');
        $this->db->select('ANY_VALUE (classes.class) as class');
        $this->db->select('ANY_VALUE (student_session.section_id) as section_id');
        $this->db->select('ANY_VALUE (sections.section) as section');
        $this->db->from('student_attendences'); 
        $this->db->join('student_session', 'student_attendences.student_session_id = student_session.id', 'left'); 
        $this->db->join('students', 'student_session.student_id = students.id', 'left');  
        $this->db->join('classes', 'student_session.class_id = classes.id', 'left'); 
        $this->db->join('sections', 'student_session.section_id = sections.id', 'left'); 
        $this->db->where("student_session.session_id", $this->current_session); 
        $this->db->where("Month(student_attendences.date)", $todays_month); 
        $this->db->where("(student_attendences.attendence_type_id = '3'  OR student_attendences.attendence_type_id = '4'  )", NULL, FALSE);  
        $this->db->group_by('student_attendences.student_session_id'); 
        $this->db->order_by('counter', 'desc'); 
        $this->db->limit(10);

        $query = $this->db->get(); 
        $query_result = $query->result_array();  
    

        $return_array = array();
        return $query_result;  
 
    }

    public function get_late_absent_list( $todays_month,  $class_id,  $section_id ){  
        $nmonth = date("m", strtotime($todays_month));
        $this->db->select("COUNT(student_attendences.attendence_type_id) AS 'counter'");
        $this->db->select("student_attendences.student_session_id");
        $this->db->select("student_attendences.attendence_type_id");
        $this->db->select("students.lastname");
        $this->db->select("students.firstname");
        $this->db->select('student_session.class_id');
        $this->db->select('classes.class');
        $this->db->select('student_session.section_id');
        $this->db->select('sections.section');
        $this->db->from('student_attendences'); 
        $this->db->join('student_session', 'student_attendences.student_session_id = student_session.id', 'left'); 
        $this->db->join('students', 'student_session.student_id = students.id', 'left');  
        $this->db->join('classes', 'student_session.class_id = classes.id', 'left'); 
        $this->db->join('sections', 'student_session.section_id = sections.id', 'left'); 
        $this->db->where("student_session.session_id", $this->current_session); 
        $this->db->where("student_session.class_id", $class_id ); 
        $this->db->where("student_session.section_id", $section_id ); 
        $this->db->where("Month(student_attendences.date)", $nmonth); 
        $this->db->where("(  student_attendences.attendence_type_id = '3'  OR student_attendences.attendence_type_id = '4'  )", NULL, FALSE);  
        $this->db->group_by('student_attendences.student_session_id'); 
        $this->db->order_by('counter', 'desc');  

        $query = $this->db->get(); 
        $query_result = $query->result_array();  
         
        return $query_result;  
 
    }
	
	  public function get_absent(){ 
        $todays_month = date("Y-m-d");
        $this->db->select("ANY_VALUE (student_attendences.student_session_id) as student_session_id");
		$this->db->select("ANY_VALUE (student_attendences.subject_id) as subject_id");		
        $this->db->select("ANY_VALUE (student_attendences.attendence_type_id) as attendence_type_id");		
        $this->db->select("ANY_VALUE (student_attendences.teacher_id) as teacher_id");		
        $this->db->select("ANY_VALUE (students.lastname) AS lastname ");
        $this->db->select("ANY_VALUE (students.firstname) as firstname");
		$this->db->select("ANY_VALUE (students.guardian_phone) AS 'contact'");       
		$this->db->select('ANY_VALUE (student_session.class_id) as class_id');	   
        $this->db->select('ANY_VALUE (student_session.section_id) as section_id');
		$this->db->select('ANY_VALUE (classes.class) as class');		
        $this->db->select('ANY_VALUE (sections.section) as section');		
		$this->db->select("ANY_VALUE (subjects.name) AS 'subjectname'");		
		$this->db->select("ANY_VALUE (teachers.name) AS 'teacher_name'");
		$this->db->select("ANY_VALUE (teachers.lastname) AS 'teacher_lastname'");	       
		$this->db->from('student_attendences');  
		
		$this->db->join('student_session', 'student_attendences.student_session_id = student_session.id', 'left'); 
        $this->db->join('students', 'student_session.student_id = students.id', 'left');  
        $this->db->join('classes', 'student_session.class_id = classes.id', 'left'); 
        $this->db->join('sections', 'student_session.section_id = sections.id', 'left');
		$this->db->join('subjects', 'student_attendences.subject_id = subjects.id', 'left');		
		$this->db->join('teachers', 'student_attendences.teacher_id = teachers.id', 'left'); 
        
		$this->db->where("student_session.session_id", $this->current_session); 
        $this->db->where("student_attendences.date", $todays_month);
        $this->db->where("(student_attendences.attendence_type_id = '4'  )", NULL, FALSE);  
        $this->db->group_by('student_attendences.student_session_id'); 
        $this->db->order_by('lastname', 'asc'); 
        
        $query = $this->db->get(); 
        $query_result = $query->result_array();  
         
        return $query_result;  
 
    }
	
	 public function countSubjectByTeacherUsing( $teacher_id ){ 
		$count = 0; 
        $todays_month = date("Y-m-d");
        $this->db->select("COUNT(student_attendences.id) as count_subject ");       
		$this->db->from('student_attendences');  
        $this->db->where("student_attendences.date", $todays_month);
        $this->db->where("student_attendences.teacher_id", $teacher_id);
		$this->db->group_by('student_attendences.student_session_id'); 
        $this->db->group_by('student_attendences.subject_id'); 
        
        $query = $this->db->get(); 
        $query_result = $query->row_array();  
         
		if( $query_result ){
			$count = $query_result['count_subject'];
		}
        return $count;  
 
    }

    public function countSubjectByTeacherUsingattendance( $teacher_id ){ 
        $count = 0; 
        $todays_month = date("Y-m-d");
        // $this->db->select("COUNT(student_attendences.id) as count_subject ");       
        $this->db->select("*");       
        $this->db->from('student_attendences');  
        $this->db->join('student_session', 'student_session.id = student_attendences.student_session_id', 'left');
        $this->db->where("student_attendences.date", $todays_month);
        $this->db->where("student_attendences.teacher_id", $teacher_id);  
        $this->db->group_by('student_session.class_id');  
        $this->db->group_by('student_session.section_id');  
        
        $query = $this->db->get(); 
        $query_result = $query->result_array();  
        $query_result_count = count( $query_result );
       /* if( $query_result ){
            $count = $query_result['count_subject'];
        }*/
        return $query_result_count;  
 
    }
	
	public function list_teacher_attendance_checked( $teacher_id, $section_id, $class_id, $subject_id, $date ){ 
		$id='';
		$date_format= date('Y-m-d', strtotime($date));
		$current_date = date('Y-m-d H:i:s');       
	   $data = array(
		
            'teacher_id' => $teacher_id,
            'subject_id' => $subject_id,
			'class_id' => $class_id,
			'section_id' => $section_id,
			'date' => date($date_format),
			'created_at' => $current_date
        );		
	    $this->db->insert('teacher_checked_attendance', $data);
		
		return $this->db->insert_id();
	}
		 
	
	public function get_attendance_checked( $id ){ 
        $todays_month = date("Y-m-d"); 
        /* $this->db->select("teacher_checked_attendance.teacher_id");       
		$this->db->select("teacher_checked_attendance.subject_id");
		$this->db->select("teacher_checked_attendance.section_id");		
		$this->db->select("teacher_checked_attendance.class_id");		
		$this->db->select("teacher_checked_attendance.date");
		$this->db->select("subjects.name");
		$this->db->select("sections.section");
		$this->db->select("classes.class ");	
		
		$this->db->from('teacher_checked_attendance');  
        $this->db->where("teacher_checked_attendance.date", $todays_month);
        $this->db->where("teacher_checked_attendance.teacher_id", $id);
		$this->db->group_by("teacher_checked_attendance.section_id");
		$this->db->group_by("teacher_checked_attendance.date");
		
		$this->db->join('classes', 'teacher_checked_attendance.class_id = classes.id'); 
        $this->db->join('sections', 'teacher_checked_attendance.section_id = sections.id');
		$this->db->join('subjects', 'teacher_checked_attendance.subject_id = subjects.id');		
		       
        $query = $this->db->get(); 
        $query_result = $query->result_array();   */
		 
		//adduct 
		$this->db->select('teacher_subjects.id');
		$this->db->select('teacher_subjects.session_id');
		$this->db->select('teacher_subjects.class_section_id');
		$this->db->select('teacher_subjects.subject_id');
		$this->db->select('subjects.name as subject_name');
		$this->db->select('teacher_subjects.teacher_id'); 
		$this->db->select('class_sections.class_id');
		$this->db->select('classes.class');
		$this->db->select('class_sections.section_id');
		$this->db->select('sections.section');
		$this->db->from('teacher_subjects'); 
		$this->db->join('class_sections', 'class_sections.id = teacher_subjects.class_section_id', 'left');
		$this->db->join('classes', 'classes.id = class_sections.class_id', 'left');
		$this->db->join('sections', 'sections.id = class_sections.section_id', 'left');
		$this->db->join('subjects', 'subjects.id = teacher_subjects.subject_id', 'left');
		$this->db->where('teacher_subjects.teacher_id', $id);
		$this->db->where('teacher_subjects.session_id', $this->current_session);   
		$query2 = $this->db->get(); 
		$allsubjectarray =  $query2->result_array();  
		$return_array = array();
		if( $allsubjectarray ){
			foreach( $allsubjectarray as $allsubject ){ 
		 
				$teacher_subjects_id = $allsubject['id']; 
				$teacher_subjects_session_id = $allsubject['session_id'];
				$teacher_subjects_class_section_id = $allsubject['class_section_id'];
				$teacher_subjects_subject_id = $allsubject['subject_id'];
				$teacher_subjects_teacher_id = $allsubject['teacher_id'];
				$teacher_subjects_class_id = $allsubject['class_id'];
				$teacher_subjects_section_id = $allsubject['section_id']; 
					 
				$this->db->select('*');
				$this->db->from('teacher_checked_attendance');  
				$this->db->where('teacher_id', $teacher_subjects_teacher_id);
				$this->db->where('subject_id', $teacher_subjects_subject_id);
				$this->db->where('class_id', $teacher_subjects_class_id);
				$this->db->where('section_id', $teacher_subjects_section_id);
				$this->db->where('date', $todays_month); 
				$query_check_if_check = $this->db->get(); 
				$ischeck =  $query_check_if_check->row_array(); 
				if( $ischeck ){
					$allsubject['status'] = 'checked';
				} else {
					$allsubject['status'] = 'unchecked';
				}
				$return_array[] = $allsubject;
				 
			}
			
		} 
	    return $return_array;  
	}

     public function get_attendance_checked_by_teacher_subject_today( $id ){ 
        $todays_month = date("Y-m-d"); 
       
        $this->db->select('teacher_subjects.id');
        $this->db->select('teacher_subjects.session_id');
        $this->db->select('teacher_subjects.class_section_id');
        $this->db->select('teacher_subjects.subject_id');
        $this->db->select('subjects.name as subject_name');
        $this->db->select('teacher_subjects.teacher_id'); 
        $this->db->select('class_sections.class_id');
        $this->db->select('classes.class');
        $this->db->select('class_sections.section_id');
        $this->db->select('sections.section');
        $this->db->from('teacher_subjects'); 
        $this->db->join('class_sections', 'class_sections.id = teacher_subjects.class_section_id', 'left');
        $this->db->join('classes', 'classes.id = class_sections.class_id', 'left');
        $this->db->join('sections', 'sections.id = class_sections.section_id', 'left');
        $this->db->join('subjects', 'subjects.id = teacher_subjects.subject_id', 'left');
        $this->db->where('teacher_subjects.teacher_id', $id);
        $this->db->where('teacher_subjects.session_id', $this->current_session);   
        $this->db->group_by('classes.id');
        $this->db->group_by('sections.id'); 
        $this->db->group_by('subjects.id'); 
        $this->db->group_by('teacher_subjects.teacher_id'); 
        $query2 = $this->db->get(); 
        $allsubjectarray =  $query2->result_array();  

        // printx($allsubjectarray);
        $return_array = array();
        if( $allsubjectarray ){
            foreach( $allsubjectarray as $allsubject ){ 
         
                $teacher_subjects_id = $allsubject['id']; 
                $teacher_subjects_session_id = $allsubject['session_id'];
                $teacher_subjects_class_section_id = $allsubject['class_section_id'];
                $teacher_subjects_subject_id = $allsubject['subject_id'];
                $teacher_subjects_teacher_id = $allsubject['teacher_id'];
                $teacher_subjects_class_id = $allsubject['class_id'];
                $teacher_subjects_section_id = $allsubject['section_id']; 
                     
                $this->db->select('*');
                $this->db->from('student_attendences');  
                $this->db->join('student_session', 'student_session.id = student_attendences.student_session_id', 'left');
                $this->db->where('student_attendences.teacher_id', $teacher_subjects_teacher_id);
                $this->db->where('student_attendences.subject_id', $teacher_subjects_subject_id);
                $this->db->where('student_session.class_id', $teacher_subjects_class_id);
                $this->db->where('student_session.section_id', $teacher_subjects_section_id);
                $this->db->where('student_attendences.date', $todays_month); 
                $query_check_if_check = $this->db->get(); 
                $ischeck =  $query_check_if_check->row_array(); 
                // printr(  $this->db->last_query() );
                if( $ischeck ){
                    $allsubject['status'] = 'checked';
                // printr(  $ischeck );
                } else {
                    $allsubject['status'] = 'unchecked';
                }
                $return_array[] = $allsubject;
                 
            }
            
        } 
        // exit();

        return $return_array;  
    }
	
	public function get_teacher_attendance_checked( $teacher_id, $section_id, $class_id, $subject_id, $date ){ 
		$this->db->select('*');
		$this->db->from('teacher_checked_attendance'); 
		$this->db->where('teacher_checked_attendance.teacher_id', $teacher_id);
		$this->db->where('teacher_checked_attendance.section_id', $section_id);
		$this->db->where('teacher_checked_attendance.class_id', $class_id);
		$this->db->where('teacher_checked_attendance.subject_id', $subject_id);
		$this->db->where('teacher_checked_attendance.date', $date);   
		$query2 = $this->db->get(); 
		return $query2->row_array();  
		
		
	}
	
	public function searchAttendenceClassSectionByGender($class_id, $section_id, $date, $gender = 'Male',$subject, $session_id=null) {
        if( empty($session_id) ){
			$session_id = $this->current_session;
		}
		$session_filter = " AND ss.session_id = " . $this->db->escape($session_id);
		$subject_filter = "";
        if (!empty($subject)) {
            $subject_filter = " AND sa.subject_id = " . $this->db->escape($subject);
        }
		$sql = "SELECT 
            ss.id AS student_session_id,
            ss.session_id,
            s.id AS student_id,
            s.firstname,
            s.lastname,
            s.suffix,
            s.middlename,
            s.roll_no,
            s.lrn,
            s.admission_no,
            s.status,
            sa.id AS attendence_id,
            sa.date,
            sa.attendence_type_id,
            sa.subject_id,
            sa.excuse_note,
            sa.exempted_note,
            at.type AS att_type,
            at.key_value AS `key`
        FROM student_session ss
        JOIN students s ON s.id = ss.student_id AND s.gender = '" . $gender . "'
        LEFT JOIN student_attendences sa 
            ON sa.student_session_id = ss.id 
            AND sa.date = " . $this->db->escape($date) . "
            $subject_filter
        LEFT JOIN attendence_type at ON at.id = sa.attendence_type_id
        WHERE ss.class_id = " . $this->db->escape($class_id) . "
            AND ss.section_id = " . $this->db->escape($section_id) . "
            $session_filter
        ORDER BY s.lastname, s.firstname";


        $query = $this->db->query($sql);
        return $query->result_array(); 
    }
	
	 
	
	public function searchAttendenceClassSectionSubjectGender($class_id, $section_id, $date, $subject, $gender='Male') {

        $sql = "SELECT 
            student_sessions.attendence_id,
            students.status,
            students.firstname,
            students.suffix,
            students.middlename,
            student_sessions.date,
            students.roll_no,
            students.lrn,
            students.admission_no,
            students.lastname,
            students.gender,
            student_sessions.attendence_type_id,
            student_sessions.id AS student_session_id,
            attendence_type.type AS `att_type`,
            attendence_type.key_value AS `key`,
            student_sessions.excuse_note,
            student_sessions.exempted_note
        FROM 
            students,
            (
                SELECT 
                    ANY_VALUE(student_session.id) AS id,
                    ANY_VALUE(student_session.student_id) AS student_id,
                    ANY_VALUE(IFNULL(student_attendences.date, 'xxx')) AS date,
                    ANY_VALUE(IFNULL(student_attendences.id, 0)) AS attendence_id,
                    ANY_VALUE(student_attendences.attendence_type_id) AS attendence_type_id,
                    ANY_VALUE(student_attendences.excuse_note) AS excuse_note,
                    ANY_VALUE(student_attendences.exempted_note) AS exempted_note
                FROM 
                    student_session
                LEFT JOIN 
                    student_attendences 
                    ON student_attendences.student_session_id = student_session.id  
                    AND student_attendences.date = " . $this->db->escape($date) . "
                    AND student_attendences.subject_id = " . $this->db->escape($subject) . "
                WHERE  
                    student_session.session_id = " . $this->db->escape($this->current_session) . " 
                    AND student_session.class_id = " . $this->db->escape($class_id) . " 
                    AND student_session.section_id = " . $this->db->escape($section_id) . " 
                GROUP BY 
                    student_session.student_id
            ) AS student_sessions
        LEFT JOIN 
            attendence_type 
            ON attendence_type.id = student_sessions.attendence_type_id
        WHERE 
            student_sessions.student_id = students.id 
            AND students.gender = '" . $gender . "'
        ORDER BY 
            students.lastname, students.firstname";


  
        $query = $this->db->query($sql);
        return $query->result_array(); 
    }
	
	 public function getbygender($gender='Male') {
        $subject = $this->input->post('subject_id');
        $this->db->select()->from('student_attendences');
        if ($subject != null) {
            $this->db->where('subject_id', $subject);
        }
        
		$this->db->where('gender', $gender);
        $this->db->order_by('lastname');
        $this->db->order_by('firstname');

        $query = $this->db->get(); 
        
		return $query->result_array(); 
    }
	
	public function get_student_attendance( $student_id, $session_id, $type_id = null  ){
		if( $type_id ){
		$query = $this->db->query('
			SELECT 
			student_attendences.student_session_id,student_attendences.id,date,subjects.name as subject_name,teachers.name, teachers.lastname 
			FROM student_attendences
			JOIN student_session ON student_attendences.student_session_id = student_session.id
			LEFT JOIN subjects ON student_attendences.subject_id = subjects.id 
			LEFT JOIN teachers ON student_attendences.teacher_id = teachers.id 
			WHERE student_id = '.$student_id.' AND student_session.session_id = '. $session_id.' AND student_attendences.attendence_type_id = '.$type_id.'
			order by date ASC; 
			'); 
		  $result = $query->result_array(); 
		} else {
			$query = $this->db->query('
			SELECT 
			student_attendences.student_session_id,student_attendences.id,date,subjects.name as subject_name,teachers.name, teachers.lastname 
			FROM student_attendences
			JOIN student_session ON student_attendences.student_session_id = student_session.id 
			LEFT JOIN subjects ON student_attendences.subject_id = subjects.id 
			LEFT JOIN teachers ON student_attendences.teacher_id = teachers.id 
			WHERE student_id = '.$student_id.' AND student_session.session_id = '. $session_id.'
			order by date ASC; 
			'); 
		  $result = $query->result_array(); 	
		}

		  return $result;
		
	}
	
	 public function add_data($data) { 

       
		date_default_timezone_set('Asia/Manila');
		$current_date = date('Y-m-d H:i:s');
		$data_time = array(
			'created_at' => $current_date 
		);

		$data_save = array_merge($data,$data_time ); 
		$this->db->insert('student_attendences', $data_save);
		return $this->db->insert_id();
    }
	
	 public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('student_attendences');
    }

    // push notif for absent greater than 10
    public function count_student_absences($session_id, $student_session_id, $subject_id){
        $this->db->select("COUNT(student_attendences.id) as counts")
                ->from("student_attendences")
                ->join("student_session", "student_session.id = student_attendences.student_session_id")
                ->where("student_attendences.student_session_id", $student_session_id)
                ->where("student_attendences.subject_id", $subject_id)
                ->where("student_session.session_id", $session_id)
                ->where("student_attendences.attendence_type_id", 4);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result(); 
        }else {
            return 0; 
        }

    }

    public function get_parents_id($student_session_id) {
        $this->db->select("users.user_id");
        $this->db->from("users");
        $this->db->where("FIND_IN_SET('$student_session_id', users.childs) >", 0);
        $this->db->order_by("users.user_id", "DESC");
        $this->db->limit(1);  // Limit to get only the first record
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();  // Use row() to get a single record
        }

        return null;
    }
}
