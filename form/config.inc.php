<?php
define('CAPTCHA_PHP', '');
define('FILE_DIR', './storage/');
define('PAGE_DEF', 40);

define('SPAM_FILE', 'spam.log.txt');

define('ALLOW_USER', true);
define('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);

$user_keys = array(
	'test'
);

$cans_of_spam = array(
	'CP_TXT',
	'CP_IP',
	'ADV_TXT',
	'ADV_FILE',
	'ADV_IP',
	'STRICT_TXT',
	'STRICT_FILE',
	'STRICT_IP',
	'MISC_TXT',
	'MISC_FILE',
	'MISC_IP',
);

$ADMIN = array(
	'test'
);

?>