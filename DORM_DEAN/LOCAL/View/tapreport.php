<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Gate Pass     </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row"> 
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Tapping Report</h3><br/> 
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>

                                        <th>Student</th>
                                        <th>Image</th> 
                                        <th>Exit Date & Time</th>  
                                        <th>Return Date & Time</th>  
                                        <th>Purpose</th> 
                                        <th>Destination</th> 
                                        <th>Status</th> 
                                        <th>Type</th> 
                                        <th>TAP Details</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if( $studentlist ){
                                        foreach ($studentlist as $student) {
                                            $student_id = $student['student_id'];
                                            $exit_date = $student['exit_date'];
                                            $return_date = $student['return_date'];
                                            $return_date = $student['return_date'];
                                            // printx($student);
                                            $tapdetails = $this->gatepass_model->getstudenttapdetails($student_id,$exit_date);

                                            if( $return_date != $exit_date){
                                                $tapdetails2 = $this->gatepass_model->getstudenttapdetails($student_id,$return_date);

                                            } else {
                                                $tapdetails2  = false;
                                            }
                                            ?>
                                            <tr>
                                                <td><?php echo $student['lastname'].', '.$student['firstname'].' '.$student['middlename']; ?></td> 
                                                <td><img src=" <?php echo site_url( $student['image']); ?>" width="100px" height="80px"></td> 
                                                <td><?php echo $student['exit_date'].' - '.$student['exit_time']; ?></td> 
                                                <td><?php echo $student['return_date'].' - '.$student['return_time']; ?></td> 
                                                <td><?php echo $student['purpose']; ?></td> 
                                                <td><?php echo $student['destination']; ?></td> 
                                                <td><?php echo $student['status']; ?></td> 
                                                <td><?php echo $student['type']; ?></td> 
                                                <td>
                                                    <?php
                                                    if(  $tapdetails  ){
                                                        foreach ($tapdetails as $tapdetails_value) {
                                                            echo $tapdetails_value."<br/>";
                                                        }
                                                    } else {
                                                        ?>
                                                        <em style="color:red;">Did Not Tap Yet</em>
                                                        <?php
                                                    } 
                                                    if(  $tapdetails2  ){
                                                        foreach ($tapdetails2 as $tapdetails_value2) {
                                                            echo $tapdetails_value2."<br/>";
                                                        }
                                                    }  
                                                    ?>
                                                </td> 
                                            </tr> 
                                            <?php
                                        }
                                    } else { 
                                        ?>
                                        <tr>
                                             <td colspan="9" align="center" style="color:red;"><em>No Data To Display</em></td> 
                                        </tr>  
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table><!-- /.table -->



                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
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

