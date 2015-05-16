<div class="col-xs-12 home page">
    <div class="col-xs-12">
        <div class="splash">
            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                <!-- Indicators -->
                <ol class="carousel-indicators">
                    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                </ol>
                <!-- Wrapper for slides -->
                <div class="carousel-inner" role="listbox">
                    <div class="item active">
                        <img src="img/tanks2.png" alt="j.s. flags gameplay">
                        <a ui-sref="jsflags">
                            <div class="carousel-caption">
                                JSFlags -- A multiplayer game of capture the flag with tanks. Game engine and artifical intelligence written completely in Javascript.
                            </div>
                        </a>
                    </div>
                    <div class="item">
                        <img id="jsflags-tournament" src="img/jsflags_tournament.jpg" alt="watching j.s. flags on projector">
                        <a ui-sref="jsflags">
                            <div class="carousel-caption">
                                JSFlags tournament at Unicity. Participates created an AI to compete for dominance.
                            </div>
                        </a>
                    </div>
                    <div class="item">
                        <img src="img/google-sheets.png" alt="...">
                        <a ui-sref="sms-tracker">
                            <div class="carousel-caption">
                                SMS Tracker -- Tracks attendence of classroom using SMS texts. Attendence is saved to Mongo Database and shared using Google Sheets.
                            </div>
                        </a>
                    </div>
                </div>
                <!-- Controls -->
                <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </div>
    <div class="blobs col-xs-12 no-margin">
    	<div class="blob-container col-xs-12 col-md-4">
    		<div class="blob">
	    		<h3>About Eric</h3>
	    		<p>I am a Front-end Web Developer with <span><?php echo $timeExperience; ?></span> of experience.</p>  
	    		<p>This sites allows me to show off my projects.</p>
	    		<div><a ui-sref="about">Learn more</a></div>
	    	</div>
    	</div>
    	<div class="blob-container col-xs-12 col-md-4">
    		<div class="blob">
	    		<h3>My Projects</h3>
	    		<p>Personal projects and work projects I've created over the years</p>
	    		<div><a ui-sref="projects">Learn more</a></div>
    		</div>
    	</div>
    	<div class="blob-container col-xs-12 col-md-4">
    		<div class="blob">
	    		<h3>Contact</h3>
	    		<p>My contact information</p>
	    		<div><a ui-sref="contact">Learn more</a></div>
	    	</div>
    	</div>
    </div>
</div>
