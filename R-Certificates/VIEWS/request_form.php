<style type="text/css">
    @media print
    {
        .no-print, .no-print *
        {
            display: none !important;
        }
    }
    
    .text-danger {
        font-size: 9pt;
        color: #dc3545; /* Bootstrap's danger color */
    }

    .mailbox-date .form-control,
    .mailbox-date .btn {
        font-size: 0.85rem;
        padding: 0.25rem 0.5rem;
    }
    .text-danger.small {
        font-size: 0.75rem;
    }

    tr.row-checked td { background-color: #fff8e1 !important; }

    .cchk {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        -webkit-user-select: none;
        user-select: none;
    }

    .cchk-box {
        width: 16px;
        height: 16px;
        min-width: 16px;
        border: 2px solid #bbb;
        border-radius: 3px;
        background: #fff;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .12s, border-color .12s;
        position: relative;
    }

    .cchk-box::after {
        content: '';
        display: none;
        position: absolute;
        left: 50%;
        top: 45%;
        width: 4px;
        height: 8px;
        border: 2px solid #fff;
        border-top: none;
        border-left: none;
        transform: translate(-50%, -50%) rotate(45deg);
    }

    .cchk.is-checked .cchk-box {
        background: #822D36;
        border-color: #822D36;
    }

    .cchk.is-checked .cchk-box::after { display: block; }
    .cchk.is-indeterminate .cchk-box {
        background: #822D36;
        border-color: #822D36;
    }

    .cchk.is-indeterminate .cchk-box::after {
        display: block;
        position: absolute;
        left: 50%;
        top: 50%;
        width: 8px;
        height: 0;
        border: none;
        border-top: 2px solid #fff;
        transform: translate(-50%, -50%);
    }

    #request-form-table th.col-select-chk,
    #request-form-table td.col-select-chk {
        width: 36px;
        text-align: center;
        vertical-align: middle;
    }

    #request-form-table tbody tr { cursor: pointer; }

</style>

