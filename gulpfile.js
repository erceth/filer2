var gulp = require("gulp");
var less = require('gulp-less');
var concat = require('gulp-concat');

gulp.task("less", function() {
	gulp.src(["less/main.less"])
		.pipe(less())
		.pipe(gulp.dest("public/styles/"));
	gulp.src(["bower_components/bootstrap/less/bootstrap.less"])
		.pipe(less())
		.pipe(concat("lib.css"))
		.pipe(gulp.dest("public/styles/"));
});

gulp.task("js", function() {
	gulp.src("js/**/*")
		.pipe(concat("main.js"))
		.pipe(gulp.dest("public/js/"));

	gulp.src(["bower_components/jquery/dist/jquery.min.js", "bower_components/bootstrap/dist/js/bootstrap.min.js"])
		.pipe(concat("lib.js"))
		.pipe(gulp.dest("public/js/"));

	gulp.src("bower_components/jquery/dist/jquery.min.map")
		.pipe(gulp.dest("public/js/"));
});

gulp.task("fonts", function() {
	gulp.src(["bower_components/bootstrap/fonts/*"])
		.pipe(gulp.dest("public/fonts/"));
});



//tasks to run when developing
gulp.task("dev", ["less", "js", "fonts"]);

gulp.task("watch", function () {
	gulp.watch(["less/**/*", "js/**/*"], ["dev"]);
});

gulp.task("default", ["dev", "watch"]);
