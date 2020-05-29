<?php declare(strict_types=1);

namespace App\Dto;

/**
 * The "PandaScoreUpcomingMatchDto" class
 */
final class PandaScoreUpcomingMatchDto
{
    private int $id;
    private string $type;
    private string $status;
    /**
     * @var PandaScoreLeagueDto
     */
    private PandaScoreLeagueDto $league;
    /**
     * @var array|PandaScoreTeamDto[]
     */
    private array $teams;

    public function __construct(int $id, string $type, string $status, PandaScoreLeagueDto $league, array $teams)
    {
        $this->id = $id;
        $this->type = $type;
        $this->status = $status;
        $this->league = $league;
        $this->teams = $teams;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return PandaScoreLeagueDto
     */
    public function getLeague(): PandaScoreLeagueDto
    {
        return $this->league;
    }

    /**
     * @return PandaScoreTeamDto[]|array
     */
    public function getTeams()
    {
        return $this->teams;
    }
}
