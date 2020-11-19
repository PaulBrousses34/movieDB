<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use App\Entity\User;
use App\Services\Slugger;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
// use Nelmio\Alice\Loader\NativeLoader;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class NelmioAliceFixtures extends Fixture
{
    private $slugger;
    private $passwordEncoder;

    public function __construct(Slugger $slugger, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->slugger = $slugger;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $em)
    {
        $loader = new MovieDbNativeLoader();
        
        //importe le fichier de fixtures et récupère les entités générés
        $entities = $loader->loadFile(__DIR__.'/fixtures.yaml')->getObjects();
        
        //empile la liste d'objet à enregistrer en BDD
        foreach ($entities as $entity) {
            // On calcule le slug que si on a affaire à un Movie
            if ($entity instanceof Movie) {
                $entity->setSlug($this->slugger->slugify($entity->getTitle()));
            } elseif ($entity instanceof User) {
                // Pour ajouter le mdp encodé à un utilisateur,
                // on peut lui donner directement ce qu'on a obtenur avec la commande
                // bin/console security:encode-password
                // $entity->setPassword('$argon2id$v=19$m=65536,t=4,p=1$/URqBujmigdPs0hp+vTK6g$MjV9hFfj7KnNjwkW3ga5jyUIxsRMz3o0FmGt2v5PPGk');
                $encodedPassword = $this->passwordEncoder->encodePassword($entity, 'Derrick');
                $entity->setPassword($encodedPassword);
            }

            $em->persist($entity);
        };
        
        //enregistre
        $em->flush();
    }
}
