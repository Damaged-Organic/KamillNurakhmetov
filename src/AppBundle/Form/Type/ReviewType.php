<?php
// src/AppBundle/Form/Type/ReviewType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("text", 'textarea', [
                'attr' => [
                    'placeholder' => "review.text.placeholder"
                ]
            ])
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\Review',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return "review";
    }
}