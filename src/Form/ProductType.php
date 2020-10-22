<?php

namespace App\Form;

use App\Entity\Producer;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom :',
                'attr' => [
                    'placeholder' => 'Saisissez le nom du produit'
                ]
            ])
            ->add('details', TextType::class, [
                'label' => 'Détail :',
                'attr' => [
                    'placeholder' => 'Saisissez le conditionnement du produit'
                ]
            ])
            ->add(
                'price',
                NumberType::class,
                [
                    'label' => 'Prix :',
                    'attr' => [
                        'placeholder' => 'Saisissez le prix du produit'
                    ]
                ]
            )
            ->add('deposit', NumberType::class, [
                'required' => false,
                'label' => 'Caution :',
                'attr' => [
                    'placeholder' => 'Saisissez une caution pour ce produit si nécessaire'
                ]
            ])
            ->add('producer', EntityType::class, [
                'label' => 'Sélectionnez un producteur :',
                'class' => Producer::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'js-select'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
