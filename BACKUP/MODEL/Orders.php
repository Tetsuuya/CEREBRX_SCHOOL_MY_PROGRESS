<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class orders extends CI_Controller {
git 
    function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->load->helper('menu_helper');
        $this->lang->load('message', 'english');
        $this->load->library('customlib');
		$this->load->library('cart');
        $this->load->model('menuitemtype_model');
        $this->load->model('menuitems_model');
        $this->load->model('menuclass_model');
        $this->load->model('order_model');
        $this->load->model('ordercustomer_model');
        $this->load->model('orderdetails_model');
		$this->load->model('menusettings_model');
		$this->load->model('strand_model');
		$this->load->model('setting_model');
		$this->role;
        $this->load->library('auth');
        $this->auth->is_logged_in_cafeteria_staff();
        $this->load->model('Notification_API_model','notification_api_model');
        $this->load->model('Sms_model','SMSM'); 
		
        $this->load->model('Events_model','events_model'); 
        $this->load->model('Student_model'); 
		 $this->load->model('studentload_model'); 
    } 

    //index function
	function index(){
		$this->session->set_userdata('top_menu', 'Orders');
        $this->session->set_userdata('sub_menu', 'orders/index');
		$daterange="";
		$data['title'] = 'Current School Year Order Transaction';
		$per_page = $this->input->get('per_page');					
		$session_details = $this->menusettings_model->get();
		$session_id = $session_details[0]['school_year'];
		$get_order_total = $this->order_model->get_order_total( $session_id );
		$data['dailyamount'] = $get_order_total['dailyamount'];
		$data['monthlyamount'] = $get_order_total['monthlyamount'];
		$data['yearlyamount'] = $get_order_total['yearlyamount'];			
		$data['totalamount'] = $get_order_total['totalamount'];			
		$data['per_page'] = $per_page;		
		if( empty( $session_id )){
			$session_id = $this->input->get('session_id');
		}
		$session = $this->session_model->getAllSession();
		$session_details = $this->menusettings_model->get();
		$current_session_id = $session_details[0]['school_year'];
		
		if( empty( $session_id )){
			$session_id = $current_session_id;
		}

			
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			 $daterangepre = $this->input->post('date_from');
			 $daterangepos = $this->input->post('date_to');
			/* $daterangepre = substr($daterange,0,10);
			$daterangepos = substr($daterange,14,24); 
			$rangepre = str_replace('/', '-', $daterangepre);
			$rangepos = str_replace('/', '-', $daterangepos);*/
			$data['rangepre']=$daterangepre;
			$data['rangepos']=$daterangepos; 
			$data['is_range']=true;
			/*$orderlist = $this->order_model->get_range( $daterangepre, $daterangepos, $session_id); 
			$data['orderlist'] = $orderlist;*/
			/* $data['orderlist'] = $orderlist['records'];
			$data['order_pagination'] = $orderlist['pagination']; */
			$data['is_range']=true;
			$data['session_id']= $session_id;
			$this->load->view('layout/cafeteria/header', $data);
			$this->load->view('cafeteria/orders/orderList', $data);
			$this->load->view('layout/cafeteria/footer', $data);
		}
		else {	
			$daterangepre = date('m/d/Y');
			$daterangepos = date('m/d/Y');
			/* $daterangepre = substr($daterange,0,10);
			$daterangepos = substr($daterange,0,10); */
			/* $rangepre = str_replace('/', '-', $daterangepre);
			$rangepos = str_replace('/', '-', $daterangepos); */
			$data['rangepre']= $daterangepre;
			$data['rangepos']= $daterangepos;
			
			/* $daterangepre = date('m/d/Y');
			$daterangepos = date('m/d/Y'); */
			/*$orderlist = $this->order_model->get_range( $daterangepre, $daterangepos, $session_id );
			$data['orderlist'] = $orderlist;*/
			$data['is_range']= false;
			$data['session_id']= $session_id;
			$this->load->view('layout/cafeteria/header', $data);
			$this->load->view('cafeteria/orders/orderList', $data);
			$this->load->view('layout/cafeteria/footer', $data);
		}
	}

	function orders_list(){
		$this->session->set_userdata('top_menu', 'Orders');
        $this->session->set_userdata('sub_menu', 'orders/index');
		$daterange="";
		$data['title'] = 'Current School Year Order Transaction';
 
		$per_page = $this->input->get('per_page');
		$session_id = $this->input->post('session_id');
		$search = $this->input->post('search');
		$date_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');
		if( empty( $session_id )){
			$session_id = $this->input->get('session_id');
		}
		$session = $this->session_model->getAllSession();
		$session_details = $this->menusettings_model->get();
		$current_session_id = $session_details[0]['school_year'];
		
		if( empty( $session_id )){
			$session_id = $current_session_id;
		}

		if( empty( $date_from )){
			$date_from = date("Y-m-d");
			// $date_from = "2019-10-16";
		}

		if( empty( $date_to )){
			$date_to = date("Y-m-d");
		}
		$data['search'] = $search;
		$data['per_page'] = $per_page;
		$data['session_id'] = $session_id;
		$data['sessionlist'] = $session;
		$data['date_from'] = $date_from;
		$data['date_to'] = $date_to;
		$data['date_to'] = $date_to;
        $orderlist = $this->order_model->get_range_paginationv2( $date_from, $date_to, $session_id, 10 ); 
        // printx( $this->db->last_query());
        // printx( $orderlist );
	    $data['orderlist'] = $orderlist['records'];
	    $data['orderlist_pagination'] = $orderlist['pagination'];
	    $data['total_amount'] = $orderlist['total_amount'];

		$get_order_total = $this->order_model->get_order_total( $session_id );
		$data['dailyamount'] = $get_order_total['dailyamount'];
		$data['monthlyamount'] = $get_order_total['monthlyamount'];
		$data['yearlyamount'] = $get_order_total['yearlyamount'];			
		$data['totalamount'] = $get_order_total['totalamount'];
		 
		 
		$this->load->view('layout/cafeteria/header', $data);
		$this->load->view('cafeteria/orders/orderListPagination', $data);
		$this->load->view('layout/cafeteria/footer', $data); 
	}

	public function getordersjson(){
		$daterangepre = $this->input->post('date_from');
		$daterangepos = $this->input->post('date_to');
		$session_id = $this->input->post('session_id');
		$daterangepre = date('m/d/Y', strtotime($daterangepre )); 
		$daterangepos = date('m/d/Y', strtotime($daterangepos )); 
		$orderlist = $this->order_model->get_range( $daterangepre, $daterangepos, $session_id );
		
        echo json_encode($orderlist);
	}


	function monthly(){
		$data['title'] = 'Orders';
		$data['orderlist'] = $this->order_model->get();
		$this->load->view('layout/cafeteria/header', $data);
		$this->load->view('cafeteria/orders/ordermonthlyList', $data);
		$this->load->view('layout/cafeteria/footer', $data);
	}	

	function daily(){
		$data['title'] = 'Orders';
		$data['orderlist'] = $this->order_model->get();
		$this->load->view('layout/cafeteria/header', $data);
		$this->load->view('cafeteria/orders/orderdailyList', $data);
		$this->load->view('layout/cafeteria/footer', $data);
	}
    function add() {
		$total = $this->cart->total();
		//$session_id = $this->setting_model->getCurrentSession();
		$session_details = $this->menusettings_model->get();
		$session_id =  $session_details[0]['school_year'];
		$menu_item_order_details= '';
		$counter = 1;
		$check_order = TRUE;
		$student_id = $this->input->post('student_id');
		$name = $this->input->post('customer_name');
		$cash_amount = $this->input->post('cash_amount');
		$payment_method = $this->input->post('payment_method');
		$set_date = $this->input->post('set_date');
		$load_balance = 0;
		if( $total ){
			if( $payment_method == "normal" && $cash_amount ){
				if( $cash_amount <= 0  ){
					$this->session->set_flashdata('msg', '<div class="alert alert-warning text-left">Please enter amount not equal to zero or negative.</div>');
					redirect('cafeteria/cafeteria/dashboard');
				}
			}
			if( $payment_method == "normal" && $total ){
				if( $total > $cash_amount ){
					$this->session->set_flashdata('msg', '<div class="alert alert-warning text-left">Not enough cash.</div>');
					redirect('cafeteria/cafeteria/dashboard');
				}
			}
		
			if( $payment_method  == 'student_load'){
				if( empty( $student_id ) ){
					$this->session->set_flashdata('msg', '<div class="alert alert-warning text-left">Please select existing student.</div>');
					redirect('cafeteria/cafeteria/dashboard');
				} 
			}
			
			$created_at  = date('Y-m-d H:i:s');
			$date_purchase = date('Y-m-d H:i:s', strtotime($set_date));
			$order_array = array(
				'date_purchase' => $date_purchase,
				'created_at' => $created_at,
				'amount' => $total,
				'payment_method' => $payment_method,
				'is_active' => 1, 
				'session_id' => $session_id
			);
			
			if( $payment_method  == "normal" ){
				$order_array = array_merge( $order_array, array('order_given_amount' => $cash_amount ));
			} else {
				$load_balance = $this->student_model->getstudentload( $student_id );
				if( $load_balance ){
					$order_array = array_merge( $order_array, array('order_given_amount' => $load_balance ));	
				}
				
			}
			
			$order_id = $this->order_model->add( $order_array );
			if( $order_id ){
				$order_details_array = array();
				$contents = $this->cart->contents();
				if( $contents ){
					foreach( $contents as $value => $key ){
						$order_details[] = array(
							'order_id' => $order_id,
							'menu_item_id' => $key['id'],
							'quantity' => $key['qty'],
							'amount' => $key['price'],
							'is_active' => 1,
							'created_at' => date('Y-m-d H:i:s')
						);
						
						$menu_item_array = $this->menuitems_model->get( $key['id'] );
						if( $counter == 1){
							$menu_item_order_details .= $menu_item_array['name'];
							$counter++;
						} else {
							$menu_item_order_details .= ', '. $menu_item_array['name'];
						}
					} 
					$this->db->insert_batch('order_details', $order_details );
				}
				$customer_array = array(
					'order_id' => $order_id,
					'student_id' => $student_id,
					'name' => $name,
					'is_active' => 1,
					'created_at' => date('Y-m-d H:i:s')
				);
				
				$customer = $this->ordercustomer_model->add( $customer_array );

				if( $student_id  ){  
					$section_numbers = $this->student_model->get($student_id );


					date_default_timezone_set('Asia/Manila');
					$todays_date = date('Y-m-d H:i:s', strtotime($set_date));
					$todays_time = date('H:i:s',strtotime($set_date));
					$current_date = date('m/d/Y');
					$get_date_only = date('m/d/Y', strtotime($set_date));
					/*$todays_date = date('Y-m-d H:i:s');
					$todays_time = date('H:i:s');
					$current_date = date('m/d/Y');
					$get_date_only = date('m/d/Y', strtotime($set_date));*/
					//if( $get_date_only == $current_date ){
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

						$case_date_display =  date('M d, Y', strtotime($set_date)); 
						//$case_date_display =  date('M d, Y', strtotime($todays_date)); 
						$guardian_phone = $section_numbers['guardian_phone'];
						$student_firstname = $section_numbers['firstname'];
						$guardian_phone = str_replace(' ', '', $guardian_phone);
						$guardian_phone = str_replace('-', '', $guardian_phone); 
						$sms_message =  $student_firstname.' ordered '.$ordered.': '.$menu_item_order_details.' total '.number_format($total,2).' on '.$case_date_display.'. SMS Autogenerated by Bridgette.';

						if (strlen($guardian_phone)>10) {
							$data_sms_panrent = array(
								'notification_id' => $order_id,
								'sms_msg' => $sms_message,
								'sms_number' => $guardian_phone, 
								'sms_status' => 'idle',
								'sms_teacher' => 'yes',
								'sms_parent' => 'no' 
							);  
							if( isset($data_sms_panrent) ){ 
								 $this->SMSM->add($data_sms_panrent);
							}  
						}
					//}
					
					
					if( $load_balance ){
						$current_balance = $load_balance - $total;
						$this->student_model->add( array('id' => $student_id, 'load_balance' => $current_balance ));
					}
				}
			 
				$this->cart->destroy();
				$notification_array = array();
				$notification_array[] =  '<div class="alert alert-success text-left">Order successfully paid.</div>';
				if( $payment_method == "normal"){
					$change = $cash_amount - $total;
					if( $change >= 0 ){
						$notification_array[] = '<div class="alert alert-success text-left">Change: <b>Php '.number_format($change,'2','.',',').'</b></div>';
					}
				}
				$this->session->set_flashdata('msg', $notification_array );
			}
		} else {
			$this->session->set_flashdata('msg', '<div class="alert alert-warning text-left">Please select items.</div>');
		}
				
		redirect('cafeteria/cafeteria/dashboard');
    }
	
	public function details( $id ){
		$data['title'] = 'Order Details';
		$data['orderdetails'] = $this->order_model->get($id);
		$data['itemdetails'] = $this->orderdetails_model->get_orders( $id );
		$this->load->view('layout/cafeteria/header', $data);
		$this->load->view('cafeteria/orders/orderdetails', $data);
		$this->load->view('layout/cafeteria/footer', $data);
	}

