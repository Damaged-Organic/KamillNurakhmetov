<?php
// src/AppBundle/Entity/Repository/ChapterRepository.php
namespace AppBundle\Entity\Repository;

use Doctrine\ORM\Query;

use AppBundle\Entity\Repository\Contract\CustomEntityRepository,
    AppBundle\Entity\Book;

class ChapterRepository extends CustomEntityRepository
{
    public function findAll()
    {
        $query = $this->createQueryBuilder("chapter")
            ->select("chapter")
            ->orderBy('chapter.chapterOrder', 'ASC')
            ->getQuery();

        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        return $query->getResult();
    }

    public function findMaxChapterOrder(Book $book)
    {
        $query = $this->createQueryBuilder("chapter")
            ->select("MAX(chapter.chapterOrder)")
            ->where("chapter.book = :book")
            ->setParameter('book', $book)
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    public function findFirstChapterByBook(Book $book)
    {
        $query = $this->createQueryBuilder("chapter")
            ->select("chapter")
            ->where("chapter.book = :book")
            ->setParameter('book', $book)
            ->orderBy('chapter.chapterOrder', 'ASC')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findAnyChapterByBook(Book $book, $chapterId)
    {
        $query = $this->createQueryBuilder("chapter")
            ->select("chapter")
            ->where("chapter.book = :book")
            ->andWhere("chapter.id = :chapterId")
            ->setParameters([
                'book'      => $book,
                'chapterId' => $chapterId
            ])
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findPreviousChapter($book, $chapterOrder)
    {
        $query = $this->createQueryBuilder("chapter")
            ->select("chapter")
            ->where("chapter.book = :book")
            ->andWhere("chapter.chapterOrder < :chapterOrder")
            ->setParameters([
                'book'         => $book,
                'chapterOrder' => $chapterOrder
            ])
            ->orderBy('chapter.chapterOrder', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findNextChapter($book, $chapterOrder)
    {
        $query = $this->createQueryBuilder("chapter")
            ->select("chapter")
            ->where("chapter.book = :book")
            ->andWhere("chapter.chapterOrder > :chapterOrder")
            ->setParameters([
                'book'         => $book,
                'chapterOrder' => $chapterOrder
            ])
            ->orderBy('chapter.chapterOrder', 'ASC')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}