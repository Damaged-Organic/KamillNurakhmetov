<?php
// src/MenuBundle/Entity/Repository/MenuRepository.php
namespace MenuBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query;

class MenuRepository extends EntityRepository
{
    public function findAll()
    {
        $query = $this->createQueryBuilder('menu')
            ->select('menu')
            ->getQuery();

        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        return $query->getResult();
    }
}
