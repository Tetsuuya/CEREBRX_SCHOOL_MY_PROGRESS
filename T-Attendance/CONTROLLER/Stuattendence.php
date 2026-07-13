<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class stuattendence extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->lang->load('message', 'english');
        $this->load->library('auth');
        $this->load->library('excel');
        $this->auth->is_logged_in_teacher();
        $this->load->model('Notification_API_model','notification_api_model');
        $this->load->model('Sms_model','SMSM'); 
        $this->load->model('gradingsetting_model');
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    function index() {
        
        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'stuattendence/index');
        $data['title'] = 'Add Fees Type';
        $data['title_list'] = 'Fees Type List';
        //$class = $this->class_model->get();
		
		$student_session_data = $this->session->userdata("student");
		$teacher_id = $student_session_data['teacher_id'];
        $class = $this->class_model->get_calss_section_subject_all( $teacher_id);

        $setting_result = $this->setting_model->get();
        $data['session_id'] = $setting_result[0]['session_id'];
        $session = $this->session_model->getAllSession();
        $data['sessionlist'] = $session;

        // echo "<pre>";print_r($teacher_id);echo "</pre>"; 
        // echo "<pre>";print_r($this->db->last_query());echo "</pre>"; 
        // echo "<pre>";print_r($class);echo "</pre>";exit();
        // echo "<pre>";print_r($this->db->last_query());echo "</pre>";
        // echo "<pre>";print_r($class);echo "</pre>";exit();
		  // print_r($this->db->last_query()); ;
    //    print_r($class);exit;
        $data['teacher_id'] = $teacher_id;
        $data['classlist'] = $class;
        $data['class_id'] = "";
        $data['section_id'] = "";
        $data['subject_id'] = "";
        $data['date'] = "";
        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
        // $this->form_validation->set_rules('session_id', 'School Year', 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/stuattendence/attendenceList', $data);
            $this->load->view('layout/teacher/footer', $data);
        } else {
            $class = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $subject = $this->input->post('subject_id');
            $date = $this->input->post('date');
            $session_id = $this->input->post('session_id');
            $attendence_array = $this->input->post('attendencetype'); 
            $excuse_notes = $this->input->post('excuse_note'); 
            // $exempeted_notes = $this->input->post('exempted_note'); 
            // var_dump($this->input->post('exempted_note')); exit();

        //    $attendance_date = date('Y-m-d', $this->customlib->datetostrtotime($date));

            // if (!empty($attendence_array) && is_array($attendence_array)) {
            //     foreach ($attendence_array as $student_session_id => $attendence_type_id) {

            //         $data = array(
            //             'attendence_type_id' => $attendence_type_id,
            //             'date' => $attendance_date,
            //         );

            //         if (!empty($excuse_notes[$student_session_id]) && $attendence_type_id == 6) {
            //             $data['excuse_note'] = trim($excuse_notes[$student_session_id]);
            //             $data['exempted_note'] = null;
            //         } elseif (!empty($exempeted_notes[$student_session_id]) && $attendence_type_id == 7) {
            //             $data['exempted_note'] = trim($exempeted_notes[$student_session_id]);
            //             $data['excuse_note'] = null;
            //         }

            //         $this->db->where('student_session_id', $student_session_id);
            //         $this->db->update('student_attendences', $data);
            //     }
            // }


            $classes = $this->class_model->get_calss_section_subject_all( $teacher_id, $session_id);
            // print_h($classes);
            if($classes) {
                $data['classlist'] = $classes;
            }
            $data['session_id'] = $session_id;
          /*   $student_list = $this->stuattendence_model->get();
            $student_male = $this->stuattendence_model->get('Male');
            $student_female = $this->stuattendence_model->get('Female');
            $data['studentlist'] = $student_list;
            $data['student_male'] = $student_male;
            $data['student_female'] = $student_female; */
            $data['class_id'] = $class;
            $data['section_id'] = $section;
            $data['subject_id'] = $subject;
            $data['date'] = $date;
            $search = $this->input->post('search');
            $holiday = $this->input->post('holiday');

            if ($search == "saveattendence") {
                $session_ary = $this->input->post('student_session'); 
                $excuse_notes = $this->input->post('excuse_note'); 
                // $exempeted_notes = $this->input->post('exempted_note'); 

                foreach ($session_ary as $key => $value) {
                    $checkForUpdate = $this->input->post('attendendence_id' . $value); 
                    $attendance_date = date('Y-m-d', $this->customlib->datetostrtotime($date));
                    $attendence_type_id = $this->input->post('attendencetype' . $value); 
                    
                    if ($checkForUpdate != 0) {
                    // if (false) { for testing to insert new records instead of updating existing ones
                        
                        if (isset($holiday)) {
                            $arr = array(
                                'id' => $checkForUpdate,
                                'student_session_id' => $value,
                                'attendence_type_id' => 5,
                                'subject_id' => $subject,
                                'teacher_id' => $teacher_id,
                                'date' => date('Y-m-d', $this->customlib->datetostrtotime($date))
                            ); 
                        } else {
                            // die('INSERT');
                            $arr = array(
                                'id'                 => $checkForUpdate,
                                'student_session_id' => $value,
                                'subject_id'         => $subject,
                                'teacher_id'         => $teacher_id,
                                'attendence_type_id' => $attendence_type_id,
                                'date'               => date('Y-m-d', $this->customlib->datetostrtotime($date))
                            );

                            if (!empty($excuse_notes[$value]) && $attendence_type_id == 6) {
                                $arr['excuse_note']   = trim($excuse_notes[$value]);
                                $arr['exempted_note'] = null;
                            /* } elseif (!empty($exempeted_notes[$value]) && $attendence_type_id == 7) {
                                $arr['exempted_note'] = trim($exempeted_notes[$value]);
                                $arr['excuse_note']   = null; */
                            }
                        }

                        // var_dump($attendence_type_id, $exempeted_notes[$value]); exit();
                        $insert_id = $this->stuattendence_model->add($arr); 


                        // // 🔽 Add or update excuse note
                        // if (!empty($excuse_notes[$value])) {
                        //     $excuse_note = trim($excuse_notes[$value]);
                        //     $this->db->where('student_session_id', $value);
                        //     $this->db->where('date', $arr['date']);
                        //     $this->db->where('subject_id', $subject);
                        //     $this->db->update('student_attendences', ['excuse_note' => $excuse_note]);
                        // }


                        if( ($insert_id && $arr['attendence_type_id'] != 1)){ //this does send sms when absent, late, excused, exempted
                        // if ($insert_id) { // same with the above, this does send sms when absent, late, excused, exempted
                            $this->SMSM->remove_attendance($arr['id']);
                            $student_details = $this->student_model->getStudentusingSessionid( $arr['student_session_id']);
                            $can_send_sms = $this->student_model->isCurrentEnrollment($arr['student_session_id']); // enhancement to check if the student is currently enrolled in the current session
                            
                            $guardian_phone =  $student_details->guardian_phone;
                            $firstname =  $student_details->firstname;
                            $lastname =  $student_details->lastname;
                            $attendence_detail = $this->attendencetype_model->get($arr['attendence_type_id']); 
                            $attendence_type =  $attendence_detail['type']; 
                            $subject_detail = $this->subject_model->get($arr['subject_id']); 
                            $subject_name =  $subject_detail['name'];
                            $attendance_date =  $arr['date'];
                            date_default_timezone_set('Asia/Manila');
                            $attendance_date_display =  date('F d, Y', strtotime($attendance_date)); 
                            $attendance_date_today =  date('F d, Y');
     
                            $sms_message =  $firstname.' '.$lastname.' is '.$attendence_type.' from '.$subject_name.' on '.$attendance_date_display.'. This is an autogenerated SMS by Bridgette. Please do not reply.';
                            // if( $guardian_phone ){
                            if ($can_send_sms && $guardian_phone) {
                                $guardian_phone = str_replace(' ', '', $guardian_phone);
                                $guardian_phone = str_replace('-', '', $guardian_phone);

                                if (strlen($guardian_phone) > 10) {
                                    $data_sms = array(
                                        'attendance_id' => $insert_id,
                                        'sms_msg' => $sms_message,
                                        'sms_number' => $guardian_phone,
                                        'sms_status' => 'idle',
                                        'sms_teacher' => 'no',
                                        'sms_parent' => 'yes'
                                    );

                                    $this->SMSM->add($data_sms);
                                }
                            }

                        
                        } 
                       

                    } else {
                        if (isset($holiday)) {
                            $arr = array(
                                'student_session_id' => $value,
                                'attendence_type_id' => 5,
                                'subject_id' => $subject,
                                'teacher_id' => $teacher_id,
                                'date' => date('Y-m-d', $this->customlib->datetostrtotime($date))
                            );
                        } else {
                            $arr = array(
                                'student_session_id' => $value,
                                'attendence_type_id' => $this->input->post('attendencetype' . $value),
                                'subject_id' => $subject,
                                'teacher_id' => $teacher_id,
                                'date' => date('Y-m-d', $this->customlib->datetostrtotime($date))
                            );

                            // Add excuse note to the main array if provided
                            if (!empty($excuse_notes[$value]) && $attendence_type_id == 6) {
                                $arr['excuse_note']   = trim($excuse_notes[$value]);
                                $arr['exempted_note'] = null; // clear the other
                            /* } elseif (!empty($exempeted_notes[$value]) && $attendence_type_id == 7) {
                                $arr['exempted_note'] = trim($exempeted_notes[$value]);
                                $arr['excuse_note']   = null; // clear the other */
                            }
                        }
                        $insert_id = $this->stuattendence_model->add($arr);

                        if( ($insert_id && $arr['attendence_type_id'] != 1) ){
                            $this->SMSM->remove_attendance($insert_id);
                        // if ($insert_id) {
                        // if( ($insert_id && $arr['attendence_type_id'] != 1) && $session_id == $this->current_session  ){ //removed for push notification and sms que purpose
                            $student_details = $this->student_model->getStudentusingSessionid( $arr['student_session_id']);

                            $can_send_sms = $this->student_model->isCurrentEnrollment($arr['student_session_id']); // enhancement to check if the student is currently enrolled in the current session

                            // $session_id = $this->setting_model->getCurrentSession(); // for push notif for absent greater than 10
                            $total_absent = $this->stuattendence_model->count_student_absences($session_id, $arr['student_session_id'], $arr['subject_id']); // enhancement
                            
                            

                            // $parent_id = $this->stuattendence_model->get_parents_id($student_details->id); // if there are more than 1 siblings, get the highest ID and use it for sending push notification in mobile appication
                            // $parentIds = json_encode(['P-' . $parent_id->user_id]);
                            // $decodedParentIds = json_decode($parentIds); 
                            // printx('test2');
                            $guardian_phone =  $student_details->guardian_phone;
                            $firstname =  $student_details->firstname;
                            $lastname =  $student_details->lastname;
                            log_message('debug', 'SMS check for new record. insert_id=' . $insert_id . ' student_session_id=' . $arr['student_session_id'] . ' date=' . $arr['date'] . ' can_send_sms=' . var_export($can_send_sms, true) . ' guardian_phone=' . var_export($guardian_phone, true) . ' phone_len=' . strlen((string)$guardian_phone) );
                            $attendence_detail = $this->attendencetype_model->get($arr['attendence_type_id']); 
                            $attendence_type =  $attendence_detail['type']; 
                            $subject_detail = $this->subject_model->get($arr['subject_id']); 
                            $subject_name =  $subject_detail['name'];
                            $attendance_date =  $arr['date'];
                            date_default_timezone_set('Asia/Manila');
                            $attendance_date_display =  date('F d, Y', strtotime($attendance_date));
                            $attendance_date_today =  date('F d, Y');
                            
                              // start push notif for absent greater than 10
                            // if ($total_absent[0]->counts >= 10){
                            //     $sms_message =  $firstname.' '.$lastname. ' has ' . $total_absent[0]->counts . ' absences in ' .$subject_name. ' for the current school year' . '. This is an autogenerated SMS by Bridgette. Please do not reply.';
                            //     $this->send_notification($parentIds, $sms_message); // send notification the mobile application
                            //     $this->store_notification($decodedParentIds[0], $sms_message, "MMA"); // store the notification 

                            //     if( $guardian_phone ){
                            //         $guardian_phone = str_replace(' ', '', $guardian_phone);
                            //         $guardian_phone = str_replace('-', '', $guardian_phone);
                            //         if (strlen($guardian_phone)>10) {
                            //             $data_sms  = array(
                            //                 'sms_msg' => $sms_message,
                            //                 'sms_number' => $guardian_phone, 
                            //                 'sms_status' => 'idle',
                            //                 'sms_teacher' => 'no',
                            //                 'sms_parent' => 'yes' 
                            //             );
                            //             $this->SMSM->add($data_sms);  
                            //         }
                            //     }
                            // }
                            // end push notif for absent greater than 10

                            $sms_message =  $firstname.' '.$lastname.' is '.$attendence_type.' from '.$subject_name.' on '.$attendance_date_display.'. This is an autogenerated SMS by Bridgette. Please do not reply.';
                            // $this->send_notification($parentIds, $sms_message); // send notification the mobile application
                            // $this->store_notification($decodedParentIds[0], $sms_message, "MMA"); // store the notification 
                            // if( $guardian_phone ){
                            if ($can_send_sms && $guardian_phone) {
                                $guardian_phone = str_replace(' ', '', $guardian_phone);
                                $guardian_phone = str_replace('-', '', $guardian_phone);

                                if (strlen($guardian_phone) > 10) {
                                    $data_sms = array(
                                        'attendance_id' => $insert_id,
                                        'sms_msg' => $sms_message,
                                        'sms_number' => $guardian_phone,
                                        'sms_status' => 'idle',
                                        'sms_teacher' => 'no',
                                        'sms_parent' => 'yes'
                                    );
                                    log_message('debug', 'Reached SMS block for new record. insert_id=' . $insert_id . ' type=' . $arr['attendence_type_id'] . ' can_send_sms=' . var_export($can_send_sms, true) . ' phone=' . $guardian_phone);

                                    $this->SMSM->add($data_sms);
                                }
                            }
                        } 
                      
                    }
                }

                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Attendance Saved Successfully</div>');
                redirect('teacher/stuattendence/index');
            } else {
                
            }
            $attendencetypes = $this->attendencetype_model->get(); 
            $data['attendencetypeslist'] = $attendencetypes;
            // printx($data['attendencetypeslist']);
            if(  $subject ){
                 $resultlist_male = $this->stuattendence_model->searchAttendenceClassSectionSubjectGender($class, $section, date('Y-m-d', $this->customlib->datetostrtotime($date)), $subject, 'Male', $session_id);
                 $resultlist_female = $this->stuattendence_model->searchAttendenceClassSectionSubjectGender($class, $section, date('Y-m-d', $this->customlib->datetostrtotime($date)), $subject, 'Female', $session_id);

                if( empty( $resultlist_male) || empty( $resultlist_female) ){
                $resultlist_male = $this->stuattendence_model->searchAttendenceClassSectionByGender($class, $section, date('Y-m-d', $this->customlib->datetostrtotime($date)), 'Male', $subject,$session_id);
                $resultlist_female = $this->stuattendence_model->searchAttendenceClassSectionByGender($class, $section, date('Y-m-d', $this->customlib->datetostrtotime($date)), 'Female',$subject, $session_id);
                }
            } else {
                 $resultlist_male = $this->stuattendence_model->searchAttendenceClassSection($class, $section, date('Y-m-d', $this->customlib->datetostrtotime($date)), $session_id);
                 $resultlist_female = $this->stuattendence_model->searchAttendenceClassSection($class, $section, date('Y-m-d', $this->customlib->datetostrtotime($date)), $session_id);
            }  
			//$data['resultlist'] = $resultlist;
            $data['resultlist_male'] = $resultlist_male;
            $data['resultlist_female'] = $resultlist_female;
            // printx($data['resultlist_male']); 
            $data['excuse_type_id'] = 6; 
            // $data['exempted_type_id'] = 7; 
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/stuattendence/attendenceList', $data);
            $this->load->view('layout/teacher/footer', $data);
        }
    }

