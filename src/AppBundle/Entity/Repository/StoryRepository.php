<?php
// src/AppBundle/Entity/Repository/StoryRepository.php:2
namespace AppBundle\Entity\Repository;

use Doctrine\ORM\Query,
    Doctrine\ORM\Tools\Pagination\Paginator;

use AppBundle\Entity\Repository\Contract\CustomEntityRepository,
    AppBundle\Entity\Story;

class StoryRepository extends CustomEntityRepository
{
    public function findAll()
    {
        $query = $this->createQueryBuilder("story")
            ->select("story")
            ->orderBy('story.isFreeForAll', 'DESC')
            ->addOrderBy('story.storyOrder', 'ASC')
            ->getQuery()
        ;

        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        return $query->getResult();
    }

    public function findAllIndexedById()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('story, review')
            ->from('AppBundle:Story', 'story', 'story.id')
            ->leftJoin('story.reviews', 'review', 'WITH', 'review.isActive = :isActive')
            ->setParameter('isActive', TRUE)
            ->getQuery()
        ;

        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        return $query->getResult();
    }

    public function findAllByPageSortedFiltered($page, $results_per_page, $sortingParameter = NULL, $filterParameter = NULL)
    {
        $first_record = ($page * $results_per_page) - $results_per_page;

        $query = $this->createQueryBuilder("story")
            ->select("story, storyCategory, review")
            ->leftJoin('story.reviews', 'review', 'WITH', 'review.isActive = :isActive')
            ->leftJoin('story.storyCategory', 'storyCategory')
            ->setParameter('isActive', TRUE)
        ;

        if( $filterParameter )
        {
            $query
                ->where('storyCategory.alias = :alias')
                ->setParameter('alias', $filterParameter)
            ;
        }

        switch( $sortingParameter )
        {
            case Story::SORTING_AVAILABLE:
                $query->addOrderBy('story.isFreeForAll', 'DESC');
            break;

            case Story::SORTING_VIEWED:
                $query->addOrderBy('story.views', 'DESC');
            break;

            case Story::SORTING_REVIEWED:
                $query
                    ->addSelect('COUNT(review.id) AS HIDDEN reviewsNumber')
                    ->groupBy('story.id')
                    ->addOrderBy("reviewsNumber", 'DESC')
                ;
            break;

            default:
                $query->orderBy('story.storyOrder', 'ASC');
            break;
        }

        $query = $query
            ->setFirstResult($first_record)
            ->setMaxResults($results_per_page)
            ->getQuery()
        ;

        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        return new Paginator($query);
    }

    public function findMaxStoryOrder()
    {
        $query = $this->createQueryBuilder("story")
            ->select("MAX(story.storyOrder)")
            ->getQuery();

        return $query->getSingleScalarResult();
    }
}
