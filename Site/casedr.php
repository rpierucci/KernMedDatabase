<?php 
require('connect.php');
require('navdoctor.php');

//nurse thats viewing
$eid = 11;
$pid;

//check for case number
if (isset($_GET['q']) && $_GET['q'] != '') {
	//sanitize here
	$q = ($_GET['q']);
	$sqlDiag = "SELECT * FROM patient NATURAL JOIN cases INNER JOIN diagnosis ON diagnosis.did = cases.did WHERE cases.cid = '{$q}'";
	$sqlNurse = "SELECT fname, lname, licno FROM employee NATURAL JOIN cases WHERE cid = '{$q}'";
	$sqlDoctor = "SELECT * FROM employee NATURAL JOIN diagnosis INNER JOIN cases ON diagnosis.did = cases.did WHERE cases.cid = '{$q}'";

	$resultDiag = pg_query($db, $sqlDiag);
	$resultNurse = pg_query($db, $sqlNurse);
	$resultDoctor = pg_query($db, $sqlDoctor);

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

	
	$sqlCase = "SELECT * FROM cases NATURAL JOIN patient WHERE cid = {$q}";
	if ($result = pg_query($db, $sqlCase)) {
?>
<div class="container">
<?php
		while ($row = pg_fetch_assoc($result)) {
			$pid = $row['pid'];
			$sqlPast = "SELECT * FROM cases WHERE pid = {$pid} ORDER BY vdate DESC";
?>
	<h1>Case ID: <?php echo str_pad($row['cid'], 5, "0", STR_PAD_LEFT); ?></h1>	
		<div class="row">
			<div class="col-md-4">
				<div class="bio">
				<hl><b>Patient Information</b></hl><hr>
				<div class="col-md-5">
					<hl><b>+</b> Name:</hl> <br>
					<hl><b>+</b> Address:</hl><br><br>
					<hl><b>+</b> Phone:</hl><br>
					<hl><b>+</b> Date of Birth:</hl><br>
					<hl><b>+</b> Sex:</hl><br>
					<hl><b>+</b> Insurance:</hl><br>
					<hl><b>+</b> Langauge:</hl><br><br>
				</div>
				<div class="col-md-7">
					<?php echo $row['fname']." "; echo $row['mname']." "; echo $row['lname']." ";?><br>
					<?php echo $row['street']; ?><br>
					<?php echo $row['city']." "; echo $row['state']." "; echo $row['zip']." "; ?><br>
					<?php echo $row['phone']; ?><br>
					<?php echo $row['dob']; ?><br>
					<?php echo $row['sex']; ?><br>
					<?php echo $row['instype']; ?><br>
					<?php echo $row['language']; ?><br><br>
				</div>
				</div>
				<br><br>
				<div class="past">
					<hl><b>Patient's History</b></hl><hr>
					<table style="width: 100%;">
					<thead><tr>
						<th></th>
						<th><hl>Date</hl></th>
						<th><hl>Case ID</hl></th>
					</tr></thead>
					<tbody>
<?php
			if ($result2 = pg_query($db, $sqlPast)) {
				while ($row2 = pg_fetch_assoc($result2)) {
					if ($row2['cid'] != $row['cid']) {
						echo "<tr onclick =\"window.open('case.php?q=".$row2['cid']."', '_blank');\">";
						echo "<td><hl><b>+</b></hl></td>";
						echo "<td>".$row2['vdate']."</td>";
						echo "<td>".str_pad($row2['cid'], 5, "0", STR_PAD_LEFT)."</td>";
						echo "</tr>";
					}
				}
			}
?>
					</table>
				</div>
			</div>
			<div class="col-md-8">
				<div class="caseoverview">
					<hl><b>Case Overview</b></hl><hr>
					<div class="row">				
					<div class="col-md-4">
						<div class="coadmission">
							<hl><b>Admission</b></hl><hr>
							<hl>Nurse:</hl> <?php echo $nursefname." ".$nurselname;?><br>
							<hl>Date of Visit:</hl> <?php echo $row['vdate']; ?><br>
							<hl>Blood Pressure:</hl> <sup><?php echo $row['bpsys']; ?></sup>&frasl;<sub><?php echo $row['bpdia']; ?></sub><br>
							<hl>Heart Rate:</hl> <?php echo $row['hrate']; ?> bpm<br>
							<hl>Respiratory Rate:</hl> <?php echo $row['rrate']; ?> <br>
						</div>
					</div>
					<div class="col-md-4">
						<div class="coadmission">
							<hl><b>Diagnosis</b></hl><hr>
							<hl>Doctor:</hl> <?php echo $doctorfname." ".$doctorlname; ?><br>
							<hl>Diagnosis:</hl><br>
							&emsp; <?php echo $diagnosis; ?>
						</div>
					</div>
					<div class="col-md-4">
						<div class="coadmission">
							<hl><b>Reports & Charts</b></hl><hr>
							<center>
								<form id="reports" action="reportsingle.php" method="POST" target="_blank" name="reportform">
									<button type="Submit" form="reports" name="current"  value='<?php echo $q; ?>'> Current Case Report</button><br></form>
								<form id="reporta" action="reportall.php" method="POST" target="_blank" name="reportaform">
									<button type="Submit" form="reporta" name="all"  value='<?php echo $pid; ?>'>All Case Report</button></form><br>
						<button onclick="window.open('charts.php?q=<?php echo $q;?>', '_blank')">Vital Charts</button>
								</form>
							</center>
						</div>
					</div>
					</div>
					<br>	
					<div class="row">
					<div class="col-md-12">
						<div class="coadmission">
							<hl><b>Lab Results</b></hl><hr>
							<div class="col-md-5">							
								<hl>Blood Urea Nitrogen:</hl><br>
								<hl>Calcium:</hl><br>
								<hl>Carbon Dioxide:</hl><br>
								<hl>Chloride:</hl><br>
							</div>
							<div class="col-md-1">
								<?php echo $labbun; ?><br>
								<?php echo $labcalcium; ?><br>
								<?php echo $labc02; ?><br>
								<?php echo $labchloride; ?><br>	
							</div>
							<div class="col-md-5">
								<hl>Creatinine:</hl><br>
								<hl>Glucose:</hl><br>
								<hl>Potassium:</hl><br>
								<hl>Sodium:</hl><br>
							</div>
							<div class="col-md-1">
								<?php echo $labcreatinine; ?><br>
								<?php echo $labglucose; ?><br>
								<?php echo $labpotassium; ?><br>
								<?php echo $labsodium; ?><br>
							</div>
						</div>
					</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-12">
							<div class="coprecase">
								<hl><b>Prescriptions</b></hl><hr>
								<table style="width: 100%;">
								<thead><tr>
								<th><hl>Medication</hl></th>
								<th><hl>Dosage</hl></th>
								<th><hl>Frequency</hl></th>
								<th><hl>Start Date</hl></th>
								<th><hl>End Date</hl></th>
								</tr></thead>
								<tbody>
<?php
			for ($i = 0; $i < $prescriptioncount; $i++) {
				echo "<tr>";
				echo "<td>".$premed[$i]."</td>";
				echo "<td>".$predosage[$i]."</td>";
				echo "<td>".$prefreq[$i]."</td>";
				echo "<td>".$presdate[$i]."</td>";
				echo "<td>".$preedate[$i]."</td>";
				echo "</tr>";
			}
?>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>



<?php


		}		
	}
}
?>
