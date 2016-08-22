<?php
// src/AppBundle/Entity/Subscription.php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\DoctrineMapping\IdMapper,
    AppBundle\Entity\Utility\DoctrineMapping\CurrencyListInterface;

/**
 * @ORM\Table(name="subscriptions")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SubscriptionRepository")
 */
class Subscription implements CurrencyListInterface
{
    use IdMapper;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="subscription")
     */
    protected $orders;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     */
    protected $durationNumber;

    /**
     * @ORM\Column(type="string", length=60)
     */
    protected $durationMeasure;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     **/
    protected $priceUah;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     **/
    protected $priceUsd;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     **/
    protected $priceRub;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orders = new ArrayCollection;
    }

    /**
     * To string
     */
    public function __toString()
    {
        return ( $this->name ) ? $this->name : "";
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Subscription
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set durationNumber
     *
     * @param integer $durationNumber
     * @return Subscription
     */
    public function setDurationNumber($durationNumber)
    {
        $this->durationNumber = $durationNumber;

        return $this;
    }

    /**
     * Get durationNumber
     *
     * @return integer 
     */
    public function getDurationNumber()
    {
        return $this->durationNumber;
    }

    /**
     * Set durationMeasure
     *
     * @param string $durationMeasure
     * @return Subscription
     */
    public function setDurationMeasure($durationMeasure)
    {
        $this->durationMeasure = $durationMeasure;

        return $this;
    }

    /**
     * Get durationMeasure
     *
     * @return string 
     */
    public function getDurationMeasure()
    {
        return $this->durationMeasure;
    }

    /**
     * Set priceUah
     *
     * @param string $priceUah
     * @return Subscription
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
     * @return Subscription
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
     * @return Subscription
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

    /**
     * Add orders
     *
     * @param \AppBundle\Entity\Order $orders
     * @return Subscription
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
