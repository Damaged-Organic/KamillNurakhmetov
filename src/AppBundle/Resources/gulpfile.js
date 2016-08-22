var gulp = require("gulp"),
    config = require("./config"),

    glob = require("glob"),
    exec = require("child_process").exec,

	imagemin = require("gulp-imagemin"),
	pngquant = require("imagemin-pngquant");


gulp.task("js", function(){
    var files = glob.sync(config.js + "app/*.js"),
        fileName, bundle;

	files.map(function(entryFile){
        fileName = entryFile.match(/\w+(?=\.js)/gi)[0];
        bundle = exec("jspm bundle-sfx "+ entryFile +" "+ config.build +"js/"+ fileName +".bundle.min.js --minify --skip-source-maps");
	});

});

gulp.task("css", function(){

    var files = glob.sync(config.css + "*.less"),
        fileName, bundle;

	files.map(function(entry){
        fileName = entry.match(/\w+(?=\.less)/gi)[0];
        bundle = exec('lessc --clean-css '+ entry +' --autoprefix="last 2 versions" '+ config.build +'/css/'+ fileName +'.bundle.min.css');
	});

});

gulp.task("fonts", function(){
	gulp.src(config.font + "**/*.*")
		.pipe(gulp.dest(config.build + "fonts"));
});

gulp.task("images", function(){

	gulp.src(config.img + "**/*.*")
		.pipe(imagemin({
			optimizationLevel: 4,
			progressive: true,
			svgoPlugins: [{removeViewBox: false}],
			use: [pngquant()]
		}))
		.pipe(gulp.dest(config.build + "images/"));
});

gulp.task("watcher", function(){
    //gulp.watch(config.js + "**/*.js", ["js"]);
	gulp.watch(config.css + "**/*.less", ["css"]);
	gulp.watch(config.font + "**/*.*", ["fonts"]);
	gulp.watch(config.img + "**/*.*", ["images"]);
});

gulp.task("default", ["css", "fonts", "images", "js", "watcher"]);
