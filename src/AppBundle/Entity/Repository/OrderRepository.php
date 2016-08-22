<?php
// src/AppBundle/Entity/Repository/OrderRepository.php
namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Repository\Contract\CustomEntityRepository,
    AppBundle\Service\Payment\LiqPayProcessing;

class OrderRepository extends CustomEntityRepository
{
    public function findPendingOrder($orderId)
    {
        $query = $this->createQueryBuilder("o")
            ->select("o")
            ->where("o.orderId = :orderId")
            ->andWhere("o.orderStatus != :success")
            ->andWhere("o.orderStatus != :failure")
            ->setParameters([
                'orderId' => $orderId,
                'success' => LiqPayProcessing::LIQ_PAY_ORDER_STATUS_SUCCESS,
                'failure' => LiqPayProcessing::LIQ_PAY_ORDER_STATUS_FAILURE,
            ])
            ->getQuery()
        ;

        return $query->getSingleResult();
    }
}