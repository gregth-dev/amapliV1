<?php

namespace App\Form;

use App\Entity\Organism;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OrganismType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => "Saisissez le nom de l'association"
                ]
            ])
            ->add('amount', NumberType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => "Saisissez le montant de l'adhésion"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Organism::class,
        ]);
    }
}
