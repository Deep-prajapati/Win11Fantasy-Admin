@extends('layouts.app')

@section('contents')
    <div class="row mb-2">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-4">
                            <div class="card-body">
                                <h5 class="card-title text-primary">{{ $match->league->name }} ( {{ $match->round }})</h5>
                                <div class="container mt-4">
                                    <div class="row  text-center">
                                        <div class="col-5 text-start">
                                            <img src="{{ $match->localteam_image_path }}" height="50" width="50"
                                                alt="Local Team" class="img-fluid rounded">
                                            <p class="mt-2 fw-bold">{{ $match->localteam_code }}</p>
                                        </div>
                                        <div class="col-2 text-center">
                                            <p class="h4 fw-bold">VS</p>
                                        </div>
                                        <div class="col-5 text-end">
                                            <img src="{{ $match->visitorteam_image_path }}" height="50" width="50"
                                                alt="Visitor Team" class="img-fluid rounded">
                                            <p class="mt-2 fw-bold">{{ $match->visitorteam_code }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3 text-center">
                            <div class="card-body pb-0 px-0 px-md-4">
                                <img src="{{ $match->league->image_path }}" height="140" alt="Match"
                                    data-app-dark-img="{{ $match->league->image_path }}"
                                    data-app-light-img="{{ $match->league->image_path }}">
                            </div>
                        </div>
                        <div class="col-sm-5 text-end">
                            <p>Contest : {{$contest->id }}</p>
                            <p>Contest Type : {{$contest->contestType->contest_type ?? ''}}</p>
                            <p>User : {{$user->name ?? ''}}</p>
                            <p>Total Points : {{$team->points ?? 0}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @php
        $groupedPlayers = $team->players->groupBy('position_id');

        $order = [
            3,
            1,
            4,
            2
        ];
    @endphp
    
    <div class="row mb-2">
        @foreach($order as $role)
            @if(isset($groupedPlayers[$role]))
                
                <div class="col-12 mt-3">
                    <h5 class="fw-bold">
                        @if($role == 1)
                            Batsman
                        @elseif($role == 2)
                            Bowler
                        @elseif($role == 3)
                            Wicket-Keeper
                        @elseif($role == 4)
                            All-Rounder
                        @endif
                    </h5>
                </div>

                @foreach($groupedPlayers[$role] as $player)
                    <div class="col-sm-3 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-start">
                                    
                                    <div class="me-3">
                                        <img src="{{ $player->image_path }}" 
                                            class="d-block rounded" 
                                            height="50" width="50">
                                    </div>

                                    <div>
                                        <div>
                                            {{ $player->fullname ?? '' }}
                                        </div>

                                        @if($team->caption_id == $player->player_id)
                                            ({{ $player->position_name }}) 
                                            <span class="badge bg-label-primary">Captain</span>
                                            @php($percent = 2)

                                        @elseif($team->voic_caption_id == $player->player_id)
                                            ({{ $player->position_name }}) 
                                            <span class="badge bg-label-info">Vice-Captain</span>
                                            @php($percent = 1.5)

                                        @else
                                            ({{ $player->position_name }})
                                            @php($percent = 1)
                                        @endif

                                        <h6 class="card-title mb-2">
                                            {{ number_format(($points[$player->player_id] ?? 0) * $percent, 2) }}
                                        </h6>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            @endif
        @endforeach
    </div>
@endsection