/****************************
 * development Mode
 * for conditional building
 ****************************/

/**
 * Note this workflow  is to stick with traditional way of using develpemnt with native IDE eg eclipse for android and xcode for ios
 * need to make minor modifications to make it work with cli
 */

var IS_PRODUCTION_MODE = true;
var IS_DEVELOPMENT_MODE = !IS_PRODUCTION_MODE;



var www_base = '../www';
//var www_base = '../android/platforms/android/assets/www';

var WORKING_DIR_BASE = './www';
/****************************
 * project specific paths
 ****************************/

var paths = {
    html: {
        src: WORKING_DIR_BASE + '/index.html'
    },
    /**
     * Note in BIP in mobileinit.js a conditional loading of few js and css files is made based on the platform [version]
     * so keep following files
     * /mobiscrill
     *     /css
     *         |- mobiscroll.ios7.css
     *         |- mobiscroll.ios.css
     *         |- mobiscroll.android.css
     *     /js
     *         |- mobiscroll.ios7.js
     *         |- mobiscroll.ios.js
     *         |- mobiscroll.android.js
     *
     *
     * if possible minified version can be kept
     */
    scripts: {
        src: [

            //WORKING_DIR_BASE + '/js/jquery.mobile-1.3.2.min.js',
            WORKING_DIR_BASE + '/js/jquery-1.9.1.min.js',
            WORKING_DIR_BASE + '/js/jquery.mobile-1.3.2.js',
            WORKING_DIR_BASE + '/js/webservice.js',
            WORKING_DIR_BASE + '/js/sqlhelper.js',
            WORKING_DIR_BASE + '/js/offlinehelper.js',
            //WORKING_DIR_BASE + '/js/isPhoneGap.js',
            WORKING_DIR_BASE + '/js/date.format.js',
            WORKING_DIR_BASE + '/js/stopwatch.js',
            WORKING_DIR_BASE + '/js/moment.js',
            WORKING_DIR_BASE + '/js/filehelper.js',
            WORKING_DIR_BASE + '/js/rangeslider.min.js',
            //old
            //WORKING_DIR_BASE + '/mobiscroll/js/mobiscroll.core.js',
            //WORKING_DIR_BASE + '/mobiscroll/js/mobiscroll.datetime.js',
            //WORKING_DIR_BASE + '/mobiscroll/js/i18n/mobiscroll.i18n.sv.js',


            //new 
            WORKING_DIR_BASE + '/mobiscroll/js/mobiscroll.core.js',
            WORKING_DIR_BASE + '/mobiscroll/js/mobiscroll.scroller.js',
            WORKING_DIR_BASE + '/mobiscroll/js/mobiscroll.datetime.js',
            WORKING_DIR_BASE + '/mobiscroll/js/i18n/mobiscroll.i18n.sv.js',
            WORKING_DIR_BASE + '/mobiscroll/js/mobiscroll.scroller.android.js',
            WORKING_DIR_BASE + '/mobiscroll/js/mobiscroll.scroller.ios.js',
            WORKING_DIR_BASE + '/mobiscroll/js/mobiscroll.scroller.ios7.js',
            WORKING_DIR_BASE + '/js/jquery.getScriptSync.js',
            WORKING_DIR_BASE + '/js/iscroll.js',
            WORKING_DIR_BASE + '/js/config.js',
            WORKING_DIR_BASE + '/js/app.js',
            WORKING_DIR_BASE + '/js/common.js',
            WORKING_DIR_BASE + '/js/tasks.js',
            WORKING_DIR_BASE + '/js/registration.js',
            WORKING_DIR_BASE + '/js/bipappv2.js',
            WORKING_DIR_BASE + '/js/reportgraphs.js',
            WORKING_DIR_BASE + '/js/verticalSlider.js',
            WORKING_DIR_BASE + '/js/mobileinit.js',
            WORKING_DIR_BASE + '/js/jstorage.js',
            WORKING_DIR_BASE + '/js/parse.js'

        ]
    },
    styles: {
        src: [
            WORKING_DIR_BASE + '/css/jquery.mobile-1.3.2.css',
            WORKING_DIR_BASE + '/css/core.css',
            WORKING_DIR_BASE + '/css/iscroll.css',
            WORKING_DIR_BASE + '/css/rangeslider.css',
            //old
            //WORKING_DIR_BASE + '/mobiscroll/css/mobiscroll.core.css'

            //new
            WORKING_DIR_BASE + '/mobiscroll/css/mobiscroll.icons.css',
            WORKING_DIR_BASE + '/mobiscroll/css/mobiscroll.scroller.css',
            WORKING_DIR_BASE + '/mobiscroll/css/mobiscroll.scroller.android.css',
            WORKING_DIR_BASE + '/mobiscroll/css/mobiscroll.scroller.ios.css',
            WORKING_DIR_BASE + '/mobiscroll/css/mobiscroll.scroller.ios7.css',


            //WORKING_DIR_BASE + '/css/qpark.css',
            // WORKING_DIR_BASE + '/css/css/jquery.mobile.iscrollview.css',
            //WORKING_DIR_BASE + '/css/css/demo.css',
            //WORKING_DIR_BASE + '/css/jquery.mobile.fixedToolbar.polyfill.css'
        ]
    },
    images: {
        src: [WORKING_DIR_BASE + '/images/**/*', WORKING_DIR_BASE + '/css/images/**/*']
    },
    audios: {
        src: [WORKING_DIR_BASE + '/audios/**/*']
    }
};



