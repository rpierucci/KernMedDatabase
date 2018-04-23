<?php 
require('connect.php');
require('navdoctor.php');

$sqlPatient = "SELECT fname, lname, pid FROM patient ORDER BY lname ASC;";
$resultPatient = pg_query($db, $sqlPatient);
$patientCount = pg_num_rows($resultPatient);
$pfname = array();
$plname = array();
$pid = array();
while ($row = pg_fetch_assoc($resultPatient)) {
	$pfname[] = $row['fname'];
	$plname[] = $row['lname'];
	$pid[] = $row['pid'];
}


//doctor thats viewing
$eid = $_GET['q'];
?>
	<div class="container">
		<h1>Search</h1>	
		<div class="searchlist">
			<input type="text" id="name" onkeyup="namesearch()" placeholder="Search by last name..." style="width:100%;"><hr>
			<div class="col-md-12">
				<table id="list" style="width: 100%;">
				<thead><tr>
					<th><hl><b>Last Name</b></hl></th>
					<th><hl><b>First Name</b></hl></th>
					<th><hl><b>Patient ID</b></hl></th>
				</tr></thead>
				<tbody>
<?php 
	for ($i = 0; $i < $patientCount; $i++) {
		echo "<tr onclick =\"window.location='patient.php?q=".$pid[$i]."';\">";
		echo "<td>".$plname[$i]."</td>";
		echo "<td>".$pfname[$i]."</td>";
		echo "<td>".str_pad($pid[$i], 5, "0", STR_PAD_LEFT)."</td>";
		echo "</tr>";
	}
?>
				</table>
				<script>
				function namesearch() {
					var input, filter, table, tr, td, i;
					input = document.getElementById("name");
					filter = input.value.toUpperCase();
					table = document.getElementById("list");
					tr = table.getElementsByTagName("tr");
					for (i = 0; i < tr.length; i++) {
						td = tr[i].getElementsByTagName("td")[0];
						if (td) {
							if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
								tr[i].style.display = "";
							} else {
								tr[i].style.display = "none";
							}
						}
					}
				}
				</script>


			</div>
		</div>
