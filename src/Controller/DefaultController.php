<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage", methods={"get"})
     */

    public function index(){
        return $this->render('core/login.html.twig', ['name'=> 'John Doe']);
    }
}