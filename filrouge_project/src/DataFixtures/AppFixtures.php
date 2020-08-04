<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Profil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $pwdEncoder;

    public function __construct(UserPasswordEncoderInterface $pwdEncoder)
    {
        $this->pwdEncoder= $pwdEncoder;
    }
    public function load(ObjectManager $manager)
    {
        $profils = ["apprenant", "admin","formateur", "cm"];
        $faker = Factory::create("fr_FR");
        foreach ($profils as $value) {
            $profil = new Profil();
            $profil->setLibelle($value);
            $manager->persist($profil);
            $manager->flush();
            for ($i=1; $i <= 2; $i++) { 
                $user = new User();
                $user
                    ->setNom($faker->lastName)
                    ->setPrenom($faker->firstName)
                    ->setEmail($faker->email)
                    ->setProfil($profil)
                    ->setPassword($this->pwdEncoder->encodePassword($user,"Test"));
                $manager->persist($user);
            } 
            $manager->flush(); 
        }   
    }
}
