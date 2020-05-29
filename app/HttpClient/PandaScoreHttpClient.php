<?php declare(strict_types=1);

namespace App\HttpClient;

use App\Dto\PandaScoreUpcomingMatchDto;
use App\HttpClient\Builder\HandlerStackBuilderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * The "PandaScoreHttpClient" Class
 */
class PandaScoreHttpClient extends AbstractHttpClient
{
    private const PER_PAGE = 100;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    public function __construct(
        SerializerInterface $serializer,
        HandlerStackBuilderInterface $handlerStackBuilder,
        LoggerInterface $logger = null,
        array $config = []
    )
    {
        $this->serializer = $serializer;
        parent::__construct($handlerStackBuilder, $logger, $config);
    }

    /**
     * @return PandaScoreUpcomingMatchDto[]
     */
    public function upcomingMatches()
    {
        $this->logger->info(__METHOD__);

        $query = $this->getQuery();
        $query['per_page'] = static::PER_PAGE;
        $promise = $this->getAsync('csgo/matches/upcoming', compact('query'));
        $response = $this->handlePromise($promise);

        return $this->serializer->deserialize(
            $response,
            PandaScoreUpcomingMatchDto::class,
            JsonEncoder::FORMAT
        );
    }

    private function getQuery()
    {
        return $this->getConfig('query');
    }
}
