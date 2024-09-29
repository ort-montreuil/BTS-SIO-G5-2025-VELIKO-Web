<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('roles')
            ->add('password')
            ->add('nom')
            ->add('prenom')
            ->add('date_naissance', null, [
                'widget' => 'single_text'
            ])
            ->add('adresse')
            ->add('code_postal')
            ->add('last_password_changed', null, [
                'widget' => 'single_text'
            ])
            ->add('ville');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
