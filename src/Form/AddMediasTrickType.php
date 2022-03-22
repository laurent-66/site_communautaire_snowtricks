<?php

namespace App\Form;

use App\Entity\Video;
use App\Entity\Figure;
use App\Form\VideoType;
use App\Entity\Illustration;
use App\Form\IllustrationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AddMediasTrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'illustrations',
                CollectionType::class,
                [
                    'entry_type' => IllustrationType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'label' =>false,
                ] 
            )
            ->add(
                'videos',
                CollectionType::class,
                [
                    'entry_type' => VideoType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'label' =>false
                ]
            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class, 
        ]);
    }
}
