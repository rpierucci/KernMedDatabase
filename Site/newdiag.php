<?php 
require('connect.php');
require('navdoctor.php');

//doctor thats viewing
$eid = 11;
$cid = $_GET['q'];
$sqlDoctor = "SELECT fname, lname, licno FROM employee WHERE eid = '{$eid}'";
$resultDoctor = pg_query($db, $sqlDoctor);

//grab a bunch of variables to make my life easier
$sqlDid = "SELECT * FROM patient NATURAL JOIN cases INNER JOIN diagnosis ON diagnosis.did = cases.did WHERE cases.cid = '{$cid}'";
$resultDid = pg_query($db, $sqlDid);
while ($row = pg_fetch_assoc($resultDid)) {
	$did = $row['did'];
}

$sqlLid = "SELECT * FROM diagnosis FULL OUTER JOIN labs ON diagnosis.lid = labs.labid WHERE diagnosis.did = '{$did}'";
$resultLid = pg_query($db, $sqlLid);
while ($row = pg_fetch_assoc($resultLid)) {
	$lid = $row['labid'];
}

while ($row = pg_fetch_assoc($resultDoctor)) {
	$dfname = $row['fname'];
	$dlname = $row['lname'];
	$dlicno = $row['licno'];
}

//var_dump($_POST);

if ((!empty($_POST['diag'])) && (!empty($_POST['bun'])) && (!empty($_POST['calcium'])) && (!empty($_POST['c02'])) && (!empty($_POST['chloride'])) && (!empty($_POST['creatinine'])) && (!empty($_POST['glucose'])) && (!empty($_POST['potassium'])) && (!empty($_POST['sodium']))) {
	$sqlDiag = "UPDATE diagnosis
		SET diagnosis = '{$_POST['diag']}', status = 2
		WHERE did = '{$did}'";
	if ($result = pg_query($db, $sqlDiag)) {
	//success
	}
	
	$sqlLabs = "UPDATE labs
		SET bun = {$_POST['bun']}, calcium = {$_POST['calcium']}, c02 = {$_POST['c02']}, chloride = {$_POST['chloride']}, creatinine = {$_POST['creatinine']}, glucose = {$_POST['glucose']}, potassium = {$_POST['potassium']}, sodium = {$_POST['sodium']}
 		WHERE labid = '{$lid}'";
	if ($result = pg_query($db, $sqlLabs)) {
	//success
	}
	
	if ((!empty($_POST['pmed1'])) && (!empty($_POST['pdos1'])) && (!empty($_POST['pfre1'])) && (!empty($_POST['psdt1'])) && (!empty($_POST['pedt1']))) {
		echo "FILLED IN MED\n";
		$sqlPre = "INSERT INTO prescription (medication, dosage, frequency)
			VALUES ('{$_POST['pmed1']}', '{$_POST['pdos1']}', '{$_POST['pfre1']}') RETURNING preid";
		$sqlPreCon = "INSERT INTO prescribes (prid, did, sdate, edate) VALUES (lastval(), {$did}, '{$_POST['psdt1']}'::date, '{$_POST['pedt1']}'::date)";
		if ($result = pg_query($db, $sqlPre)) {
			if ($result2 = pg_query($db, $sqlPreCon)) {
			}
		}
	}

	if ((!empty($_POST['pmed2'])) && (!empty($_POST['pdos2'])) && (!empty($_POST['pfre2'])) && (!empty($_POST['psdt2'])) && (!empty($_POST['pedt2']))) {
		echo "FILLED IN MED\n";
		$sqlPre = "INSERT INTO prescription (medication, dosage, frequency)
			VALUES ('{$_POST['pmed2']}', '{$_POST['pdos2']}', '{$_POST['pfre2']}') RETURNING preid";
		$sqlPreCon = "INSERT INTO prescribes (prid, did, sdate, edate) VALUES (lastval(), {$did}, '{$_POST['psdt2']}'::date, '{$_POST['pedt2']}'::date)";
		if ($result = pg_query($db, $sqlPre)) {
			if ($result2 = pg_query($db, $sqlPreCon)) {
			}
		}
	}
	if ((!empty($_POST['pmed3'])) && (!empty($_POST['pdos3'])) && (!empty($_POST['pfre3'])) && (!empty($_POST['psdt3'])) && (!empty($_POST['pedt3']))) {
		echo "FILLED IN MED\n";
		$sqlPre = "INSERT INTO prescription (medication, dosage, frequency)
			VALUES ('{$_POST['pmed3']}', '{$_POST['pdos3']}', '{$_POST['pfre3']}') RETURNING preid";
		$sqlPreCon = "INSERT INTO prescribes (prid, did, sdate, edate) VALUES (lastval(), {$did}, '{$_POST['psdt3']}'::date, '{$_POST['pedt3']}'::date)";
		if ($result = pg_query($db, $sqlPre)) {
			if ($result2 = pg_query($db, $sqlPreCon)) {
			}
		}
	}
	if ((!empty($_POST['pmed4'])) && (!empty($_POST['pdos4'])) && (!empty($_POST['pfre4'])) && (!empty($_POST['psdt4'])) && (!empty($_POST['pedt4']))) {
		echo "FILLED IN MED\n";
		$sqlPre = "INSERT INTO prescription (medication, dosage, frequency)
			VALUES ('{$_POST['pmed4']}', '{$_POST['pdos4']}', '{$_POST['pfre4']}') RETURNING preid";
		$sqlPreCon = "INSERT INTO prescribes (prid, did, sdate, edate) VALUES (lastval(), {$did}, '{$_POST['psdt4']}'::date, '{$_POST['pedt4']}'::date)";
		if ($result = pg_query($db, $sqlPre)) {
			if ($result2 = pg_query($db, $sqlPreCon)) {
			}
		}
	}
	if ((!empty($_POST['pmed5'])) && (!empty($_POST['pdos5'])) && (!empty($_POST['pfre5'])) && (!empty($_POST['psdt5'])) && (!empty($_POST['pedt5']))) {
		echo "FILLED IN MED\n";
		$sqlPre = "INSERT INTO prescription (medication, dosage, frequency)
			VALUES ('{$_POST['pmed5']}', '{$_POST['pdos5']}', '{$_POST['pfre5']}') RETURNING preid";
		$sqlPreCon = "INSERT INTO prescribes (prid, did, sdate, edate) VALUES (lastval(), {$did}, '{$_POST['psdt5']}'::date, '{$_POST['pedt5']}'::date)";
		if ($result = pg_query($db, $sqlPre)) {
			if ($result2 = pg_query($db, $sqlPreCon)) {
			}
		}
	}
	header("Location: doctor.php");
}


