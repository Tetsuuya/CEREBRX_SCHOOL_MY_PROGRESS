<style type="text/css">
    .dashboard-colored-box {
        font-size: 20px;
        vertical-align: middle; 
        display: block;
        text-align: center;  
        margin: 0 auto; 
        padding-top:10%;
    }
</style>
<div class="content-wrapper" style="min-height: 946px;">   
    
    <section class="content">    
        <div class="row">
            <div class="col-xs-12">
            <?php if ($this->session->flashdata('msg')) { ?>
                <?php echo $this->session->flashdata('msg') ?>
            <?php } ?>
            </div> 

            <div class="row">      
                <div class="col-xs-12"> 
                     <div class="col-md-4 col-sm-6 col-xs-12">
                        <a href="<?php echo base_url(); ?>idproduction/faculties">
                            <div class="info-box">
                                <span class="info-box-icon bg-orange"><i class="fa fa-users"></i></span>
                                <div class="info-box-content">
                                      <h3>Faculties</h3>
                                </div>
                            </div>
                        </a>
                    </div>     
                     <div class="col-md-4 col-sm-6 col-xs-12">
                        <a href="<?php echo base_url(); ?>idproduction/faculties">
                            <div class="info-box">
                                <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
                                <div class="info-box-content">
                                     <h3>Printed Faculties</h3>
                                </div>
                            </div>
                        </a>
                    </div>    
                     <div class="col-md-4 col-sm-6 col-xs-12">
                        <a href="<?php echo base_url(); ?>idproduction/faculties">
                            <div class="info-box">
                                <span class="info-box-icon bg-red"><i class="fa fa-users"></i></span>
                                <div class="info-box-content">
                                     <h3>Not Printed Faculties</h3>
                                </div>
                            </div>
                        </a>
                    </div> 
                </div>
            </div>
 
            <div class="row">      
                <div class="col-xs-12"> 
                     <div class="col-md-4 col-sm-6 col-xs-12">
                        <a href="<?php echo base_url(); ?>idproduction/students/pending">
                            <div class="info-box">
                                <span class="info-box-icon bg-orange"><i class="fa fa-users"></i></span>
                                <div class="info-box-content">
                                      <h3>Students Pending</h3>
                                </div>
                            </div>
                        </a>
                    </div>     
                     <div class="col-md-4 col-sm-6 col-xs-12">
                        <a href="<?php echo base_url(); ?>idproduction/students/process">
                            <div class="info-box">
                                <span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
                                <div class="info-box-content">
                                     <h3>Students Process</h3>
                                </div>
                            </div>
                        </a>
                    </div>    
                     <div class="col-md-4 col-sm-6 col-xs-12">
                        <a href="<?php echo base_url(); ?>idproduction/students/delivered">
                            <div class="info-box">
                                <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
                                <div class="info-box-content">
                                     <h3>Students Delivered</h3>
                                </div>
                            </div>
                        </a>
                    </div> 
                </div>
            </div>



        </div>
        <div class="row">         
			<div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <h3> <i class="fa fa-user-secret"></i>  <?php echo $this->lang->line('profile'); ?> </h3> 
                    </div> 
                </div>
              <div class="box box-primary">
                <div class="box-body box-profile">                 
                   <img class="profile-user-img img-responsive img-circle" src="<?php echo base_url().$idproduction['image']?>" alt="User profile picture">
      
                  <h3 class="profile-username text-center"><?php echo $idproduction['name'].' '.$idproduction['middlename'].' '.$idproduction['lastname'] ?></h3> 
                  <ul class="list-group list-group-unbordered">
                   <li class="list-group-item">
                      <b><?php echo $this->lang->line('gender'); ?></b> <a class="pull-right text-aqua"><?php echo $idproduction['sex'] ?></a>
                    </li>
                    <li class="list-group-item">
                      <b><?php echo $this->lang->line('date_of_birth'); ?></b> <a class="pull-right text-aqua">                    
                       <?php echo date($this->customlib->getSchoolDateFormat(),$this->customlib->yyyymmddTodateformat($idproduction['dob']));?>
                      </a>
                    </li>
                    <li class="list-group-item">
                      <b><?php echo $this->lang->line('phone'); ?></b> <a class="pull-right text-aqua"><?php echo $idproduction['phone'] ?></a>
                    </li> 
                    <li class="list-group-item">
                      <b><?php echo $this->lang->line('email'); ?></b> <a class="pull-right text-aqua"><?php echo $idproduction['email'] ?></a>
                    </li>
                     <li class="list-group-item">
                      <b><?php echo $this->lang->line('address'); ?></b> <a class="pull-right text-aqua"><?php echo $idproduction['address'] ?></a>
                    </li>     

                  </ul> 
                </div>
              </div>
            </div>
            <div class="col-md-8">
                  
            </div>
        </div>   
    </section>
</div>
 
<script type="text/javascript">
     

    function firstToUpperCase(str) {
        return str.substr(0, 1).toUpperCase() + str.substr(1);
    }
</script>

  

