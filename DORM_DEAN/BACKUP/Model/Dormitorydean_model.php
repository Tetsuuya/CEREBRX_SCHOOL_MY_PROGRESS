<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dormitorydean_model extends CI_Model {

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
        $this->db->select()->from('dormitorydean');
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
    
    public function getdormitorydean($id = null) {
        $this->db->select('dormitorydean.*,users.id as `user_tbl_id`,users.username,users.password as `user_tbl_password`,users.is_active as `user_tbl_active`');
        $this->db->from('dormitorydean');
        $this->db->join('users', 'users.user_id = dormitorydean.id', 'left'); 
        $this->db->where('users.role', 'dormitorydean');
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
        $this->db->delete('dormitorydean');
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
            $this->db->update('dormitorydean', $data); 
        } else {
            $this->db->insert('dormitorydean', $data);
            return $this->db->insert_id();
        }
    }

    public function removeStudent($student_id) {
        $this->db->where('student_id', $student_id);
        $this->db->delete('student_hostel_room');

    // Check if any rows were affected
    return $this->db->affected_rows() > 0;
}


    public function getTotaldormitorydean() {
        $sql = "SELECT count(*) as `total_dormitorydean` FROM `dormitorydean`";
        $query = $this->db->query($sql);
        return $query->row(); 
    }

    public function getstudentsunderthedean( $dormdean_id, $session_id){
        $this->db->select('hostel.dormdean_id');
        $this->db->select('student_hostel_room.id'); 
        $this->db->select('student_hostel_room.hostel_id');
        $this->db->select('student_hostel_room.student_id');
        $this->db->select('student_hostel_room.session_id'); 
        $this->db->select('student_hostel_room.is_active');
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
        $this->db->select('students.image');

        $this->db->from('student_hostel_room');

        $this->db->join('students', 'student_hostel_room.student_id = students.id ', 'left'); 
        $this->db->join('hostel_rooms', 'hostel_rooms.id = student_hostel_room.hostel_id', 'left'); 
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left'); 
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left'); 
        
        $this->db->where('hostel.dormdean_id', $dormdean_id);
        $this->db->where('student_hostel_room.session_id', $session_id);
        $this->db->order_by('students.lastname', 'ASC');
        $this->db->order_by('students.firstname', 'ASC');
        $this->db->order_by('students.middlename', 'ASC');
        $query = $this->db->get();
        return $query->result_array(); 
    }

    public function getstudentsdetails( $student_id, $session_id){
        $this->db->select('hostel.dormdean_id');
        $this->db->select('student_hostel_room.id'); 
        $this->db->select('student_hostel_room.hostel_id as room_id');
        $this->db->select('hostel_rooms.hostel_id as dorm_id');
        $this->db->select('student_hostel_room.student_id');
        $this->db->select('student_hostel_room.session_id'); 
        $this->db->select('student_hostel_room.is_active');
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
        $this->db->from('student_hostel_room');
        $this->db->join('students', 'student_hostel_room.student_id = students.id ', 'left'); 
        $this->db->join('hostel_rooms', 'hostel_rooms.id = student_hostel_room.hostel_id', 'left'); 
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left'); 
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left'); 
        $this->db->where('student_hostel_room.student_id', $student_id);
        $this->db->where('student_hostel_room.session_id', $session_id);
        $this->db->order_by('students.lastname', 'ASC');
        $this->db->order_by('students.firstname', 'ASC');
        $this->db->order_by('students.middlename', 'ASC');
        $query = $this->db->get();
        return $query->row_array(); 
    }

    public function getstudentsunderroomdetails( $room_id, $session_id){
        $this->db->select('hostel.dormdean_id');
        $this->db->select('student_hostel_room.id'); 
        $this->db->select('student_hostel_room.hostel_id as room_id');
        $this->db->select('hostel_rooms.hostel_id as dorm_id');
        $this->db->select('student_hostel_room.student_id');
        $this->db->select('student_hostel_room.session_id'); 
        $this->db->select('student_hostel_room.is_active');
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
        $this->db->from('student_hostel_room');
        $this->db->join('students', 'student_hostel_room.student_id = students.id ', 'left'); 
        $this->db->join('hostel_rooms', 'hostel_rooms.id = student_hostel_room.hostel_id', 'left'); 
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left'); 
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left'); 
        $this->db->where('student_hostel_room.hostel_id', $room_id);
        $this->db->where('student_hostel_room.session_id', $session_id);
        $this->db->order_by('students.lastname', 'ASC');
        $this->db->order_by('students.firstname', 'ASC');
        $this->db->order_by('students.middlename', 'ASC');
        $query = $this->db->get();
        return $query->result_array(); 
    }

}
