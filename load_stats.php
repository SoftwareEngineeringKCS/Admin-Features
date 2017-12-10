<?php #JORGE ESPADA
	
	date_default_timezone_set('America/New_York');
	include ('includes/db_config.php');

	$check_data = FALSE;

	$inStart = $_REQUEST['inStart'];
	$inEnd = $_REQUEST['inEnd'];
	$inWhatVal = (int)$_REQUEST['inWhatVal'];
	$inWhatText = $_REQUEST['inWhatText'];
	$inByVal = (int)$_REQUEST['inByVal'];
	$inByText = $_REQUEST['inByText'];
	if ($_REQUEST['inAll'] == 'false') {
		$inAll = 0;
	} else {
		$inAll = 1;
	}
	$inConsultantId = $_REQUEST['inConsultantId'];

	# Validate data.
	$vInStart = date_parse($inStart);
	$vInEnd = date_parse($inEnd);
	if (checkdate($vInStart['month'], $vInStart['day'], $vInStart['year']) && checkdate($vInEnd['month'], $vInEnd['day'], $vInEnd['year']) && $inWhatVal > 0 && $inByVal > 0) {
		
		echo "<p class='result' style='text-align: center'>";
		if ($inAll == 0) {
			$query = sprintf("SELECT CONCAT(last_name, ', ', first_name) FROM Consultants WHERE id = '%s'", $inConsultantId);
			$stats_res1 = mysqli_query($conex, $query);
			if ($stats_res1) {
				if (mysqli_num_rows($stats_res1) > 0) {
					$r = mysqli_fetch_array($stats_res1);
					echo "<b>Consultants</b>: " . $r[0] . " | ";
				} else {
					echo "<b>Consultants</b>: Current | ";		
				}

				mysqli_free_result($stats_res1);
			} else {
				echo "<b>Consultants</b>: Current | ";	
			}
		} else {
			echo "<b>Consultants</b>: All | ";
		}
		echo "<b>Range</b>: " . date_format(date_create($inStart), 'M j, Y') . " - " . date_format(date_create($inEnd), 'M j, Y');
		echo "<br><b>What you got</b>: " . $inWhatText . " | ";
		echo "<b>Filtered by</b>: " . $inByText;
		echo "</p>";

		$query = sprintf("CALL usp_Load_Stats('%d', '%s', '%s', '%s', '%d', '%d')", $inAll, $inConsultantId, $inStart, $inEnd, $inWhatVal, $inByVal);
		$stats_res2 = mysqli_query($conex, $query);
		if ($stats_res2) {
			$labels = array();
			$data = array();
			$bground = array();
			$bcolor = array();
			while ($row = mysqli_fetch_assoc($stats_res2)) {
				for ($i=0; $i < (count($row)-1) ; $i++) { 
		 			array_push($labels, $row['label']);
		 			array_push($data, $row['num']);
		 			array_push($bground, 'rgba(54, 162, 235, 0.2)');
		 			array_push($bcolor, 'rgba(54, 162, 235, 1)');
				}
			}
			
			$check_data = TRUE;
			mysqli_free_result($stats_res2);
		} else {
			echo "<p class='error'>Loading Data... Failed! [Connection Error]";
			echo "<br>Contact Administrator!</p>";
			echo "<p><a href='staff.php'>TRY AGAIN</a></p>";
		}

	} else {
		echo "<h2>The following fields are invalid!</h2>";
		echo "<p class='error'>";
		if (!checkdate($vInStart['month'], $vInStart['day'], $vInStart['year'])) echo "\"Start Date\", ";
		if (!checkdate($vInEnd['month'], $vInEnd['day'], $vInEnd['year'])) echo "\"End Date\", ";
		if (!$inWhatVal > 0) echo "\"What you get\", ";
		if (!$inByVal > 0) echo "\"Filter by\"";
		echo "</p>";
	}

	mysqli_close($conex);
?>

<br>
<canvas id="myChart" width="400" height="200" <?php if ($check_data == FALSE) echo "style='display: none'"; ?>></canvas>
<script>
	var getLabels = new Array();
	<?php foreach($labels as $val){ ?>
        getLabels.push('<?php echo $val; ?>');
    <?php } ?>
    var getData = new Array();
	<?php foreach($data as $val){ ?>
        getData.push('<?php echo $val; ?>');
    <?php } ?>
    var getBground = new Array();
	<?php foreach($bground as $val){ ?>
        getBground.push('<?php echo $val; ?>');
    <?php } ?>
    var getBcolor = new Array();
	<?php foreach($bcolor as $val){ ?>
        getBcolor.push('<?php echo $val; ?>');
    <?php } ?>

	var ctx = document.getElementById("myChart");
	var myChart = new Chart(ctx, {
	    type: 'bar',
	    data: {
	        labels: getLabels,
	        datasets: [{
	            label: '# of Meetings',
	            data: getData,
	            backgroundColor: getBground,
	            borderColor: getBcolor,
	            borderWidth: 1
	        }]
	    },
	    options: {
	        scales: {
	            yAxes: [{
	                ticks: {
	                    beginAtZero:true
	                }
	            }]
	        }
	    }
	});
</script>