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

/*!
 * Cette classe permet de créer le formulaire de modification de mon organisation.
 */
class MyOrganisationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('organisationName', TextType::class, [
                'label' => 'Nom de l\'organisation',
                'required' => true,
                'attr' => [
                    'class' => 'w-full',
                ],
            ])
            ->add('siret', TextType::class, [
                'label' => 'N° SIRET',
                'required' => false,
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
