const mix = require('laravel-mix')
require('laravel-mix-polyfill')

mix
.js('resources/js/app.js', 'public/js')
.sass('resources/scss/app.scss', 'public/css')
mix
