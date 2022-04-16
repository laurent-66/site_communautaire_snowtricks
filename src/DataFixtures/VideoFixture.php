<?php
namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Video;
use App\DataFixtures\FigureFixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class VideoFixture extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');
        // this reference returns the Figure object created in FigureFixture
        $figure = $this->getReference(FigureFixture::FIG_REF);

        // create 20 products! Bam!
        for ($i = 0; $i < 20; $i++) {

            $urlVideo = $faker->imageUrl(500, 250);

            $video = new Video();
            $video->setUrlVideo($urlVideo);
            $video->setFigure($figure);

            $manager->persist($video);
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