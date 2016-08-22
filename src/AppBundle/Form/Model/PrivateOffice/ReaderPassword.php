<?php
// src/AppBundle/Form/Model/PrivateOffice/ReaderPassword.php
namespace AppBundle\Form\Model\PrivateOffice;

use Symfony\Component\Validator\Constraints as Assert;

class ReaderPassword
{
    /**
     * @Assert\NotBlank(
     *      message="private_office.old_password.not_blank"
     * )
     */
    protected $oldPassword;

    /**
     * @Assert\NotBlank(
     *      message="private_office.new_password.not_blank"
     * )
     */
    protected $newPassword;

    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getNewPassword()
    {
        return $this->newPassword;
    }
}