// Symfony said require scss
require('../css/app.scss');

/* fix for https://github.com/symfony/webpack-encore/pull/54 */
global.$ = global.jQuery = require('jquery');

require('bootstrap');

require('./javascript.js');
require('./fullscreen.js');
// require('./0-top/javascript.js');
