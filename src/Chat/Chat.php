<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/19
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Chat;

use CozeSdk\Kernel\Bot\Chat as ChatInterface;
use CozeSdk\Kernel\Exception\HttpException;
use CozeSdk\OfficialAccount\AccessToken as AccessTokenInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Chat implements ChatInterface
{

    protected HttpClientInterface $httpClient;
    protected CacheInterface $cache;
    protected ?string $access_token = null;
    protected ?string $chatId = null;

    protected ?array $message = [];
    protected array $defaultOptions = [
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ];
    protected array $apiList = [
        'chat_create' => 'v1/conversation/create',
    ];


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

    public function setChatId(string $chatId): void
    {
        $this->chatId = $chatId;
    }

    public function getChatId(): string
    {
        return $this->chatId;
    }

    public function getMetaData(): array
    {
        // TODO: Implement getMetaData() method.

    }

    public function setMessage(array $message=[]): void
    {
        $this->message = $message;
    }

    public function getMessage(): array
    {
        return $this->message;
    }

    public function getRole(): string
    {
        $role_menu = ['user', 'assistant'];
        return '';
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function createChat(): array
    {
        $message = $this->getMessage();
        if ($message) {
            $this->defaultOptions['body'] = json_encode($message);
        }
        $response                      = $this->httpClient->request(
            'POST',
            $this->apiList['chat_create'],
            $this->defaultOptions,
        )->toArray(false);
        if (empty($response['data'])) {
            throw new HttpException('Failed to create chat: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
        }
        $chat_data = $response['data'];
        $this->setChatId($chat_data['id']);
        return $response;
    }

}