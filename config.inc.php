<?php
define('CAPTCHA_PHP', '');
define('FILE_DIR', './storage/');
define('PAGE_DEF', 40);
define('TOP_TXT', file_get_contents('top.txt'));

define('SQLHOST', 'localhost');
define('SQLUSER', 'username');
define('SQLPASS', 'password');
define('SQLDB', 'spamdb');
define('SQLTABLE_SPAM', 'spam');

define('ALLOW_USER', true);
define('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);

$user_keys = array(
	'define',
	'the',
	'keys',
	'here'
);

$cans_of_spam = array(
	'ADV_TXT',
	'ADV_IMG',
	'ADV_IP',
	'CP_IP',
	'PROXY_IP',
	'MISC_IP'
);

$ADMIN = array(
	'login',
	'passwords',
	'go_here'
);

?>
