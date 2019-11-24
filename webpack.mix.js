'use strict';

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



mix.sass('resources/assets/sass/app.scss', 'public/css/app.css');

mix.js('resources/assets/js/app.js', 'public/js/app.js');
mix.js('resources/assets/js/game-chart.js', 'public/js/game-chart.js');



mix.browserSync({port: '8081', proxy: 'localhost:8081'});



  // Full API
  // mix.extract(vendorLibs);
  // mix.sass(src, output);
  // mix.less(src, output);
  // mix.browserSync('my-site.dev');
  // mix.combine(files, destination);
  // mix.copy(from, to);
  // mix.minify(file);
  // mix.sourceMaps(); // Enable sourcemaps
  // mix.version(); // Enable versioning.
  mix.disableNotifications();
  // mix.setPublicPath('path/to/public');
  // mix.autoload({}); <-- Will be passed to Webpack's ProvidePlugin.
  // mix.webpackConfig({}); <-- Override webpack.config.js, without editing the file directly.
  // mix.then(function () {}) <-- Will be triggered each time Webpack finishes building.
  // mix.options({
  //   extractVueStyles: false // default
  // });
