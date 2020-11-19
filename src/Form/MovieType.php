<?php

namespace App\Form;

use App\Entity\Movie;
use App\Repository\GenreRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            // Le deuxième argument de add() est le type de champs
            // Il est déterminé automatiquement par Symfony
            // en lisant les propriétés de notre entité
            // Si on ne souhaite pas le modifier, on peut mettre null,
            // ce qui nous permet d'ajouter des options pour ce champs en troisième argument
            ->add('genres', null, [
                'expanded' => true,
                // Une option query_builder nous permet de spécifier comment récupérer les genres en BDD
                // il s'agit d'un QueryBuilder comme on en utilise dans le repository
                'query_builder' => function (GenreRepository $genreRepository) {
                    return $genreRepository->createQueryBuilder('g')
                        ->orderBy('g.name', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
