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
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Bot implements BotInterface
{
    protected ?array $botIdList = [];
    protected ?string $spaceId = null;
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
     * @param \CozeSdk\OfficialAccount\AccessToken $accessToken
     * @param \Symfony\Contracts\HttpClient\HttpClientInterface|null $httpClient
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function __construct(AccessTokenInterface $accessToken, ?HttpClientInterface $httpClient = null)
    {
        $this->access_token                               = $accessToken->getToken();
        $this->defaultOptions['headers']['Authorization'] = 'Bearer ' . $this->access_token;
        $this->httpClient                                 = $httpClient ?? HttpClient::create(['base_uri' => 'https://api.coze.cn/']);
    }

    public function setSpaceId(string $spaceId): Bot
    {
        $this->spaceId = $spaceId;
        return $this;
    }

    public function getSpaceId(): string
    {
        return $this->spaceId;
    }

    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    public function getBotIdList(): array
    {
        if (!$this->botIdList) {
            try {
                $botList = $this->getBotList();
            } catch (HttpException|ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
                throw new HttpException("Failed to get bot list: " . $e->getMessage());
            }
            $this->botIdList = array_column($botList['space_bots'],'bot_id');
        }
        return $this->botIdList;
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function getBotList(): array
    {
        if (!$this->spaceId) throw new ParamsException('spaceId is required');
        $this->defaultOptions['query'] = [
            'space_id' => $this->spaceId
        ];
        $response                      = $this->httpClient->request(
            'GET',
            $this->apiList['bot_list'],
            $this->defaultOptions,
        )->toArray(false);
        if (empty($response['data'])) {
            throw new HttpException('Failed to get bot list: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
        }
        return $response['data'];
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