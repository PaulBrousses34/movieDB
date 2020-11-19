<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DirectorRepository")
 */
class Director extends Employment
{
    public function __toString()
    {
        return $this->getPerson()->getName();
    }

}
