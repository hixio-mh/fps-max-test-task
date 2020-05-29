<?php

namespace App;

use App\Dto\PandaScoreUpcomingMatchDto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

/**
 * Class UpcomingMatch
 *
 * @property string $id
 * @property int $panda_score_upcoming_match_id
 * @property string $type
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class UpcomingMatch extends Model
{
    use Notifiable;

    /**
     * @inheritDoc
     */
    protected $table = 'upcoming_matches';
    /**
     * @inheritDoc
     */
    protected $fillable = [
        'panda_score_upcoming_match_id',
        'type',
        'status',
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
        $upcomingMatches = $collection
            ->keyBy(
                function (PandaScoreUpcomingMatchDto $dto) {
                    return $dto->getId();
                }
            );
        $upcomingMatchIds = $upcomingMatches->keys();
        $existingIds = DB::table($this->table)
                         ->select('panda_score_upcoming_match_id')
                         ->whereIn('panda_score_upcoming_match_id', $upcomingMatchIds)
                         ->pluck('panda_score_upcoming_match_id');
        /** @var PandaScoreUpcomingMatchDto[] $newUpcomingMatches */
        $newUpcomingMatches = $upcomingMatches->reject(
            function (PandaScoreUpcomingMatchDto $dto) use ($existingIds) {
                return $existingIds->contains($dto->getId());
            }
        );

        $insertedRows = 0;
        foreach ($newUpcomingMatches as $newUpcomingMatch) {
            $model = new static(
                [
                    'panda_score_upcoming_match_id' => $newUpcomingMatch->getId(),
                    'type' => $newUpcomingMatch->getType(),
                    'status' => $newUpcomingMatch->getStatus(),
                ]
            );
            $model->save();
            $insertedRows++;
        }

        return $insertedRows;
    }
}
