<?php

namespace App\Command;

use App\Repository\MovieRepository;
use App\Services\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MovieSlugifyCommand extends Command
{
    protected static $defaultName = 'app:movie:slugify';

    private $em;
    private $movieRepository;
    private $slugger;

    public function __construct(EntityManagerInterface $em, MovieRepository $movieRepository, Slugger $slugger)
    {
        // Sans cette ligne, on a une erreur qui nous dit que le 
        // constructeur parent de la commande n'a pas été exécuté
        parent::__construct();

        $this->em = $em;
        $this->movieRepository = $movieRepository;
        $this->slugger = $slugger;
    }

    protected function configure()
    {
        $this
            ->setDescription('Génère le slug pour tous les films')
            ->addArgument('movieId', InputArgument::OPTIONAL, 'Id d\'un film dont on veut calculer le slug')
            ->addOption('slug', null, InputOption::VALUE_REQUIRED, 'Le slug exact qu\'on veut pour un film')
            ;
        }
        
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $movieId = $input->getArgument('movieId');
        $exactSlug = $input->getOption('slug');
        
        // Si $movieId n'est pas nul, on modifie que le movie qui a cet id
        if ($movieId) {
            // $io->note(sprintf('You passed an argument: %s', $movieId));
            // On va chercher le film en base de données
            $movie = $this->movieRepository->find($movieId);
            
            // Pour le cas où $movie est null, ça veut dire que l'id n'existe en base de donnée,
            // donc on lève une exception
            if ($movie == null) {
                throw new \Exception('Aucun film n\'existe en base de donnée avec cet id.');
            }

            if ($exactSlug) {
                $movie->setSlug($exactSlug);
            } else {
                $movie->setSlug($this->slugger->slugify($movie->getTitle()));
            }
        } else {
            // Sinon modifie le slug de tous les movie
            // Notre objectif est de recalculer le slug de tous les films
            // Récupérons tous les films
            $movies = $this->movieRepository->findAll();
    
            foreach ($movies as $movie) {
                $movie->setSlug($this->slugger->slugify($movie->getTitle()));
            }
        }

        $this->em->flush();
        
        $io->success('Les ou les films ont été mis à jour');

        return 0;
    }
}

