<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
// printr($student);
?>
<div class="content-wrapper" style="min-height: 946px;">
 
    <section class="content-header">
        <h1>
            <i class="fa fa-user-plus"></i> <?php echo $this->lang->line('student_information'); ?> <small><?php echo $this->lang->line('student1'); ?></small></h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-3">           
                <div class="box box-primary">
                    <div class="box-body box-profile">
						<?php
							$image = base_url().$student['image'];
							if(!@GetImageSize($image)){
								$image = base_url().'uploads/student_images/no_image.png';
							} 
						?>
                        <img class="profile-user-img img-responsive img-circle" src="<?php echo $image; ?>" alt="User profile picture">
                        <h3 class="profile-username text-center"><?php echo $student['firstname'] . " " . $student['lastname']; ?></h3>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b><?php echo $this->lang->line('admission_no'); ?></b> <a class="pull-right text-aqua"><?php echo $student['admission_no']; ?></a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo $this->lang->line('lrn'); ?></b> <a class="pull-right text-aqua"><?php echo $student['lrn']; ?></a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo 'Grade';//$this->lang->line('class'); ?></b> <a class="pull-right text-aqua"><?php echo $student['class']; ?></a>
                            </li>
							<?php 
							if( $student['strand_id'] != 0 && $student['strand_id'] ){
								$strand_id = $student['strand_id'];
								$check_firstsem_strand = $this->studentstrand_model->get_latest_strand( $student_id, 1, $session_id );
								$check_secondsem_strand = $this->studentstrand_model->get_latest_strand( $student_id, 2, $session_id );
								if( $check_firstsem_strand || $check_secondsem_strand  ){
									if( $check_firstsem_strand   ){
										$get_strand_first_sem = $check_firstsem_strand['strand_id'];
									} else {
										$get_strand_first_sem = $strand_id; 
									}
									$strand_name = $this->strand_model->get( $get_strand_first_sem);
									?>
									<li class="list-group-item">
										<b>First Sem Strand</b><br/><a class="pull-right text-aqua"><?php echo $strand_name['name'].' '.$strand_name['class']; ?></a>
										<br/>
										<br/>
									</li>
									
									<?php
									if( $check_secondsem_strand   ){
										$get_strand_second_sem = $check_secondsem_strand['strand_id'];
									} else {
										$get_strand_second_sem = $strand_id; 
									}
									$strand_name = $this->strand_model->get( $get_strand_second_sem );	
									?>
									 <li class="list-group-item">
										<b>Second Sem Strand</b><br/><a class="pull-right text-aqua"><?php echo $strand_name['name'].' '.$strand_name['class']; ?></a><br/><br/>
									</li>
									<?php
								} else {
									$strand_name = $this->strand_model->get( $strand_id );	
									?>
									 <li class="list-group-item">
										<b>Strand</b><br/> <a class="pull-right text-aqua"><?php echo $strand_name['name'].' '.$strand_name['class']; ?></a>
										<br/>
										<br/>
									</li>
									<?php	
								}
								
							}?>
                            <li class="list-group-item">
                                <b><?php echo $this->lang->line('section'); ?></b> <a class="pull-right text-aqua"><?php echo $student['section']; ?></a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo 'Grantee';//$this->lang->line('rte'); ?></b> <a class="pull-right text-aqua">
                                <?php 
                                    if( $student['rte'] == 'Yes'){
                                        echo 'ESC';
                                    } elseif( $student['government_voucher'] == 'Yes'){
                                        echo 'Government Voucher';  
                                    } else {
                                        echo 'No';
                                    }
                                    
                                ?>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo 'Grantee #';//$this->lang->line('rte'); ?></b> <a class="pull-right text-aqua">
								<?php 
									if( $student['rte'] == 'Yes'){ 
										echo $student['esc_number']; 
									} elseif( $student['government_voucher'] == 'Yes'){ 
                                        echo $student['government_number'];  
									} else {
										echo 'No';
									}
									
								?>
								</a>
                            </li>
                             <li class="list-group-item">
                                 <b><?php echo $this->lang->line('gender'); ?></b> <a class="pull-right text-aqua"><?php echo $this->lang->line(strtolower($student['gender'])); ?></a>
                            </li>
                             <li class="list-group-item">
                                 <b><?php echo 'Blood Type'; ?></b> <a class="pull-right text-aqua"><?php echo $student['blood_type']; ?></a>
                            </li>
                            <li class="list-group-item">
                                 <b><?php echo 'Lunch Pass'; ?></b> <a class="pull-right text-aqua"><?php echo $student['lunch_pass']; ?></a>
                            </li>
                             <li class="list-group-item">
                                 <b><?php echo 'Commuter Pass'; ?></b> <a class="pull-right text-aqua"><?php echo $student['commuter_pass']; ?></a>
                            </li>
                             <li class="list-group-item">
                                 <b><?php echo 'Adventist Parent'; ?></b> <a class="pull-right text-aqua"><?php echo isset($student['subsidized']) ? $student['subsidized'] : ''; ?></a>
                            </li>
                             <li class="list-group-item">
                                  <b><?php echo 'Baptized Adventist'; ?></b> <a class="pull-right text-aqua"><?php echo isset($student['if_adventist']) ? $student['if_adventist'] : ''; ?></a>
                             </li>
                              <li class="list-group-item" id="year_baptism_display" <?php if (!isset($student['if_adventist']) || $student['if_adventist'] != 'yes') echo 'style="display:none;"'; ?>>
                                  <b><?php echo 'Year of Baptism'; ?></b> <a class="pull-right text-aqua"><?php echo isset($student['year_baptism']) ? $student['year_baptism'] : ''; ?></a>
                             </li>
                              <li class="list-group-item" id="institution_display" <?php if (!isset($student['subsidized']) || $student['subsidized'] != 'yes') echo 'style="display:none;"'; ?>>
                                 <b><?php echo 'Institution'; ?></b> <a class="pull-right text-aqua"><?php echo isset($student['institution']) ? $student['institution'] : ''; ?></a>
                            </li>
                             <li class="list-group-item">
                                 <b><?php echo 'Status'; ?></b> <a class="pull-right text-aqua"><?php echo $student['status']; ?></a>
                            </li>
                             <?php
                            if( $student['dormitory'] == 'Yes' ){  
                                $hostelroom = $this->hostelroom_model->get($student['hostel_id']);
                                ?>
                                 <li class="list-group-item">
                                     <b><?php echo 'Dormitory'; ?></b> <a class="pull-right text-aqua">Room No.:<?php echo $hostelroom['room_no']; ?></a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#activity" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('profile'); ?></a></li>
                        
                        <li class=""><a href="#exam" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('grades'); ?></a></li>
                        <li class=""><a href="#documents" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('documents'); ?></a></li>
                        <li class=" pull-right">
                            <a href="<?php echo base_url(); ?>/registrar/student/edit/<?php echo $student['id'] ?>" class="btn btn-warning btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"  style="color: red;">
                                <i class="fa fa-pencil"></i> <?php echo $this->lang->line('student'); ?> <?php echo $this->lang->line('edit'); ?>
                            </a>
                        </li>
                        <li class="pull-right">
                            <a href="#"  class="schedule_modal text-green" data-toggle="tooltip" title="<?php echo $this->lang->line('login_detail'); ?>"><i class="fa fa-key"></i>
                                <?php echo $this->lang->line('login_details'); ?>
                            </a>
                        </li>
						
                       <li class="pull-right"><a href="<?php echo base_url() ?>/registrar/student/delete/<?php echo $student['id']; ?>" class="text-red" onclick="return confirm('Are you sure you want to delete this Student? All related data can not be recovered!');"><i class="fa fa-trash"></i> <?php echo $this->lang->line('delete'); ?> <?php echo $this->lang->line('student'); ?></a></li>
                        <li class="pull-right">

                            <!-- <a href="#"  class="schedule_modal text-green" data-toggle="tooltip" title="<?php echo $this->lang->line('login_detail'); ?>"><i class="fa fa-key"></i>
                                <?php echo $this->lang->line('login_details'); ?>
                            </a> -->
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="activity"> 
                            <table class="table table-hover table-striped">
                                <tbody>   
                                    <tr>
                                        <td class="col-md-4"><?php echo $this->lang->line('admission_date'); ?></td>
                                        <td class="col-md-5">                                           
                                            <?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student['admission_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $this->lang->line('date_of_birth'); ?></td>
                                        <td><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student['dob'])); ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $this->lang->line('category'); ?></td>
                                        <td>
                                            <?php
                                            foreach ($category_list as $value) {
                                                if ($student['category_id'] == $value['id']) {
                                                    echo $value['category'];
                                                }
                                            }
                                            ?>  
                                        </td>
                                    </tr>
                                    <!--tr>
                                        <td><?php echo $this->lang->line('mobile_no'); ?></td>
                                        <td><?php echo $student['mobileno']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $this->lang->line('cast'); ?></td>
                                        <td><?php echo $student['cast']; ?></td>
                                    </tr>
                                    <tr-->
                                        <td><?php echo $this->lang->line('religion'); ?></td>
                                        <td><?php echo $student['religion']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $this->lang->line('email'); ?></td>
                                        <td><?php echo $student['email']; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <h3><?php echo $this->lang->line('address'); ?> <?php echo $this->lang->line('detail'); ?></h3>
                            <table class="table table-hover table-striped"><tbody>
                                    <tr>
                                        <td><?php echo $this->lang->line('current_address'); ?></td>
                                        <td><?php echo $new_complete_current_address?$new_complete_current_address:'Address 1, Barangay, City/Town, Province'; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $this->lang->line('permanent_address'); ?></td>
                                        
                                        <td><?php echo $new_complete_permanent_address?$new_complete_permanent_address:'Address 1, Barangay, City/Town, Province'; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <h3><?php echo $this->lang->line('parent'); ?> / <?php echo $this->lang->line('guardian_details'); ?> </h3>
                            <table class="table table-hover table-striped">
                                <tr>
                                    <td  class="col-md-4"><?php echo $this->lang->line('father_name'); ?></td>
                                    <td  class="col-md-5"><?php echo $student['father_name']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('father_phone'); ?></td>
                                    <td><?php echo $student['father_phone']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('father_occupation'); ?></td>
                                    <td><?php echo $student['father_occupation']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('mother_name'); ?></td>
                                    <td><?php echo $student['mother_name']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('mother_phone'); ?></td>
                                    <td><?php echo $student['mother_phone']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('mother_occupation'); ?></td>
                                    <td><?php echo $student['mother_occupation']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('guardian_name'); ?></td>
                                    <td><?php echo $student['guardian_name']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('guardian_relation'); ?></td>
                                    <td><?php echo $student['guardian_relation']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo 'Guardians Phone No. <br>Receives SMS';//$this->lang->line('guardian_phone'); ?></td>
                                    <td><?php echo $student['guardian_phone']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('guardian_occupation'); ?></td>
                                    <td><?php echo $student['guardian_occupation']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('guardian_address'); ?></td>
                                    <td><?php echo $new_complete_guardian_address?$new_complete_guardian_address:'Address 1, Barangay, City/Town, Province'; ?></td>
                                </tr>
                                </tbody>
                            </table>
                            <h3><?php echo $this->lang->line('miscellaneous_details'); ?></h3>
                            <table class="table table-hover table-striped">
                                <tbody>
                                    <tr>
                                        <td  class="col-md-4"><?php echo $this->lang->line('previous_school_details'); ?></td>
                                        <td  class="col-md-5"><?php echo $student['previous_school']; ?></td>
                                    </tr>
                                    <!--tr>
                                        <td  class="col-md-4"><?php echo $this->lang->line('national_identification_no'); ?></td>
                                        <td  class="col-md-5"><?php echo $student['adhar_no']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $this->lang->line('local_identification_no'); ?></td>
                                        <td><?php echo $student['samagra_id']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $this->lang->line('bank_account_no'); ?></td>
                                        <td><?php echo $student['bank_account_no']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $this->lang->line('bank_name'); ?></td>
                                        <td><?php echo $student['bank_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $this->lang->line('ifsc_code'); ?></td>
                                        <td><?php echo $student['ifsc_code']; ?></td>
                                    </tr-->
                                </tbody>
                            </table>
                        </div>
                              
                        <div class="tab-pane" id="documents">
                            <div class="timeline-header no-border">
                                <button type="button"  data-student-session-id="<?php echo $student['student_session_id'] ?>" class="btn btn-xs btn-primary pull-right myTransportFeeBtn"> <i class="fa fa-upload"></i>  Upload Documents</button>

                                <h2 class="page-header"><?php echo $this->lang->line('documents'); ?> <?php echo $this->lang->line('list'); ?></h2>                                
                                <table class="table table-striped table-bordered table-hover example">
                                    <thead>
                                        <tr>
                                            <th>
                                                <?php echo $this->lang->line('title'); ?>
                                            </th>
                                            <th>
                                                <?php echo $this->lang->line('file'); ?> <?php echo $this->lang->line('name'); ?>
                                            </th>
                                            <th class="mailbox-date text-right">
                                                <?php echo $this->lang->line('action'); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <div class="row">                                     
                                        <tbody>
                                            <?php
                                            if (empty($student_doc)) {
                                                ?>
                                                <!-- <tr>
                                                    <td colspan="5" class="text-danger text-center"><?php echo $this->lang->line('no_record_found'); ?></td>

                                                </tr> -->
                                                <?php
                                            } else {
                                                foreach ($student_doc as $value) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $value['title']; ?></td>
                                                        <td><?php echo $value['doc']; ?></td>
                                                        <td class="mailbox-date pull-right">
                                                            <a href="<?php echo base_url(); ?>registrar/student/download/<?php echo $value['student_id'] . "/" . $value['doc']; ?>"class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>">
                                                                <i class="fa fa-download"></i>
                                                            </a>
                                                            <a href="<?php echo base_url(); ?>registrar/student/doc_delete/<?php echo $value['id'] . "/" . $value['student_id']; ?>"class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('Are you sure you want to delete this item?');">
                                                                <i class="fa fa-remove"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                </table>
                            </div>
                            </table>
                        </div>                 
                        
			<div class="tab-pane" id="exam">
							<h3 class="page-header"><?php echo $this->lang->line('grades'); ?> <?php echo $this->lang->line('list'); ?></h3>
							<?php
							if (empty($list_of_subjects) ) {
								?>
								<div class="alert alert-danger">
									No Grades Found.
								</div>
								<?php
							} else { 
								if( $enableStrand ){
									$gradingsettings = $this->gradingsetting_model->getbySession( $session_id );
									$get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array();	
									$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
									$total_quarter = count( $get_quarter );
									$decimal_grades = isset($gradingsettings->decimal_grades)?$gradingsettings->decimal_grades:0;
									$decimal_average = isset($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;
									$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:2;
									$if_gradeisnull = isset($gradingsettings->if_gradeisnull)?$gradingsettings->if_gradeisnull:'blank';
									$allow_to_pass = isset($gradingsettings->allow_to_pass)?$gradingsettings->allow_to_pass:'no';
									
									
									?>	
									<div class="table-responsive"> 
										<table class="table table-hover table-striped">
											<thead>
												<tr>
													<th width="25%">
														First Semester
													</th>
													<?php 
													if( $get_quarter ){
														foreach( $get_quarter as $key => $value ){
															?>
															<th><?php echo $value.ordinal_suffix($value);?> Quarter</th>
															<?php
														}
													}?>
													<th>
														Final Grade
													</th>
												</tr>
											</thead>
											<tbody>
											<?php
												$set_semester = 1;
												$check_firstsem_strand = $this->studentstrand_model->get_latest_strand( $student_id, $set_semester, $session_id );
												if( $check_firstsem_strand ){
													$get_firstsem_strand = $check_firstsem_strand['strand_id'];
												} else {
													$get_firstsem_strand = $strand_id; 
												}
												$get_first_semester = $this->specializedsubject_model->getByStrandSemester( $get_firstsem_strand, $set_semester, $session_id );
												$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $set_semester, $session_id );
												if( $custom_subject ){
													$get_first_semester = array_merge( $get_first_semester, $custom_subject );
													
												}
												$get_first_semester = $this->customsubject_model->unsetsubjectdrop( $get_first_semester, $student_id, $set_semester, $session_id );		
												$first = 0;
												$second = 0;
												$third = 0;
												$fourth = 0;
												$get_ave = 0;
												$get_count = 0;
												$final_grade = 0;
												$count_subjects_first = 0;
												$count_subjects_second = 0;
												$count_subjects_third = 0;
												$count_subjects_fourth = 0;
												$subject_count = 0;
												$total_ave = 0;
												$complete_first = true;
												$complete_second = true;
												$complete_third = true;
												$complete_fourth = true;
												$complete_grades = true;
												if( $get_first_semester ){
													foreach( $get_first_semester as $first_semester => $firstsemester ){
														$complete_grades = true;
														$subject_id = $firstsemester['subject_id'];
														$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );
														$display_card = $subject_manager_details['display_card'];
														$include_computation = $subject_manager_details['include_computation'];
														$is_number = $subject_manager_details['is_number'];
														$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
														$align_right = $subject_manager_details['align_right'];
														?>
														<?php
														if( $display_card == 'yes'){
															if( $include_computation == 'yes'){
																?>
																<tr>
																<?php 
															} else {
																?>
																<tr style="background-color:yellow;">
																<?php
															}?>
															<?php
														}
														if( $display_card == 'yes'){
															if($align_right == 'yes'){
																?>
																<td><span style="margin-left:20px;"><?php echo $firstsemester['name'];?></span></td>
																<?php 
															} else {
																?>
																<td><strong><?php echo $firstsemester['name'];?></strong></td>
																<?php 
															}
														}
														$get_final_grade = 0;
														for( $qtr=0;$qtr<$total_quarter;$qtr++ ) {
															$quarter = $get_quarter[$qtr];
															$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, 'ga', $session_id );
															if( empty( $grade_per_quarter )){
																$complete_grades = false;
															}
															if( $include_computation == 'yes'){
																$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, 'ga', $session_id );
																if( empty($checkifChild)){
																	$grade_per_quarter_with_unit = $grade_per_quarter * $units;
																	if($quarter==1){ 
																		if( empty( $grade_per_quarter )){
																			$complete_first = false;
																		} 
																		$first = $first + $grade_per_quarter_with_unit;
																		$count_subjects_first = $count_subjects_first + $units;
																	} elseif($quarter==2){ 	
																		if( empty( $grade_per_quarter )){
																			$complete_second = false;
																		} 
																		$second = $second + $grade_per_quarter_with_unit; 
																		$count_subjects_second = $count_subjects_second + $units;
																	} elseif($quarter==3){ 
																		if( empty( $grade_per_quarter )){
																			$complete_third = false;
																		} 
																		$third = $third + $grade_per_quarter_with_unit; 
																		$count_subjects_third = $count_subjects_third + $units;
																	} elseif($quarter==4){
																		if( empty( $grade_per_quarter )){
																			$complete_fourth = false;
																		} 
																		$fourth = $fourth + $grade_per_quarter_with_unit; 
																		$count_subjects_fourth = $count_subjects_fourth + $units;
																	}
																}
															}
															
															if( $display_card == 'yes'){
																if( $grade_per_quarter == null || $grade_per_quarter == 0  ){
																	if( $if_gradeisnull == 'blank'){
																		?>
																		<td></td>
																		<?php
																	}  else {
																		?>
																		<td><?php echo number_format((float)0, $decimal_grades, '.', '');?></td>
																		<?php
																	}
																} else {
																	$display_q_grade = $this->grade_model->get_letter_grade_special($grade_per_quarter, $firstsemester['name']);
																	?>
																	<td><?php echo $display_q_grade;?></td>
																	<?php
																}
															}
															$get_final_grade = $get_final_grade + $grade_per_quarter;
														}
														
														if( empty( $get_final_grade ) ){
															$final_grade = '';
														} else {
															$final_grade =  $get_final_grade / $total_quarter;
														}
														if( $include_computation == 'yes' ){ 
															if( empty( $checkifChild ) ){
																if( $allow_to_pass == 'yes'){
																	if( $final_grade == '74.5'){
																		$final_grade = '75';
																	} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
																		$final_grade = '75';
																	}
																}
															}
														}
														
														$final_grade =  number_format((float)$final_grade, $decimal_finalgrade, '.', '');
														if( $display_card == 'yes'){
															if( $complete_grades ){
																$display_f_grade = $this->grade_model->get_letter_grade_special($final_grade, $firstsemester['name']);
																?>
																<td><?php echo $display_f_grade;?></td>
																<?php 
															} else {
																?>
																<td></td>
																<?php
															}
														}
														if( $display_card == 'yes'){
															?>
															</tr>
															<?php
														}
														if( $include_computation == 'yes' ){ 
															$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, 'ga', $session_id );
															if( empty($checkifChild)){
																$final_grade = $final_grade * $units;
																$total_ave = $total_ave + $final_grade;
																$subject_count = $subject_count + $units;
															}
														}
													}
													$first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
													$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
													$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
													$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
													$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
													$second_ave =  number_format((float)$second_ave, $decimal_average, '.', '');
													$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
													$fourth_ave =  number_format((float)$fourth_ave, $decimal_average, '.', '');
													?>
													<tr></tr>  
													<tr>
														<td><b>Average</b></td>
														<?php 
														//$total_average = 0;
														$complete_average = 0;
														if( $get_quarter ){
															foreach( $get_quarter as $key => $value ){
																if($value==1){ 
																	if( $first_ave != 0 && $first_ave != 0.00 && $first_ave != null ){
																		$complete_average++;
																	}
																	if( $complete_first ) {
																		?><td><b><?php echo $first != 0 ? $first_ave:'';?></b></td><?php
																	} else {
																		?><td><b></b></td><?php
																	}
																	//$total_average = $total_average + $first_ave;
																	
																} elseif($value==2){
																	if( $second_ave != 0 && $second_ave != 0.00 && $second_ave != null ){
																		$complete_average++;
																	}	
																	if( $complete_second ) {
																		?><td><b><?php echo $second != 0 ? $second_ave:'';?></b></td><?php
																	} else {
																		?><td><b></b></td><?php
																	}
																	//$total_average = $total_average + $second_ave;
																} elseif($value==3){
																	if( $third_ave != 0 && $third_ave != 0.00 && $third_ave != null ){
																		$complete_average++;
																	}
																	if( $complete_third ) {
																		?><td><b><?php echo $third != 0 ? $third_ave:'';?></b></td><?php
																	} else {
																		?><td><b></b></td><?php
																	}
																	//$total_average = $total_average + $third_ave;
																} elseif($value==4){
																	if( $fourth_ave != 0 && $fourth_ave != 0.00 && $fourth_ave != null ){
																		$complete_average++;
																	}
																	if( $complete_fourth ) {
																		?><td><b><?php echo $fourth != 0 ? $fourth_ave:'';?></b></td><?php
																	} else {
																		?><td><b></b></td><?php
																	}
																	//$total_average = $total_average + $fourth_ave;
																}
															}
														}
														$count_quarter = $complete_average;
														$total_ave =   $subject_count != 0 ? $total_ave / $subject_count:0;
														//if( $count_quarter == $total_quarter && $complete_grades ){ 
														if( $complete_first && $complete_second && $complete_third && $complete_fourth ){ 
															$final_grade =  number_format((float)$total_ave, $decimal_average, '.', '');
															?>
															<td><b><?php echo $final_grade;?></b></td>
															<?php
														} else {
															?>
															<td></td> 
															<?php
														} 
														?>
													</tr>       
													<?php
														
												}
											?>
											<tr><td colspan="10"><small><i>Notes:</i></small></td></tr>  
											<tr><td colspan="10"><small><i>Grade highlighted doesnt include in computation.</i></small></td></tr>  
											<tr><td colspan="10"><small><i>The average will be displayed until grades completed.</i></small></td></tr>  
										</tbody>
										</table>
									</div> 
									<?php 
									$get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();	
									$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
									$total_quarter = count( $get_quarter );
									?>
									<table class="table table-hover table-striped">
										<thead>
											<tr>
												<th width="25%">
													Second Semester
												</th>
												<?php 
												if( $get_quarter ){
													foreach( $get_quarter as $key => $value ){
														?>
														<th><?php echo $value.ordinal_suffix($value);?> Quarter</th>
														<?php
													}
												}?>
												<th>
													Final Grade
												</th>
											</tr>
										</thead>
										<tbody>
										<?php
											$set_semester = 2;
											$check_secondsem_strand = $this->studentstrand_model->get_latest_strand( $student_id, $set_semester, $session_id );
											if( $check_secondsem_strand ){
												$get_strand_second_sem = $check_secondsem_strand['strand_id'];
											} else {
												$get_strand_second_sem = $strand_id; 
											}
											$get_second_semester = $this->specializedsubject_model->getByStrandSemester( $get_strand_second_sem, $set_semester, $session_id );
											$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $set_semester, $session_id );
											if( $custom_subject ){
												$get_second_semester = array_merge( $get_second_semester, $custom_subject );
											}
											$get_second_semester = $this->customsubject_model->unsetsubjectdrop( $get_second_semester, $student_id, $set_semester, $session_id );			
											$first = 0;
											$second = 0;
											$third = 0;
											$fourth = 0;
											$get_ave = 0;
											$get_count = 0;
											$final_grade = 0;
											$count_subjects_first = 0;
											$count_subjects_second = 0;
											$count_subjects_third = 0;
											$count_subjects_fourth = 0;
											$complete_first = true;
											$complete_second = true;
											$complete_third = true;
											$complete_fourth = true;
											$subject_count = 0;
											$complete_grades = true;
											$total_ave = 0;
											if( $get_second_semester ){
												foreach( $get_second_semester as $second_semester => $secondsemester ){
													$complete_grades = true;
													$subject_id = $secondsemester['subject_id'];
													$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );
													$display_card = $subject_manager_details['display_card'];
													$include_computation = $subject_manager_details['include_computation'];
													$is_number = $subject_manager_details['is_number'];
													$units = !empty($subject_manager_details['units'])?$subject_manager_details['units']:1;
													$align_right = $subject_manager_details['align_right'];
													?>
													<?php
													if( $display_card == 'yes'){
														if( $include_computation == 'yes'){
															?>
															<tr>
															<?php 
														} else {
															?>
															<tr style="background-color:yellow;">
															<?php
														}?>
														<?php
													}
													if( $display_card == 'yes'){
														if($align_right == 'yes'){
															?>
															<td><span style="margin-left:20px;"><?php echo $secondsemester['name'];?></span></td>
															<?php 
														} else {
															?>
															<td><strong><?php echo $secondsemester['name'];?></strong></td>
															<?php 
														}
													}
													$get_final_grade = 0;
													for( $qtr=0;$qtr<$total_quarter;$qtr++ ) {
														$quarter = $get_quarter[$qtr];
														//$getgradeperquarter = $this->grade_model->getgradeperquarter( $student_id, $subject_id, $quarter, $set_semester );
														//$grade_per_quarter = isset($getgradeperquarter['final_grade'])?$getgradeperquarter['final_grade']:null;
														$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, 'ga', $session_id );
														if( $grade_per_quarter == null ){
															$complete_grades = false;
														} 
														if( $include_computation == 'yes'){
															$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, 'ga', $session_id );
															if( empty($checkifChild)){
																$grade_per_quarter_with_unit = $grade_per_quarter * $units;
																if($quarter==1){ 
																	if( empty( $grade_per_quarter )){
																			$complete_first = false;
																		} 
																		$first = $first + $grade_per_quarter_with_unit;
																		$count_subjects_first = $count_subjects_first + $units;
																	} elseif($quarter==2){ 	
																		if( empty( $grade_per_quarter )){
																			$complete_second = false;
																		} 
																		$second = $second + $grade_per_quarter_with_unit; 
																		$count_subjects_second = $count_subjects_second + $units;
																	} elseif($quarter==3){ 
																		if( empty( $grade_per_quarter )){
																			$complete_third = false;
																		} 
																		$third = $third + $grade_per_quarter_with_unit; 
																		$count_subjects_third = $count_subjects_third + $units;
																	} elseif($quarter==4){
																		if( empty( $grade_per_quarter )){
																			$complete_fourth = false;
																		} 
																		$fourth = $fourth + $grade_per_quarter_with_unit; 
																		$count_subjects_fourth = $count_subjects_fourth + $units;
																	}
															}
														}
														if( $display_card == 'yes'){
															if( $grade_per_quarter == null || $grade_per_quarter == 0  ){
																if( $if_gradeisnull == 'blank'){
																	?>
																	<td></td>
																	<?php
																}  else {
																	?>
																	<td><?php echo number_format((float)0, $decimal_grades, '.', '');?></td>
																	<?php
																}
															} else {
																$display_q_grade = $this->grade_model->get_letter_grade_special($grade_per_quarter, $secondsemester['name']);
																?>
																<td><?php echo $display_q_grade;?></td>
																<?php
															}
														}
														$get_final_grade = $get_final_grade + $grade_per_quarter;
													}
													
													if( $get_final_grade == null ){
														$final_grade = '';
													} else {
														$final_grade =  $get_final_grade / $total_quarter;
													}
													if( $include_computation == 'yes' ){ 
														if( empty( $checkifChild ) ){
															if( $allow_to_pass == 'yes'){
																if( $final_grade == '74.5'){
																	$final_grade = '75';
																} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
																	$final_grade = '75';
																}
															}
														}
													}
													
													$final_grade =  number_format((float)$final_grade, $decimal_finalgrade, '.', '');
													if( $display_card == 'yes'){
														if( $complete_grades ){
															$display_f_grade = $this->grade_model->get_letter_grade_special($final_grade, $secondsemester['name']);
															?>
															<td><?php echo $display_f_grade;?></td>
															<?php 
														} else {
															?>
															<td></td>
															<?php
														}
													}
													if( $display_card == 'yes'){
														?>
														</tr>
														<?php
													}
													if( $include_computation == 'yes' ){ 
														$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id,'ga', $session_id );
														if( empty($checkifChild)){
															$final_grade = $final_grade * $units;
															$total_ave = $total_ave + $final_grade;
															$subject_count = $subject_count + $units;
														}
													}
												}
												$first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
												$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
												$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
												$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
												$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
												$second_ave =  number_format((float)$second_ave, $decimal_average, '.', '');
												$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
												$fourth_ave =  number_format((float)$fourth_ave, $decimal_average, '.', '');
												?>
												<tr></tr>  
												<tr>
													<td><b>Average</b></td>
													<?php 
													//$total_average = 0;
													$complete_average = 0;
													if( $get_quarter ){
														foreach( $get_quarter as $key => $value ){
															if($value==1){ 
																if( $first_ave != 0 && $first_ave != 0.00 && $first_ave != null ){
																	$complete_average++;
																}
																if( $complete_first ) {
																	?><td><b><?php echo $first != 0 ? $first_ave:'';?></b></td><?php
																} else {
																	?><td><b></b></td><?php
																}
																//$total_average = $total_average + $first_ave;
																
															} elseif($value==2){
																if( $second_ave != 0 && $second_ave != 0.00 && $second_ave != null ){
																	$complete_average++;
																}	
																if( $complete_second ) {
																	?><td><b><?php echo $second != 0 ? $second_ave:'';?></b></td><?php
																} else {
																	?><td><b></b></td><?php
																}
																//$total_average = $total_average + $second_ave;
															} elseif($value==3){
																if( $third_ave != 0 && $third_ave != 0.00 && $third_ave != null ){
																	$complete_average++;
																}
																if( $complete_third ) {
																	?><td><b><?php echo $third != 0 ? $third_ave:'';?></b></td><?php
																} else {
																	?><td><b></b></td><?php
																}
																//$total_average = $total_average + $third_ave;
															} elseif($value==4){
																if( $fourth_ave != 0 && $fourth_ave != 0.00 && $fourth_ave != null ){
																	$complete_average++;
																}
																if( $complete_fourth ) {
																	?><td><b><?php echo $fourth != 0 ? $fourth_ave:'';?></b></td><?php
																} else {
																	?><td><b></b></td><?php
																}
																//$total_average = $total_average + $fourth_ave;
															}
														}
													}
													
													$count_quarter = $complete_average;
													$total_ave =   $subject_count != 0 ? $total_ave / $subject_count:0;
													if( $complete_first && $complete_second && $complete_third && $complete_fourth ){ 
														$final_grade =  number_format((float)$total_ave, $decimal_average, '.', '');
														?>
														<td><b><?php echo $final_grade;?></b></td>
														<?php
													} else {
														?>
														<td></td> 
														<?php
													} 
													?>
												</tr>       
												<?php
													
											}
										?>
										<tr><td colspan="10"><small><i>Notes:</i></small></td></tr>  
										<tr><td colspan="10"><small><i>Grade highlighted doesnt include in computation.</i></small></td></tr>  
										<tr><td colspan="10"><small><i>The average will be displayed until grades completed.</i></small></td></tr>  
									</tbody>
									</table>
									<?php 
								} else {
									?>
									<div class="table-responsive"> 
										<table class="table table-hover table-striped">
											<thead>
												<tr>
													<th>
														<?php echo $this->lang->line('subject'); ?>
													</th>
													<th>
														Term 1
													</th>
													<th>
														Term 2
													</th>
													<th>
														Term 3
													</th>
													<th>
														Final Grade
													</th>
												</tr>
											</thead>
											<tbody>
											<?php
												//$get_master_sheet = $this->grade_model->get_master_sheet( $list_of_subjects, $student_id );
												$first = 0;
												$second = 0;
												$third = 0;
												$fourth = 0;
												$count_subjects_first = 0;
												$count_subjects_second = 0;
												$count_subjects_third = 0;
												$count_subjects_fourth = 0;
												$last_quarter = 3;
												$get_ave = 0;
												$get_count = 3;
												$final_grade = 0;
												$total_ave = 0;
												$subject_count = 0;
												$gradingsettings = $this->gradingsetting_model->getbySession( $session_id );
												$decimal_grades = isset($gradingsettings->decimal_grades)?$gradingsettings->decimal_grades:0;
												$decimal_average = isset($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;
												$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:2;
												$if_gradeisnull = isset($gradingsettings->if_gradeisnull)?$gradingsettings->if_gradeisnull:'blank';
												$allow_to_pass = isset($gradingsettings->allow_to_pass)?$gradingsettings->allow_to_pass:'no';
												$combine_category = 'ga';
												$complete_grades = true;
												$complete_first = true;
												$complete_second = true;
												$complete_third = true;
												$complete_fourth = true;
												foreach( $list_of_subjects as $listsubject => $subjects ){
													$subject_id = $subjects['id'];
													$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );
													$display_card = $subject_manager_details['display_card'];
													$include_computation = $subject_manager_details['include_computation'];
													$is_number = $subject_manager_details['is_number'];
													$align_right = $subject_manager_details['align_right'];
														if( $display_card == 'yes'){
															if( $include_computation == 'yes'){
																?>
																<tr>
																<?php
															} else {
																?>
																<tr style="background-color:yellow;">
																<?php
															}
															
															if( $align_right == 'yes' ){
																?>
																<td><span style="margin-left:20px;"><?php echo $subjects['name'];?></span></td>
																<?php
															} else {
																?>
																<td><strong><?php echo $subjects['name'];?></strong></td>
																<?php
															}
														}
														$get_final_grade = 0;
														for($x=1;$x<=$last_quarter;$x++){
															//$getgradeperquarter = $this->grade_model->getgradeperquarter( $student_id, $subject_id, $x );
															//$grade_per_quarter = isset($getgradeperquarter['final_grade'])?$getgradeperquarter['final_grade']:null;
															$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $x, 'ga', $session_id );
															if( $include_computation == 'yes' ){
																if( $grade_per_quarter == null ){
																	$complete_grades = false;
																}  
																$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, 'ga', $session_id );
																if( empty( $checkifChild ) ){
																	if($x==1){ 
																		$first = $first + $grade_per_quarter;
																		$grade_per_quarter != null?$count_subjects_first ++:'';
																		if( empty( $grade_per_quarter )){
																			$complete_first = false;
																		}
																	} elseif($x==2){ 
																		$second = $second + $grade_per_quarter; 
																		$grade_per_quarter != null?$count_subjects_second++:'';
																		if( empty( $grade_per_quarter )){
																			$complete_second = false;
																		}
																	} elseif($x==3){ 
																		$third = $third + $grade_per_quarter; 
																		$grade_per_quarter != null?$count_subjects_third++:'';
																		if( empty( $grade_per_quarter )){
																			$complete_third = false;
																		}
																	}
																}
															}
															if( $display_card == 'yes'){
																if( $grade_per_quarter == null || $grade_per_quarter == 0 ){
																	if( $if_gradeisnull == 'blank'){
																	?>
																	<td></td>
																	<?php
																	}  else {
																	?>
																	<td><?php echo number_format((float)0, $decimal_grades, '.', '');?></td>
																	<?php
																	} 
																} else {
																	$subject_name_jhs = isset($subjects['name']) ? $subjects['name'] : '';
                                                                    $display_q_grade = $this->grade_model->get_letter_grade_special($grade_per_quarter, $subject_name_jhs);
																	?>
																	<td><?php echo $display_q_grade;?></td>
																	<?php
																}
															}	
															$get_final_grade = $get_final_grade + $grade_per_quarter;
														}
														if( $get_final_grade ){
															$final_grade = $get_final_grade / $last_quarter;
															if( $include_computation == 'yes' ){ 
																if( empty( $checkifChild ) ){
																	if( $allow_to_pass == 'yes'){
																		if( $final_grade == '74.5'){
																			$final_grade = '75';
																		} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
																			$final_grade = '75';
																		}
																	}
																}
															}
															
															$final_grade = number_format((float)$final_grade, $decimal_finalgrade, '.', '');
														} else {
															$final_grade = '';
														}  
														
														if( $get_final_grade != null ){
															$get_ave = $get_ave + $final_grade;
															 
														}
														
														if( $display_card == 'yes'){
															if( $complete_grades ){
																$subject_name_jhs = isset($subjects['name']) ? $subjects['name'] : '';
                                                                $display_f_grade = $this->grade_model->get_letter_grade_special($final_grade, $subject_name_jhs);
																?>
																<td><?php echo $display_f_grade;?></td>
																<?php
															} else {
																?>
																<td></td>
																<?php
															}
														}
														if( $display_card == 'yes'){
															?>
															</tr>
															<?php
														}
														
														if( $include_computation == 'yes' ){ 
															$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, 'ga', $session_id );
															if( empty($checkifChild  )){
																$total_ave = $total_ave + $final_grade;
																$subject_count++;
															}
														}
												}
												$first_ave =  $count_subjects_first != 0?$first/$count_subjects_first:0;
												$second_ave = $count_subjects_second != 0?$second/$count_subjects_second:0;
												$third_ave =  $count_subjects_third != 0?$third/$count_subjects_third:0;
												$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');
												$second_ave =  number_format((float)$second_ave, $decimal_average, '.', '');
												$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');
												
											?>    
											<tr></tr>  
											<tr>
												<td><b>Average</b></td>
												<?php 
												if( $complete_first ){
													?><td><b><?php echo $first != 0 ? $first_ave:'';?></b></td><?php 
												} else {
													?><td><b></b></td><?php 
												}
												if( $complete_second ){
													?><td><b><?php echo $second != 0 ? $second_ave:'';?></b></td><?php 
												} else {
													?><td><b></b></td><?php 
												}
												if( $complete_third ){
													?><td><b><?php echo $third != 0 ? $third_ave:'';?></b></td><?php 
												} else {
													?><td><b></b></td><?php 
												}
												if( $complete_grades  ){  
												//if( $first  ){  
													$total_ave =   $subject_count != 0 ? $total_ave / $subject_count:0;
													$final_grade = number_format((float)$total_ave, $decimal_average, '.', '');
													?>
													<td><b><?php echo $final_grade;?></b></td>
													<?php
												} else {
													?>
													<td></td> 
													<?php
												}  
												?>
											</tr>   
											<tr></tr>  
											<tr></tr>  
											<tr><td colspan="10"><small><i>Notes:</i></small></td></tr>  
											<tr><td colspan="10"><small><i>Grade highlighted doesnt include in computation.</i></small></td></tr>  
											<tr><td colspan="10"><small><i>The average will be displayed until grades completed.</i></small></td></tr>  
										</tbody>
										</table>
										
									</div> 
									<?php
								}
								?>
								<h3> Conduct Grade</h3>
                                <?php 
                                if( $enableStrand ){
                                     $gradingsettings = $this->gradingsetting_model->getbySession( $session_id );
                                    $get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array(); 
                                    $get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
                                    $total_quarter = count( $get_quarter );
                                    ?>
                                    <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                            1st Semester
                                            </th>
                                             <?php 
                                                if( $get_quarter ){
                                                    foreach( $get_quarter as $key => $value ){
                                                        ?>
                                                        <th><?php echo $value.ordinal_suffix($value);?> Qtr</th>
                                                        <?php
                                                    }
                                                }?>
                                            <th>
                                                Final Grade
                                            </th>
                                        </tr>
                                    </thead>
                                   <tbody>
                                    <?php 
                                       
                                        $total_conduct_number = 0;
                                        $conduct_number = 0;
                                        $count_conduct_number = 0;
                                        if( $getConductSubject ){
                                            foreach( $getConductSubject as $getConduct => $conduct){
                                                $conduct_id = $conduct['id'];
                                                if( $conduct_id ){
                                                    
                                                    ?> 
                                                    <tr>
                                                        <td><?php echo $conduct['name'];?></td>
                                                        <?php 
                                                        for( $qtr=0;$qtr<$total_quarter;$qtr++ ) {
                                                            $quarter = $get_quarter[$qtr];
                                                            $conduct_details = $this->conductimportbatchdetail_model->getAllDetailsConductByStudentPerQuarter( $conduct_id, $student_id, $class_id, $section_id, $quarter, $session_id );
                                                            if( isset( $conduct_details ) && $conduct_details ){
                                                                 $count_conduct_number++;
                                                                  $conduct_number = $this->conductimportbatchdetail_model->conduct_to_number($conduct_details['conduct'] );
                                                                $total_conduct_number = $total_conduct_number + $conduct_number;
                                                                ?>
                                                                <td><?php echo $conduct_details['conduct'];?></td>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <td></td>
                                                                <?php
                                                            }
                                                        }
                                                        if( $count_conduct_number == $total_quarter ){
                                                        ?>
                                                        <td>
                                                            <?php 
                                                            if( $total_conduct_number > 0 ){
                                                            $conduct_average = $total_conduct_number / $count_conduct_number;
                                                            $conduct_final = $this->conductimportbatchdetail_model->conduct_to_letter( $conduct_average );
                                                                echo $conduct_final;
                                                         }
                                                        ?>
                                                        </td>
                                                        <?php
                                                        } else {
                                                            ?>
                                                            <td></td>
                                                            <?php
                                                        }
                                                        ?>
                                                    </tr>
                                                    <?php 
                                                } 
                                            }
                                        }?>
                                    </tbody>
                                    </table>
                                    <?php

                                     $gradingsettings = $this->gradingsetting_model->getbySession( $session_id );
                                    $get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array(); 
                                    $get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
                                    $total_quarter = count( $get_quarter );
                                    ?>
                                    <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                            2nd Semester
                                            </th>
                                             <?php 
                                                if( $get_quarter ){
                                                    foreach( $get_quarter as $key => $value ){
                                                        ?>
                                                        <th><?php echo $value.ordinal_suffix($value);?> Qtr</th>
                                                        <?php
                                                    }
                                                }?>
                                            <th>
                                                Final Grade
                                            </th>
                                        </tr>
                                    </thead>
                                   <tbody>
                                    <?php 
                                       
                                        $total_conduct_number = 0;
                                        $conduct_number = 0;
                                        $count_conduct_number = 0;
                                        if( $getConductSubject ){
                                            foreach( $getConductSubject as $getConduct => $conduct){
                                                $conduct_id = $conduct['id'];
                                                if( $conduct_id ){
                                                    
                                                    ?> 
                                                    <tr>
                                                        <td><?php echo $conduct['name'];?></td>
                                                        <?php 
                                                        for( $qtr=0;$qtr<$total_quarter;$qtr++ ) {
                                                            $quarter = $get_quarter[$qtr];
                                                            $conduct_details = $this->conductimportbatchdetail_model->getAllDetailsConductByStudentPerQuarter( $conduct_id, $student_id, $class_id, $section_id, $quarter, $session_id );
                                                            if( isset( $conduct_details ) && $conduct_details ){
                                                                 $count_conduct_number++;
                                                                  $conduct_number = $this->conductimportbatchdetail_model->conduct_to_number($conduct_details['conduct'] );
                                                                $total_conduct_number = $total_conduct_number + $conduct_number;
                                                                ?>
                                                                <td><?php echo $conduct_details['conduct'];?></td>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <td></td>
                                                                <?php
                                                            }
                                                        }
                                                        if( $count_conduct_number == $total_quarter ){
                                                        ?>
                                                        <td>
                                                            <?php 
                                                            if( $total_conduct_number > 0 ){
                                                            $conduct_average = $total_conduct_number / $count_conduct_number;
                                                            $conduct_final = $this->conductimportbatchdetail_model->conduct_to_letter( $conduct_average );
                                                                echo $conduct_final;
                                                         }
                                                        ?>
                                                        </td>
                                                        <?php
                                                        } else {
                                                            ?>
                                                            <td></td>
                                                            <?php
                                                        }
                                                        ?>
                                                    </tr>
                                                    <?php 
                                                } 
                                            }
                                        }?>
                                    </tbody>
                                    </table>
                                    <?php

                                } else {
                                    ?>
								<table class="table table-hover table-striped">
									<thead>
										<tr>
											<th>
												Conduct
											</th>
											<th>
												1st Qtr
											</th>
											<th>
												2nd Qtr
											</th>
											<th>
												3rd Qtr
											</th>
											<th>
												4th Qtr
											</th>
											<th>
												Final Grade
											</th>
										</tr>
									</thead>
								<tbody>
									<?php 
										$last_quarter = 4;
                                        $total_quarter = 4;
                                        $total_conduct_number = 0;
                                        $conduct_number = 0;
                                        $count_conduct_number = 0;
										if( $getConductSubject ){
											foreach( $getConductSubject as $getConduct => $conduct){
												$conduct_id = $conduct['id'];
												if( $conduct_id ){
													
													?> 
													<tr>
														<td><?php echo $conduct['name'];?></td>
														<?php 
														for($x=1;$x<=4;$x++){
															if( $x <= $last_quarter ){
																$conduct_details = $this->conductimportbatchdetail_model->getAllDetailsConductByStudentPerQuarter( $conduct_id, $student_id, $class_id, $section_id, $x, $session_id );
																//printx( $conduct_details );
																if( isset( $conduct_details ) && $conduct_details ){
                                                                     $count_conduct_number++;
                                                                    $conduct_number = $this->conductimportbatchdetail_model->conduct_to_number($conduct_details['conduct'] );
                                                                    $total_conduct_number = $total_conduct_number + $conduct_number;
																	?>
																	<td><?php echo $conduct_details['conduct'];?></td>
																	<?php
																} else {
																	?>
																	<td></td>
																	<?php
																}
															} else {
																?>
																<td></td>
																<?php
															}
															
														}
                                                        if( $count_conduct_number == $total_quarter ){
                                                        ?>
                                                        <td>
                                                            <?php 
                                                            if( $total_conduct_number > 0 ){
                                                            $conduct_average = $total_conduct_number / $count_conduct_number;
                                                            $conduct_final = $this->conductimportbatchdetail_model->conduct_to_letter( $conduct_average );
                                                                 echo $conduct_final;
                                                         }
                                                        ?>
                                                        </td>
                                                        <?php
                                                        } else {
                                                            ?>
                                                            <td></td>
                                                            <?php
                                                        }
														?>
													</tr>
													<?php 
												} 
											}
										}?>
									</tbody>
								</table>
								<?php
                               }
							}
							?>                   
						</div>      
                    </div>
                </div>
            </div>
    </section> 
