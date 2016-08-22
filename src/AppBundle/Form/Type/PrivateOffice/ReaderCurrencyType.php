<?php
// src/AppBundle/Form/Type/PrivateOffice/ReaderCurrencyType.php
namespace AppBundle\Form\Type\PrivateOffice;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Form\Model\PrivateOffice\ReaderCurrency;

class ReaderCurrencyType extends AbstractType
{
    private $preferredCurrency;

    public function __construct($preferredCurrency = NULL)
    {
        $this->preferredCurrency = $preferredCurrency;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currencyList = ReaderCurrency::getCurrencyList();

        $builder
            ->add('preferredCurrency', 'choice', [
                'choices'         => array_combine(
                    $currencyList, ["UAH", "USD", "RUB"]
                ),
                'expanded'        => TRUE,
                'multiple'        => FALSE,
                'data'            => ( $this->preferredCurrency ) ?: $currencyList[0],
                'invalid_message' => "private_office.preferred_currency.invalid_message",
                'label'           => 'private_office.preferred_currency.label'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Form\Model\PrivateOffice\ReaderCurrency',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'reader_currency';
    }
}