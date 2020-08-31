<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Profil;
use App\Entity\Competence;
use App\Entity\GroupeCompetence;
use App\Entity\GroupeTag;
use App\Entity\Tag;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $pwdEncoder;
    private $repo;

    public function __construct(UserPasswordEncoderInterface $pwdEncoder, UserRepository $repo)
    {
        $this->pwdEncoder= $pwdEncoder;
        $this->repo = $repo;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create("fr_FR");
      /*  $profils = ["apprenant", "admin","formateur", "cm"];
        foreach ($profils as $value) {
            $profil = new Profil();
            $profil->setLibelle($value)
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

        $user=$this->repo->find(3);
        $grpcompetence=new GroupeCompetence();
        $grpcompetence
            ->setLibelle('developper le back-end appli web ')
            ->setDescriptif('descriptif')
            ->setAdmin($user);
        $competence1=new Competence;
        $competence1
            ->setLibelle('Créer une base de données')
            ->addGroupeCompetence($grpcompetence);
        $manager->persist($competence1);
        $competence2=new Competence;
        $competence2
            ->setLibelle('Développer les compétences d\'accès aux données')
            ->addGroupeCompetence($grpcompetence);
        $manager->persist($competence2);
        $manager->flush();
        $manager->persist($grpcompetence);
        $manager->flush(); 

        $grptags = new GroupeTag;
        $grptags
            ->setLibelle("Développement Web");
        $libelles = ["HTML 5", "Node JS", "Angular"];
        foreach($libelles as $val){
            $tag = new Tag;
            $tag
                ->setLibelle($val)
                ->setDescriptif($faker->text)
                ->addGroupeTag($grptags);
            $manager->persist($tag);
        }
        $manager->flush();
        $manager->persist($grptags);
        $manager->flush();    */   
    }
    
}
