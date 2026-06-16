<style>

.no-js #loader { display: none;  }

.js #loader { display: block; position: absolute; left: 100px; top: 0; }

.se-pre-con {

	position: fixed;

	left: 0px;

	top: 0px;

	width: 100%;

	height: 100%;

	z-index: 9999;

	background: url(<?php echo base_url().'uploads/schools_images/bridgette_owl.gif';?>) center no-repeat white;

}

/* added meal plan styles */
.meal-plan-radio {
    margin-right: 5px;
}

.btn-group {
    display: flex;
    gap: 10px;
}

.btn-group input[type="radio"] {
    margin-right: 3px;
}

.flash-message {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px;
    border-radius: 4px;
    color: #fff;
    font-weight: bold;
    z-index: 9999;
    display: none;
}

.flash-success {
    background-color: #28a745;
}

.flash-error {
    background-color: #dc3545;
}

.panel-pink .panel-heading {
    background-color: #e91e63;
    border-color: #e91e63;
    color: white;
}

.label-pink {
    background-color: #e91e63;
}

#student_search_unreg::-webkit-calendar-picker-indicator {
    display: none !important;
}

</style>

<div class="se-pre-con"></div>

<div class="content-wrapper">

	<div id="flashMessage" class="flash-message"></div>
    <section class="content-header">

        <h1><i class="fa fa-shopping-cart"></i>Register Student</h1>

    </section>

    <!-- Main content -->

    <section class="content">

        <div class="row">

			<div class="col-md-12">  

			    <?php if ($this->session->flashdata('msg')) { ?> <div >  <?php echo $this->session->flashdata('msg') ?> </div> <?php } ?>

			    <div class="box box-primary">

                    <div class="box-header ptbnull">

                        <h3 class="box-title titlefix"><?php echo 'Auto Loading'; ?></h3>                   

                    </div>

                    <div class="box-body">

                    	<br />

	                    <div class="box-body">

                            <!-- Second Form: Session Dropdown with Remarks -->

                            <h5 class="mb-3">Run Auto Load</h5>

                            <form action="https://cerebrox.thinkfast.app/cafeteria/student/auto_load" method="POST" id="loadForm">

                                <div class="row mb-3">

                                    <div class="col-md-2 col-12 mb-3 mb-md-0">

                                        <label for="sessionDropdown" class="form-label fw-bold">Select School Year:</label>

                                        <select class="form-control" id="sessionDropdown" name="session_id">

                                            <option value=""><?php echo $this->lang->line('select'); ?></option>

											<?php

											foreach ($sessionlist as $session) {

												?>

												<option value="<?php echo $session['id'] ?>" <?php if ($session['id'] == $session_id) echo "selected=selected" ?>><?php echo $session['session'] ?></option>

												<?php

												$count++;

											}

											?>

                                        </select>

                                    </div>

                                    <div class="col-md-10 col-12">

                                        <label for="remarks" class="form-label fw-bold">Remarks</label>

                                        <input type="text" class="form-control" id="remarks" name="remarks" placeholder="Enter any remarks..." required>

                                    </div>

                                </div>

                                <div class="row g-3">

                                    <div class="col-12" style="display:flex;justify-content: end;padding: 15px;">

                                        <button type="button" id="review_load" data-session="20" class="btn btn-warning w-100 ml-auto" data-bs-toggle="modal" data-bs-target="#loadReviewModal">Review Load</button>

                                        <button type="button" class="btn btn-success w-100" style="margin-left: 8px;" onclick="confirmSubmit()">Load Now</button>

                                    </div>

                                </div>

                            </form>

                            

                            <script>

                                // function confirmSubmit() {

                                //     // Confirmation prompt before form submission

                                //     if (confirm("Are you sure you want to trigger auto load?")) {

                                //         // If confirmed, submit the form

                                //         document.getElementById("loadForm").submit();

                                //     } else {

                                //         // If canceled, do nothing

                                //         return false;

                                //     }

                                // }

                                // $('#review_load').on('click', function(){

                                //   $('#loadReviewModal').modal('show');

                                // });

								function confirmSubmit() {
									// Get the remarks field value
									var remarks = document.getElementById("remarks").value.trim();
									var sessionId = document.getElementById("sessionDropdown").value;
									
									// Check if remarks is empty
									if (!remarks) {
										alert("Please enter remarks before proceeding.");
										return false;
									}

									// Check if session is selected
									if (!sessionId) {
										alert("Please select a School Year before proceeding.");
										return false;
									}
									
									// If remarks is filled and session selected, show confirmation dialog
									if (confirm("Are you sure you want to trigger auto load?")) {
										document.getElementById("loadForm").submit();
									}
									
									return false;
								}

                            </script>

                            

	                        <!-- First Form: Upload CSV/Excel -->

                            <h5 class="mb-3">Override Meal Plans</h5>

                            <form action="https://cerebrox.thinkfast.app/cafeteria/student/import_smp_excel" method="POST" enctype="multipart/form-data" class="mb-4">

                                <div class="row mb-3"  style="padding: 15px;padding-top: 0px;">

                                    <div class="col-12">

                                        <label for="mealPlanFile" class="form-label fw-bold">Upload CSV/Excel File</label>

                                        <div style="display: flex;">

                                            <div style="width: 200px;background-color: #d8d8d8; padding: 3px; margin-right: 8px;border-radius: 4px;">

                                                <input type="file" class="form-control" id="mealPlanFile" onchange="handleFileUpload(event)" name="mealPlanFile" accept=".csv, .xlsx" style="background-color: #d8d8d8;" required>

                                                <input type="hidden" id="jsonData" name="jsonData" />

                                            </div>

                                            <button type="submit" class="btn btn-primary w-100">Upload File</button>

                                            <button type="button" id="view_format" class="btn btn-primary w-100" style="margin-left: 8px;" data-bs-toggle="modal" data-bs-target="#viewFormatModal"><i class="fa fa-eye"></i></button>

                                        </div>

                                    </div>

                                </div>

                            </form>

                            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>

                            <script>

                                $('#view_format').on('click', function(){

                                  $('#viewFormatModal').modal('show');

                                });

                                

                                function handleFileUpload(event) {

                                    const file = event.target.files[0];

                                    

                                    if (file) {

                                        const reader = new FileReader();

                                        

                                        reader.onload = function(e) {

                                            const data = e.target.result;

                                            

                                            // Read the Excel file

                                            const workbook = XLSX.read(data, { type: 'binary' });

                                            

                                            // Get the first sheet

                                            const sheetName = workbook.SheetNames[0];

                                            const sheet = workbook.Sheets[sheetName];

                                            

                                            // Convert the sheet to JSON

                                            const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

                            

                                            // Log the JSON data (for debugging)

                                            console.log(jsonData);

                                            

                                            // Set the JSON data into the hidden input field

                                            document.getElementById('jsonData').value = JSON.stringify(jsonData);

                                        };

                                        

                                        // Read the file as binary string

                                        reader.readAsBinaryString(file);

                                    }

    }

                            </script>

                        </div>

                    </div>

                </div>

                <div class="box box-primary">

                    <div class="box-header ptbnull">

                        <h3 class="box-title titlefix"><?php echo 'Search Student'; ?></h3>                   

                    </div>

                    <div class="box-body">

                    	<br />

	                    <div class="box-body">

							<?php echo $this->customlib->getCSRF(); ?>
