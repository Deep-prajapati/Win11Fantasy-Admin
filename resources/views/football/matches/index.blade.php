@extends('layouts.app')
@section('styles')
    @livewireStyles
@endsection
@section('contents')
    <div class="card">
        <h5 class="card-header">Filters</h5>
        <div class="card-body">
            @livewire('football-matches')
        </div>
    </div>
@endsection
@section('scripts')
    @livewireScripts
@endsection
