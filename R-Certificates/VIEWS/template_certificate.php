<!DOCTYPE html>
<html>
	<head>
	</head>
	<body align="center">
		<div style="width:800px; height:600px; padding:20px; text-align:center; border: 10px solid #787878">
			<div style="width:750px; height:550px; padding:20px; text-align:center; border: 5px solid #787878">
				<table width="100%">
					<tr>
						<td width="50%" align="left"><span><img src="<?php echo $image_src;?>" alt="School Logo" style="text-align: left;"/></span></td>
						<td width="50%" align="right"><span><img src="<?php echo $image_seal;?>" alt="School Seal" style="text-align: right;"/></span></td> 
					</tr> 
				</table>
			   <span style="font-size:50px; font-weight:bold">Certificate and Awards</span>
			   <br><br>
				<span style="font-size:30px"><b  style="text-decoration: underline;text-align: center"><?php echo $award_name;?></b></span><br/><br/>
			   <br><br>
			   <span style="font-size:25px"><i>is given to</i></span>
			   <br><br>
			   <span style="font-size:30px"><b><?php echo $student_name;?></b></span><br/><br/>
			   <span style="font-size:25px"><i>For his achievement as one of the selected few who excel in this category. We therefore congratulate you and may prosper more.</i></span> <br/>
			   <span style="font-size:25px"><i>Given this <span style="text-decoration: underline;"><?php echo $date; ?></span> at <span style="text-decoration: underline;"><?php echo $school_name; ?>.</i></span> <br/>
			   <br />
				<table width="100%">
					<tr>
						<td width="50%"style="text-align: left;"><span style="text-decoration: underline;"><?php echo $teacher; ?></span></td>
						<td width="50%" style=" text-align: right;"><span style="text-decoration: underline;"><?php echo $principal; ?></span></td> 
					</tr>
					 <tr>
						<td width="50%"style="text-align: left;">Teacher</td>
						<td width="50%" style=" text-align: right;"> Principal</td> 
					</tr> 
				</table>
				 
			</div>
		</div> 
	</body>
</html> 