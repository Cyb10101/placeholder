<?php
namespace App\Utility;

/**
 * Class RequestUtility
 */
abstract class RequestUtility {
    protected static function _filter($type, $keyOrVariable, $filter = FILTER_DEFAULT, $options = null, $default = null) {
        if ($type === 'filter_var') {
            $variable = filter_var($keyOrVariable, $filter, $options);
        } else {
            $variable = filter_input($type, $keyOrVariable, $filter, $options);
        }

        if ($default !== null && empty($variable)) {
            return $default;
        }
        return $variable;
    }

    // _GET
    public static function get(string $name, $default = null, $filter = FILTER_DEFAULT) {
        return self::_filter(INPUT_GET, $name, $filter, null, $default);
    }

    public static function getBool(string $name, $default = null) {
        return self::get($name, $default, FILTER_VALIDATE_BOOLEAN);
    }

    public static function getInt(string $name, $default = null) {
        return self::get($name, $default, FILTER_VALIDATE_INT);
    }

    public static function getFloat(string $name, $default = null) {
        return self::get($name, $default, FILTER_VALIDATE_FLOAT);
    }

    public static function getEmail(string $name, $default = null) {
        return self::get($name, $default, FILTER_VALIDATE_EMAIL);
    }

    public static function getUrl(string $name, $default = null) {
        return self::get($name, $default, FILTER_VALIDATE_URL);
    }

    public static function getIP(string $name, $default = null) {
        return self::get($name, $default, FILTER_VALIDATE_IP);
    }

    public static function getHexColor(string $name, $default = null) {
        $color = self::get($name, $default);
        if (preg_match('/^#[a-f0-9]{6}$/i', $color)) {
            return $color;
        } else if(preg_match('/^[a-f0-9]{6}$/i', $color)) {
            return '#' . $color;
        } else if (preg_match('/^#[a-f0-9]{3}$/i', $color)) {
            return $color;
        } else if(preg_match('/^[a-f0-9]{3}$/i', $color)) {
            return '#' . $color;
        }
        return $default;
    }

    public static function getRaw(string $name, $default = null) {
        if (is_array($_GET[$name])) {
            foreach ($_GET[$name] as $key => &$value) {
                $value = addslashes($value);
            }
        } else {
            $_GET[$name] = addslashes($_GET[$name]);
        }
        return (!empty($_GET[$name]) ? $_GET[$name] : $default);
    }

    // _POST
    public static function post(string $name, $default = null, $filter = FILTER_DEFAULT) {
        return self::_filter(INPUT_POST, $name, $filter, null, $default);
    }

    public static function postBool(string $name, $default = null) {
        return self::post($name, $default, FILTER_VALIDATE_BOOLEAN);
    }

    public static function postInt(string $name, $default = null) {
        return self::post($name, $default, FILTER_VALIDATE_INT);
    }

    public static function postFloat(string $name, $default = null) {
        return self::post($name, $default, FILTER_VALIDATE_FLOAT);
    }

    public static function postEmail(string $name, $default = null) {
        return self::post($name, $default, FILTER_VALIDATE_EMAIL);
    }

    public static function postUrl(string $name, $default = null) {
        return self::post($name, $default, FILTER_VALIDATE_URL);
    }

    public static function postIP(string $name, $default = null) {
        return self::post($name, $default, FILTER_VALIDATE_IP);
    }

    public static function postHexColor(string $name, $default = null) {
        $color = self::post($name, $default);
        if (preg_match('/^#[a-f0-9]{6}$/i', $color)) {
            return $color;
        } else if(preg_match('/^[a-f0-9]{6}$/i', $color)) {
            return '#' . $color;
        } else if (preg_match('/^#[a-f0-9]{3}$/i', $color)) {
            return $color;
        } else if(preg_match('/^[a-f0-9]{3}$/i', $color)) {
            return '#' . $color;
        }
        return $default;
    }

    public static function postRaw(string $name, $default = null) {
        if (is_array($_POST[$name])) {
            foreach ($_POST[$name] as $key => &$value) {
                $value = addslashes($value);
            }
        } else {
            $_POST[$name] = addslashes($_POST[$name]);
        }
        return (!empty($_POST[$name]) ? $_POST[$name] : $default);
    }

    // _COOKIE
    public static function cookie(string $name, $default = null, $filter = FILTER_DEFAULT) {
        return self::_filter(INPUT_COOKIE, $name, $filter, null, $default);
    }

    public static function cookieBool(string $name, $default = null) {
        return self::cookie($name, $default, FILTER_VALIDATE_BOOLEAN);
    }

    public static function cookieInt(string $name, $default = null) {
        return self::cookie($name, $default, FILTER_VALIDATE_INT);
    }

    public static function cookieFloat(string $name, $default = null) {
        return self::cookie($name, $default, FILTER_VALIDATE_FLOAT);
    }

    public static function cookieEmail(string $name, $default = null) {
        return self::cookie($name, $default, FILTER_VALIDATE_EMAIL);
    }

