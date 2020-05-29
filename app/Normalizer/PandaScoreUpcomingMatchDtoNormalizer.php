<?php declare(strict_types=1);

namespace App\Normalizer;

use App\Dto\PandaScoreLeagueDto;
use App\Dto\PandaScoreTeamDto;
use App\Dto\PandaScoreUpcomingMatchDto;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

/**
 * The "PandaScoreUpcomingMatchDtoNormalizer" class
 */
class PandaScoreUpcomingMatchDtoNormalizer implements ContextAwareDenormalizerInterface
{
    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        return $type === PandaScoreUpcomingMatchDto::class;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $iterator = new \ArrayIterator();
        foreach ($data as $datum) {
            $league = $datum['league'];
            $leagueDto = new PandaScoreLeagueDto($league['id'], $league['name']);

            $teams = [];
            foreach ($datum['opponents'] as $item) {
                $opponent = $item['opponent'];
                $teams[] = new PandaScoreTeamDto($opponent['id'], $opponent['name']);
            }

            $dto = new PandaScoreUpcomingMatchDto(
                $datum['id'],
                $datum['match_type'],
                $datum['status'],
                $leagueDto,
                $teams
            );
            $iterator->append($dto);
        }

        return $iterator;
    }
}
