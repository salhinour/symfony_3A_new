<?php

namespace App\Controller;
use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
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

            //return $this->redirectToRoute('app_book');
        }

        return $this->render('book/formBook.html.twig', ['form' => $form->createView()]);
    }
    #[Route('/listBook', name: 'app_book')]
    public function affiche(BookRepository $BRepo, ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();
        $publishedBooks = $BRepo->findBy(['published' => true]);
        $unpublishedBooks = $BRepo->findBy(['published' => false]); // Récupère les livres non publiés


        return $this->render('book/afficheB.html.twig', [

            'books' => $publishedBooks,
            'unpublishedBooks' => $unpublishedBooks,

        ]);
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
//Créer une méthode qui permet de supprimer les auteurs dont le « nb_books » est égale à zéro
#[Route("/delete-bookk", name: 'delete_book')]
public function deleteAuthorsAndBooks(ManagerRegistry $manager, AuthorRepository $authorRepository, BookRepository $bookRepository): Response
    {
        $em = $manager->getManager();

        // Récupérez les auteurs ayant nb_books égal à zéro
        $authorsToDelete = $authorRepository->findBy(['nb_books' => 0]);

        // Supprimez les livres associés à ces auteurs
        foreach ($authorsToDelete as $author) {
            $booksToDelete = $bookRepository->findBy(['author' => $author]);
            foreach ($booksToDelete as $book) {
                $em->remove($book);
            }
        }

        // Supprimez les auteurs
        foreach ($authorsToDelete as $author) {
            $em->remove($author);
        }

        $em->flush();

        return $this->redirectToRoute('app_book');
    }
    #[Route('/book/{ref}', name: 'app_showB')]
    public function showAuthor($ref,BookRepository $repoB){
        $book=$repoB->find($ref);
      return $this->render('book/showB.html.twig',['book'=>$book]);
    }
}




