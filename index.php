<?php
include 'Asymmetric.php';

$data = '{"error":"rrrrrrrrr"}';
Tinfoil::init();
Tinfoil::Enc($data);
file_put_contents ("e.tfl",$data);


?>