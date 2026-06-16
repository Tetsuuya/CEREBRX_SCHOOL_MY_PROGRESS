	

<div class="content-wrapper">

	<section class="content-header">

		<div class="col-md-6">

		<h2>

			<i class="fa fa-user-plus"></i>Student Transactions <small><?php echo $this->lang->line('student1'); ?></small></h2>

		</div>

		<div class="col-md-2">

			<br/>

			Select School Year:

		</div>

		<div class="col-md-4">

			<br/>

			<form id="select_session" action="<?php echo site_url('cafeteria/orders/load_history/'.$student_id ) ?>"  method="post" accept-charset="utf-8">

				<select  id="session_id_header" name="session_id" class="form-control" required  >

					<option value=""><?php echo $this->lang->line('select'); ?></option>

					<?php

					foreach ($sessionlist as $session) {

						?>

						<option value="<?php echo $session['session_id'] ?>" <?php if ($session['session_id'] == $session_id) echo "selected=selected" ?>><?php echo $session['session'] ?></option>

						<?php

						$count++;

					}

					?>

				</select>

			</form>

		</div>

	

	</section>

		 <section>

			<?php 

			$get_total_spent_details = $this->order_model->get_total_spent( $student_id, $session_id );

			$get_total_spent = isset($get_total_spent_details['total_amount'])?$get_total_spent_details['total_amount']:0;

			$get_total_load_details = $this->studentload_model->get_total_load( $student_id, $session_id );

			$get_total_load =  isset($get_total_load_details['total_amount'])?$get_total_load_details['total_amount']:0;

			$total_balance = $get_total_load - $get_total_spent;

			?>

	        <div class="row"> 

				<div class="col-lg-12 col-xs-12">

					<div class="col-lg-4 col-xs-12">

						<!-- small box -->

						<div class="small-box bg-aqua">

							<div class="inner">

								<h3><?php echo number_format($get_total_spent,'2','.',',') ?></h3> 

								<p>Total Spent</p>

							</div>

							<div class="icon">

								<i class="fa fa-rub" aria-hidden="true"></i>

							</div> 

						</div>

					</div>

					 <div class="col-lg-4 col-xs-12">

						<!-- small box -->

						<div class="small-box bg-orange">

							<div class="inner">

								<h3><?php echo number_format($get_total_load,'2','.',',') ?></h3> 

								<p>Total Load</p>

							</div>

							<div class="icon">

								<i class="fa fa-rub" aria-hidden="true"></i>

							</div> 

						</div>

					</div>

					<div class="col-lg-4 col-xs-12">

						<!-- small box -->

						<div class="small-box bg-navy">

							<div class="inner">

								<h3><?php  echo number_format($total_balance,'2','.',',' )?></h3> 

								<p>Balance</p>

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

				<div class="row">

					<div class="col-md-3">           

						<div class="box box-primary">

								<div class="box-body box-profile" style="height:650px;">

								<?php

									$image = base_url().$student['image'];

									if(!@GetImageSize($image)){

										$image = base_url().'uploads/student_images/no_image.png';

									} 

								?>				

								 <img class="profile-user-img img-responsive img-circle" style="height: 300px; width: 300px;" src="<?php echo $image;?>" alt="User profile picture">

								<h3 class="profile-username text-center"><?php echo $student['firstname'].' '.$student['middlename'].' '.$student['lastname'].' '.$student['suffix']?></h3> 

								<ul class="list-group list-group-unbordered">

									<li class="list-group-item">

										<b><?php echo $this->lang->line('admission_no'); ?></b><a class="pull-right text-aqua"><?php echo $student['admission_no'] ?></a>

									</li>

									<li class="list-group-item">

										<b>Grade</b> <a class="pull-right text-aqua"><?php echo $student['class'] ?></a>

									</li>

									<li class="list-group-item">

										<b><?php echo $this->lang->line('section'); ?></b> <a class="pull-right text-aqua"><?php echo $student['section'] ?></a>

									</li>

									<li class="list-group-item">

										<b><?php echo $this->lang->line('gender'); ?></b> <a class="pull-right text-aqua"><?php echo $student['gender'] ?></a>

									</li>     

								</ul> 

							</div>

						</div>

					</div>

					<div class="col-md-9"> 

						<div class="nav-tabs-custom" >

							<ul class="nav nav-tabs">    

								<li><a href="<?php echo base_url(); ?>cafeteria/orders/view/<?php echo $student_id ?>" >Summary of Transactions</a></li> 

								<li ><a href="<?php echo base_url(); ?>cafeteria/orders/deleted_order/<?php echo $student_id ?>" >Deleted Order</a></li>                     

								<li  class="active"> <a href="<?php echo base_url(); ?>cafeteria/orders/load_history/<?php echo $student_id ?>" >Load History</a></li>

								<li class=""><a href="<?php echo base_url(); ?>cafeteria/orders/loaddeleted/<?php echo $student_id ?>" >Deleted Load</a></li>

							</ul>						

							<div class="tab-content" style="display:relative;width:100%;float:right;height:600px;overflow:hidden;overflow-y:scroll;">

								<div class="box box-primary">					

									<div class="box-body">

											<div class="table-responsive mailbox-messages">

												<table class="table table-striped table-bordered table-hover example">

													<thead>

														<th>#</th>								

														<th>Date Load</th>								

														<th>Amount</th>		

														<th>Remarks</th>
														
														<th>Action</th>	

														<th>Loaded By</th>

													</thead>

													<tbody><?php 

													$sum = 0;

													$count = 1;

													foreach ($get_student_load as $student_load) { 

														?>

														<tr>												

															<td><?php echo $count;?></td> 															

															<td><span title="<?php echo $student_load['date_load'];?>"><?php echo date('F d, Y', strtotime($student_load['date_load'])); ?></span></td>

															<td><?php echo $student_load['amount'];?></td>

															<td><?php echo $student_load['remarks'];?></td>
															<!-- deleted load -->
															<td>
																<button type="button" class="btn btn-danger btn-xs open-delete-modal"
																	data-toggle="modal"
																	data-target="#deleteLoadModal"
																	data-load-id="<?php echo $student_load['id']; ?>">
																	<i class="fa fa-remove"></i>
																</button>
															</td>

															<td><?php echo $student_load['user_id'] . ' (' . $student_load['user_role'] . ')'; ?></td>

														</tr>

														<?php      

														$count++;

															$sum += $student_load['amount'];

														}

													?>

													<tbody>

												</table>

											</div>

									</div>

								</div>				 

							</div>	

							<br><hr>					

							<h5><strong><i>&nbsp &nbsp Total Expenditures: &nbsp </i></strong>

								<strong style="color:red;"><?php echo 'P '. number_format($sum, 2, '.', ',');?></strong>

							</h5>

							<br>							

						</div>

					</div>

			</div>

		</div>

	</section>
	<!-- deleted load -->
	<div class="modal fade" id="deleteLoadModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Load</h4>
				</div>
				<form id="deleteLoadForm" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="deleted_reason">Reason for Deletion</label>
							<textarea name="deleted_reason" id="deleted_reason" class="form-control" required></textarea>
						</div>
						<input type="hidden" name="load_id" id="modal_load_id">
						<input type="hidden" name="student_id" id="modal_student_id" value="<?php echo $student_id; ?>">
						<input type="hidden" name="session_id" id="modal_session_id" value="<?php echo $session_id; ?>">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-danger">Delete Load</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</div>

<script type="text/javascript">

    $(document).ready(function () {

        $("#btnreset").click(function () {           

            $("#form1")[0].reset();

        });

    });
	// deleted load
	$(document).ready(function () {
		$('.open-delete-modal').on('click', function () {
			var loadId = $(this).data('load-id');
			$('#modal_load_id').val(loadId);
			$('#modal_student_id').val('<?php echo $student_id; ?>');
			$('#modal_session_id').val('<?php echo $session_id; ?>');
		});

		$('#deleteLoadForm').submit(function(e) {
			e.preventDefault();
			if(confirm('Are you sure you want to delete this load? The student load balance will be updated!')) {
				this.action = '<?php echo site_url('cafeteria/orders/delete_load/'.$student_id ) ?>';
				this.submit();
			}
		});
	});

	

</script>



