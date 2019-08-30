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
        $userA = new User();

        $userA  ->setEmail("aurelia.h@hotmail.fr")
                ->setPassword("mdp")
                ->setPseudo("Oré");
//                ->setRoles()
//                ->setA

        // Création de 3 users valides & 3 non valides
        for ($i = 0; $i < 6; $i++) {
            $user = new User();
            $user->setEmail("cdwdm-user-$i@yopmail.com")
                ->setPseudo("User-$i")
                ->setFirstName("Prénom-usr-$i")
                ->setLastName("Nom-usr-$i")
                ->setCreationDate(new \DateTime())
                ->setPassword($this->userPasswordEncoderInterface->encodePassword($user, 'mdp'));
            if ($i >= 3) {
                $user->removeRole('ROLE_USER')->addRole('ROLE_USER_PENDING');
//                $user->addRole('ROLE_USER_PENDING');
            }

            $manager->persist($user);
        }

        // Création de 2 admins
        for ($i = 0; $i < 2; $i++) {
            $user = new User();
            $user->setEmail("cdwdm-admin-$i@yopmail.com")
                ->setPseudo("Admin-$i")
                ->setFirstName("Prénom-adm-$i")
                ->setLastName("Nom-adm-$i")
                ->setCreationDate(new \DateTime())
                ->setPassword($this->userPasswordEncoderInterface->encodePassword($user, 'mdp'))
                ->addRole('ROLE_ADMIN');

            $manager->persist($user);
        }

        $manager->flush();

        // Création de 4 tracks
        $track = new Track();
        $track->setTitle("Piano")
            ->setTrack("29b94fedfa846690689b6438e1cd615b")
            ->setImage("2f29d6a6e4a5e189ea01c24286dd5948")
            ->setNum(1)
            ->setId1(0)
//                ->setUser($user);
            ->setCreationDate(new \DateTime());
        $manager->persist($track);

        $track = new Track();
        $track->setTitle("Grand mère")
            ->setTrack("b576e4c5abd899aa8bb7a73326d82b81")
            ->setImage("4bdf16b20d7711aa0ee1ed8e3aa9ea98")
            ->setNum(2)
            ->setId1(1)
//                ->setUser(1);
            ->setCreationDate(new \DateTime());
        $manager->persist($track);

        $track = new Track();
        $track->setTitle("Révalité")
            ->setTrack("eb20a9de7f26f78dfc7b1050700db7e4")
            ->setImage("48e4d82ab1bdd10c01acca6e2dd51cc6")
            ->setNum(2)
            ->setId1(1)
//                ->setUser(1);
            ->setCreationDate(new \DateTime());
        $manager->persist($track);

        $track = new Track();
        $track->setTitle("Révalité2")
            ->setTrack("982c1577f27b685b1586db7ba468cea3")
            ->setImage("d4c0c2b918c260a4e6d08308886bb210")
            ->setNum(3)
            ->setId1(3)
//                ->setUser(1);
            ->setCreationDate(new \DateTime());
        $manager->persist($track);

        $manager->flush();


        // Création des mots clefs
        $motclef = new MotClef();
        $motclef->setMot("Piano");
        $manager->persist($motclef);

        $manager->flush();

        $motclef = new MotClef();
        $motclef->setMot("Chant");
        $manager->persist($motclef);

        $motclef = new MotClef();
        $motclef->setMot("Rap");
        $manager->persist($motclef);

        $motclef = new MotClef();
        $motclef->setMot("Disco");
        $manager->persist($motclef);

        $motclef = new MotClef();
        $motclef->setMot("Percussions");
        $manager->persist($motclef);

        $manager->flush();

    }
}