<?php
// src/AppBundle/Validator/Constraints/DeliveryServiceValidator.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator;

use AppBundle\Entity\OrderBookCredentials;

class DeliveryServiceValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $deliveryServices = [
            OrderBookCredentials::DELIVERY_SERVICE_DHL,
            OrderBookCredentials::DELIVERY_SERVICE_NP
        ];

        if( !in_array($value, $deliveryServices, TRUE) ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
