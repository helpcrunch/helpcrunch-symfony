<?php

namespace Helpcrunch\Annotation;

interface AuthSpecificationInterface
{
    public function checkPermission(): bool;
}
