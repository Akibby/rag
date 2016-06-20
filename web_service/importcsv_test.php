<?php
// https://gist.github.com/jaywilliams/385876
include("csv_to_array.php");
// define connection parameters
$valkyrAddress = "192.168.1.101";
$valkyrUsername = "odin";
$valkyrPassword = "admin";
$valkyrDatabase = "ragnarok";
$connect = mysqli_connect($valkyrAddress, $valkyrUsername, $valkyrPassword, $valkyrDatabase) or die("Error " . mysqli_error($connect));

// path where CSV is stored
define('CSV_PATH','/../assets/');

// name of CSV
$csv_file = CSV_PATH . "test.csv";

function csv_to_db($filename='', $dbconnection='', $delimiter=',') {
	if (!file_exists($filename) || !is_readable($filename))
		return FALSE;

	$header = NULL;
	$data = array();
	if (($handle = fopen($filename, 'r')) !== FALSE) {
		while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
			if (!$header)
				$header = $row;
			else
				$data[] = array_combine($header, $row);
		}
		fclose($handle);
	}

	// select lawson columns from associative array into array
	$idlawson = array_column($data, "idLawsonRequisitionNo");
	if (!$idlawson) {
		echo "no lawson number found";
	}
	else {
        echo PHP_EOL;
        print_r($idlawson);
        echo PHP_EOL;

		// define the query we will use to insert into the corresponding table
		// $query_l = "INSERT INTO Lawson(idLawsonRequisitionNo) VALUES('"implode("), (",$idlawson) . "');";
        $base = 'INSERT INTO Lawson(idLawsonRequisitionNo) VALUES (';
        $values = implode("), (", $idlawson);
        $query_lawson = $base . $values . ");";
        print_r($query_lawson);
        echo PHP_EOL;

		// insert lawson data into mysql table
			if (mysqli_query($dbconnection, $query_lawson)) {
			 	echo "Successfully inserted " . mysqli_affected_rows($dbconnection) . " rows into Lawson" . PHP_EOL;

			}
			else {
				// find a way to catch and display all errors...
			 	echo "[LAWSON] Error occurred: " . mysqli_error($dbconnection) . PHP_EOL;
			}
	}

	//return $data;
}

function test($filename='', $dbconnection='', $delimiter=',') {
	if (!file_exists($filename) || !is_readable($filename))
		return FALSE;

	$header = NULL;
	$data = array();
	if (($handle = fopen($filename, 'r')) !== FALSE) {
		while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
			if (!$header)
				$header = $row;
			else
				$data[] = array_combine($header, $row);
		}
		fclose($handle);
	}

	// select po info from data into associative array
		$po = array();
		$po += array_slice($data[0],1,6);
		print_r($po);
		$base = 'INSERT INTO PurchaseOrder(idPurchaseOrder, poStatus, poNickname, poQuoteNo, poOrderDate, poReceivedDate, Lawson_idLawsonRequisitionNo) VALUES (';
		$idlawson = array_column($data, "idLawsonRequisitionNo");
		$lawson = "(select Lawson_idLawsonRequisitionNo from Lawson where idLawsonRequisitionNo = ";
		$law = $idlawson[0];
		$values = implode(", ", $po);
		$query_po = $base . $values . ", " . $lawson . $law . "));";
		print_r($query_po);
		echo PHP_EOL;

		$base = 'INSERT INTO Lawson(idLawsonRequisitionNo) VALUES (';
        $values = implode("), (", $idlawson);
        $query_lawson = $base . $values . ");";
        print_r($query_lawson);

		// insert po data into mysql table
			if (mysqli_query($dbconnection, $query_po)) {
			 	echo "Successfully inserted " . mysqli_affected_rows($dbconnection) . " rows into Lawson" . PHP_EOL;
			}
			else {
				// find a way to catch and display all errors...
			 	echo "[PO] Error occurred: " . mysqli_error($dbconnection) . PHP_EOL;
			}

	// return $data;
}

print_r(test($csv_file, $connect));


// ** prints long string of column names from device table
// $result = mysqli_query($connect, 'SELECT * FROM Device');
// while ($property = mysqli_fetch_field($result)) {
// 	echo $property->name;
// }

?>
