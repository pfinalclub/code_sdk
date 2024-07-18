<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/18
 * Email: <lampxiezi@163.com>
 */

declare(strict_types=1);

namespace CozeSdk\Kernel;

use CozeSdk\Kernel\Contracts\Config as ConfigInterface;
use CozeSdk\Kernel\Support\Arr;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;

class Config implements ConfigInterface
{
    protected array $requiredKeys = [];

    public function __construct(
        protected array $items = [],
    )
    {
        $this->checkMissingKeys();
    }

    public function checkMissingKeys(): bool
    {
        if (empty($this->requiredKeys)) {
            return true;
        }

        $missingKeys = [];

        foreach ($this->requiredKeys as $key) {
            if (!$this->has($key)) {
                $missingKeys[] = $key;
            }
        }

        if (!empty($missingKeys)) {
            throw new InvalidArgumentException(sprintf("\"%s\" cannot be empty.\r\n", implode(',', $missingKeys)));
        }

        return true;
    }

    public function all(): array
    {
        return $this->items;
    }

    #[Pure]
    public function has(string $key): bool
    {
        return Arr::has($this->items, $key);
    }

    public function set(string $key, mixed $value = null): void
    {
        Arr::set($this->items, $key, $value);
    }

    #[Pure]
    public function get(array|string $key, mixed $default = null): mixed
    {
        if (is_array($key)) {
            return $this->getMany($key);
        }

        return Arr::get($this->items, $key, $default);
    }

    #[Pure]
    public function getMany(array $keys): array
    {
        $config = [];

        foreach ($keys as $key => $default) {
            if (is_numeric($key)) {
                [$key, $default] = [$default, null];
            }

            $config[$key] = Arr::get($this->items, $key, $default);
        }

        return $config;
    }


    #[Pure]
    public function offsetExists(mixed $offset): bool
    {
        return $this->has(strval($offset));
    }

    #[Pure]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get(strval($offset));
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set(strval($offset), $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->set(strval($offset));
    }

}