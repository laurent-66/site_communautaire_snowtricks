<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\FigureGroupFixture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FigureFixture extends Fixture implements DependentFixtureInterface
{
    public const FIG_REF = 'fig-ref_%s';
    private $slugger;
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {

        //fixtures Figures

        $dataSnowTrickCollection = [
            [
                'name' => 'Indy grab',
                'description' =>
                'saisie de la carre frontside de la planche,
                 entre les deux pieds, avec la main arrière ',
                'Cover_image' => 'Indy-grab800x600.jpg',
                'alternative_attribute' => 'Indy-grab',
                'figure_group' => 0,
                'pseudo_id' => 0,
                'created_at' => '2022-05-11 10:53:43',
                'updated_at' => '2022-05-11 10:53:43',
                'fixture' => 1
            ],
            [
                'name' => 'Japan air grab',
                'description' =>
                'saisie de l\'avant de la planche,avec la main
                 avant, du côté de la carre frontside.',
                'Cover_image' => 'Japan-air-grab800x600.jpg',
                'alternative_attribute' => 'Japan air grab',
                'figure_group' => 1,
                'pseudo_id' => 0,
                'created_at' => '2022-05-11 10:53:43',
                'updated_at' => '2022-05-11 10:53:43',
                'fixture' => 1
            ],
            [
                'name' => 'Mute grab',
                'description' =>
                'saisie de la carre frontside de la planche
                 entre les deux pieds avec la main avant',
                'Cover_image' => 'mute-grab800x600.jpg',
                'alternative_attribute' => 'mute grab',
                'figure_group' => 2,
                'pseudo_id' => 1,
                'created_at' => '2022-05-11 10:53:43',
                'updated_at' => '2022-05-11 10:53:43',
                'fixture' => 1
            ],
            [
                'name' => 'Nose grab',
                'description' => 'saisie de la partie 
                avant de la planche, avec la main avant',
                'Cover_image' => 'Nose-grab800x600.jpg',
                'alternative_attribute' => 'nose grab',
                'figure_group' => 3,
                'pseudo_id' => 2,
                'created_at' => '2022-05-11 10:53:43',
                'updated_at' => '2022-05-11 10:53:43',
                'fixture' => 1
            ],
            [
                'name' => 'Sad ou melancholie',
                'description' =>
                'saisie de la carre backside de la planche, entre les deux pieds, 
                avec la main avant. Le rider est en position goofy.',
                'Cover_image' => 'Sad-melancholie800x600.jpg',
                'alternative_attribute' => 'sad ou melancholie',
                'figure_group' => 4,
                'pseudo_id' => 1,
                'created_at' => '2022-05-11 10:53:43',
                'updated_at' => '2022-05-11 10:53:43',
                'fixture' => 1
            ],
            [
                'name' => 'Seat belt',
                'description' => 'saisie du carre frontside à l\'arrière avec la main avant',
                'Cover_image' => 'Seatbelt800x600.jpg',
                'alternative_attribute' => 'Seat belt',
                'figure_group' => 5,
                'pseudo_id' => 1,
                'created_at' => '2022-05-11 10:53:43',
                'updated_at' => '2022-05-11 10:53:43',
                'fixture' => 1
            ],
            [
                'name' => 'Stalefish',
                'description' =>
                'saisie de la carre backside de la planche entre les deux pieds avec la main arrière,
                 sur cette image le rider est en position regular (son pied gauche est à l\'avant).',
                'Cover_image' => 'stalefish800x600.jpg',
                'alternative_attribute' => 'stalefish',

                'figure_group' => 6,
                'pseudo_id' => 2,
                'created_at' => '2022-05-11 10:53:43',
                'updated_at' => '2022-05-11 10:53:43',
                'fixture' => 1
            ],
            [
                'name' => 'Tail grab',
                'description' =>
                'saisie de la partie arrière de la planche, avec la main arrière. 
                Le rider est ici en position goofy.',
                'Cover_image' => 'tail-grab800x600.jpg',
                'alternative_attribute' => 'tail grab',
                'figure_group' => 7,
                'pseudo_id' => 0,
                'created_at' => '2022-05-11 10:53:43',
                'updated_at' => '2022-05-11 10:53:43',
                'fixture' => 1
            ],
            [
                'name' => 'Truck driver',
                'description' =>
                'saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture)',
                'Cover_image' => 'truck-driver800x600.jpg',
                'alternative_attribute' => 'Truck driver',
                'figure_group' => 8,
                'pseudo_id' => 1,
                'created_at' => '2022-05-11 10:53:43',
                'updated_at' => '2022-05-11 10:53:43',
                'fixture' => 1
            ],
            [
                'name' => 'Tail grab avec front flip',
                'description' =>
                'Le rider en position regular, effectue un front flip en saisissant la partie arrière de la planche,
                 avec la main arrière.',
                'Cover_image' => 'front-flip800x600.jpg',
                'alternative_attribute' => 'Tail grab avec front flip',
                'figure_group' => 7,
                'pseudo_id' => 2,
                'created_at' => '2022-05-11 10:53:43',
                'updated_at' => '2022-05-11 10:53:43',
                'fixture' => 1
            ],
            ];

        for ($i = 0; $i < count($dataSnowTrickCollection); $i++) {
            $userRandom = rand(0, 2);
            $figure = new Figure();
            $figure->setName($dataSnowTrickCollection[$i]['name']);
            $figure->setSlug($this->slugger->slug($dataSnowTrickCollection[$i]['name'])->lower());
            $figure->setDescription($dataSnowTrickCollection[$i]['description']);
            $figure->setCoverImage($dataSnowTrickCollection[$i]['Cover_image']);
            $figure->setAlternativeAttribute($dataSnowTrickCollection[$i]['alternative_attribute']);
            $figure->setFigureGroup($this->getReference('fig-grp-ref_' . $dataSnowTrickCollection[$i]['figure_group']));
            $figure->setAuthor($this->getReference('user_' . $userRandom));
            $figure->setCreatedAt(new \Datetime($dataSnowTrickCollection[$i]['created_at']));
            $figure->setCreatedAt(new \Datetime($dataSnowTrickCollection[$i]['updated_at']));
            $figure->setFixture(1);
            $manager->persist($figure);
            $manager->flush();
            $this->addReference(sprintf(self::FIG_REF, $i), $figure);
        }
        // dump($this->getReference('fig-ref_1'));

    }

    public const FIG_REF_TEST = 'fig-ref_1';
    

    public function getDependencies()
    {
        return [
            UserFixture::class,
            FigureGroupFixture::class
        ];
    }
}
