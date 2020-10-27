# Tinfoil DRM port to PHP

* How To use
```
 php encrypt.php -i link.json -o jenc.tfl -t zlib -k public.key
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

