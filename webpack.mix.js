let mix = require('laravel-mix');

mix.postCss("resources/css/style.css", "resources/dist", [
        require("tailwindcss"),
    ]).options({
    processCssUrls: false,
    uglify: {
        comments: false
    }
});
