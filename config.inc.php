<?php

// SpamDB config file

//define('CAPTCHA_PHP', 'captcha.php');
define('FILE_DIR', './storage/'); // Directory to store image files in
define('PAGE_DEF', 40); // Number of displayed entries per page
define('TOP_TXT', file_get_contents('top.txt')); // extra HTML to display in spam.php

define('SQLHOST', 'localhost'); // SQL host
define('SQLUSER', 'CHANGEME'); // SQL login
define('SQLPASS', 'CHANGEME'); // SQL password
define('SQLDB', 'CHANGEME'); // SQL database name
define('SQLTABLE_SPAM', 'spam'); // SQL table name

define('ALLOW_USER', true); // Allow users to submit spam
define('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']); // Don't touch

// Valid API keys
$user_keys = array(
	'put',
	'the',
	'keys',
	'here'
);

// Avaialable spam submission types
$cans_of_spam = array(
	'ADV_TXT', // Text
	'ADV_IMG', // Image
	'ADV_IP', // IP
	'CP_IP', // "automated" CP spam IP
	'PROXY_IP', // Proxy IP
	'MISC_IP' // ?
);

// Admin passwords
$ADMIN = array(
	'CHANGEME1',
	'CHANGEME2'
);

?>
