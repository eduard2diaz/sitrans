<?php

namespace App\Form\Transformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DatetoStringTransformer implements DataTransformerInterface
{
    public function transform($datetime)
    {

        if (null === $datetime) {
            return;
        }

        return $datetime->format('Y-m-d');
    }

    public function reverseTransform($issueNumber)
    {
        if (!$issueNumber) {
            return;
        }

        return new \DateTime($issueNumber);
    }

}
