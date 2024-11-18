<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/19
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Chat;

use Closure;
use CozeSdk\Kernel\Chat\Chat as ChatInterface;
use CozeSdk\Kernel\Exception\HttpException;
use CozeSdk\OfficialAccount\Application;
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
    protected ?array $additionalMessages = [];
    protected array $apiList = [
        'chat'              => 'v3/chat',
        'chat_message_list' => 'v3/chat/message/list',
        'chat_detail'       => 'v3/chat/retrieve',
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

	protected Application $application;

	public function __construct(Application $application, ?HttpClientInterface $httpClient = null)
    {
		$this->application = $application;
        $this->access_token                               = $application->getAccessToken()->getToken();
        $this->defaultOptions['headers']['Authorization'] = 'Bearer ' . $this->access_token;
        $this->httpClient                                 = $httpClient ?? HttpClient::create(['base_uri' => 'https://api.coze.cn/']);
		$this->botId = $application->getBotId();
    }

    public function setBotId(?string $botId=null): Chat
    {
		if ($botId==null) {
			$botId = $this->application->getBotId();
		}
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
     * @throws HttpException
     */
    public function Build(bool $response_type = false): array|Closure
    {
        if (!$this->botId) {
            throw new HttpException("Failed to Chat: BotId is needed");
        }
        if ($this->conversationId) {
            $this->defaultOptions['query'] = [
                'conversation_id' => $this->conversationId,
            ];
        }

        $customer_options['body'] = [
            'bot_id'              => $this->botId,
            'user_id'             => $this->userId,
            'stream'              => $response_type,
            'auto_save_history'   => true,
            'additional_messages' => $this->defaultAdditionalMessages,
        ];
		$options = array_merge($this->defaultOptions, $customer_options);
		// 确保 body 被转换为 JSON 字符串
		$options['body'] = json_encode($options['body'], JSON_UNESCAPED_UNICODE);
		try {
            $response = $this->httpClient->request(
                'POST',
                $this->apiList['chat'],
				$options
            );
            if ($response_type) {
                // 流式请求
                return function () use ($response) {
                    $stream = $this->httpClient->stream($response);

                    header('Content-Type: application/json');
                    echo "["; // Start JSON array
                    $first = true;

                    foreach ($stream as $chunk) {
                        $content = $chunk->getContent();
                        // Clean up content by removing unnecessary newlines and whitespace
                        $content = trim($content);
                        print_r($content);
                        // Ensure proper JSON formatting
                        if (!$first) {
                            echo ","; // Add comma to separate data chunks
                        } else {
                            $first = false;
                        }
                        // Output cleaned content
                        echo $content;
                        flush(); // Flush output buffer
                    }

                    echo "]"; // End JSON array
                };
            }

            // 非流式请求
            $responseData = $response->toArray(false);
            if (empty($responseData['data'])) {
                throw new HttpException('Failed to create chat: ' . json_encode($responseData, JSON_UNESCAPED_UNICODE));
            }
            $this->setConversationId($responseData['data']['conversation_id']);
            $this->setChatId($responseData['data']['id']);
            return $responseData;
        } catch (
        ClientExceptionInterface|
        ServerExceptionInterface|
        TransportExceptionInterface|
        RedirectionExceptionInterface|
        DecodingExceptionInterface $e
        ) {
            throw new HttpException('Failed to create chat: ' . $e->getMessage());
        }
    }

    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    public function getChatRetrieve(?string $chatId): array
    {
        if (!$chatId) throw new HttpException("Failed to get chat detail: chatId not found");
        try {
            $response = $this->httpClient->request(
                'GET',
                $this->apiList['chat_detail'],
                array_merge($this->defaultOptions, [
                    'query' => [
                        'conversation_id' => $this->conversationId,
                        'chat_id'         => $chatId
                    ]
                ])
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

    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    public function getChatMessageList(?string $chatId): array
    {
        if (!$chatId) throw new HttpException("Failed to get chat message list: chatId not found");
        $customer_options['query'] = [
            'chat_id'         => $chatId,
            'conversation_id' => $this->conversationId
        ];
        try {
            $response = $this->httpClient->request(
                'GET',
                $this->apiList['chat_message_list'],
                array_merge($this->defaultOptions, $customer_options)
            )->toArray(false);
            if (empty($response['data'])) {
                throw new HttpException('Failed to get chat message list: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
            }
        } catch (HttpException|TransportExceptionInterface|ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface  $e) {
            throw new HttpException('Failed to get chat message list: ' . $e->getMessage());
        }
        return $response['data'];
    }

	/**
	 * sendMessage
	 * @param string $message
	 * @param bool $response_type
	 * @return array
	 * @throws \CozeSdk\Kernel\Exception\HttpException
	 */
	public function sendMessage(string $message,bool $response_type = false): array
	{
		if (!$this->userId) {
			// 随机生成一个 userId
			$this->setUserId( uniqid());
		}
		$this->Query($message)->Build($response_type);
		try {
			while (true) {
				$status_info =  $this->getChatRetrieve($this->chatId);
				if ($status_info['status'] == 'completed') {
					return $this->getChatMessageList($this->chatId);
				}
				sleep(1);
			}
		} catch (HttpException $e) {
			throw new HttpException('Failed to send message: ' . $e->getMessage());
		}
	}

}