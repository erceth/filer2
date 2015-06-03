<?php 

$reminders = new DB\SQL\Mapper($database,'reminders');  //create mapper
$reminders->reset();