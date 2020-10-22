<?php

namespace App\Form\OrderForm;

use App\Entity\Product;
use App\Entity\Delivery;
use App\Repository\ProductRepository;
use App\Repository\DeliveryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class OrderType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $idContract = $options['data']['idContract'];
        $idProducer = $options['data']['idProducer'];
        $startDate = $options['data']['startDate'];

        $builder
            ->add('quantity', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Saisissez une quantité'
                ]
            ])
            ->add('product', EntityType::class, [
                'label' => false,
                'class' => Product::class,
                'choice_label' => 'fullName',
                'query_builder' => function (ProductRepository $pr) use ($idProducer) {
                    return $pr->createQueryBuilder('p')
                        ->andWhere('p.producer = :producer')
                        ->setParameter('producer', $idProducer)
                        ->orderBy('p.id', 'ASC');
                },
                'multiple' => false,
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('deliveries', EntityType::class, [
                'class' => Delivery::class,
                'choice_label' => 'dateString',
                'query_builder' => function (DeliveryRepository $dr) use ($idContract, $startDate) {
                    return $dr->createQueryBuilder('d')
                        ->andWhere('d.contract = :contract')
                        ->andWhere('d.date >= :date')
                        ->setParameter('contract', $idContract)
                        ->setParameter('date', $startDate)
                        ->orderBy('d.id', 'ASC');
                },
                'multiple' => true,
                'expanded' => true,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
