<div class="content-wrapper">  
		<section class="content-header">
        <h1><i class="fa fa-user-plus"></i> <?php echo $this->lang->line('student_information'); ?> <small><?php echo $this->lang->line('student1'); ?></small></h1>
        </section>    
        <section class="content">
            <div class="row">           
                <div class="col-md-12">             
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h4>Enrolled Students</h4>
                            <div class="pull-right box-tools">                           
                                
                            </div>
                        </div>
						
						
                        <form id="form1" action="<?php echo site_url('registrar/reserved/activate') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                            <div class="box-body">
                              <?php
							  if ($this->session->flashdata('msg')) { 
									if(is_array($this->session->flashdata('msg'))){
										$ab =  $this->session->flashdata('msg');
										for($x=0;$x<count($ab);$x++ ){
											echo $ab[$x];
										}
									} else {
										echo $this->session->flashdata('msg');
									}
								} ?>
                                <?php echo $this->customlib->getCSRF(); ?>
							
								<div class="tab-pane active table-responsive no-padding" id="tab_1">
									 
									<table class="table table-striped table-bordered table-hover example">
										<thead>
											<tr>
												<th>#</th>
												<th><?php echo $this->lang->line('student_name'); ?></th>
												<th><?php echo 'Grade';//$this->lang->line('class'); ?></th>
												<th><?php echo 'SY'; ?></th> 
												<th><?php echo 'Date Enrolled'; ?></th>
												<th><?php echo 'Status'; ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
											if (empty($studentlist)) {
												?>
												<?php
											} else {
												$count = 1;
												foreach ($studentlist as $student) {
													?>
													<tr> 
														<td><?php echo $count; ?></td>
														<td><?php echo $student['lastname'] . ", " . $student['firstname']. " " . $student['suffix']. " " . $student['middlename']; ?></td>
														<td><?php echo $student['class']?></td>
														<td><?php echo $student['session'] ?></td>
														
														<td><?php echo $student['enrolled_at'] ?></td>
														<?php 
														if(  $student['status'] == 'pending'){
															?>
															<td><label class="label label-primary"><?php echo $student['status'] ?></label></td>
															<?php
														} else {
															?>
															<td><label class="label label-success"><?php echo $student['status'] ?></label></td>
															<?php
														}
														?>
														
														 
													</tr>
													<?php
													$count++;
												}
											}
											?>
										</tbody>
									</table>
								</div>   
                             </div>
							<div class="box-footer">
							 </div>
						</form>
					</div> 
				</div>
			</div>
        </section>
 </div>