<!-- //kench -->
							<div class="row">
								<div class="col-sm-12">
									<div class="panel panel-info">
										<div class="panel-heading">
											<h4 class="panel-title">Search Students</h4>
										</div>
										<div class="panel-body">
											<div class="form-group">
												<div class="col-sm-2">
													<label>Select School Year:</label>
													<select id="session_id" name="session_id" class="form-control" required>
														<option value=""><?php echo $this->lang->line('select'); ?></option>
														<?php
														foreach ($sessionlist as $session) {
															?>
															<option value="<?php echo $session['id'] ?>" <?php if ($session['id'] == $session_id) echo "selected=selected" ?>><?php echo $session['session'] ?></option>
															<?php
														}
														?>
													</select>
												</div>
												
												<div class="col-sm-8">
													<label>Student Name:</label>  
													<select class="form-control select2" id="student_select" style="width: 100%;">
														<option value="">Select a student...</option>
													</select>
													<input type="hidden" id="student_id" name="student_id">
												</div>
												
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Selected Students Section -->
							<div class="row">
								<div class="col-sm-12">
									<div class="panel panel-success">
										<div class="panel-heading">
											<h4 class="panel-title">Selected Students for Registration (<span id="student_count">0</span>)</h4>
										</div>
										<div class="panel-body">
											<div id="selected_students_container">
												<div id="no_students_message" class="alert alert-info text-center">
													<i class="fa fa-info-circle"></i> No students selected yet. Search and add students above.
												</div>
												<div id="selected_students_list" style="display: none;">
													<div class="table-responsive">
														<table class="table table-striped table-hover">
															<thead>
																<tr>
																	<th>#</th>
																	<th>Student Name</th>
																	<th>Gender</th>
																	<th>Grade</th>
																	<th>Section</th>
																	<th style="width: 200px;">Meal Plan</th>
																	<th>Action</th>
																</tr>
															</thead>
															<tbody id="students_table_body">
																<!-- Selected students will be added here -->
															</tbody>
														</table>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Registration Form -->
							<form action="<?php echo site_url('cafeteria/student/register_by_student') ?>" id="register_student" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
								<input type="hidden" name="session_id" id="form_session_id" value="<?php echo $session_id; ?>">
								<div id="hidden_student_inputs">
									<!-- Hidden inputs for selected students will be added here -->
								</div>
								
								<div class="box-footer">
									<button type="submit" name="search" id="register_selected_students" value="search" class="btn btn-danger btn-sm pull-right checkbox-toggle" formaction="<?php echo base_url(); ?>cafeteria/student/register_batch/"><i class="fa fa-minus"></i> Unregister Selected Students</button>
									<button type="button" class="btn btn-warning pull-right" id="clear_all" style="margin-right: 10px;" disabled>
										<i class="fa fa-trash"></i> Clear All
									</button>
								</div>
							</form>

							<!-- ============================================ -->
							<!-- FEATURE: UNREGISTERED STUDENT SEARCH PANEL -->
							<!-- Added: New panel to search and register unregistered students -->
							<!-- ============================================ -->
							
							<div class="row">
								<div class="col-sm-12">
									<div class="panel panel-warning">
										<div class="panel-heading">
											<h4 class="panel-title"><i class="fa fa-search"></i> Search UNREGISTERED Students</h4>
										</div>
										<div class="panel-body">
											<div class="form-group">
												<!-- School Year Dropdown for unregistered search -->
												<div class="col-sm-2">
													<label>Select School Year:</label>
													<select id="session_id_unreg" name="session_id_unreg" class="form-control" required>
														<option value=""><?php echo $this->lang->line('select'); ?></option>
														<?php
														foreach ($sessionlist as $session) {
															?>
															<option value="<?php echo $session['id'] ?>" <?php if ($session['id'] == $session_id) echo "selected=selected" ?>><?php echo $session['session'] ?></option>
															<?php
														}
														?>
													</select>
												</div>
												
												<!-- Student Name Search Field - populated via AJAX -->
												<div class="col-sm-8">
													<label>Student Name:</label>  
													<input type="text"
													list="student_list_unreg"
													class="form-control"
													id="student_search_unreg"
													placeholder="Type student name to search..." autocomplete="chrome-off">
													<datalist id="student_list_unreg"></datalist>
												</div>
												
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Selected UNREGISTERED Students Display Section -->
							<div class="row">
								<div class="col-sm-12">
									<div class="panel panel-danger">
										<div class="panel-heading">
											<h4 class="panel-title">Selected UNREGISTERED Students for Registration (<span id="student_count_unreg">0</span>)</h4>
										</div>
										<div class="panel-body">
											<div id="selected_students_container_unreg">
												<!-- Empty state message -->
												<div id="no_students_message_unreg" class="alert alert-warning text-center">
													<i class="fa fa-exclamation-triangle"></i> No unregistered students selected yet. Search and add students above.
												</div>
												<!-- Table shows when students are selected -->
												<div id="selected_students_list_unreg" style="display: none;">
													<div class="table-responsive">
														<table class="table table-striped table-hover">
															<thead>
																<tr>
																	<th>#</th>
																	<th>Student Name</th>
																	<th>Gender</th>
																	<th>Grade</th>
																	<th>Section</th>
																	<th style="width: 200px;">Meal Plan</th>
																	<th>Action</th>
																</tr>
															</thead>
															<tbody id="students_table_body_unreg">
																<!-- Selected unregistered students dynamically added here via JS -->
															</tbody>
														</table>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Registration Form for Unregistered Students -->
							<form action="<?php echo site_url('cafeteria/student/register_by_student') ?>" id="register_student_unreg" name="employeeform_unreg" method="post" accept-charset="utf-8" enctype="multipart/form-data">
								<input type="hidden" name="session_id" id="form_session_id_unreg" value="<?php echo $session_id; ?>">
								<!-- Hidden inputs container - dynamically populated with selected student IDs and meal plans -->
								<div id="hidden_student_inputs_unreg">
									<!-- Hidden inputs for selected students will be added here via JavaScript -->
								</div>
								
								<div class="box-footer">
									<!-- Register button - disabled until students are selected -->
									<button type="submit" name="search" id="register_selected_students_unreg" value="search" class="btn btn-success btn-sm pull-right checkbox-toggle" formaction="<?php echo base_url(); ?>cafeteria/student/register_batch/" disabled><i class="fa fa-plus"></i> Register Selected Students</button>
									<!-- Clear all button - disabled until students are selected -->
									<button type="button" class="btn btn-warning pull-right" id="clear_all_unreg" style="margin-right: 10px;" disabled>
										<i class="fa fa-trash"></i> Clear All
									</button>
								</div>
							</form>
							<!-- END UNREGISTERED STUDENT SEARCH FEATURE -->

							<div class="row">

								 <form action="<?php echo site_url('cafeteria/student/register_student') ?>"  id="register_student" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">

									<div class="box-body">

										<div class="form-group">

											<div class="col-sm-2">

												<label> Select School Year:</label>

												<select  id="session_id" name="session_id" class="form-control" required  >

													<option value=""><?php echo $this->lang->line('select'); ?></option>

													<?php

													foreach ($sessionlist as $session) {

														?>

														<option value="<?php echo $session['id'] ?>" <?php if ($session['id'] == $session_id) echo "selected=selected" ?>><?php echo $session['session'] ?></option>

														<?php

														$count++;

													}

													?>

												</select>

												<span class="text-danger"><?php echo form_error('session_id'); ?></span>

											</div>

											<!-- <div class="col-sm-10" >

												 <label> Name:</label>  

												 <input type="text" list="student_list" class="form-control" id="student_name" name="customer_name" autocomplete=off >

												 <datalist id="student_list"></datalist> 

												 <input type="hidden" id="student_id" name="student_id">

												   <span class="text-danger"><?php echo form_error('student_id'); ?></span>

											</div> -->

											<div class="col-sm-10" >

												 <div class="col-md-5">

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

			                                    <div class="col-md-5">

			                                        <div class="form-group">

			                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('section'); ?></label>

			                                            <select  id="section_id" name="section_id" class="form-control" >

			                                                <option value=""><?php echo $this->lang->line('select'); ?></option>

			                                            </select>

			                                            <span class="text-danger"><?php echo form_error('section_id'); ?></span>

			                                        </div>

			                                    </div>

			                                     <div class="col-md-2">

			                                        <div class="form-group">

			                                            <label for="exampleInputEmail1">Select Category</label>

			                                            <select  id="category" name="category" class="form-control" >

			                                                <option value=""><?php echo $this->lang->line('select'); ?></option>

			                                                <option value="yes" <?php echo $get_category == "yes"?"selected=selected":"";?>>Registered</option>

			                                                <option value="no" <?php echo  $get_category == "no"?"selected=selected":""?> >Not Registered</option>

			                                            </select>

			                                            <span class="text-danger"><?php echo form_error('category'); ?></span>

			                                        </div>

			                                    </div>

											</div>

										</div>

									</div>

									<div class="box-footer">

										<?php 

										if((!empty( $studentlistmale) && !empty( $get_category )) || !empty( $studentlistfemale) && !empty( $get_category ) ){

											?>

											 <table border="0" cellpadding="0" cellspacing="0" class="table table-striped table-bordered table-hover example1">

			                                <thead>

			                                    <tr>

			                                        <th>#<input type="checkbox" id="selectall" /></th>

			                                        <th>Last Name</th>

			                                        <th>First Name</th>

			                                        <th>Middle Name</th>
													<!-- added meal plan -->
													<th>Meal Plan</th>

			                                    </tr>

			                                </thead>

			                                <tbody>

                                        <?php

										if( !empty( $studentlistmale) ){

											?>

												<tr>

			                                        <th colspan="4">Male</th>

			                                    </tr>

											<?php

	                                        $count = 1;

	                                        foreach ($studentlistmale as $student1){



												$student_allow = $student1['allow'];

												$student_id = $student1['id'];

												$student_lastname = $student1['lastname'];

												$student_firstname = $student1['firstname'];

												$student_suffix = $student1['suffix'];

												$student_middlename = $student1['middlename'];

											   ?>

	                                            <tr>	

	                                            	<?php 

	                                            	if( $student_allow == 'yes'){

	                                            		?>

	                                            		 <td class="mailbox-name"><input type="checkbox" name="student_id[]" class="register_student_checkbox" value="<?php echo $student_id;?>" checked ><?php echo $count ?></td>

	                                            		<?php

	                                            	} else {

	                                            		?>

	                                            		 <td class="mailbox-name"><input type="checkbox" name="student_id[]" class="register_student_checkbox" value="<?php echo $student_id;?>" ><?php echo $count ?></td>

	                                            		<?php

	                                            	}

	                                            	?>

	                                                

	                                                 <td class="mailbox-name"><?php echo $student_lastname ?></td>

	                                                 <td class="mailbox-name"><?php echo $student_firstname.' '.$student_suffix ?></td>

	                                                 <td class="mailbox-name"><?php echo $student_middlename ?></td>
													<!-- added meal plan -->
													<td class="mailbox-name">
														<div class="btn-group" role="group">
															<input type="radio" 
																class="meal-plan-radio" 
																name="meal_plan_<?php echo $student_id; ?>" 
																value="cafeteria" 
																<?php echo (!isset($student1['meal_plan']) || $student1['meal_plan'] === 'cafeteria' || $student1['meal_plan'] === 'normal' ? 'checked' : ''); ?>
																data-student="<?php echo $student_id; ?>"> Cafeteria: ₱3000

															<input type="radio" 
																class="meal-plan-radio" 
																name="meal_plan_<?php echo $student_id; ?>" 
																value="subsidized_1" 
																<?php echo (isset($student1['meal_plan']) && $student1['meal_plan'] === 'subsidized_1' ? 'checked' : ''); ?>
																data-student="<?php echo $student_id; ?>"> Subsidized: ₱3500

															<input type="radio" 
																class="meal-plan-radio" 
																name="meal_plan_<?php echo $student_id; ?>" 
																value="subsidized_2" 
																<?php echo (isset($student1['meal_plan']) && $student1['meal_plan'] === 'subsidized_2' ? 'checked' : ''); ?>
																data-student="<?php echo $student_id; ?>"> Subsidized: ₱4000

															<input type="radio" 
																class="meal-plan-radio" 
																name="meal_plan_<?php echo $student_id; ?>" 
																value="subsidized_3" 
																<?php echo (isset($student1['meal_plan']) && $student1['meal_plan'] === 'subsidized_3' ? 'checked' : ''); ?>
																data-student="<?php echo $student_id; ?>"> Subsidized: ₱4500
														</div>
													</td>

	                                            </tr>

	                                            <?php

												$count++; 

	                                        }

										}

										if( !empty( $studentlistfemale) ){

											?>

												<tr>

			                                        <th colspan="4">Female</th>

			                                    </tr>

											<?php

	                                        $count = 1;

	                                        foreach ($studentlistfemale as $student1){



												$student_allow = $student1['allow'];

												$student_id = $student1['id'];

												$student_lastname = $student1['lastname'];

												$student_firstname = $student1['firstname'];

												$student_suffix = $student1['suffix'];

												$student_middlename = $student1['middlename'];

											   ?>

	                                            <tr>

	                                                 <?php 

	                                            	if( $student_allow == 'yes'){

	                                            		?>

	                                            		 <td class="mailbox-name"><input type="checkbox" name="student_id[]" class="register_student_checkbox" value="<?php echo $student_id;?>" checked /><?php echo $count ?></td>

	                                            		<?php

	                                            	} else {

	                                            		?>

	                                            		 <td class="mailbox-name"><input type="checkbox" name="student_id[]" class="register_student_checkbox" value="<?php echo $student_id;?>" /><?php echo $count ?></td>

	                                            		<?php

	                                            	}

	                                            	?>

	                                                 <td class="mailbox-name"><?php echo $student_lastname ?></td>

	                                                 <td class="mailbox-name"><?php echo $student_firstname.' '.$student_suffix ?></td>

	                                                 <td class="mailbox-name"><?php echo $student_middlename ?></td>
													<!-- added meal plan -->
													
													<td class="mailbox-name">
														<div class="btn-group" role="group">
															<input type="radio" 
																class="meal-plan-radio" 
																name="meal_plan_<?php echo $student_id; ?>" 
																value="cafeteria" 
																<?php echo (!isset($student1['meal_plan']) || $student1['meal_plan'] === 'cafeteria' || $student1['meal_plan'] === 'normal' ? 'checked' : ''); ?>
																data-student="<?php echo $student_id; ?>"> Cafeteria: ₱3000

															<input type="radio" 
																class="meal-plan-radio" 
																name="meal_plan_<?php echo $student_id; ?>" 
																value="subsidized_1" 
																<?php echo (isset($student1['meal_plan']) && $student1['meal_plan'] === 'subsidized_1' ? 'checked' : ''); ?>
																data-student="<?php echo $student_id; ?>"> Subsidized: ₱3500

															<input type="radio" 
																class="meal-plan-radio" 
																name="meal_plan_<?php echo $student_id; ?>" 
																value="subsidized_2" 
																<?php echo (isset($student1['meal_plan']) && $student1['meal_plan'] === 'subsidized_2' ? 'checked' : ''); ?>
																data-student="<?php echo $student_id; ?>"> Subsidized: ₱4000

															<input type="radio" 
																class="meal-plan-radio" 
																name="meal_plan_<?php echo $student_id; ?>" 
																value="subsidized_3" 
																<?php echo (isset($student1['meal_plan']) && $student1['meal_plan'] === 'subsidized_3' ? 'checked' : ''); ?>
																data-student="<?php echo $student_id; ?>"> Subsidized: ₱4500
														</div>
													</td>

	                                                

	                                            </tr>

	                                            <?php

												$count++; 

	                                        }

										}

										?>

	                                </tbody>

	                            </table>

											<?php

										}

										?>

									</div>

									<div class="box-footer">

										<?php 

										$get_category = isset($get_category) ? $get_category : null;

										if( $get_category == 'yes'){

											?>

											<button type="submit" name="search" value="search" class="btn btn-warning btn-sm pull-right checkbox-toggle" formaction="<?php echo base_url(); ?>cafeteria/student/unregister_batch/"><i class="fa fa-minuss"></i> Unregister Students</button>

											<?php

										} else if( $get_category == 'no'){

											?>

											<button type="submit" name="search" value="search" class="btn btn-success btn-sm pull-right checkbox-toggle" formaction="<?php echo base_url(); ?>cafeteria/student/register_batch/"><i class="fa fa-plus"></i> Register Students</button>

											<?php

										}

										?>

										

										<!-- <button type="submit" name="search" value="search" class="btn btn-success btn-sm pull-right" style='margin-right:5px;' ><i class="fa fa-plus"></i> Register Student</button> -->

									</div>

								</form>

							</div>

                        <div class="table-responsive mailbox-messages">

							<h5>List of students who are allow to order in the cafeteria</h5>

							<p>(-) balance means amount need to pay in the cafeteria.</p>

							<p>(+) balance means remaining load of the student.</p>

							 <form action="<?php echo site_url('cafeteria/student/register_student') ?>"  id="register_student" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">

								<div class="input-group col-md-10">

								  <input type="text" class="form-control" name="search" value="<?php echo $search;?>" placeholder="Please enter the firstname or middlename or lastname of the registered student you want to search" aria-label="Recipient's username" aria-describedby="basic-addon2">

								  <input type="hidden" name="session_id" value="<?php echo $session_id;?>" />

								  <div class="input-group-btn">

									<button class="btn btn-sm btn-primary" type="submit">Search</button>

								  </div>

								</div>

							</form>

							<br/>

							<!--<button class="btn btn-success" style="float: right; margin: 10px; margin-top: -5px;" id="printButton">Print All</button>-->

                           <table border="0" cellpadding="0" cellspacing="0" class="table table-striped table-bordered table-hover example">

                                <thead>

                                    <tr>

                                        <th>#</th>

                                        <th>Student Name</th>

                                        <th>Grade</th>

                                        <th>Section</th>

                                        <th>Total Spent</th>

                                        <th>Total Load</th>

                                        <th>Balance</th>

                                        <th class="no-print">Action</th>

                                    </tr>

                                </thead>

                                <tbody>

                                        <?php

										if( $studentlist ){

                                        $count = 1;

                                        foreach ($studentlist as $student){

											$student_id = $student->id;

											// $get_total_spent_list = $this->order_model->get_total_spent_list( $student_id, $session_id );

											// /* $get_total_spent = number_format($get_total_spent_list['get_total_amount'],2,".","," );

											// $get_total_load = number_format($get_total_spent_list['get_total_load'],2,".","," );

											// $balance = $get_total_load - $get_total_spent;

											// $balance =number_format($balance,2,".","," ); */

											// $get_total_spent = $get_total_spent_list['get_total_amount'];

											// $get_total_load = $get_total_spent_list['get_total_load'];

											// $balance = $get_total_load - $get_total_spent;

											$get_total_load_details = $this->studentload_model->get_total_load($student_id, $session_id);
											$get_total_load = isset($get_total_load_details['total_amount']) ? $get_total_load_details['total_amount'] : 0;

											$get_total_spent_details = $this->order_model->get_total_spent($student_id, $session_id);
											$get_total_spent = isset($get_total_spent_details['total_amount']) ? $get_total_spent_details['total_amount'] : 0;

											$balance = $get_total_load - $get_total_spent;

										   ?>

                                            <tr>

                                                 <td class="mailbox-name"><?php echo $count ?></td>

                                                 <td class="mailbox-name"><?php echo $student->lastname.', '.$student->firstname.' '.$student->middlename; ?></td>

                                                 <td class="mailbox-name"><?php echo $student->class; ?></td>

                                                 <td class="mailbox-name"><?php echo $student->section; ?></td>

                                                 <!-- <td class="mailbox-name"><?php echo $get_total_spent; ?></td>

                                                 <td class="mailbox-name"><?php echo $get_total_load; ?></td>

                                                 <td class="mailbox-name"><?php echo $balance; ?></td> -->
												<td class="mailbox-name"><?php echo number_format($get_total_spent, 2, '.', ','); ?></td>
												<td class="mailbox-name"><?php echo number_format($get_total_load, 2, '.', ','); ?></td>
												<td class="mailbox-name"><?php echo number_format($balance, 2, '.', ','); ?></td>

                                                 <td class="mailbox-name">

													<a href="<?php echo base_url(); ?>cafeteria/orders/view/<?php echo $student->id;?>?session_id=<?php echo $session_id;?>" class="btn btn-primary btn-xs">

                                                       View Profile

                                                    </a>

													<a class="btn btn-success btn-xs load_button" stud="<?php echo $student->id; ?>">

                                                      Add Load

                                                    </a>

													<a href="<?php echo base_url(); ?>cafeteria/orders/load_history/<?php echo $student->id; ?>?session_id=<?php echo $session_id;?>" class="btn btn-default btn-xs">

                                                        View Load History

                                                    </a>

													<a href="javascript:void(0);" class="btn btn-danger btn-xs unregister-student-btn" data-student-session-id="<?php echo $student->student_session_id; ?>" data-student-id="<?php echo $student->id; ?>" data-student-name="<?php echo htmlspecialchars($student->lastname.', '.$student->firstname.' '.$student->middlename); ?>" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>">

                                                       Unregister

                                                    </a>

													<?php if ($student->is_deactivate == 'no'): ?>
														<a href="<?php echo base_url(); ?>cafeteria/student/deactivate/<?php echo $student->student_session_id; ?>?session_id=<?php echo $session_id;?>" 
														class="btn btn-warning btn-xs"
														onclick="return confirm('Are you sure you want to deactivate this student?');">
														Deactivate
														</a>
													<?php else: ?>
														<a href="<?php echo base_url(); ?>cafeteria/student/activate/<?php echo $student->student_session_id; ?>?session_id=<?php echo $session_id;?>" 
														class="btn btn-info btn-xs"
														onclick="return confirm('Are you sure you want to activate this student?');">
														Activate
														</a>
													<?php endif; ?>





												 </td>

                                            </tr>

                                            <?php

											$count++; 

                                        }

									}else {

										?>

										<tr>

											<td colspan="8">

												<div class="alert alert-warning">No Results Found.</div>

											</td>

										</tr>

										<?php

									}

									?>

                                </tbody>

                            </table>

							<nav>

							<?php echo $studentlist_pagination; ?>

							</nav>

                        </div>

                    </div>

                </div>

            </div> 

        </div> 

    </section>

