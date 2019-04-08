<?php
namespace Igate\Doctrine\DBAL\Types;
use Doctrine\DBAL\Types;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class IgateTimeType extends Types\TimeType
{
    public function getName()
    {
        return 'igatetime';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $val = \Igate\DateTime::createFromFormat($platform->getTimeFormatString(), $value);
        if (!$val) {
            throw \Doctrine\DBAL\Types\ConversionException::conversionFailed($value, $this->getName());
        }
        return $val;
    }
}
