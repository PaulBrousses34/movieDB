<?php

namespace App\Command;

use App\Repository\MovieRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MovieGetPostersCommand extends Command
{
    protected static $defaultName = 'app:movie:get-posters';

    private $movieRepository;

    public function __construct(MovieRepository $movieRepository)
    {
        parent::__construct();

        $this->movieRepository = $movieRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Va récupérer tous les posters des films en base de données')
            // On pourrait, comme pour app:movie:slugify, donner la possibilité de préciser l'id d'un film
            // De même, on pourrait proposer de préciser soi-même l'url d'un poster
            // ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // On va récupérer la liste de tous nos films
        $movies = $this->movieRepository->findAll();

        foreach ($movies as $movie) {
            $title = $movie->getTitle();

            // Il semblerait que la fonction file_get_contents ou l'API n'apprécie pas les espaces dans l'URL
            // On va les remplacer par leur équivalent pour les URL : %20
            $title = urlencode($title);

            // On déclare l'URL pour contacter l'API
            $url = 'http://www.omdbapi.com/?apikey='.$_ENV['OMDB_KEY'].'&t='.$title;

            // On exécute la requête et on reécupère le JSON le reçù (en string)
            $json = file_get_contents($url);
            // On utilise json_decode() pour retrouver un objet
            $omdbResult = json_decode($json);

            // On ajoute le fichier que si On a bien reçu un poster pour ce film
            // c-à-d : l'objet JSON contient bien une propriété Poster
            if (isset($omdbResult->Poster) && $omdbResult->Poster != "N/A") {
                // On va chercher le contenu de l'image
                $image = file_get_contents($omdbResult->Poster);
                // On prend le contenu et on l'envoie dans un fichier
                file_put_contents('public/posters/'.$movie->getId().'.jpg', $image);
            }
        }

        $io = new SymfonyStyle($input, $output);

        // Attention, on n'a pas annoncé quels films n'avaient pas de posters.
        // Ça pourait être une fonctionnalité pour plus tard.
        $io->success('Tous les posters pour les films en base de données ont été récupérés.');
        
        return 0;
    }
}
