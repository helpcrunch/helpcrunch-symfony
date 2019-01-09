<?php

namespace Helpcrunch\Validator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;

class ArrayType extends AbstractType implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'array';
    }

    /**
     * {@inheritdoc}
     */
    public function transform($data)
    {
        if (!is_array($data) && $data && is_string($data)) {
            try {
                $jsonData = json_decode($data. true);
            } catch (\Throwable $exception) {}
            if (!empty($jsonData) && is_array($jsonData)) {
                $data = $jsonData;
            }
        } elseif (is_null($data) || ($data === '') || ($data === false)) {
            $data = [];
        } elseif (!is_array($data)) {
            $data = [$data];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($data)
    {
        return $data;
    }
}
