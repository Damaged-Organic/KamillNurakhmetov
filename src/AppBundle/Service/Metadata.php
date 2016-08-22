<?php
// src/AppBundle/Service/Metadata.php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

class Metadata
{
    protected $_manager;

    private $currentMetadata;

    private $previousRoute;
    private $currentRoute;

    public function __construct(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    /*
     * Get rid of "_default" routing suffix
     */
    private function formatRoute($route)
    {
        return str_replace("_default", "", $route);
    }

    public function setPreviousRoute($previousRoute)
    {
        if( $previousRoute )
            $this->previousRoute = $this->formatRoute($previousRoute);
    }

    public function getPreviousRoute()
    {
        return $this->previousRoute;
    }

    public function setCurrentRoute($currentRoute)
    {
        if( $currentRoute )
            $this->currentRoute = $this->formatRoute($currentRoute);
    }

    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }

    public function setCurrentMetadata()
    {
        if( $this->currentRoute )
            $this->currentMetadata = $this->_manager->getRepository('AppBundle:Metadata')
                ->findOneByRoute($this->currentRoute);
    }

    public function getCurrentMetadata()
    {
        return $this->currentMetadata;
    }
}
