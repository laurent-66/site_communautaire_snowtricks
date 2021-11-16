<?php
namespace App\DataFixtures;

use App\Entity\FigureGroup;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class FigureGroupFixture extends Fixture
{
    public const FIG_GRP_REF = 'fig-grp-ref';

    public function load(ObjectManager $manager)
    {
        // create 20 products! Bam!
        for ($i = 0; $i < 20; $i++) {
            $figureGroup = new FigureGroup();
            $figureGroup->setName('name');

            $manager->persist($figureGroup);
        }

        $manager->flush();
        $this->addReference(self::FIG_GRP_REF, $figureGroup);
    }

}