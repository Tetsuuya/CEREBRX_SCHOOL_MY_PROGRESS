<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-shopping-cart"></i> <?php echo $title; ?> Monthly Report</h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
			<div class="col-md-12">             
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo 'Orders List'; ?></h3>                   
                    </div>
                    <div class="box-body">
                    	<br />
                    	<form id='form1' action="#"  method="post" accept-charset="utf-8">
	                        <div class="box-body">
	                            <?php echo $this->customlib->getCSRF(); ?>
	                            <div class="row">
	                              	<div class="col-md-6">
	                                    <div class="form-group">
	                                        <label for="exampleInputEmail1" class="col-md-2"><?php echo $this->lang->line('month'); ?></label>
	                                        <div class="col-md-10">
		                                        <select  id="month" name="month" class="form-control" >
		                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
		                                            <?php
													for($m=1; $m<=12; ++$m){
														$first_month = date('F', mktime(0, 0, 0, $m, 1));
														?>
														<option value="<?php echo $first_month; ?>" <?php if (set_value('school_days_first_month', $record->school_days_first_month) == $first_month) echo "selected"; ?>><?php echo $first_month; ?></option>
														<?php
													}
													?>
		                                        </select>
		                                        <span class="text-danger"><?php echo form_error('month'); ?></span>
		                                    </div>
	                                    </div>
	                                </div>
	                                <div class="col-md-6">
	                                    <div class="form-group">
	                                        <label for="exampleInputEmail1" class="col-md-2">Year</label>
	                                        <div class="col-md-10">
		                                        <select  id="year" name="year" class="form-control" >
		                                            <option value="">2017</option>
		                                            <option value="">2018</option>
		                                            <option value="">2019</option>
		                                            <option value="">2020</option>
		                                        </select>
		                                        <span class="text-danger"><?php echo form_error('year'); ?></span>
		                                    </div>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	                        <div class="box-footer">
	                        	
	                            <button type="submit" name="search" value="search" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
	                        </div>
	                    </form>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer Name</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <?php
                                        $count = 1;
                                        foreach ($orderlist as $order) {
											$payment_method = $order['payment_method'] == 'student_load'?"Student Credit Load":'Cash';
                                            ?>
                                            <tr>
                                                 <td class="mailbox-name"><?php echo $order['id'] ?></td>
                                                 <td class="mailbox-name"><?php echo $order['name'] ?></td>
                                                 <td class="mailbox-name"><?php echo $order['amount'] ?></td>
                                                 <td class="mailbox-name"><?php echo $payment_method ?></td>
                                                 <td class="mailbox-name"><?php echo $order['created_at'] ?></td>
                                            </tr>
                                            <?php
                                        }
                                        $count++;
                                  
                                    ?>	
										<tr>
											 <td class="mailbox-name">10</td>
											 <td class="mailbox-name">Precious Lee</td>
											 <td class="mailbox-name">120</td>
											 <td class="mailbox-name">normal</td>
											 <td class="mailbox-name">2018-02-11 08:15:33</td>
										</tr>
										<tr>
											 <td class="mailbox-name">11</td>
											 <td class="mailbox-name">Norman Avaro</td>
											 <td class="mailbox-name">20</td>
											 <td class="mailbox-name">normal</td>
											 <td class="mailbox-name">2018-02-11 08:15:33</td>
										</tr>
										<tr>
											 <td class="mailbox-name">12</td>
											 <td class="mailbox-name">Jessica Langit</td>
											 <td class="mailbox-name">80</td>
											 <td class="mailbox-name">normal</td>
											 <td class="mailbox-name">2018-02-10 08:15:33</td>
										</tr>
										<tr>
											 <td class="mailbox-name">13</td>
											 <td class="mailbox-name">Ericka Enriquez</td>
											 <td class="mailbox-name">65</td>
											 <td class="mailbox-name">Student Credit Load</td>
											 <td class="mailbox-name">2018-02-10 08:15:33</td>
										</tr><tr>
											 <td class="mailbox-name">14</td>
											 <td class="mailbox-name">Jean Malim Lee</td>
											 <td class="mailbox-name">120</td>
											  <td class="mailbox-name">Student Credit Load</td>
											 <td class="mailbox-name">2018-02-11 08:15:33</td>
										</tr>
										<tr>
											 <td class="mailbox-name">15</td>
											 <td class="mailbox-name">Nora Mae Tagapulot</td>
											 <td class="mailbox-name">120</td>
											  <td class="mailbox-name">Student Credit Load</td>
											 <td class="mailbox-name">2018-02-11 08:15:33</td>
										</tr><tr>
											 <td class="mailbox-name">16</td>
											 <td class="mailbox-name">James Dean Cruz</td>
											 <td class="mailbox-name">120</td>
											 <td class="mailbox-name">Student Credit Load</td>
											 <td class="mailbox-name">2018-02-11 08:15:45</td>
										</tr><tr>
											 <td class="mailbox-name">17</td>
											 <td class="mailbox-name">Daniel Ford Sinco</td>
											 <td class="mailbox-name">120</td>
											<td class="mailbox-name">Student Credit Load</td>
											 <td class="mailbox-name">2018-02-11 10:15:33</td>
										</tr><tr>
											 <td class="mailbox-name">18</td>
											 <td class="mailbox-name">Bella Gutierez</td>
											 <td class="mailbox-name">120</td>
											 <td class="mailbox-name">Student Credit Load</td>
											<td class="mailbox-name">2018-02-11 10:15:08</td>
										</tr><tr>
											 <td class="mailbox-name">19</td>
											 <td class="mailbox-name">Mariestellar Panilag</td>
											 <td class="mailbox-name">120</td>
											 <td class="mailbox-name">normal</td>
											 <td class="mailbox-name">2018-02-11 11:15:08</td>
										</tr><tr>
											 <td class="mailbox-name">20</td>
											 <td class="mailbox-name">Danilo Franco Yap</td>
											 <td class="mailbox-name">120</td>
											 <td class="mailbox-name">normal</td>
											<td class="mailbox-name">2018-02-11 11:15:08</td>
										</tr><tr>
											 <td class="mailbox-name">21</td>
											 <td class="mailbox-name">Alvar Lim</td>
											 <td class="mailbox-name">120</td>
											 <td class="mailbox-name">normal</td>
											<td class="mailbox-name">2018-02-11 11:15:08</td>
										</tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
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

