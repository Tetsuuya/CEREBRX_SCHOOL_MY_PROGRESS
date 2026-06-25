<style type="text/css">
    @media print
    {
        .no-print, .no-print *
        {
            display: none !important;
        }
    }
</style>

<div class="content-wrapper" style="min-height: 946px;">  
    <section class="content-header">
        <h1>
               <i class="fa fa-users"></i> Faculty and Staff</h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">       
            <div class="col-md-12">           
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('add'); ?> Employee</h3>
                    </div> 
                    <form id="form1" action="<?php echo site_url('idproduction/faculties/add/' ) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8"  enctype="multipart/form-data">
                        <div class="box-body">
                            <?php if ($this->session->flashdata('msg')) { ?>
                                <?php echo $this->session->flashdata('msg') ?>
                            <?php } ?>         

                             <?php echo $this->customlib->getCSRF(); ?>
                          
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Firstname *</label>
                                    <input id="category" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name' ); ?>" required />
                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Middlename</label>
                                    <input id="category" name="middlename" placeholder="" type="text" class="form-control"  value="<?php echo set_value('middlename' ); ?>" />
                                    <span class="text-danger"><?php echo form_error('middlename'); ?></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Lastname *</label>
                                    <input id="category" name="lastname" placeholder="" type="text" class="form-control"  value="<?php echo set_value('lastname' ); ?>" required/>
                                    <span class="text-danger"><?php echo form_error('lastname'); ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Email</label>
                                    <input id="category" name="email" placeholder="" type="text" class="form-control"  value="<?php echo set_value('email' ); ?>" />
                                    <span class="text-danger"><?php echo form_error('email'); ?></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Sex </label>
                                    <input id="category" name="sex" placeholder="" type="text" class="form-control"  value="<?php echo set_value('sex'); ?>"  />
                                    <span class="text-danger"><?php echo form_error('sex'); ?></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Phone </label>
                                    <input id="category" name="phone" placeholder="" type="text" class="form-control"  value="<?php echo set_value('phone' ); ?>"  />
                                    <span class="text-danger"><?php echo form_error('phone'); ?></span>
                                </div>
                            </div> 
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('date_of_birth'); ?> </label>
                                    <input id="dob" name="dob" placeholder="" type="text" class="form-control"  value="<?php echo set_value('dob'); ?>" readonly="readonly"  />
                                    <span class="text-danger"><?php echo form_error('dob'); ?></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Purok and Barangay </label>
                                    <input id="category" name="address" placeholder="" type="text" class="form-control"  value="<?php echo set_value('address'); ?>"  />
                                    <span class="text-danger"><?php echo form_error('address'); ?></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">City and Province </label>
                                    <input id="category" name="address2" placeholder="" type="text" class="form-control"  value="<?php echo set_value('address2'); ?>"  />
                                    <span class="text-danger"><?php echo form_error('address2'); ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Position </label>
                                    <input id="position" name="position" placeholder="" type="text" class="form-control"  value="<?php echo set_value('position'); ?>"  />
                                    <span class="text-danger"><?php echo form_error('position'); ?></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">TIN</label>
                                    <input id="category" name="tin" placeholder="" type="text" class="form-control"  value="<?php echo set_value('tin'); ?>" />
                                    <span class="text-danger"><?php echo form_error('tin'); ?></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">SSS</label>
                                    <input id="category" name="sss" placeholder="" type="text" class="form-control"  value="<?php echo set_value('sss'); ?>" />
                                    <span class="text-danger"><?php echo form_error('sss'); ?></span>
                                </div>
                            </div>
                            <div class="form-group"> 
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">PhilHealth</label>
                                    <input id="category" name="phic" placeholder="" type="text" class="form-control"  value="<?php echo set_value('phic'); ?>" />
                                    <span class="text-danger"><?php echo form_error('phic'); ?></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Pag-Ibig</label>
                                    <input id="category" name="pagibig" placeholder="" type="text" class="form-control"  value="<?php echo set_value('pagibig'); ?>" />
                                    <span class="text-danger"><?php echo form_error('pagibig'); ?></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">tPRC</label>
                                    <input id="category" name="prc_no" placeholder="" type="text" class="form-control"  value="<?php echo set_value('prc_no'); ?>" />
                                    <span class="text-danger"><?php echo form_error('prc_no'); ?></span>
                                </div>
                            </div>
                            <div class="form-group"> 
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">ID Number *</label>
                                    <input id="category" name="id_no" placeholder="" type="text" class="form-control"  value="<?php echo set_value('id_no'); ?>"  />
                                    <span class="text-danger"><?php echo form_error('id_no'); ?></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Emergency Contact Person Name</label>
                                    <input id="emergency" name="emergency" placeholder="" type="text" class="form-control"  value="<?php echo set_value('emergency'); ?>"  />
                                    <span class="text-danger"><?php echo form_error('emergency'); ?></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Emergency Contact Person Number</label>
                                    <input id="emergency_contact" name="emergency_contact" placeholder="" type="text" class="form-control"  value="<?php echo set_value('emergency_contact'); ?>"  />
                                    <span class="text-danger"><?php echo form_error('emergency_contact'); ?></span>
                                </div>
                            </div>
                            <div class="form-group"> 
 
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Date Hired</label>
                                    <input id="date_hired" name="date_hired" placeholder="" type="text" class="form-control"  value="<?php echo set_value('date_hired'); ?>"  />
                                    <span class="text-danger"><?php echo form_error('date_hired'); ?></span>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Active:</label>
                                    <!-- <input id="date_hired" name="date_hired" placeholder="" type="text" class="form-control"  value="<?php echo set_value('date_hired', $employee['date_hired']); ?>"  /> -->

                                    <label class="radio-inline">
                                      <input type="radio" name="is_active" value="yes" checked>Active
                                    </label>
                                    <label class="radio-inline">
                                      <input type="radio" name="is_active" value="no">In-Acitve
                                    </label>
                                    <span class="text-danger"><?php echo form_error('is_active'); ?></span>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Photo Dimensions(552 × 547)</label>
                                    <input id="image" name="image" placeholder="" type="file" class="form-control"  value="" />
                                    <span class="text-danger"><?php echo form_error('image'); ?></span>
                                     <?php
                                    "<br/><br/><br/>";
                                    // echo $employee['signature'];
                                    ?> 
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Signature Only upload PNG file(274 x 83)</label>
                                    <input id="signature" name="signature" placeholder="" type="file" class="form-control"  value="" />
                                    <span class="text-danger"><?php echo form_error('signature'); ?></span>
                                    <?php
                                    "<br/><br/><br/>";
                                    // echo $employee['signature'];
                                    ?> 
                                </div>
                            </div>
                            
                            <div class="form-group"> 

                                
                                 
                            </div>
                            
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </form>
                </div>   
            </div>   
        </div>
    </section>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
        $('#dob,#admission_date').datepicker({  
            format: date_format,
            autoclose: true
        });
        $("#btnreset").click(function () {            
            $("#form1")[0].reset();
        });
    });
</script>

<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';
    function printDiv(elem) {      
        Popup(jQuery(elem).html());
    }

          function Popup(data) 
        {
          
            var frame1 = $('<iframe />');
            frame1[0].name = "frame1";
            frame1.css({ "position": "absolute", "top": "-1000000px" });
            $("body").append(frame1);
            var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
            frameDoc.document.open();
        //Create a new HTML document.
        frameDoc.document.write('<html>');
        frameDoc.document.write('<head>');
        frameDoc.document.write('<title></title>');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/bootstrap/css/bootstrap.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/font-awesome.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/ionicons.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/AdminLTE.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/skins/_all-skins.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/iCheck/flat/blue.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/morris/morris.css">');


        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/datepicker/datepicker3.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/daterangepicker/daterangepicker-bs3.css">');
        frameDoc.document.write('</head>');
        frameDoc.document.write('<body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body>');
        frameDoc.document.write('</html>');
        frameDoc.document.close();
        setTimeout(function () {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
        }, 500);
        

        return true;
    }
</script>
