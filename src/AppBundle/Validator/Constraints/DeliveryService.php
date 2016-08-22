<?php
// src/AppBundle/Validator/Constraints/DeliveryService.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DeliveryService extends Constraint
{
    public $message = "reader.delivery.service.valid";
}
