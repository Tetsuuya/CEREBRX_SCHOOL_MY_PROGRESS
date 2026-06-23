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
        <h1>
            <i class="fa fa-users"></i> Employee Data</h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">       
            <div class="col-md-12">              
                <div class="box box-primary" id="example">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Employee List</h3>
                        <a href="<?php echo base_url(); ?>idproduction/faculties/add/" class="btn btn-success btn-sm pull-right"  data-toggle="tooltip" title="<?php echo $this->lang->line('add'); ?>">
                                <i class="fa fa-plus"></i> Add New Employee
                            </a> 
                        <br/>

                    </div>

                    <div class="box-body">

                        <div class="mailbox-controls">

                        </div>

                        <div class="table-responsive mailbox-messages">

                                <table class="table table-striped table-bordered table-hover example">

                                <thead>

                                    <tr>

                                        <th>#</th>

                                        <th>Photo</th> 

                                        <th>Lastname</th>

                                        <th>Firstname</th>

                                        <th>Middlename</th>   

                                        <th>ID Number</th>   

                                        <th>DOB</th>   

                                        <th>TIN</th> 

                                        <th>SSS</th> 

                                        <th>PhilHealth</th> 

                                        <th>PagIbig</th> 

                                        <th>PRC</th> 

                                        <th>Hired</th> 

                                        <th>Position</th>  

                                        <th class="text-right no-print"><?php echo $this->lang->line('action'); ?>

                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                        <?php

                                   

                                        $count = 1;

                                        foreach ($employees as $employee) {

 

                                            ?>

                                            <tr> 

                                                <td class="mailbox-name"> <?php echo $count; ?> </td>

                                                <td class="mailbox-name"> 

                                                    <?php

                                                    // echo $employee['image'];

                                                    if( file_exists(  $employee['image'] )){

                                                        ?>

                                                        <img src="<?php echo base_url($employee['image']); ?>" class="img-responsive" style="width: 100px;height: 120px;"/>  

                                                        <?php  

                                                    } else {

                                                        echo '<span class="label label-danger">Missing Photo</span> ';

                                                    }

                                                    if( file_exists(  $employee['signature'])){

                                                        ?>

                                                        <img src="<?php echo base_url($employee['signature']); ?>" class="img-responsive"style="width: 100px;height: 100px;" /> 

                                                        <?php  

                                                    } else {

                                                        echo '<span class="label label-danger">Missing Signature</span>';

                                                    }



                                                    ?>

                                                    

                                                </td> 

                                                <td class="mailbox-name"> <?php echo $employee['lastname']; ?></td>

                                                <td class="mailbox-name"> <?php echo $employee['name']; ?></td>

                                                <td class="mailbox-name"> <?php echo $employee['middlename']; ?></td>    

                                                <td class="mailbox-name"> <?php echo $employee['id_no']; ?></td>    

                                                <td class="mailbox-name"> 

                                                    <?php 

                                                    if( $employee['dob'] == "0000-00-00"){

                                                        echo '<span class="label label-danger">Unasign</span>';

                                                    } else {

                                                        echo $employee['dob']; 

                                                    }

                                                    



                                                    ?>

                                                </td>    

                                                <td class="mailbox-name"> <?php echo $employee['tin']?$employee['tin']:'<span class="label label-danger">Unasign</span>'; ?></td>   

                                                <td class="mailbox-name"> <?php echo $employee['sss']?$employee['sss']:'<span class="label label-danger">Unasign</span>'; ?></td>   

                                                <td class="mailbox-name"> <?php echo $employee['phic']?$employee['phic']:'<span class="label label-danger">Unasign</span>'; ?></td>   

                                                <td class="mailbox-name"> <?php echo $employee['pagibig']?$employee['pagibig']:'<span class="label label-danger">Unasign</span>'; ?></td>    

                                                <td class="mailbox-name"> <?php echo $employee['prc_no']?$employee['prc_no']:'<span class="label label-danger">Unasign</span>'; ?></td>   

                                                <td class="mailbox-name"> <?php echo $employee['date_hired']?$employee['date_hired']:'<span class="label label-danger">Unasign</span>'; ?></td>   

                                                <td class="mailbox-name"> <?php echo $employee['position']?$employee['position']:'<span class="label label-danger">Unasign</span><a'; ?></td>    

                                                <td class="mailbox-date pull-right no-print">
                                                    <button 
                                                        class="btn btn-sm btn-info updaterfidbutton" 
                                                        data-employee-id="<?php echo $employee['id']; ?>"
                                                        data-employee-name="<?php echo $employee['lastname'] . ', ' . $employee['name'] . ' ' . $employee['middlename']; ?>"
                                                        data-current-rfid="<?php echo isset($employee['rfid']) ? $employee['rfid'] : ''; ?>"
                                                    >
                                                        Update RFID Number
                                                    </button>

                                                    <a href="<?php echo base_url(); ?>idproduction/faculties/edit/<?php echo $employee['id'];  ?>" class="btn btn-info btn-sm"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">

                                                        <i class="fa fa-pencil"></i> Edit

                                                    </a> 

                                                    <a href="<?php echo base_url(); ?>idproduction/faculties/generate_front/<?php echo $employee['id'];  ?>" class="btn btn-success btn-sm"  data-toggle="tooltip" title="Generate Front" target="_blank">

                                                        <i class="fa fa-card"></i> Front 

                                                    </a> 

                                                    <a href="<?php echo base_url(); ?>idproduction/faculties/generate_back/<?php echo $employee['id'];  ?>" class="btn btn-warning btn-sm"  data-toggle="tooltip" title="Generate Back" target="_blank">

                                                        <i class="fa fa-card"></i> Back 

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

<div id="updaterfidModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header alert-warning">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Updating RFID Information</h4>
                <h5 id="employee-name-display">Select an employee</h5> <!-- Changed: Dynamic display -->
            </div>
            <form action="<?php echo base_url(); ?>idproduction/faculties/update_rfid_information/" method="post">
                <input type="hidden" name="employee_id" id="modal-employee-id" value="" /> <!-- Changed: Dynamic ID -->
                <div class="modal-body">
                    <h4>RFID Information</h4>
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="rfid_number">New RFID Number:</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                value="" 
                                name="rfid_number" 
                                id="rfid_number"
                                placeholder="Enter RFID number"
                                required
                            />  
                        </div> 
                    </div>  
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary">Save</button>
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
// Your existing modal script
$(document).on('click', '.updaterfidbutton', function (e) {
    e.preventDefault();
    
    var employeeId = $(this).data('employee-id');
    var employeeName = $(this).data('employee-name');
    var currentRfid = $(this).data('current-rfid');
    
    $('#modal-employee-id').val(employeeId);
    $('#employee-name-display').text(employeeName);
    $('#rfid_number').val(currentRfid || '');
    
    $('#updaterfidModal').modal('show');
    setTimeout(function() {
        $('#rfid_number').focus();
    }, 500);
}); 

$('#updaterfidModal').on('shown.bs.modal', function() {
    $('#rfid_number').focus();
});

// Enhanced alert handling
$(document).ready(function() {
    // Auto-hide success alerts after 6 seconds
    $('.alert-success').each(function() {
        var $alert = $(this);
        setTimeout(function() {
            $alert.fadeOut('slow', function() {
                $alert.remove();
            });
        }, 6000);
    });
    
    // Add sound effect for success (optional)
    if ($('.alert-success').length > 0) {
        // You can add a success sound here if you have audio files
        // new Audio('path/to/success-sound.mp3').play();
    }
    
    // Smooth scroll to alert if it exists
    if ($('.alert').length > 0) {
        $('html, body').animate({
            scrollTop: $('.alert').offset().top - 100
        }, 500);
    }
});

// Form submission with loading state
$('#updaterfidModal form').on('submit', function() {
    var $submitBtn = $(this).find('button[type="submit"]');
    var originalText = $submitBtn.text();
    
    // Show loading state
    $submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
    
    // Re-enable button after 3 seconds (fallback)
    setTimeout(function() {
        $submitBtn.html(originalText).prop('disabled', false);
    }, 3000);
});

// Close modal on successful submission (if you want to auto-close)
$(document).ready(function() {
    <?php if ($this->session->flashdata('msg') && strpos($this->session->flashdata('msg'), 'alert-success') !== false): ?>
        $('#updaterfidModal').modal('hide');
    <?php endif; ?>
});
</script>

