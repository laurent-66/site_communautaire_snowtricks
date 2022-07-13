<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Comment;
use App\ImageOptimizer;
use App\Form\CommentType;
use App\Services\Youtube;
use App\Form\NewTrickType;
use App\Form\EditTrickType;
use App\Form\EditOneVideoType;
use App\Services\UniqueIdImage;
use App\Form\UpdateCoverImageType;
use App\Services\VideosProperties;
use App\Repository\VideoRepository;
use App\Services\DeleteImageStored;
use App\Repository\FigureRepository;
use App\Form\EditOneIllustrationType;
use App\Repository\CommentRepository;
use App\Services\AlternativeAttribute;
use App\Services\RegisterFileUploaded;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FigureGroupRepository;
use App\Services\IllustrationsProperties;
use App\Repository\IllustrationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FigureController extends AbstractController
{
    private $figureRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FigureRepository $figureRepository,
        SluggerInterface $slugger,
        CommentRepository $commentRepository,
        IllustrationRepository $illustrationRepository,
        VideoRepository $videoRepository,
        FigureGroupRepository $figureGroupRepository,
        UniqueIdImage $uniqueIdImage,
        RegisterFileUploaded $registerFileUploaded
    ) {

        $this->entityManager = $entityManager;
        $this->figureRepository = $figureRepository;
        $this->slugger = $slugger;
        $this->commentRepository = $commentRepository;
        $this->illustrationRepository = $illustrationRepository;
        $this->videoRepository = $videoRepository;
        $this->figureGroupRepository = $figureGroupRepository;
        $this->uniqueIdImage = $uniqueIdImage;
        $this->registerFileUploaded = $registerFileUploaded;
    } 


