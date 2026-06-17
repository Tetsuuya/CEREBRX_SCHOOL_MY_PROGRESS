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
</style>
<div class="se-pre-con"></div>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-shopping-cart"></i>Register Student</h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
			<div class="col-md-12">             
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo 'Search Student'; ?></h3>                   
                    </div>
                    <div class="box-body">
                    	<br />
	                    <div class="box-body">
	                       <?php if ($this->session->flashdata('msg')) { ?> <div >  <?php echo $this->session->flashdata('msg') ?> </div> <?php } ?>
							<?php echo $this->customlib->getCSRF(); ?>
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
			                                                <option value="yes">Register</option>
			                                                <option value="no">Not Register</option>
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
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <?php
										if( $studentlist ){
                                        $count = 1;
                                        foreach ($studentlist as $student){
											$student_id = $student->id;
											$get_total_spent_list = $this->order_model->get_total_spent_list( $student_id, $session_id );
											/* $get_total_spent = number_format($get_total_spent_list['get_total_amount'],2,".","," );
											$get_total_load = number_format($get_total_spent_list['get_total_load'],2,".","," );
											$balance = $get_total_load - $get_total_spent;
											$balance =number_format($balance,2,".","," ); */
											$get_total_spent = $get_total_spent_list['get_total_amount'];
											$get_total_load = $get_total_spent_list['get_total_load'];
											$balance = $get_total_load - $get_total_spent;
										   ?>
                                            <tr>
                                                 <td class="mailbox-name"><?php echo $count ?></td>
                                                 <td class="mailbox-name"><?php echo $student->lastname.', '.$student->firstname.' '.$student->middlename; ?></td>
                                                 <td class="mailbox-name"><?php echo $student->class; ?></td>
                                                 <td class="mailbox-name"><?php echo $student->section; ?></td>
                                                 <td class="mailbox-name"><?php echo $get_total_spent; ?></td>
                                                 <td class="mailbox-name"><?php echo $get_total_load; ?></td>
                                                 <td class="mailbox-name"><?php echo $balance; ?></td>
                                                 <td class="mailbox-name">
													<a href="<?php echo base_url(); ?>cafeteria/orders/view/<?php echo $student->id;?>?session_id=<?php echo $session_id;?>" class="btn btn-primary btn-xs">
                                                       View Profile
                                                    </a>
													<!-- <a class="btn btn-success btn-xs load_button" stud="<?php echo $student->id; ?>">
                                                      Add Load
                                                    </a> -->
													<a href="<?php echo base_url(); ?>cafeteria/orders/load_history/<?php echo $student->id; ?>?session_id=<?php echo $session_id;?>" class="btn btn-default btn-xs">
                                                        View Load History
                                                    </a>
													<a href="<?php echo base_url(); ?>cafeteria/student/unregister/<?php echo $student->student_session_id; ?>?session_id=<?php echo $session_id;?>" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('Are you sure you want to remove this item?');">
                                                       Unregistered
                                                    </a>
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
<script type="text/javascript">
$(document).ready(function () { 
    $("#student_name").keypress(function(){
        var search_student = $(this).val();
        var session_id = $('#session_id').val();
        var options = {};
        options.url = "<?php echo base_url('cafeteria/student/getsearchstudentnotallow'); ?>";
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
 </script>
<script>
	$(document).ready(function() {
		// Animate loader off screen
		$(".se-pre-con").fadeOut(3000);;
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
		$('.example').dataTable( {
			"order": [],
			"columnDefs": [ {
			  "targets"  : 'sorting_disabled',
			  "orderable": false,
			}]
		});
	});
</script>