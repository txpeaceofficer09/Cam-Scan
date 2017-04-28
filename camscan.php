<?php

$cams = file('/home/someone/camscan/log.txt'); // Get an array of all the cams from the log.txt file.
$updated = array('ONLINE'=>array(),'OFFLINE'=>array()); // Initialize an array to hold our cameras that have changed their status.
$admins = array(
	'someone@microsoft.com',
	'someone-else@google.com'
); // Set an array of people to get e-mailed about status changes on cameras.

// Check the current status of a camera.
function checkCam($ip) {
	$retVal = false; // Initialize $retVal with a default value.
	if ($sock = @fsockopen($ip, 80, $errno, $errstr, 10)) {
		$retVal = true; // Change $retVal because we successfully connected to the camera.
		fclose($sock); // Close the socket because we no longer need it open.
	}
	return $retVal; // return the value of $retVal.
}

// Get the last known status of the camera from our log.txt file.
function getCam($ip) {
	$arr = file('/home/someone/camscan/log.txt'); // Get the cam data for us to parse it.

	foreach($arr AS $cam) {
		$data = explode(',', $cam); // Separate the CSV data into variables we can work with.
		if ($data[0] == $ip) {
			return intval(trim($data[2])) == 1 ? true : false; // Return whether the cam was online or offline last time it was scanned.
		}
	}
}

// Iterate over all the cameras and scan them to see if they are online.
foreach ($cams AS $i=>$c) {
	$a = explode(',', trim($c));
	$status = checkCam($a[0]);

	if ($status != getCam($a[0])) {
		$cams[$i] = $a[0].",".$a[1].",".($status == true ? 1 : 0); // Update the camera state.
		echo $a[0]." changed state to ".($status == true ? 'ONLINE' : 'OFFLINE')."\n"; // Tell anyone who cares that this cam changed states.

		array_push($updated[$status], $a[1]." (".$a[0].") ".($status == true ? 'ONLINE' : 'OFFLINE')); // Add the camera to the list of cameras that changed states.
	} else {
		$cams[$i] = trim($c); // Remove the New Line character from camera that is not changing status so we don't end up with extra line breaks when we write our changes to the log.txt file.
	} 
}

// Open the log.txt file and write our changes to it.
if ($fp = fopen('/home/someone/camscan/log.txt', 'w')) {
	fputs($fp, join("\n", $cams)); // Write changes to the file.
	fclose($fp); // Close the file because we are done with it.
}

// Create an e-mail message with all the camera updates.
$msg = count($updated['ONLINE'])." camera(s) came online:\n\n" .
	join("\n", $updated['ONLINE'])."\n\n" .
	count($updated['OFFLINE'])." camera(s) went offline:\n\n" .
	join("\n", $updated['OFFLINE']);

// If one of our lists of cameras that changed states has any cameras in it then send an e-mail to each administrator telling them about the changes.
if (count($updated['ONLINE']) > 0 || count($updated['OFFLINE']) > 0) {
	foreach ($admins AS $addr) {
		mail($addr, 'Cameras Status Changed', $msg);
	}
}

echo "\n\n".count($cams)." cameras scanned.\n"; // Tell anyone who cares that we scanned X number of cameras.

?>
