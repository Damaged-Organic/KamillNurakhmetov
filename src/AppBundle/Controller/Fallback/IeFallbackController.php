<?php
// src/AppBundle/Controller/Fallback/IeFallbackController.php
namespace AppBundle\Controller\Fallback;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class IeFallbackController
{
    private $_templating;

    public function setTemplating(EngineInterface $templating)
    {
        $this->_templating = $templating;
    }

    public function ieFallbackAction()
    {
        return $this->_templating->renderResponse('AppBundle:Fallback:ie.html.twig');
    }
}
