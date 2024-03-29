<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityRepository;


/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Pour les admin : Rechercher un user avec partie de son nom/prénom/pseudo
     *
     * @param $query
     * @return \Doctrine\ORM\Query
     */
    public function findByFirstNameOrLastName($query)
    {
        /** Requête SQL
         * SELECT *
         * FROM User u
         * WHERE u.lastname LIKE %query% OR u.firstname LIKE %query% OR u.pseudo LIKE %query%
         * DISTINCT
         */

        $qb = $this->createQueryBuilder('u');                  // On est dans le UserRepository donc u = user ?????
        $qb
            ->where($qb->expr()->like('u.firstName', ':query'))
            ->orWhere($qb->expr()->like('u.lastName', ':query'))
            ->orWhere($qb->expr()->like('u.pseudo', ':query'))
            ->setParameter('query', '%' . $query . '%')// Ajout des '%' obligatoire
            ->orderBy('u.creationdate', 'DESC')
            ->distinct();

        return $qb->getQuery();
    }

    /**
     * Modifie le fait que l'utilisateur soit actif ou non
     *
     * @param bool $actif
     * @param int $id
     * @return \Doctrine\ORM\Query
     */
    public function switchActifUserActionQuery(bool $actif, int $id)
    {
        $qb = $this->createQueryBuilder('u');   // On est dans le UserRepository donc u = user

        if ($actif == false) {
            $query = $qb->update()
                ->where('u.id = ?1')
                ->set('u.actif', ':actif')
                ->setParameter('actif', true)
                ->setParameter(1, $id)
                ->getQuery();
            $result = $query->execute();

        } else {
            $query = $qb->update()
                ->where('u.id = ?1')
                ->set('u.actif', ':actif')
                ->setParameter('actif', false)
                ->setParameter(1, $id)
                ->getQuery();
            $result = $query->execute();

        }
        return $result;
    }


    /**
     * @param $query
     * @return \Doctrine\ORM\Query
     */
    public function findByKeyWordOrTitle($query)
    {
        /** Requête SQL
         * SELECT *
         * FROM User u
         * WHERE u.title LIKE %query%
         * //         * OR u.firstname LIKE %query%
         * DISTINCT
         */

        $qb = $this->createQueryBuilder('u');                   // Comment on sait que u = User ?????
        $qb
            ->where($qb->expr()->like('u.title', ':query'))
//            ->orWhere($qb->expr()->like('u.lastName', ':query'))
            ->setParameter('query', '%' . $query . '%')// Ajout des '%' obligatoire
            ->distinct();

        return $qb->getQuery();
    }
}