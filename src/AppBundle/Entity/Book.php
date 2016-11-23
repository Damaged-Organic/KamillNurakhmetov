<?php
// src/AppBundle/Entity/Book.php
namespace AppBundle\Entity;

use DateTime;

use Symfony\Component\HttpFoundation\File\File,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Component\Security\Core\Exception\BadCredentialsException;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo,
    Gedmo\Translatable\Translatable;

use Vich\UploaderBundle\Mapping\Annotation as Vich;

use AppBundle\Entity\Utility\DoctrineMapping\IdMapper,
    AppBundle\Entity\Utility\DoctrineMapping\SlugMapper,
    AppBundle\Entity\Utility\DoctrineMapping\TranslationMapper,
    AppBundle\Entity\Utility\DoctrineMapping\CurrencyListInterface;

/**
 * @ORM\Table(name="books")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\BookRepository")
 *
 * @Gedmo\TranslationEntity(class="AppBundle\Entity\BookTranslation")
 *
 * @Vich\Uploadable
 */
class Book implements Translatable, CurrencyListInterface
{
    use IdMapper, SlugMapper, TranslationMapper;

    const WEB_PATH = "/uploads/books/covers/";

    /**
     * @ORM\OneToMany(targetEntity="BookTranslation", mappedBy="object", cascade={"persist", "remove"})
     */
    protected $translations;

    /**
     * @ORM\OneToMany(targetEntity="Chapter", mappedBy="book", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $chapters;

    /**
     * @ORM\OneToMany(targetEntity="Review", mappedBy="book", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    protected $reviews;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Reader", mappedBy="books")
     */
    protected $readers;

