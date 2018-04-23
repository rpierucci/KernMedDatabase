<?php
require('connect.php');
require('navdoctor.php');
$pid = $_GET['q'];
$sqlPatient = "SELECT * FROM patient WHERE pid = '{$pid}'";
$sqlCase = "SELECT * FROM cases INNER JOIN diagnosis ON diagnosis.did = cases.did WHERE pid = '{$pid}' ORDER BY vdate DESC";
$resultpatient = pg_query($db, $sqlPatient);
$resultcase = pg_query($db, $sqlCase);
if (!$resultpatient) {
    echo "errorpatient\n";
    exit;
}
if (!$resultcase) {
    echo "errorcase\n";
    exit;
}
?>
<div class="container">
<?php
while ($row = pg_fetch_assoc($resultpatient)) {
?>
    			<h1>Patient Information</h1>
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
<?php
}
?>	
					</div>
				</div>
				<div class="col-md-8">
					<div class="visits">
						<hl><b>Cases</b></hl><hr>
						 <table style="width:100%">
						<tablehead><tr>
							<th></th>
							<th><hl>Date</hl></th>
							<th><hl>Blood Pressure</hl></th>
							<th><hl>Diagnosis</hl></th>
						</tr></tablehead>
<?php
while ($row = pg_fetch_assoc($resultcase)) {
    echo "<tr onclick  =\"window.location='casedr.php?q=".$row['cid']."';\">";
    echo "<td><hl><b>+</b></hl></td>";
    echo "<td>".$row['vdate']."</td>";
    echo "<td>".$row['bpsys']."/".$row['bpdia']."</td>";
    echo "<td>".$row['diagnosis']."</td>";
    echo "</tr>";
}
?>
						</table> 
					</div>
				</div>

			</div><br><br>
			</div>
			<br><br><br>
	</body>
