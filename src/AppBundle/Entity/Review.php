<?php
// src/AppBundle/Entity/Review.php
namespace AppBundle\Entity;

use DateTime;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\DoctrineMapping\IdMapper;

/**
 * @ORM\Table(name="reviews")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ReviewRepository")
 */
class Review
{
    use IdMapper;

    /**
     * @ORM\ManyToOne(targetEntity="Book", inversedBy="reviews")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $book;

    /**
     * @ORM\ManyToOne(targetEntity="Story", inversedBy="reviews")
     * @ORM\JoinColumn(name="story_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $story;

    /**
     * @ORM\ManyToOne(targetEntity="Reader", inversedBy="reviews")
     * @ORM\JoinColumn(name="reader_id", referencedColumnName="id")
     */
    protected $reader;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $publishedAt;

    /**
     * @ORM\Column(type="text", nullable=false)
     *
     * @Assert\NotBlank(
     *      message = "review.text.not_blank"
     * )
     * @Assert\Length(
     *      min = 5,
     *      max = 2500,
     *      minMessage = "review.text.length.min",
     *      maxMessage = "review.text.length.max"
     * )
     */
    protected $text;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $authorCredentials;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isActive;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this
            ->setPublishedAt(new DateTime)
            ->setIsActive(FALSE)
        ;
    }

    /**
     * To string
     */
    public function __toString()
    {
        return ( $this->publishedAt ) ? $this->publishedAt->format('d-m-Y H:i') : "";
    }

    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     * @return Review
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Review
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
     * Set isActive
     *
     * @param string $isActive
     * @return Review
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return string
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set book
     *
     * @param \AppBundle\Entity\Book $book
     * @return Review
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
     * Set reader
     *
     * @param \AppBundle\Entity\Reader $reader
     * @return Review
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
     * Set story
     *
     * @param \AppBundle\Entity\Story $story
     * @return Review
     */
    public function setStory(\AppBundle\Entity\Story $story = null)
    {
        $this->story = $story;

        return $this;
    }

    /**
     * Get story
     *
     * @return \AppBundle\Entity\Story
     */
    public function getStory()
    {
        return $this->story;
    }

    /**
     * Set authorCredentials
     *
     * @param string $authorCredentials
     * @return Review
     */
    public function setAuthorCredentials($authorCredentials)
    {
        $this->authorCredentials = $authorCredentials;

        return $this;
    }

    /**
     * Get authorCredentials
     *
     * @return string
     */
    public function getAuthorCredentials()
    {
        return $this->authorCredentials;
    }
}
