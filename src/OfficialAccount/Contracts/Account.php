<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/18
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);

namespace CozeSdk\OfficialAccount\Contracts;

use OpenSSLAsymmetricKey;

/**
 * 账户实体接口。
 */
interface Account
{
    /**
     * 返回账户的唯一标识符（kid）。
     *
     * @return string
     */
    public function getKid(): string;

    /**
     * 返回账户的发行者标识符（iss）。
     *
     * @return string
     */
    public function getIss(): string;

    /**
     * 返回账户的头部参数。
     *
     * 返回的数组应该包含以下键：
     * - alg：签名算法（例如 RS256）
     * - typ：令牌类型（例如 JWT）
     * - kid：账户的唯一标识符（kid）
     *
     * @return array{
     *     alg: string,
     *     typ: string,
     *     kid: string
     * }
     */
    public function getHeaderParams(): array;

    /**
     * 返回账户的载荷。
     *
     * 返回的数组应该包含以下键：
     * - iss：账户的发行者标识符（iss）
     * - aud：账户的受众标识符（例如 api.coze.cn）
     * - iat：账户的发行时间戳（可选）
     * - exp：账户的过期时间戳（可选）
     * - jti：JWT 令牌的唯一标识符（jti）
     *
     * @return array{
     *     iss: string,
     *     aud: string,
     *     iat: int|string,
     *     exp: float|int|string,
     *     jti: string
     * }
     * @throws \Exception 如果载荷无法生成。
     */
    public function getPayload(): array;

    /**
     * 返回账户的签名。
     *
     * 签名使用 RS256 算法和账户关联的私钥生成。
     *
     * @return string
     * @throws \Exception 如果签名无法生成。
     */
    public function getSignature(): string;

    /**
     * 返回 JWT 令牌的唯一标识符（jti）。
     *
     * @return string
     * @throws \Exception 如果 jti 无法生成。
     */
    public function getJti(): string;

    /**
     * 返回账户关联的私钥。
     *
     * @return OpenSSLAsymmetricKey
     * @throws \Exception 如果私钥无法加载。
     */
    public function getPrivateKey(): OpenSSLAsymmetricKey;
}