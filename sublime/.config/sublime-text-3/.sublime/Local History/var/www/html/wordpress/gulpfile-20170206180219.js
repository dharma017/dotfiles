;
(function () {
    'use strict';

    var gulp = require('gulp');
    var path = require('path');
    var wrap = require('gulp-wrap');
    var sass = require('gulp-sass');
    var watch = require('gulp-watch');
    var notify = require("gulp-notify");
    var concat = require('gulp-concat');
    var declare = require('gulp-declare');

    // Source Path
    var SOURCE_SASS = 'wp-content/themes/timber-starter-theme/assets/sass/**/*.scss';

    // Sass compilation
    gulp.task('sass', function () {
        gulp.src(SOURCE_SASS)
            .pipe(sass().on('error', sass.logError))
            .pipe(gulp.dest('wp-content/themes/timber-starter-theme/assets/css/'))
            .pipe(notify('Sass Compiled'));
    });

    // Watch task
    gulp.task('default', ['sass'], function () {
        gulp.watch(SOURCE_SASS, ['sass']);
     });

    // gulp.task('default', ['sass']);

})();
