<?php
require_once('config.inc.php');
require_once('lib_common.inc.php');

@session_start();

$key = $_GET['key']??'';

if (!$key && !($_SERVER['REQUEST_TIME']-($_SESSION['lasttime']??0))) {
	error('ERROR', 'ERROR: Requesting too fast.', '
You can apply for an API key <a href="javascript:alert(\'requestkey@kolyma.org\');">here</a>.<br />
<span id="a"></span>
<script>
setTimeout(function(){
	document.getElementById("a").innerHTML = \'<a href="javascript:location.reload();">Please refresh the page.</a>\';
}, 1000);
</script>');
}
$_SESSION['lasttime'] = $_SERVER['REQUEST_TIME'];

$conn = new mysqli(SQLHOST, SQLUSER, SQLPASS, SQLDB) or die('MySQLi ERROR');
$stmt = $conn->stmt_init();

$conn->query("CREATE TABLE IF NOT EXISTS ".SQLTABLE_SPAM." (
	`id` INT NOT NULL AUTO_INCREMENT,
	`time` INT,
	`spam` TINYTEXT,
	`content` TEXT,
	`notes` TEXT,
	`addr` TINYTEXT,
	`status` TINYTEXT,
	PRIMARY KEY(`id`))") or die($conn->error);

$check = htmlspecialchars($_REQUEST['c']??'');
$safeq = htmlspecialchars($_GET['q']??'');
$safespam = htmlspecialchars($_GET['spam']??'');
$verifonly = $_GET['verifonly']??''=='on';

$claus = " WHERE (content LIKE '%$safeq%'";
if ($check) $claus .= " AND '$check' REGEXP content";
if ($safespam && ($safespam != 'ALL')) $claus .= " AND spam LIKE '".$safespam."'";
if ($verifonly) $claus .= " AND status LIKE 'verified'";
$claus .= ") ORDER BY id DESC";

if ($key) {
	if (!in_array($key, $user_keys)) {
		error('ERROR', 'ERROR: Bad Key', 'Access to the Kolyma NET spam API is provided per user basis.<br />
You can apply for an API key <a href="javascript:alert(\'requestkey@kolyma.org\');">here</a>.');
	}

	// good!
	$result = SQL_result($claus);
	if ($result) {
		header('Content-Type:text/plain');
		while ($row = $result->fetch_array()) {
			echo $row["content"]."\r\n";
		}
	}
	exit;
}

$result = SQL_result($claus." LIMIT ".PAGE_DEF." OFFSET ".strval(intval($_GET["p"]??0)*PAGE_DEF));

// interface
$title = 'Kolyma Network Anti-Spam Database';
?><!DOCTYPE html>
<html>
	<head>
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">

		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title><?=$title?></title>
		<meta name="robots" content="nofollow,noarchive" />
		<!-- META -->
		<!-- EVIL CACHE -->
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="pragma" content="no-cache" />
		<!-- STYLE -->
		<link type="text/css" rel="stylesheet" href="common.css" media="all" />
		<style>
body {
	background-image: url(data:image/gif;base64,R0lGODdhPAA8AJkAANCznMWtmbOektzApiwAAAAAPAA8AAAC/5SPqcvtCJ6c1IUAsgZ48wuG4qV1njYMZxmkbjYGQpipJeDmuo7zdYoTRSwiRGuFg3lio4HpswF9SJzhobpMSXsZAVQoS6KcpoH3MsPIZsAtVWAbDoVYziuZ3nhhnavWVASVFkYVNdbixKbH0mXQQ8I0FdERVnOi4tXWcqFVZXKAqAbJNEoTdUT28vSlZvUoFRmpxIdh87LXw1Z4cAc5wzFIulIbt+FS6MELVBJsMPK7utqTAmfrbAPoqAlCVknaOkWsclej7VzlRyYJAhc+slTllDNFbZBIq+10I3WGJks77cc4bCSgAcsUp1SESd9+hRv4Q9wiT/aWwWIHMEQaXP/GyNn52EEfjIrYoGGkpVHaGEuF2kTZiFASvm8qp+2YR4xlophqHEKpcwlFOWjO0IB6cGRQMBlMm86oAFXBnzVRHcwbs2xFDkQ8lt28aSzMPHWFCHHNKkaDo44etk5LM7DerFJ/6hSTM6xSWrUkgGDieOIIl46IgNJxl7JwrnY34tzViuQiw2c1+k662wmoVhqC3yD29YOPLnhIbqBE9hOl0tBmLht6VDqy5NPPOKmQYabFRko+I7OAtEQYoTZeTAaLPQslI3d8RHPWzXiQpc3DlnsD468Wy+g1p58S9WTL7yItG5E5DgPtxOa/v7u5sgSOP62ARWr+GFimUcEsurX/wraQcslVFxgVIdhSgg243GZfW7EBAo+BCwqFW3L+hSYGf0pYhAsT9ghESFo7CHRXNIq5ch86cCxw0FI0ZXdOEQlQtQCNMjqjwIvi6YgaSV/9qENIQA75VXCzxdBIgJ005NBF2rBz23MxKKVMN/14otYezXyYR4bAuccOfFFK80lZfWDAESAGAhQOAlvpFU1yKUmnJikuUjXNc5J0GSZT0iUE3gf9+IejRwO+duYay+3DzkWBIPSYkZHYc1h7NLGB0IEjPahHP0Zl9A4TrW2TJVmBYNTneJfSkGmKoi1ayjmqIjlZcaGwFxxtbjB1ymd2ihVprgaWEqqHUzZajSYFKYq3J7FgZneqn1zAgtxIomQEqilTFjcYcibimlodUjrKrXqyOWqZFAUAADs=);
}
#c_legend, #adminbar {
	float: right;
	line-height: 1.5em;
}
#postform, #infobox {
	background-color: #CFC;
}
#spamlists, #pager {
	background-color: #EFEFEF;
}
textarea {
	font-size: 1.5em;
}
#postform summary {
	cursor: pointer;
	font-family: Tahoma, Verdana, Arial;
}
#postform summary:focus {
	outline: none;
}
#chaimg {
	vertical-align: bottom;
}
			table {
	border-style: solid;
	border-width: 1px;
	border-color: #000;
}
input[name="fcontent"], input[name="icontent"] {
	display: block;
}
.spamfile {
	max-height: 100px;
}
		</style>
		<!-- SCRIPT -->
		<script>
