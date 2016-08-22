<?php
// src/AppBundle/Form/Type/Order/OrderBookCredentialsType.php
namespace AppBundle\Form\Type\Order;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Entity\OrderBookCredentials;

class OrderBookCredentialsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $deliveryServices = [
            OrderBookCredentials::DELIVERY_SERVICE_DHL => OrderBookCredentials::DELIVERY_SERVICE_DHL,
            OrderBookCredentials::DELIVERY_SERVICE_NP  => OrderBookCredentials::DELIVERY_SERVICE_NP,
        ];

        $builder
            ->add('pseudonym', 'text', [
                'label' => 'reader.pseudonym.label_small'
            ])
            ->add('email', 'email', [
                'label' => 'reader.email.label'
            ])
            ->add('phone', 'text', [
                'required' => FALSE,
                'label'    => 'common.phone.label'
            ])
            ->add('deliveryService', 'choice', [
                'label'           => 'reader.delivery.service.label',
                'choices'         => $deliveryServices,
                'expanded'        => TRUE,
                'multiple'        => FALSE,
                'invalid_message' => "reader.delivery.service.valid",
            ])
            ->add('deliveryCity', 'text', [
                'label' => 'reader.delivery.city.label'
            ])
            ->add('deliveryOffice', 'text', [
                'required' => FALSE,
                'label'    => 'reader.delivery.office.label'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\OrderBookCredentials',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'order_book_credentials';
    }
}
