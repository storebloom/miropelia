'use strict';

const { dest, series, src, task, watch } = require('./node_modules/gulp');
const babel = require('gulp-babel');
const dotenv = require('dotenv').config();
const gulpSass = require('gulp-sass');
const rename = require('gulp-rename');
const uglify = require('gulp-uglify');

if (dotenv.error) {
	throw dotenv.error;
}

// Read .env file parsed values into config variable.
const config = dotenv.parsed;

// Set up local domain (default to localhost).
const localDomain = typeof config.DOMAIN === 'string' ? config.DOMAIN : 'localhost';

/**
 * Source path mappings.
 */
const paths = {
	php: {
		src: ['.*php', './**/*.php'],
	},
	scripts: {
		src: ['assets/src/js/*.js'],
		dest: 'assets/dist/js',
	},
	scriptVendors: {
		src: ['assets/src/js/vendor/*.js'],
		dest: 'assets/dist/vendor/js',
	},
	styles: {
		src: ['assets/src/sass/*.scss', 'assets/src/sass/**/*.scss'],
		dest: 'assets/dist/css',
	}
};

/**
 * Build JavaScript for vendors.
 *
 * @param cb
 */
function jsVendors(cb) {
	src(paths.scriptVendors.src)
		.pipe(uglify())
		.pipe(rename({ suffix: '.min' }))
		.pipe(dest(paths.scriptVendors.dest));

	cb();
}

/**
 * Build JavaScript from JS source files.
 *
 * @param cb
 */
function js(cb) {
	src(paths.scripts.src)
		.pipe(
			babel({
				presets: ['@babel/preset-react'],
				env: {
					production: {
						presets: ['minify'],
						plugins: ['transform-es2015-classes'],
					},
				},
			})
		)
		.pipe(rename({ suffix: '.min' }))
		.pipe(dest(paths.scripts.dest));

	cb();
}

/**
 * Build CSS from SASS (SCSS) source files.
 *
 * @param cb
 */
function sass(cb) {
	src(paths.styles.src)
		.pipe(gulpSass({ outputStyle: 'compressed' }))
		.pipe(dest(paths.styles.dest));

	cb();
}

/**
 * Watch SASS/JS files and write to dist.
 *
 * @param cb
 */
function watcher(cb) {
	watch([...paths.scripts.src, ...paths.styles.src, ...paths.php.src], series(js, sass, jsVendors));
	cb();
}

// Exports.
exports.jsVendors = task(jsVendors);
exports.js = task(js);
exports.sass = task(sass);

exports.build = series(sass, js, jsVendors);
exports.watch = watcher;