</div>

<form  action="<?php echo site_url('cafeteria/student/register_student') ?>"  id="section_id_submit" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">

	<input type="hidden" name="session_id" id="get_session_id">

	<input type="hidden" name="get_class_id" id="get_class_id" >

	<input type="hidden" name="get_section_id" id="get_section_id" >

	<input type="hidden" name="get_category_id" id="get_category_id" >

</form>

 <form  action="<?php echo site_url('cafeteria/student/register_student') ?>"  id="session_id_submit" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">

		<input type="hidden" id="session_id_submit_input" name="session_id" />

	</form> 

<div id="load_amount" class="modal fade" role="dialog">

    <div class="modal-dialog">

        <!-- Modal content-->

        <div class="modal-content">

            <form id="form1" action="<?php echo site_url('cafeteria/student/save_load') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">

                <div class="modal-body">

                    <div class="modal-header">

                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                        <h4 class="modal-title">Student Load</h4>

                    </div>

                    <div class="modal-body">

						<div class="row">

							<div class="col-md-12" style="margin-bottom: 10px; ">

							<h3>Available Load: <span id="student_balance"></span></h3>

								<input type="text" name="load_amount"  class="form-control clear_button" style="height:100px;text-align:center;font-size:50px;" placeholder="Enter Load Amount" required /> 

								<div class="form-group" style="margin-top:15px;">
									<label for="remarks">Remarks</label>
									<textarea name="remarks" id="remarks" class="form-control" placeholder="Enter remarks here..." rows="3" required></textarea>
								</div>

								<input type="hidden" id="load_session_id" name="load_session_id" value="<?php echo $session_id;?>" />

								<input type="hidden" id="per_page" name="per_page" value="<?php echo $per_page;?>" />

							</div>

						</div>

                    </div>

                    <div class="modal-footer">

                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to submit this amount?');">Submit Load</button>

                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    </div>

                </div>

				<input type="hidden" id="get_student_id" name="get_student_id" />

            </form> 

        </div>

    </div>

 </div>

 <!-- Modal -->

