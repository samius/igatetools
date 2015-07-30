<?php

namespace Igate\DateTime;
use Igate\DateTime;

/**
 * Interval vyjadreny pomoci dvou objektu DateTime. Pokud je $start nebo $end rovno NULL, je v tomto smeru interval
 * nekonecny.
 *
 * @category Igate
 * @package DateTime
 * @author milan
 */
class Interval
{
    /**
     * @return \DateTime|null
     */
    private $start;

    /**
     * @return \DateTime|null
     */
    private $end;

    public function __construct(\DateTime $start = null, \DateTime $end = null)
    {
        $this->start = $start;
        $this->end   = $end;

        $this->assertInputDate();
    }

    /**
     * podle toho, zda je interval uzavreny
     * 5.1.2005 - 10.5.2005
     * 5.1.2005 - ?
     * ? - 10.5.2005
     * ? - ?
     * @return string
     */
    public function __toString()
    {
        if ($this->start && $this->end) {
            return $this->start->format(DateTime::HUMAN_DATE).' - '.$this->end->format(DateTime::HUMAN_DATE);
        } elseif ($this->start) {
            return $this->start->format(DateTime::HUMAN_DATE) . ' - ?';
        } elseif ($this->end) {
            return '? - ' . $this->end->format(DateTime::HUMAN_DATE);
        } else {
            return '? - ?';
        }
    }

    /**
     * @throws \InvalidArgumentException Pokud je konec driv, nez zacatek.
     */
    private function assertInputDate()
    {
        if ($this->start > $this->end && $this->start !== null && $this->end !== null) {
            throw new \InvalidArgumentException('Pocatecni datum je vetsi nez koncove');
        }
    }

    /**
     * @param string $startString retezec ve formatu 2010-10-15 23:00:12
     * @param string $endString   retezec ve formatu 2010-10-15 23:00:12
     * @return Interval
     */
    public static function fromString($startString = null, $endString = null)
    {
        $start = ($startString === null) ? null : new DateTime($startString);
        $end   = ($endString === null) ? null : new DateTime($endString);
        
        return new self($start, $end);
    }

    /**
     * @param Interval $interval
     * @return bool
     */
    public function isIntersecting(Interval $interval)
    {
        $i2Start = $interval->getStart();
        $i2End   = $interval->getEnd();

        // i1      |
        // i2      |
        //
        // i1      _______________
        // i2         _____________________
        if ($this->start == $i2Start && $i2Start == $i2End && $this->end == $i2Start) {
            return true;
        }

        // i1      |_______|
        // 12  |_______________________________
        if (($i2End == null && $this->end > $i2Start) || ($this->end == null && $i2End > $this->start)) {
            return true;
        }

        // i1      |_______________
        // 12  |___________________
        if ($i2End === null && $this->end === null) {
            return true;
        }

        return ($i2Start < $this->end && $i2Start >= $this->start || $i2End > $this->start && $i2End < $this->end) ||
               ($this->start < $i2End && $this->start >= $i2Start || $this->end > $i2Start && $this->end < $i2End);
    }

    /**
     * Vraci prusecik dvou intervalu
     * @param Interval $interval
     * @return Interval
     */
    public function intersect(Interval $interval = null)
    {
        //null je v tomto pripade brano jako prazdna mnozina - prunik s prazdnou mnozinou je vzdy prazdna mn.
        if ($interval === null) {
            return null;
        }

        if (!$this->isIntersecting($interval)) {
            return null;
        }

        $newStart = \max($this->start, $interval->getStart()); // max(null, 30) =  30

        if ($this->end === null || $interval->getEnd() === null) {
            $newEnd   = \max($this->end, $interval->getEnd()); // vezmu nenullovou hodnotu - pokud jsou obe nullove, vezmu jednu z nich
        } else {
            $newEnd   = \min($this->end, $interval->getEnd());
        }

        return new self($newStart, $newEnd);
    }

    /**
     * @return \DateTime|null
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return \DateTime|null
     */
    public function getEnd()
    {
        return $this->end;
    }

    public function getLengthInDays()
    {
        if ($this->start === null || $this->end === null) {
            return null;
        }
        return $this->getStart()->diff($this->getEnd())->days;
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @return int
     */
    public static function lengthInDays(\DateTime $start, \DateTime $end)
    {
        return $start->diff($end)->days;
    }

    /**
     * @return bool
     */
    public function isOpen()
    {
        return ! isset($this->start) || ! isset($this->end);
    }
}
