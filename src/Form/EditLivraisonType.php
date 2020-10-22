<?php

namespace App\Form;

use App\Entity\Producer;
use App\Entity\EditLivraison;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EditLivraisonType extends AbstractType
{
    private $transformer;

    public function __construct(FrenchToDateTimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('producer', EntityType::class, [
                'class' => Producer::class,
                'label' => false,
                'choice_label' => 'name',
                'multiple' => false,
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('orientation', ChoiceType::class, [
                'required' => true,
                'label' => false,
                'multiple' => false,
                'choices'  => [
                    'Format portrait' => 'portrait',
                    'Format paysage' => 'landscape',
                ],
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('startDate', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Date de début',
                    'class' => 'dateInput'
                ]
            ])
            ->add('endDate', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Date de fin',
                    'class' => 'dateInput'
                ]
            ])
            ->add('docName', TextType::class, [
                'label' => 'Nom du document :',
                'required' => false
            ])
            ->add('saveDoc', CheckboxType::class, [
                'label' => 'Enregistrer le document',
                'required' => false
            ]);
        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EditLivraison::class,
        ]);
    }
}
