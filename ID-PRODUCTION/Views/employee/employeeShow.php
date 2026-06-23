<div class="content-wrapper" style="min-height: 946px;"> 
    <section class="content-header">
        <h1>
            <i class="fa fa-money"></i> <?php echo $this->lang->line('fees_collection'); ?> 
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">          
            <div class="col-md-6">              
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <img class="profile-user-img img-responsive img-circle" src="<?php echo base_url() . $userinfolist['image'] ?>" alt="User profile picture">
                        <h3 class="profile-username text-center"><?php echo $userinfolist['lastname'].', '.$userinfolist['firstname'].' '. $userinfolist['middlename'] ?><br/>
                       <small>
                            <a href="#"  class="schedule_modal text-green" data-toggle="tooltip" title="<?php echo $this->lang->line('login_detail'); ?>"><i class="fa fa-key"></i>
                                <?php echo $this->lang->line('login_details'); ?>
                            </a></small>
                        </h3>

                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b><?php echo $this->lang->line('gender'); ?></b> <a class="pull-right text-aqua"><?php echo $userinfolist['sex'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo $this->lang->line('date_of_birth'); ?></b> <a class="pull-right text-aqua"><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($userinfolist['dob'])); ?></a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo $this->lang->line('phone'); ?></b> <a class="pull-right text-aqua"><?php echo $userinfolist['phone'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo $this->lang->line('gender'); ?></b> <a class="pull-right text-aqua"><?php echo $userinfolist['sex'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo $this->lang->line('email'); ?></b> <a class="pull-right text-aqua"><?php echo $userinfolist['email'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo $this->lang->line('address'); ?></b> <a class="pull-right text-aqua"><?php echo $userinfolist['address'] ?></a>
                            </li>
                            <hr/>
                            <li class="list-group-item">
                                <b>TIN Number</b> <a class="pull-right text-aqua"><?php echo $userinfolist['tin_number'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>SSS Number</b> <a class="pull-right text-aqua"><?php echo $userinfolist['sss_number'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>PhilHealth Number</b> <a class="pull-right text-aqua"><?php echo $userinfolist['philhealth_number'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>PagIbig Number</b> <a class="pull-right text-aqua"><?php echo $userinfolist['pagibig_number'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Date Hired</b> <a class="pull-right text-aqua"><?php echo $userinfolist['date_hired'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Position</b> <a class="pull-right text-aqua"><?php echo $userinfolist['position'] ?></a>
                            </li>
                            <hr/>
                            <li class="list-group-item">
                                <b>Emergency Contact Person</b> <a class="pull-right text-aqua"><?php echo $userinfolist['emergency_contact_person'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Emergency Contact Number</b> <a class="pull-right text-aqua"><?php echo $userinfolist['emergency_contact_number'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>RFID Number</b> <a class="pull-right text-aqua"><?php echo $userinfolist['rfid_number'] ?></a>

 
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
        </div> 
    </section>
</div>

<script type="text/javascript">
    $(document).on('click', '.schedule_modal', function () {
        $('.modal-title').html("");
        $('.modal-title').html("<?php echo $this->lang->line('login_details'); ?>");
        var base_url = '<?php echo base_url() ?>';
        var user_id = '<?php echo $id ?>';
        var user_role = '<?php echo $role ?>';
        var teacher_name = '<?php echo $userinfolist["firstname"] ?>';
        $.ajax({
            type: "post",
            url: base_url + "hr/employee/getlogindetail",
            data: {'user_id': user_id, 'user_role': user_role},
            dataType: "json",
            success: function (response) {         
                var data = "";
                data += '<div class="table-responsive">';
                data += '<p class="lead text text-center">' + teacher_name + '</p>';
                data += '<table class="table table-hover">';
                data += '<thead>';
                data += '<tr>';
                data += '<th>'+"<?php echo $this->lang->line('user_type'); ?>"+'</th>';
                data += '<th class="text text-center">'+"<?php echo $this->lang->line('username'); ?>"+'</th>';
                data += '<th class="text text-center">'+"<?php echo $this->lang->line('password'); ?>"+'</th>';
                data += '</tr>';
                data += '</thead>';
                data += '<tbody>';
                $.each(response, function (i, obj)
                {
                    console.log(obj);
                    data += '<tr>';
                    data += '<td><b>' + firstToUpperCase(obj.role) + '</b></td>';
                    data += '<td class="text text-center">' + obj.username + '</td> ';
                    data += '<td class="text text-center">' + obj.password + '</td> ';
                    data += '</tr>';
                });
                data += '</tbody>';
                data += '</table>';
                data += '<b class="lead text text-danger" style="font-size:14px;"> '+"<?php echo $this->lang->line('login_url'); ?>"+': ' + base_url + 'site/userlogin</b>';
                data += '</div>  ';              
                $('.modal-body').html(data);
                $("#scheduleModal").modal('show');
            }
        });
    });

    function firstToUpperCase(str) {
        return str.substr(0, 1).toUpperCase() + str.substr(1);
    }
</script>

<div id="scheduleModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
            </div>
        </div>
    </div>
</div>