<?php
// src/AppBundle/Admin/SubscriptionAdmin.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin,
    Sonata\AdminBundle\Datagrid\ListMapper,
    Sonata\AdminBundle\Datagrid\DatagridMapper,
    Sonata\AdminBundle\Form\FormMapper,
    Sonata\AdminBundle\Route\RouteCollection;

class SubscriptionAdmin extends Admin
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
            ->addIdentifier("name", "text", [
                "label" => "Период"
            ])
            ->add("priceUah", "number", [
                "label"     => "Цена (UAH)",
                "precision" => 2
            ])
            ->add("priceUsd", "number", [
                "label"     => "Цена (USD)",
                "precision" => 2
            ])
            ->add("priceRub", "number", [
                "label"     => "Цена (RUB)",
                "precision" => 2
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add("name", "text", [
                'disabled'  => TRUE,
                'read_only' => TRUE,
                'label'     => "Период"
            ])
        ->end()
        ->with("Цены")
            ->add("priceUah", "number", [
                "label"     => "Цена (UAH)",
                "precision" => 2
            ])
            ->add("priceUsd", "number", [
                "label"     => "Цена (USD)",
                "precision" => 2
            ])
            ->add("priceRub", "number", [
                "label"     => "Цена (RUB)",
                "precision" => 2
            ])
        ;
    }
}