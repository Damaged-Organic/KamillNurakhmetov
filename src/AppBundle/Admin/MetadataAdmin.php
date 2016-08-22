<?php
// src/AppBundle/Admin/MetadataAdmin.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin,
    Sonata\AdminBundle\Datagrid\ListMapper,
    Sonata\AdminBundle\Datagrid\DatagridMapper,
    Sonata\AdminBundle\Form\FormMapper,
    Sonata\AdminBundle\Route\RouteCollection;

class MetadataAdmin extends Admin
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
            ->add("route", "text", [
                "label"    => "Роутер (Системная настройка)"
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add("title", "text", [
                "label" => "Заголовок страницы"
            ])
            ->add("route", "text", [
                "label"    => "Роутер (Системная настройка)",
                "disabled" => TRUE
            ])
            ->add("description", "textarea", [
                "label"    => "Описание страницы",
                "required" => FALSE
            ])
            ->add('text', 'sonata_formatter_type', [
                'label'            => "Дополнительный текст",
                'required'         => FALSE,
                'event_dispatcher' => $formMapper->getFormBuilder()->getEventDispatcher(),
                'format_field'     => 'textFormatter',
                'source_field'     => 'rawText',
                'ckeditor_context' => 'base_config',
                'listener'         => TRUE,
                'target_field'     => 'text'
            ])
            ->add("robots", "text", [
                "label"    => "Метаданные для поисковых ботов",
                "required" => FALSE
            ])
            ->end()
            ->with("Локализации")
                ->add("translations", "a2lix_translations_gedmo", [
                    "label"              => "Управление локализациями",
                    "translatable_class" => 'AppBundle\Entity\Metadata',
                    "required"           => FALSE,
                    "fields"             => [
                        "title" => [
                            "locale_options" => [
                                "en" => [
                                    "label" => "Page title"
                                ]
                            ]
                        ],
                        "description" => [
                            "locale_options" => [
                                "en" => [
                                    "label" => "Page description"
                                ]
                            ],
                            "required" => FALSE
                        ]
                    ]
                ])
        ;
    }
}
