@extends('layouts.app')
@section('contents')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Add Contest type</h5>
            <a href="{{ route('admin.football.contest.type.index') }}" class="btn btn-danger">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.football.contest.type.add') }}" method="POST">
                @csrf
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <label for="contest_type" class="form-label">Name</label>
                        <input type="text" class="form-control" id="contest_type" placeholder="Contest type"
                            name="contest_type" value="{{ old('contest_type') }}">

                        @error('contest_type')
                            <div id="contest_type" class="form-text text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label for="short_code" class="form-label">Short code</label>
                        <input type="text" class="form-control" id="short_code" placeholder="Contest type"
                            name="short_code" value="{{ old('short_code') }}">

                        @error('short_code')
                            <div id="short_code" class="form-text text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"> Save Contest Type</button>
            </form>
        </div>
    </div>
@endsection
