<?php
// src/AppBundle/Admin/AuthorAdmin.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin,
    Sonata\AdminBundle\Datagrid\ListMapper,
    Sonata\AdminBundle\Datagrid\DatagridMapper,
    Sonata\AdminBundle\Form\FormMapper,
    Sonata\AdminBundle\Route\RouteCollection;

class AuthorAdmin extends Admin
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
            ->addIdentifier("title", "text", [
                "label" => "Заголовок страницы"
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add("title", "text", [
                "label" => "Заголовок страницы"
            ])
            ->add('text', 'sonata_formatter_type', [
                'label'            => "Биография",
                'required'         => TRUE,
                'event_dispatcher' => $formMapper->getFormBuilder()->getEventDispatcher(),
                'format_field'     => 'textFormatter',
                'source_field'     => 'rawText',
                'ckeditor_context' => 'base_config',
                'listener'         => TRUE,
                'target_field'     => 'text'
            ])
            ->add('worksNumber', 'number', [
                "label" => "Количество произведений"
            ])
            ->add('readersNumber', 'number', [
                "label" => "Количество читателей"
            ])
            ->end()
            ->with("Локализации")
                ->add("translations", "a2lix_translations_gedmo", [
                    "label"              => "Управление локализациями",
                    "translatable_class" => 'AppBundle\Entity\Author',
                    "required"           => FALSE,
                    "fields"             => [
                        "title" => [
                            "locale_options" => [
                                "en" => [
                                    "label" => "Page title"
                                ]
                            ]
                        ],
                        "text" => [
                            "locale_options" => [
                                "en" => [
                                    "label"       => "Biography",
                                    'field_type'  => 'ckeditor',
                                    'config_name' => 'base_config'
                                ]
                            ]
                        ]
                    ]
                ])
        ;
    }
}