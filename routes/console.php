<?php

use Illuminate\Support\Facades\Schedule;
use App\Jobs\{
    BotJoinContest,
    GetFixture,
    CreateContest,
    GeneratePoints,
    GetBatBall,
    GetTeams,
    PrizeDistribute,
    RankGenerate,
    SetPointsRank,
    UpdateFixture,
};

// ⏱️ HOURLY
// Cricket
Schedule::job(new GetFixture)->hourly()->withoutOverlapping();
Schedule::job(new CreateContest)->hourly()->withoutOverlapping();
// Schedule::job(new FootballMatches)->hourly()->withoutOverlapping();
// Schedule::job(new FootballCreateContest)->hourly()->withoutOverlapping();


// ⏱️ 5 Min
Schedule::job(new RankGenerate)->everyMinute()->withoutOverlapping();
Schedule::job(new PrizeDistribute)->everyFiveMinutes()->withoutOverlapping();
// Schedule::job(new FootballPrizeDistribute)->everyFiveMinutes()->withoutOverlapping();


// ⏱️ 2 Min
Schedule::job(new BotJoinContest)->everyTwoMinutes()->withoutOverlapping();


// every minute
Schedule::job(new UpdateFixture)->everyMinute()->withoutOverlapping();
Schedule::job(new GetTeams)->everyMinute()->withoutOverlapping();
// Schedule::job(new FootballMatches)->everyMinute()->withoutOverlapping();
// Schedule::job(new FootballCreateContest)->everyMinute()->withoutOverlapping();
// Schedule::job(new FootballCreateContest)->everyMinute()->withoutOverlapping();

// Schedule::job(new CancleContest)->everyMinute()->withoutOverlapping();
// Schedule::job(new FootballCancleContest)->everyMinute()->withoutOverlapping();


// every half minute
// Schedule::job(new GetLineUp)->everyThirtySeconds()->withoutOverlapping();
Schedule::job(new GetBatBall)->everyThirtySeconds()->withoutOverlapping();
Schedule::job(new SetPointsRank)->everyThirtySeconds()->withoutOverlapping();
Schedule::job(new GeneratePoints)->everyThirtySeconds()->withoutOverlapping();
// Schedule::job(new FootBallSetPointsRank)->everyMinute()->withoutOverlapping();