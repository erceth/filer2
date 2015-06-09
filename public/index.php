<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

	ini_set('error_reporting', E_ALL);

	$f3 = require("../includes/f3/base.php");

	$f3->config("../includes/config.cfg");

	//$f3->set('DEBUG',3);

	require_once($f3->get("CLASSES_PATH") . "/database.php");
	require_once($f3->get("CLASSES_PATH") . "/user.php");
	require_once($f3->get("CLASSES_PATH") . "/files.php");
	require_once($f3->get("CLASSES_PATH") . "/reminders.php");
	require_once($f3->get("CLASSES_PATH") . "/quotes.php");

	$template = new Template;
	// print_r( $f3->get("SESSION.firstName")); 
	// if ($f3->get("SESSION.firstName")) {
	// 	$f3->set('firstName', $f3->get("SESSION.firstName"));
	// }
	echo $template->render($f3->get("TEMPLATE_PATH") . "/header.html");


	/**** LOGIN ****/

	$f3->route("GET @home: /", "homePage");
	$f3->route("POST /", "homePage");

	function homePage($f3) {
		global $template;
		$f3->set("messages", array() );
		if ($f3->get("POST.login") && $f3->exists("POST.username") && $f3->exists("POST.password")) {
			global $database;
			global $users;
			//get all non admin users
			$users->reset();
			$users->load(array('username=?', $f3->get("POST.username")));
			if ($users->password === $f3->get("POST.password")) { //hash post password
				$f3->set('SESSION.loggedIn', true);
				$f3->set('SESSION.username', $f3->get("POST.username"));
				$f3->set("SESSION.firstName", $users->first_name);
				
				$f3->set('SESSION.id', $users->id);

				if ($users->admin) {
					$f3->set('SESSION.admin', true);
					$f3->reroute('@admin');
				} else {
					$f3->set('SESSION.admin', false);
					$f3->reroute('@user');
				}
			} else {
				$f3->push("messages", "incorrect username/password");
			}
		}

		echo $template->render($f3->get("TEMPLATE_PATH") ."/home.html");
		echo $template->render($f3->get("TEMPLATE_PATH") ."/footer.html");
	}


	/**** ADMIN ****/
	$f3->route('GET @admin: /admin', "adminPage");
	$f3->route('POST /admin', "adminPage");

	function adminPage($f3) {
		if ($f3->get("SESSION.loggedIn") && $f3->get("SESSION.admin")) {
			global $template;
			global $users;
			global $files;
			global $database;
			global $reminders;
			global $quotes;

			/*** check form submissions ***/
			/*** new user ***/
			if ($f3->get("POST.new-user")) {
				$users->reset();
				$users->load(array("username=?", $f3->get("POST.username")));
				if ($users->id) {
					$f3->set("new_user_error", "Username " . $f3->get("POST.username") . " is not available.  Try a different one.");
				} else {
					$users->reset();
					$users->username = $f3->get("POST.username");
					$users->first_name = $f3->get("POST.first-name");
					$users->last_name = $f3->get("POST.last-name");
					$users->password = $f3->get("POST.password");
					$users->admin = $f3->get("POST.admin");
					$users->save();
				}
			}
			/*** delete user ***/
			if ($f3->get("POST.delete-user")) {
				//delete users files
				$all_user_files = $files->find("user_id='" . $f3->get("POST.userID") . "'");
				foreach($all_user_files as $file) {
					$fileName = "../files/" . $file->file_name;
					if (is_file($fileName)) {
						if(!unlink($fileName)) {
							echo "problem deleting file" . $filename;
							return;
						}
					}
				}

				$userID = $f3->get("POST.userID");
				$database->begin(); //database transaction
				$database->exec("DELETE FROM users WHERE id=?", $userID);
				$database->exec("DELETE FROM files WHERE user_id=?", $userID);
				$database->commit();
			}
			/*** new reminder ***/
			if ($f3->get("POST.new-reminder")) {
				$reminders->reset();
				$reminders->text = $f3->get("POST.text");
				$reminders->save();
			}
			/*** delete reminder ***/
			if ($f3->get("POST.delete-reminder")) {
				$reminders->reset();
				$reminders->load(array("id=?", $f3->get("POST.reminderID")));
				$reminders->erase();
			}

			/*** new quote ***/
			if ($f3->get("POST.new-quote")) {
				$quotes->reset();
				$quotes->text = $f3->get("POST.text");
				$quotes->save();
			}
			/*** delete quote ***/
			if ($f3->get("POST.delete-quote")) {
				$quotes->reset();
				$quotes->load(array("id=?", $f3->get("POST.quoteID")));
				$quotes->erase();
			}

			/*** get all non admin users ***/
			$f3->set("users", $users->find(array('admin=?','0')) ); //set users for template use //TODO: turn this into a class method call

			/*** get all reminders ***/
			$f3->set("admin", true);
			$f3->set("reminders_block", $f3->get("SNIPPETS_PATH") . "/reminders.html");
			$f3->set("all_reminders", $reminders->find());

			/*** get all quotes ***/
			$quotes->reset();
			$f3->set("all_quotes", $quotes->find());
			
        	echo $template->render($f3->get("TEMPLATE_PATH") ."/admin.html");
        	echo $template->render($f3->get("TEMPLATE_PATH") ."/footer.html");
		} else {
			$f3->reroute('@home');
		}
    }

    /**** ADMIN USER DETAILS ****/
    $f3->route('GET  /admin/user/@userID', "adminUserDetail");
	$f3->route('POST /admin/user/@userID', "adminUserDetail");

	function adminUserDetail($f3) {
		if ($f3->get("SESSION.loggedIn") && $f3->get("SESSION.admin")) {
			global $template;
			global $users;
			global $files;

			/*** delete file ***/
			if ($f3->get("POST.delete-file")) {
				$files->load(array("id=?", $f3->get("POST.file_id")));
				$fileName = "../files/" . $files->file_name;
				if (is_file($fileName)) {
					if(!unlink($fileName)) {
						echo "problem deleting file " . $fileName;
						return;
					}
					$files->erase();
				}
			}


			/*** upload files ***/
			if ($f3->get("POST.upload-file")) {

				$web = \Web::instance();
				$f3->set('UPLOADS','../files/');

				$userID = $f3->get("POST.user_id");

				$overwrite = false; // set to true, to overwrite an existing file; Default: false

				$files = $web->receive (function($file, $formFieldName) use ($userID, $files) {
					$fileNameArray = explode("/", $file["name"]);
					$fileName = $fileNameArray[count($fileNameArray)-1];

					//INSERT file
					$files->reset();
					$files->file_name = $fileName;
					$files->user_id = $userID;
					$result = $files->save();

					return $result;



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
				    function($fileBaseName, $formFieldName) { //changes the name of the file
				    	$fileNameArray = explode(".", $fileBaseName);
				    	$extention = $fileNameArray[count($fileNameArray)-1];
				    	$hashname = hash("md5", $fileBaseName . date("Y-m-d H:i:s")) . "." . $extention;
				    	return $hashname;
				    }
				);
				$f3->reroute("/admin/user/" . $userID);
			}

			/*** user's details ***/
			$users->load(array("id=?", $f3->get("PARAMS.userID") ))->copyTo("user_details");
			//TODO: add if lookup fail
			// if ($f3->get("user_details")) {
			// 	//404
			// 	$f3->reroute('@home');	
			// }

			/*** user's files ***/
			$f3->set("users_files", $f3->get("SNIPPETS_PATH") . "/users_files.html");
			$f3->set("files", $files->find("user_id='" . $f3->get("PARAMS.userID") . "'"));
			
        	echo $template->render($f3->get("TEMPLATE_PATH") ."/admin_user_details.html");
        	echo $template->render($f3->get("TEMPLATE_PATH") ."/footer.html");
		} else {
			$f3->reroute('@home');
		}
    }

    /**** REMINDER ****/

    $f3->route("GET @reminder: /admin/reminder", "reminderPage");
    //$f3->route("POST /admin/reminder", "reminderPage");

    function reminderPage ($f3) {
    	global $template;
    	echo $template->render($f3->get("TEMPLATE_PATH") ."/reminder.html");
    	echo $template->render($f3->get("TEMPLATE_PATH") ."/footer.html");	
    }


    /**** QUOTES ****/

    $f3->route("GET @quotes: /admin/quotes", "quotePage");
    //$f3->route("POST /admin/reminder", "reminderPage");

    function quotePage ($f3) {
    	global $template;
    	echo $template->render($f3->get("TEMPLATE_PATH") ."/quote.html");	
    	echo $template->render($f3->get("TEMPLATE_PATH") ."/footer.html");
    }




    /**** USER ****/

	$f3->route('GET @user: /dashboard', function($f3) {
			if ($f3->get("SESSION.loggedIn") && !$f3->get("SESSION.admin")) {
		    	global $template;
		    	global $users;
		    	global $files;
		    	global $reminders;

		    	$f3->set("users_files", $f3->get("SNIPPETS_PATH") . "/users_files.html");
		    	$users->load(array("id=?", $f3->get("SESSION.id") ))->copyTo("user_details");
				$f3->set("files", $files->find("user_id='" . $f3->get("SESSION.id") . "'"));

				/*** get all reminders ***/
				$f3->set("all_reminders", $reminders->find());
				$f3->set("reminders_block", $f3->get("SNIPPETS_PATH") . "/reminders.html");

		        echo $template->render($f3->get("TEMPLATE_PATH") ."/user.html");
		        echo $template->render($f3->get("TEMPLATE_PATH") ."/footer.html");
		    } else {
		    	$f3->reroute('@home');	
		    }
	    }
	);

	$f3->route('GET /view/@filename', function ($f3, $params) {
		//TODO: check credentials
		global $files;

		$files->load(array("file_name=?", $f3->get("PARAMS.filename")));

		if ($f3->get("SESSION.admin") || ($f3->get("SESSION.id") == $files->user_id) ) {
			$web = \Web::instance();
			$web->send("../files/" . $f3->get("PARAMS.filename"), NULL, 2048, false );	
		} else {
			//404
			$f3->reroute('@home');
		}

		
	});

	$f3->route('GET /download/@filename', function ($f3, $params) {
		//TODO: check credentials
		$web = \Web::instance();
		$web->send("../files/" . $f3->get("PARAMS.filename"), NULL, 2048, true );
    });

    $f3->route('GET /logout', function ($f3, $params) {
		$f3->clear('SESSION');
		$f3->reroute('@home');
    });


	// $f3->route("POST /v1-api/login", function($f3) {
	// 		if ($f3->exists("POST.username") && $f3->exists("POST.password")) {
	// 			global $database;
	// 			global $users;
	// 			//get all non admin users
	// 			$users->reset();
	// 			$users->load(array('username=?', $f3->get("POST.username")));
	// 			if ($users->password === $f3->get("POST.password")) { //hash post password
	// 				$f3->set('SESSION.loggedIn', true);
	// 				$f3->set('SESSION.username', $f3->get("POST.username"));
	// 				$f3->set('SESSION.id', $users->id);

	// 				if ($users->admin) {
	// 					$f3->set('SESSION.admin', true);
	// 					$f3->reroute('@admin');
	// 				} else {
	// 					$f3->set('SESSION.admin', false);
	// 					$f3->reroute('@user');
	// 				}
	// 			} else {
	// 				//redirect to home screen with incorrect username/password error
	// 				echo "incorrect username/password";
	// 				//$f3->reroute('@home');
	// 			}
	// 		} else {
	// 			//redirect to home screen missing username and/or password variables error
	// 			echo "missing username and/or password variables";
	// 			//$f3->reroute('@home');
	// 		}
	// 	}
	// );

	$f3->set("quote_of_day", getQuoteOfTheDay());


	$f3->run();




	


/*
give option to name file on upload or keep date
STYLING!
test user security
create 404, uncomment reroutes last
conform password
hide logout link
only allow audio uploads
write to error log
-add date to reminders



*/





?>



