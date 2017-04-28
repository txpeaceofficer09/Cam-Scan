<?php

$cams = file('/home/someone/camscan/log.txt');
$stats = array('ONLINE'=>array(),'OFFLINE'=>array());

echo " IP            \t\t| NAME                         \t\t\t| STATUS\n";
echo "=====================================================================================\n";

foreach ($cams AS $cam) {
	$data = explode(',', trim($cam));

	while (strlen($data[1]) < 30) {
		$data[1] .= ' ';
	}

	$status = $data[2] == 1 ? 'ONLINE' : 'OFFLINE';

	echo $data[0]."\t\t| ".$data[1]."\t\t| ".$status."\n";

	array_push($stats[$status], $data[0]);
}

echo "\n".count($stats['ONLINE'])." cameras online, ".count($stats['OFFLINE'])." cameras offline, ".count($cams)." total cameras.\n";

?>
