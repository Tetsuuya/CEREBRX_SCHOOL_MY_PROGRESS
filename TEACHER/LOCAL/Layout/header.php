<!DOCTYPE html>
<html <?php echo $this->customlib->getRTL();?>>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $this->customlib->getAppName(); ?></title>        
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="theme-color" content="#6D2932" />
    <link href="<?php echo base_url(); ?>backend/images/s-favican.png" rel="shortcut icon" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css">       
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/ss-main.css">
    <?php
    if($this->customlib->getRTL() != ""){
        ?>
        <!-- Bootstrap 3.3.5 RTL -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/bootstrap-rtl/css/bootstrap-rtl.min.css"/>  
        <!-- Theme RTL style -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/AdminLTE-rtl.min.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/ss-rtlmain.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/skins/_all-skins-rtl.min.css" />

        <?php 

    }else{

    }

    ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/font-awesome.min.css">      
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/ionicons.min.css">       

    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/iCheck/flat/blue.css">      
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/morris/morris.css">       
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">        
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/datepicker/datepicker3.css">       
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/daterangepicker/daterangepicker-bs3.css">      
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/sweet-alert/sweetalert2.ciss">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/custom_style.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/datepicker/css/bootstrap-datetimepicker.css">
     <!--print table-->
    <link href="<?php echo base_url(); ?>backend/dist/cssdata/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>backend/dist/cssdata/buttons.bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>backend/dist/cssdata/semantic.min.css">

 <!--./print table -->
    <script src="<?php echo base_url(); ?>backend/custom/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>backend/dist/js/moment.min.js"></script>
    <script src="<?php echo base_url(); ?>backend/datepicker/js/bootstrap-datetimepicker.js"></script>
    <script src="<?php echo base_url(); ?>backend/datepicker/date.js"></script>       
    <script src="<?php echo base_url(); ?>backend/dist/js/jquery-ui.min.js"></script>
    <!-- <?php
    if (strpos($_SERVER['REQUEST_URI'], "dashboard") !== false){ 
        $session_information = $this->session->userdata('student');
        $session_name = $session_information['username']."_".$session_information['role'].$session_information[$session_information['role'].'_id']."@".$session_information['sch_name'].".drift"; 
        $session_name = preg_replace('/\s*/', '', $session_name); 
        $session_name = strtolower($session_name);  
        ?>

        <script>
        !function() {
          var t;
          if (t = window.driftt = window.drift = window.driftt || [], !t.init) return t.invoked ? void (window.console && console.error && console.error("Drift snippet included twice.")) : (t.invoked = !0, 
          t.methods = [ "identify", "config", "track", "reset", "debug", "show", "ping", "page", "hide", "off", "on" ], 
          t.factory = function(e) {
            return function() {
              var n;
              return n = Array.prototype.slice.call(arguments), n.unshift(e), t.push(n), t;
            };
          }, t.methods.forEach(function(e) {
            t[e] = t.factory(e);
          }), t.load = function(t) {
            var e, n, o, i;
            e = 3e5, i = Math.ceil(new Date() / e) * e, o = document.createElement("script"), 
            o.type = "text/javascript", o.async = !0, o.crossorigin = "anonymous", o.src = "https://js.driftt.com/include/" + i + "/" + t + ".js", 
            n = document.getElementsByTagName("script")[0], n.parentNode.insertBefore(o, n);
          });
        }();
        drift.SNIPPET_VERSION = '0.3.1';
        drift.load('6a3u9det6z2n');

        drift.on('ready', function() {
          drift.api.setUserAttributes({ 
            email: "<?php echo $session_name; ?>" 
          });
        });
        </script>

        <?php  
    }
    ?> -->
