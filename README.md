# SpamDB
Spam Database

## API
 * You will need a copy of `spamdb_client.php` in your software
 * You will also need an API-key, which should be put in `spam_client.php`
 * You can change which spam categories are checked by editing `CANS_OF_SPAM` in `spam_client.php`
 * SpamDB can check IP addresses, text and file MD5 hashes
 * SpamDB by default only checks against verified spam, to check against unverified submissions to SpamDB, set `$verifyonle=false`
 * To check if data is spam, pass it to the function `spamcheck`
```
require_once "spamdb_client.php";
spamcheck($ip='', $txt='', $md5='', $verifyonly=true);
```
