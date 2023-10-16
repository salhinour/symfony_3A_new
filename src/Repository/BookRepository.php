<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    // Vous pouvez ajouter des méthodes de recherche personnalisées ici

    /*
    public function findByAuthor($author)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.author = :author')
            ->setParameter('author', $author)
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPublishedBooks()
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.published = :published')
            ->setParameter('published', true)
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
    */

    // Vous pouvez également créer des méthodes spécifiques à votre application ici
}