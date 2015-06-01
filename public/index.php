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
				global $users;
				//get all non admin users
				$f3->set("users", $users->find(array('admin=?','0')) ); //set users for template use
	        	echo $template->render($f3->get("TEMPLATE_PATH") ."/admin.html");
			} else {
				$f3->reroute('@home');
			}
	    }
	);

	$f3->route('GET /admin/user/@userID', function($f3) {
			if ($f3->get("SESSION.loggedIn") && $f3->get("SESSION.admin")) {
				global $template;
				global $users;
				global $files;
				$users->load(array("id=?", $f3->get("PARAMS.userID") ))->copyTo("user_details");
				$f3->set("files", $files->find("user_id='" . $f3->get("PARAMS.userID") . "'"));
				// if ($f3->get("user_details")) {
				// 	//404
				// 	$f3->reroute('@home');	
				// }
				
	        	echo $template->render($f3->get("TEMPLATE_PATH") ."/admin_user_details.html");
			} else {
				$f3->reroute('@home');
			}
	    }
	);

	$f3->route('POST /admin/new-user', function($f3) {
			if ($f3->get("SESSION.loggedIn") && $f3->get("SESSION.admin")) {
				global $users;
				$users->name = $f3->get("POST.name");
				$users->password = $f3->get("POST.password");
				$users->admin = $f3->get("POST.admin");
				$users->save();
				$f3->reroute('@admin');
			} else {
				$f3->reroute('@home');
			}
	    }
	);

	$f3->route('POST /admin/delete-user', function($f3) {
			if ($f3->get("SESSION.loggedIn") && $f3->get("SESSION.admin")) {
				global $files;
				global $database;

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

				$f3->reroute('@admin');
			} else {
				$f3->reroute('@home');
			}
	    }
	);

	$f3->route('POST /admin/upload-file', function($f3) {
			if ($f3->get("SESSION.loggedIn") && $f3->get("SESSION.admin")) {
				global $files;
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
				    function($fileBaseName, $formFieldName) {
				    	$fileNameArray = explode(".", $fileBaseName);
				    	$extention = $fileNameArray[count($fileNameArray)-1];
				    	$hashname = hash("md5", $fileBaseName . date("Y-m-d H:i:s")) . "." . $extention;
				    	return $hashname;
				    }
				);
				$f3->reroute("/admin/user/" . $userID);

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

	$f3->route('GET /view/@filename', function ($f3, $params) {
		//check session variables

		$web = \Web::instance();
		$web->send("../files/" . $f3->get("PARAMS.filename"), NULL, 2048, false );
	});

	$f3->route('GET /download/@filename', function ($f3, $params) {
		$web = \Web::instance();
		$web->send("../files/" . $f3->get("PARAMS.filename"), NULL, 2048, true );
    });

    $f3->route('GET /logout', function ($f3, $params) {
		$f3->clear('SESSION');
		$f3->reroute('@home');
    });


	$f3->route("POST /v1-api/login", function($f3) {
			if ($f3->exists("POST.username") && $f3->exists("POST.password")) {
				global $database;
				global $users;
				//get all non admin users
				$users->reset();
				$users->load(array('name=?', $f3->get("POST.username")));
				echo ($users->admin == 1);
				if ($users->password === $f3->get("POST.password")) { //hash post password
					$f3->set('SESSION.loggedIn', true);
					$f3->set('SESSION.user', $f3->get("POST.username"));
					if ($users->admin) {
						$f3->set('SESSION.admin', true);
						$f3->reroute('@admin');
					} else {
						$f3->set('SESSION.admin', false);
						$f3->reroute('@user');
					}
				} else {
					//redirect to home screen with incorrect username/password error
					echo "incorrect username/password";
					//$f3->reroute('@home');
				}
			} else {
				//redirect to home screen missing username and/or password variables error
				echo "missing username and/or password variables";
				//$f3->reroute('@home');
			}
		}
	);


	$f3->run();

	include $f3->get("TEMPLATE_PATH") . "/footer.html";
?>


