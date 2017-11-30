<?php #JORGE ESPADA
	
	function sendEmail($from, $to, $passDateTime) {
		 $subject = "TESTING - Appointment Cancellation";
		 $header = "From: " . $from;

		 if ($from == "") {
		 	$message = "Dear Student, #THIS IS ONLY A TEST#
		 				\nYour appointment on [" . date_format(date_create($passDateTime), 'm/d/Y h:i A') . "] has been cancelled by [The Consultant] 
		 				\nBest regards,
		 				\nKean Career Services";
		 	return mail($to, $subject, $message);
		 } else {
			$message = "Dear Student, #THIS IS ONLY A TEST#
		 				\nYour appointment on [" . date_format(date_create($passDateTime), 'm/d/Y h:i A') . "] has been cancelled by [" . $from . "] 
		 				\nBest regards,
		 				\nKean Career Services";
		 	return mail($to, $subject, $message, $header);
		 }
	}

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

		// Check for form submission:
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			include ('includes/db_config.php');

			echo "<div id='administrative_result' style='display: block;'>";
			// Minimal form validation:
			if (isset($_POST['features']) && $_POST['features'] == 'Set-Availability') {
				echo "<h1>Set-Availability Result</h1>";

				if (isset($_POST['btnSetAvailability'])) { 
					if ($_POST['start_month'] == '' || $_POST['start_day'] == '' || $_POST['start_year'] == '' || !is_numeric($_POST['start_year']) ||  
							$_POST['end_month'] == '' || $_POST['end_day'] == '' || $_POST['end_year'] == '' || !is_numeric($_POST['end_year']) || 
							$_POST['start_time'] == '' || $_POST['end_time'] == '' || $_POST['duration'] == '' || 
							(!isset($_POST['daysMO']) && !isset($_POST['daysTU']) && !isset($_POST['daysWE']) && !isset($_POST['daysTH']) && !isset($_POST['daysFR']))) {
						echo "<h2>The following fields cannot be empty!</h2>";
						echo "<p class='error'>";
						if ($_POST['start_month'] == '' || $_POST['start_day'] == '' || $_POST['start_year'] == '') echo "\"Start Period\", ";
						if ($_POST['end_month'] == '' || $_POST['end_day'] == '' || $_POST['end_year'] == '') echo "\"End Period\", ";
						if ($_POST['start_time'] == '') echo "\"Start Time\", ";
						if ($_POST['end_time'] == '') echo "\"End Time\", ";
						if ($_POST['duration'] == '') echo "\"Meeting Time\", ";
						if (!isset($_POST['daysMO']) && !isset($_POST['daysTU']) && !isset($_POST['daysWE']) && !isset($_POST['daysTH']) && !isset($_POST['daysFR'])) echo "\"Meeting Days\"";
						if (!is_numeric($_POST['start_year']) || !is_numeric($_POST['end_year'])) echo "<br><font size=2 color='steelblue'>\"The year of both periods must be a number!\"</font>";
						echo "</p>";
						echo "<p><button type='button' value='SA' style='height: 30px;' onclick='mainDisplay(this)'>BACK</button></p>";
					} else {
						// Validate Dates.
						if (checkdate($_POST['start_month'], $_POST['start_day'], $_POST['start_year']) && checkdate($_POST['end_month'], $_POST['end_day'], $_POST['end_year'])) {
								$period_start = $_POST['start_year'] . "-" . $_POST['start_month'] . "-" . $_POST['start_day'];
								$period_end = $_POST['end_year'] . "-" . $_POST['end_month'] . "-" . $_POST['end_day'];
								$time_start = $_POST['start_time'];
								$time_end = $_POST['end_time'];
								$duration = $_POST['duration'];

								if (date_create($period_start) <= date_create($period_end) && strtotime($time_start) <= strtotime($time_end)) {
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

										if ($row[0] == "Availability Period Successfully Created!") {
											echo "<p class='result'>$row[0]</p>"; // Message
										} else {
											echo "<p class='error'>$row[0]</p>"; // Message
										}
										echo "<p><button type='button' value='SA' style='height: 30px;' onclick='mainDisplay(this)'>BACK</button></p>";

										mysqli_free_result($result);
										mysqli_close($conex);
									} else {
										echo "<p class='error'>[Start Period-Time] \"" . $period_start . " " . $time_start . "\" must be greater than current datetime.</p>";
										echo "<p><button type='button' value='SA' style='height: 30px;' onclick='mainDisplay(this)'>BACK</button></p>";
									}
								} else {
									echo "<p class='error'>\"Start-Period\" must be less than or equal than \"End-Period\" and
															\"Start-Time\" must be less than or equal than \"End-Time\".</p>";
									echo "<p><button type='button' value='SA' style='height: 30px;' onclick='mainDisplay(this)'>BACK</button></p>";
								}	
						} else {
							echo "<p class='error'>\"Start-Period\" and/or \"End-Period\" are not valid dates.
													Please check the day number (30-day month or 31-day month).</p>";
							echo "<p><button type='button' value='SA' style='height: 30px;' onclick='mainDisplay(this)'>BACK</button></p>";
						}
					}
				} elseif (isset($_POST['btnSetFinish']) || isset($_POST['btnSetClose']) || isset($_POST['btnSetCancel'])) {
					#echo "GOT IT!"; //TEST
					if (isset($_POST['btnSetFinish'])) {
						# Finish Period.
						$query = sprintf("CALL usp_Review_Set('%d', '%d', '%d', '%s')", 2, 1, (int) $_POST['btnSetFinish'], $_POST['notes_set']);
						$result = mysqli_query($conex, $query);
						if ($result) {
							echo "<p class='question'>PERIOD FINISHED SUCCESSFULLY!</p>";
							echo "<p><button type='button' value='SA' style='height: 30px;' onclick='mainDisplay(this)'>BACK</button></p>";
						} else {
							echo "<p class='error'>Problem trying to finish period.<br>Contact Administrator!</p>";
						}
					} elseif (isset($_POST['btnSetClose'])) {
						# Close Period.
						$query = sprintf("CALL usp_Review_Set('%d', '%d', '%d', '%s')", 2, 2, (int) $_POST['btnSetClose'], $_POST['notes_set']);
						$result = mysqli_query($conex, $query);
						if ($result) {
							echo "<p class='question'>PERIOD CLOSED SUCCESSFULLY!</p>";
							echo "<p><button type='button' value='SA' style='height: 30px;' onclick='mainDisplay(this)'>BACK</button></p>";
						} else {
							echo "<p class='error'>Problem trying to close period.<br>Contact Administrator!</p>";
						}
					} elseif (isset($_POST['btnSetCancel'])) {
						# Cancel Period.
						$query = sprintf("CALL usp_Review_Set('%d', '%d', '%d', '%s')", 2, 3, (int) $_POST['btnSetCancel'], $_POST['notes_set']);
						$result = mysqli_query($conex, $query);
						if (mysqli_affected_rows($conex) == 0) {
							echo "<p class='error'>Problem trying to cancel period.<br>Contact Administrator!</p>";
						} else {
							echo "<p class='question'>PERIOD CANCELLED SUCCESSFULLY!</p>";
							# Get list of appointments (datetime).
							$query = sprintf("CALL usp_Send_Email_Cancellation('%d', '%d', '%s', '%s')", 1, (int) $_POST['btnSetCancel'], '', '');
							$res1 = mysqli_query($conex, $query);
							
							if ($res1) {
								if (mysqli_num_rows($res1) > 0) {
									while ($row1 = mysqli_fetch_array($res1)) {
										$ap_datetime = $row1['get_datetime'];
										$c_id = $_SESSION['user_id'];
										$set_notes = $_POST['notes_set'];
										#New connection for this to work.
										include ('includes/db_config_email.php');
										# Cancel Appointment.
										$query = sprintf("CALL usp_Cancel_Appointment('%d', '%s', '%s', '%s')", 1, $c_id, $ap_datetime, $set_notes);
										$res3 = mysqli_query($email_conex, $query);
										if (mysqli_affected_rows($email_conex) > 0) {
											echo "<br><br><font size='2' color=#6CBB3C>Cancelling appointment on [" . date_format(date_create($ap_datetime), 'm/d/Y h:i A') . "] ... Done!</font>";
											# Send Email.
											$query = sprintf("CALL usp_Send_Email_Cancellation('%d', '%d', '%s', '%s')", 2, 0, $c_id, $ap_datetime);
											$res2 = mysqli_query($email_conex, $query);
											
											if ($res2) {
												if (mysqli_num_rows($res2) > 0) {
													$row2 = mysqli_fetch_array($res2);
													if (sendEmail($row2['c_email'], $row2['email'], $ap_datetime)) {
														echo "<br><font size='2' color=#6CBB3C>Sending Email to \"" . $row2['name'] . "\" [" . $row2['email'] . "] ... Done!</font>";
													} else {
														echo "<br><font size='2' color=red>Sending Email to Student... Failed! [Email Server]</font>";
													}
												} else {
													echo "<br><font size='2' color=red>Sending Email to Student... Failed! [No email found]</font>";
												}
												mysqli_free_result($res2);
											} else {
												echo "<br><font size='2' color=red>Sending Email to Student... Failed! [Connection Error]</font>";
											}

										} else {
											echo "<br><br><font size='2' color=red>Cancelling appointment on [" . date_format(date_create($ap_datetime), 'm/d/Y h:i A') . "] ... Failed!</font>";
										}
									}
									mysqli_close($email_conex);
								} else {
									echo "<p class='ad'>No Appointments found for this period. No email was sent!</p>";
								}
								mysqli_free_result($res1);
							} else {
								echo "<p class='error'>Problem trying to send email cancellations!<br>Contact Administrator!</p>";
							}
							echo "<p><button type='button' value='SA' style='height: 30px;' onclick='mainDisplay(this)'>BACK</button></p>";
						}
					}
					mysqli_close($conex);
				}

			} elseif (isset($_POST['features']) && $_POST['features'] == 'Manage-Appointments') {
				echo "<h1>Manage-Appointments Result</h1>";
				
				if (isset($_POST['btnApOut'])) {
					//echo "AP Out: " . $_POST['btnApOut']; #TEST

					$query = sprintf("UPDATE Students_Appointment SET checked_out = '%d', out_datetime = '%s', note = '%s' WHERE id = '%d'", 1, $system_datetime, $_POST['notes_manage'], (int)$_POST['btnApOut']);
					$result = mysqli_query($conex, $query);
					$query = sprintf("SELECT confirm_num FROM Students_Appointment WHERE id = '%d'", (int)$_POST['btnApOut']);
					$result = mysqli_query($conex, $query);
					if ($result) {
						if (mysqli_num_rows($result) > 0) {
							$row = mysqli_fetch_array($result);
							$query = sprintf("UPDATE Students_Check_In SET active = '%d' WHERE confirm_num = '%s'", 0, $row[0]);
							$result = mysqli_query($conex, $query);
							echo "<p class='result'>";
							echo "Appointment Check-Out: DONE<br>Check-In Deactivation: DONE.";
							echo "</p>";
						} else {
							echo "<p class='error'>Appointment Check-Out: DONE<br>Check-In Deactivation: FAILED.";
							echo "<br>Contact Administrator!</p>";
						}
						//mysqli_free_result($result);
					} else {
						echo "<p class='error'>Appointment Check-Out: DONE<br>Check-In Deactivation: FAILED.";
						echo "<br>Contact Administrator!</p>";
					}
					mysqli_close($conex);
				} elseif (isset($_POST['btnApCancel'])) {
					//echo "AP Cancel: " . $_POST['btnApCancel']; #TEST

					#Appointment ID.
					$ap_id = (int) $_POST['btnApCancel'];
					$query = sprintf("UPDATE Students_Appointment SET cancelled = '%d', note = '%s' WHERE id = '%d'", 1, $_POST['notes_manage'], $ap_id);
					$result = mysqli_query($conex, $query);

					echo "<p class='result'>";
					if (mysqli_affected_rows($conex) > 0) {
						echo "Cancelling Appointment... Done!";
						#Deactive Check-In
						$query = sprintf("SELECT SA.confirm_num, C.email, S.email, SA.set_datetime, S.first_name, S.last_name FROM Students_Appointment SA INNER JOIN Students S ON S.id = SA.student_id INNER JOIN Consultants C ON C.id = SA.consultant_id WHERE SA.id = '%d'", $ap_id);
						$result = mysqli_query($conex, $query);
						if ($result) {
							if (mysqli_num_rows($result) > 0) {
								#Deactive Check-In.
								$row = mysqli_fetch_array($result);
								$query = sprintf("UPDATE Students_Check_In SET active = '%d' WHERE confirm_num = '%s'", 0, $row[0]);
								$res_c = mysqli_query($conex, $query);

								if (mysqli_affected_rows($conex) > 0) {
									echo "<br>Deactivating Check-In... Done!";
								} else {
									echo "<br><font size='2' color=red>Deactivating Check-In... Failed! [No Check-In Found]</font>";
								}
								#Free the time for other????

								
							} else {
								echo "<br><font size='2' color=red>Deactivating Check-In... Failed! [No Appointment Found]</font>";
								echo "<br>Contact Administrator!</p>";
							}
							mysqli_free_result($result);
						} else {
							echo "<br><font size='2' color=red>Deactivating Check-In... Failed! [Connection Error]</font>";
							echo "<br>Contact Administrator!</p>";
						}
						# Send cancellation email.
						if (sendEmail($row[1], $row[2], $row[3])) {
							echo "<br><font size='2' color=#6CBB3C>Sending Email to \"" . $row[5] . ", " . $row[4] . "\" [" . $row[2] . "] ... Done!</font>";
						} else {
							echo "<br><font size='2' color=red>Sending Email to \"" . $row[5] . ", " . $row[4] . "\" [" . $row[2] . "] ... Failed!</font>";
						}
					} else {
						echo "<font size='2' color=red>Cancelling Appointment... Failed!</font>";
					}
					echo "</p>";

				} else {
					//echo "WI Out: " . $_POST['btnWiOut']; #TEST

					$query = sprintf("UPDATE Students_Check_In SET active = '%d' WHERE id = '%d'", 0, (int)$_POST['btnWiOut']);
					$result = mysqli_query($conex, $query);
					echo "<p class='result'>";
					echo "Check-In Deactivation: DONE.";
					echo "</p>";
				}

				echo "<p><button type='button' style='height: 30px;' onclick='return populateBoth()'>CONTINUE</button></p>";

			} elseif (isset($_POST['features']) && $_POST['features'] == 'View-Statistics') { 
				echo "<h1>View-Statistics Result</h1>";

				# WRITE VIEW-STATISTICS RESULT HERE

			}
			echo "</div>";
		}
	}

