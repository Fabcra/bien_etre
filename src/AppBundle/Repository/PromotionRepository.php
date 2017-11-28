<?php

namespace AppBundle\Repository;

/**
 * PromotionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PromotionRepository extends \Doctrine\ORM\EntityRepository
{

    public function promoWithProvider($slug)
    {

        $qb = $this->createQueryBuilder('promo');

        $qb
            ->leftJoin('promo.provider', 'prov')->addSelect('prov')
            ->leftJoin('promo.service', 's')->addSelect('s')
            ->andWhere('promo.name LIKE :slug')
            ->setParameter('slug', $slug);


        return $qb
            ->getQuery()
            ->getSingleResult();

    }



}
