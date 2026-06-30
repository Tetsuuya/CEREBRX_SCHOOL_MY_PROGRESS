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
                <form id="form1" action="<?php echo base_url() ?>teacher/notification/edit/<?php echo $id ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
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
                                       <!--  <div class="checkbox">
                                            <label><input type="checkbox" name="visible_to_std" value="student" <?php if (set_value('visible_to_std') == "student" || $notification['visible_student'] == "Yes") echo "checked" ?> /> <b><?php echo $this->lang->line('student'); ?></b> </label>
                                        </div> -->
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="visible_to_par" id="notify_parent"  value="parent" <?php if (set_value('visible_to_par') == "parent" || $notification['visible_parent'] == "Yes") echo "checked" ?>  /> <b><?php echo $this->lang->line('parent'); ?></b></label>
                                            <div id="selected_parent_display" style="font-size: 11px; color: #555; margin-left: 20px; margin-top: 4px; display: none;"></div>
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
                                    <!-- modification: hidden field added to store targeted specific student IDs -->
                                    <input type="hidden" id="custom_student_ids" name="custom_student_ids" value="<?php echo set_value('custom_student_ids', $notification['custom_student_ids']); ?>">
									<?php
									  if( $notification['approved'] != 'yes' && ( $notification['created_id'] == $teacher_id && $notification['created_by'] == 'teacher' )){?>
											<button type="submit" class="btn btn-primary" onclick.submit="Are "><i class="fa fa-envelope-o"></i> <?php echo $this->lang->line('send'); ?> </button>
									<?php 
									  }
									  ?>
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


