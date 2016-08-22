<?php
// src/AppBundle/Controller/Common/CommonController.php
namespace AppBundle\Controller\Common;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CommonController extends Controller
{
    public function paginationBarAction($sortingParameter = NULL)
    {
        $paginationBar = $this->get('app.pagination_bar')->getPaginationBar();

        return $this->render('AppBundle:Common:paginationBar.html.twig', [
            'paginationBar'    => $paginationBar,
            'sortingParameter' => $sortingParameter
        ]);
    }
}