<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\ConfirmPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'w-full',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un prénom.',
                    ])
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'w-full',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom.',
                    ])
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'w-full',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un e-mail.',
                    ])
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'En vous inscrivant sur GreenScoreWeb, vous acceptez nos conditions générales d’utilisation.',
                'required' => true,
                'error_bubbling' => false,
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions générales d\'utilisation',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer un mot de passe.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe de avoir au moins {{ limit }} caractères.',
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('passwordConfirmation', PasswordType::class, [
                'mapped' => false,
                'label' => 'Confirmation du mot de passe',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de confirmer votre mot de passe.',
                    ]),
                    new ConfirmPassword(),
                ],
            ])
            ->add('organisation', TextType::class, [
                'label' => 'Vous souhaitez rejoindre une organisation ?',
                'required' => false,
                'error_bubbling' => false,
                'attr' => [
                    'placeholder' => 'Entrez le code que votre organisation vous a envoyé',
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
