<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MatchController as ApiMatch;
use App\Http\Controllers\Api\AuthController as ApiAuth;
use App\Http\Controllers\Api\UserController as ApiUser;
use App\Http\Controllers\Api\UserChatController as ApiUserChat;
use App\Http\Controllers\Api\RechargeController as ApiRecharge;
use App\Http\Controllers\Api\SettingController as ApiSetting;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Sports\FootballSportsController;
use App\Http\Controllers\Sports\OddsSportsController;
use App\Http\Controllers\Sports\SportsmonkController;
use App\Http\Middleware\UserBlocked;
use App\Http\Controllers\Api\Football\MatchController as ApiFMatch;

/***************  CRICKET API  *******************/
Route::get('/getfixture', [SportsmonkController::class, 'getfixture']);
Route::get('/updatefixture', [SportsmonkController::class, 'updatefixture']);
Route::get('/getTeams', [SportsmonkController::class, 'getTeams']);
// Route::get('/getlineup', [SportsmonkController::class, 'getfixturelineup']);
Route::get('/getbatball', [SportsmonkController::class, 'getBettingBolling']);
Route::get('/generatePoints', [SportsmonkController::class, 'generatePoints']);
Route::get('/set-points-rank', [SportsmonkController::class, 'setPointsRanks']);
Route::get('/createContest', [SportsmonkController::class, 'createContest']);
Route::get('/rankGenerate',[SportsmonkController::class,'rankGenerate']);

Route::get('/prizeDistribute',[SportsmonkController::class,'prizeDistribute']);
Route::get('/botJoinContest',[SportsmonkController::class,'botJoinContest']);
Route::get('/resetBotsTeams',[SportsmonkController::class,'resetBotsTeams']);
Route::get('/cancel-contest',[SportsmonkController::class,'cancelContest']);

Route::prefix('odds')->group(function () 
{
    Route::get('types',[OddsSportsController::class,'getBaseTypes']);
});

Route::prefix('football')->group(function () 
{
    Route::get('matches',[FootballSportsController::class,'getMatches']);
    Route::get('matches/update',[FootballSportsController::class,'updateMatch']);
    Route::get('get-teams',[FootballSportsController::class,'getTeamWithPlayers']);
    Route::get('get-player-details',[FootballSportsController::class,'getPlayerDetails']);
    Route::get('generate-player-points',[FootballSportsController::class,'makePlayerPoints']);
    Route::get('create-contest', [FootballSportsController::class, 'createContest']);

    Route::get('set-points-rank', [FootballSportsController::class, 'setPointsRanks']);
    Route::get('prizeDistribute',[FootballSportsController::class,'prizeDistribute']);
    Route::get('cancel-contest',[FootballSportsController::class,'cancelContest']);
});

Route::get('appsettings',[CommonController::class,'getSettings']);

Route::prefix('user')->group(function () 
{
    Route::post('login', [ApiAuth::class, 'login']);
    Route::post('otpverify', [ApiAuth::class, 'otpVerify']);
    Route::post('register', [ApiAuth::class, 'register']);

    Route::group(['middleware' => 'auth:api'], function () 
    {
        Route::get('profile', [ApiUser::class, 'profile']);
        Route::post('profile-update', [ApiUser::class, 'profileUpdate']);
        Route::get('transaction', [ApiUser::class, 'transaction']);

        Route::group(['prefix' => 'chat'], function () 
        {
            Route::get('/', [ApiUserChat::class, 'index']);
            Route::get('/users', [ApiUserChat::class, 'chatusers']);
            Route::post('/conversation', [ApiUserChat::class, 'conversation']);
            Route::post('/send', [ApiUserChat::class, 'store']);
            Route::post('/{conversation}/send', [ApiUserChat::class, 'sendMessage']);
            Route::get('/{conversation}/messages', [ApiUserChat::class, 'messages']);
        });

        Route::get('leaderboard',[ApiUser::class,'leaderboard']);
        Route::post('recharge',[ApiRecharge::class,'initiate']);
        Route::post('withdraw',[ApiRecharge::class,'withdraw']);

        Route::group(['prefix' => 'setting'], function () 
        {
            Route::get('/upi', [ApiSetting::class, 'UPI']);
            Route::post('/version', [ApiSetting::class, 'Version']);
        });
    });
});

Route::group(['prefix' => 'cricket'], function () 
{
    Route::match(['get','post'],'/matches/{status}', [ApiMatch::class, 'index'])->where('status', 'live|upcoming|complete');
    Route::post('/match/{fixture_id}', [ApiMatch::class, 'matchdetails']);
    Route::post('/match/{fixture_id}/players', [ApiMatch::class, 'players']);
    Route::post('/match/{fixture_id}/playing11', [ApiMatch::class, 'playing11']);
    Route::post('match/{fixture_id}/contests', [ApiMatch::class, 'contests']);
    Route::post('contest/{contest_id}/price-details', [ApiMatch::class, 'priceDetails']);
    Route::post('match/{fixture_id}/get-score', [ApiMatch::class, 'getScore']);
});

Route::group(['middleware' => ['auth:api', UserBlocked::class]], function () 
{
    Route::group(['prefix' => 'cricket'], function () 
    {
        Route::post('match/{fixture_id}/{contest_id}/view', [ApiMatch::class, 'mactchContestView']);
        Route::get('match/{fixture_id}/join-contest/list',[ApiMatch::class,'joinedContest']);
        Route::post('match/{fixture_id}/join-contest',[ApiMatch::class,'addJoinContest']);
        Route::get('match/{fixture_id}/get-teams', [ApiMatch::class, 'getTeam']);
        Route::post('match/{fixture_id}/create-team', [ApiMatch::class, 'createTeam']);
        Route::post('match/{fixture_id}/update-team', [ApiMatch::class, 'updateTeam']);
        Route::get('match/{fixture_id}/{team_id}/team', [ApiMatch::class, 'preview']);
        Route::get('/mine-matches/cricket', [ApiMatch::class, 'mymatches']);
    });
});

Route::group(['prefix' => 'football'], function () 
{
    Route::get('matches/{status}', [ApiFMatch::class, 'index'])->where('status', 'live|upcoming|complete');
    Route::get('match/{match_id}/contests', [ApiFMatch::class, 'contests']);
    Route::get('match/{match_id}/teams', [ApiFMatch::class, 'teamsData']);
    Route::get('/match/{match_id}', [ApiFMatch::class, 'matchdetails']); 
    Route::get('match/{match_id}/get-score', [ApiFMatch::class, 'getScore']);
});

Route::group(['middleware' => ['auth:api',  UserBlocked::class]], function () 
{
    Route::group(['prefix' => 'football'], function () 
    {
        Route::get('match/{match_id}/{contest_id}/view', [ApiFMatch::class, 'mactchContestView']);
        Route::post('match/{match_id}/join-contest',[ApiFMatch::class,'addJoinContest']);
        Route::get('match/{match_id}/get-teams', [ApiFMatch::class, 'getTeam']);
        Route::post('match/{match_id}/create-team', [ApiFMatch::class, 'createTeam']);
        // Route::post('match/{match_id}/update-team', [ApiFMatch::class, 'updateTeam']);
        // Route::get('match/{match_id}/{team_id}/team', [ApiFMatch::class, 'preview']);
        Route::get('/mine-matches', [ApiFMatch::class, 'mymatches']);
    });
});