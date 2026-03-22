<?php

namespace App\Livewire\Cricket\Match;

use App\Models\Contest;
use App\Models\JoinCrickContest;
use App\Models\Transection;
use App\Models\UserWallet;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Flasher\Laravel\Facade\Flasher;
use Illuminate\Support\Facades\DB;

class ContestList extends Component
{
    use WithPagination;

    public $fixture;
    protected $listeners = ['refreshComponent' => '$refresh'];
    public $contestIdToCancel;

    public function mount($fixture)
    {
        $this->fixture = $fixture;
    }


    public function confirmCancel($contestId)
    {
        $this->contestIdToCancel = $contestId;
        sweetalert()
            ->showDenyButton()
            ->info('Are you sure you want to cancel this contest ?');
    }

    #[On('sweetalert:confirmed')]
    public function onConfirmed(array $payload): void
    {
        if (!$this->fixture->is_completed) {
            $contest = Contest::find($this->contestIdToCancel);        
            if ($contest) {
                DB::beginTransaction();
                try {
                    // Mark contest as cancelled
                    $contest->is_cancelled = true;
                    $contest->update();

                    // Get users who joined this contest
                    $joined = JoinCrickContest::where([
                            'match_id' => $this->fixture->fixture_id,
                            'contest_id' => $contest->id
                        ])
                        ->whereHas('user', fn($q) => $q->where('role', 2)) // regular users
                        ->get();

                    foreach ($joined as $data) {
                        // Refund entry fees to wallet
                        UserWallet::where('user_id', $data->user_id)
                            ->increment('balance', $data->entryfee_deposit)
                            ->increment('bonus', $data->entryfee_bonus);

                        // Log transaction
                        Transection::create([
                            'user_id' => $data->user_id,
                            'type' => 1,
                            'amount' => $data->entryfee_deposit,
                            'desc' => 'Contest Cancelled | ' . $this->fixture->localteam_code . ' - ' . $this->fixture->visitorteam_code,
                        ]);
                    }

                    DB::commit();
                    Flasher::success('Contest cancelled and users refunded successfully.');
                } catch (\Exception $e) {
                    DB::rollBack();
                    Flasher::error('Something went wrong while processing refunds.');
                }
            } else {
                Flasher::error('Contest not found.');
            }
        } else {
            Flasher::error('This action cannot be performed.');
        }
    }

    #[On('sweetalert:denied')]
    public function onDeny(array $payload): void
    {
        $this->contestIdToCancel = null;
        flash()->info('Contest Cancelation cancelled.');
    }

    public function render()
    {
        $contests = Contest::query();
        $contests = $contests->where(['match_id' => $this->fixture->fixture_id])->paginate(env('PER_PAGE_RECORDS', 10))->withPath(request()->url());
        return view('livewire.cricket.match.contest-list', [
            'contests' => $contests
        ]);
    }
}