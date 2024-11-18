<?php

	/**
	 * Author: PFinal南丞
	 * Date: 2024/7/18
	 * Email: <lampxiezi@163.com>
	 */
	declare(strict_types=1);

	namespace CozeSdk\Tests;

	use CozeSdk\OfficialAccount\Application;
	use PHPUnit\Framework\TestCase as BaseTestCase;

	class TestCase extends BaseTestCase
	{
		protected Application|null $app = null;

		protected function setUp(): void
		{
			$app       = new Application(
				config: [
					'kid'     => 'Mh9cF6xnzFdqL5osVx-i1Os6-mM7Psjb1swU7UBgJ6Q',
					'iss'     => '1135933249080',
					'keyPath' => __DIR__ . '/private_key.pem',
					'spaceId' => '7374606142925733940',
					'botId'   => '7381736405354971163'
				]
			);
			$this->app = $app;
		}
	}