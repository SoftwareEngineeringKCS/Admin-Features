<?php #JORGE ESPADA
	
	if (!isset($_REQUEST['typeManage'])) {
		echo "<h1>Forbidden</h1>";
		echo "<p style='font-size: 1.2em'>You don't have permission to access this page.</p>";
	} else {
		$getTypeManage = $_REQUEST['typeManage'];
		$getTypeId = $_REQUEST['typeId'];

		include ('includes/db_config.php');

		date_default_timezone_set('America/New_York');

		# Review:
		switch ($getTypeManage) {
			case 'btnApOut':
				echo "<h2>Review Checked-Out Appointment</h2>";
				$query = sprintf("CALL usp_Review_Manage('%d', '%d')", 1, (int)$getTypeId);
				$empty_msg = "<p class='ad'>Appointment not found!</p>";
				$error_msg = "<p class='error'>Problem trying to get appointment information.<br>Contact Administrator!</p>";
				$name_date = "set_datetime";
				$question = "<br>Notes:<br><textarea class='input' name='notes_manage' maxlength='250' rows='5' cols='40'></textarea><br><br><b>Do you want to Checked-Out Appointment?</b>";
				break;
			case 'btnApCancel':
				echo "<h2>Review Cancel Appointment</h2>";
				$query = sprintf("CALL usp_Review_Manage('%d', '%d')", 1, (int)$getTypeId);
				$empty_msg = "<p class='ad'>Appointment not found!</p>";
				$error_msg = "<p class='error'>Problem trying to get appointment information.<br>Contact Administrator!</p>";
				$name_date = "set_datetime";
				$question = "<br>Notes:<br><textarea class='input' name='notes_manage' maxlength='250' rows='5' cols='40'></textarea><br><br><b>Do you want to Cancel Appointment?</b>";
				break;
			case 'btnWiOut':
				echo "<h2>Review Checked-Out Walk-In</h2>";
				$query = sprintf("CALL usp_Review_Manage('%d', '%d')", 2, (int)$getTypeId);
				$empty_msg = "<p class='ad'>Walk-In not found!</p>";
				$error_msg = "<p class='error'>Problem trying to get Walk-In information.<br>Contact Administrator!</p>";
				$name_date = "in_datetime";
				$question = "<br><br><b>Do you want to Checked-Out Walk-In?</b>";
				break;
		}

		# Show Data.
		$result = mysqli_query($conex, $query);
		if ($result) {
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				echo "<p class='question'>";
				echo "Student: " . $row['student'] . " [" . $row['s_id'] . "]";
				echo "<br>Reason: " . $row['description'];
				echo "<br>Meeting: " . date_format(date_create($row[$name_date]), 'm/d/Y h:i A');
				echo $question . "<br><font size='2' color=red>WARNING: THIS ACTION CANNOT BE UNDONE!</font>";
				echo "</p>";
			} else {
				echo $empty_msg;
			}
			mysqli_free_result($result);
		} else {
			echo $error_msg;
		}
		mysqli_close($conex);
	}

?>

<div <?php if (!isset($_REQUEST['typeManage'])) echo ' style="display: none;"'; ?>>
	<p>
		<input type="hidden" name=<?php echo $getTypeManage; ?> value=<?php echo $getTypeId; ?>>
		<button type='button' style='height: 30px;' onclick='goBack()'>BACK</button>
		<button type='submit' style='height: 30px;'>CONFIRM</button>
	</p>
</div>
