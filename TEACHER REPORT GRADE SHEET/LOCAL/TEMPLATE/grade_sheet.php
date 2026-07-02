<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<table width="100%">
		<tr>
			<td width="15%" style="vertical-align: top"><img src="<?php echo base_url();?>uploads/school_content/logo/<?php echo $logo;?>" style="height: 120px;"></td>
			<td width="70%" style="text-align: center;">
				<table  align="center">
					<tr> 
						<td>
							<h1 style="color:#559830;"><?php echo $school_name; ?></h1> 
							<p style="font-size:18px;margin:10px;"><?php echo $city.' '.$province; ?></p>
							<h2 style="font-weight:30px;font-family: Allerta, sans-serif;margin:5px;color:#298390;"><?php echo $class_name." - ".$section_name; ?></h2>
						 	<h2 style="font-weight:30px;margin:5px;color:red;text-transform:uppercase;"><em><?php echo $teacher_name_upper; ?></em></h2>
						 	<h2 style="font-weight:30px;font-family: Allerta, sans-serif;margin:5px;color:#298390;"><?php echo $school_year; ?></h2>
						</td>
					</tr>
				</table>

			
			</td>
			  <td width="15%" style="vertical-align: top"></td>
			
			
		</tr>
	</table>
	<br />
 	<table width="100%" style="margin-bottom:10px;">
		<tr>
			 <td  width="50%" align="left">Subject:<span style="border-bottom: 1px solid black;">&nbsp;<?php echo $subject_name; ?></span></td>
			 <td  width="50%" align="right">Teacher:<span style=" border-bottom: 1px solid black;text-transform:uppercase;font-size:14px;">&nbsp;<?php echo $teacher_name; ?></span></td>
		</tr>
		 
	</table>
	<?php
	if(isset($content) && $content ){
		echo $content;
	}
	?>
</body>
</html>