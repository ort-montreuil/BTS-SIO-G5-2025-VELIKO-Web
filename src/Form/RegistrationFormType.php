<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    // La vue est affichée dans la méthode register() du contrôleur
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Ajouter un champ pour l'email
        $builder
            ->add('email', EmailType::class)
            ->add('nom')
            ->add('prenom')
            // Ajouter un champ pour la date de naissance avec un widget de type single_text
            ->add('date_naissance', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
            ])
            // Ajouter un champ pour l'adresse
            ->add('adresse')
            // Ajouter un champ pour le code postal
            ->add('code_postal')
            // Ajouter un champ pour la ville
            ->add('ville')
            // Ajouter un champ pour le mot de passe avec des contraintes de validation
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a password']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
            // Ajouter une case à cocher pour accepter les termes avec une contrainte de validation
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(['message' => 'You should agree to our terms.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Définir les options par défaut pour le formulaire
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}