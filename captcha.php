<?php
const CAPTCHA_WIDTH = 100;
const CAPTCHA_HEIGHT = 20;
const CAPTCHA_LENGTH = 4;
const CAPTCHA_GAP = 20;
const CAPTCHA_TEXTY = 20;
const CAPTCHA_FONTMETHOD = 0;
const CAPTCHA_FONTFACE = array('./font1.gdf');
const CAPTCHA_ECOUNT = 2;

@session_start();

$abc = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
$lcode = '';
for($i = 0; $i < CAPTCHA_LENGTH; $i++) {
	$lcode .= $abc[array_rand($abc)];
}

$dcode = md5($lcode);
$_SESSION['captcha_dcode'] = $dcode;

$captcha = ImageCreateTrueColor(CAPTCHA_WIDTH, CAPTCHA_HEIGHT);
$randcolR = rand(100, 230); $randcolG = rand(100, 230); $randcolB = rand(100, 230);
$backColor = ImageColorAllocate($captcha, $randcolR, $randcolG, $randcolB);
ImageFill($captcha, 0, 0, $backColor);
$txtColor = ImageColorAllocate($captcha, $randcolR - 40, $randcolG - 40, $randcolB - 40);
$rndFontCount = count(CAPTCHA_FONTFACE);

for($p = 0; $p < CAPTCHA_LENGTH; $p++){
	if(CAPTCHA_FONTMETHOD){
		if(rand(1, 2)==1) $degree = rand(0, 25);
		else $degree = rand(335, 360);
		ImageTTFText($captcha, rand(14, 16), $degree, ($p + 1) * CAPTCHA_GAP, CAPTCHA_TEXTY, $txtColor, CAPTCHA_FONTFACE[rand(0, $rndFontCount - 1)], substr($lcode, $p, 1));
	}else{
		$font = ImageLoadFont(CAPTCHA_FONTFACE[rand(0, $rndFontCount - 1)]);
		ImageString($captcha, $font, ($p + 1) * CAPTCHA_GAP, CAPTCHA_TEXTY - 18, substr($lcode, $p, 1), $txtColor);
	}
}

for($n = 0; $n < CAPTCHA_ECOUNT; $n++){
	ImageEllipse($captcha, rand(1, CAPTCHA_WIDTH), rand(1, CAPTCHA_HEIGHT), rand(50, 100), rand(12, 25), $txtColor);
	ImageEllipse($captcha, rand(1, CAPTCHA_WIDTH), rand(1, CAPTCHA_HEIGHT), rand(50, 100), rand(12, 25), $backColor);
}

header('Content-Type: image/png');
header('Cache-Control: no-cache');
ImagePNG($captcha);
ImageDestroy($captcha);
?>