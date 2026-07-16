<?php 
if (!isset($exempted_type_id)) { $exempted_type_id = 7; } 
$is_flag_ceremony = (isset($subject_name) && (stripos($subject_name, 'Flag Ceremony') !== FALSE || stripos($subject_name, 'Chapel') !== FALSE));
?>
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
            <i class="fa fa-calendar-check-o"></i> <?php echo $this->lang->line('attendance'); ?> </h1>
    </section>   
    <section class="content">
        <div class="row"> 
            <div class="col-md-12"> 
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <form id='form1' action="<?php echo site_url('teacher/stuattendence/index') ?>"  method="post" accept-charset="utf-8">
                        <div class="box-body"> 
                            <?php
                            if ($this->session->flashdata('msg')) {
                                echo $this->session->flashdata('msg');
                            }
                            ?>
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo "School Year"; ?></label>
                                        <select  id="session_id" name="session_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($sessionlist as $session) {
                                            ?>
                                            <option value="<?php echo $session['id'] ?>"<?php if ($session['id'] == $session_id) echo "selected=selected" ?>><?php echo $session['session'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>					
                                        <span class="text-danger"><?php echo form_error('session_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('class'); ?></label>
                                        <select  id="class_id" name="class_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($classlist as $class) { 
                                                ?> 
                                                <option value="<?php echo $class['class_id'] ?>" data-section="<?php echo $class['section_id'] ?>" data-subject="<?php echo $class['subject_id'] ?>" <?php
                                                if ($class_id == $class['class_id'] && $section_id == $class['section_id'] && $subject_id == $class['subject_id']) {
                                                    echo "selected =selected";
                                                }
                                                ?>><?php echo $class['class_name'].' - '.$class['section_name'].' - '.$class['subject_name'] ?></option>
                                                        <?php
                                                         
                                                    }
                                                    ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                        <input type="hidden" id="section_id" name="section_id" /> 
                                        <input type="hidden" id="subject_id" name="subject_id" /> 
                                    </div>
                                </div>
                                <!-- <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('class'); ?></label>
                                        <select  id="class_id" name="class_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                        <input type="hidden" id="section_id" name="section_id" /> 
                                        <input type="hidden" id="subject_id" name="subject_id" /> 
                                    </div>
                                </div> -->
                                <!--div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('section'); ?></label>
                                        <select  id="section_id" name="section_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                    </div>
                                </div-->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">
                                            <?php echo $this->lang->line('attendance'); ?>
                                            <?php echo $this->lang->line('date'); ?>
                                        </label>
                                        <input id="date" name="date" placeholder="" type="text" class="form-control"  value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>" readonly="readonly"/>
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" name="search" value="search" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                           </div>
                    </form>
                </div>
                <?php
                if (isset($resultlist_male) || isset($resultlist_female) ) {
                    ?>
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-users"></i> <?php echo $this->lang->line('student'); ?> <?php echo $this->lang->line('list'); ?></h3>
                            <div class="box-tools pull-right">

                            </div>
                        </div>
                        <div class="box-body">
                            <?php
                            if (!empty($resultlist_male) || !empty($resultlist_female)) {
                                $checked = "";
                                if (!isset($msg)) {
                                    $resultlist_male[0]['attendence_type_id'] = isset($resultlist_male[0]['attendence_type_id'] )?$resultlist_male[0]['attendence_type_id'] :'';
                                    $resultlist_female[0]['attendence_type_id'] = isset($resultlist_female[0]['attendence_type_id'] )?$resultlist_female[0]['attendence_type_id'] :'';
                                    if($resultlist_male[0]['attendence_type_id'] != "" || $resultlist_female[0]['attendence_type_id'] != ""  ) {
                                        if ($resultlist_male[0]['attendence_type_id'] != 5 || $resultlist_female[0]['attendence_type_id'] != 5 ) {
                                            ?>
                                            <div class="alert alert-success"><?php echo $this->lang->line('attendance_already_submitted_you_can_edit_record'); ?></div>
                                            <?php
                                        } else {
                                            $checked = "checked='checked'";
                                            ?>
                                            <div class="alert alert-warning"><?php echo $this->lang->line('attendance_already_submitted_as_holiday'); ?></b>, <?php echo $this->lang->line('you_can_edit_record'); ?></div>
                                            <?php
                                        }
                                    }
                                } else {
                                    ?>
                                    <div class="alert alert-success"><?php echo $this->lang->line('attendance_saved_successfully'); ?></div>
                                    <?php
                                }
                                ?>
                                <form action="<?php echo site_url('teacher/stuattendence/index') ?>" method="post">
                                    <?php echo $this->customlib->getCSRF(); ?>
                                    <div class="mailbox-controls">
                                        <span class="button-checkbox">
                                            <button type="button" class="btn btn-sm btn-primary" data-color="primary"><?php echo $this->lang->line('mark_as_holiday'); ?></button>
                                            <input type="checkbox" class="hidden" name="holiday" value="checked" <?php echo $checked; ?>/>
                                        </span>
                                        <div class="pull-right">
                                            <button type="submit" name="search" value="saveattendence" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-save"></i> <?php echo $this->lang->line('save_attendance'); ?> </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                                    <input type="hidden" name="section_id" value="<?php echo $section_id; ?>">
                                    <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
                                    <input type="hidden" name="date" value="<?php echo $date; ?>">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th> 
                                                    <!--<th class="visible-md visible-lg"><?php echo $this->lang->line('admission_no'); ?></th>
                                                    <th class="visible-md visible-lg"><?php echo $this->lang->line('lrn'); ?></th> -->
                                                    <th><?php echo $this->lang->line('name'); ?></th>
                                                    <th><?php echo $this->lang->line('status'); ?></th>
                                                    <th>Value</th>
                                                    <th class=""><?php echo $this->lang->line('attendance'); ?></th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
												<?php 
												if( !empty( $resultlist_male )  ){ 

													?>
													<tr>
														<td><b>Male</b><td>
													   
													</tr>
													<?php
													$row_count = 1;
													foreach ($resultlist_male as $key => $value) {
														if (!is_array($value) || !isset($value['student_session_id'])) {
															continue;
														}
														$student_status = isset($value['status'])?$value['status']:'';
                                                    
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <input type="hidden" name="student_session[]" value="<?php echo $value['student_session_id']; ?>">
                                                                <input  type="hidden" value="<?php echo $value['attendence_id']; ?>"  name="attendendence_id<?php echo $value['student_session_id']; ?>">
                                                                <?php echo $row_count; ?>
                                                            </td>
                                                        
                                                            <!--<td class="visible-md visible-lg">
                                                                <?php echo $value['admission_no']; ?>
                                                            </td>
                                                            <td class="visible-md visible-lg">
                                                                <?php echo $value['lrn']; ?>
                                                            </td>
                                                            -->
                                                            <td>
                                                                <?php 
                                                                $lastname = isset($value['lastname']) ? $value['lastname'] : '';
                                                                $firstname = isset($value['firstname']) ? $value['firstname'] : '';
                                                                $suffix = isset($value['suffix']) ? $value['suffix'] : '';
                                                                $middlename = isset($value['middlename']) ? $value['middlename'] : '';
                                                                echo "<b>".$lastname ."</b>, " . $firstname." ".$suffix." ".$middlename; 
                                                                ?>
                                                            </td>
                                                            <td><?php echo $value['status']; ?></td>

                                                            <td>
                                                                <?php 
                                                                    if( $value['att_type'] ){
                                                                        if( $value['att_type'] == 'Present'){
                                                                            ?>
                                                                                <label class='label-success label'><?php echo $value['att_type'];?></label>
                                                                            <?php
                                                                        } if( $value['att_type'] == 'Late'){
                                                                            ?>
                                                                                <label class='label-warning label'><?php echo $value['att_type'];?></label>
                                                                            <?php
                                                                        } if( $value['att_type'] == 'Absent'){
                                                                                ?>
                                                                                <label class='label-danger label'><?php echo $value['att_type'];?></label>
                                                                            <?php
                                                                        }
                                                                        if( $value['att_type'] == 'Excuse'){
                                                                            ?>
                                                                                <label class='label-danger label'><?php echo $value['att_type'];?></label>
                                                                            <?php
                                                                        } 
                                                                        /* if( $value['att_type'] == 'Exempted'){
                                                                            ?>
                                                                                <label class='label-warning label'><?php echo $value['att_type'];?></label>
                                                                            <?php
                                                                        } */ 
                                                                        if( strpos($value['att_type'], 'SC') !== FALSE || strpos($value['att_type'], 'OC') !== FALSE || strpos($value['att_type'], 'OSR') !== FALSE ){
                                                                            ?>
                                                                                <label class='label-info label'><?php echo $value['att_type'];?></label>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                        <?php
                                                                    } else {
                                                                        ?>
                                                                        <label class='label-default label'>No Attendance</label>
                                                                        <?php
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $c = 1;
                                                                $count = 0; 
                                                                foreach ($attendencetypeslist as $key => $type) {
                                                                    if ($type['key_value'] != "H" && $type['id'] != $exempted_type_id) {
                                                                        if (!$is_flag_ceremony && in_array($type['id'], [8, 9, 10])) {
                                                                            continue;
                                                                        }
                                                                        $att_type= str_replace(" ","_",strtolower($type['type']));
                                                                        
                                                                        if ($value['date'] != "xxx") {
                                                                            ?>
                                                                            <div class="radio radio-info radio-inline">
                                                                                <input 
                                                                                    type="radio"
                                                                                    id="attendencetype<?php echo $value['student_session_id'] . "-" . $count; ?>"
                                                                                    value="<?php echo $type['id']; ?>"
                                                                                    name="attendencetype<?php echo $value['student_session_id']; ?>"
                                                                                    <?php echo ($value['attendence_type_id'] == $type['id']) ? "checked" : ""; ?>
                                                                                    <?php echo ($value['status'] != 'active') ? 'disabled' : ''; ?>
                                                                                >
                                                                                <label for="attendencetype<?php echo $value['student_session_id'] . "-" . $count; ?>">
                                                                                    <?php 
                                                                                    $display_type = ucfirst($type['type']);
                                                                                    if (strpos($display_type, ' - ') !== FALSE) {
                                                                                        $display_type = explode(' - ', $display_type)[0];
                                                                                    }
                                                                                    echo $display_type;
                                                                                    ?>
                                                                                </label>
                                                                            </div>
                                                                            <?php
                                                                        } else {
                                                                            ?>
                                                                            <div class="radio radio-info radio-inline">
                                                                                <input 
                                                                                    <?php if ($c == 1) echo "checked"; ?> 
                                                                                    type="radio" 
                                                                                    id="attendencetype<?php echo $value['student_session_id'] . "-" . $count; ?>" 
                                                                                    value="<?php echo $type['id'] ?>" 
                                                                                    name="attendencetype<?php echo $value['student_session_id']; ?>"
                                                                                    <?php echo ($value['status'] != 'active') ? 'disabled' : ''; ?>
                                                                                >
                                                                                <label for="attendencetype<?php echo $value['student_session_id'] . "-" . $count; ?>">
                                                                                    <?php 
                                                                                    $display_type = ucfirst($type['type']);
                                                                                    if (strpos($display_type, ' - ') !== FALSE) {
                                                                                        $display_type = explode(' - ', $display_type)[0];
                                                                                    }
                                                                                    echo $display_type;
                                                                                    ?>
                                                                                </label>
                                                                            </div>
                                                                            <?php
                                                                        }
                                                                        $c++;
                                                                        $count++;
                                                                    }
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <!-- Excuse Note -->
                                                                <input type="text" 
                                                                    name="excuse_note[<?php echo $value['student_session_id']; ?>]" 
                                                                    class="form-control form-control-sm excuse-note-input" 
                                                                    placeholder="Enter excuse note" 
                                                                    id="excuse_input_<?php echo $value['student_session_id']; ?>" 
                                                                    value="<?php echo !empty($value['excuse_note']) ? htmlspecialchars($value['excuse_note']) : ''; ?>"
                                                                    <?php echo ($value['attendence_type_id'] != $excuse_type_id) ? 'style="display:none;"' : ''; ?>
                                                                    <?php echo !empty($value['excuse_note']) ? $value['excuse_note'] : ''; ?>
                                                                >

                                                                <!-- Exempted Note -->
                                                                <!-- <input type="text" 
                                                                    name="exempted_note[<?php echo $value['student_session_id']; ?>]" 
                                                                    class="form-control form-control-sm exempted-note-input" 
                                                                    placeholder="Enter exempted note" 
                                                                    id="exempted_input_<?php echo $value['student_session_id']; ?>" 
                                                                    value="<?php echo !empty($value['exempted_note']) ? htmlspecialchars($value['exempted_note']) : ''; ?>"
                                                                    <?php echo ($value['attendence_type_id'] != $exempted_type_id) ? 'style="display:none;"' : ''; ?>
                                                                    <?php echo !empty($value['exempted_note']) ? $value['exempted_note'] : ''; ?> 
                                                                > -->
                                                                

                                                                <div id="excuse_error_<?php echo $value['student_session_id']; ?>" class="text-danger small"></div>
                                                            </td>
                                                            
                                                        </tr>
                                                        <?php
                                                        $row_count++;
                                                    
													}
												}
												if( !empty( $resultlist_female )){ 
													?>
													<tr>
													   <td><b>Female</b><td>
													   
													</tr>
													<?php
													$row_count = 1;
													foreach ($resultlist_female as $key => $value) {
														if (!is_array($value) || !isset($value['student_session_id'])) {
															continue;
														}
														$student_status = isset($value['status'])?$value['status']:'';
                                                    
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <input type="hidden" name="student_session[]" value="<?php echo $value['student_session_id']; ?>">
                                                                <input  type="hidden" value="<?php echo $value['attendence_id']; ?>"  name="attendendence_id<?php echo $value['student_session_id']; ?>">
                                                                <?php echo $row_count; ?>
                                                            </td>

                                                            <!--<td class="visible-md visible-lg">
                                                                <?php echo $value['admission_no']; ?>
                                                            </td>
                                                            <td class="visible-md visible-lg">
                                                                <?php echo $value['lrn']; ?>
                                                            </td>
                                                            -->
                                                            <td>
                                                                <?php 
                                                                $lastname = isset($value['lastname']) ? $value['lastname'] : '';
                                                                $firstname = isset($value['firstname']) ? $value['firstname'] : '';
                                                                $suffix = isset($value['suffix']) ? $value['suffix'] : '';
                                                                $middlename = isset($value['middlename']) ? $value['middlename'] : '';
                                                                echo "<b>".$lastname ."</b>, " . $firstname." ".$suffix." ".$middlename; 
                                                                ?>
                                                            </td>
                                                            <td><?php echo $value['status']; ?></td>

                                                                <td>
                                                                <?php 
                                                                if( $value['att_type'] ){
                                                                    if( $value['att_type'] == 'Present'){
                                                                        ?>
                                                                            <label class='label-success label'><?php echo $value['att_type'];?></label>
                                                                        <?php
                                                                    } if( $value['att_type'] == 'Late'){
                                                                        ?>
                                                                            <label class='label-warning label'><?php echo $value['att_type'];?></label>
                                                                        <?php
                                                                    } if( $value['att_type'] == 'Absent'){
                                                                        ?>
                                                                            <label class='label-danger label'><?php echo $value['att_type'];?></label>
                                                                        <?php
                                                                    }if( $value['att_type'] == 'danger'){
                                                                        ?>
                                                                            <label class='label-success label'><?php echo $value['att_type'];?></label>
                                                                        <?php
                                                                    }/* if( $value['att_type'] == 'Exempted'){
                                                                        ?>
                                                                            <label class='label-warning label'><?php echo $value['att_type'];?></label>
                                                                        <?php
                                                                    } */  
                                                                    if( strpos($value['att_type'], 'SC') !== FALSE || strpos($value['att_type'], 'OC') !== FALSE || strpos($value['att_type'], 'OSR') !== FALSE ){
                                                                        ?>
                                                                            <label class='label-info label'><?php echo $value['att_type'];?></label>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <label class='label-default label'>No Attendance</label>
                                                                    <?php
                                                                }
                                                                    ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $c = 1;
                                                                $count = 0; 
                                                                foreach ($attendencetypeslist as $key => $type) {
                                                                    if ($type['key_value'] != "H" && $type['id'] != $exempted_type_id) {
                                                                        if (!$is_flag_ceremony && in_array($type['id'], [8, 9, 10])) {
                                                                            continue;
                                                                        }
                                                                        $att_type= str_replace(" ","_",strtolower($type['type']));
                                                                        if ($value['date'] != "xxx") {
                                                                            ?>
                                                                            <div class="radio radio-info radio-inline">
                                                                                <input 
                                                                                    type="radio"
                                                                                    id="attendencetype<?php echo $value['student_session_id'] . "-" . $count; ?>"
                                                                                    value="<?php echo $type['id']; ?>"
                                                                                    name="attendencetype<?php echo $value['student_session_id']; ?>"
                                                                                    <?php echo ($value['attendence_type_id'] == $type['id']) ? "checked" : ""; ?>
                                                                                    <?php echo ($value['status'] != 'active') ? 'disabled' : ''; ?>
                                                                                >
                                                                                <label for="attendencetype<?php echo $value['student_session_id'] . "-" . $count; ?>">
                                                                                    <?php 
                                                                                    $display_type = ucfirst($type['type']);
                                                                                    if (strpos($display_type, ' - ') !== FALSE) {
                                                                                        $display_type = explode(' - ', $display_type)[0];
                                                                                    }
                                                                                    echo $display_type;
                                                                                    ?>
                                                                                </label>
                                                                            </div>
                                                                            <?php
                                                                        } else {
                                                                            ?>
                                                                            <div class="radio radio-info radio-inline">
                                                                                <input 
                                                                                    <?php if ($c == 1) echo "checked"; ?> 
                                                                                    type="radio" 
                                                                                    id="attendencetype<?php echo $value['student_session_id'] . "-" . $count; ?>" 
                                                                                    value="<?php echo $type['id'] ?>" 
                                                                                    name="attendencetype<?php echo $value['student_session_id']; ?>"
                                                                                    <?php echo ($value['status'] != 'active') ? 'disabled' : ''; ?>
                                                                                >
                                                                                <label for="attendencetype<?php echo $value['student_session_id'] . "-" . $count; ?>">
                                                                                    <?php 
                                                                                    $display_type = ucfirst($type['type']);
                                                                                    if (strpos($display_type, ' - ') !== FALSE) {
                                                                                        $display_type = explode(' - ', $display_type)[0];
                                                                                    }
                                                                                    echo $display_type;
                                                                                    ?>
                                                                                </label>
                                                                            </div>
                                                                            <?php
                                                                        }
                                                                        $c++;
                                                                        $count++;
                                                                    }
                                                                }
                                                                ?>
                                                            </td>

                                                            <td>
                                                                <!-- Excuse Note -->
                                                                <input type="text" 
                                                                    name="excuse_note[<?php echo $value['student_session_id']; ?>]" 
                                                                    class="form-control form-control-sm excuse-note-input" 
                                                                    placeholder="Enter excuse note" 
                                                                    id="excuse_input_<?php echo $value['student_session_id']; ?>" 
                                                                    value="<?php echo !empty($value['excuse_note']) ? htmlspecialchars($value['excuse_note']) : ''; ?>"
                                                                    <?php echo ($value['attendence_type_id'] != $excuse_type_id) ? 'style="display:none;"' : ''; ?>
                                                                    <?php echo !empty($value['excuse_note']) ? $value['excuse_note'] : ''; ?>
                                                                >

                                                                <!-- Exempted Note -->
                                                                <!-- <input type="text" 
                                                                    name="exempted_note[<?php echo $value['student_session_id']; ?>]" 
                                                                    class="form-control form-control-sm exempted-note-input" 
                                                                    placeholder="Enter exempted note" 
                                                                    id="exempted_input_<?php echo $value['student_session_id']; ?>" 
                                                                    value="<?php echo !empty($value['exempted_note']) ? htmlspecialchars($value['exempted_note']) : ''; ?>"
                                                                    <?php echo ($value['attendence_type_id'] != $exempted_type_id) ? 'style="display:none;"' : ''; ?>
                                                                    <?php echo !empty($value['exempted_note']) ? $value['exempted_note'] : ''; ?>
                                                                > -->
                                                                

                                                                <div id="excuse_error_<?php echo $value['student_session_id']; ?>" class="text-danger small"></div>
                                                            </td>

                                                        </tr>
                                                        <?php
                                                        $row_count++;
                                                    
													}
                                                }
												?>
                                               
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                                <?php
                            }else {
                                ?>
                                <div class="alert alert-info">No student admitted in this Class-Section</div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
                </section>
            </div>

<!-- <script>
 var note = document.getElementById("excuse_input_");

 var noteVal = note.value.trim();

 var is_valid = true;

 document.getElementById("excuse_input".InnerText = "";);

 f (noteVal === "") {
        document.getElementById("excuse_input_").innerText = "School name is required.";
        schoolInput.focus();
        isValid = false;
    }

</script> -->


<script>
$(document).ready(function () {
    var excuse_type_id   = '<?php echo $excuse_type_id; ?>';
    var exempted_type_id = '<?php echo $exempted_type_id; ?>';

    // Show/hide input on radio change
    $(document).on('change', 'input[type=radio]', function () {
        var radio = $(this);
        var nameAttr = radio.attr('name');
        var student_session_id = nameAttr.replace('attendencetype', '');
        var selected_type_id = radio.val();

        var excuseInput   = $('#excuse_input_'   + student_session_id);
        var excuseError   = $('#excuse_error_'   + student_session_id);
        // var exemptedInput = $('#exempted_input_' + student_session_id);
        // var exemptedError = $('#exempted_error_' + student_session_id);

        // Hide both first
        excuseInput.hide().prop('disabled', true).val('');
        excuseError.text('');
        // exemptedInput.hide().prop('disabled', true).val('');
        // exemptedError.text('');

        // Then show only the relevant one
        if (selected_type_id == excuse_type_id) {
            excuseInput.show().prop('disabled', false);
        // } else if (selected_type_id == exempted_type_id) {
        //     exemptedInput.show().prop('disabled', false);
        }
    });

    // Validate on form submission
    $('form').on('submit', function (e) {
        var isValid = true;
        var hasAbsentStudents = false;

        // Clear all previous errors
        $('.excuse-note-input').next('.text-danger').text('');

        $('input[type=radio]:checked').each(function () {
            var radio = $(this);
            var student_session_id = radio.attr('name').replace('attendencetype', '');
            var selected_type_id = radio.val();

            if (selected_type_id == excuse_type_id) {
                hasAbsentStudents = true;
                var excuseInput = $('#excuse_input_' + student_session_id);
                var excuseError = $('#excuse_error_' + student_session_id);

                // if (excuseInput.val().trim() === '') {
                //     excuseError.text('Excuse note is required for absent students.');
                //     if (isValid) {
                //         excuseInput.focus();
                //     }
                //     isValid = false;
                // }
            }
        });

        // If no absent students, allow submission
        if (!hasAbsentStudents) {
            return true;
        }

        if (!isValid) {
            e.preventDefault();
            // Show a general alert if needed
            alert('Please provide excuse notes for all absent students.');
            return false;
        }

        // If we get here, validation passed
        return true;
    });

    // Enable immediate saving when excuse note is entered
    $(document).on('keyup', '.excuse-note-input', function(e) {
        if (e.which === 13) { // Enter key
            $(this).closest('form').submit();
        }
    });
});
</script>




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
        
            /*$(document).on('change', '#class_id', function (e) {
            
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
        });*/
        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
        $('#date').datepicker({
            format: date_format,
            autoclose: true
        });
    });
</script>
<script type="text/javascript">
    $(function () {
        $('.button-checkbox').each(function () {
            var $widget = $(this),
                    $button = $widget.find('button'),
                    $checkbox = $widget.find('input:checkbox'),
                    color = $button.data('color'),
                    settings = {
                        on: {
                            icon: 'glyphicon glyphicon-check'
                        },
                        off: {
                            icon: 'glyphicon glyphicon-unchecked'
                        }
                    };
            $button.on('click', function () {
                $checkbox.prop('checked', !$checkbox.is(':checked'));
                $checkbox.triggerHandler('change');
                updateDisplay();
            });
            $checkbox.on('change', function () {
                updateDisplay();
            });

            function updateDisplay() {
                var isChecked = $checkbox.is(':checked');
                $button.data('state', (isChecked) ? "on" : "off");
                $button.find('.state-icon')
                        .removeClass()
                        .addClass('state-icon ' + settings[$button.data('state')].icon);
                if (isChecked) {
                    $button
                            .removeClass('btn-success')
                            .addClass('btn-' + color + ' active');
                } else {
                    $button
                            .removeClass('btn-' + color + ' active')
                            .addClass('btn-primary');
                }
            }

            function init() {
                updateDisplay();
                if ($button.find('.state-icon').length == 0) {
                    $button.prepend('<i class="state-icon ' + settings[$button.data('state')].icon + '"></i> ');
                }
            }
            init();
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('change', '#session_id', function(e) {
            var session_id = $(this).val();
            var teacher_id = '<?php echo $teacher_id; ?>';
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';

            $.ajax({
                type: "GET",
                url: base_url + "teacher/stuattendence/getGradeBySession",
                data: {'teacher_id': teacher_id, 'session_id': session_id },
                dataType: "json",
                success: function (data) {
                    $('#class_id').empty(); // Clear the existing options
                    $('#class_id').append(div_data); // Add the default option

                    $.each(data, function (i, obj)
                    {
                        var selected = '';
                        if ('<?php echo $class_id; ?>' == obj.class_id && '<?php echo $section_id; ?>' == obj.section_id && '<?php echo $subject_id; ?>' == obj.subject_id) {
                            selected = 'selected="selected"';
                        }
                        div_data = "<option value='" + obj.class_id + "' data-section='" + obj.section_id + "' data-subject='" + obj.subject_id + "' " + selected + ">" + obj.class_name + " - " + obj.section_name + " - " + obj.subject_name + "</option>";
                        $('#class_id').append(div_data);
                    });
                    $('#class_id').append(div_data);
                }
            });
        });
    });
</script>

<script type="text/javascript">
$(document).ready(function () {
    $("#class_id").change(function(){ 

        var class_id = $(this).val();  

        var element = $(this).find('option:selected'); 
        var section_id = element.attr("data-section"); 
        var subject_id = element.attr("data-subject"); 

        $('input#section_id').val( section_id );  
        $('input#subject_id').val( subject_id );  
 
    });
});
</script>