<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Importgradesbatch_model extends CI_Model {

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
    public function get( $id = null, $teacher_id=null, $session_id=null, $quarter=null ) {
        $this->db->select('import_grades_batch.id, import_grades_batch.class_id, import_grades_batch.section_id, import_grades_batch.quarter, import_grades_batch.subject_id, import_grades_batch.created_at, class, section, subjects.name, published,  import_grades_batch.teacher_id')->from('import_grades_batch');
		$this->db->join('classes', 'import_grades_batch.class_id = classes.id ');
		$this->db->join('sections', 'import_grades_batch.section_id = sections.id ', 'LEFT');
		$this->db->join('subjects', 'import_grades_batch.subject_id = subjects.id');
		
        if( $teacher_id != null ){
			$this->db->where('import_grades_batch.teacher_id', $teacher_id );
		} else {
			$this->db->where('published', 1 );
		}
         if( $quarter ){
             $this->db->where('import_grades_batch.quarter', $quarter );
        }
		if ($id != null) {
            $this->db->where('import_grades_batch.id', $id);
        } else {
			if( $session_id ){ 
				$this->db->where('import_grades_batch.session_id', $session_id ); 
			} else {
				$this->db->where('import_grades_batch.session_id', $this->current_session ); 
			}
            $this->db->order_by('import_grades_batch.id','DESC');
        }
       
        $query = $this->db->get();
	/* 	print_r( $query );
		die(); */
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array(); 
        }
    }

	public function getBySession( $session_id ,$id = null, $teacher_id=null ) {
        $this->db->select('import_grades_batch.id, import_grades_batch.class_id, import_grades_batch.section_id, import_grades_batch.quarter, import_grades_batch.subject_id, import_grades_batch.created_at, class, section, subjects.name, published,  import_grades_batch.teacher_id')->from('import_grades_batch');
		$this->db->join('classes', 'import_grades_batch.class_id = classes.id ');
		$this->db->join('sections', 'import_grades_batch.section_id = sections.id ', 'LEFT');
		$this->db->join('subjects', 'import_grades_batch.subject_id = subjects.id');
		$this->db->where('import_grades_batch.session_id', $session_id ); 
		 
        if( $teacher_id != null ){
			$this->db->where('import_grades_batch.teacher_id', $teacher_id );
		} else {
			$this->db->where('published', 1 );
		}
		if ($id != null) {
            $this->db->where('import_grades_batch.id', $id);
        } else {
            $this->db->order_by('import_grades_batch.id','DESC');
        }
        $query = $this->db->get();
	/* 	print_r( $query );
		die(); */
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
        $this->db->delete('import_grades_batch');
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
            $this->db->update('import_grades_batch', $data); 
        } else {
            $this->db->insert('import_grades_batch', $data); 
            return $this->db->insert_id();
        }
    }
	
	public function get_limit( $id = null, $teacher_id=null, $limit=10 ) {
        $this->db->select('import_grades_batch.id, import_grades_batch.class_id, import_grades_batch.section_id, import_grades_batch.quarter, import_grades_batch.subject_id, import_grades_batch.created_at, class, section, subjects.name, published,  import_grades_batch.teacher_id')->from('import_grades_batch');
		$this->db->join('classes', 'import_grades_batch.class_id = classes.id ');
		$this->db->join('sections', 'import_grades_batch.section_id = sections.id ', 'LEFT');
		$this->db->join('subjects', 'import_grades_batch.subject_id = subjects.id');
		$this->db->where('import_grades_batch.session_id', $this->current_session ); 
		 
        if( $teacher_id != null ){
			$this->db->where('import_grades_batch.teacher_id', $teacher_id );
		} else {
			$this->db->where('published', 1 );
		}
		if ($id != null) {
            $this->db->where('import_grades_batch.id', $id);
        } else {
            $this->db->order_by('import_grades_batch.id','DESC');
        }
		$this->db->limit($limit); 
        $query = $this->db->get();
	/* 	print_r( $query );
		die(); */
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array(); 
        }
    }


    public function getteacherswhosubmittop10() {

        /*  SELECT
        `import_grades_batch`.*, `teachers`.`name`, `teachers`.`middlename`, `teachers`.`lastname`, `classes`.`class`, `sections`.`section`, `subjects`.`name`
        FROM
        `import_grades_batch`
        LEFT JOIN `teachers` ON `teachers`.`id` = `import_grades_batch`.`teacher_id`
        LEFT JOIN `classes` ON `classes`.`id` = `import_grades_batch`.`class_id`
        LEFT JOIN `sections` ON `sections`.`id` = `import_grades_batch`.`section_id`
        LEFT JOIN `subjects` ON `subjects`.`id` = `import_grades_batch`.`subject_id`
        */

        $this->db->select(' `import_grades_batch`.*');
        $this->db->select(' `teachers`.`name` as firstname, `teachers`.`middlename`, `teachers`.`lastname`');
        $this->db->select(' `classes`.`class` as classname');
        $this->db->select(' `sections`.`section` as sectionname');
        $this->db->select(' `subjects`.`name`  as subjectname');
        $this->db->from('import_grades_batch');
        $this->db->join('teachers', 'import_grades_batch.teacher_id = teachers.id ', 'LEFT');
        $this->db->join('classes', 'import_grades_batch.class_id = classes.id ', 'LEFT');
        $this->db->join('sections', 'import_grades_batch.section_id = sections.id ', 'LEFT');
        $this->db->join('subjects', 'import_grades_batch.subject_id = subjects.id', 'LEFT'); 
        $this->db->where('import_grades_batch.session_id', $this->current_session ); 
         
        $this->db->order_by('import_grades_batch.created_at','DESC');
        $this->db->limit('10'); 
        $query = $this->db->get(); 
        
        return $query->result_array(); 
         
    }

    public function getteacherswhosubmit() {

        /*  SELECT
        `import_grades_batch`.*, `teachers`.`name`, `teachers`.`middlename`, `teachers`.`lastname`, `classes`.`class`, `sections`.`section`, `subjects`.`name`
        FROM
        `import_grades_batch`
        LEFT JOIN `teachers` ON `teachers`.`id` = `import_grades_batch`.`teacher_id`
        LEFT JOIN `classes` ON `classes`.`id` = `import_grades_batch`.`class_id`
        LEFT JOIN `sections` ON `sections`.`id` = `import_grades_batch`.`section_id`
        LEFT JOIN `subjects` ON `subjects`.`id` = `import_grades_batch`.`subject_id`
        */

        $this->db->select(' `import_grades_batch`.*');
        $this->db->select(' `teachers`.`name` as firstname, `teachers`.`middlename`, `teachers`.`lastname`');
        $this->db->select(' `classes`.`class` as classname');
        $this->db->select(' `sections`.`section` as sectionname');
        $this->db->select(' `subjects`.`name`  as subjectname');
        $this->db->from('import_grades_batch');
        $this->db->join('teachers', 'import_grades_batch.teacher_id = teachers.id ', 'LEFT');
        $this->db->join('classes', 'import_grades_batch.class_id = classes.id ', 'LEFT');
        $this->db->join('sections', 'import_grades_batch.section_id = sections.id ', 'LEFT');
        $this->db->join('subjects', 'import_grades_batch.subject_id = subjects.id', 'LEFT'); 
        $this->db->where('import_grades_batch.session_id', $this->current_session ); 
          
        $query = $this->db->get(); 
        
        return $query->result_array(); 
         
    }

    public function getdashboardsubmitcount() {

        $return_array = array();
        /*
        SELECT
        import_grades_details.batch_id,
        import_grades_details.approved
        FROM
        import_grades_batch
        LEFT JOIN import_grades_details on import_grades_batch.id = import_grades_details.batch_id
        WHERE
        import_grades_batch.session_id = '13'
        AND import_grades_batch.published = '1'
        AND import_grades_details.approved = '0'
        GROUP BY import_grades_details.batch_id
        */

        $this->db->select('import_grades_details.batch_id');
        $this->db->select('import_grades_details.approved');

        $this->db->from('import_grades_batch');
        $this->db->join('import_grades_details', 'import_grades_batch.id = import_grades_details.batch_id ', 'LEFT'); 
        $this->db->where('import_grades_batch.session_id', $this->current_session ); 
        $this->db->where('import_grades_batch.published', '1' ); 
        $this->db->where('import_grades_details.approved', '0' ); 
        $this->db->group_by('import_grades_details.batch_id'); 
          
        $query = $this->db->get(); 
        $result_array =  $query->result_array(); 
        $pending_count = count( $result_array );
        $return_array['pending_count'] = $pending_count;

        /*
        SELECT
        import_grades_details.batch_id,
        import_grades_details.approved,
        import_grades_batch.published
        FROM
        import_grades_batch
        LEFT JOIN import_grades_details on import_grades_batch.id = import_grades_details.batch_id
        WHERE
        import_grades_batch.session_id = '13'
        AND import_grades_batch.published = '1'
        GROUP BY import_grades_details.batch_id
        */

        $this->db->select('import_grades_details.batch_id');
        $this->db->select('import_grades_details.approved');

        $this->db->from('import_grades_batch');
        $this->db->join('import_grades_details', 'import_grades_batch.id = import_grades_details.batch_id ', 'LEFT'); 
        $this->db->where('import_grades_batch.session_id', $this->current_session ); 
        $this->db->where('import_grades_batch.published', '1' ); 
        $this->db->where('import_grades_details.approved', '0' ); 
        $this->db->group_by('import_grades_details.batch_id'); 
          
        $query2 = $this->db->get(); 
        $result_array2 =  $query2->result_array(); 
        $all_count = count( $result_array2 );
        $return_array['all_count'] = $all_count;

        return $return_array; 
         
    }

    public function getbydepartment( $department_id  ) {
        $this->db->select('import_grades_batch.id, import_grades_batch.class_id, import_grades_batch.section_id, import_grades_batch.quarter, import_grades_batch.subject_id, import_grades_batch.created_at, class, section, subjects.name, published,  import_grades_batch.teacher_id')->from('import_grades_batch');
        $this->db->join('classes', 'import_grades_batch.class_id = classes.id ');
        $this->db->join('sections', 'import_grades_batch.section_id = sections.id ', 'LEFT');
        $this->db->join('subjects', 'import_grades_batch.subject_id = subjects.id'); 
        $this->db->where('published', 1 ); 
        $this->db->where('import_grades_batch.session_id', $this->current_session ); 
        $this->db->where('subjects.department_id', $department_id );  
        $this->db->order_by('import_grades_batch.id','DESC');
        
        $query = $this->db->get();
    /*  print_r( $query );
        die(); */
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array(); 
        }
    }

    public function getteacherswhosubmittop10bydepartment( $department_id ) {  

        $this->db->select(' `import_grades_batch`.*');
        $this->db->select(' `teachers`.`name` as firstname, `teachers`.`middlename`, `teachers`.`lastname`');
        $this->db->select(' `classes`.`class` as classname');
        $this->db->select(' `sections`.`section` as sectionname');
        $this->db->select(' `subjects`.`name`  as subjectname');
        $this->db->from('import_grades_batch');
        $this->db->join('teachers', 'import_grades_batch.teacher_id = teachers.id ', 'LEFT');
        $this->db->join('classes', 'import_grades_batch.class_id = classes.id ', 'LEFT');
        $this->db->join('sections', 'import_grades_batch.section_id = sections.id ', 'LEFT');
        $this->db->join('subjects', 'import_grades_batch.subject_id = subjects.id', 'LEFT'); 
        $this->db->where('import_grades_batch.session_id', $this->current_session ); 
        $this->db->where('subjects.department_id', $department_id );  
         
        $this->db->order_by('import_grades_batch.created_at','DESC');
        $this->db->limit('10'); 
        $query = $this->db->get(); 
        
        return $query->result_array(); 
         
    }

    
    public function getbyteachersessionquarter( $teacher_id = null, $session_id=null, $quarter=null ) {
          $this->db->select('
            ANY_VALUE(import_grades_batch.id) as id,
            ANY_VALUE(import_grades_batch.class_id) as class_id,
            ANY_VALUE(import_grades_batch.section_id) as section_id,
            ANY_VALUE(import_grades_batch.quarter) as quarter,
            ANY_VALUE(import_grades_batch.subject_id) as subject_id,
            ANY_VALUE(import_grades_batch.created_at) as created_at,
            ANY_VALUE(classes.class) as class,
            ANY_VALUE(sections.section) as section,
            ANY_VALUE(subjects.name) as name,
            ANY_VALUE(import_grades_batch.published) as published,
            ANY_VALUE(import_grades_batch.teacher_id) as teacher_id,
            ANY_VALUE(classes.id) as classes_id,
            ANY_VALUE(sections.id) as sections_id,
            ANY_VALUE(import_grades_batch.session_id) as session_id')->from('import_grades_batch');
      /*  $this->db->select('import_grades_batch.id, import_grades_batch.class_id, import_grades_batch.section_id, import_grades_batch.quarter, import_grades_batch.subject_id, import_grades_batch.created_at, class, section, subjects.name, published,  import_grades_batch.teacher_id, import_grades_batch.session_id')->from('import_grades_batch');*/
        $this->db->join('classes', 'import_grades_batch.class_id = classes.id ');
        $this->db->join('sections', 'import_grades_batch.section_id = sections.id ', 'LEFT');
        $this->db->join('subjects', 'import_grades_batch.subject_id = subjects.id');
        
        if( $quarter != null ){
            $this->db->where('import_grades_batch.quarter', $quarter );
        } 

        if( $teacher_id != null ){
            $this->db->where('import_grades_batch.teacher_id', $teacher_id );
        } 

        if( $session_id ){ 
            $this->db->where('import_grades_batch.session_id', $session_id ); 
        } else {
            $this->db->where('import_grades_batch.session_id', $this->current_session ); 
        } 
            
        $this->db->group_by('classes_id');
        $this->db->group_by('sections_id');  
        $this->db->order_by('id','DESC');
     
        $query = $this->db->get();
    /*  print_r( $query );
        die(); */
        
        return $query->result_array(); 
        
    }

    public function getbyteachersessionquartersubject( $teacher_id = null, $session_id=null, $quarter=null, $subject_id=null ) {


        $this->db->select(' `import_grades_batch`.*');
        $this->db->select(' `teachers`.`name` as firstname, `teachers`.`middlename`, `teachers`.`lastname`');
        $this->db->select(' `classes`.`class` as classname');
        $this->db->select(' `sections`.`section` as sectionname');
        $this->db->select(' `subjects`.`name`  as subjectname');
        $this->db->from('import_grades_batch');
        $this->db->join('teachers', 'import_grades_batch.teacher_id = teachers.id ', 'LEFT');
        $this->db->join('classes', 'import_grades_batch.class_id = classes.id ', 'LEFT');
        $this->db->join('sections', 'import_grades_batch.section_id = sections.id ', 'LEFT');
        $this->db->join('subjects', 'import_grades_batch.subject_id = subjects.id', 'LEFT');  

        if( $quarter != null ){
            $this->db->where('import_grades_batch.quarter', $quarter );
        } 

        if( $teacher_id != null ){
            $this->db->where('import_grades_batch.teacher_id', $teacher_id );
        } 


        if( $subject_id != null ){
            $this->db->where('import_grades_batch.subject_id', $subject_id );
        } 

        if( $session_id ){ 
            $this->db->where('import_grades_batch.session_id', $session_id ); 
        } else {
            $this->db->where('import_grades_batch.session_id', $this->current_session ); 
        } 
            

        $this->db->order_by('import_grades_batch.created_at','DESC');
          
        $query = $this->db->get(); 
        
        return $query->row_array(); 
           
        
    }
	
	public function getbyID( $id ) {
        $this->db->select('import_grades_batch.id, import_grades_batch.class_id, import_grades_batch.section_id, import_grades_batch.quarter, import_grades_batch.subject_id, import_grades_batch.created_at, class, section, subjects.name, published,  import_grades_batch.teacher_id, import_grades_batch.session_id,import_grades_batch.template')->from('import_grades_batch');
        $this->db->join('classes', 'import_grades_batch.class_id = classes.id ');
        $this->db->join('sections', 'import_grades_batch.section_id = sections.id ', 'LEFT');
        $this->db->join('subjects', 'import_grades_batch.subject_id = subjects.id');
        $this->db->where('import_grades_batch.id', $id);
        $query = $this->db->get();
        
		return $query->row_array(); 
    }

    public function get_latest_quarter( $session_id=null){
        if( empty($session_id ) ){
            $session_id = $this->current_session;
        }
        $this->db->select('quarter');
        $this->db->from('import_grades_batch');
        $this->db->where('import_grades_batch.session_id', $session_id);
        $this->db->order_by('quarter','DESC');
        $this->db->limit('1');
        $query = $this->db->get();

        $result = $query->row_array(); 

        $quarter = isset($result['quarter'])?$result['quarter']:'';
        return $quarter;

    }

     public function getBySessionQuarter( $session_id=null,$quarter=null) {
        if(empty( $session_id )){
            $session_id  = $this->current_session;
        }
        $this->db->select('import_grades_batch.id, import_grades_batch.class_id, import_grades_batch.section_id, import_grades_batch.quarter, import_grades_batch.subject_id, import_grades_batch.created_at, class, section, subjects.name, published,  import_grades_batch.teacher_id')->from('import_grades_batch');
        $this->db->join('classes', 'import_grades_batch.class_id = classes.id ');
        $this->db->join('sections', 'import_grades_batch.section_id = sections.id ', 'LEFT');
        $this->db->join('subjects', 'import_grades_batch.subject_id = subjects.id');
        $this->db->where('import_grades_batch.session_id', $session_id ); 
        $this->db->where('published', 1 );
         if( $quarter ){
                $this->db->where('import_grades_batch.quarter', $quarter );
        }
         $this->db->order_by('import_grades_batch.id','DESC');
       
       
        $query = $this->db->get();
        
        return $query->result_array(); 
    }

        public function getBySessionQuarterStatus( $session_id=null,$quarter=null, $status=null) {
        if(empty( $session_id )){
            $session_id  = $this->current_session;
        }
       /* $this->db->select('import_grades_batch.id, import_grades_batch.class_id, import_grades_batch.section_id, import_grades_batch.quarter, import_grades_batch.subject_id, import_grades_batch.created_at, class, section, subjects.name, published,  import_grades_batch.teacher_id, 
            ( SELECT COUNT(import_grades_details.id) FROM import_grades_details WHERE approved = "0"  and import_grades_batch.id = import_grades_details.batch_id ) AS pending_status, ( SELECT COUNT(import_grades_details.id) FROM import_grades_details WHERE approved = "1" and import_grades_batch.id = import_grades_details.batch_id   ) AS  approved_status' )->from('import_grades_batch');
        $this->db->join('classes', 'import_grades_batch.class_id = classes.id ');
        $this->db->join('sections', 'import_grades_batch.section_id = sections.id ', 'LEFT');
        $this->db->join('subjects', 'import_grades_batch.subject_id = subjects.id');
        $this->db->where('import_grades_batch.session_id', $session_id ); 
        $this->db->where('published', 1 );
         if( $quarter ){
                $this->db->where('import_grades_batch.quarter', $quarter );
        }
         $this->db->order_by('import_grades_batch.id','DESC');*/

         if( $quarter  ){
            if( $status ){
                $binary = $status == 'pending'?'0':"1";
                $query = $this->db->query("SELECT import_grades_batch.id, ( SELECT COUNT(import_grades_details.id) FROM import_grades_details WHERE approved = '0' AND import_grades_batch.id = import_grades_details.batch_id ) AS pending_status,
                import_grades_batch.id, import_grades_batch.class_id, import_grades_batch.section_id, import_grades_batch.quarter, import_grades_batch.subject_id, import_grades_batch.created_at, class, section, subjects.name, published,  import_grades_batch.teacher_id, teachers.name as tfirstname, 
                    teachers.lastname as tlastname,
                ( SELECT COUNT(import_grades_details.id) FROM import_grades_details WHERE approved = '1' AND import_grades_batch.id = import_grades_details.batch_id )  AS approved_status
                FROM import_grades_batch 
                JOIN import_grades_details ON import_grades_batch.id = import_grades_details.batch_id 
                JOIN classes ON import_grades_batch.class_id = classes.id 
                LEFT JOIN sections ON import_grades_batch.section_id = sections.id
                JOIN subjects ON import_grades_batch.subject_id = subjects.id
                JOIN teachers ON import_grades_batch.teacher_id = teachers.id
                WHERE import_grades_batch.session_id  = ".$session_id." AND published = '1'
                AND import_grades_batch.quarter = ".$quarter." AND import_grades_details.approved = ".$binary." group by import_grades_batch.id ORDER BY import_grades_batch.id DESC  " );
            } else {
                 $query = $this->db->query("SELECT import_grades_batch.id, ( SELECT COUNT(import_grades_details.id) FROM import_grades_details WHERE approved = '0' AND import_grades_batch.id = import_grades_details.batch_id ) AS pending_status,
                import_grades_batch.id, import_grades_batch.class_id, import_grades_batch.section_id, import_grades_batch.quarter, import_grades_batch.subject_id, import_grades_batch.created_at, class, section, subjects.name, published,  import_grades_batch.teacher_id, teachers.name as tfirstname, 
                    teachers.lastname as tlastname,
                ( SELECT COUNT(import_grades_details.id) FROM import_grades_details WHERE approved = '1' AND import_grades_batch.id = import_grades_details.batch_id )  AS approved_status
                FROM import_grades_batch 
                JOIN import_grades_details ON import_grades_batch.id = import_grades_details.batch_id 
                JOIN classes ON import_grades_batch.class_id = classes.id 
                LEFT JOIN sections ON import_grades_batch.section_id = sections.id
                JOIN subjects ON import_grades_batch.subject_id = subjects.id
                JOIN teachers ON import_grades_batch.teacher_id = teachers.id
                WHERE import_grades_batch.session_id  = ".$session_id." AND published = '1'
                AND import_grades_batch.quarter = ".$quarter." AND import_grades_details.approved = '1' group by import_grades_batch.id ORDER BY import_grades_batch.id DESC  "  );
            }
        
        } else {
             if( $status ){
                $binary = $status == 'pending'?'0':"1";
                 $query = $this->db->query("SELECT import_grades_batch.id, ( SELECT COUNT(import_grades_details.id) FROM import_grades_details WHERE approved = '0' AND import_grades_batch.id = import_grades_details.batch_id ) AS pending_status,
                    import_grades_batch.id, import_grades_batch.class_id, import_grades_batch.section_id, import_grades_batch.quarter, import_grades_batch.subject_id, import_grades_batch.created_at, class, section, subjects.name, published,  import_grades_batch.teacher_id, teachers.name as tfirstname, 
                teachers.lastname as tlastname,
                ( SELECT COUNT(import_grades_details.id) FROM import_grades_details WHERE approved = '1' AND import_grades_batch.id = import_grades_details.batch_id )  AS approved_status
                FROM import_grades_batch 
                JOIN import_grades_details ON import_grades_batch.id = import_grades_details.batch_id 
                JOIN classes ON import_grades_batch.class_id = classes.id 
                LEFT JOIN sections ON import_grades_batch.section_id = sections.id
                JOIN subjects ON import_grades_batch.subject_id = subjects.id
                JOIN teachers ON import_grades_batch.teacher_id = teachers.id
                WHERE import_grades_batch.session_id  = ".$session_id." AND published = '1'
                AND import_grades_details.approved = ".$binary."  group by import_grades_batch.id ORDER BY import_grades_batch.id DESC ");
            } else {
                $query = $this->db->query("SELECT import_grades_batch.id, ( SELECT COUNT(import_grades_details.id) FROM import_grades_details WHERE approved = '0' AND import_grades_batch.id = import_grades_details.batch_id ) AS pending_status,
                    import_grades_batch.id, import_grades_batch.class_id, import_grades_batch.section_id, import_grades_batch.quarter, import_grades_batch.subject_id, import_grades_batch.created_at, class, section, subjects.name, published,  import_grades_batch.teacher_id, teachers.name as tfirstname, 
                teachers.lastname as tlastname,
                ( SELECT COUNT(import_grades_details.id) FROM import_grades_details WHERE approved = '1' AND import_grades_batch.id = import_grades_details.batch_id )  AS approved_status
                FROM import_grades_batch 
                JOIN import_grades_details ON import_grades_batch.id = import_grades_details.batch_id 
                JOIN classes ON import_grades_batch.class_id = classes.id 
                LEFT JOIN sections ON import_grades_batch.section_id = sections.id
                JOIN subjects ON import_grades_batch.subject_id = subjects.id
                JOIN teachers ON import_grades_batch.teacher_id = teachers.id
                WHERE import_grades_batch.session_id  = ".$session_id." AND published = '1' group by import_grades_batch.id
                ORDER BY import_grades_batch.id DESC ");

            }

           
        }
  
        return $query->result_array(); 
        
    }

    public function getBySessionQuarterv2( $session_id=null,$quarter=null ) {
        if(empty( $session_id )){
            $session_id  = $this->current_session;
        }
        $this->db->select('import_grades_batch.id, import_grades_batch.class_id, import_grades_batch.section_id, import_grades_batch.quarter, import_grades_batch.subject_id, import_grades_batch.created_at, class, section, subjects.name, published,  import_grades_batch.teacher_id,import_grades_batch.teacher_id, teachers.name as tfirstname, teachers.lastname as tlastname')->from('import_grades_batch');
        $this->db->join('classes', 'import_grades_batch.class_id = classes.id ');
        $this->db->join('sections', 'import_grades_batch.section_id = sections.id ', 'LEFT');
        $this->db->join('subjects', 'import_grades_batch.subject_id = subjects.id');
        $this->db->join('teachers', 'import_grades_batch.teacher_id = teachers.id');
        $this->db->where('import_grades_batch.session_id', $session_id ); 
        $this->db->where('published', 1 );
         if( $quarter ){
                $this->db->where('import_grades_batch.quarter', $quarter );
        }
         $this->db->order_by('import_grades_batch.id','DESC');
       
       
        $query = $this->db->get();
        
        return $query->result_array(); 
    }

}
