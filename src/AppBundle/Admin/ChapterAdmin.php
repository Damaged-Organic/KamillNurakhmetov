<?php
// src/AppBundle/Admin/ChapterAdmin.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin,
    Sonata\AdminBundle\Datagrid\ListMapper,
    Sonata\AdminBundle\Datagrid\DatagridMapper,
    Sonata\AdminBundle\Form\FormMapper,
    Sonata\AdminBundle\Route\RouteCollection;

class ChapterAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('list')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier("title", "text", [
                "label" => "Название главы"
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $route = $this->getRequest()->attributes->get("_route");

        if( $route == "admin_app_chapter_edit" || $route == "admin_app_chapter_create" )
        {
            $bookTitle = ( $this->getSubject()->getBook() ) ? $this->getSubject()->getBook()->getTitle() : NULL;

            $formMapper
                ->with("Глава книги \"{$bookTitle}\"")
                    ->add("chapterOrder", "number", [
                        "label" => "Номер главы",
                    ])
                    ->add("title", "text", [
                        "label" => "Название главы",
                    ])
                    ->add('text', 'sonata_formatter_type', [
                        'label'            => "Содержание главы",
                        'required'         => FALSE,
                        'event_dispatcher' => $formMapper->getFormBuilder()->getEventDispatcher(),
                        'format_field'     => 'textFormatter',
                        'source_field'     => 'rawText',
                        'ckeditor_context' => 'base_config',
                        'listener'         => TRUE,
                        'target_field'     => 'text'
                    ])
                ->end()
                ->with("Локализации")
                    ->add("translations", "a2lix_translations_gedmo", [
                        "label"              => "Управление локализациями",
                        "translatable_class" => 'AppBundle\Entity\Chapter',
                        "required"           => FALSE,
                        "fields"             => [
                            "title" => [
                                "locale_options" => [
                                    "en" => [
                                        "label" => "Chapter title"
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
            ;
        } else {
            if( $this->getSubject() && $this->getSubject()->getId() )
            {
                $formMapper
                    ->add("chapterOrder", "number", [
                        "label" => "Номер главы"
                    ])
                    ->add("title", "text", [
                        "label" => "Название главы"
                    ])
                    ->add("link", "link_field", [
                        'mapped' => FALSE,
                        'data'   => $this->getSubject()->getId()
                    ])
                ;
            } else {
                $formMapper
                    ->add("chapterOrder", "number", [
                        "label"     => "Номер главы",
                        "read_only" => TRUE
                    ])
                    ->add("title", "text", [
                        "label" => "Название главы"
                    ])
                ;
            }
        }
    }

    public function getFormTheme()
    {
        return array_merge(
            parent::getFormTheme(),
            [
                'ApplicationSonataUserBundle:Admin/Form:link_field.html.twig'
            ]
        );
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'ApplicationSonataUserBundle:Admin/Form:edit.html.twig';
            break;

            default:
                return parent::getTemplate($name);
            break;
        }
    }

    public function getSubject()
    {
        if( $this->subject === NULL && $this->request )
            $this->subject = FALSE;

        return $this->subject;
    }
}