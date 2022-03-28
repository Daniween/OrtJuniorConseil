<?php

namespace App\Repository;

use App\Entity\Competence;
use App\Entity\Etudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Competence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Competence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Competence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompetenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Competence::class);
    }

    /**
     * @param Competence $entity
     * @param bool $flush
     */
    public function add(Competence $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param Competence $entity
     * @param bool $flush
     */
    public function remove(Competence $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param bool $trash
     * @return array|mixed
     */
    public function findAll($trash = false)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c');

        if ($trash) {
            $qb->where('c.status = :status')
                ->setParameter('status', Competence::TYPE_TRASH);
        } else {
            $qb->where('c.status != :status')
                ->setParameter('status', Competence::TYPE_TRASH);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Etudiant $etudiant
     * @return mixed
     */
    public function findByEtudiantOwned(Etudiant $etudiant): mixed
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c', 'ce', 'e')
            ->join('c.etudiantCompetences', 'ce')
            ->join('ce.etudiant', 'e')
            ->where('c.status = :status')
            ->setParameter('status', Competence::TYPE_PUBLIC)
            ->andWhere('e = :etudiant')
            ->setParameter('etudiant', $etudiant);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Etudiant $etudiant
     * @return mixed
     */
    public function findByEtudiantNotOwned(Etudiant $etudiant): mixed
    {
        $idEtudiant = $etudiant->getId();
        $em = $this->getEntityManager();
        $qb = $em->createQuery("SELECT c FROM App\Entity\Competence c WHERE c.id NOT IN (
            SELECT c2 FROM App\Entity\Competence c2, App\Entity\EtudiantCompetence ce, App\Entity\Etudiant e
            WHERE c2 = ce.competence AND e = ce.etudiant AND e = '$idEtudiant' AND c2.status = 'public'
        )");

        return $qb->getResult();
    }


    // /**
    //  * @return Competence[] Returns an array of Competence objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Competence
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
