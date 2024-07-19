<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/18
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);

namespace CozeSdk\OfficialAccount;

use CozeSdk\Kernel\Support\Arr;
use CozeSdk\OfficialAccount\Contracts\Account as AccountInterface;
use JetBrains\PhpStorm\ArrayShape;
use OpenSSLAsymmetricKey;
use PHPUnit\Logging\Exception;
use RuntimeException;

class Account implements AccountInterface
{
    public function __construct(
        protected string $kid,
        protected string $iss,
        protected ?string $key_path = null,
        protected ?string $iat = null,
        protected ?string $exp = null,
        protected ?string $token = null,
    ){

    }

    public function getKid(): string
    {
        return $this->kid;
    }

    public function getIss(): string
    {
        return $this->iss;
    }

    #[ArrayShape(['alg' => "string", 'typ' => "string", 'kid' => "string"])]
    public function getHeaderParams(): array
    {
        if ($this->kid === null) {
            throw new RuntimeException('No kid configured.');
        }
        return [
            'alg' => "RS256",
            'typ' => "JWT",
            'kid' => $this->kid
        ];
    }

    /**
     * @throws \Exception
     */
    #[ArrayShape(['iss' => "string", "aud" => "string", 'iat' => "int|string", 'exp' => "float|int|string", 'jti' => "string"])]
    public function getPayload(): array
    {
        if ($this->iss === null) {
            throw new RuntimeException('No iss configured.');
        }

        return [
            'iss' => $this->iss,
            "aud" => "api.coze.cn",
            'iat' => $this->iat ?: time(),
            'exp' => $this->exp ?: time() + 3600,
            'jti' => $this->getJti()
        ];
    }

    /**
     * @throws \Exception
     */
    public function getSignature(): string
    {
        $header  = $this->getHeaderParams();
        $payload = $this->getPayload();
        // 使用Base64Url 编码
        $signature_input = Arr::base64UrlEncode(json_encode($header)) . "." . Arr::base64UrlEncode(json_encode($payload));
        try {
            // 使用 RS256 私钥为 kid 对 header_payload 进行签名
            $privateKey = $this->getPrivateKey();
            openssl_sign($signature_input,$signature,$privateKey,OPENSSL_ALGO_SHA256);
            $signature = $signature_input.".".Arr::base64UrlEncode($signature);
        } catch (\Exception $e) {
            throw new Exception('Error encrypting signature: ' . $e->getMessage());
        }
        return $signature;
    }

    /**
     * @throws \Exception
     */
    public function getJti(): string
    {
        # 生成随机64位字符串
        try {
            return bin2hex(random_bytes(64));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function getPrivateKey(): bool|OpenSSLAsymmetricKey
    {
        if (!$this->key_path) {
            $this->key_path = __DIR__;
        }
        $key_path_pr = $this->key_path .'/private_key.pem';
        if (!file_exists($key_path_pr)) {
            throw new \Exception(".pem 文件不存在:".$key_path_pr);
        }
        $private_key_content = file_get_contents($key_path_pr);
        return openssl_pkey_get_private($private_key_content);
    }
}