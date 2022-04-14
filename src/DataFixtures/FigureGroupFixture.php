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

        $datasGroupTricks = ['Indy', 'Japan','Mute', 'Nose grab', 'Sad', 'Seat belt', 'stalefish', 'tail grab', 'Truck driver'];

        foreach ($datasGroupTricks as $itemGroup) {

            $figureGroup = new FigureGroup();
            $figureGroup->setName($itemGroup);

            $manager->persist($figureGroup);

            $this->addReference(self::FIG_GRP_REF.$itemGroup, $figureGroup);
        }

        $manager->flush();


    }

}