</div>

<script type="text/javascript">
    $(".myTransportFeeBtn").click(function () {       
        $("span[id$='_error']").html("");
        $('#transport_amount').val("");
        $('#transport_amount_discount').val("0");
        $('#transport_amount_fine').val("0");
        var student_session_id = $(this).data("student-session-id");
        $('.transport_fees_title').html("<b>Upload Document</b>");
        $('#transport_student_session_id').val(student_session_id);
        $('#myTransportFeesModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    });
</script>
<div class="modal fade" id="myTransportFeesModal" role="dialog">
    <div class="modal-dialog">        
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title title text-center transport_fees_title"></h4>
            </div>
            <div class="">
                <div class="form-horizontal">
                    <div class="">
                        <input  type="hidden" class="form-control" id="transport_student_session_id"  value="0" readonly="readonly"/>
                        <form id="form1" action="<?php echo site_url('registrar/student/create_doc') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                             <?php echo $this->customlib->getCSRF(); ?>
                            <div id='upload_documents_hide_show'>                              
                                <input type="hidden" name="student_id" value="<?php echo $student_doc_id; ?>" id="student_id">
                                <h4><?php echo $this->lang->line('upload_documents1'); ?></h4>
                                <div class="col-md-12">
                                    <div class="">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('title'); ?></label>
                                                <input id="first_title" name="first_title" placeholder="" type="text" class="form-control"  value="<?php echo set_value('first_title'); ?>" />
                                                <span class="text-danger"><?php echo form_error('first_title'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('Documents'); ?></label>
                                                <input id="first_doc_id" name="first_doc" placeholder="" type="file" style="margin-top:8px; border:0; outline:none;" class="form-control"  value="<?php echo set_value('first_doc'); ?>" />
                                                <span class="text-danger"><?php echo form_error('first_doc'); ?></span>
                                            </div>
                                        </div>
                                    </div></div>
                            </div>
                            <div class="modal-footer" style="clear:both">
                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="scheduleModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title_logindetail"></h4>
            </div>
            <div class="modal-body_logindetail">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).on('click', '.schedule_modal', function () {
        $('.modal-title_logindetail').html("");
        $('.modal-title_logindetail').html("<?php echo $this->lang->line('login_details'); ?>");
        var base_url = '<?php echo base_url() ?>';
        var student_id = '<?php echo $student["id"] ?>';
        var student_first_name = '<?php echo $student["firstname"] ?>';
        var student_last_name = '<?php echo $student["lastname"] ?>';
        $.ajax({
            type: "post",
            url: base_url + "registrar/student/getlogindetail",
            data: {'student_id': student_id},
            dataType: "json",
            success: function (response) {              
                var data = "";
                data += '<div class="col-md-12">';
                data += '<div class="table-responsive">';
                data += '<p class="lead text text-center">' + student_first_name + ' ' + student_last_name + '</p>';
                data += '<table class="table table-hover">';
                data += '<thead>';
                data += '<tr>';
                data += '<th>'+"<?php echo $this->lang->line('user_type'); ?>"+'</th>';
                data += '<th class="text text-center">'+"<?php echo $this->lang->line('username'); ?>"+'</th>';
                data += '<th class="text text-center">'+"<?php echo $this->lang->line('password'); ?>"+'</th>';
                data += '</tr>';
                data += '</thead>';
                data += '<tbody>';
				
				// this shows parents details only
                // $.each(response, function (i, obj)
                // {
                //     if( obj.role == "parent" ||obj.role == "Parent" ){ 
                //         data += '<tr>';
                //         data += '<td><b>' + firstToUpperCase(obj.role) + '</b></td>';
                //         data += '<td class="text text-center">' + obj.username + '</td> ';
                //         data += '<td class="text text-center">' + obj.password + '</td> ';
                //         data += '</tr>';
                //     }

                // });
                // data += '</tbody>';
                // data += '</table>'; 
                // data += '</div>  ';
                // data += '</div>  ';               
                // $('.modal-body_logindetail').html(data);
                // $("#scheduleModal").modal('show');

				$.each(response, function (i, obj)
                {
                    data += '<tr>';
                    data += '<td><b>' + firstToUpperCase(obj.role) + '</b></td>';
                    data += '<td class="text text-center">' + obj.username + '</td> ';
                    data += '<td class="text text-center">' + obj.password + '</td> ';
                    data += '</tr>';
                });
                data += '</tbody>';
                data += '</table>';
                data += '<b class="lead text text-danger" style="font-size:14px;"> '+"<?php echo $this->lang->line('login_url'); ?>"+': ' + base_url + 'site/userlogin</b>';
                data += '</div>  ';
                data += '</div>  ';
                $('.modal-body_logindetail').html(data);
                $("#scheduleModal").modal('show');


            }
        });
    });

    function firstToUpperCase(str) {
        return str.substr(0, 1).toUpperCase() + str.substr(1);
    }
</script>

<script>
    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            title: '',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });
</script>

