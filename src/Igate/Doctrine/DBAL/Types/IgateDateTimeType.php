<?php
namespace Igate\Doctrine\DBAL\Types;
use Doctrine\DBAL\Types;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class IgateDateTimeType extends Types\DateTimeType
{
    public function getName()
    {
        return 'igatedatetime';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $val = \Igate\DateTime::createFromFormat($platform->getDateTimeFormatString(), $value);
        if (!$val) {
            throw \Doctrine\DBAL\Types\ConversionException::conversionFailed($value, $this->getName());
        }
        return $val;
    }
}
