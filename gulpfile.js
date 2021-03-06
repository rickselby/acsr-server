const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {
    mix
        .sass([
            'app.scss'
        ], 'resources/assets/generated/sass.css')
        .styles([
            '../generated/sass.css',
            '../bower/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
            // done this way in case there's any plain CSS to add
        ])
        .scripts([
            '../bower/jquery/dist/jquery.js',
            '../bower/bootstrap-sass/assets/javascripts/bootstrap.js',
            '../bower/moment/min/moment.min.js',
            '../bower/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
        ])
        .copy('resources/assets/bower/bootstrap-sass/assets/fonts/bootstrap', 'public/vendor/fonts')
        .copy('resources/assets/bower/font-awesome/fonts', 'public/vendor/fonts')
        .copy('resources/images', 'public/images')
        .version([
            'css/all.css',
            'js/all.js',
            'public/images',
        ]);
});
