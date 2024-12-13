<?php

namespace App\Controller;

use App\Entity\Organisation;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\RegistrationOrganisationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/inscription-organisation', name: 'app_register_organisation')]
    public function registerOrganisation(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = new User();
        $organisation = new Organisation();

        $form = $this->createForm(RegistrationOrganisationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_ORGANISATION']);

            $organisation->setOrganisationName($form->get('organisationName')->getData());
            $organisation->setOrganisationCode($organisation->generateOrganisationCode());
            if ($form->get('siret')->getData()) {
                //Enlever les espaces
                $siret = str_replace(' ', '', $form->get('siret')->getData());
                $organisation->setSiret($siret);
            }

            $organisation->setCity('France');

            $user->setIsAdminOf($organisation);

            $entityManager->persist($user);
            $entityManager->persist($organisation);
            $entityManager->flush();

            return $this->redirectToRoute('app_register_organisation_code', ['code' => $organisation->getOrganisationCode()]);
        }

        return $this->render('registration/register_organisation.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/inscription-organisation/{code}', name: 'app_register_organisation_code')]
    public function registerOrganisationCode(string $code): Response {
        return $this->render('registration/register_organisation_code.html.twig', [
            'code' => $code,
        ]);
    }
}

