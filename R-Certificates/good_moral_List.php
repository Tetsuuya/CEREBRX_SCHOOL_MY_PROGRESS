<!-- Toastify CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<style type="text/css">
    @media print {
        .no-print, .no-print * { display: none !important; }
    }

    /* Row highlight on check */
    tr.row-checked td { background-color: #fff8e1 !important; }

    /* ── Custom checkbox ── */
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
    /* Tick mark */
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
    /* Checked state */
    .cchk.is-checked .cchk-box {
        background: #822D36;
        border-color: #822D36;
    }
    .cchk.is-checked .cchk-box::after { display: block; }
    /* Indeterminate state (master only) */
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

    /* ── Checkbox column: always visible, beside LRN ── */
    #good-moral-table th.col-select-chk,
    #good-moral-table td.col-select-chk {
        width: 36px;
        text-align: center;
        vertical-align: middle;
    }

    /* Rows are clickable */
    #good-moral-table tbody tr { cursor: pointer; }

    /* Select-all checkbox in thead */
    #gm-select-all-cchk { cursor: pointer; }
</style>

<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1><i class=" "></i> Good Moral</h1>
    </section>
    <section class="content">
        <div class="row">

            <!-- ══ Filter Form ══ -->
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Select Grade</h3>
                    </div>
                    <form id="form1" action="<?php echo site_url('registrar/certificate/good_moral') ?>" name="classform" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php if ($this->session->flashdata('msg')) { ?>
                                <?php echo $this->session->flashdata('msg') ?>
                            <?php } ?>
                            <?php echo $this->customlib->getCSRF(); ?>

                            <div class="form-group">
                                <label><?php echo "School Year"; ?></label>
                                <select id="year" name="year" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($school_year as $session): ?>
                                        <option value="<?php echo $session['id'] ?>" <?php if ($session['id'] == $year_id) echo "selected"; ?>>
                                            <?php
                                                $session_parts = explode('-', $session['session']);
                                                echo $session_parts[0] . '-' . $session_parts[1];
                                            ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('session_id'); ?></span>
                            </div>

                            <div class="form-group">
                                <label>Date</label>
                                <label for="date_input"><?php echo $this->lang->line('date_label'); ?></label>
                                <input type="date" id="date_input" name="date_input" class="form-control" value="<?php echo isset($date) ? $date : ''; ?>">
                                <span class="text-danger"><?php echo form_error('date_input'); ?></span>
                            </div>

                            <div class="form-group">
                                <label>Grade</label>
                                <select id="class_id" name="class_id" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($classlist as $class): ?>
                                        <option value="<?php echo $class['id'] ?>" <?php if ($class['id'] == $class_id) echo "selected"; ?>>
                                            <?php echo $class['class'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('class'); ?></span>
                            </div>

                            <div class="form-group">
                                <label><?php echo $this->lang->line('section'); ?></label>
                                <select id="section_id" name="section_id" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                </select>
                                <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('search'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Filter Form -->

            <!-- ══ Student List ══ -->
            <div class="col-md-8">
                <div class="box box-primary" id="sublist">

                    <div class="box-header with-border">
                        <h3 class="box-title">Student List</h3>
                    </div>

                    <div class="box-body">

                        <?php if (!empty($student_result)): ?>

                        <!--
                            ONE batch form wraps the entire table.
                            The old batch buttons (in box-header) are replaced by the
                            two buttons in the action bar below — they call openGmBatchModal()
                            which sets the template value before opening the modal.
                        -->
                        <form id="goodmoralbatchform"
                              action="<?php echo site_url('registrar/certificate/good_moral') ?>"
                              method="post"
                              accept-charset="utf-8">

                            <?php echo $this->customlib->getCSRF(); ?>
                            <input type="hidden" name="session_id"  value="<?php echo isset($session_id) ? $session_id : ''; ?>">
                            <input type="hidden" name="class_id"    value="<?php echo isset($class_id)   ? $class_id   : ''; ?>">
                            <input type="hidden" name="section_id"  value="<?php echo isset($section_id) ? $section_id : ''; ?>">
                            <input type="hidden" name="year"        value="<?php echo isset($year_id)    ? $year_id    : ''; ?>">
                            <input type="hidden" name="date_input"  value="<?php echo isset($date)       ? $date       : ''; ?>">
                            <input type="hidden" name="action"      value="print_batch">
                            <!-- template is set by JS when user picks WITHOUT or WITH VIOLATION -->
                            <input type="hidden" id="gm_batch_template" name="template" value="">

                            <!-- Top action bar: selection counter (left) + batch buttons (right) -->
                            <div class="no-print" style="margin-bottom:10px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px;">
                                <span style="font-size:12px; color:#888;">
                                    <span id="gm-selected-count">0</span> selected
                                </span>
                                <div style="display:flex; gap:6px;">
                                    <button type="button" class="btn btn-info btn-sm" onclick="submitGmBatch(1)">
                                        <i class="fa fa-print"></i> <strong>Batch - WITHOUT-VIOLATION</strong>
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" onclick="submitGmBatch(2)">
                                        <i class="fa fa-print"></i> <strong>Batch - WITH-VIOLATION</strong>
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive mailbox-messages">
                                <table class="table table-striped table-bordered table-hover example" id="good-moral-table">
                                    <thead>
                                        <tr>
                                            <!-- Checkbox column — first, right beside LRN -->
                                            <th class="col-select-chk no-print">
                                                <span class="cchk" id="gm-select-all-cchk"
                                                      onclick="gmSelectAll(this)"
                                                      title="Select / deselect all">
                                                    <span class="cchk-box"></span>
                                                </span>
                                            </th>
                                            <th><?php echo $this->lang->line('lrn') ? $this->lang->line('lrn') : 'LRN'; ?></th>
                                            <th><?php echo $this->lang->line('student_name'); ?></th>
                                            <th><?php echo $this->lang->line('gender'); ?></th>
                                            <th class="text-right no-print"><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($student_result as $student):
                                            $full_name = htmlspecialchars(
                                                $student['lastname'].', '.$student['firstname'].' '.$student['middlename'],
                                                ENT_QUOTES
                                            );
                                        ?>
                                        <tr data-id="<?php echo $student['id']; ?>"
                                            data-name="<?php echo $full_name; ?>">

                                            <!-- Checkbox cell beside LRN -->
                                            <td class="col-select-chk no-print">
                                                <span class="cchk gm-row-cchk"
                                                      data-sid="<?php echo $student['id']; ?>"
                                                      data-name="<?php echo $full_name; ?>">
                                                    <span class="cchk-box"></span>
                                                </span>
                                            </td>

                                            <td class="mailbox-name"><?php echo $student['lrn']; ?></td>
                                            <td class="mailbox-name"><?php echo $student['lastname'].', '.$student['firstname'].' '.$student['middlename']; ?></td>
                                            <td class="mailbox-name"><?php echo $student['gender']; ?></td>

                                            <!-- Individual print buttons — same as your original -->
                                            <td class="mailbox-date pull-right no-print">
                                                <form action="<?php echo site_url('registrar/certificate/good_moral') ?>" method="post" accept-charset="utf-8">
                                                    <?php echo $this->customlib->getCSRF(); ?>
                                                    <input type="hidden" name="session_id"  value="<?php echo isset($session_id) ? $session_id : ''; ?>">
                                                    <input type="hidden" name="class_id"    value="<?php echo $class_id; ?>">
                                                    <input type="hidden" name="year"        value="<?php echo isset($year_id) ? $year_id : ''; ?>">
                                                    <input type="hidden" name="date_input"  value="<?php echo isset($date) ? $date : ''; ?>">
                                                    <input type="hidden" name="student_id"  value="<?php echo $student['id']; ?>">
                                                    <input type="hidden" name="template"    value="">

                                                    <button type="submit" name="action" value="print_individual"
                                                            onclick="setTemplateIndividual(1, this)"
                                                            class="custom-button">
                                                        <i class="fa fa-print"></i> WITHOUT-VIOLATION
                                                    </button>

                                                    <button type="submit" name="action" value="print_individual"
                                                            onclick="setTemplateIndividual(2, this)"
                                                            class="custom-button">
                                                        <i class="fa fa-print"></i> WITH-VIOLATION
                                                    </button>
                                                </form>
                                            </td>

                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive -->

                        </form><!-- /#goodmoralbatchform -->

                        <?php else: ?>
                            <p class="text-muted">No students found. Please select a grade and section.</p>
                        <?php endif; ?>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
            <!-- /Student List -->

        </div>
    </section>
</div>


<!-- ══════════════════════════════════════════════
     SCRIPTS
══════════════════════════════════════════════ -->

<script type="text/javascript">
    /* ── Keep your original template setters ── */
    function setTemplateBatch(templateId) {
        document.getElementById('gm_batch_template').value = templateId;
    }
    function setTemplateIndividual(templateId, button) {
        let form = button.closest('form');
        form.querySelector('input[name="template"]').value = templateId;
    }

    /* ── Popup/printDiv kept for any other usage ── */
    var base_url = '<?php echo base_url() ?>';
    function printDiv(elem) { Popup(jQuery(elem).html()); }
    function Popup(data) {
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        frame1.css({ "position": "absolute", "top": "-1000000px" });
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow
                     : (frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument);
        frameDoc.document.open();
        frameDoc.document.write('<html><head><title></title>');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/bootstrap/css/bootstrap.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/font-awesome.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/AdminLTE.min.css">');
        frameDoc.document.write('</head><body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body></html>');
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
/* ═══════════════════════════════════════════════
   TOASTIFY HELPER
═══════════════════════════════════════════════ */
function gmShowToast(message, type, duration) {
    var colors = {
        success: 'linear-gradient(to right, #2e7d32, #43a047)',
        error:   'linear-gradient(to right, #822D36, #c0392b)',
        warning: 'linear-gradient(to right, #e65100, #ef6c00)',
        info:    'linear-gradient(to right, #1565c0, #1976d2)'
    };
    Toastify({
        text: message,
        duration: duration !== undefined ? duration : 4000,
        close: true,
        gravity: 'top',
        position: 'center',
        stopOnFocus: true,
        style: {
            background: colors[type] || colors.info,
            borderRadius: '8px',
            fontSize: '13px',
            minWidth: '300px',
            maxWidth: '480px',
            lineHeight: '1.6',
            boxShadow: '0 4px 18px rgba(0,0,0,0.18)'
        }
    }).showToast();
}

/* ═══════════════════════════════════════════════
   CHECKBOX STATE HELPERS
═══════════════════════════════════════════════ */
function cchkIsChecked(el)  { return el.classList.contains('is-checked'); }
function cchkSet(el, state) {
    el.classList.toggle('is-checked',       state === true);
    el.classList.toggle('is-indeterminate', false);
}
function cchkSetIndeterminate(el) {
    el.classList.remove('is-checked');
    el.classList.add('is-indeterminate');
}

/* ═══════════════════════════════════════════════
   KEEP MASTER CHECKBOX IN SYNC
═══════════════════════════════════════════════ */
function updateGmMasterCheckbox() {
    var all     = document.querySelectorAll('#good-moral-table .gm-row-cchk').length;
    var checked = document.querySelectorAll('#good-moral-table .gm-row-cchk.is-checked').length;
    var master  = document.getElementById('gm-select-all-cchk');
    if (!master) return;
    if      (checked === 0)   cchkSet(master, false);
    else if (checked === all) cchkSet(master, true);
    else                      cchkSetIndeterminate(master);

    var lbl = document.getElementById('gm-selected-count');
    if (lbl) lbl.textContent = checked;
}

/* ═══════════════════════════════════════════════
   MASTER CHECKBOX — SELECT / DESELECT ALL
═══════════════════════════════════════════════ */
function gmSelectAll(masterEl) {
    var shouldSelect = !cchkIsChecked(masterEl);
    document.querySelectorAll('#good-moral-table .gm-row-cchk').forEach(function(chk) {
        cchkSet(chk, shouldSelect);
        chk.closest('tr').classList.toggle('row-checked', shouldSelect);
    });
    cchkSet(masterEl, shouldSelect);
    updateGmMasterCheckbox();
}

/* ═══════════════════════════════════════════════
   WIRE ROW CLICKS + INDIVIDUAL CHECKBOXES
═══════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#good-moral-table tbody tr').forEach(function(row) {

        /* Click anywhere on the row (not on a button/form) toggles checkbox */
        row.addEventListener('click', function(e) {
            if (e.target.closest('button') || e.target.closest('form')) return;
            var chk = row.querySelector('.gm-row-cchk');
            if (!chk) return;
            var nowChecked = !cchkIsChecked(chk);
            cchkSet(chk, nowChecked);
            row.classList.toggle('row-checked', nowChecked);
            updateGmMasterCheckbox();
        });

        /* Click directly on the custom checkbox span */
        var chk = row.querySelector('.gm-row-cchk');
        if (chk) {
            chk.addEventListener('click', function(e) {
                e.stopPropagation();
                var nowChecked = !cchkIsChecked(chk);
                cchkSet(chk, nowChecked);
                row.classList.toggle('row-checked', nowChecked);
                updateGmMasterCheckbox();
            });
        }
    });
});

