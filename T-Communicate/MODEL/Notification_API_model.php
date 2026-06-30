<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Notification_API_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_date = $this->setting_model->getDateYmd();
    } 

    public function add($data) {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('notification_api', $data); 
        } else {
           
            $query=$this->db->get_where('notification_api',
                array( 
                    'notification_id'=>$data['notification_id'],
                    'type'=>$data['type'],
                    'publish_date'=>$data['publish_date']
            )); //check if 'id' field is existed or not 

            if( $query->num_rows() > 0 ){  // id found stop
            
                $this->db->where('notification_id', $data['notification_id']);
                $this->db->update('notification_api', $data); 
            } else {// id not found continue..  
                $this->db->insert('notification_api', $data);     
           }    
 
        }
    }

    function get_all(){
        $this->db->select('*');
        $this->db->from('notification_api');
        $this->db->where('status', 'pending');
        $query = $this->db->get();        
        return $query->result();
    }

    

}
