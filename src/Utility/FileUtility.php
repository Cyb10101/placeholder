<?php
namespace App\Utility;

/**
 * Class FileUtility
 */
abstract class FileUtility {
    /**
     * @param string $filenameOrURL
     * @return string
     */
    public static function openFileOrURL(string $filenameOrURL) {
        $handle = fopen($filenameOrURL, 'rb');
        $content = '';
        while (!feof($handle)) {
            $content .= fread($handle, 1024);
        }
        fclose($handle);
        return $content;
    }

    /**
     * Get max upload size
     * @return int
     */
    public static function uploadMaxSize() {
        $maxSize = self::parseSize(ini_get('post_max_size'));
        $maxUpload = self::parseSize(ini_get('upload_max_filesize'));
        if ($maxUpload > 0 && $maxUpload < $maxSize) {
            $maxSize = $maxUpload;
        }
        return $maxSize;
    }

    /**
     * Parse upload size to bytes (1M -> 1048576)
     * @param $size
     * @return int
     */
    public static function parseSize($size): int {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = (int)preg_replace('/[^0-9\.]/', '', $size);
        if ($unit) {
            return (int)round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return (int)round($size);
        }
    }

    /**
     * @param int $errorCode
     * @return string
     */
    public static function getUploadErrorMessage(int $errorCode = 0): string {
        $errors = [
            UPLOAD_ERR_OK => '',
            UPLOAD_ERR_INI_SIZE => 'Die Datei ist zu groß!',
            UPLOAD_ERR_FORM_SIZE => 'Die Datei ist zu groß!',
            UPLOAD_ERR_PARTIAL => 'Upload error partial!',
            UPLOAD_ERR_NO_FILE => 'Bitte eine gültige Datei auswählen!',
            UPLOAD_ERR_NO_TMP_DIR => 'Konnte Datei nicht zwischenspeichern!',
            UPLOAD_ERR_CANT_WRITE => 'Konnte Datei nicht speichern!',
            UPLOAD_ERR_EXTENSION => 'Falsche Dateierweiterung!',
        ];
        if (isset($errors[$errorCode])) {
            return $errors[$errorCode];
        }
        return 'Unbekannter Fehler beim hochladen der Datei!';
    }

