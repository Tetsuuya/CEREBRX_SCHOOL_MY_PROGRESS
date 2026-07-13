<style type="text/css">

    .radio {
        padding-left: 20px; }
    .radio label {
        display: inline-block;
        vertical-align: middle;
        position: relative;
        padding-left: 5px; }
    .radio label::before {
        content: "";
        display: inline-block;
        position: absolute;
        width: 17px;
        height: 17px;
        left: 0;
        margin-left: -20px;
        border: 1px solid #cccccc;
        border-radius: 50%;
        background-color: #fff;
        -webkit-transition: border 0.15s ease-in-out;
        -o-transition: border 0.15s ease-in-out;
        transition: border 0.15s ease-in-out; }
    .radio label::after {
        display: inline-block;
        position: absolute;
        content: " ";
        width: 11px;
        height: 11px;
        left: 3px;
        top: 3px;
        margin-left: -20px;
        border-radius: 50%;
        background-color: #555555;
        -webkit-transform: scale(0, 0);
        -ms-transform: scale(0, 0);
        -o-transform: scale(0, 0);
        transform: scale(0, 0);
        -webkit-transition: -webkit-transform 0.1s cubic-bezier(0.8, -0.33, 0.2, 1.33);
        -moz-transition: -moz-transform 0.1s cubic-bezier(0.8, -0.33, 0.2, 1.33);
        -o-transition: -o-transform 0.1s cubic-bezier(0.8, -0.33, 0.2, 1.33);
        transition: transform 0.1s cubic-bezier(0.8, -0.33, 0.2, 1.33); }
    .radio input[type="radio"] {
        opacity: 0;
        z-index: 1; }
    .radio input[type="radio"]:focus + label::before {
        outline: thin dotted;
        outline: 5px auto -webkit-focus-ring-color;
        outline-offset: -2px; }
    .radio input[type="radio"]:checked + label::after {
        -webkit-transform: scale(1, 1);
        -ms-transform: scale(1, 1);
        -o-transform: scale(1, 1);
        transform: scale(1, 1); }
    .radio input[type="radio"]:disabled + label {
        opacity: 0.65; }
    .radio input[type="radio"]:disabled + label::before {
        cursor: not-allowed; }
    .radio.radio-inline {
        margin-top: 0; }

    .radio-primary input[type="radio"] + label::after {
        background-color: #337ab7; }
    .radio-primary input[type="radio"]:checked + label::before {
        border-color: #337ab7; }
    .radio-primary input[type="radio"]:checked + label::after {
        background-color: #337ab7; }

    .radio-danger input[type="radio"] + label::after {
        background-color: #d9534f; }
    .radio-danger input[type="radio"]:checked + label::before {
        border-color: #d9534f; }
    .radio-danger input[type="radio"]:checked + label::after {
        background-color: #d9534f; }

    .radio-info input[type="radio"] + label::after {
        background-color: #5bc0de; }
    .radio-info input[type="radio"]:checked + label::before {
        border-color: #5bc0de; }
    .radio-info input[type="radio"]:checked + label::after {
        background-color: #5bc0de; }
</style>

<div class="content-wrapper" style="min-height: 946px;">   
    <section class="content-header">
        <h1>
            <i class="fa fa-calendar-check-o"></i> <?php echo $this->lang->line('attendance'); ?> <small> <?php echo $this->lang->line('by_date1'); ?></small>        </h1>
    </section>  
    <section class="content">
        <div class="row">  
            <div class="col-md-12"> 
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <form id='form1' action="<?php echo site_url('teacher/stuattendence/import') ?>"  method="post" accept-charset="utf-8">
                        <div class="box-header with-border">
                            <h3 class="box-title">Import Grades</h3>
                            <div class="pull-right box-tools"> 
                              <!--   <button type="submit" class="btn btn-primary btn-sm" formaction="<?php echo site_url('teacher/stuattendence/generate_attendance') ?>" ><i class="fa fa-file-excel-o"></i> Generate Spreadsheet</button> -->
                            </div>
                            
                        </div>
                        <div class="box-body">
                            <?php
                            if ($this->session->flashdata('msg')) {
                                echo $this->session->flashdata('msg');
                            }
                            ?>
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Type</label>
                                        <select  id="attendance_type" name="attendance_type" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <option value="Present" <?php if( $attendance_type == 'Present' ){ echo "selected";}?>>Present</option>
                                            <option value="Late" <?php if( $attendance_type == 'Late' ){ echo "selected";}?>>Late</option>
                                            <option value="Absent" <?php if( $attendance_type == 'Absent' ){ echo "selected";}?>>Absent</option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('attendance_type'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right">Custom Attendance Number</button>
                        </div>
                    </form>
                    <div class="box-body"> 
                        <div class="table-responsive">  
                            <?php 
                            if( $resultlist_male || $resultlist_female ){ 
                                $startMonth = $this->setting_model->getStartMonth();
                                $endMonth = $this->setting_model->getEndMonth(); 
                                $session_current = $this->setting_model->getCurrentSessionName(); 
                                $centenary = substr($session_current, 0, 2); //2017-18 to 2017
                                $year_first_substring = substr($session_current, 2, 2); //2017-18 to 2017
                                $year_second_substring = substr($session_current, 5, 2); //2017-18 to 18
                                
                                 $dateObj   = DateTime::createFromFormat('!m', $startMonth);
                                // $startMonthName = $dateObj->format('F'); // March
                                $startMonthName = $first_month;
                                 $dateObj2   = DateTime::createFromFormat('!m', $endMonth);
                                // $endMonthName = $dateObj2->format('F'); // March
                                $endMonthName = $last_month; // March
                                
                                $start_timestamp = strtotime($startMonthName.' '. $centenary.$year_first_substring);
                                $end_timestamp    = strtotime($endMonthName.' '. $centenary.$year_second_substring);
                                $startMonth = date('Y-m-01', $start_timestamp);
                                $endMonth  = date('Y-m-t', $end_timestamp); // A leap year!
                         
                                $total_number_days = 0;
                                $start = $month = strtotime($startMonth);
                                $end = strtotime($endMonth);
                                if( $school_days ){
                                    $school_days = is_serialized( $school_days )?unserialize($school_days):$school_days; 
                                    $school_days = $school_days != '' ? $school_days:array();
                                } 

                                $header_month = $month;
                                $primary_header_month = $month;
                                $selection_header_month = $month;
                                $body_month = $month;

                                ?> 
                                <table class="table table-striped" border="2">
                                    <thead>
                                        <tr><th style="text-align: center;" colspan="14"><h4><?php echo $attendance_type; ?></h4></th></tr>
                                        <tr>
                                            <?php
                                            while($header_month < $end){
                                                $monthly_name = date('M', $header_month);   
                                                $monthly_name_f = date('F_Y', $header_month);   
                                                $header_month = strtotime("+1 month", $header_month); 
                                                $monthly_timestamp = strtotime($monthly_name); 
                                                $monthly_first_second = date('Y-m-01', $monthly_timestamp);
                                                $monthly_last_second  = date('Y-m-t', $monthly_timestamp);  
                                                if(isset($school_days[$monthly_name_f])){
                                                    $number_days = $school_days[$monthly_name_f]; 
                                                }  
                                                echo "<th style='color:red; text-align:center;'>".$monthly_name."</th>"; 
                                            }  
                                            ?> 
                                        </tr> 
                                    </thead>
                                    <tbody>
                                        <?php

                                            while($body_month < $end){
                                                $monthly_name = date('M', $body_month);   
                                                $monthly_name_f = date('F_Y', $body_month);   
                                                $body_month = strtotime("+1 month", $body_month); 
                                                $monthly_timestamp = strtotime($monthly_name); 
                                                $monthly_first_second = date('Y-m-01', $monthly_timestamp);
                                                $monthly_last_second  = date('Y-m-t', $monthly_timestamp);  
                                                if(isset($school_days[$monthly_name_f])){
                                                    $number_days = $school_days[$monthly_name_f]; 
                                                }  
                                                echo "<td style='color:green; text-align:center;'><strong>".$number_days."</strong></td>"; 
                                            }  
                                            ?> 
                                    </tbody>
                            </table>
                            <form id='form2' action="<?php echo site_url('teacher/stuattendence/import_save') ?>"  method="post" accept-charset="utf-8">
                                <button type="submit" class="btn btn-info pull-right" style="margin-bottom: 10px;">Save Attendance Number</button>
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr> 
                                            <th width="15%">Fullname</th>
                                            <?php 
                                            $counter = 0; 
                                           
                                            while($selection_header_month < $end){
                                                $monthly_name = date('M', $selection_header_month);    
                                                $selection_header_month = strtotime("+1 month", $selection_header_month);  
                                                 
                                                ?>

                                                 <th  width="2%"  style='text-align:center;'><?php echo $monthly_name; ?></th>  
                                                
                                                <?php  
                                                $counter++; 
                                            }  
                                            ?> 
                                            <th  width="2%" style='text-align:center;'>Total</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php 
										if( !empty( $resultlist_male )){
											?>
											<td><b>Male</b></td>
											<?php
											$row_count = 1;
											foreach ($resultlist_male as $key => $value) { 
												$primary_header_month = $month;

												 $this->db->select('*');
													$this->db->from('student_attendance_custom');  
													$this->db->where('student_id', $value['id']); 
													$this->db->where('attendance_type', $attendance_type); 
													$this->db->where('session_id', $session_id); 
												   
													$query = $this->db->get(); 
													$query_row_array = $query->row();  
													if( $query_row_array ){
														$attendance_array = unserialize($query_row_array->attendance);
													} else {
														$attendance_array = array();
													} 

												?>
												<tr>
													<td>  
														<?php echo $value['lastname'].', '.$value['firstname'].' '.$value['middlename']; ?>
														<input type="hidden" name="studentdetail[<?php echo $row_count; ?>][student_id]" value="<?php echo $value['id']; ?>">
														<input type="hidden" name="studentdetail[<?php echo $row_count; ?>][attendance_type]" value="<?php echo $attendance_type; ?>">
													</td>
													<?php
													$current_total = 0;
													while($primary_header_month < $end){
														$monthly_name = date('M', $primary_header_month);   
														$monthly_name2 = date('M_Y', $primary_header_month);   
														$monthly_name_f = date('F_Y', $primary_header_month);   
														$primary_header_month = strtotime("+1 month", $primary_header_month); 
														if(isset($school_days[$monthly_name_f])){
															$number_days = $school_days[$monthly_name_f]; 
														} 

														if( isset( $attendance_array[$monthly_name2] ) && $attendance_array[$monthly_name2] ) {
															$current_value = $attendance_array[$monthly_name2]; 
														} else {
															$current_value = 0; 
														}
														  $current_total += $current_value;
														
														?>

														<td><input type="text" class="form-control" name="studentdetail[<?php echo $row_count; ?>][attendance][<?php echo $monthly_name2; ?>]"  value="<?php echo $current_value; ?>"/></td>  
														
														<?php  
														$counter++; 
													}   
													?> 
													<td style="text-align: center;">
														<?php echo $current_total; ?>
													</td>
												</tr>
												<?php
												$row_count++;
											}
										}
										if( !empty( $resultlist_female )){
											?>
											<td><b>Female</b></td>
											<?php
											$row_count = 1;
											foreach ($resultlist_female as $key => $value) { 
												$primary_header_month = $month;

												 $this->db->select('*');
													$this->db->from('student_attendance_custom');  
													$this->db->where('student_id', $value['id']); 
													$this->db->where('attendance_type', $attendance_type); 
													$this->db->where('session_id', $session_id); 
												   
													$query = $this->db->get(); 
													$query_row_array = $query->row();  
													if( $query_row_array ){
														$attendance_array = unserialize($query_row_array->attendance);
													} else {
														$attendance_array = array();
													} 

												?>
												<tr>
													<td>  
														<?php echo $value['lastname'].', '.$value['firstname'].' '.$value['middlename']; ?>
														<input type="hidden" name="studentdetails[<?php echo $row_count; ?>][student_id]" value="<?php echo $value['id']; ?>">
														<input type="hidden" name="studentdetails[<?php echo $row_count; ?>][attendance_type]" value="<?php echo $attendance_type; ?>">
													</td>
													<?php
													$current_total = 0;
													while($primary_header_month < $end){
														$monthly_name = date('M', $primary_header_month);   
														$monthly_name2 = date('M_Y', $primary_header_month);   
														$monthly_name_f = date('F_Y', $primary_header_month);   
														$primary_header_month = strtotime("+1 month", $primary_header_month); 
														if(isset($school_days[$monthly_name_f])){
															$number_days = $school_days[$monthly_name_f]; 
														} 

														if( isset( $attendance_array[$monthly_name2] ) && $attendance_array[$monthly_name2] ) {
															$current_value = $attendance_array[$monthly_name2]; 
														} else {
															$current_value = 0; 
														}
														  $current_total += $current_value;
														
														?>

														<td><input type="text" class="form-control" name="studentdetails[<?php echo $row_count; ?>][attendance][<?php echo $monthly_name2; ?>]"  value="<?php echo $current_value; ?>"/></td>  
														
														<?php  
														$counter++; 
													}   
													?> 
													<td style="text-align: center;">
														<?php echo $current_total; ?>
													</td>
												</tr>
												<?php
												$row_count++;
											}
										}
                                        ?>
                                    </tbody>
                                </table>

                                <button type="submit" class="btn btn-info pull-right">Save Attendance Number</button>
                            </form> 
                            </div> 
                            <?php
                        }
                        ?>
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
                        url: base_url + "teacher/sections/getByClass",
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
                });
                var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
                $('#date').datepicker({                       
                    format: date_format,
                    autoclose: true
                });
            });
        </script>