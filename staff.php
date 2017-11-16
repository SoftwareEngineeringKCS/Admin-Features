<?php #JORGE ESPADA
	
	session_start();	
	$page_title = 'Kean Career Services';

	if (!isset($_SESSION["user_id"])) {
		include ('includes/header.html');
		include ('login.html');
	} else {
		include ('includes/header.html');

		date_default_timezone_set('America/New_York');
		$system_datetime = date("Y-m-d H:i");
		//print($system_datetime); #TEST

		echo "<br><a href='sign_out.php'>SIGN-UP</a>";

		// Check for form submission:
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			include ('includes/db_config.php');
			// Print the results:
			if (isset($_POST['features']) && $_POST['features'] == 'Set-Availability') {
				echo "<h1>Set-Availability Result</h1>";
				// Validate Dates.
				if (checkdate($_POST['start_month'], $_POST['start_day'], $_POST['start_year']) && checkdate($_POST['end_month'], $_POST['end_day'], $_POST['end_year'])) {
						$period_start = $_POST['start_year'] . "-" . $_POST['start_month'] . "-" . $_POST['start_day'];
						$period_end = $_POST['end_year'] . "-" . $_POST['end_month'] . "-" . $_POST['end_day'];
						$time_start = $_POST['start_time'];
						$time_end = $_POST['end_time'];
						$duration = $_POST['duration'];

						if (date_create($period_start) <= date_create($period_end) || strtotime($time_start) <= strtotime($time_end)) {
							if (date_create($period_start . " " . $time_start) >= date_create($system_datetime)) {
								$daysMO = isset($_POST['daysMO']) ? 1 : 0;
								$daysTU = isset($_POST['daysTU']) ? 1 : 0;
								$daysWE = isset($_POST['daysWE']) ? 1 : 0;
								$daysTH = isset($_POST['daysTH']) ? 1 : 0;
								$daysFR = isset($_POST['daysFR']) ? 1 : 0;
								$days = "";
								if ($daysMO) {
									$days = "MO";
								}
								if ($daysTU && $days == "") {
									$days = "TU";
								} else if ($daysTU && $days != "") {
									$days = $days . "-TU";
								}
								if ($daysWE && $days == "") {
									$days = "WE";
								} else if ($daysWE && $days != "") {
									$days = $days . "-WE";
								}
								if ($daysTH && $days == "") {
									$days = "TH";
								} else if ($daysTH && $days != "") {
									$days = $days . "-TH";
								}
								if ($daysFR && $days == "") {
									$days = "FR";
								} else if ($daysFR && $days != "") {
									$days = $days . "-FR";
								}

								$query = sprintf("CALL usp_Set_Availability('%s','%s', '%s','%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', @message)", $_SESSION['user_id'], $period_start, $period_end, $time_start, $time_end, $days, $duration, 0, 0, 0, "");
								$result = mysqli_query($conex, $query);
								$row = mysqli_fetch_array($result);

								echo "<p>$row[0]</p>"; // Message

								mysqli_free_result($result);
								mysqli_close($conex);
							} else {
								echo "<p class='error'>[Start Period-Time] \"" . $period_start . " " . $time_start . "\" must be greater than current datetime.</p>";
							}
						} else {
							echo "<p class='error'>Start-Period must be less than or equal than End-Period and
													Start-Time must be less than or equal than End-Time.</p>";
						}	
				} else {
					echo "<p class='error'>Start Period and/or End Period are not valid dates.
											Please check the day number (30-day month or 31-day month).</p>";
				}				
			} else if (isset($_POST['features']) && $_POST['features'] == 'Manage-Appointments') {
				echo "<h1>Manage-Appointments Result</h1>";

				# WRITE MANAGE-APPOINTMENTS RESULT HERE

			} else if (isset($_POST['features']) && $_POST['features'] == 'View-Statistics') { 
				echo "<h1>View-Statistics Result</h1>";

				# WRITE VIEW-STATISTICS RESULT HERE

			}
			
		} // End of main submission IF.

		echo "<h1>Administrative Features</h1>";
	}

?>

