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
        <h1> <i class=" "></i> Honor Students <small></h1>
    </section>   
    <section class="content">
        <div class="row">          
            <div class="col-md-12">              
                                
                <div class="box box-primary" id="sublist"> 
                     <div class="box-header with-border">
                        <h3 class="box-title">Select Student by Grade and Section</h3>
                    </div> 
                    <div class="box-body">
                        <form id='form1' action="<?php echo site_url('teacher/report/honor_students') ?>"  method="post" accept-charset="utf-8">
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
																						  <option  value="<?php echo $value; ?>" <?php if($semester == $value) echo "selected"; ?>><?php echo $key; ?></option>
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
                                                 <?php
                                                     foreach ($getquarter as $key => $value) {
                                                         if ($key > 3) {
                                                             continue;
                                                         }
                                                     ?>
                                                     <option  value="<?php echo $key; ?>" <?php if($quarter == $key || $quarter == $value) echo "selected"; ?>>Term <?php echo $key; ?></option>
                                                     <?php
                                                     }
                                                 ?>
 												<option  value="final" <?php if($quarter == 'final') echo "selected"; ?>> Final Grade</option>
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
							<form action="<?php echo site_url('teacher/certificate/print_certificate_batch') ?>" id="confirmcertificatebatchform" name="confirmcertificatebatchform" method="post" accept-charset="utf-8">
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
															<th>Grade</th>  
															<th></th>   
														</tr>
													</thead>
													<tbody>
														<?php
													
														if (empty($studentlist)) {
															?>
															<tr>
																<td colspan="32" class="text-danger text-center"><?php echo $this->lang->line('no_record_found'); ?></td>

															</tr>
															<?php
														} else {
															$row_count = 1;
															foreach ($studentlist as  $key => $student_value) {  
																$student_id = $student_value["id"];
																$class = $student_value["class"];
																$section = $student_value["section"];
																$url = base_url().'teacher/report/show_report_card/'.$student_id; 
																$final_grade = $student_value['final_grade'];
																$final_grade = number_format( $final_grade,'2','.',',');
																$background_color =  isset($student_value["background_color"])?$student_value["background_color"]:'';
																$text_color = isset($student_value["text_color"])?$student_value["text_color"]:'';
																$name = isset($student_value["name"])?$student_value["name"]:'';
																$firstname = !empty($student_value['firstname'])?strtoupper($student_value['firstname']):''; 
																$lastname = !empty($student_value['lastname'])?strtoupper($student_value['lastname']):''; 
																$suffix = !empty($student_value['suffix'])?strtoupper($student_value['suffix']):''; 
																$middlename = !empty($student_value['middlename'])?strtoupper($student_value['middlename'][0]).'.':''; 
																$strand_id = $student_value['strand_id'];
																?>
																<tr>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $row_count; ?></td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $name; ?></td>
																	<?php
																	if( empty($section_id) ){
																		?>
																		<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $student_value['class']; ?></td>
																		<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $student_value['section']; ?></td>
																		<?php 
																	}
																	?>
																																		<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $lastname; ?></td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $firstname.' '.$suffix; ?></td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $middlename; ?></td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $final_grade; ?>	</td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" >
																	<?php 
																	if( $name ){
																		$show_batch = true;
																		?>
																		<input type="hidden" name="batch_id[]" value="<?php echo $student_id;?>" />
																		<input type="hidden" name="awards[]"  value="<?php echo $name;?>"  />
																		<input type="hidden" name="grade[]"  value="<?php echo $final_grade;?>"  />
																		<input type="hidden" name="class_id"  value="<?php echo $class_id;?>"  />
																		<input type="hidden" name="section_id"  value="<?php echo $section_id;?>"  />
																		<input type="hidden" name="quarter"  value="<?php echo $quarter;?>"  />
																		<button class="btn btn-default btn-xs confirm_student" title="Create Certificate" data-toggle="tooltip" data-id="<?php echo $student_id;?>" data-awards="<?php echo $name;?>" data-grade="<?php echo $final_grade;?>" data-fullname="<?php echo $lastname.", ".$firstname.' '.$suffix.' '.$middlename;?>" onclick="confirm_student( this )"><i class="fa fa-print"></i> Create</button></td>
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
										<button class="btn btn-primary btn-sm" title="Create Certificate" data-toggle="modal" data-target="#confirm_certificate_batch" ><i class="fa fa-print"></i> Create Batch Certificate</button></td>
									</div>
									<?php
								}
								?>
								 <div id="confirm_certificate_batch" class="modal fade" role="dialog">
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
            <form class="modal_confirm_certificate" action="<?php echo site_url('teacher/certificate/print_certificate') ?>"  id="confirmcertificateform" name="confirmcertificateform" method="post" accept-charset="utf-8">
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
					
					$( "#confirm_certificate input#award_name" ).val(  awards );     
					$( "#confirm_certificate input#student_id" ).val( student_id );  
					$( "#confirm_certificate input#student_name" ).val( student_fullname );  
					$( "#confirm_certificate input#award_id" ).val( awards );   
					$( "#confirm_certificate input#award_date" ).val( date_today );   
					$( "#confirm_certificate input#quarter_id" ).val( quarter_id );   
					$( "#confirm_certificate input#quarter_student_grade" ).val( student_grade );   
					$('#confirm_certificate').modal('show');
				}

				$('#confirmcertificateform').submit(function() {

					// submission stuff

					$('#confirm_certificate').modal('hide'); 

					window.location.reload();
				});
				$('#confirmcertificatebatchform').submit(function() {

					// submission stuff

					$('#confirm_certificate_batch').modal('hide'); 

					window.location.reload();
				});
				var base_url = '<?php echo base_url() ?>';
				function printDiv(elem) {       
					Popup(jQuery(elem).html());
				}
			</script>