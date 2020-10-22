<?php

namespace App\Form\OrderForm;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class Order2Type extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $idProducer = $options['data']['idProducer'];

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
            ]);
        /* $builder->get('produit')
            ->addModelTransformer(new OrderTransformer($this->entityManager)); */
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
