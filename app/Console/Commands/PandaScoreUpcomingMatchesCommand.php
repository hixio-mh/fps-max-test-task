<?php declare(strict_types=1);

namespace App\Console\Commands;

use App\HttpClient\AbstractHttpClient;
use App\League;
use App\Team;
use App\UpcomingMatch;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;

/**
 * The "MatchesCommand" class
 */
class PandaScoreUpcomingMatchesCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected $signature = 'panda-score:upcoming-matches';
    /**
     * @inheritdoc
     */
    protected $description = 'Get upcoming CS:GO matches';
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var AbstractHttpClient|\App\HttpClient\PandaScoreHttpClient
     */
    private AbstractHttpClient $httpClient;

    public function __construct(LoggerInterface $logger, AbstractHttpClient $httpClient)
    {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->logger->info(__METHOD__);

        $dto = $this->httpClient->upcomingMatches();

        $upcomingMatch = new UpcomingMatch();
        $countNewUpcomingMatches = $upcomingMatch->createFromPandaScoreUpcomingMatchDtoCollection($dto);
        $league = new League();
        $countNewLeagues = $league->createFromPandaScoreUpcomingMatchDtoCollection($dto);
        $team = new Team();
        $countNewTeams = $team->createFromPandaScoreUpcomingMatchDtoCollection($dto);

        $messages = [
            sprintf('Count New Upcoming Matches: %d', $countNewUpcomingMatches),
            sprintf('Count New Leagues: %d', $countNewLeagues),
            sprintf('Count New Teams: %d', $countNewTeams),
        ];
        $message = implode("\n", $messages);
        $this->logger->info($message);

        return 0;
    }
}
