<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sms_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function get($id = null) {
        $this->db->select()->from('sms_que');
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
    public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('sms_que');
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
            $this->db->update('sms_que', $data); 
        } else {
             
            $notificationexist = $this->checkifsmsexist( $data['sms_number'],  $data['sms_msg'] ) ; 
            if( $notificationexist == TRUE ){
                return false;
            } else { 
                $this->db->insert('sms_que', $data);
                return $this->db->insert_id();
            }
            
        }  
		// if (isset($data['id'])) {
  //           $this->db->where('id', $data['id']);
  //           $this->db->update('sms_que', $data); 
  //       } else {
  //           $this->db->insert('sms_que', $data);
  //           return $this->db->insert_id();
  //       }
    }

    public function checkifnotificationexist($id, $number, $message) {
       
        $this->db->select('sms_status')->from('sms_que'); 
        $this->db->where('notification_id', $id); 
        $this->db->where('sms_number', $number); 
        $this->db->where('sms_msg', $message);  
        $query = $this->db->get(); 
        $result = $query->row_array(); 

        if( $result['sms_status'] ){
            $stat = TRUE;
        } else {
             $stat = FALSE;
        } 
        return $stat;  
       
    }
    public function checkifsmsexist( $number, $message) {
         $date_today = date("Y-m-d");
        $this->db->select('sms_status')->from('sms_que');  
        $this->db->where('sms_number', $number); 
        $this->db->where('sms_msg', $message); 
        $this->db->where('DATE(date_created)', $date_today ); 
        $query = $this->db->get(); 
        $result = $query->row_array(); 

        if( $result['sms_status'] ){
            $stat = TRUE;
        } else {
             $stat = FALSE;
        } 
        return $stat;  
       
    }

    public function check_notification($id) {
        $this->db->select('sms_status')->from('sms_que'); 
        $this->db->where('notification_id', $id); 
        $query = $this->db->get(); 
        $result = $query->result_array(); 

        $stat = '';
        $status_array = array();
        if( $result ){
            foreach($result as $key => $value) {
                $status_array[] = $value['sms_status'];
            
                # code...
                
            }
        } 
        if (in_array("success", $status_array)) {
            $stat = 'success';  
        } elseif (in_array("idle", $status_array)) {
            $stat = 'idle';  
        } elseif (in_array("failed", $status_array)) {
            $stat = 'failed'; 
        }

        return $stat;  
       
    }

    public function remove_notification($id, $type='all') { 
        $this->db->where('notification_id', $id); 
        if( $type == 'teacher'){
            $this->db->where('sms_teacher', 'yes'); 
        } elseif( $type == 'parent'){
            $this->db->where('sms_parent','yes'); 
        }
        $this->db->delete('sms_que');
         
       
    }

    public function remove_attendance($id, $type='all') { 
        $this->db->where('attendance_id', $id); 
        if( $type == 'teacher'){
            $this->db->where('sms_teacher', 'yes'); 
        } elseif( $type == 'parent'){
            $this->db->where('sms_parent','yes'); 
        }
        $this->db->delete('sms_que');  
    } 
    
    public function remove_dorm_attendance($id, $type='all') { 
        $this->db->where('dorm_attendance_id', $id); 
        if( $type == 'teacher'){
            $this->db->where('sms_teacher', 'yes'); 
        } elseif( $type == 'parent'){
            $this->db->where('sms_parent','yes'); 
        }
        $this->db->delete('sms_que');  
    }

    public function remove_prefect($id, $type='all') { 
        $this->db->where('prefect_id', $id); 
        if( $type == 'teacher'){
            $this->db->where('sms_teacher', 'yes'); 
        } elseif( $type == 'parent'){
            $this->db->where('sms_parent','yes'); 
        }
        $this->db->delete('sms_que');  
    }

    public function remove_guidance($id, $type='all') { 
        $this->db->where('guidance_id', $id); 
        if( $type == 'teacher'){
            $this->db->where('sms_teacher', 'yes'); 
        } elseif( $type == 'parent'){
            $this->db->where('sms_parent','yes'); 
        }
        $this->db->delete('sms_que');  
    }

    public function remove_clinic($id, $type='all') { 
        $this->db->where('clinic_id', $id); 
        if( $type == 'teacher'){
            $this->db->where('sms_teacher', 'yes'); 
        } elseif( $type == 'parent'){
            $this->db->where('sms_parent','yes'); 
        }
        $this->db->delete('sms_que');  
    }

     public function getsendpending($date=null) {
        $return = array();
        $this->db->select('count(id) as success')->from('sms_que'); 
        $this->db->where('sms_status','success'); 
        $query = $this->db->get();
        $results1 = $query->row_array(); 
        $success = $results1['success'];


        $this->db->select('count(id) as idle')->from('sms_que'); 
        $this->db->where('sms_status','idle'); 
        $query = $this->db->get();
        $results1 = $query->row_array(); 
        $idle = $results1['idle'];

         $return = array( 'success' => $success, 'idle' => $idle);
        return $return; 
         
    }

    public function getdailyinfo(){
        $dailytap = date('Y-m-d');
        // $url = "http://sms.goshencybernetics.com/sms/getdailytap/mma/".$dailytap;
        // $url = "http://sms.goshencybernetics.com/sms/getdailytap/mma";
        $url = "hhttp://157.230.43.105/smsgateway/systemapi/getdailytap/MMA/".$dailytap;
         
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data_daily_tap = curl_exec($ch);

        if(curl_errno($ch)){
            $return_data['dailytap'] = 0;
        } else {  
            $transaction_daily_tap = json_decode($data_daily_tap, TRUE); 
            curl_close($ch); 
            $return_data['dailytap'] = $transaction_daily_tap[0]["tap_count"]; 
        } 


        // $url = "http://sms.goshencybernetics.com/sms/getsmscount/mma/";
        $url = "hhttp://157.230.43.105/smsgateway/systemapi/getsmscount/MMA/".$dailytap;
         
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data_smscount = curl_exec($ch);

        if(curl_errno($ch)){
            $datasmscount = 0;
        } else {  
            $transaction_smscount = json_decode($data_smscount, TRUE);  
            curl_close($ch); 
            $datasmscount = $transaction_smscount[0]["tap_count"]; 
        }  

        $return_data['datasmscount'] = $datasmscount;

        

        // $sja_api_tapping_data = $this->callAPI('GET', 'http://sms.goshencybernetics.com/systemapi/get_sms_data/SJA/'.$dailytap , false);
        $sja_api_tapping_data = $this->callAPI('GET', 'http://157.230.43.105/smsgateway/systemapi/get_sms_data/MMA/'.$dailytap , false);

        $sja_return_tapping_data = json_decode($sja_api_tapping_data, true); 


        $return_data['dailytap'] = $sja_return_tapping_data['today_tapping_in'].'/'.$sja_return_tapping_data['today_tapping_out'];

        $this->db->select('count(id) AS today_faculty_tapping_in');
        $this->db->from('facultystaff_dtr_log'); 
        $this->db->where('type', 'IN' );
        $this->db->where('DATE(date)', $dailytap );
        $today_faculty_tapping_in = $this->db->get()->row_array();
        
        $this->db->select('count(id) AS today_faculty_tapping_out');
        $this->db->from('facultystaff_dtr_log'); 
        $this->db->where('type', 'OUT' );
        $this->db->where('DATE(date)', $dailytap );
        $today_faculty_tapping_out = $this->db->get()->row_array();

        $return_data['faculty_dailytap'] = $today_faculty_tapping_in['today_faculty_tapping_in'].'/'.$today_faculty_tapping_out['today_faculty_tapping_out'];

        return $return_data;
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
