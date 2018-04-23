<?php
require('connect.php');
//current case report
$q = $_POST['current'];
//grab all case and patient info
$sqlCase = "SELECT * FROM cases NATURAL JOIN patient WHERE cid = '{$q}'";
//grab all diagnosis info
$sqlDiag = "SELECT * FROM patient NATURAL JOIN cases INNER JOIN diagnosis ON diagnosis.did = cases.did WHERE cases.cid = '{$q}'";
$sqlNurse = "SELECT fname, lname, licno FROM employee NATURAL JOIN cases where cid = '{$q}'";
$sqlDoctor = "SELECT * FROM employee NATURAL JOIN diagnosis INNER JOIN cases ON diagnosis.did = cases.did WHERE cases.cid = '{$q}'";

$resultCase = pg_query($db, $sqlCase);
$resultDiag = pg_query($db, $sqlDiag);
$resultNurse= pg_query($db, $sqlNurse);
$resultDoctor = pg_query($db, $sqlDoctor);

while ($row = pg_fetch_assoc($resultCase)) {
	$pid = $row['pid'];
	$fname = $row['fname'];
	$lname = $row['lname'];
	$mname = $row['mname'];
	$street = $row['street'];
	$city = $row['city'];
	$state = $row['state'];
	$zip = $row['zip'];
	$phone = $row['phone'];
	$dob = $row['dob'];
	$sex = $row['sex'];
	$instype = $row['instype'];
	$language = $row['language'];
	$bpsys = $row['bpsys'];
	$bpdia = $row['bpdia'];
	$hrate = $row['hrate'];
	$rrate = $row['rrate'];
	$vdate = $row['vdate'];
	$case = $row['cid'];
}
while ($row = pg_fetch_assoc($resultDiag)) {
	$diagnosis = $row['diagnosis'];
	$diagnosisid = $row['did'];
}
while ($row = pg_fetch_assoc($resultNurse)) {
	$nursefname = $row['fname'];
	$nurselname = $row['lname'];
	$nurselicno = $row['licno'];
}
while ($row = pg_fetch_assoc($resultDoctor)) {
	$doctorfname = $row['fname'];
	$doctorlname = $row['lname'];
	$doctorlicno = $row['licno'];
}

$sqlLab = "SELECT * FROM diagnosis FULL OUTER JOIN labs ON diagnosis.lid = labs.labid WHERE diagnosis.did = '{$diagnosisid}'";

$resultLab = pg_query($db, $sqlLab);

while ($row = pg_fetch_assoc($resultLab)) {
	$labbun = $row['bun'];
	$labcalcium = $row['calcium'];
	$labc02 = $row['c02'];
	$labchloride = $row['chloride'];
	$labcreatinine = $row['creatinine'];
	$labglucose = $row['glucose'];
	$labpotassium = $row['potassium'];
	$labsodium = $row['sodium'];
}

$sqlPre = "SELECT * FROM prescription INNER JOIN prescribes ON prescribes.prid = prescription.preid WHERE prescribes.did = '{$diagnosisid}'";

$resultPre = pg_query($db, $sqlPre);
$prescriptioncount = pg_num_rows($resultPre);

$premed = array();
$predosage = array();
$prefreq = array();
$presdate = array();
$preedate = array();

while ($row = pg_fetch_assoc($resultPre)) {
	$premed[] = $row['medication'];
	$predosage[] = $row['dosage'];
	$prefreq[] = $row['frequency'];
	$presdate[] = $row['sdate'];
	$preedate[] = $row['edate'];
}

// Include the main TCPDF library (search for installation path).
require_once('tcpdf/tcpdf.php');

// create new PDF document
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Kern Medical');
$pdf->SetTitle('Report');
$pdf->SetSubject('Patient Report');
$pdf->SetKeywords('patient, report, case, vitals, kernmed, hospital,');
// set default header data
$pdf->SetHeaderData("img/kernmed.jpg", 80, "Kern Medical Patient Report", "Report Generated ".date('m-d-Y H:i:s')."\nPatient: ".$fname." ".$lname."  |   Patient ID: ".$pid." ");

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// -------------------------------------------------------------------

// add a page
$pdf->AddPage();

$html = '<h2>Case:'.str_pad($case, 5, "0", STR_PAD_LEFT).'</h2><br>
	Visit Date: '.$vdate.'<br>
	Admitting Nurse: '.$nursefname.' '.$nurselname.'<br><br>
	Blood Pressure: '.$bpsys.'/'.$bpdia.'<br>
	Heart Rate: '.$hrate.' bpm<br>
	Respiratory Rate: '.$rrate.' bpm<br><br>

	<b>Lab Results</b><br>
	<table style="width: 50%; border:2px solid black";>
	<thead><tr>
		<th style="border-bottom:2px solid black";>Lab</th>
		<th style="border-bottom:2px solid black";><center>Value</center></th>
	</tr></thead>
	<tbody>
	<tr>
		<td style="border-right:2px solid black";>Blood Urea Nitrogen</td>
		<td>'.$labbun.'</td>
	</tr>
	<tr>
		<td style="border-right:2px solid black";>Calcium</td>
		<td>'.$labcalcium.'</td>
	</tr>
	<tr>
		<td style="border-right:2px solid black";>Carbon Dioxide</td>
		<td>'.$labc02.'</td>
	</tr>
	<tr>
		<td style="border-right:2px solid black";>Chloride</td>
		<td>'.$labchloride.'</td>
	</tr>
	<tr>
		<td style="border-right:2px solid black";>Creatinine</td>
		<td>'.$labcreatinine.'</td>
	</tr>
	<tr>
		<td style="border-right:2px solid black";>Glucose</td>
		<td>'.$labglucose.'</td>
	</tr>
	<tr>
		<td style="border-right:2px solid black";>Potassium</td>
		<td>'.$labpotassium.'</td>
	</tr>
	<tr>
		<td style="border-right:2px solid black";>Sodium</td>
		<td>'.$labsodium.'</td>
	</tr>
	</table><br><br>

	Assigned Doctor: '.$doctorfname.' '.$doctorlname.'<br>
	Diagnosis: '.$diagnosis.'<br><br>
	
	<b>Prescriptions</b><br>
	<table style="width: 100%; border:2px solid black";>
	<thead><tr>
		<th style="border-bottom:2px solid black";>Medication</th>
		<th style="border-bottom:2px solid black";>Dosage</th>
		<th style="border-bottom:2px solid black";>Frequency</th>
		<th style="border-bottom:2px solid black";>Start Date</th>
		<th style="border-bottom:2px solid black";>End Date</th>
	</tr></thead>
	<tbody>';

for ($i = 0; $i < $prescriptioncount; $i++) {
	$html .='
		<tr>
			<td style ="font-size:12px;">'.$premed[$i].'</td>
			<td style ="font-size:12px;">'.$predosage[$i].'</td>
			<td style ="font-size:12px;">'.$prefreq[$i].'</td>
			<td style ="font-size:12px;">'.$presdate[$i].'</td>
			<td style ="font-size:12px;">'.$preedate[$i].'</td>
		</tr>';
}

$html .='</table><br><br>';


$pdf->writeHTML($html, true, false, true, false, '');

//Close and output PDF document
$pdf->Output('patientreport.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
