<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\ConfirmPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class MyAccountFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'attr' => [
                    'class' => 'w-full',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => false,
                'attr' => [
                    'class' => 'w-full',
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit avoir au moins {{ limit }} caractères.',
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('passwordConfirmation', PasswordType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Confirmation du mot de passe',
                'constraints' => [
                    new ConfirmPassword(),
                ],
            ])
            ->add('codeOrganisation', TextType::class, [
                'required' => false,
                'mapped' => false,
                'label' => "Code de l'organisation",
                'attr' => [
                    'placeholder' => 'Entrez le code à 8 caractères que votre organisation vous a envoyé',
                ],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le code doit avoir {{ limit }} caractères.',
                        'max' => 8,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
