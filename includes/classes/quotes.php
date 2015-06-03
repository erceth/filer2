<?php 

$quotes = new DB\SQL\Mapper($database,'quotes');  //create mapper
$quotes->reset();