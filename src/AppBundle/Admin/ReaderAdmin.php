<?php
// src/AppBundle/Admin/ReaderAdmin.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin,
    Sonata\AdminBundle\Datagrid\ListMapper,
    Sonata\AdminBundle\Datagrid\DatagridMapper,
    Sonata\AdminBundle\Form\FormMapper,
    Sonata\AdminBundle\Route\RouteCollection;

class ReaderAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove("create")
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add("id", "text", [
                'label' => "ID"
            ])
            ->addIdentifier("email", "text", [
                'label' => "Email пользователя"
            ])
            ->add("pseudonym", "text", [
                'label' => "Псевдоним пользователя"
            ])
            ->add("preferredCurrency", "text", [
                'label' => "Предпочитаемая валюта"
            ])
            ->add("isSubscribed", "boolean", [
                'label' => "Подписан"
            ])
            ->add("isActive", "boolean", [
                'label' => "Активен"
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add("email", "text", [
                'read_only' => TRUE,
                'disabled'  => TRUE,
                'label'     => "Email пользователя"
            ])
            ->add("pseudonym", "text", [
                'label' => "Псевдоним пользователя"
            ])
            ->add("preferredCurrency", "text", [
                'label' => "Предпочитаемая валюта"
            ])
            ->add("isSubscribed", "checkbox", [
                'read_only' => TRUE,
                'disabled'  => TRUE,
                'label'     => "Подписан"
            ])
            ->add("subscriptionEnd", "datetime", [
                'read_only' => TRUE,
                'disabled'  => TRUE,
                'widget'    => "single_text",
                'label'     => "Срок действия подписки"
            ])
            ->add("isActive", "checkbox", [
                'label' => "Активен"
            ])
        ;
    }

    public function configure()
    {
        parent::configure();

        $this->datagridValues['_sort_by']    = 'email';
        $this->datagridValues['_sort_order'] = 'ASC';
    }
}