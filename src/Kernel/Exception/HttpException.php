<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/19
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);
namespace CozeSdk\Kernel\Exception;

use Exception;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HttpException extends Exception
{
    public ?ResponseInterface $response;

    /**
     * HttpException constructor.
     */
    public function __construct(string $message, ?ResponseInterface $response = null, int $code = 0)
    {
        parent::__construct($message, $code);

        $this->response = $response;

        $response?->getInfo();
    }
}