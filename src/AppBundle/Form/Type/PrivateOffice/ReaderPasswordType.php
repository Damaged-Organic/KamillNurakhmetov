<?php
// src/AppBundle/Form/Type/PrivateOffice/ReaderPasswordType.php
namespace AppBundle\Form\Type\PrivateOffice;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver;

class ReaderPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', 'password', [
                'label' => 'private_office.old_password.label'
            ])
            ->add('newPassword', 'repeated', [
                'first_name'      => 'password',
                'second_name'     => 'confirm',
                'type'            => 'password',
                'invalid_message' => 'private_office.new_password.invalid_message',
                'first_options'   => [
                    'label' => 'private_office.new_password.label'
                ],
                'second_options'  => [
                    'label' => 'private_office.new_password_repeated.label'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Form\Model\PrivateOffice\ReaderPassword',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'reader_password';
    }
}