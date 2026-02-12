<?php

namespace App\Repository;

use App\Entity\Covoiturage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Covoiturage>
 */
class CovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Covoiturage::class);
    }

    //    /**
    //     * @return Covoiturage[] Returns an array of Covoiturage objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Covoiturage
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
  public function findByFilters(?string $depart, ?string $arrivee, ?string $date, ?float $prixMax, ?int $dureeMax, ?int $noteMin, bool $estEcologique): array
{
    $qb = $this->createQueryBuilder('c')
        ->join('c.voiture', 'v')
        ->andWhere('c.statut = :statut')
        ->setParameter('statut', 'Ouvert');

    // 1. Filtre Départ (Insensible à la casse)
    if (!empty($depart)) {
        $qb->andWhere('LOWER(c.lieu_depart) LIKE LOWER(:depart)')
           ->setParameter('depart', '%' . $depart . '%');
    }

    // 2. Filtre Arrivée
    if (!empty($arrivee)) {
        $qb->andWhere('LOWER(c.lieu_arrivee) LIKE LOWER(:arrivee)')
           ->setParameter('arrivee', '%' . $arrivee . '%');
    }

    // 3. Filtre DATE (Le plus sensible)
    if (!empty($date)) {
        try {
            // On force la conversion en objet DateTime pour que Doctrine 
            // puisse comparer correctement avec la colonne DATE de la BDD
            $dateObj = new \DateTime($date);
            $qb->andWhere('c.date_depart = :valDate')
               ->setParameter('valDate', $dateObj->format('Y-m-d'));
        } catch (\Exception $e) {
            // Si la date est mal formée, on ignore le filtre au lieu de tout casser
        }
    }

    // 4. Filtre Prix
    if ($prixMax !== null && $prixMax > 0) {
        $qb->andWhere('c.prix_personne <= :prixMax')
           ->setParameter('prixMax', $prixMax);
    }

    // 5. Filtre Écologique
    if ($estEcologique === true) {
        $qb->andWhere('v.energie LIKE :elec')
           ->setParameter('elec', '%lectrique%');
    }

    // 6. Filtre DURÉE (Le nouveau venu)
    if ($dureeMax !== null && $dureeMax > 0) {
        $qb->andWhere('c.duree <= :maxMinutes')
           ->setParameter('maxMinutes', $dureeMax * 60);
    }

    $trajets = $qb->getQuery()->getResult();

    // 7. Filtre Note (PHP)
    if ($noteMin !== null && $noteMin > 0) {
        $trajets = array_filter($trajets, function($t) use ($noteMin) {
            return $t->getVoiture()->getUser()->getNoteMoyenne() >= $noteMin;
        });
    }

    return array_values($trajets);
}
    
}

