$(document).ready(function() {
    $(".hamburger").on("click", function() {
        $(".mobile-navigation").slideToggle(function() {

        });
    });
    setTimeout(function() {
    	$("body").removeClass("preload");	
    }, 2000);
    
});

