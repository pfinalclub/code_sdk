<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/18
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);

namespace CozeSdk\OfficialAccount;

use CozeSdk\OfficialAccount\Contracts\Account as AccountInterface;
use Firebase\JWT\JWT;
use PHPUnit\Logging\Exception;
use Random\RandomException;
use RuntimeException;

class Account implements AccountInterface
{
    public function __construct(
        protected string     $kid,
        protected string     $iss,
        protected ?string    $iat = null,
        protected ?string    $exp = null,
        protected ?string    $token = null,
    ){

    }
    public function getKid(): string
    {
        return $this->kid;
    }

    public function getHeaderParams(): string
    {
        if ($this->kid === null) {
            throw new RuntimeException('No kid configured.');
        }
        return json_encode([
            'alg' => "RS256",
            'typ' => "JWT",
            'kid' => $this->kid
        ]);
    }

    public function getPayload(): array
    {
        if ($this->iss === null) {
            throw new RuntimeException('No iss configured.');
        }

        return [
            'iss' => $this->iss,
            "aud" => "api.coze.cn",
            'iat' => $this->iat ?: time(),
            'exp' => $this->exp ?: time() + 3600 * 2,
            'jti' => $this->getJti()
        ];
    }

    public function getSignature(): string
    {
        $header_str  = $this->getHeaderParams();
        $payload_str = $this->getPayload();
        $signature   = '1';
        // 使用Base64Url 编码
        $header_payload = base64_encode($header_str) . base64_encode($payload_str);
        // 使用 RS256 私钥为 kid 对 header_payload 进行签名
        $privateKey = $this->getPrivateKey();
        try {

            $jwt = JWT::encode($data, $privateKey, 'RS256');

        } catch (\Exception $e) {
            throw new Exception('Error encrypting signature: ' . $e->getMessage());
        }
        return $signature;
    }

    public function getJti(): string
    {
        # 生成随机64位字符串
        try {
            return bin2hex(random_bytes(64));
        } catch (RandomException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function getPrivateKey(): string
    {
        return "
-----BEGIN RSA PRIVATE KEY-----
  {$this->getKid()}
-----END RSA PRIVATE KEY-----
";
    }
}