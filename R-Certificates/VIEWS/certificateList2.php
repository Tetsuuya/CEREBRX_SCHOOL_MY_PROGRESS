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
        <h1> <i class=" "></i> Certificate <small></h1>
    </section>   
    <section class="content">
        <div class="row">          
            <div class="col-md-4">              
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Create Certificate</h3>
                    </div> 
                    <form id="form1" action="<?php echo site_url('teacher/awards/create') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php if ($this->session->flashdata('msg')) { ?>
                                <?php echo $this->session->flashdata('msg') ?>
                            <?php } ?>      
							<?php echo $this->customlib->getCSRF(); ?>
							<div class="form-group">
                                <label>Grade</label>
                                <select  id="class_id" name="class_id" class="form-control" >
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php
                                    foreach ($classlist as $class) {
                                        ?>
                                        <option value="<?php echo $class['id'] ?>"><?php echo $class['class'] ?></option>
                                        <?php
                                        $count++;
                                    }
                                    ?>
                                </select>
                                <span class="class_id_error text-danger"></span>
                            </div> 
							<div class="form-group">
                                <label> Awards</label>
                                <select  id="awards_id" name="awards_id" class="form-control" disabled>
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php
                                    foreach ($awardslist as $award) {
                                        ?>
                                        <option value="<?php echo $award['id'] ?>"><?php echo $award['name'] ?></option>
                                        <?php
                                        $count++;
                                    }
                                    ?>
                                </select>
                                <span class="class_id_error text-danger"></span>
                            </div> 
                        </div>
                        <div class="box-footer">
                            <!--button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button-->
                        </div>
                    </form>
                </div> 
            </div>
            <div class="col-md-8">               
                <div class="box box-primary" id="sublist"> 
					 <div class="box-header with-border">
                        <h3 class="box-title">Student List</h3>
                    </div> 
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="top_students" class="table table-striped table-bordered table-hover  ">
                                <thead>
									<tr> 
										<th><?php echo $this->lang->line('lrn')?$this->lang->line('lrn'):'LRN'; ?></th>
										<th><?php echo $this->lang->line('student_name'); ?></th>  </th>
										<th><?php echo $this->lang->line('gender'); ?></th>  
                                        <th class="text-right no-print"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                     
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<div class="row">          
            <div class="col-md-12">  
				<div class="box box-primary" id="sublist2"> 
					<div class="box-header with-border">
                        <h3 class="box-title">Certificate History</h3>
                    </div> 
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="certificate_history" class="table table-striped table-bordered table-hover  ">
                                <thead>
									<tr> 
										<th><?php echo 'Award';?></th>
										<th><?php echo 'Type';?></th>
										<th><?php echo $this->lang->line('lrn')?$this->lang->line('lrn'):'LRN'; ?></th>
										<th><?php echo $this->lang->line('student_name'); ?></th>  
										<th><?php echo $this->lang->line('gender'); ?></th>  
										<th>Date</th>  
                                        <th class="text-right no-print"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php if (empty($certificatelist)) {
                                        ?>
                                      
                                        <?php
                                    } else {
                                        $count = 1;
                                        foreach ($certificatelist as $certificate) {
                                            ?>
                                            <tr>
                                                <td class="mailbox-name"> <?php echo $certificate['name']; ?></td> 
                                                <td class="mailbox-name"><?php echo $certificate['type']; ?></td>
                                                <td class="mailbox-name"><?php echo $certificate['lrn']; ?></td>
                                                <td class="mailbox-name"><?php echo $certificate['lastname'].', '.$certificate['firstname']; ?></td>
                                                <td class="mailbox-name"><?php echo $certificate['gender']; ?></td>
                                                <td class="mailbox-name"><?php echo $certificate['date']; ?></td>
                                                <td class="mailbox-date pull-right no-print">
                                                    <a href="<?php echo base_url(); ?>registrar/certificate/print_pdf/<?php echo $certificate['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="Print">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                    <a href="<?php echo base_url(); ?>registrar/certificate/delete/<?php echo $certificate['id'] ?>"class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('Are you sure you want to delete this item?');">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
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

<div id="confirm_certificate" class="modal fade" role="dialog">
	
		<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Confirm Certificate</h4>
				</div>
				<form class="modal_confirm_certificate" action="<?php echo site_url('registrar/certificate/create') ?>"  id="confirmcertificateform" name="confirmcertificateform" method="post" accept-charset="utf-8">
				<div class="modal-body">
					
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
							<input type="text" id="award_name" name="award_name" placeholder="Award" class="form-control" readonly > 
						</div>
					</div>
					<div class="form-group" style="margin-bottom: 10px;">
						<label class="col-sm-4">Date</label>
						<div class="col-sm-8">
							<input type="date" id="award_date" name="award_date" placeholder="Date" required="required" class="form-control" > 
						</div> 
					</div> 
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<input type="submit" class="btn btn-success" value="Save" >   
				</div>
				</form>  
			</div> 
		
	</div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#btnreset").click(function () {            
            $("#form1")[0].reset();
        });
    });