<div id="administrative_features"<?php if (!isset($_SESSION["user_id"])) echo ' style="display: none;"'; ?> >
	<form action="staff.php" method="post">	
		<p>
			<span class="input">
				<input type="radio" name="features" value="Set-Availability"<?php if (isset($_POST['features']) && ($_POST['features'] == 'Set-Availability')) echo ' checked="checked"'; ?> onclick="doSet()" /> Set-Availability
				<input type="radio" name="features" value="Manage-Appointments"<?php if (isset($_POST['features']) && ($_POST['features'] == 'Manage-Appointments')) echo ' checked="checked"'; ?> onclick="doManage()" />  Manage-Appointments
				<input type="radio" name="features" value="View-Statistics"<?php if (isset($_POST['features']) && ($_POST['features'] == 'View-Statistics')) echo ' checked="checked"'; ?> onclick="doStats()" />  View-Statistics
			</span>
		</p>
		<script type="text/javascript">
			function doSet() {
			    var x = document.getElementById("show_set");
			    var y = document.getElementById("show_manage");
			    var w = document.getElementById("show_stats");
			    var z = document.getElementById("show_submit");
			    x.style.display = "block";
			    y.style.display = "none";
			    w.style.display = "none";
			    z.style.display = "block";
			}
			function doManage() {
			    var x = document.getElementById("show_set");
			    var y = document.getElementById("show_manage");
			    var w = document.getElementById("show_stats");
			    var z = document.getElementById("show_submit");
			    x.style.display = "none";
			    y.style.display = "block";
			    w.style.display = "none";
			    z.style.display = "none";
			}
			function doStats() {
			    var x = document.getElementById("show_set");
			    var y = document.getElementById("show_manage");
			    var w = document.getElementById("show_stats");
			    var z = document.getElementById("show_submit");
			    x.style.display = "none";
			    y.style.display = "none";
			    w.style.display = "block";
			    z.style.display = "none";
			}
		</script>
		<div id="show_set" style="display: none;">
			<table width="100%">
				<tr>
					<td width="38%" style="border: 0px"><p><strong><u>SET NEW AVAILABILITY PERIOD:</u></strong></p></td>
					<td width="62%" style="border: 0px"><p><strong><u>ACTIVE PERIOD LIST:</u></strong></p></td>
				</tr>
				<tr>
					<td style="border: 0px">
						<p>Start Period:
						<br>
						<select name='start_month' required="required" style='height: 30px; width: 100px'>
							<option value=''>#Month</option>
							<option value='1'<?php if (isset($_POST['start_month']) && $_POST['start_month'] == "1") echo " selected"; ?>>January</option>
							<option value='2'<?php if (isset($_POST['start_month']) && $_POST['start_month'] == "2") echo " selected"; ?>>February</option>
							<option value='3'<?php if (isset($_POST['start_month']) && $_POST['start_month'] == "3") echo " selected"; ?>>March</option>
							<option value='4'<?php if (isset($_POST['start_month']) && $_POST['start_month'] == "4") echo " selected"; ?>>April</option>
							<option value='5'<?php if (isset($_POST['start_month']) && $_POST['start_month'] == "5") echo " selected"; ?>>May</option>
							<option value='6'<?php if (isset($_POST['start_month']) && $_POST['start_month'] == "6") echo " selected"; ?>>June</option>
							<option value='7'<?php if (isset($_POST['start_month']) && $_POST['start_month'] == "7") echo " selected"; ?>>July</option>
							<option value='8'<?php if (isset($_POST['start_month']) && $_POST['start_month'] == "8") echo " selected"; ?>>August</option>
							<option value='9'<?php if (isset($_POST['start_month']) && $_POST['start_month'] == "9") echo " selected"; ?>>September</option>
							<option value='10'<?php if (isset($_POST['start_month']) && $_POST['start_month'] == "10") echo " selected"; ?>>October</option>
							<option value='11'<?php if (isset($_POST['start_month']) && $_POST['start_month'] == "11") echo " selected"; ?>>November</option>
							<option value='12'<?php if (isset($_POST['start_month']) && $_POST['start_month'] == "12") echo " selected"; ?>>December</option>
						</select>
						<select name='start_day' required="required" style='height: 30px; width: 65px'>
							<option value=''>#Day</option>
							<option value='1'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "1") echo " selected"; ?>>1</option>
							<option value='2'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "2") echo " selected"; ?>>2</option>
							<option value='3'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "3") echo " selected"; ?>>3</option>
							<option value='4'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "4") echo " selected"; ?>>4</option>
							<option value='5'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "5") echo " selected"; ?>>5</option>
							<option value='6'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "6") echo " selected"; ?>>6</option>
							<option value='7'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "7") echo " selected"; ?>>7</option>
							<option value='8'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "8") echo " selected"; ?>>8</option>
							<option value='9'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "9") echo " selected"; ?>>9</option>
							<option value='10'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "10") echo " selected"; ?>>10</option>
							<option value='11'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "11") echo " selected"; ?>>11</option>
							<option value='12'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "12") echo " selected"; ?>>12</option>
							<option value='13'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "13") echo " selected"; ?>>13</option>
							<option value='14'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "14") echo " selected"; ?>>14</option>
							<option value='15'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "15") echo " selected"; ?>>15</option>
							<option value='16'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "16") echo " selected"; ?>>16</option>
							<option value='17'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "17") echo " selected"; ?>>17</option>
							<option value='18'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "18") echo " selected"; ?>>18</option>
							<option value='19'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "19") echo " selected"; ?>>19</option>
							<option value='20'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "20") echo " selected"; ?>>20</option>
							<option value='21'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "21") echo " selected"; ?>>21</option>
							<option value='22'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "22") echo " selected"; ?>>22</option>
							<option value='23'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "23") echo " selected"; ?>>23</option>
							<option value='24'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "24") echo " selected"; ?>>24</option>
							<option value='25'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "25") echo " selected"; ?>>25</option>
							<option value='26'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "26") echo " selected"; ?>>26</option>
							<option value='27'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "27") echo " selected"; ?>>27</option>
							<option value='28'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "28") echo " selected"; ?>>28</option>
							<option value='29'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "29") echo " selected"; ?>>29</option>
							<option value='30'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "30") echo " selected"; ?>>30</option>
							<option value='31'<?php if (isset($_POST['start_day']) && $_POST['start_day'] == "31") echo " selected"; ?>>31</option>
						</select>
						<input type='text' name='start_year' required="required" value="<?php echo date('Y') ; ?>" style='height: 20px; width: 45px; text-align: center;'>
						</p>
						<p>End Period:
						<br>
						<select name='end_month' required="required" style='height: 30px; width: 100px'>
							<option value=''>#Month</option>
							<option value='1'<?php if (isset($_POST['end_month']) && $_POST['end_month'] == "1") echo " selected"; ?>>January</option>
							<option value='2'<?php if (isset($_POST['end_month']) && $_POST['end_month'] == "2") echo " selected"; ?>>February</option>
							<option value='3'<?php if (isset($_POST['end_month']) && $_POST['end_month'] == "3") echo " selected"; ?>>March</option>
							<option value='4'<?php if (isset($_POST['end_month']) && $_POST['end_month'] == "4") echo " selected"; ?>>April</option>
							<option value='5'<?php if (isset($_POST['end_month']) && $_POST['end_month'] == "5") echo " selected"; ?>>May</option>
							<option value='6'<?php if (isset($_POST['end_month']) && $_POST['end_month'] == "6") echo " selected"; ?>>June</option>
							<option value='7'<?php if (isset($_POST['end_month']) && $_POST['end_month'] == "7") echo " selected"; ?>>July</option>
							<option value='8'<?php if (isset($_POST['end_month']) && $_POST['end_month'] == "8") echo " selected"; ?>>August</option>
							<option value='9'<?php if (isset($_POST['end_month']) && $_POST['end_month'] == "9") echo " selected"; ?>>September</option>
							<option value='10'<?php if (isset($_POST['end_month']) && $_POST['end_month'] == "10") echo " selected"; ?>>October</option>
							<option value='11'<?php if (isset($_POST['end_month']) && $_POST['end_month'] == "11") echo " selected"; ?>>November</option>
							<option value='12'<?php if (isset($_POST['end_month']) && $_POST['end_month'] == "12") echo " selected"; ?>>December</option>
						</select>
						<select name='end_day' required="required" style='height: 30px; width: 65px'>
							<option value=''>#Day</option>
							<option value='1'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "1") echo " selected"; ?>>1</option>
							<option value='2'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "2") echo " selected"; ?>>2</option>
							<option value='3'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "3") echo " selected"; ?>>3</option>
							<option value='4'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "4") echo " selected"; ?>>4</option>
							<option value='5'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "5") echo " selected"; ?>>5</option>
							<option value='6'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "6") echo " selected"; ?>>6</option>
							<option value='7'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "7") echo " selected"; ?>>7</option>
							<option value='8'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "8") echo " selected"; ?>>8</option>
							<option value='9'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "9") echo " selected"; ?>>9</option>
							<option value='10'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "10") echo " selected"; ?>>10</option>
							<option value='11'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "11") echo " selected"; ?>>11</option>
							<option value='12'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "12") echo " selected"; ?>>12</option>
							<option value='13'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "13") echo " selected"; ?>>13</option>
							<option value='14'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "14") echo " selected"; ?>>14</option>
							<option value='15'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "15") echo " selected"; ?>>15</option>
							<option value='16'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "16") echo " selected"; ?>>16</option>
							<option value='17'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "17") echo " selected"; ?>>17</option>
							<option value='18'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "18") echo " selected"; ?>>18</option>
							<option value='19'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "19") echo " selected"; ?>>19</option>
							<option value='20'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "20") echo " selected"; ?>>20</option>
							<option value='21'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "21") echo " selected"; ?>>21</option>
							<option value='22'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "22") echo " selected"; ?>>22</option>
							<option value='23'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "23") echo " selected"; ?>>23</option>
							<option value='24'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "24") echo " selected"; ?>>24</option>
							<option value='25'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "25") echo " selected"; ?>>25</option>
							<option value='26'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "26") echo " selected"; ?>>26</option>
							<option value='27'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "27") echo " selected"; ?>>27</option>
							<option value='28'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "28") echo " selected"; ?>>28</option>
							<option value='29'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "29") echo " selected"; ?>>29</option>
							<option value='30'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "30") echo " selected"; ?>>30</option>
							<option value='31'<?php if (isset($_POST['end_day']) && $_POST['end_day'] == "31") echo " selected"; ?>>31</option>
						</select>
						<input type='text' name='end_year' required="required" value="<?php echo date('Y') ; ?>" style='height: 20px; width: 45px; text-align: center;'>
						</p>
						<p>Start Time:             End Time:<br>
							<select name='start_time' required="required" style='height: 30px; width: 112px'>
								<option value=''>#Time</option>
								<option value='08:00'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "08:00") echo " selected"; ?>>08:00</option>
								<option value='08:30'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "08:30") echo " selected"; ?>>08:30</option>
								<option value='09:00'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "09:00") echo " selected"; ?>>09:00</option>
								<option value='09:30'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "09:30") echo " selected"; ?>>09:30</option>
								<option value='10:00'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "10:00") echo " selected"; ?>>10:00</option>
								<option value='10:30'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "10:30") echo " selected"; ?>>10:30</option>
								<option value='11:00'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "11:00") echo " selected"; ?>>11:00</option>
								<option value='11:30'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "11:30") echo " selected"; ?>>11:30</option>
								<option value='12:00'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "12:00") echo " selected"; ?>>12:00</option>
								<option value='12:30'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "12:30") echo " selected"; ?>>12:30</option>
								<option value='13:00'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "13:00") echo " selected"; ?>>13:00</option>
								<option value='13:30'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "13:30") echo " selected"; ?>>13:30</option>
								<option value='14:00'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "14:00") echo " selected"; ?>>14:00</option>
								<option value='14:30'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "14:30") echo " selected"; ?>>14:30</option>
								<option value='15:00'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "15:00") echo " selected"; ?>>15:00</option>
								<option value='15:30'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "15:30") echo " selected"; ?>>15:30</option>
								<option value='16:00'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "16:00") echo " selected"; ?>>16:00</option>
								<option value='16:30'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "16:30") echo " selected"; ?>>16:30</option>
								<option value='17:00'<?php if (isset($_POST['start_time']) && $_POST['start_time'] == "17:00") echo " selected"; ?>>17:00</option>
							</select>
							<select name='end_time' required="required" style='height: 30px; width: 112px'>
								<option value=''>#Time</option>
								<option value='08:00'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "08:00") echo " selected"; ?>>08:00</option>
								<option value='08:30'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "08:30") echo " selected"; ?>>08:30</option>
								<option value='09:00'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "09:00") echo " selected"; ?>>09:00</option>
								<option value='09:30'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "09:30") echo " selected"; ?>>09:30</option>
								<option value='10:00'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "10:00") echo " selected"; ?>>10:00</option>
								<option value='10:30'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "10:30") echo " selected"; ?>>10:30</option>
								<option value='11:00'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "11:00") echo " selected"; ?>>11:00</option>
								<option value='11:30'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "11:30") echo " selected"; ?>>11:30</option>
								<option value='12:00'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "12:00") echo " selected"; ?>>12:00</option>
								<option value='12:30'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "12:30") echo " selected"; ?>>12:30</option>
								<option value='13:00'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "13:00") echo " selected"; ?>>13:00</option>
								<option value='13:30'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "13:30") echo " selected"; ?>>13:30</option>
								<option value='14:00'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "14:00") echo " selected"; ?>>14:00</option>
								<option value='14:30'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "14:30") echo " selected"; ?>>14:30</option>
								<option value='15:00'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "15:00") echo " selected"; ?>>15:00</option>
								<option value='15:30'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "15:30") echo " selected"; ?>>15:30</option>
								<option value='16:00'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "16:00") echo " selected"; ?>>16:00</option>
								<option value='16:30'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "16:30") echo " selected"; ?>>16:30</option>
								<option value='17:00'<?php if (isset($_POST['end_time']) && $_POST['end_time'] == "17:00") echo " selected"; ?>>17:00</option>
							</select>
						</p>
						<table>
							<tr>
								<td style="border: 0px">
									<p style="padding: 20px 0 0 20px">Meeting Days:<br>
										<input type="checkbox" name="daysMO" value="MO" checked /> Monday<br>
										<input type="checkbox" name="daysTU" value="TU" checked /> Tuesday<br>
										<input type="checkbox" name="daysWE" value="WE" checked /> Wednesday<br>
										<input type="checkbox" name="daysTH" value="TH" checked /> Thursday<br>
										<input type="checkbox" name="daysFR" value="FR" /> Friday
									</p>
								</td>
								<td valign="top" style="border: 0px">
									<p>Meeting Duration:<br>
										<select name='duration' required="required" style='height: 30px; width: 100px'>
											<option value=''>#Minutes</option>
											<option value='15'<?php if (isset($_POST['duration']) && $_POST['duration'] == "15") echo " selected"; ?>>15</option>
											<option value='30'<?php if (isset($_POST['duration']) && $_POST['duration'] == "30") echo " selected"; ?>>30</option>
											<option value='60'<?php if (isset($_POST['duration']) && $_POST['duration'] == "60") echo " selected"; ?>>60</option>
										</select>
									</p>
								</td>
							</tr>
						</table>
					</td>
					<td style="vertical-align: top; border: 0px;">
						<?php 

							include ('includes/db_config.php');
							$query = sprintf("SELECT * FROM Availability_Setting WHERE consultant_id = '%s' ORDER BY id", $_SESSION["user_id"]);
							$result = mysqli_query($conex, $query);
							if ($result) {
								if (mysqli_num_rows($result) > 0) {
									echo "<p><table border = 1>";
									echo "<tr><th colspan='2'>Period<th colspan='2'>Time<th colspan='2'>Meeting";
									echo "<tr><th>Start<th>End<th>Start<th>End<th>Days<th>Duration";
									while ($row = mysqli_fetch_array($result)) {
										$p_start = $row['period_start'];
										$p_end = $row['period_end'];
										$t_start = $row['time_start'];
										$t_end = $row['time_end'];
										$d = $row['days'];
										$dur = $row['duration'];
										echo "<tr>";
										echo "<td>$p_start<td>$p_end<td>$t_start<td>$t_end<td>$d<td style='text-align: center;'>$dur";
									}
									echo "</table></p>";
								} else {
									echo "<br><br>### EMPTY LIST ###";
								}
							} else {
								echo "<br>Problem trying to get list: " . mysqli_error();
								echo "<br>Contact Administrator!";
							}
							mysqli_free_result($result);
							mysqli_close($conex);

						?>
					</td>
				</tr>
			</table>
		</div>
		<div id="show_manage" style="display: none;">
			<?php

				/*include ('includes/db_config.php');
				
				mysqli_free_result($result);
				mysqli_close($conex);*/

			?>

			<center><p><img src='pictures/under_construction.png' alt='Under Construction Error' style='width: 400px; height: 150px;'></p></center>
		</div>
		<div id="show_stats" style="display: none;">
			<?php

				/*include ('includes/db_config.php');
				
				mysqli_free_result($result);
				mysqli_close($conex);*/

			?>

			<center><p><img src='pictures/under_construction.png' alt='Under Construction Error' style='width: 400px; height: 150px;'></p></center>
		</div>
		<div id="show_submit" style="display: none;">
			<p><input type="submit" name="submit" value="SET-AVAILABILITY" style="background-color: #f7dc6f; height: 30px;" /></p>
		</div>
	</form>
</div>

<?php include ('includes/footer.html'); ?>
