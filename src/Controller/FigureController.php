<?php

namespace App\Controller;

use Exception;
use App\Entity\Figure;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\NewTrickType;
use App\Form\AddMediasTrickType;
use App\Repository\VideoRepository;
use App\Repository\FigureRepository;
use App\Form\EditOneIllustrationType;
use App\Repository\CommentRepository;
use App\Form\EditIllustrationTrickType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FigureGroupRepository;
use App\Repository\IllustrationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FigureController extends AbstractController
{

    private  $figureRepository;

    public function __construct(FigureRepository $figureRepository )
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
        $this->codeYoutube = '';

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
            $videosCollection = $formTrick->get('videos')->getData();

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

            if ($videosCollection) {

                //préparation des urls images pour la base de données 
                foreach( $videosCollection as $objectVideo ) {
                    //récupération de l'url video
                    $urlVideo = $objectVideo->getUrlVideo();

                    if ( stristr($urlVideo,"embed") ) {

                        try {

                            $attrSrc = stristr($urlVideo, 'embed/'); // recherche l'occurence 'm'
                            $this->codeYoutube = substr($attrSrc, 6, 11);

                            //enregistrement de l'url de la video dans l'instance de l'object video
                            $objectVideo->setUrlVideo($this->codeYoutube);

                            //enregistrement de l'id de la figure dans l'instance de l'object video
                            $objectVideo->setFigure($this->trick);

                            $objectVideo->setEmbed(true);

                            //persistance de l'instance video
                            $this->entityManager->persist($objectVideo);

                            //enregistrement des videos dans l'object figure courante
                            $this->trick->addVideo($objectVideo);

                        } catch (FileException $e) {
                            dump($e);
                        }


                    }else{

                        try {

                            //récupération de l'url video
                            $this->codeYoutube = substr($urlVideo, -11);

                            //enregistrement de l'url de la video dans l'instance de l'object video
                            $objectVideo->setUrlVideo($this->codeYoutube);


                            //enregistrement de l'id de la figure dans l'instance de l'object video
                            $objectVideo->setFigure($this->trick);

                            $objectVideo->setEmbed(true);
                            
                            //persistance de l'instance video
                            $this->entityManager->persist($objectVideo);
                            
                            //enregistrement des videos dans l'object figure courante
                            $this->trick->addVideo($objectVideo);    

                            }catch (FileException $e) {
                                dump($e);
                            }
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
     * consulter une figure
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

        //récupération de toute les url illustration lié à la figure joint dans un tableau $illustration
        $arrayMedias = [];
        $arrayIllustrationLength = count($arrayIllustration);

        for ($i = 0 ; $i < (int)$arrayIllustrationLength ; $i++) {
            $uri_Illustration = $arrayIllustration[$i]->getUrlIllustration();
            $url_Illustration = '/uploads/illustrationsCollection/'.$uri_Illustration;
            array_push($arrayMedias, $url_Illustration );   
        }  
        
        //récupération de toute les url video lié à la figure joint dans un tableau $video
        $arrayVideo = $videoRepository->findBy(['figure' => $figure]);

        //récupération de toute les url illustration lié à la figure joint dans un tableau $illustration

        $arrayVideoLength = count($arrayVideo);

        for ($i = 0 ; $i < (int)$arrayVideoLength ; $i++) {

            $url_video = $arrayVideo[$i]->getUrlVideo();

            array_push($arrayMedias, $url_video);   
        }      

        //nombre d'items dans la collection des médias
        $nbItemsIllustrations = count($arrayMedias);

        //nombre de slides nécessaire pour afficher toutes les illustrations (entier arrondi supérieur) 

        $nbSlides = round(($nbItemsIllustrations/6), 0 , PHP_ROUND_HALF_UP);

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
 
        return $this->render('core/figures/trick.html.twig', ['figure' => $figure, 'comments' => $comments, 'formComment' => $formComment->createView(), 'arrayMedias' => $arrayMedias, 'nbItemsIllustrations' => $nbItemsIllustrations, 'nbSlides' => $nbSlides ]);
    }



    /**
     * trick edit
     * 
     * @Route("/tricks/{slug}/edit", name="trickEditPage", methods={"get"})
     */

    public function trickEdit(
        $slug,
        FigureRepository $figureRepository,
        CommentRepository $commentRepository,
        IllustrationRepository $illustrationRepository,
        VideoRepository $videoRepository,
        Request $request

    ){

        //je récupère la figure qui correspond au slug
        $figure = $figureRepository->findOneBySlug($slug);

        //Je récupère tous les commentaires lié à la figure
        $comments = $commentRepository->findBy(['figure' => $figure]);

        //je récupère tous les medias lié à la figure

        $arrayIllustration = $illustrationRepository->findBy(['figure' => $figure]);

        //récupération de toute les url illustration lié à la figure joint dans un tableau $illustration
        $arrayMedias = [];
        $objectMedia = [];

        $arrayIllustrationLength = count($arrayIllustration);
        
        for ($i = 0 ; $i < (int)$arrayIllustrationLength ; $i++) {
                $id = $arrayIllustration[$i]->getId();
                $uri_Illustration = $arrayIllustration[$i]->getUrlIllustration();
                $tag = "img";
                $objectMedia = ["path"=>$uri_Illustration, "type" => $tag, "id" => $id ];

            array_push($arrayMedias, $objectMedia);

        }   

        //récupération de toute les url video lié à la figure joint dans un tableau $video
        $arrayVideo = $videoRepository->findBy(['figure' => $figure]);

        //récupération de toute les url illustration lié à la figure joint dans un tableau $illustration
        
        $arrayVideoLength = count($arrayVideo);
        
        for ($i = 0 ; $i < (int)$arrayVideoLength ; $i++) {
            $id = $arrayVideo[$i]->getId();
            $url_video = $arrayVideo[$i]->getUrlVideo();
            $tag = "iframe";
            $objectMedia = ["path"=>$url_video, "type"=> $tag , "id"=>$id];

            array_push($arrayMedias, $objectMedia);
         
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

        return $this->render('core/figures/trickEdit.html.twig', ['figure' => $figure, 'comments' => $comments, 'formComment' => $formComment->createView(), 'arrayMedias' => $arrayMedias]);
    }


    /**
     * trick delete
     * 
     * @Route("/tricks/{slug}/delete", name="trickDeletePage")
     */

    public function trickDelete(
        $slug,
        EntityManagerInterface $entityManager,
        FigureRepository $figureRepository,
        IllustrationRepository $illustrationRepository
    ){

        $currentTrick = $figureRepository->findOneBySlug($slug);
        $idTrick = $currentTrick->getId();

        $arrayIllustrations = $illustrationRepository->findByFigure($idTrick);

        //Delete physical images on server

        foreach($arrayIllustrations as $objectIllustration) {
            $fileName = $objectIllustration->getUrlIllustration();
            $pathIllustrationsCollection = $this->getParameter('illustrationsCollection_directory');
            $filePath = $pathIllustrationsCollection."/".$fileName;
            unlink($filePath);
        }

        //remove trick in database

        $entityManager->remove($currentTrick);
        $entityManager->flush();

    
        //Redirection
        return $this->redirectToRoute('homePage');

    }

    /** CRUD média trick */


    /**
     * Creating a media of a trick
     * 
     * @Route("/tricks/{slug}/create/medias", name="trickCreateMediasPage")
     */

    public function trickCreateMedias(

        $slug,
        FigureRepository $figureRepository,
        EntityManagerInterface $entityManager,
        IllustrationRepository $illustrationRepository,
        VideoRepository $videoRepository,
        SluggerInterface $slugger,
        Request $request

    ){
        $this->entityManager = $entityManager;
        $this->trick = new Figure();
        //je récupère la figure qui correspond au slug
        $currentfigure = $figureRepository->findOneBySlug($slug);
        $this->currentfigure = $currentfigure;
        $this->codeYoutube = '';

        //TODO vérifier si l'image ou la video est déjà inséré

        //création du formulaire avec les propriétées de l'entitée trick
        $formAddMediasTrick = $this->createForm(AddMediasTrickType::class, $this->trick);

        //renseigne l'instance $user des informations entrée dans le formulaire et envoyé dans la requête
        $formAddMediasTrick->handleRequest($request); 

        if($formAddMediasTrick->isSubmitted() && $formAddMediasTrick->isValid()) {
            $updateTrick = $formAddMediasTrick->getData();
            // $updateTrick->setAuthor($this->getUser());

            //chargement et enregistrement de la collection d'images

            //Définition de la collection d'objets illustration 

            $imagesCollection = $formAddMediasTrick->get('illustrations')->getData();
            $videosCollection = $formAddMediasTrick->get('videos')->getData();

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
                        $objectIllustration->setFigure($this->currentfigure);

                        //persistance de l'instance illustration
                        $this->entityManager->persist($objectIllustration);

                        //enregistrement des illustrations dans l'instance de l'object figure courante
                        $this->currentfigure->addIllustration($objectIllustration);


                    } catch (FileException $e) {
                        dump($e);
                    }

                }

            }

            if ($videosCollection) {

                //préparation des urls images pour la base de données 
                foreach( $videosCollection as $objectVideo ) {
                    //récupération de l'url video
                    $urlVideo = $objectVideo->getUrlVideo();

                    if ( stristr($urlVideo,"embed") ) {

                        try {

                            $attrSrc = stristr($urlVideo, 'embed/'); // recherche l'occurence 'm'
                            $this->codeYoutube = substr($attrSrc, 6, 11);

                            //enregistrement de l'url de la video dans l'instance de l'object video
                            $objectVideo->setUrlVideo($this->codeYoutube);

                            //enregistrement de l'id de la figure dans l'instance de l'object video
                            $objectVideo->setFigure($this->currentfigure);

                            $objectVideo->setEmbed(true);

                            //persistance de l'instance video
                            $this->entityManager->persist($objectVideo);

                            //enregistrement des videos dans l'object figure courante
                            $this->currentfigure->addVideo($objectVideo);

                        } catch (FileException $e) {
                            dump($e);
                        }


                    }else{

                        try {

                            //récupération de l'url video
                            $this->codeYoutube = substr($urlVideo, -11);

                            //enregistrement de l'url de la video dans l'instance de l'object video
                            $objectVideo->setUrlVideo($this->codeYoutube);


                            //enregistrement de l'id de la figure dans l'instance de l'object video
                            $objectVideo->setFigure($this->currentfigure);

                            $objectVideo->setEmbed(true);
                            
                            //persistance de l'instance video
                            $this->entityManager->persist($objectVideo);
                            
                            //enregistrement des videos dans l'object figure courante
                            $this->currentfigure->addVideo($objectVideo);    

                            }catch (FileException $e) {
                                dump($e);
                            }
                        }
                    }
                }

                //persistance de la figure
                $this->entityManager->persist($this->currentfigure);

                $this->entityManager->flush();

                //Redirection
                return $this->redirectToRoute('trickEditPage', ['slug'=> $slug]);

        }    

        return $this->render('core/figures/trickAddMedia.html.twig', ['formAddMediasTrick' => $formAddMediasTrick->createView(),'currentfigure' => $currentfigure, 'codeYoutube' => $this->codeYoutube]);
    }

    /**
     * Updating a media of a trick
     * 
     * @Route("/tricks/{slug}/edit/medias/{id}", name="trickEditMediasPage")
     */

    public function trickEditMedias(

        $slug,
        $id,
        FigureRepository $figureRepository,
        EntityManagerInterface $entityManager,
        IllustrationRepository $illustrationRepository,
        VideoRepository $videoRepository,
        SluggerInterface $slugger,
        Request $request

    ){


        dump($request);
        exit;

        //je récupère la figure qui correspond au slug
        $currentfigure = $figureRepository->findOneBySlug($slug);
        $currentIllustration = $illustrationRepository->findOneById($id);

        $this->entityManager = $entityManager;
        $this->currentfigure = $currentfigure;
        $this->currentIllustration = $currentIllustration;
        $this->codeYoutube = '';

        $formEditMediasTrick = $this->createForm(EditOneIllustrationType::class);

        $formEditMediasTrick->handleRequest($request); 
    
        if($formEditMediasTrick->isSubmitted() && $formEditMediasTrick->isValid()) { 

            $objectIllustration = $formEditMediasTrick->get('urlIllustration')->getData();

            $image = $objectIllustration->getClientOriginalName();

            //récupération du nom sans extension de l'image
            $originalFilename = pathinfo($image, PATHINFO_FILENAME);

            $safeFilename = $slugger->slug($originalFilename);

            // renommage du fichier composé du nom du fichier slugger-identifiant sha1 unique.son extension
            $newFilename = $safeFilename.'-'.uniqid().'.'.$objectIllustration->guessClientExtension();
            

            // enregistrement du média sur le serveur à l'adresse indiqué par mediasCollection_directory
            try {
                $objectIllustration->move(
                $this->getParameter('illustrationsCollection_directory'),
                $newFilename
                );

                //enregistrement de l'url de l'illustration dans l'instance de l'object illustration
                $this->currentIllustration->setUrlIllustration($newFilename);
                
                //enregistrement de l'id de la figure dans l'instance de l'object illustration
                $this->currentIllustration->setFigure($this->currentfigure);
                
                //persistance de l'instance illustration
                $this->entityManager->persist($this->currentIllustration);
                
                //enregistrement des illustrations dans l'instance de l'object figure courante
                $this->currentfigure->addIllustration($currentIllustration);
            
            } catch (FileException $e) {
                dump($e);
            }

            //persistance de la figure
            $this->entityManager->persist($this->currentfigure);

            $this->entityManager->flush();
            
            //Redirection
            return $this->redirectToRoute('trickEditPage', ['slug'=> $slug]);

        }

        return $this->render('core/figures/trickEditIllustration.html.twig', ['formEditMediasTrick' => $formEditMediasTrick->createView(),'currentfigure' => $currentfigure]);
    }

    /** Deleting a media from a trick */

    /**
     * trick delete illustration
     * 
     * @Route("/tricks/{slug}/delete/illustration/{id}", name="trickDeleteIllustrationPage")
     */

    public function trickDeleteIllustration(

        $slug,
        $id,
        EntityManagerInterface $entityManager,
        IllustrationRepository $illustrationRepository
    ){
    
        $currentIllustration = $illustrationRepository->findOneById($id);
    
        $fileName = $currentIllustration->getUrlIllustration();

        $entityManager->remove($currentIllustration);

        $entityManager->flush();

        $pathIllustrationsCollection = $this->getParameter('illustrationsCollection_directory');
        $filePath = $pathIllustrationsCollection."/".$fileName;
        unlink($filePath);
        
        //Redirection
        return $this->redirectToRoute('trickEditPage', ['slug'=> $slug]);

    }

    /**
     * trick delete video
     * 
     * @Route("/tricks/{slug}/delete/video/{id}", name="trickDeleteVideoPage")
     */

    public function trickDeleteVideo(

        $slug,
        $id,
        EntityManagerInterface $entityManager,
        VideoRepository $videoRepository

    ){

        $currentIdVideo = $videoRepository->findOneById($id);

        //suppression de la video
        $entityManager->remove($currentIdVideo);

        $entityManager->flush();
        
        //Redirection
        return $this->redirectToRoute('trickEditPage', ['slug'=> $slug]);

    }

}