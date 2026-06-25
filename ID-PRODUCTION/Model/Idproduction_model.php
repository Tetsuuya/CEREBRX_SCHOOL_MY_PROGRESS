<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Idproduction_model extends CI_Model {

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
        $this->db->select()->from('idproduction');
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
    public function getidproduction($id = null) {
        $this->db->select('idproduction.*,users.id as `user_tbl_id`,users.username,users.password as `user_tbl_password`,users.is_active as `user_tbl_active`');
        $this->db->from('idproduction');
        $this->db->join('users', 'users.user_id = idproduction.id', 'left'); 
        $this->db->where('users.role', 'idproduction');
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
        $this->db->delete('idproduction');
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
            $this->db->update('idproduction', $data); 
        } else {
            $this->db->insert('idproduction', $data);
            return $this->db->insert_id();
        }
    }

    public function getTotalidproduction() {
        $sql = "SELECT count(*) as `total_idproduction` FROM `idproduction`";
        $query = $this->db->query($sql);
        return $query->row(); 
    }

    /**
     * synchronizes a teacher's details to the employee/faculties table
     * maps similar information except for address, tin, sss, phic, pagibig, etc
     * searches for existing records by email, then phone, then name
     */
    public function syncTeacherToEmployee($teacher_id, $old_teacher = null) {
        $teacher = $this->db->get_where('teachers', array('id' => $teacher_id))->row_array();
        if (!$teacher) {
            return;
        }

        // Safe mapping using isset() to avoid PHP notices/warnings
        $employee_data = array(
            'name'        => isset($teacher['name']) ? $teacher['name'] : '',
            'middlename'  => isset($teacher['middlename']) ? $teacher['middlename'] : '',
            'lastname'    => isset($teacher['lastname']) ? $teacher['lastname'] : '',
            'email'       => (!empty($teacher['email'])) ? $teacher['email'] : null,
            'sex'         => isset($teacher['sex']) ? $teacher['sex'] : '',
            'dob'         => (!empty($teacher['dob']) && $teacher['dob'] !== '0000-00-00') ? $teacher['dob'] : null,
            'phone'       => isset($teacher['phone']) ? $teacher['phone'] : '',
            'address'     => isset($teacher['address']) ? $teacher['address'] : '',
            'is_active'   => isset($teacher['is_active']) ? $teacher['is_active'] : 'yes',
            'image'       => isset($teacher['image']) ? $teacher['image'] : 'uploads/student_images/no_image.png',
            'position'    => isset($teacher['type_teacher']) ? $teacher['type_teacher'] : 'Teacher'
        );

        $existing = null;

        // match by email
        if (!empty($teacher['email'])) {
            $existing = $this->db->get_where('employee', array('email' => $teacher['email']))->row_array();
        }

        // match by phone
        if (!$existing && !empty($teacher['phone'])) {
            $existing = $this->db->get_where('employee', array('phone' => $teacher['phone']))->row_array();
        }

        // match by name
        if (!$existing) {
            // use the old name details to search in case nag change name
            $search_name = ($old_teacher !== null) ? $old_teacher['name'] : $teacher['name'];
            $search_lastname = ($old_teacher !== null) ? $old_teacher['lastname'] : $teacher['lastname'];
            $search_middlename = ($old_teacher !== null) ? $old_teacher['middlename'] : $teacher['middlename'];

            $this->db->where('name', $search_name);
            $this->db->where('lastname', $search_lastname);
            if (!empty($search_middlename)) {
                $this->db->where('middlename', $search_middlename);
            } else {
                $this->db->group_start();
                $this->db->where('middlename', '');
                $this->db->or_where('middlename', null);
                $this->db->group_end();
            }
            $existing = $this->db->get('employee')->row_array();
        }

        if ($existing) {
            // update ang employee record nga na match
            $this->db->where('id', $existing['id']);
            $this->db->update('employee', $employee_data);
        } else {
            // insert as a new employee and generate a basic ID Number 
            $employee_data['id_no'] = date('Y') . '-' . str_pad($teacher_id, 4, '0', STR_PAD_LEFT);
            $this->db->insert('employee', $employee_data);
        }
    }

    /**
     * synchronizes an employee's signature path to the corresponding teacher's record
     *
     * @param int $employee_id
     * @param string $signature_path
     * @param array $old_employee
     */
    public function syncEmployeeSignatureToTeacher($employee_id, $signature_path, $old_employee = null) {
        // fetch the employee record to obtain match details
        $employee = $this->db->get_where('employee', array('id' => $employee_id))->row_array();
        if (!$employee) {
            return;
        }

        // match the employee to a teacher using the helper method and old employee details
        $existing_teacher = $this->findTeacherByEmployee($employee, $old_employee);

        // update teacher signature if match found
        if ($existing_teacher) {
            $this->db->where('id', $existing_teacher['id']);
            $this->db->update('teachers', array('signature' => $signature_path));
        }
    }

    /**
     * synchronizes an employee's details to the teachers table
     * matches the employee to the teacher using email, phone, or name
     *
     * @param int $employee_id
     * @param array $old_employee
     */
    public function syncEmployeeToTeacher($employee_id, $old_employee = null) {
        // fetch the employee record to obtain match details
        $employee = $this->db->get_where('employee', array('id' => $employee_id))->row_array();
        if (!$employee) {
            return;
        }

        $teacher_data = array(
            'name'         => isset($employee['name']) ? $employee['name'] : '',
            'middlename'   => isset($employee['middlename']) ? $employee['middlename'] : '',
            'lastname'     => isset($employee['lastname']) ? $employee['lastname'] : '',
            'email'        => (!empty($employee['email'])) ? $employee['email'] : null,
            'sex'          => isset($employee['sex']) ? $employee['sex'] : '',
            'dob'          => (!empty($employee['dob']) && $employee['dob'] !== '0000-00-00') ? $employee['dob'] : null,
            'phone'        => isset($employee['phone']) ? $employee['phone'] : '',
            'address'      => isset($employee['address']) ? $employee['address'] : '',
            'is_active'    => isset($employee['is_active']) ? $employee['is_active'] : 'yes',
            'image'        => isset($employee['image']) ? $employee['image'] : 'uploads/student_images/no_image.png',
            'signature'    => isset($employee['signature']) ? $employee['signature'] : '',
            'type_teacher' => (strcasecmp($employee['position'], 'advisory') == 0) ? 'Advisory' : 'Staff',
            'is_teacher'   => 'yes'
        );

        // match the employee to a teacher using the helper method and old employee details
        $existing = $this->findTeacherByEmployee($employee, $old_employee);

        if ($existing) {
            // update existing teacher record
            $this->db->where('id', $existing['id']);
            $this->db->update('teachers', $teacher_data);
        } else {
            // insert new teacher record
            $this->db->insert('teachers', $teacher_data);
        }
    }

    /**
     * helper method to find a matching teacher record for a given employee
     * matches by email, phone, or full name (supporting old name searches for edits)
     *
     * @param array $employee
     * @param array $old_employee
     * @return array|null
     */
    private function findTeacherByEmployee($employee, $old_employee = null) {
        $existing_teacher = null;

        // gamiton ang old employee details para sa database lookups in case gi edit sa user ang name/email/phone
        $search_email = ($old_employee !== null) ? $old_employee['email'] : $employee['email'];
        $search_phone = ($old_employee !== null) ? $old_employee['phone'] : $employee['phone'];
        $search_name = ($old_employee !== null) ? $old_employee['name'] : $employee['name'];
        $search_lastname = ($old_employee !== null) ? $old_employee['lastname'] : $employee['lastname'];
        $search_middlename = ($old_employee !== null) ? $old_employee['middlename'] : $employee['middlename'];

        // match by email
        if (!empty($search_email)) {
            $existing_teacher = $this->db->get_where('teachers', array('email' => $search_email))->row_array();
        }

        // match by phone
        if (!$existing_teacher && !empty($search_phone)) {
            $existing_teacher = $this->db->get_where('teachers', array('phone' => $search_phone))->row_array();
        }

        // match by name
        if (!$existing_teacher) {
            $this->db->where('name', $search_name);
            $this->db->where('lastname', $search_lastname);
            if (!empty($search_middlename)) {
                $this->db->where('middlename', $search_middlename);
            } else {
                $this->db->group_start();
                $this->db->where('middlename', '');
                $this->db->or_where('middlename', null);
                $this->db->group_end();
            }
            $existing_teacher = $this->db->get('teachers')->row_array();
        }

        return $existing_teacher;
    }

}


