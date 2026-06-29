<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-map-o"></i> <?php echo $this->lang->line('examinations'); ?> <small><?php echo $this->lang->line('student_fee1'); ?></small>  </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
					<form action="<?php echo site_url('teacher/grade/importcustomgrade') ?>"  id="employeeform" name="employeeform" method="post" enctype="multipart/form-data">
						<div class="box-header with-border">
							<h3 class="box-title">Import Custom Grades</h3>
							<div class="pull-right box-tools"> 
								<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#TemplateModal" id="templateModalButton" ><i class="fa fa-file-excel-o"></i> Generate Spreadsheet</button>
							</div>
							
						</div>
                        <div class="box-body">
						<?php if ($this->session->flashdata('msg')) { ?> <div class="alert alert-success">  <?php echo $this->session->flashdata('msg') ?> </div> <?php } ?>
						<?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('student'); ?></label>
                                        <select id="student_id" name="student_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($studentList as $student) {
                                                ?>
                                                <option value="<?php echo $student['id'] ?>"><?php echo $student['lastname'].', '.$student['firstname'] ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('student_id'); ?></span>
                                    </div>
								</div>
                                <!--
								<div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"> Semester</label>
                                        <select  id="semester_id" name="semester_id" class="form-control" >
                                          <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($getSemester as $key => $value ) {
                                                ?>
                                                <option value="<?php echo $key ?>"><?php echo $value; ?></option>
                                                <?php
                                                $count++;
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('semester_id'); ?></span>
                                    </div>
								</div>
                                -->
                                <input type="hidden" id="semester_id" name="semester_id" value="1">
								<div class="col-md-3">
                                    <div class="form-group">
                                    <label for="exampleInputEmail1">Term</label>
                                        <select  id="quarter" name="quarter" class="form-control" >
                                           <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                                foreach ($getquarter as $key => $value) {
                                                    $term_label = $value;
                                                    if ($key == 1) {
                                                        $term_label = 'Term 1';
                                                    } elseif ($key == 2) {
                                                        $term_label = 'Term 2';
                                                    } elseif ($key == 3) {
                                                        $term_label = 'Term 3';
                                                    } elseif ($key == 4) {
                                                        continue;
                                                    }
                                                ?>
                                                <option  value="<?php echo $key; ?>" <?php //if ($value['quarter'] == $value) echo "selected"; ?>><?php echo $term_label; ?></option>
                                                <?php
                                                }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('quarter'); ?></span>
                                    </div>
                                </div>
								<div class="col-md-3">
                                    <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('subject'); ?></label>
                                        <select  id="subject_id" name="subject_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('subject_id'); ?></span>
                                    </div>
								</div>
							</div>
							<div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('select_xlsx_file'); ?></label>
                                        <input type='file' name='file' id="file" size='20' />
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                     </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('import_grades'); ?></button>
                        </div>
						<div id="TemplateModal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog">
							<div class="modal-dialog modal-sm">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
										<h5 class="modal-title">Select Sheet Template To Generate</h5>
									</div>
									<div class="modal-body">
									<select id="template_id" name="template_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
									</select>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
										<button type="submit" class="btn btn-primary" formaction="<?php echo site_url('teacher/grade/generate_spreadsheet_custom') ?>">Generate</button>
									</div>
								</div>
							</div>
						</div>
					</form>
                </div>
               </section>
            </div>
		
            <script type="text/javascript">
                $(document).ready(function () {
                    $(document).on('change', '#student_id', function (e) {
                        $('#subject_id').html("");
                        var student_id = $(this).val();
                        var semester_id = $("#semester_id").val();
                        var base_url = '<?php echo base_url() ?>';
                        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
                        if (student_id && semester_id) {
                            $.ajax({
                                type: "GET",
                                url: base_url + "/teacher/CustomSubject/getSubjectbySemester",
                                data: {'student_id': student_id, 'semester_id': semester_id},
                                dataType: "json",
                                success: function (data) {
                                    $.each(data, function (i, obj)
                                    {	
                                        div_data += "<option value=" + obj.id + ">" + obj.name +"</option>";
                                    });
                                    $('#subject_id').append(div_data);
                                }
                            });
                        }
                    });
                });
				$(document).ready(function () {
                    $(document).on('click', '#templateModalButton', function (e) {
                        $('#template_id').html("");
                        var student_id =  $('#student_id').val();
                        var base_url = '<?php echo base_url() ?>';
                        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
						
						$.ajax({
                            type: "GET",
                            url: base_url + "teacher/Student/getStudentClassID",
                            data: {'student_id': student_id},
                            dataType: "json",
                            success: function (data) {
								 $.ajax({
									type: "GET",
									url: base_url + "teacher/template/getByClass",
									data: {'class_id': data},
									dataType: "json",
									success: function (data) {
										$.each(data, function (i, obj)
										{	
											div_data += "<option value=" + obj.id + ">" + obj.title + "</option>";
										});
										$('#template_id').append(div_data);
									}
								}); 
                            }
                        });
					});
				  
				});
            </script>