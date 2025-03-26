<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/*!
 * Cette classe permet de valider le champ de confirmation de mot de passe.
 */
class ConfirmPasswordValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var ConfirmPassword $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $form = $this->context->getRoot(); // Récupère le formulaire complet
        $plainPassword = $form->get('plainPassword')->getData();

        if ($value !== $plainPassword) {
            // Ajoute une erreur au champ
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