function updateinput(value) {
	var c = document.postform.content;
	var f = document.postform.fcontent;
	var i = document.postform.icontent;
	if (value.search(/_IMG$/)!=-1) {
		c.style.display = i.style.display = 'none';
		c.value = i.value = f.style.display = '';
	} else if (value.search(/_IP$/)!=-1) {
		c.style.display = f.style.display = 'none';
		c.value = f.value = i.style.display = '';
	} else if (value.search(/_TXT$/)!=-1) {
		f.style.display = i.style.display = 'none';
		f.value = i.value = c.style.display = '';
	}
}
function spamjs(value) {
	for (var i=0; i<document.forms.length; i++) {
		with (document.forms[i]) {
			if (typeof(spam)!='undefined') {
				for (var j=0; j<spam.options.length; j++) {
					if (spam.options[j].value==value) { spam.value=value; }
				}
			}
		}
	}

	updateinput(value);
}
		</script>
	</head>
	<body dir="ltr" bgcolor="#C5AD99" onload="updateinput(document.postform.spam.value);">
		<?=TOP_TXT?>
		<table align="CENTER" width="95%" border="1" cellpadding="5" cellspacing="7" id="postform"><tbody><tr><td>
			<form align="RIGHT" id="adminbar" action="admin.php" method="POST">
				<?php if (in_array($_SESSION['adminpass']??'', $ADMIN)) { ?>
				<button type="submit" name="action" value="logout" >Logout</button>
				<?php } else { ?>
				<label>ADMIN:<input type="password" name="pwd" value="" /></label><button type="submit" name="action" value="login">Login</button>
				<?php } ?>
			</form>
			<form action="user.php" method="POST" name="postform" enctype="multipart/form-data">
				<?php if (ALLOW_USER) { ?>
				<summary><font size="+1"><b>Spam Form</b></font></summary>
					<a href="//www.kolyma.org" target="_blank"><img align="RIGHT" src="data:image/png;base64,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" border="1" /></a>
					<blockquote>
						<label>Spam:<?=HTM_cansel()?></label><button type="submit">Submit</button><br />
						<input type="file" name="fcontent" value="" />
						<input type="text" name="icontent" value="" placeholder="IP pattern" />
						<textarea name="content" cols="30" rows="4"></textarea><textarea name="notes" cols="48" rows="4" placeholder="Notes"></textarea><br />
						<?php if (!in_array($_SESSION['adminpass']??'', $ADMIN)) { ?>
						<script src="https://sys.kolyma.org/kaptcha/kaptcha.js"></script>
						<noscript>
							<input type="hidden" name="_KAPTCHA">
							<input type="hidden" name="_KAPTCHA_NOJS">
							<iframe src="https://sys.kolyma.org/kaptcha/kaptcha.php?nojs" style="border:none;width:400px;height:150px"></iframe><br>
							<input type="text" name="_KAPTCHA_KEY" placeholder="Paste here"><br>
						</noscript>
						<?php } ?>
						<u><a href="https://blog.kolyma.org/fighting-spam">Read more about how KolymaNET fights spam.</a></u>
						<center><img border="1" src="https://spam.kolyma.org/spamad.gif"></center>
					</blockquote>
			<?php } else { echo '<font color="#F00"><b>User spam submissions are disabled.</b></font>'; } ?>
			</form>
		</td></tr></tbody></table>
		<br clear="ALL" />
		<table align="CENTER" width="95%" border="1" cellpadding="2" id="spamlists">
			<thead>
				<tr><td colspan="4">
					<div align="RIGHT" id="c_legend">
						<label><table border="1" cellspacing="0" cellpadding="0" align="LEFT"><tbody><tr><td width="14" height="14" bgcolor="#FFFFAF"></td></tr></tbody></table>
						&nbsp;Pending</label>
						<br clear="ALL" />
						<label><table border="1" cellspacing="0" cellpadding="0" align="LEFT"><tbody><tr><td width="14" height="14" bgcolor="#AFFFAF"></td></tr></tbody></table>
						&nbsp;Verified</label>
					</div>
					<form action="spam.php" method="GET">
						<label>Search:<input type="search" name="q" value="<?=$safeq?>" /></label><?=HTM_cansel(true)?><button type="submit">Submit</button><br />
						<label><input type="checkbox" name="verifonly" value="on"<?=$verifonly?' checked="checked"':''?> />Verified only</label>
					</form>
				</td></tr>
				<tr><th width="5%">ID</th><th width="10%">Spam</th><th>Content</th><th>Notes</th></tr>
			</thead>
			<tbody>
				<?php
