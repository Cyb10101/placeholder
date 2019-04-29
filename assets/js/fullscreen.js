window.CYB = window.CYB || {};

// var fullscreen = new window.CYB.Fullscreen();
// fullscreen.toggleFullscreen(this);
window.CYB.Fullscreen = function () {

    this.launchFullscreen = function (element) {
        var method = element.requestFullscreen;
        if (!method) {
            ['mozRequestFullScreen', 'webkitRequestFullScreen', 'msRequestFullscreen'].forEach(function (required) {
                method = method || element[required];
            });
        }
        method.call(element);
    };

    this.exitFullscreen = function() {
        var method = document.exitFullscreen;
        if (!method) {
            ['mozCancelFullScreen', 'webkitExitFullscreen', 'msExitFullscreen'].forEach(function (required) {
                method = method || document[required];
            });
        }
        method.call(document);
    };

    this.toggleFullscreen = function(element) {
        if (typeof this.getFullscreenElement() === 'undefined') {
            this.launchFullscreen(element);
        } else {
            this.exitFullscreen();
        }
    };

    this.getFullscreenElement = function () {
        return document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || document.msFullscreenElement;
    };

    this.isFullscreenEnabled = function () {
        return document.fullscreenEnabled || document.mozFullScreenEnabled || document.webkitFullscreenEnabled || document.msFullscreenEnabled;
    };

    this.dumpFullscreen = function () {
        console.log('Fullscreen is enabled:', this.isFullscreenEnabled(), 'Element is:', this.getFullscreenElement());
    };

    this.initializeEventLog = function () {
        $(document).on('fullscreenchange', function(event) {
            console.log('fullscreenchange event!', event);
        });
        $(document).on('mozfullscreenchange', function(event) {
            console.log('mozfullscreenchange event!', event);
        });
        $(document).on('webkitfullscreenchange', function(event) {
            console.log('webkitfullscreenchange event!', event);
        });
        $(document).on('msfullscreenchange', function(event) {
            console.log('msfullscreenchange event!', event);
        });
    };
};
