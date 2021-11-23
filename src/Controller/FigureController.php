<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FigureController extends AbstractController
{
    /**
     * tricks list Home
     * 
     * @Route("/", name="tricksListHome")
     */

    private  $figureRepository;

    public function __construct(FigureRepository $figureRepository)
    {
        $this->figureRepository = $figureRepository;
    }

    public function trickList(Request $request){

        $figures = $this->figureRepository->findAll();


        return $this->render('core/home.html.twig', ['figures'=> $figures]);
    }

    /**
     * trick view
     * 
     * @Route("/trick/view/{id}", name="trickViewPage", methods={"get"})
     */

    public function trickView(){
        return $this->render('core/editTrick.html.twig');
    }

    /**
     * trick edit
     * 
     * @Route("/trick/edit/{id}", name="trickEditPage")
     */

    public function trickEdit(){
        return $this->render('core/editTrick.html.twig');
    }

    /**
     * trick delete
     * 
     * @Route("/trick/edit/{id}", name="trickEditPage")
     */

    public function trickDelete(){
        return $this->render('core/editTrick.html.twig');
    }
}