<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Repository\FigureRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends AbstractController
{
    private  $figureRepository;

    public function __construct(FigureRepository $figureRepository)
    {
        $this->figureRepository = $figureRepository;
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return response
     * 
     * @Route("/", name="homePage", methods={"get"})
     * 
     */
    public function home(Request $request)
    {

        $paginator = $this->figureRepository->getFigureByLimit(1, Figure::LIMIT_PER_PAGE);

        return $this->render(
            'core/figures/home.html.twig', 
            [
                'figures' => $paginator,
                'page' => 1,
                'pageTotal' => ceil(count($paginator) / Figure::LIMIT_PER_PAGE)
            ]
        );

    }

    /**
     * response ajax
     *
     * @route("/ajax/figures", name="get_figure_ajax", methods={"get"})
     * 
     * @param Request $request
     * 
     */
    public function getFiguresWithAjaxRequest(Request $request)
    {
        $pageTargeted = $request->query->getInt('page');
        $figures = $this->figureRepository->getFigureByLimit($pageTargeted, Figure::LIMIT_PER_PAGE);
        return new JsonResponse(
            [
                "html" => $this->renderView('core/figures/__list_figures.html.twig', ['figures' => $figures])
            ]
        );
    }
}