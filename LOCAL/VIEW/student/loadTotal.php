<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-shopping-cart"></i> <?php echo $title; ?></h1>
    </section>
    <!-- Main content -->
	 <section>
	 <br/>
	        <div class="row"> 
				<div class="col-lg-12 col-xs-12">
					<div class="col-lg-6 col-xs-6">
						<!-- small box -->
						<div class="small-box bg-green">
							<div class="inner">
								<h3><?php  echo number_format($totalamount,'2','.',',' )?></h3> 
								<p>Current School Year Collection</p>
							</div>
							<div class="icon">
								<i class="fa fa-rub" aria-hidden="true"></i>
							</div> 
						</div>
					</div>
					<div class="col-lg-6 col-xs-6">
						<!-- small box -->
						<div class="small-box bg-navy">
							<div class="inner">
								<h3><?php  echo number_format($totalall,'2','.',',' )?></h3> 
								<p>Total Collection</p>
							</div>
							<div class="icon">
								<i class="fa fa-rub" aria-hidden="true"></i>
							</div> 
						</div>
					</div>
			</div>
        </div>
	</section>
    <section class="content">
        <div class="row">
			<div class="col-md-6">             
                <div class="box box-primary">
					<?php
					if(count($orderlist) > 0 ) {	
					?>
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Load Transaction</h3>                   
                    </div>
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>School Year</th>
                                        <th>Total Collection</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <?php
                                        $count = 1;
                                        foreach ($orderlist as $order) {
                                            ?>
                                            <tr>
                                                 <td><?php echo $count ?></td>
                                                 <td><?php echo $order['session'] ?></td>
                                                 <td align="right"><?php echo $order['total_amount'] ?></td>
                                                 <td>
													<a class="btn btn-primary btn-xs" href="<?php echo base_url(); ?>cafeteria/orders/total_orders/?session_id=<?php echo $order['session_id'];?>">
														<i class="fa fa-reorder"> View Monthly Details</i>
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
					<?php
					}
					?>
                </div>
            </div> 
			<div class="col-md-6">             
                <div class="box box-primary">
					<?php
					if($session_selected) {	
					?>
                    <div class="box-header ptbnull">
						<h3 class="box-title titlefix">Monthly Transaction of <?php echo $get_session_name; ?> </h3>                   
						<br/>
					</div>
					
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-striped table-bordered table-hover example">
								 <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Month</th>
                                        <th>Total Collection</th>
                                    </tr>
                                </thead>
                                <tbody>
								<?php 
								 foreach ($session_selected as $selected) {
									 $count=1;
									 $monthName = $getMonthList[$selected['date_purchase']];
									?>
									<tr>
										 <td><?php echo $count ?></td>
										 <td><?php echo $monthName; ?></td>
										 <td align="right"><?php echo $selected['total_amount'] ?></td>
									</tr>
									<?php
									$count++;
								}     
								?>
								</tbody>
                            </table>
                        </div>
                    </div>
					<?php
					}
					?>
                </div>
            </div>  
        </div> 
    </section>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#btnreset").click(function () {           
            $("#form1")[0].reset();
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#btnreset").click(function () {           
            $("#form1")[0].reset();
        });
    });
</script>

<script>
  $(function () {
    //Initialize Select2 Elements
  
    //Date range picker
    $('#reservation').daterangepicker() 
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    )
 
  })
</script>

