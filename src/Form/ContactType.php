<?php
// src/Form/ContactType.php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Prénom *',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le prénom',
                    'autofocus' => true
                ],
                'row_attr' => ['class' => 'mb-3']
            ])
            
            ->add('nom', TextType::class, [
                'label' => 'Nom *',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le nom'
                ],
                'row_attr' => ['class' => 'mb-3']
            ])
            
            ->add('email', EmailType::class, [
                'label' => 'Email *',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'exemple@domain.com'
                ],
                'row_attr' => ['class' => 'mb-3']
            ])
            
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '+33 1 23 45 67 89'
                ],
                'row_attr' => ['class' => 'mb-3']
            ])
            
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez l\'adresse complète',
                    'rows' => 3
                ],
                'row_attr' => ['class' => 'mb-3']
            ])
            
            ->add('imageFile', FileType::class, [
                'label' => 'Photo de profil',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',   
                            'image/gif',
                        ], 
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG ou GIF)',
                    ])
                ],
                'row_attr' => ['class' => 'mb-3'],
                'help' => 'Formats acceptés : JPG, PNG, GIF (max 2Mo)'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}