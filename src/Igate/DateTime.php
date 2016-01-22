<?php
namespace Igate;

/**
 * @category Igate
 * @version $Id$
 * @author milan
 */
class DateTime extends \DateTime
{
    /**
     * @var Igate\DateTime
     */
    private static $now;

    // 25.2.1983
    const HUMAN_FULL = 'j.n.Y G:i:s';

    //12.45
    const HUMAN_TIME = 'G:i';

    // 25.2.1983
    const HUMAN_DATE = 'j.n.Y';

    // 1983-02-25
    const DB_DATE = 'Y-m-d';

    // 1983-02-25 12:45:42
    const DB_FULL = 'Y-m-d H:i:s';
    const PART_SECOND = 'second',
          PART_MINUTE = 'minute',
          PART_HOUR   = 'hour',
          PART_DAY    = 'day',
          PART_WEEK   = 'week',
          PART_MONTH  = 'month',
          PART_YEAR   = 'year';

    public function  __construct($time = null, \DateTimeZone $object = null)
    {
        if (!$time && self::$now) {
            $time = self::now()->format(self::DB_FULL);
            $object = self::now()->getTimezone();
        }

        if ($object) {
            parent::__construct($time, $object);
        } else {
            parent::__construct($time);
        }
    }


    /**
     * @static
     * @param string $timeFromDb Cas v databazovem formatu.
     * @return DateTime
     */
    public static function fromDb($timeFromDb)
    {
        return self::createFromFormat(self::getDbFormat($timeFromDb), $timeFromDb);
    }

    /**
     * @param \DateTime $dateTime
     * @return DateTime
     */
    public static function fromDateTime(\DateTime $dateTime)
    {
        return new self($dateTime->format(self::DB_FULL));
    }

    /**
     * @static
     * @param int $timestamp
     * @return DateTime
     */
    public static function fromTimestamp($timestamp)
    {
        return new self("@$timestamp");
    }

    /**
     * @static
     * @param string $timeFromDb
     * @return string Format data.
     */
    private static function getDbFormat($timeFromDb)
    {
        if (strpos($timeFromDb, ':') === false) {
            // mame pouze datum
            return self::DB_DATE;
        }

        if (strpos($timeFromDb, '-') === false) {
            // mame pouze cas
            return 'H:i:s';
        }

        return self::DB_FULL;
    }

    /**
     * Zkratka, aby slo s datem pracovat v jedom prikazu a nemuselo se preukladat do promenne apod.
     * @static
     * @return DateTime
     */
    public static function now()
    {
        if (isset (self::$now)) {
            return clone(self::$now);
        }
        return new self;
    }

    /**
     * @param $minuteInDay
     * @return DateTime
     */
    public function setMinuteInDay($minuteInDay)
    {
        return $this->resetTime()->addPart($minuteInDay, self::PART_MINUTE);
    }

    /**
     * Vraci pocet minut od pulnoci
     * @return int
     */
    public function getMinuteInDay()
    {
        $hours = (int) $this->format('H');
        $minutes = (int)$this->format('i');

        return $hours * 60 + $minutes;
    }
    
    /**
     * Nastavi patricny den v tydnu (1=monday, 7=sunday)
     * @param int $targetDay
     * @return DateTime
     */
    public function setDayOfWeek($targetDay)
    {
        $actualDay = (int)$this->format('N');
        return $this->addOrSubDays($actualDay, $targetDay);
    }

    /**
     * @return int cislo dne v tydnu (1=monday, 7=sunday)
     */
    public function getDayOfWeek()
    {
        return $this->format('N');
    }
    
    /**
     * Nastavi patricny den v mesici
     * @param int $targetDay
     * @return DateTime
     */
    public function setDayOfMonth($targetDay)
    {
        $actualDay = (int)$this->format('j');
        return $this->addOrSubDays($actualDay, $targetDay);
    }
    
    /**
     * Nastavi patricny den v roce, CISLOVANY OD 0!!!
     * Pokud tedy chci nastavit 1.1., nastavim den 0.
     * V neprestupnem roce je 31.12. den 364,
     * v prestupnem roce je 31.12. den 365.
     *
     * @param int $targetDay
     * @return DateTime
     */
    public function setDayOfYear($targetDay)
    {
        $actualDay = (int)$this->format('z');
        return $this->addOrSubDays($actualDay, $targetDay);
    }

    /**
     * Pricte nebo odecte patricny pocet dnu
     * @param int $actualDay - den, ktery je nastaveny v soucasnosti. Muze predstavovat den v tydnu, mesici, roce...
     * @param int $targetDay - den, ktery chci nastavit (v tydnu, mesici, roce..)
     * 
     * @return DateTime
     */
    private function addOrSubDays($actualDay, $targetDay)
    {
        if ($actualDay >= $targetDay) {
            $diff = $actualDay-$targetDay;
            $this->sub(new \DateInterval("P{$diff}D"));
        } else {
            $diff = $targetDay - $actualDay;
            $this->add(new \DateInterval("P{$diff}D"));
        }

        return $this;
    }

    /**
     * @param int $number
     * @param string $part
     * @return DateTime
     */
    public function subPart($number, $part)
    {
        $interval = new \DateInterval($this->getIntervalString($number, $part));

        return $this->sub($interval);
    }

    /**
     * @param int $number
     * @param string $part
     * @return DateTime
     */
    public function addPart($number, $part)
    {
        $interval = new \DateInterval($this->getIntervalString($number, $part));

        return $this->add($interval);
    }

    /**
     * Vynuluje sekundy
     * @return DateTime
     */
    public function resetSeconds()
    {
        $seconds = $this->format('s');
        return $this->subPart($seconds, self::PART_SECOND);
    }

