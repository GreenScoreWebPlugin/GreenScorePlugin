<?php

namespace App\Form;

use App\Entity\Organisation;
use App\Validator\ConfirmPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class MyOrganisationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('organisationName', TextType::class, [
                'label' => 'Nom de l\'organisation',
                'attr' => [
                    'class' => 'w-full',
                ],
            ])
            ->add('siret', TextType::class, [
                'label' => 'N° SIRET',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'mapped' => false,
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
                        'minMessage' => 'Votre mot de passe de avoir au moins {{ limit }} caractères.',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Organisation::class,
        ]);
    }
}
