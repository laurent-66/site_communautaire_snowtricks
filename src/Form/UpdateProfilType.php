<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UpdateProfilType extends AbstractType
{
    /**
     * Undocumented function
     *
     * @param FormBuilderInterface $builder
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, ['label' => 'Pseudo','required' => false])
            ->add('email', TextType::class, ["label" => "Email",'required' => false])
            ->add('urlPhotoFile', FileType::class, [

                'label' => 'Image de profil (jpeg,jpg ou png)',
                'required' => false,
            ])

            ->add('alternativeAttribute', TextType::class, [

                'label' => 'Décrivez l\'image en un mot',
                'required' => false
            ])

            ->add('save', SubmitType::class, [
                "label" => "Modifier le profil",
                "attr" => [
                    'class' => 'btn btn-success d-block my-4 mx-auto'
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['updateMail','base','uploadFile','altAttrUploadFile']
        ]);
    }
}
