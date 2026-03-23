<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\TypesValue;
use App\Models\JoinCrickContest;

function joinedCricTeamCount($user_id, $match_id, $contest_id)
{
    return JoinCrickContest::where(['match_id' => $match_id, 'user_id' => $user_id, 'contest_id' => $contest_id])->count();
}
function alreayJoinedContestWithTeam($user_id, $team_id, $match_id, $contest_id)
{
    return JoinCrickContest::where(['created_team_id' => $team_id, 'match_id' => $match_id, 'user_id' => $user_id, 'contest_id' => $contest_id])->exists();
}
function todayUserCount()
{
    return User::where('role', 2)->whereDate('created_at', Carbon::today())->count();
}

function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 5) $class = 'active open';
    elseif ($type == 4) $class = 'collapsed';
    elseif ($type == 3) $class = 'show';
    elseif ($type == 2) $class = 'true';
    else $class = 'active';
    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) return $class;
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);
            if (strtolower(@$routeParam[0]) == strtolower($param)) return $class;
            else return;
        }
        return $class;
    }
}
function userStatusBage($user)
{
    if ($user->is_banned) {
        return '<span class="badge bg-label-danger me-1">Blocked</span>';
    } else {
        return '<span class="badge bg-label-primary me-1">Active</span>';
    }
}
function rechargePaymentStatus($status)
{
    switch ($status) {
        case 1:
            return '<span class="badge  bg-label-primary  me-1">Pending</span>';
            break;
        case 2:
            return '<span class="badge  bg-label-success  me-1">Approved</span>';
            break;
        case 3:
            return '<span class="badge bg-label-danger  me-1">Cancelled</span>';
            break;
        default:
            return '<span class="badge me-1">NA</span>';
            break;
    }
}

function formatDateTime($datetime, $format = 'g:i A, j M Y')
{
    $date = Carbon::parse($datetime, 'UTC')->setTimezone('Asia/Kolkata');
    return $date->format($format);
}

function matchStatusBage($match)
{
    if ($match->is_live) {
        return '<span class="badge bg-label-info me-1">Live</span>';
    } else if ($match->is_cancelled) {
        return '<span class="badge bg-label-danger me-1">Cancelled</span>';
    } else if ($match->is_completed) {
        return '<span class="badge bg-label-success me-1">Completed</span>';
    } else {
        return '<span class="badge bg-label-primary me-1">Upcomming</span>';
    }
}
function matchFootballStatusBage($match)
{
    if ($match->is_live) {
        return '<span class="badge bg-label-info me-1">Live</span>';
    } else if ($match->is_cancelled) {
        return '<span class="badge bg-label-danger me-1">Cancelled</span>';
    } else if ($match->is_completed) {
        return '<span class="badge bg-label-success me-1">Completed</span>';
    } else if($match->is_upcomming) {
        return '<span class="badge bg-label-primary me-1">Upcomming</span>';
    }else{
        return '<span class="badge me-1">NA</span>';
    }
}
function matchStatusBageByStatus($status)
{
    switch ($status) {
        case 'NS':
            return '<span class="badge bg-label-primary me-1">Upcomming</span>';
            break;
        case 'Aban.':
            return '<span class="badge bg-label-danger me-1">Cancelled</span>';
            break;
        case 'Live':
            return '<span class="badge bg-label-success me-1">Live</span>';
            break;

        default:
            return '<span class="badge me-1">NA</span>';
            break;
    }
}
function getUsersFilesUrl($data)
{
    return env('APP_URL') . "/" . $data;
}

function getContestForBotFill() {}
function checkUsersInContestForMatch($match_id, $contest_id)
{
    return JoinCrickContest::where(['match_id' => $match_id, 'contest_id' => $contest_id])->whereHas('user', function ($query) {
        $query->where('role', 2);
    })->count();
}
function countBotUserJoinedInContestForMatch($match_id, $contest_id, $user_id)
{
    return JoinCrickContest::where(['match_id' => $match_id, 'contest_id' => $contest_id, 'user_id' => $user_id])->count();
}
function botsAllowedInContest($match_id, $contest, $contest_type)
{
    $botCount = JoinCrickContest::where([
        'match_id' => $match_id,
        'contest_id' => $contest->id
    ])->whereHas('user', function ($query) {
        $query->where('role', 3);
    })->count();
    $botUserCount = User::where('role', 3)->count();
    $maxBotsTeam = $contest_type->max_entries * $botUserCount;
    return ($maxBotsTeam >= $contest->defaultContest->bot_user) ? ($botCount < $contest->defaultContest->bot_user) : (($botCount < $maxBotsTeam) ? true : false);
}
function getsportmonksImage($imagePath)
{
    $defaultImage = asset('assets/img/placeholder.png');
    if (empty($imagePath) || $imagePath === 'https://cdn.sportmonks.com' || $imagePath === 'https://cdn.sportmonks.com/') {
        return $defaultImage;
    }
    return $imagePath;
}

function getDefaultCredits($positionId)
{
    switch ($positionId) {
        case 1: // Batsman
            return 9.5;
        case 2:
            return 8.5;
        case 3:
            return 8.0;
        case 4: // All-rounder
            return 9.5;
        default: // Unknown position
            return 6.0;
    }
}

function typeStore($data)
{
    if (isset($data['id'])) {
        return  TypesValue::updateorCreate([
            'type_id' => $data['id'],
        ], [
            'name' => $data['name'] ?? '',
            'code' => $data['code'] ?? '',
            'model_type' => $data['model_type'] ?? '',
            'developer_name' => $data['developer_name'] ?? '',
            'stat_group' => $data['stat_group'] ?? '',
        ]);
    }
}

function generateShortCode(string $input): string
{
    $input = trim(preg_replace('/\s+/', ' ', $input));
    $words = explode(' ', $input);
    $shortCode = '';
    foreach ($words as $word) {
        $shortCode .= strtoupper($word[0]);
    }

    return $shortCode;
}
