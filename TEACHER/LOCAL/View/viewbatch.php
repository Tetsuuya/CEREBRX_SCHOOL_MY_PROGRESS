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
								<h3 class="box-title"><?php //echo $title_list;   ?><?php echo $this->lang->line('imported_grades_details'); ?></h3>
								<div class="box-tools pull-right">
								</div><!-- /.box-tools -->
							</div><!-- /.box-header -->
							<div class="box-body">
								<div class="mailbox-controls" style="font-size:14px;">
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
								<div class="table-responsive mailbox-messages">
								 <form id="form1" id="employeeform" name="employeeform" method="post" accept-charset="utf-8"  enctype="multipart/form-data">
									
									<table class="table table-striped table-bordered table-hover example">
										<thead>
											<tr>
												<td  colspan="2"><h4><small><b>Grade & Section:</b></small>&nbsp;<span class="label label-success"><?php echo $importgrades['class'].' '.$importgrades['section']; ?></span></h4></td>
												<td><h4><small><b>Subject:</b></small>&nbsp;<span class="label label-success"><?php echo $importgrades['name']; ?></span></h4></td>
												<td><h4><small><b>Submitted By:</b></small>&nbsp;
													<span class="label label-success">
													<?php if($importgrades['teacher_id'] == 0 ){
															echo "Administrator";
														  } else {
															  $teacher_id  = $importgrades['teacher_id'];
															  $teacher_details  = $this->teacher_model->get( $teacher_id );
															  $teacher_name = isset($teacher_details['name'])?$teacher_details['name']:'';
															  $teacher_lastname = isset($teacher_details['lastname'])?$teacher_details['lastname']:'';
															  echo $teacher_name.' '.$teacher_lastname;
														  }
													?>
													</span></h4>
												</td>
												<td align="right"><h4><small><b>Quarter:</b></small>&nbsp;<span class="label label-success"><?php echo $this->setting_model->getquarter($importgrades['quarter']); ?></span></h4></td>
											</tr>
											<tr>
												<th style="background-color:#ffff;" ><a href="<?php echo site_url('teacher/grade/imported') ?>" class="btn btn-sm btn-default">Back</a></th>
												<th style="background-color:#ffff;"></th>
												<th style="background-color:#ffff;"></th>
												<th style="background-color:#ffff;"></th>
												
												<th style="background-color:#ffff;">
												</th>
											</tr>
											<tr>
												<th style="background-color:#ffff;" ><input type="checkbox" id="selectall" /></th>
												<th style="background-color:#ffff;"></th>
												<th style="background-color:#ffff;"></th>
												
												<th style="background-color:#ffff;"></th>
												<th style="background-color:#ffff;" class="text-right">
													<?php 
													if( $importgrades['published'] != 1 ){
													?>
													<a href="<?php echo base_url(); ?>teacher/grade/submit/<?php echo $id; ?>" class="btn btn-success btn-sm"  data-toggle="tooltip" title="Submit Grades">
														Submit Grades
													</a>
													<button type="submit" class="btn btn-danger btn-sm"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete_all'); ?>" onclick="return confirm('Are you sure you want to delete these item?');" formaction="<?php echo base_url(); ?>teacher/grade/deleted_all/">Delete Selected</button>
													<?php
													}
													?>	
													<!--<a href="<?php echo site_url(); ?>teacher/grade/class_record/<?php echo $id;?>" class="btn btn-primary btn-sm"  data-toggle="tooltip" title="<?php echo 'View Class Record'; ?>">View Class Record</a> -->
												</th>
											</tr>
											<tr>
												<th class="sorting_disabled"></th>
												<th class="sorting_disabled"><?php echo "Student Name"; ?>   </th>
												<th class="sorting_disabled"><?php echo $this->lang->line('admission_no'); ?>   </th>
												<th class="sorting_disabled"><?php echo $this->lang->line('grade'); ?>   </th>
												<?php 
												if( $importgrades['published'] == 1 ){?>
													<th class="sorting_disabled"><?php echo $this->lang->line('status'); ?>   </th>
												<?php 
												} else {
													?>
														<th class="sorting_disabled"></th>
													<?php
												}?>
												<!-- <th class="sorting_disabled text-right"><?php echo $this->lang->line('action'); ?></th> -->
											</tr>
										</thead>
										<tbody>
											<tr>
												<td><b>Male</b></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												
											</tr>
											<?php
											foreach ($studentlist_male as $gradesdetails) { 
												$status = $gradesdetails['approved'] == 1?'Approved':'Pending';
												$get_grades =  $gradesdetails['grades'];
												$paint_red= '';
												if( $get_grades < 75 ){
													$paint_red = 'style="color:red;font-weight:bold;"';
												}
												?>
											<tr <?php echo $paint_red; ?>>
												<td class="mailbox-name">
												<?php 
												if( $status == 'Pending'){ 
													?>
													<input type="checkbox" class="approved_grades" name="approved_grades[]" value="<?php echo $gradesdetails['import_grades_details_id']?>" />
													<?php
												} else {
													?>
													<input type="checkbox" class="approved_grades" name="approved_grades[]" value="<?php echo $gradesdetails['import_grades_details_id']?>"   />	
													<?php
												}
												?>
												</td>
												<td class="mailbox-name"> <?php echo $gradesdetails['lastname'].', '.$gradesdetails['firstname'] ?></td>
												<td class="mailbox-name"> <?php echo $gradesdetails['admission_no'] ?></td>
												<td class="mailbox-name"> <?php echo $gradesdetails['grades'] ?></td>
												<?php 
													if( $importgrades['published'] == 1 ){?>
														<?php 
														if( $status == 'Approved') {?>
															<td class="mailbox-name"><label class="label label-success"><?php echo $status ?></label></td>
															<?php 
														} elseif( $status == 'Pending') {?>
															<td class="mailbox-name"><label class="label label-warning"><?php echo $status ?></label></td>
															<?php
														}
														?>
														<?php 
													} else {
														?>
														<td></td>
														<?php
													}
												?>
												<!--<td class="mailbox-date pull-right">
													<?php 
													if( $importgrades['published'] != 1 ){?>
														
														<a href="<?php echo base_url(); ?>teacher/grade/delete_details/<?php echo $gradesdetails['import_grades_details_id']?>" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('Are you sure you want to delete this item?');">
														<?php echo $this->lang->line('delete'); ?>
														</a>															
															
													<?php
													}
													if( $importgrades['published'] == 1 ){?>
														<a href="#" id="<?php echo $gradesdetails['import_grades_details_id'];?>" class="btn btn-warning btn-xs request_button"  data-toggle="tooltip" title="Request to change" stgrade="<?php echo $gradesdetails['grades'] ?>">
															Request to Update Grade
														</a>
														<?php 
													}
													?>
												</td> --->
											</tr>
											<?php
											}
											?>
											<tr>
												<td><b>Female</b></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
														
											</tr>
											<?php
											foreach ($studentlist_female as $gradesdetails) { 
												$status = $gradesdetails['approved'] == 1?'Approved':'Pending';
												$get_grades = $gradesdetails['grades'];
												$paint_red= '';
												if( $get_grades < 75 ){
													$paint_red = 'style="color:red;font-weight:bold;"';
												}
												?>
											<tr <?php echo $paint_red; ?>>
												<td class="mailbox-name">
												<?php 
												if( $status == 'Pending'){ 
													?>
													<input type="checkbox" class="approved_grades" name="approved_grades[]" value="<?php echo $gradesdetails['import_grades_details_id']?>" />
													<?php
												} else {
													?>
													<input type="checkbox" class="approved_grades" name="approved_grades[]" value="<?php echo $gradesdetails['import_grades_details_id']?>"   />	
													<?php
												}
												?>
												</td>
												<td class="mailbox-name"> <?php echo $gradesdetails['lastname'].', '.$gradesdetails['firstname'] ?></td>
												<td class="mailbox-name"> <?php echo $gradesdetails['admission_no'] ?></td>
												<td class="mailbox-name"> <?php echo $gradesdetails['grades'] ?></td>
												<?php 
												if( $importgrades['published'] == 1 ){?>
												<?php 
													if( $status == 'Approved') {?>
														<td class="mailbox-name"><label class="label label-success"><?php echo $status ?></label></td>
														<?php 
													} elseif( $status == 'Pending') {?>
														<td class="mailbox-name"><label class="label label-warning"><?php echo $status ?></label></td>
														<?php
													}
													?>
												<?php 
												} else {
													?>
													<td></td>
													<?php
												}
												?>
												<!--<td class="mailbox-date pull-right">
													<?php 
													//if( $importgrades['published'] != 1 ){?>
												
														<a href="<?php echo base_url(); ?>teacher/grade/delete_details/<?php echo $gradesdetails['import_grades_details_id']?>" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('Are you sure you want to delete this item?');">
														<?php echo $this->lang->line('delete'); ?>
														</a>															
													<?php
													//}
													?>
													<?php 
													
													//if( $importgrades['published'] == 1 ){?>
														<a href="#" id="<?php echo $gradesdetails['import_grades_details_id'];?>" class="btn btn-warning btn-xs request_button"  data-toggle="tooltip" title="Request to change" stgrade="<?php echo $gradesdetails['grades'] ?>">
															Request to Update Grade
														</a>
													<?php 
													//}
												?>
												</td> -->
											</tr>
												<?php
											}
											?>
										</tbody>
									</table><!-- /.table -->
									<input type="hidden" name="quarter" value="<?php echo $importgrades['quarter'];?>" />
									<input type="hidden" name="subject_id" value="<?php echo $importgrades['subject_id'];?>" />
									</form>
								</div><!-- /.mail-box-messages -->
							</div><!-- /.box-body -->
						</div>
                </div>
                </section>
			</div>
			<div class="modal" tabindex="-1" role="dialog" id="request_modal">
				
								
				  <div class="modal-dialog" role="document">
				  <form action="<?php echo site_url('teacher/request/request_grade') ?>"  id="employeeform" name="employeeform" method="post">
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
					  </div>
					  <div class="modal-footer">
						<input type="hidden" name="grade_details_id" id="grade_details_id"/>
						<input type="hidden" name="grade_request_id" id="grade_request_id"/>
						<input type="hidden" name="grade_batch_id" id="grade_batch_id" value="<?php echo $id; ?>"/>
						<button type="submit" class="btn btn-primary">Submit Request</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					  </div>
					</div>
				  </form>
				  </div>
		</div>