/**
 * Permet de créer un trick
 *
 * @return Response
 *
 * @Route("tricks/new", name="newtrickPage")
 */
    public function create(Request $request, ImageOptimizer $imageOptimizer)
    {
        if($this->getUser()) {

            $codeYoutube = '';
            $groupTricks = $this->figureGroupRepository->findAll();
            $formTrick = $this->createForm(NewTrickType::class);
            $formTrick->handleRequest($request);

            if ($formTrick->isSubmitted() && $formTrick->isValid()) {
                $newTrick = $formTrick->getData();
                $newTrick->setAuthor($this->getUser());
                $newTrick->setfixture(0);
                $coverImage = $newTrick->getCoverImageFile();
                $alternativeAttribute = $newTrick->getAlternativeAttribute();

                if ($coverImage) {
                    $originalFilename = pathinfo($coverImage->getClientOriginalName(), PATHINFO_FILENAME);

                    $newFilename = $this->uniqueIdImage->generateUniqIdFileName($coverImage);

                    $imagesDirectory = $this->getParameter('images_directory'); 

                    $this->registerFileUploaded->registerFile($coverImage, $newFilename, $imagesDirectory);

                    $newTrick->setCoverImage($newFilename);

                    AlternativeAttribute::autoCompleteAttribute($newTrick, $originalFilename, $alternativeAttribute);
                } else {
                    $newTrick->setCoverImage('defaultCoverImage');
                    $newTrick->setAlternativeAttribute('Image de couverture par défaut');
                }

                $imagesCollection = $newTrick->getIllustrations();
                $videosCollection = $newTrick->getVideos();

                if ($imagesCollection) {
                    foreach ($imagesCollection as $objectIllustration) {
                        $image = $objectIllustration->getFileIllustration();

                        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

                        $newFilename = $this->uniqueIdImage->generateUniqIdFileName($image);
                        $objectIllustration->setUrlIllustration($newFilename);
                        $altAttrIllustration = $objectIllustration->getAlternativeAttribute();

                        AlternativeAttribute::autoCompleteAttribute(
                            $objectIllustration,
                            $originalFilename,
                            $altAttrIllustration
                        );

                        $fileIllustration = $objectIllustration->getFileIllustration();
                        $illustrationCollectionDirectory = $this->getParameter('illustrationsCollection_directory');

                        $this->registerFileUploaded->registerFile(
                            $fileIllustration,
                            $newFilename,
                            $illustrationCollectionDirectory
                        );

                        $objectIllustration->setUrlIllustration($newFilename);
                        $objectIllustration->setFigure($newTrick);
                        $objectIllustration->setFixture(0);
                        $this->entityManager->persist($objectIllustration);
                        $newTrick->addIllustration($objectIllustration);
                    }
                }

                if ($videosCollection) {
                    foreach ($videosCollection as $objectVideo) {
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

                //resizing coverImage
                $nameTrick = $newTrick->getCoverImage();
                $fileExtension = stristr(strtolower($nameTrick),'.');
                $nameTrickOnly = substr($nameTrick, 0, -strlen($fileExtension));
                $pathCoverImage = $this->getParameter('images_directory');
                $filename = $pathCoverImage.'/'.$nameTrickOnly.$fileExtension;
                $imageOptimizer->resize($filename);

                $this->addFlash('success','La figure a été créé avec succès !');
                return $this->redirectToRoute('homePage');
            }

            return $this->render(
                'core/figures/trickCreate.html.twig',
                ['formTrick' => $formTrick->createView(),'groupTricks' => $groupTricks]
            );

        } else {
            $this->addFlash('access_denied','Accés non authorisé !');
            return $this->redirectToRoute('homePage');
        }

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
    public function trickView($slug, Request $request)
    {

        $figure = $this->figureRepository->findOneBySlug($slug);
        // dump($figure);
        // exit;
        $figureId = $figure->getId();

        $comments = $this->commentRepository->getCommentsPagination($figureId, $page = 1);
        $paginator = $this->commentRepository->getCommentByLimit(1, Comment::LIMIT_PER_PAGE);
        $arrayIllustration = $this->illustrationRepository->findBy(['figure' => $figure]);
        $arrayImagesWithPropreties = IllustrationsProperties::generateProperties($arrayIllustration);
        $arrayVideo = $this->videoRepository->findBy(['figure' => $figure]);
        $arrayVideosWithProperties = VideosProperties::generateProperties($arrayVideo);
        $arrayMedias = array_merge($arrayImagesWithPropreties, $arrayVideosWithProperties);
        $formComment = $this->createForm(CommentType::class);
        $formComment->handleRequest($request);

        if ($formComment->isSubmitted() && $formComment->isValid()) {
            try {
                $newComment = $formComment->getData();
                $newComment->setFigure($figure);
                $newComment->setAuthor($this->getUser());
                $newComment->setFixture(0);
                $this->entityManager->persist($newComment);
                $this->entityManager->flush();
            } catch (Exception $e) {
                dump($e);
            }

            return $this->redirectToRoute('trickViewPage', ['slug' => $slug]);
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

    public function trickEdit($slug, Request $request, ImageOptimizer $imageOptimizer)
    {
        if($this->getUser()) {

            $figure = $this->figureRepository->findOneBySlug($slug);
            $comments = $this->commentRepository->findBy(['figure' => $figure]);

            $arrayIllustration = $this->illustrationRepository->findBy(['figure' => $figure]);
            $arrayImagesWithPropreties = IllustrationsProperties::generateProperties($arrayIllustration);

            //idem video

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
            $errorUploadMessage = '';

            if ($formEditTrick->isSubmitted() && $formEditTrick->isValid()) {
                try {
                    $nameTrick = $figure->getName();

                    $formTrick = $formEditTrick->getData();
                    $updateNameTrickField = $formTrick->getName();
                    $nameTrickSluger = $this->slugger->slug($updateNameTrickField);
                    $coverImageFile = $formTrick->getCoverImageFile();
                    $coverImageTrick = $formTrick->getCoverImage();
                    $alternativeAttribute = $formTrick->getAlternativeAttribute();
                    $descriptionfield = $formTrick->getDescription();
                    $figureGroupSelect = $formTrick->getFigureGroup();

                    //Define a unique entity for the name field in the figure edit form
                    //The name of the figure can also be its own, but cannot be identical to another figure.

                    $arrayListNameTricks = [];
                    $arrayListNameAllTricks = [];
                    $arrayTricks = $this->figureRepository->findAll();

                    foreach ($arrayTricks as $trick) {
                        $trickName = $trick->getName();
                        array_push($arrayListNameAllTricks, $trickName);
                    }

                    foreach ($arrayTricks as $trick) {
                        $trickName = $trick->getName();
                        array_push($arrayListNameTricks, $trickName);
                        if ($trickName === $updateNameTrickField) {
                            $indexUpdateName = array_search($updateNameTrickField, $arrayListNameTricks);
                        }
                    }

                    //generate array without the update name trick

                    if (isset($indexUpdateName)) {
                        unset($arrayListNameTricks[$indexUpdateName]);
                    }

                    if (
                        ($updateNameTrickField === $nameTrick &&
                        in_array($updateNameTrickField, $arrayListNameTricks) === false) ||
                        ($updateNameTrickField !== $nameTrick &&
                        in_array($updateNameTrickField, $arrayListNameAllTricks) === false)
                    ) {
                        if ($coverImageFile) {
                            $originalFilename = pathinfo($coverImageFile->getClientOriginalName(), PATHINFO_FILENAME);

                            $newFilename = $this->uniqueIdImage->generateUniqIdFileName($coverImageFile);
                            $imagesDirectory = $this->getParameter('images_directory');

                            $this->registerFileUploaded->registerFile($coverImageFile, $newFilename, $imagesDirectory);

                            $figure->setCoverImage($newFilename);
                            $figure->setAlternativeAttribute($originalFilename);
                            $figure->setFixture(0);
                        }

                        $codeYoutube = '';
                        $imagesCollection = $formTrick->getIllustrations();
                        $videosCollection = $formTrick->getVideos();
                        $arrayObjectIllustration = [];

                        if ($imagesCollection) {
                            foreach ($imagesCollection as $objectIllustration) {
                                $image = $objectIllustration->getFileIllustration();

                                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                                    $newFilename = $this->uniqueIdImage->generateUniqIdFileName($image);
                                    $altAttrIllustration = $objectIllustration->getAlternativeAttribute();

                                    AlternativeAttribute::autoCompleteAttribute(
                                        $objectIllustration,
                                        $originalFilename,
                                        $altAttrIllustration
                                    );

                                    $objectIllustration->setUrlIllustration($newFilename);
                                    $fileIllustration = $objectIllustration->getFileIllustration();
                                    $illustrationCollectionDirectory = $this->getParameter(
                                        'illustrationsCollection_directory'
                                    );
                                    $this->registerFileUploaded->registerFile(
                                        $fileIllustration,
                                        $newFilename,
                                        $illustrationCollectionDirectory
                                    );

                                    $objectIllustration->setUrlIllustration($newFilename);
                                    $objectIllustration->setFigure($figure);
                                    $objectIllustration->setFixture(0);

                                    $this->entityManager->persist($objectIllustration);

                                    $figure->addIllustration($objectIllustration);

                                    array_push($arrayObjectIllustration, $objectIllustration);
                            }
                        }

                        $arrayObjectVideo = [];

                        if ($videosCollection) {
                            foreach ($videosCollection as $objectVideo) {
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
                                }
                            }
                        }

                        $arrayMedias = array_merge($arrayObjectIllustration, $arrayObjectVideo);

                        $fixtureDefinition = $figure->getFixture();

                        $figure->setName($updateNameTrickField);

                        $figure->setSlug($nameTrickSluger);
                        $figure->setDescription($descriptionfield);
                        $figure->setFigureGroup($figureGroupSelect);
                        $figure->setFixture($fixtureDefinition);
                        $this->entityManager->persist($figure);
                        $this->entityManager->flush();

                        //resizing coverImage
                        $nameTrick = $figure->getCoverImage();
                        $fileExtension = stristr(strtolower($nameTrick),'.');
                        $nameTrickOnly = substr($nameTrick, 0, -strlen($fileExtension));
                        $pathCoverImage = $this->getParameter('images_directory');
                        $filename = $pathCoverImage.'/'.$nameTrickOnly.$fileExtension;
                        $imageOptimizer->resize($filename);

                        $this->addFlash('success','La figure a été modifié avec succès !');
                        $newSlug = $figure->getSlug();
                        return $this->redirectToRoute('trickViewPage', ['slug' => $newSlug]);


                    } else {
                        $messageError = 'Le nom de la figure est déjà existant';
                        return $this->render(
                            'core/figures/trickEdit.html.twig',
                            ['figure' => $figure,
                            'comments' => $comments,
                            'arrayMedias' => $arrayMedias,
                            'formEditTrick' => $formEditTrick->createView(),
                            'messageError' => $messageError ,
                            'error' => true ]
                        );
                    }
                } catch (Exception $e) {
                    dump($e);
                }
            }

            return $this->render(
                'core/figures/trickEdit.html.twig',
                ['figure' => $figure,
                'comments' => $comments,
                'arrayMedias' => $arrayMedias,
                'formEditTrick' => $formEditTrick->createView(),
                'messageError' => $messageError ,
                'error' => false ]
            );

        } else {
            $this->addFlash('access_denied','Accés non authorisé !');
            return $this->redirectToRoute('homePage');
        }
    }


    /**
     * trick delete video
     *
     * @Route("/tricks/{slug}/edit/updateCoverImage", name="trickUpdateCoverImage")
     */

    public function trickUdapteCoverImage($slug, Request $request)
    {
        if($this->getUser()) {
            $figure = $this->figureRepository->findOneBySlug($slug);
            $formUpdateCoverImage = $this->createForm(UpdateCoverImageType::class);
            $formUpdateCoverImage->handleRequest($request);

            if ($formUpdateCoverImage->isSubmitted() && $formUpdateCoverImage->isValid()) {
                try {
                    $coverImageFile = $formUpdateCoverImage->get('coverImage')->getData();
                    $originalFilename = pathinfo($coverImageFile->getClientOriginalName(), PATHINFO_FILENAME);

                    $newFilename = $this->uniqueIdImage->generateUniqIdFileName($coverImageFile);
                    $imagesDirectory = $this->getParameter('images_directory');

                    $this->registerFileUploaded->registerFile($coverImageFile, $newFilename, $imagesDirectory);

                    $figure->setCoverImage($newFilename);
                    $figure->setAlternativeAttribute($originalFilename);
                    $figure->setFixture(0);

                    $this->entityManager->persist($figure);

                    $this->entityManager->flush();
                    $this->addFlash('success','L\'image de couverture a été modifié avec succès !');
                    return $this->redirectToRoute('trickEditPage', ['slug' => $slug]);

                } catch (Exception $e) {
                    dump($e);
                }
            }

            return $this->render(
                'core/figures/updateCoverImage.html.twig',
                ['slug' => $slug,
                'formUpdateCoverImage' => $formUpdateCoverImage->createView()]
            );

        } else {
            $this->addFlash('access_denied','Accés non authorisé !');
            return $this->redirectToRoute('homePage');
        }          

    }


    /**
     * trick delete
     *
     * @Route("/tricks/{slug}/delete", name="trickDeletePage")
     */

    public function trickDelete($slug)
    {
        if($this->getUser()) {
            $currentTrick = $this->figureRepository->findOneBySlug($slug);
            $idTrick = $currentTrick->getId();
            $arrayIllustrations = $this->illustrationRepository->findByFigure($idTrick);

            foreach ($arrayIllustrations as $objectIllustration) {
                $fileName = $objectIllustration->getUrlIllustration();

                $stateFixtureIllustration = $objectIllustration->getFixture();

                //Delete the illustration image file stored on the server

                if ($stateFixtureIllustration === false) {
                    $pathIllustrationsCollection = $this->getParameter('illustrationsCollection_directory');

                    DeleteImageStored::deleteImage($fileName, $pathIllustrationsCollection);
                }
            }

            //Delete the cover image file stored on the server

            $fileNameCoverImage = $currentTrick->getCoverImage();

            $stateFixtureCurrentTrick = $currentTrick->getFixture();

            if ($stateFixtureCurrentTrick === false && $fileNameCoverImage !== "defaultCoverImage") {
                $pathCoverImage = $this->getParameter('images_directory');
                DeleteImageStored::deleteImage($fileNameCoverImage, $pathCoverImage); 
            }

            $this->entityManager->remove($currentTrick);
            $this->entityManager->flush();
            $this->addFlash('success','La figure a été supprimé avec succès !');
            return $this->redirectToRoute('homePage');

        } else {
            $this->addFlash('access_denied','Accés non authorisé !');
            return $this->redirectToRoute('homePage');
        }
    }



    /**
     * Updating a media of a trick
     *
     * @Route("/tricks/{slug}/edit/medias/{id}", name="trickEditMediasPage")
     */

    public function trickEditMedias($slug, $id, Request $request)
    {
        if($this->getUser()) {
            $typeMedia = $request->query->get('type');
            $currentfigure = $this->figureRepository->findOneBySlug($slug);
            $currentIllustration = $this->illustrationRepository->findOneById($id);
            $currentVideo = $this->videoRepository->findOneById($id);
            $codeYoutube = '';

            if ($typeMedia === 'image') {
                $formEditMediasTrick = $this->createForm(EditOneIllustrationType::class);
                $formEditMediasTrick->handleRequest($request);

                if ($formEditMediasTrick->isSubmitted() && $formEditMediasTrick->isValid()) {
                    //delete file Illustration stored
                    $fileName = $currentIllustration->getUrlIllustration();
                    $pathIllustrationsCollection = $this->getParameter('illustrationsCollection_directory');
                    DeleteImageStored::deleteImage($fileName, $pathIllustrationsCollection);

                    // add File Illustration
                    $formData = $formEditMediasTrick->getData();
                    $illustration = $formData->getFileIllustration();

                    $originalFilename = pathinfo($illustration, PATHINFO_FILENAME);

                    $formData = $formEditMediasTrick->getData();
                    $illustration = $formData->getFileIllustration();

                    $originalFilename = pathinfo($illustration, PATHINFO_FILENAME);
                    $imageUploaded  = $formEditMediasTrick->get('fileIllustration')->getData();
                    $originalFilename = pathinfo($imageUploaded->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $this->uniqueIdImage->generateUniqIdFileName($imageUploaded);
                    $illustrationsCollectionDirectory = $this->getParameter('illustrationsCollection_directory');

                    $this->registerFileUploaded->registerFile(
                        $imageUploaded,
                        $newFilename,
                        $illustrationsCollectionDirectory
                    );

                    // register url Illustration
                    $currentIllustration->setUrlIllustration($newFilename);
                    $currentIllustration->setAlternativeAttribute($originalFilename);
                    $currentIllustration->setFixture(0);
                    $currentIllustration->setFigure($currentfigure);
                    $this->entityManager->persist($currentIllustration);

                    $currentfigure->addIllustration($currentIllustration);
                    $this->entityManager->persist($currentfigure);
                    $this->entityManager->flush();
                    $this->addFlash('successCollection','L\'illustration a été modifié avec succès !');
                    return $this->redirectToRoute('trickEditPage', ['slug' => $slug]);
                }

                return $this->render(
                    'core/figures/trickEditIllustration.html.twig',
                    ['formEditMediasTrick' => $formEditMediasTrick->createView(),'currentfigure' => $currentfigure]
                );
            } else {
                $formEditMediasTrick = $this->createForm(EditOneVideoType::class);
                $formEditMediasTrick->handleRequest($request);

                if ($formEditMediasTrick->isSubmitted() && $formEditMediasTrick->isValid()) {
                    $objectVideo = $formEditMediasTrick->getData();
                    $urlVideo = $objectVideo->getUrlVideo();

                    try {
                        $codeYoutube = Youtube::typeUrl($urlVideo);
                        $currentVideo->setUrlVideo($codeYoutube);
                        $currentVideo->setFigure($currentfigure);
                        $this->entityManager->persist($currentVideo);
                        $this->entityManager->flush($currentVideo);
                        $currentfigure->addVideo($currentVideo);
                        $this->entityManager->persist($currentfigure);
                        $this->entityManager->flush($currentfigure);
                        $this->addFlash('successCollection','La video a été modifié avec succès !');
                        return $this->redirectToRoute('trickEditPage', ['slug' => $slug]);

                    } catch (FileException $e) {
                        dump($e);
                    }
                }

                return $this->render(
                    'core/figures/trickEditVideo.html.twig',
                    ['formEditMediasTrick' => $formEditMediasTrick->createView(),
                    'currentfigure' => $currentfigure]
                );
            }
        } else {
            $this->addFlash('access_denied','Accés non authorisé !');
            return $this->redirectToRoute('homePage');
        }
    }


    /** Deleting a media from a trick */

    /**
     * trick delete illustration
     *
     * @Route("/tricks/{slug}/delete/illustration/{id}", name="trickDeleteIllustrationPage")
     */

    public function trickDeleteIllustration($slug, $id)
    {
        if($this->getUser()) {
            $currentIllustration = $this->illustrationRepository->findOneById($id);
            $fileName = $currentIllustration->getUrlIllustration();
            $this->entityManager->remove($currentIllustration);
            $this->entityManager->flush();
            $pathIllustrationsCollection = $this->getParameter('illustrationsCollection_directory');
            DeleteImageStored::deleteImage($fileName, $pathIllustrationsCollection);
            $this->addFlash('successCollection','L\'illustration a été supprimé avec succès !');
            return $this->redirectToRoute('trickEditPage', ['slug' => $slug]);
        } else {
            $this->addFlash('access_denied','Accés non authorisé !');
            return $this->redirectToRoute('homePage');
        }
    }

    /**
     * trick delete video
     *
     * @Route("/tricks/{slug}/delete/video/{id}", name="trickDeleteVideoPage")
     */

    public function trickDeleteVideo($slug, $id)
    {
        if($this->getUser()) {
            $currentIdVideo = $this->videoRepository->findOneById($id);
            $this->entityManager->remove($currentIdVideo);
            $this->entityManager->flush();
            $this->addFlash('successCollection','La vidéo a été supprimé avec succès !');
            return $this->redirectToRoute('trickEditPage', ['slug' => $slug]);
        } else {
            $this->addFlash('access_denied','Accés non authorisé !');
            return $this->redirectToRoute('homePage');
        }

    }

    /**
     * trick delete cover image
     *
     * @Route("/tricks/{slug}/edit/deleteCoverImage", name="trickDeleteCoverImage")
     */

    public function trickDeleteCoverImage($slug)
    {
        if($this->getUser()) {

            $figure = $this->figureRepository->findOneBySlug($slug);
            $currentCoverImage = $figure->getCoverImage();

            if ($figure->getFixture() === false) {
                $pathCoverImage = $this->getParameter('images_directory');
                DeleteImageStored::deleteImage($currentCoverImage, $pathCoverImage);
            }

            $figure->setCoverImage('defaultCoverImage');
            $figure->setAlternativeAttribute('image par defaut');
            $figure->setFixture(0);
            $this->entityManager->persist($figure);
            $this->entityManager->flush();
            $this->addFlash('success','L\'image de couverture a été supprimé avec succès !');
            return $this->redirectToRoute('trickEditPage', ['slug' => $slug]);
        } else {
            $this->addFlash('access_denied','Accés non authorisé !');
            return $this->redirectToRoute('homePage');
        }
    }
}
