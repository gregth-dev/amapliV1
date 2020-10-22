<?php

namespace App\Form;

use App\Entity\HTFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class HtAccessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => "Nom d'utilisateur"
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => "mot de passe"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => HTFile::class,
        ]);
    }
}
