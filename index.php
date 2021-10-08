<?php
include 'Asymmetric.php';

$data = file_get_contents('Link.json');
$data2 = $data;

//Get Lib and public Key
Tinfoil::init();

//Encypt
Tinfoil::Enc($data);
file_put_contents ("enc.tfl",$data);

//Just Compress
Tinfoil::Pack($data2);
file_put_contents ("emp.tfl",$data2);


?>