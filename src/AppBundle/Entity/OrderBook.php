<?php
// src/AppBundle/Entity/OrderBook.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\DoctrineMapping\IdMapper;

/**
 * @ORM\Table(name="orders_books")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\OrderBookRepository")
 */
class OrderBook
{
    use IdMapper;

    /**
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="orderBooks")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    protected $order;

    /**
     * @ORM\ManyToOne(targetEntity="Book", inversedBy="orderBooks")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     */
    protected $book;

    /**
     * @ORM\Column(type="integer")
     */
    protected $quantity;

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return OrderBook
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set order
     *
     * @param \AppBundle\Entity\Order $order
     * @return OrderBook
     */
    public function setOrder(\AppBundle\Entity\Order $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \AppBundle\Entity\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set book
     *
     * @param \AppBundle\Entity\Book $book
     * @return OrderBook
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

    public function getItemsPrice(Reader $user)
    {
        $price = $this->getBook()->getPricePaper($user->getPreferredCurrency());

        return bcmul($price, $this->getQuantity(), 2);
    }
}
