<?php declare(strict_types=1);

namespace App\Logger;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\MemoryPeakUsageProcessor;

/**
 * The "PandaScoreMatchesCommandLogger" class
 */
class PandaScoreMatchesCommandLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @return Logger
     */
    public function __invoke()
    {
        $lineFormatter = new LineFormatter(
            "[%datetime%] %channel%.%level_name%:\n%message%\n%context%\n%extra%\n",
            null,
            true
        );

        $stdoutHandler = new StreamHandler('php://stdout');
        $stdoutHandler->setFormatter($lineFormatter);

        $fileHandler = new StreamHandler(storage_path('logs/console-command-panda-score:matches.log'));
        $fileHandler->setFormatter($lineFormatter);

        $handlers = [
            $stdoutHandler,
            $fileHandler
        ];
        $processors = [
            new MemoryPeakUsageProcessor()
        ];

        return new Logger(__CLASS__, $handlers, $processors);
    }
}
