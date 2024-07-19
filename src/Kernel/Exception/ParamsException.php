<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/19
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Kernel\Exception;

use PHPUnit\Framework\Exception;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ParamsException extends Exception
{
    public ?ResponseInterface $response;
    public function __construct(string $message, ?ResponseInterface $response = null, int $code = 0)
    {
        parent::__construct($message, $code);

        $this->response = $response;

        $response?->getInfo();
    }
}