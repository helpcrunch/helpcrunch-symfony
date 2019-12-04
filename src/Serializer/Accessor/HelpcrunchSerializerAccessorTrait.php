<?php

namespace Helpcrunch\Serializer\Accessor;

use Helpcrunch\Authentication;

trait HelpcrunchSerializerAccessorTrait
{
    public function getDateTimeByPlatform(string $property)
    {
        $value = $this->{$property};
        if (empty($value) && Authentication::isAuthenticatedAsMobile()) {
            return 0;
        }

        return $value;
    }
}
