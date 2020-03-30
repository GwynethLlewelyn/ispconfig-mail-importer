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
	die("Missing filename; run with 'php mail_forward_add.php filename.csv" . PHP_EOL);
}



require 'soap_config.php';

// setup the SOAP client which will communicate with ISPConfig 3
$client = new SoapClient(null, array('location' => $soap_location,
        'uri'      => $soap_uri,
        'trace' => 1,
        'exceptions' => 1));

$row = 1;
if (($handle = fopen($argv[1] "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        echo "<p> $num fields in line $row: <br /></p>\n";
        $row++;
        for ($c=0; $c < $num; $c++) {
            echo $data[$c] . "<br />\n";

			try {
			    if($session_id = $client->login($username, $password)) {
			        echo 'Logged successfull. Session ID:'.$session_id.'<br />';
			    }
			
			    //* Set the function parameters.
			    $client_id = 1;
			    $params = array(
			        'server_id' => 1,
			        'source' => 'hallo@test.int',
			        'destination' => 'ciao@test.int',
			        'type' => 'forward',
			        'active' => 'y'
			    );
			
			    $affected_rows = $client->mail_forward_add($session_id, $client_id, $params);
			
			    echo "Forwarding ID: ".$affected_rows."<br>";
			
			    if($client->logout($session_id)) {
			        echo 'Logged out.<br />';
			    }
			
			
			} catch (SoapFault $e) {
			    echo $client->__getLastResponse();
			    die('SOAP Error: '.$e->getMessage());
			}
			        }
    }
    fclose($handle);
}


?>

