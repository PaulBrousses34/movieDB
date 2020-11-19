<?php

namespace App\Controller\Api\V1;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Services\Slugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v1/movies", name="api_v1_movie_")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(MovieRepository $movieRepository, SerializerInterface $serializer)
    {
        // On récupère tous les films, ce sont des objets Movie classiques
        $movies = $movieRepository->findAll();

        // On demande au Serializer de normaliser nos films
        // C'est-à-dire qu'on lui demande de transformer nos objets en array
        // De plus, on lui spécifie qu'on veut normaliser selon le groupe «movie_browse«
        // présent dans les annotations de notre entité Movie
        $arrayMovies = $serializer->normalize($movies, null, ['groups' => 'movie_browse']);

        // La méthode json() retourne un objet JsonResponse qui est un objet Response particulier
        // avec un Content-Type adapté au JSON
        // et la méthode sérialise (c'est-à-dire elle fait un json_encode) nos données
        return $this->json($arrayMovies);
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function read(Movie $movie, SerializerInterface $serializer)
    {
        $arrayMovie = $serializer->normalize($movie, null, ['groups' => 'movie_read']);

        return $this->json($arrayMovie);
    }

    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(Request $request, SerializerInterface $serializer, Slugger $slugger)
    {
        // On crée un nouvel objet à partir du JSON reçu
        $jsonData = json_decode($request->getContent());

        // On crée un nouveau Movie
        $movie = new Movie();

        // On attribue le title :
        $movie->setTitle($jsonData->title);
        $movie->setSlug($slugger->slugify($jsonData->title));

        // On periste le Movie
        $em = $this->getDoctrine()->getManager();
        $em->persist($movie);
        $em->flush();

        return $this->json(
            // On sérialise l'objet qui vient d'être créé
            $serializer->normalize(
                $movie,
                null,
                ['groups' => 'movie_read']
            ),
            201 // On précise le code de status de réponse pour confirmer que le film est ajouté
        );
    }

}
