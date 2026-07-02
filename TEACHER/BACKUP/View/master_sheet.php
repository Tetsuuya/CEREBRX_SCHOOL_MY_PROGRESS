<!DOCTYPE html>
<html>
<head>
	<title></title> 
</head>
<body>
	<table width="100%">
		<tr>
			<!--<td width="15%" style="vertical-align: top"><img src="././backend/images/coass_logo.png" style="height: 120px;"> </td>-->
			<td width="15%" style="vertical-align: top"><img src="<?php echo base_url();?>uploads/school_content/logo/<?php echo $logo;?>" style="height: 120px;"></td> 
			<td width="70%" style="text-align: center;">
				<table  align="center">
					<tr> 
						<td>
							<h2><?php echo $school_name; ?></h2> 
							<?php echo $city.', '.$province; ?><br /> <br /> <br /> 
							<h3>Master Sheet</h3> 
						</td>
					</tr>
				</table> 
			</td> 
			<td width="15%" style="vertical-align: top"></td> 
		</tr>
	</table>
	<table width="100%">
		<tr>
			 <td align="left"><u><?php echo $quarter_display; ?> Grading</u></td>
			 <td align="right"><u><?php echo $class_name." - ".$section_name; ?></u></td>
		</tr>
		<tr>
			 <td align="left"><u><?php echo $teacher_name; ?></u></td>
			 <td align="right"><u><?php echo $school_year; ?></u></td>
		</tr>
	</table>
	<?php
	if(isset($content) && $content ){
		echo $content;
	}
	?>
	 
</body>
</html>