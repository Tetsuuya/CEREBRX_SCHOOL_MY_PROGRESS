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
        <h1> <i class=" "></i> Grade Sheet<small></h1>
    </section>   
    <section class="content">
        <div class="row">          
            <div class="col-md-12">              
                                
                <div class="box box-primary" id="sublist"> 
                     <div class="box-header with-border">
                        <h3 class="box-title">Select Student by Grade and Section</h3>
                    </div> 
                    <div class="box-body">
                        <form id='form1' action="<?php echo site_url('teacher/report/grade_sheet') ?>"  method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="row">
                                    <div class="col-md-3">
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
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('subject'); ?></label>
                                            <select  id="subject_id" name="subject_id" class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('subject_id'); ?></span>
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
                if (!empty($studentlist_male) || !empty($studentlist_female)) {
                    ?>
                    <div class="box box-info" id="attendencelist">
                        <div class="box-header ptbnull" >
                            <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('student'); ?> List</h3>
                            <div >
                                <form id='form1' action="<?php echo site_url('teacher/report/grade_sheet') ?>"  method="post" accept-charset="utf-8">
                                    <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                                    <input type="hidden" name="section_id" value="<?php echo $section_id; ?>"> 
                                    <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
                                    <input type="hidden" name="semester_id" value="<?php echo $semester_id; ?>">
                                    <input type="hidden" name="session_id" value="<?php echo $session_id; ?>">
                                    <input type="hidden" name="action" value="print_grade_sheet">
                                    <button type="submit" class="btn btn-primary btn-sm pull-right" style="margin-top: 3px;margin-bottom: -5px">PRINT GRADE SHEET</button>
                                </form>
                            </div>
                        </div>
                        <div class="box-body table-responsive">
                             
                            <?php
                            if (!empty($studentlist_male) || !empty($studentlist_female)) {
                                ?>
                                <div class="mailbox-controls">
                                    <div class="pull-right">
                                    </div>
                                </div>

                                  <table class="table table-hover table-striped table-bordered xyz example">
                                    <thead>
                                        <tr> 
                                            <th>Student Name</th> 
                                            <?php
											if( $enableStrand ){
												$gradingsettings = $this->gradingsetting_model->getbySession( $session_id );
												$get_quarter = array();
												$total_quarter =0;
												if( $semester_id == 1 ){
													$get_quarter = isset($gradingsettings->qtr_first_sem )?$gradingsettings->qtr_first_sem:array();	
													$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
													$total_quarter = count( $get_quarter );
												} else {
													$get_quarter = isset($gradingsettings->qtr_second_sem )?$gradingsettings->qtr_second_sem:array();	
													$get_quarter = is_serialized( $get_quarter )?unserialize($get_quarter):$get_quarter;
													$total_quarter = count( $get_quarter );
												}
												
												if( $get_quarter ){
													foreach( $get_quarter as $key => $value ){
														?>
														<th><?php echo ordinal_suffix_roman($value);?></th>
														<?php
													}
													/* ?>
													<th>F</th>
													<?php */
												}
											} else {
												for($x=1;$x<=3;$x++){
													?>
													<th><?php echo ordinal_suffix_roman($x);?></th>  
													<?php
												}
											}	
                                            ?>
                                          	<th>Final</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($studentlist_male)) {
                                            ?>
                                            <tr>
                                                <td colspan="32" class="text-danger text-center"><?php echo $this->lang->line('no_record_found'); ?></td>

                                            </tr>
                                            <?php
                                        } else {
											if( $enableStrand ){
											?>
											 <tr>
                                                <td class="sorting_disabled"><strong>Male</strong></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
											<?php 
											} else {
												?>
												<tr>
                                                <td class="sorting_disabled"><strong>Male</strong></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
												</tr>
												<?php
											}
                                            $row_count = 1;
											$gradingsettings = $this->gradingsetting_model->getbySession( $session_id );
											$decimal_grades = isset($gradingsettings->decimal_grades)?$gradingsettings->decimal_grades:0;
											$decimal_average = isset($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;
											$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:2;
											$if_gradeisnull = isset($gradingsettings->if_gradeisnull)?$gradingsettings->if_gradeisnull:'blank';
											$combine_category = 'ga';
                                            foreach ($studentlist_male as  $student_value) { 
												$complete_grades = true;
												$empty_grades = true;
                                                $student_id = $student_value["id"]; 
												$lastname = $student_value['lastname'];
												$firstname = $student_value['firstname'];
												$suffix = $student_value['suffix'];
												$middlename = !empty($student_value['middlename'])?$student_value['middlename'][0].'.':'';
                                                ?>
                                                <tr> 
                                                    <td class="tdclsname" style="text-transform:uppercase;"><?php echo $lastname.', '.$firstname.' '.$suffix.' '.$middlename; ?></td> 
                                                    <?php 
                                                    if( $enableStrand ){
                                                       /*  $examresult1 = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, 1,  $subject_id   );
                                                        $grades1 = isset($examresult1->grades)?$examresult1->grades:' ';
                                                        echo "<th>".$grades1."</th>";
                                                        
                                                        $examresult2 = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, 2,  $subject_id   );
                                                        $grades2 = isset($examresult2->grades)?$examresult2->grades:' ';
                                                        echo "<th>".$grades2."</th>"; */
														$final_grade = 0;
														$not_available = false;
														for( $qtr=0;$qtr<$total_quarter;$qtr++ ) {
															$y = $get_quarter[$qtr];
															if( $y == 1 || $y == 2 ){
																$semester_id = 1;
															} elseif( $y == 3 || $y == '4'){
																$semester_id = 2;
															}
															$getStudentSubjectSemesterDrop = $this->customsubject_model->getStudentSubjectSemesterDrop( $student_id, $subject_id, $semester_id , $session_id );
															if( empty( $getStudentSubjectSemesterDrop)){
																$not_available = false;
																$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $y, $combine_category, $session_id );
																$grade_per_quarter = isset($grade_per_quarter)?$grade_per_quarter:null;
																$grade_per_quarter = number_format($grade_per_quarter, $decimal_grades, '.', '');
																if( $grade_per_quarter == null || $grade_per_quarter == 0 ){
																	$complete_grades = false;
																	if( $if_gradeisnull == 'blank'){
																		?>
																	<th></th>
																	<?php
																	}  else {
																		?>
																		<th><?php echo number_format((float)0, $decimal_grades, '.', '');?></th>
																		<?php
																	}
																} else {
																	$final_grade = $final_grade + $grade_per_quarter;
																	?>
																	<th><?php echo $grade_per_quarter;?></th>
																	<?php
																}
															} else {
																$not_available = true;
																?>
																<th>N/A</th>
																<?php
															}
															
														}
														if($not_available){
															?>
															<th>N/A</th>
															<?php
														} else {
															if( $complete_grades && $final_grade != 0 ){
																$average = $final_grade / $total_quarter;
																?>
																<th><?php echo number_format( $average, $decimal_finalgrade, '.',' ');?></th>
																<?php
															} else {
																?>
																<th></th>
																<?php
															}
														} 
                                                    } else {
														$final_grade = 0;
														for( $x=1;$x<=3;$x++){
															//$examresult1 = $this->grade_model->getgradeperquarter( $student_id, $subject_id, $x   );
															$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $x, $combine_category, $session_id );
															$grade_per_quarter = isset($grade_per_quarter)?$grade_per_quarter:null;
															$grade_per_quarter = number_format($grade_per_quarter, $decimal_grades, '.', '');
															if( $grade_per_quarter == null || $grade_per_quarter == 0 ){
																$complete_grades = false;
																if( $if_gradeisnull == 'blank'){
																	?>
																<th></th>
																<?php
																}  else {
																	?>
																	<th><?php echo number_format((float)0, $decimal_grades, '.', '');?></th>
																	<?php
																}
															} else {
																$final_grade = $final_grade + $grade_per_quarter;
																?>
																<th><?php echo $grade_per_quarter;?></th>
																<?php
															}
														}
														if( $complete_grades && $final_grade != 0 ){
															$average = $final_grade / 3;
															?>
															<th><?php echo number_format( $average, $decimal_finalgrade, '.',' ');?></th>
															<?php
														} else {
															?>
															<th></th>
															<?php
														}
                                                    }
                                                         
                                                    ?>  
                                                </tr>
                                                <?php
                                                $row_count++;
                                            }
                                        }
                                        ?>
										<?php if (empty($studentlist_female)) {
                                            ?>
                                            <tr>
                                                <td colspan="32" class="text-danger text-center"><?php echo $this->lang->line('no_record_found'); ?></td>

                                            </tr>
                                            <?php
                                        } else {
											if( $enableStrand ){
											?>
											 <tr>
                                                <td class="sorting_disabled"><strong>Female</strong></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
											<?php 
											} else {
												?>
												<tr>
                                                <td class="sorting_disabled"><strong>Female</strong></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
												</tr>
												<?php
											}
                                            $row_count = 1;
											$gradingsettings = $this->gradingsetting_model->getbySession( $session_id );
											$decimal_grades = isset($gradingsettings->decimal_grades)?$gradingsettings->decimal_grades:0;
											$decimal_average = isset($gradingsettings->decimal_average)?$gradingsettings->decimal_average:0;
											$decimal_finalgrade = isset($gradingsettings->decimal_finalgrade)?$gradingsettings->decimal_finalgrade:2;
											$if_gradeisnull = isset($gradingsettings->if_gradeisnull)?$gradingsettings->if_gradeisnull:'blank';
											$combine_category = 'ga';
                                            foreach ($studentlist_female as  $student_value) { 
												$complete_grades = true;
												$empty_grades = true;
                                                $student_id = $student_value["id"]; 
												$lastname = $student_value['lastname'];
												$firstname = $student_value['firstname'];
												$suffix = $student_value['suffix'];
												$middlename = !empty($student_value['middlename'])?$student_value['middlename'][0].'.':'';
                                                ?>
                                                <tr> 
                                                    <td class="tdclsname" style="text-transform:uppercase;"><?php echo $lastname.', '.$firstname.' '.$suffix.' '.$middlename; ?></td> 
                                                    <?php 
                                                    if( $enableStrand ){
                                                       /*  $examresult1 = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, 1,  $subject_id   );
                                                        $grades1 = isset($examresult1->grades)?$examresult1->grades:' ';
                                                        echo "<th>".$grades1."</th>";
                                                        
                                                        $examresult2 = $this->examresult_model->get_exam_results_by_subject_quarter( $student_id, 2,  $subject_id   );
                                                        $grades2 = isset($examresult2->grades)?$examresult2->grades:' ';
                                                        echo "<th>".$grades2."</th>"; */
														$final_grade = 0;
														for( $qtr=0;$qtr<$total_quarter;$qtr++ ) {
															$y = $get_quarter[$qtr];
															if( $y == 1 || $y == 2 ){
																$semester_id = 1;
															} elseif( $y == 3 || $y == '4'){
																$semester_id = 2;
															}
															$getStudentSubjectSemesterDrop = $this->customsubject_model->getStudentSubjectSemesterDrop( $student_id, $subject_id, $semester_id , $session_id );
															if( empty( $getStudentSubjectSemesterDrop)){
																$not_available = false;
																$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $y, $combine_category, $session_id );
																$grade_per_quarter = isset($grade_per_quarter)?$grade_per_quarter:null;
																$grade_per_quarter = number_format($grade_per_quarter, $decimal_grades, '.', '');
																if( $grade_per_quarter == null || $grade_per_quarter == 0 ){
																	$complete_grades = false;
																	if( $if_gradeisnull == 'blank'){
																		?>
																	<th></th>
																	<?php
																	}  else {
																		?>
																		<th><?php echo number_format((float)0, $decimal_grades, '.', '');?></th>
																		<?php
																	}
																} else {
																	$final_grade = $final_grade + $grade_per_quarter;
																	?>
																	<th><?php echo $grade_per_quarter;?></th>
																	<?php
																}
															} else {
																$not_available = true;
																?>
																<th>N/A</th>
																<?php
															}
															
														}
														if( $not_available ){
															?>
															<th>N/A</th>
															<?php
														} else {
															if( $complete_grades && $final_grade != 0 ){
																$average = $final_grade / $total_quarter;
																?>
																<th><?php echo number_format( $average, $decimal_finalgrade, '.',' ');?></th>
																<?php
															} else {
																?>
																<th></th>
																<?php
															}
														} 
                                                    } else {
														$final_grade = 0;
														for( $x=1;$x<=3;$x++){
															//$examresult1 = $this->grade_model->getgradeperquarter( $student_id, $subject_id, $x   );
															$grade_per_quarter = $this->subjectcombine_model->getComputedCombinedGrade( $student_id, $subject_id, $x, $combine_category, $session_id );
															$grade_per_quarter = isset($grade_per_quarter)?$grade_per_quarter:null;
															$grade_per_quarter = number_format($grade_per_quarter, $decimal_grades, '.', '');
															if( $grade_per_quarter == null || $grade_per_quarter == 0 ){
																$complete_grades = false;
																if( $if_gradeisnull == 'blank'){
																	?>
																<th></th>
																<?php
																}  else {
																	?>
																	<th><?php echo number_format((float)0, $decimal_grades, '.', '');?></th>
																	<?php
																}
															} else {
																$final_grade = $final_grade + $grade_per_quarter;
																?>
																<th><?php echo $grade_per_quarter;?></th>
																<?php
															}
														}
														if( $complete_grades && $final_grade != 0 ){
															$average = $final_grade / 3;
															?>
															<th><?php echo number_format( $average, $decimal_finalgrade, '.',' ');?></th>
															<?php
														} else {
															?>
															<th></th>
															<?php
														}
                                                    }
                                                         
                                                    ?>  
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
        var subject_id_post = '<?php echo $subject_id; ?>';
        populateSection(section_id_post, class_id_post, subject_id_post);
        function populateSection(section_id_post, class_id_post, subject_id_post) {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "/teacher/sections/getByClass",
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

            $('#subject_id').html("");
            var divs_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "teacher/subject/getSubjctByClass",
                data: {'class_id': class_id_post},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if (subject_id_post == obj.id) {
                            sel = "selected";
                        }
                        divs_data += "<option value=" + obj.id + " " + sel + ">" + obj.name + "</option>";
                    });
                    $('#subject_id').append(divs_data);
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

        }

        $(document).on('change', '#class_id', function (e) {
            $('#section_id').html("");
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
            $('#subject_id').html("");
            var divs_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "teacher/subject/getSubjctByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
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