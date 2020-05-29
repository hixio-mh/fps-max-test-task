<?php declare(strict_types=1);

namespace App\HttpClient\Builder;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * The "HandlerStackBuilder" class
 */
class HandlerStackBuilder implements HandlerStackBuilderInterface
{
    private HandlerStack $stack;

    public function __construct()
    {
        $this->stack = new HandlerStack(new CurlHandler());
    }

    public function pushHttpErrorsMiddleware()
    {
        $this->stack->push(Middleware::httpErrors(), Middleware::class . '::httpErrors');

        return $this;
    }

    public function pushRedirectMiddleware()
    {
        $this->stack->push(Middleware::redirect(), Middleware::class . '::redirect');

        return $this;
    }

    public function pushCookiesMiddleware()
    {
        $this->stack->push(Middleware::cookies(), Middleware::class . '::cookies');

        return $this;
    }

    public function pushPrepareBodyMiddleware()
    {
        $this->stack->push(Middleware::prepareBody(), Middleware::class . '::prepareBody');

        return $this;
    }

    public function pushLogMiddleware(
        LoggerInterface $logger,
        MessageFormatter $formatter,
        string $logLevel = LogLevel::INFO
    )
    {
        $this->stack->push(Middleware::log($logger, $formatter, $logLevel), Middleware::class . '::log');

        return $this;
    }

    public function pushRetryMiddleware(
        LoggerInterface $logger,
        string $logLevel = LogLevel::INFO,
        int $retries = 3,
        int $retryIntervalMSec = 3000
    )
    {
        $middleware = Middleware::retry(
            function (
                int $retry,
                RequestInterface $request,
                ?ResponseInterface $response = null,
                ?RequestException $exception = null
            ) use ($logger, $logLevel, $retries) {
                $retry++;

                $message = sprintf(
                    'Trying to establish a connection with the "%s" (%d/%d)',
                    $request->getUri(),
                    $retry,
                    $retries
                );
                $logger->log($logLevel, $message);

                $statusCode = 0;
                if ($response !== null) {
                    $statusCode = $response->getStatusCode();
                }

                if (($statusCode >= 100 && $statusCode < 400) || $retry >= $retries) {
                    return false;
                }

                return true;
            },
            function (int $retry, ResponseInterface $response = null) use ($logger, $logLevel, $retryIntervalMSec) {
                $message = sprintf('Fell asleep for %d milliseconds', $retryIntervalMSec);
                $logger->log($logLevel, $message);

                return $retryIntervalMSec;
            }
        );
        $this->stack->push($middleware, Middleware::class . '::retry');

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function build(): HandlerStack
    {
        return $this->stack;
    }
}
