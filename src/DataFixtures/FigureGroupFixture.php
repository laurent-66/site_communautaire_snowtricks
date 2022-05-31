<?php
namespace App\DataFixtures;

use App\Entity\FigureGroup;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class FigureGroupFixture extends Fixture
{

    public const FIG_GRP_REF = 'fig-grp-ref_%s';

    public function load(ObjectManager $manager)
    {

        $datasGroupTricks = ['Indy', 'Japan','Mute', 'Nose grab', 'Sad', 'Seat belt', 'stalefish', 'tail grab', 'Truck driver'];

        for($i = 0 ; $i < count($datasGroupTricks) ; $i++ ) {

            $figureGroup = new FigureGroup();
            $figureGroup->setName($datasGroupTricks[$i]);
            $manager->persist($figureGroup);
            $manager->flush();
            $this->addReference(sprintf(self::FIG_GRP_REF, $i), $figureGroup);
        }
    }
}