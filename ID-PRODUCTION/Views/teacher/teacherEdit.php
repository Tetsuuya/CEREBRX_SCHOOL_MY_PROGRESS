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

                        <h3 class="box-title"><?php echo $this->lang->line('edit_teacher'); ?></h3>

                    </div> 

                    <form action="<?php echo site_url('idproduction/teacher/edit/' . $id) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8"  enctype="multipart/form-data">

                        <div class="box-body">

                            <?php if ($this->session->flashdata('msg')) { ?>

                                <?php echo $this->session->flashdata('msg') ?>

                            <?php } ?>  

                             <?php echo $this->customlib->getCSRF(); ?>

                            <div class="form-group">

                                <label for="exampleInputEmail1" class="required">Teacher Name</label>

                                <input id="category" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name', $teacher['name']); ?>" />

                                <span class="text-danger"><?php echo form_error('name'); ?></span>

                            </div>

							<div class="form-group">

                                  <label for="exampleInputEmail1">Teacher Middle Name</label>

                                <input id="category" name="middlename" placeholder="" type="text" class="form-control"  value="<?php echo set_value('middlename', $teacher['middlename']); ?>" />

                                <span class="text-danger"><?php echo form_error('middlename'); ?></span>

                            </div>

							<div class="form-group">

                                <label for="exampleInputEmail1" class="required">Teacher Last Name</label>

                                <input id="category" name="lastname" placeholder="" type="text" class="form-control"  value="<?php echo set_value('lastname', $teacher['lastname']); ?>" />

                                <span class="text-danger"><?php echo form_error('lastname'); ?></span>

                            </div>

                            <div class="form-group">

                                <label for="exampleInputEmail1"><?php echo $this->lang->line('email'); ?></label>

                                <input id="category" name="email" placeholder="" type="text" class="form-control"  value="<?php echo set_value('email', $teacher['email']); ?>" />

                                <span class="text-danger"><?php echo form_error('email'); ?></span>

                            </div>

                            <div class="form-group">

                                <label for="exampleInputFile" class="required"> <?php echo $this->lang->line('gender'); ?> &nbsp;&nbsp;</label>

                                <select class="form-control" name="gender">

                                    <option value=""><?php echo $this->lang->line('select'); ?></option>

                                    <?php

                                    foreach ($genderList as $key => $value) {

                                        ?>

                                        <option value="<?php echo $key; ?>" <?php if (set_value('gender', $teacher['sex']) == $key) echo "selected"; ?>><?php echo $value; ?></option>

                                        <?php

                                    }

                                    ?>

                                </select>

                                <span class="text-danger"><?php echo form_error('gender'); ?></span>

                            </div>

                            <div class="form-group">

                                <label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('date_of_birth'); ?></label>

                                <input id="dob" name="dob" placeholder="" type="text" class="form-control"  value="<?php echo set_value('dob', date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($teacher['dob']))); ?>" readonly="readonly"/>

                                <span class="text-danger"><?php echo form_error('dob'); ?></span>

                            </div>

                            <div class="form-group">

                                <label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('address'); ?></label>

                                <textarea id="address" name="address" placeholder=""  class="form-control" ><?php echo set_value('address', $teacher['address']); ?></textarea>

                                <span class="text-danger"><?php echo form_error('address'); ?></span>

                            </div>

                            <div class="form-group">

                                <label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('phone'); ?></label>

                                <input id="phone" name="phone" placeholder="" type="text" class="form-control"  value="<?php echo set_value('phone', $teacher['phone']); ?>" />

                                <span class="text-danger"><?php echo form_error('phone'); ?></span>

                            </div>

                            <div class="form-group">

                                <label><?php echo 'Type';//$this->lang->line('rte'); ?></label>

                                <div class="radio" style="margin-top: 2px;">

                                    <label><input class="radio-inline type_teacher" type="radio" name="type_teacher" value="Advisory"  <?php

                                        echo set_value('type_teacher', $teacher['type_teacher']) === "Advisory" ? "checked" : "";

                                        ?>  > Advisory</label>

                                        <label><input class="radio-inline type_teacher"  type="radio" name="type_teacher" value="Staff" <?php

                                            echo set_value('type_teacher', $teacher['type_teacher']) === "Staff" ? "checked" : "";

                                            ?>  > Staff/Faculty</label>

                                </div>

                                <span class="text-danger"><?php echo form_error('type'); ?></span>

                            </div> 

                            <div class="form-group">

                                <label><?php echo 'Status'; ?></label>

                                <div class="radio" style="margin-top: 2px;">

                                    <label><input class="radio-inline is_active" checked="checked" type="radio" name="is_active" value="yes"  <?php

                                        echo set_value('is_active', $teacher['is_active']) == "yes" ? "checked" : "";

                                        ?>  > Activate</label>

                                        <label><input class="radio-inline is_active"type="radio" name="is_active" value="no" <?php

                                            echo set_value('is_active', $teacher['is_active']) == "no" ? "checked" : "";

                                            ?>  > Deactivate</label>

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

                    <div class="box-header ptbnull">

                        <h3 class="box-title titlefix"><?php echo $this->lang->line('teacher_list'); ?></h3>                       

                    </div>

                    <div class="box-body">

                        <div class="mailbox-controls">                          

                        </div>

                        <div class="table-responsive mailbox-messages">

                            <table class="table table-striped table-bordered table-hover example">

                                <thead>

                                    <tr>

                                        <th>

                                        </th>

                                        <th><?php echo $this->lang->line('teacher_name'); ?></th>

                                        <th><?php echo $this->lang->line('gender'); ?></th>

                                        <th><?php echo $this->lang->line('date_of_birth'); ?></th>

                                        <th><?php echo $this->lang->line('phone'); ?></th>

                                        <th class="text-right"><?php echo $this->lang->line('action'); ?></th>

                                    </tr>

                                </thead>

                                <tbody>

                                        <?php

                                    

                                        $count = 1;

                                        foreach ($teacherlist as $teacher_display) {

                                            ?>

                                            <tr>

                                                <td></td>

                                                <td class="mailbox-name"> <?php echo $teacher_display['name'].' '.$teacher_display['middlename'].' '.$teacher_display['lastname'] ?></td>

                                                <td class="mailbox-name"> <?php echo $teacher_display['sex'] ?></td>

                                                <td class="mailbox-name"> <?php echo $teacher_display['dob'] ?></td>

                                                <td class="mailbox-name"> <?php echo $teacher_display['phone'] ?></td>

                                                <td class="mailbox-date pull-right"">

                                                    <a href="<?php echo base_url(); ?>idproduction/teacher/view/<?php echo $teacher_display['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>" >

                                                        <i class="fa fa-reorder"></i>

                                                    </a>

                                                    <a href="<?php echo base_url(); ?>idproduction/teacher/edit/<?php echo $teacher_display['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">

                                                        <i class="fa fa-pencil"></i>

                                                    </a>

                                                </td>

                                            </tr>

                                            <?php

                                        }

                                        $count++;

                                  

                                    ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                    <div class="">

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

		 var type_teacher_post = '<?php echo $teacher['type_teacher']; ?>';

		  

        if ( type_teacher_post == "Staff") {

            $("#advisory_teacher").hide();

        }

        if ( type_teacher_post == "Advisory") {

            $("#advisory_teacher").show();



        } 

		

        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';

        $('#dob,#admission_date').datepicker({           

            format: date_format,

            autoclose: true

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

