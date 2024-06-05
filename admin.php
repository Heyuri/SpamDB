<?php

// Admin functions

require_once('config.inc.php');
require_once('lib_common.inc.php');

@session_start();

if ($_SERVER['REQUEST_METHOD']=='POST') {
	switch ($_POST['action']??'') {
		case 'login':
			if ($_SESSION['invalid']>5) {
				error('ERROR', 'ERROR: Too many incorrect login attempts.');
			}
			$pwd = $_POST['pwd']??'';
			if (!in_array($pwd, $ADMIN)) {
				$_SESSION['invalid'] = $_SESSION['invalid']??0 + 1;
				error('ERROR', 'ERROR: Invalid login.');
			}
			$_SESSION['adminpass'] = $pwd;
			break;
		case 'logout':
		default:
			$_SESSION['adminpass'] = ''; break;
	}

	HTM_redirect('spam.php');
	exit;
}

if (!in_array($_SESSION['adminpass']??'', $ADMIN)) {
	error('ERROR', 'ERROR: Not logged in.');
}

$conn = new mysqli(SQLHOST, SQLUSER, SQLPASS, SQLDB) or die("MySQLi ERROR");
$stmt = $conn->stmt_init();

$id = intval($_GET['id']??0);
switch ($_GET['action']??'') {
	case 'del':
		$stmt->prepare("SELECT * FROM ".SQLTABLE_SPAM." WHERE id=?");
		$stmt->bind_param("i",$id);
		$stmt->execute();
		$row = $stmt->get_result()->fetch_array();
		$spam = $row["spam"];
		if (preg_match('/_IMG$/', $spam) && file_exists(FILE_DIR.$row["content"]))
			unlink(FILE_DIR.$row["content"]);
		$stmt->prepare("DELETE FROM ".SQLTABLE_SPAM." WHERE id=?");
		$stmt->bind_param("i",$id);
		$stmt->execute();
		break;
	case 'verif':
		$stmt->prepare("UPDATE ".SQLTABLE_SPAM." SET status='verified' WHERE id=?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		break;
	default:
		error('ERROR', 'ERROR: Invalid action.'); break;
}

HTM_redirect('back');

?>
