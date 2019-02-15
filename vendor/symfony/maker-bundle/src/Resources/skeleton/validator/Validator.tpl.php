<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class <?= $class_name ?> extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint <?= $constraint_class_name ?> */

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
