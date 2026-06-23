<footer class="main-footer">
    &copy;  <?php echo date('Y'); ?> 
    <?php echo $this->customlib->getAppName(); ?> <?php echo $this->customlib->getAppVersion(); ?>
</footer>
<div class="control-sidebar-bg"></div>
</div>
<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>

<link href="<?php echo base_url(); ?>backend/toast-alert/toastr.css" rel="stylesheet"/>
<script src="<?php echo base_url(); ?>backend/toast-alert/toastr.js"></script>


<script src="<?php echo base_url(); ?>backend/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>backend/dist/js/raphael-min.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/morris/morris.min.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/sparkline/jquery.sparkline.min.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/knob/jquery.knob.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/daterangepicker/daterangepicker.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/fastclick/fastclick.min.js"></script>
<script src="<?php echo base_url(); ?>backend/dist/js/app.min.js"></script>
<script src="<?php echo base_url(); ?>backend/dist/js/demo.js"></script>
<script src="<?php echo base_url(); ?>backend/sweet-alert/sweetalert2.min.js"></script>
</body>
</html>



<script src="<?php echo base_url(); ?>backend/js/school-custom.js"></script>




<script type="text/javascript">
	$(document).ready(function() {
		<?php

		
		if($this->session->flashdata('success_msg')){
			?>
			successMsg("<?php echo $this->session->flashdata('success_msg'); ?>");
			<?php
		}else if($this->session->flashdata('error_msg')){
			?>
			errorMsg("<?php echo $this->session->flashdata('error_msg'); ?>");
			<?php
		}else if($this->session->flashdata('warning_msg')){
			?>
			infoMsg("<?php echo $this->session->flashdata('warning_msg'); ?>");
			<?php
		}else if($this->session->flashdata('info_msg')){
			?>
			warningMsg("<?php echo $this->session->flashdata('info_msg'); ?>");
			<?php
		}
		?> 
	});
</script>