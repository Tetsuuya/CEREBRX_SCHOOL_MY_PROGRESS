<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Order_model extends CI_Model {

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
	
	

    /**
     * This function will delete the record based on the id
     * @param $id
     */
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
	

    /**
     * This function will take the post data passed from the controller
     * If id is present, then it will do an update
     * else an insert. One function doing both add and edit.
     * @param $data
     */
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
	
	public function get_dailyamount(){
		$dailyamount=0;
		$datedaily = date('Y-m-d');
		/* $order = $this->order_model->get();
		
		foreach ($order as $dailytime) {
			$date_purchase = $dailytime['date_purchase'];
			$datepurchase = substr($date_purchase,0,10);
			if ($datedaily == $datepurchase) {
				$dailyamount+=$dailytime['amount'];
			}
		} */
		$this->db->select("SUM(`amount`) as amount ");
		$this->db->from('orders'); 
		$this->db->where("DATE(`date_purchase`)",$datedaily);
		$this->db->where("is_deleted", 0 );//use date function
		$query = $this->db->get();
		$this->db->group_by('DATE(date_purchase), MONTH(date_purchase), YEAR(date_purchase)');
        $result = $query->row_array();
		if( $result ){
			$dailyamount = $result['amount'];
		}
		return $dailyamount;
	}
	
	public function get_monthlyamount(){
		$monthlyamount=0;
		$datemontly = date('m');
		$dateyearly = date('Y');

		$this->db->select("SUM(`amount`) as amount ");
		$this->db->from('orders'); 
		$this->db->where("MONTH(`date_purchase`)",$datemontly);//use date function
		$this->db->where("YEAR(`date_purchase`)",$dateyearly);//use date function
		$this->db->where("is_deleted", 0 );
		$this->db->group_by('MONTH(date_purchase), YEAR(date_purchase)');
		$query = $this->db->get();
        $result = $query->row_array();
		if( $result ){
			$monthlyamount = $result['amount'];
		}
		return $monthlyamount;
	}

	
	public function get_yearlyamount(){
		$yearlyamount=0;
		//$order = $this->order_model->get();
		$dateyearly = date('Y');
		/* foreach ($order as $yearlytime) {
			$date_purchase = $yearlytime['date_purchase'];
			$datepurchase = substr($date_purchase,0,4);
			if ($dateyearly == $datepurchase) {
				$yearlyamount+=$yearlytime['amount'];
			}
		}
		 */
		$this->db->select("SUM(`amount`) as amount ");
		$this->db->from('orders'); 
		$this->db->where("YEAR(`date_purchase`)",$dateyearly);//use date function
		$this->db->where("is_deleted", 0 );
		$this->db->group_by('YEAR(date_purchase)');
		$query = $this->db->get();
        $result = $query->row_array();
		if( $result ){
			$yearlyamount = $result['amount'];
		}
		return $yearlyamount;
	}
	
	public function get_range( $range_from, $range_to, $is_range ) {

		if( $is_range ){
			$query = $this->db->query('SELECT distinct student_id from orders left join order_customer on order_customer.order_id = orders.id where is_deleted="0" and (DATE(orders.date_purchase) BETWEEN "'.date('Y-m-d', strtotime($range_from)).'" and "'.date('Y-m-d', strtotime($range_to)).'" ) order by student_id asc' );
			$result = $query->result_array();
			$result_array1 = array();
			if( $result ){
				foreach( $result as $key => $value ){
					$student_id = $value['student_id'];
					$query1 = $this->db->query('SELECT ANY_VALUE(`orders`.`id`) as id,sum(orders.amount) as amount,student_id, ANY_VALUE(`orders`.`payment_method`) as payment_method, ANY_VALUE(`order_customer`.`name`) as name, ANY_VALUE(`orders`.`date_purchase`) as date_purchase from orders left join order_customer on order_customer.order_id = orders.id where is_deleted="0" and student_id="'.$student_id.'" and DATE(orders.date_purchase)  BETWEEN "'.date('Y-m-d', strtotime($range_from)).'" and  "'.date('Y-m-d', strtotime($range_to)).'" order by date_purchase asc ');
					$result_array1[] = $query1->row_array();
				}
			}
			
			return $result_array1;
		} else {
			$query = $this->db->query('SELECT distinct student_id from orders left join order_customer on order_customer.order_id = orders.id where DATE(orders.date_purchase) ="'.date('Y-m-d', strtotime($range_from)).'" and is_deleted="0" order by student_id asc');
			$result = $query->result_array();
			$result_array1 = array();
			if( $result ){
				foreach( $result as $key => $value ){
					$student_id = $value['student_id'];
					$query1 = $this->db->query('SELECT ANY_VALUE(`orders`.`id`) as id,sum(orders.amount) as amount,student_id, ANY_VALUE(`orders`.`payment_method`) as payment_method, ANY_VALUE(`order_customer`.`name`) as name, ANY_VALUE(`orders`.`date_purchase`) as date_purchase from orders left join order_customer on order_customer.order_id = orders.id where DATE(orders.date_purchase) ="'.date('Y-m-d', strtotime($range_from)).'" and is_deleted="0" and student_id="'.$student_id.'" order by date_purchase asc ');
					$result_array1[] = $query1->row_array();
				}
			}
				        
			return $result_array1;
		}
    }

    public function get_range_paginationv2( $range_from, $range_to, $session_id='', $per_page = 20 ) {
		$this->db->select('COUNT(orders.id) AS counter, SUM(orders.amount) AS total_amount');
		$this->db->from('orders'); 
		$this->db->where('DATE(`date_purchase`) BETWEEN "'. date('Y-m-d', strtotime($range_from)). '" and "'. date('Y-m-d', strtotime($range_to)).'"');
		$this->db->where("is_deleted", 0 );
		$this->db->where("session_id", $session_id ); 
        $query = $this->db->get()->row(); 
        // printx( $query );
        // $num_rows = count( $query );
        $num_rows = $query->counter;
        $total_amount = $query->total_amount;
		$total_rows = $num_rows;
		$set_per_page = 20;
		$config['base_url'] = site_url('cafeteria/orders/orders_list/?session_id='.$session_id);
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
		
		$this->db->select('order_customer.name, order_customer.student_id,  orders.session_id, orders.payment_method, sum(amount) as amount, date(date_purchase) as date_purchase')->from('orders');
		$this->db->join('order_customer', 'order_customer.order_id = orders.id', 'left'); 
		$this->db->where('DATE(`date_purchase`) BETWEEN "'. date('Y-m-d', strtotime($range_from)). '" and "'. date('Y-m-d', strtotime($range_to)).'"');
		$this->db->where("is_deleted", 0 );
		$this->db->where("session_id", $session_id );
		$this->db->order_by('order_customer.name','ASC');
		$this->db->group_by('order_customer.student_id');
		$this->db->limit($set_per_page, $per_page); 
		$result['records'] = $this->db->get()->result();
		$result['pagination'] = $this->pagination->create_links();
		$result['total_amount'] = $total_amount;
		
		return $result;

	}
	
	public function get_range_pagination( $range_from, $range_to, $session_id='', $per_page = 20 ) {
        $this->db->select('*, sum(amount) as amount, date(date_purchase) as date_purchase');
		$this->db->from('orders');
		$this->db->where('DATE(`date_purchase`) BETWEEN "'. date('Y-m-d', strtotime($range_from)). '" and "'. date('Y-m-d', strtotime($range_to)).'"');
		$this->db->where("is_deleted", 0 );
		$this->db->where("session_id", $session_id );
		$this->db->join('order_customer', 'order_customer.order_id = orders.id', 'left'); 
		$this->db->order_by('orders.id','DESC');
		$this->db->group_by('order_customer.student_id');
        $query = $this->db->get();

		$num_rows = count( $query );
		$total_rows = $num_rows;
		$set_per_page = 20;
		$config['base_url'] = site_url('cafeteria/orders/index/?session_id='.$session_id);
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
		
		$this->db->select('*, sum(amount) as amount, date(date_purchase) as date_purchase');
		$this->db->from('orders');
		$this->db->where('DATE(`date_purchase`) BETWEEN "'. date('Y-m-d', strtotime($range_from)). '" and "'. date('Y-m-d', strtotime($range_to)).'"');
		$this->db->where("is_deleted", 0 );
		$this->db->where("session_id", $session_id );
		$this->db->join('order_customer', 'order_customer.order_id = orders.id', 'left'); 
		$this->db->order_by('orders.id','DESC');
		$this->db->group_by('order_customer.student_id');
        $query = $this->db->get();
		$this->db->limit($set_per_page, $per_page);
		$result['records'] = $this->db->get()->result();
		$result['pagination'] = $this->pagination->create_links();
    }
	
	
	public function get_monthlytransaction( $datemontly, $dateyearly ){
		$monthlyamount=0;

		$this->db->select("SUM(`amount`) as amount, students.firstname, students.lastname, students.middlename, students.suffix, students.id as student_id ");
		$this->db->from('students'); 
		$this->db->join('order_customer', 'order_customer.student_id = students.id', 'left'); 
		$this->db->join('orders', 'orders.id = order_customer.order_id', 'left'); 
		$this->db->where("MONTH(`orders.date_purchase`)",$datemontly);//use date function
		$this->db->where("YEAR(`orders.date_purchase`)",$dateyearly);//use date function
		$this->db->where("is_deleted", 0 );
		$this->db->where("order_customer.student_id <>", 0 );
		$this->db->group_by('order_customer.student_id');
		$query = $this->db->get();
        $result = $query->result_array();
		
		return $result;
	}


	public function get_cafeteria_monthlytransaction( $id, $datemontly='', $dateyearly='' , $session_id = ''){
		$monthlyamount=0;

	/* 	$this->db->select(" 
		students.firstname,
		students.lastname, 
		students.middlename, 
		students.suffix, 
		students.id as student_id,
		orders.id,
		menu_item.id,
		menu_item.name as menu_name,
		order_details.quantity,
		order_details.amount,
		order_details.menu_item_id,
		order_details.order_id as orders_id,
		orders.payment_method,
		orders.date_purchase as orders_date_purchase,
		orders.amount as orders_amount,
		DAY(orders.date_purchase) AS DAY,		
		MONTH(orders.date_purchase) AS MONTH,		
		YEAR(orders.date_purchase) AS YEAR,
		orders.id AS `details_id`
		"); */
		$this->db->select(" 
			students.firstname,
			students.lastname, 
			students.middlename, 
			students.suffix, 
			students.id as student_id,
			orders.id,
			orders.date_purchase as orders_date_purchase,
			orders.amount as orders_amount,
			orders.remarks,
			DAY(orders.date_purchase) AS DAY,		
			MONTH(orders.date_purchase) AS MONTH,		
			YEAR(orders.date_purchase) AS YEAR,
			orders.id AS `details_id`
			");
		$this->db->from('orders'); 
		$this->db->join('order_customer', 'order_customer.order_id = orders.id');		
		$this->db->join('students', 'order_customer.student_id = students.id');		
		
		//$this->db->join('order_customer', 'order_customer.student_id = students.id'); 		
		//$this->db->join('order_details', 'order_details.order_id = order_customer.order_id');		
		//$this->db->join('menu_item', 'menu_item.id = order_details.menu_item_id'); 		
		if( $datemontly ){
			$this->db->where("MONTH(`date_purchase`)",$datemontly);
		}
		if( $dateyearly ){
			$this->db->where("YEAR(`date_purchase`)",$dateyearly);
		}
		if( $session_id ){
			$this->db->where("orders.session_id",$session_id);
		}
		$this->db->where("students.id", $id );
		$this->db->where("is_deleted", 0 );	

		//$this->db->group_by('order_details.order_id');		
		$this->db->order_by('orders.date_purchase','DESC');		
		$query = $this->db->get();
        $result = $query->result_array();

		return $result;
	}
	
	public function get_items($id){
		$this->db->select('*')->from('order_details');		
        //$this->db->select('*')->from('orders');
		$this->db->where('order_id', $id);
		$this->db->join('menu_item', 'order_details.menu_item_id = menu_item.id', 'left'); 
		$this->db->order_by('order_details.id');
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function deleted_order( $id, $session_id = null ){
		$this->db->select(" 
			orders.id,
			orders.date_purchase as orders_date_purchase,
			orders.deleted_at,
			orders.amount as orders_amount,
			orders.deleted_reason,
			DAY(orders.date_purchase) AS DAY,		
			MONTH(orders.date_purchase) AS MONTH,		
			YEAR(orders.date_purchase) AS YEAR,
			orders.id AS `details_id`
			");
		$this->db->from('orders'); 
		$this->db->join('order_customer', 'order_customer.order_id = orders.id');				
        //$this->db->select('*')->from('orders');
		$this->db->where('student_id', $id);
		$this->db->where('is_deleted', 1 );
		$this->db->where('session_id', $session_id );
		$this->db->order_by('orders.date_purchase','DESC');	
		$query = $this->db->get();
		
		return $query->result_array();
		
	}	
	
	public function get_order_journal( $ids ){
		$today = date('Y-m-d');
  
        $this->db->select('order_customer.student_id');  
        $this->db->select('orders.amount as type');  
        $this->db->select('orders.date_purchase as created_at');     
        $this->db->from('orders'); 
        $this->db->join('order_customer', 'orders.id = order_customer.order_id'); 
        $this->db->where_in('order_customer.student_id', $ids);
        $this->db->where('DATE(orders.date_purchase)', $today);
        $query = $this->db->get(); 
        $results =  $query->result_array(); 
        $final_value = array();

        foreach ($results as $value) {
			$created_at = $value['created_at'];
			$todays_time = date("H:i:s",strtotime($created_at));
			if ($todays_time < "12") {
				$ordered = "BREAKFAST";
			} elseif ($todays_time >= "12" && $todays_time <= "13") {
				$ordered = "LUNCH"; 
			} elseif ($todays_time > "13" && $todays_time < "17") {
				$ordered = "SNACKS";
			} elseif ($todays_time >= "17" && $todays_time < "19") {
				$ordered = "DINNER";
			} elseif ($todays_time >= "19") {
				$ordered = "DINNER";
			}  
			
			$basic_info = array(
                'classification'=>'cafeteria',
                'description'=>'Cafeteria ('.$ordered.')',

            );
            $final_value[] = array_merge($value, $basic_info); 
        }
		
        return $final_value;  
	}
	
	public function get_canteen( $id, $datemontly='', $dateyearly='' ){
		$monthlyamount=0;

	/* 	$this->db->select(" 
		students.firstname,
		students.lastname, 
		students.middlename, 
		students.suffix, 
		students.id as student_id,
		orders.id,
		menu_item.id,
		menu_item.name as menu_name,
		order_details.quantity,
		order_details.amount,
		order_details.menu_item_id,
		order_details.order_id as orders_id,
		orders.payment_method,
		orders.date_purchase as orders_date_purchase,
		orders.amount as orders_amount,
		DAY(orders.date_purchase) AS DAY,		
		MONTH(orders.date_purchase) AS MONTH,		
		YEAR(orders.date_purchase) AS YEAR,
		orders.id AS `details_id`
		"); */
		$this->db->select(" 
			students.firstname,
			students.lastname, 
			students.middlename, 
			students.suffix, 
			students.id as student_id,
			orders.id,
			orders.date_purchase as orders_date_purchase,
			orders.amount as orders_amount,
			DAY(orders.date_purchase) AS DAY,		
			MONTH(orders.date_purchase) AS MONTH,		
			YEAR(orders.date_purchase) AS YEAR,
			orders.id AS `details_id`
			");
		$this->db->from('orders'); 
		$this->db->join('order_customer', 'order_customer.order_id = orders.id');		
		$this->db->join('students', 'order_customer.student_id = students.id');		
		
		//$this->db->join('order_customer', 'order_customer.student_id = students.id'); 		
		//$this->db->join('order_details', 'order_details.order_id = order_customer.order_id');		
		//$this->db->join('menu_item', 'menu_item.id = order_details.menu_item_id'); 		
		if( $datemontly ){
			$this->db->where("MONTH(`date_purchase`)",$datemontly);
		}
		if( $dateyearly ){
			$this->db->where("YEAR(`date_purchase`)",$dateyearly);
		}
		$this->db->where_in("students.id", $id );
		$this->db->where("is_deleted", 0 );	

		//$this->db->group_by('order_details.order_id');		
		$this->db->order_by('orders.date_purchase','DESC');		
		$query = $this->db->get();
        $result = $query->result_array();

		return $result;
	}
	
	function get_order_total( $session_id = '' ){
		$dailyamount=0;
		$datedaily = date('Y-m-d');
		$datemontly = date('m');
		$dateyearly = date('Y');
		$query = $this->db->query('
				SELECT 
					SUM(CASE When DATE(`date_purchase`)="'.$datedaily.'" AND `is_deleted` = 0 AND `session_id` = "'.$session_id.'" Then amount Else 0 End ) as dailyamount,
					SUM(CASE When MONTH(`date_purchase`)="'.$datemontly.'" AND YEAR(`date_purchase`)='.$dateyearly.' 	AND `is_deleted` = 0 AND `session_id` = "'.$session_id.'" Then amount Else 0 End ) as monthlyamount,
					SUM(CASE When YEAR(`date_purchase`)="'.$dateyearly.'" AND `is_deleted` = 0 AND `session_id`= "'.$session_id.'" Then amount Else 0 End ) as yearlyamount,
					SUM(CASE When `is_deleted` = 0  AND `session_id`= "'.$session_id.'" Then amount Else 0 End ) as totalamount,
					SUM(CASE When `is_deleted` = 0  Then amount Else 0 End ) as totalall
					FROM orders
				'); 
		 $result = $query->row_array(); 	
		 
		 return $result;	 
	}
	
	public function get_orders_by_sy() {
        $this->db->select('session_id,sum(`amount`) as total_amount,session')->from('orders');
		$this->db->where('orders.is_deleted', 0);
		$this->db->join('sessions', 'orders.session_id = sessions.id');		
		$this->db->group_by('session_id');
		$this->db->order_by('session_id');
        $query = $this->db->get();
		
        return $query->result_array(); 
    }
	
	public function get_orders_monthly( $session_id ) {
        $this->db->select('session_id,sum(`amount`) as total_amount,session,MONTH(`date_purchase`) as date_purchase ')->from('orders');
		$this->db->where('orders.is_deleted', 0);
		$this->db->where('orders.session_id', $session_id );
		$this->db->join('sessions', 'orders.session_id = sessions.id');		
		$this->db->group_by('MONTH(date_purchase)');
		$this->db->order_by('date_purchase');
        $query = $this->db->get();
		
        return $query->result_array(); 
    }
	
	public function get_total_spent($student_id = null, $session_id = null ) {
        $this->db->select('sum(`amount`) as total_amount')->from('orders');
		$this->db->join('order_customer', 'order_customer.order_id = orders.id', 'left'); 
		$this->db->where('orders.is_deleted', 0);
		$this->db->where('order_customer.student_id', $student_id );
		$this->db->where('orders.session_id', $session_id );
		$this->db->group_by('student_id');
        $query = $this->db->get();
        
		return $query->row_array(); 
    }
	
	public function get_total_spent_list($student_id = null, $session_id = null ) {
        $result = $this->db->query("SELECT SUM(total_amount) AS `get_total_amount`, SUM(total_load) AS `get_total_load`
			FROM ( SELECT SUM(amount) as total_amount, null as `total_load`
			FROM `order_customer`
			JOIN `orders` ON `orders`.`id` = `order_customer`.`order_id`
			WHERE `order_customer`.`student_id` = '".$student_id."'
			AND `orders`.`session_id` = '".$session_id."'
			AND `orders`.`is_deleted` = '0'
			GROUP BY `order_customer`.`student_id` 
			UNION ALL 
			SELECT null as total_amount, SUM(amount) as `total_load`
			FROM `student_load`
			WHERE `student_load`.`student_id` = '".$student_id."'
			AND `student_load`.`session_id` = '".$session_id."'
			GROUP BY `student_load`.`student_id` ) as a");
		   
		   return $result->row_array(); 
    }
	
	
	public function getallowstudents_pagination( $session_id = '', $current_session_id, $per_page = 15, $search='' ) {
		$this->load->library('pagination');
		$get_result_id = array();
	 	if( $search ){
	 		$this->db->select('students.id as id,student_session.id as `student_session_id`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no ,allow,students.lastname,students.firstname,students.middlename');
	 		$this->db->from('students');
	 		$this->db->join('student_session', 'student_session.student_id = students.id');
	 		$this->db->join('classes', 'student_session.class_id = classes.id', 'left');
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
	 		$this->db->join('classes', 'student_session.class_id = classes.id', 'left');
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
	 		$this->db->join('classes', 'student_session.class_id = classes.id', 'left');
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
         $this->db->join('classes', 'student_session.class_id = classes.id', 'left');
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

	// public function getallowstudents_pagination($session_id = '', $current_session_id, $per_page = 15, $search = '') {
	// 	$this->load->library('pagination');
	// 	$get_result_id = array();

	// 	if ($search) {
	// 		// Initial search to get matching student IDs
	// 		$this->db->select('students.id');
	// 		$this->db->from('students');
	// 		$this->db->join('student_session', 'student_session.student_id = students.id');
	// 		$this->db->like('students.firstname', $search, 'both');
	// 		$this->db->or_like('students.lastname', $search, 'both');
	// 		$this->db->or_like('students.middlename', $search, 'both');
	// 		$search_query = $this->db->get()->result();

	// 		if ($search_query) {
	// 			foreach ($search_query as $col) {
	// 				$get_result_id[] = $col->id;
	// 			}
	// 		} else {
	// 			$get_result_id = [];
	// 		}
	// 	}

	// 	if (empty($session_id)) {
	// 		$session_id = $current_session_id;
	// 	}

	// 	// Count total rows for pagination
	// 	$this->db->select('students.id');
	// 	$this->db->from('students');
	// 	$this->db->join('student_session', 'student_session.student_id = students.id');
	// 	$this->db->where('student_session.session_id', $session_id);
	// 	$this->db->where('student_session.allow', 'yes');
	// 	if (!empty($get_result_id)) {
	// 		$this->db->where_in('students.id', $get_result_id);
	// 	}
	// 	$total_rows = $this->db->count_all_results();

	// 	// Pagination setup
	// 	$config['base_url'] = site_url('cafeteria/student/register_student/?session_id=' . $session_id);
	// 	$config['total_rows'] = $total_rows;
	// 	$config['per_page'] = $per_page;
	// 	$config['full_tag_open'] = '<ul class="pagination">';
	// 	$config['full_tag_close'] = '</ul>';
	// 	$config['first_tag_open'] = '<li>';
	// 	$config['first_tag_close'] = '</li>';
	// 	$config['last_tag_open'] = '<li>';
	// 	$config['last_tag_close'] = '</li>';
	// 	$config['next_tag_open'] = '<li>';
	// 	$config['next_tag_close'] = '</li>';
	// 	$config['prev_tag_open'] = '<li>';
	// 	$config['prev_tag_close'] = '</li>';
	// 	$config['cur_tag_open'] = '<li class="active"><a href="#">';
	// 	$config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
	// 	$config['num_tag_open'] = '<li>';
	// 	$config['num_tag_close'] = '</li>';
	// 	$config['page_query_string'] = TRUE;
	// 	$this->pagination->initialize($config);

	// 	// Final data query
	// 	$this->db->select('students.id as id, student_session.id as student_session_id, student_session.is_deactivate, classes.id as class_id, classes.class, sections.id as section_id, sections.section, students.admission_no, student_session.allow, students.lastname, students.firstname, students.middlename');
	// 	$this->db->from('students');
	// 	$this->db->join('student_session', 'student_session.student_id = students.id');
	// 	$this->db->join('classes', 'student_session.class_id = classes.id');
	// 	$this->db->join('sections', 'sections.id = student_session.section_id', 'left');
	// 	$this->db->where('student_session.session_id', $session_id);
	// 	$this->db->where('student_session.allow', 'yes');
	// 	if (!empty($get_result_id)) {
	// 		$this->db->where_in('students.id', $get_result_id);
	// 	}
	// 	$this->db->order_by('students.lastname', 'asc');
	// 	$this->db->order_by('students.firstname', 'asc');
	// 	$this->db->limit($per_page, $per_page);
		
	// 	$result['records'] = $this->db->get()->result();
	// 	$result['pagination'] = $this->pagination->create_links();

	// 	return $result;
	// }

	
	public function get_range_individual( $student_id, $range_from, $range_to, $session_id='' ) {
        $this->db->select('order_customer.name, order_customer.student_id,  orders.session_id, orders.payment_method, amount, date_purchase')->from('orders');
		$this->db->where('DATE(`date_purchase`) BETWEEN "'. date('Y-m-d', strtotime($range_from)). '" and "'. date('Y-m-d', strtotime($range_to)).'"');
		$this->db->where("is_deleted", 0 );
		$this->db->where("session_id", $session_id );
		$this->db->where("student_id", $student_id );
		$this->db->join('order_customer', 'order_customer.order_id = orders.id', 'left'); 
		$this->db->order_by('orders.id','DESC');
        $query = $this->db->get();

		return $query->result_array(); 
    }

    public function get_range_individual_with_details( $student_id, $range_from, $range_to, $session_id='' ) {
        $this->db->select('order_customer.name, order_customer.student_id,  orders.session_id, orders.payment_method, order_details.amount, date_purchase,menu_item.name as order_name, order_details.quantity')->from('orders');
		$this->db->where('DATE(`date_purchase`) BETWEEN "'. date('Y-m-d', strtotime($range_from)). '" and "'. date('Y-m-d', strtotime($range_to)).'"');
		$this->db->where("is_deleted", 0 );
		$this->db->where("session_id", $session_id );
		$this->db->where("student_id", $student_id );
		$this->db->join('order_customer', 'order_customer.order_id = orders.id', 'left'); 
		$this->db->join('order_details', 'order_details.order_id = orders.id', 'left'); 
		$this->db->join('menu_item', 'menu_item.id = order_details.menu_item_id', 'left'); 
		$this->db->order_by('orders.id','DESC');
        $query = $this->db->get();

		return $query->result_array(); 
    }

	public function get_range_individual1( $student_id, $range_from, $range_to, $session_id='' ) {
        $this->db->select('orders.id,order_customer.name, order_customer.student_id,  orders.session_id, orders.payment_method, amount, date_purchase')->from('orders');
		$this->db->where('DATE(`date_purchase`) BETWEEN "'. date('Y-m-d', strtotime($range_from)). '" and "'. date('Y-m-d', strtotime($range_to)).'"');
		$this->db->where("is_deleted", 0 );
		if( $session_id ){
			$this->db->where("session_id", $session_id );
		}
		$this->db->where("student_id", $student_id );
		$this->db->join('order_customer', 'order_customer.order_id = orders.id', 'left'); 
		$this->db->order_by('orders.id','DESC');
        $query = $this->db->get();

		return $query->result_array(); 
    }
	
	public function get_student_transactions_with_loads($student_id, $session_id, $from_date, $to_date)
	{
		$sql = "
			SELECT 
				orders.id AS reference_id,
				CASE
					WHEN HOUR(orders.date_purchase) BETWEEN 5 AND 7 THEN 'Breakfast'
					WHEN HOUR(orders.date_purchase) BETWEEN 11 AND 13 THEN 'Lunch'
					WHEN HOUR(orders.date_purchase) BETWEEN 17 AND 19 THEN 'Dinner'
					ELSE 'Snack'
				END AS name,
				order_customer.student_id,
				orders.session_id,
				orders.payment_method,
				orders.amount,
				orders.date_purchase AS date,
				'transaction' AS type,
				NULL AS remarks
			FROM orders
			LEFT JOIN order_customer ON order_customer.order_id = orders.id
			WHERE 
				DATE(orders.date_purchase) BETWEEN ? AND ?
				AND orders.is_deleted = 0
				AND order_customer.student_id = ?
				AND orders.session_id = ?

			UNION ALL

			SELECT 
				sl.id AS reference_id,
				CASE
					WHEN LOWER(sl.remarks) LIKE '%excess%' THEN CONCAT(MONTHNAME(sl.date_load), ' ', YEAR(sl.date_load), ' Excess load')
					WHEN LOWER(sl.remarks) LIKE '%load allowance%' THEN CONCAT(MONTHNAME(sl.date_load), ' ', YEAR(sl.date_load), ' load')
					ELSE CONCAT(MONTHNAME(sl.date_load), ' ', YEAR(sl.date_load), ' Load')
				END AS name,
				sl.student_id,
				sl.session_id,
				NULL AS payment_method,
				sl.amount,
				sl.date_load AS date,
				'load' AS type,
				remarks
			FROM student_load sl
			WHERE 
				sl.student_id = ?
				AND sl.is_deleted = 0
				AND sl.session_id = ?                    -- ✅ Added session_id filter
				AND DATE(sl.date_load) BETWEEN ? AND ?

			ORDER BY date DESC
		";

		$query = $this->db->query($sql, [
			$from_date, $to_date, $student_id, $session_id,  // For orders
			$student_id, $session_id, $from_date, $to_date   // For student_load (now includes session_id)
		]);

		return $query->result_array();
	}

	public function get_student_transactions_and_loads($student_id, $session_id, $from_date, $to_date)
	{
		$sql = "
			SELECT 
				sub.group_date,
				sub.name,
				sub.student_id,
				sub.session_id,
				sub.payment_method,
				SUM(sub.amount) AS amount,
				MIN(sub.date_purchase) AS date,
				'transaction' AS type,
				NULL AS remarks
			FROM (
				SELECT 
					DATE(o.date_purchase) AS group_date,
					CASE
						WHEN HOUR(o.date_purchase) BETWEEN 5 AND 7 THEN 'Breakfast'
						WHEN HOUR(o.date_purchase) BETWEEN 11 AND 13 THEN 'Lunch'
						WHEN HOUR(o.date_purchase) BETWEEN 17 AND 19 THEN 'Dinner'
						ELSE 'Snack'
					END AS name,
					oc.student_id,
					o.session_id,
					o.payment_method,
					o.amount,
					o.date_purchase
				FROM orders o
				LEFT JOIN order_customer oc ON oc.order_id = o.id
				WHERE 
					DATE(o.date_purchase) BETWEEN ? AND ?
					AND o.is_deleted = 0
					AND oc.student_id = ?
					AND o.session_id = ?
			) AS sub
			GROUP BY 
				sub.group_date,
				sub.name,
				sub.student_id,
				sub.session_id,
				sub.payment_method

			UNION ALL

			SELECT 
				DATE(sl.date_load) AS group_date,
				CASE
					WHEN LOWER(sl.remarks) LIKE '%excess%' THEN CONCAT(MONTHNAME(sl.date_load), ' ', YEAR(sl.date_load), ' Excess load')
					WHEN LOWER(sl.remarks) LIKE '%load allowance%' THEN CONCAT(MONTHNAME(sl.date_load), ' ', YEAR(sl.date_load), ' load')
					ELSE CONCAT(MONTHNAME(sl.date_load), ' ', YEAR(sl.date_load), ' Load')
				END AS name,
				sl.student_id,
				sl.session_id,
				NULL AS payment_method,
				SUM(sl.amount) AS amount,
				MIN(sl.date_load) AS date,
				'load' AS type,
				sl.remarks
			FROM student_load sl
			WHERE 
				sl.student_id = ?
				AND sl.is_deleted = 0
				AND DATE(sl.date_load) BETWEEN ? AND ?
			GROUP BY 
				group_date,
				name,
				sl.student_id,
				sl.session_id,
				sl.remarks

			ORDER BY date DESC
		";

		$query = $this->db->query($sql, [
			$from_date, $to_date, $student_id, $session_id,  // for orders subquery
			$student_id, $from_date, $to_date                // for student_load
		]);

		return $query->result_array();
	}

	public function get_last_known_balance_before($student_id, $session_id, $before_date)
	{
		$sql = "
			SELECT SUM(amount) AS total_amount FROM (
				-- Get all meals consumed before the given date
				SELECT 
					-o.amount AS amount
				FROM orders o
				LEFT JOIN order_customer oc ON oc.order_id = o.id
				WHERE 
					DATE(o.date_purchase) < ?
					AND o.is_deleted = 0
					AND oc.student_id = ?
					AND o.session_id = ?
				
				UNION ALL

				-- Get all loads before the given date
				SELECT 
					sl.amount AS amount
				FROM student_load sl
				WHERE 
					sl.student_id = ?
					AND sl.is_deleted = 0
					AND DATE(sl.date_load) < ?
					AND sl.session_id = ?
			) AS combined
		";

		$query = $this->db->query($sql, [
			$before_date, $student_id, $session_id,  // for orders
			$student_id, $before_date, $session_id   // for loads
		]);

		$result = $query->row();
		return $result ? floatval($result->total_amount) : 0.00;
	}



}
