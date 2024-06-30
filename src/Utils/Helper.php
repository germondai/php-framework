<?php

declare(strict_types=1);

namespace Utils;

use App\Entity\Media;

class Helper
{
    private static string $basePath;
    private static string $linkPath;

    public static function setPaths(string $basePath, string $linkPath): void
    {
        self::$basePath = $basePath;
        self::$linkPath = $linkPath;
    }

    public static function getBasePath(): string
    {
        return self::$basePath;
    }

    public static function getLinkPath(): string
    {
        return self::$linkPath;
    }

    public static function getRequest(): string
    {
        $url = $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'];
        $request = str_replace(self::$linkPath, '', $url);
        if ($url === str_replace('public/', '', self::$linkPath))
            $request = '';

        return $request;
    }

    public static function isDev(): bool
    {
        $address = $_SERVER['SERVER_ADDR'] ?? true;
        return ($address == '127.0.0.1' || $address == '::1');
    }

    public static function formatLink(string $link): string
    {
        return htmlspecialchars(str_starts_with($link, 'https://') ? $link : self::$linkPath . $link);
    }

    public static function getEnv(string $env, bool $die = false)
    {
        $envName = $env;
        $env = $_ENV[$env] ?? false;

        if ($env)
            return $env;

        trigger_error('Add valid "' . $envName . '" to .env!');

        if ($die)
            die();
    }

