<?php

namespace App\Controller;


use App\Form\NewTrickType;
use App\Entity\Illustration;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\IllustrationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class IllustrationController extends AbstractController
{

    private  $figureRepository;

    public function __construct(IllustrationRepository $illustrationRepository)
    {
        $this->illustrationRepository = $illustrationRepository;
    }


/**
 * Permet de créer un trick
 *
 * @return Response
 * 
 * @Route("tricks/{slug}/illustration/new", name="newCollectionIllustration")
 */
    public function create(
        $slug,
        Request $request, 
        FigureRepository $figureRepository,
        EntityManagerInterface $entityManager,
        IllustrationRepository $illustrationRepository,
        SluggerInterface $slugger
        ){

        $this->entityManager = $entityManager;
        $this->illustration = new Illustration();

        //je récupère la figure qui correspond au slug
        $currentTrick = $figureRepository->findOneBySlug($slug);

        //création du formulaire avec les propriétées de l'entitée Comment
        $formIllustration = $this->createForm(Illustration::class, $this->illustration);

        //renseigne l'instance $user des informations entrée dans le formulaire et envoyé dans la requête
        $formIllustration->handleRequest($request); 


        if($formIllustration->isSubmitted() && $formIllustration->isValid()) {
            $newIllustration = $formIllustration->getData();

            //chargement et enregistrement de la collection d'images

            //Définition de la collection d'objets illustration 

            $imagesCollection = $currentTrick->get('illustrations')->getData();


            //// A faire création du formtype d'ajout d'illustration ///


            ///////// A vérifier ////////////


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
                        $objectIllustration->setFigure($currentTrick);

                        //persistance de l'instance illustration
                        $this->entityManager->persist($objectIllustration);

                        //enregistrement des illustrations dans l'instance de l'object figure
                        $currentTrick->addIllustration($objectIllustration);


                    } catch (FileException $e) {
                        dump($e);
                    }

                }

            }

            //persistance de l'illustration
            $this->entityManager->persist($newIllustration);

            $this->entityManager->flush();

            //Redirection
            return $this->redirectToRoute('homePage');
        }

        return $this->render('core/figures/addCollectionImage.html.twig', ['formIllustration' => $formIllustration->createView()]);
    }

    
}