<?php declare(strict_types=1);

namespace App\Providers;

use App\Console\Commands\PandaScoreUpcomingMatchesCommand;
use App\HttpClient\Builder\HandlerStackBuilder;
use App\HttpClient\PandaScoreHttpClient;
use App\Logger\PandaScoreMatchesCommandLogger;
use GuzzleHttp\MessageFormatter;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * The "PandaScoreMatchesCommandServiceProvider" class
 */
class PandaScoreMatchesCommandServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        PandaScoreMatchesCommandLogger::class => PandaScoreMatchesCommandLogger::class,
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->app->singleton(
            PandaScoreUpcomingMatchesCommand::class,
            function (Application $app) {
                /** @var SerializerInterface $serializer */
                $serializer = $app->get(SerializerInterface::class);
                /** @var PandaScoreMatchesCommandLogger $logger */
                $logger = $app->get(PandaScoreMatchesCommandLogger::class);
                $psrLogger = $logger();

                $messageFormatter = new MessageFormatter(MessageFormatter::DEBUG);
                $handlerStackBuilder = new HandlerStackBuilder();
                $handlerStackBuilder
                    ->pushHttpErrorsMiddleware()
                    ->pushRedirectMiddleware()
                    ->pushCookiesMiddleware()
                    ->pushPrepareBodyMiddleware()
                    ->pushLogMiddleware($psrLogger, $messageFormatter)
                    ->pushRetryMiddleware($psrLogger);

                $httpClient = new PandaScoreHttpClient(
                    $serializer,
                    $handlerStackBuilder,
                    $psrLogger,
                    [
                        'base_uri' => config('app.api.panda_score.uri'),
                        'query' => [
                            'token' => config('app.api.panda_score.token')
                        ],
                        'headers' => [
                            'User-Agent' => config('app.name')
                        ]
                    ]
                );

                return new PandaScoreUpcomingMatchesCommand($psrLogger, $httpClient);
            }
        );
        parent::register();
    }
}