</head>
<body class="hold-transition skin-blue fixed sidebar-mini">
    <div class="wrapper">
        <header class="main-header">               
            <a href="<?php echo base_url(); ?>teacher/teacher/dashboard" class="logo">                  
                <span class="logo-mini">S S</span>                   
                 <span class="logo-lg"><img src="<?php echo base_url(); ?>backend/images/logo.png" alt="<?php echo $this->customlib->getAppName() ?>" /></span>
            </a>               
            <nav class="navbar navbar-static-top" role="navigation">                  
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
               <div class="col-md-5 col-sm-3 col-xs-5">  
                 <span href="#" class="sidebar-session">
                      <?php echo $this->setting_model->getCurrentSchoolName(); ?>
                  </span>
                </div>        
             <div class="col-md-7 col-sm-9 col-xs-7">
               <div class="pull-right">           
                <form class="navbar-form navbar-left search-form" role="search"  action="<?php echo site_url('teacher/admin/search'); ?>" method="POST">
                 <?php echo $this->customlib->getCSRF(); ?>
                 <div class="input-group" style="padding-top:3px;">
                    <input type="text" name="search_text" class="form-control search-form search-form3" placeholder="<?php echo $this->lang->line('search_by_name,_roll_no,_enroll_no,_national_identification_no,_local_identification_no_etc..'); ?>">
                    <span class="input-group-btn">
                        <button type="submit" name="search" id="search-btn" style="padding: 3px 12px !important;border-radius: 0px 30px 30px 0px; background: #fff;" class="btn btn-flat"><i class="fa fa-search"></i></button>
                    </span>
                </div>
            </form>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav"> 
                  
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false"><?php echo $this->customlib->getStudentSessionUserName(); ?>
                            <i class="fa fa-user-secret fa-fw"></i>  <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="<?php echo base_url(); ?>teacher/teacher/profile"><i class="fa fa-user"></i> <?php echo $this->lang->line('profile'); ?></a>
                            </li> 
                            <li><a href="<?php echo base_url(); ?>teacher/teacher/changepass"><i class="fa fa-key"></i> <?php echo $this->lang->line('change_password'); ?></a>
                            </li>
                            <li class="divider"></li>
                            <li><a href="<?php echo base_url(); ?>site/logout"><i class="fa fa-sign-out fa-fw"></i> <?php echo $this->lang->line('logout'); ?></a>
                            </li>
                        </ul>                             
                    </li> 
                </ul>
            </div>
           </div>
          </div>
        </nav>
    </header>
    <aside class="main-sidebar">                
        <section class="sidebar" id="sibe-box">
            <form class="navbar-form navbar-left search-form2" role="search"  action="<?php echo site_url('teacher/admin/search'); ?>" method="POST">
             <?php echo $this->customlib->getCSRF(); ?>
             <div class="input-group ">
                <input type="text" name="search_text" class="form-control search-form" placeholder="<?php echo $this->lang->line('search_by_name,_roll_no,_enroll_no,_national_identification_no,_local_identification_no_etc..'); ?>">
                <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" style="padding: 3px 12px !important;border-radius: 0px 30px 30px 0px; background: #fff;" class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </form>                    
        <ul class="sidebar-menu">
			 <li class="removehover">
				<span class="logo-lg"><img src="<?php echo base_url(); ?>backend/images/school_logo.png" alt="<?php echo $this->customlib->getAppName() ?>" class="img-responsive"/></span>
				<h2 class="school-yr"><?php echo "SY " . $this->setting_model->getCurrentSessionName(); ?></h2>
			</li>
            <li class="  <?php echo set_Topmenu('dashboard'); ?>"><a href="<?php echo base_url(); ?>teacher/teacher/dashboard"><i class="fa fa-user-secret"></i> <?php echo 'My Dashboard';//$this->lang->line('my_profile'); ?></a></li>
            
            <li class="treeview <?php echo set_Topmenu('Student'); ?>">
                <a href="#">
                    <i class="fa fa-users"></i> <span><?php echo $this->lang->line('student'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">

                   

                   <!-- <?php if ((int)$teacher_id == 66): ?>   -->
                        <!-- <?php echo 'Using search1'; ?> -->
                        <!-- <li class="<?php echo set_Submenu('student/search1'); ?>">
                            <a href="<?php echo base_url(); ?>teacher/student/search1">
                                <i class="fa fa-angle-double-right"></i>
                                Student Records
                            </a>
                        </li> -->
                    <!-- <?php else: ?> -->
                        <!-- <?php echo 'Using search'; ?> -->
                        <!-- <li class="<?php echo set_Submenu('student/search'); ?>">
                            <a href="<?php echo base_url(); ?>teacher/student/search">
                                <i class="fa fa-angle-double-right"></i>
                                Student Records
                            </a>
                        </li> -->
                    <!-- <?php endif; ?> -->



                
                    <li class="<?php echo set_Submenu('student/search'); ?>"><a href="<?php echo base_url(); ?>teacher/student/search"><i class="fa fa-angle-double-right"></i> <?php echo 'Student Records'; ?></a></li>


                    <li class="<?php echo set_Submenu('student/incident'); ?>"><a href="<?php echo base_url(); ?>teacher/student/incident"><i class="fa fa-angle-double-right"></i> Incident Report</a></li>  
                    <!-- <li class="<?php echo set_Submenu('printing/ready'); ?>"><a href="<?php echo base_url(); ?>teacher/printing/ready"><i class="fa fa-angle-double-right"></i>For Printing</a></li>  -->
                    
                    <li class="<?php echo set_Submenu('printing/ready'); ?>"><a href="<?php echo base_url(); ?>teacher/printing/ready"><i class="fa fa-angle-double-right"></i>Ready For Printing</a></li>
					   <?php
                    $get_user_data = $this->session->userdata("student");
                    $get_user_data_id = $get_user_data['teacher_id'];
                    $check_if_coordinator = $this->Usersposition_model->check_coordinator( $get_user_data_id );
                    $header_department_id = $check_if_coordinator['department_id'];
                    if( $check_if_coordinator ){
                        $header_department_details = $this->department_model->get( $header_department_id );
                        $header_department_code = $header_department_details['code']; 
                        if( $header_department_code == 'senior'){
                            ?>
                            <li class="<?php echo set_Submenu('student/student_strand'); ?>"><a href="<?php echo base_url(); ?>teacher/student/update_strand"><i class="fa fa-angle-double-right"></i>Student Strand</a></li> 
                            <?php
                        } else {
                                ?>
                            <li class="<?php echo set_Submenu('student/junior'); ?>"><a href="<?php echo base_url(); ?>teacher/student/junior"><i class="fa fa-angle-double-right"></i>JHS List</a></li> 
                            <?php
                        }
                        ?>
                    <!--    <li class="<?php echo set_Submenu('student/strand'); ?>"><a href="<?php echo base_url(); ?>teacher/student/update_strand"><i class="fa fa-angle-double-right"></i>Tutorial Class</a></li>  -->
                        <?php
                    }
                    if( $get_user_data_id == 3 ){
                        ?>
                        <li class="<?php echo set_Submenu('printing/check'); ?>"><a href="<?php echo base_url(); ?>teacher/printing/check"><i class="fa fa-angle-double-right"></i>Double check</a></li>
                         <li class="<?php echo set_Submenu('ateilla/index'); ?>"><a href="<?php echo base_url(); ?>teacher/ateilla/index"><i class="fa fa-angle-double-right"></i>Student ID Missing Info </a></li> 
                        <li class="<?php echo set_Submenu('printing/readyreg'); ?>"><a href="<?php echo base_url(); ?>teacher/printing/ready_print"><i class="fa fa-angle-double-right"></i>Ready For Printing (All)</a></li>  
                        <li class="<?php echo set_Submenu('printing/goprint'); ?>"><a href="<?php echo base_url(); ?>teacher/printing/goprint"><i class="fa fa-angle-double-right"></i>Go For Print</a></li> 
                        <li class="<?php echo set_Submenu('upload_photo/index'); ?>"><a href="<?php echo base_url(); ?>teacher/printing/upload_photo"><i class="fa fa-angle-double-right"></i>Upload Student Photo</a></li> 
                        <li class="<?php echo set_Submenu('signature/index'); ?>"><a href="<?php echo base_url(); ?>teacher/printing/upload_signature"><i class="fa fa-angle-double-right"></i>Upload Student Sig</a></li> 

                        <?php
                    }
                    ?>
                   
				</ul>
            </li>
 
			<li class="  <?php echo set_Topmenu('subjects'); ?>"><a href="<?php echo base_url(); ?>teacher/subject"><i class="fa fa-mortar-board"></i> <?php echo $this->lang->line('subjects'); ?></a></li> 


                        <li class="treeview <?php echo set_Topmenu('Exam Pass'); ?>">

                            <a href="#">

                                <i class="fa fa-rub"></i> <span>Exam Pass</span> <i class="fa fa-angle-left pull-right"></i>

                            </a>

                            <ul class="treeview-menu">

                                <li class="<?php echo set_Submenu('exampass/search'); ?>"><a href="<?php echo base_url(); ?>teacher/exampass/search"><i class="fa fa-angle-double-right"></i> Student List</a></li> 
                                
                            </ul>

                        </li> 




            <!-- <li class="treeview <?php echo set_Topmenu('clearance'); ?>">
                <a href="#">
                    <i class="fa fa-book"></i> <span>Clearance</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="<?php echo set_Submenu('teacher/clearance'); ?>"><a href="<?php echo base_url(); ?>teacher/clearance"><i class="fa fa-angle-double-right"></i> Flag Students</a></li> 
                    <li class="<?php echo set_Submenu('teacher/clearance/flag_student_list'); ?>"><a href="<?php echo base_url(); ?>teacher/clearance/flag_student_list"><i class="fa fa-angle-double-right"></i> List of Students</a></li> 
                </ul>
            </li>  -->
            
            <li class="treeview <?php echo set_Topmenu('Assignments'); ?>">
                <a href="#">
                    <i class="fa fa-book"></i> <span>Assignments</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="<?php echo set_Submenu('assignments/notify'); ?>"><a href="<?php echo base_url(); ?>teacher/assignments/notify"><i class="fa fa-angle-double-right"></i> Notify</a></li> 
                </ul>
            </li>
			<li class="treeview <?php echo set_Topmenu('Examinations'); ?>">
				<a href="#">
					<i class="fa fa-map-o"></i> <span><?php echo $this->lang->line('grades'); ?></span> <i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
                   <?php 
                    $setting_result = $this->setting_model->get();
                    $import_grade_settings = $setting_result[0]['import_grade'];
                    if( $import_grade_settings == 'yes' ){
                      ?>
                      <li class="<?php echo set_Submenu('import/index'); ?>"><a href="<?php echo base_url(); ?>teacher/grade/import"><i class="fa fa-angle-double-right"></i> Import Grades</a></li>
                      <?php 
                    }
                    ?>
					<li class="<?php echo set_Submenu('importcustom/index'); ?>"><a href="<?php echo base_url(); ?>teacher/grade/importcustomgrade"><i class="fa fa-angle-double-right"></i> Import Custom Grades</a></li>
				    <li class="<?php echo set_Submenu('grade/imported'); ?>"><a href="<?php echo base_url(); ?>teacher/grade/imported"><i class="fa fa-angle-double-right"></i> Imported Grades</a></li>
					<li class="<?php echo set_Submenu('conduct/import'); ?>"><a href="<?php echo base_url(); ?>teacher/conduct/import"><i class="fa fa-angle-double-right"></i> Import Conduct</a></li> 
					<li class="<?php echo set_Submenu('conduct/imported'); ?>"><a href="<?php echo base_url(); ?>teacher/conduct/imported"><i class="fa fa-angle-double-right"></i> Imported Conducts</a></li>
                    <li class="<?php echo set_Submenu('conduct/updates'); ?>"><a href="<?php echo base_url(); ?>teacher/conduct/update_conduct"><i class="fa fa-angle-double-right"></i> Input Conduct</a></li>
                     <li class="<?php echo set_Submenu('request/index'); ?>"><a href="<?php echo base_url(); ?>teacher/request"><i class="fa fa-angle-double-right"></i> Request for Grade</a></li>
				</ul>
			</li>
            <!--li class="treeview <?php echo set_Topmenu('Student Information'); ?>">
                <a href="#">
                    <i class="fa fa-user-plus"></i> <span><?php echo $this->lang->line('student_information'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="<?php echo set_Submenu('student/search'); ?>"><a href="<?php echo base_url(); ?>teacher/student/search"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('student_details'); ?></a></li>
                    <li class="<?php echo set_Submenu('student/create'); ?>"><a href="<?php echo base_url(); ?>teacher/student/create"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('student_admission'); ?></a></li>
                    <li class="<?php echo set_Submenu('category/index'); ?>"><a href="<?php echo base_url(); ?>teacher/category"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('student_categories'); ?></a></li>
                </ul>
            </li-->
            
                <li class="treeview <?php echo set_Topmenu('Attendance'); ?>">
                    <a href="#">
                        <i class="fa fa-calendar-check-o"></i> <span><?php echo $this->lang->line('attendance'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li class="<?php echo set_Submenu('stuattendence/index'); ?>"><a href="<?php echo base_url(); ?>teacher/stuattendence"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('student_attendance'); ?></a></li>
                        <!--li class="<?php echo set_Submenu('stuattendence/attendenceReport'); ?>"><a href="<?php echo base_url(); ?>teacher/stuattendence/attendencereport"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('attendance_by_date'); ?></a></li-->
                        <li class="<?php echo set_Submenu('stuattendence/classattendencereport'); ?>"><a href="<?php echo base_url(); ?>teacher/stuattendence/classattendencereport"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('attendance_report'); ?></a></li> 
                        <li class="<?php echo set_Submenu('stuattendence/import'); ?>"><a href="<?php echo base_url(); ?>teacher/stuattendence/import"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('attendance'); ?>&nbsp;Import</a></li> 
						
					</ul>
                </li>
                <li class="treeview <?php echo set_Topmenu('Academics'); ?>">
                    <a href="#">
                        <i class="fa fa-mortar-board"></i> <span><?php echo $this->lang->line('academics'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li class="<?php echo set_Submenu('academicbehavior/search'); ?>"><a href="<?php echo base_url(); ?>teacher/academicbehavior/search"><i class="fa fa-angle-double-right"></i>Academic Behaviors</a></li>
                    </ul>
                </li-->
                <!--li class="treeview <?php echo set_Topmenu('Examinations'); ?>">
                    <a href="#">
                        <i class="fa fa-map-o"></i> <span><?php echo $this->lang->line('examinations'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li class="<?php echo set_Submenu('exam/index'); ?>"><a href="<?php echo base_url(); ?>teacher/exam"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('exam_list'); ?></a></li>
                        <li class="<?php echo set_Submenu('examschedule/index'); ?>"><a href="<?php echo base_url(); ?>teacher/examschedule"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('exam_schedule'); ?></a></li>
                        <li class="<?php echo set_Submenu('mark/index'); ?>"><a href="<?php echo base_url(); ?>teacher/mark"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('marks_register'); ?></a></li>
						<li class="<?php echo set_Submenu('grade/index'); ?>"><a href="<?php echo base_url(); ?>teacher/grade"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('marks_grade'); ?></a></li>
						<li class="<?php echo set_Submenu('import/index'); ?>"><a href="<?php echo base_url(); ?>teacher/grade/import"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('import_grades'); ?></a></li>
					</ul>
                </li>
                <li class="treeview <?php echo set_Topmenu('Academics'); ?>">
                    <a href="#">
                        <i class="fa fa-mortar-board"></i> <span><?php echo $this->lang->line('academics'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li class="<?php echo set_Submenu('timetable/index'); ?>"><a href="<?php echo base_url(); ?>teacher/timetable"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('class_timetable'); ?></a></li>                            
                        <li class="<?php echo set_Submenu('subject/index'); ?>"><a href="<?php echo base_url(); ?>teacher/subject"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('subjects'); ?></a></li>
                        <li class="<?php echo set_Submenu('teacher/index'); ?>"><a href="<?php echo base_url(); ?>teacher/teacher"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('teachers'); ?></a></li>
                        <li class="<?php echo set_Submenu('classes/index'); ?>"><a href="<?php echo base_url(); ?>teacher/classes"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('class'); ?></a></li>
                        <li class="<?php echo set_Submenu('sections/index'); ?>"><a href="<?php echo base_url(); ?>teacher/sections"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('sections'); ?></a></li>
                    </ul>
                </li-->
                <!--li class="treeview <?php echo set_Topmenu('Download Center'); ?>">
                    <a href="#">
                        <i class="fa fa-download"></i> <span><?php echo $this->lang->line('download_center'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li class="<?php echo set_Submenu('content/index'); ?>"><a href="<?php echo base_url(); ?>teacher/content"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('upload_content'); ?></a></li>
                        <li class="<?php echo set_Submenu('content/assignment'); ?>"><a href="<?php echo base_url(); ?>teacher/content/assignment"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('assignments'); ?></a></li>
                        <li class="<?php echo set_Submenu('content/studymaterial'); ?>"><a href="<?php echo base_url(); ?>teacher/content/studymaterial"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('study_material'); ?></a></li>
                        <li class="<?php echo set_Submenu('content/syllabus'); ?>"><a href="<?php echo base_url(); ?>teacher/content/syllabus"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('syllabus'); ?></a></li>
                        <li class="<?php echo set_Submenu('content/other'); ?>"><a href="<?php echo base_url(); ?>teacher/content/other"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('other_downloads'); ?></a></li>
                    </ul>
                </li-->
                <li class="treeview <?php echo set_Topmenu('Library'); ?>">
                    <a href="#">
                        <i class="fa fa-book"></i> <span><?php echo $this->lang->line('library'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                     
                        <li class="<?php echo set_Submenu('book/getall'); ?>">
                            <a href="<?php echo base_url(); ?>teacher/book/getall"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('book_list'); ?></a></li>
                              <!--li class="<?php echo set_Submenu('book/issue'); ?>">
                             <a href="<?php echo base_url(); ?>teacher/book/issue">
                             <i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('book_issued'); ?></a>
                         </li-->
                        </ul>
                    </li>
					<li class="treeview <?php echo set_Topmenu('Certificate'); ?>">
						<a href="#">
							<i class="fa fa-certificate"></i> <span><?php echo 'Certificate & Awards'; ?></span> <i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu">
							<li class="<?php echo set_Submenu('certificate'); ?>"><a href="<?php echo base_url(); ?>teacher/certificate"><i class="fa fa-angle-double-right"></i> Certificate</a></li>
							<li class="<?php echo set_Submenu('awards'); ?>"><a href="<?php echo base_url(); ?>teacher/awards"><i class="fa fa-angle-double-right"></i> Awards</a></li> 
							<!-- <li class="<?php echo set_Submenu('certificate/diploma'); ?>"><a href="<?php echo base_url(); ?>teacher/certificate/diploma"><i class="fa fa-angle-double-right"></i> Diploma</a></li>  -->
						</ul>
					</li>
                    <!--li class="treeview <?php echo set_Topmenu('Transport'); ?>">
                        <a href="#">
                            <i class="fa fa-bus"></i> <span><?php echo $this->lang->line('transport'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li class="<?php echo set_Submenu('route/index'); ?>"><a href="<?php echo base_url(); ?>teacher/route"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('routes'); ?></a></li>
                            <li class="<?php echo set_Submenu('vehicle/index'); ?>"><a href="<?php echo base_url(); ?>teacher/vehicle"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('vehicles'); ?></a></li>
                            <li class="<?php echo set_Submenu('vehroute/index'); ?>"><a href="<?php echo base_url(); ?>teacher/vehroute"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('assign_vehicle'); ?></a></li>
                        </ul>
                    </li-->
                    <!--li class="treeview <?php echo set_Topmenu('Hostel'); ?>">
                        <a href="#">
                            <i class="fa fa-building-o"></i> <span><?php echo $this->lang->line('hostel'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li class="<?php echo set_Submenu('teacher/hostelroom/index'); ?>"><a href="<?php echo base_url(); ?>teacher/hostelroom"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('hostel_rooms'); ?></a></li>
                            <li class="<?php echo set_Submenu('teacher/roomtype/index'); ?>"><a href="<?php echo base_url(); ?>teacher/roomtype"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('room_type'); ?></a></li>
                            <li class="<?php echo set_Submenu('teacher/hostel/index'); ?>"><a href="<?php echo base_url(); ?>teacher/hostel"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('hostel'); ?></a></li>
                        </ul>

                    </li>-->
                    <li class="treeview <?php echo set_Topmenu('Communicate'); ?>">
                        <a href="#">
                            <i class="fa fa-bullhorn"></i> <span><?php echo $this->lang->line('communicate'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li class="<?php echo set_Submenu('notification/index'); ?>"><a href="<?php echo base_url(); ?>teacher/notification">
                                <i class="fa fa-angle-double-right"></i>
                                <?php echo $this->lang->line('notice_board'); ?>
                            </a></li>
                            <li class="<?php echo set_Submenu('notification/add'); ?>"><a href="<?php echo base_url(); ?>teacher/notification/add"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('send_message'); ?></a></li>
                        </ul>
                    </li>
                    <li class="treeview <?php echo set_Topmenu('Reports'); ?>">
                        <a href="#">
                            <i class="fa fa-line-chart"></i> <span><?php echo $this->lang->line('reports'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <!--li class="<?php echo set_Submenu('student/studentreport'); ?>"><a href="<?php echo base_url(); ?>teacher/student/studentreport"><i class="fa fa-angle-double-right"></i>
                                <?php echo $this->lang->line('student_report'); ?></a></li>

                               
                         
                                    <li class="<?php echo set_Submenu('stuattendence/classattendencereport'); ?>"><a href="<?php echo base_url(); ?>teacher/stuattendence/classattendencereport"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('attendance_report'); ?></a></li>
                                    <li class="<?php echo set_Submenu('mark/index'); ?>"><a href="<?php echo base_url(); ?>teacher/mark"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('exam_marks_report'); ?></a></li-->
									
									<li class="<?php echo set_Submenu('report/student_ranking'); ?>"><a href="<?php echo base_url(); ?>teacher/report/student_ranking"><i class="fa fa-angle-double-right"></i> Student Ranking</a></li>
									<li class="<?php echo set_Submenu('report/honor_students'); ?>"><a href="<?php echo base_url(); ?>teacher/report/honor_students"><i class="fa fa-angle-double-right"></i>  Honor Students</a></li>
									<li class="<?php echo set_Submenu('report/report_card'); ?>"><a href="<?php echo base_url(); ?>teacher/report/report_card"><i class="fa fa-angle-double-right"></i>  Report Card</a></li>
                                    <li class="<?php echo set_Submenu('report/master_sheet'); ?>"><a href="<?php echo base_url(); ?>teacher/report/master_sheet"><i class="fa fa-angle-double-right"></i>  Master Sheet</a></li>
									<li class="<?php echo set_Submenu('report/grade_sheet'); ?>"><a href="<?php echo base_url(); ?>teacher/report/grade_sheet"><i class="fa fa-angle-double-right"></i>  Grade Sheet</a></li>
									 <li class="<?php echo set_Submenu('conduct/grade_sheet'); ?>"><a href="<?php echo base_url(); ?>teacher/conduct/grade_sheet"><i class="fa fa-angle-double-right"></i>  Conduct Grade Sheet</a></li>
								</ul>
                            </li>
						      <li class="treeview <?php echo set_Topmenu('Employee Record'); ?>">
                                <a href="#">
                                    <i class="fa fa-info-circle"></i> <span>Employee Record</span> <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <!-- <li class="<?php echo set_Submenu('dtr/my_dtr'); ?>"><a href="<?php echo base_url(); ?>teacher/dtr/my_dtr"><i class="fa fa-angle-double-right"></i> My DTR</a></li> -->  
                                    <li class="<?php echo set_Submenu('dtr/dtr_summary'); ?>"><a href="<?php echo base_url(); ?>teacher/dtr/dtr_summary"><i class="fa fa-angle-double-right"></i> DTR Summary</a></li>  
                                    <li class="<?php echo set_Submenu('dtr/request_leave'); ?>"><a href="<?php echo base_url(); ?>teacher/dtr/request_leave"><i class="fa fa-angle-double-right"></i> Apply for Leave</a></li>  
                                </ul>
                            </li>  
                        </ul>
                    </section>               
                </aside>
            
                <?php

                $session_information = $this->session->userdata('student');
                $user_role = $session_information['role'];
                $user_id = $session_information[$session_information['role'].'_id'];
         
                ?>
               