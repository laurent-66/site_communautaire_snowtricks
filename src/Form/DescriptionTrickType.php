<?php

namespace App\Form;

use App\Entity\Figure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DescriptionTrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label'=>'Modifier le nom de la figure',

                'required' => false
            ])

            ->add('description', TextareaType::class, [
                    'label'=>'Modifier la description de la figure',
                    'required' => false
                ])

            // ->add('figureGroup', ChoiceType::class, [
            //     'choices' => [
            //         'Sélectionner un group' => [
            //             'Yes' => 'stock_yes',
            //             'No' => 'stock_no'
            //         ]
            //     ]
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
