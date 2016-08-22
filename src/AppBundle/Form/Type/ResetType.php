<?php
// src/AppBundle/Form/Type/ResetType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver;

class ResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reader', new ReaderType(ReaderType::TYPE_RESET), [
                'data_class' => 'AppBundle\Entity\Reader'
            ])
        ;
    }

    public function getName()
    {
        return 'reset';
    }
}