<?php

// evil cache
header('cache-control: no-cache,no-store,must-revalidate'); 
header('pragma: no-cache'); 
header('expires: 0');

/* Error */
function error($title='ERROR', $title2='Something went wrong', $description='') {
	?><!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title><?=$title?></title>
		<meta name="robots" content="nofollow,noarchive" />
		<!-- META -->
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<!-- EVIL CACHE -->
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="pragma" content="no-cache" />
		<!-- STYLE -->
		<link type="text/css" rel="stylesheet" href="common.css" media="all" />
		<style>
body {
	background-image: url("static/brick-wall-bloody.png");
	color: #000;
	font-size: 90%;
}

.doc {
	background-color: #211A;
	color: #EEE;
	padding: 0.5em 0.2em;
}

a { color: #77F; }
a:hover { color: #F77; }
		</style>
	</head>
	<body dir="ltr" bgcolor="#F00" text="#000">
		<div id="upper" align="RIGHT">
			[<a href="javascript:void(0);" onclick="history.back();">Return</a>]
		</div>
		<h1><?=$title2?></h1>
		<?=$description?"<p class=\"doc\">$description</p>":'<!--NO DESCRIPTION-->'?>
	</body>
</html><?php

	exit;
}

/* MySQLi functions */

function HTM_sqltable($result, $fieldtl=[], $input='') {
	mysqli_data_seek($result, 0);
	mysqli_field_seek($result, 0);
	$htm = '<table class="n_sql n_table" border="1" cellspacing="0"><thead><tr>';
	if ($input) {
		$htm.= '<th></th>';
	}
	while ($field=mysqli_fetch_field($result)) {
		$htm.= '<th class="n_col n_col_'.$field->name.'"><nobr>'.
			($fieldtl[$field->name]??('<small>'.ucfirst($field->name).'</small>')).'</nobr></th>';
	}
	$htm.= '</tr></thead><tbody>';
	while ($ass=mysqli_fetch_assoc($result)) {
		$htm.= '<tr>';
		if ($input) {
			$htm.= '<td><input type="checkbox" name="'.$ass[$input].'" value="true" /></td>';
		}
		foreach ($ass as $key=>$val) {
			$htm.= "<td class=\"n_col n_col_$key\">$val</td>";
		}
		$htm.= '</tr>';
	}
	$htm.= '</tbody></table>';
	return $htm;
}

/* HTML functions */

function HTM_redirect($to, $time=0) {
	if($to=='back') {
		$to = $_SERVER['HTTP_REFERER']??'';
	}
	$tojs = $to==($_SERVER['HTTP_REFERER']??'') ? 'history.go(-1);' : "location.href=\"$to\";";
	?><!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>Redirecting...</title>
		<meta name="robots" content="nofollow,noarchive" />
		<!-- META -->
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<!-- EVIL CACHE -->
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="pragma" content="no-cache" />
		<!-- SCRIPT -->
		<meta http-equiv="refresh" content="<?=$time+1?>;URL=<?=$to?>" />
		<script>
setTimeout(function(){<?=$tojs?>}, <?=$time*1000?>);
		</script>
	</head>
	<body>
		Redirecting...
		<p>If your browser doesn't redirect for you, please click: <a href="<?=$to?>" onclick="event.preventDefault();<?=$tojs?>">Go</a></p>
	</body>
</html><?php
	exit;
}

?>