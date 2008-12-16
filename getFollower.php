#!/usr/bin/php
<?php
require_once('config.php');

define('USERDIR', DIR.'/'.strtolower(USER));

if (!file_exists(USERDIR)) {
	mkdir(USERDIR, 0755);
}

$filename = USERDIR.'/'.strftime("%Y%m%d").'_'.strtolower(USER).".txt";
if (file_exists($filename)) {
	exit($filename.' already exists.'."\n");
}

// Twitter
define('USER_URL', 'http://twitter.com/users/show/'.USER.'.xml');
define('FOLLOWER_URL', 'http://twitter.com/statuses/followers.xml');
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, USER_URL);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POST, 0);
curl_setopt($curl, CURLOPT_USERPWD, USER.':'.PASSWD);

function parse($xml) {
	$users = array();

	foreach($xml->user as $user) {
		$out['id']	= (int)$user->id;
		$out['screen_name'] = (string)$user->screen_name;
		$out['name'] = (string)$user->name;
		$out['follower'] = (int)$user->followers_count;

		$users[] = array('datetime' => DATETIME, 'user' => $out);
	}
	
	return $users;
}

function printList($arr) {
	if (count($arr) == 0)
		return;

	foreach($arr as $user)
		$o[] = strtolower($user['user']['screen_name']);
		sort($o);

	foreach($o as $user)
		$out .= $user.", ";

	echo "(".count($arr)."): " . substr($out, 0, strlen($out)-2)."\n";
}

#curl_setopt($curl, CURLOPT_POSTFIELDS, "status=".$msg);
$buffer = curl_exec($curl);
$xml = simplexml_load_string($buffer);

if (strlen($buffer) < 10) {
	echo "User-Data downloaded from Twitter was too short, so the local copy, if it exists, isn't replaced.\n";
} else {
	file_put_contents(DIR.'/'.strtolower(USER).'.xml', $buffer);
}

$follower = $xml->followers_count;
$friends  = $xml->friends_count;
$updates  = $xml->statuses_count;
$created  = strtotime($xml->created_at);
$days	  = floor((mktime()-$created)/(60*60*24));
$pages 	  = ceil($follower / 100);
$score	  = ($follower / $friends) * 1 / ($updates / $days);

$arrFollower = array();
for($i = 1; $i <= $pages; $i++) {
	curl_setopt($curl, CURLOPT_URL, FOLLOWER_URL.'?page='.$i);
	$xml = simplexml_load_string(curl_exec($curl));
	
	$append = parse($xml);
	$arrFollower = array_merge($arrFollower, $append);
}

curl_close($curl);

// Save
foreach($arrFollower as $user)
	$o[] = strtolower($user['user']['screen_name']);

if (count($o) < 1) {
	exit("No followers were found. That's unlikey so that no file was created.\n");
}

sort($o);

$out = implode("\n", $o);

file_put_contents($filename, $out);
echo $out;

#printList($arrFollower);
?>
