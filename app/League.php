<?php

namespace App;

use App\Dto\PandaScoreLeagueDto;
use App\Dto\PandaScoreUpcomingMatchDto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

/**
 * Class League
 *
 * @property string $id
 * @property int $panda_score_league_id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 */
class League extends Model
{
    use Notifiable;

    /**
     * @inheritDoc
     */
    protected $table = 'leagues';
    /**
     * @inheritDoc
     */
    protected $fillable = [
        'panda_score_league_id',
        'name',
    ];

    /**
     * @inheritDoc
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getKeyType()
    {
        return 'string';
    }

    /**
     * @param iterable|PandaScoreUpcomingMatchDto[] $items
     *
     * @return int
     */
    public function createFromPandaScoreUpcomingMatchDtoCollection(iterable $items)
    {
        $collection = collect($items);
        $leagues = $collection
            ->map(
                function (PandaScoreUpcomingMatchDto $dto) {
                    return $dto->getLeague();
                }
            )
            ->keyBy(
                function (PandaScoreLeagueDto $dto) {
                    return $dto->getId();
                }
            );
        $leagueIds = $leagues->keys();

        $existingIds = DB::table($this->table)
                         ->select('panda_score_league_id')
                         ->whereIn('panda_score_league_id', $leagueIds)
                         ->pluck('panda_score_league_id');
        /** @var PandaScoreLeagueDto[] $newLeagues */
        $newLeagues = $leagues->reject(
            function (PandaScoreLeagueDto $dto) use ($existingIds) {
                return $existingIds->contains($dto->getId());
            }
        );

        $insertedRows = 0;
        foreach ($newLeagues as $newLeague) {
            $model = new static(
                [
                    'panda_score_league_id' => $newLeague->getId(),
                    'name' => $newLeague->getName(),
                ]
            );
            $model->save();
            $insertedRows++;
        }

        return $insertedRows;
    }
}
