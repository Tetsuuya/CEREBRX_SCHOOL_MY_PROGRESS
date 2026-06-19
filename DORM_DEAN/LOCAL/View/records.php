<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Gatepass</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-4">
                <!-- Horizontal Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Pass</h3>
                    </div><!-- /.box-header -->
                    <form id="form1" action="<?php echo base_url('dormitorydean/gatepass/save_data') ?>"  method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php if ($this->session->flashdata('msg')) { ?>
                                <?php echo $this->session->flashdata('msg') ?>
                            <?php } ?>
                            <?php
                            if (isset($error_message)) {
                                echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                            }
                            ?>
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Student <span style="color:red;">*</span></label>
                                <select id="student_id" name="student_id" class="form-control">
                                    <option value=""  >Select</option>
                                    <?php
                                    if( $studentlist ){
                                        foreach ($studentlist as $student_key => $student_value) {

                                            ?>
                                             <option value="<?php echo $student_value['student_id']; ?>"  ><?php echo $student_value['lastname'].', '.$student_value['firstname'].' '.$student_value['middlename']; ?></option>
                                            <?php
                                            // code...
                                        }

                                    }
                                    ?>
                                </select> 
                                <span class="text-danger"><?php echo form_error('student'); ?></span>
                            </div>
							 <div class="form-group">
                                <label for="exampleInputEmail1">Exit Date <span style="color:red;">*</span></label>
                                <input id="exit_date" name="exit_date" placeholder="" type="date" class="form-control date" required="required" />
                                <span class="text-danger"><?php echo form_error('exit_date'); ?></span>
                            </div> 
                             <div class="form-group">
                                <label for="exampleInputEmail1">Exit Time <span style="color:red;">*</span></label>
                                <input id="exit_time" name="exit_time" placeholder="" type="time" class="form-control date" required="required" />
                                <span class="text-danger"><?php echo form_error('exit_time'); ?></span>
                            </div>          
                            <div class="form-group">
                                <label for="exampleInputEmail1">Return Date <span style="color:red;">*</span></label>
                                <input id="return_date" name="return_date" placeholder="" type="date" class="form-control date" required="required"/>
                                <span class="text-danger"><?php echo form_error('return_date'); ?></span>
                            </div> 
                            <div class="form-group">
                                <label for="exampleInputEmail1">Return Time <span style="color:red;">*</span></label>
                                <input id="return_time" name="return_time" placeholder="" type="time" class="form-control date" required="required"/>
                                <span class="text-danger"><?php echo form_error('return_time'); ?></span>
                            </div> 
                            <div class="form-group">
                                <label for="exampleInputEmail1">Purpose/Reason <span style="color:red;">*</span></label>
                                <textarea  id="purpose" name="purpose" placeholder="Purpose/Reason" type="text" class="form-control" required="required"></textarea> 
                                <span class="text-danger"><?php echo form_error('purpose'); ?></span>
                            </div> 
                            <div class="form-group">
                                <label for="exampleInputEmail1">Destination <span style="color:red;">*</span></label>
                                <textarea  id="destination" name="destination" placeholder="destination" type="text" class="form-control" required="required"></textarea> 
                                <span class="text-danger"><?php echo form_error('destination'); ?></span>
                            </div> 
                            <div class="form-group"> 
                                <label class="radio-inline">
                                    <input type="radio" name="type_of_gatepass" value="regular" checked>Regular Gatepass
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="type_of_gatepass" value="campus" >Campus Leave
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="type_of_gatepass" value="emergency" >Emergency Gatepass
                                </label>
                            </div> 

                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </form>
                </div>

            </div><!--/.col (right) -->
            <!-- left column -->
            <div class="col-md-8">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Request List</h3> <br/> <br/>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    
                    <!-- Search Box Below Title -->
                    <div style="padding: 10px 10px 5px 10px; background: #f9f9f9; border-bottom: 1px solid #ddd;">
                        <div class="row">
                            <div class="col-md-6">
                                <?php if (!empty($search)): ?>
                                    <small class="text-muted" style="line-height: 30px;">Showing results for: <strong><?php echo htmlspecialchars($search); ?></strong></small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <div style="display: inline-block;">
                                    <input type="text" id="search_input" class="form-control input-sm" placeholder="Search by lastname..." value="<?php echo htmlspecialchars(isset($search) ? $search : ''); ?>" autocomplete="off" style="width: 250px; height: 30px; display: inline-block;">
                                    <button class="btn btn-primary btn-sm" type="button" id="search_button" style="height: 28px; padding: 5px 10px; margin-left: 5px;">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                    <?php if (!empty($search)): ?>
                                        <a href="<?php echo base_url('dormitorydean/gatepass/records'); ?>" class="btn btn-default btn-sm" style="height: 30px; padding: 5px 10px; margin-left: 5px;">Clear</a>
                                    <?php endif; ?>
                                </div>
                                <div id="search_suggestions" style="position: absolute; z-index: 1000; background: white; border: 1px solid #ddd; display: none; max-height: 300px; overflow-y: auto; width: 250px; box-shadow: 0 2px 4px rgba(0,0,0,0.2); margin-top: 2px; right: 10px;"></div>
                            </div>
                        </div>
                    </div>
                    <!-- End Search Box -->
                    
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr> 
                                        <th>Student</th> 
                                        <th>Exit Date</th> 
                                        <th>Time</th> 
                                        <th>Return Date</th> 
                                        <th>Time</th> 
                                        <th>Purpose</th> 
                                        <th>Destination</th> 
                                        <th>Status</th> 
                                        <th>Type</th> 
                                        <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($listofrequest as $request) {
                                        if( $request['status']  == "pending"){
                                            $status_display = '<button class="btn btn-warning btn-xs">Pending</button';
                                        } elseif( $request['status']  == "approve"){
                                            $status_display = '<button class="btn btn-success btn-xs">Approved</button';
                                        } elseif( $request['status']  == "denied"){
                                            $status_display = '<button class="btn btn-danger btn-xs">Declined</button';
                                        }
                                        ?>
                                        <tr>
                                            <td class="mailbox-name"><?php echo $request['lastname'].', '.$request['firstname'].' '.$request['middlename']; ?></td>
                                            <td class="mailbox-name"><?php echo $request['exit_date']; ?></td>
                                            <td class="mailbox-name"><?php echo $request['exit_time']; ?></td>
                                            <td class="mailbox-name"><?php echo $request['return_date']; ?></td>
                                            <td class="mailbox-name"><?php echo $request['return_time']; ?></td>
                                            <td class="mailbox-name"><?php echo ucwords( $request['purpose'] ); ?></td> 
                                            <td class="mailbox-name"><?php echo ucwords( $request['destination'] ); ?></td>
                                            <td class="mailbox-name"><?php echo $status_display; ?></td>
                                            <td class="mailbox-name"><?php echo ucwords( $request['type'] ); ?></td>
                                            <td class="mailbox-date pull-right">
                                                <!-- <a href="<?php echo base_url(); ?>classes/edit/<?php echo $request['id']; ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                    <i class="fa fa-pencil"></i>
                                                </a> -->
                                                <?php 
                                                // Show actions ONLY for Regular and Emergency (NOT for Campus Leave)
                                                if( $request['status'] == "pending" && $request['type'] != "campus" ){  
                                                ?>
                                                <a href="<?php echo base_url(); ?>dormitorydean/gatepass/delete_data/<?php echo $request['id']; ?>"class="btn btn-danger btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('Are you sure you want to Delete this request?');">Delete
                                                </a>
                                                <a href="<?php echo base_url(); ?>dormitorydean/gatepass/approved_data/<?php echo $request['id']; ?>"class="btn btn-success btn-xs"  data-toggle="tooltip" title="Approve" onclick="return confirm('Are you sure you want to Approve this request?');">Approved
                                                </a>
                                                <a href="<?php echo base_url(); ?>dormitorydean/gatepass/declined_data/<?php echo $request['id']; ?>"class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Declined" onclick="return confirm('Are you sure you want to Decline this request?');">Declined
                                                </a>
                                                <?php 
                                                } elseif( $request['type'] == "campus" ) {
                                                    // Campus Leave: Read-only, no actions
                                                    echo '<span class="text-muted"><i>Managed by Principal</i></span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                            <?php
                                        }
                                        ?>

                                </tbody>
                            </table><!-- /.table -->



                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                    
                    <!-- Pagination -->
                    <?php if (isset($total_pages) && $total_pages > 1): ?>
                    <div class="box-footer clearfix">
                        <ul class="pagination pagination-sm no-margin pull-right">
                            <?php 
                            $search_param = !empty($search) ? '&search=' . urlencode($search) : '';
                            ?>
                            <?php if ($current_page > 1): ?>
                                <li><a href="<?php echo base_url('dormitorydean/gatepass/records?page=' . ($current_page - 1) . $search_param); ?>">«</a></li>
                            <?php else: ?>
                                <li class="disabled"><span>«</span></li>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $current_page - 2);
                            $end_page = min($total_pages, $current_page + 2);
                            
                            if ($start_page > 1):
                            ?>
                                <li><a href="<?php echo base_url('dormitorydean/gatepass/records?page=1' . $search_param); ?>">1</a></li>
                                <?php if ($start_page > 2): ?>
                                    <li class="disabled"><span>...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <?php if ($i == $current_page): ?>
                                    <li class="active"><span><?php echo $i; ?></span></li>
                                <?php else: ?>
                                    <li><a href="<?php echo base_url('dormitorydean/gatepass/records?page=' . $i . $search_param); ?>"><?php echo $i; ?></a></li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <?php if ($end_page < $total_pages): ?>
                                <?php if ($end_page < $total_pages - 1): ?>
                                    <li class="disabled"><span>...</span></li>
                                <?php endif; ?>
                                <li><a href="<?php echo base_url('dormitorydean/gatepass/records?page=' . $total_pages . $search_param); ?>"><?php echo $total_pages; ?></a></li>
                            <?php endif; ?>
                            
                            <?php if ($current_page < $total_pages): ?>
                                <li><a href="<?php echo base_url('dormitorydean/gatepass/records?page=' . ($current_page + 1) . $search_param); ?>">»</a></li>
                            <?php else: ?>
                                <li class="disabled"><span>»</span></li>
                            <?php endif; ?>
                        </ul>
                        
                        <div class="pull-left">
                            Showing <?php echo (($current_page - 1) * 15 + 1); ?> 
                            to <?php echo min($current_page * 15, $total_records); ?> 
                            of <?php echo $total_records; ?> entries
                            <?php if (!empty($search)): ?>
                                <span class="text-muted">(filtered from search)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <!-- End Pagination -->
                    
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->

        </div>
        <div class="row">
            <!-- left column -->

            <!-- right column -->
            <div class="col-md-12">

            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->


