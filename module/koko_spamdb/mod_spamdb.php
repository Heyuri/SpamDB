<?php
require_once(ROOTPATH.'module/spamdb/spamdb_client.php');

class mod_spamdb implements IModule {
	private $API_KEY = 'apipassword';

	public function __construct($PMS) {
	}

	public function getModuleName() {
		return __CLASS__.' : K! SpamDB';
	}

	public function getModuleVersionInfo() {
		return 'Koko BBS Release 1';
	}

	public function autoHookRegistBeforeCommit(&$name, &$email, &$sub, &$com, &$category, &$age, $dest, $isReply, $imgWH, &$status) {
		if (defined('VIPDEF')) return;
		$filehash = '';
		if ($dest) {
		    if (is_file($dest))
		        $filehash = md5_file($dest);
		}
		if (spamcheck($this->API_KEY, getREMOTE_ADDR(), $com, $filehash)) {
			error('Your post could not be submitted due to the contents of your post or your IP address being listed in our SpamDB. Contact administration if you think this is a mistake.');
		}
	}
}