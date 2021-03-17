<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Profil;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
// use Symfony\Component\Security\Core\Encoder\User

class AppFixtures extends Fixture
{
    private $encode;

    public function __construct(UserPasswordEncoderInterface $encode)
    {
        $this->encode = $encode;
    }

    public function load(ObjectManager $manager)
    {
        $admin= new User() ;

        $admin->setUsername("adminsystem");
        $admin->setPassword($this->encode->encodePassword($admin,"passer"));
        $admin->setNom("Tine");
        $admin->setPrenom("Moussa");
        $admin->setPhone('771185836');
        $admin->setAdresse('FATICK');
        $admin->setCni("123457982");
        $profil=new Profil();
        $profil->setLibelle("AdminSystem");
        $admin->setProfil( $profil);
       
        $manager->persist($admin);
        $manager->flush();


         $admin= new User() ;

        $admin->setUsername("adminagence");
        $admin->setPassword($this->encode->encodePassword($admin,"passer"));
        $admin->setNom("guiro");
        $admin->setPrenom("thierno");
        $admin->setPhone('771905836');
        $admin->setAdresse('PARIS');
        $admin->setCni("12345745678");
        $profil=new Profil();
        $profil->setLibelle("AdminAgence");
        $admin->setProfil( $profil);
       
        $manager->persist($admin);
        $manager->flush();

        $admin= new User() ;

        $admin->setUsername("useragence");
        $admin->setPassword($this->encode->encodePassword($admin,"passer"));
        $admin->setNom("diop");
        $admin->setPrenom("modou");
        $admin->setAdresse('LOUGA');
        $admin->setCni("123457876548");
        $admin->setPhone('777685836');
        $profil=new Profil();
        $profil->setLibelle("UserAgence");
        $admin->setProfil( $profil);
       
        $manager->persist($admin);
        $manager->flush();

        $admin= new User() ;

        $admin->setUsername("caissiere");
        $admin->setPassword($this->encode->encodePassword($admin,"passer"));
        $admin->setNom("aminata");
        $admin->setPrenom("ba");
        $admin->setPhone('7711859876');
        $admin->setAdresse('dakar');
        $admin->setCni("1264533876548");
        $profil=new Profil();
        $profil->setLibelle("Caissiere");
        $admin->setProfil( $profil);
       
        $manager->persist($admin);
        $manager->flush();
    }
}