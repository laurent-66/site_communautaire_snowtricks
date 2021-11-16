<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class Userfixture extends Fixture
{
    public const USER_REF = 'user-ref';

    public function load(ObjectManager $manager)
    {
        // create 20 products! Bam!
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setPseudo('pseudo:'.$i);
            $user->setEmail(mt_rand(10, 100));
            $user->setPassword(mt_rand(10, 100));
            $user->setUrlPhoto(mt_rand(10, 100));

            $manager->persist($user);
        }

        $manager->flush();

        // other fixtures can get this object using the UserFixtures::ADMIN_USER_REFERENCE constant
        $this->addReference(self::USER_REF, $user);
    }

}