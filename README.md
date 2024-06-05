# SpamDB
Simple cross-imageboard spam database

## Kokonotsuba setup
1. Copy mod_spamdb.php (don't forget to include this in config.php) and spamdb/spamdb_client.php to modules/
2. Change the $API_KEY in mod_spamdb.php and $user_keys in config.inc.php, make sure that they match
3. Change API_URL in spamdb_client.php to point to spam.php, use the full url
4. Edit SQL details in config.inc.php
5. Open spam.php and the table will be created, you're now good to go

## API
 * You will need a copy of `spamdb_client.php` in your software
 * You will also need an API-key, which should be put in `spam_client.php`
 * You can change which spam categories are checked by editing `CANS_OF_SPAM` in `spam_client.php`
 * SpamDB can check IP addresses, text and file MD5 hashes
 * SpamDB by default only checks against verified spam, to check against unverified submissions to SpamDB, set `$verifyonly=false`
 * To check if data is spam, pass it to the function `spamcheck`
```
require_once "spamdb_client.php";
spamcheck($ip='', $txt='', $md5='', $verifyonly=true);
```
