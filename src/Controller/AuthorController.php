<?php


namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType; // Ajout de l'importation de la classe AuthorType
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AuthorController extends AbstractController
{
    
    public $authors = array(


        array(
            'id' => 1, 'picture' => '/images/Victor-Hugo.jpg',
            'username' => ' Victor Hugo', 'email' => 'victor.hugo@gmail.com ', 'nb_books' => 100
        ),
        array(
            'id' => 2, 'picture' => '/images/william-shakespeare.jpg',
            'username' => ' William Shakespeare', 'email' => ' william.shakespeare@gmail.com', 'nb_books' => 200
        ),
        array(
            'id' => 3, 'picture' => '/images/Taha_Hussein.jpg',
            'username' => ' Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300
        ),
    );

    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/author/{id}', name: 'app_show')]
    public function showAuthor($id,AuthorRepository $repoA){
        $author=$repoA->find($id);
      return $this->render('author/show.html.twig',['author'=>$author]);
    }
    #[Route('/list',name: 'list')]
    public function list(){
        $authors = array(
            array('id' => 1, 'picture' => 'images/victor-hugo.jpg','username' => 'Victor Hugo', 'email' =>
                'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => 'images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>
                ' william.shakespeare@gmail.com', 'nb_books' => 200 ),
            array('id' => 3, 'picture' => 'images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' =>
                'taha.hussein@gmail.com', 'nb_books' => 300),
        );
    return $this->render('author/list.html.twig',['authors'=>$authors]);
    }
    #[Route('/show/{id}',name: 'show')]
    public function auhtorDetails ($id)
    {
        $author = null;
        // Parcourez le tableau pour trouver l'auteur correspondant à l'ID
        foreach ($this->authors as $authorData) {
            if ($authorData['id'] == $id) {
                $author = $authorData;
            };
        };
        return $this->render('author/showAuthor.html.twig', [
            'author' => $author,
            'id' => $id
        ]);
    }
            //afficher la liste des auteurs
    #[Route('/listAuthor', name: 'app_list')]
    public function affiche(AuthorRepository $ARepo, ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();
        $authors = $ARepo->findAll();

        return $this->render('author/affiche.html.twig', [

            'authors' => $authors

        ]);
    }
   
    //ajouter a partir d'un formulaire
    #[Route("/form/new", name:'form_new')]
    
   public function newUser(Request $request,ManagerRegistry $manager)
   {
       $authors = new Author(); 
       //ijecter le formualire
       $form = $this->createForm(AuthorType::class, $authors);
       $form->handleRequest($request);
       if($form->isSubmitted()){
        $em=$manager->getManager();
        $em->persist($authors);
        $em->flush();
        return $this->redirectToRoute('app_list');
    }


       return $this->render('author/form.html.twig', ['form' => $form->createView()]);  

    }
//ajouter author dans la base de maniere statique
    #[Route("/add-author", name: 'add_author')]
    public function addAuthor(ManagerRegistry $manager)
    {
        // Créez une nouvelle instance de l'auteur
        $authors = new Author();
        //je veux ajouter ses deux attribut
        $authors->setUsername('testStatic');
        $authors->setEmail('test@gmail.com');
        // recuperer le manager
        $em=$manager->getManager();
        $em->persist($authors);
        $em->flush();

            return new Response('Author added succesfully');
        }

//modifier le formulaire
    #[Route("/edit-author/{id}", name: 'edit_author')]
public function editAuthor($id, Request $request,AuthorRepository $rep,ManagerRegistry $manager)
{
   //chercher l'author
    $authors = $rep->find($id);
   //creer le formulaire
    $form = $this->createForm(AuthorType::class, $authors);
    $form->handleRequest($request);
       if($form->isSubmitted()){
        $em=$manager->getManager();
        $em->persist($authors);
        $em->flush();
        return $this->redirectToRoute('app_list');}
    return $this->render('author/edit.html.twig', ['form' => $form->createView(), ]);

}

#[Route("/delete-author/{id}", name: 'delete_author')]
public function deleteAuthor(Request $request,$id, ManagerRegistry $manager,AuthorRepository $authorRepository): Response
{
    // Récupérez l'auteur depuis la base de données en utilisant l'ID
    $authors = $authorRepository->find($id);


    //injecter
    $em =$manager->getManager();
    // Supprimez l'auteur
    $em->remove($authors);
    $em->flush();

    return $this->redirectToRoute('app_list'); 
}

}