/* ═══════════════════════════════════════════════
   OPEN BATCH MODAL
   templateId: 1 = WITHOUT-VIOLATION, 2 = WITH-VIOLATION
═══════════════════════════════════════════════ */
function openGmBatchModal(templateId) {
    var total = document.querySelectorAll('#good-moral-table .gm-row-cchk').length;
    if (total === 0) {
        gmShowToast('\u26A0 No students found in the list.', 'warning');
        return;
    }

    /* Store template choice so submitGmBatch() can read it */
    document.getElementById('gm_batch_template').value = templateId;

    /* Update modal title */
    var typeLabel = (templateId == 1) ? 'WITHOUT-VIOLATION' : 'WITH-VIOLATION';
    document.getElementById('gm-modal-type-label').textContent = typeLabel;

    /* Show count */
    var checked = document.querySelectorAll('#good-moral-table .gm-row-cchk.is-checked');
    var count   = checked.length > 0 ? checked.length : total;
    document.getElementById('gm-batch-count').textContent = count;

    var note = document.getElementById('gm-batch-all-note');
    note.textContent = (checked.length === 0)
        ? '(No selection \u2014 all students will be printed.)'
        : '';

    $('#confirm_gm_batch').modal('show');
}

/* ═══════════════════════════════════════════════
   SUBMIT BATCH FORM
   Injects batch_id[] hidden inputs, then submits.
═══════════════════════════════════════════════ */
function submitGmBatch() {
    var dateVal = document.getElementById('gm_confirm_date').value;
    if (!dateVal) {
        gmShowToast('\u26A0 Please confirm the date before printing.', 'warning');
        return;
    }

    /* Sync confirmed date back into the form */
    var formDate = document.querySelector('#goodmoralbatchform input[name="date_input"]');
    if (formDate) formDate.value = dateVal;

    /* Remove any previously injected inputs */
    document.querySelectorAll('#goodmoralbatchform .gm-batch-dynamic').forEach(function(el) { el.remove(); });

    var checked = document.querySelectorAll('#good-moral-table .gm-row-cchk.is-checked');
    var targets = checked.length > 0
        ? checked   /* only selected students */
        : document.querySelectorAll('#good-moral-table .gm-row-cchk'); /* all if none selected */

    if (targets.length === 0) {
        gmShowToast('\u2716 No students to print.', 'error');
        return;
    }

    /* Inject one batch_id[] per target student */
    targets.forEach(function(chk) {
        var input       = document.createElement('input');
        input.type      = 'hidden';
        input.className = 'gm-batch-dynamic';
        input.name      = 'batch_id[]';
        input.value     = chk.getAttribute('data-sid');
        document.getElementById('goodmoralbatchform').appendChild(input);
    });

    $('#confirm_gm_batch').modal('hide');
    document.getElementById('goodmoralbatchform').submit();
}
</script>

