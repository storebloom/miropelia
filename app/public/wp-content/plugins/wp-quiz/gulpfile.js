const gulp = require('gulp');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');
const removeEmptyLines = require('gulp-remove-empty-lines');
const zip = require('gulp-zip');
// const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const babel = require('gulp-babel');
const babelHelpers = require('gulp-babel-external-helpers');

const sassOptions = {
	errLogToConsole: true,
	outputStyle: 'compressed'
};

const backendJsSrc = 'assets/admin/src/**/*.js';

gulp.task('sass', function() {
	return gulp
		.src('assets/frontend/scss/*.scss')
		.pipe(sourcemaps.init())
		.pipe(sass(sassOptions).on('error', sass.logError))
		.pipe(autoprefixer())
		.pipe(sourcemaps.write('../sourcemaps'))
		.pipe(gulp.dest('assets/frontend/css'));
});

gulp.task('admin-sass', function() {
	return gulp
		.src('assets/admin/scss/*.scss')
		.pipe(sourcemaps.init())
		.pipe(sass(sassOptions).on('error', sass.logError))
		.pipe(autoprefixer())
		.pipe(sourcemaps.write('../sourcemaps'))
		.pipe(gulp.dest('assets/admin/css'));
});

gulp.task('sass-no-maps', function() {
	return gulp
		.src('assets/frontend/scss/*.scss')
		.pipe(sass(sassOptions).on('error', sass.logError))
		.pipe(autoprefixer())
		.pipe(removeEmptyLines())
		.pipe(gulp.dest('assets/frontend/css'));
});

gulp.task('admin-sass-no-maps', function() {
	return gulp
		.src('assets/admin/scss/*.scss')
		.pipe(sass(sassOptions).on('error', sass.logError))
		.pipe(autoprefixer())
		.pipe(removeEmptyLines())
		.pipe(gulp.dest('assets/admin/css'));
});

gulp.task('js', () => {
	return gulp
		.src('assets/frontend/src/**/*.js')
		.pipe(sourcemaps.init())
		// .pipe(concat('wp-quiz.js'))
		.pipe(babel())
		.pipe(babelHelpers('babel-helpers.js'))
		// .pipe(uglify())
		.pipe(sourcemaps.write('../sourcemaps'))
		.pipe(gulp.dest('assets/frontend/js'));
});

gulp.task('admin-js', () => {
	return gulp
		.src(backendJsSrc)
		.pipe(sourcemaps.init())
		// .pipe(concat('admin.js'))
		.pipe(babel())
		.pipe(babelHelpers('babel-helpers.js'))
		// .pipe(uglify())
		.pipe(sourcemaps.write('../sourcemaps'))
		.pipe(gulp.dest('assets/admin/js'));
});

gulp.task('js-no-maps', () => {
	return gulp
		.src('assets/frontend/src/**/*.js')
		// .pipe(concat('wp-quiz.js'))
		.pipe(babel())
		.pipe(babelHelpers('babel-helpers.js'))
		.pipe(uglify())
		.pipe(gulp.dest('assets/frontend/js'));
});

gulp.task('admin-js-no-maps', () => {
	return gulp
		.src(backendJsSrc)
		// .pipe(concat('admin.js'))
		.pipe(babel())
		.pipe(babelHelpers('babel-helpers.js'))
		.pipe(uglify())
		.pipe(gulp.dest('assets/admin/js'));
});

gulp.task('watch', () => {
	gulp.watch('assets/admin/**/*.scss', gulp.series('admin-sass'));
	gulp.watch('assets/frontend/**/*.scss', gulp.series('sass'));
	gulp.watch('assets/admin/src/**/*.js', gulp.series('admin-js'));
	gulp.watch('assets/frontend/src/**/*.js', gulp.series('js'));
});

gulp.task('dev', gulp.series('sass', 'admin-sass', 'js', 'admin-js'));
gulp.task('default', gulp.series('sass-no-maps', 'admin-sass-no-maps', 'js-no-maps', 'admin-js-no-maps'));