<div class="modal fade" id="loadReviewModal" tabindex="-1" aria-labelledby="loadReviewModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h5 class="modal-title" id="loadReviewModalLabel">Student Load Review</h5>

            </div>

            <div class="modal-body">

                <!-- Search Field (Sticky) -->

                <div class="mb-3">

                    <input type="text" id="searchStudent" class="form-control" placeholder="Search student..." style="position: sticky; top: 0; z-index: 1;">

                </div>



                <!-- Student List (Scrollable) -->

                <ul class="list-group" id="studentList" style="height: 400px; overflow-y: auto;">

					<?php foreach ($allowed_students as $student) { ?>

						<li class="list-group-item">
							<?php echo $student->lastname . ", " . $student->firstname . " " . $student->suffix; ?>: 
							<strong>
								<?php 
								if ($student->subsidized == "yes") {

									if ($student->meal_plan == "subsidized_1") {
										echo "3500";
									} elseif ($student->meal_plan == "subsidized_2") {
										echo "4000";
									} elseif ($student->meal_plan == "subsidized_3") {
										echo "4500";
									}

								} else {
									echo ($student->gender == "Male" ? "3200" : "3000");
								}
								?>
							</strong>
						</li>

					<?php } ?>

				</ul>


            </div>

        </div>

    </div>

