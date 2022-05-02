<?php
namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Figure;
use App\Entity\Comment;
use App\DataFixtures\UserFixture;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\FigureGroupFixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class FigureFixture extends Fixture implements DependentFixtureInterface
{
    public const FIG_REF = 'fig-ref_%s';

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        $slugger = new AsciiSlugger();

        for ($i = 0; $i < 10; $i++) {

            $titleFigure = $faker->sentence($nbWords = 3, $variableNbWords = true);
            $slug = $slugger->slug($titleFigure);
            $description = $faker->sentence($nbWords = 20, $variableNbWords = true);
            $alternativeAttribute = $faker->sentence($nbWords = 2, $variableNbWords = true);

            $figure = new Figure();
            $figureGroupRefRandom = rand(0,8);
            $authorRandom = rand(0,9);          
            $figure->setName($titleFigure);
            $figure->setSlug($slug);
            $figure->setDescription($description);

            $listPictures = file_get_contents('https://picsum.photos/v2/list');
            $coverImage = json_decode($listPictures, true)[$i]["download_url"];
            
            $figure->setCoverImage($coverImage);
            $figure->setAlternativeAttribute($alternativeAttribute);
            $figure->setFixture(1);
            $figure->setAuthor($this->getReference('user_'.$authorRandom  ));
            $figure->setFigureGroup($this->getReference('fig-grp-ref_'.$figureGroupRefRandom));
            $manager->persist($figure); 
            $manager->flush();
            $this->addReference(sprintf(self::FIG_REF, $i), $figure); 
        }

    }


    public function getDependencies()
    {
        return [
            UserFixture::class,
            FigureGroupFixture::class
        ];
    }
}