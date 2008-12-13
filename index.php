<?php
/**
* Achtung unten noch Google Analytics anpassen oder rauslöschen
*/

setlocale(LC_TIME, "de_DE");
define('USER', 'AndiH');
define('DIR', realpath('.').'/'.strtolower(USER));
define('ORDER', 'older');

?>
<?= '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title><?= USER; ?>'s follower</title>
	<style>
	body {
		font-family: Helvetica, Arial, sans-serif;
	}
	h1 {
		margin-bottom: 0px;
	}
	h3 {
		margin-top: 0px;
	}
	.day {
		float:left; 
		width:50px; 
		background-color: #EFF5FB; 
		color: #81BEF7; 
		font-size: 20pt; 
		text-align: center;
	}
	span.cntAdd, span.cntDel {
		color: lightgray;
		font-weight: normal;
	}
	a {
		text-decoration: none;
		color: #2E9AFE;
	}
	a:hover {
		text-decoration: underline;
	}
	#location {
		color: gray;
	}
	</style>
	<script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
		var pageTracker = _gat._getTracker("");
		pageTracker._initData();
		pageTracker._trackPageview();
	</script>
</head>

<body>
	<img src="http://s3.amazonaws.com/twitter_production/profile_images/58356754/q618ffrimwhawer_jetable.com_21ae36f7_bigger.jpg" border="0" align="left" style="padding-right: 5px; padding-bottom: 10px;" width=73 height=73 />
	<h1><a href="http://twitter.com/<?= USER; ?>">@<?= USER; ?></a> / Jens</h1>
	<div id="location">Standort: <strong>Marburg</strong></div>
<?php

function parseDate($strTime) {
	$y = substr($strTime, 0, 4);
	$m = substr($strTime, 4, 2);
	$d = substr($strTime, 6, 2);
	
	return strtotime("$y-$m-$d 00:00:00");
}

$files = array();
if ($handle = opendir(DIR)) {
    while (false !== ($file = readdir($handle))) {
        if (!is_dir($file) && $file != '.' && $file != '..' && substr($file, 0, 1) != '.' ) {
			$files[] = $file;
		}
	}
	closedir($handle);
}

if (ORDER == 'older')
	rsort($files);
else
	sort($files);

$adds = array();
$dels = array();

$last = '';
for ($i = 0; $i < min(count($files), 14); $i++) {
	if ($i == 0) {
		$last = $files[$i];
		continue;
	}
	
	$file = $files[$i];
	
	if (ORDER == 'older') {
		$old		= explode("\n", file_get_contents(DIR.'/'.$file));
		$new		= explode("\n", file_get_contents(DIR.'/'.$last));
		$oldDate	= parseDate($last);
		$newDate	= parseDate($file);
	}
	else {
		$old		= explode("\n", file_get_contents(DIR.'/'.$last));
		$new		= explode("\n", file_get_contents(DIR.'/'.$file));
		$oldDate	= parseDate($file);
		$newDate	= parseDate($last);
	}
	
	$add = array_diff($new, $old);
	$del = array_diff($old, $new);
	
	foreach($add as $user) {
		if ($user == '') {
			$add = array();
			break;
		}
		break;
	}
	
	foreach($del as $user) {
		if ($user == '') {
			$del = array();
			break;
		}
	}
	
	$adds[] = count($add);
	$dels[] = count($del);
	$labels[] = strftime("%d.%m.%Y", $newDate);
	
	$last = $file;
?>
	<div style="clear: both;">
		<div class="day"><?= strftime("%d", $newDate); ?><span style="font-size: 9px;"><br /><?= strftime("%b %y", $newDate); ?></span></div>
		<div style="float:left; width:250px; padding-left: 5px;">
			<h3>Hinzugekommene: <? if (count($add) != 0): ?><span class="cntAdd"><?= count($add); ?></span><? endif; ?></h3>
			<ul>
<? foreach ($add as $user): ?>
				<li style="list-style-image:url(images/user_add.png);"><a href="http://twitter.com/<?= $user; ?>">@<?= $user; ?></a></li>
<? endforeach; ?>
<? if (count($add) == 0): ?>
				<li style="list-style-image:url(images/user_go.png); color: lightgray;">keine Veränderung</li>
<? endif;?>
			</ul>
		</div>
		<div style="float: left;">
			<h3>Gehende: <? if (count($del) != 0): ?><span class="cntDel"><?= count($del); ?></span><? endif; ?></h3>
			<ul>
<? foreach ($del as $user): ?>
				<li style="list-style-image:url(images/user_del.png);"><a href="http://twitter.com/<?= $user; ?>">@<?= $user; ?></a></li>
<? endforeach; ?>
<? if (count($del) == 0): ?>
				<li style="list-style-image:url(images/user_go.png); color: lightgray;">keine Veränderung</li>
<? endif; ?>
			</ul>
		</div>
<? } // for ?>
	</div>

<?php
for($i = count($adds)-1; $i >= 0; $i--) {
	$dataStr[] = $adds[$i] - $dels[$i];
	$labelStr[] = $labels[$i];
}
?>
<div style="clear:both; padding-top: 20px;">
<h2>Grafische Darstellung</h2>
<img src="http://chart.apis.google.com/chart?cht=lc&chs=600x100&chco=0077CC&chm=B,E6F2FA,0,0,0&chls=1,0,0&chd=t:<?= implode(',', $dataStr); ?>&chds=<?= min($dataStr)-1; ?>,<?= max($dataStr); ?>&chxt=x,y&chxl=0:|<?= $labelStr[0]; ?>||<?= $labelStr[count($labelStr)-1]; ?>|1:|<?= min($dataStr)-1; ?>|<?= max($dataStr); ?>" width="600" height="100" border="0" />
</div>
		
</body>
</html>