<div class="content-wrapper" style="min-height: 946px;">   
    <section class="content-header">
        <h1> <i class=" "></i> Request Form </h1>
    </section>   
    <section class="content">
        <div class="row">          
            <div class="col-md-4">              
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Select Grade</h3>
                    </div> 
                    <form id="form1" action="<?php echo site_url('registrar/certificate/request_form') ?>"  id="classform" name="classform" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php if ($this->session->flashdata('msg')) { ?>
                                <?php echo $this->session->flashdata('msg') ?>
                            <?php } ?>      
							<?php echo $this->customlib->getCSRF(); ?>
                            <!-- School Year Here-->
                            <div class="form-group">

										<label for="exampleInputEmail1"><?php echo "School Year"; ?></label>

										<select  id="year" name="year" class="form-control" >

											<option value=""><?php echo $this->lang->line('select'); ?></option>

											<?php

											foreach ($school_year as $session) {

											?>

											<option value="<?php echo $session['id'] ?>" <?php if($session['id'] == $year_id){ echo "selected"; } ?>>
                                                    <?php 
                                                        $session_parts = explode('-', $session['session']); 
                                                        echo $session_parts[0] . '-' . $session_parts[1]; // Outputs 2023-2024
                                                    ?>
                                            </option>
											<?php

											$count++;

											}

											?>

										</select>					

										<span class="text-danger"><?php echo form_error('session_id'); ?></span>

                            </div>
                           

                             <!-- Grade level -->
                            <div class="form-group">
                                <label>Grade</label>
                                <select  id="class_id" name="class_id" class="form-control" >
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php
                                    foreach ($classlist as $class) {
                                        ?>
                                        <option value="<?php echo $class['id'] ?>" <?php if( $class['id']  == $class_id ){ echo "selected";}?>><?php echo $class['class'] ?></option>
                                        <?php
                                        $count++;
                                    }
                                    ?>
                                </select> 
								<span class="text-danger"><?php echo form_error('class'); ?></span>
                            </div>  

                            <!-- section -->
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('section'); ?></label>
                                <select  id="section_id" name="section_id" class="form-control" >
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                </select>
                                <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                            </div>   

                            <!-- date -->
                             <div class="form-group">
                                <label>Date</label>
                                <label for="exampleInputDate"><?php echo $this->lang->line('date_label'); ?></label>
                                <input type="date" id="date_input" name="date_input" class="form-control" value="<?php echo isset($date) ? $date : ''; ?>">
                                <span class="text-danger"><?php echo form_error('date_input'); ?></span>
                            </div>

                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('search'); ?></button>
                        </div>
                    </form>
                </div> 
            </div>
            <div class="col-md-8">               
                <div class="box box-primary" id="sublist"> 
					<div class="box-header with-border">
                        <h3 class="box-title">Student List</h3> 
                        <?php 
                        if( isset($class_id) && isset($section_id) && $class_id && $section_id ){
                            ?>
                            <form id="form1" action="<?php echo site_url('registrar/certificate/request_form') ?>"  id="classform" name="classform" method="post" accept-charset="utf-8">
                                <input type="hidden" name="session_id" value="<?php echo $session_id; ?>">
                                <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                                <input type="hidden" name="section_id" value="<?php echo $section_id; ?>">
                                <input type="hidden" name="year" value="<?php echo $year_id; ?>">
                                <input type="hidden" name="date_input" value="<?php echo $date; ?>">
                                <input type="hidden" name="action" value="print_batch">
                                <!-- print batch button   -->
                            </form>
                            <?php
                        }
                        ?>
                    </div> 
                    
                    <div class="box-body">
                        <!-- selection counter and batch print button -->
                        <div class="no-print" style="margin-bottom:10px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px;">
                            <span style="font-size:12px; color:#888;">
                                <span id="rf-selected-count">0</span> selected
                            </span>
                            <div>
                                <button type="button" class="btn btn-info btn-sm" onclick="submitRfBatch()">
                                    <i class="fa fa-print"></i> <strong>Print Batch</strong>
                                </button>
                            </div>
                        </div>

                        <form id="requestformbatchform"
                              action="<?php echo site_url('registrar/certificate/request_form') ?>"
                              method="post"
                              accept-charset="utf-8">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <input type="hidden" name="session_id"  value="<?php echo isset($session_id) ? $session_id : ''; ?>">
                            <input type="hidden" name="class_id"    value="<?php echo isset($class_id)   ? $class_id   : ''; ?>">
                            <input type="hidden" name="section_id"  value="<?php echo isset($section_id) ? $section_id : ''; ?>">
                            <input type="hidden" name="year"        value="<?php echo isset($year_id)    ? $year_id    : ''; ?>">
                            <input type="hidden" name="date_input"  value="<?php echo isset($date)       ? $date       : ''; ?>">
                            <input type="hidden" name="action"      value="print_batch">

                            <div class="table-responsive mailbox-messages">
                                <table class="table table-striped table-bordered table-hover example" id="request-form-table">
                                <thead>
                                    <tr> 
                                        <!-- checkbox column header -->
                                        <th class="col-select-chk no-print">
                                            <span class="cchk" id="rf-select-all-cchk"
                                                  onclick="rfSelectAll(this)"
                                                  title="Select / deselect all">
                                                <span class="cchk-box"></span>
                                            </span>
                                        </th>
                                        <th><?php echo $this->lang->line('lrn') ? $this->lang->line('lrn') : 'LRN'; ?></th>
                                        <th><?php echo $this->lang->line('student_name'); ?></th>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <th>School Name</th>
                                        <th>School Address</th>
                                        <th>Remarks</th>
                                        <th class="text-right no-print"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($student_result)) { ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No students found</td>
                                        </tr>
                                    <?php } else {
                                        foreach ($student_result as $student) { 
                                            $full_name = htmlspecialchars(
                                                $student['lastname'].', '.$student['firstname'].' '.$student['middlename'],
                                                ENT_QUOTES
                                            );
                                        ?>
                                            <tr data-id="<?php echo $student['id']; ?>" data-name="<?php echo $full_name; ?>">
 
                                                <!-- checkbox cell -->
                                                <td class="col-select-chk no-print">
                                                    <span class="cchk rf-row-cchk"
                                                          data-sid="<?php echo $student['id']; ?>"
                                                          data-name="<?php echo $full_name; ?>">
                                                        <span class="cchk-box"></span>
                                                    </span>
                                                </td>

                                                <!-- LRN -->
                                                <td class="mailbox-name"><?php echo $student['lrn']; ?></td>

                                                <!-- Student Name -->
                                                <td class="mailbox-name">
                                                    <?php echo $student['lastname'] . ', ' . $student['firstname'] . ' ' . $student['middlename']; ?>
                                                </td>

                                                <!-- Gender -->
                                                <td class="mailbox-name"><?php echo $student['gender']; ?></td>

                                                <!-- School Name -->
                                                <td>

                                                         <input type="text" 
                                                             name="recipient_school[<?php echo $student['id']; ?>]" 
                                                             class="form-control form-control-sm" 
                                                             placeholder="School Name" 
                                                             id="school_input_<?php echo $student['id']; ?>"
                                                             value="<?php echo !empty($student['school_name']) ? htmlspecialchars($student['school_name']) : ''; ?>"
                                                             required>
                                                         <div id="school_error_<?php echo $student['id']; ?>" class="text-danger small"></div>
                                                     </td>

                                                 <!-- School Address -->
                                                 <td>
                                                     <input type="text" 
                                                         name="school_address[<?php echo $student['id']; ?>]" 
                                                         class="form-control form-control-sm" 
                                                         placeholder="School Address" 
                                                         id="address_input_<?php echo $student['id']; ?>" 
                                                         value="<?php echo !empty($student['school_address']) ? htmlspecialchars($student['school_address']) : ''; ?>"
                                                         required>
                                                     <div id="address_error_<?php echo $student['id']; ?>" class="text-danger small"></div>
                                                 </td>

                                                 <!-- Remarks -->
                                                 <td>
                                                     <input type="text" 
                                                         name="remarks[<?php echo $student['id']; ?>]" 
                                                         class="form-control form-control-sm" 
                                                         placeholder="Remarks" 
                                                         id="remarks_input_<?php echo $student['id']; ?>" 
                                                         required>
                                                     <div id="remarks_error_<?php echo $student['id']; ?>" class="text-danger small"></div>
                                                 </td>

                                                <!-- print button -->
                                                <td class="text-right no-print">
                                                    <button type="button" 
                                                            onclick="submitRfSingle(<?php echo $student['id']; ?>)"
                                                            class="btn btn-default btn-sm" 
                                                            data-toggle="tooltip" 
                                                            title="<?php echo $this->lang->line('print'); ?>">
                                                        <i class="fa fa-print"></i> Print
                                                    </button>
                                                </td>
                                         </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        </form>
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

<script type="text/javascript">
    $(document).ready(function () {
    var session_id_post = '<?php echo $year_id; ?>';
    console.log(session_id_post);
    var section_id_post = '<?php echo $section_id; ?>';
    var class_id_post = '<?php echo $class_id; ?>';
    populateSection(session_id_post, section_id_post, class_id_post);

    function populateSection(session_id_post, section_id_post, class_id_post) {
        $('#section_id').html("");
        var base_url = '<?php echo base_url() ?>';
        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        $.ajax({
            type: "GET",
            url: base_url + "/registrar/sections/getByClass",
            data: {'class_id': class_id_post, 'session_id' : session_id_post},
            dataType: "json",
            success: function (data) {
                $.each(data, function (i, obj)
                {
                    var select = "";
                    if (section_id_post == obj.section_id) {
                        var select = "selected=selected"; 
                    }
                    div_data += "<option value=" + obj.section_id + " " + select + ">" + obj.section + "</option>";
                });
                $('#section_id').append(div_data);
            }
        });
    }

    $(document).on('change', '#class_id', function (e) {
        $('#section_id').html("");
        var class_id = $(this).val();
        var base_url = '<?php echo base_url() ?>';
        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        $.ajax({
            type: "GET",
            url: base_url + "registrar/sections/getByClass",
            data: {'class_id': class_id},
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
    var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
    $('#date').datepicker({
        format: date_format,
        autoclose: true
    });
});



</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the class_id, year select elements, and form
        const classSelect = document.getElementById('class_id');
        const yearSelect = document.getElementById('year');
        const form = document.getElementById('form1');

        // Function to submit the form
        function submitForm() {
            form.submit();
        }

        // Listen for changes on the year dropdown
        yearSelect.addEventListener('change', submitForm);

        // Listen for changes on the class dropdown
        classSelect.addEventListener('change', submitForm);
    });
</script>

<script>
function validateForm(studentId) {
    var schoolInput = document.getElementById("school_input_" + studentId);
    var addressInput = document.getElementById("address_input_" + studentId);
    var remarksInput = document.getElementById("remarks_input_" + studentId);

    var schoolVal = schoolInput.value.trim();
    var addressVal = addressInput.value.trim();
    var remarksVal = remarksInput.value.trim();

    var isValid = true;

    // Reset previous errors
    document.getElementById("school_error_" + studentId).innerText = "";
    document.getElementById("address_error_" + studentId).innerText = "";
    document.getElementById("remarks_error_" + studentId).innerText = "";

    if (schoolVal === "") {
        document.getElementById("school_error_" + studentId).innerText = "School name is required.";
        schoolInput.focus();
        isValid = false;
    }

    if (addressVal === "") {
        document.getElementById("address_error_" + studentId).innerText = "School address is required.";
        addressInput.focus();
        isValid = false;
    }

    if (remarksVal === "") {
        document.getElementById("remarks_error_" + studentId).innerText = "Remarks are required.";
        remarksInput.focus();
        isValid = false;
    }

    return isValid;
}
</script>

<script type="text/javascript">
/* checkbox state helpers */
function cchkIsChecked(el)  { return el.classList.contains('is-checked'); }
function cchkSet(el, state) {
    el.classList.toggle('is-checked',       state === true);
    el.classList.toggle('is-indeterminate', false);
}
function cchkSetIndeterminate(el) {
    el.classList.remove('is-checked');
    el.classList.add('is-indeterminate');
}

/* keep master checkbox in sync */
function updateRfMasterCheckbox() {
    var all     = document.querySelectorAll('#request-form-table .rf-row-cchk').length;
    var checked = document.querySelectorAll('#request-form-table .rf-row-cchk.is-checked').length;
    var master  = document.getElementById('rf-select-all-cchk');
    if (!master) return;
    
    if      (checked === 0)   cchkSet(master, false);
    else if (checked === all) cchkSet(master, true);
    else                      cchkSetIndeterminate(master);

    var lbl = document.getElementById('rf-selected-count');
    if (lbl) lbl.textContent = checked;
}

/* master checkbox — select / deselect all */
function rfSelectAll(masterEl) {
    var shouldSelect = !cchkIsChecked(masterEl);
    document.querySelectorAll('#request-form-table .rf-row-cchk').forEach(function(chk) {
        cchkSet(chk, shouldSelect);
        chk.closest('tr').classList.toggle('row-checked', shouldSelect);
    });
    cchkSet(masterEl, shouldSelect);
    updateRfMasterCheckbox();
}

/* individual checkboxes */
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#request-form-table tbody tr').forEach(function(row) {
      
        row.addEventListener('click', function(e) {
            if (e.target.closest('button') || e.target.closest('form') || e.target.closest('input')) return;
            var chk = row.querySelector('.rf-row-cchk');
            if (!chk) return;
            var nowChecked = !cchkIsChecked(chk);
            cchkSet(chk, nowChecked);
            row.classList.toggle('row-checked', nowChecked);
            updateRfMasterCheckbox();
        });

        var chk = row.querySelector('.rf-row-cchk');
        if (chk) {
            chk.addEventListener('click', function(e) {
                e.stopPropagation();
                var nowChecked = !cchkIsChecked(chk);
                cchkSet(chk, nowChecked);
                row.classList.toggle('row-checked', nowChecked);
                updateRfMasterCheckbox();
            });
        }
    });
});

