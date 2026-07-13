<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Stuattendencecustom_model extends CI_Model {

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
        $this->db->select()->from('student_attendance_custom');  
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

        
        if (isset($data['id']) ) {
            $this->db->where('id', $data['id']);
            $this->db->update('student_attendance_custom', $data);  
            return $data['id'];
        } else {

            date_default_timezone_set('Asia/Manila');
            $current_date = date('Y-m-d H:i:s');
            $data_time = array(
                'created_at' => $current_date 
            );

            $data_save = array_merge($data,$data_time ); 
            $this->db->insert('student_attendance_custom', $data_save);
            return $this->db->insert_id(); 
        } 
    } 

    public function get_attendance($student_id, $attendance_type , $session=null  ) {
        if( $session == null ){
            $session = $this->current_session;
        } 
        $this->db->select()->from('student_attendance_custom');  
      
        $this->db->where('student_id', $student_id);
        $this->db->where('attendance_type', $attendance_type);
        $this->db->where('session_id', $session);
    
        $query = $this->db->get(); 
         
         return $query->row_array(); 
         
    }



    public function get_hern_attendance($student_id, $session=null  ) {
        if($session == null) {
            $session = $this->current_session;
        }
    
        // Use join to get the related records
        $this->db->select('MONTH(date) as month, attendence_type.type, COUNT(*) as count')
            ->from('student_attendences')
            ->join('student_session', 'student_session.id = student_attendences.student_session_id')
            ->join('attendence_type', 'attendence_type.id = student_attendences.attendence_type_id')
            ->where('student_session.student_id', $student_id)
            ->where('student_session.session_id', $session)
            ->group_by('MONTH(date), attendence_type.type')
            ->order_by('MONTH(date) ASC');
    
        $query = $this->db->get();
        $results = $query->result_array();
        
        $months = [
            1 => 'january',
            2 => 'february',
            3 => 'march',
            4 => 'april',
            5 => 'may',
            6 => 'june',
            7 => 'july',
            8 => 'august',
            9 => 'september',
            10 => 'october',
            11 => 'november',
            12 => 'december'
        ];
    
        $presents = array_fill_keys($months, 0);
        $absents = array_fill_keys($months, 0);
        $lates = array_fill_keys($months, 0);
    
        foreach($results as $row) {
            $monthName = $months[$row['month']];
    
            switch ($row['type']) {
                case 'Present':
                    $presents[$monthName] = (int)$row['count'];
                    break;
                case 'Absent':
                    $absents[$monthName] = (int)$row['count'];
                    break;
                case 'Late':
                case 'Late with excuse':
                    $lates[$monthName] += (int)$row['count'];  // adding because we're treating 'Late with excuse' as 'Late'
                    break;
            }
        }
    
        return [
            'presents' => $presents,
            'absents' => $absents,
            'lates' => $lates
        ];
         
    }

}
