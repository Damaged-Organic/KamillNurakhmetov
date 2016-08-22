<?php
// src/AppBundle/Form/Model/PrivateOffice/ReaderCurrency.php
namespace AppBundle\Form\Model\PrivateOffice;

use Symfony\Component\Validator\Constraints as Assert;

use AppBundle\Entity\Utility\DoctrineMapping\CurrencyListInterface;

class ReaderCurrency implements CurrencyListInterface
{
    /**
     * @Assert\NotBlank(message="private_office.preferred_currency.not_blank")
     * @Assert\Choice(
     *      callback="getCurrencyList",
     *      message="private_office.preferred_currency.invalid_message"
     * )
     */
    protected $preferredCurrency;

    static public function getCurrencyList()
    {
        return [self::UAH, self::USD, self::RUB];
    }

    public function setPreferredCurrency($preferredCurrency)
    {
        $this->preferredCurrency = $preferredCurrency;

        return $this;
    }

    public function getPreferredCurrency()
    {
        return $this->preferredCurrency;
    }
}