<?php
// src/AppBundle/EventListener/OrderSetter.php
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\Session;

use Doctrine\ORM\Event\LifecycleEventArgs;

use AppBundle\Entity\Chapter,
    AppBundle\Entity\Story;

class OrderSetter
{
    private $_session;

    public function __construct(Session $session)
    {
        $this->_session = $session;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $_manager = $args->getEntityManager();
        $entity   = $args->getEntity();

        if( $entity instanceof Chapter )
        {
            if( $this->_session->getFlashBag()->has('previous_max_order') ) {
                $maxOrder = $this->_session->getFlashBag()->get('previous_max_order')[0] + 1;
            } else {
                $maxOrder = $_manager->getRepository("AppBundle:Chapter")->findMaxChapterOrder($entity->getBook()) + 1;
            }

            $entity->setChapterOrder($maxOrder);

            $this->_session->getFlashBag()->add('previous_max_order', $maxOrder);
        }

        if( $entity instanceof Story )
        {
            $maxOrder = $_manager->getRepository("AppBundle:Story")->findMaxStoryOrder() + 1;

            $entity->setStoryOrder($maxOrder);
        }
    }
}