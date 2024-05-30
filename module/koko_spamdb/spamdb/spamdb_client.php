<?php
define('API_URL', 'https://example.com/spam.php');
define('CANS_OF_SPAM', array(
	'ADV_TXT',
	'ADV_IMG',
	'ADV_IP',
	'CP_IP',
	'PROXY_IP',
	'MISC_IP',
));

function _spamapi($key, $spam, $verifyonly, $c) {
	$spam_result = array_map('rtrim', file(API_URL.
		"?key=$key&spam=$spam&verifyonly=".($verifyonly?'on':''),
		false,stream_context_create(array("http" => array(
			"method" => "POST", "header" => "Content-type: application/x-www-form-urlencoded",
			"content" => http_build_query(array("c" => $c), "", "&")
		)))));
	return !empty($spam_result);
}

function spamcheck($key, $ip='', $txt='', $md5='', $verifyonly=true) {
	$c = "";
	foreach (CANS_OF_SPAM as $spam) {
		if ($txt && preg_match('/_TXT$/', $spam)) {
			if (_spamapi($key,$spam,$verifyonly,$txt))
				return true;
		} elseif ($md5 && preg_match('/_IMG$/', $spam)) {
			if (_spamapi($key,$spam,$verifyonly,$md5))
				return true;
		} elseif ($ip && preg_match('/_IP$/', $spam)) {
			if (_spamapi($key,$spam,$verifyonly,$ip))
				return true;
			if (_spamapi($key,$spam,$verifyonly,gethostbyaddr($ip)))
				return true;
		}
	}
	return false;
}

?>
