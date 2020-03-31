<?php
// Blatantly copied from the ISPConfig 3 sources and adapted by Gwyneth Llewelyn (20200330)

if (PHP_SAPI !== 'cli') {
?>
<!DOCTYPE html>

<html>
<head>
	<title>Oops</title>
</head>

<body>
	<h1>Oops!</h1>
	Sorry, this can't be run from the web (for now)"
</body>
</html>
<?php
	exit;
}

if ($argc < 2) {
	die("Missing filename; run with 'php " . $argv[0] . " filename.csv'" . PHP_EOL);
}

require 'soap_config.php';

// setup the SOAP client which will communicate with ISPConfig 3
$client = new SoapClient(null, array('location' => $soap_location,
		'uri'	   => $soap_uri,
		'trace' => 1,
		'exceptions' => 1));

$row = 1;
if (($handle = fopen($argv[1], "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$num = count($data);
		echo "$num fields in line $row: " . print_r($data, true) . PHP_EOL;
		$row++;

		try {
			if($session_id = $client->login($username, $password)) {
				echo 'Logged in successfully. Session ID: ' . $session_id . PHP_EOL;
			}
		
			//* Set the function parameters.
			$client_id = 1;
			$params = array(
				'server_id' => is_numeric($data[0]) ? trim($data[0]) : 1,
				'source' => is_numeric($data[0]) ? (filter_var(trim($data[1]), FILTER_VALIDATE_EMAIL) ?? 'invalid') : (filter_var(trim($data[0]), FILTER_VALIDATE_EMAIL) ?? 'invalid'),
				'destination' => is_numeric($data[0]) ? (trim($data[2]) ?? 'invalid') : (trim($data[1]) ?? 'invalid'),
				'type' => $data[3] ?? 'forward',
				'active' => $data[4] ?? 'y'
			);

			$affected_rows = $client->mail_forward_add($session_id, $client_id, $params);
			echo "Forwarding ID: " . $affected_rows . PHP_EOL;

			// var_dump($params);
		
			if($client->logout($session_id)) {
				echo 'Logged out.' . PHP_EOL;
			}

			} catch (SoapFault $e) {
				echo $client->__getLastResponse();
				die('SOAP Error: ' . $e->getMessage() . PHP_EOL);
			}
		}
	}
fclose($handle);
?>

