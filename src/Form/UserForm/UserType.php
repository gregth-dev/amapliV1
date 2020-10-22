<?php

namespace App\Form\UserForm;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'lastName',
                TextType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Saisir le nom',
                    ],
                ]
            )
            ->add(
                'firstName',
                TextType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Saisir le prénom',
                    ]
                ]
            )
            ->add(
                'address',
                TextType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => "Saisir l'addresse",
                    ]
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Saisir la ville',
                    ]
                ]
            )
            ->add(
                'postcode',
                TextType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Saisir le code postal',
                    ]
                ]
            )
            ->add(
                'phone1',
                TelType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Téléphone 1',
                    ]
                ]
            )
            ->add(
                'phone2',
                TelType::class,
                [
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Téléphone 2 (facultatif)',
                    ]
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => false,
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Saisir le mail',
                    ]
                ]
            )
            ->add(
                'email2',
                EmailType::class,
                [
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Saisir le mail secondaire (facultatif)',
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['registration'],
        ]);
    }
}
