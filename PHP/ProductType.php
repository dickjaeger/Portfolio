<?php

namespace App\Form;

use App\Entity\ProductsStore;
use App\Repository\ProductsStoreRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{SubmitType, TextType, FileType, NumberType, ChoiceType};

class ProductType extends AbstractType
{   
    private $productRepo;

    public function __construct(ProductsStoreRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nazwa'])

            ->add('quantity', NumberType::class, ['label' => 'Ilość'])

            ->add('img', FileType::class, ['label' => 'Zdjęcie',
                                      'data_class' => null,
                                        'required' => false,
                                          'mapped' => false
            ])

            ->add('min_quantity', NumberType::class, ['label' => 'Minimalna ilość'])

            ->add('unit', TextType::class, ['label' => 'Jednostka'])

            ->add('category', ChoiceType::class, ['choices' => $this->productRepo->getCategories(),
                                             'choice_label' => function ($value) {return ($value);},
                                                    'label' => 'Kategoria'                                               
            ])

            ->add('new_category', TextType::class, ['label' => 'Nowa kategoria',
                                                 'required' => false,
                                                   'mapped' => false
            ])
            
            ->add('submit', SubmitType::class, ['label' => 'Dodaj!']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductsStore::class,
        ]);
    }
}
