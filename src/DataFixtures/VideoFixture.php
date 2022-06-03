<?php
namespace App\DataFixtures;

use App\Entity\Video;
use App\DataFixtures\FigureFixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class VideoFixture extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {

        // Option to load a specialized fixture with a preselection of youtube video on the snowtrick

        // list link video

        // https://youtu.be/Ey5elKTrUCk
        // https://youtu.be/2RjS4-T7IdU
        // https://youtu.be/SQyTWk7OxSI
        // https://youtu.be/nHMjX8RL3Oc
        // https://youtu.be/QMrelVooJR4
        // https://youtu.be/de6DOa1C380 
        // https://youtu.be/V9xuy-rVj9w 
        // https://youtu.be/NQ1MERtpFLQ
        // https://youtu.be/2vNVpnjVsYg
        // https://youtu.be/gbHU6J6PRRw



        $datasYoutube = [
            'Ey5elKTrUCk',
            '2RjS4-T7IdU',
            'SQyTWk7OxSI',
            'nHMjX8RL3Oc', 
            'QMrelVooJR4', 
            'de6DOa1C380', 
            'V9xuy-rVj9w', 
            'NQ1MERtpFLQ', 
            '2vNVpnjVsYg',
            'gbHU6J6PRRw'
            ];

        for($i = 0 ; $i < count($datasYoutube) ; $i++ ) {
            $video = new Video();
            $figRandom = rand(0,9);
            $video->setFigure($this->getReference('fig-ref_'.$figRandom ));
            $video->setUrlVideo($datasYoutube[$i]);
            $manager->persist($video);
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