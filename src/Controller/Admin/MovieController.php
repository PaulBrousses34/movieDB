<?php

namespace App\Controller\Admin;

use App\Entity\Movie;
use App\Form\DeleteType;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use App\Services\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/movie", name="admin_movie_")
 */
class MovieController extends AbstractController
{   
    // C'est commenté mais on pourrait faire une injection de dépdance
    // dans un contr'oleur pour y inséreir, par exemple, l'EntityManager
    // private $em;

    // public function __construct(EntityManagerInterface $em)
    // {
    //     $this->em = $em;
    // }

    /**
     * @Route("/", name="browse")
     */
    public function browse(MovieRepository $movieRepository)
    {
        return $this->render('admin/movie/browse.html.twig', [
            'movies' => $movieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", requirements={"id": "\d+"})
     */
    public function edit(Movie $movie, Request $request, Slugger $slugger)
    {
        // Testons notre voter
        // $this->denyAccessUnlessGranted('EDIT', $movie);

        $form = $this->createForm(MovieType::class, $movie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On utilise le service Slugger, il a une méthode slugify
            // qui calcule le slug de n'importe quelle chaien de caractère
            // On place le résultat de slugify() dans la propriété $slug de $movie
            $movie->setSlug($slugger->slugify($movie->getTitle()));

            // On a mis à jour notre objet $movie, on change donc la valeur de $updatedAt
            // $movie->setUpdatedAt(new \Datetime());
            
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_movie_browse');
        }

        // On prépare un formulaire pour supprimer le Movie
        $formDelete = $this->createForm(DeleteType::class, null, [
            'action' => $this->generateUrl('admin_movie_delete', ['id' => $movie->getId()])
        ]);

        return $this->render('admin/movie/edit.html.twig', [
            'form' => $form->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request, Slugger $slugger)
    {
        // On initialise un nouvel objet $movie
        $movie = new Movie();

        // On crée le formulaire et on l'associe à notre Movie
        $form = $this->createForm(MovieType::class, $movie);

        $form->handleRequest($request);

        // On vérfie si le fomurlaire est envoyé et si les données reçues sont valides
        if ($form->isSubmitted() && $form->isValid()) {
            // On utilise le service slugger pour calculer le slug de ce nouveau film
            $title = $movie->getTitle();
            $slug = $slugger->slugify($title);
            $movie->setSlug($slug);            

            // On récupère l'EntityManager
            $em = $this->getDoctrine()->getManager();
            // On persiste $movie, car c'est un nouveau Movie
            $em->persist($movie);
            // On flush
            $em->flush();

            // Notre objet est en BDD, on redirige vers la liste des Movies
            return $this->redirectToRoute('admin_movie_browse');
        }

        return $this->render('admin/movie/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id": "\d+"}, methods={"DELETE"})
     */
    public function delete(EntityManagerInterface $em, Movie $movie, Request $request)
    {
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        // isValid() va vérifier le token CSRF du formulaire et ainsi
        // on s'assure que la requête n'a pas été forgée par un tier
        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            $em->remove($movie);
            $em->flush();
        }

        return $this->redirectToRoute('admin_movie_browse');
    }
}
