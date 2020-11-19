<?php

namespace App\Controller\Admin;

use App\Entity\Genre;
use App\Form\DeleteType;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/genre", name="admin_genre_")
 */
class GenreController extends AbstractController
{
    /**
     * @Route("/", name="browse")
     */
    public function browse(GenreRepository $genreRepository)
    {
        return $this->render('admin/genre/browse.html.twig', [
            'genres' => $genreRepository->findAll(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", requirements={"id": "\d+"})
     */
    public function edit(Genre $genre, Request $request)
    {
        $form = $this->createForm(GenreType::class, $genre);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On a mis à jour notre objet $genre, on change donc la valeur de $updatedAt
            // $genre->setUpdatedAt(new \Datetime());

            // Grâce à handleRequest(), l'objet $genre a été modifié
            // et s'est vu attribué les valeurs reçues depuis le formulaire
            // $em = $this->getDoctrine()->getManager();
            // $em->flush();
            // On peut écrire les deux lignes précédentes en une seule
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_genre_browse');
        }

        // On prépare un formulaire pour supprimer le genre
        // Voici comment on ferait san FormType, pour créer le formulaire direct dans le contrôleur
        /*
        $formDelete = $this->createFormBuilder() // createFormBuilder donne le même objet que le $builder dans nos FormType
                    ->setAction($this->generateUrl('admin_genre_delete', ['id' => $genre->getId()]))
                    ->setMethod('DELETE')
                    ->add('deleteButton', SubmitType::class, [
                        'label' => 'Supprimer',
                    ])
                    // On n'a pas à faire ça dans un FormType, mais ici on doit demandé au FormBuilder
                    // de nous fournir l'objet Form quand on a fini de lui ajouter des champs
                    ->getForm()
                    ;  
        */
        $formDelete = $this->createForm(DeleteType::class, null, [
            'action' => $this->generateUrl('admin_genre_delete', ['id' => $genre->getId()])
        ]);

        return $this->render('admin/genre/edit.html.twig', [
            'form' => $form->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request)
    {
        // On initialise un nouvel objet $genre
        $genre = new Genre();

        // On crée le formulaire et on l'associe à notre Genre
        $form = $this->createForm(GenreType::class, $genre);

        $form->handleRequest($request);

        // On vérfie si le fomurlaire est envoyé et si les données reçues sont valides
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère l'EntityManager
            $em = $this->getDoctrine()->getManager();
            // On persiste $genre, car c'est un nouveau Genre
            $em->persist($genre);
            // On flush
            $em->flush();

            // Notre objet est en BDD, on redirige vers la liste des genres
            return $this->redirectToRoute('admin_genre_browse');
        }

        return $this->render('admin/genre/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id": "\d+"}, methods={"DELETE"})
     */
    public function delete(EntityManagerInterface $em, Genre $genre, Request $request)
    {
        // On pourrait se contenter de ces trois lignes
        // Cependant, on souhaite vérifier le token du formulaire de suppression
        // $em->remove($genre);
        // $em->flush();
        // return $this->redirectToRoute('admin_genre_browse');

        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        // isValid() va vérifier le token CSRF du formulaire et ainsi
        // on s'assure que la requête n'a pas été forgée par un tier
        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            $em->remove($genre);
            $em->flush();
        }

        return $this->redirectToRoute('admin_genre_browse');
    }
}
