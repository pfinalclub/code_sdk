<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/18
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);

namespace CozeSdk\OfficialAccount;

use CozeSdk\Kernel\Contracts\AccessToken as AccessTokenInterface;
use HttpException;
use Psr\SimpleCache\CacheInterface;
use Random\RandomException;
use RuntimeException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AccessToken implements AccessTokenInterface
{
    protected HttpClientInterface $httpClient;

    protected CacheInterface $cache;
    const CACHE_KEY_PREFIX = 'official_account';
    public function __construct(
        protected string     $sign,
        protected ?string    $token = null,
        ?CacheInterface      $cache = null,
        ?HttpClientInterface $httpClient = null,
        protected ?bool      $stable = false
    )
    {
        $this->httpClient = $httpClient ?? HttpClient::create(['base_uri' => 'https://api.coze.cn/', 'headers' => $this->getTokenHeader()]);
        $this->cache      = $cache ?? new Psr16Cache(new FilesystemAdapter(namespace: 'coze', defaultLifetime: 1500));
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function getToken(): string
    {
        return $this->refresh();
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
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
     */
    public function getAccessToken(): string
    {
        $response = $this->httpClient->request(
            'GET',
            'api/permission/oauth2/token',
            [
               "duration_seconds"=>86399,
                "grant_type"=>"urn:ietf:params:oauth:grant-type:jwt-bearer"
            ]
        )->toArray(false);
        if (empty($response['access_token'])) {
            throw new HttpException('Failed to get access_token: '.json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        return $response['access_token'];
    }

    public function getTokenHeader(): array
    {
        $header['Content-Type']  = "application/json";
        $header['Authorization'] = 'Bearer ' . $this->sign;
        return $header;
    }
}