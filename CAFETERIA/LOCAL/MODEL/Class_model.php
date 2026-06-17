<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Class_model extends CI_Model {

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
      $this->db->select()->from('classes');
      if ($id != null) {
        $this->db->where('id', $id);
      } else {
        $this->db->order_by('sequence');
      }
      $query = $this->db->get();
      if ($id != null) {
        return $query->row_array(); 
      } else {
        return $query->result_array(); 
      }
    }
	
	public function get_by_teacher($id) {
		// $this->db->select('*')->from('classes'); 
        // $this->db->where('id', $id);
       
		$this->db->select('classes.*'); 
		$this->db->from('teacher_subjects'); 
		$this->db->join('class_sections', 'teacher_subjects.class_section_id = class_sections.id', 'left');  
		$this->db->join('classes', 'class_sections.class_id = classes.id', 'left');  
		$this->db->where('teacher_subjects.teacher_id', $id); 
		$this->db->where('teacher_subjects.session_id', $this->current_session); 
		$this->db->group_by('classes.id');  
        $query = $this->db->get(); 
		return $query->result_array(); 
       
    }

    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id) {
      $this->db->trans_begin();
      $this->db->where('id', $id);
      $this->db->delete('classes');//class record delete.

      $this->db->where('class_id', $id);
      $this->db->delete('class_sections');//class_sections record delete.

      if ($this->db->trans_status() === FALSE)
      {
        $this->db->trans_rollback();
      }
      else
      {
        $this->db->trans_commit();
      }
      return TRUE;
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
        $this->db->update('classes', $data); 
      } else {
        $this->db->insert('classes', $data); 
      }
    }

    function check_data_exists($data) {
      $this->db->where('class', $data);
      
      $query = $this->db->get('classes');
      if ($query->num_rows() > 0) {
        return $query->row();
      } else {
        return FALSE;
      }
    }

    public function class_exists($str)
    {

      $class = $this->security->xss_clean($str);
      $res=$this->check_data_exists($class);

      if ($res) {
       $pre_class_id=$this->input->post('pre_class_id');
       if(isset($pre_class_id)){
        if($res->id == $pre_class_id ){
          return TRUE;
        }
      }
      $this->form_validation->set_message('class_exists', 'Record already exists');
      return FALSE;
    } else {
      return TRUE;
    }
  }

  //rubin
       public function get_calss_section_subject( $id, $session_id = null ){
        if(empty( $session_id )){
          $session_id = $this->current_session;
        }
        /* SELECT
        classes.id AS class_id,
        classes.class AS class_name,
        subjects.`name` AS subject_name,
        subjects.id AS subject_id,
        sections.id AS section_id,
        sections.section AS section_name,
        
        FROM  `teacher_subjects`
        LEFT JOIN `class_sections` ON `teacher_subjects`.`class_section_id` = `class_sections`.`section_id`
        LEFT JOIN `classes` ON `class_sections`.`class_id` = `classes`.`id`
        LEFT JOIN `subjects` ON `teacher_subjects`.`subject_id` = `subjects`.`id`
        LEFT JOIN `sections` ON `teacher_subjects`.`class_section_id` = `sections`.`id`
        WHERE
        `teacher_subjects`.`teacher_id` = '1'
        GROUP BY
        `classes`.`id` */

        $this->db->select('ANY_VALUE ( classes.id ) AS id'); 
        $this->db->select('ANY_VALUE ( classes.class ) AS class'); 
        $this->db->select('ANY_VALUE ( classes.id ) AS class_id'); 
        $this->db->select('ANY_VALUE ( classes.class ) AS class_name'); 
        $this->db->select('ANY_VALUE ( subjects.`name` ) AS subject_name'); 
        $this->db->select('ANY_VALUE ( subjects.`id` ) AS subject_id'); 
        $this->db->select('ANY_VALUE ( sections.`id` ) AS section_id'); 
        $this->db->select('ANY_VALUE ( sections.`section` ) AS section_name');  
        $this->db->from('teacher_subjects'); 

        $this->db->join('class_sections', 'teacher_subjects.class_section_id = class_sections.id', 'left');  
       /*  $this->db->join('classes', 'class_sections.class_id = classes.id', 'left'); 
        $this->db->join('subjects', 'teacher_subjects.subject_id = subjects.id', 'left'); 
        $this->db->join('sections', 'class_sections.section_id = sections.id', 'left');  */
        $this->db->join('classes', 'class_sections.class_id = classes.id', 'left'); 
        $this->db->join('subjects', 'teacher_subjects.subject_id = subjects.id', 'left'); 
        $this->db->join('sections', 'class_sections.section_id = sections.id', 'left'); 
    
        $this->db->where('teacher_subjects.teacher_id', $id); 
        $this->db->where('teacher_subjects.session_id', $session_id );  
        $this->db->group_by('class_sections.class_id');  
        $query = $this->db->get(); 
        return $query->result_array(); 

  }

  public function get_calss_section_subject_all( $id ){
        if(empty($session_id)) {
          $session_id = $this->current_session;
        }

        $this->db->select('classes.id AS id'); 
        $this->db->select('classes.class AS class'); 
        $this->db->select('classes.id AS class_id'); 
        $this->db->select('classes.class AS class_name'); 
        $this->db->select('subjects.`name` AS subject_name'); 
        $this->db->select('subjects.`id` AS subject_id'); 
        $this->db->select('sections.`id` AS section_id'); 
        $this->db->select('sections.`section` AS section_name');  
        $this->db->from('teacher_subjects'); 

        $this->db->join('class_sections', 'teacher_subjects.class_section_id = class_sections.id', 'left');  
        $this->db->join('classes', 'class_sections.class_id = classes.id', 'left'); 
        $this->db->join('subjects', 'teacher_subjects.subject_id = subjects.id', 'left'); 
        $this->db->join('sections', 'class_sections.section_id = sections.id', 'left'); 

        $this->db->where('teacher_subjects.teacher_id', $id);  
		$this->db->where('teacher_subjects.session_id', $session_id); 
        $this->db->where('classes.id != ',  ' ');  
        $this->db->where('sections.id != ', ' ');  
        $this->db->where('subjects.id != ', ' ');    
        // $this->db->group_by('subjects.id');  
        $query = $this->db->get(); 
        return $query->result_array(); 

  } 

  public function get_calss_section_subject_list( ){
         

        $this->db->select('classes.id AS id'); 
        $this->db->select('classes.class AS class'); 
        $this->db->select('classes.id AS class_id'); 
        $this->db->select('classes.class AS class_name'); 
        $this->db->select('subjects.`name` AS subject_name'); 
        $this->db->select('subjects.`id` AS subject_id'); 
        $this->db->select('sections.`id` AS section_id'); 
        $this->db->select('sections.`section` AS section_name');  
        $this->db->from('teacher_subjects'); 

        $this->db->join('class_sections', 'teacher_subjects.class_section_id = class_sections.id', 'left');  
        $this->db->join('classes', 'class_sections.class_id = classes.id', 'left'); 
        $this->db->join('subjects', 'teacher_subjects.subject_id = subjects.id', 'left'); 
        $this->db->join('sections', 'class_sections.section_id = sections.id', 'left'); 

        $this->db->where('teacher_subjects.session_id', $this->current_session); 
 
        $query = $this->db->get(); 
        return $query->result_array(); 

  }   

  public function get_class_section_listv1( ){
         

        $this->db->select('classes.id AS id'); 
        $this->db->select('classes.class AS class'); 
        $this->db->select('classes.id AS class_id'); 
        $this->db->select('classes.class AS class_name');  
        $this->db->select('sections.`id` AS section_id'); 
        $this->db->select('sections.`section` AS section_name');  
        $this->db->from('teacher_subjects'); 

        $this->db->join('class_sections', 'teacher_subjects.class_section_id = class_sections.id', 'left');  
        $this->db->join('classes', 'class_sections.class_id = classes.id', 'left');  
        $this->db->join('sections', 'class_sections.section_id = sections.id', 'left'); 
        
        $this->db->group_by(array("sections.id", "classes.id"));
        $this->db->order_by('class');
        $query = $this->db->get(); 
        return $query->result_array(); 

  } 
  public function get_class_section_list( ){
         

        $this->db->select('classes.id AS id'); 
        $this->db->select('classes.class AS class'); 
        $this->db->select('classes.id AS class_id'); 
        $this->db->select('classes.class AS class_name');  
        $this->db->select('sections.`id` AS section_id'); 
        $this->db->select('sections.`section` AS section_name');  
        $this->db->from('class_sections');  
        $this->db->join('classes', 'class_sections.class_id = classes.id', 'left');  
        $this->db->join('sections', 'class_sections.section_id = sections.id', 'left'); 
        
        $this->db->where('class_sections.session_id',$this->current_session);  
        $this->db->group_by(array("sections.id", "classes.id"));
        $this->db->order_by('classes.sequence', "ASC");
        $query = $this->db->get(); 
        return $query->result_array(); 

  } 
  public function get_class_section_subject_by_teacher( $id  ){
         

        $this->db->select('classes.id AS id'); 
        $this->db->select('classes.class AS class'); 
        $this->db->select('classes.id AS class_id'); 
        $this->db->select('classes.class AS class_name');  
        $this->db->select('sections.`id` AS section_id'); 
        $this->db->select('sections.`section` AS section_name');  
        $this->db->from('teacher_subjects'); 

        $this->db->join('class_sections', 'teacher_subjects.class_section_id = class_sections.id', 'left');  
        $this->db->join('classes', 'class_sections.class_id = classes.id', 'left');  
        $this->db->join('sections', 'class_sections.section_id = sections.id', 'left'); 
        
        $this->db->where('teacher_subjects.teacher_id', $id);  
        $this->db->group_by(array("sections.id", "classes.id"));
        $this->db->order_by('class');
        $query = $this->db->get(); 
        return $query->result_array(); 

  } 

  public function get_class_section_subject_by_teachersession( $id  ){
         

        $this->db->select('classes.id AS id'); 
        $this->db->select('classes.class AS class'); 
        $this->db->select('classes.id AS class_id'); 
        $this->db->select('classes.class AS class_name');  
        $this->db->select('sections.`id` AS section_id'); 
        $this->db->select('sections.`section` AS section_name');  
        $this->db->from('teacher_subjects'); 

        $this->db->join('class_sections', 'teacher_subjects.class_section_id = class_sections.id', 'left');  
        $this->db->join('classes', 'class_sections.class_id = classes.id', 'left');  
        $this->db->join('sections', 'class_sections.section_id = sections.id', 'left'); 
        
        $this->db->where('teacher_subjects.teacher_id', $id);  
        $this->db->where('class_sections.session_id',$this->current_session);  
        $this->db->group_by(array("sections.id", "classes.id"));
        $this->db->order_by('class');
        $query = $this->db->get(); 
        return $query->result_array(); 

  } 

  public function get_class_section_subject_list( ){ 

        $this->db->select('classes.id AS id'); 
        $this->db->select('classes.class AS class'); 
        $this->db->select('classes.id AS class_id'); 
        $this->db->select('classes.class AS class_name'); 
        $this->db->select('subjects.`name` AS subject_name'); 
        $this->db->select('subjects.`id` AS subject_id'); 
        $this->db->select('sections.`id` AS section_id'); 
        $this->db->select('sections.`section` AS section_name');  
        $this->db->from('teacher_subjects'); 

        $this->db->join('class_sections', 'teacher_subjects.class_section_id = class_sections.id', 'left');  
        $this->db->join('classes', 'class_sections.class_id = classes.id', 'left'); 
        $this->db->join('subjects', 'teacher_subjects.subject_id = subjects.id', 'left'); 
        $this->db->join('sections', 'class_sections.section_id = sections.id', 'left'); 
 
        $this->db->group_by('classes.id');  
        $query = $this->db->get(); 
        return $query->result_array(); 

  }

  public function getbyname($name = null) {
    $this->db->select()->from('classes'); 
    $this->db->like('class', $name); 
    $this->db->order_by('id'); 
    $query = $this->db->get(); 
    return $query->row_array(); 

  }
  
  public function getnextClassID( $id ){
	 $this->db->select('id, sequence, class')->from('classes'); 
	 $this->db->where('id', $id);
	 $details = $this->db->get()->row_array(); 
	 $sequence_id = (int)$details['sequence'];
	 $sequence_id++;
	 $this->db->select('id, sequence, class')->from('classes'); 
	 $this->db->where('sequence', $sequence_id);
	 $query = $this->db->get(); 
	 
	 return $query->row_array(); 
  }

  public function get_calss_section_subjectv2( ){
    $this->db->select('ANY_VALUE ( classes.id ) AS id');
    $this->db->select('ANY_VALUE ( classes.class ) AS class'); 
    $this->db->select('ANY_VALUE ( classes.id ) AS class_id'); 
    $this->db->select('ANY_VALUE ( classes.class ) AS class_name'); 
    $this->db->select('ANY_VALUE ( subjects.`name` ) AS subject_name'); 
    $this->db->select('ANY_VALUE ( subjects.`id` ) AS subject_id'); 
    $this->db->select('ANY_VALUE ( sections.`id` ) AS section_id'); 
    $this->db->select('ANY_VALUE ( sections.`section` ) AS section_name');  
    $this->db->from('teacher_subjects'); 

    $this->db->join('class_sections', 'teacher_subjects.class_section_id = class_sections.id', 'left');
    $this->db->join('classes', 'class_sections.class_id = classes.id', 'left'); 
    $this->db->join('subjects', 'teacher_subjects.subject_id = subjects.id', 'left'); 
    $this->db->join('sections', 'class_sections.section_id = sections.id', 'left');
         
    $this->db->where('teacher_subjects.session_id',$this->current_session );  
    $this->db->group_by('class_sections.class_id');  
    $query = $this->db->get(); 
    return $query->result_array(); 

  }

}
