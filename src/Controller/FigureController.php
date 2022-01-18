<?php

namespace App\Controller;

use Exception;
use App\Entity\Figure;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\NewTrickType;
use App\Repository\VideoRepository;
use App\Repository\FigureRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FigureGroupRepository;
use App\Repository\IllustrationRepository;
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
        $this->trick = new Figure();

        //récupération array des groupes de tricks pour liste déroulante formulaire
        $groupTricks = $figureGroupRepository->findAll();

        //création du formulaire avec les propriétées de l'entitée Comment
        $formTrick = $this->createForm(NewTrickType::class, $this->trick);

        //renseigne l'instance $user des informations entrée dans le formulaire et envoyé dans la requête
        $formTrick->handleRequest($request); 


        if($formTrick->isSubmitted() && $formTrick->isValid()) {
            $newTrick = $formTrick->getData();
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
                } catch (FileException $e) {
                    dump($e);
                }

                $newTrick->setCoverImage($newFilename);

                //Persister l'image
                $this->entityManager->persist($newTrick);

            }

            //chargement et enregistrement de la collection d'images

            //Définition de la collection d'objets illustration 

            $imagesCollection = $formTrick->get('illustrations')->getData();

            if ($imagesCollection) {

                //préparation des urls images pour la base de données 
                foreach( $imagesCollection as $objectIllustration ) {
                    //récupération de l'image
                    $image = $objectIllustration->getFileIllustration()->getClientOriginalName();
                    //récupération du nom sans extension de l'image
                    $originalFilename = pathinfo($image, PATHINFO_FILENAME);
                    //slugger le nom du fichier
                    $safeFilename = $slugger->slug($originalFilename);
                    // renommage du fichier composé du nom du fichier slugger-identifiant sha1 unique.son extension
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$objectIllustration->getFileIllustration()->guessExtension();

                    // enregistrement du média sur le serveur à l'adresse indiqué par mediasCollection_directory
                    try {
                        $objectIllustration->getFileIllustration()->move(
                            $this->getParameter('illustrationsCollection_directory'),
                            $newFilename
                        );
                        //enregistrement de l'url de l'illustration dans l'instance de l'object illustration
                        $objectIllustration->setUrlIllustration($newFilename);

                        //enregistrement de l'id de la figure dans l'instance de l'object illustration
                        $objectIllustration->setFigure($newTrick);

                        //persistance de l'instance illustration
                        $this->entityManager->persist($objectIllustration);

                        //enregistrement des illustrations dans l'instance de l'object figure
                        $newTrick->addIllustration($objectIllustration);


                    } catch (FileException $e) {
                        dump($e);
                    }

                }

            }

            //persistance de la figure
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
        VideoRepository $videoRepository,
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
        $arrayVideo = $videoRepository->findBy(['figure' => $figure]);


        //récupération de toute les url illustration et vidéos liés à la figure joint dans un tableau $medias
        $medias = [];
        $arrayIllustrationLength = count($arrayIllustration);
        $arrayVideoLength = count($arrayVideo);

        //Ajout des illustrations dans les medias
        for ($i = 0 ; $i < (int)$arrayIllustrationLength ; $i++) {
            $url_Illustration = $arrayIllustration[$i]->getUrlIllustration();
            array_push($medias, $url_Illustration );   
        } 

        //Ajout des videos dans les medias
        for ($i = 0 ; $i < (int)$arrayVideoLength ; $i++) {
            $url_video = $arrayVideo[$i]->getUrlVideo();
            array_push($medias, $url_video);   
        } 
        
        //nombre d'items dans la collection d'illustration
        $nbItemsMedias = count($medias);

        //nombre d'image par slide
        $nbItemsBySlide = 6;

        //nombre d'image ne complétant pas une slide entière
        $restMediasBySlide = (int)$nbItemsMedias%$nbItemsBySlide;

        //nombre d'image constituant des slides complet

        $nbMediasSlideComplete = $nbItemsMedias - $restMediasBySlide;

        //nombre de slides nécessaire pour afficher toutes les illustrations (entier arrondi supérieur) 

        $nbSlides = round(($nbItemsMedias/6), 0 , PHP_ROUND_HALF_UP);

        $this->arrayFirstLoop = $arrayFirstLoop = [];
        $this->arrayCurrentLoop = $arrayCurrentLoop = [];
        $this->arrayLastLoop = $arrayLastLoop = [];

     
        for ($j = 0; $j <= 5; $j++){

            $this->arrayFirstLoop = array_push($arrayFirstLoop, $medias[$j]); 

        }


        for ($j = 1; $j <= 5; $j++){

            $this->arrayCurrentLoop = array_push($arrayCurrentLoop, $medias[$j]); 
            
        }   


        for ($j = 1; $j <= $restMediasBySlide; $j++) {

            $this->arrayLastLoop = array_push($arrayLastLoop, $medias[$j]); 
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
 
        return $this->render('core/figures/trick.html.twig', ['figure' => $figure, 'comments' => $comments, 'formComment' => $formComment->createView(), 'medias' => $medias, 'nbSlides' => $nbSlides, 'arrayFirstLoop' => $arrayFirstLoop, 'arrayCurrentLoop' => $arrayCurrentLoop,  'arrayLastLoop' => $arrayLastLoop]);
    }

    /**
     * trick delete
     * 
     * @Route("/tricks/{slug}/delete", name="trickDeletePage")
     */

    public function trickDelete(){

    }
}