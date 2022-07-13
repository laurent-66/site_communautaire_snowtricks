<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Comment;
use App\DataFixtures\UserFixture;
use App\Repository\FigureRepository;
use App\Repository\CommentRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixture extends Fixture implements DependentFixtureInterface
{
    public function __construct(FigureRepository $figureRepository)
    {
        $this->figureRepository = $figureRepository;
    }
    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr-FR');
        $datetime = new DateTime();

        for ($i = 0; $i < 9; $i++) {

            $authorRefRandom = rand(0, 2);
            $figRandom = rand(0, 9);
            $content = $faker->sentence($nbWords = 30, $variableNbWords = true);
            $datetime = $faker->datetime();

            $comment = new Comment();

            $comment->setContent($content);
            $comment->setCreatedAt(new \DateTime());
            $comment->setUpdatedAt(new \DateTime());
            $comment->setFixture(1);

            $comment->setAuthor($this->getReference('user_' . $authorRefRandom));

            $figure = $this->getReference('fig-ref_' . $figRandom);

            if($figure->getFixture() == 1) {

                $comment->setFigure($figure);

            }
            
            $manager->persist($comment);
            $manager->flush(); 
        }
    }

    public function getDependencies()
    {
        return [
            UserFixture::class,
            FigureFixture::class
        ];
    }
}
