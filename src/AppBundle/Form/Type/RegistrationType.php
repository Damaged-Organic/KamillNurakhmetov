<?php
// src/AppBundle/Form/Type/RegistrationType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reader', new ReaderType(ReaderType::TYPE_REGISTRATION), [
                'data_class' => 'AppBundle\Entity\Reader'
            ])
        ;
    }

    public function getName()
    {
        return 'registration';
    }
}