console.log('is producton mode ' + IS_PRODUCTION_MODE);
/****************************
  node modules
 ****************************/

var gulp = require('gulp'),
    pkg = require('./package.json'),
    changed = require('gulp-changed'), //do tasks only for changed stuffs
    jshint = require('gulp-jshint'), //for js linting
    clean = require('gulp-clean'), //to delete files and folders
    concat = require('gulp-concat'), //to concat files
    stripDebug = require('gulp-strip-debug'), //to eliminate the debug scripts
    uglify = require('gulp-uglify'), //uglify(minify) js
    beautify = require('gulp-beautify'), //beautify js
    prettifyHTML = require('gulp-prettify'), //prettify html
    rename = require("gulp-rename"),
    imagemin = require('gulp-imagemin'),
    minifyHTML = require('gulp-minify-html'),
    //autoprefix = require('gulp-autoprefixer'),
    minifyCSS = require('gulp-minify-css'),
    gulpif = require('gulp-if'),
    preprocess = require('gulp-preprocess'),
    connect = require('gulp-connect');
// watch = require('gulp-watch');

/****************************
 * configure gulp tasks
 ****************************/


gulp.task('htmlpage', function() {

    /**
     * Same HTML page for all platforms
     *
     */
    gulp.src(paths.html.src)
    //pipe(preprocess())
    // .pipe(changed('./www'))
    .pipe(minifyHTML())
        .pipe(gulp.dest(www_base));


    gulp.src(WORKING_DIR_BASE + '/app.html')
    //pipe(preprocess())
    // .pipe(changed('./www'))
    .pipe(minifyHTML())
        .pipe(gulp.dest(www_base));

});

gulp.task('clean_scripts', function() {
    //only clean js inside of /js directory since phonegap.js  (with same name has different scripts withing the two platform directories)
    // ie do not touch phonegap.js
    gulp
        .src([ www_base + '/js/**/combined.js'], {
            read: false //do not read before removing ( is more faster )
        })
        .pipe(clean({
            force: true //safely removes files outside current directory
        }));
});

gulp.task('scripts', ['clean_scripts'], function() {
    /**
     * make sure you concat js files with semicolon (;) as file separater
     * otherwise you'll get js errors
     */
    gulp.src(paths.scripts.src)
        .pipe(concat('combined.js', {
            newLine: ';'
        }))
    //remove comments and console.log for production
    //.pipe(gulpif(IS_PRODUCTION_MODE, stripDebug()))
    // .pipe(gulpif(IS_PRODUCTION_MODE, uglify({
    //     mangle: false,
    //     preserveComments: 'all',
    //     outSourceMap: true,
    //     compress: true
    // })))
    .pipe(gulpif(IS_DEVELOPMENT_MODE, beautify()))

    //for production uglify otherwise beautify
    /*
    .pipe(gulpif(IS_DEVELOPMENT_MODE, uglify({
        mangle: false,
        preserveComments: 'all',
        outSourceMap: true,
        compress: true
    }), beautify()))
    */

    //.pipe(rename(pkg.name + '.js'))
    //.pipe(rename("combined.js"))
        .pipe(gulp.dest(www_base + '/js'));

    //.pipe(gulp.dest('../assets/www/scripts.min.js'))

    //.pipe(jshint())
    //.pipe(jshint.reporter('default'));
    //

});

