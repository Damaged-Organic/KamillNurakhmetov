<?php
// src/AppBundle/Entity/Order.php
namespace AppBundle\Entity;

use DateTime;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\DoctrineMapping\IdMapper;

/**
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\OrderRepository")
 */
class Order
{
    use IdMapper;

    /**
     * @ORM\ManyToOne(targetEntity="Reader", inversedBy="orders")
     * @ORM\JoinColumn(name="reader_id", referencedColumnName="id")
     */
    protected $reader;

    /**
     * @ORM\ManyToOne(targetEntity="Book", inversedBy="orders")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     */
    protected $book;

    /**
     * @ORM\OneToMany(targetEntity="OrderBook", mappedBy="order", cascade={"persist", "remove"})
     */
    protected $orderBooks;

    /**
     * @ORM\OneToOne(targetEntity="OrderBookCredentials", mappedBy="order", cascade={"persist", "remove"})
     */
    protected $orderBookCredentials;

    /**
     * @ORM\ManyToOne(targetEntity="Subscription", inversedBy="orders")
     * @ORM\JoinColumn(name="subscription_id", referencedColumnName="id")
     */
    protected $subscription;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    protected $orderId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     */
    protected $orderDateTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $orderStatus;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $orderData;

    /**
     * @ORM\Column(type="string", length=56, nullable=true)
     */
    protected $orderSignature;

    /**
     * @ORM\Column(type="string", length=500)
     */
    protected $itemTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $itemDescription;

    /**
     * @ORM\Column(type="string", length=3)
     */
    protected $itemCurrency;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $itemPrice;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orderBooks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set orderId
     *
     * @param string $orderId
     * @return Order
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set orderDateTime
     *
     * @param \DateTime $orderDateTime
     * @return Order
     */
    public function setOrderDateTime($orderDateTime)
    {
        $this->orderDateTime = $orderDateTime;

        return $this;
    }

    /**
     * Get orderDateTime
     *
     * @return \DateTime
     */
    public function getOrderDateTime()
    {
        return $this->orderDateTime;
    }

    /**
     * Set orderStatus
     *
     * @param string $orderStatus
     * @return Order
     */
    public function setOrderStatus($orderStatus)
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    /**
     * Get orderStatus
     *
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->orderStatus;
    }


    /**
     * Set orderData
     *
     * @param string $orderData
     * @return Order
     */
    public function setOrderData($orderData)
    {
        $this->orderData = $orderData;

        return $this;
    }

    /**
     * Get orderData
     *
     * @return string
     */
    public function getOrderData()
    {
        return $this->orderData;
    }

    /**
     * Set orderSignature
     *
     * @param string $orderSignature
     * @return Order
     */
    public function setOrderSignature($orderSignature)
    {
        $this->orderSignature = $orderSignature;

        return $this;
    }

    /**
     * Get orderSignature
     *
     * @return string
     */
    public function getOrderSignature()
    {
        return $this->orderSignature;
    }

    /**
     * Set itemTitle
     *
     * @param string $itemTitle
     * @return Order
     */
    public function setItemTitle($itemTitle)
    {
        $this->itemTitle = $itemTitle;

        return $this;
    }

    /**
     * Get itemTitle
     *
     * @return string
     */
    public function getItemTitle()
    {
        return $this->itemTitle;
    }

    /**
     * Set itemDescription
     *
     * @param string $itemDescription
     * @return Order
     */
    public function setItemDescription($itemDescription)
    {
        $this->itemDescription = $itemDescription;

        return $this;
    }

    /**
     * Get itemDescription
     *
     * @return string
     */
    public function getItemDescription()
    {
        return $this->itemDescription;
    }

    /**
     * Set itemCurrency
     *
     * @param string $itemCurrency
     * @return Order
     */
    public function setItemCurrency($itemCurrency)
    {
        $this->itemCurrency = $itemCurrency;

        return $this;
    }

    /**
     * Get itemCurrency
     *
     * @return string
     */
    public function getItemCurrency()
    {
        return $this->itemCurrency;
    }

    /**
     * Set itemPrice
     *
     * @param string $itemPrice
     * @return Order
     */
    public function setItemPrice($itemPrice)
    {
        $this->itemPrice = $itemPrice;

        return $this;
    }

    /**
     * Get itemPrice
     *
     * @return string
     */
    public function getItemPrice()
    {
        return $this->itemPrice;
    }

    /**
     * Set reader
     *
     * @param \AppBundle\Entity\Reader $reader
     * @return Order
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
     * @return Order
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
     * Set subscription
     *
     * @param \AppBundle\Entity\Subscription $subscription
     * @return Order
     */
    public function setSubscription(\AppBundle\Entity\Subscription $subscription = null)
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * Get subscription
     *
     * @return \AppBundle\Entity\Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * Add orderBook
     *
     * @param \AppBundle\Entity\OrderBook $orderBook
     * @return Order
     */
    public function addOrderBook(\AppBundle\Entity\OrderBook $orderBook)
    {
        $orderBook->setOrder($this);
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

    /**
     * Set orderBookCredentials
     *
     * @param \AppBundle\Entity\OrderBookCredentials $orderBookCredentials
     * @return Order
     */
    public function setOrderBookCredentials(\AppBundle\Entity\OrderBookCredentials $orderBookCredentials = null)
    {
        $orderBookCredentials->setOrder($this);
        $this->orderBookCredentials = $orderBookCredentials;

        return $this;
    }

    /**
     * Get orderBookCredentials
     *
     * @return \AppBundle\Entity\OrderBookCredentials
     */
    public function getOrderBookCredentials()
    {
        return $this->orderBookCredentials;
    }
}
