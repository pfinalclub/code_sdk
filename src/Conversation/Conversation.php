<?php
/**
 * Author: PFinal南丞
 * Date: 2024/8/3
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);

namespace CozeSdk\Conversation;

use CozeSdk\Kernel\Conversation\Conversation as ConversationInterface;
use CozeSdk\Kernel\Exception\HttpException;
use CozeSdk\OfficialAccount\AccessToken as AccessTokenInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Conversation implements ConversationInterface
{
    protected HttpClientInterface $httpClient;
    protected CacheInterface $cache;
    protected ?string $access_token = null;
    protected ?string $conversationId = null;
    protected ?array $defaultBody = [];
    protected array $defaultOptions = [
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ];
    protected array $apiList = [
        'conversation_create' => 'v1/conversation/create',
    ];

    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function __construct(AccessTokenInterface $accessToken, ?HttpClientInterface $httpClient = null)
    {
        $this->access_token                               = $accessToken->getToken();
        $this->defaultOptions['headers']['Authorization'] = 'Bearer ' . $this->access_token;
        $this->httpClient                                 = $httpClient ?? HttpClient::create(['base_uri' => 'https://api.coze.cn/']);
    }
    public function setConversationId(string $conversationId): void
    {
        $this->conversationId = $conversationId;
    }

    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    public function getConversationId(): string
    {
        if (is_null($this->conversationId)) {
            $this->createConversation();
        }
        return $this->conversationId;
    }

    public function getBody(): ?array
    {
        return $this->defaultBody;
    }


    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    public function createConversation(): void
    {
        $message = $this->getBody();
        if ($message) {
            $this->defaultOptions['body'] = json_encode($message);
        }
        try {
            $response = $this->httpClient->request(
                'POST',
                $this->apiList['conversation_create'],
                $this->defaultOptions,
            )->toArray(false);
        } catch (ClientExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface $e) {
            throw new HttpException('Failed to create chat: ' . $e->getMessage());
        }
        if (empty($response['data'])) {
            throw new HttpException('Failed to create chat: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
        }
        $conversationData = $response['data'];
        $this->setConversationId($conversationData['id']);
    }
}