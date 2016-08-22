<?php
// src/AppBundle/Entity/Repository/ReaderRepository.php
namespace AppBundle\Entity\Repository;

use DateTime;

use Doctrine\ORM\EntityRepository;

class ReaderRepository extends EntityRepository
{
    public function findExpiredRegistrationRequests()
    {
        $query = $this->createQueryBuilder('r')
            ->select('r')
            ->where('r. registrationDigestDatetime < :datetime')
            ->setParameter('datetime', new DateTime('now'))
            ->getQuery();

        return $query->getResult();
    }

    public function findExpiredResetRequests()
    {
        $query = $this->createQueryBuilder('r')
            ->select('r')
            ->where('r. resetDigestDatetime < :datetime')
            ->setParameter('datetime', new DateTime('now'))
            ->getQuery();

        return $query->getResult();
    }

    public function findExpiredSubscriptions()
    {
        $query = $this->createQueryBuilder('r')
            ->select('r')
            ->where('r. subscriptionEnd <= :datetime')
            ->setParameter('datetime', new DateTime('now'))
            ->getQuery();

        return $query->getResult();
    }
}