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

        for ($i = 0; $i < 10; $i++) {

            $alternativeAttribute = $faker->sentence($nbWords = 2, $variableNbWords = true);

            $illustration = new Illustration();

            $figRandom = rand(0,2);

            $listPictures = file_get_contents('https://picsum.photos/v2/list');
            $urlIllustration = json_decode($listPictures, true)[$i]["download_url"];

            $illustration->setUrlIllustration($urlIllustration);

            $illustration->setFigure($this->getReference('fig-ref_'.$figRandom ));
            $illustration->setAlternativeAttribute($alternativeAttribute);
            $illustration->setFixture(1);
            $manager->persist($illustration);
            $manager->flush();

        }


    }

    public function getDependencies()
    {
        return [
            FigureFixture::class
        ];
    }

}