<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-bullhorn"></i> <?php echo $this->lang->line('communicate'); ?> <small><?php echo $this->lang->line('student_fee1'); ?></small>
        </h1>
  <!--        <ul class="breadcrumb"><li><a href="/"><i class="fa fa-home"></i>Home</a></li>
  <li><a href="#">Fee Management</a></li>
  <li class="active">Student</li>
  </ul>   -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if ($this->session->flashdata('msg')) { ?>
                    <?php echo $this->session->flashdata('msg') ?>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid1">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-commenting-o"></i> <?php echo $this->lang->line('notice_board'); ?></h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo base_url() ?>teacher/notification/add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?php echo $this->lang->line('post_new_message'); ?></a>


                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="box-group" id="accordion">

                            <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                            <?php if (empty($notificationlist)) {
                                ?>
                                <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                                <?php
                            } else {
                                foreach ($notificationlist as $key => $notification) {
                                    $current_status = $this->SMSM->check_notification($notification['id']);
                                    
                                    $approved = $notification['approved'];
                                     if ($approved == 'bypass' || !empty($notification['custom_student_ids'])) {
                                         $approved_display = '<b class="text-success">Direct SMS</b>'; // UI shows "Direct SMS"
                                     } else if( $approved == 'yes' ){
                                         $approved_display = '<b class="text-success">Approved</b>';
                                         
                                     } else {
                                         if ($current_status != 'idle' && $current_status != FALSE) {
                                             $approved_display = '<b class="text-success">Sent</b>';
                                         } else {
                                             $approved_display = '<b class="text-warning">Pending</b>';
                                         }
                                     }
                                    ?>
                                    <div class="panel box box-primary">
                                        <div class="box-header with-border">
                                            <h4 class="box-title">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $notification['id']; ?>" aria-expanded="false" class="collapsed">
                                                    <?php echo $notification['title']; ?>
                                                </a>


                                            </h4>

                                            <div class="pull-right">
                                                <span><?php echo  $approved_display; ?></span>
                                                &nbsp;  &nbsp;  
                                                <?php
                                                if( ($approved == 'no'|| $approved == FALSE) && empty($notification['custom_student_ids']) && ($current_status == 'idle' || $current_status == FALSE) ){
													if( $teacher_id == $notification['created_id'] && $notification['created_by'] == 'teacher' ){
                                                    ?>
													 <a href="<?php echo base_url() ?>teacher/notification/edit/<?php echo $notification['id'] ?>" class="" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>" data-original-title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    &nbsp;  <a href="<?php echo base_url() ?>teacher/notification/delete/<?php echo $notification['id'] ?>" class="" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" data-original-title="<?php echo $this->lang->line('delete'); ?>">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                    <?php
													} else {
														?>
														<a href="<?php echo base_url() ?>teacher/notification/edit/<?php echo $notification['id'] ?>" class="" data-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>" data-original-title="<?php echo $this->lang->line('view'); ?>">
															<i class="fa fa-reorder"></i>
														</a>
														<?php
													}
                                                } else {
													?> 	<a href="<?php echo base_url() ?>teacher/notification/edit/<?php echo $notification['id'] ?>" class="" data-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>" data-original-title="<?php echo $this->lang->line('view'); ?>">
															<i class="fa fa-reorder"></i>
														</a>
													<?php
												} 
                                                if(  $current_status == 'idle' || $current_status == FALSE ){ 
                                                    ?>
                                                    
                                                    <?php
                                                } else {
                                                     if ($approved == 'yes' || $approved == 'bypass' || !empty($notification['custom_student_ids'])) {
                                                         echo '<span><b class="text-success">Sent</b></span>';
                                                     }
                                                }
                                                ?>

                                            </div>
                                        </div>
                                        <div id="collapse<?php echo $notification['id']; ?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                            <div class="box-body">
                                                <div class="row">

                                                    <div class="col-md-9">

                                                        <?php echo $notification['message']; ?>
                                                    </div><!-- /.col -->
                                                    <div class="col-md-3">

                                                        <div class="box box-solid">

                                                            <div class="box-body no-padding">
                                                                <ul class="nav nav-pills nav-stacked">
                                                                    <li><i class="fa fa-calendar-check-o"></i> <?php echo $this->lang->line('publish_date'); ?> : <?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($notification['publish_date'])); ?> </li>
                                                                    <li><i class="fa fa-calendar"></i> <?php echo $this->lang->line('notice_date'); ?> : <?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($notification['date'])); ?> </li>

                                                                </ul>
                                                                <h4 class="text text-primary"> <?php echo $this->lang->line('message_to'); ?></h4>
                                                                <ul class="nav nav-pills nav-stacked">
                                                                    <li><i class="fa fa-user" aria-hidden="true"></i> <?php echo $this->lang->line('student'); ?> : <?php echo $notification['visible_student']; ?> </li>
                                                                    <li>
                                                                        <i class="fa fa-user" aria-hidden="true"></i>
                                                                        <?php echo $this->lang->line('parent'); ?> : 
                                                                        <?php 
                                                                        if ($notification['visible_parent'] == 'Yes' || $notification['visible_parent'] == 'yes') {
                                                                            $specific_students = array();
                                                                            $specific_class_sections = array();

                                                                            if (!empty($notification['custom_student_ids'])) {
                                                                                $student_ids = explode(',', $notification['custom_student_ids']);
                                                                                $this->db->select('students.firstname, students.lastname, students.guardian_name, students.guardian_phone, classes.class, sections.section, student_session.class_id, student_session.section_id');
                                                                                $this->db->from('students');
                                                                                $this->db->join('student_session', 'student_session.student_id = students.id AND student_session.session_id = (SELECT session_id FROM sch_settings LIMIT 1)', 'left', FALSE);
                                                                                $this->db->join('classes', 'classes.id = student_session.class_id', 'left');
                                                                                $this->db->join('sections', 'sections.id = student_session.section_id', 'left');
                                                                                $this->db->where_in('students.id', $student_ids);
                                                                                $query = $this->db->get();
                                                                                $specific_students = $query->result_array();
                                                                                
                                                                                foreach ($specific_students as $student) {
                                                                                    if (!empty($student['class_id']) && !empty($student['section_id'])) {
                                                                                        $key = $student['class_id'] . $student['section_id'];
                                                                                        $specific_class_sections[$key] = true;
                                                                                    }
                                                                                }
                                                                            }

                                                                            $all_parents_sections = array();
                                                                            if (!empty($notification['custom_parent'])) {
                                                                                $pairs = explode(',', $notification['custom_parent']);
                                                                                foreach ($pairs as $pair) {
                                                                                    $parts = explode('-', $pair);
                                                                                    if (count($parts) === 2) {
                                                                                        $class_id = $parts[0];
                                                                                        $section_id = $parts[1];
                                                                                        $key = $class_id . $section_id;
                                                                                        
                                                                                        if (!isset($specific_class_sections[$key])) {
                                                                                            $this->db->select('classes.class, sections.section');
                                                                                            $this->db->from('class_sections');
                                                                                            $this->db->join('classes', 'classes.id = class_sections.class_id');
                                                                                            $this->db->join('sections', 'sections.id = class_sections.section_id');
                                                                                            $this->db->where('class_sections.class_id', $class_id);
                                                                                            $this->db->where('class_sections.section_id', $section_id);
                                                                                            $query = $this->db->get();
                                                                                            $res = $query->row_array();
                                                                                            if ($res) {
                                                                                                $all_parents_sections[] = $res['class'] . ' ' . $res['section'];
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }

                                                                            if (!empty($specific_students) || !empty($all_parents_sections)) {
                                                                                if (!empty($specific_students)) {
                                                                                    echo 'Specific Parent:';
                                                                                    foreach ($specific_students as $student) {
                                                                                        $class_sec = '';
                                                                                        if (!empty($student['class']) && !empty($student['section'])) {
                                                                                            $class_sec = $student['class'] . ' ' . $student['section'] . ' - ';
                                                                                        }
                                                                                        echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;' . $class_sec . $student['guardian_name'] . ' (' . $student['guardian_phone'] . ')';
                                                                                    }
                                                                                }
                                                                                if (!empty($all_parents_sections)) {
                                                                                    if (!empty($specific_students)) {
                                                                                        echo '<br>';
                                                                                    }
                                                                                    if (count($all_parents_sections) > 1) {
                                                                                        $last = array_pop($all_parents_sections);
                                                                                        echo 'All Parents: ' . implode(', ', $all_parents_sections) . ' and ' . $last;
                                                                                    } else {
                                                                                        echo 'All Parents: ' . $all_parents_sections[0];
                                                                                    }
                                                                                }
                                                                            } else {
                                                                                echo 'Yes';
                                                                            }
                                                                        } else {
                                                                            echo 'No';
                                                                        }
                                                                        ?>
                                                                    </li>
                                                                    <li>

                                                                        <i class="fa fa-user" aria-hidden="true"></i>
                                                                        <?php echo $this->lang->line('teacher'); ?> : <?php echo $notification['visible_teacher']; ?>
                                                                    </li>

                                                                </ul>
                                                            </div><!-- /.box-body -->
                                                        </div><!-- /. box -->

                                                    </div><!-- /.col -->
                                                </div><!-- /.row -->


                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                }
                            }
                            ?>


                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>

            </div>

            <!-- right column -->
        </div>
</div>

</section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script type="text/javascript">
    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
        $('.date').datepicker({
            // format: "dd-mm-yyyy",

            format: date_format,
            autoclose: true
        });

        $("#btnreset").click(function () {
            /* Single line Reset function executes on click of Reset Button */
            $("#form1")[0].reset();
        });

    });
</script>
<script>
    $(function () {
        //Add text editor
        $("#compose-textarea").wysihtml5();
    });
</script>