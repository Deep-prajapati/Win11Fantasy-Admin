<?php

namespace App\Livewire\Football;

use Livewire\Component;
use App\Models\FootballContestType;
use Flasher\Laravel\Facade\Flasher;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\On;

class ContestType extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $listeners = ['refreshComponent' => '$refresh'];
    public $contestIdToDelete;

    public function confirmDelete($contestId)
    {
        $this->contestIdToDelete = $contestId;
        sweetalert()
            ->showDenyButton()
            ->info('Are you sure you want to delete the user?');
    }

    #[On('sweetalert:confirmed')]
    public function onConfirmed(array $payload): void
    {
        $contest = FootballContestType::find($this->contestIdToDelete);
        if ($contest) {
            $contest->delete();
            Flasher::success('Contest type deleted successfully.');
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
        $contestTypes = FootballContestType::orderBy('status', 'desc')->paginate(env('PER_PAGE_RECORDS', 10));

        return view('livewire.football.contest-type', ['contestTypes' => $contestTypes]);
    }
}
