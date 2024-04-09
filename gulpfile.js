const { src, dest } = require('gulp');
const concat = require('gulp-concat');
const terser = require('gulp-terser');
const sourcemaps = require('gulp-sourcemaps');
const postcss = require('gulp-postcss');
const cssnano = require('cssnano');
const autoprefixer = require('autoprefixer');

const minify = require('gulp-clean-css');
const minifyJs = require('gulp-uglify');

const cssPath = 'assets/front_end/modern/css/*.css';

function cssBundle() {
    return src([
        'assets/front_end/modern/css/bootstrap.min.css',
        'assets/front_end/modern/css/iziModal.min.css',
        'assets/front_end/modern/css/intlTelInput.css',
        'assets/front_end/modern/css/plugins.css',
        'assets/front_end/modern/css/all.min.css',
        'assets/front_end/modern/css/swiper-bundle.min.css',
        'assets/front_end/modern/css/bootstrap-tabs-x.min.css',
        'assets/front_end/modern/css/sweetalert2.min.css',
        'assets/front_end/modern/css/select2.min.css',
        'assets/front_end/modern/css/select2-bootstrap4.min.css',
        'assets/front_end/modern/css/star-rating.min.css',
        'assets/front_end/modern/css/theme.min.css',
        'assets/front_end/modern/css/theme.css',
        'assets/front_end/modern/css/daterangepicker.css',
        'assets/front_end/modern/css/bootstrap-table.min.css',
        'assets/front_end/modern/css/lightbox.css',
    ])
        .pipe(sourcemaps.init())
        .pipe(concat('eshop-bundle.css'))
        .pipe(postcss([autoprefixer(), cssnano()])) //not all plugins work with postcss only the ones mentioned in their documentation
        .pipe(sourcemaps.write('.'))
        .pipe(dest('assets/front_end/modern/css'));
}
exports.cssBundle = cssBundle;

function cssBundleMain() {
    return src([
        'assets/front_end/modern/css/bootstrap.min.css',
        'assets/front_end/modern/css/style.css',
        'assets/front_end/modern/css/products.css',
        'assets/front_end/modern/css/custom.css',
    ])
        .pipe(sourcemaps.init())
        .pipe(concat('eshop-bundle-main.css'))
        .pipe(postcss([autoprefixer(), cssnano()])) //not all plugins work with postcss only the ones mentioned in their documentation
        .pipe(sourcemaps.write('.'))
        .pipe(dest('assets/front_end/modern/css'));
}
exports.cssBundleMain = cssBundleMain;

function cssBundleMainRTL() {
    return src([
        'assets/front_end/modern/css/bootstrap.min.css',
        'assets/front_end/modern/css/style.css',
        'assets/front_end/modern/css/custom.css',
        'assets/front_end/modern/css/products.css',
    ])
        .pipe(sourcemaps.init())
        .pipe(concat('eshop-bundle-main.css'))
        .pipe(postcss([autoprefixer(), cssnano()])) //not all plugins work with postcss only the ones mentioned in their documentation
        .pipe(sourcemaps.write('.'))
        .pipe(dest('assets/front_end/modern/css/rtl'));
}
exports.cssBundleMainRTL = cssBundleMainRTL;



// minifying js
const jsBundle = () =>
    src([
        'assets/front_end/modern/js/iziModal.min.js',
        'assets/front_end/modern/js/popper.min.js',
        'assets/front_end/modern/js/bootstrap.min.js',
        'assets/front_end/modern/js/swiper-bundle.min.js',
        'assets/front_end/modern/js/select2.full.min.js',
        'assets/front_end/modern/js/star-rating.min.js',
        'assets/front_end/modern/js/theme.min.js',
        'assets/front_end/modern/js/bootstrap-tabs-x.min.js',
        'assets/front_end/modern/js/jquery.ez-plus.js',
        'assets/front_end/modern/js/bootstrap-table.min.js',
        'assets/front_end/modern/js/jquery.blockUI.js',
        'assets/front_end/modern/js/sweetalert2.min.js',
        'assets/front_end/modern/js/modernizr-custom.js',
        'assets/front_end/modern/js/lazyload.min.js',
        'assets/front_end/modern/js/intlTelInput.js',
        'assets/front_end/modern/js/lightbox.js',
        'assets/front_end/modern/js/custom.js',
        'assets/front_end/modern/js/plugins.js',
        'assets/front_end/modern/js/theme.js',

    ])
        .pipe(concat('eshop-bundle-js.js'))
        .pipe(minifyJs())
        .pipe(dest('assets/front_end/modern/js/'));

exports.jsBundle = jsBundle;

const topJsBundle = () =>
    src([
        'assets/front_end/modern/js/moment.min.js',
        'assets/front_end/modern/js/daterangepicker.js',
    ])
        .pipe(concat('eshop-bundle-top-js.js'))
        .pipe(minifyJs())
        .pipe(dest('assets/front_end/modern/js/'));

exports.topJsBundle = topJsBundle;