</div>



<!-- Modal -->

<div class="modal fade" id="viewFormatModal" tabindex="-1" aria-labelledby="viewFormatModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h5 class="modal-title" id="viewFormatModalLabel">View File Format</h5>

            </div>

            <div class="modal-body">

                <img src="https://cerebrox.thinkfast.app/uploads/view_format.png" width="100%">

            </div>

        </div>

    </div>

</div>





<script type="text/javascript">

document.getElementById('searchStudent').addEventListener('keyup', function() {

        let filter = this.value.toLowerCase();

        let studentItems = document.querySelectorAll('#studentList .list-group-item');

        

        studentItems.forEach(function(item) {

            let text = item.textContent.toLowerCase();

            item.style.display = text.includes(filter) ? '' : 'none';

        });

    });

$(document).ready(function () { 

    $("#student_name").keypress(function(){

        var search_student = $(this).val();

        var session_id = $('#session_id').val();

        var options = {};

        options.url = "<?php echo base_url('cafeteria/student/getsearchstudentallow'); ?>";

        options.type = "POST";

        options.data = { "search_student": search_student, "session_id":session_id };

        options.dataType = "json";

        options.success = function (data) {

            $("#student_list").empty();

            for(var i=0;i<data.length;i++)

            {

				if(data[i].middlename == null){

					var middlename = '';

				} else {

					var middlename = data[i].middlename;

				}

                $("#student_list").append("<option value='" + 

                data[i].lastname + ", "+ data[i].firstname + " "+ middlename + "' data-id='"+  data[i].id +"'  ></option>");

            }

        };

        $.ajax(options);

    });



    $("#student_name").bind('input', function () {

        var x = checkExists( $('#student_name').val() );

        $('#student_id').val(x);

      

    });



    function checkExists(inputValue) {

        console.log(inputValue);

        

        var x = document.getElementById("student_list");

        var i;

        var flag;

        var flagx;

        for (i = 0; i < x.options.length; i++) {

            if(inputValue == x.options[i].value){

                flag = true;

                flagx = $( x.options[i] ).attr('data-id');

            }

        }

        return flagx;

    }



	 

});



</script>



 <script type="text/javascript">

 $(document).on('click', '.load_button', function (e) {

		var value = $(this).attr('stud');

		$('.clear_button').val("");

		$('#remarks').val(""); // Clear remarks on open

		$('#student_balance').html("");

		$('#get_student_id').val(value);

		var session_id = $('#session_id').val();

		var options = {};

        options.url = "<?php echo base_url('cafeteria/student/getavailableload'); ?>";

        options.type = "POST";

        options.data = { "search_student": value, "session_id":session_id };

        options.dataType = "json";

        options.success = function (data) {

			$('#student_balance').html('Php '+ data.load_balance )

        };

        $.ajax(options); 

		$('#load_amount').modal('show');

	});	

	$(document).on('change', '#session_id', function (e) {

		   var session_id = $('#session_id').val();

		   $('#session_id_submit_input').val(session_id);

		   $('#session_id_submit').submit();

	});	

	

	$(document).on('change', '#sessionDropdown', function (e) {

		   var session_id = $('#sessionDropdown').val();

		   $('#session_id_submit_input').val(session_id);

		   $('#session_id_submit').submit();

	});	

 </script>

<script>

	$(document).ready(function() {

		// Animate loader off screen

		$(".se-pre-con").fadeOut(3000);;

	});

