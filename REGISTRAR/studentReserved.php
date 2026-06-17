<style>

input[type="text"], input[type="password"], input[type="tel"] {

	background-image: linear-gradient(0deg,#ccc 2px,rgba(0,0,0,0) 0),linear-gradient(0deg,rgba(0,0,0,0) 1px,transparent 0) !important;

	border:0px;

	color:black;

}

</style>

<div class="content-wrapper">  

		<section class="content-header">

        <h1><i class="fa fa-user-plus"></i> <?php echo $this->lang->line('student_information'); ?> <small><?php echo $this->lang->line('student1'); ?></small></h1>

        </section>    

        <section class="content">

            <div class="row">           

                <div class="col-md-12">             

                    <div class="box box-primary">

                        <div class="box-header with-border">

                            <h4>Reserved Students</h4>

                            <div class="pull-right box-tools">                           

                                <a href="#" class="btn btn-primary btn-md" data-toggle="modal" data-target="#ReservedStudent" id="ReservedStudentButton" >

                                    <i class="fa fa-plus"></i> Add Student

                                </a>

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

								<?php //if ($this->session->flashdata('enrollment_msg')) { ?>

                                <?php //echo $this->session->flashdata('enrollment_msg') ?>

                                <?php //} ?> 

                                <?php echo $this->customlib->getCSRF(); ?>

							

								<div class="tab-pane active table-responsive no-padding" id="tab_1">

									<?php 

									if( $count_unconfirmed > 0 ){

										?>

											<a href="<?php echo base_url().'registrar/reserved/unconfirmed';?>" style="color:#ffff;"><h4 class="pull-left"><label class="label label-warning" style="cursor:pointer;">View Unconfirmed Pre-enrollment Students ( <?php echo $count_unconfirmed;?> )</label></h4></a>

										<?php

									}?>

									<table class="table table-striped table-bordered table-hover example">

										<thead>

											<tr>

												<th class="sorting_disabled"><input type="checkbox" id="selectall" /></th>

												<th><?php echo 'Date Reserved'; ?></th>

												<th><?php echo $this->lang->line('student_name'); ?></th>

												<th><?php echo 'LRN'; ?></th>

												<th><?php echo 'Guardian #';//$this->lang->line('class'); ?></th>

												<th><?php echo 'Grade';//$this->lang->line('class'); ?></th>

												<th><?php echo 'Type'; ?></th>

												<th>Enrollment Option</th>                                        

												<th>Status</th>                                        

												<th>View</th>    

												<th>Action</th>    

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

														$id = $student['id'];

														$enrollment_status = $student['enrollment_status'];

														$date_scheduled = $student['date_scheduled'];

														$process_type = $student['process_type'];

														$payment_type = $student['payment_type'];

														$payment_link = $student['payment_link'];

														$paid_status = $student['paid_status'];

														$payment_fees = $student['payment_fees'];

														if( $payment_type ){

                                           					$payment_type = $this->enrollmentonline_model->check_payment_type( $payment_type );

                                     					}

													?>

													<tr> 

														<td >

															<input type="checkbox" name="get_id[]" class="get_student_id" value="<?php echo $student['id'] ?>"/>

															<?php echo $count; ?>

														</td>

														<td><?php echo $student['created_at'] ?></td>

														<td><?php echo $student['lastname'].', '.$student['firstname'] . " " .$student['suffix']." ".$student['middlename']; ?></td>


														<td><?php echo $student['lrn']?></td>

														<td><?php echo $student['guardian_phone']?></td>

														<td><?php echo $student['class']?></td>

														<?php 

														if(  $student['type'] == 'new'){

															?>

															<td><label class="label label-success"><?php echo $student['type'].' '.'student'; ?></label></td>

															<?php

														} elseif(  $student['type'] == 'old'){

															?>

															<td><label class="label label-primary"><?php echo $student['type'].' '.'student'; ?></label></td>

															<?php

														} else {

															?>

															<td><label class="label label-warning"><?php echo $student['type'].' '.'student'; ?></label></td>

															<?php

														}

														?>

														<td><?php echo '<strong>'.strtoupper($process_type).'</strong>:'.$payment_type.' - '.$payment_fees;?></td>

														  <?php

														if( $enrollment_status == 'pending'){

															?>

															<td><label class="label label-primary"><?php echo ucfirst($enrollment_status);?></label></td>

															<?php

														}

														if( $enrollment_status == 'passed'){

															?>

															<td><label class="label label-success"><?php echo ucfirst($enrollment_status);?></label></td>

															<?php

														}

														if( $enrollment_status == 'failed'){

															?>

															<td><label class="label label-danger"><?php echo ucfirst($enrollment_status);?></label></td>

															<?php

														}?>

													   <td>

													   	<a class="btn btn-default btn-xs click_modal" id="<?php echo $id;?>">View</a>

													   </td>

													   <td>

														

														<?php 

														if( $process_type == 'online'){

			                                                if( $enrollment_status == 'pending'){ 

																if( !empty($payment_link) ){ 

																	?> 

																	<a href="<?php echo base_url().'registrar/reserved/passed/'.$id; ?>" class="btn btn-success btn-xs">Pass</a>

																	<?php 

																}

																if( $student['type'] != 'old' ) {?>

																	<a href="<?php echo base_url().'registrar/reserved/failed/'.$id; ?>" class="btn btn-danger btn-xs">Fail</a>

																	<?php 

																}

			                                                    if( $paid_status != 'yes' || $paid_status != 'promissory'){

			                                                        ?> 

			                                                        <a href="<?php echo base_url().'registrar/reserved/pay_on_site/'.$id; ?>" class="btn btn-warning  btn-xs" onclick="return confirm('Online Enrollment will be updated to Pre-Enroll. Are you sure you want to update?');">Pay on Site</a>

			                                                        <?php

			                                                    }

															}

														} else {

			                                              if( $enrollment_status == 'pending'){ 

			                                                    ?> 

			                                                    <a href="#" class="btn btn-success btn-xs" data-toggle="modal" data-target=".sentSMS_<?php echo $id;?>">

			                                                        Pass

			                                                    </a>

			                                                    <div class="modal fade sentSMS_<?php echo $id;?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

			                                                      <div class="modal-dialog modal-dialog-centered" role="document">

			                                                        <div class="modal-content">

			                                                          <div class="modal-footer" style="text-align:left;">

			                                                            SENT SMS ? 

			                                                            <a href="<?php echo base_url().'registrar/reserved/passed/'.$id.'?sms=yes'; ?>" class="btn btn-success" >YES</a>

			                                                            <a href="<?php echo base_url().'registrar/reserved/passed/'.$id.'?sms=no'; ?>"  class="btn btn-primary">NO</a>

			                                                            <a href="<?php echo base_url().'registrar/reserved/passed/'.$id.'?sms=no'; ?>"  class="btn btn-default" data-dismiss="modal">CLOSE</a>

			                                                          </div>

			                                                        </div>

			                                                      </div>

			                                                    </div>

			                                                    <?php

			                                                     if( $student['type'] != 'old' ) {?>

			                                                    <a href="<?php echo base_url().'registrar/reserved/failed/'.$id; ?>" class="btn btn-danger btn-xs">Fail</a>

			                                                    <?php 

			                                                  } 

			                                                }

			                                            }

			                                            if(( $student['review_cashier'] == 'yes' &&  $enrollment_status == 'passed') || ( $student['paid_status'] == 'promissory' &&  $enrollment_status == 'passed')){

			                                               ?> 

			                                                <a href="<?php echo base_url().'registrar/reserved/get_activate/'.$id; ?>" class="btn btn-success  btn-xs" onclick="return confirm('Are you sure you want to enroll this student ?');">Activate</a>

			                                                <?php      

			                                            } else {

			                                            	if( $enrollment_status == 'passed'){

			                                            		?>

			                                            	 <label class="label label-default">Not yet review by the cashier</label>

			                                            	 <?php

			                                            	}

			                                            	

			                                            }

			                                            ?> 

														

														</td>

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

								<button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this data?');" formaction="<?php echo base_url(); ?>registrar/reserved/delete_all/"><?php echo $this->lang->line('delete'); ?></button>

								<button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to unconfirmed this data?');" formaction="<?php echo base_url(); ?>registrar/reserved/unconfirmed_all/"><?php echo "unconfirmed" ?></button>

								<button type="submit" class="btn btn-info pull-right" onclick="return confirm('Are you sure you want to activate this data? Only status Reserved will be activated.');" >Activate</button>

							</div>

						</form>

					</div> 

				</div>

			</div>

        </section>

 </div>

<div class="modal fade" id="ReservedStudent" role="dialog">

	<div class="modal-dialog  modal-lg">

		

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

									<label for="exampleInputEmail1" class="required"><?php echo 'Incoming Grade';//$this->lang->line('class'); ?></label>

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

						

							

								<div class="clearfix"></div>

							<div class="col-md-3 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('first_name'); ?></label>

									<input id="firstname" name="firstname" placeholder="Enter First Name" type="text" class="form-control input-lg"  value="<?php echo set_value('firstname'); ?>" required  onkeyup="this.value = this.value.toUpperCase();"  />

									<span class="text-danger"><?php echo form_error('firstname'); ?></span>

								</div>

							</div>

							<div class="col-md-3 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1"><?php echo $this->lang->line('middle_name'); ?></label>

									<input id="middlename" name="middlename" placeholder="Enter Middle Name" type="text" class="form-control input-lg"  value="<?php echo set_value('middlename'); ?>"  onkeyup="this.value = this.value.toUpperCase();"  />

									<span class="text-danger"><?php echo form_error('middlename'); ?></span>

								</div>

							</div>

							<div class="col-md-3 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('last_name'); ?></label>

									<input id="lastname" name="lastname" placeholder="Enter Last Name" type="text" class="form-control input-lg"  value="<?php echo set_value('lastname'); ?>" required  onkeyup="this.value = this.value.toUpperCase();" />

									<span class="text-danger"><?php echo form_error('lastname'); ?></span>

								</div>

							</div>

							 <div class="col-md-3 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1">Suffix</label>

									<input id="suffix" name="suffix" placeholder="Enter Suffix" type="text" class="form-control input-lg"  value="<?php echo set_value('suffix'); ?>"  onkeyup="this.value = this.value.toUpperCase();"  />

									<span class="text-danger"><?php echo form_error('suffix'); ?></span>

								</div>

							</div>

							<div class="col-md-3 col-sm-6">

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

							<div class="col-md-3">

								<label for="exampleInputEmail1" class="col-sm-12 title">Religion<span style="color:red;">*</span></label>

								<select  id="religion" name="religion" class="form-control" required  style="height:50px;" >

									<option value=""><?php echo $this->lang->line('select'); ?> Religion</option>

									<?php

									foreach ($religionList as $key ) {

										?>

										<option value="<?php echo $key ?>"<?php if (set_value('religion') == $key) echo "selected=selected" ?>><?php echo $key ?></option>

										<?php

									}

									?>

									<option value="other">Other</option>

								</select>

								<span class="text-danger"><?php echo form_error('religion'); ?></span>

							</div>

							<div class="col-md-3" id="display_religion" style="display:none;">

								<label for="exampleInputEmail1" class="col-sm-12 title">Enter Religion<span style="color:red;">*</span></label>

								<input type="text" id="other_religion" name="other_religion" placeholder="Enter Other Religion" class="form-control input-lg" />

								<span class="text-danger"><?php echo form_error('other_religion'); ?></span>

							</div>

							 <div class="col-md-3 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('date_of_birth'); ?></label>

									<input id="dob" name="dob" placeholder="mm/dd/yyyy" type="text" class="form-control input-lg"  value="<?php echo set_value('dob'); ?>" readonly="readonly" required />

									<span class="text-danger"><?php echo form_error('dob'); ?></span>

								</div>

							</div>

							

							<div class="clearfix"></div>

							<div class="col-md-3 col-sm-3">

								<div class="form-group">

									<label for="exampleInputEmail1"><?php echo $this->lang->line('lrn'); ?></label>

									<input id="lrn" name="lrn" placeholder="Enter LRN" type="text" class="form-control input-lg"  value="<?php echo set_value('lrn'); ?>" />

									<span class="text-danger"><?php echo form_error('lrn'); ?></span>

								</div>

							</div>

							<div class="col-md-3 col-sm-3">

								<div class="form-group">

								<label for="exampleInputEmail1">Modality<span style="color:red;">*</span></label>

								<select  id="modality" name="modality" class="form-control input-lg" required >

									<option value=""><?php echo $this->lang->line('select'); ?> Modality</option>

									<?php

									foreach ($modalityList as $key => $value ) {

										?>

										<option value="<?php echo $key ?>"<?php if (set_value('modality') == $key) echo "selected=selected" ?>><?php echo $value ?></option>

										<?php

									}

									?>

								</select>

								<span class="text-danger"><?php echo form_error('modality'); ?></span>

								</div>

							</div>

							

							<div class="col-md-3 col-sm-3"  id="strand" style="display:none">

								<div class="form-group">

									<label class="required" for="exampleInputEmail1"><?php echo "Strand"; ?></label>

									<select class="form-control"  placeholder="Select Strand" name="strand_id" id="strand_id" style="height:50px;">

									</select> 

									<span class="text-danger"><?php echo form_error('strand_id'); ?></span>

								</div>

							</div>



							<div class="col-md-3" id="paymenttype" style="display:none;">

								<label for="exampleInputEmail1" class="title">Select Enrollment/Fees Option<span style="color:red;">*</span></label>

								<select class="form-control"  placeholder="Select Type" name="payment_type" id="payment_type" style="height:50px;">

								</select> 

								<span class="text-danger"><?php echo form_error('payment_type'); ?></span>

							</div>

							<div class="clearfix"></div>

							<div class="col-md-6 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1">Last School Attended</label>

									<input id="school_name" name="school_name" placeholder="Enter Last School Attended" type="text" class="form-control input-lg"  value="<?php echo set_value('school_name'); ?>"   onkeyup="this.value = this.value.toUpperCase();" />

									<span class="text-danger"><?php echo form_error('school_name'); ?></span>

								</div>

							</div>

							<div class="col-md-6 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1">Enter School Address</label>

									<input id="school_address" name="school_address" placeholder="Enter School Address" type="text" class="form-control input-lg"  value="<?php echo set_value('school_address'); ?>"   onkeyup="this.value = this.value.toUpperCase();"  />

									<span class="text-danger"><?php echo form_error('school_address'); ?></span>

								</div>

							</div>

                             <div class="col-md-6" id="subsidized_wrapper" name="subsidized">

                                <label class="title">Is your parent working in an Adventist institution?<span style="color:red;">*</span></label>&nbsp;&nbsp;<span><input class="form-check-inline mr-0 subsidized_radio" type="radio" name="subsidized" id="subsidized1" value="yes"

                                      <?php if (set_value('subsidized') == 'yes') echo 'checked'; ?> required />Yes</span>

                                   <span><input class="form-check-inline mr-0  mt-3 ml-4 subsidized_radio" type="radio" name="subsidized" id="subsidized2" value="no"

                                      <?php if (set_value('subsidized') == 'no') echo 'checked'; ?>

                                      />No</span>

                             </div>

                             <div class="col-md-6" id="mission_wrapper" style="display: none;">

                                <label class="title">Name of Institution?<span style="color:red;">*</span></label>

                                <input id="institution" name="institution" placeholder="Name of Institution" value="<?php echo set_value('institution') ?>" type="text" class="form-control input-lg"  onkeyup="this.value = this.value.toUpperCase();">

                                 <span class="text-danger show_error"><?php echo form_error("institution");?></span>

                             </div>

                             <div class="clearfix"></div>

                                   <div class="form-group">

		                                 <div class="col-md-6" id="if_adventist">

		                                      <label class="title">If Adventist, baptized?<span style="color:red;">*</span></label>

			                                    <div class="form-check" id="baptized_rad">

			                                       <span> <input class="form-check-inline mr-0 mt-3 baptized_radio" type="radio" name="if_adventist" id="baptized1" value="yes"  <?php if (set_value('if_adventist') == 'yes') echo 'checked'; ?> required />Yes

			                                       </span>

			                                       <span>

			                                       <input class="form-check-inline mr-0  mt-3 ml-4 baptized_radio" type="radio"  name="if_adventist" id="baptized2" value="no" <?php if (set_value('if_adventist') == 'no') echo 'checked'; ?> />No</span>

			                                    </div>

		                                     <span class="text-danger show_error"><?php echo form_error("if_adventist");?></span>

		                                 </div>

		                                 <div class="year_baptized col-md-6" id="baptism_wrapper" style="display:none;" >

		                                    <label class="title">Year of Baptism<span style="color:red;">*</span></label>

		                                    <input type="text" min="1990" max="2099" step="1"  maxlength="4" size="4" id="year_baptism" name="year_baptism" placeholder="yyyy" class="form-control is_number" value="<?php echo set_value('year_baptism') ?>">

		                                     <span class="text-danger show_error"><?php echo form_error("year_baptism");?></span>

		                                 </div>

		                              </div>

							

							<div class="clearfix"></div>

							<div class="col-md-3 col-sm-6">

								<div class="form-group">

									<h4 style="text-decoration:underline;">Guardian Information</h4>

								</div>

							</div>

							<div class="clearfix"></div>

							<div class="col-md-3 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('guardian_name'); ?></label>

									<input id="guardian_name" name="guardian_name" placeholder="Enter Guardian Name" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_name'); ?>" required  onkeyup="this.value = this.value.toUpperCase();" />

									<span class="text-danger"><?php echo form_error('guardian_name'); ?></span>

								</div>

							</div>

							<div class="col-md-3 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1"><?php echo $this->lang->line('guardian_midname'); ?></label>

									<input id="guardian_midname" name="guardian_midname" placeholder="Enter Guardian Middle Name" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_midname'); ?>"  onkeyup="this.value = this.value.toUpperCase();"  />

									<span class="text-danger"><?php echo form_error('guardian_midname'); ?></span>

								</div>

							</div>

							<div class="col-md-3 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('guardian_lastname'); ?></label>

									<input id="guardian_lastname" name="guardian_lastname" placeholder="Enter Guardian Lastname" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_lastname'); ?>" required   onkeyup="this.value = this.value.toUpperCase();"  />

									<span class="text-danger"><?php echo form_error('guardian_lastname'); ?></span>

								</div>

							</div>

							<div class="col-md-3 col-sm-6">

								 <div class="form-group">

									<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('guardian_phone'); ?></label>

									<input id="guardian_phone" name="guardian_phone" placeholder="Enter Guardian Phone" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_phone'); ?>" required  />

									<span class="text-danger"><?php echo form_error('guardian_phone'); ?></span>

								</div>

							</div>

							

							<div class="col-md-3 col-sm-6"> 

								<div class="form-group">

									<label for="exampleInputEmail1" class="required">Street</label>

									<input id="guardian_street" name="guardian_street" placeholder="Enter Street" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_street'); ?>" required  onkeyup="this.value = this.value.toUpperCase();"  />

									<span class="text-danger"><?php echo form_error('guardian_street'); ?></span>

								</div>

							</div>

							<div class="col-md-3 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1" class="required">Barangay </label>

									<input id="guardian_barangay" name="guardian_barangay" placeholder="Enter Barangay" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_barangay'); ?>" required   onkeyup="this.value = this.value.toUpperCase();" />

									<span class="text-danger"><?php echo form_error('guardian_barangay'); ?></span>

								</div>

							</div>

							<div class="col-md-3 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1" class="required">City/Municipality </label>

									<input id="guardian_city_municipality" name="guardian_city_municipality" placeholder="Enter City/Municipality" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_city_municipality'); ?>" required  onkeyup="this.value = this.value.toUpperCase();"  />

									<span class="text-danger"><?php echo form_error('guardian_city_municipality'); ?></span>

								</div>

							</div>

							

							<div class="col-md-3 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1" class="required">Province</label>

									<input id="guardian_province" name="guardian_province" placeholder="Enter Province" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_province'); ?>" required  onkeyup="this.value = this.value.toUpperCase();"   />

									<span class="text-danger"><?php echo form_error('guardian_province'); ?></span>

								</div>

							</div>

							<div class="col-md-3 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1" class="required">Guardian Relation</label>

									<input id="guardian_relation" name="guardian_relation" placeholder="Enter Guardian Relation" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_relation'); ?>"  required  onkeyup="this.value = this.value.toUpperCase();"  />

									<span class="text-danger"><?php echo form_error('guardian_relation'); ?></span>

								</div>

							</div>

						

							<div class="col-md-12" style="margin-top:10px;">

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

						<div class="col-md-4 col-sm-6">

						<div class="form-group">

							<label for="exampleInputEmail1" class="required"><?php echo 'Grade';//$this->lang->line('class'); ?></label>

							<select  id="class_id_old" name="class_id" class="form-control input-lg" required >

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

						<div class="col-md-4 col-sm-6">

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

						<div class="col-md-4 col-sm-6" id="strand_old" style="display:none">

							<div class="form-group">

								<label class="required" for="exampleInputEmail1"><?php echo "Strand"; ?></label>

								<select class="form-control"  placeholder="Select Strand" name="strand_id" id="strand_id_old" style="height:50px;">

								</select> 

								<span class="text-danger"><?php echo form_error('strand_id'); ?></span>

							</div>

						</div>

						<div class="clearfix"></div>

						<div class="col-md-3 col-sm-6">

							<div class="form-group">

								<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('first_name'); ?></label>

								<input id="firstname_old" name="firstname" placeholder="Enter First Name" type="text" class="form-control input-lg"  value="<?php echo set_value('firstname'); ?>" required  onkeyup="this.value = this.value.toUpperCase();"  />

								<span class="text-danger"><?php echo form_error('firstname'); ?></span>

							</div>

						</div>

						<div class="col-md-3 col-sm-6">

							<div class="form-group">

								<label for="exampleInputEmail1"><?php echo $this->lang->line('middle_name'); ?></label>

								<input id="middlename_old" name="middlename" placeholder="Enter Middle Name" type="text" class="form-control input-lg"  value="<?php echo set_value('middlename'); ?>"  onkeyup="this.value = this.value.toUpperCase();"  />

								<span class="text-danger"><?php echo form_error('middlename'); ?></span>

							</div>

						</div>

						<div class="col-md-3 col-sm-6">

							<div class="form-group">

								<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('last_name'); ?></label>

								<input id="lastname_old" name="lastname" placeholder="Enter Last Name" type="text" class="form-control input-lg"  value="<?php echo set_value('lastname'); ?>" required  onkeyup="this.value = this.value.toUpperCase();" />

								<span class="text-danger"><?php echo form_error('lastname'); ?></span>

							</div>

						</div>

						 <div class="col-md-3 col-sm-6">

							<div class="form-group">

								<label for="exampleInputEmail1">Suffix</label>

								<input id="suffix_old" name="suffix" placeholder="Enter Suffix" type="text" class="form-control input-lg"  value="<?php echo set_value('suffix'); ?>"  onkeyup="this.value = this.value.toUpperCase();"  />

								<span class="text-danger"><?php echo form_error('suffix'); ?></span>

							</div>

						</div>

						<div class="col-md-3 col-sm-6">

							 <div class="form-group">

								<label for="exampleInputEmail1">Phone Number</label>

								<input id="mobileno" name="mobileno" placeholder="Enter Student Phone" type="text" class="form-control input-lg"  value="<?php echo set_value('mobileno'); ?>" />

								<span class="text-danger"><?php echo form_error('mobileno'); ?></span>

							</div>

						</div>

						<div class="col-md-3">

							<label for="exampleInputEmail1" class="col-sm-12 title">Religion<span style="color:red;">*</span></label>

							<select  id="religion_old" name="religion" class="form-control" required  style="height:50px;" >

								<option value=""><?php echo $this->lang->line('select'); ?> Religion</option>

								<?php

								foreach ($religionList as $key ) {

									?>

									<option value="<?php echo $key ?>"<?php if (set_value('religion') == $key) echo "selected=selected" ?>><?php echo $key ?></option>

									<?php

								}

								?>

								<option value="other">Other</option>

							</select>

							<span class="text-danger"><?php echo form_error('religion'); ?></span>

						</div>

						<div class="col-md-3" id="display_religion_old" style="display:none;">

							<label for="exampleInputEmail1" class="col-sm-12 title">Enter Religion<span style="color:red;">*</span></label>

							<input type="text" id="other_religion_old" name="other_religion" placeholder="Enter Other Religion" class="form-control input-lg" />

							<span class="text-danger"><?php echo form_error('other_religion'); ?></span>

						</div>

						<div class="col-md-3 col-sm-3">

							<div class="form-group">

							<label for="exampleInputEmail1">Modality<span style="color:red;">*</span></label>

							<select  id="modality" name="modality" class="form-control input-lg" required >

								<option value=""><?php echo $this->lang->line('select'); ?> Modality</option>

								<?php

								foreach ($modalityList as $key => $value ) {

									?>

									<option value="<?php echo $key ?>"<?php if (set_value('modality') == $key) echo "selected=selected" ?>><?php echo $value ?></option>

									<?php

								}

								?>

							</select>

							<span class="text-danger"><?php echo form_error('modality'); ?></span>

							</div>

						</div>

						<div class="col-md-3" id="paymenttype_old" style="display:none;">

							<label for="exampleInputEmail1" class="title">Select Enrollment/Fees Option<span style="color:red;">*</span></label>

							<select class="form-control"  placeholder="Select Type" name="payment_type" id="payment_type_old" style="height:50px;">

							</select> 

							<span class="text-danger"><?php echo form_error('payment_type'); ?></span>

						</div>

						<div class="clearfix"></div>

						<div class="form-group">

                                 <div class="col-md-6" id="subsidized_wrapper" name="subsidized">

                                    <label class="title">Is your parent working in an Adventist institution?<span style="color:red;">*</span></label>&nbsp;&nbsp;<span><input class="form-check-inline mr-0 subsidized_radio" type="radio" name="subsidized" id="subsidized1" value="yes"

                                          <?php if (set_value('subsidized') == 'yes') echo 'checked'; ?> required />Yes</span>

                                       <span><input class="form-check-inline mr-0  mt-3 ml-4 subsidized_radio" type="radio" name="subsidized" id="subsidized2" value="no"

                                          <?php if (set_value('subsidized') == 'no') echo 'checked'; ?>

                                          />No</span>

                                 </div>

                                 <div class="col-md-6" id="mission_wrapper" style="display: none;">

                                    <label class="title">Name of Institution?<span style="color:red;">*</span></label>

                                    <input id="institution" name="institution" placeholder="Name of Institution" value="<?php echo set_value('institution') ?>" type="text" class="form-control input-lg"  onkeyup="this.value = this.value.toUpperCase();">

                                     <span class="text-danger show_error"><?php echo form_error("institution");?></span>

                                 </div>

                              </div>

                              <div class="clearfix"></div>

                                  <div class="form-group">

		                                 <div class="col-md-6" id="if_adventist">

		                                      <label class="title">If Adventist, baptized?<span style="color:red;">*</span></label>

			                                    <div class="form-check" id="baptized_rad">

			                                       <span> <input class="form-check-inline mr-0 mt-3 baptized_radio" type="radio" name="if_adventist" id="baptized3" value="yes"  <?php if (set_value('if_adventist') == 'yes') echo 'checked'; ?> required />Yes

			                                       </span>

			                                       <span>

			                                       <input class="form-check-inline mr-0  mt-3 ml-4 baptized_radio" type="radio"  name="if_adventist" id="baptized4" value="no" <?php if (set_value('if_adventist') == 'no') echo 'checked'; ?> />No</span>

			                                    </div>

		                                     <span class="text-danger show_error"><?php echo form_error("if_adventist");?></span>

		                                 </div>

		                                 <div class="year_baptized col-md-6" id="baptism_wrapper" style="display:none;" >

		                                    <label class="title">Year of Baptism<span style="color:red;">*</span></label>

		                                    <input type="text" min="1990" max="2099" step="1"  maxlength="4" size="4" id="year_baptism" name="year_baptism" placeholder="yyyy" class="form-control is_number" value="<?php echo set_value('year_baptism') ?>">

		                                     <span class="text-danger show_error"><?php echo form_error("year_baptism");?></span>

		                                 </div>

		                              </div>

						<div class="clearfix"></div>

						<br/>

						<div class="col-md-4 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('guardian_name'); ?></label>

									<input id="guardian_name_old" name="guardian_name" placeholder="Enter Guardian Name" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_name'); ?>" required  onkeyup="this.value = this.value.toUpperCase();"  />

									<span class="text-danger"><?php echo form_error('guardian_name'); ?></span>

								</div>

							</div>

							<div class="col-md-4 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1"><?php echo $this->lang->line('guardian_midname'); ?></label>

									<input id="guardian_midname_old" name="guardian_midname" placeholder="Enter Guardian Middle Name" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_midname'); ?>"  onkeyup="this.value = this.value.toUpperCase();"  />

									<span class="text-danger"><?php echo form_error('guardian_midname'); ?></span>

								</div>

							</div>

							<div class="col-md-4 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('guardian_lastname'); ?></label>

									<input id="guardian_lastname_old" name="guardian_lastname" placeholder="Enter Guardian Lastname" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_lastname'); ?>" required  onkeyup="this.value = this.value.toUpperCase();"  />

									<span class="text-danger"><?php echo form_error('guardian_lastname'); ?></span>

								</div>

							</div>

							<div class="col-md-4 col-sm-6">

								<div class="form-group">

									<label for="exampleInputEmail1" class="required">Guardian Relation</label>

									<input id="guardian_relation_old" name="guardian_relation" placeholder="Enter Guardian Relation" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_relation'); ?>"  required  onkeyup="this.value = this.value.toUpperCase();" />

									<span class="text-danger"><?php echo form_error('guardian_relation'); ?></span>

								</div>

							</div>

							<div class="col-md-4 col-sm-6">

								 <div class="form-group">

									<label for="exampleInputEmail1" class="required"><?php echo $this->lang->line('guardian_phone'); ?></label>

									<input id="guardian_phone_old" name="guardian_phone" placeholder="Enter Guardian Phone" type="text" class="form-control input-lg"  value="<?php echo set_value('guardian_phone'); ?>" required />

									<span class="text-danger"><?php echo form_error('guardian_phone'); ?></span>

								</div>

							</div>

						

						

							<div class="col-md-12" style="margin-top:10px;">

							<button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>

							<button type="submit" class="btn btn-primary pull-right"><?php echo $this->lang->line('save'); ?></button>

						</div>

						</form>

					</div>

				</div>

			</div>

	</div>

</div>

<div class="modal" tabindex="-1" role="dialog" id="studinfo">

  <div class="modal-dialog modal-lg" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title">Student Information (<span id="display_student_status"></span>)</h5>

      </div>

      <div class="modal-body"> 

        <form action="<?php echo site_url('registrar/reserved/update_modal') ?>" method="post" >

        <input type="hidden" id="display_rid" name="display_rid" />

		<div class="row" style="margin:5px;"> 

			<div class="col-md-3">

				<label class="title">Student Type</label>

				<input type="text" id="display_type" name="display_type" class="form-username form-control" disabled="disabled"  /> 

			</div>

		</div>

        <div class="row" style="margin:5px;"> 

			<div class="col-md-3">

				<label class="title">School Year</label>

				<input type="text" id="display_schoolyear" name="display_schoolyear" class="form-username form-control" disabled="disabled"  /> 

			</div>

			  

			<div class="col-md-3">

				<label class="title">Admission No</label>

				<input type="text" id="display_ad" name="display_ad" class="form-username form-control" disabled="disabled"  /> 

			</div>  

            <div class="col-md-3">

                <label class="title">LRN</label>

                <input type="text" id="display_lrn" name="display_lrn" class="form-username form-control"   /> 

            </div> 

			

        </div>

        <div class="row" style="margin:5px;"> 


	        <div class="col-md-3">

				<label class="title">First Name</label>

				<input type="text" id="display_first" name="display_first" class="form-username form-control" /> 

			</div>

			<div class="col-md-3">

				<label class="title">Middle Name</label>

				<input type="text" id="display_middlename" name="display_middlename" class="form-username form-control"   /> 

			</div>

			<div class="col-md-3">

				<label class="title">Last Name</label>

				<input type="text" id="display_lastname" name="display_lastname" class="form-username form-control"  />  

			</div> 

			<div class="col-md-3">

				<label class="title">Suffix</label>

				<input type="text" id="display_suffix" name="display_suffix" class="form-username form-control"  /> 

			</div> 

		</div>  

        <div class="row"style="margin:5px;"> 

			<div class="col-md-3">

				<label class="title">Gender</label>

				<input type="text" id="display_gender" name="display_gender" class="form-username form-control"  disabled="disabled" /> 

			</div>  

			<div class="col-md-3">

				<label class="title">Date of Birth</label>

				<input type="text" id="display_dob" name="display_dob" class="form-username form-control"  disabled="disabled"  /> 

			</div>  

            <div class="col-md-3">

                <label class="title">Religion</label>

                <input type="text" id="display_religion" name="display_religion" class="form-username form-control"   disabled="disabled" /> 

            </div> 

			<div class="col-md-6">

				<label class="title">If Baptized?</label>

				<input type="text" id="display_baptized" name="display_baptized" class="form-username form-control" disabled="disabled"  /> 

			</div> 

			<div class="col-md-6">

				<label class="title">Year Baptized</label>

				<input type="text" id="display_year_baptized" name="display_year_baptized" class="form-username form-control" disabled="disabled"  /> 

			</div>

		</div>  

          <div class="row"style="margin:5px;"> 

             <div class="col-md-3">

                <label class="title">Incoming Grade</label>

                <input type="text" id="display_grade" name="display_grade" class="form-username form-control" disabled="disabled"  /> 

            </div>  

			<div class="col-md-3" id="displaystrand" style="display:none;">

				<label class="title">Strand</label>

				<input type="text" id="display_strand" name="display_strand" class="form-username form-control"    disabled="disabled"/> 

			</div>

             <div class="col-md-3">

                <label class="title">Modular</label>

                <input type="text" id="display_modular" name="display_modular" class="form-username form-control" disabled="disabled"  /> 

            </div>

            <div class="col-md-3">

                <label class="title">Email Address</label>

                <input type="text" id="display_email" name="display_email" class="form-username form-control" disabled="disabled"  /> 

            </div>

        </div>  

        <div class="row"style="margin:5px;" id="displayfortransferee" style="display:none;"> 

             <div class="col-md-6">

                <label class="title">Last School Attended</label>

                <input type="text" id="display_school_attended" name="display_school_attended" class="form-username form-control" disabled="disabled"  /> 

            </div> 

            <div class="col-md-6" >

                <label class="title">School Address</label>

                <input type="text" id="display_school_address" name="display_school_address" class="form-username form-control" disabled="disabled"  /> 

            </div>

        </div>  

        <div class="row" style="margin:5px;"> 

			<div class="col-md-12">

				<h4 style="text-decoration:underline;">Guardian Information</h4>

			</div>

		</div>

        <div class="row" style="margin:5px;"> 

        	<div class="col-md-3">

				<label class="title">Name</label>

				<input type="text" id="display_guardianfirst" name="display_guardianfirst" class="form-username form-control" disabled="disabled" /> 

			</div>

			<div class="col-md-3">

				<label class="title">Middle Name</label>

				<input type="text" id="display_guardianmiddlename" name="display_guardianmiddlename" class="form-username form-control" disabled="disabled"  /> 

			</div>

			<div class="col-md-3">

				<label class="title">Last Name</label>

				<input type="text" id="display_guardianlastname" name="display_guardianlastname" class="form-username form-control" disabled="disabled"  />  

			</div> 

			<div class="col-md-3">

				<label class="title">Phone Number</label>

				<input type="text" id="display_guardiannumber" name="display_guardiannumber" class="form-username form-control" /> 

			</div> 

		</div> 

        <div class="row" style="margin:5px;"> 

			<div class="col-md-3">

				<label class="title">Relation</label>

				<input type="text" id="display_relation" name="display_relation" class="form-username form-control" disabled="disabled"  /> 

			</div> 

            <div class="col-md-9" id="display_address_area" style="display: none;">

                <label class="title">Guardian Address</label>

                <input type="text" id="display_address" name="display_address" class="form-username form-control" disabled="disabled"  /> 

            </div> 

		</div>  

		 <div class="row" style="margin:5px;"> 

            <div class="col-md-6">

                <label class="title">Is your parent working in an Adventist institution?</label>

                <input type="text" id="display_subsidized" name="display_subsidized" class="form-username form-control" disabled="disabled"  /> 

            </div> 

            <div class="col-md-6" id="display_institution_area" style="display: none;">

                <label class="title">Name of Institution</label>

                <input type="text" id="display_institution" name="display_institution" class="form-username form-control" disabled="disabled"  /> 

            </div> 

        </div>  

		<hr>

        <div class="row" style="margin:5px;"> 

        	<div class="col-md-12">

				<label class="title">Document 1:</label> <a id="display_document_1_a" href="" target="_blank"><span id="display_document_1"></span></a>

			</div>

        	<div class="col-md-12"> 

				<label class="title">Document 2:</label> <a id="display_document_2_a" href="" target="_blank"><span id="display_document_2"></span></a>

			</div>

        	<div class="col-md-12"> 

				<label class="title">Document 3:</label> <a id="display_document_3_a" href="" target="_blank"><span id="display_document_3"></span></a>

			</div> 	

            <div class="col-md-12"> 

                <label class="title">Document 4:</label> <a id="display_document_4_a" href="" target="_blank"><span id="display_document_4"></span></a>

            </div> 

			<p  class="col-md-12"><em>To view the files uploaded please click the File name link above!</em></p>

		</div> 

      </div>

      <div class="modal-footer">

	    <button type="button" class="btn btn-success float-left" id="generate_enrollment">Generate Enrollment Form</button>
	
        <button type="submit" class="btn btn-success" >Update Information</button>

        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

      </div>

      </form>

    </div>



  </div>

</div>

<script>

$(document).on('click', '.click_modal', function (e) { 

		var strand_id =null;

		$('#display_strand').val('');

		$('#displaystrand').hide();

		var get_id = $(this).attr("id");

		var base_url = '<?php echo base_url() ?>'; 

	    var form_fields = { 'id':get_id};  

	    var url =  base_url + "registrar/reserved/getinfo";

	    data = form_fields

	    callback = function( json ){
			console.log(json); // Debugging

	        if ( json == null ) {

	            alert('Student can not be found!');

	        } else { 

	        	var link_url = base_url+"uploads/student_documents/";

	        	var folder_name = "r"+json.id+"/"; 

	            $('#display_rid').val(json.id);

                $('#display_first').val(json.firstname);

				$('#display_middlename').val(json.middlename);

				$('#display_lastname').val(json.lastname);

				$('#display_suffix').val(json.suffix);

				$('#display_gender').val(json.gender);

	            $('#display_guardianfirst').val(json.guardian_name);

				$('#display_guardianmiddlename').val(json.guardian_midname);

				$('#display_guardianlastname').val(json.guardian_lastname);

				$('#display_relation').val(json.guardian_relation);

				$('#display_guardiannumber').val(json.guardian_phone);

				$('#display_dob').val(json.dob);

				$('#display_schoolyear').val(json.session);

				$('#display_grade').val(json.class); 

				$('#display_ad').val(json.admission_no); 

				$('#display_lrn').val(json.lrn); 

				$('#display_subsidized').val(json.subsidized); 

				$('#display_year_baptized').val(json.year_baptism); 



				var admission_no = json.student_admission_no;

				var lrn = json.lrn;

				var strand_id = json.strand_id;

                var type = json.type;

                var modality = json.get_modality;

                var email_address = json.email_address;

                var subsidized = json.subsidized;

                  var if_adventist = json.if_adventist;

                var year_baptism = json.year_baptism;

                if( subsidized == 'yes'){

                     $('#display_institution').val(json.institution); 

                     $('#display_institution_area').show(); 

                } else {

                    $('#display_institution_area').hide(); 

                }



                 if( if_adventist == 'yes'){

                     $('#display_baptized').val(json.if_adventist); 

                     $('#display_year_baptized_area').show(); 

                } else {

                    $('#display_institution_area').hide(); 

                }





                $('#display_type').val(type.toUpperCase());

               

                

                if( lrn == null ){  

                     $('#display_lrn').val("N/A");

                } else {

                     $('#display_lrn').val(lrn);

                }

                if( admission_no == null  ){  

                     $('#display_ad').val("N/A");

                } else {

                    $('#display_ad').val(admission_no);

                }



                $('#display_modular').val(modality); 

                 if( modality == 'Modular A'  ){  

                     $('#display_email').val(email_address);

                } else {

                    $('#display_email').val("N/A");

                }





                 if(json.type == 'old'){

                    $('#display_first').attr('readonly', true);

                    $('#display_lastname').attr('readonly', true);

                    $('#display_middlename').attr('readonly', true);

                    $('#display_suffix').attr('readonly', true);

                } else {

                    $('#display_first').attr('readonly', false);

                    $('#display_lastname').attr('readonly', false);

                    $('#display_middlename').attr('readonly', false);

                    $('#display_suffix').attr('readonly', false);

                }

				if( admission_no == null && lrn  ){  

					$('#display_lrn_ad').val(json.lrn);

				} else if( lrn == null && admission_no ){ 

					$('#display_lrn_ad').val(json.admission_no);

				}  else if( lrn && admission_no ){ 

					$('#display_lrn_ad').val(json.lrn);

				} else {

					$('#display_lrn_ad').val("N/A");

				} 

				

				if( strand_id ){

					var form_fields = { 'id':strand_id};  

					var url1 =  base_url + "registrar/reserved/getStrand";

					data1 = form_fields

					callback = function( json ){

						var strand_code = json.code;

						$('#display_strand').val(strand_code);

						$('#displaystrand').show();

					};

					jQuery.post( url1, data1, callback, "json" );  

					

				} else {

					$('#display_strand').val('');

					$('#displaystrand').hide();

				}


				//for displaying school name and address only for transferee and returnee
                if (type === 'transferee' || type === 'returnee') {
					$('#display_school_attended').val(json.school_name); 
					$('#display_school_address').val(json.school_address); 
					$('#displayfortransferee').show();
				} else {
					$('#displayfortransferee').hide();  
				}

                if( json.type != 'old' ){  

                    $('#display_address_area').show();

                } else {

                    $('#display_address_area').hide();

                }

                var guardian_address = json.guardian_address;

                var guardian_address1 = json.guardian_address2;

                 $('#display_address').val(guardian_address+' '+guardian_address1); 



                $('#display_religion').val(json.religion); 

				$('#display_student_status').text(json.enrollment_status);

				$('#display_document_1').text(json.doc1_title);

				$('#display_document_2').text(json.doc2_title);

				$('#display_document_3').text(json.doc3_title);

                $('#display_document_4').text(json.doc4_title);



				$("#display_document_1_a").attr("href", link_url+folder_name+json.doc1_upload);

				$("#display_document_2_a").attr("href", link_url+folder_name+json.doc2_upload);

				$("#display_document_3_a").attr("href", link_url+folder_name+json.doc3_upload);

                $("#display_document_4_a").attr("href", link_url+folder_name+json.doc4_upload);

				$('#studinfo').modal('show'); 

	        }



	    };

	    jQuery.post( url, data, callback, "json" );  



	});

</script>

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

        options.data = { "search_student": search_student, 'include_inactive': 'yes' };

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

	

	document.getElementById("student_name").onkeyup = function (e) {

        var search_student = $(this).val();

        var options = {};

         options.url = "<?php echo base_url('registrar/student/getsearchstudentname'); ?>";

        options.type = "POST";

        options.data = { "search_student": search_student, 'include_inactive': 'yes' };

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

    } 





    $("#student_name").bind('input', function () {

        var x = checkExists( $('#student_name').val() );

        $('#student_id').val(x);

		if(x){

			check_next();

			 var options = {};

			options.url = "<?php echo base_url('registrar/student/getLatestStudentInformation'); ?>";

			options.type = "GET";

			options.data = { "student_id": x };

			options.dataType = "json";

			options.success = function (data) {

				$('#firstname_old').val(data.firstname);

				$('#middlename_old').val(data.middlename);

				$('#lastname_old').val(data.lastname);

				$('#suffix_old').val(data.suffix);

				$('#phone').val(data.suffix);

				$('#religion_old').val(data.religion);

				$('#guardian_name_old').val(data.guardian_name);

				$('#guardian_midname_old').val(data.guardian_midname);

				$('#guardian_lastname_old').val(data.guardian_lastname);

				$('#mobileno').val(data.mobileno);

				$('#guardian_phone_old').val(data.guardian_phone);

				$('#guardian_relation_old').val(data.guardian_relation);

			};

			$.ajax(options);

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

					$("#foroldstudent #class_id_old").val(data.next_class);	

				} else {

					$("#recent em").text();

				}

				display_strand();

			};

			$.ajax(options);

	}



});

