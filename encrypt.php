<?php
//install same has  composer require phpseclib/phpseclib:~2.0
$chkfile = 'lib/autoload.php';
if (!file_exists($chkfile)){
	$liburl = 'https://myrincon.duckdns.org/vendor.tar.bz2';
	$fileG = 'vendor.tar.bz2';
	file_put_contents($fileG,file_get_contents($liburl));
	$phar = new PharData($fileG);
	$phar->extractTo('lib', null, true);
	if (file_exists($chkfile)) unlink($fileG);
}
require __DIR__ .'/'. $chkfile;

use phpseclib\Crypt\RSA;

function hexcode($text) {
    $retval = '';
    for($i = 0; $i < strlen($text); ++$i) {
        $retval .= '\x'.dechex(ord($text[$i]));
    }
    return $retval;
}

function wrapKey($aesKey, $pubKey = null)
{global $argss;
	$rsa = new RSA();
	$rsa->loadKey($pubKey); // public key 
	$rsa->setPublicKey();
	$rsa->setHash('sha256');
	$rsa->setMGFHash('sha256');
	$rsa->setEncryptionMode(RSA::ENCRYPTION_OAEP);
	return $rsa->encrypt($aesKey);
}



$shortopts  = "i:o:k:t:";
$argss = getopt($shortopts);

if (!isset($argss['i'])||!isset($argss['o'])){
die ('
usage   php encrypt.php -i file1 -o fileenc -t zlib -k public.key

-t zlib      use zlib commpression
-k public key file to use for encryption
-i input file
-o output file
');
}

$buffer = '';

$aesKey = openssl_random_pseudo_bytes(0x10);
$input = file_get_contents($argss['i']);

if  ($argss['t'] == 'zlib'){
	echo "compressing with zlib".PHP_EOL;
	$flag = "\x0E";
	$buf = gzcompress($input ,9);
} else {
	$flag = "\x00";
	echo 'no compression used'.PHP_EOL;
	$buf = $input;
}

$sz = strlen($buf);

if (isset($argss['k'])){
	if ($flag == "\x00") $flag = "\xF0"; else $flag = "\xFE";
	$pubKey = file_get_contents($argss['k']);
	$sessionKey = wrapKey($aesKey,$pubKey);
	$buf .= str_repeat("\x00", (0x10 - ($sz % 0x10)));
	$buf = openssl_encrypt($buf, 'aes-128-ecb', $aesKey,OPENSSL_RAW_DATA);//	$buf = substr($buf,0,-16);//clear some end stuff
} else {
	$sessionKey = str_repeat("\x00", 256);
	$buf = $buf . str_repeat("\x00", (0x10 - ($sz % 0x10)));
}

echo "b'".hexcode($aesKey)."'".PHP_EOL;
$buffer .= "TINFOIL";
$buffer .= $flag;
$buffer .= $sessionKey;
$buffer .= pack('P', $sz);
$buffer .= $buf;
file_put_contents($argss['o'],$buffer);

echo 'fin'.PHP_EOL;