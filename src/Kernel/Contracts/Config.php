<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/18
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);

namespace CozeSdk\Kernel\Contracts;

use  ArrayAccess;

interface Config extends ArrayAccess
{
    /**
     * all
     * @return array
     */
    public function all(): array;

    public function has(string $key): bool;

    public function set(string $key, mixed $value = null): void;

    /**
     * get
     * @param array|string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(array|string $key, mixed $default = null): mixed;
}