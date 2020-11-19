<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Casting;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DevController extends AbstractController
{
    /**
     * Produit des fausses données pour la base de données
     * 
     * @Route("/dev/fake-data", name="dev_fake_data")
     */
    public function fakeData(EntityManagerInterface $em)
    {
        // Pour associer les objets entre eux, on crée des tableaux
        // On les initialise vides
        $genres = [];
        $persons = [];
        $movies = [];
        $castings = [];

        // Commençaons par créer des Genre
        $genre = new Genre();
        $genre->setName('Horreur');
        $genres[] = $genre;

        // On persiste l'objet
        $em->persist($genre);
        
        $genre = new Genre();
        $genre->setName('Comédie');
        $em->persist($genre);
        $genres[] = $genre;

        // Créons des Person
        $person = new Person();
        $person->setName('Parmène Pipoy');
        $em->persist($person);
        $persons[] = $person;

        $person = new Person();
        $person->setName('Kate Winslet');
        $em->persist($person);
        $persons[] = $person;

        $person = new Person();
        $person->setName('Leonardo Dicaprio');
        $em->persist($person);
        $persons[] = $person;

        // Créons des movie
        $movie = new Movie();
        $movie->setTitle('Jurassic Park');
        $em->persist($movie);
        $movies[] = $movie;

        $movie = new Movie();
        $movie->setTitle('E.T.');
        $em->persist($movie);
        $movies[] = $movie;

        $movie = new Movie();
        $movie->setTitle('Bohemian Rhapsody');
        $em->persist($movie);
        $movies[] = $movie;

        // Créons maintenant des Castings, il en faut plusieurs pour avoir des listes suffisament conséquentes à afficher
        $casting = new Casting();
        $casting->setRole('Néarque');
        $casting->setCreditOrder(mt_rand(1,42));
        $em->persist($casting);
        $castings[] = $casting;

        $casting = new Casting();
        $casting->setRole('Freddy');
        $casting->setCreditOrder(mt_rand(1,42));
        $em->persist($casting);
        $castings[] = $casting;

        $casting = new Casting();
        $casting->setRole('Bilbo');
        $casting->setCreditOrder(mt_rand(1,42));
        $em->persist($casting);
        $castings[] = $casting;

        $casting = new Casting();
        $casting->setRole('Alex');
        $casting->setCreditOrder(mt_rand(1,42));
        $em->persist($casting);
        $castings[] = $casting;

        $casting = new Casting();
        $casting->setRole('Obi-Wan');
        $casting->setCreditOrder(mt_rand(1,42));
        $em->persist($casting);
        $castings[] = $casting;

        $casting = new Casting();
        $casting->setRole('John');
        $casting->setCreditOrder(mt_rand(1,42));
        $em->persist($casting);
        $castings[] = $casting;

        $casting = new Casting();
        $casting->setRole('Artour Cuillère');
        $casting->setCreditOrder(mt_rand(1,42));
        $em->persist($casting);
        $castings[] = $casting;

        $casting = new Casting();
        $casting->setRole('Jack Gray');
        $casting->setCreditOrder(mt_rand(1,42));
        $em->persist($casting);
        $castings[] = $casting;

        $casting = new Casting();
        $casting->setRole('ET');
        $casting->setCreditOrder(mt_rand(1,42));
        $em->persist($casting);
        $castings[] = $casting;

        // Occupons maintenant de relier tout ce beau monde
        // On a créé des tableaux contenent tosu les objets créée plus tôt,
        // On va troucher un moyen de les mixer aléatoirement
        // On a besoin d'associer des genres à des films et une Person et un Movie à chaque Casting
        foreach ($movies as $currentMovie) {
            // Pour chacun des Movie, on associe un genre au hasard
            // On mélange d'abord les objets dans $genres
            shuffle($genres);
            $currentMovie->addGenre($genres[0]);
        }
        // Pour le dernier film de la boucle, on lui ajoute aussi l'autre genre créé plus haut
        $currentMovie->addGenre($genres[1]);

        // On peut imiter la même technique pour les Casting
        foreach ($castings as $currentCasting) {
            // On mélange la liste des Person et des Movie
            shuffle($persons);
            shuffle($movies);

            // On affecte le premier objet de chaque liste à notre casting
            $currentCasting->setPerson($persons[0]);
            $currentCasting->setMovie($movies[0]);
        }

        // On flush, et on n'a pas besoin de repersister les objets
        $em->flush();


        return $this->render('dev/index.html.twig', [
            'controller_name' => 'DevController',
        ]);
    }

    /**
     * @Route("/dev/test-doctrine")
     */
    public function testDoctrine()
    {
        $actor = new Actor();

        dd($actor);
    }
}
