<?php 
require('connect.php');
require('navnurse.php');

if ((!empty($_POST['fname'])) && (!empty($_POST['mname'])) && (!empty($_POST['lname'])) && (!empty($_POST['ssn'])) && (!empty($_POST['phone'])) && (!empty($_POST['dob'])) && (!empty($_POST['sex'])) && (!empty($_POST['lang'])) && (!empty($_POST['street'])) && (!empty($_POST['city'])) && (!empty($_POST['state'])) && (!empty($_POST['zip']))) {
	$sqlPatient = "INSERT INTO patient (ssn, fname, mname, lname, street, city, state, zip, phone, dob, sex, instype, language) VALUES ({$_POST['ssn']}, '{$_POST['fname']}', '{$_POST['mname']}', '{$_POST['lname']}', '{$_POST['street']}', '{$_POST['city']}', '{$_POST['state']}', '{$_POST['zip']}', {$_POST['phone']}, '{$_POST['dob']}'::date, '{$_POST['sex']}', 'Test', '{$_POST['lang']}')";
	if ($result = pg_query($db, $sqlPatient)) {
		header("Location: nurse.php");
	}

}

?>
	<div class="container">
		<h1>Add Patient</h1>	
		<div class="searchlist">
			<hl></b> New Patient Form</b></hl><hr>
			<form id='sub' name='sub' method="POST">
			<div class="row">
				<div class="col-md-3">
					<hl><b>First Name</b></hl><br>
					<input type="text" id='fname' name='fname'><br><br>
					<hl><b>Middle Name</b></hl><br>
					<input type="text" id='mname' name='mname'><br><br>
					<hl><b>Last Name</b></hl><br>
					<input type="text" id='lname' name='lname'><br><br>
					<hl><b>Social Security Number</b></hl><br>
					<input type="number" id='ssn' name='ssn'><br><br>
				</div>
				<div class="col-md-3">
					<hl><b>Phone Number</b></hl><br>
					<input type="text" id='phone' name='phone'><br><br>
					<hl><b>Date of Birth</b></hl><br>
					<input type="date" id='dob' name='dob'><br><br>
					<hl><b>Sex</b></hl><br>
					<input type="text" id='sex' name='sex'><br><br>
					<hl><b>Primary Language</b></hl><br>
					<input type="text" id='lang' name='lang'><br><br>
				</div>
				<div class="col-md-3">
					<hl><b>Street Address</b></hl><br>
					<input type="text" id='street' name='street'><br><br>
					<hl><b>City</b></hl><br>
					<input type="text" id='city' name='city'><br><br>
					<hl><b>State</b></hl><br>
					<input type="text" id='state' name='state'><br><br>
					<hl><b>Zip Code</b></hl><br>
					<input type="text" id='zip' name='zip'><br><br>	
				</div>
				<div class="col-md-3"><br><br><br><br><br><br>
					<button type="submit">Submit</button>
					</form>
				</div>

		</div>
