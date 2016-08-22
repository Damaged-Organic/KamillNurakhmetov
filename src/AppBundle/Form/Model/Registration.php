<?php
// src/AppBundle/Form/Model/Registration.php
namespace AppBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use AppBundle\Entity\Reader;

class Registration
{
    /**
     * @Assert\Type(type="AppBundle\Entity\Reader")
     * @Assert\Valid()
     */
    protected $reader;

    public function setReader(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function getReader()
    {
        return $this->reader;
    }
}