if ($result->num_rows) {
	while ($row = $result->fetch_array()) {
		$spam = $row["spam"];
		$notes = $row["notes"];
		$id = $row["id"];
		$class = $row["status"]=='verified' ? ' class="verified"' : ' class="pending"';
		$bgcol = $row["status"]=='verified' ? ' bgcolor="#AFFFAF"' : ' bgcolor="#FFFFAF"';
		$content = htmlspecialchars($row["content"]);
		$admin = '';
		if (in_array($_SESSION['adminpass']??'', $ADMIN)) {
			$admin.= '<nobr><u><span title="Delete">[<a href="admin.php?id='.$row["id"].'&action=del">D</a>]</span>';
			if ($row["status"]=='pending') $admin.= '<span title="Verify">[<a href="admin.php?id='.$row["id"].'&action=verif">V</a>]</span>';
			$admin.= '</u></nobr>';
			if (preg_match('/_IMG$/', $spam) && file_exists(FILE_DIR.$content)) {
				if (explode('/', mime_content_type(FILE_DIR.$content), 2)[0]=='image') {
					$content.= '<div><a href="'.FILE_DIR.$content.'" target="_blank"><img src="'.FILE_DIR.$content.'" alt="FILE" border="1" class="spamfile" /></a></div>';
				} else {
					$content.= '<div><a href="'.FILE_DIR.$content.'" target="_blank">FILE</a></div>';
				}
			}
		}
		$spam = "<a href=\"spam.php?spam=$spam\">$spam</a>";
		echo "<tr$class>".
			"<td$bgcol align=\"CENTER\">$id$admin</td>".
			"<td$bgcol align=\"CENTER\">$spam</td>".
			"<td$bgcol>$content</td>".
			"<td$bgcol>$notes</td>".
			"</tr>";
	}
} else {
	echo '<tr><td colspan="4"><font color="#707070">No results</font></td></tr>';
}

$result = SQL_result($claus);
$count = $result->num_rows;
if ($count) {
	echo '</table></tbody><br clear="ALL" />';
	echo '<table id="pager" border="1"><tbody><tr>';
	echo '<td>';
	for ($page=0; $page<$count; $page+=PAGE_DEF) {
		$p = ($page/PAGE_DEF)+1;
		$pget = $_GET;
		$pget['p'] = $p-1;
		if ($p!=intval($_GET['p']??1)) echo '[<a href="spam.php?'.http_build_query($pget).'">'.$p.'</a>]';
		else echo '[<b>'.$p.'</b>]';
	}
	echo '</td></tr>';
}
				?>
			</tbody>
		</table>
	</body>
</html><?php
exit;

function SQL_result($claus, $limit=-1) { global $stmt;
	$stmt->prepare("SELECT * FROM ".SQLTABLE_SPAM.$claus);
	$stmt->execute();
	return $stmt->get_result();
	//return $stmt->selectWhere(SPAM_FILE, $claus, $limit, new OrderBy(SPAM_ID, DESCENDING, INTEGER_COMPARISON));
}

function HTM_cansel($searchmode=false) { global $cans_of_spam, $safespam;
	$htm = '<select name="spam" onchange="spamjs(this.value);">';
	$cans2 = $cans_of_spam;
	if ($searchmode) array_unshift($cans2, 'ALL');
	foreach ($cans2 as $spam) {
		$s = $spam == $safespam ? ' selected="selected"' : '';
		$htm.= "<option$s>$spam</option>";
	}
	$htm.= '</select>';
	return $htm;
}
?>
