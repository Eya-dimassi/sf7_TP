<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\CategorySearch;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CategorySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('category', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'titre',
            'label' => 'Catégorie',
            'required' => false,
            'placeholder' => 'Toutes les catégories'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategorySearch::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
