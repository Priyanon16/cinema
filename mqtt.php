<?php

$topic=$_POST['topic'];
$value=$_POST['value'];

exec("mosquitto_pub -h 103.114.201.199 -t $topic -m '$value'");

?>