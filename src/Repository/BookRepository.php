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

//query builder
//premier methode de show all
    public function ShowAllBook(){
       $list= $this->createQueryBuilder('b') //select b as from book
        ->where('b.title LIKE :param')
        ->setParameter('param','a%')
        ->orderBy('b.title','ASC') 
        ->getQuery() 
        ->getResult(); 
        return $list;
    }
  
//afficher la liste des books par ref
    
     public function showAllBooksByAuthor($ref)
     {
         return $this->createQueryBuilder('b')
             ->join('b.author','a')
             ->addSelect('a')
             ->where('b.ref LIKE :ref')
             ->setParameter('ref', '%'.$ref.'%')
             ->getQuery()
             ->getResult()
             ;
     }
     
//Afficher la liste des livres publiés avant l’année 2023 dont l’auteur a plus de 10 livre
public function showBooksByDateAndNbBooks($nbooks, $year)
{
    return $this->createQueryBuilder('b')
        ->join('b.author', 'a')
        ->addSelect('a')
        ->where('a.nb_books > :nbooks')
        ->andWhere("b.publicationDate < :year")
        ->setParameter('nbooks', $nbooks)
        ->setParameter('year', $year)
        ->getQuery()
        ->getResult();
}


// Modifier les livres dont la catégorie est « Science-Fiction » par « Romance ».
public function updateBooksCategoryByAuthor($c)
{
    return $this->getEntityManager()->createQueryBuilder()
        ->update('App\Entity\Book', 'b')
        ->set('b.category', '?1')
        ->setParameter(1, 'Romance')
        ->where('b.category LIKE ?2')
        ->setParameter(2, $c)
        ->getQuery()
        ->getResult();
}

//dql
//deuxieme methode DE SHOW  
     public function showAllDQL(){
        $em=$this->getEntityManager();
        $list=$em->createQuery('Select p from  App\Entity\Book p')
        ->getResult();
        return $list;
    }

//afficher les book trier de facon croissante selon l'auteur 
    public function booksListByAuthors()
    {
        $entityManager=$this->getEntityManager();
       $query =$entityManager->createQuery('SELECT book FROM App\Entity\Book book ORDER BY book.author ASC');
        return $query->getResult();
    }

//DQL Afficher le nombre des livres dont la catégorie est « Romance ».
    public function countRomanceBooks()
{
    $entityManager = $this->getEntityManager();
    $query = $entityManager->createQuery('SELECT b 
        FROM App\Entity\Book b
        WHERE b.category = :category
    ');
    $query->setParameter('category', 'Romance');
    return $query->getResult();
}

//Afficher la liste des livres publiés entre deux dates « 2014-01-01 » et «2018- 12-31 ».
function findBookByPublicationDate()
    {
        $em = $this->getEntityManager();
        return $em->createQuery('select b from App\Entity\Book b WHERE 
    b.publicationDate BETWEEN ?1 AND ?2')
            ->setParameter(1, '2014-01-01')
            ->setParameter(2, '2018-01-01')->getResult();
    }

//Afficher le nombre des livres dont la catégorie est « Romance »
function NbBookCategory()
    {
        $em = $this->getEntityManager();
        return $em->createQuery('select count(b) from App\Entity\Book b WHERE b.category=:category')
            ->setParameter('category', 'Romance')->getSingleScalarResult();
    }



 /* public function ShowBook($ref){
        $list= $this->createQueryBuilder('b') //select b as from book
        ->join('b.author','a')
        ->addSelect('a')
         ->where('b.ref LIKE :ref')
         ->setParameter('ref','%'.$ref.'%')
         ->getQuery() 
         ->getResult(); 
     }*/
      /* public function showDQL($ref)
        {    
            return $this->createQueryBuilder('b')
                ->join('b.author','a')
                ->where('b.ref LIKE :ref')
                ->setParameter('ref', '%'.$ref.'%') //$ref est la valeur passée en param de la fonction
                ->getQuery()
                ->getResult(); 
    }*/
    }
