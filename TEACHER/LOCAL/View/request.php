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
                <div class="box box-primary">
						<!-- general form elements -->
						<div class="box box-primary">
							<div class="box-header with-border">
								<h2 class="box-title">Request for Change of Grade</h2>
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
								<form action="<?php echo site_url('teacher/request') ?>"  id="employeeform" name="employeeform" method="post" enctype="multipart/form-data">
									<div class="row">
									<div class="col-md-12">
										<div class="form-group">
										<label for="exampleInputEmail1"><?php echo "School Year"; ?></label>
										<select  id="session_id" name="session_id" class="form-control" >
											<option value=""><?php echo $this->lang->line('select'); ?></option>
											<?php
											foreach ($sessionlist as $session) {
												if($session['id'] == $previous || $session['id']  == $current_session_id ){
												?>
												<option value="<?php echo $session['id'] ?>"<?php if ($session['id'] == $session_id) echo "selected=selected" ?>><?php echo $session['session'] ?></option>
												<?php
												}
											}
											?>
										</select>					
										<span class="text-danger"><?php echo form_error('session_id'); ?></span>
										</div>
									</div>
								</div>
								 <div class="box-footer">
									<button type="submit" class="btn btn-info pull-right"><?php echo "Change"; ?></button>
								</div>
							</form>
								<!-- <label class="alert alert-info"></label> -->
									<!-- Button trigger modal -->
									
								<div class="table-responsive mailbox-messages">
									<br/>
									<?php 
									if( $update_grade == 'yes'){
										?>
										<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalrequestform">
									  		Create Request
										</button>
										<?php
									}
									?>
								
									<br/>
									<!-- Modal -->
									<div class="modal fade" id="modalrequestform" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
									  <div class="modal-dialog modal-dialog-centered" role="document">
									    <div class="modal-content">
									      <div class="modal-header">
									        <h5 class="modal-title" id="exampleModalLongTitle">Request for Change of Grade Form FOR CURRENT SY</h5>
									        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									          <span aria-hidden="true">&times;</span>
									        </button>
									      </div>
									       <form action="<?php echo site_url('teacher/request/request_grade') ?>"  id="employeeform" name="employeeform" method="post">
									      <div class="modal-body">
									        <div class="form-group">
				                                <label for="exampleInputEmail1">Select Student</label> 
				                                <input type="text" list="student_list" class="form-control" id="student_name" name="student_name" autocomplete=off >
				                                <datalist id="student_list"></datalist>
				                                <span class="text-danger"><?php echo form_error('Student'); ?></span>

				                                <input type="hidden" id="student_id" name="student_id">
				                                <span class="text-danger"><?php echo form_error('student_id'); ?></span>
				                            </div> 
				                            <div class="form-group">
				                                 <label for="exampleInputEmail1">Select Subject</label>
	                                            <select  id="subject_id" name="subject_id" class="form-control" >
	                                               <option value=""><?php echo $this->lang->line('select'); ?></option>
	                                                <?php

	                                                    foreach ($listsubjects as $key => $value) {
	                                                    ?>
	                                                    <option  value="<?php echo $value->subject_id; ?>"><?php echo $value->name.'-'.$value->class; ?></option>
	                                                    <?php
	                                                    }
	                                                ?>
	                                            </select>
	                                       	     <span class="text-danger"><?php echo form_error('subject_id'); ?></span>
				                            </div> 
				                             <div class="form-group">
				                                 <label for="exampleInputEmail1">Select Term</label>
	                                            <select  id="quarter" name="quarter" class="form-control" >
	                                               <option value=""><?php echo $this->lang->line('select'); ?></option>
	                                                <?php
	                                                    foreach ($getquarter as $key => $value) {
	                                                    if( $value == 1 && $firstqsettings == 'yes'){
	                                                    	?>
	                                                    	  <option  value="<?php echo $key; ?>">Term 1</option>
	                                                    	<?php
	                                                    }
	                                                     if( $value == 2 && $secondqsettings == 'yes'){
	                                                    	?>
	                                                    	  <option  value="<?php echo $key; ?>">Term 2</option>
	                                                    	<?php
	                                                    }
	                                                     if( $value == 3 && $thirdqsettings == 'yes'){
	                                                    	?>
	                                                    	  <option  value="<?php echo $key; ?>">Term 3</option>
	                                                    	<?php
	                                                    }
	                                                    }
	                                                ?>
	                                            </select>
	                                       	     <span class="text-danger"><?php echo form_error('quarter'); ?></span>
				                            </div> 
				                            <div class="form-group">
				                            	 <label for="exampleInputEmail1">Are you updating INC Grade? </label>
	                                           	 <span><input type="radio" name='is_INC' value="yes">YES</span> <span><input type="radio" name='is_INC' value="no" checked>NO</span>
	                                       	     <span class="text-danger"><?php echo form_error('is_INC'); ?></span>
				                            </div>
				                          <!--   <div class="form-group">
				                            	 <label for="exampleInputEmail1">Submitted his/her grade already ? </label>
	                                           	 <span><input type="radio" name='is_submitted' value="yes" checked>YES</span> <span><input type="radio" name='is_submitted' value="no" >NO</span>
	                                       	     <span class="text-danger"><?php echo form_error('is_INC'); ?></span>
				                            </div> -->
				                            <div class="form-group">
				                            	 <label for="exampleInputEmail1">Grade Should Now Be Recorded </label>
	                                           	 <input name="to_grade" class="form-control" />
	                                       	     <span class="text-danger"><?php echo form_error('to_grade'); ?></span>
				                            </div>
				                            <div class="form-group">
				                            	 <label for="exampleInputEmail1">Reason For Change</label>
	                                           	 <textarea name="teacher_remarks" class="form-control"></textarea>
	                                       	     <span class="text-danger"><?php echo form_error('teacher_remarks'); ?></span>
				                            </div> 
									      </div>
									      <div class="modal-footer">
									      	<input type="hidden" name="set_session_id" id="set_session_id" value="<?php echo $session_id; ?>"/>
									        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									        <button type="submit" class="btn btn-primary">Submit</button>
									      </div>
										  </form>
									    </div>
									  </div>
									</div>
									 <table class="table table-striped table-bordered table-hover example">
										<thead>
											<tr>
												<th>#</th>
												<th>Date Requested</th>
												<th>Student Name</th>
												<th>Grade</th>
												<th>Section</th>
												<th>Subject</th>
												<th>Term</th>
												<th>From</th>
												<th>Update To</th>
												<th>Status</th>
												<th class="text-right"><?php echo $this->lang->line('action'); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
											$n=1;
												foreach ($listgrade as $requestgrade ) {
												?>
												<tr>
													<td class="mailbox-name"> <?php echo $n ?></td>
													<td class="mailbox-name"> <?php echo $requestgrade['date_requested'] ?></td>
													<td class="mailbox-name"> <?php echo $requestgrade['lastname'].', '.$requestgrade['firstname'].' '.$requestgrade['suffix'] ?></td>
													<td class="mailbox-name"> <?php echo $requestgrade['class'] ?></td>
													<td class="mailbox-name"> <?php echo $requestgrade['section'] ?></td>
													<td class="mailbox-name"> <?php echo $requestgrade['subject_name'] ?></td>
													<td class="mailbox-name">
														<?php 
														$term_display = '';
														if ($requestgrade['quarter'] == 1) {
															$term_display = 'Term 1';
														} elseif ($requestgrade['quarter'] == 2) {
															$term_display = 'Term 2';
														} elseif ($requestgrade['quarter'] == 3) {
															$term_display = 'Term 3';
														} else {
															$term_display = $requestgrade['quarter'];
														}
														echo $term_display;
														?>
													</td>
													<td class="mailbox-name"> <?php echo $requestgrade['from'] ?></td>
													<td class="mailbox-name"> <?php echo $requestgrade['to'] ?></td>
													<?php 
													if( $requestgrade['astatus'] == 'approved' ) {?>
														<td class="mailbox-name"><label class="label label-success"><a href="#" class="info_request_button" style="color:white;" stid="<?php echo $requestgrade['id'] ?>"><?php echo $requestgrade['astatus'] ?></a></label></td>
														<?php 
													} elseif( $requestgrade['astatus'] == 'declined' ) {?>
														<td class="mailbox-name"><label class="label label-danger"><a href="#" style="color:white;" class="info_request_button" stid="<?php echo $requestgrade['id'] ?>"><?php echo $requestgrade['astatus'] ?></a></label></td>
														<?php
													} else {
														?>
														<td class="mailbox-name"><label class="label label-warning"><?php echo $requestgrade['astatus'] ?></label></td>
														<?php
													}
													?>
													<td class="mailbox-date pull-right">
													<!-- 	<a href="#" class="btn btn-success btn-xs request_button"  title="<?php echo $this->lang->line('edit'); ?>" id="<?php echo $requestgrade['id'] ?>" stgrade="<?php echo $requestgrade['from'] ?>">
															<i class="fa fa-edit"></i>
														</a> -->
														<?php 
														if( $requestgrade['principal_remarks'] || $requestgrade['academichead_remarks'] ){
															?>
															<a href="#" class="btn btn-primary btn-xs remarks_button"  title="view Remarks" id="<?php echo $requestgrade['id'] ?>" stgrade="<?php echo $requestgrade['from'] ?>">
																<i class="fa fa-eye"></i>
															</a>
															<?php
														}
														?>
														
														<?php 
														if( $requestgrade['pstatus'] == 'pending' && $requestgrade['astatus'] == 'pending') {?>
															<a href="<?php echo base_url(); ?>teacher/request/remove/<?php echo $requestgrade['id'] ?>"class="btn btn-danger btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('Are you sure you want to delete this item?');">
																<i class="fa fa-remove"></i>
															</a>
														<?php
														}?>
													</td>

														
													
												</tr>
												<?php
												$n++;
												}
												
											?>
											
										</tbody>
									</table><!-- /.table -->
								</div><!-- /.mail-box-messages -->
							</div><!-- /.box-body -->
						</div>
					</div>
                </section>
			</div>
			<div class="modal" tabindex="-1" role="dialog" id="remarks_modal">
			  <div class="modal-dialog" role="document">
			  <form action="<?php echo site_url('teacher/request/edit') ?>"  id="employeeform" name="employeeform" method="post">
				<div class="modal-content">
				  <div class="modal-header">Remarks</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
				  <div class="modal-body">
				  	<label> Principal Remarks</label>
					<textarea name="comment_request" id="principal_remarks" class="form-control" readonly></textarea>
					<label> Academichead Remarks</label>
					<textarea name="comment_approved" id="academichead_remarks" class="form-control" readonly></textarea>
					<label> Reason</label>
					<textarea name="comment_approved" id="teacher_remarkss" class="form-control" readonly></textarea>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				  </div>
				</div>
			  </form>
			  </div>
			  </div>
			  <div class="modal" tabindex="-1" role="dialog" id="request_modal_pending">
			  <div class="modal-dialog" role="document">
			  <form action="<?php echo site_url('teacher/request/edit') ?>"  id="employeeform" name="employeeform" method="post">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title">Request To Change Grade</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
				  <div class="modal-body">
					<label> Student Grade </label>
					<input type="text" class="form-control" readonly id="from_grade" />
					<label class="required"> Update to </label>
					<input type="text" class="form-control" name="to_grade" id="to_grade" required/>
					<label> Message ( Optional )</label>
					<textarea name="comment_request" id="comment_request" class="form-control"></textarea>
					<label> Message From The Coordinator</label>
					<textarea name="comment_approved" id="comment_approved" class="form-control" readonly></textarea>
					<label> Message From The Academic Head</label>
					<textarea name="comment_fapproved" id="comment_fapproved" class="form-control" readonly></textarea>
				  </div>
				  <div class="modal-footer">
					<input type="hidden" name="grade_request_id" id="grade_request_id"/>
					<button type="submit" class="btn btn-primary">Submit Request</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				  </div>
				</div>
			  </form>
			  </div>
		</div>
	<script type="text/javascript">
	$(".request_button").click(function(event) {
		var id = $(this).attr('id');
		var stgrade = $(this).attr('stgrade');
		$('#grade_request_id').val(id);
		$('#from_grade').val(stgrade);
		  var base_url = '<?php echo base_url() ?>';
		  	$('#comment_request').val("");
			$('#grade_request_id').val("");
			$('#to_grade').val(""); 
		$.ajax({
			type: "POST",
			url: base_url + "teacher/request/getsearchid",
			data: {'grade_request_id': id},
			dataType: "json",
			success: function (data) {
				if( data ){
					// $('#comment_request').val(data.comment_requested);
					// $('#comment_approved').val(data.comment_approved);
					// $('#comment_fapproved').val(data.comment_fapproved);
					// $('#grade_request_id').val(data.id);
					// $('#to_grade').val(data.to); 

					var status = data.status;
					if( status == 'pending'){
						$('#modalrequestform').modal('show');
					} else {
						// $('#vcomment_request').val(data.comment_requested);
						// $('#vcomment_approved').val(data.comment_approved);
						// $('#vcomment_fapproved').val(data.comment_fapproved);
						// $('#vgrade_request_id').val(data.id);
						// $('#vto_grade').val(data.to); 
						// $('#vfrom_grade').val(stgrade);
						$('#modalrequestform').modal('show');
					}
				} else {

				}
				
				
			}
		});
		
	});

	
	$(".remarks_button").click(function(event) {
		var id = $(this).attr('id');
		var base_url = '<?php echo base_url() ?>';
		$.ajax({
			type: "POST",
			url: base_url + "teacher/request/getsearchid",
			data: {'grade_request_id': id},
			dataType: "json",
				success: function (data) {
					$('#principal_remarks').html(data.principal_remarks );
					$('#academichead_remarks').html(data.academichead_remarks);
					$('#teacher_remarkss').html(data.teacher_remarks); 
			}
		});
		$('#remarks_modal').modal('show');
	});
