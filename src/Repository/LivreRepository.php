<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Livre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livre[]    findAll()
 * @method Livre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    public function findDates()
    {
        return $this->createQueryBuilder('livre')
            ->select('livre.date_de_parution')
            ->distinct()
            ->getQuery()
            ->getResult();
    }


    public function findByCriter($keyword,$dateMin,$dateMax,$note,$datepub,$auteur,$genre)
    {
        $query=$this->createQueryBuilder('livre');
        if($keyword!=null)
            $query->andWhere('livre.titre LIKE :val')
            ->setParameter('val', '%' . $keyword . '%');
        if($dateMin!=null && $dateMax!=null)
            $query->andWhere('livre.date_de_parution BETWEEN :dateMin AND :dateMax')
                    ->setParameter('dateMin',  $dateMin )
                    ->setParameter('dateMax',  $dateMax );
        if($note!=null)
            $query->andWhere('livre.note=:note')
            ->setParameter('note',$note);
        if($datepub!=null)
            $query->andWhere('livre.date_de_parution=:datep')
                ->setParameter('datep',$datepub);
        if($auteur!=null)
            $query->innerJoin('livre.auteurs', 'a', 'WITH', 'a.id = :id')
                ->setParameter('id', $auteur);
        if($genre!=null)  
            $query->innerJoin('livre.genres', 'g', 'WITH', 'g.id = :id')
                ->setParameter('id', $genre);         
        return $query->getQuery()
                     ->getResult();
    }


  
}
