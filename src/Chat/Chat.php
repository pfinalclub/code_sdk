<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/19
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Chat;

use CozeSdk\Kernel\Chat\Chat as ChatInterface;
use CozeSdk\Kernel\Exception\HttpException;
use CozeSdk\OfficialAccount\AccessToken as AccessTokenInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Chat implements ChatInterface
{

    public function getMetaData(): array
    {
        // TODO: Implement getMetaData() method.
    }

    public function getMessage(): array
    {
        // TODO: Implement getMessage() method.
    }

    public function getRole(): string
    {
        // TODO: Implement getRole() method.
    }

    public function query(): array
    {
        // TODO: Implement query() method.
    }
}