</script>
<script type="text/javascript">
$(document).ready(function () { 

    $("#student_name").keypress(function(){
        var search_student = $(this).val();
        var session_id = $("#session_id").val();
        var options = {};
        options.url = "<?php echo base_url('teacher/student/getsearchstudentactivewithtransferee'); ?>";
        options.type = "POST";
        options.data = { "search_student": search_student, "session_id":session_id};
        options.dataType = "json";
        options.success = function (data) {
            $("#student_list").empty();
            for(var i=0;i<data.length;i++)
            {
                $("#student_list").append("<option value='" + 
                data[i].lastname + ", "+ data[i].firstname +  data[i].middlename + "' data-id='"+  data[i].id +"'  ></option>");
            }
        };
        $.ajax(options);
    });

    $("#student_name").bind('input', function () {
        var x = checkExists( $('#student_name').val() );
        $('#student_id').val(x);
      
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

	// $("#quarter").change(function(event) {
	// 	var student_id = $('#student_id').val();
	// 	var subject_id = $('#subject_id').val();
	// 	var quarter = $('#quarter').val();
	// 	var base_url = '<?php echo base_url() ?>';
	// 	$('#ifgradedisplay').html("");
	// 	if( student_id = ''){
	// 		alert('Please select student');
	// 	}
	// 	if( subject_id = ''){
	// 		alert('Please select subject');
	// 	}
	// 	if( quarter = ''){
	// 		alert('Please select quarter');
	// 	}
	// 	$.ajax({
	// 		type: "POST",
	// 		url: base_url + "teacher/request/search_grade",
	// 		data: {'student_id': student_id,'subject_id':subject_id,'quarter':quarter},
	// 		dataType: "json",
	// 		success: function (data) {
	// 			$('#ifgradedisplay').val(data);
	// 		}
	// 	});
		
	// });

    
});
 
 
</script>
