<!DOCTYPE html>
<html>
<head>
	<title>Grading Sheet</title>
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
		body {
			font-family: 'Inter', sans-serif;
			color: #000000;
			margin: 0;
			padding: 0;
		}
		.header-table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 20px;
		}
		.logo-cell {
			width: 35%;
			vertical-align: top;
			text-align: left;
		}
		.info-cell {
			width: 65%;
			vertical-align: top;
			text-align: right;
		}
		.uccp-title {
			font-size: 10px;
			font-weight: 700;
			color: #000000;
			text-transform: uppercase;
			margin: 0;
			padding: 0;
		}
		.school-name {
			font-size: 17px;
			font-weight: 700;
			color: darkgreen;
			margin: 2px 0 0 0;
			padding: 0;
		}
		.school-address {
			font-size: 10px;
			font-weight: 600;
			color: darkgreen;
			margin: 2px 0 0 0;
			padding: 0;
		}
		.doc-title {
			font-size: 16px;
			font-weight: 700;
			color: red;
			margin: 6px 0 0 0;
			padding: 0;
			text-transform: uppercase;
			letter-spacing: 1px;
		}
		.subject-title {
			font-size: 13px;
			font-weight: 700;
			color: blue;
			margin: 3px 0 0 0;
			padding: 0;
			text-transform: uppercase;
		}
		.class-title {
			font-size: 11px;
			font-weight: 600;
			color: darkgreen;
			margin: 2px 0 0 0;
			padding: 0;
			text-transform: uppercase;
		}
		.sy-title {
			font-size: 10px;
			font-weight: 600;
			color: #000000;
			margin: 2px 0 0 0;
			padding: 0;
		}
	</style>
</head>
<body>
	<table class="header-table">
		<tr>
			<td class="logo-cell">
				<img src="<?php echo base_url();?>uploads/school_content/logo/report-gradesheet/COCS.png" style="height: 75px;">
			</td>
			<td class="info-cell">
				<div class="uccp-title">United Church of Christ in the Philippines</div>
				<div class="school-name">CAGAYAN DE ORO CHRISTIAN SCHOOL</div>
				<div class="school-address">F. Abellanosa St., Cagayan de Oro City</div>
				<div class="doc-title">Grading Sheet</div>
				<div class="subject-title"><?php echo $subject_name; ?></div>
				<div class="class-title"><?php echo $class_name." - ".$section_name; ?></div>
				<div class="sy-title"><?php echo str_replace("S. Y.", "School Year:", $school_year); ?></div>
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