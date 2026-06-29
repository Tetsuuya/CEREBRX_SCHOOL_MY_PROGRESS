<?php 
// $firstname = "ALLARD BENJAMIN CHARLES DAVID";
// $middlename = "M";
// $lastname = "ARMADA";

//pronouns
$pronoun_subject = ($gender == 'Male') ? 'himself' : 'herself';
$pronoun_possessive = ($gender == 'Male') ? 'his' : 'her';
$pronoun_she_he = ($gender == 'Male') ? 'he' : 'she';

if (in_array($strand_id, [1, 2])) {
    $strand_name = "ABM";  // Accountancy, Business, and Management
} elseif (in_array($strand_id, [3, 4])) {
    $strand_name = "HUMSS";  // Humanities and Social Sciences
} elseif (in_array($strand_id, [5, 6])) {
    $strand_name = "STEM";  // Science, Technology, Engineering, and Mathematics
} elseif (in_array($strand_id, [9, 10])) {
    $strand_name = "TVL - Automotive Servicing";
} elseif (in_array($strand_id, [11, 12])) {
    $strand_name = "TVL - Commercial Cooking";
} elseif (in_array($strand_id, [13, 14])) {
    $strand_name = "TVL - Computer Hardware Servicing";
} elseif (in_array($strand_id, [15, 16])) {
    $strand_name = "TVL - Computer Programming";
} elseif ($strand_id == 17) {
    $strand_name = "TVL - Housekeeping";
}

$explodeschool_year = explode('-', $school_year );
$getschool_year = $explodeschool_year[0].'-'.$school_year[0].$school_year[1].$explodeschool_year[1];
// printx($getschool_year);


$parts = explode('-', $school_year); // ['2025', '2026']

$start_year = intval($parts[0]) - 1; // 2024
$end_year = intval($parts[1]) - 1;   // 2025

$prevSchoolYear = "SY $start_year-$end_year";
// printx($prevSchoolYear);

// Extract the number
$gradeNumber = intval(str_replace('Grade ', '', $class));
// Subtract 1 for previous grade
$previousGrade = $gradeNumber - 1;
// Rebuild the class name
$previousClass = "Grade " . $previousGrade;

?>


