<?php #JORGE ESPADA
	
	if (!isset($_REQUEST['filterConsultantId'])) {
		echo "<h1>Forbidden</h1>";
		echo "<p style='font-size: 1.2em'>You don't have permission to access this page.</p>";
	} else {
		date_default_timezone_set('America/New_York');
		$system_date = date("Y-m-d");
		$system_time = date("H:i");
		$system_datetime = date("Y-m-d H:i");

		include ('includes/db_config.php');
		$option_filter = $_REQUEST['optionFilter'];
		$op = $_REQUEST['filterOp'];
		$consultant_id = $_REQUEST['filterConsultantId'];

		echo "<h2>Walk-In: \"$option_filter\"</h2>";
		$query = sprintf("CALL usp_Populate_Walk_In('%d','%s')", $op, $consultant_id);
	    $result = mysqli_query($conex, $query);
	    if ($result) {
	    	if (mysqli_num_rows($result) > 0) { 
				echo "<table border = 1>";
				echo "<tr><th>#<th style='display:none;'>WI<th>ID<th>Student<th style='display:none;'>Location<th>Reason<th>Date-Time<th>Type";
				$i = 0;
				while ($row = mysqli_fetch_array($result)) {
					$i += 1;
					$id = $row['id'];
					$s_id = $row['s_id'];
					$student = $row['student'];
					$location = $row['location'];
					$reason = $row['description'];
					$date_time = date_format(date_create($row['in_datetime']), 'm/d/Y h:i A');
					$date = date_format(date_create($row['in_datetime']), 'Y-m-d');
					$time = date_format(date_create($row['in_datetime']), 'H:i');
					$ch_type = $row['check_type'];
					echo "<tr>";
					if (date_create($date) < date_create($system_date)) {
						echo "<td style='background-color: #fadbd8; text-align: right; color: #a93226;'>$i
							<td style='display:none; background-color: #fadbd8; text-align: right; color: #a93226;'>$id
							<td style='background-color: #fadbd8; text-align: center; color: #a93226;'>$s_id
							<td style='background-color: #fadbd8; color: #a93226;'>$student
							<td style='display:none; background-color: #fadbd8; color: #a93226;'>$location
							<td style='background-color: #fadbd8; color: #a93226;'>$reason
							<td style='background-color: #fadbd8; text-align: center; color: #a93226;'>$date_time
							<td style='background-color: #fadbd8; text-align: center; color: #a93226;'>$ch_type
							<td style='border: 0px;'><button type='button' name='btnWiOut' value='" . $id . "' class='btnApp' onclick='return goReviewManage(this);'>Out</button>";
					} else {
						echo "<td style='background-color: #DAEA70; text-align: right;'>$i
						<td style='display:none; background-color: #DAEA70; text-align: right;'>$id
						<td style='background-color: #DAEA70; text-align: center;'>$s_id
						<td style='background-color: #DAEA70;'>$student
						<td style='display:none; background-color: #DAEA70;'>$location
						<td style='background-color: #DAEA70;'>$reason
						<td style='background-color: #DAEA70; text-align: center;'>$date_time
						<td style='background-color: #DAEA70; text-align: center;'>$ch_type
						<td style='border: 0px;'><button type='button' name='btnWiOut' value='" . $id . "' class='btnApp' onclick='return goReviewManage(this);'>Out</button>";
					}
				}
				echo "</table>";
	    	} else {
	    		echo "<br>### EMPTY LIST ###";
	    	}

	    	mysqli_free_result($result);
	    } else {
	    	echo "<p class='error'>Please make sure to select a Walk-In Filter!";
			echo "<br><br>* Contact Administrator to inform about any problem with filters.</p>";
	    }

		mysqli_close($conex);
	}

?>
