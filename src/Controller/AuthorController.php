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

    #[Route('/author/{n}', name: 'app_show')]
    public function showAuthor($n){
      return $this->render('author/show.html.twig',['name'=>$n]);
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
    #[Route('/listAuthor', name: 'app_list')]
    public function affiche(AuthorRepository $ARepo, ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();
        $authors = $ARepo->findAll();

        return $this->render('author/affiche.html.twig', [

            'authors' => $authors

        ]);
    }
    #[Route('/ajoutAuthor', name: 'app_ajout')]
    /**
     * Summary of ajout
     * @param \App\Controller\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajouter (Request $request)
    {
      $authors=new Author();
      $authors->setUsername('Nour Salhi');
      $authors->setEmail('salhi.nour@esprit.tn');
      $em = $this->getDoctrine()->getManager();
      $em->persist($authors);
      $em->flush();
      return new Response('author cree avec id :' .$authors->getId());
    }
    #[Route("/form/new", name:'form_new')]
    
   public function newUser(Request $request, EntityManagerInterface $entityManager)
   {
       $authors = new Author(); 
       $form = $this->createForm(AuthorType::class, $authors);
       $form->handleRequest($request);
   
       if ($form->isSubmitted() && $form->isValid()) {
           // Enregistrez l'utilisateur dans la base de données
           $entityManager->persist($authors);
           $entityManager->flush();
   
           return $this->redirectToRoute('index'); // Redirigez vers la page de liste des utilisateurs
       }
   
       return $this->render('author/form.html.twig', [
           'form' => $form->createView(),
       ]);
   }

    #[Route("/add-author", name: 'add_author')]
    public function addAuthor(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créez une nouvelle instance de l'auteur
        $authors = new Author();

        // Créez le formulaire en utilisant le formulaire AuthorType et l'auteur
        $form = $this->createForm(AuthorType::class, $authors);

        // Gérez la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrez l'auteur dans la base de données
            $entityManager->persist($authors);
            $entityManager->flush();

            // Redirigez vers une page de confirmation ou une autre page
            return $this->redirectToRoute('app_list'); // Redirigez vers la page d'accueil, vous pouvez la personnaliser
        }

        // Affichez le formulaire dans une vue Twig
        return $this->render('author/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/edit-author/{id}", name: 'edit_author')]
public function editAuthor($id, Request $request): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $authors = $entityManager->getRepository(Author::class)->find($id);

    if (!$authors) {
        throw $this->createNotFoundException('Aucun auteur trouvé pour l\'ID : ' . $id);
    }

    $form = $this->createForm(AuthorType::class, $authors);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        $this->addFlash('success', 'Les informations de l\'auteur ont été mises à jour avec succès.');

        return $this->redirectToRoute('app_list'); // Redirigez vers la liste des auteurs ou une autre page
    }

    return $this->render('author/form.html.twig', [
        'form' => $form->createView(),
    ]);
}
#[Route("/delete-author/{id}", name: 'delete_author')]
public function deleteAuthor($id, EntityManagerInterface $entityManager): Response
{
    // Récupérez l'auteur depuis la base de données en utilisant l'ID
    $authors = $entityManager->getRepository(Author::class)->find($id);

    if (!$authors) {
        throw $this->createNotFoundException('Aucun auteur trouvé pour l\'ID : ' . $id);
    }

    // Supprimez l'auteur
    $entityManager->remove($authors);
    $entityManager->flush();

    $this->addFlash('success', 'L\'auteur a été supprimé avec succès.');


    return $this->redirectToRoute('app_list'); 
}
}





