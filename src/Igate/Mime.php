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
        EXT_PDF = 'pdf',
        EXT_TXT = 'pdf',
        EXT_XML = 'pdf',
        EXT_GIF = 'pdf',
        EXT_JPEG = 'jpeg',
        EXT_JPG = 'jpg',
        EXT_PNG = 'png',
        EXT_CSV = 'csv',
        EXT_SWF = 'swf',
        EXT_FLASH = 'flash';
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
            case self::EXT_PDF:
                return self::PDF;

            case self::EXT_TXT:
                return self::TXT;

            case self::EXT_XML:
                return self::XML;

            case self::EXT_GIF:
                return self::GIF;

            case self::EXT_JPEG:
            case self::EXT_JPG:
                return self::JPG;

            case self::EXT_PNG:
                return self::PNG;

            case self::EXT_CSV:
                return self::CSV;

            case self::EXT_FLASH:
            case self::EXT_SWF:
                return self::FLASH;
        }

        return self::DOWNLOAD;
    }

    /**
     * @param $mime
     * @return string
     */
    public static function getExtension($mime)
    {
        switch (strtolower($mime)) {
            case self::PNG:
                return self::EXT_PNG;
            case self::JPG:
                return self::EXT_JPG;
            case self::PDF:
                return self::EXT_PDF;
            case self::TXT:
                return self::EXT_TXT;
            case self::XML:
                return self::EXT_XML;
            case self::GIF:
                return self::EXT_GIF;
            case self::CSV:
                return self::EXT_CSV;
            case self::FLASH:
                return self::EXT_SWF;
            default:
                return '';
        }
    }
}
