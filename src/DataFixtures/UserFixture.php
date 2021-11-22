<?php
namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixture extends Fixture
{
    public const USER_REF = 'user-ref';

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');
        // create 20 products! Bam!
        for ($i = 0; $i < 20; $i++) {

            $pseudo = $faker->name;
            $email = $faker->regexify('[a-z]+@[a-z]+\.[a-z]{2,4}');
            $personImage = $faker->imageUrl(64, 64);
            $password = $faker->numerify('Hello ###');

            $user = new User();
            $user->setPseudo($pseudo);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setUrlPhoto($personImage);

            $manager->persist($user);
        }

        $manager->flush();

        // other fixtures can get this object using the UserFixtures::ADMIN_USER_REFERENCE constant
        $this->addReference(self::USER_REF, $user);
    }
}