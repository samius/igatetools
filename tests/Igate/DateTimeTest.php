<?php
namespace Igate;


/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class DateTimeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDayCountOfThisMonth()
    {
        $date = new DateTime('2010-02-01');
        $this->assertSame(28, $date->getDayCountOfThisMonth());

        $date = new DateTime('2012-02-01');
        $this->assertSame(29, $date->getDayCountOfThisMonth());
    }
}
