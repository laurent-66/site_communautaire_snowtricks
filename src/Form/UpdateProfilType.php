<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

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
            // ->add('pseudo', TextType::class, $this->getConfiguration("pseudo", "{{}}"))
            ->add('pseudo', EntityType::class, ['choice_label'=> 'pseudo', 'class' => User::class])
            ->add('email', EmailType::class, ["label"=>"Email"])
            ->add('password', PasswordType::class,["label"=>"Mot de passe"])
            ->add('url_photo', FileType::class, [

                'label' => 'Image de profil (jpeg,jpg ou png)',
    
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
    
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
            ])
            ->add('save', SubmitType::class,[
                "label"=>"Modifier le profil",
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
        ]);
    }
}
