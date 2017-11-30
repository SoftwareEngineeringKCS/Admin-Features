<?php #JORGE ESPADA
	
	$nameOperation = $_REQUEST['nameOperation'];
	$nameId = $_REQUEST['nameId'];

	include ('includes/db_config.php');

	date_default_timezone_set('America/New_York');

	# Review:
	switch ($nameOperation) {
		case 'btnSetFinish':
			echo "<h2>Review Finish Period</h2>";
			$error_msg = "<p class='error'>Problem trying to finish period.<br>Contact Administrator!</p>";
			$question = "<br>Notes:<br><textarea name='notes_set' maxlength='250' rows='5' cols='40'></textarea><br><br><b>Do you want to Finish Period?</b>";
			break;
		case 'btnSetClose':
			echo "<h2>Review Close Period</h2>";
			$error_msg = "<p class='error'>Problem trying to close period.<br>Contact Administrator!</p>";
			$name_date = "set_datetime";
			$question = "<br>Notes:<br><textarea name='notes_set' maxlength='250' rows='5' cols='40'></textarea><br><br><b>Do you want to Close Period?</b>";
			break;
		case 'btnSetCancel':
			echo "<h2>Review Cancel Period</h2>";
			$error_msg = "<p class='error'>Problem trying to cancel period.<br>Contact Administrator!</p>";
			$question = "<br>Notes:<br><textarea name='notes_set' maxlength='250' rows='5' cols='40'></textarea><br><br><b>Do you want to Cancel Period?</b>";
			break;
	}

	# Show Data.
	$query = sprintf("CALL usp_Review_Set('%d', '%d', '%d', '%s')", 1, 0, (int)$nameId, "");
	$result = mysqli_query($conex, $query);
	if ($result) {
		if (mysqli_num_rows($result) > 0) { 
			$row = mysqli_fetch_array($result);
			echo "<p class='question'>";
			echo "Consultant: " . $row['consultant'] . " [" . $row['c_id'] . "]";
			echo "<br>Start Date/Time: " . $row['start_datetime'];
			echo "<br>End Date/Time: " . $row['end_datetime'];
			echo "<br>Meeting Days: " . $row['days'];
			echo "<br>Meeting Time: " . $row['duration'] . " minutes";
			echo $question . "<br><font size='2' color=red>WARNING: THIS ACTION CANNOT BE UNDONE!</font>";
			echo "</p>";
		} else {
			echo "<p class='ad'>Period not found!</p>";
		}
		mysqli_free_result($result);
	} else {
		echo $error_msg;
	}
	mysqli_close($conex);

?>

<p>
	<input type="hidden" name=<?php echo $nameOperation; ?> value=<?php echo $nameId; ?>>
	<button type='button' style='height: 30px;' onclick='goBackPeriods()'>BACK</button>
	<button type='submit' style='height: 30px;'>CONFIRM</button>
</p>
