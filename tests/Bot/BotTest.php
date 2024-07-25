<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/19
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Tests\Bot;

use CozeSdk\Bot\Bot;
use CozeSdk\OfficialAccount\Application;
use CozeSdk\Tests\TestCase;

class BotTest extends TestCase
{

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \HttpException
     */
    public function testGetBotList()
    {
        $app = new Application(
            config: [
                'kid' => 'HC3N9VQD48ADZwrMD_uv8tQZxZ-E4eVVDHKO1XjXUNU',
                'iss' => '1135933249080',
                'key_path' => __DIR__.'/../'
            ]
        );
        $bot = new Bot($app->getAccessToken());
        $bot_list = $bot->getBotList(spaceId:"7374606142925733940");
        $this->assertIsArray($bot_list);
    }

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
    public function testGetBotInfo()
    {
        $app = new Application(
            config: [
                'kid' => 'HC3N9VQD48ADZwrMD_uv8tQZxZ-E4eVVDHKO1XjXUNU',
                'iss' => '1135933249080',
                'key_path' => __DIR__.'/../'
            ]
        );
        $bot = new Bot($app->getAccessToken());
        $bot_info = $bot->getBotDetail("7381736405354971163");
        $this->assertIsArray($bot_info);
    }
}