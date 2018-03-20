// Require elixir
const elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application as well as publishing vendor resources.
 |
 */

elixir((mix) => {
    mix.sass('style.scss', 'public/css/style.css');
    mix.sass('mail.scss', 'resources/views/mail/style.css');
    mix.sass('admin/custom-theme.scss', 'public/admin-theme/custom-theme.css');
    mix.sass('admin/daterangepicker.scss', 'public/admin-theme/daterangepicker.css');
    mix.scriptsIn('resources/assets/js/admin/', 'public/admin-theme/custom-theme.js');
});
