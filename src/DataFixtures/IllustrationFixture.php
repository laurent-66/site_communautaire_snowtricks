<?php
namespace App\DataFixtures;

use App\Entity\Illustration;
use App\DataFixtures\FigureFixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class IllustrationFixture extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        // this reference returns the Figure object created in FigureFixture
        $figure = $this->getReference(FigureFixture::FIG_REF);

        // create 20 products! Bam!
        for ($i = 0; $i < 20; $i++) {
            $illustration = new Illustration();
            $illustration->setUrlIllustration('');
            $illustration->setFigure($figure);

            $manager->persist($illustration);
        }

        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            FigureFixture::class
        ];
    }

}