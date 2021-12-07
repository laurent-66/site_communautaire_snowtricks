<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Doctrine\Common\Collections\Expr\Value;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FigureController extends AbstractController
{

    private  $figureRepository;

    public function __construct(FigureRepository $figureRepository)
    {
        $this->figureRepository = $figureRepository;
    }

    /**
     * trick view
     * 
     * @Route("/tricks/{slug}/view", name="trickViewPage", methods={"get"})
     */

    public function trickView(){
        return $this->render('core/figures/trick.html.twig');
    }


    /**
     * Undocumented function
     *
     * @param Request $request
     * 
     * @Route("/tricks/{slug}/edit", name="trickEditPage")
     * 
     * @return Response
     * 
     */
    public function trickEdit($slug,FigureRepository $figureRepository){
        //je récupère la figure qui correspond au slug
        $figure = $figureRepository->findOneBySlug($slug);

        return $this->render('core/figures/trick.html.twig', ['figure' => $figure]);

    }

    /**
     * trick delete
     * 
     * @Route("/tricks/{slug}/delete", name="trickDeletePage")
     */

    public function trickDelete(){

    }
}