@extends('layouts.app')
@section('contents')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Add Contest type</h5>
            <a href="{{ route('admin.cricket.contest.type.index') }}" class="btn btn-danger">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.cricket.contest.type.add') }}" method="POST">
                @csrf
                <div class="row">
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
                        <label for="max_entries" class="form-label">Max entries</label>
                        <input type="number" min="1" class="form-control" id="max_entries"
                            placeholder="Contest max entries" name="max_entries" value="{{ old('max_entries') }}">
                        @error('contest_type')
                            <div id="max_entries" class="form-text text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="mb-2">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div id="description" class="form-text text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="cancellable"
                        @if (old('cancellable')) checked @endif name="cancellable">
                    <label class="form-check-label" for="cancellable">Is cancellable</label>
                    @error('cancellable')
                        <div id="cancellable" class="form-text text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary"> Save Contest Type</button>
            </form>
        </div>
    </div>
@endsection
