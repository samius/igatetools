<?php
namespace Igate\Entity;

/**
 * Parent of entity repositories, with Save and Flush methods
 */
class EntityRepository extends \Doctrine\ORM\EntityRepository
{
    public function save($entity, $flush = false)
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function getEm()
    {
        return $this->_em;
    }
}
