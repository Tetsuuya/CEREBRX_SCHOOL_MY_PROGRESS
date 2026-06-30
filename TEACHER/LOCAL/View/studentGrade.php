<?php

$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

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

								$check_secondsem_strand = $this->studentstrand_model->get_latest_strand( $student_id, 2,  $session_id );

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

                                 <b><?php echo $this->lang->line('gender'); ?></b> <a class="pull-right text-aqua"><?php echo $this->lang->line(strtolower($student['gender'])); ?></a>

                            </li>

                            <li class="list-group-item">

                                 <b><?php echo 'Status'; ?></b> <a class="pull-right text-aqua"><?php echo $student['status']; ?></a>

                            </li>

                        </ul>

                    </div>

                </div>

            </div>

            <div class="col-md-9">

                 <div class="box box-primary row">

				    <div class="box-body box-profile">

							<div class="col-md-12">

								<div class="form-group">

									<div class="col-md-2">

								<?php

								if( $deparment == 'senior'){

									?>

									<form action="<?php echo site_url('teacher/student/update_strand/'); ?>"  method="post" accept-charset="utf-8">

										<input type="submit" class="btn btn-sm btn-warning pull-left" value="Back" />

										<input type="hidden" name="class_id" value="<?php echo $class_id;?>"/ >

										<input type="hidden" name="section_id" value="<?php echo $section_id;?>"/ >

										<input type="hidden" name="semester_id" value="<?php echo $semester_id;?>"/ >

										<input type="hidden" name="session_id" value="<?php echo $session_id;?>"/ >

										<input type="hidden" name="deparment" value="<?php echo $deparment;?>"/ >

									</form>

									<?php

								} else {

									?>

									<form action="<?php echo site_url('teacher/student/junior/'); ?>"  method="post" accept-charset="utf-8">

										<input type="submit" class="btn btn-sm btn-warning pull-left" value="Back" />

										<input type="hidden" name="class_id" value="<?php echo $class_id;?>"/ >

										<input type="hidden" name="section_id" value="<?php echo $section_id;?>"/ >

										<input type="hidden" name="session_id" value="<?php echo $session_id;?>"/ >

										<input type="hidden" name="deparment" value="<?php echo $deparment;?>"/ >

									</form>

									<?php 

								}

								?>

								</div>

								<form id='form2' action="<?php echo site_url('teacher/student/view_grade_coordinator/'); ?>"  method="post" accept-charset="utf-8">									

									<div class="col-md-2">

										<label for="exampleInputEmail1"><?php echo "School Year"; ?></label>

									</div>

									<div class="col-md-6">

										<select  id="session_id" name="session_id" class="form-control" >

											<option value=""><?php echo $this->lang->line('select'); ?></option>

											<?php

											foreach ($sessionlist as $session => $key ) {

												if( $key['session'] <= $session_name ){

												?>

												<option value="<?php echo $key['session_id'] ?>"<?php if ($key['session_id'] == $session_id) echo "selected=selected" ?>><?php echo $key['session'] ?></option>

												<?php

												}

											}

											?>

										</select>					

										<span class="text-danger"><?php echo form_error('session_id'); ?></span>

									</div>

									<div class="col-md-2">

										<button type="submit" name="search" value="search" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>

									</div>

									<input type="hidden" name="student_id" value="<?php echo $student_id; ?>" />

								</form>

								</div>

							</div>

							<br/>

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

																	?>

																	<td><?php echo $grade_per_quarter;?></td>

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

																?>

																<td><?php echo $final_grade;?></td>

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

															$checkifChild = $this->subjectcombine_model->checkifChild( $subject_id,'ga', $session_id );

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

																?>

																<td><?php echo $grade_per_quarter;?></td>

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

															?>

															<td><?php echo $final_grade;?></td>

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

													$complete_grades = true;

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

																	} elseif($x==4){ 

																		$fourth = $fourth + $grade_per_quarter;

																		$grade_per_quarter != null?$count_subjects_fourth++:'';		

																		if( empty( $grade_per_quarter )){

																			$complete_fourth = false;

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

																	?>

																	<td><?php echo $grade_per_quarter;?></td>

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

																?>

																<td><?php echo $final_grade;?></td>

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

												$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;

												$first_ave =  number_format((float)$first_ave, $decimal_average, '.', '');

												$second_ave =  number_format((float)$second_ave, $decimal_average, '.', '');

												$third_ave =  number_format((float)$third_ave, $decimal_average, '.', '');

												$fourth_ave =  number_format((float)$fourth_ave,$decimal_average, '.', '');

												

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

								<table class="table table-hover table-striped">

									<thead>

										<tr>

											<th>

												Conduct

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

										$last_quarter = 3;

										if( $getConductSubject ){

											foreach( $getConductSubject as $getConduct => $conduct){

												$conduct_id = $conduct['id'];

												if( $conduct_id ){

													

													?> 

													<tr>

														<td><?php echo $conduct['name'];?></td>

														<?php 

														for($x=1;$x<=$last_quarter;$x++){

															if( $x <= $last_quarter ){

																$conduct_details = $this->conductimportbatchdetail_model->getAllDetailsConductByStudentPerQuarter( $conduct_id, $student_id, $class_id, $section_id, $x, $session_id );

																//printx( $conduct_details );

																if( isset( $conduct_details ) && $conduct_details ){

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

														?>

														<td></td>

													</tr>

													<?php 

												} 

											}

										}?>

									</tbody>

								</table>

								<?php

							}

							?>                   

                    </div>

                    </div>

                </div>

            </div>

    </section> 

</div>



