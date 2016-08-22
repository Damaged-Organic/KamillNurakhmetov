<?php
// src/AppBundle/Entity/Reader.php
namespace AppBundle\Entity;

use Serializable;

use Symfony\Component\Security\Core\User\AdvancedUserInterface,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\DoctrineMapping\IdMapper,
    AppBundle\Entity\Utility\DoctrineMapping\CurrencyListInterface;

/**
 * @ORM\Table(name="readers")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ReaderRepository")
 *
 * @UniqueEntity(fields="email", message="reader.email.unique")
 */
class Reader implements AdvancedUserInterface, Serializable, CurrencyListInterface
{
    use IdMapper;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Book", inversedBy="readers")
     * @ORM\JoinTable(name="readers_books_acquired")
     */
    protected $books;

    /**
     * @ORM\OneToMany(targetEntity="Review", mappedBy="reader")
     */
    protected $reviews;

    /**
     * @ORM\OneToMany(targetEntity="Bookmark", mappedBy="reader")
     */
    protected $bookmarks;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="reader")
     */
    protected $orders;

    protected $username;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message="reader.email.not_blank", groups={"reset"})
     * @Assert\Email(
     *      message="reader.email.valid",
     *      checkMX=true,
     *      groups={"reset"}
     * )
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "reader.pseudonym.length.min",
     *      maxMessage = "reader.pseudonym.length.max"
     * )
     */
    protected $pseudonym;

    /**
     * @ORM\Column(type="string", length=64)
     *
     * @Assert\NotBlank(
     *      message="reader.password.not_blank"
     * )
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=3)
     *
     * @Assert\NotBlank(message="reader.preferred_currency.not_blank")
     * @Assert\Choice(
     *      callback="getCurrencyList",
     *      message="reader.preferred_currency.invalid_message"
     * )
     */
    protected $preferredCurrency;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Assert\Type(
     *     type="bool",
     *     message="reader.is_active.type",
     * )
     */
    protected $isActive;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isSubscribed;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $subscriptionEnd;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $registrationDigest;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $registrationDigestDatetime;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $resetDigest;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $resetDigestDatetime;

    public function __construct()
    {
        $this->books     = new ArrayCollection;
        $this->bookmarks = new ArrayCollection;
        $this->orders    = new ArrayCollection;
        $this->reviews   = new ArrayCollection;

        $this
            ->setIsActive(FALSE)
            ->setIsSubscribed(FALSE)
        ;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return ['ROLE_READER'];
    }

    public function getSalt()
    {
        return NULL;
    }

    public function eraseCredentials()
    {
    }

    public function isAccountNonExpired()
    {
        return TRUE;
    }

    public function isAccountNonLocked()
    {
        return TRUE;
    }

    public function isCredentialsNonExpired()
    {
        return TRUE;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            $this->isActive
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            $this->isActive
            ) = unserialize($serialized);
    }

    /**
     * @Assert\True(message="reader.password.legal")
     */
    public function isPasswordLegal()
    {
        return ($this->email !== $this->password);
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Reader
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function setPseudonym($pseudonym)
    {
        $this->pseudonym = $pseudonym;

        return $this;
    }

    public function getPseudonym()
    {
        return $this->pseudonym;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Reader
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Reader
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    static public function getCurrencyList()
    {
        return [self::UAH, self::USD, self::RUB];
    }

    /**
     * Set preferredCurrency
     *
     * @param boolean $preferredCurrency
     * @return Reader
     */
    public function setPreferredCurrency($preferredCurrency)
    {
        $this->preferredCurrency = $preferredCurrency;

        return $this;
    }

    /**
     * Get preferredCurrency
     *
     * @return boolean
     */
    public function getPreferredCurrency()
    {
        return $this->preferredCurrency;
    }

    /**
     * Set registrationDigest
     *
     * @param string $registrationDigest
     * @return Reader
     */
    public function setRegistrationDigest($registrationDigest)
    {
        $this->registrationDigest = $registrationDigest;

        return $this;
    }

    /**
     * Get registrationDigest
     *
     * @return string
     */
    public function getRegistrationDigest()
    {
        return $this->registrationDigest;
    }

    /**
     * Set registrationDigestDatetime
     *
     * @param \DateTime $registrationDigestDatetime
     * @return Reader
     */
    public function setRegistrationDigestDatetime($registrationDigestDatetime)
    {
        $this->registrationDigestDatetime = $registrationDigestDatetime;

        return $this;
    }

    /**
     * Get registrationDigestDatetime
     *
     * @return \DateTime
     */
    public function getRegistrationDigestDatetime()
    {
        return $this->registrationDigestDatetime;
    }

    /**
     * Set resetDigest
     *
     * @param string $resetDigest
     * @return Reader
     */
    public function setResetDigest($resetDigest)
    {
        $this->resetDigest = $resetDigest;

        return $this;
    }

    /**
     * Get resetDigest
     *
     * @return string
     */
    public function getResetDigest()
    {
        return $this->resetDigest;
    }

    /**
     * Set resetDigestDatetime
     *
     * @param \DateTime $resetDigestDatetime
     * @return Reader
     */
    public function setResetDigestDatetime($resetDigestDatetime)
    {
        $this->resetDigestDatetime = $resetDigestDatetime;

        return $this;
    }

    /**
     * Get resetDigestDatetime
     *
     * @return \DateTime
     */
    public function getResetDigestDatetime()
    {
        return $this->resetDigestDatetime;
    }

    /**
     * Add books
     *
     * @param \AppBundle\Entity\Book $book
     * @return Reader
     */
    public function addBook(\AppBundle\Entity\Book $book)
    {
        $book->addReader($this);
        $this->books[] = $book;

        return $this;
    }

    /**
     * Remove books
     *
     * @param \AppBundle\Entity\Book $books
     */
    public function removeBook(\AppBundle\Entity\Book $books)
    {
        $this->books->removeElement($books);
    }

    /**
     * Get books
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBooks()
    {
        return $this->books;
    }

    /**
     * Add reviews
     *
     * @param \AppBundle\Entity\Review $review
     * @return Reader
     */
    public function addReview(\AppBundle\Entity\Review $review)
    {
        $review->addReader($this);
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
     * Set isSubscribed
     *
     * @param boolean $isSubscribed
     * @return Reader
     */
    public function setIsSubscribed($isSubscribed)
    {
        $this->isSubscribed = $isSubscribed;

        return $this;
    }

    /**
     * Get isSubscribed
     *
     * @return boolean
     */
    public function getIsSubscribed()
    {
        return $this->isSubscribed;
    }

    /**
     * Set subscriptionEnd
     *
     * @param \DateTime $subscriptionEnd
     * @return Reader
     */
    public function setSubscriptionEnd($subscriptionEnd)
    {
        $this->subscriptionEnd = $subscriptionEnd;

        return $this;
    }

    /**
     * Get subscriptionEnd
     *
     * @return \DateTime
     */
    public function getSubscriptionEnd()
    {
        return $this->subscriptionEnd;
    }

    /**
     * Add bookmarks
     *
     * @param \AppBundle\Entity\Bookmark $bookmarks
     * @return Reader
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
     * @return Reader
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
}
