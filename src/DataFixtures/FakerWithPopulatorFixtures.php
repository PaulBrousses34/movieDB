<?php

namespace App\DataFixtures;

use App\DataFixtures\Providers\MovieAndGenreProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class FakerWithPopulatorFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // On crée notre générateur de données
        $generator = Faker\Factory::create('fr_FR');
        // On aurait pu préciser un seed() mais on ne l'a pas fait

        // On ajouté au générateur notre propre fournisseur de données
        $generator->addProvider(new MovieAndGenreProvider($generator));

        // On crée le poulator qui sait créer des entités pour Doctrine à partir de $manager
        $populator = new Faker\ORM\Doctrine\Populator($generator, $manager);

        // On demande au populator de nous créer des entités
        $populator->addEntity('App\Entity\Genre', 20, [
            'name' => function() use ($generator) { return $generator->unique()->movieGenre(); },
        ]);

        $populator->addEntity('App\Entity\Person', 20, [
            'name' => function() use ($generator) { return $generator->name(); },
        ]);

        $populator->addEntity('App\Entity\Movie', 10, [
            'title' => function() use ($generator) { return $generator->unique()->movieTitle(); },
        ]);

        $populator->addEntity('App\Entity\Casting', 50, array(
            'creditOrder' => function() use ($generator) { return $generator->numberBetween(1, 120); },
            'role' => function() use ($generator) { return $generator->firstName(); },
        )); 

        // On a précisé au populator ce qu'on voulait mais ça n'a encore rien exécuté
        // On crée toutes les données d'un coup avec ->execute();

        $inserted = $populator->execute();

        // Le populator de Faker sait parfaitement gérer toutes les relations sauf les ManyToMany
        // On doit donc faire ces relatiosn à la main

        // On obtient tous les Movie et tous les Genre créés par le populator grâce à $inserted
        $movies = $inserted['App\Entity\Movie'];
        $genres = $inserted['App\Entity\Genre'];

        foreach ($movies as $movie) {
            for ($j = 0; $j < mt_rand(1, 3); $j++) {
                shuffle($genres);
                $movie->addGenre($genres[0]);
            }
        }

        $manager->flush();
    }
}
