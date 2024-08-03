<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/19
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);

namespace CozeSdk\Bot;

use CozeSdk\Kernel\Bot\Bot as BotInterface;
use CozeSdk\Kernel\Exception\HttpException;
use CozeSdk\Kernel\Exception\ParamsException;
use CozeSdk\OfficialAccount\AccessToken as AccessTokenInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Bot implements BotInterface
{
    protected array $defaultOptions = [
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ];
    protected array $apiList = [
        'bot_list' => 'v1/space/published_bots_list',
        'bot_info' => 'v1/bot/get_online_info'
    ];
    protected HttpClientInterface $httpClient;
    protected CacheInterface $cache;
    protected ?string $access_token = null;

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
    public function __construct(AccessTokenInterface $accessToken, ?HttpClientInterface $httpClient = null)
    {
        $this->access_token                               = $accessToken->getToken();
        $this->defaultOptions['headers']['Authorization'] = 'Bearer ' . $this->access_token;
        $this->httpClient                                 = $httpClient ?? HttpClient::create(['base_uri' => 'https://api.coze.cn/']);
    }

    public function getBotId(): string
    {
        // TODO: Implement getBotId() method.
    }

    public function getSpaceId(): string
    {
        // TODO: Implement getSpaceId() method.
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function getBotList(?string $spaceId = null): array
    {
        if (!$spaceId) throw new ParamsException('spaceId is required');
        $this->defaultOptions['query'] = [
            'space_id' => $spaceId
        ];
        $response                      = $this->httpClient->request(
            'GET',
            $this->apiList['bot_list'],
            $this->defaultOptions,
        )->toArray(false);
        if (empty($response['data'])) {
            throw new HttpException('Failed to get bot list: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
        }
        return $response;
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function getBotDetail(?string $botId = null): array
    {
        if (!$botId) throw new ParamsException('botId is required');
        $this->defaultOptions['query'] = [
            'bot_id' => $botId
        ];
        $response                      = $this->httpClient->request(
            'GET',
            $this->apiList['bot_info'],
            $this->defaultOptions,
        )->toArray(false);
        if (empty($response['data'])) {
            throw new HttpException('Failed to get bot info: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
        }
        return $response;
    }


}