// added func	
	public function studentbalance(){
		
       /*  $data['month_selected'] = "";		
        $this->form_validation->set_rules('month', 'Month', 'trim|required|xss_clean');
		
		$post_month = $this->input->post('month');  
        $post_year = $this->input->post('year');  
        $month = $post_month?$post_month:date('m');
        $year = $post_year?$post_year:date('Y'); 
        $data['selected_year'] = $year;
        $data['current_year'] = date('Y');
        $data['month_selected'] = $month;
		 
        $data['monthlist'] = $this->customlib->getMonthDropdown(); */
		$studentlist = $this->student_model->getallstudenthavebalance();	
		$data['studentlist'] = $studentlist;

		$this->load->view('layout/cafeteria/header', $data);
		$this->load->view('cafeteria/reports/studentbalance', $data);
		$this->load->view('layout/cafeteria/footer', $data);
	}
	
	  public function view($id) {
        $data['title'] = 'Student Details';
		$session_id = $this->input->get('session_id');  
		$post_month = $this->input->post('month');  
        $post_year = $this->input->post('year');  
        $month = $post_month;
        $year = $post_year; 
		if( $session_id == '' ){
			$session_id = $this->input->post('session_id');
		}
		$session_details = $this->menusettings_model->get();
		$current_session_id =  $session_details[0]['school_year'];
		$session_id = empty($session_id)?$current_session_id:$session_id;
		$data['session_id'] = $session_id;
		$session = $this->studentsession_model->get_student_allow_by_sy( $id );
		$data['sessionlist'] = $session;
        $student = $this->student_model->getBySession($id, $session_id );	
		$student_id = $id;
        $data['student'] = $student;
		$data['student_id'] = $student_id;
		$data['section_id'] = $student['section_id'];
		$data['class_id'] = $student['class_id'];
		$data['session_id'] = $session_id;
		$data['set_month'] = $month;
		$data['set_year'] = isset($year)?$year:date('Y');
		$data['transaction_list'] = $this->order_model->get_cafeteria_monthlytransaction($student_id, $month, $year , $session_id );
		$details_id = $this->input->post('order_id');
		if( $details_id ){
			$data['item_list'] = $this->orderdetails_model->get_orders($details_id);
			
		} else {
			$data['item_list']='';
		}

	    $this->load->view('layout/cafeteria/header', $data);
        $this->load->view('cafeteria/reports/studentShow', $data);
        $this->load->view('layout/cafeteria/footer', $data);
    }
	
		public function monthly_transaction($id) {
		$data['title'] = 'Student Details';
		$post_month = $this->input->post('month');  
		$post_year = $this->input->post('year');  
		$month = $post_month?$post_month:date('m');
		$year = $post_year?$post_year:date('Y'); 		
		$student = $this->studentsession_model->lastschoolyearattended($id);	
		$student_id = $student['student_id'];
		$data['set_month'] = $month;
		$data['set_year'] = $year;
		$data['student'] = $student;
		$data['student_id'] = $student_id;
		$data['enableStrand'] = $this->strand_model->checkEnable( $student['class_id'] );
		$data['getStrand'] = $this->strand_model->get( $student['strand_id'] );
		$data['strand_id'] = $student['strand_id'];
		$data['section_id'] = $student['section_id'];
		$data['class_id'] = $student['class_id'];
		
		$data['transaction_list'] = $this->order_model->get_cafeteria_monthlytransaction($student_id);
		$details_id = $this->input->post('order_id');
		if( $details_id ){
			$data['item_list'] = $this->orderdetails_model->get_orders($details_id);
		} else {
			$data['item_list']='';
		}
		$data['student_load_total'] = $this->studentload_model->get_student_load_amount( $id );
		$this->load->view('layout/cafeteria/header', $data);
		$this->load->view('cafeteria/reports/studentmonthly', $data);
		$this->load->view('layout/cafeteria/footer', $data);
	}
	
	public function deleted_order($id) {
		$data['title'] = 'Student Details';	
		$session_id = $this->input->get('session_id');  
		if( $session_id == '' ){
			$session_id = $this->input->post('session_id');
		}
		$session_details = $this->menusettings_model->get();
		$current_session_id =  $session_details[0]['school_year'];
		$session_id = empty($session_id)?$current_session_id:$session_id;
		$data['session_id'] = $session_id;
		$session = $this->studentsession_model->get_student_allow_by_sy( $id );
		$data['sessionlist'] = $session;		
		$student = $this->student_model->getBySession($id, $session_id );
		$student_id = $student['id'];
		$data['student'] = $student;
		$data['student_id'] = $student_id;
		$data['section_id'] = $student['section_id'];
		$data['class_id'] = $student['class_id'];
		$data['transaction_list'] = $this->order_model->deleted_order($student_id, $session_id );
		$details_id = $this->input->post('order_id');
		if( $details_id ){
			$data['item_list'] = $this->orderdetails_model->get_orders($details_id);
		} else {
			$data['item_list']='';
		}
		$this->load->view('layout/cafeteria/header', $data);
		$this->load->view('cafeteria/reports/orderdeleted', $data);
		$this->load->view('layout/cafeteria/footer', $data);
	}
	
	public function load_history($id) {
		$data['title'] = 'Student Details';		
		$session_id = $this->input->get('session_id');  
		if( $session_id == '' ){
			$session_id = $this->input->post('session_id');
		}
		$session_details = $this->menusettings_model->get();
		$current_session_id =  $session_details[0]['school_year'];
		$session_id = empty($session_id)?$current_session_id:$session_id;
		$data['session_id'] = $session_id;
		$session = $this->studentsession_model->get_student_allow_by_sy( $id );
		$data['sessionlist'] = $session;		
		$student = $this->student_model->getBySession($id, $session_id );
		$student_id = $student['id'];
		$data['student'] = $student;
		$data['student_id'] = $student_id;
		$data['section_id'] = $student['section_id'];
		$data['class_id'] = $student['class_id'];
		
		$data['get_student_load'] = $this->studentload_model->get_student_load($id, $session_id );
		$this->load->view('layout/cafeteria/header', $data);
		$this->load->view('cafeteria/student/student_load', $data);
		$this->load->view('layout/cafeteria/footer', $data);
	} 
	
	
	public function items(){
		$id = $this->input->post('id');		 
		$orders_id = $this->input->post('order_id');		
		$post_month = $this->input->post('month'); 
		
        $post_year = $this->input->post('year');  
        $month = $post_month?$post_month:date('m');
        $year = $post_year?$post_year:date('Y'); 
		
		
		$data['title'] = 'Student Details';
        $student = $this->student_model->get($id);	
        $data['student'] = $student;
		$data['student_id'] = $student['id'];
		$data['enableStrand'] = $this->strand_model->checkEnable( $student['class_id'] );
		$data['getStrand'] = $this->strand_model->get( $student['strand_id'] );
		$data['strand_id'] = $student['strand_id'];
		$data['section_id'] = $student['section_id'];
		$data['class_id'] = $student['class_id'];
		$data['transaction_list'] =$this-> order_model->get_cafeteria_monthlytransaction($id, $month, $year);		

		$data['item_list'] = $this->orderdetails_model->get_orders($orders_id);
		
		$this->load->view('layout/cafeteria/header', $data);
        $this->load->view('cafeteria/reports/studentShow', $data);
        $this->load->view('layout/cafeteria/footer', $data);
	}
	
	/**
     * This function will delete the record based on the order_id
     * @param $id
     */
	public function remove( $id ) {		 
		$orders_id = $this->input->post('order_id');
		if( $orders_id ){
			$order_details = $this->order_model->get( $orders_id );
			$get_amount = $order_details['amount'];
			if( $get_amount ){
				$load_balance = $this->student_model->getstudentload( $id );
				$total_balance = $load_balance + $get_amount;
				if( $id ){
					$this->student_model->add(array('id'=>$id, 'load_balance' => $total_balance ));
				}
			}
			date_default_timezone_set('Asia/Manila');
			$created_at  = date('Y-m-d h:i:s');
			$this->order_model->add(array('id'=>$orders_id, 'is_deleted' => 1,'deleted_at' => $created_at  ));
		}
       /*$delete_orders = $this->order_model->remove($orders_id);		
        $delete_orders = $this->order_model->remove_order_details($orders_id);		
        $delete_orders = $this->order_model->remove_order_customer($orders_id); */
		
		$this->session->set_flashdata('msg', '<div class="alert alert-success">Deleted order successfully.</div>');
		
		redirect(base_url().'cafeteria/orders/view/'.$id);
    }
	
	public function restore( $id ) {		 
		$orders_id = $this->input->get('order_id');
		if( $orders_id ){
			$order_details = $this->order_model->get( $orders_id );
			$get_amount = $order_details['amount'];
			if( $get_amount ){
				$load_balance = $this->student_model->getstudentload( $id );
				$total_balance = $load_balance - $get_amount;
				if( $id ){
					$this->student_model->add(array('id'=>$id, 'load_balance' => $total_balance ));
				}
			}
			date_default_timezone_set('Asia/Manila');
			$created_at  = date('Y-m-d h:i:s');
			$this->order_model->add(array('id'=>$orders_id, 'is_deleted' => 0. ,'restored_at' => $created_at ));
		}
       /*$delete_orders = $this->order_model->remove($orders_id);		
        $delete_orders = $this->order_model->remove_order_details($orders_id);		
        $delete_orders = $this->order_model->remove_order_customer($orders_id); */
			
		redirect(base_url().'cafeteria/orders/view/'.$id);
    }
	
	function total_orders(){
		$this->session->set_userdata('top_menu', 'Orders');
        $this->session->set_userdata('sub_menu', 'orders/index');
		$data['title'] = 'View All Order Transaction Per School Year';
		$session_id = $this->input->get('session_id');
		if(empty( $session_id )){
			$session_details = $this->menusettings_model->get();
			$session_id = $session_details[0]['school_year'];
		}
		$get_order_total = $this->order_model->get_order_total( $session_id );
		$data['totalall'] = $get_order_total['totalall'];			
		$data['totalamount'] = $get_order_total['totalamount'];		
		$data['orderlist'] = $this->order_model->get_orders_by_sy(); 		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->load->view('layout/cafeteria/header', $data);
			$this->load->view('cafeteria/orders/orderTotal', $data);
			$this->load->view('layout/cafeteria/footer', $data);
		} else {	
			$data['orderlistmonthly'] = $this->order_model->get_orders_monthly( $session_id );
			$session_details = $this->session_model->get( $session_id );
			$get_session_name = $session_details['session'];
			$data['session_selected'] = $this->order_model->get_orders_monthly( $session_id ); 	
			$data['get_session_name'] = $get_session_name; 	
			$data['getMonthList'] = $this->customlib->getMonthList(); 	
			$this->load->view('layout/cafeteria/header', $data);
			$this->load->view('cafeteria/orders/orderTotal', $data);
			$this->load->view('layout/cafeteria/footer', $data);
		}
	}
	
	public function sy_transaction(){
		$this->session->set_userdata('top_menu', 'Orders');
        $this->session->set_userdata('sub_menu', 'orders/index');
		$data['title'] = 'View All Order Transaction Per School Year';
		$session_id = $this->input->get('session_id');
		if(empty( $session_id )){
			$session_details = $this->menusettings_model->get();
			$session_id = $session_details[0]['school_year'];
		}
		$get_order_total = $this->order_model->get_order_total( $session_id );
		$data['totalall'] = $get_order_total['totalall'];			
		$data['totalamount'] = $get_order_total['totalamount'];		
		$data['orderlist'] = $this->order_model->get_orders_by_sy(); 		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->load->view('layout/cafeteria/header', $data);
			$this->load->view('cafeteria/reports/studentsy', $data);
			$this->load->view('layout/cafeteria/footer', $data);
		} else {	
			$data['orderlistmonthly'] = $this->order_model->get_orders_monthly( $session_id );
			$session_details = $this->session_model->get( $session_id );
			$get_session_name = $session_details['session'];
			$data['session_selected'] = $this->order_model->get_orders_monthly( $session_id ); 	
			$data['get_session_name'] = $get_session_name; 	
			$data['getMonthList'] = $this->customlib->getMonthList(); 	
			$this->load->view('layout/cafeteria/header', $data);
			$this->load->view('cafeteria/reports/studentsy', $data);
			$this->load->view('layout/cafeteria/footer', $data);
		}
	}
	
	function view_orders(){
		$this->session->set_userdata('top_menu', 'Orders');
        $this->session->set_userdata('sub_menu', 'orders/index');
		$daterange="";
		$data['title'] = 'View Transaction';
		$student_id = $this->input->post('student_id');					
		$session_id = $this->input->post('session_id');					
		$date_range_from = $this->input->post('date_range_from');					
		$date_range_to = $this->input->post('date_range_to');		
		
		$orderlist = $this->order_model->get_range_individual_with_details( $student_id, $date_range_from, $date_range_to, $session_id); 
		$data['orderlist'] = $orderlist;
		$this->load->view('layout/cafeteria/header', $data);
		$this->load->view('cafeteria/orders/vieworders', $data);
		$this->load->view('layout/cafeteria/footer', $data);
	}
	

	
}

?>