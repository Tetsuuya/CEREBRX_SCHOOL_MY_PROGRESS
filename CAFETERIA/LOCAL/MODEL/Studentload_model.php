<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Studentload_model extends CI_Model {

    public function __construct() {
        parent::__construct();
		$this->current_session = $this->setting_model->getCurrentSession();
    }

    

    /**
     * This function will delete the record based on the id
     * @param $id
     */
	 public function get($id = null) {
        $this->db->select()->from('student_load');
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
	
    public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('student_load');
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
            $this->db->update('student_load', $data); 
        } else {
            $this->db->insert('student_load', $data); 
            return $this->db->insert_id();
        }
	}
	
	public function get_dailyamount(){
		$dailyamount=0;
		$datedaily = date('Y-m-d');

		$this->db->select("SUM(`amount`) as amount ");
		$this->db->from('student_load'); 
		$this->db->where("DATE(`date_load`)",$datedaily);
		$this->db->where("is_deleted", 0 );//use date function
		$query = $this->db->get();
		$this->db->group_by('DATE(date_load), MONTH(date_load), YEAR(date_load)');
        $result = $query->row_array();
		if( $result ){
			$dailyamount = $result['amount'];
		}
		return $dailyamount;
	}
	
	
	
	function get_load_total( $session_id ){
		
		$dailyamount=0;
		$datedaily = date('Y-m-d');
		$datemontly = date('m');
		$dateyearly = date('Y');
		$query = $this->db->query('
				SELECT 
					SUM(CASE When DATE(`date_load`)="'.$datedaily.'" AND `session_id` = "'.$session_id.'"  Then amount Else 0 End ) as dailyamount,
					SUM(CASE When MONTH(`date_load`)="'.$datemontly.'" AND YEAR(`date_load`)='.$dateyearly.' AND `session_id` = "'.$session_id.'" 	 Then amount Else 0 End ) as monthlyamount,
					SUM(CASE When YEAR(`date_load`)="'.$dateyearly.'" AND `session_id` = "'.$session_id.'"  Then amount Else 0 End ) as yearlyamount,
					SUM(CASE When  `session_id` = "'.$session_id.'"  Then amount Else 0 End) as totalamount,
					SUM(amount) as totalall
					FROM student_load
				'); 
		 $result = $query->row_array(); 	
	
		 
		return $result;	 
	}
	
	public function get_range( $range_from, $range_to, $session_id ) {
        $this->db->select('*')->from('student_load');
		$this->db->where('DATE(`date_load`) BETWEEN "'. date('Y-m-d', strtotime($range_from)). '" and "'. date('Y-m-d', strtotime($range_to)).'"');
		$this->db->where("session_id", $session_id );
		$this->db->join('students', 'student_load.student_id = students.id'); 
		$this->db->order_by('student_load.date_load','DESC');
        $query = $this->db->get();

		return $query->result_array(); 
    }
	
	public function get_student_load_amount( $id ){
		$this->db->select("SUM(`amount`) as amount ");
		$this->db->from('student_load'); 
		$this->db->where("student_id", $id );//use date function
		$query = $this->db->get();
        $result = $query->row_array();
		if( $result ){
			$dailyamount = $result['amount'];
		}
		return $dailyamount;
	}
	
	public function get_student_load( $id, $session_id = '' ){
		$this->db->select("*");
		$this->db->from('student_load'); 
		$this->db->where("student_id", $id );//use date function
		$this->db->where("session_id", $session_id );//use date function
		$this->db->where("is_deleted", 0); 
		$query = $this->db->get();
        $result = $query->result_array();

		return $result;
	}
	
	public function get_load_by_sy() {
        $this->db->select('session_id,sum(`amount`) as total_amount,session')->from('student_load');
		$this->db->join('sessions', 'student_load.session_id = sessions.id');		
		$this->db->group_by('session_id');
		$this->db->order_by('session_id');
        $query = $this->db->get();
		
        return $query->result_array(); 
	}
	
	public function get_load_monthly( $session_id ) {
        $this->db->select('
        	ANY_VALUE(student_load.session_id) as session_id,
        	ANY_VALUE(sum(student_load.amount)) as total_amount,
        	ANY_VALUE(sessions.session) as session,
        	ANY_VALUE(MONTH(student_load.date_load)) as date_purchase ')->from('student_load');
        //$this->db->select('session_id,sum(`amount`) as total_amount,session,MONTH(`date_load`) as date_purchase ')->from('student_load');
		$this->db->where('student_load.session_id', $session_id );
		$this->db->join('sessions', 'student_load.session_id = sessions.id');		
		$this->db->group_by('MONTH(date_purchase)');
		$this->db->order_by('date_purchase');
        $query = $this->db->get();
		
        return $query->result_array(); 
    }
	
	public function get_total_load($student_id = null, $session_id = null ) {
        $this->db->select('sum(`amount`) as total_amount')->from('student_load');
		$this->db->where('student_load.student_id', $student_id );
		$this->db->where('student_load.session_id', $session_id );
		$this->db->where('student_load.is_deleted', 0); // Only active loads
		$this->db->group_by('student_id');
        $query = $this->db->get();
        
		return $query->row_array(); 
    }	

    public function get_load_byfees( $student_id = null, $payment_id = null ) {

        $this->db->select('id,amount,payment_id')->from('student_load');
		$this->db->where('student_load.student_id', $student_id );
		$this->db->where('student_load.payment_id', $payment_id );
        $query = $this->db->get();
		return $query->row_array(); 
    }
	//load deleted
	public function get_deleted_loads($student_id, $session_id) {
		$this->db->where('student_id', $student_id);
		$this->db->where('session_id', $session_id);
		$this->db->where('is_deleted', 1);
		$query = $this->db->get('student_load');
		return $query->result_array();
	}

	public function get_load_history($student_id = null, $session_id = null ) {
        $this->db->select('*')->from('student_load');
		$this->db->where('student_load.student_id', $student_id );
		$this->db->where('student_load.session_id', $session_id );
		$this->db->order_by('date_load', "DESC");
        $query = $this->db->get();
        
		return $query->result_array(); 
    }
}