/* submit batch form */
function submitRfBatch() {
    var checked = document.querySelectorAll('#request-form-table .rf-row-cchk.is-checked');
    var targets = checked.length > 0
        ? checked   /* only selected students */
        : document.querySelectorAll('#request-form-table .rf-row-cchk'); /* all if none selected */

    if (targets.length === 0) {
        alert('No students to print.');
        return;
    }

    var isValid = true;
    
    // clear any previous batch inputs to avoid duplication
    document.querySelectorAll('#requestformbatchform .rf-batch-dynamic').forEach(function(el) { el.remove(); });

    // validate inputs for each target student and inject values dynamically into the batch form
    targets.forEach(function(chk) {
        var studentId = chk.getAttribute('data-sid');
        
        // run validation
        if (!validateForm(studentId)) {
            isValid = false;
            return;
        }

        // get values
        var schoolVal = document.getElementById('school_input_' + studentId).value.trim();
        var addressVal = document.getElementById('address_input_' + studentId).value.trim();
        var remarksVal = document.getElementById('remarks_input_' + studentId).value.trim();

        // create and append dynamic hidden inputs for batch form submission
        var inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.className = 'rf-batch-dynamic';
        inputId.name = 'batch_id[]';
        inputId.value = studentId;
        document.getElementById('requestformbatchform').appendChild(inputId);

        var inputSchool = document.createElement('input');
        inputSchool.type = 'hidden';
        inputSchool.className = 'rf-batch-dynamic';
        inputSchool.name = 'recipient_school[' + studentId + ']';
        inputSchool.value = schoolVal;
        document.getElementById('requestformbatchform').appendChild(inputSchool);

        var inputAddress = document.createElement('input');
        inputAddress.type = 'hidden';
        inputAddress.className = 'rf-batch-dynamic';
        inputAddress.name = 'school_address[' + studentId + ']';
        inputAddress.value = addressVal;
        document.getElementById('requestformbatchform').appendChild(inputAddress);

        var inputRemarks = document.createElement('input');
        inputRemarks.type = 'hidden';
        inputRemarks.className = 'rf-batch-dynamic';
        inputRemarks.name = 'remarks[' + studentId + ']';
        inputRemarks.value = remarksVal;
        document.getElementById('requestformbatchform').appendChild(inputRemarks);
    });

    if (isValid) {
        document.getElementById('requestformbatchform').submit();
    }
}

