<?php

namespace Helpcrunch\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Helpcrunch\Service\ApiRequestService;
use Helpcrunch\Service\EmailSenderService;
use Helpcrunch\Service\RedisService;
use Helpcrunch\Service\TokenValidationService;

/**
 * @property object container
 */
trait HelpcrunchServicesTrait
{
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->container->get('doctrine')->getManager();
    }

    public function getRedisService(): RedisService
    {
        if (class_exists("App\\Service\\OrganizationsRedisService")) {
            return $this->container->get("App\\Service\\OrganizationsRedisService");
        }

        return $this->container->get(RedisService::class);
    }

    public function getEmailSenderService(): EmailSenderService
    {
        return $this->container->get(EmailSenderService::class);
    }

    public function getTokenValidationService(): TokenValidationService
    {
        return $this->container->get(TokenValidationService::class);
    }

    public function getApiRequestService(): ApiRequestService
    {
        return $this->container->get(ApiRequestService::class);
    }
}
