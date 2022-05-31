<?php

namespace App\Helpers;

use App\Repository\FigureRepository;
use App\Repository\IllustrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class MediaIllustrations extends AbstractController
{
    public function __construct(
        SluggerInterface $slugger, 
        EntityManagerInterface $entityManager,
        FigureRepository $figureRepository,
        IllustrationRepository $illustrationRepository
        
        )
    {
        $this->slugger = $slugger;
        $this->entityManager = $entityManager;
        $this->figureRepository = $figureRepository;
        $this->illustrationRepository = $illustrationRepository;

    }


    public function add( string $slug, array $imagesCollection)
    {
        $currentfigure = $this->figureRepository->findOneBySlug($slug); 

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


    public function delete($slug, $id) {
    
        $currentIllustration = $this->illustrationRepository->findOneById($id);
        $fileName = $currentIllustration->getUrlIllustration();
        $this->entityManager->remove($currentIllustration);
        $this->entityManager->flush();

        $pathIllustrationsCollection = $this->getParameter('illustrationsCollection_directory');
        $filePath = $pathIllustrationsCollection."/".$fileName;
        unlink($filePath);
        
        return $this->redirectToRoute('trickEditPage', ['slug'=> $slug]);

    }
}