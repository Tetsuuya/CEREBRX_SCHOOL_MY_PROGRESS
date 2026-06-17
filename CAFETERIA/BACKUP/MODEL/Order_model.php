<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * BACKUP - ORIGINAL Order_model.php (BEFORE LEFT JOIN changes)
 * 
 * This is the ORIGINAL version with INNER JOIN for classes table.
 * 
 * Changes made in LOCAL version:
 * - Line ~585: Changed classes JOIN from INNER to LEFT (search query 1st SELECT)
 * - Line ~608: Changed classes JOIN from INNER to LEFT (search query 2nd SELECT)
 * - Line ~623: Changed classes JOIN from INNER to LEFT (no-search query)
 * - Line ~655: Changed classes JOIN from INNER to LEFT (final pagination query)
 * 
 * Problem with INNER JOIN:
 * - Students with class_id=0 (like Jessie Santuyo) were filtered out
 * - INNER JOIN requires matching records in both students and classes tables
 * 
 * Fixed by changing to LEFT JOIN:
 * - LEFT JOIN includes ALL students from student_session table
 * - Returns NULL for class/section fields when no matching class_id
 * - Allows students with invalid class_id=0 to appear in the list
 */

class Order_model extends CI_Model {

    public function __construct() {
        parent::__construct();
		$this->current_session = $this->setting_model->getCurrentSession();
    }

