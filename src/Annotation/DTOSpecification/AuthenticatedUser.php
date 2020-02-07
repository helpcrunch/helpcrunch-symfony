<?php

namespace Helpcrunch\Annotation\DTOSpecification;

use Doctrine\Common\Annotations\Annotation\Required;
use Helpcrunch\Annotation\DTOSpecificationInterface;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class AuthenticatedUser implements DTOSpecificationInterface
{
    /**
     * @Required()
     * @var string
     */
    public $class;
}
