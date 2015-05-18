<?php
	$f3 = require("../includes/f3/base.php");

	define("template_path", "../includes/templates");
	define("snippets_path", "../includes/snippets");

	$f3->set("navigation_path", "../includes/snippets/navigation.html");
	$template = new Template;
	echo $template->render(template_path . "/header.html");


	$f3->route('GET /',
	    function($f3) {
	    	global $template;
		    $datetime1 = new DateTime('2012-04-01');
			$datetime2 = new DateTime('now');
			$interval = $datetime1->diff($datetime2);
		    $f3->set("timeExperience", $interval->format('%y years %m month(s)'));
	        echo $template->render(template_path ."/home.html");
	    }
	);
	$f3->route('GET /about',
	    function() {
	        $view=new View;
	        echo $view->render(template_path ."/about.html");
	    }
	);
	$f3->route('GET /resume',
	    function() {
	        $view=new View;
	        echo $view->render(template_path ."/resume.html");
	    }
	);
	$f3->route('GET /projects',
	    function($f3) {
	    	global $template;
	    	$f3->set("webcrawler_short_description", snippets_path . "/webcrawler_short_description.html");
	    	$f3->set("webcrawler_medium_description", snippets_path . "/webcrawler_medium_description.html");
	        echo $template->render(template_path ."/projects.html");
	    }
	);
	$f3->route('GET /contact',
	    function() {
	        $view=new View;
	        echo $view->render(template_path ."/contact.html");
	    }
	);


	$f3->run();

	include template_path . "/footer.html";
?>