<script type="text/javascript">
	$("input#selectall").click(function(event) {
		if( this.checked ) {
			$('input.approved_grades[type=checkbox]:enabled').each(function() {
					this.checked = true;
			});
		} else {
			$('input.approved_grades[type=checkbox]').each(function() {
				this.checked = false;
			});
		}
	});
	$(document).ready(function () {
		$('.example').dataTable( {
			"order": [],
			"columnDefs": [ {
			  "targets"  : 'sorting_disabled',
			  "orderable": false,
			}]
		});
	});
	$(".request_button").click(function(event) {
		var id = $(this).attr('id');
		var stgrade = $(this).attr('stgrade');
		$('#grade_details_id').val(id);
		$('#from_grade').val(stgrade);
		  var base_url = '<?php echo base_url() ?>';
		  	$('#comment_request').val("");
			$('#grade_request_id').val("");
			$('#to_grade').val(""); 
		$.ajax({
			type: "POST",
			url: base_url + "teacher/request/getsearchrequest",
			data: {'grade_details_id': id},
			dataType: "json",
			success: function (data) {
				$('#comment_request').val(data.comment_requested);
					$('#grade_request_id').val(data.id);
					$('#to_grade').val(data.to); 
			}
		});
		$('#request_modal').modal('show');
	});
</script>
