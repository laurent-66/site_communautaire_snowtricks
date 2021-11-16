<?php
namespace App\DataFixtures;

use App\Entity\Figure;
use App\DataFixtures\UserFixture;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\FigureGroupFixture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class Figurefixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // this reference returns the User object created in UserFixture
        $author = $this->getReference(UserFixture::USER_REF);

        // this reference returns the FigureGroup object created in FigureGroupFixture
        $figureGroup = $this->getReference(FigureGroupFixture::FIG_GRP_REF);

        // create 20 products! Bam!
        for ($i = 0; $i < 20; $i++) {
            $figure = new Figure();
            $figure->setName('pseudo:'.$i);
            $figure->setDescription('');
            $figure->setAuthor($author);
            $figure->setFigureGroup($figureGroup);

            $manager->persist($figure);
        }

        $manager->flush();
    }


    public function getDependencies()
    {
        return [
            UserFixture::class,
            FigureGroupFixture::class
        ];
    }
}