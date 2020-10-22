<?php

namespace App\Form\ContractForm;

use App\Form\OrderForm\OrderType;
use App\Entity\ContractMember;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ContractMember2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $idContract = $options['idContract'];
        $idProducer = $options['idProducer'];
        $startDate = $options['startDate'];

        $builder
            ->add('orders', CollectionType::class, [
                'label' => false,
                'mapped' => false,
                'entry_type' => OrderType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'label' => " ",
                    'data' => [
                        'idContract' => $idContract,
                        'idProducer' => $idProducer,
                        'startDate' => $startDate
                    ]
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContractMember::class,
            'idContract' => null,
            'idProducer' => null,
            'startDate' => null,
        ]);
    }
}