    /**
     * Nastavi cas na 00:00:00
     * @return DateTime
     */
    public function resetTime()
    {
        $this->setTime(0, 0, 0);

        return $this;
    }

    /**
     * Nastavi cas na 23:59:59
     * @return DateTime
     */
    public function maxTime()
    {
        $this->setTime(23, 59, 59);
        return $this;
    }

    /**
     * @param DateTime|null $now
     */
    public static function setNow(self $now = null)
    {
        if ($now) {
            self::$now = clone($now);
        } else {
            self::$now = $now;
        }
    }

    /**
     * @param int $number
     * @param string $part "second", "minute" ...
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getIntervalString($number, $part)
    {
        switch ($part) {
            case self::PART_SECOND:
                $interval = "PT{$number}S";
                break;
            case self::PART_MINUTE:
                $interval = "PT{$number}M";
                break;
            case self::PART_HOUR:
                $interval = "PT{$number}H";
                break;
            case self::PART_DAY:
                $interval = "P{$number}D";
                break;
            case self::PART_WEEK:
                $interval = "P".(string)($number*7)."D";
                break;
            case self::PART_MONTH:
                $interval = "P{$number}M";
                break;
            case self::PART_YEAR:
                $interval = "P{$number}Y";
                break;
            default:
                throw new \InvalidArgumentException('Chybna date part '. $part);
        }
        
        return $interval;
    }

    /**
     * @param string $format
     * @param string $time
     * @param \DateTimeZone $object
     * @return DateTime
     */
    public static function createFromFormat($format, $time, $object = null)
    {
        if ($object !== null) {
            $datetime = parent::createFromFormat($format, $time, $object);
        } else {
            $datetime = parent::createFromFormat($format, $time);
        }

        return $datetime ? self::fromDateTime($datetime) : false;
    }

    /**
     * @return bool
     */
    public function isWeekend()
    {
        $dayOfWeek = $this->format('w');

        return $dayOfWeek == 0 || $dayOfWeek == 6;
    }

    /**
     * return bool
     */
    public function isToday()
    {
        $timezone = $this->getTimezone();
        $now = new DateTime(null, $timezone);

        return $this->getDbDate() == $now->getDbDate();
    }


    /**
     * Prida k datu dany pocet hodin, ktere spadaji do pracovniho dne. Preskakuje tedy vikendy. Nebere v uvahu statni
     * svatky.
     *
     * @param int $hours
     */
    public function addWorkHours($hours)
    {
        while ($hours > 0) {
            $this->addPart(1, self::PART_HOUR);
            if (! $this->isWeekend()) {
                $hours--;
            }
        }
    }

    /**
     * Vrati lidsky citelny nazev mesice
     *
     * @param int $monthNumber cislo mesice
     * @param int $inflect v kolikatem pade chci nazev (napr. 1 = cerven, 2=cervna)
     *
     * @return string
     */
    public function getMonthHumanName($monthNumber = null, $inflect = 1)
    {
        if (!$monthNumber) {
            $monthNumber = $this->format('n');
        }

        switch ($monthNumber) {
            case 1:  $month = ($inflect == 1)? 'leden': 'ledna'; break;
            case 2:  $month = ($inflect == 1)? 'únor': 'února'; break;
            case 3:  $month = ($inflect == 1)? 'březen': 'března'; break;
            case 4:  $month = ($inflect == 1)? 'duben': 'dubna'; break;
            case 5:  $month = ($inflect == 1)? 'květen': 'května'; break;
            case 6:  $month = ($inflect == 1)? 'červen': 'června'; break;
            case 7:  $month = ($inflect == 1)? 'červenec': 'července'; break;
            case 8:  $month = ($inflect == 1)? 'srpen': 'srpna'; break;
            case 9:  $month = ($inflect == 1)? 'září': 'září'; break;
            case 10: $month =  ($inflect == 1)? 'říjen': 'října'; break;
            case 11: $month =  ($inflect == 1)? 'listopad': 'listopadu'; break;
            case 12: $month =  ($inflect == 1)? 'prosinec': 'prosince'; break;
            default:
                throw new \InvalidArgumentException("neexistujici mesic $monthNumber");
        }

        return $month;
    }

    /**
     * Vraci timestamp v milisekundach
     * @return int
     */
    public function getMilis()
    {
        return $this->getTimestamp()*1000;
    }

    /**
     * Nastavi posledni den v aktualnim mesici
     *
     * @return DateTime
     */
    public function setLastDayInMonth()
    {
        return $this->setDayOfMonth($this->format('t'));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->format(self::HUMAN_FULL);
    }
    /**
     * @return string
     */
    public function getDbDate()
    {
        return $this->format(self::DB_DATE);
    }
    /**
     * @return string
     */
    public function getDbDatetime()
    {
        return $this->format(self::DB_FULL);
    }
    /**
     * @return string
     */
    public function getHumanDate()
    {
        return $this->format(self::HUMAN_DATE);
    }

    /**
     * Vraci cislo predchoziho dne
     * @param $dayNum
     * @return int
     */
    public static function getPreviousDayNum($dayNum)
    {
        if ($dayNum == 1) {
            return 7;
        } else {
            return $dayNum - 1;
        }
    }

    /**
     * Returns string in format 201505
     * @return string
     */
    public function getYearMonth()
    {
        return $this->format('Ym');
    }

    /**
     * Vraci pocet dni tohoto mesice
     * @return int
     */
    public function getDayCountOfThisMonth()
    {
        return (int) $this->format('t');
    }

}
