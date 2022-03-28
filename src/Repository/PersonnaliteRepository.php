<?php

namespace App\Repository;

use App\Entity\Etudiant;
use App\Entity\Personnalite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Personnalite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personnalite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personnalite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonnaliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personnalite::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Personnalite $entity, bool $flush = true): void
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
    public function remove(Personnalite $entity, bool $flush = true): void
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
        $qb = $this->createQueryBuilder('p')
            ->select('p');

        if ($trash) {
            $qb->where('p.status = :status')
                ->setParameter('status', Personnalite::TYPE_TRASH);
        } else {
            $qb->where('p.status != :status')
                ->setParameter('status', Personnalite::TYPE_TRASH);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Etudiant $etudiant
     * @return mixed
     */
    public function findByEtudiantOwned(Etudiant $etudiant): mixed
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p', 'pe', 'e')
            ->join('p.etudiantPersonnalites', 'pe')
            ->join('pe.etudiant', 'e')
            ->where('p.status = :status')
            ->setParameter('status', Personnalite::TYPE_PUBLIC)
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
        $qb = $em->createQuery("SELECT p FROM App\Entity\Personnalite p WHERE p.id NOT IN (
            SELECT p2 FROM App\Entity\Personnalite p2, App\Entity\EtudiantPersonnalite pe, App\Entity\Etudiant e
            WHERE p2 = pe.personnalite AND e = pe.etudiant AND e = '$idEtudiant' AND p2.status = 'public'
        )");

        return $qb->getResult();
    }

    // /**
    //  * @return Personnalite[] Returns an array of Personnalite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Personnalite
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
