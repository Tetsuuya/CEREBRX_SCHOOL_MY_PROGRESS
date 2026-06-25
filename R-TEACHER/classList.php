<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content-header">
        <h1>
            <i class="fa fa-mortar-board"></i> <?php echo $this->lang->line('academics'); ?>     </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row"> 
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('class_list'); ?></h3><br>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
					<div class="box-body">
					<br/>
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
						<div class="row">
							<form action="<?php echo site_url('registrar/teacher/advisers') ?>"  id="employeeform" name="employeeform" method="get" enctype="multipart/form-data">
								<div class="col-md-2">
									<label for="exampleInputEmail1"><?php echo "School Year"; ?></label>
								</div>
								<div class="col-md-9">
									<div class="form-group">
										<select  id="session_id" name="session_id" class="form-control" >
											<option value=""><?php echo $this->lang->line('select'); ?></option>
											<?php
											foreach ($sessionlist as $session) {
											?>
											<option value="<?php echo $session['id'] ?>"<?php if ($session['id'] == $session_id) echo "selected=selected" ?>><?php echo $session['session'] ?></option>
											<?php
											$count++;
											}
											?>
										</select>					
										<span class="text-danger"><?php echo form_error('session_id'); ?></span>
									</div>
								</div>
								<div class="col-md-1">
									<button type="submit" class="btn btn-info pull-right btn-sm"><?php echo "Change"; ?></button>
								</div>
							</form>
						</div>
					</div>	
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
										<th>#
                                        </th>
                                        <th><?php echo $this->lang->line('class'); ?>
                                        </th>
                                        <th><?php echo $this->lang->line('sections'); ?>
                                        </th> 
                                        <th>Advisers
                                        </th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
									$row = 1;
                                    foreach ($vehroutelist as $vehroute) {
                                        ?>
                                        <tr>
										  <td class="mailbox-name">
                                                <?php echo $row; ?>

                                            </td>
                                            <td class="mailbox-name">
                                                <?php echo $vehroute->class; ?>

                                            </td>


                                            <td>
                                                <?php
                                                $vehicles = $vehroute->vehicles;
                                                  
                                                if (!empty($vehicles)) {


                                                    foreach ($vehicles as $key => $value) {
                                                        
                                                        $get_teacher_advisory = $this->classsection_model->get_teacher_advisory($value->class_id, $value->section_id, $session_id );
														if( !empty($get_teacher_advisory)  ){
															$get_teacher_advisory_id[$vehroute->class][] = array(
																'teacher_id' => $get_teacher_advisory->teacher_id,
																'class_id' => $value->class_id,
																'section_id' => $value->section_id,
																'class_section_id' => $value->class_section_id,
															);
														} else {
															$get_teacher_advisory_id[$vehroute->class][] = array(
																'teacher_id' => '',
																'class_id' => $value->class_id,
																'section_id' => $value->section_id,
																'class_section_id' => $value->class_section_id,
															);
														}

                                                        echo "<div>" . $value->section . "</div>";
                                                    }
                                                }
                                                ?>

                                            </td>
                                             <td class="mailbox-name">
                                                <?php 
												//$get_teacher_advisory_list = $get_teacher_advisory_id[];
												if( !empty($get_teacher_advisory_id[$vehroute->class] ) ){
													 foreach ($get_teacher_advisory_id[$vehroute->class] as $key => $teacher_advisory_id  ) {
														$get_class_id =  $teacher_advisory_id['class_id'];
														 $get_section_id =  $teacher_advisory_id['section_id'];
														 $class_section_id =  $teacher_advisory_id['class_section_id'];
														if( $teacher_advisory_id['teacher_id'] ){
															$teacher_id = $teacher_advisory_id['teacher_id'];
															$teacher_details = $this->teacher_model->get( $teacher_id );
															?>
															<strong><?php echo $teacher_details['lastname'].', '.$teacher_details['name'];  ?></strong>
															<?php 
															if( $current_session == $session_id ){
																?>
															<a class="btn btn-xs btn-success schedule_modal" style="float:right;" sc-id="<?php echo $class_section_id;?>" sc-class="<?php echo $get_class_id;?>" sc-section="<?php echo $get_section_id;?>" sc-teacher_id="<?php echo $teacher_id;?>">Edit Adviser</a>
															<?php
															}
															echo '<br/>';
														} else {
															echo "<em>No Teacher Assign Yet!</em>";
															?>
															<?php 
															if( $current_session == $session_id ){
																?>
																<a class="btn btn-xs btn-default schedule_modal" style="float:right;" sc-id="" sc-class="<?php echo $get_class_id;?>" sc-section="<?php echo $get_section_id;?>" sc-teacher_id="">Assign Adviser</a>
																<?php 
															}
															?>
															<?php
															echo '<br/>';
														}
													 }
												} 
                                                 ?>

                                            </td>
                                            
                                        </tr>
                                        <?php
										$row++;
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

<style>
/* Fix for native macOS/Chrome select scrolling bug inside Bootstrap Modals */
.modal-open .modal {
    -webkit-overflow-scrolling: auto !important;
}
</style>

<div id="scheduleModal" class="modal fade bs-example-modal-lg" role="dialog">
	<form action="<?php echo site_url('registrar/teacher/assign_advisers') ?>"  id="employeeform" name="employeeform" method="post" enctype="multipart/form-data">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title">Assign Adviser</h4>
				</div>
				<div class="modal-body">
					<select  id="teacher_id" name="teacher_id" class="form-control select2" style="width: 100%;" >
						<option value=""><?php echo $this->lang->line('select'); ?></option>
						<?php
						foreach ($teacherlist as $teacher ) {
						?>
						<option value="<?php echo $teacher['id'] ?>" ><?php echo $teacher['lastname'] ?>, <?php echo $teacher['name'] ?> <?php echo $teacher['middlename'] ?></option>
						<?php
						$count++;
						}
						?>
					</select>		
				</div>
				<div class="modal-footer">
					<input type="hidden" name="section_id" id="section_id" />
					<input type="hidden" name="class_id"  id="class_id"/>
					<input type="hidden" name="get_session_id" id="get_session_id" value="<?php echo $session_id;?>" />
					<button type="submit" class="btn btn-success">Submit</button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
    // Aggressively neutralize any Bootstrap modal focus traps once the page is fully loaded
    $(document).ready(function() {
        setTimeout(function() {
            if (typeof $.fn.modal !== 'undefined' && $.fn.modal.Constructor) {
                $.fn.modal.Constructor.prototype.enforceFocus = function() {};
            }
            $(document).off('focusin.bs.modal');
            $(document).off('focusin.modal');
        }, 500);
        
        $('.select2').select2({
            dropdownParent: $('#scheduleModal')
        });
    });

    $(document).on('click', '.schedule_modal', function () {
		 var section_id = $(this).attr('sc-section');
		 var class_id = $(this).attr('sc-class');
		 var id = $(this).attr('sc-id');
		 var teacher_id = $(this).attr('sc-teacher_id');
		$('#section_id').val(section_id);
		$('#class_id').val(class_id);
		$('#class_section_id').val(id);
		$('#teacher_id').val(teacher_id).trigger('change');
		 $("#scheduleModal").modal('show');
	});

    // Ensure it's unbound every time the modal is shown as well
    $('#scheduleModal').on('shown.bs.modal', function() {
        $(document).off('focusin.bs.modal');
        $(document).off('focusin.modal');
    });
</script>
