<?php
require_once('klabs.php');
define('KAPTCHA_API_URL', 'https://sys.kolyma.org/kaptcha/kaptcha.php');
define('VIP_API_KEY', 'test');

function kaptcha_validate($key) {
	if (isset($_REQUEST["_KAPTCHA_NOJS"])) {
		$ip = getremoteaddr();
		$check = file_get_contents('https://agree.kolyma.org/vip.php?key='.VIP_API_KEY.'&addr='.$ip);
		if ($ip == $check && (isset($_GET["nojs"]) || isset($_GET["nojscheck"]) || isset($_GET["_KAPTCHA_NOJS"]))) {
    		return true;
		}

		$k = $_REQUEST["_KAPTCHA_KEY"]??false;
		if (!$k) return false;
		return stristr(file_get_contents(KAPTCHA_API_URL."?nojscheck&key=&_KAPTCHA=".$k), "CHECK correct") ? 1 : 0;
	}

	$k = $_REQUEST["_KAPTCHA"]??false;
	if (!$k) return false;
	return stristr(file_get_contents(KAPTCHA_API_URL."?_KAPTCHA=".$k."&key=".$key), "CHECK correct") ? 1 : 0;
}
?>
