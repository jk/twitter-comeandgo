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

foreach($files as $thisFile) {
	$buffer = file_get_contents(USERDIR.'/'.$thisFile);
	$lines = explode("\n", $buffer);
	foreach ($lines as $user) {
		if (strlen($user) > 0 && !in_array($user, $users)) {
			$users[] = $user;
			$dates[] = substr($thisFile, 0, 8);
		}
	}
}

$out = "<joins>\n";
for ($i=0; $i < count($users); $i++) { 
	$out .= "\t<user name='"$users[$i]."' date='".$dates[$i]."' />\n";
}
$out .= '</joins>'
file_put_contents('joins_'.strtolower(USERNAME).'.xml');
?>