//     private $absent_type_id = 4;

//     // Add this method to your controller
// public function add_excuse() {
//     $this->form_validation->set_rules('attendence_id', 'Attendance ID', 'required|numeric');
//     $this->form_validation->set_rules('excuse_note', 'Excuse Note', 'required|trim|max_length[500]');
    
//     if ($this->form_validation->run() == FALSE) {
//         echo json_encode([
//             'status' => 'error',
//             'message' => validation_errors()
//         ]);
//         return;
//     }

//     $attendence_id = $this->input->post('attendence_id');
//     $excuse_note = $this->input->post('excuse_note');
//     $teacher_id = $this->session->userdata('student')['teacher_id'];

//     // Verify this attendance record exists and belongs to the teacher
//     $this->db->select('student_attendences.*');
//     $this->db->where('student_attendences.id', $attendence_id);
//     $this->db->where('student_attendences.teacher_id', $teacher_id);
//     $query = $this->db->get('student_attendences');
    
//     if ($query->num_rows() == 0) {
//         echo json_encode([
//             'status' => 'error',
//             'message' => 'Attendance record not found or unauthorized.'
//         ]);
//         return;
//     }

//     $attendance = $query->row();
    
//     // Only allow excuses for absent students
//     if ($attendance->attendence_type_id == $this->absent_type_id) {
//         echo json_encode([
//             'status' => 'error',
//             'message' => 'Excuse notes can only be added for absent students.'
//         ]);
//         return;
//     }