/* submit single print */
function submitRfSingle(studentId) {
    if (!validateForm(studentId)) {
        return;
    }

    var schoolVal = document.getElementById('school_input_' + studentId).value.trim();
    var addressVal = document.getElementById('address_input_' + studentId).value.trim();
    var remarksVal = document.getElementById('remarks_input_' + studentId).value.trim();

    // Create a temporary form to submit only this student's data
    var tempForm = document.createElement('form');
    tempForm.method = 'POST';
    tempForm.action = '<?php echo site_url("registrar/certificate/request_form"); ?>';
    tempForm.style.display = 'none';

    // Helper function to add hidden inputs
    function addHidden(name, value) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        tempForm.appendChild(input);
    }

    // Add session, class, section, year, date from the page state
    var sessionVal = document.querySelector('#requestformbatchform input[name="session_id"]').value;
    var classVal = document.querySelector('#requestformbatchform input[name="class_id"]').value;
    var sectionVal = document.querySelector('#requestformbatchform input[name="section_id"]').value;
    var yearVal = document.querySelector('#requestformbatchform input[name="year"]').value;
    var dateVal = document.querySelector('#requestformbatchform input[name="date_input"]').value;
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

    addHidden(csrfName, csrfHash);
    addHidden('session_id', sessionVal);
    addHidden('class_id', classVal);
    addHidden('section_id', sectionVal);
    addHidden('year', yearVal);
    addHidden('date_input', dateVal);
    addHidden('student_id', studentId);
    addHidden('recipient_school', schoolVal);
    addHidden('school_address', addressVal);
    addHidden('remarks', remarksVal);
    addHidden('action', ''); // empty for single print

    document.body.appendChild(tempForm);
    tempForm.submit();
    document.body.removeChild(tempForm);
}
</script>