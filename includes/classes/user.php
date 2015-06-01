<?php 

$users = new DB\SQL\Mapper($database,'users');  //create mapper
$users->reset();