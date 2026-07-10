<style type="text/css">
    @media print
    {
        .no-print, .no-print *
        {
            display: none !important;
        }
    }
</style>

<div class="content-wrapper" style="min-height: 946px;">   
    <section class="content-header">
        <h1> <i class=" "></i> Student Ranking<small></h1>
    </section>   
    <section class="content">
        <div class="row">          
            <div class="col-md-12">              
                                
                <div class="box box-primary" id="sublist"> 
                     <div class="box-header with-border">
                        <h3 class="box-title">Select Student by Grade and Section</h3>
                    </div> 
                    <div class="box-body">
                        <form id='form1' action="<?php echo site_url('registrar/report/student_ranking') ?>"  method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('class'); ?></label>
                                            <select  id="class_id" name="class_id" class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php
                                                foreach ($classlist as $class) {
                                                    ?>
                                                    <option value="<?php echo $class['id'] ?>" <?php
                                                    if ($class_id == $class['id']) {
                                                        echo "selected =selected";
                                                    }
                                                    ?>><?php echo $class['class'] ?></option>
                                                            <?php
                                                            $count++;
                                                        }
                                                        ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('section'); ?></label>
                                            <select  id="section_id" name="section_id" class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                        </div>
                                    </div>
									<div class="col-md-2"  id="strand" style="display:none">
										<div class="form-group">
											<label for="exampleInputEmail1"><?php echo "Semester"; ?></label>
											<select class="form-control" name="semester" id="select_strand">
												<option value=""><?php echo $this->lang->line('select'); ?></option>
												<?php
													foreach ($getSemester as  $value => $key) {
													?>
													<option value="<?php echo $value; ?>" <?php if($semester == $value) echo "selected"; ?>><?php echo $key; ?></option>
													<?php
													}
												?>
											</select> 
											<span class="text-danger"><?php echo form_error('strand'); ?></span>
										</div>
									</div>
                                    <div class="col-md-2">
                                         <div class="form-group">
                                             <label for="exampleInputEmail1">Term</label>
                                             <select  id="quarter" name="quarter" class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <option value="1" <?php if($quarter == '1' || $quarter == '1st Term') echo "selected"; ?>>Term 1</option>
                                                <option value="2" <?php if($quarter == '2' || $quarter == '2nd Term') echo "selected"; ?>>Term 2</option>
                                                <option value="3" <?php if($quarter == '3' || $quarter == '3rd Term') echo "selected"; ?>>Term 3</option>
                                                <option value="final" <?php if($quarter == 'final') echo "selected"; ?>> Final Grade</option>
                                             </select>
                                             <span class="text-danger"><?php echo form_error('quarter'); ?></span>
                                         </div>
                                     </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" name="search" value="search" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                            </div>
                        </form>
                        <?php
						$show_batch = false;
						if (isset($studentlist)) {
							?>
							<form action="<?php echo site_url('registrar/certificate/print_certificate_batch') ?>" id="confirmcertificatebatchform" name="confirmcertificatebatchform" method="post" accept-charset="utf-8">
								<div class="table-responsive mailbox-messages">
									<div class="box box-info" id="attendencelist">
										<div class="box-header ptbnull" >
											<h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('student'); ?> List</h3>
										</div>
										<div class="box-body table-responsive">
											<?php
											if (!empty($studentlist)) {
												?>
												<div class="mailbox-controls">
													<div class="pull-right">
													</div>
												</div>

												<table class="table table-hover table-striped table-bordered xyz example">
													<thead>
														<tr>
															<th>#</th>
															<th>Rank</th>
															<?php
															if( empty($section_id) ){
																?>
																<th>Grade</th>
																<th>Section</th>
																<?php
															}?>
															<th>Last Name</th>
															<th>First Name</th>
															<th>Middle Name</th>
															<th>Subject Grade</th>  
															<th>Grade</th>  
															<th></th>  
														</tr>
													</thead>
													<tbody>
													<?php if (empty($studentlist)) {
															?>
															<tr>
																<td colspan="32" class="text-danger text-center"><?php echo $this->lang->line('no_record_found'); ?></td>

															</tr>
															<?php
														} else {
															$row_count = 1;
															foreach ($studentlist as  $key => $student_value) { 
                                                                if (!is_array($student_value)) continue;
																$student_id = $student_value["id"];
																$url = base_url().'admin/report/show_report_card/'.$student_id; 
																$student_final_grade = $student_value['final_grade'];
																$student_final_grade = number_format( $student_final_grade,'2','.',',');
																$background_color =  isset($student_value["background_color"])?$student_value["background_color"]:'';
																$text_color = isset($student_value["text_color"])?$student_value["text_color"]:'';
																$name = isset($student_value["name"])?$student_value["name"]:'';
																$firstname = !empty($student_value['firstname'])?strtoupper($student_value['firstname']):''; 
																$lastname = !empty($student_value['lastname'])?strtoupper($student_value['lastname']):''; 
																$suffix = !empty($student_value['suffix'])?strtoupper($student_value['suffix']):''; 
																$middlename = !empty($student_value['middlename'])?strtoupper($student_value['middlename'][0]).'.':''; 
																$strand_id = $student_value['strand_id'];
																// $list_of_grades =  $this->examresult_model->get_student_exam_result_by_quarter( $student_id, $quarter);
																// printx($list_of_grades);
																?>
																<tr>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $row_count; ?></td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $name; ?></td>
																	<?php
																	if( empty($section_id) ){
																		?>
																		<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $student_value['class']; ?></td>
																		<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo$student_value['section']; ?></td>
																		<?php
																	}
																	?>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $lastname; ?></td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $firstname.' '.$suffix; ?></td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $middlename; ?></td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" > 
																		<?php
																		$gradingsettings = $this->gradingsetting_model->getbySession();
																		$decimal_grades = isset($gradingsettings->decimal_grades)?$gradingsettings->decimal_grades:0;
																		$decimal_average = isset($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;
																		$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:2;
																		$if_gradeisnull = isset($gradingsettings->if_gradeisnull)?$gradingsettings->if_gradeisnull:'blank';
																		$allow_to_pass = isset($gradingsettings->allow_to_pass)?$gradingsettings->allow_to_pass:'no';
																		$combine_category = 'ga';			
																		
																		if( $semester ){
																			// if($list_of_grades){
																			// 	foreach ($list_of_grades as $list_of_grades_key => $list_of_grades_value) {
																			// 		$insert_subject_name = isset($list_of_grades_value["subject_name"])?$list_of_grades_value["subject_name"]:0;
																			// 		$insert_grade = isset($list_of_grades_value["grades"])?$list_of_grades_value["grades"]:0;
																			// 		// var_dump($insert_grade); herndev
																			// 		echo ucfirst($insert_subject_name).' - '.$insert_grade.'<br/>'; 
																			// 	}
																			// }
																			 
																			if( $semester == 1 ){
																				$get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array();	
																				$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
																				$total_quarter = count( $get_quarter );
																			} else {
																				$get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();	
																				$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
																				$total_quarter = count( $get_quarter );
																			}
																			$check_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester );
																			if( $check_strand ){
																				$strand_id = $check_strand['strand_id'];
																			}
																			$getStudentSubject = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester );
																			$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id, $semester );
																			if( $custom_subject ){
																				$getStudentSubject = array_merge( $getStudentSubject, $custom_subject );
																			}
																			$getStudentSubject = $this->customsubject_model->unsetsubjectdrop( $getStudentSubject, $student_id, $semester );
																			if( $getStudentSubject ){
																				if( $quarter == 'final'){
																					foreach( $getStudentSubject as $listsubject => $subjects ){
																						$get_final_grade = 0;
																						$subject_id = $subjects['subject_id'];
																						$subject_name = $subjects['name'];
																						$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
																						$display_card = $subject_manager_details['display_card'];
																						$align_right = $subject_manager_details['align_right'];
																						if( $display_card == 'yes' ){
																							for( $qtr=0;$qtr<$total_quarter;$qtr++ ) {
																								$set_quarter = $get_quarter[$qtr];
																								$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $set_quarter, $combine_category );
																								
																								$get_final_grade = $get_final_grade + $getComputedCombinedGrade;
																							}
																						
																							$final_grade = $get_final_grade / $total_quarter;
																							$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id );
																							if( empty( $checkifChild ) ){
																								if( $allow_to_pass == 'yes'){
																									if( $final_grade == '74.5'){
																										$final_grade = '75';
																									} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
																										$final_grade = '75';
																									}
																								}
																							}
																							$final_grade =  number_format((float)$final_grade, $decimal_finalgrade, '.', '');

																							if( $align_right == 'yes' ){
																								echo '&nbsp;&nbsp;<i>'.$subject_name.' - <b>'.$final_grade.'</b></i><br/>'; 
																							} else {
																								echo $subject_name.' - <b>'.$final_grade.'</b><br/>'; 
																							}
																						}
																					}
																				} else {

																					foreach( $getStudentSubject as $listsubject => $subjects ){
																						// var_dump($subjects);
																						$subject_id = $subjects['subject_id'];
																						$subject_name = $subjects['name'];
																						$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
																						$display_card = $subject_manager_details['display_card'];
																						$align_right = $subject_manager_details['align_right'];
																						if( $display_card == 'yes' ){
																							$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category );
																							// printx($getComputedCombinedGrade);
																							
																							if( $align_right == 'yes' ){
																								if( $getComputedCombinedGrade == null || $getComputedCombinedGrade == 0  ){
																									if( $if_gradeisnull == 'blank'){
																										echo '&nbsp;&nbsp;<i>'.$subject_name.' - <b></b></i><br/>'; 
																									} else {
																										echo '&nbsp;&nbsp;<i>'.$subject_name.' - <b>'.number_format((float)0, $decimal_grades, '.', '').'</b></i><br/>'; 
																									}
																								} else {
																									echo '&nbsp;&nbsp;<i>'.$subject_name.' - <b>'.$getComputedCombinedGrade.'</b></i><br/>'; 
																								}
																							} else {
																								if( $getComputedCombinedGrade == null || $getComputedCombinedGrade == 0  ){
																									if( $if_gradeisnull == 'blank'){
																										echo $subject_name.' - <b></b><br/>'; 
																									} else {
																										echo $subject_name.' - <b>'.number_format((float)0, $decimal_grades, '.', '').'</b><br/>'; 
																									}
																								} else {
																									echo $subject_name.' - <b>'.$getComputedCombinedGrade.'</b><br/>'; 
																								}
																							}
																						} 
																					}
																				}
																			}
																		} else {
																			$getStudentSubject = $this->subject_model->getSubjctByClass( $class_id );
																			// printx($getStudentSubject);
																			if( $getStudentSubject){
																				$total_quarter = 4;
																				if( $quarter == 'final'){ 
																					foreach( $getStudentSubject as $listsubject => $subjects ){
																						$get_final_grade = 0;
																						$subject_id = $subjects['id'];
																						$subject_name = $subjects['name'];
																						$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
																						$display_card = $subject_manager_details['display_card'];
																						$align_right = $subject_manager_details['align_right'];
																						if( $display_card == 'yes' ){
																							for($xy=1;$xy<=$total_quarter;$xy++){
																								$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $xy, $combine_category );
																								$get_final_grade = $get_final_grade + $getComputedCombinedGrade;
																							}
																							$final_grade = $get_final_grade / $total_quarter;
																							$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id );
																							if( empty( $checkifChild ) ){
																								if( $allow_to_pass == 'yes'){
																									if( $final_grade == '74.5'){
																										$final_grade = '75';
																									} elseif(  $final_grade > '74.4' && $final_grade <  75 ){
																										$final_grade = '75';
																									}
																								}
																							}
																							$final_grade =  number_format((float)$final_grade, $decimal_finalgrade, '.', '');
																							if( $display_card == 'yes' ){
																								if( $align_right == 'yes' ){
																									echo '&nbsp;&nbsp;<i>'.$subject_name.' - <b>'.$final_grade.'</b></i><br/>'; 
																								} else {
																									echo $subject_name.' - <b>'.$final_grade.'</b><br/>'; 
																								}
																							}
																						} 
																					}
																				} else {
																					foreach( $getStudentSubject as $listsubject => $subjects ){
																						$subject_id = $subjects['id'];
																						$subject_name = $subjects['name'];
																						$subject_manager_details = $this->subjectmanager_model->getBySubjectSession( $subject_id );
																						$display_card = $subject_manager_details['display_card'];
																						$align_right = $subject_manager_details['align_right'];
																						if( $display_card == 'yes' ){
																							$getComputedCombinedGrade = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category );
																							if( $align_right == 'yes' ){
																								if( $getComputedCombinedGrade == null || $getComputedCombinedGrade == 0  ){
																									if( $if_gradeisnull == 'blank'){
																										echo '&nbsp;&nbsp;<i>'.$subject_name.' - <b></b></i><br/>'; 
																									} else {
																										echo '&nbsp;&nbsp;<i>'.$subject_name.' - <b>'.number_format((float)0, $decimal_grades, '.', '').'</b></i><br/>'; 
																									}
																								} else {
																									echo '&nbsp;&nbsp;<i>'.$subject_name.' - <b>'.$getComputedCombinedGrade.'</b></i><br/>'; 
																								}
																							} else {
																								if( $getComputedCombinedGrade == null || $getComputedCombinedGrade == 0  ){
																									if( $if_gradeisnull == 'blank'){
																										echo $subject_name.' - <b></b><br/>'; 
																									} else {
																										echo $subject_name.' - <b>'.number_format((float)0, $decimal_grades, '.', '').'</b><br/>'; 
																									}
																								} else {
																									echo $subject_name.' - <b>'.$getComputedCombinedGrade.'</b><br/>'; 
																								}
																							}
																						} 
																					}
																				}
																			}
																		}
																		?> 	
																	</td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $student_final_grade; ?>	</td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" >
																

																
																	<?php 
																	if( $name ){
																		$show_batch = true;
																		?>
																		<input type="hidden" name="batch_id[]" value="<?php echo $student_id;?>" />
																		<input type="hidden" name="awards[]"  value="<?php echo $name;?>"  />
																		<input type="hidden" name="grade[]"  value="<?php echo $student_final_grade;?>"  />
																		<input type="hidden" name="class_id"  value="<?php echo $class_id;?>"  />
																		<input type="hidden" name="section_id"  value="<?php echo $section_id;?>"  />
																		<input type="hidden" name="quarter"  value="<?php echo $quarter;?>"  />
																		<button class="btn btn-default btn-xs confirm_student" title="Create Certificate" data-toggle="tooltip" data-id="<?php echo $student_id;?>" data-awards="<?php echo $name;?>" data-grade="<?php echo $student_final_grade;?>" data-fullname="<?php echo $lastname.", ".$firstname.' '.$suffix.' '.$middlename;?>" data-signature = 'no' onclick="confirm_student( this )"><i class="fa fa-print"></i> Create</button>
																		<!-- <button class="btn btn-success btn-xs confirm_student" title="Create Certificate" data-toggle="tooltip" data-id="<?php echo $student_id;?>" data-awards="<?php echo $name;?>" data-grade="<?php echo $student_final_grade;?>" data-fullname="<?php echo $lastname.", ".$firstname.' '.$suffix.' '.$middlename;?>" data-signature = 'yes' onclick="confirm_student( this )"><i class="fa fa-print"></i> Create with Sig.</button> -->
																	</td>
																		<?php 
																	}?>
																	</td>
																</tr>
																<?php
																$row_count++;
															}
														}
														?>
													</tbody>
												</table>
												<?php
											} else {
												?>
												<div class="alert alert-info">
													No data to show.
												</div>
												<?php
											}
											?>
										</div>
									</div>
								</div>
								<?php 
								if( $show_batch ){
									?>
									<div class="col-md-12 text-right">
										<button class="btn btn-primary btn-sm .confirm_certificate_batch" title="Create Certificate" data-signature = 'no' onclick="confirm_student_batch( this )"><i class="fa fa-print"></i> Create Batch Certificate</button></td>
										<!-- <button class="btn btn-success btn-sm .confirm_certificate_batch" title="Create Certificate" data-signature = 'yes' onclick="confirm_student_batch( this )" ><i class="fa fa-print"></i> Create Batch Certificate with Sig</button></td> -->
									</div>
								   <?php 
								}
								?>
								 <div id="confirm_certificate_batch" class="modal fade confirm_certificate_batch" role="dialog">
									<div class="modal-dialog">
										<!-- Modal content-->
										<div class="modal-content">
											<div class="modal-body">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title">Confirm Certificate</h4>
												</div>
												<div class="modal-body">
													<div class="row">
														<div class="form-group" style="margin-bottom: 10px;">
															<label class="col-sm-4">Date</label>
															<div class="col-sm-8">
																<input type="date" id="award_date" name="award_date" placeholder="Date" required="required" class="form-control" > 
															</div> 
														</div> 
													</div> 
												</div>
												<div class="modal-footer">
													<input type="hidden" name="with_sig" />
													<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
													<input type="submit" class="btn btn-success" value="Save" >   
												</div>
											</div>
										</div>
									</div>
								</div>   
							</form>
							<?php
							}
						?>
                    </div>
                </div>
            </div>
        </div> 
    </section>
