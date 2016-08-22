<?php
// src/AppBundle/Entity/Chapter.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo,
    Gedmo\Translatable\Translatable;

use AppBundle\Entity\Utility\DoctrineMapping\IdMapper,
    AppBundle\Entity\Utility\DoctrineMapping\TranslationMapper;

/**
 * @ORM\Table(name="chapters")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ChapterRepository")
 *
 * @Gedmo\TranslationEntity(class="AppBundle\Entity\ChapterTranslation")
 *
 * @ORM\HasLifecycleCallbacks
 */
class Chapter implements Translatable
{
    use IdMapper, TranslationMapper;

    /**
     * @ORM\OneToMany(targetEntity="ChapterTranslation", mappedBy="object", cascade={"persist", "remove"})
     */
    protected $translations;

    /**
     * @ORM\ManyToOne(targetEntity="Book", inversedBy="chapters")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     */
    protected $book;

    /**
     * @ORM\OneToMany(targetEntity="Bookmark", mappedBy="chapter"))
     */
    protected $bookmarks;

    /**
     * @ORM\Column(type="string", length=511, nullable=false)
     *
     * @Gedmo\Translatable
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Gedmo\Translatable
     */
    protected $text;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $rawText;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $textFormatter;

    /**
     * @ORM\Column(type="integer")
     */
    protected $chapterOrder;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection;
        $this->bookmarks    = new ArrayCollection;
    }

    /**
     * To string
     */
    public function __toString()
    {
        return ( $this->title ) ? $this->title : "";
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Chapter
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Chapter
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set rawText
     *
     * @param string $rawText
     * @return Chapter
     */
    public function setRawText($rawText)
    {
        $this->rawText = $rawText;

        return $this;
    }

    /**
     * Get rawText
     *
     * @return string 
     */
    public function getRawText()
    {
        return $this->rawText;
    }

    /**
     * Set textFormatter
     *
     * @param string $textFormatter
     * @return Chapter
     */
    public function setTextFormatter($textFormatter)
    {
        $this->textFormatter = $textFormatter;

        return $this;
    }

    /**
     * Get textFormatter
     *
     * @return string 
     */
    public function getTextFormatter()
    {
        return $this->textFormatter;
    }

    /**
     * Set book
     *
     * @param \AppBundle\Entity\Book $book
     * @return Chapter
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
     * @ORM\PrePersist()
     */
    public function setChapterOrder($chapterOrder)
    {
        $this->chapterOrder = $chapterOrder;

        return $this;
    }

    /**
     * Get chapterOrder
     *
     * @return integer 
     */
    public function getChapterOrder()
    {
        return $this->chapterOrder;
    }

    /**
     * Add bookmarks
     *
     * @param \AppBundle\Entity\Bookmark $bookmarks
     * @return Chapter
     */
    public function addBookmark(\AppBundle\Entity\Bookmark $bookmarks)
    {
        $this->bookmarks[] = $bookmarks;

        return $this;
    }

    /**
     * Remove bookmarks
     *
     * @param \AppBundle\Entity\Bookmark $bookmarks
     */
    public function removeBookmark(\AppBundle\Entity\Bookmark $bookmarks)
    {
        $this->bookmarks->removeElement($bookmarks);
    }

    /**
     * Get bookmarks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBookmarks()
    {
        return $this->bookmarks;
    }
}