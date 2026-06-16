<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-shopping-cart"></i><?php echo $title;?></h1>
    </section>
    <!-- Main content -->
	 <section>
	 <br/>
	        <div class="row"> 
				<div class="col-lg-12 col-xs-12">
					<div class="col-lg-3 col-xs-6">
						<!-- small box -->
						<div class="small-box bg-aqua">
							<div class="inner">
								<h3><?php echo number_format($dailyamount,'2','.',',') ?></h3> 
								<p>Daily Load Collection</p>
							</div>
							<div class="icon">
								<i class="fa fa-rub" aria-hidden="true"></i>
							</div> 
						</div>
					</div>
					 <div class="col-lg-3 col-xs-6">
						<!-- small box -->
						<div class="small-box bg-orange">
							<div class="inner">
								<h3><?php echo number_format($monthlyamount,'2','.',',') ?></h3> 
								<p>Month Load Collection</p>
							</div>
							<div class="icon">
								<i class="fa fa-rub" aria-hidden="true"></i>
							</div> 
						</div>
					</div>
					<div class="col-lg-3 col-xs-6">
						<!-- small box -->
						<div class="small-box bg-green">
							<div class="inner">
								<h3><?php  echo number_format($yearlyamount,'2','.',',' )?></h3> 
								<p>Year Load Collection</p>
							</div>
							<div class="icon">
								<i class="fa fa-rub" aria-hidden="true"></i>
							</div> 
						</div>
					</div>
					<div class="col-lg-3 col-xs-6">
						<!-- small box -->
						<div class="small-box bg-navy">
							<div class="inner">
								<h3><?php  echo number_format($totalamount,'2','.',',' )?></h3> 
								<p>Curent School Year Total Load Collection</p>
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
			<div class="col-md-12">             
                <div class="box box-primary">
				<form id='form1' action="<?php echo site_url('cafeteria/student/load_history') ?>"  method="post" accept-charset="utf-8">
	                        <div class="box-body">
	                            <?php echo $this->customlib->getCSRF(); ?>
	                            <div class="row">
                                    <div class="form-group">  
                              			<div class="col-md-1">
										  <label>Date range:</label>
										</div> 										  
                              			<div class="col-md-9">
							              
							                <div class="input-group">
							                  	<div class="input-group-addon">
							                    	<i class="fa fa-calendar"></i>
							                  	</div>
							                  	<input type="text" name="data_range" class="form-control pull-right" id="reservation" value="<?php echo isset($rangepre)?$rangepre:date('m/d/Y'); ?><?php echo " - ";?><?php echo isset($rangepos)?$rangepos:date('m/d/Y'); ?>" /> 												
											</div>							                
						              	</div>
										<div class="col-md-2">
										 <button type="submit" name="search" value="search" class="btn btn-primary btn-sm pull-left checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
										<?php 
										if( $is_range ){
											?>
											<a href="<?php echo site_url('cafeteria/student/load_history') ?>" class="btn btn-primary btn-sm pull-left checkbox-toggle" style="margin-left:2px;"><i class="fa fa-refresh"></i> Reset</a>
											<?php 
										}
										?>
										</div>
					              	</div> 
	                            </div>
	                        </div>
	                    </form>
					<?php
					if($orderlist) {	
					?>
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Today's Transaction History</h3>                   
                    </div>
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student Name</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <?php
                                        $count = 1;
                                        foreach ($orderlist as $order) {
                                            ?>
                                            <tr>
                                                 <td><?php echo $count ?></td>
                                                 <td><?php echo $order['lastname'].', '.$order['firstname'].' '.$order['middlename'] ?></td>
                                                 <td align="right"><?php echo $order['amount'] ?></td>
                                                 <td><?php echo $order['date_load'] ?></td>
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

