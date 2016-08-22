<?php
// src/AppBundle/Form/Model/Reset.php
namespace AppBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use AppBundle\Entity\Reader;

class Reset
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