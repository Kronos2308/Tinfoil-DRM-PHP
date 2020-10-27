<?php
//install same has  composer require phpseclib/phpseclib:~2.0
/*
this file espect to recive a post with
a base64 encoded json has ``data`` in POST
or 
a basse64 enncoded  and zlib compressed has ``Cdata`` in POST

and then answer to the user has reply the encoded DRM tinfoil json
*/
$chkfile = 'lib/autoload.php';
if (!file_exists($chkfile)){
	$liburl = 'https://raw.githubusercontent.com/Kronos2308/Tinfoil-DRM-PHP/master/lib.tar.bz2';
	$fileG = 'lib.tar.bz2';
	file_put_contents($fileG,file_get_contents($liburl));
	$phar = new PharData($fileG);
	$phar->extractTo('lib', null, true);
	if (file_exists($chkfile)) unlink($fileG);
}
require __DIR__ .'/'. $chkfile;

use phpseclib\Crypt\RSA;

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


if(isset($_POST['data'])){
$jsonB64 = $_POST['data'];
$rawdata = 	base64_decode($jsonB64);
}
else if (isset($_POST['Cdata'])){
$jsonB64 = $_POST['Cdata'];
$rawdata = 	gzuncompress(base64_decode($jsonB64));
}
if (strlen($jsonB64) < 2 )	die('{"error":"ENC failed"}');

$aesKey = openssl_random_pseudo_bytes(0x10);
$buf = gzcompress($rawdata ,9);
$sz = strlen($buf);

$pubKey = file_get_contents('public.key');
$sessionKey = wrapKey($aesKey,$pubKey);
$buf .= str_repeat("\x00", (0x10 - ($sz % 0x10)));
$buf = openssl_encrypt($buf, 'aes-128-ecb', $aesKey,OPENSSL_RAW_DATA);

$buffer .= "TINFOIL\xFE";
$buffer .= $sessionKey;
$buffer .= pack('P', $sz);
$buffer .= $buf;
die($buffer);


?>