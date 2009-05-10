#!/usr/bin/php
<?php
require_once('config.php');
define('USERDIR', DIR.'/'.strtolower(USER));

$files = array();
if ($handle = opendir(USERDIR)) {
    while (false !== ($file = readdir($handle))) {
        if (!is_dir($file) && $file != '.' && $file != '..' && substr($file, 0, 1) != '.' ) {
			$files[] = $file;
		}
	}
	closedir($handle);
}

$users = array();
$dates = array();
$data = array();

foreach($files as $thisFile) {
	$buffer = file_get_contents(USERDIR.'/'.$thisFile);
	$lines = explode("\n", $buffer);
	foreach ($lines as $user) {
		if (strlen($user) > 0 && !in_array($user, $users)) {
			$users[] = $user;
			$dates[] = substr($thisFile, 0, 8);
			$data[] = array('user' => $user, 'join' => substr($thisFile, 0, 8));
		}
	}
}

foreach ($data as $key => $row) {
	$user[$key]		= $row['user'];
	$join[$key]		= $row['join'];
	$left[$key]		= $row['left'];
}
array_multisort($user, SORT_ASC, SORT_STRING, $join, SORT_ASC, SORT_NUMERIC, $data);

// XML
$out = "<joins>\n";
foreach ($data as $thisData) {
	$out .= "\t<user name='".$thisData['user']."' date='".$thisData['join']."' />\n";
}
$out .= "</joins>\n";
file_put_contents('dates_'.strtolower(USER).'.xml', $out);
echo $out."\n";

// Plain text
$out = '';
foreach ($data as $thisEntry) {
	$out .= $thisEntry['user'].';'.$thisEntry['join'].';'.$thisEntry['left']."\n";
}
file_put_contents('dates_'.strtolower(USER).'.txt', $out);
echo $out."\n";
?>