</script>





  <script type="text/javascript">
		// added meal plan javascript
		$(document).ready(function () {
			function showFlashMessage(message, type) {
				const flashMessage = $('#flashMessage');
				flashMessage.text(message)
					.removeClass('flash-success flash-error')
					.addClass(type === 'success' ? 'flash-success' : 'flash-error')
					.fadeIn()
					.delay(2000)
					.fadeOut();
			}

			$(document).on('change', '.meal-plan-radio', function() {
				var student_id = $(this).data('student');
				var meal_plan = $(this).val();
				
				$.ajax({
					url: '<?php echo base_url("cafeteria/student/update_meal_plan"); ?>',
					type: 'POST',
					data: {
						student_id: student_id,
						meal_plan: meal_plan
					},
					success: function(response) {
						var data = JSON.parse(response);
						if(data.status == 'success') {
							// Store the selection in localStorage
							localStorage.setItem('meal_plan_' + student_id, meal_plan);
							showFlashMessage('Meal plan updated successfully', 'success');
						} else {
							showFlashMessage('Error updating meal plan', 'error');
						}
					},
					error: function() {
						showFlashMessage('Error occurred while updating meal plan', 'error');
					}
				});
			});

			// Restore selections from localStorage on page load
			$('.meal-plan-radio').each(function() {
				var student_id = $(this).data('student');
				var saved_plan = localStorage.getItem('meal_plan_' + student_id);
				if (saved_plan && $(this).val() === saved_plan) {
					$(this).prop('checked', true);
				}
			});
		});

		$(document).ready(function () {

			var section_id_post = '<?php echo $section_id; ?>';

			var class_id_post = '<?php echo $class_id; ?>';

			var session_id_post = '<?php echo $session_id; ?>';

			var category_post = '<?php echo $get_category; ?>';

			populateSection(section_id_post, class_id_post, session_id_post);

			function populateSection(section_id_post, class_id_post, session_id_post) {

				$('#section_id').html("");

				var base_url = '<?php echo base_url() ?>';

				var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';

				$.ajax({

					type: "GET",

					url: base_url + "cafeteria/sections/getByClassBySession",

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

			}

			

			$(document).on('change', '#class_id', function (e) {

				$('#section_id').html("");

				var class_id = $(this).val();

				var session_id = $('#session_id').val();

				var base_url = '<?php echo base_url() ?>';

				var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';

				$.ajax({

					type: "GET",

					url: base_url + "cafeteria/sections/getByClassBySession",

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

			});

			 $(document).on('change', '#session_id', function (e) {

				$('#section_id').html("");

				 var section_id_post = $('#section_id').val();

				 var class_id_post = $('#class_id').val();

				var session_id_post = $('#session_id').val();

				populateSection(section_id_post, class_id_post, session_id_post);

			});

			  $(document).on('change', '#category', function (e) {

			  	 var section_id_post = $('#section_id').val();

				 var class_id_post = $('#class_id').val();

				 var session_id_post = $('#session_id').val();

				 var category_post = $('#category').val();

				 $('#get_section_id').val( section_id_post );

				 $('#get_class_id').val( class_id_post );

				 $('#get_session_id').val( session_id_post );

				 $('#get_category_id').val( category_post );

				 $('#section_id_submit').submit();

			});

			var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';

			$('#date').datepicker({

				format: date_format,

				autoclose: true

			});

		});

        </script>



        <script type="text/javascript">

	$("input#selectall").click(function(event) {

		if( this.checked ) {

			$('input.register_student_checkbox[type=checkbox]:enabled').each(function() {

					this.checked = true;

			});

		} else {

			$('input.register_student_checkbox[type=checkbox]').each(function() {

				this.checked = false;

			});

		}

	});

	$(document).ready(function () {

	    $('#printButton').on('click', function () {

            $.ajax({

                url: "https://cerebrox.thinkfast.app/cafeteria/student/fetch_all_data",

                method: "GET",

                success: function (data) {

                    console.log(data)

                    var jsonData = JSON.parse(data);

                    var printTable = $('.example').DataTable();

                    printTable.clear().rows.add(jsonData.data).draw();

                    printTable.button('.buttons-print').trigger();

                }

            });

        });

		$('.example').dataTable( {

			"order": [],

			"columnDefs": [ {

			  "targets"  : 'sorting_disabled',

			  "orderable": false,

			}],

            dom: 'Bfrtip', // Enables DataTables buttons

            buttons: [

                {

                    extend: 'print',

                    text: 'Print All',

                    exportOptions: {

                        modifier: {

                            page: 'all' // Ensures ALL rows are printed, not just the current page

                        },

                        columns: ':not(.no-print)' // Exclude columns with class 'no-print' (if needed)

                    }

                }

            ],

            "pageLength": 15, // Display 10 rows per page

        "paging": true // Pagination is enabled, but all rows will be printed

		});

	});

	//kench
	
	$(document).ready(function() {
		let selectedStudents = [];
		let studentCounter = 0;

		

		// Update session_id in form when dropdown changes
		$('#session_id').change(function() {
			$('#form_session_id').val($(this).val());
			// Clear selected students when session changes
			clearAllStudents();
			// Reload student list for new session
			loadStudentList();
		});

		// Load student list for dropdown
		function loadStudentList() {
			let session_id = $('#session_id').val();
			if (session_id) {
				$.ajax({
					url: '<?php echo site_url("cafeteria/student/get_students_ajax"); ?>',
					type: 'POST',
					data: {session_id: session_id},
					dataType: 'json',
					success: function(data) {
						console.log(data);
						$('#student_select').empty();
						$('#student_select').append('<option value="">Select a student...</option>');
						$.each(data, function(index, student) {
							
							if (!selectedStudents.some(s => s.id === student.id)) {
								$('#student_select').append(
									'<option value="' + student.id + '" ' +
									'data-name="' + student.full_name + '" ' +
									'data-gender="' + student.gender + '" ' +
									'data-meal="' + (student.meal_plan || 'cafeteria') + '" ' +
									'data-class="' + (student.class || '') + '" ' + // Changed from grade to class
									'data-section="' + (student.section || '') + '">' +
									student.full_name + '</option>'
								);
							}
						});
						
						$('#student_select').trigger('change');
					},
					error: function() {
						alert('Error loading student list. Please try again.');
					}
				});
			} else {
				$('#student_select').empty();
				$('#student_select').append('<option value="">Select a school year first...</option>');
			}
		}

		// Add student to selection when dropdown changes
		$('#student_select').change(function() {
			let studentId = $(this).val();
			if (studentId) {
				let selectedOption = $(this).find('option:selected');
				let studentName = selectedOption.data('name');
				let studentGender = selectedOption.data('gender');
				let studentMeal = selectedOption.data('meal') || 'cafeteria';
				let studentClass = selectedOption.data('class'); // Changed from grade to class
				let studentSection = selectedOption.data('section');

				// Add to selected students array
				selectedStudents.push({
					id: studentId,
					name: studentName,
					gender: studentGender,
					meal_plan: studentMeal,
					class: studentClass, // Changed
					section: studentSection
				});

				updateSelectedStudentsList();
				selectedOption.remove();
				$('#student_select').val('').trigger('change');
			}
		});

		// Manual add button (for backup)
		$('#add_student').click(function() {
			let studentId = $('#student_select').val();
			if (studentId) {
				$('#student_select').trigger('change');
			} else {
				alert('Please select a student from the dropdown first.');
			}
		});

		// Update the selected students display
		function updateSelectedStudentsList() {
			let tbody = $('#students_table_body');
			tbody.empty();

			if (selectedStudents.length === 0) {
				$('#no_students_message').show();
				$('#selected_students_list').hide();
				$('#register_selected_students, #clear_all').prop('disabled', true);
			} else {
				$('#no_students_message').hide();
				$('#selected_students_list').show();
				$('#register_selected_students, #clear_all').prop('disabled', false);

				// Update the table headers first
				$('#selected_students_list thead tr').html(`
					<th width="5%">#</th>
					<th width="25%">Student Name</th>
					<th width="10%">Gender</th>
					<th width="10%">Grade</th>
					<th width="10%">Section</th>
					<th width="30%">Meal Plan</th>
					<th width="10%">Action</th>
				`);

				$.each(selectedStudents, function(index, student) {
					console.log(student);
					let mealPlanRadios = 
						'<div class="btn-group btn-group-xs" role="group">' +
							'<label class="btn btn-default ' + (student.meal_plan === 'cafeteria' ? 'active' : '') + '">' +
								'<input type="radio" ' +
									'class="meal-plan-radio" ' + 
									'name="meal_plan_' + student.id + '" ' +
									'value="cafeteria" ' +
									(student.meal_plan === 'cafeteria' ? 'checked' : '') + ' ' +
									'data-student="' + student.id + '">' +
								' Cafeteria: ₱' + (student.gender === 'Female' ? '3000' : '3200') +
							'</label>' +

							'<label class="btn btn-default ' + (student.meal_plan === 'subsidized_1' ? 'active' : '') + '">' +
								'<input type="radio" ' +
									'class="meal-plan-radio" ' + 
									'name="meal_plan_' + student.id + '" ' +
									'value="subsidized_1" ' +
									(student.meal_plan === 'subsidized_1' ? 'checked' : '') + ' ' +
									'data-student="' + student.id + '">' +
								' Subsidized: ₱3500' +
							'</label>' +

							'<label class="btn btn-default ' + (student.meal_plan === 'subsidized_2' ? 'active' : '') + '">' +
								'<input type="radio" ' +
									'class="meal-plan-radio" ' + 
									'name="meal_plan_' + student.id + '" ' +
									'value="subsidized_2" ' +
									(student.meal_plan === 'subsidized_2' ? 'checked' : '') + ' ' +
									'data-student="' + student.id + '">' +
								' Subsidized: ₱4000' +
							'</label>' +

							'<label class="btn btn-default ' + (student.meal_plan === 'subsidized_3' ? 'active' : '') + '">' +
								'<input type="radio" ' +
									'class="meal-plan-radio" ' + 
									'name="meal_plan_' + student.id + '" ' +
									'value="subsidized_3" ' +
									(student.meal_plan === 'subsidized_3' ? 'checked' : '') + ' ' +
									'data-student="' + student.id + '">' +
								' Subsidized: ₱4500' +
							'</label>' +

							
						'</div>'


					let row = $('<tr>').attr('data-student-id', student.id)
						.append($('<td>').text(index + 1))
						.append($('<td>').html('<strong>' + student.name + '</strong>'))
						.append($('<td>').html('<span class="label label-' + (student.gender === 'Male' ? 'primary' : 'pink') + '">' + student.gender + '</span>'))
						.append($('<td>').text(student['class'] || 'N/A'))
						.append($('<td>').text(student.section || 'N/A'))
						.append($('<td>').html(mealPlanRadios))
						.append($('<td>').html('<button type="button" class="btn btn-danger btn-xs remove-student" data-student-id="' + student.id + '">' +
							'<i class="fa fa-trash"></i> Remove</button>'));

					tbody.append(row);
				});
			}

			// Update counters
			$('#student_count').text(selectedStudents.length);
			$('#register_count').text(selectedStudents.length);

			// Update hidden inputs
			updateHiddenInputs();
		}

		// Handle meal plan radio button changes
		$(document).on('change', 'input[type="radio"][name^="meal_plan_"]', function() {
			let studentId = $(this).data('student-id');
			let newMealPlan = $(this).val();
			
			// Update the selectedStudents array
			let studentIndex = selectedStudents.findIndex(s => s.id == studentId);
			if (studentIndex !== -1) {
				selectedStudents[studentIndex].meal_plan = newMealPlan;
				updateHiddenInputs();
			}
			
			// Update button group visual state
			$(this).closest('.btn-group').find('label').removeClass('active');
			$(this).closest('label').addClass('active');
		});

		// Remove student event handler
		$(document).on('click', '.remove-student', function() {
			let studentId = $(this).data('student-id');
			let studentData = selectedStudents.find(s => s.id == studentId);
			
			if (studentData) {
				// Remove from selectedStudents array
				selectedStudents = selectedStudents.filter(s => s.id != studentId);
				
				// Add back to dropdown
				let option = '<option value="' + studentId + '" ' +
					'data-name="' + studentData.name + '" ' +
					'data-gender="' + studentData.gender + '" ' +
					'data-meal="' + studentData.meal_plan + '">' +
					studentData.name + '</option>';
				$('#student_select').append(option);
				
				// Update display
				updateSelectedStudentsList();
			}
		});

		// Function to update hidden inputs
		function updateHiddenInputs() {
			$('#hidden_student_inputs').empty();
			selectedStudents.forEach(function(student) {
				// student.id, student.meal_plan
				$('#hidden_student_inputs').append(
				'<input type="hidden" name="student_id[]" value="'   + student.id         + '">' +
				'<input type="hidden" name="student_meal_plan['  + student.id + ']" value="' + student.meal_plan + '">'
				);
			});
		}

		// Clear all students
		$('#clear_all').click(function() {
			if (confirm('Are you sure you want to remove all selected students?')) {
				clearAllStudents();
			}
		});

		function clearAllStudents() {
			selectedStudents = [];
			updateSelectedStudentsList();
			loadStudentList(); // Reload the full student list
		}

		// Form submission validation
		$('#register_student').submit(function(e) {
			if (selectedStudents.length === 0) {
				e.preventDefault();
				alert('Please select at least one student to register.');
				return false;
			}

			// Update meal plans from current radio button selections before submitting
			$('input[type="radio"][name^="meal_plan_"]:checked').each(function() {
				let studentId = $(this).data('student-id');
				let mealPlan = $(this).val();
				let studentIndex = selectedStudents.findIndex(s => s.id == studentId);
				if (studentIndex !== -1) {
					selectedStudents[studentIndex].meal_plan = mealPlan;
				}
			});
			
			// Update hidden inputs with latest meal plan selections
			updateHiddenInputs();

			if (!confirm('Are you sure you want to unregister ' + selectedStudents.length + ' student(s)?')) {
				e.preventDefault();
				return false;
			}
			
			return true;
		});

		// Load initial student list
		loadStudentList();
	});

	

</script>

<!-- ============================================ -->
<!-- FEATURE: UNREGISTERED STUDENTS JAVASCRIPT -->
<!-- Handles search, selection, and registration of unregistered students -->
<!-- This mirrors the functionality of the registered students search above -->
<!-- ============================================ -->
<script type="text/javascript">
$(document).ready(function() {
	// Array to store selected unregistered students
	let selectedStudentsUnreg = [];

	// Clear search results and list when school year changes
	$('#session_id_unreg').change(function() {
		$('#form_session_id_unreg').val($(this).val());
		selectedStudentsUnreg = [];
		updateSelectedStudentsListUnreg();
		$("#student_search_unreg").val('');
		$("#student_list_unreg").empty();
	});

	// Trigger AJAX search dynamically as the user types in the search bar
	$("#student_search_unreg").keyup(function() {
		var search_student = $(this).val().trim();
		var session_id = $('#session_id_unreg').val();
		
		if (!session_id) {
			return;
		}

		if (search_student.length >= 2) {
			$.ajax({
				url: "<?php echo base_url('cafeteria/student/search_unregistered_students'); ?>",
				type: "POST",
				data: { "search_student": search_student, "session_id": session_id },
				dataType: "json",
				success: function(data) {
					$("#student_list_unreg").empty();
					$.each(data, function(index, student) {
						let middlename = student.middlename ? ' ' + student.middlename : '';
						let suffix = student.suffix ? ' ' + student.suffix : '';
						let full_name = student.lastname + ', ' + student.firstname + middlename + suffix;
						
						// Only show in datalist if not already selected in the table
						if (!selectedStudentsUnreg.some(s => s.id == student.id)) {
							$("#student_list_unreg").append(
								"<option value='" + full_name + "' " +
								"data-id='" + student.id + "' " +
								"data-gender='" + student.gender + "' " +
								"data-meal='" + (student.meal_plan || 'cafeteria') + "' " +
								"data-class='" + (student.class || '') + "' " +
								"data-section='" + (student.section || '') + "'></option>"
							);
						}
					});
				}
			});
		}
	});

	// Handle selection when user clicks/presses enter on an autocomplete option
	$("#student_search_unreg").on('input', function() {
		var val = $(this).val();
		var options = $('#student_list_unreg option');
		var matchedOption = null;
		
		options.each(function() {
			if ($(this).val() === val) {
				matchedOption = $(this);
				return false;
			}
		});
		
		if (matchedOption) {
			let studentId = matchedOption.attr('data-id');
			let studentName = val;
			let studentGender = matchedOption.attr('data-gender');
			let studentMeal = matchedOption.attr('data-meal') || 'cafeteria';
			let studentClass = matchedOption.attr('data-class');
			let studentSection = matchedOption.attr('data-section');
			
			// Add selected student to table array
			selectedStudentsUnreg.push({
				id: studentId,
				name: studentName,
				gender: studentGender,
				meal_plan: studentMeal,
				class: studentClass,
				section: studentSection
			});
			
			// Update UI Selected Students table
			updateSelectedStudentsListUnreg();
			
			// Clear input search bar and dynamic datalist
			$("#student_search_unreg").val('');
			$("#student_list_unreg").empty();
		}
	});

	/**
	 * Update the display of selected unregistered students
	 * Shows/hides table and empty message, populates table rows
	 */
	function updateSelectedStudentsListUnreg() {
		let tbody = $('#students_table_body_unreg');
		tbody.empty();
		
		// Update counter
		$('#student_count_unreg').text(selectedStudentsUnreg.length);

		// Show/hide appropriate sections based on selection count
		if (selectedStudentsUnreg.length === 0) {
			$('#no_students_message_unreg').show();
			$('#selected_students_list_unreg').hide();
			$('#register_selected_students_unreg, #clear_all_unreg').prop('disabled', true);
		} else {
			$('#no_students_message_unreg').hide();
			$('#selected_students_list_unreg').show();
			$('#register_selected_students_unreg, #clear_all_unreg').prop('disabled', false);

			// Clear and update hidden inputs container
			$('#hidden_student_inputs_unreg').empty();
			
			// Build table rows for each selected student
			$.each(selectedStudentsUnreg, function(index, student) {
				// Build meal plan radio buttons
				// Default price based on gender (Male=3200, Female=3000)
				let defaultPrice = student.gender === 'Female' ? '3000' : '3200';
				
				let mealPlanRadios = 
					'<div class="btn-group btn-group-xs" role="group">' +
						// Cafeteria option
						'<label class="btn btn-default ' + (student.meal_plan === 'cafeteria' ? 'active' : '') + '">' +
							'<input type="radio" class="meal-plan-radio-unreg" ' +
								'name="meal_plan_unreg_' + student.id + '" ' +
								'value="cafeteria" ' +
								'data-student-id="' + student.id + '" ' +
								(student.meal_plan === 'cafeteria' ? 'checked' : '') + '> ' +
							'Cafeteria: ₱' + defaultPrice +
						'</label>' +
						// Subsidized 1 option
						'<label class="btn btn-default ' + (student.meal_plan === 'subsidized_1' ? 'active' : '') + '">' +
							'<input type="radio" class="meal-plan-radio-unreg" ' +
								'name="meal_plan_unreg_' + student.id + '" ' +
								'value="subsidized_1" ' +
								'data-student-id="' + student.id + '" ' +
								(student.meal_plan === 'subsidized_1' ? 'checked' : '') + '> ' +
							'Subsidized: ₱3500' +
						'</label>' +
						// Subsidized 2 option
						'<label class="btn btn-default ' + (student.meal_plan === 'subsidized_2' ? 'active' : '') + '">' +
							'<input type="radio" class="meal-plan-radio-unreg" ' +
								'name="meal_plan_unreg_' + student.id + '" ' +
								'value="subsidized_2" ' +
								'data-student-id="' + student.id + '" ' +
								(student.meal_plan === 'subsidized_2' ? 'checked' : '') + '> ' +
							'Subsidized: ₱4000' +
						'</label>' +
						// Subsidized 3 option
						'<label class="btn btn-default ' + (student.meal_plan === 'subsidized_3' ? 'active' : '') + '">' +
							'<input type="radio" class="meal-plan-radio-unreg" ' +
								'name="meal_plan_unreg_' + student.id + '" ' +
								'value="subsidized_3" ' +
								'data-student-id="' + student.id + '" ' +
								(student.meal_plan === 'subsidized_3' ? 'checked' : '') + '> ' +
							'Subsidized: ₱4500' +
						'</label>' +
					'</div>';

				// Build table row
				let row = $('<tr>')
					.append($('<td>').text(index + 1))
					.append($('<td>').html('<strong>' + student.name + '</strong>'))
					.append($('<td>').html('<span class="label label-' + (student.gender === 'Male' ? 'primary' : 'pink') + '">' + student.gender + '</span>'))
					.append($('<td>').text(student.class || 'N/A'))
					.append($('<td>').text(student.section || 'N/A'))
					.append($('<td>').html(mealPlanRadios))
					.append($('<td>').html('<button type="button" class="btn btn-danger btn-xs remove-student-unreg" data-student-id="' + student.id + '"><i class="fa fa-trash"></i> Remove</button>'));
				
				tbody.append(row);

				// Add hidden inputs for form submission
				$('#hidden_student_inputs_unreg').append(
					'<input type="hidden" name="student_id[]" value="' + student.id + '">' +
					'<input type="hidden" name="student_meal_plan[' + student.id + ']" class="meal_plan_input_unreg_' + student.id + '" value="' + student.meal_plan + '">'
				);
			});
		}
	}

	/**
	 * Handle meal plan radio button changes
	 * Updates the student's meal plan in the array and hidden input
	 */
	$(document).on('change', '.meal-plan-radio-unreg', function() {
		let studentId = $(this).data('student-id');
		let mealPlan = $(this).val();
		
		// Update button group active state
		$(this).closest('.btn-group').find('label').removeClass('active');
		$(this).closest('label').addClass('active');
		
		// Update in selectedStudentsUnreg array
		let studentIndex = selectedStudentsUnreg.findIndex(s => s.id == studentId);
		if (studentIndex !== -1) {
			selectedStudentsUnreg[studentIndex].meal_plan = mealPlan;
		}
		
		// Update hidden input
		$('.meal_plan_input_unreg_' + studentId).val(mealPlan);
	});

	/**
	 * Remove student button handler
	 * Removes student from selection and adds back to dropdown
	 */
	$(document).on('click', '.remove-student-unreg', function() {
		let studentId = $(this).data('student-id');
		let studentData = selectedStudentsUnreg.find(s => s.id == studentId);
		
		if (studentData) {
			// Remove from selectedStudentsUnreg array
			selectedStudentsUnreg = selectedStudentsUnreg.filter(s => s.id != studentId);
			
			// Add back to dropdown
			let option = '<option value="' + studentId + '" ' +
				'data-name="' + studentData.name + '" ' +
				'data-gender="' + studentData.gender + '" ' +
				'data-meal="' + studentData.meal_plan + '" ' +
				'data-class="' + studentData.class + '" ' +
				'data-section="' + studentData.section + '">' +
				studentData.name + '</option>';
			$('#student_select_unreg').append(option);
			
			// Update display
			updateSelectedStudentsListUnreg();
		}
	});

	/**
	 * Clear all button handler
	 * Removes all selected students after confirmation
	 */
	$('#clear_all_unreg').click(function() {
		if (confirm('Are you sure you want to remove all selected unregistered students?')) {
			clearAllStudentsUnreg();
		}
	});

	/**
	 * Clear all selected students and reload dropdown
	 */
	function clearAllStudentsUnreg() {
		selectedStudentsUnreg = [];
		updateSelectedStudentsListUnreg();
		loadUnregisteredStudentList(); // Reload the full student list
	}

	/**
	 * Form submission validation
	 * Ensures at least one student is selected and updates meal plans
	 */
	$('#register_student_unreg').submit(function(e) {
		// Check if any students are selected
		if (selectedStudentsUnreg.length === 0) {
			e.preventDefault();
			alert('Please select at least one unregistered student to register.');
			return false;
		}

		// Update meal plans from current radio button selections before submitting
		$('input[type="radio"][name^="meal_plan_unreg_"]:checked').each(function() {
			let studentId = $(this).data('student-id');
			let mealPlan = $(this).val();
			let studentIndex = selectedStudentsUnreg.findIndex(s => s.id == studentId);
			if (studentIndex !== -1) {
				selectedStudentsUnreg[studentIndex].meal_plan = mealPlan;
				// Update hidden input
				$('.meal_plan_input_unreg_' + studentId).val(mealPlan);
			}
		});

		// Confirmation dialog
		if (!confirm('Are you sure you want to register ' + selectedStudentsUnreg.length + ' unregistered student(s)?')) {
			e.preventDefault();
			return false;
		}
		
		return true;
	});
});
</script>
<!-- END UNREGISTERED STUDENTS JAVASCRIPT -->

<!-- ============================================ -->
<!-- AJAX UNREGISTER HANDLER -->
<!-- Handles unregistering students from the main table dynamically -->
<!-- ============================================ -->
<script type="text/javascript">
$(document).ready(function() {
	// Handle unregister button click in the main registered students table
	$(document).on('click', '.unregister-student-btn', function(e) {
		e.preventDefault();
		
		var btn = $(this);
		var studentSessionId = btn.data('student-session-id');
		var studentId = btn.data('student-id');
		var studentName = btn.data('student-name');
		var sessionId = $('#session_id').val();
		var row = btn.closest('tr');
		
		// Confirm action
		if (!confirm('Are you sure you want to unregister ' + studentName + '?')) {
			return false;
		}
		
		// Disable button and show loading state
		btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Unregistering...');
		
		// Send AJAX request
		$.ajax({
			url: '<?php echo base_url("cafeteria/student/unregister_ajax"); ?>',
			type: 'POST',
			data: {
				student_session_id: studentSessionId,
				student_id: studentId,
				session_id: sessionId
			},
			dataType: 'json',
			success: function(response) {
				if (response.status === 'success') {
					// Show success message
					showFlashMessage('Student successfully unregistered!', 'success');
					
					// Remove the row from the table with animation
					row.fadeOut(400, function() {
						$(this).remove();
						
						// Renumber the remaining rows
						$('.example tbody tr').each(function(index) {
							$(this).find('td:first').text(index + 1);
						});
						
						// Check if table is empty
						if ($('.example tbody tr').length === 0) {
							$('.example tbody').html('<tr><td colspan="8"><div class="alert alert-warning">No Results Found.</div></td></tr>');
						}
					});
				} else {
					showFlashMessage('Error: ' + response.message, 'error');
					btn.prop('disabled', false).html('Unregister');
				}
			},
			error: function(xhr, status, error) {
				showFlashMessage('An error occurred while unregistering the student.', 'error');
				btn.prop('disabled', false).html('Unregister');
				console.error('AJAX Error:', error);
			}
		});
	});
	
	// Flash message helper function
	function showFlashMessage(message, type) {
		const flashMessage = $('#flashMessage');
		flashMessage.text(message)
			.removeClass('flash-success flash-error')
			.addClass(type === 'success' ? 'flash-success' : 'flash-error')
			.fadeIn()
			.delay(3000)
			.fadeOut();
	}
});
</script>
<!-- END AJAX UNREGISTER HANDLER -->