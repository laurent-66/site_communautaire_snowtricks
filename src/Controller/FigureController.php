<?php

namespace App\Controller;

use Exception;
use App\Form\CommentType;
use App\Form\NewTrickType;
use App\Form\EditOneVideoType;
use App\Form\AddMediasTrickType;
use App\Form\DescriptionTrickType;
use App\Form\UpdateCoverImageType;
use App\Repository\VideoRepository;
use App\Repository\FigureRepository;
use App\Form\EditOneIllustrationType;
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

    public function __construct(


        EntityManagerInterface $entityManager,
        FigureRepository $figureRepository, 
        SluggerInterface $slugger,
        CommentRepository $commentRepository,
        IllustrationRepository $illustrationRepository,
        VideoRepository $videoRepository,
        FigureGroupRepository $figureGroupRepository  
    )
    {

        $this->entityManager = $entityManager;
        $this->figureRepository = $figureRepository;
        $this->slugger = $slugger;
        $this->commentRepository = $commentRepository;
        $this->illustrationRepository = $illustrationRepository;
        $this->videoRepository = $videoRepository;
        $this->figureGroupRepository = $figureGroupRepository;

    }


/**
 * Permet de créer un trick
 *
 * @return Response
 * 
 * @Route("tricks/new", name="newtrickPage")
 */
    public function create(Request $request) {

        $codeYoutube = '';
        $groupTricks = $this->figureGroupRepository->findAll();
        $formTrick = $this->createForm(NewTrickType::class);
        $formTrick->handleRequest($request);

        if($formTrick->isSubmitted() && $formTrick->isValid()) {

            $newTrick = $formTrick->getData();
            $newTrick->setAuthor($this->getUser());
            $coverImage = $formTrick->get('coverImageFile')->getData();

            if ($coverImage) { 
                $originalFilename = pathinfo($coverImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $this->slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$coverImage->guessExtension();

                try {
                    $coverImage->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );

                } catch (FileException $e) {
                    dump($e);
                }

                $newTrick->setCoverImage($newFilename);
                $this->entityManager->persist($newTrick);

            }

            $imagesCollection = $formTrick->get('illustrations')->getData();
            $videosCollection = $formTrick->get('videos')->getData();

            if ($imagesCollection) {

                foreach( $imagesCollection as $objectIllustration ) {

                    $image = $objectIllustration->getFileIllustration()->getClientOriginalName();
                    $originalFilename = pathinfo($image, PATHINFO_FILENAME);
                    $safeFilename = $this->slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$objectIllustration->getFileIllustration()->guessExtension();

                    try {
                        $objectIllustration->getFileIllustration()->move(
                            $this->getParameter('illustrationsCollection_directory'),
                            $newFilename
                        );
                        $objectIllustration->setUrlIllustration($newFilename);
                        $objectIllustration->setFigure($newTrick);
                        $this->entityManager->persist($objectIllustration);
                        $newTrick->addIllustration($objectIllustration);


                    } catch (FileException $e) {
                        dump($e);
                    }

                }

            }

            if ($videosCollection) {

                foreach( $videosCollection as $objectVideo ) {

                    $urlVideo = $objectVideo->getUrlVideo();

                    if ( stristr($urlVideo,"embed") ) {

                        try {

                            $attrSrc = stristr($urlVideo, 'embed/');
                            $codeYoutube = substr($attrSrc, 6, 11);
                            $objectVideo->setUrlVideo($codeYoutube);
                            $objectVideo->setFigure($newTrick);
                            $this->entityManager->persist($objectVideo);
                            $newTrick ->addVideo($objectVideo);

                        } catch (FileException $e) {
                            dump($e);
                        }


                    }else{

                        try {

                            $codeYoutube = substr($urlVideo, -11);
                            $objectVideo->setUrlVideo($codeYoutube);
                            $objectVideo->setFigure($newTrick);
                            $this->entityManager->persist($objectVideo);
                            $newTrick ->addVideo($objectVideo);    

                            }catch (FileException $e) {
                                dump($e);
                            }
                        }
                    }
                }

            $this->entityManager->persist($newTrick);
            $this->entityManager->flush();

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
    public function trickView( $slug, Request $request) {
 
        $figure = $this->figureRepository->findOneBySlug($slug);
        $comments = $this->commentRepository->findBy(['figure' => $figure]);
                
        $arrayIllustration = $this->illustrationRepository->findBy(['figure' => $figure]);
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
        
        $arrayVideo = $this->videoRepository->findBy(['figure' => $figure]);         
        $arrayVideoLength = count($arrayVideo);
                
        for ($i = 0 ; $i < (int)$arrayVideoLength ; $i++) {
            $id = $arrayVideo[$i]->getId();
            $url_video = $arrayVideo[$i]->getUrlVideo();
            $tag = "iframe";
            $objectMedia = ["path"=>$url_video, "type"=> $tag , "id"=>$id];
            array_push($arrayMedias, $objectMedia);
                 
        }  
        
        $formComment = $this->createForm(CommentType::class);
        $formComment->handleRequest($request);

        if($formComment->isSubmitted() && $formComment->isValid()) {

            try{

                $newComment = $formComment->getData();
                $newComment->setFigure($figure);
                $newComment->setAuthor($this->getUser());
                $this->entityManager->persist($newComment);
                $this->entityManager->flush();

            }catch(Exception $e){

                dump($e);
                exit;
            }

            return $this->redirectToRoute('trickViewPage', ['slug'=> $slug]);
        }
 
        return $this->render('core/figures/trick.html.twig', ['figure' => $figure, 'comments' => $comments, 'formComment' => $formComment->createView(), 'arrayMedias' => $arrayMedias]);
    }



    /**
     * trick edit
     * 
     * @Route("/tricks/{slug}/edit", name="trickEditPage") 
     */

    public function trickEdit($slug,Request $request) {

        $figure = $this->figureRepository->findOneBySlug($slug);
        $comments = $this->commentRepository->findBy(['figure' => $figure]);
        $arrayIllustration = $this->illustrationRepository->findBy(['figure' => $figure]);

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

        $arrayVideo = $this->videoRepository->findBy(['figure' => $figure]);
        $arrayVideoLength = count($arrayVideo);
        
        for ($i = 0 ; $i < (int)$arrayVideoLength ; $i++) {
            $id = $arrayVideo[$i]->getId();
            $url_video = $arrayVideo[$i]->getUrlVideo();
            $tag = "iframe";
            $objectMedia = ["path"=>$url_video, "type"=> $tag , "id"=>$id];
            array_push($arrayMedias, $objectMedia);
         
        }  

        $formDescriptionTrick = $this->createForm(DescriptionTrickType::class,$figure);
        $formDescriptionTrick->handleRequest($request);
        $messageError = '';

        if($formDescriptionTrick->isSubmitted() && $formDescriptionTrick->isValid()) {
            try{
        
                $descriptionTrick = $formDescriptionTrick->getData();
                $nameTrickField = $descriptionTrick->getName();
                $descriptionfield = $descriptionTrick->getDescription();
                $figureGroupSelect = $descriptionTrick->getFigureGroup();
                $coverImageTrick = $descriptionTrick->getCoverImage();
                $coverImageTrick == null ? 'defaultCoverImage' : $coverImageTrick;
                $nameTrickSluger = $this->slugger->slug($nameTrickField);
    
                $figure->setName($nameTrickField);
                $figure->setSlug($nameTrickSluger);
                $figure->setDescription($descriptionfield);
                $figure->setCoverImage($coverImageTrick);
                $figure->setFigureGroup($figureGroupSelect);
                $this->entityManager->persist($figure);
                $this->entityManager->flush();

            }catch(Exception $e){
        
                dump($e);
                exit;
            }

            $newSlug = $figure->getSlug();

            return $this->redirectToRoute('trickViewPage', ['slug'=> $newSlug]);
        }

        return $this->render('core/figures/trickEdit.html.twig', ['figure' => $figure, 'comments' => $comments, 'arrayMedias' => $arrayMedias, 'formDescriptionTrick' => $formDescriptionTrick->createView(),  'messageError' => $messageError ,'error' => false ]);
    }




    /**
     * trick delete video
     * 
     * @Route("/tricks/{slug}/edit/updateCoverImage", name="trickUpdateCoverImage")
     */

    public function trickUdapteCoverImage(

        $slug,
        Request $request
    ){

        $figure = $this->figureRepository->findOneBySlug($slug);
        $formUpdateCoverImage = $this->createForm(UpdateCoverImageType::class, $figure);
        $formUpdateCoverImage->handleRequest($request);
    
        if($formUpdateCoverImage->isSubmitted() && $formUpdateCoverImage->isValid()) {

            try{

                $coverImage = $formUpdateCoverImage->get('coverImage')->getData();

                if ($coverImage) {
                    $originalFilename = pathinfo($coverImage->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $this->slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$coverImage->guessExtension();

                    try {
                        $coverImage->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );

                    } catch (FileException $e) {
                        dump($e);
                    }

                    $alternativeAttribute = $formUpdateCoverImage->get('alternativeAttribute')->getData();
                    $figure->setCoverImage($newFilename);
                    $figure->setAlternativeAttribute($alternativeAttribute);
                    $this->entityManager->persist($figure);
                    $this->entityManager->flush();

                } else {

                    $figure->setCoverImage("image-solid.svg");
                    $figure->setAlternativeAttribute('default-image');
                    $this->entityManager->persist($figure);
                    $this->entityManager->flush();
                }

            }catch(Exception $e){
                dump($e);
                exit;
            }
            return $this->redirectToRoute('trickEditPage', ['slug'=> $slug]);
        }
        return $this->render('core/figures/updateCoverImage.html.twig', ['slug'=> $slug, 'formUpdateCoverImage' => $formUpdateCoverImage->createView()]);
    }


    /**
     * trick delete
     * 
     * @Route("/tricks/{slug}/delete", name="trickDeletePage")
     */

    public function trickDelete($slug){

        $currentTrick = $this->figureRepository->findOneBySlug($slug);
        $idTrick = $currentTrick->getId();
        $arrayIllustrations = $this->illustrationRepository->findByFigure($idTrick);

        foreach($arrayIllustrations as $objectIllustration) {

            $fileName = $objectIllustration->getUrlIllustration();
            $pathIllustrationsCollection = $this->getParameter('illustrationsCollection_directory');
            $filePath = $pathIllustrationsCollection.'\\'.$fileName;
            unlink($filePath);
 
        }

        $fileNameCoverImage = $currentTrick->getCoverImage();

        if($fileNameCoverImage !== 'defaultCoverImage' ) {

            $pathCoverImage = $this->getParameter('images_directory');
            $filePath = $pathCoverImage.'\\'. $fileNameCoverImage ;
            unlink($filePath);
        }

        $this->entityManager->remove($currentTrick);
        $this->entityManager->flush();
        return $this->redirectToRoute('homePage');

    }

    /** CRUD média trick */


    /**
     * Creating a media of a trick
     * 
     * @Route("/tricks/{slug}/create/medias", name="trickCreateMediasPage")
     */

    public function trickCreateMedias($slug, Request $request){

        $currentfigure = $this->figureRepository->findOneBySlug($slug);
        $codeYoutube = '';
        $formAddMediasTrick = $this->createForm(AddMediasTrickType::class);
        $formAddMediasTrick->handleRequest($request); 
        // dump($formAddMediasTrick);
        // exit;

        if($formAddMediasTrick->isSubmitted() && $formAddMediasTrick->isValid()) {

            $updateTrick = $formAddMediasTrick->getData();

            dump($updateTrick);
            exit;

            $imagesCollection = $formAddMediasTrick->get('illustrations')->getData();
            $videosCollection = $formAddMediasTrick->get('videos')->getData();

            if ($imagesCollection) { 

                foreach( $imagesCollection as $objectIllustration ) {

                    $image = $objectIllustration->getFileIllustration()->getClientOriginalName();
                    $originalFilename = pathinfo($image, PATHINFO_FILENAME);
                    $safeFilename = $this->slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$objectIllustration->getFileIllustration()->guessExtension();
                    $alternativeAttribute = $objectIllustration->getAlternativeAttribute();

                    try {
                        $objectIllustration->getFileIllustration()->move(
                            $this->getParameter('illustrationsCollection_directory'),
                            $newFilename
                        );

                        $objectIllustration->setUrlIllustration($newFilename);
                        $objectIllustration->setAlternativeAttribute($alternativeAttribute);
                        $objectIllustration->setFigure($currentfigure);
                        $this->entityManager->persist($objectIllustration);
                        $currentfigure->addIllustration($objectIllustration);

                    } catch (FileException $e) {
                        dump($e);
                    }
                }
            }

            if ($videosCollection) {
                foreach( $videosCollection as $objectVideo ) {
                    $urlVideo = $objectVideo->getUrlVideo();
                    if ( stristr($urlVideo,"embed") ) {

                        try {

                            $attrSrc = stristr($urlVideo, 'embed/');
                            $codeYoutube = substr($attrSrc, 6, 11);
                            $objectVideo->setUrlVideo($codeYoutube);
                            $objectVideo->setFigure($currentfigure);
                            $objectVideo->setEmbed(true);
                            $this->entityManager->persist($objectVideo);
                            $currentfigure->addVideo($objectVideo);

                        } catch (FileException $e) {
                            dump($e);
                        }


                    }else{

                        try {
                            $codeYoutube = substr($urlVideo, -11);
                            $objectVideo->setUrlVideo($codeYoutube);
                            $objectVideo->setFigure($currentfigure);
                            $objectVideo->setEmbed(true);
                            $this->entityManager->persist($objectVideo);
                            $currentfigure->addVideo($objectVideo);    

                            } catch (FileException $e) {
                                dump($e);
                            }
                        }
                    }
                }

                $this->entityManager->persist($currentfigure);
                $this->entityManager->flush();
                return $this->redirectToRoute('trickEditPage', ['slug'=> $slug]);

        }    

        return $this->render('core/figures/trickAddMedia.html.twig', ['formAddMediasTrick' => $formAddMediasTrick->createView(),'currentfigure' => $currentfigure, 'codeYoutube' => $codeYoutube]);
    }

    /**
     * Updating a media of a trick
     * 
     * @Route("/tricks/{slug}/edit/medias/{id}", name="trickEditMediasPage")
     */

    public function trickEditMedias($slug, $id, Request $request) {

        $typeMedia = $request->query->get('type');
        $currentfigure = $this->figureRepository->findOneBySlug($slug);
        $currentIllustration = $this->illustrationRepository->findOneById($id);
        $currentVideo = $this->videoRepository->findOneById($id);
        $codeYoutube = '';

            if($typeMedia === 'image') {

                $formEditMediasTrick = $this->createForm(EditOneIllustrationType::class);
                $formEditMediasTrick->handleRequest($request); 
            
                if($formEditMediasTrick->isSubmitted() && $formEditMediasTrick->isValid()) { 

                    $objectIllustration = $formEditMediasTrick->get('urlIllustration')->getData();
                    $image = $objectIllustration->getClientOriginalName();
                    $originalFilename = pathinfo($image, PATHINFO_FILENAME);
                    $safeFilename = $this->slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$objectIllustration->guessClientExtension();
                    
                    try {
                        $objectIllustration->move(
                        $this->getParameter('illustrationsCollection_directory'),
                        $newFilename
                        );

                        $currentIllustration->setUrlIllustration($newFilename);
                        $currentIllustration->setFigure($currentfigure);
                        $this->entityManager->persist($currentIllustration);

                        $currentfigure->addIllustration($currentIllustration);
                        $this->entityManager->persist($currentfigure);
                        $this->entityManager->flush();

                    } catch (FileException $e) {
                        dump($e);
                    }

                    return $this->redirectToRoute('trickEditPage', ['slug'=> $slug]);
                } 

                return $this->render('core/figures/trickEditIllustration.html.twig', ['formEditMediasTrick' => $formEditMediasTrick->createView(),'currentfigure' => $currentfigure]);

            } else {

                $formEditMediasTrick = $this->createForm(EditOneVideoType::class);
                $formEditMediasTrick->handleRequest($request); 

                if($formEditMediasTrick->isSubmitted() && $formEditMediasTrick->isValid()) {

                    $objectVideo = $formEditMediasTrick->getData();
                    $urlVideo = $objectVideo->getUrlVideo();

                    if ( stristr($urlVideo,"embed") ) {

                        try {

                            $attrSrc = stristr($urlVideo, 'embed/'); 
                            $codeYoutube = substr($attrSrc, 6, 11);
                            $currentVideo->setUrlVideo($codeYoutube);
                            $currentVideo->setFigure($currentfigure);
                            $currentVideo->setEmbed(true);
                            $this->entityManager->persist($currentVideo);
                            $this->entityManager->flush($currentVideo);

                            $currentfigure->addVideo($currentVideo);   
                            $this->entityManager->persist($currentfigure);
                            $this->entityManager->flush($currentfigure);

                        } catch (FileException $e) {
                            dump($e);
                        }

                    }else{

                        try {

                            $codeYoutube = substr($urlVideo, -11);
                            $currentVideo->setUrlVideo($codeYoutube);
                            $currentVideo->setFigure($currentfigure);
                            $currentVideo->setEmbed(true);
                            $this->entityManager->persist($currentVideo);
                            $this->entityManager->flush($currentVideo);

                            $currentfigure->addVideo($currentVideo);
                            $this->entityManager->persist($currentfigure);
                            $this->entityManager->flush($currentfigure);

                            }catch (FileException $e) {
                                dump($e);
                            }
                    }

                    return $this->redirectToRoute('trickEditPage', ['slug'=> $slug]);    

                }

                return $this->render('core/figures/trickEditVideo.html.twig', ['formEditMediasTrick' => $formEditMediasTrick->createView(),'currentfigure' => $currentfigure]);

            }

    }


    /** Deleting a media from a trick */

    /**
     * trick delete illustration
     * 
     * @Route("/tricks/{slug}/delete/illustration/{id}", name="trickDeleteIllustrationPage")
     */

    public function trickDeleteIllustration($slug, $id) {
    
        $currentIllustration = $this->illustrationRepository->findOneById($id);
        $fileName = $currentIllustration->getUrlIllustration();
        $this->entityManager->remove($currentIllustration);
        $this->entityManager->flush();

        $pathIllustrationsCollection = $this->getParameter('illustrationsCollection_directory');
        $filePath = $pathIllustrationsCollection."/".$fileName;
        unlink($filePath);
        
        return $this->redirectToRoute('trickEditPage', ['slug'=> $slug]);

    }

    /**
     * trick delete video
     * 
     * @Route("/tricks/{slug}/delete/video/{id}", name="trickDeleteVideoPage")
     */

    public function trickDeleteVideo( $slug, $id) { 

        $currentIdVideo = $this->videoRepository->findOneById($id);

        $this->entityManager->remove($currentIdVideo);

        $this->entityManager->flush();

        return $this->redirectToRoute('trickEditPage', ['slug'=> $slug]);

    }

    /**
     * trick delete video
     * 
     * @Route("/tricks/{slug}/edit/deleteCoverImage", name="trickDeleteCoverImage")
     */

    public function trickDeleteCoverImage($slug) {

        $figure = $this->figureRepository->findOneBySlug($slug);
        $currentCoverImage = $figure->getCoverImage();
        $pathCoverImage = $this->getParameter('images_directory');
        $filePath = $pathCoverImage."/".$currentCoverImage;
        unlink($filePath);

        $figure->setCoverImage('defaultCoverImage');
        $figure->setAlternativeAttribute('image par defaut');
        $this->entityManager->persist($figure);
        $this->entityManager->flush();

        return $this->redirectToRoute('trickEditPage', ['slug'=> $slug]);
    }

}