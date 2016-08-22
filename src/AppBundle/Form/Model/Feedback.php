<?php
// src/AppBundle/Form/Model/Feedback.php
namespace AppBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Feedback
{
    /**
     * @Assert\Length(
     *      min = 2,
     *      max = 200,
     *      minMessage = "feedback.name.length.min",
     *      maxMessage = "feedback.name.length.max"
     * )
     */
    protected $name;

    /**
     * @Assert\NotBlank(
     *      message="feedback.email.not_blank"
     * )
     * @Assert\Email(
     *      message="feedback.email.valid",
     *      checkMX=true
     * )
     */
    protected $email;

    /**
     * @Assert\Length(
     *      min = 2,
     *      max = 200,
     *      minMessage = "feedback.subject.length.min",
     *      maxMessage = "feedback.subject.length.max"
     * )
     */
    protected $subject;

    /**
     * @Assert\NotBlank(
     *      message = "feedback.message.not_blank"
     * )
     * @Assert\Length(
     *      min = 5,
     *      max = 1500,
     *      minMessage = "feedback.message.length.min",
     *      maxMessage = "feedback.message.length.max"
     * )
     */
    protected $message;

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }
}