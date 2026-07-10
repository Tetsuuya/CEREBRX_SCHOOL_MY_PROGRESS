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
        <h1> <i class=" "></i> Report Card <small></h1>
    </section>   
    <section class="content">
        <div class="row">          
            <div class="col-md-12">              
                                
                <div class="box box-primary" id="sublist"> 
					 <div class="box-header with-border">
                        <h3 class="box-title">Select Student by Grade and Section</h3>
                    </div> 
                    <div class="box-body">
                        <form id='form1' action="<?php echo site_url('registrar/report/report_card') ?>"  method="post" accept-charset="utf-8">
                            <div class="box-body">
                            	  <?php if ($this->session->flashdata('msg')) { ?>
								
                                <?php echo $this->session->flashdata('msg') ?>
								
                                <?php } ?>
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="row">
									<div class="col-md-2">
										<div class="form-group">
											<label for="exampleInputEmail1"><?php echo "School Year"; ?></label>
											<select  id="session_id" name="session_id" class="form-control" >
												<option value=""><?php echo $this->lang->line('select'); ?></option>
												<?php
												foreach ($sessionlist as $session) {
												?>
												<option value="<?php echo $session['id'] ?>"<?php if ($session['id'] == $session_id) echo "selected=selected" ?>><?php echo $session['session'] ?></option>
												<?php
												$count++;
												}
												?>
											</select>					
											<span class="text-danger"><?php echo form_error('session_id'); ?></span>
										</div>
									</div>
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
                        <div class="table-responsive mailbox-messages">
                        <?php
						$show_batch = false;
						if (isset($studentlist)) {
							?>
							<div class="box box-info" id="attendencelist">
								<div class="box-header ptbnull" >
									<h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('student'); ?> List</h3>
								</div>
								<div class="box-body table-responsive">
									<div class="mailbox-controls">
										<div class="pull-right">
										</div>
									</div>
								<form id='form2' action="<?php echo site_url('registrar/report/show_report_card') ?>"  method="post" accept-charset="utf-8">									
									<table class="table table-hover table-striped table-bordered xyz example">
										<thead>
											<tr>
												<input type="hidden" name="gdisplay_signature"  />
												<input type="hidden" name="gdisplay_psignature"  />
												<input type="hidden" name="gdisplay_attendance"  />
												<td class="tdclsname" colspan="6">
													<button type="button" class="btn btn-sm btn-primary pull-right btn-print-batch">Generate Batch Form 138</button>
												</td>
											</tr>
											<tr>
												<td class="tdclsname" colspan="6">												
													<button type="button" id="batch-grade-slip" class="btn btn-sm btn-success pull-right btn-print-batch-grade-slip">Generate Batch Grade Slip</button>
												</td>										
											</tr>
											<tr>
												<th class="sorting_disabled"></th>
												<th>Last Name</th>
												<th>First Name</th>
												<th>LRN</th>
												<th>Action</th>
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
												$show_batch = true;
												$row_count = 1;
												if( $studentlist_male ){
													?>
													<tr>
														<td class="sorting_disabled"><strong>Male</strong></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
													</tr>
													<?php
													foreach ($studentlist_male as  $student_value) {
														
														$student_id = $student_value["id"];
														$url = base_url().'registrar/report/show_report_card/'.$student_id; 
														?>
														<tr>
															<td class="tdclsname"><input type="checkbox" name="student_print[]" value="<?php echo $student_id; ?>" /><?php echo $row_count;?></td>
															<td class="tdclsname"><?php echo $student_value['lastname']; ?></td>
															<td class="tdclsname"><?php echo $student_value['firstname']; ?></td> 
															<td class="tdclsname"><?php echo $student_value['lrn']; ?></td>
															<td class="tdclsname"><a class="btn btn-sm btn-primary btn-print-f138" data-student-id="<?php echo $student_id;?>">Generate Form 138</a>
															<a class="btn btn-sm btn-success btn-print-grade-slip"  data-student-id="<?php echo $student_id;?>">Generate Grade Slip</a></td>
															<!--td class="tdclsname"><a href="<?php echo $url; ?>"  class="btn btn-sm btn-primary">Print</a></td--> 
															<input type="hidden" name="class_id" value="<?php echo $class_id; ?> " />
															<input type="hidden" name="section_id" value="<?php echo $section_id; ?> " />
															<input type="hidden" name="student_id[]" value="<?php echo $student_id; ?> " />
															<input type="hidden" name="quarter" value="<?php echo $quarter; ?>" />
															<input type="hidden" name="semester_id" value="<?php echo $semester_id;?>" />
															<input type="hidden" name="session_id" value="<?php echo $session_id;?>" />
														</tr>
														<?php
														$row_count++;
													}
												}
												if( $studentlist_female ){
													?>
													<tr>
														<td class="sorting_disabled"><strong>Female</strong></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
													</tr>
													<?php
													foreach ($studentlist_female as  $student_value) {
														
														$student_id = $student_value["id"];
														$url = base_url().'registrar/report/show_report_card/'.$student_id; 
														?>
														<tr>
															<td class="tdclsname"><input type="checkbox" name="student_print[]" value="<?php echo $student_id; ?>" /><?php echo $row_count;?></td>
															<td class="tdclsname"><?php echo $student_value['lastname']; ?></td>
															<td class="tdclsname"><?php echo $student_value['firstname']; ?></td> 
															<td class="tdclsname"><?php echo $student_value['lrn']; ?></td>
															<td class="tdclsname"><a class="btn btn-sm btn-primary btn-print-f138" data-student-id="<?php echo $student_id;?>">Generate Form 138</a>
															<a class="btn btn-sm btn-success btn-print-grade-slip"  data-student-id="<?php echo $student_id;?>">Generate Grade Slip</a></td>
															<!--td class="tdclsname"><a href="<?php echo $url; ?>"  class="btn btn-sm btn-primary">Print</a></td--> 
															<input type="hidden" name="class_id" value="<?php echo $class_id; ?> " />
															<input type="hidden" name="section_id" value="<?php echo $section_id; ?> " />
															<input type="hidden" name="student_id[]" value="<?php echo $student_id; ?> " />
															<input type="hidden" name="quarter" value="<?php echo $quarter; ?>" />
															<input type="hidden" name="semester_id" value="<?php echo $semester_id;?>" />
															<input type="hidden" name="session_id" value="<?php echo $session_id;?>" />
														
														</tr>
														<?php
														$row_count++;
													}
												}
											}
											?>
										</tbody>
										<?php 
										if( $show_batch ){
											?>
											<tr>
												<input type="hidden" name="gdisplay_signature"  />
												<input type="hidden" name="gdisplay_psignature"  />
												<input type="hidden" name="gdisplay_attendance"  />
												<td class="tdclsname" colspan="6">
													<button type="button" class="btn btn-sm btn-primary pull-right btn-print-batch">Generate Batch Form 138</button>
												</td>
											</tr>
											<tr>
												<td class="tdclsname" colspan="6">												
													<button type="button" id="batch-grade-slip" class="btn btn-sm btn-success pull-right btn-print-batch-grade-slip">Generate Batch Grade Slip</button>
												</td>										
											</tr>
											<?php
										}
										?>
									</table>
								</form>	
								<form id='form3' action="<?php echo site_url('registrar/report/show_report_card') ?>"  method="post" accept-charset="utf-8">
									
									<input type="hidden" name="class_id" value="<?php echo $class_id; ?> " />
									<input type="hidden" name="section_id" value="<?php echo $section_id; ?> " />
									<input type="hidden" name="quarter" value="<?php echo $quarter; ?>" />
									<input type="hidden" name="student_id" id="get_student_id"  />
									<input type="hidden" name="semester_id" value="<?php echo $semester_id;?>" />
									<input type="hidden" name="session_id" value="<?php echo $session_id;?>" />
									<input type="hidden" name="gdisplay_signature"  />
									<input type="hidden" name="gdisplay_psignature"  />
									<input type="hidden" name="gdisplay_attendance"  />
								</form>
								<form id='form4' action="<?php echo site_url('registrar/report/show_grade_slip') ?>"  method="post" accept-charset="utf-8">
									<input type="hidden" name="class_id" value="<?php echo $class_id; ?> " />
									<input type="hidden" name="section_id" value="<?php echo $section_id; ?> " />
									<input type="hidden" name="quarter" value="<?php echo $quarter; ?>" />
									<input type="hidden" name="student_id" id="get_student_id"  />
									<input type="hidden" name="semester_id" value="<?php echo $semester_id;?>" />
										<input type="hidden" name="session_id" value="<?php echo $session_id;?>" />
								</form>
								
								<form id='form5' action="<?php echo site_url('registrar/report/show_grade_slip') ?>"  method="post" accept-charset="utf-8">
									<?php $row_count = 1;
										foreach ($studentlist as  $student_value2) {
											$student_id = $student_value2["id"];?>
											<input type="hidden" name="class_id" value="<?php echo $class_id; ?> " />
											<input type="hidden" name="section_id" value="<?php echo $section_id; ?> " />									
											<input type="hidden" name="student_id[]" value="<?php echo $student_id; ?> " />
											<input type="hidden" name="quarter" value="<?php echo $quarter; ?>" />			
											<input type="hidden" name="semester_id" value="<?php echo $semester_id;?>" />
											<input type="hidden" name="session_id" value="<?php echo $session_id;?>" />
										<?php 
										}
										?>
								
								</form>
								</div>
							</div>
							<?php
						}
						?>
						<div class="modal fade" tabindex="-1" role="dialog" id="customized_modal">
							  <div class="modal-dialog" role="document">
							    <div class="modal-content">
							      <div class="modal-header">
							        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							      </div>
							      <div class="modal-body">
							        <p><b>Display teacher signature?</b>:<span>Yes<input type="radio" value="1" name="display_signature" checked></span>&nbsp;&nbsp;<span>No<input type="radio" value="0" name="display_signature"></span></p>
							         <p><b>Display principal signature?</b>:<span>Yes<input type="radio" value="1" name="display_psignature" checked></span>&nbsp;&nbsp;<span>No<input type="radio" value="0" name="display_psignature"></span></p>
							        <p><b>Display attendance data?</b>:<span>Yes<input type="radio" value="1" name="display_attendance"></span>&nbsp;&nbsp;<span>No<input type="radio" value="0" name="display_attendance" checked></span></p>
							        <input type="hidden" id="form_no" />
							      </div>
							      <div class="modal-footer">
							        <button type="button" class="btn btn-primary" id="customized_button">Save changes</button>
							      </div>
							    </div><!-- /.modal-content -->
							  </div><!-- /.modal-dialog -->
							</div><!-- /.modal -->
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </section>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".btn-print-f138").click(function () {  
            var student_id = $(this).attr('data-student-id');
            $("#form3 #get_student_id").val(student_id);
            $("#form_no").val('form3');
            $("#customized_modal").modal('show');

			// $('#form3').submit();
        }); 

        $(".btn-print-batch").click(function () {  
        	 $("#form_no").val('form2');
            $("#customized_modal").modal('show');

			// $('#form3').submit();
        });

        $("#customized_button").click(function () {  
            var display_signature = document.querySelector('input[name="display_signature"]:checked').value;
            var display_psignature = document.querySelector('input[name="display_psignature"]:checked').value;
            var display_attendance = document.querySelector('input[name="display_attendance"]:checked').value;
             var form_no = $("#form_no").val();

          	$("input[name=gdisplay_signature]").val(display_signature);
          	$("input[name=gdisplay_psignature]").val(display_psignature);
          	$("input[name=gdisplay_attendance]").val(display_attendance);
            $("#customized_modal").modal('hide');

			$('#'+form_no).submit();
        }); 

      
		$("#batch-grade-slip").click(function () { 
            var checked_students = $('input[name="student_print[]"]:checked');
            if (checked_students.length > 0) {
                var form_clone = $('#form5').clone();
                form_clone.find('input[name="student_id[]"]').remove();
                checked_students.each(function () {
                    var student_id = $(this).val();
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'student_id[]',
                        value: student_id
                    }).appendTo(form_clone);
                });
                form_clone.appendTo('body').submit().remove();
            } else {
                $('#form5').submit();
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $(".btn-print-grade-slip").click(function () {  
            var student_id = $(this).attr('data-student-id');
             $("#form4 #get_student_id").val(student_id);
			$('#form4').submit();
        });
    });
</script>


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
					url: base_url + "registrar/sections/getByClassBySession",
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
				var session_id = $('#session_id').val();
				var base_url = '<?php echo base_url() ?>';
				var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
				$.ajax({
					type: "GET",
					url: base_url + "registrar/sections/getByClassBySession",
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