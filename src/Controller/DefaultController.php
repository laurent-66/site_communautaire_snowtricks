<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homePage", methods={"get"})
     */

    public function home(){
        return $this->render('core/home.html.twig', ['name'=> 'snowtricks']);
    }

}