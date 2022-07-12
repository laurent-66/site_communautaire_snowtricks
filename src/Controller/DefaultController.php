<?php

namespace App\Controller;

use App\Entity\Figure;
use App\ImageOptimizer;
use App\Repository\FigureRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

class DefaultController extends AbstractController
{
    private $figureRepository;

    public function __construct(FigureRepository $figureRepository, ImageOptimizer $imageOptimizer, SluggerInterface $slugger )
    {
        $this->figureRepository = $figureRepository;
        $this->imageOptimizer = $imageOptimizer;
        $this->slugger = $slugger;

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

        $figuresList = $this->figureRepository->findAll();

        foreach($figuresList as $figure) {

            $nameTrick = $figure->getCoverImage();
            $fileExtension = stristr(strtolower($nameTrick),'.');
            $nameTrickOnly = substr($nameTrick, 0, -strlen($fileExtension));
            if($figure->getFixture() == 1) {
                $pathCoverImage = $this->getParameter('cover_image_fixture_repository');
            } else {
                $pathCoverImage = $this->getParameter('images_directory');
            }
            $filename = $pathCoverImage.'\\'.$nameTrickOnly.$fileExtension;
            $this->imageOptimizer->resize($filename);
        }


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
