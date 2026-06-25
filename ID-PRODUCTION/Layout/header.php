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

    <link href="<?php print base_url(); ?>backend/signature/jquery.signaturepad.css" rel="stylesheet">
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
    <script src="<?php echo base_url().'backend/signature/jquery.signaturepad.js'?>"></script>
    <script>
    $(document).ready(function() {
      $('.sigPad').signaturePad({drawOnly:true, penColour:#13161a}); 
    });
    </script>

    <script type='text/javascript' src="https://github.com/niklasvh/html2canvas/releases/download/0.4.1/html2canvas.js"></script>
    <script src="<?php echo base_url().'backend/signature/json2.min.js'?>"></script>

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
            <a href="<?php echo base_url(); ?>idproduction/idproduction/dashboard" class="logo" style="background: #6D2932;">                  
                <!-- <span class="logo-mini">S S</span>                    -->
                <span class="logo-lg"><img src="<?php echo base_url(); ?>backend/images/logo.png"  /></span>
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
                <form class="navbar-form navbar-left search-form" role="search"  action="<?php echo site_url('idproduction/idproduction/search'); ?>" method="POST">
                 <?php echo $this->customlib->getCSRF(); ?>
                 <div class="input-group" style="padding-top:3px;">
                    <!-- <input type="text" name="search_text" class="form-control search-form search-form3" placeholder="<?php echo $this->lang->line('search_by_name,_roll_no,_enroll_no,_national_identification_no,_local_identification_no_etc..'); ?>">
                    <span class="input-group-btn">
                        <button type="submit" name="search" id="search-btn" style="padding: 3px 12px !important;border-radius: 0px 30px 30px 0px; background: #fff;" class="btn btn-flat"><i class="fa fa-search"></i></button>
                    </span> -->
                </div>
            </form>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav"> 
                  
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false"><?php echo $this->customlib->getStudentSessionUserName(); ?>
                            <i class="fa fa-user-secret fa-fw"></i>  <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="<?php echo base_url(); ?>idproduction/idproduction/profile"><i class="fa fa-user"></i> <?php echo $this->lang->line('profile'); ?></a></li>
                            <li><a href="<?php echo base_url(); ?>idproduction/idproduction/changepass"><i class="fa fa-key"></i> <?php echo $this->lang->line('change_password'); ?></a>
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
    <aside class="main-sidebar" style="background: #6D2932;">                
        <section class="sidebar" id="sibe-box">
            <form class="navbar-form navbar-left search-form2" role="search"  action="<?php echo site_url('idproduction/idproduction/search'); ?>" method="POST">
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
    				<h2 class="school-yr text-center"><?php echo "SY " . $this->setting_model->getCurrentSessionName(); ?></h2>
                </li>
                <li class="  <?php echo set_Topmenu('dashboard'); ?>"><a href="<?php echo base_url(); ?>idproduction/idproduction/dashboard"><i class="fa fa-user-secret"></i> <?php echo 'My Dashboard'; ?></a></li>  
                <!-- <li class="  <?php echo set_Topmenu('Faculties'); ?>"><a href="<?php echo base_url(); ?>idproduction/faculties"><i class="fa fa-users"></i> Faculties</a></li>   -->


                <li class="treeview <?php echo set_Topmenu('Faculties'); ?>">
                    <a href="#">
                        <i class="fa fa-users"></i> <span>Faculties</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li class="<?php echo set_Submenu('faculties/index'); ?>">
                            <a href="<?php echo base_url(); ?>idproduction/faculties">
                                <i class="fa fa-angle-double-right"></i> Active  
                            </a>
                        </li>  
                        <li class="<?php echo set_Submenu('faculties/inactive'); ?>">
                            <a href="<?php echo base_url(); ?>idproduction/faculties/inactive">
                                <i class="fa fa-angle-double-right"></i> In-Active  
                            </a>
                        </li>  
                    </ul>
                </li> 
                

                <li class="treeview <?php echo set_Topmenu('Students'); ?>">
                    <a href="#">
                        <i class="fa fa-users"></i> <span>Students</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li class="<?php echo set_Submenu('search'); ?>">
                            <a href="<?php echo base_url(); ?>idproduction/students/search">
                                <i class="fa fa-angle-double-right"></i> Search
                            </a>
                        </li>
                        <li class="<?php echo set_Submenu('pending'); ?>">
                            <a href="<?php echo base_url(); ?>idproduction/students/pending">
                                <i class="fa fa-angle-double-right"></i> Pending
                            </a>
                        </li>
                        <li class="<?php echo set_Submenu('needtoedit'); ?>">
                            <a href="<?php echo base_url(); ?>idproduction/students/needtoedit">
                                <i class="fa fa-angle-double-right"></i> Need to Edit
                            </a>
                        </li>
                        <li class="<?php echo set_Submenu('student_generatation'); ?>">
                            <a href="<?php echo base_url(); ?>idproduction/students/student_generatation" target="_blank">
                                <i class="fa fa-angle-double-right"></i> Generate Pending
                            </a>
                        </li>  
                        <li class="<?php echo set_Submenu('approval'); ?>">
                            <a href="<?php echo base_url(); ?>idproduction/students/approval">
                                <i class="fa fa-angle-double-right"></i> Approve Generated
                            </a>
                        </li>  
                        <li class="<?php echo set_Submenu('approval'); ?>">
                            <a href="<?php echo base_url(); ?>idproduction/students/approved_list">
                                <i class="fa fa-angle-double-right"></i> Approve List
                            </a>
                        </li>  
                        <li class="<?php echo set_Submenu('process'); ?>">
                            <a href="<?php echo base_url(); ?>idproduction/students/process">
                                <i class="fa fa-angle-double-right"></i> Process 
                            </a>
                        </li>  
                        <li class="<?php echo set_Submenu('delivered'); ?>">
                            <a href="<?php echo base_url(); ?>idproduction/students/delivered">
                                <i class="fa fa-angle-double-right"></i> Delivered  
                            </a>
                        </li> 
                        <li class="<?php echo set_Submenu('summary'); ?>">
                            <a href="<?php echo base_url(); ?>idproduction/students/summary">
                                <i class="fa fa-angle-double-right"></i> Summary  
                            </a>
                        </li> 
                        <li class="<?php echo set_Submenu('needtoapproved'); ?>">
                            <a href="<?php echo base_url(); ?>idproduction/students/needtoapproved">
                                <i class="fa fa-angle-double-right"></i> Need To Approved  
                            </a>
                        </li> 
                    </ul>
                </li> 

                <li class="treeview <?php echo set_Topmenu('Teachers'); ?>">
					<a href="#">
						<i class="fa fa-mortar-board"></i> <span>Teachers</span> <i class="fa fa-angle-left pull-right"></i>
					</a>
					<ul class="treeview-menu">
						<li class="<?php echo set_Submenu('teacher/index'); ?>"><a href="<?php echo base_url(); ?>idproduction/teacher"><i class="fa fa-angle-double-right"></i> Add Teacher</a></li>
						<!-- <li class="<?php echo set_Submenu('teacher/assignTeacher'); ?>"><a href="<?php echo base_url(); ?>registrar/teacher/assignteacher"><i class="fa fa-angle-double-right"></i> Assign Teacher</a></li> -->
						<!-- <li class="<?php echo set_Submenu('teacher/advisers'); ?>"><a href="<?php echo base_url(); ?>registrar/teacher/advisers"><i class="fa fa-angle-double-right"></i> Adviser</a></li> -->
					</ul>
			  </li>




















            </ul>
        </section>               
    </aside>
