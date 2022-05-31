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
                'name'=>'Indy grab',
                'description'=>'Description Indy grab',
                'Cover_image'=>'Indy-grab.JPG',
                'alternative_attribute'=>'Indy-grab',
                'figure_group'=> 0,
                'pseudo_id'=> 0,
                'created_at'=> '2022-05-11 10:53:43',
                'updated_at'=> '2022-05-11 10:53:43',
                'fixture'=> 1
            
            ],
            [
                'name'=>'Mute grab',
                'description'=>'Description mute grab',
                'Cover_image'=>'mutegrab.jpg',
                'alternative_attribute'=>'mute-grab',
                'figure_group'=> 2,
                'pseudo_id'=> 1,
                'created_at'=> '2022-05-11 10:53:43',
                'updated_at'=> '2022-05-11 10:53:43', 
                'fixture'=> 1
            
            ],
            [
                'name'=>'Stalefish grab',
                'description'=>'Description stalefish',
                'Cover_image'=>'Stalefish-Grab.jpg',
                'alternative_attribute'=>'stalefish-grab',
                'figure_group'=> 6,
                'pseudo_id'=> 2,
                'created_at'=> '2022-05-11 10:53:43',
                'updated_at'=> '2022-05-11 10:53:43',
                'fixture'=> 1
            
            ]
            ];

        for($i = 0 ; $i < count($dataSnowTrickCollection) ; $i++ ) {

            $figGroupRandom = rand(0,9);
            $userRandom = rand(0,2);

            $figure = new Figure();

            $figure->setName($dataSnowTrickCollection[$i]['name']);
            $figure->setSlug($this->slugger->slug($dataSnowTrickCollection[$i]['name']));
            $figure->setDescription($dataSnowTrickCollection[$i]['description']);
            $figure->setCoverImage($dataSnowTrickCollection[$i]['Cover_image']);
            $figure->setAlternativeAttribute($dataSnowTrickCollection[$i]['alternative_attribute']);
            $figure->setFigureGroup($this->getReference('fig-grp-ref_'.$dataSnowTrickCollection[$i]['figure_group']));
            $figure->setAuthor($this->getReference('user_'.$userRandom));
            $figure->setCreatedAt(new \Datetime($dataSnowTrickCollection[$i]['created_at']));
            $figure->setCreatedAt(new \Datetime($dataSnowTrickCollection[$i]['updated_at']));
            $figure->setFixture(1);

            $manager->persist($figure); 
            $manager->flush();
            $this->addReference(sprintf(self::FIG_REF, $i), $figure); 
        }

    }

    public function getDependencies()
    {
        return [
            UserFixture::class,
            FigureGroupFixture::class
        ];
    }
}