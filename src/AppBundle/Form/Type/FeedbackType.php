<?php
// src/AppBundle/Form/Type/FeedbackType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name", 'text', [
                'label'    => "feedback.name.label",
                'required' => FALSE
            ])
            ->add("email", 'email', [
                'label' => "feedback.email.label"
            ])
            ->add("subject", 'text', [
                'label'    => "feedback.subject.label",
                'required' => FALSE
            ])
            ->add("message", 'textarea', [
                'attr' => [
                    'placeholder' => "feedback.message.placeholder"
                ]
            ])
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Form\Model\Feedback',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return "feedback";
    }
}