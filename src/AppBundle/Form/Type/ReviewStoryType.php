<?php
// src/AppBundle/Form/Type/ReviewStoryType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReviewStoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('review', new ReviewType(), [
                'data_class' => 'AppBundle\Entity\Review'
            ])
            ->add("storyId", 'hidden', [
                'mapped' => FALSE
            ])
        ;
    }

    public function getName()
    {
        return 'review_story';
    }
}