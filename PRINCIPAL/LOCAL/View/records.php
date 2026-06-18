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
                                            $status_display = '<button class="btn btn-danger btn-xs">Denied</button';
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
                            <?php if ($current_page > 1): ?>
                                <li><a href="<?php echo base_url('principal/gatepass/records?page=' . ($current_page - 1)); ?>">«</a></li>
                            <?php else: ?>
                                <li class="disabled"><span>«</span></li>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $current_page - 2);
                            $end_page = min($total_pages, $current_page + 2);
                            
                            if ($start_page > 1):
                            ?>
                                <li><a href="<?php echo base_url('principal/gatepass/records?page=1'); ?>">1</a></li>
                                <?php if ($start_page > 2): ?>
                                    <li class="disabled"><span>...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <?php if ($i == $current_page): ?>
                                    <li class="active"><span><?php echo $i; ?></span></li>
                                <?php else: ?>
                                    <li><a href="<?php echo base_url('principal/gatepass/records?page=' . $i); ?>"><?php echo $i; ?></a></li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <?php if ($end_page < $total_pages): ?>
                                <?php if ($end_page < $total_pages - 1): ?>
                                    <li class="disabled"><span>...</span></li>
                                <?php endif; ?>
                                <li><a href="<?php echo base_url('principal/gatepass/records?page=' . $total_pages); ?>"><?php echo $total_pages; ?></a></li>
                            <?php endif; ?>
                            
                            <?php if ($current_page < $total_pages): ?>
                                <li><a href="<?php echo base_url('principal/gatepass/records?page=' . ($current_page + 1)); ?>">»</a></li>
                            <?php else: ?>
                                <li class="disabled"><span>»</span></li>
                            <?php endif; ?>
                        </ul>
                        
                        <div class="pull-left">
                            Showing <?php echo (($current_page - 1) * 15 + 1); ?> 
                            to <?php echo min($current_page * 15, $total_records); ?> 
                            of <?php echo $total_records; ?> entries
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
});
</script>
