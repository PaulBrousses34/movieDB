<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
// use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
// use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            // ->add('roles', CollectionType::class, [
            //     'entry_type' => TextType::class,
            //     'allow_add' => true,
            //     'allow_delete' => true,
            // ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Utilisateur' => 'ROLE_USER',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => false,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Retapez le mot de passe'],
                'constraints' => [
                    new Assert\NotBlank([
                        'allowNull' => true,
                        'normalizer' => 'trim',
                    ]),
                    new Assert\Length([
                        'min' => 5,
                    ])
                ]
            ])

            // On utilise des FormEvents (événements de formulaire
            // pour modifier le formulaire en fonction du contexte)
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                // Dans $event on a le formulaire et les données
                $form = $event->getForm();
                $user = $event->getData();

                // Un id null dans $user veut dire qu'on crée un utilisateur
                if ($user->getId() === null) {
                    // On souhaite ajouter un champs pour accepter les CGU
                    $form->add('cgu', CheckboxType::class, [
                        'label' => 'J\'accepte les CGU',
                        'required' => true,
                        'mapped' => false,
                    ]);
                    // On souhaite que les champs du mot de passe soient requis lors de l'ajout d'un utilisateur
                    // Il n'est pas possible de modifier un champs existant
                    // On ne peut que le supprimer et l'ajouter à nouveau
                    $form->remove('password')
                        ->add('password', RepeatedType::class, [
                            'type' => PasswordType::class,
                            'mapped' => false,
                            'required' => true,
                            'first_options'  => ['label' => 'Mot de passe'],
                            'second_options' => ['label' => 'Retapez le mot de passe'],
                            'constraints' => [
                                new Assert\NotBlank([
                                    'normalizer' => 'trim',
                                ]),
                                new Assert\Length([
                                    'min' => 5,
                                ])
                            ]
                        ]);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
