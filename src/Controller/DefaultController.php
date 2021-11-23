<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    private  $figureRepository;

    public function __construct(FigureRepository $figureRepository)
    {
        $this->figureRepository = $figureRepository;
    }

    /**
     * @Route("/", name="homePage", methods={"get"})
     */
    public function home(Request $request){

        $figures = $this->figureRepository->findAll();

        return $this->render('core/home.html.twig', ['figures'=> $figures]);

    }

}