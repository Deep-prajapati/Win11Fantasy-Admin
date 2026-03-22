<?php

namespace App\Livewire\Football\Match;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\FootballContest;
use Flasher\Laravel\Facade\Flasher;

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
            $contest = FootballContest::find($this->contestIdToCancel);
            if ($contest) {
                $contest->is_cancelled = true;
                $contest->save();
                Flasher::success('Contest cancelled successfully.');
            } else {
                Flasher::error('Contest not found.');
            }
        } else {
            Flasher::error('this action cannot be performed.');
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
        $contests = FootballContest::query();
        $contests = $contests->where(['match_id' => $this->fixture->match_id])->paginate(env('PER_PAGE_RECORDS', 10));
        return view('livewire.football.match.contest-list', [
            'contests' => $contests
        ]);
    }
}