    /**
     * @ORM\OneToMany(targetEntity="Bookmark", mappedBy="book", cascade={"remove"}, orphanRemoval=true)
     */
    protected $bookmarks;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="book")
     */
    protected $orders;

    /**
     * @ORM\OneToMany(targetEntity="OrderBook", mappedBy="book")
     */
    protected $orderBooks;

    /**
     * @ORM\Column(type="string", length=511, nullable=false)
     *
     * @Gedmo\Translatable
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=false)
     *
     * @Gedmo\Translatable
     */
    protected $description;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $year;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $pages;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=false)
     **/
    protected $priceUah;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=false)
     **/
    protected $priceUsd;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=false)
     **/
    protected $priceRub;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=false)
     **/
    protected $pricePaperUah;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=false)
     **/
    protected $pricePaperUsd;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=false)
     **/
    protected $pricePaperRub;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $views;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isAvailable = TRUE;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $hasPaper = FALSE;

    /**
     * @Assert\File(
     *     maxSize="3M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg", "image/gif"}
     * )
     *
     * @Vich\UploadableField(mapping="book_cover", fileNameProperty="coverName")
     */
    protected $coverFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $coverName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection;
        $this->readers      = new ArrayCollection;
        $this->chapters     = new ArrayCollection;
        $this->reviews      = new ArrayCollection;
        $this->bookmarks    = new ArrayCollection;
        $this->orders       = new ArrayCollection;

        $this
            ->setViews(0)
        ;
    }

    /**
     * To string
     */
    public function __toString()
    {
        return ( $this->title ) ? $this->title : "";
    }

    /* Vich uploadable methods */

    public function setCoverFile($coverFile = NULL)
    {
        $this->coverFile = $coverFile;

        if( $coverFile instanceof File )
            $this->updatedAt = new DateTime;
    }

    public function getCoverFile()
    {
        return $this->coverFile;
    }

    public function getCoverPath()
    {
        return ( $this->coverName )
            ? self::WEB_PATH.$this->coverName
            : FALSE;
    }

    /* END Vich uploadable methods */

    /**
     * Set title
     *
     * @param string $title
     * @return Book
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
     * Set description
     *
     * @param string $description
     * @return Book
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Book
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set pages
     *
     * @param integer $pages
     * @return Book
     */
    public function setPages($pages)
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Get pages
     *
     * @return integer
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Set views
     *
     * @param integer $views
     * @return Book
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Add chapters
     *
     * @param \AppBundle\Entity\Chapter $chapters
     * @return Book
     */
    public function addChapter(\AppBundle\Entity\Chapter $chapter)
    {
        $chapter->setBook($this);
        $this->chapters[] = $chapter;

        return $this;
    }

    /**
     * Remove chapters
     *
     * @param \AppBundle\Entity\Chapter $chapters
     */
    public function removeChapter(\AppBundle\Entity\Chapter $chapters)
    {
        $this->chapters->removeElement($chapters);
    }

    /**
     * Get chapters
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChapters()
    {
        return $this->chapters;
    }

    /**
     * Add reviews
     *
     * @param \AppBundle\Entity\Review $reviews
     * @return Book
     */
    public function addReview(\AppBundle\Entity\Review $review)
    {
        $review->setBook($this);
        $this->reviews[] = $review;

        return $this;
    }

    /**
     * Remove reviews
     *
     * @param \AppBundle\Entity\Review $reviews
     */
    public function removeReview(\AppBundle\Entity\Review $reviews)
    {
        $this->reviews->removeElement($reviews);
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Set coverName
     *
     * @param string $coverName
     * @return Book
     */
    public function setCoverName($coverName)
    {
        $this->coverName = $coverName;

        return $this;
    }

    /**
     * Get coverName
     *
     * @return string
     */
    public function getCoverName()
    {
        return $this->coverName;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Book
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add readers
     *
     * @param \AppBundle\Entity\Reader $reader
     * @return Book
     */
    public function addReader(\AppBundle\Entity\Reader $reader)
    {
        $this->readers[] = $reader;

        return $this;
    }

    /**
     * Remove readers
     *
     * @param \AppBundle\Entity\Reader $readers
     */
    public function removeReader(\AppBundle\Entity\Reader $readers)
    {
        $this->readers->removeElement($readers);
    }

    /**
     * Get readers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReaders()
    {
        return $this->readers;
    }

    /**
     * Set priceUah
     *
     * @param string $priceUah
     * @return Book
     */
    public function setPriceUah($priceUah)
    {
        $this->priceUah = $priceUah;

        return $this;
    }

    /**
     * Get priceUah
     *
     * @return string
     */
    public function getPriceUah()
    {
        return $this->priceUah;
    }

    /**
     * Set priceUsd
     *
     * @param string $priceUsd
     * @return Book
     */
    public function setPriceUsd($priceUsd)
    {
        $this->priceUsd = $priceUsd;

        return $this;
    }

    /**
     * Get priceUsd
     *
     * @return string
     */
    public function getPriceUsd()
    {
        return $this->priceUsd;
    }

    /**
     * Set priceRub
     *
     * @param string $priceRub
     * @return Book
     */
    public function setPriceRub($priceRub)
    {
        $this->priceRub = $priceRub;

        return $this;
    }

    /**
     * Get priceRub
     *
     * @return string
     */
    public function getPriceRub()
    {
        return $this->priceRub;
    }

    /**
     * Set pricePaperUah
     *
     * @param string $pricePaperUah
     * @return Book
     */
    public function setPricePaperUah($pricePaperUah)
    {
        $this->pricePaperUah = $pricePaperUah;

        return $this;
    }

    /**
     * Get pricePaperUah
     *
     * @return string
     */
    public function getPricePaperUah()
    {
        return $this->pricePaperUah;
    }

    /**
     * Set pricePaperUsd
     *
     * @param string $pricePaperUsd
     * @return Book
     */
    public function setPricePaperUsd($pricePaperUsd)
    {
        $this->pricePaperUsd = $pricePaperUsd;

        return $this;
    }

    /**
     * Get pricePaperUsd
     *
     * @return string
     */
    public function getPricePaperUsd()
    {
        return $this->pricePaperUsd;
    }

    /**
     * Set priceRub
     *
     * @param string $pricePaperRub
     * @return Book
     */
    public function setPricePaperRub($pricePaperRub)
    {
        $this->pricePaperRub = $pricePaperRub;

        return $this;
    }

    /**
     * Get pricePaperRub
     *
     * @return string
     */
    public function getPricePaperRub()
    {
        return $this->pricePaperRub;
    }

    /**
     * Set isAvailable
     *
     * @param boolean $isAvailable
     * @return Book
     */
    public function setIsAvailable($isAvailable)
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    /**
     * Get isAvailable
     *
     * @return boolean
     */
    public function getIsAvailable()
    {
        return $this->isAvailable;
    }

    /**
     * Set hasPaper
     *
     * @param boolean $hasPaper
     * @return Book
     */
    public function setHasPaper($hasPaper)
    {
        $this->hasPaper = $hasPaper;

        return $this;
    }

    /**
     * Get hasPaper
     *
     * @return boolean
     */
    public function getHasPaper()
    {
        return $this->hasPaper;
    }

    static public function getCurrencyList()
    {
        return [self::UAH, self::USD, self::RUB];
    }

    public function getPrice($currency)
    {
        switch($currency)
        {
            case self::UAH:
                return $this->getPriceUah();
            break;

            case self::USD:
                return $this->getPriceUsd();
            break;

            case self::RUB:
                return $this->getPriceRub();
            break;

            default:
                throw new BadCredentialsException();
            break;
        }
    }

    public function getPricePaper($currency)
    {
        switch($currency)
        {
            case self::UAH:
                return $this->getPricePaperUah();
            break;

            case self::USD:
                return $this->getPricePaperUsd();
            break;

            case self::RUB:
                return $this->getPricePaperRub();
            break;

            default:
                throw new BadCredentialsException();
            break;
        }
    }

    /**
     * Add bookmarks
     *
     * @param \AppBundle\Entity\Bookmark $bookmarks
     * @return Book
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

    /**
     * Add orders
     *
     * @param \AppBundle\Entity\Order $orders
     * @return Book
     */
    public function addOrder(\AppBundle\Entity\Order $orders)
    {
        $this->orders[] = $orders;

        return $this;
    }

    /**
     * Remove orders
     *
     * @param \AppBundle\Entity\Order $orders
     */
    public function removeOrder(\AppBundle\Entity\Order $orders)
    {
        $this->orders->removeElement($orders);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Add orderBook
     *
     * @param \AppBundle\Entity\OrderBook $orderBook
     * @return Book
     */
    public function addOrderBook(\AppBundle\Entity\OrderBook $orderBook)
    {
        $orderBook->setBook($this);
        $this->orderBooks[] = $orderBook;

        return $this;
    }

    /**
     * Remove orderBooks
     *
     * @param \AppBundle\Entity\OrderBook $orderBooks
     */
    public function removeOrderBook(\AppBundle\Entity\OrderBook $orderBooks)
    {
        $this->orderBooks->removeElement($orderBooks);
    }

    /**
     * Get orderBooks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderBooks()
    {
        return $this->orderBooks;
    }
}