</div>
 <div id="confirm_certificate" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form class="modal_confirm_certificate" action="<?php echo site_url('registrar/certificate/print_certificate') ?>"  id="confirmcertificateform" name="confirmcertificateform" method="post" accept-charset="utf-8">
                <div class="modal-body">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Confirm Certificate</h4>
                    </div>
                    <div class="modal-body">
						<div class="row">
							<div class="form-group" style="margin-bottom: 10px;">
								<label class="col-sm-4">Student Name</label>
								<div class="col-sm-8">
									<input type="hidden" id="student_id"  name="student_id" class="form-control" readonly> 
									<input type="text" id="student_name" name="student_name" placeholder="Student Name" class="form-control" readonly > 
								</div>
							</div> 
							<div class="form-group" style="margin-bottom: 10px;">
								<label class="col-sm-4">Award</label>
								<div class="col-sm-8">
									<input type="hidden" id="award_id" name="award_id"  class="form-control" readonly > 
									<input type="text" id="award_name" name="award_name" placeholder="Award" class="form-control" > 
								</div>
							</div>
							<div class="form-group" style="margin-bottom: 10px;">
								<label class="col-sm-4">Grade</label>
								<div class="col-sm-8">
									<input type="text" id="quarter_student_grade" name="grade" required="required" class="form-control" >
								</div>
							</div>
							<div class="form-group" style="margin-bottom: 10px;">
								<label class="col-sm-4">Date</label>
								<div class="col-sm-8">
									<input type="date" id="award_date" name="award_date" placeholder="Date" required="required" class="form-control" > 
									<input type="hidden" id="quarter_id" name="quarter_id" required="required" class="form-control" > 
									<!--<input type="hidden" id="quarter_student_grade" name="grade" required="required" class="form-control" > --> 
								</div> 
							</div> 
						</div>
                    </div>
                    <div class="modal-footer">
                    	<input type="hidden" name="with_sig" id="with_sig"/>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-success" value="Save" >   
                    </div>
                </div>
            </form> 
        </div>
    </div>
