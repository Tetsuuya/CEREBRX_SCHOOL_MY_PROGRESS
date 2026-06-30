<?php



if (!defined('BASEPATH'))

    exit('No direct script access allowed');



class Notification extends CI_Controller {



    function __construct() {

        parent::__construct();

        $this->load->helper('file');

        $this->lang->load('message', 'english');

        $this->load->library('auth');

        $this->auth->is_logged_in_teacher();

        $this->load->model('Notification_API_model','notification_api_model');

        $this->load->model('Sms_model','SMSM'); 

    }



      function index() {
        $this->session->set_userdata('top_menu', 'Communicate');
        $this->session->set_userdata('sub_menu', 'notification/index');
		$student_session_data = $this->session->userdata("student");
        $teacher_id = $student_session_data['teacher_id'];
        $data['teacher_id'] = $teacher_id;

        $data['title'] = 'Notifications';
        // $notifications = $this->notification_model->get();
        $notifications = $this->notification_model->getTeacherNotifications($teacher_id);
        // printx($notifications);
        $data['notificationlist'] = $notifications;
        $this->load->view('layout/teacher/header', $data);
        $this->load->view('teacher/notification/notificationList', $data);
        $this->load->view('layout/teacher/footer', $data);
    }



    function add() {

        $this->session->set_userdata('top_menu', 'Communicate');

        $this->session->set_userdata('sub_menu', 'notification/add');

        $data['title'] = 'Add Notification';

        $data['title_list'] = 'Notification List';



        $student_session_data = $this->session->userdata("student");

        $teacher_id = $student_session_data['teacher_id'];

        // $class = $this->class_model->get_calss_section_subject( $teacher_id );  

        // $class = $this->class_model->get_class_section_subject_by_teacher( $teacher_id ); 

        $class = $this->class_model->get_class_section_subject_by_teachersession( $teacher_id );
        
        // modification: sort classlist in ascending order (Grade 7 -> Grade 10)
        usort($class, function($a, $b) {
            $class_cmp = strnatcasecmp($a['class_name'], $b['class_name']);
            if ($class_cmp === 0) {
                return strnatcasecmp($a['section_name'], $b['section_name']);
            }
            return $class_cmp;
        });

        $data['classlist'] = $class;



        $this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');

        $this->form_validation->set_rules('message', 'Message', 'trim|required|xss_clean'); 

        $this->form_validation->set_rules('date', 'Notice Date', 'trim|required|xss_clean');

        $this->form_validation->set_rules('publish_date', 'Publish Date', 'trim|required|xss_clean');

        $this->form_validation->set_rules('visible[]', 'Message To', 'required');

        if ($this->form_validation->run() == FALSE) {

        } else {

            $student = "No";

            $teacher = "No";

            $parent = "No";

            $visible = $this->input->post('visible');

            foreach ($visible as $key => $value) {

                if ($value == "student") {

                    $student = "Yes";

                } else if ($value == "teacher") {

                    $teacher = "Yes";

                } else if ($value == "parent") {

                    $parent = "Yes";

                }

            }

            $data = array(

                'message' => $this->input->post('message'),

                'title' => $this->input->post('title'),

                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),

                'created_by' => 'teacher',

                'created_id' => $teacher_id,

                'visible_student' => $student,

                'visible_teacher' => $teacher,

                'visible_parent' => $parent,

                'custom_parent' => $this->input->post('parent_custom') ,
                
                'custom_student_ids' => $this->input->post('custom_student_ids') , // added

                'publish_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('publish_date'))),

            );

			if( $teacher == 'Yes' ){

				  $data = array_merge($data, array('custom_teacher' => 'all' ));   

			} else {

				 $data = array_merge($data, array('custom_teacher' => ' ' ));   

			}

            if(  isset($parent) && $parent == 'Yes' || $parent == 'parent'  ){

                $data_stat = array(  

                    'approved' => 'no'

                );

            } else {

                $approved_date  = date('Y-m-d H:i:s');

                 $data_stat = array(  

                    'approved' => 'yes',

                    'approved_by' => 'teacher',

                    'approved_date' => $approved_date,

                    'approved_id' => $teacher_id,

                );

            }

            $data = array_merge($data, $data_stat);   



            $insert_id = $this->notification_model->add($data);

             



            $message = $this->input->post('message');

            // $sms_message = htmlentities($message);

            $sms_message = strip_tags($message);



           if( $teacher == 'Yes' && $parent == 'No' ){

                $teachers_contact = $this->teacher_model->get_teachers_number();



                if(  $teachers_contact  ){

                    foreach ($teachers_contact  as $t_key => $t_value) {

                        # code...

                        $phone = $t_value['phone'];

                        $phone = str_replace(' ', '', $phone);

                        $phone = str_replace('-', '', $phone);

                        if (strlen($phone)>10) {

                            $data_sms[] = array(

                                'notification_id' => $insert_id,

                                'sms_msg' => $sms_message,

                                'sms_number' => $phone, 

                                'sms_status' => 'idle',

                                'sms_teacher' => 'yes',

                                'sms_parent' => 'no' 

                            );  

                        } 

                    } 

                } 

                if( isset($data_sms) && $data_sms ){

                    $this->db->insert_batch('sms_que', $data_sms); 

                     // $this->SMSM->add($data_sms);

                } 

            }

            // modification: direct SMS sending for selected specific parents
            $custom_student_ids = $this->input->post('custom_student_ids');
            if (!empty($custom_student_ids)) {
                $student_ids = explode(',', $custom_student_ids);
                $this->db->select('students.guardian_phone');
                $this->db->from('students');
                $this->db->where_in('students.id', $student_ids);
                $query = $this->db->get();
                $students_contact = $query->result_array();

                if (!empty($students_contact)) {
                    $this->load->library('smsgateway');
                    foreach ($students_contact as $s_value) {
                        $phone = $s_value['guardian_phone'];
                        $phone = str_replace(' ', '', $phone);
                        $phone = str_replace('-', '', $phone);
                        if (strlen($phone) > 10) {
                            $this->smsgateway->sentNotificationSMS($sms_message, $phone);
                        }
                    }
                }
            }



           





            /*$arr_api = array(

                'notification_id' => $insert_id, 

                'send_student' =>  isset($student) ? "yes" : 'no',

                'send_teacher' => isset($teacher) ? "yes" : 'no',

                'send_parent' => isset($parent) ? "yes" : 'no',

                'status' => 'pending',

                'type' => 'notification',

                'created_by' => 'teacher',

                'created_id' => $teacher_id,

                'publish_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('publish_date')))

            );



            $this->notification_api_model->add($arr_api);*/

            $this->session->set_flashdata('msg', '<div class="alert alert-success">Notification added successfully!</div>');

            redirect('teacher/notification');

        }

