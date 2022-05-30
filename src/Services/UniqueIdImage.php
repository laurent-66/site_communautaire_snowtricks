<?php 

namespace App\Services;

use Symfony\Component\String\Slugger\SluggerInterface;

class UniqueIdImage {

    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function generateUniqIdFileName($image) {

        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFilename = $this->slugger->Slug($originalFilename);

        $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

        return $newFilename;
    } 

} 