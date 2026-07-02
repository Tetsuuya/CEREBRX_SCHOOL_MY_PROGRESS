<!DOCTYPE html>
<html>
<head>
	<title></title> 
</head>
<body>
	<table width="100%" style="border-collapse: collapse; font-family: sans-serif;">
		<tr>
			<td width="15%" style="vertical-align: top; text-align: left;">
				<img src="<?php echo base_url();?>uploads/school_content/logo/cocs.png" style="height: 100px;">
			</td> 
			<td width="70%" style="text-align: center; vertical-align: top;">
				<div style="color: #008000; font-weight: bold; font-size: 20px; text-transform: uppercase;">CAGAYAN DE ORO CHRISTIAN SCHOOL</div>
				<div style="font-size: 11px; margin-top: 2px;">F. Abellanosa St., Cagayan de Oro City</div>
				<div style="color: #FF0000; font-weight: bold; font-size: 24px; margin-top: 8px; margin-bottom: 5px; letter-spacing: 1px;">MASTER SHEET</div>
				<div style="color: #008000; font-weight: bold; font-size: 16px; margin-bottom: 5px; text-transform: uppercase;"><?php echo $class_name." ".$section_name; ?></div>
				<?php 
				if ($quarter == 'final' || $quarter == 'Final') {
					$term_label = "FINAL GRADE";
				} else {
					$term_label = "TERM " . $quarter;
				}
				?>
				<div style="color: #0000FF; font-weight: bold; font-size: 14px; text-transform: uppercase;">
					<?php echo $term_label; ?> for <?php echo str_replace('S. Y.', 'School Year', $school_year); ?>
				</div>
			</td> 
			<td width="15%" style="vertical-align: top; text-align: right;">
				<img src="<?php echo base_url();?>uploads/school_content/logo/UCCP.png" style="height: 100px;">
			</td> 
		</tr>
	</table>

	<?php
	if(isset($content) && $content ){
		echo $content;
	}
	?>
	 
</body>
</html>