gulp.task('clean_styles', function() {

    //only clean js inside of /css directory since  this project has few scripts inside /mobiscroll folder for conditional loading from inside the app code
    gulp
        .src([www_base + '/css/**/*.css'], {
            read: false //do not read before removing ( is more faster )
        })
        .pipe(clean({
            force: true //safely removes files outside current directory
        }));


});
// CSS concat, auto-prefix and minify
gulp.task('styles', ['clean_styles'], function() {
    gulp
        .src(paths.styles.src)
    //.pipe(concat(pkg.name + '-final.js'))
    .pipe(concat('combined.css'))
    //.pipe(autoprefix('last 2 versions'))
    .pipe(minifyCSS())
        .pipe(gulp.dest(www_base + '/css'));;
});



//image minificaion
gulp.task('image', function() {

    //gulp.src(['../assets/www/css/images/**/*', '../assets/www/img/**/*'], {

    gulp.src(WORKING_DIR_BASE + '/css/images/**/*')
        .pipe(imagemin())
        .pipe(gulp.dest(www_base + '/css/images'));


    gulp.src(WORKING_DIR_BASE + '/images/**/*')
        .pipe(imagemin())
        .pipe(gulp.dest(www_base + '/images'));

});


//image minificaion
gulp.task('image-copy', function() {

    //gulp.src(['../assets/www/css/images/**/*', '../assets/www/img/**/*'], {

    gulp.src(WORKING_DIR_BASE + '/css/images/**/*')
    //.pipe(imagemin())
        .pipe(gulp.dest(www_base + '/css/images'));


    gulp.src(WORKING_DIR_BASE + '/images/**/*')
    //.pipe(imagemin())
        .pipe(gulp.dest(www_base + '/images'));



});

gulp.task('fonts-copy', function() {

    gulp.src(WORKING_DIR_BASE + '/css/fonts/*')
        .pipe(gulp.dest(www_base + '/css/fonts'));

});

gulp.task('audios-copy', function() {

     gulp.src(WORKING_DIR_BASE + '/audios/**/*')
    //.pipe(imagemin())
        .pipe(gulp.dest(www_base + '/audios'));

});

//JS hint task

// too many js hint errors
gulp.task('jshint', function() {
    gulp.src(paths.scripts.src)
    pipe(jshint('.jshintrc'))
        .pipe(jshint.reporter('default'));
});


//local development webserver
gulp.task('connect', function() {
    connect.server({
        root: www_base,
        livereload: true
    });
});

gulp.task('connect-wd', function() {
    connect.server({
        root: WORKING_DIR_BASE,
        port: 8081,
        livereload: true
    });
});

//JS hint task

gulp.task('default', ['htmlpage', 'scripts', 'styles','image-copy','fonts-copy','audios-copy'], function() {
    // watch for changes
    gulp.watch(paths.html.src, ['htmlpage']);
    gulp.watch(paths.scripts.src, ['scripts']);
    gulp.watch(paths.styles.src, ['styles']);
    gulp.watch(paths.styles.src, ['image-copy']);
    gulp.watch(paths.styles.src, ['fonts-copy']);
    gulp.watch(paths.styles.src, ['audios-copy']);
});

gulp.task('dev', function() {
    // watch for changes
    gulp.watch(paths.html.src, ['htmlpage']);
    gulp.watch(paths.scripts.src, ['scripts']);
    gulp.watch(paths.styles.src, ['styles']);
});


gulp.task('dev-scripts', ['scripts'], function() {
    // watch for changes
    gulp.watch(paths.scripts.src, ['scripts']);
});