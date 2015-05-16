<?php
	$f3 = require("../includes/f3/base.php");

	define("template_path", "../includes/templates");

	include template_path . "/header.html";



	$f3->route('GET /',
	    function($f3) {
		    $datetime1 = new DateTime('2012-04-01');
			$datetime2 = new DateTime('now');
			$interval = $datetime1->diff($datetime2);
		    $f3->set("timeExperience", $interval->format('%y years %m month(s)'));
	        $template=new Template;
	        echo $template->render(template_path ."/home.html");
	    }
	);
	$f3->route('GET /about',
	    function() {
	        //$f3->set('name','world');
	        $view=new View;
	        echo $view->render(template_path ."/about.html");
	    }
	);
	$f3->route('GET /resume',
	    function() {
	        //$f3->set('name','world');
	        $view=new View;
	        echo $view->render(template_path ."/resume.html");
	    }
	);
	$f3->route('GET /projects',
	    function() {
	        //$f3->set('name','world');
	        $view=new View;
	        echo $view->render(template_path ."/projects.html");
	    }
	);
	$f3->route('GET /contact',
	    function() {
	        //$f3->set('name','world');
	        $view=new View;
	        echo $view->render(template_path ."/contact.html");
	    }
	);

	$f3->run();
?>


<?php 
	include template_path . "/footer.html";
?>