    public static function snakeToCamel(string $string): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }

    public static function uploadFile(
        mixed $file,
        string $dir,
        int $quality = 80,
        int $maxWidth = 1280,
        int $maxHeight = 720
    ) {
        if (isset($file) && $file['error'] == UPLOAD_ERR_OK) {
            $tmp = $file['tmp_name'];
            $name = $file['name'];
            $size = $file['size'];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $type = finfo_file($finfo, $tmp);
            $ext = self::mime2ext($type);
            finfo_close($finfo);

            $name = self::getUniqueFileName($dir, $name, $ext);
            $path = $dir . $name;

            if (
                !file_exists($path) &&
                move_uploaded_file($tmp, $path)
            ) {
                // if img
                if (str_starts_with($type, 'image/')) {
                    $exts = ['jpg', 'jpeg', 'png', 'gif'];
                    if (in_array($ext, $exts)) {
                        $imgCreate = 'imagecreatefrom' . $ext;
                        $img = $imgCreate($path);

                        [$imgW, $imgH] = getimagesize($path);
                        if ($imgW > $maxWidth || $imgH > $maxHeight) {
                            $ratio = $imgW / $imgH;
                            if ($maxWidth / $maxHeight > $ratio) {
                                $newW = $maxHeight * $ratio;
                                $newH = $maxHeight;
                            } else {
                                $newW = $maxWidth;
                                $newH = $maxWidth / $ratio;
                            }
                        }

                        $newW = (int) round($newW ?? $imgW);
                        $newH = (int) round($newH ?? $imgH);

                        if (!empty($img)) {
                            $dst = imagecreatetruecolor($newW, $newH);

                            if (in_array($ext, ['png', 'gif', 'bmp', 'tiff', 'jp2'])) {
                                imagealphablending($dst, false);
                                imagesavealpha($dst, true);
                                $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                                imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);
                            }

                            imagecopyresampled($dst, $img, 0, 0, 0, 0, $newW, $newH, $imgW, $imgH);

                            $webpName = self::getUniqueFileName($dir, $name, 'webp');
                            $webpPath = $dir . $webpName;

                            if (
                                !file_exists($webpPath) &&
                                imagewebp($dst, $webpPath, $quality)
                            ) {
                                imagedestroy($dst);
                                imagedestroy($img);
                                unlink($path);

                                $type = 'image/webp';
                                $ext = 'webp';
                                $name = $webpName;
                                $path = $webpPath;
                                $size = filesize($path);
                            }
                        }
                    }
                }

                $media = new Media();
                $media->setName($name);
                $media->setPath($path);
                $media->setUrl('media/' . $name);
                $media->setType($type);
                $media->setExtension($ext);
                $media->setSize($size);

                return $media;
            }
        }

        return false;
    }

    public static function getUniqueFileName(string $dir, string $name, string $extension): string
    {
        $name = pathinfo($name, PATHINFO_FILENAME);
        $newName = $name . '.' . $extension;

        if (!file_exists($dir . $newName))
            return $newName;

        $counter = 1;
        while (file_exists($dir . $newName)) {
            $newName = $name . '_' . $counter . '.' . $extension;
            $counter++;
        }

        return $newName;
    }

    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public static function mime2ext(string $mime): string
    {
        $mime_map = [
            'video/3gpp2' => '3g2',
            'video/3gp' => '3gp',
            'video/3gpp' => '3gp',
            'application/x-compressed' => '7zip',
            'audio/x-acc' => 'aac',
            'audio/ac3' => 'ac3',
            'application/postscript' => 'ai',
            'audio/x-aiff' => 'aif',
            'audio/aiff' => 'aif',
            'audio/x-au' => 'au',
            'video/x-msvideo' => 'avi',
            'video/msvideo' => 'avi',
            'video/avi' => 'avi',
            'application/x-troff-msvideo' => 'avi',
            'application/macbinary' => 'bin',
            'application/mac-binary' => 'bin',
            'application/x-binary' => 'bin',
            'application/x-macbinary' => 'bin',
            'image/bmp' => 'bmp',
            'image/x-bmp' => 'bmp',
            'image/x-bitmap' => 'bmp',
            'image/x-xbitmap' => 'bmp',
            'image/x-win-bitmap' => 'bmp',
            'image/x-windows-bmp' => 'bmp',
            'image/ms-bmp' => 'bmp',
            'image/x-ms-bmp' => 'bmp',
            'application/bmp' => 'bmp',
            'application/x-bmp' => 'bmp',
            'application/x-win-bitmap' => 'bmp',
            'application/cdr' => 'cdr',
            'application/coreldraw' => 'cdr',
            'application/x-cdr' => 'cdr',
            'application/x-coreldraw' => 'cdr',
            'image/cdr' => 'cdr',
            'image/x-cdr' => 'cdr',
            'zz-application/zz-winassoc-cdr' => 'cdr',
            'application/mac-compactpro' => 'cpt',
            'application/pkix-crl' => 'crl',
            'application/pkcs-crl' => 'crl',
            'application/x-x509-ca-cert' => 'crt',
            'application/pkix-cert' => 'crt',
            'text/css' => 'css',
            'text/x-comma-separated-values' => 'csv',
            'text/comma-separated-values' => 'csv',
            'application/vnd.msexcel' => 'csv',
            'application/x-director' => 'dcr',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/x-dvi' => 'dvi',
            'message/rfc822' => 'eml',
            'application/x-msdownload' => 'exe',
            'video/x-f4v' => 'f4v',
            'audio/x-flac' => 'flac',
            'video/x-flv' => 'flv',
            'image/gif' => 'gif',
            'application/gpg-keys' => 'gpg',
            'application/x-gtar' => 'gtar',
            'application/x-gzip' => 'gzip',
            'application/mac-binhex40' => 'hqx',
            'application/mac-binhex' => 'hqx',
            'application/x-binhex40' => 'hqx',
            'application/x-mac-binhex40' => 'hqx',
            'text/html' => 'html',
            'image/x-icon' => 'ico',
            'image/x-ico' => 'ico',
            'image/vnd.microsoft.icon' => 'ico',
            'text/calendar' => 'ics',
            'application/java-archive' => 'jar',
            'application/x-java-application' => 'jar',
            'application/x-jar' => 'jar',
            'image/jp2' => 'jp2',
            'video/mj2' => 'jp2',
            'image/jpx' => 'jp2',
            'image/jpm' => 'jp2',
            'image/jpeg' => 'jpeg',
            'image/pjpeg' => 'jpeg',
            'application/x-javascript' => 'js',
            'application/json' => 'json',
            'text/json' => 'json',
            'application/vnd.google-earth.kml+xml' => 'kml',
            'application/vnd.google-earth.kmz' => 'kmz',
            'text/x-log' => 'log',
            'audio/x-m4a' => 'm4a',
            'audio/mp4' => 'm4a',
            'application/vnd.mpegurl' => 'm4u',
            'audio/midi' => 'mid',
            'application/vnd.mif' => 'mif',
            'video/quicktime' => 'mov',
            'video/x-sgi-movie' => 'movie',
            'audio/mpeg' => 'mp3',
            'audio/mpg' => 'mp3',
            'audio/mpeg3' => 'mp3',
            'audio/mp3' => 'mp3',
            'video/mp4' => 'mp4',
            'video/mpeg' => 'mpeg',
            'application/oda' => 'oda',
            'audio/ogg' => 'ogg',
            'video/ogg' => 'ogg',
            'application/ogg' => 'ogg',
            'font/otf' => 'otf',
            'application/x-pkcs10' => 'p10',
            'application/pkcs10' => 'p10',
            'application/x-pkcs12' => 'p12',
            'application/x-pkcs7-signature' => 'p7a',
            'application/pkcs7-mime' => 'p7c',
            'application/x-pkcs7-mime' => 'p7c',
            'application/x-pkcs7-certreqresp' => 'p7r',
            'application/pkcs7-signature' => 'p7s',
            'application/pdf' => 'pdf',
            'application/octet-stream' => 'pdf',
            'application/x-x509-user-cert' => 'pem',
            'application/x-pem-file' => 'pem',
            'application/pgp' => 'pgp',
            'application/x-httpd-php' => 'php',
            'application/php' => 'php',
            'application/x-php' => 'php',
            'text/php' => 'php',
            'text/x-php' => 'php',
            'application/x-httpd-php-source' => 'php',
            'image/png' => 'png',
            'image/x-png' => 'png',
            'application/powerpoint' => 'ppt',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.ms-office' => 'ppt',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/x-photoshop' => 'psd',
            'image/vnd.adobe.photoshop' => 'psd',
            'audio/x-realaudio' => 'ra',
            'audio/x-pn-realaudio' => 'ram',
            'application/x-rar' => 'rar',
            'application/rar' => 'rar',
            'application/x-rar-compressed' => 'rar',
            'audio/x-pn-realaudio-plugin' => 'rpm',
            'application/x-pkcs7' => 'rsa',
            'text/rtf' => 'rtf',
            'text/richtext' => 'rtx',
            'video/vnd.rn-realvideo' => 'rv',
            'application/x-stuffit' => 'sit',
            'application/smil' => 'smil',
            'text/srt' => 'srt',
            'image/svg+xml' => 'svg',
            'application/x-shockwave-flash' => 'swf',
            'application/x-tar' => 'tar',
            'application/x-gzip-compressed' => 'tgz',
            'image/tiff' => 'tiff',
            'font/ttf' => 'ttf',
            'text/plain' => 'txt',
            'text/x-vcard' => 'vcf',
            'application/videolan' => 'vlc',
            'text/vtt' => 'vtt',
            'audio/x-wav' => 'wav',
            'audio/wave' => 'wav',
            'audio/wav' => 'wav',
            'application/wbxml' => 'wbxml',
            'video/webm' => 'webm',
            'image/webp' => 'webp',
            'audio/x-ms-wma' => 'wma',
            'application/wmlc' => 'wmlc',
            'video/x-ms-wmv' => 'wmv',
            'video/x-ms-asf' => 'wmv',
            'font/woff' => 'woff',
            'font/woff2' => 'woff2',
            'application/xhtml+xml' => 'xhtml',
            'application/excel' => 'xl',
            'application/msexcel' => 'xls',
            'application/x-msexcel' => 'xls',
            'application/x-ms-excel' => 'xls',
            'application/x-excel' => 'xls',
            'application/x-dos_ms_excel' => 'xls',
            'application/xls' => 'xls',
            'application/x-xls' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-excel' => 'xlsx',
            'application/xml' => 'xml',
            'text/xml' => 'xml',
            'text/xsl' => 'xsl',
            'application/xspf+xml' => 'xspf',
            'application/x-compress' => 'z',
            'application/x-zip' => 'zip',
            'application/zip' => 'zip',
            'application/x-zip-compressed' => 'zip',
            'application/s-compressed' => 'zip',
            'multipart/x-zip' => 'zip',
            'text/x-scriptzsh' => 'zsh',
        ];

        return isset($mime_map[$mime]) ? $mime_map[$mime] : false;
    }
}