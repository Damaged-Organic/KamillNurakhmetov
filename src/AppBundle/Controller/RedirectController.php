<?php
// src/AppBundle/Controller/RedirectController.php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class RedirectController extends Controller
{
    public function redirectToDefaultLocaleAction(Request $request)
    {
        $path = $request->getRequestUri();

        $uri = str_replace(
            $this->container->getParameter('domain'),
            $this->container->getParameter('locale').'.'.$this->container->getParameter('domain'),
            $request->getSchemeAndHttpHost()
        );

        return $this->redirect($uri.$path, 307);
    }
}
