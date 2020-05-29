<?php declare(strict_types=1);

namespace App\HttpClient;

use App\HttpClient\Builder\HandlerStackBuilderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use function GuzzleHttp\Promise\settle;


/**
 * The "AbstractHttpClient" class
 */
abstract class AbstractHttpClient extends Client
{
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    public function __construct(
        HandlerStackBuilderInterface $handlerStackBuilder,
        LoggerInterface $logger = null,
        array $config = []
    )
    {
        $config['handler'] = $handlerStackBuilder->build();
        $config[RequestOptions::HEADERS]['Accept-Encoding'] = 'gzip, deflate, identity';
        $config[RequestOptions::DECODE_CONTENT] = true;
        $config[RequestOptions::ALLOW_REDIRECTS] = true;
        if (!isset($config[RequestOptions::CONNECT_TIMEOUT])) {
            $config[RequestOptions::CONNECT_TIMEOUT] = 10;
        }
        if (!isset($config[RequestOptions::READ_TIMEOUT])) {
            $config[RequestOptions::READ_TIMEOUT] = 10;
        }

        $this->logger = $logger ?? new NullLogger();

        parent::__construct($config);
    }

    protected function handlePromise(PromiseInterface $promise)
    {
        $completePromise = current(settle($promise)->wait());
        switch ($completePromise['state']) {
            default:
                $message = 'Guzzle\'s promise implementation has failed: "Unknown state"';
                $this->logger->emergency($message);

                throw new \RuntimeException($message);
            case PromiseInterface::FULFILLED:
                /** @var \Psr\Http\Message\ResponseInterface $response */
                $response = $completePromise['value'];
                $stream = $response->getBody();
                if ($stream->tell()) {
                    $stream->rewind();
                }

                return $stream->getContents();
            case PromiseInterface::REJECTED:
                /** @var \GuzzleHttp\Exception\RequestException $requestException */
                $requestException = $completePromise['reason'];

                throw $requestException;
        }
    }
}
