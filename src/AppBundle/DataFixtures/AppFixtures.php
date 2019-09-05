<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\MotClef;
use AppBundle\Entity\Track;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $userPasswordEncoderInterface;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoderInterface)
    {
        $this->userPasswordEncoderInterface = $userPasswordEncoderInterface;
    }

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
//--------------------------------------------------------USERS--------------------------------------------------------

        // UserA
        $userA = new User();

        $userA->setEmail("aurelia.h+user@hotmail.fr")
            ->setPassword($this->userPasswordEncoderInterface->encodePassword($userA, 'mdpmdp'))
            ->setPseudo("Toto")
//                ->setRoles(['ROLE_USER'])                 Pas la peine car se fait tout seul dans le contructeur
            ->setActif(1);
//                ->setCreationdate(new \DateTime());         Pas la peine car se fait tout seul dans le contructeur

        // On marque l'entité comme étant à sauvegarder en base de donnée (sera effectué grâce au flush()
        $manager->persist($userA);

        // UserAdmin
        $userAdmin = new User();
        $userAdmin->setEmail("aurelia.h+admin@hotmail.fr")
            ->setPassword($this->userPasswordEncoderInterface->encodePassword($userAdmin, 'mdpmdp'))
            ->setPseudo("Dudu")
            ->setActif(1)
            ->addRole('ROLE_ADMIN');
        $manager->persist($userAdmin);


        // 1 users valide, 1 non validé et 1 admin
        $user = new User();
        $user->setEmail("cdw-user@yopmail.com")
            ->setPseudo("User")
            ->setFirstName("Prénom-usr")
            ->setLastName("Nom-usr")
            ->setActif(1)
            ->setCreationDate(new \DateTime())
            ->setPassword($this->userPasswordEncoderInterface->encodePassword($user, 'mdpmdp'));
        $manager->persist($user);

        $userPending = new User();
        $userPending->setEmail("cdw-userP@yopmail.com")
            ->setPseudo("UserP")
            ->setFirstName("Prénom-usrP")
            ->setLastName("Nom-usrP")
            ->setActif(1)
            ->setCreationDate(new \DateTime())
            ->setPassword($this->userPasswordEncoderInterface->encodePassword($user, 'mdpmdp'));
        $userPending->removeRole('ROLE_USER')->addRole('ROLE_USER_PENDING');
        $manager->persist($userPending);

        $userADMIN = new User();
        $userADMIN->setEmail("cdw-admin@yopmail.com")
            ->setPseudo("Admin")
            ->setFirstName("Prénom-adm")
            ->setLastName("Nom-adm")
            ->setActif(1)
            ->setCreationDate(new \DateTime())
            ->setPassword($this->userPasswordEncoderInterface->encodePassword($user, 'mdpmdp'))
            ->addRole('ROLE_ADMIN');
        $manager->persist($userADMIN);

        $manager->flush();

        // User non actif
        $userNA = new User();
        $userNA->setEmail("cdw-userNA@yopmail.com")
            ->setPseudo("UserNA")
            ->setFirstName("Prénom-usrNA")
            ->setLastName("Nom-usrNA")
            ->setActif(0)
            ->setCreationDate(new \DateTime())
            ->setPassword($this->userPasswordEncoderInterface->encodePassword($user, 'mdpmdp'));
        $manager->persist($userNA);
        $manager->flush();


        // On sauvegarde en BDD
        $manager->flush();


//--------------------------------------------------------MOTS CLEFS--------------------------------------------------------
        $mc1 = new MotClef();
        $mc1->setMot('Cordes')
            ->setActif(1);
        $manager->persist($mc1);

        $mc2 = new MotClef();
        $mc2->setMot('Vent')
            ->setActif(1);
        $manager->persist($mc2);

        $mc3 = new MotClef();
        $mc3->setMot('Voix')
            ->setActif(1);
        $manager->persist($mc3);

        $mc4 = new MotClef();
        $mc4->setMot('Rock')
            ->setActif(1);
        $manager->persist($mc4);

        $manager->flush();


