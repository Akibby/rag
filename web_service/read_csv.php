<?php
// https://gist.github.com/jaywilliams/385876
define('csv_path', "../assets/");
$csv = csv_path . "test.csv";

function csv_to_array($filename='', $delimiter=',') {
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
	return $data;
}

print_r(csv_to_array($csv));

?>
