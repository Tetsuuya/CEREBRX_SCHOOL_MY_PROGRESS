<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-map-o"></i> <?php echo $this->lang->line('examinations'); ?> <small><?php echo $this->lang->line('student_fee1'); ?></small>  </h1>
    </section>
    <!-- Main content -->
    <section class="content">
                <div class="box box-primary">
						<!-- general form elements -->
						<div class="box box-primary">
							<div class="box-header with-border">
								<h3 class="box-title">Imported Grades</h3>
								<div class="alert alert-success" style="font-size:16px;"><strong>Important Reminders  before CLICKING the SUBMIT GRADES button:</strong><br/><br/>
									<p>- Please <strong>REVIEW</strong> the imported data by clicking the <strong>VIEW</strong> button .</p>
									<p>- Incase there are <strong>MISSING DATA</strong> in importing grades. Please <strong>reupload</strong> and <strong>review</strong> again.</p>
								</div>
							</div>
							<div class="box-body">
								<div class="mailbox-controls" style="font-size:18px;">
								<?php if ($this->session->flashdata('msg')) { 
										if(is_array($this->session->flashdata('msg'))){
											$ab =  $this->session->flashdata('msg');
											for($x=0;$x<count($ab);$x++ ){
												echo $ab[$x];
											}
										} else {
											echo $this->session->flashdata('msg');
										}
									} ?>
								</div>
								<div class="table-responsive mailbox-messages">
									 <table class="table table-striped table-bordered table-hover example">
										<thead>
											<tr>
												<th>#</th>
												<th><?php echo $this->lang->line('created_at'); ?></th>
												<th><?php echo $this->lang->line('class'); ?>   </th>
												<th><?php echo $this->lang->line('section'); ?>   </th>
												<th><?php echo $this->lang->line('subject'); ?>   </th>
												<th>Term</th>
												<th>Published</th>
												<th>Status</th>
												<th class="text-right"><?php echo $this->lang->line('action'); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
											$n=1;
												foreach ($importgradesbatch as $gradesbatch) {
														$published = $gradesbatch['published'];
														
														$display_published = $published == 1?'Yes':'No';
														$status = $this->importgradesdetails_model->get_pending_status($gradesbatch['id'] );
												?>
												<tr>
													<td class="mailbox-name"> <?php echo $n ?></td>
													<td class="mailbox-name"> <?php echo $gradesbatch['created_at'] ?></td>
													<td class="mailbox-name"> <?php echo $gradesbatch['class'] ?></td>
													<td class="mailbox-name"> <?php echo $gradesbatch['section'] ?></td>
													<td class="mailbox-name"> <?php echo $gradesbatch['name'] ?></td>
													<td class="mailbox-name"> 
														<?php 
														$term_display = '';
														if ($gradesbatch['quarter'] == 1) {
															$term_display = 'Term 1';
														} elseif ($gradesbatch['quarter'] == 2) {
															$term_display = 'Term 2';
														} elseif ($gradesbatch['quarter'] == 3) {
															$term_display = 'Term 3';
														} else {
															$term_display = $gradesbatch['quarter'];
														}
														echo $term_display; 
														?>
													</td>
													
													<td class="mailbox-name"> <?php echo $display_published ?></td>
													<td class="mailbox-name"> <?php echo $status ?></td>
													<td class="mailbox-date pull-right">
														<?php 
														/*if( $published != 1 ){
															?>
															<a href="<?php echo base_url(); ?>teacher/grade/submit/<?php echo $gradesbatch['id'] ?>" class="btn btn-success btn-xs"  data-toggle="tooltip" title="Submit Grades">
																Submit Grades
															</a>
															<?php
														}*/
														?>
														<a href="<?php echo base_url(); ?>teacher/grade/view_batch/<?php echo $gradesbatch['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>"> 
															<i class="fa fa-reorder"></i>Review Grades
														</a>
														<?php 
														if( $published != 1 ){
															?>
															<a href="<?php echo base_url(); ?>teacher/grade/delete_batch/<?php echo $gradesbatch['id'] ?>"class="btn btn-danger btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('Are you sure you want to delete this item?');">
																<i class="fa fa-remove"></i>
															</a>
														<?php
														}
														?>
													</td>
												</tr>
												<?php
												$n++;
												}
												
											?>
											
										</tbody>
									</table><!-- /.table -->
								</div><!-- /.mail-box-messages -->
							</div><!-- /.box-body -->
						</div>
					</div>
                </section>
			</div>
