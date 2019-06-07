var CYB = CYB || {};
CYB.CybProject = CYB.CybProject || {};

/**
 * Url switcher
 */
CYB.CybProject.UrlSwitcher = function () {
    /**
     * Default Settings
     * @type {Object}
     */
    var $container = {
        extension: $('.tx-urlswitcher')
    };

    /**
     * Default Settings
     * @type {Object}
     */
    var settings = {
        lastUrl: ''
    };

    /**
     * Timer
     * @type {Object}
     */
    this.timer = {
        previewImage: false
    };

    /**
     * Public function to initialize class
     * @return {void}
     */
    this.initialize = function () {
        if ($container.extension.length === 0) {
            return;
        }
        var instance = this;
        setUrlAndLink(instance);

        $container.extension.find('.radio-toolbar').find('form').submit(function (event) {
            event.preventDefault();
            return false;
        });
        $container.extension.find('.radio-toolbar').find('label').click(function () {
            var id = $(this).prop('for');
            var radioToolbar = $(this).closest('.radio-toolbar');
            radioToolbar.find('radio').prop('checked', false);
            radioToolbar.find('#' + id).prop('checked',true);
            setUrlAndLink(instance);
        });

        $container.extension.find('select').change(function () {
            setUrlAndLink(instance);
        });
        $container.extension.find('input[name=width]').change(function () {
            if ($container.extension.find('select[name=format]').val() !== 'width-height') {
                $container.extension.find('select[name=format]').val('width-width');
            }
            setUrlAndLink(instance);
        });
        $container.extension.find('input[name=height]').change(function () {
            $container.extension.find('select[name=format]').val('width-height');
            setUrlAndLink(instance);
        });
        $container.extension.find('input[name=text]').change(function () {
            setUrlAndLink(instance);
        });
        $container.extension.find('#switch-color').click(function (event) {
            var forecolor = $container.extension.find('input[name=forecolor]').val();
            var backcolor = $container.extension.find('input[name=backcolor]').val();
            $container.extension.find('input[name=forecolor]').val(backcolor);
            $container.extension.find('input[name=backcolor]').val(forecolor);

            var useForeColor = $container.extension.find('input[name=useForeColor]').prop('checked');
            var useBackColor = $container.extension.find('input[name=useBackColor]').prop('checked');
            $container.extension.find('input[name=useForeColor]').prop('checked', useBackColor);
            $container.extension.find('input[name=useBackColor]').prop('checked', useForeColor);

            setUrlAndLink(instance);

            event.preventDefault();
            return false;
        });

        $container.extension.find('input[name=forecolor]').change(function () {
            $container.extension.find('input[name=useForeColor]').prop('checked', true);
            setUrlAndLink(instance);
        });
        $container.extension.find('input[name=backcolor]').change(function () {
            $container.extension.find('input[name=useBackColor]').prop('checked', true);
            setUrlAndLink(instance);
        });
        $container.extension.find('input[name=useForeColor]').change(function () {
            setUrlAndLink(instance);
        });
        $container.extension.find('input[name=useBackColor]').change(function () {
            setUrlAndLink(instance);
        });

        $container.extension.find('select[name=font]').change(function () {
            setUrlAndLink(instance);
        });
        $container.extension.find('select[name=border]').change(function () {
            setUrlAndLink(instance);
        });

        $container.extension.find('.preview-image-refresh').click(function (event) {
            instance.changePreviewImage(true);
            event.preventDefault();
            return false;
        });

        /*instance.changePreviewImage(false);
        setInterval(function() {
            instance.changePreviewImage(false);
        }, 3000);*/
    };

    var setUrlAndLink = function (instance) {
        var url = generateUrl(instance, false, false);
        $container.extension.find('input[name=url]').val(url);
        $container.extension.find('.preview-image').prop('href', url);

        instance.changePreviewImage(false);
    };

    var generateUrl = function (instance, preview, previewForce) {
        var type = $container.extension.find('input[name=type]:checked').val();
        var category = $container.extension.find('input[name=category]:checked').val();
        var format = $container.extension.find('select[name=format]').val();
        var width = Number($container.extension.find('input[name=width]').val());
        var height = Number($container.extension.find('input[name=height]').val());
        var text = $container.extension.find('input[name=text]').val();
        var position = $container.extension.find('select[name=position]').val();
        var forecolor = $container.extension.find('input[name=forecolor]').val();
        var backcolor = $container.extension.find('input[name=backcolor]').val();
        var font = $container.extension.find('select[name=font]').val();
        var border = Number($container.extension.find('select[name=border]').val());
        var useForeColor = $container.extension.find('input[name=useForeColor]').prop('checked');
        var useBackColor = $container.extension.find('input[name=useBackColor]').prop('checked');

        if (type === 'image') {
            $container.extension.find('.select-category').slideDown(400);
            $container.extension.find('.switch-color').hide(400);
            $container.extension.find('.backcolor').hide(400);
        } else {
            $container.extension.find('.select-category').slideUp(400);
            $container.extension.find('.switch-color').show(400);
            $container.extension.find('.backcolor').show(400);
        }

        width = Math.max(16, Math.min(2048, width));
        height = Math.max(16, Math.min(2048, height));
        border = Math.max(0, Math.min(16, border));

        if (format === 'width-height') {
            format = width + 'x' + height;
        } else if (format === 'width-width') {
            format = width;
            height = format;
        } else if ({}.hasOwnProperty.call(formatTable, format)) {
            var dimensions = formatTable[format];
            width = dimensions[0];
            height = dimensions[1];
        }
        if (!preview) {
            $container.extension.find('input[name=width]').val(width);
            $container.extension.find('input[name=height]').val(height);
        }

        // Generate url
        var url = getBaseUrl() + '/' + type + '/' + format + '/';
        var urlParts = [];

        if (type === 'image') {
            if (category !== 'none') {
                url += category + '/';

                if (useForeColor) {
                    url += forecolor.substring(1) + '/';
                }
            } else {
                if (useForeColor) {
                    urlParts.push('forecolor=' + forecolor.substring(1));
                }
            }
        } if (type === 'text') {
            if (useForeColor) {
                url += forecolor.substring(1) + '/';

                if (useBackColor) {
                    url += backcolor.substring(1) + '/';
                }
            } else {
                if (useBackColor) {
                    urlParts.push('backcolor=' + backcolor.substring(1));
                }
            }
        } if (type === 'svg') {
            if (useForeColor) {
                url += forecolor.substring(1) + '/';

                if (useBackColor) {
                    url += backcolor.substring(1) + '/';
                }
            } else {
                if (useBackColor) {
                    urlParts.push('backcolor=' + backcolor.substring(1));
                }
            }
        }

        if (text !== '') {
            urlParts.push('text=' + encodeURI(text));
        }
        if (position !== '') {
            urlParts.push('position=' + encodeURI(position));
        }
        if (font !== '') {
            urlParts.push('font=' + encodeURI(font));
        }
        if (!isNaN(border) && border > 0) {
            urlParts.push('border=' + border);
        }

        if (preview && previewForce) {
            var date = new Date();
            urlParts.push('time=' + date.getTime());
        }

        if (urlParts.length > 0) {
            url += '?' + urlParts.join('&');
        }

        return url;
    };

    this.changePreviewImage = function (force) {
        var instance = this;
        var newUrlLink = generateUrl(instance, true, false);
        var newUrlImage = generateUrl(instance, true, force);
        var $image = $container.extension.find('.preview-image img');

        if (force === true || settings.lastUrl !== newUrlLink) {
            var my_image = new Image();
            my_image.className = 'img-fluid';
            my_image.onload = function() {
                $image.parent().html($(my_image));
            };
            my_image.src = newUrlImage;

            settings.lastUrl = newUrlLink;
            /*$image.fadeOut(1000, function() {
                $image.prop('src', newUrlImage);
            }).fadeIn(1000);*/
        }
    };

    /**
     * Get base url (fix for Internet Explorer)
     * @return {string}
     */
    var getBaseUrl = function() {
        if (!window.location.origin) {
            return window.location.protocol + '//' + window.location.hostname;
        }

        return window.location.origin;
    };

    var formatTable = {
        // Computer Display Standards - http://en.wikipedia.org/wiki/File:Vector_Video_Standards2.svg
        'cga': [320, 200],
        'qvga': [320, 240],
        'vga': [640, 480],
        'wvga': [800, 480],
        'svga': [800, 600],
        'wsvga': [1024, 600],
        'xga': [1024, 768],
        'wxga': [1280, 800],
        'wsxga': [1440, 900],
        'wuxga': [1920, 1200],
        'wqxga': [2560, 1600],

        // Video Standards
        'ntsc': [720, 480],
        'pal': [768, 576],
        'hd720': [1280, 720],
        'hd1080': [1920, 1080],
        '2k': [2048, 1920]
    };
};

jQuery(function($) {
    var urlSwitcher = new CYB.CybProject.UrlSwitcher();
    urlSwitcher.initialize();
});
