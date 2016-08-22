<?php
// src/AppBundle/Admin/ReviewAdmin.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin,
    Sonata\AdminBundle\Datagrid\ListMapper,
    Sonata\AdminBundle\Datagrid\DatagridMapper,
    Sonata\AdminBundle\Form\FormMapper,
    Sonata\AdminBundle\Route\RouteCollection;

class ReviewAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add("id", "number", [
                "label" => "ID"
            ])
            ->addIdentifier("publishedAt", "datetime", [
                "label" => "Дата публикации",
                'format'    => "d-m-y H:i",
            ])
            ->add("book.title", "text", [
                "label" => "Книга"
            ])
            ->add("story.title", "text", [
                "label" => "Рассказ"
            ])
            ->add("authorCredentials", "text", [
                "label" => "Автор рецензии"
            ])
            ->add("isActive", "boolean", [
                "label" => "Отбражается на сайте"
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $route = $this->getRequest()->attributes->get("_route");

        $formMapper
            ->add("publishedAt", "datetime", [
                'widget'      => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'label'       => "Дата публикации"
            ])
        ;

        if( $route == "admin_app_book_edit" || $route == "admin_app_book_create" ) {
            $formMapper
                ->add("book.title", "text", [
                    'read_only' => TRUE,
                    'disabled'  => TRUE,
                    "label"     => "Произведение"
                ])
            ;
        } elseif( $route == "admin_app_story_edit" || $route == "admin_app_story_create" ) {
            $formMapper
                ->add("story.title", "text", [
                    'read_only' => TRUE,
                    'disabled'  => TRUE,
                    "label"     => "Произведение"
                ])
            ;
        } else {
            $entity = $this->getSubject();

            if( $entity ) {
                if( $entity->getBook() ) {
                    $formMapper
                        ->add("book.title", "text", [
                            'read_only' => TRUE,
                            'disabled'  => TRUE,
                            "label"     => "Книга"
                        ])
                    ;
                } elseif( $entity->getStory() ) {
                    $formMapper
                        ->add("story.title", "text", [
                            'read_only' => TRUE,
                            'disabled'  => TRUE,
                            "label"     => "Рассказ"
                        ])
                    ;
                }
            }
        }

        $formMapper
            ->add("authorCredentials", "text", [
                "label" => "Автор рецензии"
            ])
            ->add("text", "textarea", [
                "label" => "Рецензия"
            ])
            ->add("isActive", "checkbox", [
                'required' => FALSE,
                "label"    => "Отображается на сайте"
            ])
        ;
    }

    public function configure()
    {
        parent::configure();

        $this->datagridValues['_sort_by']    = 'publishedAt';
        $this->datagridValues['_sort_order'] = 'DESC';
    }
}
