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

        <h1> <i class=" "></i> Master Sheet<small></h1>

    </section>   

    <section class="content">

        <div class="row">          

            <div class="col-md-12">              

                                

                <div class="box box-primary" id="sublist"> 

                     <div class="box-header with-border">

                        <h3 class="box-title">Select Student by Grade and Section</h3>

                    </div> 

                    <div class="box-body">

                        <form id='form1' action="<?php echo site_url('teacher/report/master_sheet') ?>"  method="post" accept-charset="utf-8">

                            <div class="box-body">

                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="row">

                                <div class="col-sm-2">

                            		<label><?php echo $this->lang->line('session'); ?></label>

                            		<select  id="session_id" name="session_id" class="form-control" require>

										<option value=""><?php echo $this->lang->line('select'); ?></option>

										<?php

										foreach ($sessionlist as $session) {

											if($session['id'] == $previous || $session['id']  == $current_session_id ){

											?>

											<option value="<?php echo $session['id'] ?>"<?php echo $session['id'] == $session_id?"selected=selected":"" ?>><?php echo $session['session'] ?></option>



											<?php

											}

										}

										?>

									</select>					

									<span class="text-danger"><?php echo form_error('session_id'); ?></span>

                            	</div>

                                    <div class="col-md-2">

                                        <div class="form-group">

                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('class'); ?></label>

                                            <select  id="class_id" name="class_id" class="form-control" >

                                                <option value=""><?php echo $this->lang->line('select'); ?></option>

                                                <?php

                                                /*foreach ($classlist as $class) {

                                                    ?>

                                                    <option value="<?php echo $class['id'] ?>" <?php

                                                    if ($class_id == $class['id']) {

                                                        echo "selected =selected";

                                                    }

                                                    ?>><?php echo $class['class'] ?></option>

                                                            <?php

                                                            $count++;

                                                        }*/

                                                        ?>

                                            </select>

                                            <span class="text-danger"><?php echo form_error('class_id'); ?></span>

                                        </div>

                                    </div>

                                    <div class="col-md-3">

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

											<select class="form-control" name="semester_id" id="select_strand">

												<option value=""><?php echo $this->lang->line('select'); ?></option>

												<?php

													 foreach ($getSemester as  $value => $key) {

													?>

														<option value="<?php echo $value; ?>" <?php echo $semester_id==$value?"selected":'';?>><?php echo $key; ?></option>

													<?php

													} 

												?>

											</select> 

											<span class="text-danger"><?php echo form_error('strand'); ?></span>

										</div>

									</div> 

                                    <div class="col-md-2">

                                        <div class="form-group">

                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('quarter'); ?></label>

                                            <select  id="quarter" name="quarter" class="form-control" >

                                               <option value=""><?php echo $this->lang->line('select'); ?></option>

                                                <?php

                                                    foreach ($getquarter as $key => $value) {

                                                    ?>

                                                    <option  value="<?php echo $key; ?>" <?php if($quarter == $value) echo "selected"; ?>><?php echo $value; ?></option>

                                                    <?php

                                                    }

                                                ?>

                                                 <option  value="final" <?php if($quarter == 'final') echo "selected"; ?>>Final</option>

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

                        <div class="table-responsive mailbox-messages">

                 <?php

                if (isset($studentlist)) {

                    ?>

                    <div class="box box-info" id="attendencelist">

                        <div class="box-header ptbnull" >

                            <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('student'); ?> List</h3>

                            <div>

                               <form id='form1mastersheet' class=" pull-right" action="<?php echo site_url('teacher/report/master_sheet') ?>"  method="post" accept-charset="utf-8">

                                    <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">

                                    <input type="hidden" name="section_id" value="<?php echo $section_id; ?>"> 

                                    <input type="hidden" name="session_id" value="<?php echo $session_id; ?>"> 

                                    <input type="hidden" name="quarter" value="<?php echo $quarter; ?>">

                                    <input type="hidden" name="semester_id" value="<?php echo $semester_id; ?>">

                                    <input type="hidden" name="action" value="print_master_sheet">

									<!-- <button type="button" id="printsf5" class="btn btn-primary btn-sm" style="margin-top: 3px;margin-bottom: -5px" >PRINT DEPED SF5</button> -->

                                    <button type="submit" id="printsms" class="btn btn-primary btn-sm" style="margin-top: 3px;margin-bottom: -5px">PRINT MASTER SHEET</button>

                                </form> 

                            </div>

                        </div>

							<div class="box-body table-responsive">

							<?php 

							$row_count = 0;

							if( $enableStrand ){

								?>

								<table class="table table-hover table-striped table-bordered xyz example">

									<?php 

									$gradingsettings = $this->gradingsetting_model->getbySession( $session_id ); 

									if( $semester_id == 1 ){

										$get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array(); 

									} else {

										$get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();   

									}

									$if_gradeisnull = isset($gradingsettings->if_gradeisnull)?$gradingsettings->if_gradeisnull:'blank';

									$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;

									$sql_quarter = $this->student_model->sql_array_to_string( $get_quarter );  

									$total_quarter = count($get_quarter); 

									$getsubjectlist = array();

									$getstrandlist = array();

									$getsubjectperstrand = array();

									?>

									<thead>

										<tr> 

											<th class="sorting_disabled">Student Name</th> 

											<?php

											$xy=0;

											foreach ($studentlist as  $student_value) {

												$dont_check = TRUE;

												$student_id = $student_value["id"]; 

												$strand_id = $student_value["strand_id"];

												$check_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id, $session_id );

												if( $check_strand ){

													$strand_id = $check_strand['strand_id'];

												}

												

												$subjectlist = $this->specializedsubject_model->getByStrandSemester( $strand_id, $semester_id, $session_id );

												$custom_subject = $this->customsubject_model->getSubjectbySemester( $student_id , $semester_id, $session_id );

												if( $custom_subject ){

													foreach( $custom_subject as $key => $value ){

														$get_cs_id = $value['subject_id'];

														$custom_subject_student[ $get_cs_id ][] = $student_id;

													}

													$dont_check = FALSE;

													$subjectlist = array_merge( $subjectlist, $custom_subject );

												}

												if(!in_array( $strand_id, $getstrandlist) || $dont_check == FALSE ){

													$getstrandlist[] = $strand_id;

													if( $subjectlist ){

														foreach ($subjectlist as $subject_key => $subject_value) { 

															$subject_id = $subject_value['subject_id'];

															$sm_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );

															$display_card = $sm_details['display_card'];

															$include_computation = $sm_details['include_computation'];

															$getsubjectperstrand[ $strand_id ][] = $subject_id;

															if(!in_array( $subject_id, $getsubjectlist )){

																$getsubjectlist[] = $subject_id;

																if( $display_card == 'yes'){

																	$xy++;

																	echo "<th>".$subject_value['name']."</th>";	

																}

																

															}

															

														}

													}

												}

												

											

											}

											if( $quarter != 'final'){

												echo "<th>Conduct</th>";

											}

											 echo "<th>Gen Ave</th>";

											?>

										</tr>

									</thead>

									<tbody>

									

									<?php

									if (!empty( $studentlist_male )) { 

										?>

										<tr>

											<th class="mailbox-name"><b>Male</b></th>

											<?php 

											for( $jo=1;$jo<=$xy;$jo++){

												?>

												<th class="mailbox-name"></th>

												<?php

											}

											?>

											<th class="mailbox-name"></th>

											<?php 

											if( $quarter != 'final'){

												?>

												<th class="mailbox-name"></th>

												<?php 

											}

											?>

										<tr>

										<?php

										foreach ($studentlist_male as  $student_value) {

											$total_grade = 0;

											$total_subject_list = 0;

											$count_subject = 0;

											$display_total_grade = 0;

											$student_id = $student_value["id"]; 

											$lastname = $student_value['lastname'];

											$firstname = $student_value['firstname'];

											$suffix = $student_value['suffix'];

											$middlename = !empty($student_value['middlename'])?$student_value['middlename'][0].'.':'';

											$strand_id = $student_value["strand_id"];

											$check_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id, $session_id );

											if( $check_strand ){

												$strand_id = $check_strand['strand_id'];

											} 

											//if( $strand_id ){

												?>

												<td class="tdclsname" style="text-transform:uppercase;"><?php echo $lastname.', '.$firstname.' '.$suffix.' '.$middlename; ?></td> 

												<?php

												

												foreach ($getsubjectlist as $subject_value	) {

													$complete_first = true;

													$complete_second = true;

													$complete_third = true;

													$complete_fourth = true;

													$complete_grades =true;

													$complete_quarter =true;

													$subject_id = $subject_value;

													$sm_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );

													$display_card = $sm_details['display_card'];

													$include_computation = $sm_details['include_computation'];

													$units = !empty($sm_details['units'])?$sm_details['units']:1;

													if(isset( $getsubjectperstrand[$strand_id])){

														if(in_array( $subject_id,$getsubjectperstrand[$strand_id])){

															if( isset($custom_subject_student[ $subject_id ]) && !in_array( $student_id, $custom_subject_student[ $subject_id ] )){

																if( $display_card == 'yes'){

																	echo "<th>N/A</th>";

																}

															} else {

																$checkifdrop = $this->customsubject_model->getStudentSubjectSemesterDrop($student_id, $subject_id, $semester_id, $session_id );

																if( count($checkifdrop) == 0 ){ 

																		if( $quarter == "final"){

																			 $total_current_grade = 0;

																			 for ($q_i=0; $q_i < $total_quarter ; $q_i++) { 

																				$set_quarter = $get_quarter[$q_i];

																				$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $set_quarter, $combine_category, $session_id );

																				$total_current_grade +=  $grade_per_quarter; 

																				if( $set_quarter == 1 ){

																					if( empty( $grade_per_quarter ) && $include_computation == 'yes'){

																						$complete_first = false;

																					}

																				}

																				if( $set_quarter == 2 ){

																					if( empty( $grade_per_quarter ) && $include_computation == 'yes'){

																						$complete_second = false;

																					}

																				}

																				if( $set_quarter == 3 ){

																					if( empty( $grade_per_quarter ) && $include_computation == 'yes'){

																						$complete_third = false;

																					}

																				}

																				if( $set_quarter == 4 ){

																					if( empty( $grade_per_quarter ) && $include_computation == 'yes'){

																						$complete_fourth = false;

																					}

																				}

																			}



																			$display_grade =  $total_current_grade / $total_quarter;

																			if( $include_computation == 'yes' ){ 

																				$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );

																			}

																			

																			$display_grade =number_format( $display_grade, $decimal_finalgrade,".",".");

																			if( $display_card == 'yes'){

																				if( ( $complete_first && $complete_second ) || ( $complete_third && $complete_fourth ) ){

																					if( $display_grade == null || $display_grade == 0 ){

																						$complete_grades = false;

																						if( $if_gradeisnull == 'blank'){

																							echo "<th></th>";

																						} else {

																							echo "<th style=\"color:red\">".$display_grade."</th>";

																						}

																					} else {

																						if( $display_grade < 75 ){

																							echo "<th style=\"color:red\">".$display_grade."</th>";

																						} else {

																							echo "<th>".$display_grade."</th>";

																						}

																					}

																				} else {

																					echo "<th></th>";

																					$complete_grades = false;	

																				}

																			}

																							

																			if( $include_computation == 'yes'){

																				$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );

																				if( empty($checkifChild)){

																					$display_grade = $display_grade * $units;

																					$total_grade += $display_grade;

																					$total_subject_list = $total_subject_list + $units;

																				}

																			}

																		} else { 

																			$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id );

																			if( $include_computation == 'yes'){

																				if( $grade_per_quarter == null ){

																					$complete_grades = false;

																					$complete_quarter = false;

																				}

																				$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category , $session_id );

																				if( empty($checkifChild)){

																					$grade_per_quarter_with_unit = $grade_per_quarter * $units;

																					$display_total_grade = $display_total_grade + $grade_per_quarter_with_unit;

																					$count_subject = $count_subject + $units;

																				}

																			}

																			if( $display_card == 'yes'){

																				if( $grade_per_quarter == null || $grade_per_quarter == 0  ){

																					if( $if_gradeisnull == 'blank'){

																						?>

																						<th></th>

																						<?php

																					}  else {

																						?>

																						<th><?php echo number_format(0, $decimal_grades, '.', '');?></th>

																						<?php

																					}

																				} else {

																					if( $grade_per_quarter < 75 ){

																						?>

																						<th style="color:red;"><?php echo $grade_per_quarter;?></th>

																						<?php

																					} else {

																						?>

																						<th><?php echo $grade_per_quarter;?></th>

																						<?php

																					}

																				}

																			}

																		}

																} else {

																	if( $display_card == 'yes'){

																	?>

																	<th>N/A</th>

																	<?php

																	}

																}

															}

														} else {

															if( $display_card == 'yes'){

															?>

															<th>N/A</th>

															<?php

															}

														}

													} else {

														if( $display_card == 'yes'){

														?>

														<th>N/A</th>

														<?php

														}

													}

												}

												

											if( $quarter != "final"){

												echo "<th>";

												$student_conduct = '';

												$getConductSubject = $this->conductsession_model->getDetailByclassAndSection( $class_id, $section_id, $session_id );

												if( $getConductSubject ){

													foreach( $getConductSubject as $getConduct => $conduct){

															$conduct_id = $conduct['id'];

															$conduct_details = $this->conductimportbatchdetail_model->getAllDetailsConductByStudentPerQuarter( $conduct_id, $student_id, $class_id, $section_id, $quarter, $session_id );

															if( !empty( $conduct_details ) && $conduct_details ){

																$student_conduct .= $conduct_details['conduct'];

																echo $student_conduct;

															}

													}

												}

												echo "</th>";

											}

											if( $quarter == "final"){

												$display_total_grade =  $total_subject_list != 0 ? $total_grade / $total_subject_list:'';

												$display_total_grade =number_format( (float)$display_total_grade,$decimal_average,".",".");

												if( $complete_grades ){

													if( $display_total_grade == null || $display_total_grade == 0 || $display_total_grade == 0.00 ){

														if( $if_gradeisnull == 'blank'){

															echo "<th></th>";

														} else {

															echo "<th style=\"color:red;\">".$display_total_grade."</th>";

														} 

													} else {

														if( $display_total_grade < 75  ){

															echo "<th style=\"color:red;\">".$display_total_grade."</th>";

														} else {

															echo "<th>".$display_total_grade."</th>";

														}

													}

													

												} else {

													echo "<th> </th>";

												}

											} else {

												$average_grade =  $count_subject != 0?$display_total_grade/$count_subject:0;

												$average_grade =  number_format($average_grade, $decimal_average, '.', '');

												if( $complete_quarter ){

													if( $average_grade == null || $average_grade == 0 || $average_grade == 0.00 ){

														if( $if_gradeisnull == 'blank'){

															echo "<th></th>";

														} else {

															echo "<th style=\"color:red;\">".$average_grade."</th>";

														} 

													} else {

														if( $average_grade < 75  ){

															echo "<th style=\"color:red;\">".$average_grade."</th>";

														} else {

															echo "<th>".$average_grade."</th>";

														}

													}

												} else {

													echo "<th> </th>";

												}

											}

											?>

											</tr>

											<?php

											$row_count++;

											}

										//}

									}

									?> 

									<?php

								if (!empty( $studentlist_female )) { 

										?>

										<tr>

											<th class="mailbox-name"><b>Female</b></th>

											<?php 

											for( $jo=1;$jo<=$xy;$jo++){

												?>

												<th class="mailbox-name"></th>

												<?php

											}

											?>

											<th class="mailbox-name"></th>

											<?php 

											if( $quarter != 'final'){

												?>

												<th class="mailbox-name"></th>

												<?php 

											}

											?>

										<tr>

										<?php

										foreach ($studentlist_female as  $student_value) { 

											$total_grade = 0;

											$total_subject_list = 0;

											$count_subject = 0;

											$display_total_grade = 0;

											$student_id = $student_value["id"]; 

											$lastname = $student_value['lastname'];

											$firstname = $student_value['firstname'];

											$suffix = $student_value['suffix'];

											$middlename = !empty($student_value['middlename'])?$student_value['middlename'][0].'.':'';

											$strand_id = $student_value["strand_id"];

											$check_strand = $this->studentstrand_model->get_latest_strand( $student_id, $semester_id, $session_id );

											if( $check_strand ){

												$strand_id = $check_strand['strand_id'];

											} 

											//if( $strand_id ){

												?>

												<td class="tdclsname" style="text-transform:uppercase;"><?php echo $lastname.', '.$firstname.' '.$suffix.' '.$middlename; ?></td> 

												<?php

												

												foreach ($getsubjectlist as $subject_value	) {

													$complete_first = true;

													$complete_second = true;

													$complete_third = true;

													$complete_fourth = true;

													$complete_grades =true;

													$complete_quarter =true;

													$subject_id = $subject_value;

													$sm_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );

													$display_card = $sm_details['display_card'];

													$include_computation = $sm_details['include_computation'];

													$units = !empty($sm_details['units'])?$sm_details['units']:1;

													if(isset( $getsubjectperstrand[$strand_id])){

														if(in_array( $subject_id,$getsubjectperstrand[$strand_id])){

															if( isset($custom_subject_student[ $subject_id ]) && !in_array( $student_id, $custom_subject_student[ $subject_id ] )){

																if( $display_card == 'yes'){

																	echo "<th>N/A</th>";

																}

															} else {

																$checkifdrop = $this->customsubject_model->getStudentSubjectSemesterDrop($student_id, $subject_id, $semester_id, $session_id );

																if( count($checkifdrop) == 0 ){ 

																		if( $quarter == "final"){

																			 $total_current_grade = 0;

																			 for ($q_i=0; $q_i < $total_quarter ; $q_i++) { 

																				$set_quarter = $get_quarter[$q_i];

																				$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $set_quarter, $combine_category, $session_id );

																				

																				$total_current_grade +=  $grade_per_quarter; 

																				if( $set_quarter == 1 ){

																					if( empty( $grade_per_quarter ) && $include_computation == 'yes'){

																						$complete_first = false;

																					}

																				}

																				if( $set_quarter == 2 ){

																					if( empty( $grade_per_quarter ) && $include_computation == 'yes'){

																						$complete_second = false;

																					}

																				}

																				if( $set_quarter == 3 ){

																					if( empty( $grade_per_quarter ) && $include_computation == 'yes'){

																						$complete_third = false;

																					}

																				}

																				if( $set_quarter == 4 ){

																					if( empty( $grade_per_quarter ) && $include_computation == 'yes'){

																						$complete_fourth = false;

																					}

																				}

																			}



																			$display_grade =  $total_current_grade / $total_quarter;

																			if( $include_computation == 'yes' ){ 

																				$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );

																			}

																			

																			$display_grade =number_format( $display_grade, $decimal_finalgrade,".",".");

																			if( $display_card == 'yes'){

																				if( ( $complete_first && $complete_second ) || ( $complete_third && $complete_fourth ) ){

																					if( $display_grade == null || $display_grade == 0 ){

																						$complete_grades = false;

																						if( $if_gradeisnull == 'blank'){

																							echo "<th></th>";

																						} else {

																							echo "<th>".$display_grade."</th>";

																						}

																					} else {

																						if( $display_grade < 75 ){

																							echo "<th style=\"color:red\">".$display_grade."</th>";

																						} else {

																							echo "<th>".$display_grade."</th>";

																						}

																					}

																				} else {

																					echo "<th></th>";

																					$complete_grades = false;	

																				}

																			}

																							

																			if( $include_computation == 'yes'){

																				$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );

																				if( empty($checkifChild)){

																					$display_grade = $display_grade * $units;

																					$total_grade += $display_grade;

																					$total_subject_list = $total_subject_list + $units;

																				}

																			}

																		} else { 

																			$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id );

																		

																			if( $include_computation == 'yes'){

																				if( $grade_per_quarter == null ){

																					$complete_grades = false;

																					$complete_quarter = false;

																				}

																				$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category , $session_id );

																				if( empty($checkifChild)){

																					$grade_per_quarter_with_unit = $grade_per_quarter * $units;

																					$display_total_grade = $display_total_grade + $grade_per_quarter_with_unit;

																					$count_subject = $count_subject + $units;

																				}

																			}

																			if( $display_card == 'yes'){

																				if( $grade_per_quarter == null || $grade_per_quarter == 0  ){

																					if( $if_gradeisnull == 'blank'){

																						?>

																						<th></th>

																						<?php

																					}  else {

																						?>

																						<th><?php echo number_format(0, $decimal_grades, '.', '');?></th>

																						<?php

																					}

																				} else {

																					if( $grade_per_quarter < 75 ){

																						?>

																						<th style="color:red;"><?php echo $grade_per_quarter;?></th>

																						<?php

																					} else {

																						?>

																						<th><?php echo $grade_per_quarter;?></th>

																						<?php

																					}

																				}

																			}

																		}

																} else {

																	if( $display_card == 'yes'){

																	?>

																	<th>N/A</th>

																	<?php

																	}

																}

															}

														} else {

															if( $display_card == 'yes'){

															?>

															<th>N/A</th>

															<?php

															}

														}

													} else {

														if( $display_card == 'yes'){

														?>

														<td>N/A</td>

														<?php

														}

													}

												}

												

											if( $quarter != "final"){

												echo "<th>";

												$student_conduct = '';

												$getConductSubject = $this->conductsession_model->getDetailByclassAndSection( $class_id, $section_id, $session_id );

												if( $getConductSubject ){

													foreach( $getConductSubject as $getConduct => $conduct){

															$conduct_id = $conduct['id'];

															$conduct_details = $this->conductimportbatchdetail_model->getAllDetailsConductByStudentPerQuarter( $conduct_id, $student_id, $class_id, $section_id, $quarter, $session_id );

															if( !empty( $conduct_details ) && $conduct_details ){

																$student_conduct .= $conduct_details['conduct'];

																echo $student_conduct;

															}

													}

												}

												echo "</th>";

											}

											if( $quarter == "final"){

												$display_total_grade =  $total_subject_list != 0 ? $total_grade / $total_subject_list:'';

												$display_total_grade =number_format( (float)$display_total_grade,$decimal_average,".",".");

												if( $complete_grades ){

													if( $display_total_grade == null || $display_total_grade == 0 || $display_total_grade == 0.00 ){

														if( $if_gradeisnull == 'blank'){

															echo "<th></th>";

														} else {

															echo "<th style=\"color:red;\">".$display_total_grade."</th>";

														} 

													} else {

														if( $display_total_grade < 75  ){

															echo "<th style=\"color:red;\">".$display_total_grade."</th>";

														} else {

															echo "<th>".$display_total_grade."</th>";

														}

													}

												} else {

													echo "<th> </th>";

												}

											} else {

												$average_grade =  $count_subject != 0?$display_total_grade/$count_subject:0;

												$average_grade =  number_format($average_grade, $decimal_average, '.', '');

												if( $complete_quarter ){

													if( $average_grade == null || $average_grade == 0 || $average_grade == 0.00 ){

														if( $if_gradeisnull == 'blank'){

															echo "<th></th>";

														} else {

															echo "<th style=\"color:red;\">".$average_grade."</th>";

														} 

													} else {

														if( $average_grade < 75  ){

															echo "<th style=\"color:red;\">".$average_grade."</th>";

														} else {

															echo "<th>".$average_grade."</th>";

														}

													}

												} else {

													echo "<th> </th>";

												}

											}

											?>

											</tr>

											<?php

											$row_count++;

											}

										//}

									}

									?>

									</tbody>

								</table>

								<?php 

							} else {

									?>

									<table class="table table-hover table-striped table-bordered xyz example">

										<thead>

											<tr> 

												<th class="sorting_disabled">Student Name</th> 

												<?php

												$abc=0;

												if( $subjectlist ){

													foreach ($subjectlist as $subject_key => $subject_value) { 

														$subject_id = $subject_value['id'];

														$sm_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );

														$display_card = $sm_details['display_card'];

														if( $display_card == 'yes'){

															$abc++;

															echo "<th>".$subject_value['name']."</th>";	

														}

													}

												}

												if( $quarter != 'final'){

													echo "<th>Conduct</th>";	

												}

												echo "<th>Gen Ave</th>";

												?>  

											</tr>	

										</thead>

										<tbody>

											<?php 

											if( $studentlist_male ){

												

												?>

												<tr>

													<th class="mailbox-name"><b>Male</b></th>

													<?php 

													for( $jo=1;$jo<=$abc;$jo++){

														?>

														<th class="mailbox-name"></th>

														<?php

													}

													?>

													<th class="mailbox-name"></th>

													<?php 

													if( $quarter != 'final'){

														?>

														<th class="mailbox-name"></th>

														<?php

													}

													?>

												</tr>

												<?php

												foreach ($studentlist_male as  $student_value) { 

													$student_id = $student_value["id"];

													$lastname = $student_value['lastname'];

													$firstname = $student_value['firstname'];

													$suffix = $student_value['suffix'];

													$middlename = !empty($student_value['middlename'])?$student_value['middlename'][0].'.':'';

													$url = base_url().'academichead/report/show_report_card/'.$student_id; 

													?>

													<tr> 

														<td class="tdclsname" style="text-transform:uppercase;"><?php echo $lastname.', '.$firstname.' '.$suffix.' '.$middlename; ?></td> 

														<?php

														$total_grade = 0;

														$total_subject_list = 0;

														$display_total_grade = 0; 

														$average_grade = 0; 

														$count_subject = 0;

														$get_count = 4;

														$last_quarter = 4;

														if( $subjectlist ){

															$complete_grades =true;

															$complete_first =true;

															$complete_second =true;

															$complete_third =true;

															$complete_fourth =true;

															foreach ($subjectlist as $subject_key => $subject_value ) {

																$get_final_grade = 0;

																$subject_id = $subject_value['id'];

																$subject_name = $subject_value['name'];

																$sm_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );

																$display_card = $sm_details['display_card'];

																$include_computation = $sm_details['include_computation'];

																$display_number = $sm_details['display_number'];

																$units = 1;

																if( $quarter == "final"){

																	$complete_grades =true;

																	for($xy=1;$xy<=$last_quarter;$xy++){

																		$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $xy, $combine_category, $session_id );

																		if( $display_number == 'yes'){

																			$get_final_grade = $get_final_grade + $grade_per_quarter;

																		}

																		if( $xy == 1 ){

																			if( $grade_per_quarter == null &&  $include_computation == 'yes' ){

																				$complete_first = false;

																			}

																		}

																		if( $xy == 2 ){

																			if( $grade_per_quarter == null &&  $include_computation == 'yes'  ){

																				$complete_second = false;

																			}

																		}

																		if( $xy == 3 ){

																			if( $grade_per_quarter == null &&  $include_computation == 'yes' ){

																				$complete_third = false;

																			}

																		}

																		if( $xy == 4 ){

																			if( $grade_per_quarter == null &&  $include_computation == 'yes' ){

																				$complete_fourth = false;

																			}

																		}

																	}

																	if( $display_number == 'yes' ){

																		$display_grade = $get_final_grade / $last_quarter;

																	}

																	if( $include_computation == 'yes' ){ 

																		$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );

																	}

																	if( $display_number == 'yes' ){

																		$display_grade = number_format((float)$display_grade, $decimal_finalgrade, '.', '');

																	}

																	if( $display_card == 'yes'){

																		if( $complete_first && $complete_second && $complete_third && $complete_fourth ){

																			if( $display_grade == 0 ){

																				echo "<th></th>";

																			} else {

																				if( $display_grade < 75 ){

																					echo "<th style=\"color:red\">".$display_grade."</th>";

																				} else {

																					echo "<th>".$display_grade."</th>";

																				}

																			

																			}

																		} else {

																			echo "<th></th>";

																			$complete_grades = false;	

																		}

																	}

																	

																	if( $include_computation == 'yes'){

																		$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );

																		if( empty($checkifChild)){

																			$total_grade += $display_grade;

																			 $total_subject_list++;

																		}

																	}

																} else {

																	$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id );

																	if( $include_computation == 'yes'){

																		if( $grade_per_quarter == null ){

																			$complete_grades = false;

																			$complete_quarter = false;

																		}

																		$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category , $session_id );

																		if( empty($checkifChild)){

																			$grade_per_quarter_with_unit = $grade_per_quarter * $units;

																			$display_total_grade = $display_total_grade + $grade_per_quarter_with_unit;

																			$count_subject = $count_subject + $units;

																		}

																	}

																	if( $display_card == 'yes'){

																		if( $grade_per_quarter == null || $grade_per_quarter == 0  ){

																			if( $if_gradeisnull == 'blank'){

																				?>

																				<th></th>

																				<?php

																			}  else {

																				?>

																				<th><?php echo number_format(0, $decimal_grades, '.', '');?></th>

																				<?php

																			}

																		} else {

																			if( $grade_per_quarter < 75 ){

																				?>

																				<th style="color:red;"><?php echo $grade_per_quarter;?></th>

																				<?php 

																			} else {

																				?>

																				<th><?php echo $grade_per_quarter;?></th>

																				<?php

																			}?>

																			<?php

																		}

																	}

																}

															}

														}

														if( $quarter != "final"){

															echo "<th>";

															$student_conduct = '';

															$getConductSubject = $this->conductsession_model->getDetailByclassAndSection( $class_id, $section_id, $session_id );

															if( $getConductSubject ){

																foreach( $getConductSubject as $getConduct => $conduct){

																		$conduct_id = $conduct['id'];

																		$conduct_details = $this->conductimportbatchdetail_model->getAllDetailsConductByStudentPerQuarter( $conduct_id, $student_id, $class_id, $section_id, $quarter , $session_id );

																		if( !empty( $conduct_details ) && $conduct_details ){

																			$student_conduct .= $conduct_details['conduct'];

																			echo $student_conduct;

																		}

																}

															}

															echo "</th>";

														}

													   if( $quarter == "final"){

														   if( $total_subject_list ){

															  $display_total_grade =  $total_grade / $total_subject_list;

															  $display_total_grade =number_format($display_total_grade,$decimal_average,".",".");

															  if( $complete_grades ){

																 if( $display_total_grade < 75  ){

																	echo "<td style=\"color:red;\">".$display_total_grade."</th>";  

																 } else {

																	echo "<td>".$display_total_grade."</th>";  

																 }

																	

															  } else {

																echo "<th> </th>";

															  }

														   } else {

															   echo "<th> </th>";

														   }

															

													   } else {

														  $average_grade =  $count_subject != 0?$display_total_grade/$count_subject:0;

														  $average_grade =  number_format($average_grade, $decimal_average, '.', '');

														  if( $average_grade && $complete_grades ){

															  if( $average_grade < 75  ){

																echo "<th style=\"color:red;\">".$average_grade."</th>";  

															  } else {

																echo "<th>".$average_grade."</th>";   

															  }

														  } else {

															echo "<th> </th>";

														  }

													   }

														?>  

													</tr>

													<?php

												}

											}

											if( $studentlist_female ){

												

												?>

												<tr>

													<th class="mailbox-name"><b>Female</b></th>

													<?php 

													for( $jo=1;$jo<=$abc;$jo++){

														?>

														<th class="mailbox-name"></th>

														<?php

													}

													?>

													<th class="mailbox-name"></th>

													<?php 

													if( $quarter != 'final'){

														?>

														<th class="mailbox-name"></th>

														<?php

													}

													?>

												</tr>

												<?php

												foreach ($studentlist_female as  $student_value) { 

													$student_id = $student_value["id"];

													$lastname = $student_value['lastname'];

													$firstname = $student_value['firstname'];

													$suffix = $student_value['suffix'];

													$middlename = !empty($student_value['middlename'])?$student_value['middlename'][0].'.':'';

													$url = base_url().'academichead/report/show_report_card/'.$student_id; 

													?>

													<tr> 

														<td class="tdclsname" style="text-transform:uppercase;"><?php echo $lastname.', '.$firstname.' '.$suffix.' '.$middlename; ?></td> 

														<?php

														$total_grade = 0;

														$total_subject_list = 0;

														$display_total_grade = 0; 

														$average_grade = 0; 

														$count_subject = 0;

														$get_count = 4;

														$last_quarter = 4;

														if( $subjectlist ){

															$complete_grades =true;

															$complete_first =true;

															$complete_second =true;

															$complete_third =true;

															$complete_fourth =true;

															foreach ($subjectlist as $subject_key => $subject_value ) {

																$get_final_grade = 0;

																$subject_id = $subject_value['id'];

																$sm_details = $this->subjectmanager_model->getBySubjectSession( $subject_id, $session_id );

																$display_card = $sm_details['display_card'];

																$include_computation = $sm_details['include_computation'];

																$display_number = $sm_details['display_number'];

																$units = 1;

																if( $quarter == "final"){

																	$complete_grades =true;

																	for($xy=1;$xy<=$last_quarter;$xy++){

																		$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $xy, $combine_category, $session_id );

																		if( $display_number == 'yes'){

																			$get_final_grade = $get_final_grade + $grade_per_quarter;

																		}

																		if( $xy == 1 ){

																			if( $grade_per_quarter == null && $include_computation == 'yes'  ){

																				$complete_first = false;

																			}

																		}

																		if( $xy == 2 ){

																			if( $grade_per_quarter == null && $include_computation == 'yes' ){

																				$complete_second = false;

																			}

																		}

																		if( $xy == 3 ){

																			if( $grade_per_quarter == null && $include_computation == 'yes'  ){

																				$complete_third = false;

																			}

																		}

																		if( $xy == 4 ){

																			if( $grade_per_quarter == null && $include_computation == 'yes'  ){

																				$complete_fourth = false;

																			}

																		}

																	}

																	if( $display_number == 'yes' ){

																		$display_grade = $get_final_grade / $last_quarter;

																	}

																	if( $include_computation == 'yes' ){ 

																		$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );

																	}

																	if( $display_number == 'yes' ){

																		$display_grade = number_format((float)$display_grade, $decimal_finalgrade, '.', '');

																	}

																	if( $display_card == 'yes'){

																		if( $complete_first && $complete_second && $complete_third && $complete_fourth ){

																			if( $display_grade == 0 ){

																				echo "<th></th>";

																			} else {

																				if( $display_grade < 75 ){

																					echo "<th style=\"color:red;\">".$display_grade."</th>";

																				} else {

																					echo "<th>".$display_grade."</th>";

																				}

																			

																			}

																		} else {

																			echo "<th></th>";

																			$complete_grades = false;	

																		}

																	}

																	

																	if( $include_computation == 'yes'){

																		$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category, $session_id );

																		if( empty($checkifChild)){

																			$total_grade += $display_grade;

																			 $total_subject_list++;

																		}

																	}

																} else {

																	$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $quarter, $combine_category, $session_id );

																	if( $include_computation == 'yes'){

																		if( $grade_per_quarter == null ){

																			$complete_grades = false;

																			$complete_quarter = false;

																		}

																		$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id, $combine_category , $session_id );

																		if( empty($checkifChild)){

																			$grade_per_quarter_with_unit = $grade_per_quarter * $units;

																			$display_total_grade = $display_total_grade + $grade_per_quarter_with_unit;

																			$count_subject = $count_subject + $units;

																		}

																	}

																	if( $display_card == 'yes'){

																		if( $grade_per_quarter == null || $grade_per_quarter == 0  ){

																			if( $if_gradeisnull == 'blank'){

																				?>

																				<th></th>

																				<?php

																			}  else {

																				?>

																				<th><?php echo number_format(0, $decimal_grades, '.', '');?></th>

																				<?php

																			}

																		} else {

																			if( $grade_per_quarter < 75 ){

																				?>

																				<th style="color:red;"><?php echo $grade_per_quarter;?></th>

																				<?php

																			} else {

																				?>

																				<th><?php echo $grade_per_quarter;?></th>

																				<?php

																			}

																			?>

																			

																			<?php

																		}

																	}

																}

															}

														}

														if( $quarter != "final"){

															echo "<th>";

															$student_conduct = '';

															$getConductSubject = $this->conductsession_model->getDetailByclassAndSection( $class_id, $section_id, $session_id );

															if( $getConductSubject ){

																foreach( $getConductSubject as $getConduct => $conduct){

																		$conduct_id = $conduct['id'];

																		$conduct_details = $this->conductimportbatchdetail_model->getAllDetailsConductByStudentPerQuarter( $conduct_id, $student_id, $class_id, $section_id, $quarter , $session_id );

																		if( !empty( $conduct_details ) && $conduct_details ){

																			$student_conduct .= $conduct_details['conduct'];

																			echo $student_conduct;

																		}

																}

															}

															echo "</th>";

														}

													   if( $quarter == "final"){

														   if( $total_subject_list ){

															  $display_total_grade =  $total_grade / $total_subject_list;

															  $display_total_grade =number_format($display_total_grade,$decimal_average,".",".");

															  if( $complete_grades ){

																  if( $display_total_grade < 75 ){

																	 echo "<td style=\"color:red;\">".$display_total_grade."</th>"; 

																  } else {

																	echo "<td>".$display_total_grade."</th>"; 

																  }

															  } else {

																echo "<th> </th>";

															  }

														   } else {

															   echo "<th> </th>";

														   }

															

													   } else {

														  $average_grade =  $count_subject != 0?$display_total_grade/$count_subject:0;

														  $average_grade =  number_format($average_grade, $decimal_average, '.', '');

														  if( $average_grade && $complete_grades ){

															  if( $average_grade < 75 ){

																echo "<th style=\"color:red;\">".$average_grade."</th>";  

															  } else {

																 echo "<th>".$average_grade."</th>";   

															  }

														  } else {

															echo "<th> </th>";

														  }

													   }

														?>  

													</tr>

													<?php

												}

											}?>

											

										</tbody>

									</table>

									<?php

									}

									?>

								</div>

							</div>

						<?php

						}

						?>

                        </div>

					</div>

                </div>

            </div>

        </div>

    </section>

