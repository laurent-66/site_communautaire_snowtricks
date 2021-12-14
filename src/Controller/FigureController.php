<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use App\Repository\CommentRepository;
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
     * @Route("/tricks/{slug}/edit", name="trickEditPage", methods={"get"})
     */

    public function trickEdit(){
        return $this->render('core/figures/trickEdit.html.twig');
    }


    /**
     * Undocumented function
     *
     * @param Request $request
     * 
     * @Route("/tricks/{slug}/view", name="trickViewPage")
     * 
     * @return Response
     * 
     */
    public function trickView($slug,FigureRepository $figureRepository, CommentRepository $commentRepository ){
        //je récupère la figure qui correspond au slug
        $figure = $figureRepository->findOneBySlug($slug);
        $comments = $commentRepository->findBy(['figure' => $figure]);

        return $this->render('core/figures/trick.html.twig', ['figure' => $figure, 'comments' => $comments]);

    }

    /**
     * trick delete
     * 
     * @Route("/tricks/{slug}/delete", name="trickDeletePage")
     */

    public function trickDelete(){

    }
}