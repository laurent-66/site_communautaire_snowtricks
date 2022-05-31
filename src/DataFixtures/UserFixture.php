<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixture extends Fixture
{

    public const USER_REF = 'user_%s';

    public function load(ObjectManager $manager)
    {

                $dataAuthors = [
                    [
                        'pseudo'=>'Alexis Wisozk',
                        'email'=>'corl@rbcdvyck.yia',
                        'password'=>'Hello611',
                        'url_photo'=>'https://picsum.photos/id/0/5616/3744',
                        'alternative_attribute'=>'photo_Alexis',
                        'created_at'=> '2022-05-11 10:53:43',
                        'updated_at'=> '2022-05-11 10:53:43',

                        'fixture'=> 1
                    ],
                    [
                        'pseudo'=>'Elinore Marquardt',
                        'email'=>'kzgxqve@llp.de',
                        'password'=>'Hello657',
                        'url_photo'=>'https://picsum.photos/id/1000/5626/3635',
                        'alternative_attribute'=>'photo_Elinore',
                        'created_at'=> '2022-05-11 10:53:43',
                        'updated_at'=> '2022-05-11 10:53:43',
                        'fixture'=> 1
                    ],
                    [
                        'pseudo'=>'Casandra Runte',
                        'email'=>'kd@itwc.yiwg',
                        'password'=>'Hello724',
                        'url_photo'=>'https://picsum.photos/id/1005/5760/3840',
                        'alternative_attribute'=>'photo_Casandra',
                        'created_at'=> '2022-05-11 10:53:43',
                        'updated_at'=> '2022-05-11 10:53:43',
                        'fixture'=> 1
                    ],
        
                ];
        
                for($i = 0 ; $i < count($dataAuthors) ; $i++ ) {
        
                    $user = new User();
        
                    $user->setPseudo($dataAuthors[$i]['pseudo']);
                    $user->setEmail($dataAuthors[$i]['email']);
                    $user->setPassword($dataAuthors[$i]['password']);
                    $user->setUrlPhoto($dataAuthors[$i]['url_photo']);
                    $user->setAlternativeAttribute($dataAuthors[$i]['alternative_attribute']);
                    $user->setCreatedAt(new \Datetime($dataAuthors[$i]['created_at']));
                    $user->setUpdatedAt(new \Datetime($dataAuthors[$i]['updated_at']));
                    $user->setFixture(1);
                    
                    $manager->persist($user); 
                    $manager->flush();
                    $this->addReference(sprintf(self::USER_REF , $i), $user); 
        
                }

    }
}