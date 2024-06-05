<?php

// User spam submission handler

require_once('config.inc.php');
require_once('lib_common.inc.php');

if (!ALLOW_USER) {
	error('ERROR', 'ERROR: User spam submissions are currently disabled.');
}

if ($_SERVER['REQUEST_METHOD']!='POST') {
	error('ERROR', 'ERROR: Unjust POST.');
}

@session_start();

$pending = (in_array($_SESSION['adminpass']??'', $ADMIN) ? "verified" : "pending");

$conn = new mysqli(SQLHOST, SQLUSER, SQLPASS, SQLDB) or die('MySQLi ERROR');
$stmt = $conn->stmt_init();

$time = $_SERVER['REQUEST_TIME'];
$safespam = htmlspecialchars($_POST['spam']??'');
$captcha = md5(strtoupper(htmlspecialchars($_POST['captcha']??'')));

if (!in_array($_SESSION['adminpass']??'', $ADMIN) && $captcha != $_SESSION['captcha_dcode']) error('ERROR', 'ERROR: Invalid captcha!');

if (!in_array($safespam, $cans_of_spam)) {
	error('ERROR', 'ERROR: Invalid spam type.');
}
if (preg_match('/_IMG$/', $safespam)) {
	$f = $_FILES['fcontent'];
	if (!preg_match('/^image\//', $f['type'])) error('ERROR', 'Unsupported file type!');
	$ext = explode('.', $f['name'])[1];
	$safecontent = md5_file($f['tmp_name']).$ext; // Temporary
	$dest = FILE_DIR.$safecontent;
	move_uploaded_file($f['tmp_name'], $dest); // Upload the file first...
	// ...Then remove EXIF to match md5 from koko
	if (function_exists('exif_read_data') && function_exists('exif_imagetype')) {
        $imageType = exif_imagetype($dest);
        if ($imageType == IMAGETYPE_JPEG) {
            $exif = @exif_read_data($dest);
            if ($exif !== false) {
                $image = imagecreatefromjpeg($dest);
                imagejpeg($image, $dest, 100);
                imagedestroy($image);
            }
        }
    }
	$safecontent = md5_file($dest); // Get new md5, with clean EXIF
	rename($dest, FILE_DIR.$safecontent); // Done, yes this is weird
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
