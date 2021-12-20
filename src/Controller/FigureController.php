<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\NewTrickType;
use App\Repository\FigureRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FigureGroupRepository;
use App\Repository\IllustrationRepository;
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
 * Permet de créer un trick
 *
 * @return Response
 * 
 * @Route("tricks/new", name="newtrickPage")
 */
    public function create(
        Request $request, 
        EntityManagerInterface $entityManager,
        FigureGroupRepository $figureGroupRepository
        
        ){

        $this->entityManager = $entityManager;


        //récupération array des groupes de tricks pour liste déroulante formulaire
        $groupTricks = $figureGroupRepository->findAll();

        $newTrick = new Figure();

        //création du formulaire avec les propriétées de l'entitée Comment
        $formTrick = $this->createForm(NewTrickType::class, $newTrick);

        //renseigne l'instance $user des informations entrée dans le formulaire et envoyé dans la requête
        $formTrick->handleRequest($request);
        dump($formTrick);
        exit;
 

        if($formTrick->isSubmitted() && $formTrick->isValid()) {




            //TODO

            $coverImage = $formTrick->get('coverImage')->getData();







        
            //Persister le commentaire
            $this->entityManager->persist($newTrick);
            $this->entityManager->flush();

            //Redirection
            return $this->redirectToRoute('homePage');
        }

        return $this->render('core/figures/trickCreate.html.twig', ['formTrick' => $formTrick->createView(),'groupTricks' => $groupTricks]);
    }










    /**
     * trick edit
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
    public function trickView(
        $slug,
        FigureRepository $figureRepository,
        CommentRepository $commentRepository,
        IllustrationRepository $illustrationRepository,
        Request $request, 
        EntityManagerInterface $entityManager
         ) {
        $this->entityManager =  $entityManager;   

        //je récupère la figure qui correspond au slug
        $figure = $figureRepository->findOneBySlug($slug);

        //Je récupère tous les commentaires lié à la figure
        $comments = $commentRepository->findBy(['figure' => $figure]);

        //je récupère tous les medias lié à la figure

        $arrayIllustration = $illustrationRepository->findBy(['figure' => $figure]);
        // dump($arrayIllustration[0]->getUrlIllustration());
        // exit;

        //récupération de toute les url illustration lié à la figure joint dans un tableau $illustration
        $illustrations = [];
        $arrayIllustrationLength = count($arrayIllustration);

        for ($i = 0 ; $i < (int)$arrayIllustrationLength ; $i++) {
            $url_Illustration = $arrayIllustration[$i]->getUrlIllustration();
            array_push($illustrations, $url_Illustration );   
        }      

        $newComment = new Comment();
        //création du formulaire avec les propriétées de l'entitée Comment
        $formComment = $this->createForm(CommentType::class, $newComment);

        //renseigne l'instance $user des informations entrée dans le formulaire et envoyé dans la requête
        $formComment->handleRequest($request);

        if($formComment->isSubmitted() && $formComment->isValid()) {

            //Persister le commentaire
            $this->entityManager->persist($newComment);
            $this->entityManager->flush();
            //Redirection
            return $this->redirectToRoute('homePage');
        }

        return $this->render('core/figures/trick.html.twig', ['figure' => $figure, 'comments' => $comments, 'formComment' => $formComment->createView(), 'illustrations' => $illustrations]);
    }

    /**
     * trick delete
     * 
     * @Route("/tricks/{slug}/delete", name="trickDeletePage")
     */

    public function trickDelete(){

    }
}