<script>
// Destroy DataTables for this specific table to use server-side pagination
$(document).ready(function() {
    var table = $('.example');
    if ($.fn.DataTable && $.fn.DataTable.isDataTable(table)) {
        table.DataTable().destroy();
    }
    
    // Live search with autocomplete
    var searchTimeout;
    var $searchInput = $('#search_input');
    var $searchButton = $('#search_button');
    var $suggestions = $('#search_suggestions');
    
    // Function to perform search
    function performSearch() {
        var searchTerm = $searchInput.val().trim();
        if (searchTerm.length > 0) {
            window.location.href = '<?php echo base_url('dormitorydean/gatepass/records?search='); ?>' + encodeURIComponent(searchTerm);
        }
    }
    
    // Search button click handler
    $searchButton.on('click', function() {
        performSearch();
    });
    
    $searchInput.on('keyup', function() {
        clearTimeout(searchTimeout);
        var searchTerm = $(this).val().trim();
        
        if (searchTerm.length === 0) {
            $suggestions.hide().empty();
            return;
        }
        
        // Show suggestions after user stops typing for 300ms
        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '<?php echo base_url('dormitorydean/gatepass/search_students'); ?>',
                type: 'POST',
                data: { search_term: searchTerm },
                dataType: 'json',
                success: function(response) {
                    $suggestions.empty();
                    
                    if (response && response.length > 0) {
                        var html = '<ul class="list-group" style="margin-bottom: 0;">';
                        $.each(response, function(index, student) {
                            var fullname = student.lastname + ', ' + student.firstname + ' ' + student.middlename;
                            var exitDate = student.exit_date || 'N/A';
                            var status = student.status || 'N/A';
                            var type = student.type || 'N/A';
                            
                            html += '<li class="list-group-item" style="cursor: pointer; padding: 10px;" data-name="' + student.lastname + '">';
                            html += '<strong>' + fullname + '</strong><br>';
                            html += '<small>Exit: ' + exitDate + ' | Status: ' + status + ' | Type: ' + type + '</small>';
                            html += '</li>';
                        });
                        html += '</ul>';
                        
                        $suggestions.html(html).show();
                        
                        // Handle suggestion click
                        $suggestions.find('li').on('click', function() {
                            var selectedName = $(this).data('name');
                            $searchInput.val(selectedName);
                            $suggestions.hide();
                            // Trigger search
                            window.location.href = '<?php echo base_url('dormitorydean/gatepass/records?search='); ?>' + encodeURIComponent(selectedName);
                        });
                    } else {
                        $suggestions.html('<div class="alert alert-info" style="margin: 5px;">No students found</div>').show();
                    }
                },
                error: function() {
                    $suggestions.html('<div class="alert alert-danger" style="margin: 5px;">Error loading suggestions</div>').show();
                }
            });
        }, 300);
    });
    
    // Handle Enter key to search
    $searchInput.on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            performSearch();
        }
    });
    
    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search_input, #search_suggestions').length) {
            $suggestions.hide();
        }
    });
});
</script>

