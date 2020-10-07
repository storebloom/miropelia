'use strict';

var gulp = require('./node_modules/gulp');
var sass = require('./node_modules/gulp-sass');
var babel = require( './node_modules/gulp-babel');
var uglify = require('./node_modules/gulp-uglify');
var terser = require('./node_modules/gulp-terser');
var pump = require('./node_modules/pump');
var browserify = require('./node_modules/gulp-browserify');
var rename = require('./node_modules/gulp-rename');

//sass
gulp.task('sass', function () {
	gulp.src(['assets/src/sass/*.scss', 'assets/dist/css/**/*.scss'])
	        .pipe(sass({outputStyle: 'compressed'}))
	        .pipe(gulp.dest('assets/dist/css'));
});

//js
gulp.task('js', function (cb) {
	pump([
			gulp.src('assets/src/js/*.js'),
			browserify({
				insertGlobals : true,
			}),
			babel({
				presets: ["@babel/preset-react"],
				env: {
					"production": {
						"presets": ["minify"],
						"plugins": ["transform-es2015-classes"]
					}
				}
			}),
			rename({
				suffix: '.min'
			}),
			gulp.dest('assets/dist/js')
		],
		cb
	);
});

// Default task
gulp.task('default', function () {
	gulp.start('sass');
	gulp.start('js');
});