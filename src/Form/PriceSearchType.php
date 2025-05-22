<?php

namespace App\Form;
use App\Entity\PriceSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('minPrice', IntegerType::class, [
            'required' => false,
            'label' => 'Prix minimum',
            'attr' => ['placeholder' => '0']
        ])
        ->add('maxPrice', IntegerType::class, [
            'required' => false,
            'label' => 'Prix maximum',
            'attr' => ['placeholder' => '1000']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PriceSearch::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
