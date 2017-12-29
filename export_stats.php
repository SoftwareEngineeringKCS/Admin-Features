<?php #JORGE ESPADA
	
	date_default_timezone_set('America/New_York');

	if (!isset($_POST['export'])) {
		echo "<h1>Forbidden</h1>";
		echo "<p style='font-size: 1.2em'>You don't have permission to access this page.</p>";
	} else {
		$legend_1 = $_POST['legend_1'];
		$legend_2 = $_POST['legend_2'];
		$legend_3 = $_POST['legend_3'];
		$legend_4 = $_POST['legend_4'];
		$inLabels = $_POST['labels'];
		$labels = unserialize(base64_decode($inLabels));
		$inData = $_POST['data'];
		$data = unserialize(base64_decode($inData));

		$colHeader = "";
		$colHeader .= $legend_1 . "\n";
		$colHeader .= $legend_2 . "\n";
		$colHeader .= $legend_3 . "\n";
		$colHeader .= $legend_4 . "\n";
		$colHeader .= "\n";
		$colHeader .= "Description" . "\t" . "# Meetings";

		$setData = "";
		$i = 0;
		while ($i < count($labels)) {
			$rowData = '"' . $labels[$i] . '"' . "\t" . '"' . $data[$i] . '"';
			$setData .= $rowData . "\n";
			$i++;
		}

		header("Content-type: application/xls");
		header("Content-Disposition: attachment; filename=KCS_Stats.xls");
		 
		echo ucwords($colHeader). "\n" . $setData . "\n";
		
	}

?>