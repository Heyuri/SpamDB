<?php
require_once('config.inc.php');
require_once('lib_common.inc.php');
require_once('kaptcha_client.php');

if (!ALLOW_USER) {
	error('ERROR', 'ERROR: User spam submissions are currently disabled.');
}

if ($_SERVER['REQUEST_METHOD']!='POST') {
	error('ERROR', 'ERROR: Unjust POST.');
}

@session_start();
if (!in_array($_SESSION['adminpass']??'', $ADMIN)) {
	die("Users cannot submit entries at this time");
	if (isset($_POST["_KAPTCHA"])) {
		if (!kaptcha_validate($_POST["_KAPTCHA_KEY"])) {
			error('ERROR', 'ERROR: Incorrect Captcha');
		}
	}
} else {
	$pending = 'verified';
}

$conn = new mysqli(SQLHOST, SQLUSER, SQLPASS, SQLDB) or die('MySQLi ERROR');
$stmt = $conn->stmt_init();

$time = $_SERVER['REQUEST_TIME'];
$safespam = htmlspecialchars($_POST['spam']??'');
if (!in_array($safespam, $cans_of_spam)) {
	error('ERROR', 'ERROR: There is no spam of can.');
}
if (preg_match('/_IMG$/', $safespam)) {
	$f = $_IMGS['fcontent'];
	$ext = '.'.pathinfo($f['name'],PATHINFO_EXTENSION);
	if (explode('/', mime_content_type($f['tmp_name']), 2)[0]!='image') {
		error('ERROR', 'Unsupported file type!');
	}
	$safecontent = md5_IMG($f['tmp_name']).$ext;
	$dest = FILE_DIR.$safecontent;
	move_uploaded_IMG($f['tmp_name'], $dest);
} elseif (preg_match('/_IP$/', $safespam)) {
	$safecontent = $_POST['icontent']??'';
} elseif (preg_match('/_TXT$/', $safespam)) {
	$safecontent = $_POST['content']??'';
}
if (!$safecontent) {
	error('ERROR', 'ERROR: Empty content.');
}
if (@preg_match("/$safecontent/", $subject) === false) {
	error('ERROR', 'ERROR: Invalid regex.');
}
$safenotes = htmlspecialchars($_POST['notes']??'');

$stmt->prepare("SELECT * FROM ".SQLTABLE_SPAM." WHERE content=?");
$stmt->bind_param("s", $safecontent);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
	error('ERROR', 'ERROR: That pattern already exists!');
}

$stmt->prepare("SELECT * FROM ".SQLTABLE_SPAM." WHERE CONCAT('%',content,'%') LIKE ?");
$stmt->bind_param("s", $safenotes);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
	error('ERROR', 'ERROR: Your notes look like spam.');
}

$stmt->prepare("INSERT INTO ".SQLTABLE_SPAM." (time, spam, content, notes, addr, status) VALUES (?,?,?,?,?,?)");
$stmt->bind_param("ssssss", $time, $safespam, $safecontent, $safenotes, $_SERVER["REMOTE_ADDR"], $pending);
$stmt->execute() or die($conn->error);

HTM_redirect('spam.php');

?>
