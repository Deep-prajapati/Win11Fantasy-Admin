@extends('layouts.app')
@section('styles')
@livewireStyles()
@endsection
@section('contents')
{{-- //ricket.match.contestList --}}
<div class="row mb-2">
    <div class="col-lg-8 mb-4 ">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">{{ $match->league->name }} ( {{ $match->round }})</h5>
                            <div class="container mt-4">
                                <div class="row  text-center">
                                    <div class="col-5 text-start">
                                        <img src="{{ getsportmonksImage($match->localteam_image_path) }}" height="50"
                                            width="50" alt="Local Team" class="img-fluid rounded">
                                        <p class="mt-2 fw-bold">{{ $match->localteam_code }}</p>
                                    </div>
                                    <div class="col-2 text-center">
                                        <p class="h4 fw-bold">VS</p>
                                    </div>
                                    <div class="col-5 text-end">
                                        <img src="{{ getsportmonksImage($match->visitorteam_image_path) }}"
                                            height="50" width="50" alt="Visitor Team" class="img-fluid rounded">
                                        <p class="mt-2 fw-bold">{{ $match->visitorteam_code }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ getsportmonksImage($match->league->image_path) }}" height="140"
                                alt="Match" data-app-dark-img="{{ getsportmonksImage($match->league->image_path) }}"
                                data-app-light-img="{{ getsportmonksImage($match->league->image_path) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h3>{{ formatDateTime($match->starting_at) }}</h3>
                @if ($match->is_upcomming)
                <h5 class="card-title text-primary">⏳ Match Countdown</h5>
                <p id="countdown"></p>
                <button class="btn btn-primary" type="button" id="addMoreContest">Add More Contest</button>
                @else
                <h5>{!! matchStatusBage($match) !!}</h5>
                <p>{{ $match->note ?? 'NA' }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAddMoreContest" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddMoreContestTitle">Add More Contest</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Select More Contests</label>
                @if (count($moreContests) > 0)
                @foreach ($moreContests as $contest)
                <div class="form-check">
                    <input class="form-check-input contest-checkbox" type="checkbox"
                        value="{{ $contest->id }}" id="contest_{{ $contest->id }}">
                    <label class="form-check-label" for="contest_{{ $contest->id }}">
                        First Prize: {{ $contest->first_prize }} {{ $contest->contestType?->contest_type ?? '' }}
                        (&#8377; {{ $contest->entry_fees }})
                    </label>
                </div>
                @endforeach
                @else
                <div class="alert alert-info text-center">
                    No more contests available at the moment.
                </div>

                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                @if (count($moreContests) > 0)
                <button type="button" class="btn btn-primary" id="addContestBtn">Add Contest</button>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mb-2">
    <divl class="col-sm-12">
        @livewire('cricket.match.contestList',['fixture'=>$match])
    </divl>
</div>

@endsection
@section('scripts')
@livewireScripts()
<script>
    $(document).ready(function() {
        $('#addMoreContest').on('click', function() 
        {
            $('#modalAddMoreContest').modal('show');
        });

        $('#addContestBtn').on('click', function() 
        {
            let selectedValues = [];

            $('.contest-checkbox:checked').each(function() {
                selectedValues.push($(this).val());
            });

            let url = "{{ route('admin.cricket.match.contests.add', $match->fixture_id) }}";
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: csrfToken,
                    contests: selectedValues
                },
                success: function(response) 
                {
                    if (response.success) 
                    {
                        alert(response.message);
                        window.location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) 
                {
                    console.error("Error:", xhr.responseText);
                    alert("Something went wrong. Please try again.");
                }
            });

        });
    });
</script>
@if ($match->is_upcomming)
<script>
    var matchTime = new Date("{{ \Carbon\Carbon::parse($match->starting_at)->toISOString() }}").getTime();

    var matchTimeIST = new Date(
        "{{ \Carbon\Carbon::parse($match->starting_at)->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s') }}");
    var istTimeString = matchTimeIST.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    }) + " IST";
    var x = setInterval(function() {
        var now = new Date().getTime();
        var distance = matchTime - now;
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        document.getElementById("countdown").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds +
            "s ";
        if (distance < 0) {
            clearInterval(x);
            document.getElementById("countdown").innerHTML = "🏏 Match Started!";
        }
    }, 1000);
</script>
@endif
@endsection