<?php
// src/AppBundle/Admin/BookAdmin.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin,
    Sonata\AdminBundle\Datagrid\ListMapper,
    Sonata\AdminBundle\Datagrid\DatagridMapper,
    Sonata\AdminBundle\Form\FormMapper;

use AppBundle\Entity\Book;

class BookAdmin extends Admin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add("id", "number", [
                "label" => "ID"
            ])
            ->addIdentifier("title", "text", [
                "label" => "Название книги"
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
            ->add("views", "number", [
                "label" => "Просмотры"
            ])
            ->add("isAvailable", 'boolean', [
                "label" => 'В наличии'
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        if( $book = $this->getSubject() )
        {
            $imageRequired = ( $book->getCoverName() ) ? FALSE : TRUE;

            $imageHelp = ( $imagePath = $book->getCoverPath() )
                ? '<img src="'.$imagePath.'" class="admin-preview" />'
                : FALSE;
        } else {
            $imageRequired = TRUE;
            $imageHelp     = FALSE;
        }

        $formMapper
            ->with("Книга")
                ->add("title", "text", [
                    "label" => "Название книги"
                ])
                ->add("coverFile", "vich_file", [
                    'label'         => "Обложка книги",
                    'required'      => $imageRequired,
                    'allow_delete'  => FALSE,
                    'download_link' => FALSE,
                    'help'          => $imageHelp
                ])
                ->add("year", "number", [
                    "label" => "Год издания"
                ])
                ->add("pages", "number", [
                    "label" => "Количество страниц"
                ])
                ->add("description", "textarea", [
                    "label" => "Аннотация"
                ])
                ->add("views", "number", [
                    'required'  => FALSE,
                    "label"     => "Просмотры",
                    //"read_only" => TRUE
                ])
                ->add("hasPaper", 'checkbox', [
                    "required" => FALSE,
                    "label"    => 'Есть издательская версия'
                ])
                ->add("isAvailable", 'checkbox', [
                    "required" => FALSE,
                    "label"    => 'В наличии'
                ])
            ->end()
            ->with("Цены электронной версии")
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
            ->end()
            ->with("Цены бумажной версии")
                ->add("pricePaperUah", "number", [
                    "label"     => "Цена (UAH)",
                    "precision" => 2
                ])
                ->add("pricePaperUsd", "number", [
                    "label"     => "Цена (USD)",
                    "precision" => 2
                ])
                ->add("pricePaperRub", "number", [
                    "label"     => "Цена (RUB)",
                    "precision" => 2
                ])
            ->end()
            ->with("Локализации")
                ->add("translations", "a2lix_translations_gedmo", [
                    "label"              => "Управление локализациями",
                    "translatable_class" => 'AppBundle\Entity\Book',
                    "required"           => FALSE,
                    "fields"             => [
                        "title" => [
                            "locale_options" => [
                                "en" => [
                                    "label" => "Book title"
                                ]
                            ]
                        ],
                        "description" => [
                            "locale_options" => [
                                "en" => [
                                    "label" => "Annotation"
                                ]
                            ]
                        ]
                    ]
                ])
            ->end()
            ->with("Главы")
                ->add("chapters", "sonata_type_collection", [
                    'required'     => FALSE,
                    'by_reference' => FALSE,
                ], [
                    'multiple' => TRUE,
                    'edit'     => 'inline',
                    'inline'   => 'table'
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

    public function getFormTheme()
    {
        return array_merge(
            parent::getFormTheme(),
            ['ApplicationSonataUserBundle:Admin/Form:link_field.html.twig']
        );
    }

    public function postUpdate($book)
    {
        if( !($book instanceof Book) )
            return;

        $this->getConfigurationPool()->getContainer()->get('session')->getFlashBag()->get('previous_max_order');
    }
}
