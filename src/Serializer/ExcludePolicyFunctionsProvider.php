<?php

namespace Helpcrunch\Serializer;

use Helpcrunch\Authentication;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class ExcludePolicyFunctionsProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('isAuthenticatedAsDevice', function () {}, function () {
                return Authentication::isAuthenticatedAsDevice();
            }),
            new ExpressionFunction('isAuthenticatedAsUser', function () {}, function () {
                return Authentication::isAuthenticatedAsUser();
            }),
            new ExpressionFunction('isAuthenticatedAsMobile', function () {}, function () {
                return Authentication::isAuthenticatedAsMobile();
            }),
        ];
    }
}
