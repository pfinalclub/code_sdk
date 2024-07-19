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
use JetBrains\PhpStorm\ArrayShape;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpOptions;
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
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function getToken(): string
    {
        $token = $this->cache->get($this->getKey());
        if ($token && is_string($token)) {
            return $token;
        }
        return $this->refresh();
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \HttpException
     */
    #[ArrayShape(['access_token' => 'string'])]
    public function toQuery(): array
    {
        return ['access_token' => $this->getToken()];
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function refresh(): string
    {
        return $this->getAccessToken();
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getAccessToken(): string
    {
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