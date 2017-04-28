<?php

$dir = '/home/someone/camscan/';
$cams = file($dir.'log.txt');
$updated = array();
$admins = array(
	'someone@microsoft.com',
	'someone-else@google.com'
);

function checkCam($ip) {
	$retVal = false;
	if ($sock = @fsockopen($ip, 80, $errno, $errstr, 10)) {
		$retVal = true;
		fclose($sock);
	}
	return $retVal;
}

function getCam($ip) {
	$arr = file($dir.'log.txt');

	foreach($arr AS $cam) {
		$data = explode(',', $cam);
		if ($data[0] == $ip) {
			return intval(trim($data[2])) == 1 ? true : false;
		}
	}
}

foreach ($cams AS $i=>$c) {
	$a = explode(',', trim($c));
	$status = checkCam($a[0]);

	if ($status != getCam($a[0])) {
		$cams[$i] = $a[0].",".$a[1].",".($status == true ? 1 : 0);
		echo $a[0]." changed state to ".($status == true ? 'ONLINE' : 'OFFLINE')."\n";

		array_push($updated, $a[1]." (".$a[0].") ".($status == true ? 'ONLINE' : 'OFFLINE'));
	} else {
		$cams[$i] = trim($c); // Remove the New Line character from camera that is not changing status so we don't end up with extra line breaks when we write our changes to the log.txt file.
	} 
}

if ($fp = fopen($dir.'log.txt', 'w')) {
	fputs($fp, join("\n", $cams)); 
	fclose($fp);
}

if (count($updated) > 0) {
	foreach ($admins AS $addr) {
		mail($addr, 'Cameras Status Changed', join("\n", $updated));
	}
}

echo "\n\n".count($cams)." cameras scanned.\n";

?>
