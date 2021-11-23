<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FigureController extends AbstractController
{
    /**
     * trick view
     * 
     * @Route("/tricks/{slug}/view", name="trickViewPage", methods={"get"})
     */

    public function trickView(){
        return $this->render('core/viewTrick.html.twig');
    }

    /**
     * trick edit
     * 
     * @Route("/tricks/{slug}/edit", name="trickEditPage")
     */

    public function trickEdit(){
        return $this->render('core/editTrick.html.twig');
    }

    /**
     * trick delete
     * 
     * @Route("/tricks/{slug}/delete", name="trickDeletePage")
     */

    public function trickDelete(){

    }
}