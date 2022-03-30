<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Etudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Etudiant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etudiant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etudiant[]    findAll()
 * @method Etudiant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtudiantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etudiant::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Etudiant $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Etudiant $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param string $token
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findOneByResetToken(string $token)
    {
        return $this->createQueryBuilder('e')
            ->select('e')
            ->where("e.resetToken = :token")
            ->setParameter('token', $token)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param SearchData $search
     * @return array|mixed
     */

    public function findSearch(SearchData $search)
    {

        // Génération de chaine de characteres pour ne pas avoir deux alias de sous requetes identiques
        function getName($n) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';

            for ($i = 0; $i < $n; $i++) {
                $index = rand(0, strlen($characters) - 1);
                $randomString .= $characters[$index];
            }

            return $randomString;
        }

        $query = $this
            ->createQueryBuilder('e')
            ->select('e', 'ee')
            ->leftJoin('e.etude', 'ee')
            ->andWhere('e.activate != :activate')
            ->setParameter('activate', 0)
            ->andWhere('e.completed != :completed')
            ->setParameter('completed', 0)
            ->orderBy('e.name', 'ASC');

        $em   = $this->getEntityManager();
        $expr = $em->getExpressionBuilder();

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('CONCAT(e.name, e.firstName) LIKE :q')
                ->setParameter('q', "%{$search->q}%")
            ;
        }


        if (!empty($search->etude)) {
            $query = $query
                ->andWhere('ee IN (:etude)')
                ->setParameter('etude', $search->etude);
        }

        if (!empty($search->competence)) {

            foreach ($search->competence as $competence) {
                $competence = $competence->getLibelle();
                $tempCompetence = "competence_".getName(8)."_";

                $query->andWhere($expr->in('e',
                    "
                    SELECT ".$tempCompetence."subquery_e.id FROM App\Entity\Etudiant ".$tempCompetence."subquery_e
                    LEFT JOIN ".$tempCompetence."subquery_e.etudiantCompetences ".$tempCompetence."sub_ec
                    LEFT JOIN ".$tempCompetence."sub_ec.competence ".$tempCompetence."sub_ecs
                    WHERE ".$tempCompetence."sub_ec.competence = ".$tempCompetence."sub_ecs.id
                    AND ".$tempCompetence."sub_ecs.libelle = '$competence'
                "
                ));
            }
        }

        if (!empty($search->personnalite)) {

            foreach ($search->personnalite as $personnalite) {
                $personnalite = $personnalite->getLibelle();
                $tempPersonnalite = "personnalite_".getName(8)."_";

                $query->andWhere($expr->in('e',
                    "
                    SELECT ".$tempPersonnalite."subquery_e.id FROM App\Entity\Etudiant ".$tempPersonnalite."subquery_e
                    LEFT JOIN ".$tempPersonnalite."subquery_e.etudiantPersonnalites ".$tempPersonnalite."sub_ep
                    LEFT JOIN ".$tempPersonnalite."sub_ep.personnalite ".$tempPersonnalite."sub_eps
                    WHERE ".$tempPersonnalite."sub_ep.personnalite = ".$tempPersonnalite."sub_eps.id
                    AND ".$tempPersonnalite."sub_eps.libelle = '$personnalite'
                "
                ));
            }
        }

        return $query->getQuery()->getResult();

    }

    // /**
    //  * @return Etudiant[] Returns an array of Etudiant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Etudiant
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
