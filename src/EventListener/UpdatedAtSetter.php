<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdatedAtSetter
{
    // Ce listener se verra exécuté la méthode preUpdate lors d'un événement
    // C'est pour ça qu'il porte ce nom
    public function preUpdate(LifecycleEventArgs $args)
    {
        // $args contient l'objet conerné par l'événement
        // C'est-é-dire que n'importe quel objet d'une entité de notre projet
        // s'il est modifié et qu'il y a un flush, il peut être intercepté ici
        $entity = $args->getObject();

        if (!($entity instanceof User)) {
            $entity->setUpdatedAt(new \DateTime());
        }
    }
}