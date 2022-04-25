<?php
namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Illustration;
use App\DataFixtures\FigureFixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class IllustrationFixture extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');
        // this reference returns the Figure object created in FigureFixture
        $figure = $this->getReference(FigureFixture::FIG_REF);

        // create 20 products! Bam!
        for ($i = 0; $i < 15; $i++) {

            // $urlIllustration = $faker->imageUrl(1000,350);
            $alternativeAttribute = $faker->sentence($nbWords = 2, $variableNbWords = true);

            // $urlIllustration = $faker->imageUrl(1000,350);
            $alternativeAttribute = $faker->sentence($nbWords = 2, $variableNbWords = true);

            // $urlIllustration = $faker->imageUrl(500, 250);
            $illustration = new Illustration();

            $listPictures = file_get_contents('https://picsum.photos/v2/list');
            $urlIllustration = json_decode($listPictures, true)[$i]["download_url"];

            $illustration->setUrlIllustration($urlIllustration);
            $illustration->setFigure($figure);
            $illustration->setAlternativeAttribute($alternativeAttribute);
            $illustration->setFixture(1);

            $manager->persist($illustration);
        }

        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            FigureFixture::class
        ];
    }

}