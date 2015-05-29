<?php
// class MySQLDatabase {
// 	private $connection = null;
// 	private $database_name;
// 	private $database_user;
// 	private $database_password;

// 	function __construct($database_name, $database_user, $database_password) {
// 		$this->database_name = $database_name;
// 		$this->database_user = $database_user;
// 		$this->database_password = $database_password;
// 	}

// 	function open_connection() {
// 		$this->connection = new DB\SQL(
// 			"mysql:host=localhost;port=3306;dbname=" . $this->database_name,
// 			$this->database_user,
// 			$this->database_password
// 		);
// 	}

		
		
// }


// $database = new MySQLDatabase($f3->get("DATABASE_NAME"), $f3->get("DATABASE_USER"), $f3->get("DATABASE_PASSWORD"));
// $database->open_connection();

$database = new DB\SQL(
	"mysql:host=localhost;port=3306;dbname=" . $f3->get("DATABASE_NAME"),
	$f3->get("DATABASE_USER"),
	$f3->get("DATABASE_PASSWORD")
);


?>
