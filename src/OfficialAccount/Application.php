<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/18
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);

namespace CozeSdk\OfficialAccount;

use CozeSdk\Kernel\Contracts\AccessToken as AccessTokenInterface;
use CozeSdk\Kernel\Traits\InteractWithConfig;
use CozeSdk\OfficialAccount\Contracts\Account as AccountInterface;
use CozeSdk\OfficialAccount\Contracts\Application as ApplicationInterface;

class Application implements ApplicationInterface
{
    use InteractWithConfig;
    protected ?AccountInterface $account = null;
    protected ?AccessToken $accessToken = null;

    public function getAccount(): AccountInterface
    {
        if (!$this->account) {
            $this->account = new Account(
                kid:(string) $this->config->get('kid'),
                iss:(string) $this->config->get('iss'),
                key_path: (string) $this->config->get('key_path'),
                iat: (string) $this->config->get('iat'),
                exp: (string) $this->config->get('exp'),
                token: (string) $this->config->get('token')
            );
        }
        return $this->account;
    }

    public function setAccount(AccountInterface $account): static
    {
        $this->account = $account;
        return $this;
    }

    public function getAccessToken(): AccessTokenInterface
    {
        if (!$this->accessToken) {
            $this->accessToken = new AccessToken(
                sign: $this->getAccount()->getSignature()
            );
            return $this->accessToken;
        }
        return $this->accessToken;
    }
}