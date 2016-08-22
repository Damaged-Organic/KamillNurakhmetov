<?php
// src/AppBundle/Entity/Bookmark.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\DoctrineMapping\IdMapper;

/**
 * @ORM\Table(name="bookmarks")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\BookmarkRepository")
 */
class Bookmark
{
    use IdMapper;

    /**
     * @ORM\ManyToOne(targetEntity="Reader", inversedBy="bookmarks")
     * @ORM\JoinColumn(name="reader_id", referencedColumnName="id")
     */
    protected $reader;

    /**
     * @ORM\ManyToOne(targetEntity="Book", inversedBy="bookmarks")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     */
    protected $book;

    /**
     * @ORM\ManyToOne(targetEntity="Chapter", inversedBy="bookmarks")
     * @ORM\JoinColumn(name="chapter_id", referencedColumnName="id")
     */
    protected $chapter;

    /**
     * Set reader
     *
     * @param \AppBundle\Entity\Reader $reader
     * @return Bookmark
     */
    public function setReader(\AppBundle\Entity\Reader $reader = null)
    {
        $this->reader = $reader;

        return $this;
    }

    /**
     * Get reader
     *
     * @return \AppBundle\Entity\Reader 
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * Set book
     *
     * @param \AppBundle\Entity\Book $book
     * @return Bookmark
     */
    public function setBook(\AppBundle\Entity\Book $book = null)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get book
     *
     * @return \AppBundle\Entity\Book 
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * Set chapter
     *
     * @param \AppBundle\Entity\Chapter $chapter
     * @return Bookmark
     */
    public function setChapter(\AppBundle\Entity\Chapter $chapter = null)
    {
        $this->chapter = $chapter;

        return $this;
    }

    /**
     * Get chapter
     *
     * @return \AppBundle\Entity\Chapter 
     */
    public function getChapter()
    {
        return $this->chapter;
    }
}