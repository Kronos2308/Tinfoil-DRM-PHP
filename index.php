<?php
include 'Asymmetric.php';

$data = file_get_contents('Link.json');//'{"error":"rrrrrrrrr"}';
Tinfoil::init();
file_put_contents ("ee.tfl",Tinfoil::Pack($data));
Tinfoil::Enc($data);
file_put_contents ("e.tfl",$data);


?>