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
	use RuntimeException;

	class Account implements AccountInterface
	{
		private string $kid;
		private string $iss;
		private ?string $keyPath;
		private ?string $iat;
		private ?string $exp;
		private ?string $token;

		public function __construct(
			string  $kid,
			string  $iss,
			?string $keyPath = null,
			?string $iat = null,
			?string $exp = null,
			?string $token = null
		)
		{
			$this->kid     = $kid;
			$this->iss     = $iss;
			$this->keyPath = $keyPath;
			$this->iat     = $iat;
			$this->exp     = $exp;
			$this->token   = $token;
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
			if (!$this->kid) {
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
			if (!$this->iss) {
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

			$signatureInput = Arr::base64UrlEncode(json_encode($header)) . "." . Arr::base64UrlEncode(json_encode($payload));

			$privateKey = $this->getPrivateKey();

			openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);

			return $signatureInput . "." . Arr::base64UrlEncode($signature);
		}

		/**
		 * @throws \Exception
		 */
		public function getJti(): string
		{
			return bin2hex(random_bytes(64));
		}

		/**
		 * @throws \Exception
		 */
		public function getPrivateKey(): OpenSSLAsymmetricKey
		{
			if (!$this->keyPath) {
				$this->keyPath = __DIR__ . '/private_key.pem';
			}

			if (!file_exists($this->keyPath)) {
				throw new RuntimeException("Private key file not found: {$this->keyPath}");
			}

			$privateKeyContent = file_get_contents($this->keyPath);

			return openssl_pkey_get_private($privateKeyContent);
		}
	}