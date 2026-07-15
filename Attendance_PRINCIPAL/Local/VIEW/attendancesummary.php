<style type="text/css">
    .summary-card {
        border-radius: 6px;
        border-left: 4px solid #3c8dbc;
    }
    .date-range-group label {
        font-weight: 600;
        color: #555;
    }
    .btn-generate {
        background: linear-gradient(135deg, #3c8dbc, #1a6fa8);
        color: #fff;
        border: none;
        padding: 9px 24px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.3px;
        transition: background 0.2s;
    }
    .btn-generate:hover {
        background: linear-gradient(135deg, #1a6fa8, #145f93);
        color: #fff;
    }
    .btn-generate i { margin-right: 6px; }
    .divider-label {
        font-size: 13px;
        font-weight: 700;
        color: #888;
        text-align: center;
        line-height: 34px;
    }
</style>

<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-bar-chart"></i> Attendance Summary
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Attendance</a></li>
            <li class="active">Attendance Summary</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary summary-card">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Select Criteria</h3>
                    </div>

                    <form id="summaryForm" action="<?php echo site_url('principal/stuattendence/export_attendance_summary'); ?>" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>

                            <!-- Row 1: Grade, Section, Subject -->
                            <div class="row">
                                <!-- Grade -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sum_class_id"><i class="fa fa-graduation-cap"></i> Grade</label>
                                        <select id="sum_class_id" name="class_id" class="form-control">
                                            <option value="">-- Select Grade --</option>
                                            <?php foreach ($classlist as $class): ?>
                                                <option value="<?php echo $class['id']; ?>" <?php echo ($class_id == $class['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $class['class']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Section -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sum_section_id"><i class="fa fa-users"></i> Section</label>
                                        <select id="sum_section_id" name="section_id" class="form-control">
                                            <option value="">-- Select Section --</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Subject -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sum_subject_id"><i class="fa fa-book"></i> Subject</label>
                                        <select id="sum_subject_id" name="subject_id" class="form-control">
                                            <option value="">-- Select Subject --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: Date Range -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-default" style="border-radius:4px; padding: 14px 18px 4px; background:#f9f9f9; border:1px solid #e0e0e0;">
                                        <p class="text-muted" style="margin-bottom:10px; font-weight:600;"><i class="fa fa-calendar"></i> Date Range</p>
                                        <div class="row date-range-group">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="sum_start_date">Start Date</label>
                                                    <input type="text"
                                                           id="sum_start_date"
                                                           name="start_date"
                                                           class="form-control"
                                                           placeholder="e.g. July 1, 2026"
                                                           autocomplete="off"
                                                           value="<?php echo isset($start_date) ? $start_date : ''; ?>"
                                                           required>
                                                </div>
                                            </div>

                                            <div class="col-md-1">
                                                <div class="divider-label">to</div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="sum_end_date">End Date</label>
                                                    <input type="text"
                                                           id="sum_end_date"
                                                           name="end_date"
                                                           class="form-control"
                                                           placeholder="e.g. July 15, 2026"
                                                           autocomplete="off"
                                                           value="<?php echo isset($end_date) ? $end_date : ''; ?>"
                                                           required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" id="btnGenerate" name="generate" value="1" class="btn btn-generate pull-right">
                                <i class="fa fa-file-excel-o"></i> Generate Attendance Summary
                            </button>
                        </div>
                    </form>
                </div><!-- /.box -->

                <!-- Info note -->
                <div class="callout callout-info" style="margin-top: 0;">
                    <h4><i class="fa fa-info-circle"></i> Note</h4>
                    <p>This will generate and download an <strong>Excel (.xlsx)</strong> attendance summary sheet for the selected class, section, subject, and date range. All students will be listed with their attendance counts (Present, Absent, Late, etc.) for the specified period.</p>
                </div>

            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
    </section>
</div><!-- /.content-wrapper -->

<script type="text/javascript">
    var base_url = '<?php echo base_url(); ?>';
    var date_format = '<?php echo strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']); ?>';

    $(document).ready(function () {

        // Date pickers
        $('#sum_start_date').datepicker({
            format: date_format,
            autoclose: true
        });
        $('#sum_end_date').datepicker({
            format: date_format,
            autoclose: true
        });

        // Grade → Section cascade
        $(document).on('change', '#sum_class_id', function () {
            var class_id = $(this).val();
            $('#sum_section_id').html('<option value="">-- Select Section --</option>');
            $('#sum_subject_id').html('<option value="">-- Select Subject --</option>');
            if (!class_id) return;

            $.ajax({
                type: "GET",
                url: base_url + "principal/sections/getByClass",
                data: { 'class_id': class_id },
                dataType: "json",
                success: function (data) {
                    var opts = '<option value="">-- Select Section --</option>';
                    $.each(data, function (i, obj) {
                        opts += '<option value="' + obj.section_id + '">' + obj.section + '</option>';
                    });
                    $('#sum_section_id').html(opts);
                }
            });
        });

        // Section → Subject cascade
        $(document).on('change', '#sum_section_id', function () {
            var class_id   = $('#sum_class_id').val();
            var section_id = $(this).val();
            $('#sum_subject_id').html('<option value="">-- Select Subject --</option>');
            if (!class_id || !section_id) return;

            $.ajax({
                type: "POST",
                url: base_url + "principal/subject/getSubjctByClassandSection",
                data: { 'class_id': class_id, 'section_id': section_id },
                dataType: "json",
                success: function (data) {
                    var opts = '<option value="">-- Select Subject --</option>';
                    $.each(data, function (i, obj) {
                        opts += '<option value="' + obj.id + '">' + obj.name + '</option>';
                    });
                    $('#sum_subject_id').html(opts);
                }
            });
        });

        // Form validation before submit
        $('#summaryForm').submit(function (e) {
            var cls  = $('#sum_class_id').val();
            var sec  = $('#sum_section_id').val();
            var subj = $('#sum_subject_id').val();
            var sd   = $('#sum_start_date').val();
            var ed   = $('#sum_end_date').val();

            if (!cls || !sec || !subj || !sd || !ed) {
                e.preventDefault();
                alert('Please fill in all fields: Grade, Section, Subject, Start Date, and End Date.');
                return false;
            }
        });

    });
</script>
