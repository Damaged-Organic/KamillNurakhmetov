<?php
// src/Application/Sonata/UserBundle/Form/Type/LinkFieldType.php
namespace Application\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class LinkFieldType extends AbstractType
{
    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'link_field';
    }
}