    public static function cookieUrl(string $name, $default = null) {
        return self::cookie($name, $default, FILTER_VALIDATE_URL);
    }

    public static function cookieIP(string $name, $default = null) {
        return self::cookie($name, $default, FILTER_VALIDATE_IP);
    }

    public static function cookieHexColor(string $name, $default = null) {
        $color = self::post($name, $default);
        if (preg_match('/^#[a-f0-9]{6}$/i', $color)) {
            return $color;
        } else if(preg_match('/^[a-f0-9]{6}$/i', $color)) {
            return '#' . $color;
        } else if (preg_match('/^#[a-f0-9]{3}$/i', $color)) {
            return $color;
        } else if(preg_match('/^[a-f0-9]{3}$/i', $color)) {
            return '#' . $color;
        }
        return $default;
    }

    public static function cookieRaw(string $name, $default = null) {
        if (is_array($_COOKIE[$name])) {
            foreach ($_COOKIE[$name] as $key => &$value) {
                $value = addslashes($value);
            }
        } else {
            $_COOKIE[$name] = addslashes($_COOKIE[$name]);
        }
        return (!empty($_COOKIE[$name]) ? $_COOKIE[$name] : $default);
    }

    public static function files(string $name) {
        $files = [];
        if (is_array($_FILES[$name]) && is_array($_FILES[$name]['name'])) {
            for ($i = 0; $i < count($_FILES[$name]['name']); $i++) {
                $file = new \stdClass();
                $file->name = $_FILES[$name]['name'][$i];
                $file->type = $_FILES[$name]['type'][$i];
                $file->tmpName = $_FILES[$name]['tmp_name'][$i];
                $file->error = $_FILES[$name]['error'][$i];
                $file->size = $_FILES[$name]['size'][$i];
                $files[] = $file;
            }
        } else {
            $file = new \stdClass();
            $file->name = $_FILES[$name]['name'];
            $file->type = $_FILES[$name]['type'];
            $file->tmpName = $_FILES[$name]['tmp_name'];
            $file->error = $_FILES[$name]['error'];
            $file->size = $_FILES[$name]['size'];
            $files[] = $file;
        }
        return $files;
    }

    // Filter
    public static function filter($variable, $default = null, $filter = FILTER_DEFAULT, $options = null) {
        return self::_filter('filter_var', $variable, $filter, $options, $default);
    }

    public static function filterBool($variable, $default = null) {
        return self::filter($variable, $default, FILTER_VALIDATE_BOOLEAN);
    }

    public static function filterInt($variable, $default = null) {
        return self::filter($variable, $default, FILTER_VALIDATE_INT);
    }

    public static function filterFloat($variable, $default = null) {
        return self::filter($variable, $default, FILTER_VALIDATE_FLOAT);
    }

    public static function filterEmail($variable, $default = null) {
        return self::filter($variable, $default, FILTER_VALIDATE_EMAIL);
    }

    public static function filterUrl($variable, $default = null) {
        return self::filter($variable, $default, FILTER_VALIDATE_URL);
    }

    public static function filterIP($variable, $default = null) {
        return self::filter($variable, $default, FILTER_VALIDATE_IP);
    }

    public static function filterIPv4($variable, $default = null) {
        return self::filter($variable, $default, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    public static function filterIPv6($variable, $default = null) {
        return self::filter($variable, $default, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    public static function filterHexColor($variable, $default = null) {
        if (preg_match('/^#[a-f0-9]{6}$/i', $variable)) {
            return $variable;
        } else if(preg_match('/^[a-f0-9]{6}$/i', $variable)) {
            return '#' . $variable;
        } else if (preg_match('/^#[a-f0-9]{3}$/i', $variable)) {
            return $variable;
        } else if(preg_match('/^[a-f0-9]{3}$/i', $variable)) {
            return '#' . $variable;
        }
        return $default;
    }

    // Sanitize
    public static function sanitize($variable, $default = null, $filter = FILTER_DEFAULT) {
        return self::_filter('filter_var', $variable, $filter, null, $default);
    }

    public static function sanitizeInt($variable, $default = null) {
        return self::sanitize($variable, $default, FILTER_SANITIZE_NUMBER_INT);
    }

    public static function sanitizeFloat($variable, $default = null) {
        return self::sanitize($variable, $default, FILTER_SANITIZE_NUMBER_FLOAT);
    }

    public static function sanitizeEmail($variable, $default = null) {
        return self::sanitize($variable, $default, FILTER_SANITIZE_EMAIL);
    }

    public static function sanitizeUrl($variable, $default = null) {
        return self::sanitize($variable, $default, FILTER_SANITIZE_URL);
    }

    public static function sanitizeIP($variable, $default = null) {
        return self::sanitize($variable, $default, FILTER_VALIDATE_IP);
    }

    public static function headerDisableCache() {
        header('Expires: 0');
        header('Pragma: public');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private');
    }
}
