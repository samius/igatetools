<?php
namespace Igate;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class BarcodeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSsccCheckDigit()
    {
        try {
            Barcode::getSsccCheckDigit(123456);
            $this->fail('bad length');
        } catch (\InvalidArgumentException $e) {
        }

        $this->assertEquals(5, Barcode::getSsccCheckDigit(12345678901234567));
        $this->assertEquals(5, Barcode::getSsccCheckDigit(98765432109876543));
        $this->assertEquals(7, Barcode::getSsccCheckDigit(65432198765412398));
    }
}
