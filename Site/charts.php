<?php 
require('connect.php');
require('navnurse.php');


//nurse thats viewing
$eid = 8;
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
			$sqlAllCases = "SELECT * FROM cases WHERE pid = '{$pid}' ORDER by vdate ASC";
			$resultCases = pg_query($db, $sqlAllCases);
			$casecount = pg_num_rows($resultCases);
			$casenumber = array();
			$allvdate = array();
			$allbpsys = array();
			$allbpdia = array();
			$allhrate = array();
			$allrrate = array();
			while ($rowall = pg_fetch_assoc($resultCases)) {
				$casenumber[] = $rowall['cid'];
				$allvdate[] = $rowall['vdate'];
				$allbpsys[] = $rowall['bpsys'];
				$allbpdia[] = $rowall['bpdia'];
				$allhrate[] = $rowall['hrate'];
				$allrrate[] = $rowall['rrate'];
			}


			$sqlPast = "SELECT * FROM cases WHERE pid = {$pid} ORDER BY vdate DESC";
?>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(hrateChart);
	  google.charts.setOnLoadCallback(rrateChart);
	  google.charts.setOnLoadCallback(bpChart);

      function hrateChart() {
        var data = google.visualization.arrayToDataTable([
          ['Date', 'Heart Rate'],
          <?php
		  for ($k = 0; $k < $casecount; $k++) {
			if ($k != $casecount - 1) {
		  	echo "['".$allvdate[$k]."', ".$allhrate[$k]."],";
			} else {
		  	echo "['".$allvdate[$k]."', ".$allhrate[$k]."]";
		 	}
		  }
		  ?>
        ]);

        var options = {
          title: 'Heart Rate',
          legend: { position: 'bottom' },
		  backgroundColor: '#E0E0E0'
        };

        var chart = new google.visualization.LineChart(document.getElementById('hrate_chart'));

        chart.draw(data, options);
	  }

      function rrateChart() {
        var data = google.visualization.arrayToDataTable([
          ['Date', 'Respiratory Rate'],
          <?php
		  for ($k = 0; $k < $casecount; $k++) {
			if ($k != $casecount - 1) {
		  	echo "['".$allvdate[$k]."', ".$allrrate[$k]."],";
			} else {
		  	echo "['".$allvdate[$k]."', ".$allrrate[$k]."]";
		 	}
		  }
		  ?>
        ]);

        var options = {
          title: 'Respiratory Rate',
          legend: { position: 'bottom' },
		  backgroundColor: '#E0E0E0'
        };

        var chart = new google.visualization.LineChart(document.getElementById('rrate_chart'));

        chart.draw(data, options);
	  }

      function bpChart() {
        var data = google.visualization.arrayToDataTable([
          ['Date', 'Systolic', 'Diastolic'],
          <?php
		  for ($k = 0; $k < $casecount; $k++) {
			if ($k != $casecount - 1) {
		  	echo "['".$allvdate[$k]."', ".$allbpsys[$k].", ".$allbpdia[$k]."],";
			} else {
		  	echo "['".$allvdate[$k]."', ".$allbpsys[$k].", ".$allbpdia[$k]."]";
		 	}
		  }
		  ?>
        ]);

        var options = {
          title: 'Blood Pressure',
		  legend: { position: 'bottom' },
		  backgroundColor: '#E0E0E0'
        };

        var chart = new google.visualization.LineChart(document.getElementById('bp_chart'));

        chart.draw(data, options);
	  }
    </script>

	<h1>Patient Graph Data</h1>	
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
			</div>
			<div class="col-md-8">
				<div class="chartoverview">
					<hl><b>Charts</b></hl><hr>
					<div class="row">				
						<div class="col-md-12">
							<div class="cocharts">
								<hl><b>Blood Pressure</b></hl><hr>
								<div id="bp_chart" style="width:100%;height: 92%;"></div>
							</div>	
						</div>
					</div><br>
					<div class="row">				
						<div class="col-md-12">
							<div class="cocharts">
								<hl><b>Heart Rate</b></hl><hr>
								<div id="hrate_chart" style="width:100%;height: 92%;"></div>
							</div>	
						</div>
					</div><br>
					<div class="row">				
						<div class="col-md-12">
							<div class="cocharts">
								<hl><b>Respiratory Rate</b></hl><hr>
								<div id="rrate_chart" style="width:100%;height: 92%;"></div>
							</div>	
						</div>
					</div><br>
					<div class="row">
						<div class="col-md-12">
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