    /**
     * @param string $filename
     * @return string
     */
    public static function getMimeTypeByFilename(string $filename): string {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $filename);
        finfo_close($fileInfo);
        return $mimeType;
    }

    /**
     * @param string $filename
     * @return string
     */
    public static function getMimeTypeByFileExtension(string $filename): string {
        $mimeTypes = self::listExtensionToMimeType();
        $explode = explode('.', $filename);
        $extension = strtolower(end($explode));
        if (!empty($mimeTypes[$extension])) {
            return $mimeTypes[$extension];
        }
        return '';
    }

    /**
     * @return array
     */
    protected function listExtensionToMimeType(): array {
        $mimeTypes = [
            // Application
            'ez' => 'application/andrew-inset',
            'hqx' => 'application/mac-binhex40',
            'cpt' => 'application/mac-compactpro',
            'doc' => 'application/msword',
            'bin' => 'application/octet-stream',
            'dms' => 'application/octet-stream',
            'lha' => 'application/octet-stream',
            'lzh' => 'application/octet-stream',
            'exe' => 'application/octet-stream',
            'class' => 'application/octet-stream',
            'so' => 'application/octet-stream',
            'dll' => 'application/octet-stream',
            'oda' => 'application/oda',
            'pdf' => 'application/pdf',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            'smi' => 'application/smil',
            'smil' => 'application/smil',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'wbxml' => 'application/vnd.wap.wbxml',
            'wmlc' => 'application/vnd.wap.wmlc',
            'wmlsc' => 'application/vnd.wap.wmlscriptc',
            'bcpio' => 'application/x-bcpio',
            'vcd' => 'application/x-cdlink',
            'pgn' => 'application/x-chess-pgn',
            'cpio' => 'application/x-cpio',
            'csh' => 'application/x-csh',
            'dcr' => 'application/x-director',
            'dir' => 'application/x-director',
            'dxr' => 'application/x-director',
            'dvi' => 'application/x-dvi',
            'spl' => 'application/x-futuresplash',
            'gtar' => 'application/x-gtar',
            'hdf' => 'application/x-hdf',
            'js' => 'application/x-javascript',
            'skp' => 'application/x-koan',
            'skd' => 'application/x-koan',
            'skt' => 'application/x-koan',
            'skm' => 'application/x-koan',
            'latex' => 'application/x-latex',
            'nc' => 'application/x-netcdf',
            'cdf' => 'application/x-netcdf',
            'sh' => 'application/x-sh',
            'shar' => 'application/x-shar',
            'swf' => 'application/x-shockwave-flash',
            'sit' => 'application/x-stuffit',
            'sv4cpio' => 'application/x-sv4cpio',
            'sv4crc' => 'application/x-sv4crc',
            'tar' => 'application/x-tar',
            'tcl' => 'application/x-tcl',
            'tex' => 'application/x-tex',
            'texinfo' => 'application/x-texinfo',
            'texi' => 'application/x-texinfo',
            't' => 'application/x-troff',
            'tr' => 'application/x-troff',
            'roff' => 'application/x-troff',
            'man' => 'application/x-troff-man',
            'me' => 'application/x-troff-me',
            'ms' => 'application/x-troff-ms',
            'ustar' => 'application/x-ustar',
            'src' => 'application/x-wais-source',
            'xhtml' => 'application/xhtml+xml',
            'xht' => 'application/xhtml+xml',
            'zip' => 'application/zip',

            // Audio
            'au' => 'audio/basic',
            'snd' => 'audio/basic',
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'kar' => 'audio/midi',
            'mpga' => 'audio/mpeg',
            'mp2' => 'audio/mpeg',
            'mp3' => 'audio/mpeg',
            'aif' => 'audio/x-aiff',
            'aiff' => 'audio/x-aiff',
            'aifc' => 'audio/x-aiff',
            'm3u' => 'audio/x-mpegurl',
            'ram' => 'audio/x-pn-realaudio',
            'rm' => 'audio/x-pn-realaudio',
            'rpm' => 'audio/x-pn-realaudio-plugin',
            'ra' => 'audio/x-realaudio',
            'wav' => 'audio/x-wav',

            // Chemical
            'pdb' => 'chemical/x-pdb',
            'xyz' => 'chemical/x-xyz',

            // Image
            'bmp' => 'image/bmp',
            'gif' => 'image/gif',
            'ief' => 'image/ief',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'jpe' => 'image/jpeg',
            'png' => 'image/png',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'djvu' => 'image/vnd.djvu',
            'djv' => 'image/vnd.djvu',
            'wbmp' => 'image/vnd.wap.wbmp',
            'ras' => 'image/x-cmu-raster',
            'pnm' => 'image/x-portable-anymap',
            'pbm' => 'image/x-portable-bitmap',
            'pgm' => 'image/x-portable-graymap',
            'ppm' => 'image/x-portable-pixmap',
            'rgb' => 'image/x-rgb',
            'xbm' => 'image/x-xbitmap',
            'xpm' => 'image/x-xpixmap',
            'xwd' => 'image/x-xwindowdump',

            // Model
            'igs' => 'model/iges',
            'iges' => 'model/iges',
            'msh' => 'model/mesh',
            'mesh' => 'model/mesh',
            'silo' => 'model/mesh',
            'wrl' => 'model/vrml',
            'vrml' => 'model/vrml',

            // Text
            'css' => 'text/css',
            'html' => 'text/html',
            'htm' => 'text/html',
            'asc' => 'text/plain',
            'txt' => 'text/plain',
            'rtx' => 'text/richtext',
            'rtf' => 'text/rtf',
            'sgml' => 'text/sgml',
            'sgm' => 'text/sgml',
            'tsv' => 'text/tab-separated-values',
            'wml' => 'text/vnd.wap.wml',
            'wmls' => 'text/vnd.wap.wmlscript',
            'etx' => 'text/x-setext',
            'xml' => 'text/xml',
            'xsl' => 'text/xml',

            // Video
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mpe' => 'video/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'mxu' => 'video/vnd.mpegurl',
            'avi' => 'video/x-msvideo',
            'movie' => 'video/x-sgi-movie',
            'asf' => 'video/x-ms-asf',
            'asx' => 'video/x-ms-asf',
            'wm' => 'video/x-ms-wm',
            'wmv' => 'video/x-ms-wmv',
            'wvx' => 'video/x-ms-wvx',

            // X-Conference
            'ice' => 'x-conference/x-cooltalk',
        ];
        return $mimeTypes;
    }

    /**
     * @param string $mimeType
     * @return string
     */
    public static function getFileExtensionByMimeType(string $mimeType): string {
        $extensions = self::listMimeTypeToExtension();
        $mimeType = strtolower($mimeType);
        if (!empty($extensions[$mimeType])) {
            return $extensions[$mimeType];
        }
        return '';
    }

    /**
     * @return array
     */
    protected function listMimeTypeToExtension(): array {
        $mimeTypes = [
            // Application
            'application/andrew-inset' => 'ez',
            'application/mac-binhex40' => 'hqx',
            'application/mac-compactpro' => 'cpt',
            'application/msword' => 'doc',
            'application/oda' => 'oda',
            'application/pdf' => 'pdf',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.wap.wbxml' => 'wbxml',
            'application/vnd.wap.wmlc' => 'wmlc',
            'application/vnd.wap.wmlscriptc' => 'wmlsc',
            'application/x-bcpio' => 'bcpio',
            'application/x-cdlink' => 'vcd',
            'application/x-chess-pgn' => 'pgn',
            'application/x-cpio' => 'cpio',
            'application/x-csh' => 'csh',
            'application/x-dvi' => 'dvi',
            'application/x-futuresplash' => 'spl',
            'application/x-gtar' => 'gtar',
            'application/x-hdf' => 'hdf',
            'application/x-javascript' => 'js',
            'application/x-latex' => 'latex',
            'application/x-sh' => 'sh',
            'application/x-shar' => 'shar',
            'application/x-shockwave-flash' => 'swf',
            'application/x-stuffit' => 'sit',
            'application/x-sv4cpio' => 'sv4cpio',
            'application/x-sv4crc' => 'sv4crc',
            'application/x-tar' => 'tar',
            'application/x-tcl' => 'tcl',
            'application/x-tex' => 'tex',
            'application/x-troff-man' => 'man',
            'application/x-troff-me' => 'me',
            'application/x-troff-ms' => 'ms',
            'application/x-ustar' => 'ustar',
            'application/x-wais-source' => 'src',
            'application/zip' => 'zip',

            // Audio
            'audio/x-mpegurl' => 'm3u',
            'audio/x-pn-realaudio-plugin' => 'rpm',
            'audio/x-realaudio' => 'ra',
            'audio/x-wav' => 'wav',

            // Chemical
            'chemical/x-pdb' => 'pdb',
            'chemical/x-xyz' => 'xyz',

            // Image
            'image/bmp' => 'bmp',
            'image/gif' => 'gif',
            'image/ief' => 'ief',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/svg+xml' => 'svg',
            'image/vnd.wap.wbmp' => 'wbmp',
            'image/x-cmu-raster' => 'ras',
            'image/x-portable-anymap' => 'pnm',
            'image/x-portable-bitmap' => 'pbm',
            'image/x-portable-graymap' => 'pgm',
            'image/x-portable-pixmap' => 'ppm',
            'image/x-rgb' => 'rgb',
            'image/x-xbitmap' => 'xbm',
            'image/x-xpixmap' => 'xpm',
            'image/x-xwindowdump' => 'xwd',

            // Text
            'text/css' => 'css',
            'text/html' => 'html',
            'text/plain' => 'txt',
            'text/richtext' => 'rtx',
            'text/rtf' => 'rtf',
            'text/tab-separated-values' => 'tsv',
            'text/vnd.wap.wml' => 'wml',
            'text/vnd.wap.wmlscript' => 'wmls',
            'text/x-setext' => 'etx',
            'text/xml' => 'xml',

            // Video
            'video/mpeg' => 'mpg',
            'video/vnd.mpegurl' => 'mxu',
            'video/x-msvideo' => 'avi',
            'video/x-sgi-movie' => 'movie',
            'video/x-ms-asf' => 'asf',
            'video/x-ms-wm' => 'wm',
            'video/x-ms-wmv' => 'wmv',
            'video/x-ms-wvx' => 'wvx',

            // X-Conference
            'x-conference/x-cooltalk' => 'ice',
        ];
        return $mimeTypes;
    }

    /**
     * @param string $filename
     * @return \stdClass
     */
    public static function getImageSize(string $filename): \stdClass {
        $data = getimagesize($filename);
        $return = new \stdClass();
        $return->width = (int)$data[0];
        $return->height = (int)$data[1];
        $return->mime = $data['mime'];
        return $return;
    }

    /**
     * @param string $filename
     * @param array $data
     * @param array $header
     * @return void
     */
    public static function writeArrayToCsv(string $filename, array $data, array $header = []) {
        $fp = fopen($filename, 'w+');
        if (count($header) > 0) {
            fputcsv($fp, $header);
        }
        foreach ($data as $item) {
            if (!is_array($item)) {
                $item = [$item];
            }
            fputcsv($fp, $item);
        }
        fclose($fp);
    }
}
