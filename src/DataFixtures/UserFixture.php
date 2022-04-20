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

        for ($i = 0; $i < 6; $i++) {

            $pseudo = $faker->name;
            $email = $faker->regexify('[a-z]+@[a-z]+\.[a-z]{2,4}');

            // $listPictures = file_get_contents('https://picsum.photos/v2/list');
            // $personImage = json_decode($listPictures, true)[$i]["download_url"];

            $personImage  = 'https://picsum.photos/seed/picsum/64/64';

            // $personImage = $faker->imageUrl(64, 64);
            $password = $faker->numerify('Hello ###');
            $alternativeAttribute = $faker->sentence($nbWords = 2, $variableNbWords = true);
            
            $user = new User();
            $user->setPseudo($pseudo);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setUrlPhoto($personImage);
            $user->setAlternativeAttribute($alternativeAttribute);

            $manager->persist($user);
        }

        $manager->flush();

        // other fixtures can get this object using the UserFixtures::ADMIN_USER_REFERENCE constant
        $this->addReference(self::USER_REF, $user);
    }
}