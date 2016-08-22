<?php
// src/AppBundle/Entity/Story.php
namespace AppBundle\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo,
    Gedmo\Translatable\Translatable;

use AppBundle\Entity\Utility\DoctrineMapping\IdMapper,
    AppBundle\Entity\Utility\DoctrineMapping\TranslationMapper;

/**
 * @ORM\Table(name="stories")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\StoryRepository")
 *
 * @Gedmo\TranslationEntity(class="AppBundle\Entity\StoryTranslation")
 *
 * @ORM\HasLifecycleCallbacks
 */
class Story
{
    use IdMapper, TranslationMapper;

    const SORTING_AVAILABLE = 'available';
    const SORTING_VIEWED    = 'most_viewed';
    const SORTING_REVIEWED  = 'most_reviewed';

    /**
     * @ORM\OneToMany(targetEntity="StoryTranslation", mappedBy="object", cascade={"persist", "remove"})
     */
    protected $translations;

    /**
     * @ORM\ManyToOne(targetEntity="StoryCategory", inversedBy="stories")
     * @ORM\JoinColumn(name="story_category_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $storyCategory;

    /**
     * @ORM\OneToMany(targetEntity="Review", mappedBy="story", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    protected $reviews;

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
    protected $text;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    protected $rawText;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $textFormatter;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    protected $publicationDate;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $views;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isFreeForAll;

    /**
     * @ORM\Column(type="integer")
     */
    protected $storyOrder;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection;
        $this->reviews      = new ArrayCollection;

        $this
            ->setPublicationDate(new DateTime)
            ->setViews(0)
            ->setIsFreeForAll(FALSE)
        ;
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
     * @return Story
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
     * @return Story
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
     * @return Story
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
     * @return Story
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
     * Set publicationDate
     *
     * @param \DateTime $publicationDate
     * @return Story
     */
    public function setPublicationDate($publicationDate)
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    /**
     * Get publicationDate
     *
     * @return \DateTime
     */
    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    /**
     * Set views
     *
     * @param integer $views
     * @return Story
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
     * Set isFreeForAll
     *
     * @param boolean $isFreeForAll
     * @return Story
     */
    public function setIsFreeForAll($isFreeForAll)
    {
        $this->isFreeForAll = $isFreeForAll;

        return $this;
    }

    /**
     * Get isFreeForAll
     *
     * @return boolean
     */
    public function getIsFreeForAll()
    {
        return $this->isFreeForAll;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setStoryOrder($storyOrder)
    {
        $this->storyOrder = $storyOrder;

        return $this;
    }

    /**
     * Get storyOrder
     *
     * @return integer
     */
    public function getStoryOrder()
    {
        return $this->storyOrder;
    }

    /**
     * Add reviews
     *
     * @param \AppBundle\Entity\Review $reviews
     * @return Story
     */
    public function addReview(\AppBundle\Entity\Review $reviews)
    {
        $this->reviews[] = $reviews;

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
     * Set storyCategory
     *
     * @param \AppBundle\Entity\StoryCategory $storyCategory
     * @return Story
     */
    public function setStoryCategory(\AppBundle\Entity\StoryCategory $storyCategory = null)
    {
        $this->storyCategory = $storyCategory;

        return $this;
    }

    /**
     * Get storyCategory
     *
     * @return \AppBundle\Entity\StoryCategory
     */
    public function getStoryCategory()
    {
        return $this->storyCategory;
    }

    static public function getSortingParameters()
    {
        return [self::SORTING_AVAILABLE, self::SORTING_VIEWED, self::SORTING_REVIEWED];
    }
}
