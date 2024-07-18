<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/18
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Kernel\Traits;
use CozeSdk\Kernel\Config;
use CozeSdk\Kernel\Contracts\Config as ConfigInterface;
trait InteractWithConfig
{
    protected ConfigInterface $config;

    public function __construct(array|ConfigInterface $config)
    {
        $this->config = is_array($config) ? new Config($config) : $config;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    public function setConfig(ConfigInterface $config): static
    {
        $this->config = $config;

        return $this;
    }
}