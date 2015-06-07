<?php 

$quotes = new DB\SQL\Mapper($database,'quotes');  //create mapper
$quotes->reset();

function getQuoteOfTheDay() {
	global $quotes;
	$all_quotes = $quotes->find();
	if (count($all_quotes) < 1) {
		return "knowledge is power";
	}
	srand(date("Ymd")); //seed random generator
	$randval = rand();
	$randval = $randval % count($all_quotes);

	return $all_quotes[$randval]->text;
}