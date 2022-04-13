const mix = require("laravel-mix");

mix.disableSuccessNotifications();

mix.setPublicPath("./resources/dist")
    .postCss("./resources/css/filament-curator.css", "filament-curator.css", [
        require("tailwindcss/nesting")(),
        require("tailwindcss")("./tailwind.config.js"),
    ])
    .options({
        processCssUrls: false,
    });
