<?php
require('connect.php');
require('navdoctor.php');
//valid eid 11 10 9
$eid = 11;
$status = 1;
$sqlDoctor = "SELECT eid, fname, lname, licno, lictype FROM employee WHERE eid = '{$eid}'";
$sqlDiagStatus = "SELECT cid, pid, lname, dob FROM patient NATURAL JOIN cases INNER JOIN diagnosis ON diagnosis.did = cases.did WHERE diagnosis.status = '{$status}' AND diagnosis.eid = '{$eid}'";
$sqlRecentDiag = "SELECT cid, vdate, lname, ssn FROM patient NATURAL JOIN cases INNER JOIN diagnosis ON diagnosis.did = cases.did WHERE diagnosis.status = 2 AND diagnosis.eid = '{$eid}' ORDER BY vdate DESC";
$sqlRecentDiagAll = "SELECT cid, vdate, patient.lname, employee.lname elname FROM patient NATURAL JOIN cases INNER JOIN diagnosis NATURAL JOIN employee ON diagnosis.did = cases.did WHERE diagnosis.status = 2 ORDER BY vdate DESC";
$resultDoctor = pg_query($db, $sqlDoctor);
$resultDiagStatus = pg_query($db, $sqlDiagStatus);
$resultRecentDiag = pg_query($db, $sqlRecentDiag);
$resultRecentDiagAll = pg_query($db, $sqlRecentDiagAll);
if (!$resultDoctor) {
    echo "errordoctor\n";
    exit;
}
if (!$resultDiagStatus) {
	echo "errordiag\n";
	exit;
}
if (!$resultRecentDiag) {
	echo "errorcases\n";
	exit;
}
if (!$resultRecentDiagAll) {
	echo "errorcasesall\n";
	exit;
}
?>

<div class="container">
<?php
while ($row = pg_fetch_assoc($resultDoctor)) {
?>
	<h1>Welcome <?php echo $row['fname']; $nursefname = $row['fname']; echo " ".$row['lname'];?></h1><hl><b><h5>License Number: </b></hl><?php echo $row['licno']; ?> <hl><b>License Type:</hl></b> <?php echo $row['lictype']; }?><br><br>
			<div class="row">
				<div class="col-md-4">
					<div class="nursepatientlist">
					<hl><b>Patients Awaiting Diagnosis</b></hl><hr>
					<table style="width: 100%;">
					<thead><tr>
						<th></th>
						<th><hl>Last Name</hl></th>
						<th><hl>Date of Birth</hl></th>
					</tr></thead>
					<tbody>
					<?php
					while ($row = pg_fetch_assoc($resultDiagStatus)) {
						echo "<tr onclick =\"window.location='newdiag.php?q=".$row['cid']."';\">";
						echo "<td><hl><b>+</b></hl></td>";
						echo "<td>".$row['lname']."</td>";
						echo "<td>".$row['dob']."</td>";
						echo "</tr>";
					}
?>
					<tbody>
					</table>
					</div>	
				</div>
				<div class="col-md-8">
					<div class="nursecaselist">
					<hl><b><?php echo $nursefname; ?>'s Recent Diagnoses</b></hl><hr>
					<table style="width: 100%;">
					<thead>
						<th></th>
						<th><hl>Visit Date</hl></th>
						<th><hl>Last Name</hl></th>
						<th><hl>Case ID</hl></th>
					</thead>
					<tbody>
					<?php
					while ($row = pg_fetch_assoc($resultRecentDiag)) {
						echo "<tr onclick =\"window.location='casedr.php?q=".$row['cid']."';\">";
						echo "<td><hl><b>+</b></hl></td>";
						echo "<td>".$row['vdate']."</td>";
						echo "<td>".$row['lname']."</td>";
						echo "<td>".str_pad($row['cid'], 5, "0", STR_PAD_LEFT)."</td>";
						echo "</tr>";
					}
?>
					<tbody>
					</table>
					</div>	
				</div>
				<br><br><br><br>
				<br><br><br><br>
				<br><br><br><br>
				<br><br><br><br>
				<div class="col-md-8">
					<div class="nursecaselist">
					<hl><b>All Recent Diagnoses</b></hl><hr>
					<table style="width: 100%;">
					<thead><tr>
						<th></th>
						<th><hl>Visit Date</hl></th>
						<th><hl>Last Name</hl></th>
						<th><hl>Doctor</hl></th>
					</tr></thead>
					<tbody>
<?php 
					$allcount = 0;
					while ($row = pg_fetch_assoc($resultRecentDiagAll)) {
						echo "<tr onclick =\"window.location='casedr.php?q=".$row['cid']."';\">";
						echo "<td><hl><b>+</b></hl></td>";
						echo "<td>".$row['vdate']."</td>";
						echo "<td>".$row['lname']."</td>";
						echo "<td>".$row['elname']."</td>";
						echo "</tr>";
						$allcount = $allcount + 1;
						if ($allcount == 10) {
							break;
						}
					}
?>
					<tbody>
					</table>
					</div>	
				</div>
			</div>
	</div>
	</body>
