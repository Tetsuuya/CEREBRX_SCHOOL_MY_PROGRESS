<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Student_model extends CI_Model {

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
    public function getStudents() {

        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no, students.lrn,students.admission_date,students.firstname, students.middlename,  students.lastname,students.image,students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name ,students.guardian_email , students.guardian_midname , students.guardian_lastname , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active, students.esc_number ,students.government_number ,students.created_at ,students.updated_at,students.father_name,students.father_midname, students.mother_name,  students.mother_midname, students.mother_lastname, students.rfid_number, students.father_lastname,students.rte,students.gender,students.load_balance,students.hostel_id,students.dormitory,users.id as `user_tbl_id`,users.username,users.password as `user_tbl_password`,users.is_active as `user_tbl_active`,suffix')->from('students');
        
        /** Conflict - Rubins Code
        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.guardian_midname , students.guardian_lastname , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.esc_number ,students.government_number ,students.created_at ,students.updated_at,students.father_name,students.father_midname,students.father_lastname,students.rte,students.gender,users.id as `user_tbl_id`,users.username,users.password as `user_tbl_password`,users.is_active as `user_tbl_active`')->from('students');
        **/

        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','left');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        $this->db->join('users', 'users.user_id = students.id', 'left');
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('users.role', 'student');

        $this->db->order_by('students.id');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getRecentRecord($id = null) {
        $this->db->select('classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.lrn,students.admission_date,students.firstname,  students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,students.category_id,    students.rfid_number, students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.guardian_email ,students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.esc_number ,students.government_number ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is, students.father_lastname, students.mother_lastname,students.load_balance,students.hostel_id,students.dormitory')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','left');
        $this->db->where('student_session.session_id', $this->current_session);
        if ($id != null) {
            $this->db->where('students.id', $id);
        } else {
            
        }
        $this->db->order_by('students.id', 'desc');
        $this->db->limit(5);
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
	
    public function get($id = null) {
        /* $this->db->select('student_session.transport_fees,student_session.vehroute_id,student_session.id as `student_session_id`,student_session.fees_discount,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode,students.religion, students.cast,students.dob ,students.current_address, students.previous_school,students.government_voucher,students.blood_type,
            students.guardian_is, students.commuter_pass, students.lunch_pass, students.rfid_number, students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.guardian_relation,students.guardian_phone,students.guardian_email,students.guardian_address,students.guardian_barangay_id,students.guardian_city_id,students.guardian_province_id,students.current_barangay_id,students.current_city_id,students.current_province_id,students.permanent_barangay_id,students.esc_number ,students.government_number ,students.permanent_city_id,students.permanent_province_id,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte, students.lrn, students.middlename, students.father_midname, students.father_lastname, students.mother_midname, students.mother_lastname, students.guardian_midname, students.guardian_lastname,students.strand_id, status,students.load_balance,students.hostel_id,students.dormitory,student_session.enrolled_at,session')->from('students'); */
		// $this->db->select('student_session.transport_fees,student_session.vehroute_id,student_session.id as `student_session_id`,student_session.fees_discount,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode,students.religion, students.cast,students.dob ,students.current_address, students.previous_school,students.government_voucher,students.blood_type,
  //           students.guardian_is, students.commuter_pass, students.lunch_pass, students.rfid_number, students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.guardian_relation,students.guardian_phone,students.guardian_email,students.guardian_address,students.guardian_barangay_id,students.guardian_city_id,students.guardian_province_id,students.current_barangay_id,students.current_city_id,students.current_province_id,students.permanent_barangay_id,students.esc_number ,students.government_number ,students.permanent_city_id,students.permanent_province_id,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte, students.lrn, students.middlename, students.father_midname, students.father_lastname, students.mother_midname, students.mother_lastname, students.guardian_midname, students.guardian_lastname,student_session.strand_id, status,students.load_balance,students.hostel_id,students.dormitory,student_session.enrolled_at,session,suffix,students.guardian_address2,students.school_name,students.school_address,students.signature_image,CONCAT(UCASE(MID(student_session.esc_grantee,1,1)),MID(student_session.esc_grantee,2)) as rte, CONCAT(UCASE(MID(student_session.gov_voucher,1,1)),MID(student_session.gov_voucher,2)) as government_voucher')->from('students');
        $this->db->select('student_session.transport_fees,student_session.vehroute_id,student_session.id as `student_session_id`,student_session.fees_discount,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode,students.religion, students.cast,students.dob ,students.current_address, students.previous_school,students.government_voucher,students.blood_type,
            students.guardian_is, students.commuter_pass, students.lunch_pass, students.rfid_number, students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.guardian_relation,students.guardian_phone,students.guardian_email,students.guardian_address,students.guardian_barangay_id,students.guardian_city_id,students.guardian_province_id,students.current_barangay_id,students.current_city_id,students.current_province_id,students.permanent_barangay_id,students.esc_number ,students.government_number ,students.permanent_city_id,students.permanent_province_id,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte, students.lrn, students.middlename, students.father_midname, students.father_lastname, students.mother_midname, students.mother_lastname, students.guardian_midname, students.guardian_lastname,student_session.strand_id, status,students.load_balance,students.hostel_id,students.dormitory,student_session.enrolled_at,session,suffix,students.guardian_address2,students.school_name,students.school_address,students.signature_image, students.esc_grantee, CONCAT(UCASE(MID(student_session.gov_voucher,1,1)),MID(student_session.gov_voucher,2)) as government_voucher,student_session.allow, student_session.session_id')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','left');
        $this->db->join('sessions', 'sessions.id = student_session.session_id');
        $this->db->where('student_session.session_id', $this->current_session);
        if ($id != null) {
            $this->db->where('students.id', $id);
        } else {
            $this->db->order_by('student_session.enrolled_at', 'desc');
            $this->db->order_by('students.id', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }

    }
	
	 public function getBySession($id = null, $session_id = null ) {
        /* $this->db->select('student_session.transport_fees,student_session.vehroute_id,student_session.id as `student_session_id`,student_session.fees_discount,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode,students.religion, students.cast,students.dob ,students.current_address, students.previous_school,students.government_voucher,students.blood_type,
            students.guardian_is, students.commuter_pass, students.lunch_pass, students.rfid_number, students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.guardian_relation,students.guardian_phone,students.guardian_email,students.guardian_address,students.guardian_barangay_id,students.guardian_city_id,students.guardian_province_id,students.current_barangay_id,students.current_city_id,students.current_province_id,students.permanent_barangay_id,students.esc_number ,students.government_number ,students.permanent_city_id,students.permanent_province_id,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte, students.lrn, students.middlename, students.father_midname, students.father_lastname, students.mother_midname, students.mother_lastname, students.guardian_midname, students.guardian_lastname,students.strand_id, status,students.load_balance,students.hostel_id,students.dormitory,student_session.enrolled_at,session')->from('students'); */
		$this->db->select('student_session.transport_fees,student_session.vehroute_id,student_session.id as `student_session_id`,student_session.fees_discount,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode,students.religion, students.cast,students.dob ,students.current_address, students.previous_school,students.government_voucher,students.blood_type,
            students.guardian_is, students.commuter_pass, students.lunch_pass, students.rfid_number, students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.guardian_relation,students.guardian_phone,students.guardian_email,students.guardian_address,students.guardian_barangay_id,students.guardian_city_id,students.guardian_province_id,students.current_barangay_id,students.current_city_id,students.current_province_id,students.permanent_barangay_id,students.esc_number ,students.government_number ,students.permanent_city_id,students.permanent_province_id,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte, students.lrn, students.middlename, students.father_midname, students.father_lastname, students.mother_midname, students.mother_lastname, students.guardian_midname, students.guardian_lastname,student_session.strand_id, status,students.load_balance,students.hostel_id,students.dormitory,student_session.enrolled_at,session, students.suffix,student_session.modality,guardian_address2,signature_image, strand.code as strand_code, strand.name as strand_name, students.esc_grantee ')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','left');
        $this->db->join('sessions', 'sessions.id = student_session.session_id');
        $this->db->join('strand', 'strand.id = student_session.strand_id', 'left');
		//$this->db->where('status', 'active');
		if( $session_id ){
			$this->db->where('student_session.session_id', $session_id);
		} else {
			$this->db->where('student_session.session_id', $this->current_session);
		}
        if ($id != null) {
            $this->db->where('students.id', $id);
        } else {
            $this->db->order_by('student_session.enrolled_at', 'desc');
            $this->db->order_by('students.id', 'desc');
        }
        $query = $this->db->get();
 
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }

    }

    public function get_with_limit($id = null, $limit=5) {
        $this->db->select('student_session.transport_fees,student_session.vehroute_id,student_session.id as `student_session_id`,student_session.fees_discount,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.lrn,students.admission_date,students.firstname,  students.middlename,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion, students.cast,    students.dob ,students.current_address, students.previous_school,students.government_voucher,students.blood_type,
            students.guardian_is,students.esc_number ,students.government_number ,
            students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.rfid_number, students.guardian_relation,students.guardian_email ,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.load_balance,students.hostel_id,students.dormitory')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','left');
        $this->db->where('student_session.session_id', $this->current_session);
        if ($id != null) {
            $this->db->where('students.id', $id);
        } else {
            $this->db->order_by('students.id', 'desc');
        }
		 $this->db->limit($limit);
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function search_student() {
        $this->db->select('classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.lrn,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,students.category_id,    students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.esc_number ,students.government_number, students.rfid_number,  ,students.guardian_relation,students.guardian_email ,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.load_balance,students.hostel_id,students.dormitory')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','left');
        $this->db->where('student_session.session_id', $this->current_session);
        if ($id != null) {
            $this->db->where('students.id', $id);
        } else {
            $this->db->order_by('students.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getstudentdoc($id) {
        $this->db->select()->from('student_doc');
        $this->db->where('student_id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

   

   public function searchByClassSection($class_id = null, $section_id = null, $order_by = "students.id", $session_id = null ) {
        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,students.lrn,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.esc_number ,students.government_number, students.rfid_number, students.guardian_lastname  ,students.guardian_relation,students.guardian_email ,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.rte,students.gender, students.father_lastname,students.load_balance,students.hostel_id,students.dormitory, students.suffix,student_session.strand_id,students.guardian_is, students.mother_name,students.mother_lastname,students.mother_midname,father_midname,students.guardian_midname,students.guardian_lastname,students.signature_image,student_session.allow')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
		if(empty( $session_id )){
			$session_id = $this->current_session;
		} 
		$this->db->where('student_session.session_id', $session_id );
       // $this->db->where('students.status', 'active');
        if ($class_id != null) {
            $this->db->where('student_session.class_id', $class_id);
        }
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }
        
		if( $order_by == 'lastname'){
			  $this->db->order_by('students.lastname');
			  $this->db->order_by('students.firstname');
		} else {
			$this->db->order_by('students.id');
		}

        $query = $this->db->get();
        return $query->result_array();
    }
	
    // This function is used to get student id and strand id based on session id and class id. It is used in attendance module to get the students of a particular class and session along with their strand id.
    public function getStudentIDAndStrandIDBySessionIDAndClassID($class_id = null, $order_by = "s.id", $session_id = null) 
        {
            $session_id = isset($session_id) ? $session_id : $this->current_session;

            if (empty($class_id)) {
                return [];
            }

            $this->db->select('
                s.id, 
                ss.class_id, 
                ss.strand_id, 
                s.firstname, 
                s.middlename, 
                s.lastname, 
                s.suffix
            ')
            ->from('students AS s')
            ->join('student_session AS ss', 'ss.student_id = s.id', 'inner')
            ->where('ss.session_id', $session_id)
            ->where('ss.class_id', $class_id)
            ->where('s.status', 'active')
            ->order_by($order_by);

            return $this->db->get()->result_array();
        }

	public function searchByClassSectionSession($class_id = null, $section_id = null, $order_by = "students.id", $session_id=null) {

        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,students.lrn,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.esc_number ,students.government_number, students.rfid_number, students.guardian_lastname  ,students.guardian_relation,students.guardian_email ,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.rte,students.gender, students.father_lastname,students.load_balance,students.hostel_id,students.dormitory, students.suffix,student_session.strand_id')->from('students');

        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
		if( $session_id ){
			$this->db->where('student_session.session_id', $session_id );
		} else {
			$this->db->where('student_session.session_id', $this->current_session);
		}
        if ($class_id != null) {
            $this->db->where('student_session.class_id', $class_id);
        }
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }
		  if( $order_by == 'lastname'){
			  $this->db->order_by('students.lastname');
			  $this->db->order_by('students.firstname');
		} else {
			$this->db->order_by($order_by);
		}

        $query = $this->db->get();
        return $query->result_array();
    }
	
	public function notsearchByClassSection($class_id = null, $section_id = null, $order_by = "students.id") {
        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,students.lrn,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.esc_number ,students.government_number, students.rfid_number, students.guardian_lastname  ,students.guardian_relation,students.guardian_email ,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.rte,students.gender, students.father_lastname,students.load_balance,students.hostel_id,students.dormitory')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        $this->db->where('student_session.session_id', $this->current_session);
        if ($class_id != null) {
            $this->db->where('student_session.class_id', $class_id);
        }
        if ($section_id != null) {
            $this->db->where('student_session.section_id <>', $section_id);
        }
        $this->db->order_by($order_by);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function searchByClassSectionCategoryGenderRte($class_id = null, $section_id = null
    , $category = null, $gender = null, $rte = null, $order_by = 'students.id') {
        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no, students.lrn,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address, students.rfid_number,    students.permanent_address,students.category_id, categories.category,students.guardian_email ,   students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.esc_number ,students.government_number ,students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.rte,students.gender,students.load_balance,students.hostel_id,students.dormitory')->from('students');

        $this->db->join('student_session', 'student_session.student_id = students.id','LEFT');
        $this->db->join('classes', 'student_session.class_id = classes.id','LEFT');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id','LEFT');
        $this->db->where('student_session.session_id', $this->current_session);
        if ($class_id != null) {
            $this->db->where('student_session.class_id', $class_id);
        }
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }
        if ($category != null) {
            $this->db->where('students.category_id', $category);
        }
        if ($gender != null) {
            $this->db->where('students.gender', $gender);
        }
        if ($rte != null) {
            $this->db->where('students.rte', $rte);
        }
        $this->db->order_by($order_by);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function searchFullText($searchterm , $include_session = "yes", $session_id = '', $active_only = false ) {
		$searchterm = trim( $searchterm );
        $this->db->select('classes.id AS `class_id`,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.lrn,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,      students.esc_number ,students.government_number ,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code ,students.father_name ,students.father_lastname , students.guardian_email ,students.guardian_name , students.rfid_number, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.gender,students.rte,student_session.session_id,students.load_balance,students.hostel_id,students.dormitory,students.suffix,guardian_lastname')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        if(  $include_session == "yes"){
			if( empty( $session_id )){
				$session_id = $this->current_session;
			}
            $this->db->where('student_session.session_id', $session_id );
        } 
        if( $active_only ){
             $this->db->where('students.status', 'active' );
        }
        $this->db->group_start();
        $this->db->like('students.firstname', $searchterm);
        $this->db->or_like('students.lastname', $searchterm);
        $this->db->or_like('students.guardian_name', $searchterm);
        $this->db->or_like('students.adhar_no', $searchterm);
        $this->db->or_like('students.samagra_id', $searchterm);
        $this->db->or_like('students.roll_no', $searchterm);
        $this->db->or_like('students.admission_no', $searchterm);
        $this->db->or_like('students.lrn', $searchterm);
        $this->db->or_like('students.rfid_number', $searchterm);
       // $this->db->or_like('students.rfid', $searchterm);
        $this->db->group_end();
        
        $starts_with = $this->db->escape_like_str($searchterm) . '%';
        $contains = '%' . $this->db->escape_like_str($searchterm) . '%';
        
        // Order by priority: lastname starts with > firstname starts with > lastname contains > firstname contains
        $this->db->order_by("CASE 
            WHEN students.lastname LIKE '$starts_with' THEN 1 
            WHEN students.firstname LIKE '$starts_with' THEN 2
            WHEN students.lastname LIKE '$contains' THEN 3
            WHEN students.firstname LIKE '$contains' THEN 4
            ELSE 5 
        END", '', FALSE);
        $this->db->order_by('students.lastname', 'asc');
        $this->db->order_by('students.firstname', 'asc');
       // $this->db->group_by('students.id');
        $this->db->limit('10');
        $query = $this->db->get();
        return $query->result_array();
    }

     public function searchFullTextTranferee($searchterm , $include_session = "yes", $session_id = '', $active_only = FALSE ) {
        $searchterm = trim( $searchterm );
        $this->db->select('classes.id AS `class_id`,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.lrn,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,      students.esc_number ,students.government_number ,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code ,students.father_name ,students.father_lastname , students.guardian_email ,students.guardian_name , students.rfid_number, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.gender,students.rte,student_session.session_id,students.load_balance,students.hostel_id,students.dormitory,students.suffix,guardian_lastname')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        if(  $include_session == "yes"){
            if( empty( $session_id )){
                $session_id = $this->current_session;
            }
            $this->db->where('student_session.session_id', $session_id );
        } 
       $this->db->group_start();
        $this->db->like('students.firstname', $searchterm);
        $this->db->or_like('students.lastname', $searchterm);
        $this->db->or_like('students.guardian_name', $searchterm);
        $this->db->or_like('students.adhar_no', $searchterm);
        $this->db->or_like('students.samagra_id', $searchterm);
        $this->db->or_like('students.roll_no', $searchterm);
        $this->db->or_like('students.admission_no', $searchterm);
        $this->db->or_like('students.lrn', $searchterm);
        if( $active_only ){
            $this->db->not_like('students.status', 'inactive');
        }
       // $this->db->or_like('students.rfid', $searchterm);
       $this->db->group_end();
        $this->db->order_by('students.id');
        //$this->db->group_by('students.id');
        $this->db->limit('10');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function searchFullTextPhoto($searchterm , $include_session = "yes", $session_id = '') {
		$searchterm = trim( $searchterm );
        // $this->db->select('classes.id AS `class_id`,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.lrn,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,      students.esc_number ,students.government_number ,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code ,students.father_name ,students.father_lastname , students.guardian_email ,students.guardian_name , students.rfid_number, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.gender,students.rte,student_session.session_id,students.load_balance,students.hostel_id,students.dormitory,students.suffix,guardian_lastname')->from('students');
        // $this->db->join('student_session', 'student_session.student_id = students.id');
        // $this->db->join('classes', 'student_session.class_id = classes.id');
        // $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        // $this->db->join('categories', 'students.category_id = categories.id', 'left');

        $this->db->select('*')->from('students');
        
        // if(  $include_session == "yes"){
		// 	if( empty( $session_id )){
		// 		$session_id = $this->current_session;
		// 	}
        //     $this->db->where('student_session.session_id', $session_id );
        // } 
        $this->db->group_start();
        $this->db->like('students.lastname', $searchterm);
        $this->db->or_like('students.firstname', $searchterm);
        // $this->db->or_like('students.guardian_name', $searchterm);
        // $this->db->or_like('students.adhar_no', $searchterm);
        // $this->db->or_like('students.samagra_id', $searchterm);
        // $this->db->or_like('students.roll_no', $searchterm);
        $this->db->or_like('students.admission_no', $searchterm);
        $this->db->or_like('students.lrn', $searchterm);
       // $this->db->or_like('students.rfid', $searchterm);
        $this->db->group_end();
        // $this->db->order_by('students.id');
        // $this->db->group_by('students.id');
        $this->db->limit('10');
        $query = $this->db->get();
        return $query->result_array();
    }
	
    public function searchbylastnameandfirstname($lastname, $firstname, $admission_no) {
        $this->db->select('classes.id AS `class_id`,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.lrn,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,      students.esc_number ,students.government_number ,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code ,students.father_name ,students.father_lastname , students.guardian_email ,students.guardian_name , students.rfid_number, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.gender,students.rte,student_session.session_id,students.load_balance,students.hostel_id,students.dormitory')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id', 'left'); 
        $this->db->group_start();
        $this->db->like('students.admission_no', $admission_no); 
        $this->db->like('students.lastname', $lastname); 
        $this->db->like('students.firstname', $firstname);
        $this->db->group_end();
        $this->db->order_by('students.id');
        $this->db->limit('1');
        $query = $this->db->get();
        return $query->row();
    }

    public function remove($id) {
        $this->db->trans_start();
        $this->db->where('id', $id);
        $this->db->delete('students');

        $this->db->where('student_id', $id);
        $this->db->delete('student_session');

        $this->db->where('user_id', $id);
        $this->db->where('role', 'student');
        $this->db->delete('users');
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    public function doc_delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('student_doc');
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
            $this->db->update('students', $data);
        } else {
            $this->db->insert('students', $data);
            return $this->db->insert_id();
        }
    }

    public function add_student_sibling($data_sibling) {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('student_sibling', $data_sibling);
        } else {
            $this->db->insert('student_sibling', $data_sibling);
            return $this->db->insert_id();
        }
    }

    public function add_student_session($data) {
        $this->db->where('session_id', $data['session_id']);
        $this->db->where('student_id', $data['student_id']);
        $q = $this->db->get('student_session');
        if ($q->num_rows() > 0) {
            $rec = $q->row_array();
            $this->db->where('id', $rec['id']);
            $this->db->update('student_session', $data);
        } else {
            $this->db->insert('student_session', $data);
            return $this->db->insert_id();
        }
    }

    public function add_student_session_update($data) {
        $this->db->where('session_id', $data['session_id']);
        $q = $this->db->get('student_session');
        if ($q->num_rows() > 0) {
            $this->db->where('session_id', $student_session);
            $this->db->update('student_session', $data);
        } else {
            $this->db->insert('student_session', $data);
            return $this->db->insert_id();
        }
    }

    public function adddoc($data) {
        $this->db->insert('student_doc', $data);
        return $this->db->insert_id();
    }

    public function read_siblings_students($ids_comma) {
        $query = $this->db->query('select * from students WHERE id in (' . $ids_comma . ')');
        return $query->result_array();
    }

    public function getAttedenceByDateandClass($date) {
        $sql = "SELECT IFNULL(student_attendences.id, 0) as attencence FROM `student_session`left JOIN student_attendences on student_attendences.student_session_id=student_session.id and student_attendences.date=" . $this->db->escape($date) . " and student_attendences.attendence_type_id != 2 where student_session.class_id=7 and student_session.session_id=$this->current_session";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function searchCurrentSessionStudents() {
        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no students.lrn,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.esc_number ,students.government_number, students.rfid_number,  ,students.guardian_relation,students.guardian_email ,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.rte,students.gender,students.load_balance,students.hostel_id,students.dormitory')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        $this->db->where('student_session.session_id', $this->current_session);

        $this->db->order_by('students.firstname', 'asc');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function searchLibraryStudent($class_id = null, $section_id = null) {
        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,
           IFNULL(libarary_members.id,0) as `libarary_member_id`,
           IFNULL(libarary_members.library_card_no,0) as `library_card_no`,sections.section,students.id,students.admission_no , students.roll_no,students.lrn,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.esc_number ,students.government_number, students.rfid_number,  ,students.guardian_relation,students.guardian_phone,students.guardian_email ,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.rte,students.gender,students.load_balance,students.hostel_id,students.dormitory')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        $this->db->join('libarary_members', 'libarary_members.member_id = students.id and libarary_members.member_type = "student"', 'left');


        $this->db->where('student_session.session_id', $this->current_session);
        if ($class_id != null) {
            $this->db->where('student_session.class_id', $class_id);
        }
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }
        $this->db->order_by('students.id');

        $query = $this->db->get();
        return $query->result_array();
    }
	
	public function searchIDByClassSection( $lrn, $class_id = null, $section_id = null) {
        $this->db->select('students.id, students.lrn' )->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->where('student_session.session_id', $this->current_session);
        if ($lrn != null) {
            $this->db->where('students.lrn', $lrn);
        }
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }
		if ($lrn != null) {
            $this->db->where('student_session.section_id', $section_id);
        }
        $this->db->order_by('students.id');
        
        $query = $this->db->get();
        return $query->row(); 
    }
	
	public function getIDByClassSection( $id, $class_id = null, $section_id = null  ){
		
		if( $id ){
			$this->db->select('students.id, students.admission_no' )->from('students');	
			$this->db->join('student_session', 'student_session.student_id = students.id');
			$this->db->join('classes', 'student_session.class_id = classes.id');
			$this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
			$this->db->where('students.admission_no', $id);
			//$this->db->where('students.lrn', $id);
			$this->db->where('student_session.session_id', $this->current_session);
			if($class_id != null) {
				$this->db->where('student_session.class_id', $class_id);
			}
			if($section_id != null) {
				$this->db->where('student_session.section_id', $section_id);
			}
			$query = $this->db->get();
			$row = $query->row();
			
			if( $row ){
				return $row->id; 	
			} else {
				return false;	
			}	
		} else {
			return false;
		}
	}
	
	public function getIDByClassSectionBySession( $id, $class_id = null, $section_id = null, $session_id ){
		
		if( $id ){
			$this->db->select('students.id, students.admission_no' )->from('students');	
			$this->db->join('student_session', 'student_session.student_id = students.id');
			$this->db->join('classes', 'student_session.class_id = classes.id');
			$this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
			$this->db->where('students.admission_no', $id);
			//$this->db->where('students.lrn', $id);
			$this->db->where('student_session.session_id', $session_id);
			if($class_id != null) {
				$this->db->where('student_session.class_id', $class_id);
			}
			if($section_id != null) {
				$this->db->where('student_session.section_id', $section_id);
			}
			$query = $this->db->get();
			$row = $query->row();
			
			if( $row ){
				return $row->id; 	
			} else {
				return false;	
			}	
		} else {
			return false;
		}
	}
    
     public function getstudentsByClassSectionGender($class_id = null, $section_id = null, $gender = null, $session_id=null, $is_active = FALSE ) {

        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.esc_number ,students.government_number, students.rfid_number,  ,students.guardian_relation,students.guardian_phone,students.guardian_email ,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.rte,students.gender, students.father_lastname, gender, lrn,students.load_balance,students.hostel_id,students.dormitory,students.suffix,student_session.strand_id')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id','left');
		if (empty($session_id)) {
            $session_id = $this->current_session;
        }
		$this->db->where('student_session.session_id', $session_id);
        if ($class_id != null) {
            $this->db->where('student_session.class_id', $class_id);
        }
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }
         if ($gender != null) {
            $this->db->where('students.gender', $gender );
        }
        if( $is_active ){
             $this->db->where('students.status <>', 'inactive' );
        }
        $this->db->order_by('students.lastname');
		$this->db->order_by('students.firstname');
        
        
        $query = $this->db->get();
        return $query->result_array(); 
    }
	
	 public function getstudentsPhoneByClassSection($class_id = null, $section_id = null ) {

        $this->db->select('students.guardian_phone as phone')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id','left');
        $this->db->where('student_session.session_id', $this->current_session);
        if ($class_id != null) {
            $this->db->where('student_session.class_id', $class_id);
        }
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }  
        $this->db->where('students.guardian_phone is NOT NULL', NULL, FALSE);
        $this->db->where('students.status', 'active');
        $this->db->order_by('students.lastname');
        $this->db->order_by('students.firstname'); 
        
        $query = $this->db->get();
        return $query->result_array(); 
    }

    public function getPhoneClassSectionSubject($class_id, $section_id,  $subject) {

        $sql="select students.guardian_phone as phone from students ,(SELECT student_session.id,student_session.student_id ,IFNULL(student_attendences.date, 'xxx') as date,IFNULL(student_attendences.id, 0) as attendence_id,student_attendences.attendence_type_id FROM `student_session` LEFT JOIN student_attendences ON student_attendences.student_session_id=student_session.id where  student_session.session_id=" . $this->db->escape($this->current_session) . " and student_session.class_id=" . $this->db->escape($class_id) . " and student_session.section_id=" . $this->db->escape($section_id) . " AND student_attendences.subject_id=" . $this->db->escape($subject) . "  ) as student_sessions LEFT JOIN attendence_type ON attendence_type.id=student_sessions.attendence_type_id where student_sessions.student_id=students.id and students.guardian_phone is  NOT NULL";

  
        $query = $this->db->query($sql);
        return $query->result_array(); 
    }

    public function getStudentbySession( $id ) {
        $this->db->select('students.*,class,section')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id'); 
        $this->db->join('classes', 'student_session.class_id = classes.id'); 
        $this->db->join('sections', 'student_session.section_id = sections.id'); 
        $this->db->where('student_session.student_id', $id ); 
        $this->db->where('student_session.session_id', $this->current_session ); 
 

        $query = $this->db->get(); 
        return $query->row();  
    }
    

    public function getStudentusingSessionid( $id ) {
        $this->db->select('students.*,class,section')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id'); 
        $this->db->join('classes', 'student_session.class_id = classes.id'); 
        $this->db->join('sections', 'student_session.section_id = sections.id'); 
        $this->db->where('student_session.id', $id ); 
        $this->db->where('student_session.session_id', $this->current_session ); 
 

        $query = $this->db->get(); 
        return $query->row();  
    }
	
	 public function getrankingclasssectionperSubject(  $class,  $section=null,  $quarter ,  $semester=NULL, $subject_id=NULL ){
		$this->load->model('gradingsetting_model');	
		$gradingsettings = $this->gradingsetting_model->get();
		
		if( $semester ){
			if( $semester == 1 ){
				$get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array();	
			} else {
				$get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();	
			}
			$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
			$sql_quarter = $this->sql_array_to_string( $get_quarter );
			$total_quarter = count($get_quarter);
			
		}
	
		if( $semester ){
			if( $quarter == $total_quarter ){
				$get_query = "SELECT  AVG(get_final_grade)  as final_grade,  AE.`student_id`, AE.`name` as subject_name,  AE.`quarter`,AE.`student_id` as id"; 
				$get_query .="	FROM ( SELECT SUM(ROUND(get_marks,0))/".$total_quarter." AS get_final_grade, `exam_results`.`student_id`, `subjects`.`name`, `quarter`"; 
				$get_query .="	FROM `exam_results`"; 
				$get_query .="	JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`"; 
				$get_query .="	JOIN `students` ON `students`.`id` = `exam_results`.`student_id`"; 
				$get_query .="	JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`"; 
				if( $section != null ){
					$get_query .="	JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`"; 
				}
				$get_query .="	JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`"; 
				$get_query .="	JOIN `strand` ON `strand`.`id` = `student_session`.`strand_id`"; 
				$get_query .="	JOIN `specialized_subjects` ON `specialized_subjects`.`strand_id` = `strand`.`id`"; 
				$get_query .="	WHERE `student_session`.`class_id` = '".$class."'"; 
				if( $section != null ){
					$get_query .="	AND `student_session`.`section_id` = '".$section."'"; 
				}
				$get_query .="	AND `exam_results`.`session_id` = '".$this->current_session."'"; 
				$get_query .="	AND `subjects`.`kind` <> 'child'"; 
				$get_query .="	AND `exam_results`.`subject_id` NOT IN ( SELECT parent_id FROM subjects WHERE `exam_results`.`subject_id` =  `subjects`.`parent_id` AND `subjects`.`kind` = 'child' )"; 
				$get_query .="	AND `specialized_subjects`.`semester` = '".$semester."'"; 
				$get_query .="	AND `specialized_subjects`.`subject_id` <=> `exam_results`.`subject_id`"; 
				if( $sql_quarter ){
					$get_query .="	AND `quarter` IN (".$sql_quarter.")"; 
				}
				$get_query .="	GROUP BY `exam_results`.`student_id`, `exam_results`.`subject_id`"; 
				$get_query .="	UNION  ALL"; 
				$get_query .="	SELECT SUM(get_final_grade_per_quarter ) / ".$total_quarter." AS get_final_grade, AC.`student_id`, AC.`name` , AC.`quarter`"; 
				$get_query .="	FROM ( SELECT (SUM(ROUND(get_marks,0)) / COUNT(get_marks)) AS `get_final_grade_per_quarter`, `exam_results`.`student_id`, `subjects`.`name`, `subjects`.`id`  as subject_id, `quarter`"; 
				$get_query .="	FROM `exam_results`"; 
				$get_query .="	JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`"; 
				$get_query .="	JOIN `students` ON `students`.`id` = `exam_results`.`student_id`"; 
				$get_query .="	JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`"; 
				if( $section != null ){
					$get_query .="	JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`"; 
				}
				$get_query .="	JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`"; 
				$get_query .="	JOIN `strand` ON `strand`.`id` = `student_session`.`strand_id`"; 
				$get_query .="	JOIN `specialized_subjects` ON `specialized_subjects`.`strand_id` = `strand`.`id`"; 
				$get_query .="	WHERE `student_session`.`class_id` = '".$class."'"; 
				//
				if( $subject_id != null ){
					$get_query .="	AND `exam_results`.`subject_id` = '".$subject_id."'"; 
				}
				if( $section != null ){
					$get_query .="	AND `student_session`.`section_id` = '".$section."'"; 
				}
				$get_query .="	AND `exam_results`.`session_id` = '".$this->current_session."'"; 
				$get_query .="	AND `subjects`.`kind` = 'child'"; 
				$get_query .="	AND `specialized_subjects`.`semester` = '".$semester."'"; 
				$get_query .="	AND `specialized_subjects`.`subject_id` <=> `subjects`.`parent_id`"; 
				if( $sql_quarter ){
					$get_query .="	AND `quarter` IN (".$sql_quarter.")"; 
				}
				$get_query .="	GROUP BY `exam_results`.`student_id`, `subjects`.`parent_id`, `exam_results`.`quarter`"; 
				$get_query .="	) as AC  group by `student_id`  ) as AE  group by `student_id` order by final_grade DESC"; 
				
				$result = $this->db->query( $get_query );

			} else {
				$get_query = "SELECT SUM(get_final_grade)/SUM(total) AS `final_grade`, `student_id`,`quarter`, id";
				$get_query .=" FROM ( SELECT `exam_results`.`id`, `parent_id`, `exam_results`.`student_id`, `get_marks`, `student_session`.`class_id`, `student_session`.`section_id`, `quarter`, `exam_results`.`session_id`, SUM(ROUND(get_marks,0)) AS `get_final_grade`, count( get_marks) as `total`";
				$get_query .=" FROM `exam_results`";
				$get_query .=" JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`";
				$get_query .=" JOIN `students` ON `students`.`id` = `exam_results`.`student_id`";
				$get_query .=" JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`";
				if( $section != null ){
					$get_query .=" JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`";
				}
				$get_query .=" JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`";
				$get_query .=" JOIN `strand` ON `strand`.`id` = `student_session`.`strand_id`";
				$get_query .=" JOIN `specialized_subjects` ON `specialized_subjects`.`strand_id` = `strand`.`id`";
				$get_query .=" WHERE `student_session`.`class_id` = '".$class."'";
				if( $subject_id != null ){
					$get_query .="	AND `exam_results`.`subject_id` = '".$subject_id."'"; 
				}
				if( $section != null ){
					$get_query .=" AND `student_session`.`section_id` = '".$section."'";
				}
				$get_query .=" AND `quarter` = '".$quarter."'";
				$get_query .=" AND `exam_results`.`session_id` = '".$this->current_session."'";
				$get_query .=" AND `subjects`.`kind` <> 'child'";
				$get_query .=" AND `exam_results`.`subject_id` NOT IN ( SELECT parent_id FROM subjects WHERE `exam_results`.`subject_id` =  `subjects`.`parent_id` AND `subjects`.`kind` = 'child' )";
				$get_query .=" AND `specialized_subjects`.`semester` = '".$semester."'";
				$get_query .=" AND `specialized_subjects`.`subject_id` <=> `exam_results`.`subject_id`";
				if( $sql_quarter ){
					$get_query .=" AND `quarter` IN (".$sql_quarter.")";
				}
				$get_query .=" GROUP BY `exam_results`.`student_id`";
				$get_query .=" UNION ALL SELECT `exam_results`.`id`, `parent_id`, `exam_results`.`student_id`, `get_marks`, `student_session`.`class_id`, `student_session`.`section_id`, `quarter`, `exam_results`.`session_id`, (SUM(ROUND(get_marks,0)) / COUNT(get_marks)) AS `get_final_grade`, count( DISTINCT parent_id) as `total`";
				$get_query .=" FROM `exam_results`";
				$get_query .=" JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`";
				$get_query .=" JOIN `students` ON `students`.`id` = `exam_results`.`student_id`";
				$get_query .=" JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`";
				if( $section != null ){
					$get_query .=" JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`";
				}
				$get_query .=" JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`";
				$get_query .=" JOIN `strand` ON `strand`.`id` = `student_session`.`strand_id`";
				$get_query .=" JOIN `specialized_subjects` ON `specialized_subjects`.`strand_id` = `strand`.`id`";
				$get_query .=" WHERE `student_session`.`class_id` = '".$class."'";
				if( $section != null ){
					$get_query .=" AND `student_session`.`section_id` = '".$section."'";
				}
				$get_query .=" AND `quarter` = '".$quarter."'";
				$get_query .=" AND `exam_results`.`session_id` = '".$this->current_session."'";
				$get_query .=" AND `subjects`.`kind` = 'child'";
				$get_query .="  AND `specialized_subjects`.`semester` = '".$semester."'";
				$get_query .=" AND `specialized_subjects`.`subject_id` <=> `subjects`.`parent_id`";
				if( $sql_quarter ){
					$get_query .=" AND `quarter` IN (".$sql_quarter.")";
				}
				$get_query .=" GROUP BY `exam_results`.`student_id`, `subjects`.`parent_id` ) as a group by `student_id` order by final_grade DESC ";
				
				$result = $this->db->query( $get_query );
			}
		} else {
			if( $quarter == 4 ){
				$get_query = "SELECT AVG(get_final_grade)  as final_grade,  AE.`student_id`, AE.`name` as subject_name,  AE.`quarter`,AE.`student_id` as id";
				$get_query .=" FROM ( SELECT SUM(CEIL(get_marks)) / 4 AS get_final_grade, `exam_results`.`student_id`, `subjects`.`name`, `quarter`";
				$get_query .="	FROM `exam_results`";
				$get_query .="	JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`";
				$get_query .="	JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`";
				if( $section != null ){
					$get_query .="	LEFT JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`";
				}
				$get_query .="	JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`";
				$get_query .="	WHERE `student_session`.`class_id` = '".$class."'";
				if( $subject_id != null ){
					$get_query .="	AND `exam_results`.`subject_id` = '".$subject_id."'"; 
				}
				if( $section != null ){
					$get_query .="	AND `student_session`.`section_id` = '".$section."'";
				}
				$get_query .="	AND `exam_results`.`session_id` = '".$this->current_session."'";
				$get_query .="	AND `subjects`.`kind` <> 'child'";
				$get_query .="	AND `exam_results`.`subject_id` NOT IN ( SELECT parent_id FROM subjects WHERE `exam_results`.`subject_id` =  `subjects`.`parent_id` AND `subjects`.`kind` = 'child' )";
				$get_query .="	GROUP BY `exam_results`.`student_id`, `exam_results`.`subject_id`";
				$get_query .="	UNION  ALL";
				$get_query .="	SELECT SUM(get_final_grade_per_quarter ) / 4 AS get_final_grade, AC.`student_id`, AC.`name` , AC.`quarter`";
				$get_query .="	FROM ( SELECT (SUM(CEIL(get_marks)) / COUNT(get_marks)) AS `get_final_grade_per_quarter`, `exam_results`.`student_id`, `subjects`.`name`, `subjects`.`id`  as subject_id, `quarter`";
				$get_query .="	FROM `exam_results`";
				$get_query .="	JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`";
				$get_query .="	JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`";
				if( $section != null ){
					$get_query .="	LEFT JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`";
				}
				$get_query .="	JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`";
				$get_query .="	WHERE `student_session`.`class_id` = '".$class."'";
				if( $section != null ){
					$get_query .="	AND `student_session`.`section_id` = '".$section."'";
				}
				$get_query .="	AND `exam_results`.`session_id` = '".$this->current_session."'";
				$get_query .="	AND `subjects`.`kind` = 'child'";
				$get_query .="	GROUP BY `exam_results`.`student_id`, `subjects`.`parent_id`, `exam_results`.`quarter`"; 
				$get_query .="	) as AC  group by `student_id`  ) as AE   group by `student_id`  order by final_grade DESC";
				
				$result = $this->db->query( $get_query ); 
				
			} else {
				$get_query ="SELECT SUM(get_final_grade)/SUM(total) AS `final_grade`, `student_id`,`quarter`, id ";
				$get_query .=" FROM ( SELECT `exam_results`.`id`, `parent_id`, `exam_results`.`student_id`, `get_marks`, `student_session`.`class_id`, `student_session`.`section_id`, `quarter`, `exam_results`.`session_id`, SUM(CEIL(get_marks)) AS `get_final_grade`, count( get_marks) as `total`";
				$get_query .=" FROM `exam_results`";
				$get_query .=" JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`";
				$get_query .=" JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`";
				if( $section != null ){
					$get_query .=" LEFT JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`";
				}
				$get_query .=" JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`";
				$get_query .=" WHERE `student_session`.`class_id` = '".$class."'";
				if( $subject_id != null ){
					$get_query .="	AND `exam_results`.`subject_id` = '".$subject_id."'"; 
				}
				if( $section != null ){
					$get_query .=" AND `student_session`.`section_id` = '".$section."'";
				}
				$get_query .=" AND `quarter` = '".$quarter."'";
				$get_query .=" AND `exam_results`.`session_id` = '".$this->current_session."'";
				$get_query .=" AND `subjects`.`kind` <> 'child'";
				$get_query .=" AND `exam_results`.`subject_id` NOT IN ( SELECT parent_id FROM subjects WHERE `exam_results`.`subject_id` =  `subjects`.`parent_id` AND `subjects`.`kind` = 'child' )";
				$get_query .=" GROUP BY `exam_results`.`student_id`";
				$get_query .=" UNION ALL SELECT `exam_results`.`id`, `parent_id`, `exam_results`.`student_id`, `get_marks`, `student_session`.`class_id`, `student_session`.`section_id`, `quarter`, `exam_results`.`session_id`, (SUM(CEIL(get_marks)) / COUNT(get_marks)) AS `get_final_grade`, count( DISTINCT parent_id) as `total`";
				$get_query .=" FROM `exam_results`";
				$get_query .=" JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`";
				$get_query .=" JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`";
				if( $section != null ){
					$get_query .=" LEFT JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`";
				}
				$get_query .=" JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`";
				$get_query .=" WHERE `student_session`.`class_id` = '".$class."'";
				if( $section != null ){
					$get_query .=" AND `student_session`.`section_id` = '".$section."'";
				}
				$get_query .=" AND `quarter` = '".$quarter."'";
				$get_query .=" AND `exam_results`.`session_id` = '".$this->current_session."'";
				$get_query .=" AND `subjects`.`kind` = 'child'";
				$get_query .=" GROUP BY `exam_results`.`student_id`, `subjects`.`parent_id` ) as a group by `student_id` order by final_grade DESC ";

				$result = $this->db->query($get_query);
			}
		}
			
		$result = $result->result_array(); 	

		$result_array = array();
		if(  $result  ){
			foreach ($result as $key => $value) {
				if( isset($value['student_id'])){
					$student_info = $this->getStudentbySession( $value['student_id'] ) ;
					$new_array = array(
						'admission_no' => $student_info->admission_no,
						'lrn' => $student_info->lrn,
						'firstname' => $student_info->firstname,
						'middlename' => $student_info->middlename,
						'lastname' => $student_info->lastname,
						'dob' => $student_info->dob,
						'father_name' => $student_info->father_name,
						'father_midname' => $student_info->father_midname,
						'father_lastname' => $student_info->father_lastname,
						'father_phone' => $student_info->father_phone,
						'gender' => $student_info->gender,
						'section' => $student_info->section,
						'class' => $student_info->class,

					); 
					$result_array[] = array_merge($value, $new_array);
				}
			}
		}
		$result_array = array_merge( $result_array, array( 'quarter' => $quarter, 'semester' => $semester ));
		return $result_array;
    }
	
    public function getrankingclasssection(  $class,  $section=null,  $quarter ,  $semester=NULL ){
		$this->load->model('gradingsetting_model');	
		$gradingsettings = $this->gradingsetting_model->get();
		$decimal_grades = isset($gradingsettings->decimal_grades )?$gradingsettings->decimal_grades:'2';	
		
		if( $semester ){
			if( $semester == 1 ){
				$get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array();	
			} else {
				$get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();	
			}
			$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
			$sql_quarter = $this->sql_array_to_string( $get_quarter );
			$total_quarter = count($get_quarter);
			
		}
	
		if( $semester ){
			if( $quarter == $total_quarter ){
				$get_query = "SELECT  AVG(get_final_grade)  as final_grade,  AE.`student_id`, AE.`name` as subject_name,  AE.`quarter`,AE.`student_id` as id"; 
				//$get_query .="	FROM ( SELECT SUM(ROUND(get_marks,0))/".$total_quarter." AS get_final_grade, `exam_results`.`student_id`, `subjects`.`name`, `quarter`"; 
				$get_query .="	FROM ( SELECT SUM(FORMAT(get_marks, '".$decimal_grades."'))/".$total_quarter." AS get_final_grade, `exam_results`.`student_id`, `subjects`.`name`, `quarter`"; 
				$get_query .="	FROM `exam_results`"; 
				$get_query .="	JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`"; 
				$get_query .="	JOIN `students` ON `students`.`id` = `exam_results`.`student_id`"; 
				$get_query .="	JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`"; 
				if( $section != null ){
					$get_query .="	JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`"; 
				}
				$get_query .="	JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`"; 
				$get_query .="	JOIN `strand` ON `strand`.`id` = `student_session`.`strand_id`"; 
				$get_query .="	JOIN `specialized_subjects` ON `specialized_subjects`.`strand_id` = `strand`.`id`"; 
				$get_query .="	WHERE `student_session`.`class_id` = '".$class."'"; 
				if( $section != null ){
					$get_query .="	AND `student_session`.`section_id` = '".$section."'"; 
				}
                $get_query .="  AND `exam_results`.`session_id` = '".$this->current_session."'"; 
				$get_query .="	AND `student_session`.`session_id` = '".$this->current_session."'"; 
				$get_query .="	AND `subjects`.`kind` <> 'child'"; 
				$get_query .="	AND `exam_results`.`subject_id` NOT IN ( SELECT parent_id FROM subjects WHERE `exam_results`.`subject_id` =  `subjects`.`parent_id` AND `subjects`.`kind` = 'child' )"; 
				$get_query .="	AND `specialized_subjects`.`semester` = '".$semester."'"; 
				$get_query .="	AND `specialized_subjects`.`subject_id` <=> `exam_results`.`subject_id`"; 
				if( $sql_quarter ){
					$get_query .="	AND `quarter` IN (".$sql_quarter.")"; 
				}
				$get_query .="	GROUP BY `exam_results`.`student_id`, `exam_results`.`subject_id`"; 
				$get_query .="	UNION  ALL"; 
				$get_query .="	SELECT SUM(get_final_grade_per_quarter ) / ".$total_quarter." AS get_final_grade, AC.`student_id`, AC.`name` , AC.`quarter`"; 
				//$get_query .="	FROM ( SELECT (SUM(ROUND(get_marks,0)) / COUNT(get_marks)) AS `get_final_grade_per_quarter`, `exam_results`.`student_id`, `subjects`.`name`, `subjects`.`id`  as subject_id, `quarter`"; 
				$get_query .="	FROM ( SELECT (SUM(FORMAT(get_marks, '".$decimal_grades."')) / COUNT(get_marks)) AS `get_final_grade_per_quarter`, `exam_results`.`student_id`, `subjects`.`name`, `subjects`.`id`  as subject_id, `quarter`"; 
				$get_query .="	FROM `exam_results`"; 
				$get_query .="	JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`"; 
				$get_query .="	JOIN `students` ON `students`.`id` = `exam_results`.`student_id`"; 
				$get_query .="	JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`"; 
				if( $section != null ){
					$get_query .="	JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`"; 
				}
				$get_query .="	JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`"; 
				$get_query .="	JOIN `strand` ON `strand`.`id` = `student_session`.`strand_id`"; 
				$get_query .="	JOIN `specialized_subjects` ON `specialized_subjects`.`strand_id` = `strand`.`id`"; 
				$get_query .="	WHERE `student_session`.`class_id` = '".$class."'"; 
				if( $section != null ){
					$get_query .="	AND `student_session`.`section_id` = '".$section."'"; 
				}
				$get_query .="	AND `exam_results`.`session_id` = '".$this->current_session."'"; 
                $get_query .="  AND `student_session`.`session_id` = '".$this->current_session."'"; 
				$get_query .="	AND `subjects`.`kind` = 'child'"; 
				$get_query .="	AND `specialized_subjects`.`semester` = '".$semester."'"; 
				$get_query .="	AND `specialized_subjects`.`subject_id` <=> `subjects`.`parent_id`"; 
				if( $sql_quarter ){
					$get_query .="	AND `quarter` IN (".$sql_quarter.")"; 
				}
				$get_query .="	GROUP BY `exam_results`.`student_id`, `subjects`.`parent_id`, `exam_results`.`quarter`"; 
				$get_query .="	) as AC  group by `student_id`  ) as AE  group by `student_id` order by final_grade DESC"; 
				
				$result = $this->db->query( $get_query );

			} else {
				$get_query = "SELECT SUM(get_final_grade)/SUM(total) AS `final_grade`, `student_id`,`quarter`, id";
				//$get_query .=" FROM ( SELECT `exam_results`.`id`, `parent_id`, `exam_results`.`student_id`, `get_marks`, `student_session`.`class_id`, `student_session`.`section_id`, `quarter`, `exam_results`.`session_id`, SUM(ROUND(get_marks,0)) AS `get_final_grade`, count( get_marks) as `total`";
				$get_query .=" FROM ( SELECT `exam_results`.`id`, `parent_id`, `exam_results`.`student_id`, `get_marks`, `student_session`.`class_id`, `student_session`.`section_id`, `quarter`, `exam_results`.`session_id`, SUM(FORMAT(get_marks, '".$decimal_grades."')) AS `get_final_grade`, count( get_marks) as `total`";
				$get_query .=" FROM `exam_results`";
				$get_query .=" JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`";
				$get_query .=" JOIN `students` ON `students`.`id` = `exam_results`.`student_id`";
				$get_query .=" JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`";
				if( $section != null ){
					$get_query .=" JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`";
				}
				$get_query .=" JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`";
				$get_query .=" JOIN `strand` ON `strand`.`id` = `student_session`.`strand_id`";
				$get_query .=" JOIN `specialized_subjects` ON `specialized_subjects`.`strand_id` = `strand`.`id`";
				$get_query .=" WHERE `student_session`.`class_id` = '".$class."'";
				if( $section != null ){
					$get_query .=" AND `student_session`.`section_id` = '".$section."'";
				}
				$get_query .=" AND `quarter` = '".$quarter."'";
				$get_query .=" AND `exam_results`.`session_id` = '".$this->current_session."'";
				$get_query .=" AND `subjects`.`kind` <> 'child'";
				$get_query .=" AND `exam_results`.`subject_id` NOT IN ( SELECT parent_id FROM subjects WHERE `exam_results`.`subject_id` =  `subjects`.`parent_id` AND `subjects`.`kind` = 'child' )";
				$get_query .=" AND `specialized_subjects`.`semester` = '".$semester."'";
				$get_query .=" AND `specialized_subjects`.`subject_id` <=> `exam_results`.`subject_id`";
				if( $sql_quarter ){
					$get_query .=" AND `quarter` IN (".$sql_quarter.")";
				}
				$get_query .=" GROUP BY `exam_results`.`student_id`";
				//$get_query .=" UNION ALL SELECT `exam_results`.`id`, `parent_id`, `exam_results`.`student_id`, `get_marks`, `student_session`.`class_id`, `student_session`.`section_id`, `quarter`, `exam_results`.`session_id`, (SUM(ROUND(get_marks,0)) / COUNT(get_marks)) AS `get_final_grade`, count( DISTINCT parent_id) as `total`";
				$get_query .=" UNION ALL SELECT `exam_results`.`id`, `parent_id`, `exam_results`.`student_id`, `get_marks`, `student_session`.`class_id`, `student_session`.`section_id`, `quarter`, `exam_results`.`session_id`, (SUM(FORMAT(get_marks, '".$decimal_grades."')) / COUNT(get_marks)) AS `get_final_grade`, count( DISTINCT parent_id) as `total`";
				$get_query .=" FROM `exam_results`";
				$get_query .=" JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`";
				$get_query .=" JOIN `students` ON `students`.`id` = `exam_results`.`student_id`";
				$get_query .=" JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`";
				if( $section != null ){
					$get_query .=" JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`";
				}
				$get_query .=" JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`";
				$get_query .=" JOIN `strand` ON `strand`.`id` = `student_session`.`strand_id`";
				$get_query .=" JOIN `specialized_subjects` ON `specialized_subjects`.`strand_id` = `strand`.`id`";
				$get_query .=" WHERE `student_session`.`class_id` = '".$class."'";
				if( $section != null ){
					$get_query .=" AND `student_session`.`section_id` = '".$section."'";
				}
				$get_query .=" AND `quarter` = '".$quarter."'";
				$get_query .=" AND `exam_results`.`session_id` = '".$this->current_session."'";
                $get_query .="  AND `student_session`.`session_id` = '".$this->current_session."'"; 
				$get_query .=" AND `subjects`.`kind` = 'child'";
				$get_query .="  AND `specialized_subjects`.`semester` = '".$semester."'";
				$get_query .=" AND `specialized_subjects`.`subject_id` <=> `subjects`.`parent_id`";
				if( $sql_quarter ){
					$get_query .=" AND `quarter` IN (".$sql_quarter.")";
				}
				$get_query .=" GROUP BY `exam_results`.`student_id`, `subjects`.`parent_id` ) as a group by `student_id` order by final_grade DESC ";
				
				$result = $this->db->query( $get_query );
			}
		} else {
			if( $quarter == 4 ){
				$get_query = "SELECT AVG(get_final_grade)  as final_grade,  AE.`student_id`, AE.`name` as subject_name,  AE.`quarter`,AE.`student_id` as id";
				//$get_query .=" FROM ( SELECT SUM(CEIL(get_marks)) / 4 AS get_final_grade, `exam_results`.`student_id`, `subjects`.`name`, `quarter`";
				$get_query .=" FROM ( SELECT SUM(FORMAT(get_marks, '".$decimal_grades."')) / 4 AS get_final_grade, `exam_results`.`student_id`, `subjects`.`name`, `quarter`";
				$get_query .="	FROM `exam_results`";
				$get_query .="	JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`";
				$get_query .="	JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`";
				if( $section != null ){
					$get_query .="	LEFT JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`";
				}
				$get_query .="	JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`";
				$get_query .="	WHERE `student_session`.`class_id` = '".$class."'";
				if( $section != null ){
					$get_query .="	AND `student_session`.`section_id` = '".$section."'";
				}
				$get_query .="	AND `exam_results`.`session_id` = '".$this->current_session."'";
                $get_query .="  AND `student_session`.`session_id` = '".$this->current_session."'"; 
				$get_query .="	AND `subjects`.`kind` <> 'child'";
				$get_query .="	AND `exam_results`.`subject_id` NOT IN ( SELECT parent_id FROM subjects WHERE `exam_results`.`subject_id` =  `subjects`.`parent_id` AND `subjects`.`kind` = 'child' )";
				$get_query .="	GROUP BY `exam_results`.`student_id`, `exam_results`.`subject_id`";
				$get_query .="	UNION  ALL";
				$get_query .="	SELECT SUM(get_final_grade_per_quarter ) / 4 AS get_final_grade, AC.`student_id`, AC.`name` , AC.`quarter`";
				//$get_query .="	FROM ( SELECT (SUM(CEIL(get_marks)) / COUNT(get_marks)) AS `get_final_grade_per_quarter`, `exam_results`.`student_id`, `subjects`.`name`, `subjects`.`id`  as subject_id, `quarter`";
				$get_query .="	FROM ( SELECT (SUM(FORMAT(get_marks, '".$decimal_grades."')) / COUNT(get_marks)) AS `get_final_grade_per_quarter`, `exam_results`.`student_id`, `subjects`.`name`, `subjects`.`id`  as subject_id, `quarter`";
                // $get_query .="  FROM ( SELECT (SUM(CEIL(get_marks)) / COUNT(get_marks)) AS `get_final_grade_per_quarter`, `exam_results`.`student_id`, `subjects`.`name`, `subjects`.`id`  as subject_id, `quarter`";
				$get_query .="	FROM `exam_results`";
				$get_query .="	JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`";
				$get_query .="	JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`";
				if( $section != null ){
					$get_query .="	LEFT JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`";
				}
				$get_query .="	JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`";
				$get_query .="	WHERE `student_session`.`class_id` = '".$class."'";
				if( $section != null ){
					$get_query .="	AND `student_session`.`section_id` = '".$section."'";
				}
				$get_query .="	AND `exam_results`.`session_id` = '".$this->current_session."'";
                $get_query .=" AND `student_session`.`session_id` = '".$this->current_session."'";
				$get_query .="	AND `subjects`.`kind` = 'child'";
				$get_query .="	GROUP BY `exam_results`.`student_id`, `subjects`.`parent_id`, `exam_results`.`quarter`"; 
				$get_query .="	) as AC  group by `student_id`  ) as AE   group by `student_id`  order by final_grade DESC";
				
				$result = $this->db->query( $get_query ); 
				
			} else {
				//$get_query ="SELECT get_final_grade AS `final_grade`, `student_id`,`quarter`, id, a.`session_id`, a.`name` ";
				$get_query ="SELECT SUM(get_final_grade)/SUM(total) AS `final_grade`, `student_id`,`quarter`, id, a.`session_id`, a.`name` ";
				//$get_query .=" FROM ( SELECT `exam_results`.`id`, `parent_id`, `exam_results`.`student_id`, `get_marks`, `student_session`.`class_id`, `student_session`.`section_id`, `quarter`, `exam_results`.`session_id`, SUM(CEIL(get_marks)) AS `get_final_grade`, count( get_marks) as `total` ";
				$get_query .=" FROM ( SELECT `exam_results`.`id`, `parent_id`, `exam_results`.`student_id`, `get_marks`, `student_session`.`class_id`, `student_session`.`section_id`, `quarter`, `exam_results`.`session_id`, SUM(FORMAT(get_marks, '".$decimal_grades."')) AS `get_final_grade`, count( get_marks) as `total`, `subjects`.`name` ";
				$get_query .=" FROM `exam_results`";
				$get_query .=" JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`";
				$get_query .=" JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`";
				if( $section != null ){
					$get_query .=" LEFT JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`";
				}
				$get_query .=" JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`";
				$get_query .=" WHERE `student_session`.`class_id` = '".$class."'";
				if( $section != null ){
					$get_query .=" AND `student_session`.`section_id` = '".$section."'";
				}
				$get_query .=" AND `quarter` = '".$quarter."'";
				$get_query .=" AND `exam_results`.`session_id` = '".$this->current_session."'";
				$get_query .="	AND `student_session`.`session_id` = '".$this->current_session."'"; 
				$get_query .=" AND `subjects`.`kind` <> 'child'";
				$get_query .=" AND `exam_results`.`subject_id` NOT IN ( SELECT parent_id FROM subjects WHERE `exam_results`.`subject_id` =  `subjects`.`parent_id` AND `subjects`.`kind` = 'child' AND `exam_results`.`session_id` = '".$this->current_session."')";
				//$get_query .=" GROUP BY `exam_results`.`student_id`, `exam_results`.`subject_id`";
				$get_query .=" GROUP BY `exam_results`.`student_id`";
				//$get_query .=" UNION ALL SELECT `exam_results`.`id`, `parent_id`, `exam_results`.`student_id`, `get_marks`, `student_session`.`class_id`, `student_session`.`section_id`, `quarter`, `exam_results`.`session_id`, (SUM(CEIL(get_marks)) / COUNT(get_marks)) AS `get_final_grade`, count( DISTINCT parent_id) as `total` ";
				$get_query .=" UNION ALL SELECT `exam_results`.`id`, `parent_id`, `exam_results`.`student_id`, `get_marks`, `student_session`.`class_id`, `student_session`.`section_id`, `quarter`, `exam_results`.`session_id`, (SUM(FORMAT(get_marks, '".$decimal_grades."')) / COUNT(get_marks)) AS `get_final_grade`, count( DISTINCT parent_id) as `total`, `subjects`.`name` ";
				$get_query .=" FROM `exam_results`";
				$get_query .=" JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`";
				$get_query .=" JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`";
				if( $section != null ){
					$get_query .=" LEFT JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`";
				}
				$get_query .=" JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`";
				$get_query .=" WHERE `student_session`.`class_id` = '".$class."'";
				if( $section != null ){
					$get_query .=" AND `student_session`.`section_id` = '".$section."'";
				}
				$get_query .=" AND `quarter` = '".$quarter."'";
                $get_query .=" AND `exam_results`.`session_id` = '".$this->current_session."'";
				$get_query .=" AND `student_session`.`session_id` = '".$this->current_session."'";
				$get_query .=" AND `subjects`.`kind` = 'child'";
				$get_query .=" GROUP BY `exam_results`.`student_id`, `subjects`.`parent_id` ) as a group by `student_id` order by final_grade DESC ";
				//$get_query .=" GROUP BY `subjects`.`parent_id` ) as a order by final_grade DESC ";

				$result = $this->db->query($get_query);
			}
		}
	
		$result = $result->result_array(); 	
		$result_array = array();
		if(  $result  ){
			foreach ($result as $key => $value) {
				if( isset($value['student_id'])){
					$student_info = $this->getStudentbySession( $value['student_id'] ) ;
					$new_array = array(
						'admission_no' => $student_info->admission_no,
						'lrn' => $student_info->lrn,
						'firstname' => $student_info->firstname,
						'middlename' => $student_info->middlename,
						'lastname' => $student_info->lastname,
						'dob' => $student_info->dob,
						'father_name' => $student_info->father_name,
						'father_midname' => $student_info->father_midname,
						'father_lastname' => $student_info->father_lastname,
						'father_phone' => $student_info->father_phone,
						'gender' => $student_info->gender,
						'section' => $student_info->section,
						'class' => $student_info->class,

					); 
					$result_array[] = array_merge($value, $new_array);
				}
			}
		}
		$result_array = array_merge( $result_array, array( 'quarter' => $quarter, 'semester' => $semester ));
		return $result_array;
    }
	
    public function getrankingclasssection2(  $class,  $section,  $quarter ,  $semester=NULL ){
       /* SELECT
            `import_grades_details`.*, (SUM(grades) / COUNT(grades)) AS `final_grade`
        FROM
            `import_grades_details` 
        WHERE
            `import_grades_details`.`approved` = '1'
        AND `import_grades_details`.`class_id` = '4'
        AND `import_grades_details`.`section_id` = '5'
        AND `import_grades_details`.`quarter` = '1'
        GROUP BY
            `import_grades_details`.`student_id`
        ORDER BY
            `final_grade` DESC
        LIMIT 10*/
		
		/* $result = $this->db->query(" SELECT SUM(get_final_grade) / COUNT(get_final_grade)  AS `final_grade` FROM ( SELECT SUM(get_marks) AS `get_final_grade`,`exam_results`.`student_id`, `get_marks`,`student_session`.`class_id`,`section_id`,`quarter`,`exam_results`.`session_id` FROM exam_results JOIN subjects ON exam_results.subject_id = subjects.id  JOIN student_session ON student_session.student_id = exam_results.student_id JOIN classes ON classes.id = student_session.class_id JOIN sections ON sections.id = student_session.section_id WHERE kind='parent' AND quarter = '.$quarter.' AND student_session.class_id = '.$class.'  AND student_session.section_id = '.$section.' AND exam_results.session_id = '.$this->current_session.' GROUP BY `exam_results`.`student_id`
									UNION ALL SELECT SUM(get_marks) / COUNT(get_marks) AS `get_final_grade`,`exam_results`.`student_id`, `get_marks`,`student_session`.`class_id`,`section_id`,`quarter`,`exam_results`.`session_id` FROM exam_results JOIN subjects ON exam_results.subject_id = subjects.id  JOIN student_session ON student_session.student_id = exam_results.student_id JOIN classes ON classes.id = student_session.class_id JOIN sections ON sections.id = student_session.section_id WHERE kind='child' AND quarter = '.$quarter.' AND student_session.class_id = '.$class.'  AND student_session.section_id = '.$section.' AND exam_results.session_id = '.$this->current_session.'  GROUP BY `subjects`.`parent_id`  ) as a  ");
		$query1 = $result->result_array();
		echo "<pre>";
		print_r( $query1 );
		echo "</pre>";
		die(); */

			
			
		if( $quarter == 4 ){
			if( $semester ){
				$result = $this->db->query("SELECT AVG(get_final_grade)   as final_grade,  AE.`student_id`, AE.`name` as subject_name,  AE.`quarter`,AE.`student_id` as id
				FROM ( SELECT SUM(get_marks)/4 AS get_final_grade, `exam_results`.`student_id`, `subjects`.`name`, `quarter`
					FROM `exam_results`
					JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`
					JOIN `students` ON `students`.`id` = `exam_results`.`student_id`
					JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`
					JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`
					JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`
					JOIN `strand` ON `strand`.`id` = `student_session`.`strand_id`
					JOIN `specialized_subjects` ON `specialized_subjects`.`strand_id` = `strand`.`id`
					WHERE `student_session`.`class_id` = '".$class."'
					AND `student_session`.`section_id` = '".$section."'
					AND `exam_results`.`session_id` = '".$this->current_session."'
					AND `subjects`.`kind` <> 'child'
					AND `exam_results`.`subject_id` NOT IN ( SELECT parent_id FROM subjects WHERE `exam_results`.`subject_id` =  `subjects`.`parent_id` AND `subjects`.`kind` = 'child' )
					AND `specialized_subjects`.`semester` = '".$semester."'
					AND `specialized_subjects`.`subject_id` <=> `exam_results`.`subject_id`
					GROUP BY `exam_results`.`student_id`, `exam_results`.`subject_id`
					UNION  ALL
					SELECT SUM(get_final_grade_per_quarter ) / 4 AS get_final_grade, AC.`student_id`, AC.`name` , AC.`quarter`
					FROM ( SELECT (SUM(get_marks) / COUNT(get_marks)) AS `get_final_grade_per_quarter`, `exam_results`.`student_id`, `subjects`.`name`, `subjects`.`id`  as subject_id, `quarter`
					FROM `exam_results`
					JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`
					JOIN `students` ON `students`.`id` = `exam_results`.`student_id`
					JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`
					JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`
					JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`
					JOIN `strand` ON `strand`.`id` = `student_session`.`strand_id`
					JOIN `specialized_subjects` ON `specialized_subjects`.`strand_id` = `strand`.`id`
					WHERE `student_session`.`class_id` = '".$class."'
					AND `student_session`.`section_id` = '".$section."'
					AND `exam_results`.`session_id` = '".$this->current_session."'
					AND `subjects`.`kind` = 'child'
					AND `specialized_subjects`.`semester` = '".$semester."'
					AND `specialized_subjects`.`subject_id` <=> `subjects`.`parent_id`
					GROUP BY `exam_results`.`student_id`, `subjects`.`parent_id`, `exam_results`.`quarter` 
					) as AC  group by `student_id`  ) as AE  group by `student_id`   order by final_grade DESC 
				"); 
			} else {
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
			}
			
			$result = $result->result_array(); 	
		} else {
			if( $semester ){
				$result = $this->db->query("SELECT SUM(get_final_grade)/SUM(total) AS `final_grade`, `student_id`,`quarter`, id 
				FROM ( SELECT `exam_results`.`id`, `parent_id`, `exam_results`.`student_id`, `get_marks`, `student_session`.`class_id`, `student_session`.`section_id`, `quarter`, `exam_results`.`session_id`, SUM(get_marks) AS `get_final_grade`, count( get_marks) as `total`
				FROM `exam_results`
				JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`
				JOIN `students` ON `students`.`id` = `exam_results`.`student_id`
				JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`
				JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`
				JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`
				JOIN `strand` ON `strand`.`id` = `student_session`.`strand_id`
				JOIN `specialized_subjects` ON `specialized_subjects`.`strand_id` = `strand`.`id`
				WHERE `student_session`.`class_id` = '".$class."'
				AND `student_session`.`section_id` = '".$section."'
				AND `quarter` = '".$quarter."'
				AND `exam_results`.`session_id` = '".$this->current_session."'
				AND `subjects`.`kind` <> 'child'
				AND `exam_results`.`subject_id` NOT IN ( SELECT parent_id FROM subjects WHERE `exam_results`.`subject_id` =  `subjects`.`parent_id` AND `subjects`.`kind` = 'child' )
				AND `specialized_subjects`.`semester` = '".$semester."'
				AND `specialized_subjects`.`subject_id` <=> `exam_results`.`subject_id`
				GROUP BY `exam_results`.`student_id`
				UNION ALL SELECT `exam_results`.`id`, `parent_id`, `exam_results`.`student_id`, `get_marks`, `student_session`.`class_id`, `student_session`.`section_id`, `quarter`, `exam_results`.`session_id`, (SUM(get_marks) / COUNT(get_marks)) AS `get_final_grade`, count( DISTINCT parent_id) as `total`
				FROM `exam_results`
				JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`
				JOIN `students` ON `students`.`id` = `exam_results`.`student_id`
				JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`
				JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`
				JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`
				JOIN `strand` ON `strand`.`id` = `student_session`.`strand_id`
				JOIN `specialized_subjects` ON `specialized_subjects`.`strand_id` = `strand`.`id`
				WHERE `student_session`.`class_id` = '".$class."'
				AND `student_session`.`section_id` = '".$section."'
				AND `quarter` = '".$quarter."'
				AND `exam_results`.`session_id` = '".$this->current_session."'
				AND `subjects`.`kind` = 'child'
				AND `specialized_subjects`.`semester` = '".$semester."'
				AND `specialized_subjects`.`subject_id` <=> `subjects`.`parent_id`
				GROUP BY `exam_results`.`student_id`, `subjects`.`parent_id` ) as a group by `student_id` order by final_grade DESC ");

			} else {
				$result = $this->db->query("SELECT SUM(get_final_grade)/SUM(total) AS `final_grade`, `student_id`,`quarter`, id 
				FROM ( SELECT `exam_results`.`id`, `parent_id`, `exam_results`.`student_id`, `get_marks`, `student_session`.`class_id`, `student_session`.`section_id`, `quarter`, `exam_results`.`session_id`, SUM(get_marks) AS `get_final_grade`, count( get_marks) as `total`
				FROM `exam_results`
				JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`
				JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`
				LEFT JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`
				JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`
				WHERE `student_session`.`class_id` = '".$class."'
				AND `student_session`.`section_id` = '".$section."'
				AND `quarter` = '".$quarter."'
				AND `exam_results`.`session_id` = '".$this->current_session."'
				AND `subjects`.`kind` <> 'child'
				AND `exam_results`.`subject_id` NOT IN ( SELECT parent_id FROM subjects WHERE `exam_results`.`subject_id` =  `subjects`.`parent_id` AND `subjects`.`kind` = 'child' )
				GROUP BY `exam_results`.`student_id`
				UNION ALL SELECT `exam_results`.`id`, `parent_id`, `exam_results`.`student_id`, `get_marks`, `student_session`.`class_id`, `student_session`.`section_id`, `quarter`, `exam_results`.`session_id`, (SUM(get_marks) / COUNT(get_marks)) AS `get_final_grade`, count( DISTINCT parent_id) as `total`
				FROM `exam_results`
				JOIN `student_session` ON `student_session`.`student_id` = `exam_results`.`student_id`
				JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`
				LEFT JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`
				JOIN `subjects` ON `exam_results`.`subject_id` = `subjects`.`id`
				WHERE `student_session`.`class_id` = '".$class."'
				AND `student_session`.`section_id` = '".$section."'
				AND `quarter` = '".$quarter."'
				AND `exam_results`.`session_id` = '".$this->current_session."'
				AND `subjects`.`kind` = 'child'
				GROUP BY `exam_results`.`student_id`, `subjects`.`parent_id` ) as a group by `student_id` order by final_grade DESC ");
			}
			$result = $result->result_array(); 	
			
			
		}
		
		$result_array = array();
		if(  $result  ){
			foreach ($result as $key => $value) {
				if( isset($value['student_id'])){
					$student_info = $this->getStudentbySession( $value['student_id'] ) ;
					$new_array = array(
						'admission_no' => $student_info->admission_no,
						'lrn' => $student_info->lrn,
						'firstname' => $student_info->firstname,
						'middlename' => $student_info->middlename,
						'lastname' => $student_info->lastname,
						'dob' => $student_info->dob,
						'father_name' => $student_info->father_name,
						'father_midname' => $student_info->father_midname,
						'father_lastname' => $student_info->father_lastname,
						'father_phone' => $student_info->father_phone,
						'gender' => $student_info->gender,

					); 
					$result_array[] = array_merge($value, $new_array);
				}
			}
		}
		$result_array = array_merge( $result_array, array( 'quarter' => $quarter, 'semester' => $semester ));
		return $result_array;
			 

    }
	
	public function getrankingclasssection1(  $class,  $section,  $quarter ,  $limit=FALSE ){
       /* SELECT
            `import_grades_details`.*, (SUM(grades) / COUNT(grades)) AS `final_grade`
        FROM
            `import_grades_details` 
        WHERE
            `import_grades_details`.`approved` = '1'
        AND `import_grades_details`.`class_id` = '4'
        AND `import_grades_details`.`section_id` = '5'
        AND `import_grades_details`.`quarter` = '1'
        GROUP BY
            `import_grades_details`.`student_id`
        ORDER BY
            `final_grade` DESC
        LIMIT 10*/

        $this->db->select('exam_results.id,exam_results.student_id,get_marks,class_id,section_id,quarter, exam_results.session_id');
        $this->db->select('(SUM(get_marks) / COUNT(get_marks)) AS `final_grade`');
        $this->db->from('exam_results');
        $this->db->where('class_id', $class );
        $this->db->where('section_id', $section );
        $this->db->where('quarter', $quarter ); 
        $this->db->where('exam_results.session_id', $this->current_session ); 
		$this->db->join('student_session', 'student_session.student_id = exam_results.student_id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
		
        $this->db->group_by('exam_results.student_id'); 
        $this->db->order_by('final_grade', 'desc'); 
        if( $limit ){
            $this->db->limit($limit); 
        }
        
        $query = $this->db->get();
        $result = $this->db->last_query(); 
	

        $result_array = array();
        if(  $result  ){
            foreach ($result as $key => $value) {
                $student_info = $this->getStudentbySession( $value['student_id'] ) ;
                $new_array = array(
                    'admission_no' => $student_info->admission_no,
                    'lrn' => $student_info->lrn,
                    'firstname' => $student_info->firstname,
                    'middlename' => $student_info->middlename,
                    'lastname' => $student_info->lastname,
                    'dob' => $student_info->dob,
                    'father_name' => $student_info->father_name,
                    'father_midname' => $student_info->father_midname,
                    'father_lastname' => $student_info->father_lastname,
                    'father_phone' => $student_info->father_phone,
                    'gender' => $student_info->gender,

                ); 
                $result_array[] = array_merge($value, $new_array);
 
            }
        }

        return $result_array;
         

    }

    public function get_student_count( ){
        $this->db->select('count(students.id) as `counted_id`'); 
        $this->db->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id'); 
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('students.status', 'active');
         
         
        $query = $this->db->get();

        $result_array = $query->result_array(); 
        $result_row = $query->row(); 
      
 
        return $result_row->counted_id;
         

    }
	
	
    public function getenrolledtodaycount( ){
        
		$date_today = date('Y-m-d');
        $this->db->select('count(students.id) as `counted_id`'); 
        $this->db->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id'); 
        //$this->db->where('student_session.session_id', $this->current_session); 
        $this->db->where('DATE(student_session.enrolled_at)', $date_today );
         
         
        $query = $this->db->get();

        $result_array = $query->result_array(); 
        $result_row = $query->row(); 
      
 
        return $result_row->counted_id;
         

    }
	
	public function getenrolledtoday($id = null) {
		$date_today = date('Y-m-d');
        $this->db->select('student_session.transport_fees,student_session.vehroute_id,student_session.id as `student_session_id`,student_session.fees_discount,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.admission_no , students.roll_no,students.admission_date,students.firstname,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion, students.cast,    students.dob ,students.current_address, students.previous_school,students.government_voucher,students.blood_type,
            students.guardian_is, students.commuter_pass, students.lunch_pass, students.rfid_number, 
            students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.guardian_relation,students.guardian_phone,students.guardian_email,students.guardian_address,students.guardian_barangay_id,students.guardian_city_id,students.guardian_province_id,students.current_barangay_id,students.current_city_id,students.current_province_id,students.permanent_barangay_id,students.esc_number ,students.government_number ,students.permanent_city_id,students.permanent_province_id,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte, students.lrn, students.middlename, students.father_midname, students.father_lastname, students.mother_midname, students.mother_lastname, students.guardian_midname, students.guardian_lastname,student_session.strand_id, status,students.load_balance,students.hostel_id,students.dormitory,student_session.enrolled_at,session,suffix')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','left');
        $this->db->join('sessions', 'sessions.id = student_session.session_id');
       // $this->db->where('student_session.session_id', $this->current_session);
		
        $this->db->where('DATE(student_session.enrolled_at)', $date_today );
        if ($id != null) {
            $this->db->where('students.id', $id);
        } else {
            $this->db->order_by('students.id', 'desc');
        }
        $query = $this->db->get();
 
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }

    }
	
	public function sql_array_to_string( $cat ){
		
		$SQL_Part ='';
		for ($i=0;$i<count($cat);$i++)
		{
			/* if( $i == count($cat) - 1 ){
				$SQL_Part.="'".$cat[$i]."'";
			} else {
				$SQL_Part.="'".$cat[$i]."'".",";
			} */
			if( $i == count($cat) - 1 ){
				$SQL_Part.=$cat[$i];
			} else {
				$SQL_Part.=$cat[$i].",";
			} 
		  
		}

		return $SQL_Part;
		
	}
	
	public function searchFullStudentText($searchterm, $include_inactive=no) {
        $this->db->select('firstname,middlename,lastname,status,admission_no,roll_no,lrn,id')->from('students');
        if( $include_inactive == 'no' ){
          $this->db->where('status', 'active');
        }
        $this->db->group_start();
        $this->db->like('students.firstname', $searchterm);
        $this->db->or_like('students.lastname', $searchterm);
        $this->db->or_like('students.roll_no', $searchterm);
        $this->db->or_like('students.admission_no', $searchterm);
        $this->db->or_like('students.lrn', $searchterm);
        $this->db->group_end();
        $this->db->order_by('students.id');
        $this->db->limit('10');
        $query = $this->db->get();
        return $query->result_array();
    }
	
	public function getLatestStudentInformation($id = null) {
        $this->db->select('student_session.transport_fees,student_session.vehroute_id,student_session.id as `student_session_id`,student_session.fees_discount,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion, students.cast,    students.dob ,students.current_address, students.previous_school,students.government_voucher,students.blood_type,
            students.guardian_is, students.commuter_pass, students.lunch_pass, students.rfid_number, 
            students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.guardian_relation,students.guardian_phone,students.guardian_email,students.guardian_address,students.guardian_barangay_id,students.guardian_city_id,students.guardian_province_id,students.current_barangay_id,students.current_city_id,students.current_province_id,students.permanent_barangay_id,students.esc_number ,students.government_number ,students.permanent_city_id,students.permanent_province_id,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte, students.lrn, students.middlename, students.father_midname, students.father_lastname, students.mother_midname, students.mother_lastname, students.guardian_midname, students.guardian_lastname,student_session.strand_id, status,students.load_balance,students.hostel_id,students.dormitory,session,students.suffix,students.guardian_address2,student_session.session_id')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','left');
        $this->db->join('sessions', 'sessions.id = student_session.session_id');
		$this->db->where('students.id', $id);
		$this->db->order_by('sessions.session', 'desc');
        $query = $this->db->get();
 
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }

    }
	
	public function getStudentBasicInformation( $id ){
		$this->db->select('students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion, students.cast,    students.dob ,students.current_address, students.previous_school,students.government_voucher,students.blood_type,
            students.guardian_is, students.commuter_pass, students.lunch_pass, students.rfid_number, 
            students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.guardian_relation,students.guardian_phone,students.guardian_email,students.guardian_address,students.guardian_barangay_id,students.guardian_city_id,students.guardian_province_id,students.current_barangay_id,students.current_city_id,students.current_province_id,students.permanent_barangay_id,students.esc_number ,students.government_number ,students.permanent_city_id,students.permanent_province_id,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte, students.lrn, students.middlename, students.father_midname, students.father_lastname, students.mother_midname, students.mother_lastname, students.guardian_midname, students.guardian_lastname, status,students.load_balance,students.hostel_id,students.dormitory,suffix,students.school_address,students.school_name')->from('students');
		$this->db->where('students.id', $id);
        $query = $this->db->get();
        
		return $query->row_array();
	}
	
	public function getmatchfirstlast($firstname, $lastname) {
        $this->db->select('firstname,middlename,lastname,status,admission_no,roll_no,lrn,id')->from('students');
        $this->db->where('status', 'active');
        $this->db->group_start();
        $this->db->like('students.firstname', $firstname);
        $this->db->or_like('students.lastname', $lastname);
        $this->db->group_end();
        $this->db->order_by('students.id');
        $this->db->limit('10');
        $query = $this->db->get();
        return $query->result_array();
    }

  /*    public function getStudentbyrfid( $rfid ) {

<<<<<<< HEAD
        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name ,students.guardian_email , students.guardian_midname , students.guardian_lastname , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active, students.esc_number ,students.government_number ,students.created_at ,students.updated_at,students.father_name,students.father_midname, students.mother_name,  students.mother_midname, students.mother_lastname, students.rfid_number, students.father_lastname,students.rte,students.gender,students.load_balance,students.hostel_id,students.dormitory,users.id as `user_tbl_id`,users.username,users.password as `user_tbl_password`,users.is_active as `user_tbl_active`')->from('students');
=======
        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no, students.lrn,students.admission_date,students.firstname, students.middlename,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name ,students.guardian_email , students.guardian_midname , students.guardian_lastname , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active, students.esc_number ,students.government_number ,students.created_at ,students.updated_at,students.father_name,students.father_midname, students.mother_name,  students.mother_midname, students.mother_lastname, students.rfid_number, students.father_lastname,students.rte,students.gender,students.load_balance,students.hostel_id,students.dormitory,users.id as `user_tbl_id`,users.username,users.password as `user_tbl_password`,users.is_active as `user_tbl_active`')->from('students');
     

>>>>>>> 8063fee6d5620c145f0e6c46ecc3e148aba06f32
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','left');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        $this->db->join('users', 'users.user_id = students.id', 'left'); 
        $this->db->where('students.rfid_number', $rfid);
        $this->db->where('users.role', 'student');

        $this->db->order_by('students.id');

        $query = $this->db->get();
        return $query->row_array();
    } */
	
	public function getStudentbyrfid( $rfid ) {

        $this->db->select('students.firstname, students.lastname,students.id,classes.class,students.image, student_session.is_deactivate')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->where('students.rfid_number', $rfid);

        $this->db->order_by('students.id');

        $query = $this->db->get();
        return $query->row_array();
    }
	
    public function getstudentload( $id ) {
        $this->db->select('*')->from('students');
        $this->db->where('students.id', $id);
        $this->db->order_by('students.id');

        $query = $this->db->get();
        $result = $query->row_array();
        
        return $result['load_balance'];
    }

   public function getstudentforid( $session_id ) {
        
        $this->db->select('students.admission_no');
        $this->db->select('student_session.class_id');
        $this->db->select('classes.class');
        $this->db->select('student_session.section_id');
        $this->db->select('sections.section');
        $this->db->select('students.firstname');
        $this->db->select('students.lastname');
        $this->db->select('students.middlename');
        $this->db->select('students.suffix');
        $this->db->select('students.LRN');
        $this->db->select('students.roll_no');
        $this->db->select('students.rte');
        $this->db->select('students.dob');
        $this->db->select('students.blood_type');
        $this->db->select('students.guardian_name');
        $this->db->select('students.guardian_midname');
        $this->db->select('students.guardian_lastname');
        $this->db->select('students.guardian_address');
        $this->db->select('students.guardian_address2');
        $this->db->select('students.current_address');
        $this->db->select('students.guardian_phone');
        $this->db->select('strand.code as strand_code'); 
        $this->db->from('student_session'); 
        $this->db->join('students', 'students.id = student_session.student_id', 'left'); 
        $this->db->join('classes', 'student_session.class_id = classes.id', 'left'); 
        $this->db->join('sections', 'student_session.section_id = sections.id', 'left'); 
        $this->db->join('strand', 'student_session.strand_id = strand.id', 'left'); 
        $this->db->where('student_session.session_id', $session_id);  
        $this->db->where('classes.sequence !=', '0' );   
        $this->db->where('students.status', 'active' );   
        $this->db->where('( students.rfid_number IS NULL OR students.rfid_number = "" ) ', NULL, FALSE  ); 
        $this->db->order_by("`classes`.`sequence`", "ASC");
        $this->db->order_by("`sections`.`section`", "ASC"); 
        $query = $this->db->get(); 
        $result_array = $query->result_array(); // array of result
         
         // printx($this->db->last_query());exit();
        return $result_array;
    }

    public function getmissingstudentinfo( $session_id ) {

           $this->db->select('students.id');
        $this->db->select('students.admission_no');
        $this->db->select('student_session.class_id');
        $this->db->select('classes.class');
        $this->db->select('student_session.section_id');
        $this->db->select('sections.section');
        $this->db->select('students.firstname');
        $this->db->select('students.lastname');
        $this->db->select('students.middlename'); 
        $this->db->select('students.LRN');
        $this->db->select('students.roll_no');
        $this->db->select('students.dob');
        $this->db->select('students.blood_type');
        $this->db->select('students.guardian_name');
        $this->db->select('students.guardian_midname');
        $this->db->select('students.guardian_lastname');
        $this->db->select('students.guardian_address');
        $this->db->select('students.guardian_address2');
        $this->db->select('students.current_address');
        $this->db->select('students.guardian_phone');
        $this->db->from('student_session'); 
        $this->db->join('students', 'students.id = student_session.student_id', 'left'); 
        $this->db->join('classes', 'student_session.class_id = classes.id', 'left'); 
        $this->db->join('sections', 'student_session.section_id = sections.id', 'left'); 
        $this->db->where('student_session.session_id', $session_id);  


        /*$this->db->or_where("students.admission_no", NULL, FALSE);
        $this->db->or_where("student_session.class_id", NULL, FALSE);
        $this->db->or_where("classes.class", NULL, FALSE);
        $this->db->or_where("student_session.section_id", NULL, FALSE);
        $this->db->or_where("sections.section", NULL, FALSE);
        $this->db->or_where("students.firstname", NULL, FALSE);
        $this->db->or_where("students.lastname", NULL, FALSE);
        $this->db->or_where("students.middlename", NULL, FALSE);
        $this->db->or_where("students.LRN", NULL, FALSE);
        $this->db->or_where("students.dob", NULL, FALSE);
        $this->db->or_where("students.blood_type", NULL, FALSE);
        $this->db->or_where("students.guardian_name", NULL, FALSE);
        $this->db->or_where("students.guardian_midname", NULL, FALSE);
        $this->db->or_where("students.guardian_lastname", NULL, FALSE);
        $this->db->or_where("students.guardian_address", NULL, FALSE);
        $this->db->or_where("students.current_address", NULL, FALSE);
        $this->db->or_where("students.guardian_phone", NULL, FALSE);*/

        // $this->db->where("(students.admission_no IS NULL OR student_session.class_id IS NULL OR classes.class IS NULL OR student_session.section_id IS NULL OR sections.section IS NULL OR students.firstname IS NULL OR students.lastname IS NULL OR students.middlename IS NULL OR students.LRN IS NULL OR students.dob IS NULL OR students.blood_type IS NULL OR students.guardian_name IS NULL OR students.guardian_midname IS NULL OR students.guardian_lastname IS NULL OR students.guardian_address IS NULL OR students.current_address IS NULL OR students.guardian_phone IS NULL )", NULL, FALSE); 
        // $this->db->where("( students.admission_no = ' ' OR student_session.class_id = ' ' OR classes.class = ' ' OR student_session.section_id = ' ' OR sections.section = ' ' OR students.firstname = ' ' OR students.lastname = ' ' OR students.middlename = ' ' OR students.LRN = ' '  OR students.dob = ' ' OR students.guardian_name = ' ' OR students.guardian_lastname = ' ' OR students.guardian_address = ' ' OR students.current_address = ' ' OR students.guardian_phone = ' '  )", NULL, FALSE); 

         $this->db->where("( students.admission_no = ' ' OR students.admission_no IS NULL OR students.admission_no = '-'
                            OR student_session.class_id = ' ' OR student_session.class_id IS NULL OR student_session.class_id = '-' 
                            OR classes.class = ' ' OR classes.class IS NULL OR classes.class = '-' 
                            OR student_session.section_id = ' ' OR student_session.section_id IS NULL OR student_session.section_id = '24' OR student_session.section_id = '-'
                            OR sections.section = ' ' OR sections.section = 'None' OR sections.section = 'NONE' OR sections.section IS NULL OR sections.section = '-'  
                            OR students.firstname = ' ' OR students.firstname IS NULL OR students.firstname = '-'
                            OR students.lastname = ' ' OR students.lastname IS NULL OR students.lastname = '-'  
                            OR students.dob = ' ' OR students.dob IS NULL OR students.dob = '-' 
                            OR students.guardian_name = ' 'OR students.guardian_name IS NULL OR students.guardian_name = '-'
                            OR students.guardian_lastname = ' ' OR students.guardian_lastname IS NULL OR students.guardian_lastname = '-'   
                            OR students.guardian_address = ' ' OR students.guardian_address IS NULL OR students.guardian_address = '-'  
                            OR students.guardian_address2 = ' ' OR students.guardian_address2 IS NULL OR students.guardian_address2 = '-'  
                            OR students.guardian_phone = ' ' OR students.guardian_phone = '' OR students.guardian_phone = '09' OR students.guardian_phone IS NULL  )", NULL, FALSE);  


         // $this->db->where("(  students.guardian_address IS NULL  AND  guardian_address2 IS NOT NULL ) OR  (students.guardian_address IS NOT NULL  AND  guardian_address2 IS NULL ) ", NULL, FALSE); 
        
        $this->db->where('classes.sequence !=', '0' ); 
        $this->db->order_by("`classes`.`sequence`", "ASC");
        $this->db->order_by("`sections`.`section`", "ASC"); 
        $query = $this->db->get(); 
        $result_array = $query->result_array(); // array of result
        //printx( $result_array );
        $results=array(); 

        if( $result_array ){
            foreach ($result_array as $key => $value) {
                $id = $value['id'];
                $admission_no = $value['admission_no'];
                $class_id = $value['class_id'];
                $class = $value['class'];
                $section_id = $value['section_id'];
                $section = $value['section'];
                $firstname = $value['firstname'];
                $lastname = $value['lastname'];
                $middlename = $value['middlename'];
                $lrn = $value['LRN'];
                $roll_no = $value['roll_no'];
                $dob = $value['dob'];
                $blood_type = $value['blood_type'];
                $guardian_name = $value['guardian_name'];
                $guardian_midname = $value['guardian_midname'];
                $guardian_lastname = $value['guardian_lastname'];
                $guardian_address = $value['guardian_address'];
                $guardian_address2 = $value['guardian_address2'];
                $current_address = $value['current_address'];
                $guardian_phone = $value['guardian_phone'];
                
                if( $id && $class_id && $class && $section_id && $section && $firstname && $lastname && $lrn && $dob && $guardian_name &&  $guardian_lastname &&  $guardian_phone &&  $guardian_phone != '09' ){
                    if( $section == "none" || $section == "None" ){
                            $results[] = $value;
                    } else {
                        /*if(  empty( $guardian_address ) &&  $guardian_address2 ||  $guardian_address  &&  empty( $guardian_address2 )){


                        } else {
                            $results[] = $value;
                        }*/
                         if(  $guardian_address == "" &&  $guardian_address2 == "" && $current_address == "" ||   is_null( $current_address )  && is_null( $guardian_address ) &&  is_null( $guardian_address2 )  ){
                            $results[] = $value;

                        }  
                    }
                   
                } else {
                    $results[] = $value;
                }
              
            }
        }
 
        
        return $results;
    }


    public function getreadystudentinfo( $session_id, $class_id=NULL, $section_id=NULL) {

        $this->db->select('students.id');
        $this->db->select('students.admission_no');
        $this->db->select('student_session.class_id');
        $this->db->select('classes.class');
        $this->db->select('student_session.section_id');
        $this->db->select('sections.section');
        $this->db->select('students.firstname');
        $this->db->select('students.lastname');
        $this->db->select('students.middlename'); 
        $this->db->select('students.LRN');
        $this->db->select('students.roll_no');
        $this->db->select('students.rte');
        $this->db->select('students.dob');
        $this->db->select('students.blood_type');
        $this->db->select('students.guardian_name');
        $this->db->select('students.guardian_midname');
        $this->db->select('students.guardian_lastname');
        $this->db->select('students.guardian_address');
        $this->db->select('students.current_address');
        $this->db->select('students.guardian_phone');
        $this->db->select('students.image');
        $this->db->select('students.lrn');
        $this->db->select('students.rte');
        $this->db->from('student_session'); 
        $this->db->join('students', 'students.id = student_session.student_id', 'left'); 
        $this->db->join('classes', 'student_session.class_id = classes.id', 'left'); 
        $this->db->join('sections', 'student_session.section_id = sections.id', 'left'); 
        $this->db->where('student_session.session_id', $session_id);  
        /*$this->db->or_where("students.admission_no", NULL, FALSE);
        $this->db->or_where("student_session.class_id", NULL, FALSE);
        $this->db->or_where("classes.class", NULL, FALSE);
        $this->db->or_where("student_session.section_id", NULL, FALSE);
        $this->db->or_where("sections.section", NULL, FALSE);
        $this->db->or_where("students.firstname", NULL, FALSE);
        $this->db->or_where("students.lastname", NULL, FALSE);
        $this->db->or_where("students.middlename", NULL, FALSE);
        $this->db->or_where("students.LRN", NULL, FALSE);
        $this->db->or_where("students.dob", NULL, FALSE);
        $this->db->or_where("students.blood_type", NULL, FALSE);
        $this->db->or_where("students.guardian_name", NULL, FALSE);
        $this->db->or_where("students.guardian_midname", NULL, FALSE);
        $this->db->or_where("students.guardian_lastname", NULL, FALSE);
        $this->db->or_where("students.guardian_address", NULL, FALSE);
        $this->db->or_where("students.current_address", NULL, FALSE);
        $this->db->or_where("students.guardian_phone", NULL, FALSE);*/

        // $this->db->where("(students.admission_no IS NULL OR student_session.class_id IS NULL OR classes.class IS NULL OR student_session.section_id IS NULL OR sections.section IS NULL OR students.firstname IS NULL OR students.lastname IS NULL OR students.middlename IS NULL OR students.LRN IS NULL OR students.dob IS NULL OR students.blood_type IS NULL OR students.guardian_name IS NULL OR students.guardian_midname IS NULL OR students.guardian_lastname IS NULL OR students.guardian_address IS NULL OR students.current_address IS NULL OR students.guardian_phone IS NULL )", NULL, FALSE); 
        // $this->db->where("( students.admission_no = ' ' OR student_session.class_id = ' ' OR classes.class = ' ' OR student_session.section_id = ' ' OR sections.section = ' ' OR sections.section = 'None' OR students.firstname = ' ' OR students.lastname = ' ' OR students.middlename = ' ' OR students.LRN = ' ' OR students.dob = ' ' OR students.guardian_name = ' ' OR students.guardian_lastname = ' ' OR students.guardian_address = ' ' OR students.current_address = ' ' OR students.guardian_phone = ' '  )", NULL, FALSE); 
        $this->db->where("( students.admission_no <> ' ' AND students.admission_no IS NOT NULL AND students.admission_no <> '-'
                            AND student_session.class_id <> ' ' AND student_session.class_id IS NOT NULL AND student_session.class_id <> '-' 
                            AND classes.class <> ' ' AND classes.class IS NOT NULL AND classes.class <> '-' 
                            AND student_session.section_id <> ' ' AND student_session.section_id IS NOT NULL AND student_session.section_id <> '24' AND student_session.section_id <> '-'
                            AND sections.section <> ' ' AND sections.section <> 'None' AND sections.section <> 'NONE' AND sections.section IS NOT NULL AND sections.section <> '-'  
                            AND students.firstname <> ' 'AND students.firstname IS NOT NULL AND students.firstname <> '-'
                            AND students.lastname <> ' 'AND students.lastname IS NOT NULL AND students.lastname <> '-'  
                            AND students.middlename <> ' ' AND students.middlename IS NOT NULL AND students.middlename <> '-' 
                            AND students.LRN <> ' 'AND students.LRN IS NOT NULL AND students.LRN <> '-'
                            AND students.dob <> ' ' AND students.dob IS NOT NULL AND students.dob <> '-' 
                            AND students.guardian_name <> ' 'AND students.guardian_name IS NOT NULL AND students.guardian_name <> '-'
                            AND students.guardian_lastname <> ' ' AND students.guardian_lastname IS NOT NULL AND students.guardian_lastname <> '-' 
                            AND students.guardian_address <> ' ' AND students.guardian_address IS NOT NULL AND students.guardian_address <> '-'  
                            AND students.current_address <> ' ' AND students.current_address IS NOT NULL AND students.current_address <> '-' 
                            AND students.guardian_phone <> ' ' AND students.guardian_phone <> '' AND students.guardian_phone <> '09' AND students.guardian_phone IS NOT NULL  )", NULL, FALSE); 
        
        $this->db->where('classes.sequence !=', '0' ); 
        // if( $class_id ){ 
        //     $this->db->where('student_session.class_id', $class_id );  
        // }
        // if( $section_id ){  
        //     $this->db->where('student_session.section_id', $section_id ); 
        // }
        $this->db->order_by("`classes`.`sequence`", "ASC");
        $this->db->order_by("`sections`.`section`", "ASC"); 
        $query = $this->db->get(); 
        $result_array = $query->result_array(); // array of result
        
        return $result_array;
    }


    public function getreadystudentinfoforid( $session_id, $class_id=NULL, $section_id=NULL) {

        $this->db->select('students.id');
        $this->db->select('students.admission_no');
        $this->db->select('student_session.class_id');
        $this->db->select('classes.class');
        $this->db->select('student_session.section_id');
        $this->db->select('sections.section');
        $this->db->select('students.firstname');
        $this->db->select('students.lastname');
        $this->db->select('students.middlename'); 
        $this->db->select('students.suffix'); 
        $this->db->select('students.LRN');
        $this->db->select('students.roll_no');
        $this->db->select('students.rte');
        $this->db->select('students.dob');
        $this->db->select('students.blood_type');
        $this->db->select('students.guardian_name');
        $this->db->select('students.guardian_midname');
        $this->db->select('students.guardian_lastname');
        $this->db->select('students.guardian_address');
        $this->db->select('students.guardian_address2');
        $this->db->select('students.current_address');
        $this->db->select('students.guardian_phone');
        $this->db->select('students.image');
        $this->db->select('students.lrn');
        $this->db->select('students.rte');
        $this->db->select('students.signature_image');
        $this->db->from('student_session'); 
        $this->db->join('students', 'students.id = student_session.student_id', 'left'); 
        $this->db->join('classes', 'student_session.class_id = classes.id', 'left'); 
        $this->db->join('sections', 'student_session.section_id = sections.id', 'left'); 
        $this->db->where('student_session.session_id', $session_id);  
          
        
        $this->db->where('classes.sequence !=', '0' ); 
        if( $class_id ){ 
            $this->db->where('student_session.class_id', $class_id );  
        }
        if( $section_id ){  
            $this->db->where('student_session.section_id', $section_id ); 
        }
        $this->db->order_by("`classes`.`sequence`", "ASC");
        $this->db->order_by("`sections`.`section`", "ASC"); 
        $query = $this->db->get(); 
        $result_array = $query->result_array(); // array of result
        
        return $result_array;
    }
    
    public function searchByClassSectionSubject($class_id = null, $section_id = null, $subject_id=null, $order_by = "students.id", $session_id=null) {

        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,students.lrn,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.esc_number ,students.government_number, students.rfid_number, students.guardian_lastname  ,students.guardian_relation,students.guardian_email ,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.rte,students.gender, students.father_lastname,students.load_balance,students.hostel_id,students.dormitory, students.suffix')->from('students');

        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        if( $session_id ){
            $this->db->where('student_session.session_id', $session_id );
        } else {
            $this->db->where('student_session.session_id', $this->current_session);
        }
        if ($class_id != null) {
            $this->db->where('student_session.class_id', $class_id);
        }
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }
        $this->db->order_by($order_by);

        $query = $this->db->get();
        return $query->result_array();
    }
	
	public function getcafeterialoadbalance($datemonthly='',$dateyearly=''){
		$monthlyamount=0;
		//$datemonthly = date('m');
		//$dateyearly = date('Y');
		
		/*   if($datemonthly == FALSE){
            $nmonth = date('m');
        } else {
            $nmonth = date('m',strtotime($datemonthly));
        }
        if($dateyearly == FALSE){
            $dateyearly = date('Y');
        } */

		$this->db->select(" SUM(orders.amount) as amount, students.load_balance,sections.section,classes.class,student_session.id as student_session_id, students.firstname, students.lastname, students.middlename, students.suffix, students.id as student_id ");
		$this->db->from('students'); 	
		
		$this->db->join('student_session', 'student_session.student_id = students.id');
		$this->db->join('order_customer', 'order_customer.student_id = students.id', 'left'); 
		$this->db->join('orders', 'orders.id = order_customer.order_id', 'left');		
		$this->db->join('order_details', 'order_details.order_id = order_customer.order_id', 'left');
		$this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','left');
		
		if( $nmonth ){ 
			$this->db->where("MONTH(`date_purchase`)",$nmonth);
		}
		if( $dateyearly ){
			$this->db->where("YEAR(`date_purchase`)",$dateyearly);
		}
		//$this->db->where("sections.section != 'None'");
		$this->db->where("is_deleted", 0 );
		$this->db->group_by('order_customer.student_id');
		$query = $this->db->get();
        $result = $query->result_array();
		
		return $result;
	}
	
	public function getallstudenthavebalance(){
		$this->db->select("*");
		$this->db->from('students'); 
		$this->db->where('load_balance <>', 0 ); 
		$this->db->order_by('load_balance','ASC'); 
		$query = $this->db->get();
        $result = $query->result_array();
		
		return $result;
	}
	
	public function getStudentFullnameBySession( $session_id = null, $class_id = null ) {
		$this->db->select('students.id as id, students.firstname, students.middlename, students.lastname, students.suffix')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('sessions', 'sessions.id = student_session.session_id');
		if( $session_id ){
			$this->db->where('student_session.session_id', $session_id);
		} else {
			$this->db->where('student_session.session_id', $this->current_session);
		}
		if( $class_id ){
			$this->db->where('student_session.class_id', $class_id);
		}
		$this->db->order_by('students.lastname');
		$this->db->order_by('students.firstname');
        $query = $this->db->get();
 
        return $query->result_array();

    }
	
public function searchFullTextCheckAllow($searchterm , $include_session = "yes", $allow = 'no', $session_id = '') {
		$searchterm = trim( $searchterm );
        $this->db->select('classes.id AS `class_id`,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.lrn,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,      students.esc_number ,students.government_number ,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code ,students.father_name ,students.father_lastname , students.guardian_email ,students.guardian_name , students.rfid_number, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.gender,students.rte,student_session.session_id,students.load_balance,students.hostel_id,students.dormitory,students.suffix,students.meal_plan')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id', 'left');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
		$this->db->where('student_session.allow', $allow );
        $this->db->where('student_session.is_deactivate', 'no' );
       // if(  $include_session == "yes"){
			if(empty( $session_id )){
				$session_id = $this->current_session;
			}
            $this->db->where('student_session.session_id', $session_id );
        //} 
        // Search by LASTNAME or FIRSTNAME (starts with ONLY)
        $starts_with = $this->db->escape_like_str($searchterm) . '%';
        
        $this->db->group_start();
        $this->db->where("students.lastname LIKE '$starts_with'");
        $this->db->or_where("students.firstname LIKE '$starts_with'");
        $this->db->group_end();
        
        // Order by priority: lastname starts with > firstname starts with
        $this->db->order_by("CASE 
            WHEN students.lastname LIKE '$starts_with' THEN 1 
            WHEN students.firstname LIKE '$starts_with' THEN 2
            ELSE 3 
        END", '', FALSE);
        $this->db->order_by('students.lastname', 'asc');
        $this->db->order_by('students.firstname', 'asc');
        $this->db->limit('10');
        $query = $this->db->get();
        return $query->result_array();
    }
	
	public function getallowstudents( $session_id = '', $current_session_id ) {
		$this->db->select('students.id as id,student_session.id as `student_session_id`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no ,allow,students.lastname,students.firstname,students.middlename')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','left');
		if(empty($session_id)){
			$session_id = $current_session_id;
		}
        $this->db->where('student_session.session_id', $session_id);
        $this->db->where('student_session.allow', 'yes');
		$this->db->order_by('students.lastname', 'asc');
		$this->db->order_by('students.firstname', 'asc');
        $query = $this->db->get();
 
        return $query->result_array();

    }
	
	public function searchByClassSectionGender($class_id = null, $section_id = null, $gender, $session_id = null, $active_only = FALSE ) {
        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,students.lrn,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.esc_number ,students.government_number, students.rfid_number, students.guardian_midname  ,students.guardian_lastname  ,students.guardian_relation,students.guardian_email ,students.guardian_phone,students.guardian_address,students.guardian_address2,students.guardian_barangay_id,students.guardian_city_id,students.guardian_province_id,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_midname,students.mother_name,students.mother_midname,students.mother_lastname,students.rte,students.gender, students.father_lastname,students.load_balance,students.hostel_id,students.dormitory, students.suffix,student_session.strand_id,guardian_lastname,status,modality,students.school_address, students.school_name, student_session.esc_grantee,student_session.gov_voucher,student_session.esc_no, student_session.gov_no,student_session.allow')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        if(empty( $session_id )){
			$session_id = $this->current_session;
		} 
        $this->db->where('student_session.session_id', $session_id );
		//$this->db->where('students.status', 'active');
        if ($class_id != null) {
            $this->db->where('student_session.class_id', $class_id);
        }
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }
        if ($gender != null) {
            $this->db->where('students.gender', $gender);
        }
        if( $active_only == TRUE ){
            $this->db->where('students.status', 'active');
        }
        $this->db->order_by('students.lastname', 'ASC');
        $this->db->order_by('students.firstname', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }
	
	public function searchByNoSection($class_id = null, $order_by = "students.id", $gender='Male') {
		if( $class_id ){
			$this->db->select('classes.id AS `class_id`,student_session.id as student_session_id, students.suffix, students.id,students.lrn,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.esc_number ,students.government_number, students.rfid_number, students.guardian_lastname  ,students.guardian_relation,students.guardian_email ,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.rte,students.gender, students.father_lastname,students.load_balance,students.hostel_id,students.dormitory')->from('students');
			$this->db->join('student_session', 'student_session.student_id = students.id');
			$this->db->join('classes', 'student_session.class_id = classes.id');
			$this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
			$this->db->join('categories', 'students.category_id = categories.id', 'left');
			$this->db->where('student_session.session_id', $this->current_session);
			$this->db->where('student_session.class_id', $class_id);
			$this->db->where('students.gender', $gender);
			$this->db->where('student_session.section_id IS NULL');
			$this->db->order_by($order_by);

			$query = $this->db->get();
			return $query->result_array();
		}
    }
	
	public function searchByNoneSection($class_id = null, $order_by = "students.id", $gender='Male') {
		$none_section = $this->section_model->getbyname('None');
		$none_section_id = $none_section['id'];
		if( $class_id ){
			$this->db->select('classes.id AS `class_id`,student_session.id as student_session_id, students.suffix, students.id,students.lrn,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.esc_number ,students.government_number, students.rfid_number, students.guardian_lastname  ,students.guardian_relation,students.guardian_email ,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.rte,students.gender, students.father_lastname,students.load_balance,students.hostel_id,students.dormitory')->from('students');
			$this->db->join('student_session', 'student_session.student_id = students.id');
			$this->db->join('classes', 'student_session.class_id = classes.id');
			$this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
			$this->db->join('categories', 'students.category_id = categories.id', 'left');
			$this->db->where('student_session.session_id', $this->current_session);
			$this->db->where('student_session.class_id', $class_id);
			$this->db->where('students.gender', $gender);
			$this->db->where('student_session.section_id', $none_section_id);
			$this->db->order_by($order_by);

			$query = $this->db->get();
			return $query->result_array();
		}
    }
	
	public function getsearchWithNoSection($class_id = null, $order_by = "students.id", $gender='Male') {
		$searchByNoSection = $this->searchByNoSection( $class_id, $order_by, $gender );
		$searchByNoneSection = $this->searchByNoneSection( $class_id, $order_by, $gender );
		$searchByNoSection = array_merge( $searchByNoSection, $searchByNoneSection );
		if( count($searchByNoSection ) > 0 ){
			$names = array();
			foreach ($searchByNoSection as $user) {
				$names[] =  strtoupper($user['lastname']);
			} 
			array_multisort($names, SORT_ASC, $searchByNoSection );
		}
		return $searchByNoSection;
		
	}
	
	public function searchAllStudentText($searchterm) {
		$searchterm = trim( $searchterm );
        $this->db->select('firstname,middlename,lastname,status,admission_no,roll_no,lrn,id')->from('students');
        $this->db->group_start();
        $this->db->like('students.firstname', $searchterm);
        $this->db->or_like('students.lastname', $searchterm);
        $this->db->or_like('students.roll_no', $searchterm);
        $this->db->or_like('students.admission_no', $searchterm);
        $this->db->or_like('students.lrn', $searchterm);
        $this->db->group_end();
        $this->db->order_by('students.id');
        //$this->db->limit('10');
        $query = $this->db->get();
		
        return $query->result_array();
    }
	
	public function check_unique_admission( $admission_no, $student_id=null ){
		if( $admission_no ){
			$this->db->select('students.id,students.admission_no')->from('students');
			$this->db->where('students.admission_no', $admission_no);
			if( $student_id ){
				$this->db->or_where('students.id <>', $student_id );
			}
			$query = $this->db->get();
			
			return $query->row_array();
		}
	}
	
	public function checkStudentExist($data = array()) {
		$firstname = strtoupper( $data['search_firstname'] );
        $lastname = strtoupper( $data['search_lastname'] );
        // $middlename = strtoupper( $data['search_middlename'] );
        // $suffix = strtoupper( $data['search_suffix'] );
        $gender = strtoupper( $data['search_gender'] );
        $dob = strtoupper( $data['search_dob'] );
        // $lrn = strtoupper( $data['search_lrn'] );
        // $admission = strtoupper( $data['search_admission'] );
        // $admission =  str_replace("-","",$admission );
        $this->db->select('id,guardian_phone')->from('students');
		if( $firstname && $lastname ){
			$this->db->where('UPPER(students.firstname)', trim($firstname));
			$this->db->where('UPPER(students.lastname)', trim($lastname));
		}
		// if( $middlename ){
		// 	$this->db->where('UPPER(students.middlename)', trim($middlename));
		// }
		// if( $suffix ){
		// 	$this->db->where('UPPER(students.suffix)', trim($suffix));
		// }
		if( $gender ){
			$this->db->where('UPPER(students.gender)', trim($gender));
		}
		if( $dob ){
			$this->db->where('UPPER(students.dob)', $dob);
		}
		// if( $admission ){
		// 	$this->db->where('REPLACE(students.admission_no,"-","")', trim($admission));
		// }
		// if( $lrn ){
		// 	$this->db->where('UPPER(students.lrn)', trim($lrn));
		// }
		
        $query = $this->db->get();
		$num_rows = $query->num_rows();
		if( $num_rows > 1 ){
			return $query->result_array();
		} else {
			return $query->row_array();
		}
    }
	
	public function check_if_enrolled($id, $session_id ) {
		  $this->db->select('students.id,students.admission_no')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','left');
        $this->db->join('sessions', 'sessions.id = student_session.session_id');
        $this->db->where('student_session.session_id', $session_id );
		$this->db->where('status <>', 'reserved');
        $this->db->where('students.id', $id);
        $query = $this->db->get();
		return $query->row_array();

    }

    public function searchByAllforCSV( $order_by = "students.id", $session_id = null ) {
        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,students.lrn,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.esc_number ,students.government_number, students.rfid_number, students.guardian_lastname  ,students.guardian_relation,students.guardian_email ,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.rte,students.gender, students.father_lastname,students.load_balance,students.hostel_id,students.dormitory, students.suffix,student_session.strand_id,students.guardian_is, students.mother_name,students.mother_lastname,students.mother_midname,father_midname,students.guardian_midname,students.guardian_lastname')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        if(empty( $session_id )){
            $session_id = $this->current_session;
        } 
        $this->db->where('student_session.session_id', $session_id );
        
        
        if( $order_by == 'lastname'){
              $this->db->order_by('students.lastname');
              $this->db->order_by('students.firstname');
        } else {
            $this->db->order_by($order_by);
        }

        $query = $this->db->get();
        return $query->result_array();
    }


    public function getSDAbyGender($class_id = null, $section_id = null, $gender, $session_id = null, $active_only = FALSE ) {
        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,students.lrn,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.esc_number ,students.government_number, students.rfid_number, students.guardian_midname  ,students.guardian_lastname  ,students.guardian_relation,students.guardian_email ,students.guardian_phone,students.guardian_address,students.guardian_address2,students.guardian_barangay_id,students.guardian_city_id,students.guardian_province_id,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_midname,students.mother_name,students.mother_midname,students.mother_lastname,students.rte,students.gender, students.father_lastname,students.load_balance,students.hostel_id,students.dormitory, students.suffix,student_session.strand_id,guardian_lastname,status,modality,students.school_address, students.school_name, student_session.esc_grantee,student_session.gov_voucher,student_session.esc_no, student_session.gov_no')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        if(empty( $session_id )){
            $session_id = $this->current_session;
        } 
        $this->db->where('student_session.session_id', $session_id );
        //$this->db->where('students.status', 'active');
        if ($class_id != null) {
            $this->db->where('student_session.class_id', $class_id);
        }
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }
        if ($gender != null) {
            $this->db->where('students.gender', $gender);
        }
        if( $active_only == TRUE ){
            $this->db->where('students.status', 'active');
        }
        $this->db->where('students.religion', 'SDA');
        $this->db->order_by('students.lastname', 'ASC');
        $this->db->order_by('students.firstname', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getBySessionCoordinatorCount($id, $class_id ='', $section_id = '', $session_id = '', $gender = '' ) {
        if( empty($session_id) ){
            $session_id = $this->current_session;
        }
        $this->db->select('students.id, students.firstname, students.lastname, students.middlename, student_session.student_type AS type, student_session.session_id, student_session.class_id, classes.class, student_session.created_at, student_session.enrolled_at, students.guardian_phone, students.suffix, student_session.strand_id, students.gender, students.dob, students.guardian_lastname, students.guardian_name, students.lrn, students.dob, sections.section')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('coordinator_grade', 'student_session.class_id = coordinator_grade.class_id');
        // $this->db->where('student_session.enrollment_status', 'active');
        $this->db->where('coordinator_grade.user_id', $id);
        if( $class_id ){
            $this->db->where('student_session.class_id', $class_id);
        }
        if( $section_id ){
            $this->db->where('student_session.section_id', $section_id);
        }
         if( $gender ){
            $this->db->where('students.gender', $gender );
        }
        $this->db->where('coordinator_grade.session_id', $session_id);
        $this->db->where('student_session.session_id', $session_id);
        $this->db->order_by('students.lastname', 'asc');
        $this->db->order_by('students.firstname', 'asc');
        $this->db->order_by('students.middlename', 'asc');
         $query = $this->db->get();
        return $query->num_rows();
    }

    public function update_student($data, $student_id ) {

        if (isset($student_id)) {
            $this->db->where('id', $student_id);
            $this->db->update('students', $data);

            if ($this->db->affected_rows() > 0) {
              return TRUE;
            } else {
              return FALSE;
            }
        } else { 
          return FALSE;
             
        }
    }

    public function searchByClassSectionGenderAllow($class_id = null, $section_id = null, $gender, $allow='null', $session_id = null, $active_only = FALSE ) {
        $this->db->select('students.id,students.firstname,students.lastname,students.middlename,students.suffix,students.meal_plan,student_session.allow')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        if(empty( $session_id )){
            $session_id = $this->current_session;
        } 
        $this->db->where('student_session.session_id', $session_id );
        //$this->db->where('students.status', 'active');
        if ($class_id != null) {
            $this->db->where('student_session.class_id', $class_id);
        }
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }
        if ($gender != null) {
            $this->db->where('students.gender', $gender);
        }
        if( $active_only == TRUE ){
            $this->db->where('students.status', 'active');
        }
        if( $allow ){
            if( $allow == 'yes'){
               $this->db->where('student_session.allow', 'yes');
            } else {
                $this->db->where('student_session.allow <>', 'yes'); 
            }
        }
        
        $this->db->order_by('students.lastname', 'ASC');
        $this->db->order_by('students.firstname', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function update_meal_plan($student_id, $meal_plan) {
        $this->db->where('id', $student_id);
        return $this->db->update('students', ['meal_plan' => $meal_plan]);
    }
    
    public function update_school_name($student_id, $school_name, $school_address)
    {
        $data = array (
            'school_name' => $school_name,
            'school_address' => $school_address
        );

    }

    //NEW Function 03-23-2026
    public function searchByClassSectionNoNULL($class_id = null, $section_id = null, $order_by = "students.id", $session_id = null ) {
        $this->db->select('classes.id AS class_id,student_session.id as student_session_id,students.id,students.lrn,classes.class,sections.id AS section_id,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as category_id,IFNULL(categories.category, "") as category,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , student_session.esc_no as esc_number, students.rfid_number, students.guardian_lastname  ,students.guardian_relation,students.guardian_email ,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.rte,students.gender, students.father_lastname,students.load_balance,students.hostel_id,students.dormitory, students.suffix,student_session.strand_id,students.guardian_is, students.mother_name,students.mother_lastname,students.mother_midname,father_midname,students.guardian_midname,students.guardian_lastname,students.signature_image')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id','LEFT');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
		if(empty( $session_id )){
			$session_id = $this->current_session;
		}
		$this->db->where('student_session.session_id', $session_id );
       // $this->db->where('students.status', 'active');
        if ($class_id != null) {
            $this->db->where('student_session.class_id', $class_id);
        }
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }

        $this->db->where('sections.id IS NOT NULL', null, false);
        $this->db->where('sections.id <>', '21');  // section - none

		if( $order_by == 'lastname'){
			  $this->db->order_by('students.lastname');
			  $this->db->order_by('students.firstname');
		} else {
			$this->db->order_by($order_by);
		}

        $query = $this->db->get();
        return $query->result_array();
    }

    public function isCurrentEnrollment($student_session_id)
    {
        $current_session = $this->setting_model->getCurrentSession();

        return $this->db
            ->where('id', $student_session_id)
            ->where('session_id', $current_session)
            ->count_all_results('student_session') > 0;
    }


}
