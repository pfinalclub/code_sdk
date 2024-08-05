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
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Chat implements ChatInterface
{
    protected string|null $botId = null;
    protected string|null $conversationId = null;

    protected string|null $userId = null;

    protected ?string $chatId = null;
    protected int $default_response_type = 1;

    protected ?array $additionalMessages = [];
    protected array $apiList = [
        'chat'              => 'v3/chat',
        'chat_message_list' => 'v3/chat/message/list',
        'chat_detail'       => 'v3/chat/retrieve'
    ];
    protected array $defaultOptions = [
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ];
    protected array $defaultAdditionalMessages = [
        ["role" => "user", "content" => "PHP是什么?", "content_type" => "text"]
    ];
    protected HttpClientInterface $httpClient;
    protected CacheInterface $cache;
    protected ?string $access_token = null;

    public function __construct(AccessTokenInterface $accessToken, ?HttpClientInterface $httpClient = null)
    {
        $this->access_token                               = $accessToken->getToken();
        $this->defaultOptions['headers']['Authorization'] = 'Bearer ' . $this->access_token;
        $this->httpClient                                 = $httpClient ?? HttpClient::create(['base_uri' => 'https://api.coze.cn/']);
    }

    public function setBotId(string $botId): Chat
    {
        $this->botId = $botId;
        return $this;
    }

    public function setConversationId(string $conversationId): ChatInterface
    {
        $this->conversationId = $conversationId;
        return $this;
    }

    public function setUserId(string $userId): Chat
    {
        $this->userId = $userId;
        return $this;
    }

    public function Query(string|array|null $message = null): Chat
    {
        if (is_string($message)) {
            $this->defaultAdditionalMessages[0]['content'] = $message;
        }
        return $this;
    }

    public function setChatId(string $chatId): void
    {   // 这里需要做一个 缓存的 key
        $this->chatId = $chatId;
    }

    public function getChatId(): string
    {
        // 这里做缓存的key
        return $this->chatId;
    }

    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    public function Build(int $response_type = 1): array
    {
        // $response_type = 1 非流式响应
        if (!$this->botId) throw new HttpException("Failed to Chat: BotId is need");
        if (!$this->conversationId) throw new HttpException("Failed to Chat: ConversationId is need");
        $api_url                  = $this->apiList['chat'] . '?conversation_id=' . $this->conversationId;
        $customer_options["body"] = [
            "bot_id"              => $this->botId,
            "user_id"             => $this->userId,
            "stream"              => false,
            "auto_save_history"   => true,
            "additional_messages" => $this->defaultAdditionalMessages
        ];
        try {
            $response = $this->httpClient->request(
                'POST',
                $api_url,
                array_merge($this->defaultOptions, $customer_options)
            )->toArray(false);

        } catch (ClientExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface $e) {
            throw new HttpException('Failed to create chat: ' . $e->getMessage());
        }
        if (empty($response['data'])) {
            throw new HttpException('Failed to create chat: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
        }
        $this->setChatId($response['data']['id']);
        return $response['data'];
    }

    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    public function getChatDetail(): array
    {
        if (!$this->getChatId()) throw new HttpException("Failed to get chat detail: chatId not found");

        $customer_options['query'] = [
            'chat_id'         => $this->getChatId(),
            'conversation_id' => $this->conversationId
        ];
        try {
            $response = $this->httpClient->request(
                'GET',
                $this->apiList['chat_detail'],
                array_merge($this->defaultOptions, $customer_options)
            )->toArray(false);
            if (empty($response['data'])) {
                throw new HttpException('Failed to get chat detail: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
            }
            if ($response['data']['last_error']) {
                throw new HttpException('Failed to get chat detail: ' . $response['data']['last_error']['msg']);
            }
        } catch (HttpException|TransportExceptionInterface|ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface  $e) {
            throw new HttpException('Failed to get chat detail: ' . $e->getMessage());
        }
        return $response['data'];
    }

}