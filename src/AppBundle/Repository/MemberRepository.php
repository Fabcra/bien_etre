<?php

namespace AppBundle\Repository;

/**
 * InternauteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MemberRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllMembersWithAvatar()
    {

        $qb = $this->createQueryBuilder('m')
            ->orderBy('m.registrationDate', 'DESC')
            ->leftJoin('m.avatar', 'a')->addSelect('a')
            ->leftJoin('m.locality', 'lo')->addSelect('lo');


        return $qb->getQuery()
            ->getResult();
    }
}
