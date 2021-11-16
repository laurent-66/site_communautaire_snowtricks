<?php
namespace App\DataFixtures;

use DateTime;
use App\Entity\Comment;
use App\DataFixtures\UserFixture;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\FigureGroupFixture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FigureFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        //datetime comment
        $datetime = new DateTime();
        // this reference returns the User object created in UserFixture
        $author = $this->getReference(UserFixture::USER_REF);

        // this reference returns the FigureGroup object created in FigureGroupFixture
        $figure = $this->getReference(FigureFixture::FIG_REF);

        // create 20 products! Bam!
        for ($i = 0; $i < 20; $i++) {
            $comment = new Comment();
            $comment->setDate($datetime);
            $comment->setContent('');
            $comment->setAuthor($author);
            $comment->setFigure($figure);

            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixture::class,
            FigureFixture::class
        ];
    }
}