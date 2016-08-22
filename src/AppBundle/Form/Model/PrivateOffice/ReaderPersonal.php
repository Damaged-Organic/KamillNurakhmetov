<?php
// src/AppBundle/Form/Model/PrivateOffice/ReaderPersonal.php
namespace AppBundle\Form\Model\PrivateOffice;

use Symfony\Component\Validator\Constraints as Assert;

class ReaderPersonal
{
    /**
     * @Assert\NotBlank(message="reader.email.not_blank")
     * @Assert\Email(
     *      message="reader.email.valid",
     *      checkMX=true
     * )
     */
    protected $email;

    /**
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "reader.pseudonym.length.min",
     *      maxMessage = "reader.pseudonym.length.max"
     * )
     */
    protected $pseudonym;

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setPseudonym($pseudonym)
    {
        $this->pseudonym = $pseudonym;

        return $this;
    }

    public function getPseudonym()
    {
        return $this->pseudonym;
    }
}