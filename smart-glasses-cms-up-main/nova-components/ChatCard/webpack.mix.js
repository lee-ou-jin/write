let mix = require("laravel-mix");

require("./nova.mix");

mix.setPublicPath("dist")
    .options({
        progress: false,
    })
    .js("resources/js/card.js", "js")
    .vue({ version: 3 })
    .css("resources/css/card.css", "css")
    .nova("jerry/chat-card");