</div>   
   

  <script type="text/javascript">
                $(document).ready(function () {
                    var section_id_post = '<?php echo $section_id; ?>';
                    var class_id_post = '<?php echo $class_id; ?>';
                    populateSection(section_id_post, class_id_post);
                    function populateSection(section_id_post, class_id_post) {
                        $('#section_id').html("");
                        var base_url = '<?php echo base_url() ?>';
                        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
                        $.ajax({
                            type: "GET",
                            url: base_url + "/registrar/sections/getByClass",
                            data: {'class_id': class_id_post},
                            dataType: "json",
                            success: function (data) {
                                $.each(data, function (i, obj)
                                {
                                    var select = "";
                                    if (section_id_post == obj.section_id) {
                                        var select = "selected=selected"; 
                                    }
                                    div_data += "<option value=" + obj.section_id + " " + select + ">" + obj.section + "</option>";
                                });
                                $('#section_id').append(div_data);
                            }
                        });
						$.ajax({
							type: "GET",
							url: base_url + "registrar/strand/allowStrand",
							data: {'class_id': class_id_post},
							dataType: "json",
							success: function (data) {
								if( data == true ){
									$('#strand').css('display','block');
									$('#select_strand').attr('required',true);
								} else {
									$('#strand').css('display','none');
									$('#select_strand').val("");
									$('#select_strand').attr('required',false)
								}
							}
						});
                    }

                    $(document).on('change', '#class_id', function (e) {
                        $('#section_id').html("");
                        var class_id = $(this).val();
                        var base_url = '<?php echo base_url() ?>';
                        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
                        $.ajax({
                            type: "GET",
                            url: base_url + "registrar/sections/getByClass",
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
						$.ajax({
							type: "GET",
							url: base_url + "registrar/strand/allowStrand",
							data: {'class_id': class_id},
							dataType: "json",
							success: function (data) {
								if( data == true ){
									$('#strand').css('display','block');
									$('#select_strand').attr('required',true);
								} else {
									$('#strand').css('display','none');
									$('#select_strand').val("");
									$('#select_strand').attr('required',false)
								}
							}
						});
                    });
                    var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
                    $('#date').datepicker({
                        format: date_format,
                        autoclose: true
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
                    frame1.css({"position": "absolute", "top": "-1000000px"});
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
			<script type="text/javascript">
				function confirm_student( obj ){
					var base_url = '<?php echo base_url() ?>';
					var quarter_id = '<?php echo $quarter; ?>';
					var date_today = '<?php echo date('m/d/Y') ?>';
					var student_id = $( obj ).attr( "data-id" );
					var student_fullname = $( obj ).attr( "data-fullname" );
					var student_grade = $( obj ).attr( "data-grade" );
					var awards = $( obj ).attr( "data-awards" );
					var signature = $( obj ).attr( "data-signature" );
					
					$( "#confirm_certificate input#award_name" ).val(  awards );     
					$( "#confirm_certificate input#student_id" ).val( student_id );  
					$( "#confirm_certificate input#student_name" ).val( student_fullname );  
					$( "#confirm_certificate input#award_id" ).val( awards );   
					$( "#confirm_certificate input#award_date" ).val( date_today );   
					$( "#confirm_certificate input#quarter_id" ).val( quarter_id );   
					$( "#confirm_certificate input#quarter_student_grade" ).val( student_grade );  
					$( "#confirm_certificate input#with_sig" ).val( signature );    
					$('#confirm_certificate').modal('show');
				}

				function confirm_student_batch( obj ){
					var base_url = '<?php echo base_url() ?>';
					var signature = $( obj ).attr( "data-signature" );
					$( "#confirm_certificate_batch input[name=with_sig]" ).val( signature );    
					$('#confirm_certificate_batch').modal('show');
				}

				$('#confirmcertificateform').submit(function() {

					// submission stuff

					$('#confirm_certificate').modal('hide'); 

					window.location.reload();
				});
				$('#confirmcertificatebatchform').submit(function() {

					// submission stuff

					window.location.reload();
				});
				var base_url = '<?php echo base_url() ?>';
				// function printDiv(elem) {       
				// 	Popup(jQuery(elem).html());
				// }
			</script>