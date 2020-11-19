<?php

namespace App\DataFixtures;

use App\Entity\Casting;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use App\DataFixtures\Providers\MovieAndGenreProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class FakerFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Pour utiliser Faker, il nous faut un objet Factory
        $faker = Faker\Factory::create('fr_FR');
        // On définit le seed de Faker, ça va permettre de déterminer,
        // dans son algo qui détermine les données au hasard, de «choisir» les données qu'on veut
        $faker->seed(1337);

        // On peut ajouter un Provider à notre $faker
        $faker->addProvider(new MovieAndGenreProvider($faker));

        // Pour associer les objets entre eux, on crée des tableaux
        // On les initialise vides
        $genres = [];
        $persons = [];
        $movies = [];
        $castings = [];

        for ($i = 0; $i < 10; $i++) {
            $genre = new Genre();
            $genre->setName($faker->movieGenre());
            $genre->setCreatedAt($faker->datetime());
            $manager->persist($genre);
            $genres[] = $genre;
        }

        for ($i = 0; $i < 30; $i++) {
            $person = new Person();
            $person->setName($faker->firstname().' '.$faker->lastname());
            $person->setCreatedAt($faker->datetime());
            $manager->persist($person);
            $persons[] = $person;
        }

        for ($i = 0; $i < 15; $i++) {
            $movie = new Movie();
            $movie->setTitle($faker->movieTitle());
            $movie->setCreatedAt($faker->datetime());

            // Avant de persister, on associe notre Movie à un nombre de Genres aléatoire
            for ($j = 0; $j < mt_rand(1, 3); $j++) {
                shuffle($genres);
                $movie->addGenre($genres[0]);
            }

            $manager->persist($movie);
            $movies[] = $movie;
        }

        for ($i = 0; $i < 150; $i++) {
            $casting = new Casting();
            $casting->setRole($faker->firstname());
            $casting->setCreditOrder($faker->numberBetween(1,120));

            // Avant de persister, on associe notre casting à une person et un Movie
            shuffle($persons);
            shuffle($movies);
            $casting->setPerson($persons[0]);
            $casting->setMovie($movies[0]);

            $manager->persist($casting);
            $castings[] = $casting;
        }

        $manager->flush();
    }
}
