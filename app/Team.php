<?php

namespace App;

use App\Dto\PandaScoreLeagueDto;
use App\Dto\PandaScoreTeamDto;
use App\Dto\PandaScoreUpcomingMatchDto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

/**
 * Class Team
 *
 * @property string $id
 * @property int $panda_score_team_id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 */
class Team extends Model
{
    use Notifiable;

    /**
     * @inheritDoc
     */
    protected $table = 'teams';
    /**
     * @inheritDoc
     */
    protected $fillable = [
        'panda_score_team_id',
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
        $teams = $collection
            ->map(
                function (PandaScoreUpcomingMatchDto $dto) {
                    return $dto->getTeams();
                }
            )
            ->collapse()
            ->keyBy(
                function (PandaScoreTeamDto $dto) {
                    return $dto->getId();
                }
            );
        $teamIds = $teams->keys();
        $existingIds = DB::table($this->table)
                         ->select('panda_score_team_id')
                         ->whereIn('panda_score_team_id', $teamIds)
                         ->pluck('panda_score_team_id');
        /** @var PandaScoreTeamDto[] $newTeams */
        $newTeams = $teams->reject(
            function (PandaScoreTeamDto $dto) use ($existingIds) {
                return $existingIds->contains($dto->getId());
            }
        );

        $insertedRows = 0;
        foreach ($newTeams as $newTeam) {
            $model = new static(
                [
                    'panda_score_team_id' => $newTeam->getId(),
                    'name' => $newTeam->getName(),
                ]
            );
            $model->save();
            $insertedRows++;
        }

        return $insertedRows;
    }
}
