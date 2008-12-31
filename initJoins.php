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
$entrys = array();

foreach($files as $thisFile) {
	$buffer = file_get_contents(USERDIR.'/'.$thisFile);
	$lines = explode("\n", $buffer);
	foreach ($lines as $user) {
		if (strlen($user) > 0 && !in_array($user, $users)) {
			$users[] = $user;
			$dates[] = substr($thisFile, 0, 8);
			$entrys[] = array('user' => $user, 'join' => substr($thisFile, 0, 8));
		}
	}
}

// XML
$out = "<joins>\n";
for ($i=0; $i < count($users); $i++) { 
	$out .= "\t<user name='".$users[$i]."' date='".$dates[$i]."' />\n";
}
$out .= "</joins>\n";
file_put_contents('joins_'.strtolower(USER).'.xml', $out);

// Plain text
$out = '';
foreach ($entrys as $thisEntry) {
	$out .= $thisEntry['user'].';'.$thisEntry['join'].';'.$thisEntry['left']."\n";
}
file_put_contents('dates_'.strtolower(USER).'.txt', $out);
?>