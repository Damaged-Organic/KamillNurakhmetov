<?php
// src/AppBundle/Entity/Repository/Contract/CustomEntityRepository.php
namespace AppBundle\Entity\Repository\Contract;

use Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query;

class CustomEntityRepository extends EntityRepository
{
    public function find($id)
    {
        //TODO: This is kludge for Sonata
        $id = ( is_array($id) ) ? $id['id'] : $id;

        $query = $this->createQueryBuilder("entity")
            ->select("entity")
            ->where("entity.id = :id")
            ->setParameter('id', $id)
            ->getQuery();

        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        return $query->getSingleResult();
    }

    public function findAll()
    {
        $query = $this->createQueryBuilder("entity")
            ->select("entity")
            ->getQuery();

        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        return $query->getResult();
    }
}
