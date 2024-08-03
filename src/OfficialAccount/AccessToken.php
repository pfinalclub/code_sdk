<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/18
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);

namespace CozeSdk\OfficialAccount;

use AllowDynamicProperties;
use CozeSdk\Kernel\Contracts\AccessToken as AccessTokenInterface;
use CozeSdk\Kernel\Exception\HttpException;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AllowDynamicProperties]
class AccessToken implements AccessTokenInterface
{
    protected HttpClientInterface $httpClient;

    protected CacheInterface $cache;
    const CACHE_KEY_PREFIX = 'official_account';

    public function __construct(
        protected string     $kid,
        protected string     $sign,
        protected string     $iss,
        protected ?string    $key = null,
        ?CacheInterface      $cache = null,
        ?HttpClientInterface $httpClient = null,
        protected ?bool      $stable = false
    )
    {
        $this->httpClient = $httpClient ?? HttpClient::create(['base_uri' => 'https://api.coze.cn/']);
        $this->cache      = $cache ?? new Psr16Cache(new FilesystemAdapter(namespace: 'coze', defaultLifetime: 1500));
    }


    public function getKey(): string
    {
        return $this->key ?? $this->key = sprintf('%s.access_token.%s.%s', static::CACHE_KEY_PREFIX, $this->iss, $this->kid);
    }

    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getToken(): string
    {
        try {
            $token = $this->cache->get($this->getKey());
            if ($token && is_string($token)) {
                return $token;
            }
            return $this->refresh();
        } catch (HttpException $e) {
            throw new HttpException('Failed to get access_token: ' . $e->getMessage());
        }

    }

    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    public function toQuery(): array
    {
        try {
            return ['access_token' => $this->getToken()];
        } catch (HttpException|InvalidArgumentException $e) {
            throw new HttpException('Failed to get access_token: ' . $e->getMessage());
        }
    }

    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    public function refresh(): string
    {
        try {
            return $this->getAccessToken();
        } catch (HttpException|InvalidArgumentException $e) {
            throw new HttpException('Failed to get access_token: ' . $e->getMessage());
        }
    }

    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getAccessToken(): string
    {
        try {
            $response = $this->httpClient->request(
                'POST',
                'api/permission/oauth2/token',
                [
                    "headers" => $this->getTokenHeader(),
                    "body"    => json_encode([
                        "duration_seconds" => 86399,
                        "grant_type"       => "urn:ietf:params:oauth:grant-type:jwt-bearer"
                    ])
                ]
            )->toArray(false);
        } catch (ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            throw new HttpException('Failed to get access_token: ' . $e->getMessage());
        }
        if (empty($response['access_token'])) {
            throw new HttpException('Failed to get access_token: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
        }
        $this->cache->set($this->getKey(), $response['access_token'], intval($response['expires_in']));
        return $response['access_token'];
    }

    public function getTokenHeader(): array
    {
        $header['content-type']  = "application/json";
        $header['authorization'] = 'Bearer ' . $this->sign;
        return $header;
    }
}