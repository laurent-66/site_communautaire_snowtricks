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
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


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
        FigureGroupRepository $figureGroupRepository,
        SluggerInterface $slugger
        ){

        $this->entityManager = $entityManager;

        //récupération array des groupes de tricks pour liste déroulante formulaire
        $groupTricks = $figureGroupRepository->findAll();

        //création du formulaire avec les propriétées de l'entitée Comment
        $formTrick = $this->createForm(NewTrickType::class);

        //renseigne l'instance $user des informations entrée dans le formulaire et envoyé dans la requête
        $formTrick->handleRequest($request); 

        if($formTrick->isSubmitted() && $formTrick->isValid()) {
            $newTrick = $formTrick->getData();
            dump($newTrick);
   
            $newTrick->setAuthor($this->getUser());

            // Chargement et enregistrement de l'image de couverture du trick

            $coverImage = $formTrick->get('coverImage')->getData();
            if ($coverImage) {
                $originalFilename = pathinfo($coverImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$coverImage->guessExtension();

                // Move the file to the directory where images are stored
                try {
                    $coverImage->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    dump($newFilename);
                    exit;
                } catch (FileException $e) {
                    dump($e);
                }

                $newTrick->setCoverImage($newFilename);

                //Persister l'image
                $this->entityManager->persist($newTrick);

            }

            //chargement et enregistrement de la collection d'images

            $imagesCollection = $formTrick->get('collectionIllustrations')->getData();
            dump($imagesCollection);
            exit;

            if ($imagesCollection) {

                foreach( $imagesCollection as $objectIllustration ) {

                    $image = $objectIllustration->getFilellustration()->getClientOriginalName();
                    dump($image);

                    $originalFilename = pathinfo($image, PATHINFO_FILENAME);
                    dump($originalFilename);
                    //slugger le nom du fichier
                    $safeFilename = $slugger->slug($originalFilename);
                    // renommage du fichier composé du nom du fichier slugger-identifiant sha1 unique.son extension
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$objectIllustration->getFilellustration()->guessExtension();
                    dump($newFilename);

                    // enregistrement du média sur le serveur à l'adresse indiqué par mediasCollection_directory
                    try {
                        $objectIllustration->getFilellustration()->move(
                            $this->getParameter('imagesCollection_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        dump($e);
                    }
                    
                    $newTrick->addIllustration($newFilename);

                    //Persister l'image
                    $this->entityManager->persist($newTrick);

                }

            }


            //chargement et enregistrement de la collection des videos

            // $videosCollection = $formTrick->get('videos')->getData();
            // dump($videosCollection);

            // if ($videosCollection) {

            //     foreach( $videosCollection as $objectVideo ) {

            //         $video = $objectVideo->getUrlVideo();
            //         dump($video);

            //         // enregistrement du média sur le serveur à l'adresse indiqué par mediasCollection_directory
            //         try {
            //             $newTrick->addVideo($video);

            //         } catch (FileException $e) {
            //             dump($e);
            //         }
                    
            //         //Persister l'image
            //         $this->entityManager->persist($newTrick);
            //         dump($newTrick);

            //     }

            // }


            dump($newTrick);
            exit;
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

    public function trickEdit(
        $slug,
        FigureRepository $figureRepository,
        IllustrationRepository $illustrationRepository,
        Request $request

    ){

        //je récupère la figure qui correspond au slug
        $figure = $figureRepository->findOneBySlug($slug);

        //je récupère tous les medias lié à la figure

        $arrayIllustration = $illustrationRepository->findBy(['figure' => $figure]);

        //récupération de toute les url illustration lié à la figure joint dans un tableau $illustration
        $illustrations = [];
        $arrayIllustrationLength = count($arrayIllustration);
        
        for ($i = 0 ; $i < (int)$arrayIllustrationLength ; $i++) {
            $url_Illustration = $arrayIllustration[$i]->getUrlIllustration();
            array_push($illustrations, $url_Illustration );   
        }    


        return $this->render('core/figures/trickEdit.html.twig',['figure' => $figure,'illustrations' => $illustrations]);
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


        //création du formulaire avec les propriétées de l'entitée Comment
        $formComment = $this->createForm(CommentType::class);

        //renseigne l'instance $user des informations entrée dans le formulaire et envoyé dans la requête
        $formComment->handleRequest($request);

        if($formComment->isSubmitted() && $formComment->isValid()) {
            try{

                $newComment = $formComment->getData();
                $newComment->setFigure($figure);
                $newComment->setAuthor($this->getUser());
    
                //Persister le commentaire
                $this->entityManager->persist($newComment);
                $this->entityManager->flush();

            }catch(Exception $e){

                dump($e);
                exit;
            }

            //Redirection
            return $this->redirectToRoute('trickViewPage', ['slug'=> $slug]);
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