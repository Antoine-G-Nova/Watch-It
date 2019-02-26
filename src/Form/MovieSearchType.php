<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\MovieSearch;
use App\Entity\Genre;

class MovieSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name',
                'label'=> 'Genre:',
                'attr'=> [
                    'class'=> 'form-control ml-1'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => MovieSearch::class,
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }

}
