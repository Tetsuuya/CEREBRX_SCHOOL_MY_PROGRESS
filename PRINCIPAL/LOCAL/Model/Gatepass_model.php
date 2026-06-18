<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Gatepass_model extends CI_Model {

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
        $this->db->select()->from('gatepass');
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
        $this->db->delete('gatepass');
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
            $this->db->update('gatepass', $data); 
        } else {
            $this->db->insert('gatepass', $data);
            return $this->db->insert_id();
        }
    } 


     public function getactiverecords( $dormdean_id, $session_id, $limit = null, $offset = 0 ){
        $this->db->select('gatepass.*'); 
        $this->db->select('hostel.hostel_name');
        $this->db->select('hostel_rooms.room_no'); 
        $this->db->select('hostel_rooms.room_type_id');
        $this->db->select('room_types.room_type'); 
        $this->db->select('students.lastname');
        $this->db->select('students.firstname'); 
        $this->db->select('students.middlename');
        $this->db->select('students.mobileno');
        $this->db->select('students.guardian_name');
        $this->db->select('students.guardian_midname');
        $this->db->select('students.guardian_lastname');
        $this->db->select('students.guardian_phone');
        $this->db->select('students.guardian_address');
        $this->db->select('students.guardian_address2');
        $this->db->from('gatepass');
        $this->db->join('students', 'gatepass.student_id = students.id ', 'left'); 
        $this->db->join('hostel_rooms', 'hostel_rooms.id = gatepass.room_id', 'left'); 
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left'); 
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');  
        $this->db->where('gatepass.session_id', $session_id);
        $this->db->where('gatepass.deleted', '0');
        $this->db->where('hostel.dormdean_id', $dormdean_id);
        // REMOVED filter: Show ALL types (Regular, Emergency, AND Campus Leave)
        $this->db->order_by('gatepass.status', 'DESC');
        $this->db->order_by('gatepass.created_at', 'ASC');
        $this->db->order_by('gatepass.exit_date', 'ASC');
        
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        return $query->result_array(); 
    }

    public function getactiverecords_count( $dormdean_id, $session_id ){
        $this->db->from('gatepass');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = gatepass.room_id', 'left'); 
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');  
        $this->db->where('gatepass.session_id', $session_id);
        $this->db->where('gatepass.deleted', '0');
        $this->db->where('hostel.dormdean_id', $dormdean_id);
        // REMOVED filter: Count ALL types
        return $this->db->count_all_results();
    }

    public function getactivecampusrecords( $dormdean_id, $session_id, $limit = null, $offset = 0, $search = null ){
        $this->db->select('gatepass.*'); 
        $this->db->select('hostel.hostel_name');
        $this->db->select('hostel_rooms.room_no'); 
        $this->db->select('hostel_rooms.room_type_id');
        $this->db->select('room_types.room_type'); 
        $this->db->select('students.lastname');
        $this->db->select('students.firstname'); 
        $this->db->select('students.middlename');
        $this->db->select('students.mobileno');
        $this->db->select('students.guardian_name');
        $this->db->select('students.guardian_midname');
        $this->db->select('students.guardian_lastname');
        $this->db->select('students.guardian_phone');
        $this->db->select('students.guardian_address');
        $this->db->select('students.guardian_address2');
        $this->db->from('gatepass');
        $this->db->join('students', 'gatepass.student_id = students.id ', 'left'); 
        $this->db->join('hostel_rooms', 'hostel_rooms.id = gatepass.room_id', 'left'); 
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left'); 
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');  
        $this->db->where('gatepass.session_id', $session_id);
        $this->db->where('gatepass.deleted', '0');
        // REMOVED: $this->db->where('hostel.dormdean_id', $dormdean_id); 
        // Principal now sees Campus Leave from ALL dorms
        $this->db->where('gatepass.type', 'campus'); // Show ONLY Campus Leave type
        
        // Search filter - Search multiple fields with starts-with priority
        if ($search !== null && $search !== '') {
            $starts_with = $this->db->escape_like_str($search) . '%';
            $contains = '%' . $this->db->escape_like_str($search) . '%';
            
            // Search by lastname, firstname (starts with OR contains), purpose, destination, status, type
            $this->db->group_start();
            $this->db->where("students.lastname LIKE '$starts_with'");
            $this->db->or_where("students.firstname LIKE '$starts_with'");
            $this->db->or_where("students.lastname LIKE '$contains'");
            $this->db->or_where("students.firstname LIKE '$contains'");
            $this->db->or_where("gatepass.purpose LIKE '$contains'");
            $this->db->or_where("gatepass.destination LIKE '$contains'");
            $this->db->or_where("gatepass.status LIKE '$contains'");
            $this->db->or_where("gatepass.type LIKE '$contains'");
            $this->db->group_end();
        }
        
        // Ordering: Priority to "starts with" name matches first
        if ($search !== null && $search !== '') {
            // Show "starts with" lastname first, then "starts with" firstname, then contains
            $this->db->order_by("CASE 
                WHEN students.lastname LIKE '$starts_with' THEN 1 
                WHEN students.firstname LIKE '$starts_with' THEN 2
                WHEN students.lastname LIKE '$contains' THEN 3
                WHEN students.firstname LIKE '$contains' THEN 4
                WHEN gatepass.purpose LIKE '$contains' THEN 5
                WHEN gatepass.destination LIKE '$contains' THEN 6
                ELSE 7 
            END", '', FALSE);
        }
        $this->db->order_by('gatepass.status', 'DESC');
        $this->db->order_by('gatepass.created_at', 'ASC');
        $this->db->order_by('gatepass.exit_date', 'ASC');
        
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        return $query->result_array(); 
    }

    public function getactivecampusrecords_count( $dormdean_id, $session_id, $search = null ){
        $this->db->from('gatepass');
        $this->db->join('students', 'gatepass.student_id = students.id ', 'left');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = gatepass.room_id', 'left'); 
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');  
        $this->db->where('gatepass.session_id', $session_id);
        $this->db->where('gatepass.deleted', '0');
        // REMOVED: $this->db->where('hostel.dormdean_id', $dormdean_id); 
        // Principal sees Campus Leave from ALL dorms
        $this->db->where('gatepass.type', 'campus');
        
        // Search filter - Search multiple fields with starts-with priority
        if ($search !== null && $search !== '') {
            $starts_with = $this->db->escape_like_str($search) . '%';
            $contains = '%' . $this->db->escape_like_str($search) . '%';
            
            // Search by lastname, firstname (starts with OR contains), purpose, destination, status, type
            $this->db->group_start();
            $this->db->where("students.lastname LIKE '$starts_with'");
            $this->db->or_where("students.firstname LIKE '$starts_with'");
            $this->db->or_where("students.lastname LIKE '$contains'");
            $this->db->or_where("students.firstname LIKE '$contains'");
            $this->db->or_where("gatepass.purpose LIKE '$contains'");
            $this->db->or_where("gatepass.destination LIKE '$contains'");
            $this->db->or_where("gatepass.status LIKE '$contains'");
            $this->db->or_where("gatepass.type LIKE '$contains'");
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }

    public function getapproverecords(   $dormdean_id, $session_id ){
        $this->db->select('gatepass.*'); 
        $this->db->select('hostel.hostel_name');
        $this->db->select('hostel_rooms.room_no'); 
        $this->db->select('hostel_rooms.room_type_id');
        $this->db->select('room_types.room_type'); 
        $this->db->select('students.lastname');
        $this->db->select('students.firstname'); 
        $this->db->select('students.middlename');
        $this->db->select('students.mobileno');
        $this->db->select('students.image');
        $this->db->select('students.guardian_name');
        $this->db->select('students.guardian_midname');
        $this->db->select('students.guardian_lastname');
        $this->db->select('students.guardian_phone');
        $this->db->select('students.guardian_address');
        $this->db->select('students.guardian_address2');
        $this->db->from('gatepass');
        $this->db->join('students', 'gatepass.student_id = students.id ', 'left'); 
        $this->db->join('hostel_rooms', 'hostel_rooms.id = gatepass.room_id', 'left'); 
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left'); 
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');  
        $this->db->where('gatepass.session_id', $session_id);
        $this->db->where('gatepass.status', 'approve');
        $this->db->where('hostel.dormdean_id', $dormdean_id);
        $this->db->where('gatepass.deleted', '0');
        $this->db->order_by('gatepass.status', 'DESC');
        $this->db->order_by('gatepass.created_at', 'ASC');
        $this->db->order_by('gatepass.exit_date', 'ASC');
        $query = $this->db->get();
        return $query->result_array(); 
    }

    public function getstudentapproverecords( $student_id, $session_id ){
        $this->db->select('gatepass.*'); 
        $this->db->select('hostel.hostel_name');
        $this->db->select('hostel_rooms.room_no'); 
        $this->db->select('hostel_rooms.room_type_id');
        $this->db->select('room_types.room_type'); 
        $this->db->select('students.lastname');
        $this->db->select('students.firstname'); 
        $this->db->select('students.middlename');
        $this->db->select('students.mobileno');
        $this->db->select('students.image');
        $this->db->select('students.guardian_name');
        $this->db->select('students.guardian_midname');
        $this->db->select('students.guardian_lastname');
        $this->db->select('students.guardian_phone');
        $this->db->select('students.guardian_address');
        $this->db->select('students.guardian_address2');
        $this->db->from('gatepass');
        $this->db->join('students', 'gatepass.student_id = students.id ', 'left'); 
        $this->db->join('hostel_rooms', 'hostel_rooms.id = gatepass.room_id', 'left'); 
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left'); 
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');  
        $this->db->where('gatepass.session_id', $session_id); 
        $this->db->where('gatepass.student_id', $student_id);
        $this->db->where('gatepass.deleted', '0');
        $this->db->order_by('gatepass.status', 'DESC');
        $this->db->order_by('gatepass.created_at', 'ASC');
        $this->db->order_by('gatepass.exit_date', 'ASC');
        $query = $this->db->get();
        return $query->result_array(); 
    }

    public function getstudenttapdetails($student_id,$exit_date) {
        $date = date("Y-m-d", strtotime($exit_date));
        $response = array();
        $url = "http://157.230.43.105/smsgateway/systemapi/getstudenttappingbydatetapreport/LVAA/$student_id/$date";
        $curl_handle=curl_init();
        curl_setopt($curl_handle,CURLOPT_URL, $url);
        curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
        curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
        $buffer = curl_exec($curl_handle);
        curl_close($curl_handle);

        if ( $buffer ){
            // printr($buffer);
            $buffer = json_decode($buffer);
            foreach ($buffer as $key => $value) {
                $sms_msg = $value->sms_msg;
                $date_created = $value->date_created;
                $date_created = date("F d, Y", strtotime($date_created));

                $sms_msg = str_replace('This is an Autogenerated SMS by Bridgette. Please do not reply.', '', $sms_msg);
                $sms_msg = str_replace('.', '', $sms_msg);
                $data = "123_String";    
                $whatIWant = substr($sms_msg, strpos($sms_msg, "is") + 2);    
                $response[] = trim( $date_created.' '.$whatIWant);    
            
                // code...
            }

            return ($response);
        } 
         
    }
}
