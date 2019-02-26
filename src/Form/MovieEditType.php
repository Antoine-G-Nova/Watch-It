<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;



class MovieEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du film',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ ne doit pas être vide'
                    ])
                ]
            ])

            ->add('image')

            ->add('overview', TextareaType::class, [
                'label' => 'Résumé',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ ne doit pas être vide'
                    ])
                ]
            ])

            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name',
                'expanded' => true ,
                'multiple' => true ,
                'attr' => [
                    'class' => 'd-flex flex-wrap ',
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
