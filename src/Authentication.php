<?php

namespace Helpcrunch;

use Helpcrunch\Annotation\AuthSpecification\AutoLoginAuthSpecification;
use Helpcrunch\Annotation\AuthSpecificationInterface;
use Helpcrunch\Annotation\AuthSpecification\DeviceAuthSpecification;
use Helpcrunch\Annotation\AuthSpecification\InternalAppAuthSpecification;
use Helpcrunch\Annotation\AuthSpecification\PublicApiAuthSpecification;
use Helpcrunch\Annotation\AuthSpecification\UserAuthSpecification;
use Helpcrunch\Service\AbstractTokenAuthService;
use Helpcrunch\Service\TokenAuthService\AutoLoginAuthService;
use Helpcrunch\Service\TokenAuthService\DeviceAuthService;
use Helpcrunch\Service\TokenAuthService\InternalAppAuthService;
use Helpcrunch\Service\TokenAuthService\MobileUserAuthService;
use Helpcrunch\Service\TokenAuthService\OrganizationAuthService;
use Helpcrunch\Service\TokenAuthService\ProductAuthService;
use Helpcrunch\Service\TokenAuthService\UserAuthService;
use Helpcrunch\Service\TokenAuthServiceFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

final class Authentication
{
    const AUTHENTICATED_AS_DESKTOP_USER = UserAuthSpecification::class;
    const AUTHENTICATED_AS_MOBILE_USER = UserAuthSpecification::class;
    const AUTHENTICATED_AS_DEVICE = DeviceAuthSpecification::class;
    const AUTHENTICATED_AS_ORGANIZATION_PUBLIC_KEY = PublicApiAuthSpecification::class;
    const AUTHENTICATED_AS_INTERNAL_APP = InternalAppAuthSpecification::class;
    const AUTHENTICATED_AS_AUTO_LOGIN = AutoLoginAuthSpecification::class;
    const AUTHENTICATED_ROLES = [
        MobileUserAuthService::class => self::AUTHENTICATED_AS_MOBILE_USER,
        UserAuthService::class => self::AUTHENTICATED_AS_DESKTOP_USER,
        DeviceAuthService::class => self::AUTHENTICATED_AS_DEVICE,
        OrganizationAuthService::class => self::AUTHENTICATED_AS_ORGANIZATION_PUBLIC_KEY,
        InternalAppAuthService::class => self::AUTHENTICATED_AS_INTERNAL_APP,
        AutoLoginAuthService::class => self::AUTHENTICATED_AS_AUTO_LOGIN,
        ProductAuthService::class => self::AUTHENTICATED_AS_DEVICE,
    ];

    /**
     * @var null|ContainerInterface
     */
    private static $container = null;

    /**
     * @var null|AbstractTokenAuthService
     */
    private static $tokenHandler = null;

    /**
     * @var null|string
     */
    private static $authenticatedAs = null;

    /**
     * @var null|string
     */
    public static $authToken = null;

    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }

    /**
     * @param Request $request
     * @param string[] $authenticatedRoles
     * @return bool
     * @throws \ErrorException
     */
    public static function authorize(Request $request, array $authenticatedRoles): bool
    {
        self::getAuthentication($request);

        if (self::$tokenHandler &&
            self::$tokenHandler->isTokenValid() &&
            in_array(self::$authenticatedAs, self::AUTHENTICATED_ROLES)
        ) {
            /** @var AuthSpecificationInterface $specification */
            $specification = new self::$authenticatedAs($authenticatedRoles);

            return $specification->checkPermission();
        }

        return false;
    }

    /**
     * @param Request $request
     * @return AbstractTokenAuthService|null
     * @throws \ErrorException
     */
    public static function getAuthentication(Request $request)
    {
        if (!self::$tokenHandler) {
            self::createTokenHandler($request);
        }

        return self::$tokenHandler;
    }

    /**
     * @param Request $request
     * @return void
     * @throws \ErrorException
     */
    private static function createTokenHandler(Request $request): void
    {
        if (!self::$container) {
            throw new \ErrorException();
        }

        $authHandlersFactory = new TokenAuthServiceFactory(self::$container);

        self::$tokenHandler = $authHandlersFactory->getTokenHandler($request);
        if (self::$tokenHandler) {
            self::setAuthenticatedAs();
            self::setAuthToken();
        }
    }

    private static function setAuthToken(): void
    {
        self::$authToken = self::$tokenHandler->getToken();
    }

    private static function setAuthenticatedAs(): void
    {
        self::$authenticatedAs = self::AUTHENTICATED_ROLES[get_class(self::$tokenHandler)];
    }

    public static function isAuthenticatedAsUser(): bool
    {
        return (self::$authenticatedAs == self::AUTHENTICATED_AS_DESKTOP_USER) ||
            (self::$authenticatedAs == self::AUTHENTICATED_AS_MOBILE_USER);
    }

    public static function isAuthenticatedAsDevice(): bool
    {
        return self::$authenticatedAs == self::AUTHENTICATED_AS_DEVICE;
    }

    public static function isAuthenticatedAsMobile(): bool
    {
        return self::$authenticatedAs == self::AUTHENTICATED_AS_MOBILE_USER;
    }

    public static function isAuthenticatedAsOrganizationPublicKey(): bool
    {
        return self::$authenticatedAs == self::AUTHENTICATED_AS_ORGANIZATION_PUBLIC_KEY;
    }

    public static function isAuthenticatedAsInternalApp(): bool
    {
        return self::$authenticatedAs == self::AUTHENTICATED_AS_INTERNAL_APP;
    }

    public static function isAutoLogin(): bool
    {
        return self::$authenticatedAs == self::AUTHENTICATED_AS_AUTO_LOGIN;
    }

    private function __construct() {}
    private function __clone() {}
}
