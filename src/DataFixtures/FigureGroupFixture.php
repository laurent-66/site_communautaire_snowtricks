<?php
namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\FigureGroup;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class FigureGroupFixture extends Fixture
{
    public const FIG_GRP_REF = 'fig-grp-ref';

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');
        // create 20 products! Bam!
        for ($i = 0; $i < 20; $i++) {

            $titleGrpFigure = $faker->word;

            $figureGroup = new FigureGroup();
            $figureGroup->setName($titleGrpFigure);

            $manager->persist($figureGroup);
        }

        $manager->flush();
        $this->addReference(self::FIG_GRP_REF, $figureGroup);
    }

}