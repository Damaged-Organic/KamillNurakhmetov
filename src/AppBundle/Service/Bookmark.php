<?php
// src/AppBundle/Service/Bookmark.php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

use AppBundle\Entity\Bookmark as BookmarkEntity;

class Bookmark
{
    protected $_manager;

    public function __construct(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    public function setBookmark($reader, $book, $chapter)
    {
        $bookmark = $this->_manager->getRepository('AppBundle:Bookmark')->findOneBy([
            'reader' => $reader,
            'book'   => $book
        ]);

        if( $bookmark ) {
            $bookmark->setChapter($chapter);
        } else {
            $bookmark = $this->createBookmark($reader, $book, $chapter);
        }

        $this->_manager->persist($bookmark);
        $this->_manager->flush();
    }

    public function createBookmark($reader, $book, $chapter)
    {
        $bookmark = (new BookmarkEntity)
            ->setReader($reader)
            ->setBook($book)
            ->setChapter($chapter)
        ;

        return $bookmark;
    }

    public function getBookmark($reader, $book)
    {
        $bookmark = $this->_manager->getRepository('AppBundle:Bookmark')->findOneBy([
            'reader' => $reader,
            'book'   => $book
        ]);

        return $bookmark;
    }
}