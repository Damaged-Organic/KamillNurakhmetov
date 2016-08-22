<?php
// src/AppBundle/EventListener/PageInitListener.php
namespace AppBundle\EventListener;

use AppBundle\Service\Payment\LiqPayProcessing;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Event\FilterControllerEvent;

use AppBundle\Controller\Contract\PageInitInterface,
    AppBundle\Controller\Contract\PageCleanupInterface,
    AppBundle\Service\Metadata;

class PageInitListener
{
    private $_request;
    private $_metadata;
    private $_liqPayProcessing;

    public function __construct(Request $request, Metadata $metadata, LiqPayProcessing $liqPayProcessing)
    {
        $this->_request          = $request;
        $this->_metadata         = $metadata;
        $this->_liqPayProcessing = $liqPayProcessing;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if( !$event->getRequestType() )
            return;

        $controller = $event->getController();

        $this->_metadata->setCurrentRoute($this->_request->get('_route'));

        if( $controller[0] instanceof PageInitInterface )
        {
            if( !$this->_request->isXmlHttpRequest() )
                $this->_metadata->setCurrentMetadata();
        }

        if( $controller[0] instanceof PageCleanupInterface )
        {
            if( !$this->_request->isXmlHttpRequest() )
                $this->_liqPayProcessing->resetExpiredSubscriptions();
        }
    }
}