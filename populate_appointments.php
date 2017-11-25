<?php #JORGE ESPADA
	
	date_default_timezone_set('America/New_York');
	$system_date = date("Y-m-d");
	$system_time = date("H:i");
	$system_datetime = date("Y-m-d H:i");

	include ('includes/db_config.php');
	$option_filter = $_REQUEST['optionFilter'];
	$op = $_REQUEST['filterOp'];
	$consultant_id = $_REQUEST['filterConsultantId'];

	echo "<h2>By-Appointment: \"$option_filter\"</h2>";
	$query = sprintf("CALL usp_Manage_Appointments('%d','%s')", $op, $consultant_id);
    $result = mysqli_query($conex, $query);
    if ($result) {
    	if (mysqli_num_rows($result) > 0) { 
			echo "<table border = 1>";
			echo "<tr><th>#<th>AP<th>ID<th>Student<th style='display:none;'>Location<th>Reason<th>Date-Time<th>In";
			$i = 0;
			while ($row = mysqli_fetch_array($result)) {
				$i += 1;
				$id = $row['id'];
				$s_id = $row['s_id'];
				$student = $row['student'];
				$location = $row['location'];
				$reason = $row['description'];
				$date_time = date_format(date_create($row['set_datetime']), 'm/d/Y h:i A');
				$date = date_format(date_create($row['set_datetime']), 'Y-m-d');
				$time = date_format(date_create($row['set_datetime']), 'H:i');
				$ch_in = $row['checked_in'];
				if ((int)$ch_in == 1) {
						$isHere = "YES";
						$classHere = "btnApp";
						$onclick = "return goReviewManage(this);";
					} else {
						$isHere = "NO";
						$classHere = "btnApp_taken";
						$onclick = "";
					}
				echo "<tr>";
				if (date_create($date) < date_create($system_date)) {
					echo "<td style='background-color: #fadbd8; text-align: right; color: #a93226;'>$i
						<td style='background-color: #fadbd8; text-align: right; color: #a93226;'>$id
						<td style='background-color: #fadbd8; text-align: center; color: #a93226;'>$s_id
						<td style='background-color: #fadbd8; color: #a93226;'>$student
						<td style='display:none; background-color: #fadbd8; color: #a93226;'>$location
						<td style='background-color: #fadbd8; color: #a93226;'>$reason
						<td style='background-color: #fadbd8; text-align: center; color: #a93226;'>$date_time
						<td style='background-color: #fadbd8; text-align: center; color: #a93226;'>$isHere
						<td style='border: 0px;'><button type='button' name='btnApOut' value='" . $id . "' class=$classHere onclick='$onclick'>Out</button>
						<td style='border: 0px;'><button type='button' name='btnApCancel' value='" . $id . "' class='btnApp' onclick='return goReviewManage(this);'>Cancel</button>";
				} elseif (date_create($date) == date_create($system_date) && date_create($time) < date_create($system_time) && $isHere == "NO") {
					echo "<td style='text-align: right; color: #a93226;'>$i
						<td style='text-align: right; color: #a93226;'>$id
						<td style='text-align: center; color: #a93226;'>$s_id
						<td style='color: #a93226;'>$student
						<td style='display:none; color: #a93226;'>$location
						<td style='color: #a93226;'>$reason
						<td style='text-align: center; color: #a93226;'>$date_time
						<td style='text-align: center; color: #a93226;'>$isHere
						<td style='border: 0px;'><button type='button' name='btnApOut' value='" . $id . "' class=$classHere onclick='$onclick'>Out</button>
						<td style='border: 0px;'><button type='button' name='btnApCancel' value='" . $id . "' class='btnApp' onclick='return goReviewManage(this);'>Cancel</button>";
				} else {
					if ((int)$ch_in == 1) {
						echo "<td style='background-color: #DAEA70; text-align: center;'>$i
						<td style='background-color: #DAEA70; text-align: right;'>$id
						<td style='background-color: #DAEA70; text-align: center;'>$s_id
						<td style='background-color: #DAEA70;'>$student
						<td style='display:none; background-color: #DAEA70;'>$location
						<td style='background-color: #DAEA70;'>$reason
						<td style='background-color: #DAEA70; text-align: center;'>$date_time
						<td style='background-color: #DAEA70; text-align: center;'>$isHere
						<td style='border: 0px;'><button type='button' name='btnApOut' value='" . $id . "' class=$classHere onclick='$onclick'>Out</button>
						<td style='border: 0px;'><button type='button' name='btnApCancel' value='" . $id . "' class='btnApp' onclick='return goReviewManage(this);'>Cancel</button>";
					} else {
						echo "<td align='right'>$i
							<td style='text-align: right;'>$id
							<td style='text-align: center;'>$s_id
							<td>$student
							<td style='display:none;'>$location
							<td>$reason
							<td>$date_time
							<td style='text-align: center;'>$isHere
							<td style='border: 0px;'><button type='button' name='btnApOut' value='" . $id . "' class=$classHere onclick='$onclick'>Out</button>
							<td style='border: 0px;'><button type='button' name='btnApCancel' value='" . $id . "' class='btnApp' onclick='return goReviewManage(this);'>Cancel</button>";
					}
				}
			}
			echo "</table>";
    	} else {
    		echo "<br>### EMPTY LIST ###";
    	}
    	mysqli_free_result($result);
    } else {
    	echo "<p class='error'>Please make sure to select an Appointment Filter!</p>";
    }

	mysqli_close($conex);

?>