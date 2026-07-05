<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style type="text/css">
    /*REQUIRED*/
    .carousel-row {
        margin-bottom: 10px;
    }

    .slide-row {
        padding: 0;
        background-color: #ffffff;
        min-height: 150px;
        border: 1px solid #e7e7e7;
        overflow: hidden;
        height: auto;
        position: relative;
    }

    .slide-carousel {
        width: 20%;
        float: left;
        display: inline-block;
    }

    .slide-carousel .carousel-indicators {
        margin-bottom: 0;
        bottom: 0;
        background: rgba(0, 0, 0, .5);
    }

    .slide-carousel .carousel-indicators li {
        border-radius: 0;
        width: 20px;
        height: 6px;
    }

    .slide-carousel .carousel-indicators .active {
        margin: 1px;
    }

    .slide-content {
        position: absolute;
        top: 0;
        left: 20%;
        display: block;
        float: left;
        width: 80%;
        max-height: 76%;
        padding: 1.5% 2% 2% 2%;
        overflow-y: auto;
    }

    .slide-content h4 {
        margin-bottom: 3px;
        margin-top: 0;
    }

    .slide-footer {
        position: absolute;
        bottom: 0;
        left: 20%;
        width: 78%;
        height: 20%;
        margin: 1%;
    }

    /* Scrollbars */
    .slide-content::-webkit-scrollbar {
        width: 5px;
    }

    .slide-content::-webkit-scrollbar-thumb:vertical {
        margin: 5px;
        background-color: #999;
        -webkit-border-radius: 5px;
    }

    .slide-content::-webkit-scrollbar-button:start:decrement,
    .slide-content::-webkit-scrollbar-button:end:increment {
        height: 5px;
        display: block;
    }
</style>