//check for case number
if (isset($_GET['q']) && $_GET['q'] != '') {
	//sanitize here
	$q = ($_GET['q']);
	$sqlNurse = "SELECT fname, lname, licno FROM employee NATURAL JOIN cases WHERE cid = '{$q}'";
	$sqlCase = "SELECT * FROM cases NATURAL JOIN patient WHERE cid = {$q}";
	if ($result = pg_query($db, $sqlCase)) {
?>
<div class="container">
<?php
		while ($row = pg_fetch_assoc($result)) {
			$pid = $row['pid'];
			$sqlPast = "SELECT * FROM cases WHERE pid = {$pid} ORDER BY vdate DESC";
?>
	<h1>Create Diagnosis</h1>	
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
                <center><button>Update</button></center>
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
				<div class="diagcreate">
					<hl><b>Create Diagnosis</b></hl><hr><br>
					<div class="row">
					<div class="col-md-6">
						<div class="codiag">
							<hl><b>Vitals & Admission</b></hl><hr>
							<div class="col-md-6">
									<hl>Case ID:</hl><br> 
									<hl>Admitting Nurse:</hl><br>
									<hl>Date of Visit:</hl><br>
									<hl>Blood Pressure:</hl><br>
									<hl>Heart Rate:</hl><br>
									<hl>Respiratory Rate:</hl><br>
							</div>	
							<div class="col-md-6">
								<?php
									echo str_pad($q, 5, "0", STR_PAD_LEFT)."<br>";
									if ($result3 = pg_query($db, $sqlNurse)) {
										while ($row3 = pg_fetch_assoc($result3)) {
											echo $row3['fname']." ".$row3['lname']."<br>";
										}	
									}		
									echo $row['vdate']."<br>";
									echo "<sup>".$row['bpsys']."</sup>&frasl;<sub>".$row['bpdia']."</sub><br>";
									echo $row['hrate']." bpm <br>";
									echo $row['rrate']." bpm <br>";
								?>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<form id = 'diagnosissubmit' method="POST" name='diagnosissubmit'>
						<div class="codiag">
							<hl><b>Diagnosis Information</b></hl><hr>
							<div class="col-md-4">
								<hl>Doctor:</hl><br>
								<hl>Diagnosis:</hl><br>
							</div>
							<div class="col-md-8">
								<input type='text' id='dr' name='dr' value="<?php echo $dfname." ".$dlname; ?>" readonly/><br>
								<textarea id='diag' name='diag' form='diagnosissubmit' rows='4' cols='18'>Diagnosis</textarea>
							</div>
						</div>
					</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-5">
							<div class="colabs">
								<hl><b>Lab Results</b></hl><hr>
								<div class="col-md-8">
									Blood Urea Nitrogen:<br>
									Calcium:<br>
									Carbon Dioxide:<br>
									Chloride:<br>
									Creatinine:<br>
									Glucose:<br>
									Potassium:<br>
									Sodium:<br>
								</div>
								<div class="col-md-4">
								<input type="text" id='bun' name='bun' size="5" value="<?php echo rand(7, 20);?>"><br>
								<input type="text" id='calcium' name='calcium' size="5" value="<?php echo rand(8, 10).".".rand(0, 9);?>"><br>
								<input type="text" id='c02' name='c02' size="5" value="<?php echo rand(23, 29);?>"><br>
								<input type="text" id='chloride' name='chloride' size="5" value="<?php echo rand(96, 106)?>"><br>
								<input type="text" id='creatinine' name='creatinine' size="5" value="<?php echo rand(0, 1).".".rand(0,9);?>"><br>
								<input type="text" id='glucose' name='glucose' size="5" value="<?php echo rand(80, 105)?>"><br>
								<input type="text" id='potassium' name='potassium' size="5" value="<?php echo rand(3, 5).".".rand(0, 9);?>"><br>
								<input type="text" id='sodium' name='sodium' size="5" value="<?php echo rand(135, 145);?>"><br>
								</div>
							</div>
						</div>
						<div class="col-md-7">
							<div class="copres">
								<hl><b>Write Prescription</b><hl><hr>
									<div class="col-md-12">
										<table id='prescription' style="width: 100%;">
										<thead>
											<th>Med</th>
											<th>Dose</th>
											<th>Freq</th>
											<th>Start</th>
											<th>End</th>
										</thead>
										<tbody>
										<?php $pcount = 1; ?>
										<tr id='row1'>	
											<td align="center"><input type="text" id='pmed1' name='pmed1' size="3"></td>
											<td align="center"><input type="text" id='pdos1' name='pdos1' size="3"></td>
											<td align="center"><input type="text" id='pfre1' name='pfre1' size="3"></td>
											<td align="center"><input type="date" id='psdt1' name='psdt1' size="5"></td>
											<td align="center"><input type="date" id='pedt1' name='pedt1' size="5"></td>
										</tr>
										<tr id='row2' style="display: none;">	
											<td align="center"><input type="text" id='pmed2' name='pmed2' size="3"></td>
											<td align="center"><input type="text" id='pdos2' name='pdos2' size="3"></td>
											<td align="center"><input type="text" id='pfre2' name='pfre2' size="3"></td>
											<td align="center"><input type="date" id='psdt2' name='psdt2' size="5"></td>
											<td align="center"><input type="date" id='pedt2' name='pedt2' size="5"></td>
										</tr>
										<tr id='row3' style="display: none;">	
											<td align="center"><input type="text" id='pmed3' name='pmed3' size="3"></td>
											<td align="center"><input type="text" id='pdos3' name='pdos3' size="3"></td>
											<td align="center"><input type="text" id='pfre3' name='pfre3' size="3"></td>
											<td align="center"><input type="date" id='psdt3' name='psdt3' size="5"></td>
											<td align="center"><input type="date" id='pedt3' name='pedt3' size="5"></td>
										</tr>
										<tr id='row4' style="display: none;">	
											<td align="center"><input type="text" id='pmed4' name='pmed4' size="3"></td>
											<td align="center"><input type="text" id='pdos4' name='pdos4' size="3"></td>
											<td align="center"><input type="text" id='pfre4' name='pfre4' size="3"></td>
											<td align="center"><input type="date" id='psdt4' name='psdt4' size="5"></td>
											<td align="center"><input type="date" id='pedt4' name='pedt4' size="5"></td>
										</tr>
										<tr id='row5' style="display: none;">	
											<td align="center"><input type="text" id='pmed5' name='pmed5' size="3"></td>
											<td align="center"><input type="text" id='pdos5' name='pdos5' size="3"></td>
											<td align="center"><input type="text" id='pfre5' name='pfre5' size="3"></td>
											<td align="center"><input type="date" id='psdt5' name='psdt5' size="5"></td>
											<td align="center"><input type="date" id='pedt5' name='pedt5' size="5"></td>
										</tr>

										</table>
										<center><button type='button' onclick="showrow();">New Row</button></center>
										<script>
										<?php $pcount++; ?>
										var count = 2;
										function showrow() {
											document.getElementById("row" + count).style.display = "";
											count++;


										}
										</script>

												
									</div>
								</div>
							</div>
						</div><br>
					<div class="row">
						<div class='col-md-10'>
						<button onclick="window.open('charts.php?q=<?php echo $q;?>', '_blank')">Vital Charts</button>
						</div>
						<div class='col-md-2'>
							<button type="submit">Submit</button>
						</div>
					</div>
											
<?php 
		}
?>
				</div>
			</div>
		</div>
	</div>



<?php
		
		
				
	}
}
?>
