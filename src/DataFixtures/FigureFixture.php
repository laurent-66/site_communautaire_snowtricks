<?php
namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Figure;
use App\DataFixtures\UserFixture;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\FigureGroupFixture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FigureFixture extends Fixture implements DependentFixtureInterface
{
    public const FIG_REF = 'fig-ref';

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');
        // this reference returns the User object created in UserFixture
        $author = $this->getReference(UserFixture::USER_REF);

        // this reference returns the FigureGroup object created in FigureGroupFixture
        $figureGroup = $this->getReference(FigureGroupFixture::FIG_GRP_REF);

        //intance slugger
        $slugger = new AsciiSlugger();

        for ($i = 0; $i < 20; $i++) {

            $titleFigure = $faker->sentence($nbWords = 10, $variableNbWords = true);
            $slug = $slugger->slug($titleFigure);
            $description = $faker->sentence($nbWords = 20, $variableNbWords = true);

            $coverImage = "https://picsum.photos/1000/350";
            // $coverImage = $faker->imageUrl(1000,350);

            $figure = new Figure();
            $figure->setName($titleFigure);
            $figure->setSlug($slug);
            $figure->setDescription($description);
            $figure->setCoverImage($coverImage);
            $figure->setAuthor($author);
            $figure->setFigureGroup($figureGroup);

            $manager->persist($figure);
        }

        $manager->flush();
        $this->addReference(self::FIG_REF, $figure);
    }


    public function getDependencies()
    {
        return [
            UserFixture::class,
            FigureGroupFixture::class
        ];
    }
}