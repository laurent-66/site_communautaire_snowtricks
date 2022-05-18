<?php

namespace App\Controller;

use Youtube;
use Exception;
use VideosProperties;
use App\Entity\Figure;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\NewTrickType;
use App\Form\EditTrickType;
use IllustrationsProperties;
use App\Form\EditOneVideoType;
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
use Symfony\Component\HttpFoundation\JsonResponse;
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
            $newTrick->setfixture(0);
            $coverImage = $newTrick->getCoverImageFile();

            $alternativeAttribute = $newTrick->getAlternativeAttribute();

            if ($coverImage) { 
                $originalFilename = pathinfo($coverImage->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $this->slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$coverImage->guessExtension();


                // $newFilename = UniqueIdImage::generateUniqIdFileName($coverImage);

                try {
                    $coverImage->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );

                } catch (FileException $e) {
                    dump($e);
                }

                // RegisterFileUploaded::registerFile($coverImage,$newFilename);

                $newTrick->setCoverImage($newFilename);

                if ($alternativeAttribute) {
                    $newTrick->setAlternativeAttribute($alternativeAttribute);
                } else {
                    $newTrick->setAlternativeAttribute($originalFilename);
                }

            } else {

                $newTrick->setCoverImage('defaultCoverImage');
                $newTrick->setAlternativeAttribute('Image de couverture par défaut');
            }

            $imagesCollection = $newTrick->getIllustrations();
            $videosCollection = $newTrick->getVideos();

            if ($imagesCollection) {

                foreach( $imagesCollection as $objectIllustration ) {

                    $image = $objectIllustration->getFileIllustration();

                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

                    $safeFilename = $this->slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$objectIllustration->getFileIllustration()->guessExtension();

                    try {
                        $objectIllustration->getFileIllustration()->move(
                            $this->getParameter('illustrationsCollection_directory'),
                            $newFilename
                        );
                        $objectIllustration->setUrlIllustration($newFilename);
                        $objectIllustration->setFigure($newTrick);
                        $objectIllustration->setFixture(0);
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

                    try {

                        $codeYoutube = Youtube::typeUrl($urlVideo);
                        $objectVideo->setUrlVideo($codeYoutube);
                        $objectVideo->setFigure($newTrick);
                        $this->entityManager->persist($objectVideo);
                        $newTrick->addVideo($objectVideo);  

                    } catch (FileException $e) {
                        dump($e);
                    }


                }
            }

            $newTrick->setFixture(0);
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
        $figureId = $figure->getId();

        $comments = $this->commentRepository->getCommentsPagination($figureId, $page = 1);
        $paginator = $this->commentRepository->getCommentByLimit(1, Comment::LIMIT_PER_PAGE);

                
        $arrayIllustration = $this->illustrationRepository->findBy(['figure' => $figure]);
        $arrayImagesWithPropreties = IllustrationsProperties::generateProperties($arrayIllustration);


        $arrayVideo = $this->videoRepository->findBy(['figure' => $figure]); 
        $arrayVideosWithProperties = VideosProperties::generateProperties($arrayVideo);

        $arrayMedias = array_merge($arrayImagesWithPropreties,$arrayVideosWithProperties);



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

        return $this->render('core/figures/trick.html.twig', [
            'figure' => $figure,
             'comments' => $comments, 
             'formComment' => $formComment->createView(), 
             'arrayMedias' => $arrayMedias, 
             'illustrations' => $arrayIllustration,
             'page' => 1,
             'pageTotal' => ceil(count($paginator) / Comment::LIMIT_PER_PAGE)
        ]);

    }



    /**
     * response ajax for button loadMore comments
     *
     * @route("/ajax/comments", name="get_comment_ajax", methods={"get"})
     * 
     * @param Request $request
     * 
     */
    public function getCommentsWithAjaxRequest(Request $request)
    {
        $pageTargeted = $request->query->getInt('page');

        $comments = $this->commentRepository->getCommentByLimit($pageTargeted, Comment::LIMIT_PER_PAGE);
        return new JsonResponse(
            [
                "html" => $this->renderView('core/figures/__comments.html.twig', ['comments' => $comments])
            ]

        );

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
        $arrayImagesWithPropreties = IllustrationsProperties::generateProperties($arrayIllustration);

        $arrayVideo = $this->videoRepository->findBy(['figure' => $figure]);
        $arrayVideosWithProperties = VideosProperties::generateProperties($arrayVideo);

        $arrayMedias = array_merge($arrayImagesWithPropreties, $arrayVideosWithProperties);

        // generate an object current figure without collection illustrations

        $nameTrick = $figure->getName();
        $coverTrick = $figure->getCoverImage();
        $descriptionTrick = $figure->getDescription();
        $nameSlugTrick = $this->slugger->slug($nameTrick);
        $figureGroupTrick = $figure->getFigureGroup();

        $partialFigure = new Figure();

        $partialFigure->setName($nameTrick);
        $partialFigure->setSlug($nameSlugTrick);
        $partialFigure->setCoverImage($coverTrick);
        $partialFigure->setDescription($descriptionTrick);
        $partialFigure->setFigureGroup($figureGroupTrick);

        $formEditTrick = $this->createForm(EditTrickType::class, $partialFigure);
        $formEditTrick->handleRequest($request); 
        $messageError = '';

        if($formEditTrick->isSubmitted() && $formEditTrick->isValid()) { 

            try{
        
                $formTrick = $formEditTrick->getData();
                $nameTrickField = $formTrick->getName();
                $nameTrickSluger = $this->slugger->slug($nameTrickField);
                $coverImageFile = $formTrick->getCoverImageFile();
                $coverImageTrick = $formTrick->getCoverImage();
                $alternativeAttribute = $formTrick->getAlternativeAttribute();
                $descriptionfield = $formTrick->getDescription();
                $figureGroupSelect = $formTrick->getFigureGroup();
 

                if ($coverImageFile) { 
                    $originalFilename = pathinfo($coverImageFile->getClientOriginalName(), PATHINFO_FILENAME);


                    $safeFilename = $this->slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$coverImageFile->guessExtension();

                    try {
                        $coverImageFile->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
    
                    } catch (FileException $e) {
                        dump($e);
                        exit;
                    } 
    

                    $figure->setCoverImage($newFilename);
                    $figure->setAlternativeAttribute($originalFilename);
                    $figure->setFixture(0);
    
                }

                    $codeYoutube = '';
                    $imagesCollection = $formTrick->getIllustrations();
                    $videosCollection = $formTrick->getVideos();
                    $arrayObjectIllustration = [];

                    if ($imagesCollection) {

                        foreach( $imagesCollection as $objectIllustration ) {
        
                            $image = $objectIllustration->getFileIllustration();
                            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                            $safeFilename = $this->slugger->slug($originalFilename);
                            $newFilename = $safeFilename.'-'.uniqid().'.'.$objectIllustration->getFileIllustration()->guessExtension();

                            $alternativeAttribute = $objectIllustration->getAlternativeAttribute();

                                try {

                                    $objectIllustration->getFileIllustration()->move(
                                        $this->getParameter('illustrationsCollection_directory'),
                                        $newFilename
                                    );

                                    $objectIllustration->setUrlIllustration($newFilename);
                                    $objectIllustration->setFigure($figure);
                                    $objectIllustration->setFixture(0);

                                    if( $alternativeAttribute !== null) {

                                        $objectIllustration->setAlternativeAttribute($alternativeAttribute);
  
                                    } else {

                                        $objectIllustration->setAlternativeAttribute($originalFilename);
  
                                    }

                                    $this->entityManager->persist($objectIllustration);

                                } catch (FileException $e) {
                                    dump($e);
                                    exit; 
                                }

                                $figure->addIllustration($objectIllustration);

                                array_push($arrayObjectIllustration, $objectIllustration);

                        }

                    }

                    $arrayObjectVideo = [];

                    if ($videosCollection) {

                        foreach( $videosCollection as $objectVideo ) {
        
                            $urlVideo = $objectVideo->getUrlVideo();
        

                                try {

                                    $codeYoutube = Youtube::typeUrl($urlVideo);
                                    $objectVideo->setUrlVideo($codeYoutube);
                                    $objectVideo->setFigure($figure);
                                    $this->entityManager->persist($objectVideo);
                                    $figure->addVideo($objectVideo);
                                    array_push($arrayObjectVideo, $objectVideo);

                                } catch (FileException $e) {
                                    dump($e);
                                    exit;
                                }
                        }
                    }

                $arrayMedias = array_merge( $arrayObjectIllustration, $arrayObjectVideo);


                $fixtureDefinition = $figure->getFixture();

                $figure->setName($nameTrickField);
                $figure->setSlug($nameTrickSluger);
                $figure->setDescription($descriptionfield);
                $figure->setFigureGroup($figureGroupSelect);
                $figure->setFixture($fixtureDefinition);

                $this->entityManager->persist($figure);

                $this->entityManager->flush();


            }catch(Exception $e){
                dump($e);
                exit; 
            }



            $newSlug = $figure->getSlug();

            return $this->redirectToRoute('trickViewPage', ['slug'=> $newSlug]);
        }

        return $this->render('core/figures/trickEdit.html.twig', ['figure' => $figure, 'comments' => $comments, 'arrayMedias' => $arrayMedias, 'formEditTrick' => $formEditTrick->createView(),  'messageError' => $messageError ,'error' => false ]);
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
        $formUpdateCoverImage = $this->createForm(UpdateCoverImageType::class); 
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

                        try {

                            $codeYoutube = Youtube::typeUrl($urlVideo);
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
     * trick delete cover image
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