<!-- modification: modal body structure rewritten to support list layout with autocomplete boxes -->
<div id="notify_parent_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- modal content-->
    <div class="modal-content">
      <div class="modal-header"> 
        <h4 class="modal-title">Customize Parent</h4>
      </div>
      <div class="modal-body">
        <div class="checkbox" style="border-bottom: 1px solid #ddd; padding-bottom: 8px; margin-bottom: 12px;">
              <label><input id="notify_all" class="parent_input"  name="notify_all" type="checkbox" value="all" checked="true" data-section="all" data-subject="all" >Send to All</label>
        </div>

        <?php
        $selected_classes = explode(',', $notification['custom_parent']);
        $selected_students = explode(',', $notification['custom_student_ids']);

        foreach ($classlist as $class) { 
            $key = $class['class_id'] . '_' . $class['section_id'];
            $class_section_str = $class['class_id'] . '-' . $class['section_id'];
            $is_checked = in_array($class_section_str, $selected_classes);
        ?> 
            <div class="class-section-item-group" style="margin-bottom: 12px; border-bottom: 1px dashed #eee; padding-bottom: 12px;">
                <div class="checkbox" style="margin-top: 0; margin-bottom: 5px;">
                      <label><input class="parent_input class-section-chk" name="parent_notify[]" type="checkbox" value="<?php echo $class['class_id'] ?>" data-section="<?php echo $class['section_id'] ?>" <?php echo $is_checked ? 'checked="true"' : ''; ?>><b><?php echo $class['class_name'].' - '.$class['section_name']  ?></b></label>
                </div>
                
                <div class="student-target-selectors" style="margin-left: 20px; display: <?php echo $is_checked ? 'block' : 'none'; ?>;">
                    <label class="radio-inline" style="font-size: 11px;">
                        <input type="radio" name="target_type_<?php echo $key; ?>" class="target-type-radio" value="all" checked="true"> All Parents
                    </label>
                    <label class="radio-inline" style="font-size: 11px;">
                        <input type="radio" name="target_type_<?php echo $key; ?>" class="target-type-radio" value="single"> Specific Parent
                    </label>
                    
                    <div class="single-student-search-box" style="display: none; margin-top: 6px; position: relative;">
                        <input type="text" class="form-control input-sm student-select-autocomplete" 
                               placeholder="Type student name in this class..." 
                               data-class-id="<?php echo $class['class_id'] ?>" 
                               data-section-id="<?php echo $class['section_id'] ?>" 
                               autocomplete="off" style="width: 80%;">
                        <div class="student-autocomplete-dropdown list-group" style="display: none; position: absolute; left: 0; right: 0; z-index: 1050; max-height: 200px; overflow-y: auto; width: 80%; border: 1px solid #ccc; background: white; margin-top: 2px;"></div>
                        <div class="receiver-phone-display" style="display: none; margin-top: 4px; font-size: 11px; font-weight: bold; color: #3c763d;"></div>
                        <input type="hidden" class="target-student-id-field" value="">
                    </div>
                </div>
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

        // Parse pre-populated specific students
        var selectedStudents = <?php echo isset($selected_students_json) ? $selected_students_json : '[]'; ?>;
        if (selectedStudents.length > 0) {
            selectedStudents.forEach(function(student) {
                var key = student.class_id + '_' + student.section_id;
                var rowGroup = $('.class-section-item-group').filter(function() {
                    var chk = $(this).find('.class-section-chk');
                    return chk.val() == student.class_id && chk.attr('data-section') == student.section_id;
                });
                
                if (rowGroup.length > 0) {
                    rowGroup.find('input[name="target_type_' + key + '"][value="single"]').prop('checked', true).trigger('change');
                    var studentName = student.lastname + ", " + student.firstname + " " + (student.middlename ? student.middlename : "");
                    rowGroup.find('.student-select-autocomplete').val(studentName);
                    rowGroup.find('.target-student-id-field').val(student.id);
                    rowGroup.find('.receiver-phone-display').html('Receiver: ' + student.guardian_name + ' (' + student.guardian_phone + ')').show();
                }
            });
        }

        // modification: uncheck "Send to All" master checkbox on page load if any class-section checkbox is unchecked
        var totalCheckboxesOnLoad = $('.class-section-chk').length;
        var checkedCheckboxesOnLoad = $('.class-section-chk:checked').length;
        if (checkedCheckboxesOnLoad === totalCheckboxesOnLoad) {
            $('#notify_all').prop('checked', true);
        } else {
            $('#notify_all').prop('checked', false);
        }

        $("input#notify_parent").change(function() {
            if(this.checked) {
                //Do stuffal
                $("#notify_parent_modal").modal({
                    backdrop: 'static',
                    keyboard: false
                });


            } else {
                $('#parent_custom').val('');
                $('#custom_student_ids').val('');
                $("#notify_parent_modal").modal('hide');
            }
            updateReceiverDisplay();
        });

        // modification: set all class-section checkboxes when "Send to All" is toggled
        $("input#notify_all").change(function() {
            var checked = this.checked;
            $('.class-section-chk').each(function() {
                $(this).prop('checked', checked).trigger('change');
            });
        });

        // modification: uncheck "Send to All" master checkbox if any class-section checkbox is unchecked
        $(document).on('change', '.class-section-chk', function() {
            var totalCheckboxes = $('.class-section-chk').length;
            var checkedCheckboxes = $('.class-section-chk:checked').length;
            if (checkedCheckboxes === totalCheckboxes) {
                $('#notify_all').prop('checked', true);
            } else {
                $('#notify_all').prop('checked', false);
            }

            if (checkedCheckboxes === 0) {
                $('#notify_parent').prop('checked', false);
            } else {
                $('#notify_parent').prop('checked', true);
            }
        });

        // modification: toggle specific parent search container visibility when target type changes
        $(document).on('change', '.target-type-radio', function() {
            var rowGroup = $(this).closest('.class-section-item-group');
            var searchBox = rowGroup.find('.single-student-search-box');
            if ($(this).val() === 'single') {
                searchBox.show();
            } else {
                searchBox.hide();
                searchBox.find('.student-select-autocomplete').val('');
                searchBox.find('.target-student-id-field').val('');
                searchBox.find('.receiver-phone-display').hide().html('');
            }
        });

        // modification: hide specific target options and reset inputs if section check is unchecked
        $(document).on('change', '.class-section-chk', function() {
            var rowGroup = $(this).closest('.class-section-item-group');
            var targetSelectors = rowGroup.find('.student-target-selectors');
            if (this.checked) {
                targetSelectors.show();
            } else {
                targetSelectors.hide();
                rowGroup.find('input[value="all"]').prop('checked', true);
                var searchBox = rowGroup.find('.single-student-search-box');
                searchBox.hide();
                searchBox.find('.student-select-autocomplete').val('');
                searchBox.find('.target-student-id-field').val('');
                searchBox.find('.receiver-phone-display').hide().html('');
            }
        });

        // modification: student autocomplete search inside modal
        $(document).on('keyup', '.student-select-autocomplete', function() {
            var input = $(this);
            var search = input.val().trim();
            var dropdown = input.siblings('.student-autocomplete-dropdown');
            var classId = input.attr('data-class-id');
            var sectionId = input.attr('data-section-id');

            if (search.length < 1) {
                dropdown.hide().empty();
                return;
            }

            $.ajax({
                url: "<?php echo base_url('teacher/notification/get_class_section_students'); ?>",
                type: "POST",
                data: {
                    'class_id': classId,
                    'section_id': sectionId,
                    'search': search,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: "json",
                success: function(data) {
                    dropdown.empty().show();
                    if (data.length === 0) {
                        dropdown.append('<div class="list-group-item disabled" style="font-size:12px; padding:6px 10px;">No student found</div>');
                        return;
                    }
                    data.forEach(function(student) {
                        var studentName = student.lastname + ", " + student.firstname + " " + (student.middlename ? student.middlename : "");
                        var item = $('<a href="#" class="list-group-item list-group-item-action" style="font-size:12px; padding:6px 10px; display:block;"></a>');
                        item.text(studentName);
                        item.attr('data-sid', student.id);
                        item.attr('data-gname', student.guardian_name);
                        item.attr('data-gphone', student.guardian_phone);

                        item.on('click', function(e) {
                            e.preventDefault();
                            input.val(studentName);
                            input.siblings('.target-student-id-field').val(student.id);
                            
                            // display the parent/guardian phone number preview
                            var phoneDisplay = input.siblings('.receiver-phone-display');
                            phoneDisplay.html('Receiver: ' + student.guardian_name + ' (' + student.guardian_phone + ')').show();
                            
                            dropdown.hide().empty();
                        });

                        dropdown.append(item);
                    });
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error in get_class_section_students:", xhr.responseText, status, error);
                }
            });
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.single-student-search-box').length) {
                $('.student-autocomplete-dropdown').hide().empty();
            }
        });

        $("#submit_custom_parent").click(function () { 
            var map = new Array();
            var studentIds = new Array();

            $(".class-section-item-group").each(function() {
                var chk = $(this).find('.class-section-chk');
                if (chk.is(':checked')) {
                    var class_id = chk.val();
                    var section_id = chk.attr("data-section");
                    var key = class_id + '_' + section_id;
                    var targetType = $(this).find('input[name="target_type_' + key + '"]:checked').val();

                    if (targetType === 'single') {
                        var sId = $(this).find('.target-student-id-field').val();
                        if (sId) {
                            studentIds.push(sId);
                            // map the class-section target of this specific student
                            map.push([{ 'class_id': class_id, 'section_id': section_id }]);
                        }
                    } else {
                        // target the entire class-section
                        map.push([{ 'class_id': class_id, 'section_id': section_id }]);
                    }
                }
            });
            
            var parent_custom = map.map(function(elem){ 
                return elem[0].class_id+'-'+elem[0].section_id;
            }).join(","); 
            
            $('#parent_custom').val(parent_custom);
            $('#custom_student_ids').val(studentIds.join(","));

            if (map.length === 0) {
                $('#notify_parent').prop('checked', false);
            }

            updateReceiverDisplay();
            $("#notify_parent_modal").modal('hide'); 
        });

        function updateReceiverDisplay() {
            var displayDiv = $('#selected_parent_display');
            if (!$('#notify_parent').is(':checked')) {
                displayDiv.hide().html('');
                return;
            }

            if ($('#notify_all').is(':checked')) {
                displayDiv.html('<i class="fa fa-users"></i> Receiver: All Parents').show();
                return;
            }

            var selectedItems = [];
            $(".class-section-item-group").each(function() {
                var chk = $(this).find('.class-section-chk');
                if (chk.is(':checked')) {
                    var className = chk.closest('label').text().trim();
                    var key = chk.val() + '_' + chk.attr("data-section");
                    var targetType = $(this).find('input[name="target_type_' + key + '"]:checked').val();

                    if (targetType === 'single') {
                        var studentName = $(this).find('.student-select-autocomplete').val();
                        var phoneDisplay = $(this).find('.receiver-phone-display').text().trim();
                        if (studentName) {
                            var parentInfo = phoneDisplay.replace('Receiver: ', '');
                            selectedItems.push(studentName + " (" + parentInfo + ")");
                        } else {
                            selectedItems.push(className + " (Specific Parent)");
                        }
                    } else {
                        selectedItems.push(className);
                    }
                }
            });

            if (selectedItems.length > 0) {
                displayDiv.html('<i class="fa fa-user"></i> Receiver: ' + selectedItems.join(', ')).show();
            } else {
                displayDiv.hide().html('');
            }
        }

        // initialize state on page load
        updateReceiverDisplay();

    });


</script>
<script>
    $(function () {
        //Add text editor
        $("#compose-textarea").wysihtml5();
    });
</script>