@extends('layouts.app')

@section('contents')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Add Contest</h5>
            <a href="{{ route('admin.cricket.default.contest.index') }}" class="btn btn-danger">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.cricket.default.contest.edit', $contest->id) }}" method="POST">
                @csrf
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <label for="contest_type_select" class="form-label">Contest Type</label>
                        <select class="form-select @error('contest_type') is-invalid @enderror" id="contest_type_select"
                            name="contest_type">
                            <option value="">Select Contest Type</option>
                            @foreach ($contest_types as $data)
                                <option value="{{ $data->id }}"
                                    {{ old('contest_type', $contest->contest_type) == $data->id ? 'selected' : '' }}>
                                    {{ $data->contest_type }}
                                </option>
                            @endforeach
                        </select>
                        @error('contest_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6">
                        <label for="mrp" class="form-label">MRP</label>
                        <input type="number" min="1" step="0.01"
                            class="form-control @error('mrp') is-invalid @enderror" id="mrp" name="mrp"
                            value="{{ old('mrp', $contest->mrp) }}" placeholder="Contest MRP">
                        @error('mrp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <label for="entry_fee" class="form-label">Entry Fee</label>
                        <input type="number" min="1" step="0.01"
                            class="form-control @error('entry_fee') is-invalid @enderror" id="entry_fee" name="entry_fee"
                            value="{{ old('entry_fee', $contest->entry_fees) }}" placeholder="Entry Fee">
                        @error('entry_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6">
                        <label for="usable_bonus" class="form-label">Usable Bonus (₹)</label>
                        <input type="number" min="0" max="100" step="0.01"
                            class="form-control @error('usable_bonus') is-invalid @enderror" id="usable_bonus"
                            name="usable_bonus" value="{{ old('usable_bonus', $contest->usable_bonus) }}"
                            placeholder="Usable Bonus">
                        @error('usable_bonus')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <label for="first_price" class="form-label">First Prize</label>
                        <input type="number" min="1" step="0.01"
                            class="form-control @error('first_price') is-invalid @enderror" id="first_price"
                            name="first_price" value="{{ old('first_price', $contest->first_prize) }}"
                            placeholder="First Prize">
                        @error('first_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6">
                        <label for="total_spots" class="form-label">Total Spots</label>
                        <input type="number" min="1" class="form-control @error('total_spots') is-invalid @enderror"
                            id="total_spots" name="total_spots" value="{{ old('total_spots', $contest->total_spots) }}"
                            placeholder="Total Spots">
                        @error('total_spots')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <label for="total_bots" class="form-label">Total Bots</label>
                        <input type="number" min="0" class="form-control @error('total_bots') is-invalid @enderror"
                            id="total_bots" name="total_bots" value="{{ old('total_bots',$contest->bot_user) }}" placeholder="Total Bots">
                        @error('total_bots')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-sm-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="cancellation" name="cancellation"
                                {{ old('cancellation',$contest->cancellation) ? 'checked' : '' }}>
                            <label class="form-check-label" for="cancellation">Is contest cancellation Allowed</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="is_free" name="is_free"
                                {{ old('is_free',$contest->is_free) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_free">Is Free to join contest</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="is_bonus_contest" name="is_bonus_contest"
                                {{ old('is_bonus_contest',$contest->bonus_contest) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_bonus_contest">Is bonus contest</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="is_cloneable" name="is_cloneable"
                                {{ old('is_cloneable',$contest->is_cloneable) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_cloneable">Make clone to all matches</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="is_felexible" name="is_felexible"
                                {{ old('is_felexible',$contest->is_felexible) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_felexible">Is Flexible Contest</label>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-2 {{ old('is_felexible' , $contest->is_felexible) ? '' : 'd-none' }}" id="commission-row">
                    <div class="col-sm-4">
                        <label for="admin_commission" class="form-label">
                            Admin Commission (%)
                        </label>
                        <input type="number" min="1" class="form-control @error('admin_commission') is-invalid @enderror"
                            id="admin_commission" name="admin_commission" value="{{ old('admin_commission' , $admin_commission) }}" placeholder="Admin Commission">
                        @error('admin_commission')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-4">
                        <label for="dis_commission" class="form-label">
                            Distributed Commission (%)
                        </label>
                        <input type="number" min="1" class="form-control @error('dis_commission') is-invalid @enderror"
                            id="dis_commission" name="dis_commission" value="{{ old('dis_commission' , $dis_commission) }}" placeholder="Distributed Commission">
                        @error('dis_commission')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-4">
                        <label for="dis_amount" class="form-label">
                            Distributed Amount (₹)
                        </label>
                        <input type="number" min="1" class="form-control @error('dis_amount') is-invalid @enderror"
                                id="dis_amount" name="dis_amount" value="{{ old('dis_amount' , $dis_amount) }}" placeholder="Distributed Amount">
                        @error('dis_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div id="priceBreakUps">
                    @if (old('from_rank'))
                        @foreach (old('from_rank') as $key => $fromRank)
                            <div class="row price-row">
                                <div class="col-4">
                                    <label class="form-label">From Rank</label>
                                    <input type="number" min="1"
                                        class="form-control from_rank @error("from_rank.$key") is-invalid @enderror"
                                        name="from_rank[]" value="{{ old("from_rank.$key") }}" placeholder="From Rank">
                                    @error("from_rank.$key")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-4">
                                    <label class="form-label">To Rank</label>
                                    <input type="number" min="1"
                                        class="form-control to_rank @error("to_rank.$key") is-invalid @enderror" name="to_rank[]"
                                        value="{{ old("to_rank.$key") }}" placeholder="To Rank">
                                    @error("to_rank.$key")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Prize</label>
                                    <input type="number" min="1"
                                        class="form-control prize @error("prize.$key") is-invalid @enderror" name="prize[]"
                                        value="{{ old("prize.$key") }}" placeholder="Prize">
                                    @error("prize.$key")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    @elseif(isset($contest->prizeBreakup))
                        @foreach ($contest->prizeBreakup as $key => $prize)
                            <div class="row price-row">
                                <div class="col-4">
                                    <label class="form-label">From Rank</label>
                                    <input type="number" min="1" class="form-control from_rank" name="from_rank[]"
                                        value="{{ $prize->rank_from }}" placeholder="From Rank">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">To Rank</label>
                                    <input type="number" min="1" class="form-control to_rank" name="to_rank[]"
                                        value="{{ $prize->rank_upto }}" placeholder="To Rank">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Prize</label>
                                    <input type="number" min="1" class="form-control prize" name="prize[]"
                                        value="{{ $prize->prize_amount }}" placeholder="Prize">
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="row price-row">
                            <div class="col-4">
                                <label for="from_rank" class="form-label">From rank</label>
                                <input type="number" min="0" class="form-control from_rank" name="from_rank[]"
                                    placeholder="From rank">
                            </div>
                            <div class="col-4">
                                <label for="to_rank" class="form-label">To rank</label>
                                <input type="number" min="0" class="form-control to_rank" name="to_rank[]"
                                    placeholder="To rank">
                            </div>
                            <div class="col-4">
                                <label for="prize" class="form-label">Prize</label>
                                <input type="number" min="0" class="form-control prize" name="prize[]"
                                    placeholder="Prize">
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-2 d-inline-block">
                    <button type="button" class="btn btn-outline-success" id="addRow">
                        <i class="tf-icons bx bx-list-plus"></i> Add Row
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="removeRow">
                        <i class="tf-icons bx bx-trash"></i> Remove Row
                    </button>
                    <button type="submit" class="btn btn-primary">Update Contest</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // $(document).ready(function() {
        //     $('#addRow').click(function() {
        //         var newRow = $('.price-row:first').clone();
        //         newRow.find('input').val('');
        //         $('#priceBreakUps').append(newRow);
        //     });

        //     $('#removeRow').click(function() {
        //         if ($('#priceBreakUps .price-row').length > 1) {
        //             $('#priceBreakUps .price-row:last').remove();
        //         }
        //     });
        // });

        $(document).ready(function() {
            $('#addRow').click(function() 
            {
                var newRow = $('.price-row:first').clone();
                newRow.find('input').val('');
                $('#priceBreakUps').append(newRow);
            });

            $('#removeRow').click(function() {
                if ($('#priceBreakUps .price-row').length > 1) 
                {
                    $('#priceBreakUps .price-row:last').remove();
                }
            });

            $(document).on('keyup', '.prize', function() 
            {
                if($('#is_felexible').is(':checked'))
                {
                    if((parseFloat($('#admin_commission').val()) || 0) == 0)
                    {
                        alert('Please enter admin commission to calculate total prize distribution.');
                        return true; // skip if admin commission is not set or zero
                    }

                    if((parseFloat($('#entry_fee').val()) || 0) == 0)
                    {
                        alert('Please enter entry fee to calculate total prize distribution.');
                        return true; // skip if entry fee is not set or zero
                    }

                    if((parseFloat($('#total_spots').val()) || 0) == 0)
                    {
                        alert('Please enter total spots to calculate total prize distribution.');
                        return true; // skip if total spots is not set or zero
                    }

                    let admin = parseFloat($('#admin_commission').val()) || 0;
                    let spots = parseFloat($('#total_spots').val()) || 0;
                    let entry = parseFloat($('#entry_fee').val()) || 0;

                    let poolAmount = spots * entry;
                    let total = 0;

                    $('.prize').each(function(index) 
                    {
                        let prize = parseFloat($(this).val()) || 0;
                        let from  = parseInt($('.from_rank').eq(index).val()) || 0;
                        let to    = parseInt($('.to_rank').eq(index).val()) || 0;

                        if (!from || !to || !prize) 
                        {
                            return true; // skip incomplete row
                        }

                        if (from > 0 && to >= from) 
                        {
                            let count = (to - from + 1);
                            let rowTotal = prize * count;

                            total += rowTotal;
                        }else{
                            alert('Please enter valid from and to ranks for prize distribution.');
                            return false; // skip if from and to ranks are not valid
                        }
                        
                        if ((total + admin) > 100) 
                        {
                            $(this).val(''); // current field clear
                            alert('Total prize distribution percentage cannot exceed 100%. Please adjust the prize values.');
                            return false; // loop break
                        }
                    });

                    $('#dis_commission').val(total);

                    let disAmount = (total / 100) * poolAmount;

                    $('#dis_amount').val(disAmount.toFixed(2));
                }
            });

            $('#is_felexible').change(function() 
            {
                if ($('#is_felexible').is(':checked')) 
                {
                    $('#commission-row').removeClass('d-none').show();
                    $('#admin_commission').val();
                    $('#dis_commission').val();
                }else{
                    $('#commission-row').addClass('d-none').hide();
                }
                
                $('#priceBreakUps .price-row').each(function(index) {
                    if(index > 0) {
                        $(this).remove();
                    }
                });
            });
        });
    </script>
@endsection