</script>

<script type="text/javascript">
 
	$("#class_id").change(function(){          
		var value = $(this).val();
		if (value != false ) { 
			$('#awards_id').removeAttr("disabled");
		}
		 
	});
	
	function confirm_student( obj ){
		var base_url = '<?php echo base_url() ?>';
		var date_today = '<?php echo date('m/d/Y') ?>';
		var student_id = $( obj ).attr( "data-id" );
		var student_fullname = $( obj ).attr( "data-fullname" );
		var awards_id = $( "#awards_id" ).val(); 
		var class_id = $( "#class_id" ).val();  
		var url = base_url + "registrar/awards/get_award_by_id";
		var form_fields = { 'awards_id':awards_id };
		  
		data = form_fields
		callback = function( json ){
			$( "#confirm_certificate input#award_name" ).val(  json.name );     
		};
		
		jQuery.post( url, data, callback, "json" );
		
		
		$( "#confirm_certificate input#student_id" ).val( student_id );  
		$( "#confirm_certificate input#student_name" ).val( student_fullname );  
		$( "#confirm_certificate input#award_id" ).val( awards_id );   
		$( "#confirm_certificate input#award_date" ).val( date_today );   
		 
		
		$('#confirm_certificate').modal('show');
	}
	$("#awards_id").change(function(){          
		var value = $(this).val(); 
		if (value != false ) {  
			var base_url = '<?php echo base_url() ?>';
			var div_data = '';
			$('#top_students tbody').empty();
			$.ajax({
				type: "GET",
				url: base_url + "registrar/certificate/get_student_random",
				data: {},
				dataType: "json",
				success: function (data) {
					$.each(data, function (i, obj)
					{  
						// div_data += '<tr><td>' + obj.admission_no + '</td><td>' + obj.lastname + ", " + obj.firstname + "</td><td>" + obj.dob + "</td><td>" + obj.gender + "</td><td class='pull-right'> <button class='btn btn-default btn-xs'  data-toggle='tooltip' title='Create Certificate' data-toggle='modal' data-target='#myModal'><i class='fa fa-print'></i> Create</button>"
						
						div_data += '<tr><td>' + obj.lrn + '</td><td>' + obj.lastname + ', ' + obj.firstname +  '</td><td>' + obj.gender + '</td><td class="pull-right"> <button class="btn btn-default btn-xs confirm_student" title="Create Certificate" data-toggle="tooltip" data-id="' + obj.id + ' "data-fullname="' + obj.lastname + ", " + obj.firstname + '" onclick="confirm_student( this )"><i class="fa fa-print"></i> Create</button>'
					});
					$('#top_students tbody').append(div_data); 
				}
			});
		} 
		 
	});

    var base_url = '<?php echo base_url() ?>';
    function printDiv(elem) {       
        Popup(jQuery(elem).html());
    }

     function Popup(data) 
        {
          
            var frame1 = $('<iframe />');
            frame1[0].name = "frame1";
            frame1.css({ "position": "absolute", "top": "-1000000px" });
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