const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.copyDirectory('resources/assets/dashboard/images', 'public/assets/images');
mix.copyDirectory('resources/assets/dashboard/lang', 'public/assets/lang');
mix.copyDirectory('resources/assets/dashboard/dist', 'public/assets/dist');
