<?php
// src/AppBundle/Admin/StoryAdmin.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin,
    Sonata\AdminBundle\Datagrid\ListMapper,
    Sonata\AdminBundle\Datagrid\DatagridMapper,
    Sonata\AdminBundle\Form\FormMapper;

use AppBundle\Entity\Story;

class StoryAdmin extends Admin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', 'number', [
                'label' => "ID"
            ])
            ->add('storyOrder', 'number', [
                'label' => "Порядок отображения"
            ])
            ->addIdentifier('title', 'text', [
                'label' => "Название рассказа"
            ])
            ->add('storyCategory', NULL, [
                'label' => "Жанр"
            ])
            ->add('views', 'number', [
                'label' => "Просмотры"
            ])
            ->add('isFreeForAll', 'boolean', [
                'label' => "Доступен для всех"
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with("Рассказ")
        ;

        if( $this->getSubject()->getId() )
        {
            $formMapper
                ->add('storyOrder', 'number', [
                    'label' => "Порядок отображения"
                ]);
        }

        $formMapper
            ->add("publicationDate", "sonata_type_date_picker", [
                'label'  => "Дата публикации",
                'format' => 'dd-MM-yyyy'
            ])
            ->add("title", "text", [
                "label" => "Название рассказа"
            ])
            ->add('text', 'sonata_formatter_type', [
                'label'            => "Содержание рассказа",
                'required'         => TRUE,
                'event_dispatcher' => $formMapper->getFormBuilder()->getEventDispatcher(),
                'format_field'     => 'textFormatter',
                'source_field'     => 'rawText',
                'ckeditor_context' => 'base_config',
                'listener'         => TRUE,
                'target_field'     => 'text'
            ])
            ->add('storyCategory', 'entity', [
                'class' => 'AppBundle\Entity\StoryCategory',
                'label' => "Категория рассказа"
            ])
            ->add("views", "number", [
                'required'  => FALSE,
                "label"     => "Просмотры",
                //"read_only" => TRUE
            ])
            ->add('isFreeForAll', 'checkbox', [
                'required' => FALSE,
                'label'    => "Доступен для всех"
            ])
        ->end()
        ->with("Локализации")
            ->add("translations", "a2lix_translations_gedmo", [
                "label"              => "Управление локализациями",
                "translatable_class" => 'AppBundle\Entity\Story',
                "required"           => FALSE,
                "fields"             => [
                    "title" => [
                        "locale_options" => [
                            "en" => [
                                "label" => "Story title"
                            ]
                        ]
                    ],
                    "text" => [
                        "locale_options" => [
                            "en" => [
                                "label"       => "Content",
                                'field_type'  => 'ckeditor',
                                'config_name' => 'base_config'
                            ]
                        ]
                    ]
                ]
            ])
        ->end()
        ->with("Рецензии")
            ->add("reviews", "sonata_type_collection", [
                'required'     => FALSE,
                'by_reference' => FALSE
            ], [
                'multiple' => TRUE,
                'edit'     => 'inline',
                'inline'   => 'table'
            ])
        ;
    }
}
