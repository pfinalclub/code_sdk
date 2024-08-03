<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/25
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Tests\Chat;

use CozeSdk\Chat\Chat;
use CozeSdk\Kernel\Exception\HttpException;
use CozeSdk\OfficialAccount\Application;
use CozeSdk\Tests\TestCase;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ChatTest extends TestCase
{
    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \HttpException
     */
    public function test_create_chat()
    {
        $app = new Application(
            config: [
                'kid' => '87H_tatLsKzPKQGxcp8ZRJsENRZZL7oQVbpNBaHmKlw',
                'iss' => '1135933249080',
                'key_path' => __DIR__.'/../'
            ]
        );
        $chat = new Chat($app->getAccessToken());
        $chatId = $chat->getChatId();
        var_dump($chatId);
        $this->assertIsString($chatId);
    }
}