<!-- ── Original section-dropdown / datepicker / auto-submit scripts (unchanged) ── -->
<script type="text/javascript">
    $(document).ready(function () {
        $("#btnreset").click(function () {
            $("#form1")[0].reset();
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        var session_id_post = '<?php echo $year_id; ?>';
        console.log(session_id_post);
        var section_id_post = '<?php echo $section_id; ?>';
        var class_id_post   = '<?php echo $class_id; ?>';
        populateSection(session_id_post, section_id_post, class_id_post);

        function populateSection(session_id_post, section_id_post, class_id_post) {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "/registrar/sections/getByClass",
                data: { 'class_id': class_id_post, 'session_id': session_id_post },
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj) {
                        var select = (section_id_post == obj.section_id) ? "selected=selected" : "";
                        div_data += "<option value=" + obj.section_id + " " + select + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        }

        $(document).on('change', '#class_id', function () {
            $('#section_id').html("");
            var class_id = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "registrar/sections/getByClass",
                data: { 'class_id': class_id },
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj) {
                        div_data += "<option value=" + obj.section_id + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        });

        var date_format = '<?php echo strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
        $('#date').datepicker({
            format: date_format,
            autoclose: true
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const classSelect = document.getElementById('class_id');
        const yearSelect  = document.getElementById('year');
        const form        = document.getElementById('form1');
        function submitForm() { form.submit(); }
        yearSelect.addEventListener('change', submitForm);
        classSelect.addEventListener('change', submitForm);
    });
</script>