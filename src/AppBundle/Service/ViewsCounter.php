<?php
// src/AppBundle/Service/ViewsCounter.php
namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\Session\Session;

class ViewsCounter
{
    private $_session;

    public function __construct(Session $session)
    {
        $this->_session = $session;
    }

    public function isAlreadyCounted($entity, $key)
    {
        if( $this->_session->has("{$entity}_{$key}") )
            return TRUE;

        return $this->_session->set("{$entity}_{$key}", TRUE);
    }
}