</div>



<script type="text/javascript">

$(document).ready(function () {

		var section_id_post = '<?php echo $section_id; ?>';

		var class_id_post = '<?php echo $class_id; ?>';

		var session_id_post = '<?php echo $session_id; ?>';

		populateSection(section_id_post, class_id_post, session_id_post);

		function populateSection(section_id_post, class_id_post, session_id_post) {

		$('#section_id').html("");

		var base_url = '<?php echo base_url() ?>';

		var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';

		$.ajax({

			type: "GET",

			url: base_url + "teacher/sections/getByClassBySession",

			data: {'class_id': class_id_post, 'session_id' : session_id_post },

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

			url: base_url + "teacher/strand/allowStrand",

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



		$('#class_id').html("");

		var base_url = '<?php echo base_url() ?>';

		var div_data1 = '<option value=""><?php echo $this->lang->line('select'); ?></option>';

		$.ajax({

			type: "GET",

			url: base_url + "teacher/student/getByClassSession",

			data: {'session_id':session_id_post},

			dataType: "json",

			success: function (data) {

				$.each(data, function (i, obj)

				{



					var sel = "";

					if (class_id_post == obj.class_id) {

						sel = "selected";

					}

					div_data1 += "<option value=" + obj.class_id + " " + sel + ">"  + obj.class_name + "</option>";

				});

				$('#class_id').append(div_data1);

			}

		});

		}



	$(document).on('change', '#class_id', function (e) {

		$('#section_id').html("");

		var class_id = $(this).val();

		var session_id = $('#session_id').val();

		var base_url = '<?php echo base_url() ?>';

		var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';

		$.ajax({

			type: "GET",

			url: base_url + "teacher/sections/getByClassBySession",

			data: {'class_id': class_id, 'session_id' : session_id },

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

			url: base_url + "teacher/strand/allowStrand",

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

		$(document).on('change', '#session_id', function (e) {

		$('#section_id').html("");

		 var section_id_post = $('#section_id').val();

		 var class_id_post = $('#class_id').val();

		var session_id_post = $('#session_id').val();

		populateSection(section_id_post, class_id_post, session_id_post);

		});



	var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';

	$('#date').datepicker({

		format: date_format,

		autoclose: true

	});

});

</script>



<script type="text/javascript">

	$( "#printsf5" ).click(function() {

        $('#form1mastersheet input[name=action]').val('print_master_sheet_sf5');

        // $("#form1mastersheet").attr("target", "_blank");

        $("#form1mastersheet").attr("action", "<?php echo site_url('teacher/report/print_master_sheet_sf5') ?>"); 

        $('#form1mastersheet').submit();



    });

    $( "#printsms" ).click(function() {

        $('#form1mastersheet input[name=action]').val('print_master_sheet');

        // $("#form1mastersheet").attr("target", "_blank");

        $("#form1mastersheet").attr("action", "<?php echo site_url('teacher/report/master_sheet') ?>"); 

        $('#form1mastersheet').submit(); 

    });

	

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

	

	$(document).ready(function () {

		$('.example').dataTable( {

			"order": [],

			"columnDefs": [ {

			  "targets"  : 'sorting_disabled',

			  "orderable": false,

			}]

		});

	});

</script>