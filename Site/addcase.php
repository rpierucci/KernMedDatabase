<?php
require('connect.php');
require('navnurse.php');
$eid = 3;
$sqlNurse = "SELECT * FROM employee WHERE eid = '{$eid}'";
$resultNurse = pg_query($db, $sqlNurse);
if (!$resultNurse) {
	echo "errornurse\n";
	exit;
}
?>
<div class="container">
<?php
while ($row = pg_fetch_assoc($resultNurse)) {
?>
	<h1>Welcome <?php echo $row['fname']; ?></h1><hl><b><h5>License Number: </b></hl><?php echo $row['licno']; ?> <hl><b>License Type:</hl></b> <?php echo $row['lictype']; ?><br><br>


<?php
}
?>
			<div class="row">
				<div class="col-md-12">
					<div class="details">
						<b>New Admission</b><hr><br>
						<div class="row">
							<div class="col-md-12">
							<div class="col-md-4">
								<div class="form-group">
									<label for="fname">First Name</label><br>
									<input type="text" class="form-control" id="fname"  placeholder="Patient First Name">
								</div>
								<div class="form-group">
									<label for="street">Street Address</label><br>
									<input type="text" class="form-control" id="street"  placeholder="Street Address">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="mname">Middle Name</label><br>
									<input type="text" class="form-control" id="mname"  placeholder="Patient Middle Name">
								</div>
								<div class="form-group">
									<label for="city">City</label><br>
									<input type="text" class="form-control" id="city"  placeholder="City">
								</div>
							</div>
							<div class="col-md-4">

								<div class="form-group">
									<label for="lname">Last Name</label><br>
									<input type="text" class="form-control" id="lname"  placeholder="Patient Last Name">
								</div>
								<div class="form-group">
									<label for="state">State</label><br>
									<input type="text" class="form-control" id="state"  placeholder="State">
								</div>
								<div class="form-group">
									<label for="zip">Zip Code</label><br>
									<input type="text" class="form-control" id="zip"  placeholder="Zip Code">
								</div>
							</div>
							</div>
						</div>
<form>
						  <button type="submit" class="btn btn-primary">Submit</button>
						</form>

					</div>
				</div>
			</div>
			</div>
			<br><br><br>
	</body>
