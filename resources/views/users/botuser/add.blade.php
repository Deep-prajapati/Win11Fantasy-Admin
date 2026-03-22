@extends('layouts.app')
@section('contents')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Bots</h5>
            <a href="{{ route('admin.users.bots.list') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <form id="formAccountSettings" method="POST" action="{{ route('admin.users.bots.add') }}">
                @csrf
                <div id="botsAdd">


                    @if (old('name'))
                        @foreach (old('name') as $index => $oldName)
                            <div class="row bot-row">
                                <div class="mb-3 col-md-6">
                                    <label for="name" class="form-label">Name</label>
                                    <input class="form-control @error('name.' . $index) is-invalid @enderror" type="text"
                                        name="name[]" value="{{ old('name.' . $index) }}" placeholder="user" required>
                                    @error('name.' . $index)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input class="form-control @error('email.' . $index) is-invalid @enderror"
                                        type="text" name="email[]" value="{{ old('email.' . $index) }}"
                                        placeholder="user@bot.com">
                                    @error('email.' . $index)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="row bot-row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input class="form-control @error('name') is-invalid @enderror" type="text"
                                    name="name[]" value="{{ old('name.0') }}" placeholder="user" required autofocus>
                                @error('name.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input class="form-control @error('email') is-invalid @enderror" type="text"
                                    name="email[]" value="{{ old('email.0') }}" placeholder="user@bot.com">
                                @error('email.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif

                </div>

                <div class="mt-2">
                    <button type="button" class="btn btn-outline-success" id="addRow">
                        <i class="tf-icons bx bx-list-plus"></i> Add Row
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="removeRow">
                        <i class="tf-icons bx bx-trash"></i> Remove Row
                    </button>
                    <button type="submit" class="btn btn-primary me-2">Save changes</button>
                    <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#addRow').click(function() {
                var newRow = $('.bot-row:first').clone();
                newRow.find('input').val('');
                $('#botsAdd').append(newRow);
            });

            $('#removeRow').click(function() {
                if ($('#botsAdd .bot-row').length > 1) {
                    $('#botsAdd .bot-row:last').remove();
                }
            });
        });
    </script>
@endsection
