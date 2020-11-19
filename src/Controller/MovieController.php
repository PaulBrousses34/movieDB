<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    /**
     * @Route("/", name="movie_browse")
     */
    public function browse(MovieRepository $movieRepository)
    {
        return $this->render('movie/browse.html.twig', [
            'movies' => $movieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/movie/{id}", name="movie_read", requirements={"id": "\d+"})
     */
    public function readId(Movie $movie)
    {
        return $this->redirectToRoute('movie_read_slug', [
            'slug' => $movie->getSlug(),
        ]);
    }

    /**
     * @Route("/movie/{slug}", name="movie_read_slug")
     */
    public function read(Movie $movie, MovieRepository $movieRepository)
    {
        /*
        // On peut utiliser le QueryBuilder dans un contrôleur
        // Cependant, c'est beaucoup plus propre de placer ce code dans le Repository
        // On commente donc l'ensemble et on utilise maintenant le MovieRepository
        
        // // On récupère l'EntityManager qui nous fournira le QueryBuilder
        // $em = $this->getDoctrine()->getManager();

        // // On lui demande de nous créer un nouveau QueryBuilder
        // $qb = $em->createQueryBuilder();
        // $qb
        //     //On sélectionne d'abord l'objet Movie
        //     ->from('App\Entity\Movie', 'm')
        //     ->select('m')

        //     // On fait la jointure avec ses genres
        //     ->join('m.genres', 'g')
        //     ->addSelect('g')

        //     // On précise quel est l'îd du Movie qu'on veut obtenir
        //     ->where('m.id = :id')
        //     ->setParameter('id', $id)

        //     // Pour obtenir les objets Casting reliés à ce film, on fait aussi la jointure avec la propriété castings du Movie
        //     ->join('m.castings', 'c')
        //     ->addSelect('c')
            
        //     // Chaque Casting étant forcément relié à une Person, on fait aussi cette jointure pour avoir les informations de chaque Person ayant joué dans notre film
        //     ->join('c.person', 'p')
        //     ->addSelect('p')
        // ;
        */

        // $movie = $qb->getQuery()->getOneOrNullResult();

        $movie = $movieRepository->getMovieWithRelations($movie->getId());
        
        // Éventuellement $movie vaut null, si c'est le cas, on génère une 404
        if ($movie === null) {
            throw $this->createNotFoundException('Ce film n\'existe pas');
        }

        return $this->render('movie/read.html.twig', [
            'movie' => $movie,
        ]);
    }
}
