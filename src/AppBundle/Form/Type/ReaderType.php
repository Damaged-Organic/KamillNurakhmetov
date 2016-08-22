<?php
// src/AppBundle/Form/Type/ReaderType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Entity\Reader;

class ReaderType extends AbstractType
{
    const TYPE_REGISTRATION = "type_registration";
    const TYPE_RESET        = "type_reset";

    private $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch($this->type)
        {
            case self::TYPE_REGISTRATION:
                $currencyList = Reader::getCurrencyList();

                $builder
                    ->add('email', 'email', [
                        'label' => 'reader.email.label'
                    ])
                    ->add('password', 'repeated', [
                        'first_name'      => 'password',
                        'second_name'     => 'confirm',
                        'type'            => 'password',
                        'invalid_message' => 'reader.password.invalid_message',
                        'first_options'   => [
                            'label' => 'reader.password.label'
                        ],
                        'second_options'  => [
                            'label' => 'reader.password_repeated.label'
                        ]
                    ])
                    ->add('preferredCurrency', 'choice', [
                        'choices'         => array_combine(
                            $currencyList, ["UAH", "USD", "RUB"]
                        ),
                        'expanded'        => TRUE,
                        'multiple'        => FALSE,
                        'data'            => $currencyList[0],
                        'invalid_message' => "reader.preferred_currency.invalid_message",
                        'label'           => 'reader.preferred_currency.label'
                    ])
                ;
            break;

            case self::TYPE_RESET:
                $builder
                    ->add('email', 'email', [
                        'label' => 'reader.email.label'
                    ])
                ;
            break;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\Reader',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'reader';
    }
}
