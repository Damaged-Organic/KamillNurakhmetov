<?php
// src/AppBundle/Form/Type/PrivateOffice/ReaderPersonalType.php
namespace AppBundle\Form\Type\PrivateOffice;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver;

class ReaderPersonalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', [
                'required' => FALSE,
                'label'    => 'reader.email.label'
            ])
            ->add('pseudonym', 'text', [
                'required' => FALSE,
                'label'    => 'reader.pseudonym.label'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Form\Model\PrivateOffice\ReaderPersonal',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'reader_personal';
    }
}