?>
<script type="text/javascript">
	function mainDisplay(btn) {
	    var x = document.getElementById("administrative_features");
	    var y = document.getElementById("administrative_result");
	    if (x.style.display === "block" && y.style.display === "none") {
	    	x.style.display = "none";
		    y.style.display = "block";
	    } else if (x.style.display === "none" && y.style.display === "block") {
	    	x.style.display = "block";
		    y.style.display = "none";
	    }
	    if (btn.value === "SA") {
	    	doSet();
	    } else if (btn.value === "MA") {
	    	doManage();
	    } else if (btn.value === "VS") {
	    	doStats();
	    }
	}
</script>
<div id="administrative_features"<?php if (!isset($_SESSION["user_id"]) || isset($_POST['features'])) echo ' style="display: none;"'; ?> >
	<h1>Administrative Features</h1>
	<form action="staff.php" method="post">	
		<p>
			<span class="input">
				<input type="radio" name="features" value="Set-Availability"<?php if (isset($_POST['features']) && ($_POST['features'] == 'Set-Availability')) echo ' checked="checked"'; ?> onclick="doSet()" /> Set-Availability
				<input type="radio" name="features" value="Manage-Appointments"<?php if (isset($_POST['features']) && ($_POST['features'] == 'Manage-Appointments')) echo ' checked="checked"'; ?> onclick="doManage()" />  Manage-Appointments
				<input type="radio" name="features" value="View-Statistics"<?php if (isset($_POST['features']) && ($_POST['features'] == 'View-Statistics')) echo ' checked="checked"'; ?> onclick="doStats()" />  View-Statistics
			</span>
			<a href='sign_out.php'>          <?php echo "Hello, " . $_SESSION['user_fname'] . "  "; ?><span class="button">SIGN-UP</span></a>
		</p>
		<script type="text/javascript">
			function doSet() {
			    var x = document.getElementById("show_set");
			    var y = document.getElementById("show_manage");
			    var w = document.getElementById("show_stats");
			    x.style.display = "block";
			    y.style.display = "none";
			    w.style.display = "none";
			}
			function doManage() {
			    var x = document.getElementById("show_set");
			    var y = document.getElementById("show_manage");
			    var w = document.getElementById("show_stats");
			    x.style.display = "none";
			    y.style.display = "block";
			    w.style.display = "none";
			}
			function doStats() {
			    var x = document.getElementById("show_set");
			    var y = document.getElementById("show_manage");
			    var w = document.getElementById("show_stats");
			    x.style.display = "none";
			    y.style.display = "none";
			    w.style.display = "block";
			}
		</script>
		<div id="show_set" style="display: none;">
			<div id="main_set" style="display: block; width: 100%;">
				<table width="100%">
					<tr>
						<td width="38%" style="border: 0px"><p><strong><u>SET NEW AVAILABILITY PERIOD:</u></strong></p></td>
						<td width="62%" style="border: 0px"><p><strong><u>ACTIVE PERIOD LIST:</u></strong></p></td>
					</tr>
					<tr>
						<td style="border: 0px">
							<p>Start Period:
							<br>
							<select name='start_month' style='height: 30px; width: 100px'>
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
							<select name='start_day' style='height: 30px; width: 65px'>
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
							<input type='text' name='start_year' value="<?php echo date('Y') ; ?>" style='height: 20px; width: 45px; text-align: center;'>
							</p>
							<p>End Period:
							<br>
							<select name='end_month' style='height: 30px; width: 100px'>
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
							<select name='end_day' style='height: 30px; width: 65px'>
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
							<input type='text' name='end_year' value="<?php echo date('Y') ; ?>" style='height: 20px; width: 45px; text-align: center;'>
							</p>
							<p>Start Time:             End Time:<br>
								<select name='start_time' style='height: 30px; width: 112px'>
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
								<select name='end_time' style='height: 30px; width: 112px'>
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
											<input type="checkbox" name="daysMO" value="MO"<?php if (isset($_POST['daysMO'])) echo " checked"; ?> /> Monday<br>
											<input type="checkbox" name="daysTU" value="TU"<?php if (isset($_POST['daysTU'])) echo " checked"; ?> /> Tuesday<br>
											<input type="checkbox" name="daysWE" value="WE"<?php if (isset($_POST['daysWE'])) echo " checked"; ?> /> Wednesday<br>
											<input type="checkbox" name="daysTH" value="TH"<?php if (isset($_POST['daysTH'])) echo " checked"; ?> /> Thursday<br>
											<input type="checkbox" name="daysFR" value="FR"<?php if (isset($_POST['daysFR'])) echo " checked"; ?> /> Friday
										</p>
									</td>
									<td valign="top" style="border: 0px">
										<p>Meeting Time:<br> <!-- Only title to Time from Duration -->
											<select name='duration' style='height: 30px; width: 100px'>
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
								$query = sprintf("SELECT * FROM Availability_Setting WHERE consultant_id = '%s' AND cancelled = '%d' AND closed = '%d' AND finished = '%d' ORDER BY id", $_SESSION["user_id"], 0, 0, 0);
								$result = mysqli_query($conex, $query);
								if ($result) {
									if (mysqli_num_rows($result) > 0) {
										echo "<p><table border = 1>";
										echo "<tr><th colspan='2'>Period<th colspan='2'>Time<th colspan='2'>Meeting<td colspan='3' rowspan='2'><b>FI</b>: Finish<br><b>CL</b>: Close<br><b>CA</b>: Cancel";
										echo "<tr><th>Start<th>End<th>Start<th>End<th>Days<th>Time";
										while ($row = mysqli_fetch_array($result)) {
											$id = $row['id'];
											$p_start = date_format(date_create($row['period_start']), 'm/d/Y');
											$p_end = date_format(date_create($row['period_end']), 'm/d/Y') ;
											$t_start = date_format(date_create($row['time_start']), 'H:i');
											$t_end = date_format(date_create($row['time_end']), 'H:i');
											$d = $row['days'];
											$dur = $row['duration'];
											$datetime_end = date_format(date_create($p_end . ' ' . $t_end), 'Y-m-d H:i');
											echo "<tr>";
											echo "<td>$p_start<td>$p_end<td>$t_start<td>$t_end<td style='font-size: 85%;'>$d<td style='text-align: center;'>$dur";
											if (date_create($datetime_end) > date_create($system_datetime)) {
												echo "<td style='padding: 0px; border: 0px;'><button type='button' name='btnSetFinish' value='" . $id . "' class='btnApp' onclick='return goReviewTablePeriod(this);'>FI</button>";
												echo "<td style='padding: 0px; border: 0px;'><button type='button' name='btnSetClose' value='" . $id . "' class='btnApp' onclick='return goReviewTablePeriod(this);'>CL</button>";
												echo "<td style='padding: 0px; border: 0px;'><button type='button' name='btnSetCancel' value='" . $id . "' class='btnApp' onclick='return goReviewTablePeriod(this);'>CA</button>";
											} else {
												echo "<td style='padding: 0px; border: 0px;'><button type='button' name='btnSetFinish' value='" . $id . "' class='btnApp' onclick='return goReviewTablePeriod(this);'>FI</button>";
												echo "<td style='padding: 0px; border: 0px;'><button type='button' name='btnSetClose' value='" . $id . "' class='btnApp_taken' onclick=''>CL</button>";
												echo "<td style='padding: 0px; border: 0px;'><button type='button' name='btnSetCancel' value='" . $id . "' class='btnApp_taken' onclick=''>CA</button>";
											}
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
				<p><input type="submit" name="btnSetAvailability" value="SET-AVAILABILITY" class="button" style="background-color: #f7dc6f; height: 30px;" /></p>				
			</div>
			<div id="review_set" style="display: none; width: 100%;"></div>
			<script>
				function goReviewTablePeriod(btn) {
				    var x = document.getElementById("main_set");
				    var y = document.getElementById("review_set");
				    x.style.display = "none";
				    y.style.display = "block";
				    
					var passBtnName = btn.name; // Finish, close, or cancel.
					var passBtnValue = btn.value; // ID value.

				    var htm = $.ajax({
				    type: "POST",
				    url: "review_set.php",
				    data: {nameOperation: passBtnName, nameId: passBtnValue},
				    async: false
				    }).responseText;

				    if (htm) {
				        $("#review_set").html(htm);
				        return true;
				    } else {
				        $("#review_set").html("<p class='error'>Problem trying to review operation!</p>");
				        return false;
				    }
				}
			</script>
			<script type="text/javascript">
				function goBackPeriods() {
				    var x = document.getElementById("main_set");
				    var y = document.getElementById("review_set");
				    if (x.style.display === "block" && y.style.display === "none") {
				    	x.style.display = "none";
					    y.style.display = "block";
				    } else if (x.style.display === "none" && y.style.display === "block") {
				    	x.style.display = "block";
					    y.style.display = "none";
				    }
				}
			</script>
		</div>
		<div id="show_manage" style="display: none;">
			<div id="main_manage" style="display: block; width: 100%;">
				<p>Filter Appointments By:                 Filter Walk-In By:
				<br>
				<select id="filter1" name='filter_appointment' style='height: 30px; width: 200px' onchange='return populateAppointments(this);'>
					<option value='' text=''>#Choose Filter</option>
					<option value='1'<?php if (isset($_POST['filter_appointment']) && $_POST['filter_appointment'] == "1") echo " selected"; ?> text='Active Appointments'>Active Appointments</option>
					<option value='2'<?php if (isset($_POST['filter_appointment']) && $_POST['filter_appointment'] == "2") echo " selected"; ?> text='Active Appointments (Checked-In on Top)'>Active Appointments (Checked-In on Top)</option>
					<!-- MORE FILTERS -->

				</select>
				<select id="filter2" name='filter_walk_in' style='height: 30px; width: 200px' onchange='return populateWalkIn(this);'>
					<option value='' text=''>#Choose Filter</option>
					<option value='1' text='Active Walk-In'>Active Walk-In</option>
					<!-- MORE FILTERS -->

				</select>
				</p>
				<div id="populate_appointments" style="width: 100%;"></div>
				<div id="populate_walk_in" style="width: 100%;"></div>
			</div>
			<div id="review_manage" style="display: none; width: 100%;"></div>
			<script>
				function populateAppointments(sel) {
					var passFilter = sel.options[sel.selectedIndex].text;
					var passOp = sel.value;
					var passConsultantId = "<?php echo $_SESSION['user_id'] ?>";

				    var htm = $.ajax({
				    type: "POST",
				    url: "populate_appointments.php",
				    data: {optionFilter: passFilter, filterOp: passOp, filterConsultantId: passConsultantId},
				    async: false
				    }).responseText;

				    if (htm) {
				        $("#populate_appointments").html(htm);
				        return true;
				    } else {
				        $("#populate_appointments").html("<p class='error'>Problem trying to get Appointment List!</p>");
				        return false;
				    }
				}
			</script>
			<script>
				function populateWalkIn(sel) {
					var passFilter = sel.options[sel.selectedIndex].text;
					var passOp = sel.value;
					var passConsultantId = "<?php echo $_SESSION['user_id'] ?>";

				    var htm = $.ajax({
				    type: "POST",
				    url: "populate_walk_in.php",
				    data: {optionFilter: passFilter, filterOp: passOp, filterConsultantId: passConsultantId},
				    async: false
				    }).responseText;

				    if (htm) {
				        $("#populate_walk_in").html(htm);
				        return true;
				    } else {
				        $("#populate_walk_in").html("<p class='error'>Problem trying to get Walk-In List!</p>");
				        return false;
				    }
				}
			</script>
			<script>
				function goReviewManage(btn) {
				    var x = document.getElementById("main_manage");
				    var y = document.getElementById("review_manage");
				    x.style.display = "none";
				    y.style.display = "block";
				    
					var passBtnName = btn.name; // Appointment or Walk-In
					var passBtnValue = btn.value; // ID value.

				    var htm = $.ajax({
				    type: "POST",
				    url: "review_manage.php",
				    data: {typeManage: passBtnName, typeId: passBtnValue},
				    async: false
				    }).responseText;

				    if (htm) {
				        $("#review_manage").html(htm);
				        return true;
				    } else {
				        $("#review_manage").html("<p class='error'>Problem trying to review operation!</p>");
				        return false;
				    }
				}
			</script>
			<script>
				function populateBoth() {
					var x = document.getElementById("administrative_features");
				    var y = document.getElementById("administrative_result");
				    var z = document.getElementById("show_manage");
				    x.style.display = "block";
					y.style.display = "none";
					z.style.display = "block";

					var passConsultantId = "<?php echo $_SESSION['user_id'] ?>";
					
					var sel1 = document.getElementById("filter1");
					var passFilter1 = sel1.options[sel1.selectedIndex].text;
					var passOp1 = sel1.value;
					
				    var htm1 = $.ajax({
				    type: "POST",
				    url: "populate_appointments.php",
				    data: {optionFilter: passFilter1, filterOp: passOp1, filterConsultantId: passConsultantId},
				    async: false
				    }).responseText;

				    if (htm1) {
				        $("#populate_appointments").html(htm1);
				        return true;
				    } else {
				        $("#populate_appointments").html("<p class='error'>Problem trying to get Appointment List!</p>");
				        return false;
				    }

				    /* #NOT WORKING.
					var sel2 = document.getElementById("filter2");
					var passFilter2 = sel2.options[sel2.selectedIndex].text;
					var passOp2 = sel2.value;

				    var htm2 = $.ajax({
				    type: "POST",
				    url: "populate_walk_in.php",
				    data: {optionFilter: passFilter2, filterOp: passOp2, filterConsultantId: passConsultantId},
				    async: false
				    }).responseText;

				    if (htm2) {
				        $("#populate_walk_in").html(htm2);
				        return true;
				    } else {
				        $("#populate_walk_in").html("<p class='error'>Problem trying to get Walk-In List!</p>");
				        return false;
				    }
				    */
				}
			</script>
			<script type="text/javascript">
				function goBack() {
				    var x = document.getElementById("main_manage");
				    var y = document.getElementById("review_manage");
				    if (x.style.display === "block" && y.style.display === "none") {
				    	x.style.display = "none";
					    y.style.display = "block";
				    } else if (x.style.display === "none" && y.style.display === "block") {
				    	x.style.display = "block";
					    y.style.display = "none";
				    }
				}
			</script>
			<!-- <center><p><img src='pictures/under_construction.png' alt='Under Construction Error' style='width: 400px; height: 150px;'></p></center> -->
		</div>
		<div id="show_stats" style="display: none;">
			
			<!-- SCRIPT FOR STATS -->

			<center><p><img src='pictures/under_construction.png' alt='Under Construction Error' style='width: 400px; height: 150px;'></p></center>
		</div>
	</form>
</div>

<?php include ('includes/footer.html'); ?>
