<!DOCTYPE html>
<html <?php echo $this->customlib->getRTL();?>>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $this->customlib->getAppName(); ?></title>        
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="theme-color" content="#424242" />
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
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/sweet-alert/sweetalert2.css">
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
    <?php
    if (strpos($_SERVER['REQUEST_URI'], "dashboard") !== false){ 
        $session_information = $this->session->userdata('student');
        $session_name = $session_information['username']."_".$session_information['role'].$session_information[$session_information['role'].'_id']."@".$session_information['sch_name'].".drift"; 
        $session_name = preg_replace('/\s*/', '', $session_name); 
        $session_name = strtolower($session_name);  
        ?>
        <!-- Start of Async Drift Code -->
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
        <!-- End of Async Drift Code -->
        <?php  
    }
    ?>

</head>
<body class="hold-transition skin-blue fixed sidebar-mini">
    <div class="wrapper">
        <header class="main-header">               
            <a href="<?php echo base_url(); ?>principal/principal/dashboard" class="logo" style="background: #6D2932;">                  
                <span class="logo-mini">S S</span>                   
                <span class="logo-lg"><img src="<?php echo base_url(); ?>backend/images/logo.png" alt="<?php echo $this->customlib->getAppName() ?>" /></span>
            </a>               
            <nav class="navbar navbar-static-top" role="navigation" style="background: #6D2932;">                  
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
                <form class="navbar-form navbar-left search-form" role="search"  action="<?php echo site_url('principal/principal/search'); ?>" method="POST">
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
                             <li><a href="<?php echo base_url(); ?>principal/principal/profile"><i class="fa fa-user"></i> <?php echo $this->lang->line('profile'); ?></a>
                            </li> 
                            <li><a href="<?php echo base_url(); ?>principal/principal/changepass"><i class="fa fa-key"></i> <?php echo $this->lang->line('change_password'); ?></a>
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
            <form class="navbar-form navbar-left search-form2" role="search"  action="<?php echo site_url('principal/principal/search'); ?>" method="POST">
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
            <li class="<?php echo set_Topmenu('dashboard'); ?>"><a href="<?php echo base_url(); ?>principal/principal/dashboard"><i class="fa fa-user-secret"></i> <?php echo 'My Dashboard';//$this->lang->line('my_profile'); ?></a></li>
				<!-- <li class="treeview <?php echo set_Topmenu('Finance'); ?>">
                    <a href="#">
                        <i class="fa fa-money"></i> Finance <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li class="<?php echo set_Submenu('finance/collection_report'); ?>"><a href="#"><i class="fa fa-angle-double-right"></i> Collection Report</a></li> 
                        <li class="<?php echo set_Submenu('finance/expense_report'); ?>"><a href="#"><i class="fa fa-angle-double-right"></i> Exprense Report</a></li>
                        <li class="<?php echo set_Submenu('finance/balance_report'); ?>"><a href="#"><i class="fa fa-angle-double-right"></i> Balance Report</a></li>
                    </ul>
                </li> -->
				  <li class="treeview <?php echo set_Topmenu('Student Information'); ?>">
                    <a href="#">
                         <i class="fa fa-user-plus"></i> <span><?php echo $this->lang->line('student_information'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
						<li class="<?php echo set_Submenu('student/search'); ?>"><a href="<?php echo base_url(); ?>principal/student/search"><i class="fa fa-angle-double-right"></i> Student Records</a></li>
						<!-- <li class="<?php echo set_Submenu('stdtransfer/index'); ?>"><a href="<?php echo base_url(); ?>principal/stdtransfer"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('promote_students'); ?></a></li> -->
						<li class="<?php echo set_Submenu('student/reserved'); ?>"><a href="<?php echo base_url(); ?>principal/reserved"><i class="fa fa-angle-double-right"></i> Reserved Students</a></li> 
                       <!--  <li class="<?php echo set_Submenu('reserved/paidonline'); ?>"><a href="<?php echo base_url(); ?>principal/reserved/viewpaid"><i class="fa fa-angle-double-right"></i> Online Payment</a></li> 
                        <li class="<?php echo set_Submenu('student/rprommisory'); ?>"><a href="<?php echo base_url(); ?>principal/reserved/reserved_promissory"><i class="fa fa-angle-double-right"></i> Pending Students</a></li> 
                        <li class="<?php echo set_Submenu('student/promissory'); ?>"><a href="<?php echo base_url(); ?>principal/reserved/promissory"><i class="fa fa-angle-double-right"></i> Approved Promissory</a></li>  -->
                    </ul>
                </li>
                <!--<li><a href="<?php echo base_url(); ?>principal/student/search"><i class="fa fa-users"></i> Student Records </a></li>-->
                <li class="treeview <?php echo set_Topmenu('Teachers'); ?>">
                    <a href="#">
                        <i class="fa fa-mortar-board"></i> <span>Teachers</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                         <li class="<?php echo set_Submenu('teacher/index'); ?>"><a href="<?php echo base_url(); ?>principal/teacher"><i class="fa fa-angle-double-right"></i>Add Teacher</a></li>
                         <li class="<?php echo set_Submenu('teacher/assignTeacher'); ?>"><a href="<?php echo base_url(); ?>principal/teacher/assignteacher"><i class="fa fa-angle-double-right"></i>Assign Teacher</a></li>
                        <li class="<?php echo set_Submenu('teacher/advisers'); ?>"><a href="<?php echo base_url(); ?>principal/teacher/advisers"><i class="fa fa-angle-double-right"></i>Adviser</a></li>
                    </ul>
               </li>
               <li class="treeview <?php echo set_Topmenu('Sectioning'); ?>">
                    <a href="#">
                        <i class="fa fa-user"></i> <span>Sectioning</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu"> 
                        <li class="<?php echo set_Submenu('sections/assign'); ?>"><a href="<?php echo base_url(); ?>principal/sections/assign"><i class="fa fa-angle-double-right"></i>Student Sectioning</a></li>  
                        <li class="<?php echo set_Submenu('classes/index'); ?>"><a href="<?php echo base_url(); ?>principal/classes/index"><i class="fa fa-angle-double-right"></i>Class Sectioning</a></li> 
                        <li class="<?php echo set_Submenu('sections/index'); ?>"><a href="<?php echo base_url(); ?>principal/sections/index"><i class="fa fa-angle-double-right"></i>Add Section</a></li> 
                    </ul>
                </li>
                <li class="treeview <?php echo set_Topmenu('Clearance'); ?>">
                    <a href="#">
                        <i class="fa fa-book"></i> <span>Clearance</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu"> 
                         <li class="<?php echo set_Submenu('clearance/masterlist'); ?>"><a href="<?php echo base_url(); ?>principal/clearance"><i class="fa fa-angle-double-right"></i>Master List</a></li> 
                        <!-- <li class="<?php echo set_Submenu('clearance/students'); ?>"><a href="<?php echo base_url(); ?>principal/clearance/students"><i class="fa fa-angle-double-right"></i>List of students</a></li>  -->
                    </ul>
                </li>
                <li><a href="<?php echo base_url(); ?>principal/classes"><i class="fa fa-mortar-board"></i> Grade Levels</a></li>
              
                <li class="treeview <?php echo set_Topmenu('Examinations'); ?>">
                <a href="#">
                    <i class="fa fa-map-o"></i> <span><?php echo $this->lang->line('grades'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="<?php echo set_Submenu('grade/imported_grade'); ?>"><a href="<?php echo base_url(); ?>principal/grade/imported_grade/1"><i class="fa fa-angle-double-right"></i> Imported Grades</a></li>
                    <!-- MARQUEZ -->
                    <li class="<?php echo set_Submenu('grade/post'); ?>"><a href="<?php echo base_url(); ?>principal/grade/postgrade"><i class="fa fa-angle-double-right"></i> Post Grades</a></li>
                    <!-- END -->
                    <li class="<?php echo set_Submenu('grade/input'); ?>"><a href="<?php echo base_url(); ?>principal/grade/inputgrade"><i class="fa fa-angle-double-right"></i> Input Grade</a></li>
                    <li class="<?php echo set_Submenu('grade/updates'); ?>"><a href="<?php echo base_url(); ?>principal/grade/update_grades"><i class="fa fa-angle-double-right"></i> Update Grades</a></li>
                     <!-- <li class="<?php echo set_Topmenu('Grades Imported'); ?>"><a href="<?php echo base_url(); ?>principal/grade/teacher_report"><i class="fa fa-angle-double-right"></i> Teacher Grades Status</a></li> -->
                      <li class="<?php echo set_Submenu('request/grade'); ?>"><a href="<?php echo base_url(); ?>principal/request/grade_requests_new"><i class="fa fa-angle-double-right"></i> Grade Requests</a></li>
                </ul>
            </li>
            <li class="treeview <?php echo set_Topmenu('Conduct'); ?>">
                <a href="#">
                    <i class="fa fa-map-o"></i> <span>Conduct</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                  <!--   <li class="<?php echo set_Submenu('conduct/post'); ?>"><a href="<?php echo base_url(); ?>principal/conduct/postconduct"><i class="fa fa-angle-double-right"></i> Post Conduct Grades</a></li>
                    <li class="<?php echo set_Submenu('grade/input'); ?>"><a href="<?php echo base_url(); ?>principal/grade/inputgrade"><i class="fa fa-angle-double-right"></i> Update Subject Grade</a></li> -->
                      <li class="<?php echo set_Submenu('conductrequest/grade'); ?>"><a href="<?php echo base_url(); ?>principal/conductrequest"><i class="fa fa-angle-double-right"></i> Conduct Requests</a></li>
                </ul>
            </li>
            <!-- Gate pass -->
             <li class="treeview <?php echo set_Topmenu('GatePass'); ?>">
                            <a href="#">
                                <i class="fa fa-building-o"></i> Gate Pass</span> <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li class="<?php echo set_Submenu('gatepass/records'); ?>"><a href="<?php echo base_url(); ?>principal/gatepass/records"><i class="fa fa-angle-double-right"></i> For Approval</a></li>
                                <li class="<?php echo set_Submenu('gatepass/tapreport'); ?>"><a href="<?php echo base_url(); ?>principal/gatepass/tapreport"><i class="fa fa-angle-double-right"></i> Tap Report</a></li> 
                            </ul>
                        </li> 
            <!-- MARQUEZ -->
            <li class="treeview <?php echo set_Topmenu('Academics'); ?>">
                <a href="#">
                    <i class="fa fa-mortar-board"></i> <span><?php echo $this->lang->line('academics'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="<?php echo set_Submenu('subject/subject_add'); ?>"><a href="<?php echo base_url(); ?>principal/subject/subject_add"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('subjects'); ?></a></li>
                    <li class="<?php echo set_Submenu('subjectmanager/index'); ?>"><a href="<?php echo base_url(); ?>principal/subjectmanager/index"><i class="fa fa-angle-double-right"></i>Subject Manager</a></li>
                    <li class="<?php echo set_Submenu('subjectorder/index'); ?>"><a href="<?php echo base_url(); ?>principal/subjectorder/index"><i class="fa fa-angle-double-right"></i>Subject Order</a></li>
                    <li class="<?php echo set_Submenu('strand/index'); ?>"><a href="<?php echo base_url(); ?>principal/strand"><i class="fa fa-angle-double-right"></i> <?php echo "Strands"; ?></a></li>
                </ul>
            </li>
            <li class="treeview <?php echo set_Topmenu('Customize'); ?>">
                <a href="#">
                    <i class="fa fa-map-o"></i><span>Customize Subject</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="<?php echo set_Submenu('customsubject/index'); ?>"><a href="<?php echo base_url(); ?>principal/customsubject/"><i class="fa fa-angle-double-right"></i>Add/Drop Subject</a></li>
                </ul>
           </li>

            <!-- END -->
                <!-- <li><a href="<?php echo base_url(); ?>principal/subject"><i class="fa fa-mortar-board"></i> <?php echo $this->lang->line('subjects'); ?></a></li>   -->
                <!-- <li><a href="<?php echo base_url(); ?>principal/guidance"><i class="fa fa-user"></i> Guidance Counselor</a></li>  
                <li><a href="<?php echo base_url(); ?>principal/prefect"><i class="fa fa-user"></i> Prefect of Discipline</a></li>   -->
    			<!-- <li><a href="<?php echo base_url(); ?>principal/clinic"><i class="fa fa-hospital-o"></i> Clinic</a></li>   -->
                <li class="treeview <?php echo set_Topmenu('Attendance'); ?>">
                    <a href="#">
                        <i class="fa fa-calendar-check-o"></i><span><?php echo $this->lang->line('attendance_report'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li class="<?php echo set_Submenu('stuattendence/classattendencereport'); ?>"><a href="<?php echo base_url(); ?>principal/stuattendence/classattendencereport"><i class="fa fa-angle-double-right"></i> Attendance Report</a></li>
                        <li class="<?php echo set_Submenu('stuattendence/attendanceSummary'); ?>"><a href="<?php echo base_url(); ?>principal/stuattendence/attendanceSummary"><i class="fa fa-angle-double-right"></i> Attendance Summary</a></li>
                    </ul>
                </li>
                <li class="treeview <?php echo set_Topmenu('Library'); ?>">
                    <a href="#">
                        <i class="fa fa-book"></i> <span><?php echo $this->lang->line('library'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                     
                        <li class="<?php echo set_Submenu('book/getall'); ?>">
                            <a href="<?php echo base_url(); ?>principal/book/getall"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('book_list'); ?></a></li>
                              <!--li class="<?php echo set_Submenu('book/issue'); ?>">
                             <a href="<?php echo base_url(); ?>principal/book/issue">
                             <i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('book_issued'); ?></a>
                         </li-->
                        </ul>
                    </li> 
                    <li class="treeview <?php echo set_Topmenu('Certificate'); ?>">
                        <a href="#">
                            <i class="fa fa-certificate"></i> <span><?php echo 'Certificate & Awards'; ?></span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li class="<?php echo set_Submenu('clearance/tutorial_form'); ?>"><a href="<?php echo base_url(); ?>principal/clearance/tutorial_form"><i class="fa fa-angle-double-right"></i> Tutorial Form</a></li>
                        </ul>
                    </li>
                    <li class="treeview <?php echo set_Topmenu('Communicate'); ?>">
                        <a href="#">
                            <i class="fa fa-bullhorn"></i> <span><?php echo $this->lang->line('communicate'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li class="<?php echo set_Submenu('notification/index'); ?>"><a href="<?php echo base_url(); ?>principal/notification">
                                <i class="fa fa-angle-double-right"></i>
                                <?php echo $this->lang->line('notice_board'); ?>
                            </a></li>
                            <li class="<?php echo set_Submenu('notification/add'); ?>"><a href="<?php echo base_url(); ?>principal/notification/add"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('send_message'); ?></a></li>
                        </ul>
                    </li>
                     <li class=" <?php echo set_Topmenu('Events'); ?>">
                        <a href="<?php echo base_url(); ?>principal/events"><i class="fa fa-calendar"></i>
                        <?php 
                        echo ' Events'; 
                        $count_all_pending_events_results = $this->db->where(['approved'=>'no'])->from("events")->count_all_results(); 

                        if ($count_all_pending_events_results) {
                            ?>
                            <small class="label pull-right bg-red">
                                <?php echo $count_all_pending_events_results; ?>
                            </small>
                            <?php
                        }
                        ?> 
                        </a>

                    </li>
                    <li class="treeview <?php echo set_Topmenu('Reports'); ?>">
                        <a href="#">
                            <i class="fa fa-line-chart"></i> <span><?php echo $this->lang->line('reports'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <!-- MARQUEZ -->
                                <li class="<?php echo set_Submenu('report/principal_list'); ?>"><a href="<?php echo base_url(); ?>principal/report/principal_list"><i class="fa fa-angle-double-right"></i> Principal List</a></li>
                            <!-- Gels -->   
                                 <li class="<?php echo set_Submenu('report/academic_list'); ?>"><a href="<?php echo base_url(); ?>principal/report/academic_list"><i class="fa fa-angle-double-right"></i> Academic List</a></li>
                                
                            <!-- END -->
								<li class="<?php echo set_Submenu('report/student_ranking'); ?>"><a href="<?php echo base_url(); ?>principal/report/student_ranking"><i class="fa fa-angle-double-right"></i> Student Ranking</a></li>
								<li class="<?php echo set_Submenu('report/honor_students'); ?>"><a href="<?php echo base_url(); ?>principal/report/honor_students"><i class="fa fa-angle-double-right"></i>  Honor Students</a></li>
                                <li class="<?php echo set_Submenu('report/master_sheet'); ?>"><a href="<?php echo base_url(); ?>principal/report/master_sheet"><i class="fa fa-angle-double-right"></i>  Master Sheet</a></li>
                                <li class="<?php echo set_Submenu('report/grade_sheet'); ?>"><a href="<?php echo base_url(); ?>principal/report/grade_sheet"><i class="fa fa-angle-double-right"></i>  Grade Sheet</a></li>
                                <li class="<?php echo set_Submenu('report/failing_grades_summary'); ?>"><a href="<?php echo base_url(); ?>principal/report/failing_grades_summary"><i class="fa fa-angle-double-right"></i>  Failing Grades Summary</a></li>
								<li class="<?php echo set_Submenu('report/report_card'); ?>"><a href="<?php echo base_url(); ?>principal/report/report_card"><i class="fa fa-angle-double-right"></i>  Report Card</a></li>
								<!-- <li class="<?php echo set_Submenu('report/student_master_sheet'); ?>"><a href="<?php echo base_url(); ?>principal/report/student_master_sheet"><i class="fa fa-angle-double-right"></i> Student Master Sheet</a></li> -->
								<li class="<?php echo set_Submenu('report/parent_master_sheet'); ?>"><a href="<?php echo base_url(); ?>principal/report/parent_master_sheet"><i class="fa fa-angle-double-right"></i> Parent Master Sheet</a></li>
                                <li class="<?php echo set_Submenu('report/incidentList'); ?>"><a href="<?php echo base_url(); ?>principal/report/incidentList"><i class="fa fa-angle-double-right"></i> Student Incident Report</a></li>
                                <li class="<?php echo set_Submenu('conduct/mastersheet'); ?>"><a href="<?php echo base_url(); ?>principal/conduct/conduct_mastersheet"><i class="fa fa-angle-double-right"></i>  Conduct Master Sheet</a></li>
							</ul>
                            </li>
							 <li class="treeview <?php echo set_Topmenu('Employee Record'); ?>">
                                <a href="#">
                                    <i class="fa fa-info-circle"></i> <span>Employee Record</span> <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <!-- <li class="<?php echo set_Submenu('dtr/my_dtr'); ?>"><a href="<?php echo base_url(); ?>principal/dtr/my_dtr"><i class="fa fa-angle-double-right"></i> My DTR</a></li>   -->
                                    <li class="<?php echo set_Submenu('dtr/dtr_summary'); ?>"><a href="<?php echo base_url(); ?>principal/dtr/dtr_summary"><i class="fa fa-angle-double-right"></i> DTR Summary</a></li>  
                                    <li class="<?php echo set_Submenu('dtr/request_leave'); ?>"><a href="<?php echo base_url(); ?>principal/dtr/request_leave"><i class="fa fa-angle-double-right"></i> Apply for Leave</a></li> 
                                    <!-- <li class="<?php echo set_Submenu('dtr/leave_request'); ?>"><a href="<?php echo base_url(); ?>principal/dtr/leave_request"><i class="fa fa-angle-double-right"></i> Leave Request</a></li>   -->
                                </ul>
                            </li>
                           <!--  <li class=" <?php echo set_Topmenu('Posters'); ?>">
                                <a href="<?php echo base_url(); ?>principal/posters"><i class="fa fa-object-group"></i>
                                <?php 
                                echo 'School Banners'
                                ?> 
                                </a>

                            </li> -->

                            
							<!--  <li class="treeview <?php echo set_Topmenu('School Settings'); ?>">
								<a href="#">
									<i class="fa fa-cog"></i> <span>School Settings</span> <i class="fa fa-angle-left pull-right"></i>
								</a>
								<ul class="treeview-menu"> 
									<li class="<?php echo set_Submenu('principal/sessions/index'); ?>"><a href="<?php echo base_url(); ?>principal/sessions"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('session_setting'); ?></a></li>
								</ul>
							</li> -->
                        </ul>
                    </section>               
                </aside>
                <!-- Modal: modalPoll -->
                <div class="modal fade right" id="modalPoll" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true" data-backdrop="false">
                    <div class="modal-dialog modal-full-height modal-right modal-notify modal-info" role="document">
                        <div class="modal-content">
                            <!--Header-->
                            <div class="modal-header">
                                <span class="heading lead">Bridgette Users Survey
                                </span>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="white-text">×</span>
                                </button>
                            </div>
                            <!--Body-->
                            <div class="modal-body" style="background-color: white !important; color: black !important;">
                                <div class="text-center"> 
                                    <p>
                                        <strong>Your opinion matters</strong>
                                    </p>
                                    <p>Have some ideas how to improve Bridgette?
                                        <strong>Give us your feedback.</strong>
                                    </p>
                                </div>
                                <hr>
                                <!-- Radio -->
                                <p class="">
                                    <strong>Customer Support</strong>
                                </p>
                                <div class="form-check mb-4">
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="customer_support" value="5" checked>Very good
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="customer_support" value="4">Good
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="customer_support" value="3">Mediocre
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="customer_support" value="2">Bad
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="customer_support" value="1">Very bad
                                    </label> 
                                </div>
                                <br/>
                                <!-- Radio -->
                                <p class="">
                                    <strong>Grading</strong>
                                </p>
                                <div class="form-check mb-4">
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="grading_feedback" value="5" checked>Very good
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="grading_feedback" value="4">Good
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="grading_feedback" value="3">Mediocre
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="grading_feedback" value="2">Bad
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="grading_feedback" value="1">Very bad
                                    </label> 
                                </div>
                                <br/>
                                <!-- Radio -->
                                <p class="">
                                    <strong>Student Tapping</strong>
                                </p>
                                <div class="form-check mb-4">
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="student_tapping" value="5" checked>Very good
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="student_tapping" value="4">Good
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="student_tapping" value="3">Mediocre
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="student_tapping" value="2">Bad
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="student_tapping" value="1">Very bad
                                    </label> 
                                </div>
                                <br/>
                                <!-- Radio -->
                                <p class="">
                                    <strong>DTR Attendance</strong>
                                </p>
                                <div class="form-check mb-4">
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="dtr_attendance" value="5" checked>Very good
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="dtr_attendance" value="4">Good
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="dtr_attendance" value="3">Mediocre
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="dtr_attendance" value="2">Bad
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="dtr_attendance" value="1">Very bad
                                    </label> 
                                </div>
                                <br/>
                                <!-- Radio -->
                                <p class="">
                                    <strong>SMS Notification</strong>
                                </p>
                                <div class="form-check mb-4">
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="sms_notification" value="5" checked>Very good
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="sms_notification" value="4">Good
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="sms_notification" value="3">Mediocre
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="sms_notification" value="2">Bad
                                    </label> 
                                    <label class="radio-inline form-check-label">
                                    <input type="radio" name="sms_notification" value="1">Very bad
                                    </label> 
                                </div>
                                <br/>
                                <p class="">
                                    <strong>What could we improve?</strong>
                                </p>
                                <!--Basic textarea-->
                                <div class="md-form">
                                    <textarea type="text" id="remarks_feedback" class="md-textarea form-control" rows="3" name="remarks_feedback"></textarea>
                                    <label for="remarks_feedback">Your message</label>
                                </div>
                            </div>
                            <!--Footer-->
                            <div class="modal-footer justify-content-center">
                                <a type="button" class="btn btn-success waves-effect waves-light" id="send_survey">Send
                                <i class="fa fa-paper-plane ml-1"></i>
                                </a> 
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal: modalPoll -->
                <?php

                $session_information = $this->session->userdata('student');
                $user_role = $session_information['role'];
                $user_id = $session_information[$session_information['role'].'_id'];
         
                ?>
                <script type="text/javascript">
                $( document ).ready(function() {
                    var user_role = "<?php echo $user_role; ?>"; 
                    var user_id = "<?php echo $user_id; ?>"; 
          
                    $.ajax({
                        url: 'http://157.230.43.105/survey/survey/checkifalreadygivenfeedback',
                        type: 'post',
                        data: { 'user_role': user_role,
                                'user_id': user_id,
                                'school': "MMA"},
                        success: function (response) {
                            if( response == "null" ){ 
                                $("#modalPoll").modal('show'); 
                            } else { 

                            } 
                        }
                    });
                });
                $( "#send_survey" ).click(function() {
                    var customer_support = $("div#modalPoll input[name='customer_support']:checked"). val();
                    var grading_feedback = $("div#modalPoll input[name='grading_feedback']:checked"). val();
                    var student_tapping = $("div#modalPoll input[name='student_tapping']:checked"). val();
                    var dtr_attendance = $("div#modalPoll input[name='dtr_attendance']:checked"). val();
                    var sms_notification = $("div#modalPoll input[name='sms_notification']:checked"). val();
                    var remarks = $. trim($("div#modalPoll #remarks_feedback"). val());
                    var user_role = "<?php echo $user_role; ?>"; 
                    var user_id = "<?php echo $user_id; ?>"; 
          
                    $.ajax({
                        url: 'http://157.230.43.105/survey/survey/save_survey',
                        type: 'post',
                        data: { 'customer_support': customer_support, 
                                'grading_feedback': grading_feedback, 
                                'student_tapping': student_tapping,
                                'dtr_attendance': dtr_attendance,
                                'sms_notification': sms_notification,
                                'remarks': remarks,
                                'user_role': user_role,
                                'user_id': user_id,
                                'school': "MMA"},
                        success: function (response) {
                            console.log(response);
                        }
                    });

                    $("#modalPoll").modal('hide');
                    alert("Thank you for your feedback!");
                });
                </script>