<div class="modal fade" id="ReservedStudent" role="dialog">
	<div class="modal-dialog">
		
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h5 class="modal-title title modal_title">Reserve Student</h5>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12 col-xs-12">
							<label class="col-md-4" for="exampleInputEmail1">Type:</label>
							<div class="radio" style="margin-top: 2px;">
							<label><input class="radio-inline rte" checked="checked" type="radio" name="type" value="new" onclick="javascript:fornewstudent();" <?php
								echo set_value('type') == "new" ? "checked" : "";
								?> >New Student</label>
								<label><input class="radio-inline rte"  type="radio" name="type" value="Old" onclick="javascript:foroldstudent();" <?php
									echo set_value('type') == "old" ? "checked" : "";
									?>  >Old Student</label>
								</div>
								<span class="text-danger"><?php echo form_error('type'); ?></span>
						</div>
					</div>
					<div class="row" id="fornewstudent">
						<form id="form1" action="<?php echo site_url('registrar/reserved/save_new') ?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">
							<div class="col-md-6 col-sm-6">
								<div class="form-group">
									<label for="exampleInputEmail1" class="required"><?php echo 'Grade';//$this->lang->line('class'); ?></label>
									<select  id="class_id" name="class_id" class="form-control input-lg" required  >
										<option value=""><?php echo $this->lang->line('select'); ?></option>
										<?php
										foreach ($classlist as $class) {
											?>
											<option value="<?php echo $class['id'] ?>"<?php if (set_value('class_id') == $class['id']) echo "selected=selected" ?>><?php echo $class['class'] ?></option>
											<?php
											$count++;
										}
										?>
									</select>
									<span class="text-danger"><?php echo form_error('class_id'); ?></span>
								</div>
							</div>
							<div class="col-md-6 col-sm-6">
								<div class="form-group">
									<label for="exampleInputEmail1">School Year</label>
									<select  id="session_id" name="session_id" class="form-control input-lg" required  >
										<option value=""><?php echo $this->lang->line('select'); ?></option>
										<?php
										foreach ($sessionlist as $session) {
											?>
											<option value="<?php echo $session['id'] ?>" <?php if ($session['id'] == $session_id) echo "selected=selected" ?>><?php echo $session['session'] ?></option>
											<?php
											$count++;
										}
										?>
									</select>
									<span class="text-danger"><?php echo form_error('session_id'); ?></span>
								</div>
							</div>
							<div class="col-md-4 col-sm-6">
								<div class="form-group">
									<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('first_name'); ?></label>
									<input id="firstname" name="firstname" placeholder="" type="text" class="form-control input-lg"  value="<?php echo set_value('firstname'); ?>" required />
									<span class="text-danger"><?php echo form_error('firstname'); ?></span>
								</div>
							</div>
							<div class="col-md-4 col-sm-6">
								<div class="form-group">
									<label for="exampleInputEmail1"><?php echo $this->lang->line('middle_name'); ?></label>
									<input id="middlename" name="middlename" placeholder="" type="text" class="form-control input-lg"  value="<?php echo set_value('middlename'); ?>" />
									<span class="text-danger"><?php echo form_error('middlename'); ?></span>
								</div>
							</div>
							<div class="col-md-4 col-sm-6">
								<div class="form-group">
									<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('last_name'); ?></label>
									<input id="lastname" name="lastname" placeholder="" type="text" class="form-control input-lg"  value="<?php echo set_value('lastname'); ?>" required />
									<span class="text-danger"><?php echo form_error('lastname'); ?></span>
								</div>
							</div>
							 <div class="col-md-4 col-sm-6">
								<div class="form-group">
									<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('date_of_birth'); ?></label>
									<input id="dob" name="dob" placeholder="" type="text" class="form-control input-lg"  value="<?php echo set_value('dob'); ?>" readonly="readonly" required />
									<span class="text-danger"><?php echo form_error('dob'); ?></span>
								</div>
							</div>
							<div class="col-md-4 col-sm-6">
								<div class="form-group">
									<label for="exampleInputFile" class="required"><?php echo $this->lang->line('gender'); ?></label>
									<select class="form-control input-lg" name="gender" required>
										<option value=""><?php echo $this->lang->line('select'); ?></option>
										<?php
										foreach ($genderList as $key => $value) {
											?>
											<option value="<?php echo $key; ?>" <?php if (set_value('gender',$gender) == $key) echo "selected"; ?>><?php echo $value; ?></option>
											<?php
										}
										?>
									</select>
									<span class="text-danger"><?php echo form_error('gender'); ?></span>
								</div>
							</div>
							<div class="col-md-4 col-sm-6">
								 <div class="form-group">
									<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('guardian_phone'); ?></label>
									<input id="guardian_phone" name="guardian_phone" placeholder="" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_phone'); ?>" required />
									<span class="text-danger"><?php echo form_error('guardian_phone'); ?></span>
								</div>
							</div>
							<div class="col-md-4 col-sm-6">
								<div class="form-group">
									<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('guardian_name'); ?></label>
									<input id="guardian_name" name="guardian_name" placeholder="" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_name'); ?>" required />
									<span class="text-danger"><?php echo form_error('guardian_name'); ?></span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="exampleInputEmail1"><?php echo $this->lang->line('guardian_midname'); ?></label>
									<input id="guardian_midname" name="guardian_midname" placeholder="" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_midname'); ?>" />
									<span class="text-danger"><?php echo form_error('guardian_midname'); ?></span>
								</div>
							</div>
							<div class="col-md-4 col-sm-6">
								<div class="form-group">
									<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('guardian_lastname'); ?></label>
									<input id="guardian_lastname" name="guardian_lastname" placeholder="" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_lastname'); ?>" required  />
									<span class="text-danger"><?php echo form_error('guardian_lastname'); ?></span>
								</div>
							</div>
						
							<div class="col-md-12">
								<button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
								<button type="submit" class="btn btn-primary pull-right"><?php echo $this->lang->line('save'); ?></button>
							</div>
						</form>
					</div>
					<div class="row" id="foroldstudent" style="display:none;">
					<form id="form2" action="<?php echo site_url('registrar/reserved/save_old') ?>" method="post">
							<div class="col-md-12 col-sm-6">
								<div class="form-group">
									<label for="exampleInputEmail1" class="required">Student Name (Search By Student Firstname OR Lastname OR Admission No OR LRN ):</label>
									 <input type="text" list="student_list" class="form-control input-lg" id="student_name" name="student_name" autocomplete=off >
									 <datalist id="student_list"></datalist> 
									 <input type="hidden" id="student_id" name="student_id" required>
									 <span id="recent" style="color:orange;font-size:14px;"><em></em></span>
								</div>
							</div>
							<div class="col-md-6 col-sm-6">
							<div class="form-group">
								<label for="exampleInputEmail1" class="required"><?php echo 'Grade';//$this->lang->line('class'); ?></label>
								<select  id="class_id" name="class_id" class="form-control input-lg" required >
									<option value=""><?php echo $this->lang->line('select'); ?></option>
									<?php
									foreach ($classlist as $class) {
										?>
										<option value="<?php echo $class['id'] ?>"<?php if (set_value('class_id') == $class['id']) echo "selected=selected" ?>><?php echo $class['class'] ?></option>
										<?php
										$count++;
									}
									?>
								</select>
								<span class="text-danger"><?php echo form_error('class_id'); ?></span>
							</div>
						</div>
						<div class="col-md-6 col-sm-6">
							<div class="form-group">
								<label for="exampleInputEmail1" class="required">School Year</label>
								<select  id="session_id" name="session_id" class="form-control input-lg" required >
									<option value=""><?php echo $this->lang->line('select'); ?></option>
									<?php
									foreach ($sessionlist as $session) {
										?>
										<option value="<?php echo $session['id'] ?>" <?php if ($session['id'] == $session_id) echo "selected=selected" ?>><?php echo $session['session'] ?></option>
										<?php
										$count++;
									}
									?>
								</select>
								<span class="text-danger"><?php echo form_error('session_id'); ?></span>
                            </div>
						</div>
						<div class="col-md-12">
							<button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
							<button type="submit" class="btn btn-primary pull-right"><?php echo $this->lang->line('save'); ?></button>
						</div>
						</form>
					</div>
				</div>
			</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function () {
	var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
	 $('#dob,#admission_date').datepicker({
		format: date_format,
		autoclose: true
	});
	
	 $("#student_name").keypress(function(){
        var search_student = $(this).val();
        var options = {};
        options.url = "<?php echo base_url('registrar/student/getsearchstudentname'); ?>";
        options.type = "POST";
        options.data = { "search_student": search_student };
        options.dataType = "json";
        options.success = function (data) {
            $("#student_list").empty();
            for(var i=0;i<data.length;i++)
			{
				$("#student_list").append("<option class='ab' value='" + 
				data[i].lastname + ", "+ data[i].firstname + " "+ data[i].middlename + "' data-id='"+  data[i].id +"'  ></option>");
			}
        };
        $.ajax(options);
    });

    $("#student_name").bind('input', function () {
        var x = checkExists( $('#student_name').val() );
        $('#student_id').val(x);
		if(x){
			check_next();
		}
    });
	
    function checkExists(inputValue) {
        console.log(inputValue);
        
        var x = document.getElementById("student_list");
        var i;
        var flag;
        var flagx;
        for (i = 0; i < x.options.length; i++) {
            if(inputValue == x.options[i].value){
                flag = true;
                flagx = $( x.options[i] ).attr('data-id');
            }
        }
        return flagx;
    }
	
	function check_next(){
		   var student_id = $('#student_id').val();
			var options = {};
			options.url = "<?php echo base_url('registrar/reserved/getNextClassID'); ?>";
			options.type = "POST";
			options.data = { "student_id": student_id };
			options.dataType = "json";
			options.success = function (data) {
				if(data){
					$("#recent em").text(data.msg);	
					$("#foroldstudent #class_id").val(data.next_class);	
				} else {
					$("#recent em").text();
				}
			};
			$.ajax(options);
	}

});
</script>
<script type="text/javascript">
	function foroldstudent(){
		$('#foroldstudent').css('display','block');
		$('#fornewstudent').css('display','none');
	}
	function fornewstudent(){
		$('#fornewstudent').css('display','block');
		$('#foroldstudent').css('display','none');
	}
	
	$("input#selectall").click(function(event) {
		if( this.checked ) {
			$('input.get_student_id[type=checkbox]:enabled').each(function() {
					this.checked = true;
			});
		} else {
			$('input.get_student_id[type=checkbox]').each(function() {
				this.checked = false;
			});
		}
	});	
</script>
      