</script>

<script type="text/javascript">

	function foroldstudent(){

		$('#foroldstudent').css('display','block');

		$('#fornewstudent').css('display','none');

		$('#strand_id').attr('required', false );

		$('#strand_id_old').attr('required', true );

	}

	function fornewstudent(){

		$('#fornewstudent').css('display','block');

		$('#foroldstudent').css('display','none');

		$('#strand_id').attr('required', true );

		('#strand_id_old').attr('required', false );

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

	function display_strand(){

		 var base_url = '<?php echo base_url() ?>';

		 var student_type =  $("input[name='type']:checked").val();

		 if( student_type == 'Old'){

			 var class_id = $('#class_id_old').val();

			 var strand_id = $('#strand_id_old').val();

			 } else {

			 var class_id = $('#class_id').val();

			 var strand_id = $('#strand_id').val();

		 }

		 

		 var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';

		$.ajax({

			type: "GET",

			url: base_url + "registrar/strand/allowStrand",

			data: {'class_id': class_id},

			dataType: "json",

			success: function (data) {

				if( data == true ){

					 if( student_type == 'Old'){

						$('#strand_id_old').find('option:not(:first)').remove();

						$('#strand_old').css('display','block');

						$('#strand_id_old').attr('required', true );

					} else {

						$('#strand_id').find('option:not(:first)').remove();

						$('#strand').css('display','block');

						$('#strand_id').attr('required', true );

					}

					

					 $.ajax({

							type: "GET",

							url: base_url + "registrar/strand/getStrandByClass",

							data: {'class_id': class_id},

							dataType: "json",

							success: function (data) {

								$.each(data, function (i, obj)

								{

									var sel = "";

									if (strand_id == obj.id) {

										sel = "selected";

									}

									div_data += "<option value=" + obj.id + " " + sel + ">" + obj.name + "</option>";

								});

								if( student_type == 'Old'){

									$('#strand_id_old').append(div_data);

								}else {

									$('#strand_id').append(div_data);	

								}

							}

						});

					

				} else {

					 if( student_type == 'Old'){

						$('#strand_old').css('display','none');

						$('#strand_id_old').val("");

						$('#strand_id_old').attr('required', false );

					} else {

						$('#strand').css('display','none');

						$('#strand_id').val("");

						$('#strand_id').attr('required', false );

					}

				}

			}

		});

	}

	

	$(document).on('change', '#class_id', function (e) {

		display_strand();

		display_type();

		display_type_old();

	});

	$(document).on('change', '#class_id_old', function (e) {

		display_strand();

		display_type();

		display_type_old();

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



	 $(document).on('change', 'input:radio[name=subsidized]', function() {

            //$('div[id^="mission_wrapper"]').toggle();

             var subsidized = $(this).val();

             $('#' + $(this).attr('id') + '_text').show(100);

            if(subsidized == 'yes'){

               $('div[id^="mission_wrapper"]').show(100);

               $('#mission').attr('required',true);

             } else {

               $('div[id^="mission_wrapper"]').hide();

                 $('#mission').attr('required',false);

             }

         });



		$(document).on('change', '#religion', function (e) {

			var religion = $('#religion').val();

			if(religion == 'other'){

				$('#display_religion').show(100);

				$('#other_religion').attr('required',true);

			} else {

				$('#display_religion').hide(100);

				$('#other_religion').attr('required',false);

			}



		});



		$(document).on('change', '#religion_old', function (e) {

			var religion = $('#religion_old').val();

			if(religion == 'other'){

				$('#display_religion_old').show(100);

				$('#other_religion_old').attr('required',true);

			} else {

				$('#display_religion_old').hide(100);

				$('#other_religion_old').attr('required',false);

			}

		});







	  $(document).on('change', '#religion', function() {

         var religion = $(this).val();

         if(religion == 'SDA'){

        	 document.getElementById('baptized1').checked = true;

        	 document.getElementById('baptized2').checked = false;

        	 $('.year_baptized').show(100);

         } else {

        	 document.getElementById('baptized1').checked = false;

       	  document.getElementById('baptized2').checked = true;

         $('.year_baptized').hide();

         }

         });



	  	  $(document).on('change', '#religion_old', function() {

         var religion = $(this).val();

         if(religion == 'SDA'){

        	 document.getElementById('baptized3').checked = true;

        	 document.getElementById('baptized4').checked = false;

        	 $('.year_baptized').show(100);

         } else {

        	 document.getElementById('baptized3').checked = false;

       	  document.getElementById('baptized4').checked = true;

         $('.year_baptized').hide();

         }

         });



         $(document).on('change', 'input:radio[name=if_adventist]', function() {

         var if_adventist = $(this).val();

         if(if_adventist == 'yes'){

               $('div[class^="year_baptized"]').show(100);

            } else {

               $('div[class^="year_baptized"]').hide();

            }

               // $('#' + $(this).attr('id') + '_text').show(100);

         });

         $(document).on('change', 'input:radio[name=baptized]', function() {

            //$('div[id^="baptism_wrapper"]').toggle();

             $('#' + $(this).attr('id') + '_text').show(100);

             var baptized = $(this).val();

             if(baptized == 'yes'){

               $('div[id^="baptism_wrapper"]').show(100);

             } else {

               $('div[id^="baptism_wrapper"]').hide();

             }

         });

		 $(document).on('click', '#generate_enrollment', function(e) {
			e.preventDefault();
			var student_id = $('#display_rid').val();
			var base_url = '<?php echo base_url() ?>';
			
			// Open in new tab
			window.open(base_url + 'registrar/reserved/generate_enrollment_form/' + student_id, '_blank');
		});

	



	function display_type(){

		 var base_url = '<?php echo base_url() ?>';

		 var class_id = $('#class_id').val();

		 var div_data = '<option value=""><?php echo $this->lang->line('select'); ?> Type</option>';

		$.ajax({

			type: "GET",

			url: base_url + "registration/get_payment_dropdown",

			data: {'class_id': class_id},

			dataType: "json",

			success: function (data) {

				if( data  ){

					$('#payment_type').find('option').remove();

					$('#paymenttype').css('display','block');

					$.each(data, function (i, obj)

					{

						if( obj.key ){

							div_data += "<option value=" + obj.key + ">" + obj.value +" ( "+obj.amount+" )"+"</option>";

						}

					});

					$('#payment_type').append(div_data);

					$('#payment_type').attr('required', true );

				} else {

					$('#paymenttype').css('display','none');

					$('#payment_type').val("");

					$('#payment_type').attr('required', false );

				}

			}

		});

	}



	function display_type_old(){

		 var base_url = '<?php echo base_url() ?>';

		 var class_id = $('#class_id_old').val();

		 var div_data = '<option value=""><?php echo $this->lang->line('select'); ?> Type</option>';

		$.ajax({

			type: "GET",

			url: base_url + "registration/get_payment_dropdown",

			data: {'class_id': class_id},

			dataType: "json",

			success: function (data) {

				if( data  ){

					$('#payment_type_old').find('option').remove();

					$('#paymenttype_old').css('display','block');

					$.each(data, function (i, obj)

					{

						if( obj.key ){

							div_data += "<option value=" + obj.key + ">" + obj.value +" ( "+obj.amount+" )"+"</option>";

						}

					});

					$('#payment_type_old').append(div_data);

					$('#payment_type_old').attr('required', true );

				} else {

					$('#paymenttype_old').css('display','none');

					$('#payment_type_old').val("");

					$('#payment_type_old').attr('required', false );

				}

			}

		});

	}

	



</script>

      