const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css');

mix.js('resources/js/takings/passengers/liquidation/main.js', 'resources/js/takings/passengers/liquidation/');
mix.js('resources/js/reports/passengers/sensors/cameras/main.js', 'resources/js/reports/passengers/sensors/cameras/');


if (mix.inProduction()) {
    mix.version();
}