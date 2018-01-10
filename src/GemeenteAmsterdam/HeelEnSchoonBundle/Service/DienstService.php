<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Service;

use Doctrine\ORM\EntityManager;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Dienst;

class DienstService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var array
     */
    private $states;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return NULL|mixed
     */
    public function getDienst($gebiedId)
    {
        if (isset($this->states[$gebiedId]) === false) {
            $qb = $this->em->getRepository(Dienst::class)->createQueryBuilder('dienst');
            $qb->join('dienst.gebied', 'gebied');
            $qb->andWhere('gebied.id = :gebiedId');
            $qb->setParameter('gebiedId', $gebiedId);
            $qb->andWhere('dienst.eind IS NULL');
            $diensten = $qb->getQuery()->execute();
            if (count($diensten) === 0) {
                $this->states[$gebiedId] = null;
            } else {
                $this->states[$gebiedId] = reset($diensten);
            }
        }
        return $this->states[$gebiedId];
    }

    /**
     * @return boolean
     */
    public function hasActiveDienst()
    {
        $qb = $this->em->getRepository(Dienst::class)->createQueryBuilder('dienst');
        $qb->andWhere('dienst.eind IS NULL');
        $diensten = $qb->getQuery()->execute();
        return (count($diensten) > 0);
    }
}