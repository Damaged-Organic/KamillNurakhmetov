<?php
// src/AppBundle/Entity/Repository/BookRepository.php
namespace AppBundle\Entity\Repository;

use Doctrine\ORM\Query;

use AppBundle\Entity\Repository\Contract\CustomEntityRepository;

class BookRepository extends CustomEntityRepository
{
    public function find($id)
    {
        //TODO: This is kludge for Sonata
        $id = ( is_array($id) ) ? $id['id'] : $id;

        $query = $this->createQueryBuilder("book")
            ->select("book")
            ->where("book.id = :id")
            ->setParameter('id', $id)
            ->getQuery();

        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        return $query->getOneOrNullResult();
    }

    public function findAll()
    {
        $query = $this->createQueryBuilder("book")
            ->select("book")
            ->orderBy('book.id', 'DESC')
            ->getQuery();

        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        return $query->getResult();
    }

    public function findWithActiveReviewsOnly($id)
    {
        $query = $this->createQueryBuilder("book")
            ->select("book, review")
            ->leftJoin('book.reviews', 'review', 'WITH', 'review.isActive = :isActive')
            ->where('book.id = :id')
            ->setParameters([
                'isActive' => TRUE,
                'id'       => $id
            ])
            ->getQuery();

        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        return $query->getSingleResult();
    }
}
