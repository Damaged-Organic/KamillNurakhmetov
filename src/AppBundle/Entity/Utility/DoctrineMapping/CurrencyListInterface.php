<?php
// src/AppBundle/Entity/Utility/DoctrineMapping/CurrencyListInterface.php
namespace AppBundle\Entity\Utility\DoctrineMapping;

interface CurrencyListInterface
{
    const UAH = "UAH";
    const USD = "USD";
    const RUB = "RUB";

    static public function getCurrencyList();
}