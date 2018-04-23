<?php
require('connect.php');
require('navnurse.php');
$eid = 8;
$status = 0;
$sqlNurse = "SELECT eid, fname, lname, licno, lictype FROM employee WHERE eid = '{$eid}'";
$sqlDiagStatus = "SELECT cid, pid, lname, dob FROM patient NATURAL JOIN cases INNER JOIN diagnosis ON diagnosis.did = cases.did WHERE diagnosis.status = '{$status}'";
$sqlRecentCases = "SELECT cid, vdate, lname, ssn FROM patient NATURAL JOIN cases INNER JOIN diagnosis ON diagnosis.did = cases.did WHERE diagnosis.status != 0 AND cases.eid = '{$eid}' ORDER BY vdate DESC";
$sqlRecentCasesAll = "SELECT cid, vdate, patient.lname, employee.lname elname FROM patient NATURAL JOIN cases INNER JOIN diagnosis NATURAL JOIN employee ON diagnosis.did = cases.did WHERE diagnosis.status != 0 ORDER BY vdate DESC";
$resultNurse = pg_query($db, $sqlNurse);
$resultDiagStatus = pg_query($db, $sqlDiagStatus);
$resultRecentCases = pg_query($db, $sqlRecentCases);
$resultRecentCasesAll = pg_query($db, $sqlRecentCasesAll);
if (!$resultNurse) {
    echo "errornurse\n";
    exit;
}
if (!$resultDiagStatus) {
	echo "errordiag\n";
	exit;
}
if (!$resultRecentCases) {
	echo "errorcases\n";
	exit;
}
if (!$resultRecentCasesAll) {
	echo "errorcasesall\n";
	exit;
}
?>

<div class="container">
<?php
while ($row = pg_fetch_assoc($resultNurse)) {
?>
	<h1>Welcome <?php echo $row['fname']; $nursefname = $row['fname']; echo " ".$row['lname'];?></h1><hl><b><h5>License Number: </b></hl><?php echo $row['licno']; ?> <hl><b>License Type:</hl></b> <?php echo $row['lictype']; }?><br><br>
			<div class="row">
				<div class="col-md-4">
					<div class="nursepatientlist">
					<hl><b>Patients Waiting</b></hl><hr>
					<table style="width: 100%;">
					<thead><tr>
						<th></th>
						<th><hl>Last Name</hl></th>
						<th><hl>Date of Birth</hl></th>
					</tr></thead>
					<tbody>
					<?php
					while ($row = pg_fetch_assoc($resultDiagStatus)) {
						echo "<tr onclick =\"window.location='newcase.php?q=".$row['cid']."';\">";
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
					<hl><b><?php echo $nursefname; ?>'s Recent Cases</b></hl><hr>
					<table style="width: 100%;">
					<thead>
						<th></th>
						<th><hl>Visit Date</hl></th>
						<th><hl>Last Name</hl></th>
						<th><hl>Case ID</hl></th>
					</thead>
					<tbody>
					<?php
					while ($row = pg_fetch_assoc($resultRecentCases)) {
						echo "<tr onclick =\"window.location='case.php?q=".$row['cid']."';\">";
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
					<hl><b>All Recent Cases</b></hl><hr>
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
					while ($row = pg_fetch_assoc($resultRecentCasesAll)) {
						echo "<tr onclick =\"window.location='case.php?q=".$row['cid']."';\">";
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
			<br>
			<hl><b><a href="newpatient.php">New Patient</a></b></hl>
	</div>
	</body>
