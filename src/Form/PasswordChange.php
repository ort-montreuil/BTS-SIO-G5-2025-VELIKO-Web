<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordChange extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('entrezVotreAncienMotDePasse', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your current password',
                    ]),
                ],
            ])
            ->add('entrezVotreNouveauMotDePasse', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a new password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your new password should be at least {{ limit }} characters',
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Changez mon mot de passe',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}