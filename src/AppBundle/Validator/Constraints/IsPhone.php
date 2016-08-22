<?php
// src/AppBundle/Validator/Constraints/IsPhone.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsPhone extends Constraint
{
   public $message = "common.phone.valid";
}