//--------------------------------------------------------TRACKS--------------------------------------------------------

        // Track1 (compo de base)
        $track1 = new Track();
        $track1->setTitle('Piano')
            ->setTrack('piano.mp3')
            ->setImage('endormi.jpg')
            ->setUser($userA)
            ->setNum(0)
            ->setActif(1);
        $manager->persist($track1);

        // Track2 (compo de base)
        $track2 = new Track();
        $track2->setTitle('Révalité')
            ->setTrack('revalite.mp3')
            ->setImage('cd.jpg')
            ->setUser($userAdmin)
            ->setNum(0)
            ->setActif(1);
        $manager->persist($track2);

        // Track3 (+1)
        $track3 = new Track();
        $track3->setTitle('Révalité')
            ->setTrack('revalite2.mp3')
            ->setImage('forme.png')
            ->setUser($userAdmin)
            ->setNum(1)
            ->setId1($track1)
            ->setActif(1);
        $manager->persist($track3);

        // On effectue les requêtes (sauvegarder les instances créées ci-dessus)
        $manager->flush();


//        // Création de 3 users valides & 3 non valides
//        for ($i = 0; $i < 6; $i++) {
//            $user = new User();
//            $user->setEmail("cdwdm-user-$i@yopmail.com")
//                ->setPseudo("User-$i")
//                ->setFirstName("Prénom-usr-$i")
//                ->setLastName("Nom-usr-$i")
//                ->setActif(1)
//                ->setCreationDate(new \DateTime())
//                ->setPassword($this->userPasswordEncoderInterface->encodePassword($user, 'mdp'));
//            if ($i >= 3) {
//                $user->removeRole('ROLE_USER')->addRole('ROLE_USER_PENDING');
////                $user->addRole('ROLE_USER_PENDING');
//            }
//
//            $manager->persist($user);
//        }
////
//        // Création de 2 admins
//        for ($i = 0; $i < 2; $i++) {
//            $user = new User();
//            $user->setEmail("cdwdm-admin-$i@yopmail.com")
//                ->setPseudo("Admin-$i")
//                ->setFirstName("Prénom-adm-$i")
//                ->setLastName("Nom-adm-$i")
//                ->setCreationDate(new \DateTime())
//                ->setPassword($this->userPasswordEncoderInterface->encodePassword($user, 'mdp'))
//                ->addRole('ROLE_ADMIN');
//
//            $manager->persist($user);
//        }

//        $manager->flush();
//


//        // Création de 4 tracks
//        $track = new Track();
//        $track->setTitle("Piano")
//            ->setTrack("29b94fedfa846690689b6438e1cd615b")
//            ->setImage("2f29d6a6e4a5e189ea01c24286dd5948")
//            ->setNum(1)
//            ->setId1(0)
////                ->setUser($user);
//            ->setCreationDate(new \DateTime());
//        $manager->persist($track);
//
//        $track = new Track();
//        $track->setTitle("Grand mère")
//            ->setTrack("b576e4c5abd899aa8bb7a73326d82b81")
//            ->setImage("4bdf16b20d7711aa0ee1ed8e3aa9ea98")
//            ->setNum(2)
//            ->setId1(1)
////                ->setUser(1);
//            ->setCreationDate(new \DateTime());
//        $manager->persist($track);
//
//        $track = new Track();
//        $track->setTitle("Révalité")
//            ->setTrack("eb20a9de7f26f78dfc7b1050700db7e4")
//            ->setImage("48e4d82ab1bdd10c01acca6e2dd51cc6")
//            ->setNum(2)
//            ->setId1(1)
////                ->setUser(1);
//            ->setCreationDate(new \DateTime());
//        $manager->persist($track);
//
//        $track = new Track();
//        $track->setTitle("Révalité2")
//            ->setTrack("982c1577f27b685b1586db7ba468cea3")
//            ->setImage("d4c0c2b918c260a4e6d08308886bb210")
//            ->setNum(3)
//            ->setId1(3)
////                ->setUser(1);
//            ->setCreationDate(new \DateTime());
//        $manager->persist($track);
//
//        $manager->flush();
//
//
//        // Création des mots clefs
//        $motclef = new MotClef();
//        $motclef->setMot("Piano");
//        $manager->persist($motclef);
//
//        $manager->flush();
//
//        $motclef = new MotClef();
//        $motclef->setMot("Chant");
//        $manager->persist($motclef);
//
//        $motclef = new MotClef();
//        $motclef->setMot("Rap");
//        $manager->persist($motclef);
//
//        $motclef = new MotClef();
//        $motclef->setMot("Disco");
//        $manager->persist($motclef);
//
//        $motclef = new MotClef();
//        $motclef->setMot("Percussions");
//        $manager->persist($motclef);
//
//        $manager->flush();

    }
}