    public function get($id = null) {
        $this->db->select('*')->from('orders');
        if ($id != null) {
            $this->db->where('orders.id', $id);
        } else {
			$this->db->where('orders.is_deleted', 0);
            $this->db->order_by('orders.id','DESC');
        }
		 $this->db->join('order_customer', 'order_customer.order_id = orders.id', 'left'); 
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array(); 
        } else {
            return $query->result_array(); 
        }
    }

    public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('orders');
    }
	 public function remove_order_details($orders_id) {		 
        $this->db->where('order_id', $orders_id); 
		$this->db->delete('order_details');
    }
	 public function remove_order_customer($orders_id) {		
    	$this->db->where('order_id', $orders_id); 
		$this->db->delete('order_customer');
    }

    public function add($data) {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('orders', $data); 
        } else {
            $this->db->insert('orders', $data); 
            return $this->db->insert_id();
        }
    }
	
	public function get_active() {
        $this->db->select()->from('orders');
		$this->db->order_by('id');
        $query = $this->db->get();
		return $query->result_array(); 
    }

	/**
	 * ORIGINAL METHOD - getallowstudents_pagination() with INNER JOIN
	 * 
	 * This method uses INNER JOIN for classes table (4 locations)
	 * Problem: Students with class_id=0 are filtered out
	 */
	public function getallowstudents_pagination( $session_id = '', $current_session_id, $per_page = 15, $search='' ) {
		$this->load->library('pagination');
		$get_result_id = array();
	 	if( $search ){
	 		$this->db->select('students.id as id,student_session.id as `student_session_id`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no ,allow,students.lastname,students.firstname,students.middlename');
	 		$this->db->from('students');
	 		$this->db->join('student_session', 'student_session.student_id = students.id');
	 		// ORIGINAL: INNER JOIN (default) - filters out students with invalid class_id
	 		$this->db->join('classes', 'student_session.class_id = classes.id');
	 		$this->db->join('sections', 'sections.id = student_session.section_id','left');
	 		$this->db->like('students.firstname', $search,'both');
	 		$this->db->or_like('students.lastname', $search,'both');
	 		$this->db->or_like('students.middlename', $search ,'both');
	 		$this->db->order_by('students.lastname', 'asc');
	 		$this->db->order_by('students.firstname', 'asc');
	 		$query = $this->db->get()->result();
	 		if( $query ){
	 			foreach( $query as $col ) {
	 				$id = $col->id;
	 				array_push( $get_result_id, $id );
	 			}
	 		} else {
	 			$get_result_id='';
	 		}
	 		$this->db->select('students.id as id,student_session.id as `student_session_id`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no ,allow,students.lastname,students.firstname,students.middlename');
	 		$this->db->from('students');
	 		 $this->db->join('student_session', 'student_session.student_id = students.id');
	 		// ORIGINAL: INNER JOIN (default) - filters out students with invalid class_id
	 		$this->db->join('classes', 'student_session.class_id = classes.id');
	 		$this->db->join('sections', 'sections.id = student_session.section_id','left');
	 		if(empty($session_id)){
	 			$session_id = $current_session_id;
	 		}
	 		$this->db->where('student_session.session_id', $session_id);
	 		$this->db->where('student_session.allow', 'yes');
	 		$this->db->where_in( 'students.id', $get_result_id );
	 		$this->db->order_by('students.lastname', 'asc');
	 		$this->db->order_by('students.firstname', 'asc');
	 		$query = $this->db->get()->result();
	 	} else {
	 		$this->db->select('students.id as id,student_session.id as `student_session_id`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no ,allow,students.lastname,students.firstname,students.middlename');
	 		$this->db->from('students');
	 		 $this->db->join('student_session', 'student_session.student_id = students.id');
	 		// ORIGINAL: INNER JOIN (default) - filters out students with invalid class_id
	 		$this->db->join('classes', 'student_session.class_id = classes.id');
	 		$this->db->join('sections', 'sections.id = student_session.section_id','left');
	 		if(empty($session_id)){
	 			$session_id = $current_session_id;
	 		}
	 		$this->db->where('student_session.session_id', $session_id);
	 		$this->db->where('student_session.allow', 'yes');
	 		$this->db->order_by('students.lastname', 'asc');
	 		$this->db->order_by('students.firstname', 'asc');
	 		$query = $this->db->get()->result();
		
	 	}

	 	$num_rows = count( $query );
	 	$total_rows = $num_rows;
	 	$set_per_page = 15;
	 	$config['base_url'] = site_url('cafeteria/student/register_student/?session_id='.$session_id);
	 	$config['total_rows'] = $total_rows;
	 	$config['per_page'] = $set_per_page;
	 	$config['full_tag_open'] = '<ul class="pagination">';
	 	$config['full_tag_close'] = '</ul>';
	 	$config['first_tag_open'] = '<li>';
	 	$config['first_tag_close'] = '</li>';
	 	$config['last_tag_open'] = '<li>';
	 	$config['last_tag_close'] = '</li>';
	 	$config['next_tag_open'] = '<li>';
	 	$config['next_tag_close'] = '</li>';
	 	$config['prev_tag_open'] = '<li>';
	 	$config['prev_tag_close'] = '</li>';
	 	$config['cur_tag_open'] = '<li class="active"><a href="#">';
	 	$config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
	 	$config['num_tag_open'] = '<li>';
	 	$config['num_tag_close'] = '</li>';
	 	$config['page_query_string'] = TRUE;
	 	$this->pagination->initialize($config);
	 	$this->db->select('students.id as id,student_session.id as `student_session_id`,student_session.is_deactivate,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no ,allow,students.lastname,students.firstname,students.middlename');
	 	$this->db->from('students');
	 	$this->db->join('student_session', 'student_session.student_id = students.id');
		// ORIGINAL: INNER JOIN (default) - filters out students with invalid class_id
         $this->db->join('classes', 'student_session.class_id = classes.id');
         $this->db->join('sections', 'sections.id = student_session.section_id','left');
	 	if(empty($session_id)){
	 		$session_id = $current_session_id;
	 	}
         $this->db->where('student_session.session_id', $session_id);
         $this->db->where('student_session.allow', 'yes');
	 	if( $search ){
	 		$this->db->where_in( 'students.id', $get_result_id );
	 	}
	 	$this->db->order_by('students.lastname', 'asc');
	 	$this->db->order_by('students.firstname', 'asc');
	 	$this->db->limit($set_per_page, $per_page);
	 	$result['records'] = $this->db->get()->result();
	 	$result['pagination'] = $this->pagination->create_links();
		
	 	return $result;
    }
}
