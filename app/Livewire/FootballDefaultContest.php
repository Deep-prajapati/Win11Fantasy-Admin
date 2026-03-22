<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Flasher\Laravel\Facade\Flasher;
use App\Models\FootballDefaultContest as defaultContest;
use Livewire\Attributes\On;

class FootballDefaultContest extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $listeners = ['refreshComponent' => '$refresh'];
    public $contestIdToDelete;

    public function confirmDelete($contestId)
    {
        $this->contestIdToDelete = $contestId;
        sweetalert()
            ->showDenyButton()
            ->info('Are you sure you want to delete this contest?');
    }

    #[On('sweetalert:confirmed')]
    public function onConfirmed(array $payload): void
    {
        $contest = defaultContest::find($this->contestIdToDelete);
        if ($contest) {
            $contest->delete();
            Flasher::success('Contest deleted successfully.');
        } else {
            Flasher::error('Contest not found.');
        }
    }
    #[On('sweetalert:denied')]
    public function onDeny(array $payload): void
    {
        $this->contestIdToDelete = null;
        flash()->info('Deletion cancelled.');
    }

    public function render()
    {
        $contests = defaultContest::query();
        $contests = $contests->paginate(env('PER_PAGE_RECORDS', 10));
        return view('livewire.football-default-contest', [
            'contests' => $contests,
        ]);
    }
}
