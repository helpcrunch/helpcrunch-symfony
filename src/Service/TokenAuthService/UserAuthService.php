<?php

namespace Helpcrunch\Service\TokenAuthService;

class UserAuthService extends MobileUserAuthService
{
    /**
     * @var int
     */
    private $userId;

    public function isTokenValid(bool $isLogin = false): bool
    {
        if (!$this->token || !$this->userId) {
            return false;
        }

        $authorized = ($this->userId == $this->getRedisService()->getData($this->getTokenKey()));
        if ($authorized && $isLogin) {
            $this->checkUsersKnownSessions();
        }

        return $authorized;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    private function checkUsersKnownSessions(): void
    {
        $oldToken = $this->getRedisService()->getData($this->getUserIdRedisKey());

        $this->getRedisService()->delete($this->getOrganizationsDomain() . '_' . $oldToken);
        $this->getRedisService()->delete($this->getUserIdRedisKey());

        $this->getRedisService()->pushData($this->getTokenKey(), $this->userId);
        $this->getRedisService()->pushData($this->getUserIdRedisKey(), $this->token);
    }

    private function getUserIdRedisKey(): string
    {
        return $this->getOrganizationsDomain() . '_' . $this->userId;
    }

    /**
     * @return int|null
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
