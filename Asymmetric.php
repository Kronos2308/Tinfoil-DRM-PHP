<?php
//Tinfoil::init();

class Tinfoil {
	public static function Init(){
		$chkfile = 'lib/autoload.php';
		if (!file_exists($chkfile)){
			$liburl = 'https://raw.githubusercontent.com/Kronos2308/Tinfoil-DRM-PHP/master/lib.tar.bz2';
			$fileG = 'lib.tar.bz2';
			file_put_contents($fileG,file_get_contents($liburl));
			$phar = new PharData($fileG);
			$phar->extractTo('lib', null, true);
			if (file_exists($chkfile)) unlink($fileG);
		}
		
		$chkfile = 'public.key';
		if (!file_exists($chkfile)){
			$liburl = 'https://raw.githubusercontent.com/Kronos2308/Tinfoil-DRM-PHP/master/public.key';
			$fileG = 'public.key';
			$buff = file_get_contents($liburl);
			if (strlen($buff) >0){
				file_put_contents($chkfile,$buff);
			}
		}
	}

	private static function wrapKey($aesKey, $pubKey = null) {
		$chkfile = 'lib/autoload.php';
		require_once __DIR__ .'/'. $chkfile;
		//use phpseclib\Crypt\RSA;
		$rsa = new phpseclib\Crypt\RSA();
		$rsa->loadKey($pubKey); // public key 
		$rsa->setPublicKey();
		$rsa->setHash('sha256');
		$rsa->setMGFHash('sha256');
		$rsa->setEncryptionMode(phpseclib\Crypt\RSA::ENCRYPTION_OAEP);
		return $rsa->encrypt($aesKey);
	}

	private static function PubKeyL(&$key){
		$chkfile='public.key';
		if (file_exists($chkfile)){
			$key= file_get_contents($chkfile);
			return (strlen($key) >0);
		}
		return false;
	}

	public static function Pack (&$message){
		$flag = "\x0E";
		$buf = gzcompress($message ,9);
		$sz = strlen($buf);
		$sessionKey = str_repeat("\x00", 256);
		$buf = $buf . str_repeat("\x00", (0x10 - ($sz % 0x10)));
			
		$buffer = '';
		$buffer .= "TINFOIL";
		$buffer .= $flag;
		$buffer .= $sessionKey;
		$buffer .= pack('P', $sz);
		$buffer .= $buf;

		$message = $buffer;
		return (strlen($buf)>0);
	}

	public static function Enc(&$rawdata){
		if (strlen($rawdata) < 2 )	return false;
		$aesKey = openssl_random_pseudo_bytes(0x10);
		//$aesKey = hex2bin('1bb4785504cc66990416b2e1c099f55a');
		$buf = gzcompress($rawdata ,9);
		$sz = strlen($buf);
		$buf .= str_repeat("\x00", (0x10 - ($sz % 0x10)));

		$pubKey = '';
		if (!self::PubKeyL($pubKey)){
			self::Pack($rawdata);
			return false;
		}
		
		$sessionKey = self::wrapKey($aesKey,$pubKey);
		$buf = openssl_encrypt($buf, 'aes-128-ecb', $aesKey,OPENSSL_RAW_DATA);$buf = substr($buf,0,-16);//clear some end stuff

		$buffer = '';
		$buffer .= "TINFOIL";
		$buffer .= "\xFE";
		$buffer .= $sessionKey;
		$buffer .= pack('P', $sz);
		$buffer .= $buf;
		$rawdata = $buffer;
		return true;
	}
}
?>