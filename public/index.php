<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

	ini_set('error_reporting', E_ALL);

	$f3 = require("../includes/f3/base.php");

	$f3->config("../includes/config.cfg");

	//$f3->set('DEBUG',3);

	require_once($f3->get("CLASSES_PATH") . "/database.php");

	$template = new Template;
	echo $template->render($f3->get("TEMPLATE_PATH") . "/header.html");

	$f3->route('GET @home: /', function($f3) {
	    	global $template;
	        echo $template->render($f3->get("TEMPLATE_PATH") ."/home.html");
	    }
	);

	$f3->route('GET @admin: /admin', function($f3) {
			if ($f3->get("SESSION.loggedIn") && $f3->get("SESSION.admin")) {
				global $template;
				global $database;
				//get all non admin users
				$f3->set( "users", $database->exec("SELECT * FROM users WHERE admin=?", 0) );

				
	        	echo $template->render($f3->get("TEMPLATE_PATH") ."/admin.html");
			} else {
				$f3->reroute('@home');
			}
	    }
	);

	$f3->route('GET @admin: /admin/user/@userID', function($f3) {
			if ($f3->get("SESSION.loggedIn") && $f3->get("SESSION.admin")) {
				global $template;
				global $database;
				//get all non admin users
				$f3->set( "user_details", $database->exec("SELECT * FROM users WHERE id=?", $f3->get("PARAMS.userID")) );
				if (count($f3->get("user_details")) < 1 ) {
					//404
					$f3->reroute('@home');	
				}
				
	        	echo $template->render($f3->get("TEMPLATE_PATH") ."/admin_user_details.html");
			} else {
				$f3->reroute('@home');
			}
	    }
	);

	$f3->route('POST /admin/upload-file', function($f3) {
			if ($f3->get("SESSION.loggedIn") && $f3->get("SESSION.admin")) {
				$web = \Web::instance();
				$f3->set('UPLOADS','../files/');

				$userID = $f3->get("POST.user_id");

				$overwrite = false; // set to true, to overwrite an existing file; Default: false

				$files = $web->receive (function($file, $formFieldName) use ($userID) {
					global $database;
					$fileNameArray = explode("/", $file["name"]);
					$fileName = $fileNameArray[count($fileNameArray)-1];
					echo $fileName . " - " . $userID;
					$result = $database->exec( "INSERT INTO `files` (`id`, `file_name`, `user_id`) VALUES (NULL, ?, " . $userID . ")", $fileName );
					echo $result;

					return true;


				        //var_dump($file);
				        /* looks like:
				          array(5) {
				              ["name"] =>     string(19) "csshat_quittung.png"
				              ["type"] =>     string(9) "image/png"
				              ["tmp_name"] => string(14) "/tmp/php2YS85Q"
				              ["error"] =>    int(0)
				              ["size"] =>     int(172245)
				            }
				        */
				        // $file['name'] already contains the slugged name now
			         //    

				        
				    },
				    $overwrite,
				    function($fileBaseName, $formFieldName) {
				    	$fileNameArray = explode(".", $fileBaseName);
				    	$extention = $fileNameArray[count($fileNameArray)-1];
				    	$hashname = hash("md5", $fileBaseName . date("Y-m-d H:i:s")) . "." . $extention;
				    	return $hashname;
				    }
				);

			} else {
				$f3->reroute('@home');
			}
	    }
	);

	$f3->route('GET @user: /dashboard', function($f3) {
			//check session variables
	    	global $template;
	        echo $template->render($f3->get("TEMPLATE_PATH") ."/user.html");
	    }
	);


	//API
	$f3->route("POST /v1-api/login", function($f3) {
			if ($f3->exists("POST.username") && $f3->exists("POST.password")) {
				global $database;
				$result = $database->exec("SELECT * FROM users WHERE name=?", $f3->get("POST.username"));
				print_r($result);
				echo ($result[0]["admin"] == 1);
				if (count($result) > 0 && $result[0]["password"] === $f3->get("POST.password")) { //hash post password
					if ($result[0]["admin"]) {
						$f3->set('SESSION.loggedIn', true);
						$f3->set('SESSION.admin', true);
						$f3->set('SESSION.user', $f3->get("POST.username"));
						//set session variables
						//redirect to admin screen
						$f3->reroute('@admin');
					} else {
						$f3->set('SESSION.loggedIn', true);
						$f3->set('SESSION.admin', false);
						$f3->set('SESSION.user', $f3->get("POST.username"));
						//set session variables
						//redirect to user screen
						$f3->reroute('@user');
					}
				} else {
					//redirect to home screen with incorrect username/password error
					echo "incorrect username/password";
					$f3->reroute('@home');
				}
			} else {
				//redirect to home screen missing username and/or password variables error
				echo "missing username and/or password variables";
				$f3->reroute('@home');
			}
		}
	);


	$f3->run();

	include $f3->get("TEMPLATE_PATH") . "/footer.html";
?>


