# Tinfoil DRM port to PHP

# How To use
* Client mode

```
 php encrypt.php -i link.json -o jenc.tfl -t zlib -k public.key
```
```
-t zlib      use zlib commpression
-k public key file to use for encryption
-i input file mandatory
-o output file mandatory
```

* Web server include
* * Use include Asymmetric.php or add the class Tinfoil to your program
```php
include 'Asymmetric.php';

$data = file_get_contents('Link.json');
$data2 = $data;

//Just Compress zlib
Tinfoil::Pack($data);
file_put_contents ("emp.tfl",$data2);

//Get Lib and public Key to encrypt
Tinfoil::init();
//Encypt
Tinfoil::Enc($data2);
file_put_contents ("enc.tfl",$data);



```

## build lib by yor own
### Linux
```
sudo apt install composer
composer require phpseclib/phpseclib:~2.0
mv vendor lib
```
### Any platform that supports PHP
Make a text file named `install.php`, with this code:
```php
<?php

if (!file_exists('composer.phar')){
	file_put_contents('composer-setup.php',file_get_contents('https://getcomposer.org/installer'));
	include 'composer-setup.php';
	unlink ('composer-setup.php');
}
sleep(5);
?>
```
Then in comand line
```
php install.php
php composer.phar require phpseclib/phpseclib:~2.0
php mv vendor lib
```
### 

## ToDo

