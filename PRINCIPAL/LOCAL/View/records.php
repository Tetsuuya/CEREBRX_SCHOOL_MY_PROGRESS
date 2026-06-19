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
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Request List</h3> <br/> <br/>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    
                    <!-- Search Box and Export Buttons -->
                    <div style="padding: 10px 10px 5px 10px; background: #f9f9f9; border-bottom: 1px solid #ddd;">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Export Buttons -->
                                <button class="btn btn-default btn-sm" onclick="exportTableToCSV('gatepass_records.csv')">
                                    <i class="fa fa-file-excel-o"></i> Export CSV
                                </button>
                                <button class="btn btn-default btn-sm" onclick="window.print()">
                                    <i class="fa fa-print"></i> Print
                                </button>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php if (!empty($search)): ?>
                                    <small class="text-muted" style="line-height: 30px; display: inline-block; margin-right: 10px;">
                                        Showing results for: <strong><?php echo htmlspecialchars($search); ?></strong>
                                    </small>
                                <?php endif; ?>
                                <div style="display: inline-block; position: relative;">
                                    <input type="text" id="search_input" class="form-control input-sm" placeholder="Search student name, purpose, destination..." value="<?php echo htmlspecialchars(isset($search) ? $search : ''); ?>" autocomplete="off" style="width: 250px; height: 30px; display: inline-block;">
                                    <button class="btn btn-primary btn-sm" type="button" id="search_button" style="height: 28px; padding: 5px 10px; margin-left: 5px;">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                    <?php if (!empty($search)): ?>
                                        <a href="<?php echo base_url('principal/gatepass/records'); ?>" class="btn btn-default btn-sm" style="height: 30px; padding: 5px 10px; margin-left: 5px;">Clear</a>
                                    <?php endif; ?>
                                    <!-- Suggestions dropdown positioned relative to parent -->
                                    <div id="search_suggestions" style="position: absolute; z-index: 1000; background: white; border: 1px solid #ddd; display: none; max-height: 300px; overflow-y: auto; width: 250px; box-shadow: 0 2px 4px rgba(0,0,0,0.2); margin-top: 2px; left: 0; top: 30px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Search Box -->
                    
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-striped table-bordered table-hover">
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
                                                <?php if( $request['status'] == "pending"){  ?>
                                                <a href="<?php echo base_url(); ?>principal/gatepass/delete_data/<?php echo $request['id']; ?>"class="btn btn-danger btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('Are you sure you want to Delete this request?');">Delete
                                                </a>
                                                <a href="<?php echo base_url(); ?>principal/gatepass/approved_data/<?php echo $request['id']; ?>"class="btn btn-success btn-xs"  data-toggle="tooltip" title="Approve" onclick="return confirm('Are you sure you want to Approve this request?');">Approved
                                                </a>
                                                <a href="<?php echo base_url(); ?>principal/gatepass/declined_data/<?php echo $request['id']; ?>"class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Declined" onclick="return confirm('Are you sure you want to Decline this request?');">Declined
                                                </a>
                                                <?php }  ?>
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
                                <li><a href="<?php echo base_url('principal/gatepass/records?page=' . ($current_page - 1) . $search_param); ?>">«</a></li>
                            <?php else: ?>
                                <li class="disabled"><span>«</span></li>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $current_page - 2);
                            $end_page = min($total_pages, $current_page + 2);
                            
                            if ($start_page > 1):
                            ?>
                                <li><a href="<?php echo base_url('principal/gatepass/records?page=1' . $search_param); ?>">1</a></li>
                                <?php if ($start_page > 2): ?>
                                    <li class="disabled"><span>...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <?php if ($i == $current_page): ?>
                                    <li class="active"><span><?php echo $i; ?></span></li>
                                <?php else: ?>
                                    <li><a href="<?php echo base_url('principal/gatepass/records?page=' . $i . $search_param); ?>"><?php echo $i; ?></a></li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <?php if ($end_page < $total_pages): ?>
                                <?php if ($end_page < $total_pages - 1): ?>
                                    <li class="disabled"><span>...</span></li>
                                <?php endif; ?>
                                <li><a href="<?php echo base_url('principal/gatepass/records?page=' . $total_pages . $search_param); ?>"><?php echo $total_pages; ?></a></li>
                            <?php endif; ?>
                            
                            <?php if ($current_page < $total_pages): ?>
                                <li><a href="<?php echo base_url('principal/gatepass/records?page=' . ($current_page + 1) . $search_param); ?>">»</a></li>
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

<style>
@media print {
    /* Hide elements during print */
    .box-header, .box-footer, .btn, .main-sidebar, .main-header, .content-header {
        display: none !important;
    }
    
    /* Show only the table */
    .box-body {
        padding: 0 !important;
    }
    
    table {
        width: 100% !important;
        font-size: 10px !important;
    }
    
    /* Hide action column */
    th:last-child, td:last-child {
        display: none !important;
    }
}
</style>

<script>
$(document).ready(function() {
    // Live search with autocomplete
    var searchTimeout;
    var $searchInput = $('#search_input');
    var $searchButton = $('#search_button');
    var $suggestions = $('#search_suggestions');
    
    // Function to perform search
    function performSearch() {
        var searchTerm = $searchInput.val().trim();
        if (searchTerm.length > 0) {
            window.location.href = '<?php echo base_url('principal/gatepass/records?search='); ?>' + encodeURIComponent(searchTerm);
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
                url: '<?php echo base_url('principal/gatepass/search_students'); ?>',
                type: 'POST',
                data: { search_term: searchTerm },
                dataType: 'json',
                success: function(response) {
                    console.log('Search response:', response); // Debug log
                    $suggestions.empty();
                    
                    if (response && response.length > 0) {
                        var html = '<ul style="list-style: none; padding: 0; margin: 0;">';
                        response.forEach(function(student) {
                            html += '<li style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;" ' +
                                   'data-name="' + student.lastname + '">' +
                                   '<strong>' + student.lastname + '</strong>, ' + student.firstname + ' ' + (student.middlename || '') +
                                   '</li>';
                        });
                        html += '</ul>';
                        $suggestions.html(html).show();
                        
                        // Click handler for suggestions
                        $suggestions.find('li').on('click', function() {
                            var selectedName = $(this).data('name');
                            $searchInput.val(selectedName);
                            $suggestions.hide();
                            // Trigger search
                            window.location.href = '<?php echo base_url('principal/gatepass/records?search='); ?>' + encodeURIComponent(selectedName);
                        });
                    } else {
                        $suggestions.html('<div style="padding: 8px 12px; color: #999;">No students found</div>').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Search error:', error); // Debug log
                    $suggestions.html('<div style="padding: 8px 12px; color: #red;">Error loading suggestions</div>').show();
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

// Export table to CSV
function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll("table tr");
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        
        for (var j = 0; j < cols.length - 1; j++) { // Skip last column (Actions)
            var cellText = cols[j].innerText.replace(/"/g, '""'); // Escape quotes
            row.push('"' + cellText + '"');
        }
        
        csv.push(row.join(","));
    }
    
    // Download CSV file
    var csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    var downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>
