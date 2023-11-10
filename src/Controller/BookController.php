<?php

namespace App\Controller;
use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Form\ReasearchType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AuthorRepository;


class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    //show book
    #[Route('/book/{id}', name: 'app_showB')]
    public function showBook($id,BookRepository $repoB){
        $book=$repoB->find($id);
      return $this->render('book/showBookN.html.twig',['book'=>$book]);
    }

     //afficher la  liste des book selon published 
     #[Route('/listBook', name: 'app_book')]
     public function affiche(BookRepository $BRepo, ManagerRegistry $doctrine): Response
     {
         $em = $doctrine->getManager();
         $publishedBooks = $BRepo->findBy(['published' => true]);
         $unpublishedBooks = $BRepo->findBy(['published' => false]); // Récupère les livres non publiés
         return $this->render('book/afficheB.html.twig', [
             'books' => $publishedBooks,
             'unpublishedBooks' => $unpublishedBooks
         ]);
     }

//ajouter un book du formulaire
    #[Route('/bookAdd', name: 'bookAdd')]
    public function addBook(ManagerRegistry $manager, Request $request)
    {
        $book = new Book();
        // Injecter le formulaire
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $book->setPublished(true);
            $nb=$book->getAuthor()->getNb_books()+1;
            $book->getAuthor()->setNb_books($nb);
            $em = $manager->getManager();
            $em->persist($book);
            $em->flush();
            return new Response('Book added succesfully');
        }
        return $this->render('book/formBook.html.twig', ['form' => $form->createView()]);
    }

   

//modifier le formulaire
    #[Route("/edit-book/{id}", name: 'edit_book')]
public function editAuthor($id, Request $request,BookRepository $rep,ManagerRegistry $manager)
{
   //chercher l'author
    $books= $rep->find($id);
   //creer le formulaire
    $form = $this->createForm(BookType::class, $books);
    $form->handleRequest($request);
       if($form->isSubmitted()){
        $em=$manager->getManager();
        $em->persist($books);
        $em->flush();
       return $this->redirectToRoute('app_book');}
    return $this->render('book/editB.html.twig', ['form' => $form->createView(), ]);
}

//supprimer de  formulaire
#[Route("/delete-book/{id}", name: 'delete_book')]
public function deletebook(Request $request,$id, ManagerRegistry $manager,BookRepository $BookRepository): Response
{
    // Récupérez l'auteur depuis la base de données en utilisant l'ID
    $book = $BookRepository->find($id);
    //injecter
    $em =$manager->getManager();
    // Supprimez l'auteur
    $em->remove($book);
    $em->flush();
    return $this->redirectToRoute('app_book'); 
}

//chercher un book
#[Route('/research', name: 'research')]
    public function Research( BookRepository $repo, Request $request)
    {   $book= new Book();
        $form=$this->createForm(ReasearchType::class,$book);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            return $this->render('book/showB.html.twig', [  'books' => $repo->showDQL($book->getRef()), 'form'=>$form->createView() ]);
        }
        return $this->render('book/showB.html.twig', [  'books' => $repo->findAll(), 'form'=>$form->createView() ]);
    }


//querybuilder
//show avec queryBuilder
    #[Route('/showQyeryBuilder', name: 'showQyeryBuilder')]
    public function showBqueryBuilder(BookRepository $repo){
      $list=$repo->ShowAllBook();
      return $this->render('book/showB.html.twig',['books'=>$list]);
    }

    //afficher la liste des books par ref
    #[Route('/book/list/search', name: 'app_book_search', methods: ['GET', 'POST'])]
    public function searchBookByRef(Request $request, BookRepository $bookRepository): Response
    {
        $book = new Book();
        $form = $this->createForm(ReasearchType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            return $this->render('book/listB.html.twig', [
                'books' => $bookRepository->showAllBooksByRef($book->getRef()),
                'f' => $form->createView()
            ]);
        }
        return $this->render('book/listB.html.twig', [
            'books' => $bookRepository->findAll(),
            'f' => $form->createView()
        ]);
    }
//afficher la liste des books dans author dans une annee exacte avec dql 
#[Route('/book/list/QB', name: 'app_book_list_author_date', methods: ['GET'])]
public function showBooksByDateAndNbBooks(BookRepository $bookRepository): Response
{
    return $this->render('book/listB.html.twig', [
        'books' => $bookRepository->showBooksByDateAndNbBooks(10, '2023-01-01'),
    ]);
}

#[Route('/book/list/author/update/{category}', name: 'app_book_list_author_update', methods: ['GET'])]
public function updateBooksCategoryByAuthor($category, BookRepository $bookRepository): Response
{
    $bookRepository->updateBooksCategoryByAuthor($category);
    return $this->render('book/listBookAuthor.html.twig', [
        'books' => $bookRepository->findAll(),
    ]);
}



 //DQL  
//afficher la liste des books dans author avec dql    
    #[Route('/listBookDQL', name: 'listBookDQL')]
    public function listBookByAuthor(BookRepository $repo)
    {
        $books=$repo->booksListByAuthors();
        return $this->render('book/listB.html.twig',['books'=>$books]);
    }
    
    //DQL Afficher le nombre des livres dont la catégorie est « Romance »
    #[Route('/listBookRomance', name: 'listBookRomance')]
    public function listBookBycat(BookRepository $repo)
    {
        $books=$repo->countRomanceBooks();
        return $this->render('book/listB.html.twig',['books'=>$books]);
    }

   // Afficher la liste des livres publiés entre deux dates « 2014-01-01 » et «2018- 12-31 ».
    #[Route('/listBookDate', name: 'listBookDate')]
    public function listBookBydate(BookRepository $repo)
    {
        $books=$repo->findBooksBetweenDates();
        return $this->render('book/listB.html.twig',['books'=>$books]);
    }

    //DQL Modifier les livres dont la catégorie est « Science-Fiction » par « Romance ».
    #[Route('/listBookchange', name: 'listBookchange')]
    public function listBookchange(BookRepository $repo)
    {
        $books=$repo->UpdateQB();
        return $this->render('book/listB.html.twig',['books'=>$books]);
    }

}