<!DOCTYPE html>
<html>
	<head>
		<title>Request Form</title>
	</head>
	<body style= "text-align= center;">
		<div style =" border: 0px solid black; margin-left:23px; " >   
			<table width="100%"  style=" border: 0px solid black; width: 100%;">
				<tr>
					<td> 
						<!-- Caaaertificate Header -->
						<table width="100%" style="border: 0px solid black;" >
							<tr>
								<td width="20%" style="text-align: left;">
									<img src="././backend/images/school_logo.png" style="height: 110px; margin-left: 50px; margin-top: 10px;">
								</td>

								<!-- School Name and Slogan -->
								<td width="50%">
									<table width="100%" style="text-align=center; margin-top: 10px ">
											 <tr><td style="text-align: center; font-size: 22pt; font-family: impact;"> <strong>MINDANAO MISSION ACADEMY</strong> </td></tr> 
											 <tr><td style="text-align: center; font-size: 12pt; font-family: 'times new roman'; ">Of the Seventh Day Adventists, Inc.</td></tr>
											 <tr><td style="text-align: center; font-size: 12pt;font-family: mistral;">The School That Offers Something Better </td></tr> 
											 <tr><td style="text-align: center; font-size: 12t; font-family: 'times new roman'; ">P15 Poblacion, Manticao, Misamis Oriental</td></tr> 
											 <tr><td style="text-align: center; font-size: 10pt; font-family: times new roman; font-weight: bold;"> lis.mmaregistrar@gmail.com / 09192939551</td></tr>
									</table> 
								</td>
								
								<!-- <td style="width: 75%;"></td> Empty space on the left to push content to the right -->
								<td style="width: 20%; text-align: left; font-size:9pt; padding-right: 15px; padding-top: 0px; font-family: 'times new roman', Times, serif;">
									<!-- <p>Website: www.mma.edu.org</p>
									<p>Email: mma_1947@yahoo.com</p>
									<p>Accredited by:</p>
									<p>Adventist Accrediting Agency</p>
									<p>Member</p>
									<p>ACSCU</p> -->
								</td>
							</tr>
						</table> 

						<!-- random design -->
						<div>
							========================================================================================
						</div>

					</td>
			</table>
			
			<div style="margin-left: 20px; font-size: 11pt; font-family:centurygothic;">
				<!-- <p><?php echo date('F d, Y', strtotime($date_today)); ?></p>
				<br> -->
				The Principal/Registrar <br>
				<?php echo $recipient_school; ?><br>
				<?php echo $school_address; ?>
				<br> 
				<p><?php echo date('F d, Y', strtotime($date_today)); ?></p>
			</div>

			<!-- Request Content  for Junior High School-->			
			<?php if ($class_id <= 13 ): ?> 
			<div style="width: 100%; text-align: justify; font-size: 11pt; font-family:centurygothic;">
				<div style="margin-left: 20px;">
					<p>Please furnish us a true copy of <b>FORM 137 </b>of the student/s listed below:</p>
					<table  border="1" style="border-collapse: collapse; width: 100%;">
						<thead>
							<tr>
								<th style="width: 25%;">Name</th>
								<th style="width: 25%;">Classified in your school as</th>
								<th style="width: 25%;">Last school term in your school</th>
								<th style="width: 25%;">Classified in our school as</th>
								<th style="width: 25%;">Remarks</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="text-align:center;"><?php echo $firstname; ?> <?php echo substr($middlename, 0, 1); ?>. <?php echo $lastname; ?></td>
								<td style="text-align:center;"><?php echo $previousClass; ?></td>
								<td style="text-align:center;"><?php echo $prevSchoolYear;; ?></td>
								<td style="text-align:center;"><?php echo $class; ?></td>
								<td style="text-align:center;"><?php echo $remarks; ?></td>
							</tr>
						</tbody>
					</table>
					<p> Should there be any reason for the pending release of this document, please inform us. Thank you very much for your kind and prompt attention to this request. <br>
						<br>
						☐ 1<sup>st</sup> request &nbsp;
						☐ 2<sup>nd</sup> request &nbsp; 
						☐ Urgent request &nbsp; 
						☐ The bearer is authorized to hand carry &nbsp;
						<br><br>

						<!-- Note: Please entrust to the bearer on a sealed envelope. -->
					</p>
				</div>
			</div>

			<?php else : ?>
				<!-- Request Content for Senior High School-->			

				<div style="width: 100%; text-align: justify; font-size: 11.5pt; font-family:centurygothic;">
					<!-- Request Content -->
					<div style="margin-left: 20px;">
						<p>Please furnish us a true copy of <b>FORM 137 </b> and the Junior High School of the student/s listed below:</p>
						<table  border="1" style="border-collapse: collapse; width: 100%;">
							<thead>
								<tr>
									<th style="width: 25%;">Name</th>
									<th style="width: 25%;">Classified in your school as</th>
									<th style="width: 25%;">Last school term in your school</th>
									<th style="width: 25%;">Classified in our school as</th>
									<th style="width: 25%;">Remarks</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="text-align:center;"><?php echo $firstname; ?> <?php echo substr($middlename, 0, 1); ?>. <?php echo $lastname; ?></td>
									<td style="text-align:center;"><?php echo $previousClass; ?></td>
									<td style="text-align:center;"><?php echo $prevSchoolYear;; ?></td>
									<td style="text-align:center;"><?php echo $class; ?></td>
									<td style="text-align:center;"><?php echo $remarks; ?></td>
								</tr>
							</tbody>
						</table>
						<p> Should there be any reason for the pending release of this document, please inform us. Thank you very much for your kind and prompt attention to this request. <br>
							<br>
							☐ 1<sup>st</sup> request &nbsp;
							☐ 2<sup>nd</sup> request &nbsp; 
							☐ Urgent request &nbsp; 
							☐ The bearer is authorized to hand carry &nbsp;
							<br><br>

							<!-- Note: Please entrust to the bearer on a sealed envelope. -->
						</p>
					</div>
				</div>
				
			<?php endif; ?>


			<br>
			<br>
			<br>
			
			<?php if ($class_id <= 13 ): ?>
				<div style="margin-left:-20px; margin-top: -70px;">
					<img src="uploads/template_documents/certificate/abesta_sig.png">
				</div>

				<div style=" border: 0px solid black; text-align: center; text-decoration: none; font-size: 10pt; font-family: centurygothic; margin-left:-455px; margin-top:-50px width:10px;">
					<p>
						Frelyn C. Abesta, LPT
					</p>
				</div>
				<div  style="text-align: center; margin-top:-30px; margin-left:-450px;">
					<p>JHS Registrar</p>
				</div>

				
			<?php else : ?>
				<div style="margin-left:-15px; margin-top: -70px;">
					<img src="uploads/template_documents/certificate/dulana.png">
				</div>

				<div style=" border: 0px solid black; text-align: center; text-decoration: none; font-size: 10pt; font-family: centurygothic; margin-left: -425px; margin-top:-50px width:10px;">
					<p>
						Darlene C. Dulana, LPT, MAT
					</p>
				</div>
				<div  style="text-align: center; margin-top:-30px; margin-left:-450px;">
					<p>SHS Registrar</p>
				</div>
			<?php endif; ?>

		</div>
	</body>
</html>