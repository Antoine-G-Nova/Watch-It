<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\MovieSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MovieSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name',
                'required' => false,
                'label'=> 'Genre:',
                'attr'=> [
                    'class'=> 'form-control ml-1'
                ]
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => false,
                'constraints' =>
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Au moins {{ limit }}'
                    ])
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
