<?php
// src/AppBundle/Entity/OrderBookCredentials.php
namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\DoctrineMapping\IdMapper,
    AppBundle\Validator\Constraints as CustomAssert;

/**
 * @ORM\Table(name="orders_books_credentials")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\OrderBookCredentialsRepository")
 */
class OrderBookCredentials
{
    use IdMapper;

    const DELIVERY_SERVICE_DHL = "DHL";
    const DELIVERY_SERVICE_NP  = "Новая Почта";

    /**
     * @ORM\OneToOne(targetEntity="Order", inversedBy="orderBookCredentials")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    protected $order;

    /**
     * @ORM\Column(type="string", length=254)
     *
     * @Assert\NotBlank(message="reader.email.not_blank")
     * @Assert\Email(
     *      message="reader.email.valid",
     *      checkMX=true
     * )
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="reader.pseudonym.not_blank")
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "reader.pseudonym.length.min",
     *      maxMessage = "reader.pseudonym.length.max"
     * )
     */
    protected $pseudonym;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     *
     * @CustomAssert\IsPhone
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\NotBlank(message="reader.delivery.service.not_blank")
     *
     * @CustomAssert\DeliveryService
     */
    protected $deliveryService;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="reader.delivery.city.not_blank")
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "reader.delivery.city.length.min",
     *      maxMessage = "reader.delivery.city.length.max"
     * )
     */
    protected $deliveryCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "reader.delivery.office.length.min",
     *      maxMessage = "reader.delivery.office.length.max"
     * )
     */
    protected $deliveryOffice;

    /**
     * Set email
     *
     * @param string $email
     * @return OrderBookCredentials
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

    /**
     * Set pseudonym
     *
     * @param string $pseudonym
     * @return OrderBookCredentials
     */
    public function setPseudonym($pseudonym)
    {
        $this->pseudonym = $pseudonym;

        return $this;
    }

    /**
     * Get pseudonym
     *
     * @return string 
     */
    public function getPseudonym()
    {
        return $this->pseudonym;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return OrderBookCredentials
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set deliveryService
     *
     * @param string $deliveryService
     * @return OrderBookCredentials
     */
    public function setDeliveryService($deliveryService)
    {
        $this->deliveryService = $deliveryService;

        return $this;
    }

    /**
     * Get deliveryService
     *
     * @return string 
     */
    public function getDeliveryService()
    {
        return $this->deliveryService;
    }

    /**
     * Set deliveryCity
     *
     * @param string $deliveryCity
     * @return OrderBookCredentials
     */
    public function setDeliveryCity($deliveryCity)
    {
        $this->deliveryCity = $deliveryCity;

        return $this;
    }

    /**
     * Get deliveryCity
     *
     * @return string 
     */
    public function getDeliveryCity()
    {
        return $this->deliveryCity;
    }

    /**
     * Set deliveryOffice
     *
     * @param string $deliveryOffice
     * @return OrderBookCredentials
     */
    public function setDeliveryOffice($deliveryOffice)
    {
        $this->deliveryOffice = $deliveryOffice;

        return $this;
    }

    /**
     * Get deliveryOffice
     *
     * @return string 
     */
    public function getDeliveryOffice()
    {
        return $this->deliveryOffice;
    }

    /**
     * Set order
     *
     * @param \AppBundle\Entity\Order $order
     * @return OrderBookCredentials
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
}
