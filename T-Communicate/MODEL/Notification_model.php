<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Notification_model extends CI_Model {

    public $current_session;
    public function __construct() {
        $this->load->model('setting_model');
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
        $this->db->select('*')->from('send_notification');
        $this->db->where('send_notification.session_id', $this->current_session);
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('publish_date', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array(); 
        } else {
            return $query->result_array();
        }
    }

    public function getNotificationForStudent($studentid = null) {
        $date = date('Y-m-d');
        $query = $this->db->query("SELECT
        send_notification.id,send_notification.title,send_notification.publish_date,send_notification.date,send_notification.message,
        IF (read_notification.id IS NULL,'unread','read') as notification_id
        FROM send_notification
        LEFT JOIN read_notification ON send_notification.id = read_notification.notification_id and read_notification.student_id=".$this->db->escape($studentid)." where send_notification.visible_student='Yes' order by send_notification.publish_date desc");
                return $query->result_array(); 
    }
	
    public function getNotificationForParent($parentid = null) {
        $date = date('Y-m-d');
        $query = $this->db->query("SELECT
        send_notification.id,send_notification.title,send_notification.publish_date,send_notification.date,send_notification.message,
        IF (read_notification.id IS NULL,'unread','read') as notification_id
        FROM send_notification
        LEFT JOIN read_notification ON send_notification.id = read_notification.notification_id and read_notification.parent_id=".$this->db->escape($parentid)." where send_notification.visible_parent='Yes' order by send_notification.publish_date desc");
                return $query->result_array(); 
    }
    

    public function countUnreadNotificationStudent($studentid = null) {
        $date = date('Y-m-d');
        $query = $this->db->query("select * from (SELECT  IF (read_notification.id IS NULL,'unread','read') as notification_id FROM send_notification LEFT JOIN read_notification ON send_notification.id = read_notification.notification_id and read_notification.student_id=".$this->db->escape($studentid)." where  send_notification.visible_student='Yes') final where notification_id ='unread'");
        return $query->num_rows(); 
    }

    public function countUnreadNotificationParent($parentid = null) {
        $date = date('Y-m-d');
        $query = $this->db->query("select * from (SELECT  IF (read_notification.id IS NULL,'unread','read') as notification_id FROM send_notification LEFT JOIN read_notification ON send_notification.id = read_notification.notification_id and read_notification.parent_id=".$this->db->escape($parentid)." where  send_notification.visible_parent='Yes') final where notification_id ='unread'");
        return $query->num_rows(); 
    }

    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('send_notification');
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
            $this->db->update('send_notification', $data);
        } else {
            $data = array_merge( array('session_id' => $this->current_session ), $data );
            $this->db->insert('send_notification', $data); 
            $insert_id = $this->db->insert_id();  

            //administrative notification 
            $sms_message = "System Alert: LVAA has created a new SMS BLAST!, please verify for the GSM Router!";
            $insert_id = true;
            
            $phone[] = false; //rubin 
            // $phone[] = "09666608651"; //rubin
            // $phone[] = "09169781012"; //johna
            // $phone[] = "09276669961";
            // $phone[] = "09164812418";
            // $phone[] = "09162770088"; //joan
            // $phone[] = "09356794690"; //angel
            foreach ($phone as $phonekey => $phonevalue) {

                 $data_sms[] = array(
                    'notification_id' => $insert_id,
                    'sms_msg' => $sms_message,
                    'sms_number' => $phonevalue, 
                    'sms_status' => 'idle',
                    'sms_teacher' => 'no',
                    'sms_parent' => 'no' 
                );   
            } 
                    
            if( isset($data_sms) && $data_sms ){
                $this->db->insert_batch('sms_que', $data_sms); 
            }
 
            return $insert_id;
        }
    }

    public function updateStatus($notification_id, $studentid) {
        $this->db->where('notification_id', $notification_id);
        $this->db->where('student_id', $studentid);
        $q = $this->db->get('read_notification');
        if ($q->num_rows() > 0) {
            return true;
        } else {
            $data = array(
                'notification_id' => $notification_id,
                'student_id' => $studentid
            );
            $this->db->insert('read_notification', $data);
        }
    }

    public function updateStatusforParent($notification_id, $parentid) {
        $this->db->where('notification_id', $notification_id);
        $this->db->where('parent_id', $parentid);
        $q = $this->db->get('read_notification');
        if ($q->num_rows() > 0) {
            return true;
        } else {
            $data = array(
                'notification_id' => $notification_id,
                'parent_id' => $parentid
            );
            $this->db->insert('read_notification', $data);
        }
    }

    function add_exam_schedule($data) {
        $this->db->where('exam_id', $data['exam_id']);
        $this->db->where('teacher_subject_id', $data['teacher_subject_id']);
        $q = $this->db->get('exam_schedules');
        if ($q->num_rows() > 0) {
            $result = $q->row_array();
            $this->db->where('id', $result['id']);
            $this->db->update('exam_schedules', $data);
        } else {
            $this->db->insert('exam_schedules', $data);
        }
    }

    public function get_limit($id = null, $limit = 10) {
        $this->db->select()->from('send_notification');
        if ($id != null) {
            $this->db->where('id', $id);
        }  
        
        $this->db->limit( $limit );
        $this->db->order_by('publish_date', 'DESC');

        $query = $this->db->get();
    
        return $query->result_array();
         
    }

    // new function to get notifications for parent by grade level
    // the old function was not filtering by grade level (function getNotificationForParent)
    public function getNotificationForParentByGradeLevel($student_id = null) {
        $query = $this->db->query("
            SELECT 
                sn.id,
                sn.title,
                sn.publish_date,
                sn.date,
                sn.message,
                rn.parent_id,
                IF(rn.id IS NULL, 'unread', 'read') AS notification_id,
                sn.custom_parent,
                ss.custom_parent AS matched_parent,
                rn.parent_id
            FROM send_notification sn
            LEFT JOIN read_notification rn 
                ON sn.id = rn.notification_id
            AND rn.parent_id = " . $this->db->escape($student_id) . "
            JOIN (
                SELECT CONCAT(class_id, '-', section_id) AS custom_parent, student_id
                FROM student_session
                WHERE student_id = " . $this->db->escape($student_id) ."
                and session_id = " . $this->db->escape($this->current_session) . "
            ) AS ss 
                ON FIND_IN_SET(ss.custom_parent, sn.custom_parent) > 0
            WHERE sn.visible_parent = 'Yes'
            ORDER BY sn.publish_date DESC
        ");
        return $query->result_array();
    }

    public function get_all($student_id = null) {
        $query = $this->db->query("
            SELECT 
                sn.id,
                sn.title,
                sn.publish_date,
                sn.date,
                sn.message,
                rn.parent_id,
                IF(rn.id IS NULL, 'unread', 'read') AS notification_id,
                sn.custom_parent,
                ss.custom_parent AS matched_parent,
                rn.parent_id
            FROM send_notification sn
            LEFT JOIN read_notification rn 
                ON sn.id = rn.notification_id
            AND rn.parent_id = " . $this->db->escape($student_id) . "
            JOIN (
                SELECT CONCAT(class_id, '-', section_id) AS custom_parent, student_id
                FROM student_session
                WHERE student_id = " . $this->db->escape($student_id) . "
                and session_id = " . $this->db->escape($this->current_session) . "
            ) AS ss 
                ON FIND_IN_SET(ss.custom_parent, sn.custom_parent) > 0
            WHERE sn.visible_parent = 'Yes'
            ORDER BY sn.publish_date DESC
            LIMIT 10
        ");
        return $query->result_array();
    }

    // public function getTeacherNotifications($teacher_id = null)
    // {
    //     $this->db->select('
    //         sn.id,
    //         sn.title,
    //         sn.publish_date,
    //         sn.date,
    //         sn.message,
    //         sn.custom_teacher,
    //         sn.visible_teacher,
    //         ts.teacher_id,
    //         sn.approved,
    //         sn.created_id,
    //         cs.class_id AS teacher_custom_teacher,
    //         classes.class,
    //         sections.section
    //     ');
    //     $this->db->from('send_notification sn');
    //     $this->db->join('teacher_subjects ts', 'ts.teacher_id = '.$this->db->escape($teacher_id).' AND ts.session_id = '.$this->db->escape($this->current_session) , 'inner');
    //     $this->db->join('class_sections cs', 'cs.id = ts.class_section_id', 'inner');
    //     $this->db->join('classes', 'cs.class_id = classes.id', 'inner');
    //     $this->db->join('sections', 'cs.section_id = sections.id', 'inner');
    //     $this->db->where('sn.visible_teacher', 'Yes');

    //     // Support notifications targeted to specific class_id OR 'all'
    //     $this->db->where('(
    //         FIND_IN_SET(cs.class_id, sn.custom_teacher) > 0 
    //         OR FIND_IN_SET("all", sn.custom_teacher) > 0
    //     )', null, false);

    //     $this->db->order_by('sn.publish_date', 'DESC');
    //     $this->db->order_by('sn.id', 'DESC');

    //     $query = $this->db->get();
    //     return $query->result_array();
    // }


    // public function getTeacherNotifications($teacher_id = null)
    // {
    //     $this->db->select('
    //         sn.id,
    //         sn.title,
    //         sn.publish_date,
    //         sn.date,
    //         sn.message,
    //         sn.custom_teacher,
    //         sn.visible_teacher,
    //         sn.visible_student,
    //         sn.visible_parent,
    //         ts.teacher_id,
    //         sn.approved,
    //         sn.created_id,
    //         GROUP_CONCAT(DISTINCT classes.class SEPARATOR ", ") AS classes,
    //         GROUP_CONCAT(DISTINCT sections.section SEPARATOR ", ") AS sections
    //     ', false);
        
    //     $this->db->from('send_notification sn');
    //     $this->db->join(
    //         'teacher_subjects ts',
    //         'ts.teacher_id = '.$this->db->escape($teacher_id).' 
    //         AND ts.session_id = '.$this->db->escape($this->current_session),
    //         'inner'
    //     );
    //     $this->db->join('class_sections cs', 'cs.id = ts.class_section_id', 'inner');
    //     $this->db->join('classes', 'cs.class_id = classes.id', 'inner');
    //     $this->db->join('sections', 'cs.section_id = sections.id', 'inner');
        
    //     $this->db->where('sn.visible_teacher', 'Yes');

    //     // Support notifications targeted to specific class_id OR 'all'
    //     $this->db->where('(
    //         FIND_IN_SET(ts.teacher_id, sn.custom_teacher) > 0 
    //         OR FIND_IN_SET("all", sn.custom_teacher) > 0
    //     )', null, false);

    //     $this->db->group_by('sn.id');
    //     $this->db->order_by('sn.publish_date', 'DESC');
    //     $this->db->order_by('sn.id', 'DESC');

    //     $query = $this->db->get();
    //     return $query->result_array();
    // }


    public function getTeacherNotifications($teacher_id = null)
    {
        $this->db->select('
            sn.id,
            sn.title,
            sn.publish_date,
            sn.date,
            sn.message,
            sn.custom_teacher,
            sn.custom_parent,
            sn.visible_student,
            sn.visible_teacher,
            sn.visible_parent,
            ts.teacher_id,
            sn.approved,
            sn.created_id,
            sn.created_by,
            tcs.teacher_class_section
        ');
        $this->db->from('send_notification sn');
        $this->db->join(
            'teacher_subjects ts',
            'ts.teacher_id = '.$this->db->escape($teacher_id).' 
            AND ts.session_id = '.$this->db->escape($this->current_session),
            'inner'
        );
        $this->db->join('class_sections cs', 'cs.id = ts.class_section_id');
        $this->db->join('classes', 'classes.id = cs.class_id');
        $this->db->join('sections', 'sections.id = cs.section_id');

        $subquery = "(SELECT 
                        ts2.session_id,
                        GROUP_CONCAT(DISTINCT CONCAT(cs2.class_id, '-', cs2.section_id) 
                                    ORDER BY cs2.class_id, cs2.section_id ASC) AS teacher_class_section
                    FROM teacher_subjects ts2
                    JOIN class_sections cs2 ON cs2.id = ts2.class_section_id
                    JOIN classes c2 ON cs2.class_id = c2.id
                    JOIN sections s2 ON cs2.section_id = s2.id
                    WHERE ts2.teacher_id = ".$this->db->escape($teacher_id)." 
                        AND ts2.session_id = ".$this->db->escape($this->current_session)."
                    GROUP BY ts2.session_id
                    ) tcs";

        $this->db->join($subquery, 'tcs.session_id = ts.session_id', 'inner', false);

        // conditions
        $this->db->where("(
            FIND_IN_SET(ts.teacher_id, sn.custom_teacher) > 0 
            OR sn.created_id = ts.teacher_id
            OR FIND_IN_SET(CONCAT(cs.class_id, '-', cs.section_id), sn.custom_parent) > 0
        )", null, false);

        $this->db->where("(sn.visible_parent = 'yes' OR sn.visible_teacher = 'yes')", null, false);

        // group and order
        $this->db->group_by('sn.id');
        $this->db->order_by('sn.publish_date DESC, sn.id DESC');

        $query = $this->db->get();
        return $query->result_array();
    }


    public function getTeacherNotificationswithLimit($teacher_id = null, $limit = 10)
    {
        $this->db->select('
            sn.id,
            sn.title,
            sn.publish_date,
            sn.date,
            sn.message,
            sn.custom_teacher,
            sn.custom_parent,
            sn.visible_student,
            sn.visible_teacher,
            sn.visible_parent,
            ts.teacher_id,
            sn.approved,
            sn.created_id,
            sn.created_by,
            tcs.teacher_class_section
        ');
        $this->db->from('send_notification sn');
        $this->db->join(
            'teacher_subjects ts',
            'ts.teacher_id = '.$this->db->escape($teacher_id).' 
            AND ts.session_id = '.$this->db->escape($this->current_session),
            'inner'
        );
        $this->db->join('class_sections cs', 'cs.id = ts.class_section_id');
        $this->db->join('classes', 'classes.id = cs.class_id');
        $this->db->join('sections', 'sections.id = cs.section_id');

        $subquery = "(SELECT 
                        ts2.session_id,
                        GROUP_CONCAT(DISTINCT CONCAT(cs2.class_id, '-', cs2.section_id) 
                                    ORDER BY cs2.class_id, cs2.section_id ASC) AS teacher_class_section
                    FROM teacher_subjects ts2
                    JOIN class_sections cs2 ON cs2.id = ts2.class_section_id
                    JOIN classes c2 ON cs2.class_id = c2.id
                    JOIN sections s2 ON cs2.section_id = s2.id
                    WHERE ts2.teacher_id = ".$this->db->escape($teacher_id)." 
                        AND ts2.session_id = ".$this->db->escape($this->current_session)."
                    GROUP BY ts2.session_id
                    ) tcs";

        $this->db->join($subquery, 'tcs.session_id = ts.session_id', 'inner', false);

        // conditions
        $this->db->where("(
            FIND_IN_SET(ts.teacher_id, sn.custom_teacher) > 0 
            OR sn.created_id = ts.teacher_id
            OR FIND_IN_SET(CONCAT(cs.class_id, '-', cs.section_id), sn.custom_parent) > 0
        )", null, false);

        $this->db->where("(sn.visible_parent = 'yes' OR sn.visible_teacher = 'yes')", null, false);

        // group, order, and limit
        $this->db->group_by('sn.id');
        $this->db->order_by('sn.publish_date DESC, sn.id DESC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result_array();
    }




}
