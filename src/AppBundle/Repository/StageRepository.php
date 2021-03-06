<?php

namespace AppBundle\Repository;

/**
 * StageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StageRepository extends \Doctrine\ORM\EntityRepository
{

    public function stageWhithProvider($slug)
    {

        $qb = $this->createQueryBuilder('s');

        $qb->leftJoin('s.provider', 'p')->addSelect('p')
            ->leftJoin('p.services', 'services')->addSelect('services')
            ->andWhere('s.slug LIKE :slug')
            ->setParameter('slug', $slug);

        return $qb->getQuery()
            ->getSingleResult();


    }

}
