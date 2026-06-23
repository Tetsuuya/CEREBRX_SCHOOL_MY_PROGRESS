<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-bullhorn"></i> <?php echo $this->lang->line('communicate'); ?><small><?php echo $this->lang->line('student_fee1'); ?></small>
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



                <!-- left column -->
                <form id="form1" action="<?php echo base_url() ?>hr/notification/edit/<?php echo $id ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-commenting-o"></i> <?php echo $this->lang->line('edit_message'); ?></h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <?php if ($this->session->flashdata('msg')) { ?>
                                <?php echo $this->session->flashdata('msg') ?>
                            <?php } ?>
                             <?php echo $this->customlib->getCSRF(); ?>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('title'); ?></label>
                                    <input id="title" name="title" placeholder="" type="text" class="form-control"  value="<?php echo set_value('title', $notification['title']); ?>" />
                                    <span class="text-danger"><?php echo form_error('title'); ?></span>
                                </div>
                                <div class="form-group"><label><?php echo $this->lang->line('message'); ?></label>
                                    <textarea id="compose-textarea" name="message" class="form-control" style="height: 300px" maxlength="150">
                                        <?php echo set_value('message', $notification['message']); ?>
                                    </textarea>
                                    <span class="text-danger"><?php echo form_error('message'); ?></span>
                                </div>

                            </div>
                            <div class="col-md-3">


                                <div class="box-body">
                                    <?php
                                    if (isset($error_message)) {
                                        echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                                    }
                                    ?>

                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('notice_date'); ?></label>
                                        <input id="date" name="date"  placeholder="" type="text" class="form-control date"  value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($notification['date']))); ?>" />
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('publish_on'); ?></label>
                                        <input id="publish_date" name="publish_date"  placeholder="" type="text" class="form-control date"  value="<?php echo set_value('publish_date', date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($notification['publish_date']))); ?>" />
                                        <span class="text-danger"><?php echo form_error('publish_date'); ?></span>
                                    </div>
                                    <div class="form-horizontal">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('message_to'); ?></label>
                                        <!-- <div class="checkbox">
                                            <label><input type="checkbox" name="visible_to_std" value="student" <?php if (set_value('visible_to_std') == "student" || $notification['visible_student'] == "Yes") echo "checked" ?> /> <b><?php echo $this->lang->line('student'); ?></b> </label>
                                        </div> -->
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="visible_to_par" id="notify_parent"  value="parent" <?php if (set_value('visible_to_par') == "parent" || $notification['visible_parent'] == "Yes") echo "checked" ?>  /> <b><?php echo $this->lang->line('parent'); ?></b></label>
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="visible_to_tea" value="teacher"<?php if (set_value('visible_to_tea') == "teacher" || $notification['visible_teacher'] == "Yes") echo "checked" ?>  /> <b><?php echo $this->lang->line('teacher'); ?></b> </label>
                                        </div>
                                    </div>
                                </div><!-- /.box-body -->

                            </div><!--/.col (right) -->

                            <!-- /.box-body -->
                            <div class="box-footer">
                                <div class="pull-right">
                                    <input type="hidden" id="parent_custom" name="parent_custom" value="<?php echo set_value('custom_parent', $notification['custom_parent']); ?>">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> <?php echo $this->lang->line('send'); ?> </button>
                                </div>

                            </div>
                            <!-- /.box-footer -->
                        </div>
                        <!-- /. box -->
                    </div>
                </form>
                <!-- right column -->
            </div>
        </div>
        <div class="row">
            <!-- left column -->

            <!-- right column -->
            <div class="col-md-12">

                <!-- Horizontal Form -->

                <!-- general form elements disabled -->

            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->


<div id="notify_parent_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header"> 
        <h4 class="modal-title">Customize Parent</h4>
      </div>
      <div class="modal-body">
        <div class="checkbox">
              <label><input id="notify_all" class="parent_input"  name="notify_all" type="checkbox" value="all" checked="true" data-section="all" data-subject="all" >Send to All</label>
        </div>
        <?php
        foreach ($classlist as $class) { 
        ?> 
            <div class="checkbox">
                  <label><input class="parent_input" name="parent_notify[]" type="checkbox" value="<?php echo $class['class_id'] ?>" data-section="<?php echo $class['section_id'] ?>"   checked="true"><?php echo $class['class_name'].' - '.$class['section_name']  ?></label>
            </div>
            <?php
        }
        ?>
      </div>
      <div class="modal-footer"> 
        <button id="submit_custom_parent" type="button" class="btn btn-primary" >Done</button>
      </div>
    </div>

  </div>
</div>




<script type="text/javascript">
    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
        $('.date').datepicker({
            //   format: "dd-mm-yyyy",
            format: date_format,
            autoclose: true
        });

        $("#btnreset").click(function () {
            /* Single line Reset function executes on click of Reset Button */
            $("#form1")[0].reset();
        });

        $("input#notify_parent").change(function() {
            if(this.checked) {
                //Do stuffal
                $("#notify_parent_modal").modal({
                    backdrop: 'static',
                    keyboard: false
                });


            } else {
                $('#parent_custom').val('');
                $("#notify_parent_modal").modal('hide');
            }
        });

        $("input#notify_all").change(function() {
            if(this.checked) {
                //Do attr 
                 $('#notify_parent_modal input:checkbox').prop('checked', true);

            } else { 
                $('#notify_parent_modal input:checkbox').removeAttr('checked');

            }
        });

        $("#submit_custom_parent").click(function () { 
            var map = new Array();
            var count = 1;
            $(".parent_input").each(function() {
                /*map['class'+count] = $(this).val();
                count = count + 1;
                */

                if ($(this).is(':checked')) {
                    var class_id =  $(this).val();
                    var section_id =  $(this).attr("data-section"); 

                    var obj = [ 
                        { 
                            'class_id':class_id,
                            'section_id':section_id 

                        } 
                    ];

                    map.push(obj);
                }
            });
            
            var parent_custom = map.map(function(elem){ 
                return elem[0].class_id+'-'+elem[0].section_id;
            }).join(","); 
           $('#parent_custom').val(parent_custom);

           $("#notify_parent_modal").modal('hide'); 
        });

    });


</script>
<script>
    $(function () {
        //Add text editor
        $("#compose-textarea").wysihtml5();
    });
</script>