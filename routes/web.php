<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserChatController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Web\Admin\AdminController;
use App\Http\Controllers\Web\Admin\AuthController;
use App\Http\Controllers\Web\Admin\UsersController;
use App\Http\Controllers\Web\Admin\CricketController;
use App\Http\Controllers\Web\Admin\RechargeController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\Football\MatchesController as FMController;
use App\Http\Controllers\Web\Admin\Football\ContestController as FMContestController;
use App\Http\Controllers\Web\Admin\WithdrawlController;

Route::get('send-lineup-notification',[CommonController::class,'testlinupMessage']);

Route::group(['as' => 'admin.'], function () 
{
    Route::match(['get', 'post'], '/', [AuthController::class, 'index'])->name('login');

    Route::group(['middleware' => 'admin.auth'], function () 
    {
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('profile', [AdminController::class, 'index'])->name('profile');
        
        Route::get('settings',[AdminController::class,'settings'])->name('settings');

        // user routes
        Route::get('/tnxlist', [UsersController::class, 'tnxlist'])->name('tnxlist');

        Route::group(['prefix' => 'users', 'as' => 'users.'], function () 
        {
            Route::get('/', [UsersController::class, 'index'])->name('list');
            Route::get('{user_id}/view', [UsersController::class, 'view'])->name('view');
            Route::match(['get','post'],'{user_id}/wallet', [UsersController::class, 'wallet'])->name('wallet');
            // Route::get('{user_id}/block', [UsersController::class, 'block'])->name('block');
            // Route::get('{user_id}/unblock', [UsersController::class, 'unblock'])->name('unblock');

            Route::group(['prefix' => 'bots', 'as' => 'bots.'], function () 
            {
                Route::get('/', [UsersController::class, 'botsUser'])->name('list');
                Route::match(['get','post'],'/add', [UsersController::class, 'botsUseradd'])->name('add');
                Route::get('{user_id}/status-update', [UsersController::class, 'botsUserStatus'])->name('status');
            });
        });

        // recharge routes
        Route::group(['prefix' => 'recharge', 'as' => 'recharge.'], function () 
        {
            Route::get('/', [RechargeController::class, 'index'])->name('list');
            Route::post('{recharge_id}/approve', [RechargeController::class, 'approveRecharge'])->name('approve');
            Route::post('{recharge_id}/reject', [RechargeController::class, 'rejectRecharge'])->name('reject');
        });

        Route::group(['prefix' => 'withdawal', 'as' => 'withdawal.'], function () 
        {
            Route::get('/', [WithdrawlController::class, 'index'])->name('list');
            Route::post('{withdrawal_id}/approve', [WithdrawlController::class, 'approveWithdrawal'])->name('approve');
            Route::post('{withdrawal_id}/reject', [WithdrawlController::class, 'rejectWithdrawal'])->name('reject');
        });

        // cricket routes
        Route::group(['prefix' => 'cricket', 'as' => 'cricket.'], function () 
        {
            Route::get('leagues', [CricketController::class, 'leagues'])->name('leagues');
            Route::get('matches', [CricketController::class, 'index'])->name('matches');
            // Route::get('match/{fixture_id}/cancel', [CricketController::class, 'cancelMatch'])->name('match.cancel');

            Route::group(['prefix' => 'match/{fixture_id}/contests', 'as' => 'match.contests.'], function () 
            {
                Route::get('/', [CricketController::class, 'matchContests'])->name('list');
                Route::get('/{contest_id}/view', [CricketController::class, 'matchContestView'])->name('view');
                Route::post('/add-manual', [CricketController::class, 'matchContestAddManual'])->name('add');
            });

            // Route::get('getseasons', [CricketController::class, 'getseasons'])->name('getseasons');
            Route::group(['prefix' => 'default-contest', 'as' => 'default.contest.'], function () 
            {
                Route::get('/', [CricketController::class, 'defaultContest'])->name('index');
                Route::get('/{contest_id}/view', [CricketController::class, 'defaultContestView'])->name('view');
                Route::match(['get', 'post'],'/{contest_id}/edit', [CricketController::class, 'defaultContestEdit'])->name('edit');
                Route::match(['get', 'post'], '/add', [CricketController::class, 'defaultContestAdd'])->name('add');
            });

            Route::group(['prefix' => 'contest-type', 'as' => 'contest.type.'], function () 
            {
                Route::get('/', [CricketController::class, 'contestType'])->name('index');
                // Route::get('/{contest_id}/view',[CricketController::class,'defaultContestView'])->name('view');
                Route::match(['get', 'post'], '/add', [CricketController::class, 'contestTypeAdd'])->name('add');
            });
            // Route::get('matches',[CricketController::class,'index'])->name('matches');
        });

        Route::group(['prefix' => 'football', 'as' => 'football.'], function () 
        {
            Route::get('leagues', [FMController::class, 'leagues'])->name('leagues');
            Route::get('matches', [FMController::class, 'index'])->name('matches');

            Route::group(['prefix' => 'match/{match_id}/contests', 'as' => 'match.contests.'], function () 
            {
                Route::get('/', [FMController::class, 'matchContests'])->name('list');
                Route::get('/{contest_id}/view', [FMController::class, 'matchContestView'])->name('view');
                Route::post('/add-manual', [FMController::class, 'matchContestAddManual'])->name('add');
            });

            // // Route::get('getseasons', [CricketController::class, 'getseasons'])->name('getseasons');
            Route::group(['prefix' => 'default-contest', 'as' => 'default.contest.'], function () 
            {
                Route::get('/', [FMContestController::class, 'defaultContest'])->name('index');
                Route::get('/{contest_id}/view', [FMContestController::class, 'defaultContestView'])->name('view');
                Route::match(['get', 'post'],'/{contest_id}/edit', [FMContestController::class, 'defaultContestEdit'])->name('edit');
                Route::match(['get', 'post'], '/add', [FMContestController::class, 'defaultContestAdd'])->name('add');
            });

            Route::group(['prefix' => 'contest-type', 'as' => 'contest.type.'], function () 
            {
                Route::get('/', [FMContestController::class, 'contestType'])->name('index');
                // Route::get('/{contest_id}/view',[CricketController::class,'defaultContestView'])->name('view');
                Route::match(['get', 'post'], '/add', [FMContestController::class, 'contestTypeAdd'])->name('add');
            });
            // Route::get('matches',[CricketController::class,'index'])->name('matches');
        });
    });
});