        $exam_result = $this->exam_model->get();

        $data['examlist'] = $exam_result;

        $this->load->view('layout/teacher/header', $data);

        $this->load->view('teacher/notification/notificationAdd', $data);

        $this->load->view('layout/teacher/footer', $data);

    }



   function edit($id) {

        $data['id'] = $id;

        $notification = $this->db->select('*')->from('send_notification')->where('id', $id)->get()->row_array();

        $data['notification'] = $notification;



        // fetch selected student details if any

        $selected_students_json = '[]';

        if (!empty($notification['custom_student_ids'])) {

            $student_ids = explode(',', $notification['custom_student_ids']);

            $current_session_id = $this->setting_model->getCurrentSession();

            $this->db->select('students.id, students.firstname, students.lastname, students.middlename, students.guardian_name, students.guardian_phone, student_session.class_id, student_session.section_id');

            $this->db->from('student_session');

            $this->db->join('students', 'students.id = student_session.student_id');

            $this->db->where_in('student_session.student_id', $student_ids);

            $this->db->where('student_session.session_id', $current_session_id);

            $query = $this->db->get();

            $selected_students_json = json_encode($query->result_array());

        }

        $data['selected_students_json'] = $selected_students_json;



        $student_session_data = $this->session->userdata("student");

        $teacher_id = $student_session_data['teacher_id']; 

        // $class = $this->class_model->get_calss_section_subject( $teacher_id );  

        // $class = $this->class_model->get_calss_section_subject_all( $teacher_id );

        

        // $class = $this->class_model->get_class_section_subject_by_teacher( $teacher_id );

        $class = $this->class_model->get_class_section_subject_by_teachersession( $teacher_id );

        // modification: sort classlist in ascending order 
        usort($class, function($a, $b) {
            $class_cmp = strnatcasecmp($a['class_name'], $b['class_name']);
            if ($class_cmp === 0) {
                return strnatcasecmp($a['section_name'], $b['section_name']);
            }
            return $class_cmp;
        });

        $data['classlist'] = $class;

		$data['teacher_id'] = $teacher_id;

        $data['title'] = 'Edit Notification';

        $data['title_list'] = 'Notification List';

        $this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');

        $this->form_validation->set_rules('message', 'Message', 'trim|required|xss_clean'); 

        $this->form_validation->set_rules('date', 'Notice Date', 'trim|required|xss_clean');

        $this->form_validation->set_rules('publish_date', 'Publish Date', 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {

        } else {

            $student = $this->input->post('visible_to_std');

            $teacher = $this->input->post('visible_to_tea');

            $parent = $this->input->post('visible_to_par');

            $data = array(

                'id' => $id,

                'message' => $this->input->post('message'),

                'title' => $this->input->post('title'),

                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),

                'visible_student' => isset($student) ? "Yes" : 'No',

                'visible_teacher' => isset($teacher) ? "Yes" : 'No',

                'visible_parent' => isset($parent) ? "Yes" : 'No',

                'custom_parent' => $this->input->post('parent_custom') ,
                
                // modification: added for filtering
                'custom_student_ids' => $this->input->post('custom_student_ids') ,

                'publish_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('publish_date'))),

            ); 

			

			if( isset($teacher) && ($teacher == 'Yes' || $teacher == 'teacher') ){

				  $data = array_merge($data, array('custom_teacher' => 'all' ));   

			} else {

				 $data = array_merge($data, array('custom_teacher' => ' ' ));   

			}

			

            if(  isset($parent) && $parent == 'Yes' || $parent == 'parent'  ){

                $data_stat = array(  

                    'approved' => 'no'

                );  

                $current_status = $this->SMSM->check_notification($id);



                if( $current_status == 'idle' || $current_status == FALSE){

                    $this->SMSM->remove_notification($id );

                } else {

                    $this->session->set_flashdata('msg', '<div class="alert alert-danger">Notification can not be be updated anymore!</div>');

                     redirect('teacher/notification/index');



                } 

            } else {

                 $data_stat = array(  

                    'approved' => 'yes'

                );



                $current_status = $this->SMSM->check_notification($id);



                if( $current_status == 'idle' || $current_status == FALSE){

                    $this->SMSM->remove_notification($id );

                } else {

                    $this->session->set_flashdata('msg', '<div class="alert alert-danger">Notification can not be be updated anymore!</div>');

                     redirect('teacher/notification/index');



                }





            }



            $current_status = $this->SMSM->check_notification($id);



            if( $current_status == 'idle' || $current_status == FALSE){

               $data = array_merge($data, $data_stat);    

                $this->notification_model->add($data);

            } else {

                $this->session->set_flashdata('msg', '<div class="alert alert-danger">Notification can not be be updated anymore!</div>');

                 redirect('teacher/notification/index');



            } 

            



            $message = $this->input->post('message');

            // $sms_message = htmlentities($message);

            $sms_message = strip_tags($message);



            if( isset($teacher) && ($teacher == 'Yes' || $teacher == 'teacher') && (!isset($parent) || ($parent != 'Yes' && $parent != 'parent')) ){

                $teachers_contact = $this->teacher_model->get_teachers_number();



                if(  $teachers_contact  ){

                    foreach ($teachers_contact  as $t_key => $t_value) {

                        # code...

                        $phone = $t_value['phone'];

                        $phone = str_replace(' ', '', $phone);

                        $phone = str_replace('-', '', $phone);

                        if (strlen($phone)>10) {

                            $data_sms[] = array(

                                'notification_id' => $id,

                                'sms_msg' => $sms_message,

                                'sms_number' => $phone, 

                                'sms_status' => 'idle',

                                'sms_teacher' => 'yes',

                                'sms_parent' => 'no' 

                            );  

                        } 

                    } 

                } 

                if( isset($data_sms) && $data_sms ){

                    $this->db->insert_batch('sms_que', $data_sms); 

                     // $this->SMSM->add($data_sms);

                } 

            }

            // modification: direct SMS sending for selected specific parents
            $custom_student_ids = $this->input->post('custom_student_ids');
            if (!empty($custom_student_ids)) {
                $student_ids = explode(',', $custom_student_ids);
                $this->db->select('students.guardian_phone');
                $this->db->from('students');
                $this->db->where_in('students.id', $student_ids);
                $query = $this->db->get();
                $students_contact = $query->result_array();

                if (!empty($students_contact)) {
                    $this->load->library('smsgateway');
                    foreach ($students_contact as $s_value) {
                        $phone = $s_value['guardian_phone'];
                        $phone = str_replace(' ', '', $phone);
                        $phone = str_replace('-', '', $phone);
                        if (strlen($phone) > 10) {
                            $this->smsgateway->sentNotificationSMS($sms_message, $phone);
                        }
                    }
                }
            }



            /*$arr_api = array(

                'notification_id' => $id, 

                'send_student' =>  isset($student) ? "yes" : 'no',

                'send_teacher' => isset($teacher) ? "yes" : 'no',

                'send_parent' => isset($parent) ? "yes" : 'no',

                'status' => 'pending',

                'type' => 'notification',

                'created_by' => 'teacher',

                'created_id' => $teacher_id,

                'publish_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('publish_date')))

            );



            $this->notification_api_model->add($arr_api);

*/

            $this->session->set_flashdata('msg', '<div class="alert alert-success">Notification added successfully!</div>');

            redirect('teacher/notification/index');

        }

        $exam_result = $this->exam_model->get();

        $data['examlist'] = $exam_result;

        $this->load->view('layout/teacher/header', $data);

        $this->load->view('teacher/notification/notificationEdit', $data);

        $this->load->view('layout/teacher/footer', $data);

    }



    function delete($id) {

        $current_status = $this->SMSM->check_notification($id);

        if( $current_status == 'idle' || $current_status == FALSE){

             $this->SMSM->remove_notification($id );



             $this->notification_model->remove($id);

        } else {

            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Notification can not be be deleted anymore!</div>');

             redirect('teacher/notification/index');



        }



        redirect('teacher/notification');

    }

    // modification: added for filtering
    public function get_class_section_students() {
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $search = $this->input->post('search');

        // modification: fetch the session ID first to prevent resetting the shared Active Record builder state
        $current_session_id = $this->setting_model->getCurrentSession();

        $this->db->select('students.id, students.firstname, students.lastname, students.middlename, students.guardian_name, students.guardian_phone');
        $this->db->from('student_session');
        $this->db->join('students', 'students.id = student_session.student_id');
        $this->db->where('student_session.class_id', $class_id);
        $this->db->where('student_session.section_id', $section_id);
        $this->db->where('student_session.session_id', $current_session_id);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('students.firstname', $search);
            $this->db->or_like('students.lastname', $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        echo json_encode($query->result_array());
    }

}



?>