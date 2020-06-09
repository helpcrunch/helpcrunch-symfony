<?php

namespace Helpcrunch\Service;

use Helpcrunch\Service\TokenAuthService\AutoLoginAuthService;
use Helpcrunch\Service\TokenAuthService\DeviceAuthService;
use Helpcrunch\Service\TokenAuthService\InternalAppAuthService;
use Helpcrunch\Service\TokenAuthService\JWTAuthService;
use Helpcrunch\Service\TokenAuthService\MobileDeviceAuthService;
use Helpcrunch\Service\TokenAuthService\MobileUserAuthService;
use Helpcrunch\Service\TokenAuthService\OrganizationAuthService;
use Helpcrunch\Service\TokenAuthService\ProductAuthService;
use Helpcrunch\Service\TokenAuthService\UserAuthService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class TokenAuthServiceFactory
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @return AbstractTokenAuthService|null
     */
    public function getTokenHandler(Request $request)
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader) {
            return null;
        }

        $tokenHandler = $this->processAuthHeader($authHeader);
        if (!$tokenHandler) {
            $tokenHandler = $this->createAutoLoginAuthHandler($request);
        }

        return $tokenHandler;
    }

    /**
     * @param string $authHeader
     * @return AbstractTokenAuthService|null
     */
    private function processAuthHeader(string $authHeader)
    {
        if (preg_match('/^Bearer token="(?P<token>\S+?)"$/i', $authHeader, $matches)) {
            return $this->createMobileUserAuthHandler($matches);
        }

        if (preg_match('/^Bearer user="(?P<user>\S+?)"\s+token="(?P<token>\S+?)"$/i', $authHeader, $matches)) {
            if (self::checkIsMobile()) {
                return $this->createMobileUserAuthHandler($matches);
            }

            return $this->createUserAuthHandler($matches);
        }

        if (preg_match('/^Bearer device="(?P<device>\S+?)"\s+secret="(?P<secret>\S+?)"$/i', $authHeader, $matches)) {
            if (self::checkIsMobile()) {
                return $this->createMobileDeviceAuthHandler($matches);
            }

            return $this->createDesktopDeviceAuthHandler($matches);
        }

        if (preg_match('/^Bearer product="(?P<product>\S+?)"\s+secret="(?P<secret>\S+?)"$/i', $authHeader, $matches)) {
            return $this->createProductAuthHandler($matches);
        }

        if (preg_match('/^Bearer api-key="(?P<apiKey>\S+?)"$/i', $authHeader, $matches)) {
            return $this->createOrganizationAuthHandler($matches);
        }

        if (preg_match('/^Bearer helpcrunch-service="(?P<apiKey>\S+?)"$/i', $authHeader, $matches)) {
            return $this->createInternalAppAuthHandler($matches);
        }

        if(preg_match('/^Bearer (?P<token>\S+?)$/i', $authHeader, $matches)) {
            return $this->createJWTAuthHandler($matches['token']);
        }

        return null;
    }

    private function createJWTAuthHandler(string $token): JWTAuthService
    {
        /** @var JWTAuthService $tokenHandler */
        $tokenHandler = $this->container->get(JWTAuthService::class);
        $tokenHandler->setToken($token);

        return $tokenHandler;
    }

    private function createUserAuthHandler(array $matches): UserAuthService
    {
        /** @var UserAuthService $tokenHandler */
        $tokenHandler = $this->container->get(UserAuthService::class);
        $tokenHandler->setUserId($matches['user']);
        $tokenHandler->setToken($matches['token']);

        return $tokenHandler;
    }

    private function createMobileUserAuthHandler(array $matches): MobileUserAuthService
    {
        /** @var UserAuthService $tokenHandler */
        $tokenHandler = $this->container->get(MobileUserAuthService::class);
        $tokenHandler->setToken($matches['token']);

        return $tokenHandler;
    }

    private function createDesktopDeviceAuthHandler(array $matches): DeviceAuthService
    {
        return $this->createDeviceAuthHandler(DeviceAuthService::class, $matches['device'], $matches['secret']);
    }

    private function createMobileDeviceAuthHandler(array $matches): DeviceAuthService
    {
        return $this->createDeviceAuthHandler(MobileDeviceAuthService::class, $matches['device'], $matches['secret']);
    }

    private function createDeviceAuthHandler(string $deviceAuthService, int $deviceId, string $token): DeviceAuthService
    {
        /** @var DeviceAuthService $tokenHandler */
        $tokenHandler = $this->container->get($deviceAuthService);
        $tokenHandler->setDeviceId($deviceId);
        $tokenHandler->setToken($token);

        return $tokenHandler;
    }

    private function createProductAuthHandler(array $matches): ProductAuthService
    {
        /** @var ProductAuthService $tokenHandler */
        $tokenHandler = $this->container->get(ProductAuthService::class);
        $tokenHandler->setProductId($matches['product']);
        $tokenHandler->setToken($matches['secret']);

        return $tokenHandler;
    }

    private function createOrganizationAuthHandler(array $matches): OrganizationAuthService
    {
        /** @var OrganizationAuthService $tokenHandler */
        $tokenHandler = $this->container->get(OrganizationAuthService::class);
        $tokenHandler->setToken($matches['apiKey']);

        return $tokenHandler;
    }

    /**
     * @param Request $request
     * @return AutoLoginAuthService|null
     */
    private function createAutoLoginAuthHandler(Request $request)
    {
        if ($request->query->has('admin_token')) {
            $adminToken = $request->query->get('admin_token');

            /** @var AutoLoginAuthService $tokenHandler */
            $tokenHandler = $this->container->get(AutoLoginAuthService::class);
            $tokenHandler->setToken($adminToken);

            return $tokenHandler;
        }

        return null;
    }

    private function createInternalAppAuthHandler(array $matches): InternalAppAuthService
    {
        /** @var InternalAppAuthService $tokenHandler */
        $tokenHandler = $this->container->get(InternalAppAuthService::class);
        $tokenHandler->setToken($matches['apiKey']);

        return $tokenHandler;
    }

    public static function checkIsMobile(): bool
    {
        $detect = new \Mobile_Detect();

        return $detect->isMobile() || $detect->isTablet();
    }
}
