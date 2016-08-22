<?php
// src/MenuBundle/Menu/MenuBuilder.php
namespace MenuBundle\Menu;

use Doctrine\ORM\EntityManager;

use Knp\Menu\FactoryInterface;

use Symfony\Component\HttpFoundation\RequestStack;

class MenuBuilder
{
    private $_factory;
    private $_manager;

    public function __construct(FactoryInterface $factory, EntityManager $manager)
    {
        $this->_factory = $factory;
        $this->_manager = $manager;
    }

    public function createMainMenu(RequestStack $requestStack)
    {
        $menu = $this->_factory->createItem('root');

        $items = $this->_manager->getRepository('MenuBundle:Menu')->findAll();

        $currentRoute = $requestStack->getMasterRequest()->attributes->get('_route');

        $westItems = array_slice($items, 0, 2);
        $eastItems = array_slice($items, 2, 2);

        foreach($westItems as $item)
        {
            $menu
                ->addChild($item->getTitle(), ['route' => $item->getRoute()])
                ->setAttribute('class', 'touch-hover')
            ;

            if( $item->getRoute() === str_replace("_default", "", $currentRoute) )
                $menu[$item->getTitle()]->setCurrent(TRUE);
        }

        // $menu
        //     ->addChild(NULL, [
        //         'route' => 'index', 'routeParameters' => [
        //             '_locale' => $requestStack->getMasterRequest()->attributes->get('_locale')
        //         ]
        //     ])
        //     ->setAttribute('class', 'logo-holder')
        //     ->setLinkAttribute('class', 'logo')
        // ;

        foreach($eastItems as $item)
        {
            $menu
                ->addChild($item->getTitle(), ['route' => $item->getRoute()])
                ->setAttribute('class', 'touch-hover')
            ;

            if( $item->getRoute() === str_replace("_default", "", $currentRoute) )
                $menu[$item->getTitle()]->setCurrent(TRUE);
        }

        return $menu;
    }
}
