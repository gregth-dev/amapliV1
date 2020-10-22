<?php

namespace App\Form\UserForm;

use App\Entity\PasswordUpdate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => "Mot de passe actuel",
                'attr' => [
                    'placeholder' => "Votre mot de passe actuel..."
                ]
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => "Nouveau mot de passe",
                'attr' => [
                    'placeholder' => "Votre nouveau mot de passe..."
                ]
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => "Confirmer le mot de passe",
                'attr' => [
                    'placeholder' => "Confirmation du nouveau mot de passe..."
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PasswordUpdate::class,
        ]);
    }
}
