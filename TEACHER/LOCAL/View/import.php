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
				<form action="<?php echo site_url('teacher/grade/import_grades') ?>"  id="employeeform" name="employeeform" method="post" enctype="multipart/form-data">
						<div class="box-header with-border">
							<h3 class="box-title">Import Grades</h3>
							<div class="pull-right box-tools"> 
								<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#TemplateModal" id="templateModalButton" ><i class="fa fa-file-excel-o"></i> Generate Spreadsheet</button>
							</div>
							
						</div>
						<div class="alert alert-success" style="font-size:16px;"><strong>Important Reminders  in SUBMITTING GRADES:</strong>
						<br/><br/>
							<p>- Make sure the excel is <strong>generated from the system.</strong></p>
							<p>- Please <strong>HIDE</strong> unwanted rows instead of removing it.</p>
							<p>- If student is <strong>NO LONGER</strong> studying in the school. Please be careful in removing row or just put blank on the admission no.</p>
							<p>- Please <strong>CHECK</strong> if the <strong>ADMISSION NO.</strong> of the student is present in the excel. <br/>- Row without Admission number is not readable in the system. <br/>
							- If <strong style="color:red;">MISSING</strong>, please <strong>SEARCH</strong> and <strong>INPUT</strong> the exact admission no.</p>
							<img src="<?php echo base_url().'uploads/school_content/add.png'?>" />
						</div>
                        <div class="box-body">
						<div class="mailbox-controls" style="font-size:18px;">
                        <?php if ($this->session->flashdata('msg')) { 
								if(is_array($this->session->flashdata('msg'))){
									$ab =  $this->session->flashdata('msg');
									for($x=0;$x<count($ab);$x++ ){
										echo $ab[$x];
									}
								} else {
									echo $this->session->flashdata('msg');
								}
						} ?>
                        </div>
						<?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1" class='required'><?php echo $this->lang->line('class'); ?></label>
                                        <select id="class_id" name="class_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($classlist as $class) {
                                                ?>
                                                <option value="<?php echo $class['id'] ?>"><?php echo $class['class'] ?></option>
                                                <?php
                                                $count++;
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                    </div>
								</div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1" class='required'><?php echo $this->lang->line('section'); ?></label>
                                        <select  id="section_id" name="section_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                    </div>
								</div>
								<div class="col-md-3">
                                    <div class="form-group">
                                    <label for="exampleInputEmail1" class='required'><?php echo $this->lang->line('subject'); ?></label>
                                        <select  id="subject_id" name="subject_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('subject_id'); ?></span>
                                    </div>
								</div>	
								<div class="col-md-3">
                                    <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('quarter'); ?> <small><span style="color:red;">Required in importing grades only</span></small></label>
                                        <select  id="quarter" name="quarter" class="form-control" >
                                           <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                                foreach ($getquarter as $key => $value) {
                                                if( $value == 1 && $firstqsettings == 'yes'){
                                                        ?>
                                                          <option  value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                                        <?php
                                                    }
                                                     if( $value == 2 && $secondqsettings == 'yes'){
                                                        ?>
                                                          <option  value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                                        <?php
                                                    }
                                                     if( $value == 3 && $thirdqsettings == 'yes'){
                                                        ?>
                                                          <option  value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                                        <?php
                                                    }
                                                     if( $value == 4 && $fourthqsettings == 'yes'){
                                                        ?>
                                                          <option  value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('quarter'); ?></span>
                                    </div>
                                </div>
														
							</div>
                             <?php 
                            if( $import_grade_settings == 'yes' ){
                            ?>
							  <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('select_xlsx_file'); ?></label>
                                        <input type='file' name='file' id="file" size='20' />
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
										<small><b>Note: Please select the excel generated from the system.</b></small>
                                     </div>
                                </div>
                            </div>
                             <?php 
                            }
                            ?>
                        </div>
                        <div class="box-footer">
                             <?php 
                            if( $import_grade_settings == 'yes' ){
                            ?>
                            <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('import_grades'); ?></button>
                              <?php 
                            }
                            ?>
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
										<button type="submit" class="btn btn-primary" formaction="<?php echo site_url('teacher/grade/generate_spreadsheet') ?>">Generate</button>
									</div>
								</div>
							</div>
						</div>
					</form>
                </div>
                </section>
            </div>
			
            <script type="text/javascript">
                function getSectionByClass(class_id, section_id) {
                    if (class_id != "" && section_id != "") {
                        $('#section_id').html("");
                        var base_url = '<?php echo base_url() ?>';
                        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
                        $.ajax({
                            type: "GET",
                           url: base_url + "teacher/sections/getByClass",
                            data: {'class_id': class_id},
                            dataType: "json",
                            success: function (data) {
                                $.each(data, function (i, obj)
                                {
                                    var sel = "";
                                    if (section_id == obj.section_id) {
                                        sel = "selected";
                                    }
                                    div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                                });
                                $('#section_id').append(div_data);
                            }
                        });
                    }
                }
                $(document).ready(function () {
                    var class_id = $('#class_id').val();
                    var section_id = '<?php echo set_value('section_id') ?>';
                    getSectionByClass(class_id, section_id);
                    $(document).on('change', '#class_id', function (e) {
                        $('#section_id').html("");
                        $('#subject_id').html("");
                        var class_id = $(this).val();
                        var base_url = '<?php echo base_url() ?>';
					
                        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
                        $.ajax({
                            type: "GET",
							url: base_url + "teacher/sections/getByClass",
                            data: {'class_id': class_id},
                            dataType: "json",
                            success: function (data) {
								$.each(data, function (i, obj)
                                {	
                                    div_data += "<option value=" + obj.section_id + ">" + obj.section + "</option>";
                                });
                                $('#section_id').append(div_data);
                            }
                        });
						
                    });
					$(document).on('change', '#section_id', function (e) {
					   var base_url = '<?php echo base_url() ?>';
					   var class_id = $('#class_id').val();
					   var section_id = $(this).val();
					   $('#subject_id').html(""); 
					   var divs_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
						$.ajax({
							type: "GET",
							url: base_url + "teacher/subject/getSubjctByClass",
							data: {'class_id': class_id, 'section_id': section_id },
							dataType: "json",
							success: function (data) {
								console.log(data);
								$.each(data, function (i, obj)
								{
									var sel = "";
									if (subject_id == obj.id) {
										sel = "selected";
									}
									divs_data += "<option value=" + obj.id + " " + sel + ">" + obj.name + "</option>";
								});
								$('#subject_id').append(divs_data);
							}
						});
				  });
                });
				$(document).ready(function () {
                    $(document).on('click', '#templateModalButton', function (e) {
                        $('#template_id').html("");
                        var class_id =  $('#class_id').val();
						
                        var base_url = '<?php echo base_url() ?>';
                        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
                        $.ajax({
                            type: "GET",
                            url: base_url + "teacher/template/getByClass",
                            data: {'class_id': class_id},
                            dataType: "json",
                            success: function (data) {
								$.each(data, function (i, obj)
                                {	
									div_data += "<option value=" + obj.id + ">" + obj.title + "</option>";
                                });
                                $('#template_id').append(div_data);
                            }
                        });
					});
				  
				  });
            </script>
			<script type="text/javascript">
    var elems = document.getElementsByClassName('confirmation');
    var confirmIt = function (e) {
        if (!confirm('Are you sure?')) e.preventDefault();
    };
    for (var i = 0, l = elems.length; i < l; i++) {
        elems[i].addEventListener('click', confirmIt, false);
    }
</script>