@extends('layouts.app')
@section('styles')
    @livewireStyles
@endsection
@section('scripts')
    @livewireScripts
@endsection
@section('contents')
<div class="card">
    <h5 class="card-header">All Default Contest List</h5>
    @livewire('football-default-contest')
</div>
@endsection