<div class="content-wrapper" style="min-height: 946px;">    
    <section class="content-header">
        <h1>
            <i class="fa fa-user-plus"></i> <?php echo $this->lang->line('student_information'); ?> <small><?php echo $this->lang->line('student1'); ?></small></h1>
    </section>  
    <section class="content">
        <div class="row">  
            <div class="col-md-12"> 
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <div class="box-body">
                        <?php if ($this->session->flashdata('msg')) { ?> <div class="alert alert-success">  <?php echo $this->session->flashdata('msg') ?> </div> <?php } ?>
                        <div class="">
                	       <div class="form-group">
                             	<div class="col-sm-12">
                            		<label><?php echo $this->lang->line('session'); ?></label>
                            		<select  id="session_id" name="session_id" class="form-control" require>
										<option value=""><?php echo $this->lang->line('select'); ?></option>
										<?php
										foreach ($sessionlist as $session) {
											?>
											<option value="<?php echo $session['id'] ?>"<?php echo $session['id'] == $session_id?"selected=selected":"" ?>><?php echo $session['session'] ?></option>

											<?php
										}
										?>
									</select>					
									<span class="text-danger"><?php echo form_error('session_id'); ?></span>
                            	</div>
                            </div>
                            <div class="col-md-6">
                                <form role="form" action="<?php echo site_url('teacher/student/search') ?>" method="post" class="form-horizontal"> 
                                     <?php echo $this->customlib->getCSRF(); ?>
                              
                                    <div class="form-group">

                                        <div class="col-sm-6">
                                            <label><?php echo $this->lang->line('class'); ?></label>
                                            <select  id="class_id" name="class_id" class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label><?php echo $this->lang->line('section'); ?></label>
                                            <select  id="section_id" name="section_id" class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="hide_session_id" class="session_id" value="<?php echo $session_id;?>" />
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form role="form" action="<?php echo site_url('teacher/student/search') ?>" method="post" class="form-horizontal">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label><?php echo $this->lang->line('search_by_keyword'); ?></label>
                                            <input type="text" name="search_text" class="form-control"   placeholder="<?php echo $this->lang->line('search_by_name,_roll_no,_enroll_no,_national_identification_no,_local_identification_no_etc..'); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button type="submit" name="search" value="search_full" class="btn btn-primary pull-right btn-sm checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                        </div>
                                    </div>
                                      <input type="hidden" name="hide_session_id" class="session_id" value="<?php echo $session_id;?>"  />
                                </form>
                            </div>
                        </div>
                    </div>
                </div> 
                <?php
				if( $searchby == 'filter'){
					if (isset($resultlist_male) || isset($resultlist_female)) {                   
						?>
						<div class="nav-tabs-custom">
							<ul class="nav nav-tabs">
								<li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"><i class="fa fa-list"></i> <?php echo $this->lang->line('list'); ?>  <?php echo $this->lang->line('view'); ?></a></li>
								<li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false"><i class="fa fa-newspaper-o"></i> <?php echo $this->lang->line('details'); ?> <?php echo $this->lang->line('view'); ?></a></li>
							</ul>
							<div class="tab-content">
								<div class="tab-pane active table-responsive no-padding" id="tab_1">
									<table class="table table-striped table-bordered table-hover example">
										<thead>
											<tr>
												<th class="sorting_disabled">#</th>
												<th><?php echo $this->lang->line('admission_no'); ?></th>
												<th><?php echo $this->lang->line('student_name'); ?></th>
												<th><?php echo 'Grade';//$this->lang->line('class'); ?></th>
												<th><?php echo $this->lang->line('date_of_birth'); ?></th>
												<th><?php echo 'Guardian';?></th>
												<th><?php echo 'Guardian #';?></th>
												<?php
													$col = 8;
													if( $enableStrand){
													$col = 9;	
												?>
													<th>Strand</th>
													<?php 
												}
												?>
												<th class="text-right"><?php echo $this->lang->line('action'); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
											if (empty($resultlist_female) && empty($resultlist_male)) {
												?>
												<?php
											} else {
												$count = 1;
												if( isset( $resultlist_male ) ){
														?>
														<tr>
															<td><strong>Male</strong></td>
															<?php
															for($xyz=1;$xyz<$col;$xyz++){
																?>
																<td></td>
																<?php
															}
															?>
														</tr>
														<?php
														foreach ($resultlist_male as $student) {
														?>
													
														<tr> 
															<td><?php echo $count; ?></td>
															<td><?php echo $student['admission_no']; ?></td>
															<td>
																<a href="<?php echo base_url(); ?>teacher/student/view/<?php echo $student['id']; ?>"><?php echo $student['lastname'] . ", " . $student['firstname'].' '. $student['suffix'].' '. $student['middlename']; ?>
																</a>
															</td>
															<td><?php echo $student['class'] . "(" . $student['section'] . ")" ?></td>
															<td><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student['dob'])); ?></td>
															<td><?php echo $student['guardian_lastname'].', '.$student['guardian_name']; ?></td>
															<td><?php echo $student['guardian_phone']; ?></td>
														   <?php 
															if( $enableStrand){
																$strand_id = $student['strand_id'];
																$firstsem = $this->studentstrand_model->get_latest_strand( $student['id'], 1, $session_id );
																$get_firstsem = $firstsem['strand_id'];
																$secondsem = $this->studentstrand_model->get_latest_strand( $student['id'], 2, $session_id );
																$get_secondsem = $secondsem['strand_id'];
																if( empty($firstsem) && empty($secondsem )){
																	$strand_details = $this->strand_model->get( $strand_id );
																	?>
																	 <td><?php echo $strand_details['code']; ?></td>
																	 <?php
																} else {
																	if(empty( $get_firstsem )){
																		$get_firstsem = $strand_id;
																		
																	}
																	$get_firstsem_details = $this->strand_model->get( $get_firstsem );
																	if(empty( $get_secondsem )){
																		$get_secondsem = $strand_id;
																	}
																	$get_secondsem_details = $this->strand_model->get( $get_secondsem );
																	?>
																	 <td><?php echo $get_firstsem_details['code'].'/'.$get_secondsem_details['code']; ?></td>
																	<?php
																}
															
																?>
																
																<?php 
															}
															?>
															<td class="pull-right">
																<a href="<?php echo base_url(); ?>teacher/student/view/<?php echo $student['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>" >
																	<i class="fa fa-reorder"></i>
																</a>
																<!-- <a href="<?php echo base_url(); ?>teacher/student/edit/<?php echo $student['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
																	<i class="fa fa-pencil"></i>
																</a> -->
															</td>
														</tr>
														<?php
														$count++;
													}
												}
												if( isset( $resultlist_female ) ){
														?>
														<tr>
															<td><strong>Female</strong></td>
															<?php
															for($xyz=1;$xyz<$col;$xyz++){
																?>
																<td></td>
																<?php
															}
															?>
														</tr>
														<?php
														foreach ($resultlist_female as $student) {
														?>
													
														<tr> 
															<td><?php echo $count; ?></td>
															<td><?php echo $student['admission_no']; ?></td>
															<td>
																<a href="<?php echo base_url(); ?>teacher/student/view/<?php echo $student['id']; ?>"><?php echo $student['lastname'] . ", " . $student['firstname'].' '. $student['suffix'].' '. $student['middlename']; ?>
																</a>
															</td>
															<td><?php echo $student['class'] . "(" . $student['section'] . ")" ?></td>
															<td><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student['dob'])); ?></td>
															<td><?php echo $student['guardian_lastname'].', '.$student['guardian_name']; ?></td>
															<td><?php echo $student['guardian_phone']; ?></td>
														   <?php 
															if( $enableStrand){
																$strand_id = $student['strand_id'];
																$firstsem = $this->studentstrand_model->get_latest_strand( $student['id'], 1, $session_id );
																$get_firstsem = $firstsem['strand_id'];
																$secondsem = $this->studentstrand_model->get_latest_strand( $student['id'], 2, $session_id );
																$get_secondsem = $secondsem['strand_id'];
																if( empty($firstsem) && empty($secondsem )){
																	$strand_details = $this->strand_model->get( $strand_id );
																	?>
																	 <td><?php echo $strand_details['code']; ?></td>
																	 <?php
																} else {
																	if(empty( $get_firstsem )){
																		$get_firstsem = $strand_id;
																		
																	}
																	$get_firstsem_details = $this->strand_model->get( $get_firstsem );
																	if(empty( $get_secondsem )){
																		$get_secondsem = $strand_id;
																	}
																	$get_secondsem_details = $this->strand_model->get( $get_secondsem );
																	?>
																	 <td><?php echo $get_firstsem_details['code'].'/'.$get_secondsem_details['code']; ?></td>
																	<?php
																}
															
																?>
																
																<?php 
															}
															?>
															<td class="pull-right">
																<a href="<?php echo base_url(); ?>teacher/student/view/<?php echo $student['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>" >
																	<i class="fa fa-reorder"></i>
																</a>
															</td>
														</tr>
														<?php
														$count++;
													}
												}
											}
											?>
										</tbody>
									</table>
								</div>                         
								<div class="tab-pane" id="tab_2">
									<?php 
									if (empty($resultlist_female) && empty($resultlist_male)) {
										?>
										<div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
										<?php
									} else {
										if(isset( $resultlist_male )){
											foreach ($resultlist_male as $student) {
												$image = base_url().$student['image'];
												if(!@GetImageSize($image)){
													$image = base_url().'uploads/student_images/no_image.png';
												} 
												?>
												<div class="carousel-row">
													<div class="slide-row">
														<div id="carousel-2" class="carousel slide slide-carousel" data-ride="carousel">
															<div class="carousel-inner">
																<div class="item active"> 
																	<a href="<?php echo base_url(); ?>teacher/student/view/<?php echo $student['id'] ?>"> <img class="img-responsive img-thumbnail" alt="Cinque Terre" style="width:150px;height:150px;" src="<?php echo $image; ?>" alt="Image"></a>
																</div>
															</div>
														</div>
														<div class="slide-content"> 
															<h4><a href="<?php echo base_url(); ?>teacher/student/view/<?php echo $student['id'] ?>"> <?php echo $student['lastname'] . ", " . $student['firstname']. " " . $student['suffix']." ". $student['middlename'] ?></a></h4>
															<div class="row">
																<div class="col-xs-6 col-md-6">
																	<address>
																	<strong><b><?php echo $this->lang->line('class'); ?>: </b><?php echo $student['class'] . "(" . $student['section'] . ")" ?></strong><br>
																	<b><?php echo $this->lang->line('admission_no'); ?>: <?php echo $student['admission_no'] ?></b><br/>
																	<b><?php echo $this->lang->line('date_of_birth'); ?>:
																		<?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student['dob'])); ?><br>
																		<b><?php echo $this->lang->line('gender'); ?>:&nbsp;</b><?php echo $student['gender'] ?><br>
																		</address>
																</div>
																<div class="col-xs-6 col-md-6">
																	<b>LRN: </b><?php echo $student['lrn'] ?><br>
																	<b><?php echo $this->lang->line('guardian_name'); ?>:&nbsp;</b><?php echo $student['guardian_lastname'].', '.$student['guardian_name'] ?><br>
																	<b><?php echo $this->lang->line('guardian_phone'); ?>: </b> <abbr title="Phone"><i class="fa fa-phone-square"></i>&nbsp;</abbr> <?php echo $student['guardian_phone'] ?><br>
																	<b><?php echo $this->lang->line('current_address'); ?>:&nbsp;</b><?php echo $student['current_address'] ?> <?php echo $student['city'] ?><br>
																</div>
															</div>
														</div>
														<div class="slide-footer">
															<span class="pull-right buttons">
																<a href="<?php echo base_url(); ?>teacher/student/view/<?php echo $student['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>" >
																	<i class="fa fa-reorder"></i>
																</a>
															</span>
														</div>
													</div>
												</div>
												<?php
											}
										}
										if(isset( $resultlist_female )){
											foreach ($resultlist_female as $student) {
												$image = base_url().$student['image'];
												if(!@GetImageSize($image)){
													$image = base_url().'uploads/student_images/no_image.png';
												} 
												?>
												<div class="carousel-row">
													<div class="slide-row">
														<div id="carousel-2" class="carousel slide slide-carousel" data-ride="carousel">
															<div class="carousel-inner">
																<div class="item active"> 
																	<a href="<?php echo base_url(); ?>teacher/student/view/<?php echo $student['id'] ?>"> <img class="img-responsive img-thumbnail" alt="Cinque Terre" style="width:150px;height:150px;" src="<?php echo $image; ?>" alt="Image"></a>
																</div>
															</div>
														</div>
														<div class="slide-content"> 
															<h4><a href="<?php echo base_url(); ?>teacher/student/view/<?php echo $student['id'] ?>"> <?php echo $student['lastname'] . ", " . $student['firstname']. " " . $student['suffix']." ". $student['middlename'] ?></a></h4>
															<div class="row">
																<div class="col-xs-6 col-md-6">
																	<address>
																		<strong><b><?php echo $this->lang->line('class'); ?>: </b><?php echo $student['class'] . "(" . $student['section'] . ")" ?></strong><br>
																		<b><?php echo $this->lang->line('admission_no'); ?>: <?php echo $student['admission_no'] ?></b><br/>
																		<b><?php echo $this->lang->line('date_of_birth'); ?>:
																		<?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student['dob'])); ?><br>
																		<b><?php echo $this->lang->line('gender'); ?>:&nbsp;</b><?php echo $student['gender'] ?><br>
																	</address>
																</div>
																<div class="col-xs-6 col-md-6">
																	<b>LRN: </b><?php echo $student['lrn'] ?><br>
																	<b><?php echo $this->lang->line('guardian_name'); ?>:&nbsp;</b><?php echo $student['guardian_lastname'].', '.$student['guardian_name'] ?><br>
																	<b><?php echo $this->lang->line('guardian_phone'); ?>: </b> <abbr title="Phone"><i class="fa fa-phone-square"></i>&nbsp;</abbr> <?php echo $student['guardian_phone'] ?><br>
																	<b><?php echo $this->lang->line('current_address'); ?>:&nbsp;</b><?php echo $student['current_address'] ?> <?php echo $student['city'] ?><br>
																</div>
															</div>
														</div>
														<div class="slide-footer">
															<span class="pull-right buttons">
																<a href="<?php echo base_url(); ?>teacher/student/view/<?php echo $student['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>" >
																	<i class="fa fa-reorder"></i>
																</a>
															</span>
														</div>
													</div>
												</div>
												<?php
											}
										}
									}
									?>
									</div>                                                          
									</div>                                                       
						</div>
						<?php
						}
				} elseif( $searchby == 'text'){
					?>
					<div class="nav-tabs-custom">
							<ul class="nav nav-tabs">
								<li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"><i class="fa fa-list"></i> <?php echo $this->lang->line('list'); ?>  <?php echo $this->lang->line('view'); ?></a></li>
								<li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false"><i class="fa fa-newspaper-o"></i> <?php echo $this->lang->line('details'); ?> <?php echo $this->lang->line('view'); ?></a></li>
							</ul>
							<div class="tab-content">
								<div class="tab-pane active table-responsive no-padding" id="tab_1">
									<table class="table table-striped table-bordered table-hover example">
										<thead>
											<tr>
												<th class="sorting_disabled">#</th>
												<th><?php echo $this->lang->line('admission_no'); ?></th>
												<th><?php echo $this->lang->line('student_name'); ?></th>
												<th><?php echo 'Grade';//$this->lang->line('class'); ?></th>
												<th><?php echo $this->lang->line('date_of_birth'); ?></th>
												<th><?php echo 'Guardian';?></th>
												<th><?php echo 'Guardian #';?></th>
												<?php
													$col = 8;
													if( $enableStrand){
													$col = 9;	
												?>
													<th>Strand</th>
													<?php 
												}
												?>
												<th class="text-right"><?php echo $this->lang->line('action'); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
											if (empty($resultlist)) {
												?>
												<?php
											} else {
												$count = 1;
												foreach ($resultlist as $student) {
												?>
											
												<tr> 
													<td><?php echo $count; ?></td>
													<td><?php echo $student['admission_no']; ?></td>
													<td>
														<a href="<?php echo base_url(); ?>teacher/student/view/<?php echo $student['id']; ?>"><?php echo $student['lastname'] . ", " . $student['firstname'].' '. $student['suffix'].' '. $student['middlename']; ?>
														</a>
													</td>
													<td><?php echo $student['class'] . "(" . $student['section'] . ")" ?></td>
													<td><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student['dob'])); ?></td>
													<td><?php echo $student['guardian_lastname'].', '.$student['guardian_name']; ?></td>
													<td><?php echo $student['guardian_phone']; ?></td>
												   <?php 
													if( $enableStrand){
														$strand_id = $student['strand_id'];
														$firstsem = $this->studentstrand_model->get_latest_strand( $student['id'], 1, $session_id );
														$get_firstsem = $firstsem['strand_id'];
														$secondsem = $this->studentstrand_model->get_latest_strand( $student['id'], 2, $session_id );
														$get_secondsem = $secondsem['strand_id'];
														if( empty($firstsem) && empty($secondsem )){
															$strand_details = $this->strand_model->get( $strand_id );
															?>
															 <td><?php echo $strand_details['code']; ?></td>
															 <?php
														} else {
															if(empty( $get_firstsem )){
																$get_firstsem = $strand_id;
																
															}
															$get_firstsem_details = $this->strand_model->get( $get_firstsem );
															if(empty( $get_secondsem )){
																$get_secondsem = $strand_id;
															}
															$get_secondsem_details = $this->strand_model->get( $get_secondsem );
															?>
															 <td><?php echo $get_firstsem_details['code'].'/'.$get_secondsem_details['code']; ?></td>
															<?php
														}
													
														?>
														
														<?php 
													}
													?>
													<td class="pull-right">
														<a href="<?php echo base_url(); ?>teacher/student/view/<?php echo $student['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>" >
															<i class="fa fa-reorder"></i>
														</a>
													</td>
												</tr>
												<?php
												$count++;
												}
											}
											?>
										</tbody>
									</table>
								</div>                         
								<div class="tab-pane" id="tab_2">
									<?php 
									if (empty($resultlist)) {
										?>
										<div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
										<?php
									} else {
										foreach ($resultlist as $student) {
											$image = base_url().$student['image'];
											if(!@GetImageSize($image)){
												$image = base_url().'uploads/student_images/no_image.png';
											} 
											?>
											<div class="carousel-row">
												<div class="slide-row">
													<div id="carousel-2" class="carousel slide slide-carousel" data-ride="carousel">
														<div class="carousel-inner">
															<div class="item active"> 
																<a href="<?php echo base_url(); ?>teacher/student/view/<?php echo $student['id'] ?>"> <img class="img-responsive img-thumbnail" alt="Cinque Terre" style="width:150px;height:150px;" src="<?php echo $image; ?>" alt="Image"></a>
															</div>
														</div>
													</div>
													<div class="slide-content"> 
														<h4><a href="<?php echo base_url(); ?>teacher/student/view/<?php echo $student['id'] ?>"> <?php echo $student['lastname'] . ", " . $student['firstname']. " " . $student['suffix']." ". $student['middlename'] ?></a></h4>
														<div class="row">
															<div class="col-xs-6 col-md-6">
																<address>
																<strong><b><?php echo $this->lang->line('class'); ?>: </b><?php echo $student['class'] . "(" . $student['section'] . ")" ?></strong><br>
																<b><?php echo $this->lang->line('admission_no'); ?>: <?php echo $student['admission_no'] ?></b><br/>
																<b><?php echo $this->lang->line('date_of_birth'); ?>:
																	<?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student['dob'])); ?><br>
																	<b><?php echo $this->lang->line('gender'); ?>:&nbsp;</b><?php echo $student['gender'] ?><br>
																	</address>
															</div>
															<div class="col-xs-6 col-md-6">
																<b>LRN: </b><?php echo $student['lrn'] ?><br>
																<b><?php echo $this->lang->line('guardian_name'); ?>:&nbsp;</b><?php echo $student['guardian_lastname'].', '.$student['guardian_name'] ?><br>
																<b><?php echo $this->lang->line('guardian_phone'); ?>: </b> <abbr title="Phone"><i class="fa fa-phone-square"></i>&nbsp;</abbr> <?php echo $student['guardian_phone'] ?><br>
																<b><?php echo $this->lang->line('current_address'); ?>:&nbsp;</b><?php echo $student['current_address'] ?> <?php echo $student['city'] ?><br>
															</div>
														</div>
													</div>
													<div class="slide-footer">
														<span class="pull-right buttons">
															<a href="<?php echo base_url(); ?>teacher/student/view/<?php echo $student['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>" >
																<i class="fa fa-reorder"></i>
															</a>
														</span>
													</div>
												</div>
											</div>
											<?php
										}
									}
									?>
								</div>                                                          
							</div>                                                       
						</div>
					<?php
				}
				?>
			</div>
		</div>
		</section>	
		</div>
		<script type="text/javascript">
			function getSectionByClass(class_id, section_id, session_id) {
				if (session_id != "" ) {
					$('#section_id').html("");
					var base_url = '<?php echo base_url() ?>';
					var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
					$.ajax({
						type: "GET",
						url: base_url + "teacher/sections/getByClassbySession",
						data: {'class_id': class_id,'session_id':session_id},
						dataType: "json",
						success: function (data) {
							$.each(data, function (i, obj)
							{
								var sel = "";
								if (section_id == obj.section_id) {
									sel = "selected";
								}
								div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
							});
							$('#section_id').append(div_data);
						}
					});

					$('#class_id').html("");
					var base_url = '<?php echo base_url() ?>';
					var div_data1 = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
					$.ajax({
						type: "GET",
						url: base_url + "teacher/student/getByClassSession",
						data: {'session_id':session_id},
						dataType: "json",
						success: function (data) {
							$.each(data, function (i, obj)
							{

								var sel = "";
								if (class_id == obj.class_id) {
									sel = "selected";
								}
								div_data1 += "<option value=" + obj.class_id + " " + sel + ">"  + obj.class_name + "</option>";
							});
							$('#class_id').append(div_data1);
						}
					});
				}
			}
			$(document).ready(function () {
				<?php 
				if( $class_id ){
					?>
					var class_id = '<?php echo set_value('class_id') ?>';
					<?php
				} else {
					?>
					var class_id = $('#class_id').val();
					<?php
				}
				?>
				<?php 
				if( $section_id ){
					?>
					var section_id = '<?php echo set_value('section_id') ?>';
					<?php
				} else {
					?>
					var section_id = $('#section_id').val();
					<?php
				}
				?>
			
				var session_id = $('#session_id').find(":selected").val();
				$('.session_id').val(session_id);

				getSectionByClass(class_id, section_id,session_id);
				$(document).on('change', '#class_id', function (e) {
					$('#section_id').html("");
					var class_id = $(this).val();
					var session_id = $('#session_id').find(":selected").val();
					$('.session_id').val(session_id);
					var base_url = '<?php echo base_url() ?>';
					var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
					$.ajax({
						type: "GET",
						url: base_url + "teacher/sections/getByClassbySession",
						data: {'class_id': class_id,'session_id':session_id},
						dataType: "json",
						success: function (data) {
							$.each(data, function (i, obj)
							{
								div_data += "<option value=" + obj.section_id + ">" + obj.section + "</option>";
							});
							$('#section_id').append(div_data);
						}
					});
				});
			});
		     $(document).on('change', '#session_id', function (e) {
				$('#section_id').html("");
				$('#class_id').html("");
				 var section_id_post = $('#section_id').val();
				 var class_id_post = $('#class_id').val();
				var session_id_post = $('#session_id').val();
 				$('.session_id').val(session_id_post);
				/*var base_url = '<?php echo base_url() ?>';
				var div_data1 = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
				$.ajax({
					type: "GET",
					url: base_url + "teacher/student/getByClassSession",
					data: {'session_id':session_id_post},
					dataType: "json",
					success: function (data) {
						$.each(data, function (i, obj)
						{

							var sel = "";
							if (class_id_post == obj.class_id) {
								sel = "selected";
							}
							div_data1 += "<option value=" + obj.class_id + " " + sel + ">"  + obj.class_name + "</option>";
						});
						$('#class_id').append(div_data1);
					}
				});*/
		     
				getSectionByClass(class_id_post, section_id_post, session_id_post);
			});
			$(document).ready(function () {
				$('.example').dataTable( {
					"order": [],
					"columnDefs": [ {
					  "targets"  : 'sorting_disabled',
					  "orderable": false,
					}]
				});
			});
		</script>