//     // Update the excuse note
//     $this->db->where('id', $attendence_id);
//     $updated = $this->db->update('student_attendences', [
//         'excuse_note' => $excuse_note,
//         'updated_at' => date('Y-m-d H:i:s')
//     ]);

//     if ($updated) {
//         echo json_encode([
//             'status' => 'success',
//             'message' => 'Excuse note added successfully.',
//             'student_session_id' => $attendance->student_session_id
//         ]);
//     } else {
//         echo json_encode([
//             'status' => 'error',
//             'message' => 'Failed to add excuse note. Please try again.'
//         ]);
//     }
// }

    function send_notification($parentIds, $message) {
        $url = 'http://128.199.66.214:8080/sendNotificationParents';
        $data = array(
            'parentsId' => json_decode($parentIds, true),  // Decode to ensure it's a valid JSON array
            'message' => $message
        );

        $jsonData = json_encode($data);

       

        // Initialize cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        // Uncomment if needed to ignore SSL verification (useful if server has SSL issues)
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Enable verbose output for debugging
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        // Execute and capture the response and HTTP status code
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            log_message('error', 'cURL error: ' . curl_error($ch));
        } else {
            log_message('info', 'HTTP Code: ' . $httpCode . ' Response: ' . $response);
        }

        curl_close($ch);

        // Handle the response
        if ($httpCode == 200 || $httpCode == 207) {  // Check for 200 or 207 Multi-Status
            log_message('info', 'Notification sent successfully.');
        } else {
            log_message('error', 'Failed to send notification. HTTP Code: ' . $httpCode . ' Response: ' . $response);
        }
    }

    function store_notification($user_id, $message, $school) {
        $url = 'http://157.230.43.105/pushnotif/api/store.php';
        $data = array(
            'user_id' => $user_id,  
            'message' => $message,
            'school' => $school
        );

    

        $jsonData = json_encode($data);



       

        // Initialize cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        // Uncomment if needed to ignore SSL verification (useful if server has SSL issues)
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Enable verbose output for debugging
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        // Execute and capture the response and HTTP status code
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            log_message('error', 'cURL error: ' . curl_error($ch));
        } else {
            log_message('info', 'HTTP Code: ' . $httpCode . ' Response: ' . $response);
        }

        curl_close($ch);

        // Handle the response
        if ($httpCode == 200 || $httpCode == 207) {  // Check for 200 or 207 Multi-Status
            log_message('info', 'Notification sent successfully.');
        } else {
            log_message('error', 'Failed to send notification. HTTP Code: ' . $httpCode . ' Response: ' . $response);
        }
    }

    function getGradeBySession() {
        $teacher_id = $this->input->get('teacher_id');
        $session_id = $this->input->get('session_id');
        $data = $this->class_model->get_calss_section_subject_all( $teacher_id, $session_id );
        echo json_encode($data);
    }

    function attendencereport() {
        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'stuattendence/attendenceReport');
        $data['title'] = 'Add Fees Type';
        $data['title_list'] = 'Fees Type List';
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $data['class_id'] = "";
        $data['section_id'] = "";
        $data['date'] = "";
        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', 'Date', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/stuattendence/attendencereport', $data);
            $this->load->view('layout/teacher/footer', $data);
        } else {
            $class = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $date = $this->input->post('date');
            $student_list = $this->stuattendence_model->get();
            $data['studentlist'] = $student_list;
            $data['class_id'] = $class;
            $data['section_id'] = $section;
            $data['date'] = $date;
            $search = $this->input->post('search');
            if ($search == "saveattendence") {
                $session_ary = $this->input->post('student_session');
                foreach ($session_ary as $key => $value) {
                    $checkForUpdate = $this->input->post('attendendence_id' . $value);
                    if ($checkForUpdate != 0) {
                        $arr = array(
                            'id' => $checkForUpdate,
                            'student_session_id' => $value,
                            'attendence_type_id' => $this->input->post('attendencetype' . $value),
                            'date' => date('Y-m-d', $this->customlib->datetostrtotime($date))
                        );
                        $insert_id = $this->stuattendence_model->add($arr);
                    } else {
                        $arr = array(
                            'student_session_id' => $value,
                            'attendence_type_id' => $this->input->post('attendencetype' . $value),
                            'date' => date('Y-m-d', $this->customlib->datetostrtotime($date))
                        );
                        $insert_id = $this->stuattendence_model->add($arr);
                    }
                }
            } else {
                
            }
            $attendencetypes = $this->attendencetype_model->get();
            $data['attendencetypeslist'] = $attendencetypes;
            $resultlist = $this->stuattendence_model->searchAttendenceClassSectionPrepare($class, $section, date('Y-m-d', $this->customlib->datetostrtotime($date)));
            $data['resultlist'] = $resultlist;
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/stuattendence/attendencereport', $data);
            $this->load->view('layout/teacher/footer', $data);
        }
    }

    function classattendencereport() {
        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'stuattendence/classattendencereport');
        $attendencetypes = $this->attendencetype_model->get();
        $data['attendencetypeslist'] = $attendencetypes;
        $data['title'] = 'Add Fees Type';
        $data['title_list'] = 'Fees Type List';
        $student_session_data = $this->session->userdata("student"); 
        $teacher_id = $student_session_data['teacher_id']; 
        $class = $this->class_model->get_by_teacher( $teacher_id ); 
        $teacher = $this->teacher_model->get( $teacher_id );
        $data['type_teacher'] = $teacher['type_teacher']; 
        $data['classlist'] = $class;
        $data['monthlist'] = $this->customlib->getMonthDropdown();
        $data['class_id'] = "";
        $data['section_id'] = "";
        $data['date'] = "";
        $data['month_selected'] = "";
        
        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');
        $this->form_validation->set_rules('month', 'Month', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/stuattendence/classattendencereport', $data);
            $this->load->view('layout/teacher/footer', $data);
        } else {
            $resultlist = array();
            $class = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $month = $this->input->post('month');
            $data['class_id'] = $class;
            $data['section_id'] = $section;
            $data['month_selected'] = $month;
            //$studentlist = $this->student_model->searchByClassSection($class, $section);
            // $year = date('Y');

            $session_current = $this->setting_model->getCurrentSessionName();
            $startMonth = $this->setting_model->getStartMonth();
            $centenary = substr($session_current, 0, 2); //2017-18 to 2017
            $year_first_substring = substr($session_current, 2, 2); //2017-18 to 2017
            $year_second_substring = substr($session_current, 5, 2); //2017-18 to 18
            $month_number = date("m", strtotime($month));

            if ($month_number >= $startMonth && $month_number <= 12) {
                $year = $centenary . $year_first_substring;
            } else {
                $year = $centenary . $year_second_substring;
            }
            $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month_number, $year);
            $attr_result = array();
			// male
            $attendence_array= array();
            $student_result_male = array();
            $data['no_of_days'] = $num_of_days;
            $date_result_male = array();
            for ($i = 1; $i <= $num_of_days; $i++) {
                $att_date = $year . "-" . $month_number . "-" . sprintf("%02d", $i);
                $attendence_array[] = $att_date; 
                $res = $this->stuattendence_model->searchAttendenceClassSectionByGender($class, $section, $att_date, 'Male');
                // echo '<pre>'; print_r($this->db->last_query());
                $student_result_male = $res;
                $s = array();
                foreach ($res as $result_k => $result_v) {
                    $s[$result_v['student_session_id']] = $result_v;
                }
                $date_result_male[$att_date] = $s;
            }
			//female
            $student_result_female = array();
            $data['no_of_days'] = $num_of_days;
            $date_result_female = array();
            for ($i = 1; $i <= $num_of_days; $i++) {
                $att_date = $year . "-" . $month_number . "-" . sprintf("%02d", $i);
                $res = $this->stuattendence_model->searchAttendenceClassSectionByGender($class, $section, $att_date, 'Female');
                // echo '<pre>'; print_r($this->db->last_query());
                $student_result_female = $res;
                $s = array();
                foreach ($res as $result_k => $result_v) {
                    $s[$result_v['student_session_id']] = $result_v;
                }
                $date_result_female[$att_date] = $s;
            } 
           /*  $data['resultlist'] = $date_result;
            $data['attendence_array'] = $attendence_array;
            $data['student_array'] = $student_result; */
			
			 $data['resultlist_male'] = $date_result_male;
            $data['attendence_array'] = $attendence_array;
            $data['student_array_male'] = $student_result_male;
			//female
			 $data['resultlist_female'] = $date_result_female;
            $data['student_array_female'] = $student_result_female;
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/stuattendence/classattendencereport', $data);
            $this->load->view('layout/teacher/footer', $data);
        }
    }

    function classattendencereportexcel() {
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $month_selected = $this->input->post('month_selected'); 

        
        // $this->excel->test( );
        $this->excel->generate_attendance( $class_id, $section_id, $month_selected );
    }

    function import() {
        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'stuattendence/import');
        $data['title'] = 'Add Fees Type';
        $data['title_list'] = 'Fees Type List';
        
        $student_session_data = $this->session->userdata("student"); 
        $teacher_id = $student_session_data['teacher_id']; 
        $class = $this->class_model->get_by_teacher( $teacher_id );
        $class_list = array();
        if(  $class ){
            foreach ($class as $class_key => $class_value) {
                $class_list[] = $class_value['id'];
            
            }
        } 
        $classsection = $this->classsection_model->getlistadvisory( $teacher_id, $class_list);
        
        if( $classsection ){
            $class = array_merge_recursive($class, $classsection); 
        } 
        $data['classlist'] = $class;
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $school_days_first_month = $this->input->post('school_days_first_month'); 
        $attendance_type = $this->input->post('attendance_type'); 

        $data['class_id'] =  $class_id? $class_id: "";
        $data['section_id'] =  $section_id? $section_id: "";
        $data['school_days_first_month'] =  $school_days_first_month? $school_days_first_month: ""; 
        $data['attendance_type'] =  $attendance_type? $attendance_type: ""; 
        $data['date'] = "";
        //$data['resultlist'] = "";
        $data['resultlist_male'] = "";
        $data['resultlist_female'] = "";

        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', 'Session', 'trim|required|xss_clean'); 
        $this->form_validation->set_rules('attendance_type', 'Attendance Type', 'trim|required|xss_clean'); 
        if ($this->form_validation->run() == FALSE) {
             $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/stuattendence/attendenceimport', $data);
            $this->load->view('layout/teacher/footer', $data);
        } else {

            if( $class_id && $section_id ){ 
                $resultlist_male = $this->student_model->searchByClassSectionGender($class_id, $section_id,'Male');
                $resultlist_female = $this->student_model->searchByClassSectionGender($class_id, $section_id,'Female');
                $data['resultlist_male'] = $resultlist_male;
                $data['resultlist_female'] = $resultlist_female;
            } 
         
            $gradingsetting_model = $this->gradingsetting_model->get();

            $data['first_month'] = $gradingsetting_model->school_days_first_month;
            $data['last_month'] = $gradingsetting_model->school_days_last_month;
            $data['school_days'] = $gradingsetting_model->school_days;
            $data['session_id'] = $gradingsetting_model->session_id;

            $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
            $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');
            // $this->form_validation->set_rules('date', 'Date', 'trim|required|xss_clean');
           
            $this->load->view('layout/teacher/header', $data);
            $this->load->view('teacher/stuattendence/attendenceimport', $data);
            $this->load->view('layout/teacher/footer', $data);
        }
         
    }

    function import_save() {
        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'stuattendence/import'); 
        $studentdetail = $this->input->post('studentdetail'); 
        $studentdetails = $this->input->post('studentdetails'); 
        $currentsession= $this->setting_model->getCurrentSession();  
        date_default_timezone_set('Asia/Manila');

        if( $studentdetail ){
            foreach ($studentdetail as $key => $value) {
                $student_id = $value['student_id'];
                $attendance_type = $value['attendance_type'];
                $attendance_array = $value['attendance']; 
                $attendance_serialize = serialize($attendance_array);

                $this->db->select('id');
                $this->db->from('student_attendance_custom');  
                $this->db->where('student_id', $student_id); 
                $this->db->where('attendance_type', $attendance_type); 
                $this->db->where('session_id', $currentsession); 
               
                $query = $this->db->get(); 
                $query_row_array = $query->row();  

                $data = array(
                    'student_id' => $student_id,
                    'attendance_type' => $attendance_type,
                    'session_id' => $currentsession,
                    'attendance' => $attendance_serialize,
                    'is_active' => 'yes',
                    'created_at' => date('Y-m-d H:i:s') ,
                );

                if( $query_row_array  ){
                    $this->db->where('id', $query_row_array->id );
                    $this->db->update('student_attendance_custom', $data);
                } else {    
                    $this->db->insert('student_attendance_custom', $data);
                    $this->db->insert_id();
                }
            }
        }
		
		if( $studentdetails ){
            foreach ($studentdetails as $key => $value) {
                $student_id = $value['student_id'];
                $attendance_type = $value['attendance_type'];
                $attendance_array = $value['attendance']; 
                $attendance_serialize = serialize($attendance_array);

                $this->db->select('id');
                $this->db->from('student_attendance_custom');  
                $this->db->where('student_id', $student_id); 
                $this->db->where('attendance_type', $attendance_type); 
                $this->db->where('session_id', $currentsession); 
               
                $query = $this->db->get(); 
                $query_row_array = $query->row();  

                $data = array(
                    'student_id' => $student_id,
                    'attendance_type' => $attendance_type,
                    'session_id' => $currentsession,
                    'attendance' => $attendance_serialize,
                    'is_active' => 'yes',
                    'created_at' => date('Y-m-d H:i:s') ,
                );

                if( $query_row_array  ){
                    $this->db->where('id', $query_row_array->id );
                    $this->db->update('student_attendance_custom', $data);
                } else {    
                    $this->db->insert('student_attendance_custom', $data);
                    $this->db->insert_id();
                }
            }
        }
 
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Students imported successfully.</div>'); 

        redirect('teacher/stuattendence/import');
    }

}

?>