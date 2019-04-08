<?php
namespace Igate\Doctrine\DBAL\Types;
use Doctrine\DBAL\Types;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class IgateDateTimeTzType extends Types\DateTimeTzType
{
    public function getName()
    {
        return 'igatedatetimetz';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $val = \Igate\DateTime::createFromFormat($platform->getDateTimeTzFormatString(), $value);
        if (!$val) {
            throw \Doctrine\DBAL\Types\ConversionException::conversionFailed($value, $this->getName());
        }
        return $val;
    }
}
