<?php

namespace App\Form;

use App\Entity\Contract;
use App\Entity\Producer;
use App\Entity\Emargement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EmargementType extends AbstractType
{
    private $transformer;

    public function __construct(FrenchToDateTimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contract', EntityType::class, [
                'class' => Contract::class,
                'label' => false,
                'choice_label' => 'fullName',
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
                    'placeholder' => 'Date de la 1ère distribution',
                    'class' => 'dateInput'
                ]
            ])
            ->add('endDate', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Date de la dernière distribution',
                    'class' => 'dateInput'
                ]
            ])
            ->add('docName', TextType::class, [
                'label' => 'Nom du document :',
                'required' => false
            ])
            ->add('saveDoc', CheckboxType::class, [
                'label' => 'Enregistrer',
                'required' => false
            ]);
        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Emargement::class,
        ]);
    }
}
