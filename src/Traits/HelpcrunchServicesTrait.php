<?php

namespace App\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Helpcrunch\Service\EmailSenderService;
use Helpcrunch\Service\RedisService;
use Helpcrunch\Service\TokenValidationService;

trait HelpcrunchServicesTrait
{
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->container->get('doctrine')->getManager();
    }

    public function getRedisService(): RedisService
    {
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
}
