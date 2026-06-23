<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-mortar-board"></i> <?php echo $this->lang->line('academics'); ?> <small><?php echo $this->lang->line('student_fees1'); ?></small></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('add_teacher'); ?></h3>
                    </div> 
                    <form id="form1" action="<?php echo site_url('registrar/teacher/create') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8"  enctype="multipart/form-data">
                        <div class="box-body">
                            <?php if ($this->session->flashdata('msg')) { ?>
                                <?php echo $this->session->flashdata('msg') ?>
                            <?php } ?>    
                             <?php echo $this->customlib->getCSRF(); ?>
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="required">Teacher Name</label>
                                <input id="category" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />
                                <span class="text-danger"><?php echo form_error('name'); ?></span>
                            </div>
							<div class="form-group">
                                <label for="exampleInputEmail1">Teacher Middle Name</label>
                                <input id="category" name="middlename" placeholder="" type="text" class="form-control"  value="<?php echo set_value('middlename'); ?>" />
                                <span class="text-danger"><?php echo form_error('middlename'); ?></span>
                            </div>
							<div class="form-group">
                                <label for="exampleInputEmail1" class="required">Teacher Last Name</label>
                                <input id="category" name="lastname" placeholder="" type="text" class="form-control"  value="<?php echo set_value('lastname'); ?>" />
                                <span class="text-danger"><?php echo form_error('lastname'); ?></span>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('email'); ?></label>
                                <input id="category" name="email" placeholder="" type="text" class="form-control"  value="<?php echo set_value('email'); ?>" />
                                <span class="text-danger"><?php echo form_error('email'); ?></span>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile" class="required"> <?php echo $this->lang->line('gender'); ?> &nbsp;&nbsp;</label>
                                <select class="form-control" name="gender">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php
                                    foreach ($genderList as $key => $value) {
                                        ?>
                                        <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('gender'); ?></span>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('date_of_birth'); ?></label>
                                <input id="dob" name="dob" placeholder="" type="text" class="form-control"  value="<?php echo set_value('dob'); ?>" readonly="readonly"/>
                                <span class="text-danger"><?php echo form_error('dob'); ?></span>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('address'); ?></label>
                                <textarea id="address" name="address" placeholder=""  class="form-control" ><?php echo set_value('address'); ?></textarea>
                                <span class="text-danger"><?php echo form_error('address'); ?></span>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('phone'); ?></label>
                                <input id="phone" name="phone" placeholder="" type="text" class="form-control"  value="<?php echo set_value('phone'); ?>" />
                                <span class="text-danger"><?php echo form_error('phone'); ?></span>
                            </div>
                           
                            <div class="form-group">
                                <label><?php echo 'Type';//$this->lang->line('rte'); ?></label>
                                <div class="radio" style="margin-top: 2px;">
                                    <label><input class="radio-inline type_teacher" type="radio" name="type_teacher" value="Advisory"  <?php
                                        echo set_value('type_teacher') == "Advisory" ? "checked" : "";
                                        ?>  > Advisory</label>
                                        <label><input class="radio-inline type_teacher" checked="checked" type="radio" name="type_teacher" value="Staff" <?php
                                            echo set_value('type_teacher') == "Staff" ? "checked" : "";
                                            ?>  > Staff/Faculty</label>
                                </div>
                                <span class="text-danger"><?php echo form_error('type'); ?></span>
                            </div> 
                            <!--<div id="advisory_teacher" style="display: none;">
                               <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo 'Grade';//$this->lang->line('class'); ?></label>
                                    <select  id="class_id" name="class_id" class="form-control"  multiple>
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
										
                                        /* foreach ($classlist as $class) {
											
											$class_id = $class['class_id'];
											$class_details = $this->class_model->get( $class_id );
										
											$class_name = $class_details['class'];
											$section_name = $class['section'];
                                            ?>
                                            <option value="<?php echo $class['id'] ?>"<?php if (set_value('class_id') == $class['id']) echo "selected=selected" ?>><?php echo $class_name.' '.$section_name ?></option>
                                            <?php
                                            $count++;
                                        } */
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                </div> 
                            </div> --> 
                            <div class="form-group">
                                <label><?php echo 'Status'; ?></label>
                                <div class="radio" style="margin-top: 2px;">
                                    <label><input class="radio-inline is_active" checked="checked" type="radio" name="is_active" value="yes"  <?php
                                        echo set_value('is_active') == "yes" ? "checked" : "";
                                        ?>  > Active</label>
                                        <label><input class="radio-inline is_active"type="radio" name="is_active" value="no" <?php
                                            echo set_value('is_active') == "no" ? "checked" : "";
                                            ?>  > Deactive</label>
                                </div>
                                <span class="text-danger"><?php echo form_error('is_active'); ?></span>
                            </div> 
                            <div class="form-group">
                                <label for="exampleInputFile"><?php echo $this->lang->line('teacher_photo'); ?> (150px X 150px)</label>
                                <input type='file' name='file' id="file" size='20' />

                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8">              
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('teacher_list'); ?></h3>                      
                    </div>
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('teacher_name'); ?></th>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('action'); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($teacherlist)) {
                                        ?>
                                        <tr>
                                            <td colspan="12" class="text-danger text-center"><?php echo $this->lang->line('no_record_found'); ?></td>
                                        </tr>
                                        <?php
                                    } else {
                                        $count = 1;
                                        foreach ($teacherlist as $teacher) {
                                            ?>
                                            <tr>
                                                <td class="mailbox-name"> <?php echo $teacher['name'].' '.$teacher['middlename'].' '.$teacher['lastname'] ?></td>
                                                <td class="mailbox-name"> <?php echo $teacher['sex'] ?></td>
                                                <td class="mailbox-name"> <?php echo $teacher['dob'] ?></td>
                                                <td class="mailbox-name"> <?php echo $teacher['phone'] ?></td>
                                                <td class="mailbox-date pull-right">
                                                    <a href="<?php echo base_url(); ?>registrar/teacher/view/<?php echo $teacher['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>" >
                                                        <i class="fa fa-reorder"></i>
                                                    </a>
                                                    <a href="<?php echo base_url(); ?>registrar/teacher/edit/<?php echo $teacher['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        $count++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="mailbox-controls">
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </section>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
        $('#dob,#admission_date').datepicker({  
            format: date_format,
            autoclose: true
        });
        $("#btnreset").click(function () {            
            $("#form1")[0].reset();
        });

        $('input.type_teacher').click(function () {
            
             if ($(this).attr("value") == "Staff") {
                $("#advisory_teacher").hide('slow');
            }
            if ($(this).attr("value") == "Advisory") {
                $("#advisory_teacher").show('slow');

            } 
        });
 

    });
</script>

<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';
    function printDiv(elem) {      
        Popup(jQuery(elem).html());
    }

          function Popup(data) 
        {
          
            var frame1 = $('<iframe />');
            frame1[0].name = "frame1";
            frame1.css({ "position": "absolute", "top": "-1000000px" });
            $("body").append(frame1);
            var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
            frameDoc.document.open();
        //Create a new HTML document.
        frameDoc.document.write('<html>');
        frameDoc.document.write('<head>');
        frameDoc.document.write('<title></title>');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/bootstrap/css/bootstrap.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/font-awesome.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/ionicons.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/AdminLTE.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/skins/_all-skins.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/iCheck/flat/blue.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/morris/morris.css">');


        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/datepicker/datepicker3.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/daterangepicker/daterangepicker-bs3.css">');
        frameDoc.document.write('</head>');
        frameDoc.document.write('<body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body>');
        frameDoc.document.write('</html>');
        frameDoc.document.close();
        setTimeout(function () {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
        }, 500);
        

        return true;
    }
</script>
