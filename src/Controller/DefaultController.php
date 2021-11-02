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

    /**
     * @Route("/trick", name="trickPage", methods={"get"})
     */

    public function trick(){
        return $this->render('core/trick.html.twig');
    }

    /**
     * @Route("/trick/edit", name="trickEditPage", methods={"get"})
     */

    public function trickEdit(){
        return $this->render('core/editTrick.html.twig');
    }

}