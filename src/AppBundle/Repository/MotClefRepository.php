<?php

namespace AppBundle\Repository;

/**
 * MotClefRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MotClefRepository extends \Doctrine\ORM\EntityRepository
{


    /**
     * Modifie le fait que le mot clef soit actif ou non
     *
     * @param bool $actif
     * @param int $id
     * @return \Doctrine\ORM\Query
     */
    public function switchActifMotclefActionQuery(bool $actif,int $id)
    {
        $qb = $this->createQueryBuilder('m');

        if ($actif == false) {
            $query =  $qb->update()
                ->where($qb->expr()->like('m.id', ':id'))
                ->set('m.actif', ':actif')
                ->setParameter('actif', true)
                ->setParameter('id',$id)
                ->getQuery();
            $result = $query->execute();

        } else {
            $query =  $qb->update()
//                ->where('m.id = ?1')     Ok Mais on doit utiliser un chiffre pour la key (ici 1 et donc : ->setParameter(1, $id))
                ->where($qb->expr()->like('m.id', ':id'))
                ->set('m.actif', ':actif')
                ->setParameter('actif', false)
                ->setParameter('id', $id)
                ->getQuery();
            $result = $query->execute();

        }
        return $result;
    }

    /**
     * Pour les admin : Rechercher un mot clef avec partie du mot
     *
     * @param $query
     * @return \Doctrine\ORM\Query
     */
    public function findByMot($query)
    {
        /** Requête SQL
         * SELECT *
         * FROM MotClef m
         * WHERE m.title LIKE %query%
         * DISTINCT
         */

        $qb = $this->createQueryBuilder('m');
        $qb
            ->where($qb->expr()->like('m.mot', ':query'))
            ->setParameter('query', '%'.$query.'%')             // Ajout des '%' obligatoire
            ->distinct()
        ;

        return $qb->getQuery();
    }
}
