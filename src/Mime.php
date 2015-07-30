<?php
namespace Igate;

/**
 * Ciselnik pro mime types
 *
 * @author milan
 */
class Mime
{
    const
        PDF      = 'application/pdf',
        TXT      = 'text/plain',
        XML      = 'text/xml',
        GIF      = 'image/gif',
        JPG      = 'image/jpeg',
        PNG      = 'image/png',
        BMP      = 'image/bmp',
        CSV      = 'application/csv',
        FLASH    = 'application/x-shockwave-flash',
        DOWNLOAD = 'text/download'
    ;

    /**
     * Podle pripony souboru vrati mime type.
     *
     * @param string $ext
     * @return string
     */
    public static function fromExtension($ext)
    {
        switch (strtolower($ext)) {
            case 'pdf':
                return self::PDF;

            case 'txt':
                return self::TXT;

            case 'xml':
                return self::XML;

            case 'gif':
                return self::GIF;

            case 'jpg':
            case 'jpeg':
                return self::JPG;

            case 'png':
                return self::PNG;

            case 'csv':
                return self::CSV;

            case 'swf':
            case 'flash':
                return self::FLASH;
        }

        return self::DOWNLOAD;
    }
}
