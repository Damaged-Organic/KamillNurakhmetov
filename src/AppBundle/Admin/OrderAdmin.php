<?php
// src/AppBundle/Admin/OrderAdmin.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin,
    Sonata\AdminBundle\Datagrid\ListMapper,
    Sonata\AdminBundle\Datagrid\DatagridMapper,
    Sonata\AdminBundle\Form\FormMapper,
    Sonata\AdminBundle\Route\RouteCollection;

class OrderAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove("create")
            ->remove("delete")
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier("orderId", "text", [
                'label' => "Номер заказа"
            ])
            ->add("OrderDateTime", "datetime", [
                'label'  => "Дата и время заказа",
                'format' => "d-m-y H:i"
            ])
            ->add('reader.email', 'text', [
                'label' => "Заказчик"
            ])
            ->add('book.title', 'text', [
                'label' => "Книга"
            ])
            ->add('subscription.name', 'text', [
                'label' => "Подписка"
            ])
            ->add('itemDescription', 'text', [
                'label' => "Описание покупки"
            ])
            ->add("itemPrice", "number", [
                "label"     => "Цена",
                "precision" => 2
            ])
            ->add('itemCurrency', 'text', [
                'label' => "Валюта"
            ])
            ->add('orderStatus', 'text', [
                'label' => "Статус заказа"
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add("orderId", "text", [
                'read_only' => TRUE,
                'disabled'  => TRUE,
                'label'     => "Номер заказа"
            ])
            ->add("orderDateTime", "datetime", [
                'read_only' => TRUE,
                'disabled'  => TRUE,
                'label'     => "Дата и время заказа",
                'format'    => "dd-MM-yyyy HH:mm",
                'widget'    => 'single_text',
            ])
            ->add('reader.email', 'text', [
                'read_only' => TRUE,
                'disabled'  => TRUE,
                'label'     => "Заказчик"
            ])
            ->add('book', 'entity', [
                'required'  => FALSE,
                'read_only' => TRUE,
                'disabled'  => TRUE,
                'class'     => 'AppBundle\Entity\Book',
                'label'     => "Книга"
            ])
            ->add('subscription', 'entity', [
                'required'  => FALSE,
                'read_only' => TRUE,
                'disabled'  => TRUE,
                'class'     => 'AppBundle\Entity\Subscription',
                'label'     => "Подписка"
            ])
            ->add('itemDescription', 'text', [
                'required'  => FALSE,
                'read_only' => TRUE,
                'disabled'  => TRUE,
                'label'     => "Описание покупки"
            ])
            ->add("itemPrice", "number", [
                'read_only' => TRUE,
                'disabled'  => TRUE,
                "label"     => "Цена",
                "precision" => 2
            ])
            ->add('itemCurrency', 'text', [
                'read_only' => TRUE,
                'disabled'  => TRUE,
                'label'     => "Валюта"
            ])
            ->add('orderStatus', 'text', [
                'read_only' => TRUE,
                'disabled'  => TRUE,
                'label'     => "Статус заказа"
            ])
        ;
    }

    public function configure()
    {
        parent::configure();

        $this->datagridValues['_sort_by']    = 'orderDateTime';
        $this->datagridValues['_sort_order'] = 'DESC';
    }
}
