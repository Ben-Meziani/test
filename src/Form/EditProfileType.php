<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class EditProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('firstname', TextType::class)
            ->add('documents', FileType::class, [
                'required' => false,
                'multiple' => true, 
                'mapped' => false,
                'label' => 'Upload files (PDF only)',
                'constraints' => [
                    new All([
                        new File([
                            'maxSize' => '2048k',
                            'mimeTypes' => [
                                'application/pdf', 'application/x-pdf'
                            ],
                            'mimeTypesMessage' => 'Unsuitable document format'
                        ])
                    ])
                ],
                'attr' => [
                    'accept' => '.jpg, .jpeg'
                ],
            ])
            ->add('Valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
