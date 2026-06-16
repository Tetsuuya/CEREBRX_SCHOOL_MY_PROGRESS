<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-shopping-cart"></i>Load</h1>
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
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student Name</th>
                                        <th>Grade</th>
                                        <th>Section</th>
                                        <th>Load Balance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <?php
                                        $count = 1;
                                        foreach ($studentlist as $student) {
                                            ?>
                                            <tr>
                                                 <td class="mailbox-name"><?php echo $count ?></td>
                                                 <td class="mailbox-name"><?php echo $student['lastname'].', '.$student['firstname'].' '.$student['middlename']; ?></td>
                                                 <td class="mailbox-name"><?php echo $student['class']; ?></td>
                                                 <td class="mailbox-name"><?php echo $student['section']; ?></td>
                                                 <td class="mailbox-name"><?php echo $student['load_balance']; ?></td>
                                                 <td class="mailbox-name">
													<a class="btn btn-primary btn-xs load_button" stud="<?php echo $student['id']; ?>">
                                                        <i class="fa fa-plus"></i> Add Load
                                                    </a>
													<a href="<?php echo base_url(); ?>cafeteria/orders/load_history/<?php echo $student['id']; ?>" class="btn btn-default btn-xs">
                                                        <i class="fa fa-order"></i> View Load History
                                                    </a>
												 </td>
                                            </tr>
                                            <?php
											$count++; 
                                        }
                                    ?>	
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> 
        </div> 
    </section>
</div>
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
							<h3>Student Balance: <span id="student_balance"></span></h3>
								<input type="text" name="load_amount"  class="form-control clear_button" style="height:100px;text-align:center;font-size:50px;" placeholder="Enter Load Amount" required /> 
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
        var options = {};
        options.url = "<?php echo base_url('cafeteria/student/getsearchstudentnotallow'); ?>";
        options.type = "POST";
        options.data = { "search_student": search_student };
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
		var options = {};
        options.url = "<?php echo base_url('cafeteria/student/getstd'); ?>";
        options.type = "POST";
        options.data = { "search_student": value };
        options.dataType = "json";
        options.success = function (data) {
			$('#student_balance').html('Php '+ data.load_balance )
        };
        $.ajax(options); 
		$('#load_amount').modal('show');
	});	
 </script>
