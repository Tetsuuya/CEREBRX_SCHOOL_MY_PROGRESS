<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class stuattendence extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->lang->load('message', 'english');
        $this->load->library('auth');
        $this->auth->is_logged_in_principal();
		$this->load->model('teacher_model'); 
		$this->load->model('stuattendence_model');
    }

    function index() {
        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'stuattendence/index');
        $data['title'] = 'Add Fees Type';
        $data['title_list'] = 'Fees Type List';
        // $class = $this->class_model->get();
		
		$student_session_data = $this->session->userdata("student");
		$principal_id = $student_session_data['principal_id'];
        $class = $this->class_model->get_class_section_subject( $principal_id );


        // echo "<pre>";print_r($principal_id);echo "</pre>";
        // echo "<pre>";print_r($this->db->last_query());echo "</pre>";
        // echo "<pre>";print_r($class);echo "</pre>";exit();
		  // print_r($this->db->last_query()); ;
    //    print_r($class);exit;
        $data['classlist'] = $class;
        $data['class_id'] = "";
        $data['section_id'] = "";
        $data['subject_id'] = "";
        $data['date'] = "";
        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/principal/header', $data);
            $this->load->view('principal/stuattendence/attendenceList', $data);
            $this->load->view('layout/principal/footer', $data);
        } else {
            $class = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $subject = $this->input->post('subject_id');
            $date = $this->input->post('date');
            $student_list = $this->stuattendence_model->get();
            $data['studentlist'] = $student_list;
            $data['class_id'] = $class;
            $data['section_id'] = $section;
            $data['subject_id'] = $subject;
            $data['date'] = $date;
            $search = $this->input->post('search');
            $holiday = $this->input->post('holiday');
            if ($search == "saveattendence") {
                $session_ary = $this->input->post('student_session');
                foreach ($session_ary as $key => $value) {
                    $checkForUpdate = $this->input->post('attendendence_id' . $value);
                    if ($checkForUpdate != 0) {
                        if (isset($holiday)) {
                            $arr = array(
                                'id' => $checkForUpdate,
                                'student_session_id' => $value,
                                'attendence_type_id' => 5,
                                'date' => date('Y-m-d', $this->customlib->datetostrtotime($date))
                            );
                        } else {
                            $arr = array(
                                'id' => $checkForUpdate,
                                'student_session_id' => $value,
                                'attendence_type_id' => $this->input->post('attendencetype' . $value),
                                'date' => date('Y-m-d', $this->customlib->datetostrtotime($date))
                            );
                        }
                        $insert_id = $this->stuattendence_model->add($arr);
                    } else {
                        if (isset($holiday)) {
                            $arr = array(
                                'student_session_id' => $value,
                                'attendence_type_id' => 5,
                                'date' => date('Y-m-d', $this->customlib->datetostrtotime($date))
                            );
                        } else {
                            $arr = array(
                                'student_session_id' => $value,
                                'attendence_type_id' => $this->input->post('attendencetype' . $value),
                                'date' => date('Y-m-d', $this->customlib->datetostrtotime($date))
                            );
                        }
                        $insert_id = $this->stuattendence_model->add($arr);
                    }
                }

                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Attendance Saved Successfully</div>');
                redirect('principal/stuattendence/index');
            } else {
                
            }
            $attendencetypes = $this->attendencetype_model->get();
            $data['attendencetypeslist'] = $attendencetypes;
            $resultlist = $this->stuattendence_model->searchAttendenceClassSection($class, $section, date('Y-m-d', $this->customlib->datetostrtotime($date)));
           
            $data['resultlist'] = $resultlist;
            $this->load->view('layout/principal/header', $data);
            $this->load->view('principal/stuattendence/attendenceList', $data);
            $this->load->view('layout/principal/footer', $data);
        }
    }

   // UPDATED CODE 
    function teacherattendancereport() {
        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'stuattendence/attendenceReport');
        $teacher = $this->teacher_model->getactiveattendance();
		$data['subject_list'] ="";										
        $data['teacherlist'] = $teacher;  		
        $attendencetypes = $this->attendencetype_model->get();
        $this->load->view('layout/principal/header', $data);
        $this->load->view('principal/stuattendence/teacherattendancereport', $data);
        $this->load->view('layout/principal/footer', $data);
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
            $this->load->view('layout/principal/header', $data);
            $this->load->view('principal/stuattendence/attendencereport', $data);
            $this->load->view('layout/principal/footer', $data);
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
            $this->load->view('layout/principal/header', $data);
            $this->load->view('principal/stuattendence/attendencereport', $data);
            $this->load->view('layout/principal/footer', $data);
        }
    }

    function classattendencereport() {
        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'stuattendence/classattendencereport');
        $attendencetypes = $this->attendencetype_model->get();
        $data['attendencetypeslist'] = $attendencetypes;
        $data['title'] = 'Add Fees Type';
        $data['title_list'] = 'Fees Type List';
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $data['monthlist'] = $this->customlib->getMonthDropdown();
        $data['class_id'] = "";
        $data['section_id'] = "";
        $data['subject_id'] = "";
        $data['date'] = "";
        $data['month_selected'] = "";
        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject_id', 'Subject', 'trim|required|xss_clean');
        $this->form_validation->set_rules('month', 'Month', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/principal/header', $data);
            $this->load->view('principal/stuattendence/classattendencereport', $data);
            $this->load->view('layout/principal/footer', $data);
        } else {
            $resultlist = array();
            $class = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $subject_id = $this->input->post('subject_id');
            $month = $this->input->post('month');
            $data['class_id'] = $class;
            $data['section_id'] = $section;
            $data['subject_id'] = $subject_id;
            $data['month_selected'] = $month;
            $studentlist = $this->student_model->searchByClassSectionSubject($class, $section);
            // $year = date('Y');

            $session_current = $this->setting_model->getCurrentSessionName();
            $startMonth = $this->setting_model->getStartMonth();
            $month_number = date("m", strtotime($month));

            $session_parts = explode('-', $session_current);
            $year_first = $session_parts[0];
            $year_second = isset($session_parts[1]) ? $session_parts[1] : $year_first;
            if (strlen($year_second) == 2) {
                $year_second = substr($year_first, 0, 2) . $year_second;
            }

            if ($month_number >= $startMonth && $month_number <= 12) {
                $year = $year_first;
            } else {
                $year = $year_second;
            }
            $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month_number, $year);
            $attr_result = array();
            $attendence_array = array();
            $student_result = array();
            $data['no_of_days'] = $num_of_days;
            $date_result = array();
            for ($i = 1; $i <= $num_of_days; $i++) {
                $att_date = $year . "-" . $month_number . "-" . sprintf("%02d", $i);
                $attendence_array[] = $att_date;
                // $res = $this->stuattendence_model->searchAttendenceClassSection($class, $section, $att_date);
                $res = $this->stuattendence_model->searchAttendenceClassSectionSubject($class, $section, $att_date, $subject_id);
                $student_result = $res;
                $s = array();
                foreach ($res as $result_k => $result_v) {
                    $s[$result_v['student_session_id']] = $result_v;
                }
                $date_result[$att_date] = $s;
            }
            $data['resultlist'] = $date_result;
            $data['attendence_array'] = $attendence_array;
            $data['student_array'] = $student_result;
            $this->load->view('layout/principal/header', $data);
            $this->load->view('principal/stuattendence/classattendencereport', $data);
            $this->load->view('layout/principal/footer', $data);
        }
    }

    // =====================================================================
    // ATTENDANCE SUMMARY
    // =====================================================================

    public function attendanceSummary() {
        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'stuattendence/attendanceSummary');

        $class = $this->class_model->get();
        $data['classlist']   = $class;
        $data['class_id']    = '';
        $data['section_id']  = '';
        $data['subject_id']  = '';
        $data['start_date']  = '';
        $data['end_date']    = '';

        $this->load->view('layout/principal/header', $data);
        $this->load->view('principal/stuattendence/attendancesummary', $data);
        $this->load->view('layout/principal/footer', $data);
    }

    public function export_attendance_summary() {
        set_time_limit(0);

        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $subject_id = $this->input->post('subject_id');
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');

        if (empty($class_id) || empty($section_id) || empty($start_date) || empty($end_date)) {
            redirect('principal/stuattendence/attendanceSummary');
        }

        // Parse dates
        $start_ts  = $this->customlib->datetostrtotime($start_date);
        $end_ts    = $this->customlib->datetostrtotime($end_date);
        $start_fmt = date('Y-m-d', $start_ts);
        $end_fmt   = date('Y-m-d', $end_ts);
        $date_range_label = date('M j, Y', $start_ts) . ' - ' . date('M j, Y', $end_ts);

        // Fetch students split by gender
        $boys_students = $this->student_model->getstudentsByClassSectionGender($class_id, $section_id, 'Male');
        $girl_students = $this->student_model->getstudentsByClassSectionGender($class_id, $section_id, 'Female');

        // Fetch all attendance types from the database
        $all_types = $this->db->get('attendence_type')->result_array();

        // Find Flag and Chapel subject IDs
        $subjects = $this->db->select('id, name')
                             ->from('subjects')
                             ->group_start()
                             ->like('name', 'Flag')
                             ->or_like('name', 'Chapel')
                             ->or_like('name', 'Prayer')
                             ->group_end()
                             ->get()
                             ->result_array();

        $flag_sub_ids   = array();
        $chapel_sub_ids = array();
        $prayer_sub_ids = array();

        foreach ($subjects as $sub) {
            $name_lower = strtolower($sub['name']);
            if (strpos($name_lower, 'flag') !== false) {
                $flag_sub_ids[] = $sub['id'];
            } elseif (strpos($name_lower, 'chapel') !== false) {
                $chapel_sub_ids[] = $sub['id'];
            } elseif (strpos($name_lower, 'prayer') !== false) {
                $prayer_sub_ids[] = $sub['id'];
            }
        }

        // Query all attendance types for the selected students and date range
        $query_builder = $this->db
            ->select('student_attendences.student_session_id, student_attendences.subject_id, student_attendences.attendence_type_id, COUNT(student_attendences.id) as total_count')
            ->from('student_attendences')
            ->join('student_session', 'student_attendences.student_session_id = student_session.id')
            ->where('student_session.class_id', $class_id)
            ->where('student_session.section_id', $section_id)
            ->where('DATE(student_attendences.date) >=', $start_fmt)
            ->where('DATE(student_attendences.date) <=', $end_fmt);

        if (!empty($subject_id)) {
            $query_builder->where('student_attendences.subject_id', $subject_id);
        }

        $attendances_query = $query_builder->group_by('student_attendences.student_session_id, student_attendences.subject_id, student_attendences.attendence_type_id')
                                           ->get()
                                           ->result_array();

        $student_counts = array();
        foreach ($attendances_query as $row) {
            $ssid    = $row['student_session_id'];
            $sub_id  = $row['subject_id'];
            $type_id = $row['attendence_type_id'];
            $count   = (int)$row['total_count'];

            // Skip Present (ID 1)
            if ($type_id == 1) {
                continue;
            }

            $category = null;
            if (in_array($sub_id, $flag_sub_ids)) {
                $category = 'flag';
            } elseif (in_array($sub_id, $chapel_sub_ids)) {
                $category = 'chapel';
            } elseif (in_array($sub_id, $prayer_sub_ids)) {
                $category = 'prayer';
            }

            if ($category) {
                if (!isset($student_counts[$ssid][$category][$type_id])) {
                    $student_counts[$ssid][$category][$type_id] = 0;
                }
                $student_counts[$ssid][$category][$type_id] += $count;
            }
        }

        // Determine if only specific column should be populated
        $is_flag_selected   = false;
        $is_chapel_selected = false;

        if (!empty($subject_id)) {
            if (in_array($subject_id, $flag_sub_ids)) {
                $is_flag_selected = true;
            } elseif (in_array($subject_id, $chapel_sub_ids) || in_array($subject_id, $prayer_sub_ids)) {
                $is_chapel_selected = true;
            }
        } else {
            $is_flag_selected   = true;
            $is_chapel_selected = true;
        }

        $build_summary_str = function($ssid, $category) use ($student_counts, $all_types) {
            // Display: L, A, E, SC, OC, OSR, Ex
            $desired_ids = array(3, 4, 2, 8, 9, 10, 6);
            $parts = array();

            foreach ($desired_ids as $tid) {
                $type_row = null;
                foreach ($all_types as $t) {
                    if ($t['id'] == $tid) {
                        $type_row = $t;
                        break;
                    }
                }
                if (!$type_row) continue;

                $clean_key = strip_tags($type_row['key_value']);
                $cnt = 0;
                if (isset($student_counts[$ssid][$category][$tid])) {
                    $cnt = $student_counts[$ssid][$category][$tid];
                }
                $parts[] = $clean_key . ' = ' . $cnt;
            }

            return implode(', ', $parts);
        };

        // Resolve class/section details
        $class_details       = $this->class_model->get($class_id);
        $section_details     = $this->section_model->get($section_id);
        $class_section_title = "Fines - " . $class_details['class'] . " " . $section_details['section'];
        $session_current     = $this->setting_model->getCurrentSessionName();

        // -----------------------------------------------------------------------
        // FAST XML / ZipArchive — use the same fines template
        // -----------------------------------------------------------------------
        $templatePath = FCPATH . "uploads/template_documents/Attendace_Principal/Clean_Attendace_template_v3.xlsx";
        if (!file_exists($templatePath)) {
            $templatePath = "./uploads/template_documents/Attendace_Principal/Clean_Attendace_template_v3.xlsx";
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'att_sum_') . '.xlsx';
        copy($templatePath, $tmpFile);

        $zip = new ZipArchive();
        if ($zip->open($tmpFile) !== TRUE) {
            die('Error: Could not open template zip.');
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');

        // Cell helpers
        $make_num_cell = function($col, $row, $style, $val) {
            if ($val === '' || $val === null) {
                return '<c r="' . $col . $row . '" s="' . $style . '" t="n"/>';
            }
            return '<c r="' . $col . $row . '" s="' . $style . '"><v>' . $val . '</v></c>';
        };

        $make_str_cell = function($col, $row, $style, $val) {
            $val = htmlspecialchars((string)$val, ENT_XML1, 'UTF-8');
            if ($val === '') {
                return '<c r="' . $col . $row . '" s="' . $style . '" t="n"/>';
            }
            return '<c r="' . $col . $row . '" s="' . $style . '" t="inlineStr"><is><t>' . $val . '</t></is></c>';
        };

        // Build male rows (starting at row 8)
        $male_rows_xml   = '';
        $female_rows_xml = '';
        $row_num = 8;
        $index   = 1;

        foreach ($boys_students as $student) {
            $ssid           = $student['student_session_id'];
            $flag_summary   = $is_flag_selected   ? $build_summary_str($ssid, 'flag')   : ' ';
            $chapel_summary = $is_chapel_selected ? $build_summary_str($ssid, 'chapel') : ' ';
            if ($flag_summary === '') { $flag_summary = ' '; }
            if ($chapel_summary === '') { $chapel_summary = ' '; }
            $name           = strtoupper($student['lastname'] . ', ' . $student['firstname'] . ' ' . $student['middlename']);

            $male_rows_xml .= '<row r="' . $row_num . '">'
                . $make_num_cell('A', $row_num, '4', $index)
                . $make_str_cell('B', $row_num, '5', $name)
                . $make_str_cell('C', $row_num, '6', $flag_summary)
                . $make_str_cell('D', $row_num, '6', $chapel_summary)
                . $make_str_cell('E', $row_num, '6', ' ')
                . $make_str_cell('F', $row_num, '6', ' ')
                . $make_str_cell('G', $row_num, '6', ' ')
                . $make_str_cell('H', $row_num, '6', ' ')
                . $make_str_cell('I', $row_num, '7', ' ')
                . '</row>';
            $row_num++;
            $index++;
        }

        // Female header row
        $female_header_row = $row_num;
        $female_header_xml = '<row r="' . $female_header_row . '">'
            . '<c r="A' . $female_header_row . '" s="26" t="inlineStr"><is><t>Female</t></is></c>'
            . '<c r="B' . $female_header_row . '" s="13" t="n"/>'
            . '<c r="C' . $female_header_row . '" s="8" t="n"/>'
            . '<c r="D' . $female_header_row . '" s="33" t="n"/>'
            . '<c r="E' . $female_header_row . '" s="34" t="n"/>'
            . '<c r="F' . $female_header_row . '" s="34" t="n"/>'
            . '<c r="G' . $female_header_row . '" s="34" t="n"/>'
            . '<c r="H' . $female_header_row . '" s="8" t="n"/>'
            . '<c r="I' . $female_header_row . '" s="36" t="n"/>'
            . '</row>';
        $row_num++;

        $index = 1;
        foreach ($girl_students as $student) {
            $ssid           = $student['student_session_id'];
            $flag_summary   = $is_flag_selected   ? $build_summary_str($ssid, 'flag')   : ' ';
            $chapel_summary = $is_chapel_selected ? $build_summary_str($ssid, 'chapel') : ' ';
            if ($flag_summary === '') { $flag_summary = ' '; }
            if ($chapel_summary === '') { $chapel_summary = ' '; }
            $name           = strtoupper($student['lastname'] . ', ' . $student['firstname'] . ' ' . $student['middlename']);

            $style_c_d_h = ($index === 1) ? '5' : '6';

            $female_rows_xml .= '<row r="' . $row_num . '">'
                . $make_num_cell('A', $row_num, '4', $index)
                . $make_str_cell('B', $row_num, '5', $name)
                . $make_str_cell('C', $row_num, $style_c_d_h, $flag_summary)
                . $make_str_cell('D', $row_num, $style_c_d_h, $chapel_summary)
                . $make_str_cell('E', $row_num, '5', ' ')
                . $make_str_cell('F', $row_num, '5', ' ')
                . $make_str_cell('G', $row_num, '5', ' ')
                . $make_str_cell('H', $row_num, $style_c_d_h, ' ')
                . $make_str_cell('I', $row_num, '7', ' ')
                . '</row>';
            $row_num++;
            $index++;
        }

        // Update header cells in the template
        $sheetXml = preg_replace(
            '/<c r="A3"[^>]*>.*?<\/c>/s',
            '<c r="A3" s="25" t="inlineStr"><is><t>S.Y. ' . htmlspecialchars($session_current, ENT_XML1) . '</t></is></c>',
            $sheetXml
        );
        $sheetXml = preg_replace(
            '/<c r="A4"[^>]*>.*?<\/c>/s',
            '<c r="A4" s="28" t="inlineStr"><is><t>' . htmlspecialchars($class_section_title, ENT_XML1) . '</t></is></c>',
            $sheetXml
        );
        // Row 5 col C5 â€” use date range instead of month
        $sheetXml = preg_replace(
            '/<c r="C5"[^>]*>.*?<\/c>/s',
            '<c r="C5" s="26" t="inlineStr"><is><t>' . htmlspecialchars($date_range_label, ENT_XML1) . '</t></is></c>',
            $sheetXml
        );

        // Inject student rows replacing template rows 8, 9, 10
        $new_rows = $male_rows_xml . $female_header_xml . $female_rows_xml;
        $sheetXml = preg_replace(
            '/<row r="8"[^>]*>.*?<\/row>.*?<row r="9"[^>]*>.*?<\/row>.*?<row r="10"[^>]*>.*?<\/row>/s',
            $new_rows,
            $sheetXml
        );

        // Update female header merge cell reference (matches with or without spaces/slashes)
        $sheetXml = preg_replace(
            '/<mergeCell\s+ref="A9:B9"\s*\/>/i',
            '<mergeCell ref="A' . $female_header_row . ':B' . $female_header_row . '"/>',
            $sheetXml
        );

        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        // Stream the file
        $filename  = 'FinesSummary_' . str_replace(' ', '_', $class_details['class'] . '_' . $section_details['section']) . '_' . date('Ymd', $start_ts) . '_to_' . date('Ymd', $end_ts) . '.xlsx';
        $file_size = filesize($tmpFile);

        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . $file_size);
        header('Cache-Control: max-age=0');
        readfile($tmpFile);
        @unlink($tmpFile);
        exit();
    }

	
    public function export_fines_report() {
        set_time_limit(0);
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $month = $this->input->post('month');

        if (empty($class_id) || empty($section_id) || empty($month)) {
            redirect('principal/stuattendence/classattendencereport');
        }

        // Fetch students split by gender
        $boys_students = $this->student_model->getstudentsByClassSectionGender($class_id, $section_id, 'Male');
        $girl_students = $this->student_model->getstudentsByClassSectionGender($class_id, $section_id, 'Female');

        // Search for subject IDs matching Flag, Chapel, and Ten Days of Prayer
        $subjects = $this->db->select('id, name')
                             ->from('subjects')
                             ->group_start()
                             ->like('name', 'Flag')
                             ->or_like('name', 'Chapel')
                             ->or_like('name', 'Prayer')
                             ->group_end()
                             ->get()
                             ->result_array();

        $flag_sub_ids = array();
        $chapel_sub_ids = array();
        $prayer_sub_ids = array();

        foreach ($subjects as $sub) {
            $name_lower = strtolower($sub['name']);
            if (strpos($name_lower, 'flag') !== false) {
                $flag_sub_ids[] = $sub['id'];
            } elseif (strpos($name_lower, 'chapel') !== false) {
                $chapel_sub_ids[] = $sub['id'];
            } elseif (strpos($name_lower, 'prayer') !== false) {
                $prayer_sub_ids[] = $sub['id'];
            }
        }

        // Query the Absent type id
        $absent_type = $this->db->get_where('attendence_type', array('type' => 'Absent'))->row_array();
        $absent_type_id = $absent_type ? $absent_type['id'] : 4;

        // Compute month dates
        $month_number = date("m", strtotime($month));
        $session_current = $this->setting_model->getCurrentSessionName();
        $startMonth = $this->setting_model->getStartMonth();
        
        $session_parts = explode('-', $session_current);
        $year_first = $session_parts[0];
        $year_second = isset($session_parts[1]) ? $session_parts[1] : $year_first;
        if (strlen($year_second) == 2) {
            $year_second = substr($year_first, 0, 2) . $year_second;
        }

        if ($month_number >= $startMonth && $month_number <= 12) {
            $year = $year_first;
        } else {
            $year = $year_second;
        }
        $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month_number, $year);

        $start_date = $year . "-" . $month_number . "-01";
        $end_date   = $year . "-" . $month_number . "-" . sprintf("%02d", $num_of_days);

        // Fetch monthly absences for students in class/section
        $this->db->select('student_attendences.student_session_id, student_attendences.subject_id, COUNT(student_attendences.id) as total_absences')
                 ->from('student_attendences')
                 ->join('student_session', 'student_attendences.student_session_id = student_session.id')
                 ->where('student_session.class_id', $class_id)
                 ->where('student_session.section_id', $section_id)
                 ->where('student_attendences.date >=', $start_date)
                 ->where('student_attendences.date <=', $end_date)
                 ->where('student_attendences.attendence_type_id', $absent_type_id)
                 ->group_by('student_attendences.student_session_id, student_attendences.subject_id');
        $absences_query = $this->db->get()->result_array();

        $student_absences = array();
        foreach ($absences_query as $row) {
            $ssid   = $row['student_session_id'];
            $sub_id = $row['subject_id'];
            $count  = $row['total_absences'];

            if (!isset($student_absences[$ssid])) {
                $student_absences[$ssid] = array('flag' => 0, 'chapel' => 0, 'prayer' => 0);
            }

            if (in_array($sub_id, $flag_sub_ids)) {
                $student_absences[$ssid]['flag'] += $count;
            } elseif (in_array($sub_id, $chapel_sub_ids)) {
                $student_absences[$ssid]['chapel'] += $count;
            } elseif (in_array($sub_id, $prayer_sub_ids)) {
                $student_absences[$ssid]['prayer'] += $count;
            }
        }

        // Resolve details
        $class_details        = $this->class_model->get($class_id);
        $section_details      = $this->section_model->get($section_id);
        $class_section_title  = "Fines - " . $class_details['class'] . " " . $section_details['section'];

        // -----------------------------------------------------------------------
        // FAST XML / ZipArchive approach Ã¢â‚¬â€œ no PHPExcel loading
        // -----------------------------------------------------------------------
        $templatePath = FCPATH . "uploads/template_documents/Attendace_Principal/Clean_Attendace_template_v3.xlsx";
        if (!file_exists($templatePath)) {
            $templatePath = "./uploads/template_documents/Attendace_Principal/Clean_Attendace_template_v3.xlsx";
        }

        // Copy template to a temp file so we can modify the zip in-place
        $tmpFile = tempnam(sys_get_temp_dir(), 'fines_') . '.xlsx';
        copy($templatePath, $tmpFile);

        $zip = new ZipArchive();
        if ($zip->open($tmpFile) !== TRUE) {
            die('Error: Could not open template zip.');
        }

        // Read the worksheet XML
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');

        // Helper: build a numeric cell tag
        // s= style index from template row 8 (male) / row 10 (female)
        $make_num_cell = function($col, $row, $style, $val) {
            if ($val === '' || $val === null) {
                return '<c r="' . $col . $row . '" s="' . $style . '" t="n"/>';
            }
            return '<c r="' . $col . $row . '" s="' . $style . '"><v>' . $val . '</v></c>';
        };

        // Helper: build an inlineStr cell tag
        $make_str_cell = function($col, $row, $style, $val) {
            $val = htmlspecialchars((string)$val, ENT_XML1, 'UTF-8');
            if ($val === '') {
                return '<c r="' . $col . '" s="' . $style . '" t="n"/>';
            }
            return '<c r="' . $col . $row . '" s="' . $style . '" t="inlineStr"><is><t>' . $val . '</t></is></c>';
        };

        // Build the male rows XML block (starting at row 8)
        // Template row 8 style codes: A=4, B=5, C-H=6, I=7
        $male_rows_xml   = '';
        $female_rows_xml = '';
        $row_num = 8;
        $index   = 1;

        foreach ($boys_students as $student) {
            $ssid       = $student['student_session_id'];
            $flag_fine  = isset($student_absences[$ssid]['flag'])   ? (int)$student_absences[$ssid]['flag']   * 50 : 0;
            $chapel_fine= isset($student_absences[$ssid]['chapel']) ? (int)$student_absences[$ssid]['chapel'] * 50 : 0;
            $name       = strtoupper($student['lastname'] . ', ' . $student['firstname'] . ' ' . $student['middlename']);
            $sum_formula= '=SUM(C' . $row_num . ':H' . $row_num . ')';

            $male_rows_xml .= '<row r="' . $row_num . '">'
                . $make_num_cell('A', $row_num, '4', $index)
                . $make_str_cell('B', $row_num, '5', $name)
                . $make_num_cell('C', $row_num, '6', $flag_fine > 0 ? $flag_fine : 0)
                . $make_num_cell('D', $row_num, '6', $chapel_fine > 0 ? $chapel_fine : 0)
                . $make_num_cell('E', $row_num, '6', '')
                . $make_num_cell('F', $row_num, '6', '')
                . $make_num_cell('G', $row_num, '6', '')
                . $make_num_cell('H', $row_num, '6', '')
                . '<c r="I' . $row_num . '" s="7"><f>' . htmlspecialchars($sum_formula, ENT_XML1) . '</f><v>0</v></c>'
                . '</row>';
            $row_num++;
            $index++;
        }

        // Female header row
        $female_header_row = $row_num;
        $female_header_xml = '<row r="' . $female_header_row . '">'
            . '<c r="A' . $female_header_row . '" s="26" t="inlineStr"><is><t>Female</t></is></c>'
            . '<c r="B' . $female_header_row . '" s="13" t="n"/>'
            . '<c r="C' . $female_header_row . '" s="8" t="n"/>'
            . '<c r="D' . $female_header_row . '" s="33" t="n"/>'
            . '<c r="E' . $female_header_row . '" s="34" t="n"/>'
            . '<c r="F' . $female_header_row . '" s="34" t="n"/>'
            . '<c r="G' . $female_header_row . '" s="34" t="n"/>'
            . '<c r="H' . $female_header_row . '" s="8" t="n"/>'
            . '<c r="I' . $female_header_row . '" s="36" t="n"/>'
            . '</row>';
        $row_num++;

        $index = 1;
        foreach ($girl_students as $student) {
            $ssid        = $student['student_session_id'];
            $flag_fine   = isset($student_absences[$ssid]['flag'])   ? (int)$student_absences[$ssid]['flag']   * 50 : 0;
            $chapel_fine = isset($student_absences[$ssid]['chapel']) ? (int)$student_absences[$ssid]['chapel'] * 50 : 0;
            $name        = strtoupper($student['lastname'] . ', ' . $student['firstname'] . ' ' . $student['middlename']);
            $sum_formula = '=SUM(C' . $row_num . ':H' . $row_num . ')';

            $style_c_d_h = ($index === 1) ? '5' : '6';

            $female_rows_xml .= '<row r="' . $row_num . '">'
                . $make_num_cell('A', $row_num, '4', $index)
                . $make_str_cell('B', $row_num, '5', $name)
                . $make_num_cell('C', $row_num, $style_c_d_h, $flag_fine > 0 ? $flag_fine : 0)
                . $make_num_cell('D', $row_num, $style_c_d_h, $chapel_fine > 0 ? $chapel_fine : 0)
                . $make_num_cell('E', $row_num, '5', '')
                . $make_num_cell('F', $row_num, '5', '')
                . $make_num_cell('G', $row_num, '5', '')
                . $make_num_cell('H', $row_num, $style_c_d_h, '')
                . '<c r="I' . $row_num . '" s="7"><f>' . htmlspecialchars($sum_formula, ENT_XML1) . '</f><v>0</v></c>'
                . '</row>';
            $row_num++;
            $index++;
        }

        // Replace header cells: S.Y., Fines title, Month
        $sheetXml = preg_replace(
            '/<c r="A3"[^>]*>.*?<\/c>/s',
            '<c r="A3" s="25" t="inlineStr"><is><t>S.Y. ' . htmlspecialchars($session_current, ENT_XML1) . '</t></is></c>',
            $sheetXml
        );
        $sheetXml = preg_replace(
            '/<c r="A4"[^>]*>.*?<\/c>/s',
            '<c r="A4" s="28" t="inlineStr"><is><t>' . htmlspecialchars($class_section_title, ENT_XML1) . '</t></is></c>',
            $sheetXml
        );
        $sheetXml = preg_replace(
            '/<c r="C5"[^>]*>.*?<\/c>/s',
            '<c r="C5" s="26" t="inlineStr"><is><t>For the Month of ' . htmlspecialchars($month, ENT_XML1) . '</t></is></c>',
            $sheetXml
        );

        // Replace the sheetData block: remove old rows 8, 9, 10 and inject generated rows
        $new_sheet_data_rows = $male_rows_xml . $female_header_xml . $female_rows_xml;
        $sheetXml = preg_replace(
            '/<row r="8"[^>]*>.*?<\/row>.*?<row r="9"[^>]*>.*?<\/row>.*?<row r="10"[^>]*>.*?<\/row>/s',
            $new_sheet_data_rows,
            $sheetXml
        );

        // Update the mergeCell for the female header A9:B9 to the new row (matches with or without spaces/slashes)
        $sheetXml = preg_replace(
            '/<mergeCell\s+ref="A9:B9"\s*\/>/i',
            '<mergeCell ref="A' . $female_header_row . ':B' . $female_header_row . '"/>',
            $sheetXml
        );

        // Save modified XML back into zip
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        // Stream the file to the browser
        $filename = 'Fines_' . str_replace(' ', '_', $class_details['class'] . '_' . $section_details['section'] . '_' . $month) . '.xlsx';
        $file_size = filesize($tmpFile);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . $file_size);
        header('Cache-Control: max-age=0');
        readfile($tmpFile);
        @unlink($tmpFile);
        exit();
    }

	// NEW CODE 
    function teacherattendancechecked($id) {
        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'stuattendence/attendenceReport');
        $teacher = $this->teacher_model->getactiveattendance();

        $data['teacherlist'] = $teacher;
        $data['subject_list'] =$this->stuattendence_model->get_attendance_checked_by_teacher_subject_today( $id );

        $this->load->view('layout/principal/header', $data);
        $this->load->view('principal/stuattendence/teacherattendancereport', $data);
        $this->load->view('layout/principal/footer', $data);
    }

}
