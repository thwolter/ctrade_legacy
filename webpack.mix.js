const { mix } = require('laravel-mix');

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

 
mix.js('resources/assets_original/js/app.js', 'public/js/app_1.js')
   .sass('resources/assets_original/sass/app.scss', 'public/css');

/*
mix.js([
    'resources/assets/js/app.js',
    'node_modules/jquery/dist/jquery.min.js',
    'node_modules/bootstrap/dist/js/bootstrap.js'
], 'public/js')
    .sass('resources/assets/scss/app.scss', 'public/css');
*/