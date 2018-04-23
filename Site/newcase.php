<?php 
require('connect.php');
require('navnurse.php');

//nurse thats viewing
$eid = 8;

//get list of current doctors
$sqlDrList = "SELECT eid, fname, lname FROM employee WHERE lictype = 'MD' AND edate IS NULL ORDER BY lname ASC";
$resultDrList = pg_query($db, $sqlDrList);
if (!$resultDrList) {
	echo "drlist error\n";
	exit;
}

if ((!empty($_POST['case'])) && (!empty($_POST['date'])) && (!empty($_POST['bpsys'])) && (!empty($_POST['bpdia'])) && (!empty($_POST['rrate'])) && (!empty($_POST['hrate'])) && (!empty($_POST['drlist']))) {
	$sqlCaseSubmit = "UPDATE cases
	SET vdate = '{$_POST['date']}'::date, bpsys = {$_POST['bpsys']}, bpdia = {$_POST['bpdia']}, rrate = {$_POST['rrate']}, hrate = {$_POST['hrate']}, eid = {$eid}
		WHERE cid = {$_POST['case']}";
	if ($result1 = pg_query($db, $sqlCaseSubmit)) {
		echo "\n\n\n\n\nsuccess\n";
		echo $result;
	}
	$sqlUpdateDr = "UPDATE diagnosis
		SET eid = {$_POST['drlist']}, status = 1
		FROM cases c
		WHERE c.cid = {$_POST['case']} AND c.did = diagnosis.did";
	if ($result2 = pg_query($db, $sqlUpdateDr)) {
		echo "success2\n";
		header("Location: nurse.php");
		exit();
	}
}

//check for case number
if (isset($_GET['q']) && $_GET['q'] != '') {
	//sanitize here
	$q = ($_GET['q']);
	$sqlCase = "SELECT * FROM cases NATURAL JOIN patient WHERE cid = {$q}";
	if ($result = pg_query($db, $sqlCase)) {
?>
<div class="container">
<?php
		while ($row = pg_fetch_assoc($result)) {
			$pid = $row['pid'];
			$sqlPast = "SELECT * FROM cases WHERE pid = {$pid} ORDER BY vdate DESC";
?>
	<h1>Create Case</h1>	
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
					<hl><b>Patient's History Cases</b></hl><hr>
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
				<div class="casecreate">
					<hl><b>Create Case</b></hl><hr><br>
					<div class="col-md-3">
						<form id ="casesubmit" method="POST" name="casesubmit">
							<hl>Case ID:</hl><br> 
							<hl>Date of Visit:</hl><br>
							<hl>Blood Pressure:</hl><br><br>
							<hl>Heart Rate:</hl><br>
							<hl>Respiratory Rate:</hl><br>
					</div>	
					<div class="col-md-4">
							<input type="text" id='case' name='case' value="<?php echo str_pad($row['cid'], 5, "0", STR_PAD_LEFT); ?>" readonly/><br>
							<input type="date" id='date' name='date' value="<?php echo date('Y-m-d'); ?>" /><br>
							<input type="number" placeholder="Systolic" id='bpsys' name='bpsys' min="0" step="1"/><input type ="number" placeholder="Diastolic" id='bpdia' name='bpdia' min="0" step="1"/><br>
							<input type ="number" placeholder="Beats Per Minute" id='hrate' name='hrate' min="0" step="1"/><br>
							<input type="number" placeholder="Breaths Per Minute" id='rrate' name='rrate' min="0" step="1"/><br>
					</div>
<?php 
		}
?>
					<div class="col-md-5">
						<hl>Assign Doctor to Patient:</hl>
						<form name ="doctorlist">
							<select name="drlist" id="drlist" size=20>
<?php
							while ($row = pg_fetch_assoc($resultDrList)) {			
								echo "<option id=".$row['eid']." value=".$row['eid'].">".$row['fname']." ".$row['lname']."</option>";	
							}
?>
							</select>
					<br><br>
					<button type="submit">Submit Case</button>
					</form>
					</div>
				</div>
			</div>
		</div>
	</div>



<?php
		
		
				
	}
}
?>
