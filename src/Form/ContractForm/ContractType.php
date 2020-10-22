<?php

namespace App\Form\ContractForm;

use App\Entity\Contract;
use App\Entity\Producer;
use App\Form\DeliveryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ContractType extends AbstractType
{
    private $transformer;

    public function __construct(FrenchToDateTimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du contrat :',
                'attr' => [
                    'placeholder' => 'Saisissez le nom du contrat'
                ]
            ])
            ->add('frequency', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Fréquence des livraisons :',
                'choices'  => [
                    '1 fois par semaine' => '+7days',
                    '2 fois par mois' => '+14days',
                    '1 fois par mois' => '+1month',
                    'Livraisons personnalisées' => 'null',
                ],
            ])
            ->add('emailAuto', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'label' => "Envois d'emails automatiques pour les livraisons :",
                'choices'  => [
                    'non' => false,
                    'oui' => true,
                ],
            ])
            ->add('frequencyEmailAuto', ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'label' => "Nombre de jours avant l'envoi du mail automatique :",
                'choices' => $this->emailAutoDays()
            ])
            ->add('startDate', TextType::class, [
                'label' => 'Date de la 1ère distribution :',
                'attr' => [
                    'placeholder' => 'Date de la 1ère distribution',
                    'class' => 'dateInput'
                ]
            ])
            ->add('endDate', TextType::class, [
                'label' => 'Date de la dernière distribution :',
                'attr' => [
                    'placeholder' => 'Date de la dernière distribution',
                    'class' => 'dateInput'
                ]
            ])
            ->add('producer', EntityType::class, [
                'class' => Producer::class,
                'choice_label' => 'name',
                'multiple' => false,
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('deliveries', CollectionType::class, [
                'entry_type' => DeliveryType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => ['label' => " "]
            ])
            ->add('informations', TextareaType::class, [
                'label' => 'Informations complémentaires :',
                'required' => false
            ])
            ->add('multidistribution', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Gestion des produits :',
                'choices'  => [
                    'Varie en fonction des distributions' => true,
                    'Ne varie pas en fonction des distributions' => false,
                ],
            ]);
        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contract::class,
        ]);
    }

    public function emailAutoDays(): array
    {
        for ($i = 1; $i < 31; $i++) {
            $days[$i] = "+" . strval($i) . "days";
        }
        return $days;
    }
}
