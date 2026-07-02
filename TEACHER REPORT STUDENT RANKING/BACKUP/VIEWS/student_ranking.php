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
                    <div class="box-body">`
                        <form id='form1' action="<?php echo site_url('admin/report/student_ranking') ?>"  method="post" accept-charset="utf-8">
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
									<div class="col-md-3"  id="strand" style="display:none">
										<div class="form-group">
											<label for="exampleInputEmail1"><?php echo "Semester"; ?></label>
											<select class="form-control" name="strand_id" id="select_strand">
												<option value=""><?php echo $this->lang->line('select'); ?></option>
												<?php
												
													foreach ($getSemester as  $value => $key) {
													?>
													<option value="<?php echo $value; ?>" <?php if (set_value('strand_id',$student['strand_id']) == $key['id']) echo "selected"; ?>><?php echo $key; ?></option>
													<?php
													}
												?>
											</select> 
											<span class="text-danger"><?php echo form_error('strand'); ?></span>
										</div>
									</div>
                                    <div class="col-md-3">
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
														<th>LRN</th>
														<th>Last Name</th>
														<th>First Name</th>
														<th>Middle Name</th>
														<th>Gender</th>  
														<th>Grade</th>  
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
														foreach ($studentlist as  $student_value) {
															if( !empty( $student_value["id"])){
																$student_id = $student_value["id"];
																$url = base_url().'admin/report/show_report_card/'.$student_id; 
																$final_grade = round( $student_value['final_grade'], 2);
																$background_color =  isset($student_value["background_color"])?$student_value["background_color"]:'';
																$text_color = isset($student_value["text_color"])?$student_value["text_color"]:'';
																$name = isset($student_value["name"])?$student_value["name"]:'';
																?>
																<tr>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $row_count; ?></td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $name; ?></td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $student_value['lrn']; ?></td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $student_value['lastname']; ?></td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $student_value['firstname']; ?></td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $student_value['middlename']; ?></td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $student_value['gender']; ?></td>
																	<td class="tdclsname" style="background-color:<?php echo $background_color;?>;color:<?php echo $text_color;?>" ><?php echo $final_grade; ?></td>
																</tr>
																<?php
																$row_count++;
															}
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
                    populateSection(section_id_post, class_id_post);
                    function populateSection(section_id_post, class_id_post) {
                        $('#section_id').html("");
                        var base_url = '<?php echo base_url() ?>';
                        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
                        $.ajax({
                            type: "GET",
                            url: base_url + "sections/getByClass",
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
							url: base_url + "admin/strand/allowStrand",
							data: {'class_id': class_id_post},
							dataType: "json",
							success: function (data) {
								if( data == true ){
									$('#strand').css('display','block');
								} else {
									$('#strand').css('display','none');
									$('#select_strand').val("");
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
                            url: base_url + "sections/getByClass",
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
							url: base_url + "admin/strand/allowStrand",
							data: {'class_id': class_id},
							dataType: "json",
							success: function (data) {
								if( data == true ){
									$('#strand').css('display','block');
								} else {
									$('#strand').css('display','none');
									$('#select_strand').val("");
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