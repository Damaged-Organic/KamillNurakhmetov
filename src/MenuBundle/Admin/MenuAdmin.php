<?php
// src/MenuBundle/Admin/MenuAdmin.php
namespace MenuBundle\Admin;

use Symfony\Component\Routing\Router;

use Sonata\AdminBundle\Admin\Admin,
    Sonata\AdminBundle\Datagrid\ListMapper,
    Sonata\AdminBundle\Datagrid\DatagridMapper,
    Sonata\AdminBundle\Form\FormMapper,
    Sonata\AdminBundle\Show\ShowMapper;

class MenuAdmin extends Admin
{
    protected $_router;

    protected $baseRouteName    = 'menu';
    protected $baseRoutePattern = 'menu';

    public function setRouter(Router $router)
    {
        $this->_router = $router;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        //TODO: Kludgy because of shallow "_default" and unmapped help-only parameter
        $path = ( ($menuItem = $this->getSubject()) && ($route = $menuItem->getRoute()) )
            ? $this->_router->generate($route."_default")
            : NULL;

        $formMapper
            ->add('title', 'text', [
                'label' => "Название пункта"
            ])
            ->add('route', 'text', [
                'label'    => "Роутер (Системная настройка)",
                'disabled' => TRUE
            ])
            ->add('path', 'text', [
                'label'    => "Путь (Системная настройка)",
                'mapped'   => FALSE,
                'disabled' => TRUE,
                'data'     => $path
            ])
            ->add('translations', 'a2lix_translations_gedmo', [
                'label' => "Локализации",
                'translatable_class' => 'MenuBundle\Entity\Menu',
                'fields' => [
                    'title' => [
                        'locale_options' => [
                            'ua' => [
                                'label' => "Назва пункту"
                            ],
                            'en' => [
                                'label' => 'Item title'
                            ]
                        ]
                    ]
                ]
            ])
        ;
    }

    public function configureShowField(ShowMapper $showMapper){
        $showMapper
            ->add('title', 'text', [
                'label' => "Название пункта"
            ])
            ->add('id', 'integer', [
                'label' => "Порядковый номер"
            ])
            ->add('route',  'text', [
                'label' => "Путь"
            ])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title', 'text', [
                'label' => "Название пункта"
            ])
            ->add('route',  'text', [
                'label' => "Путь"
            ])
            ->add('id', 'integer', [
                'label' => "Порядковый номер"
            ])
        ;
    }
}
