<?php

namespace App\Livewire\Cricket;

use App\Models\ContestType as ModelsContestType;
use Flasher\Laravel\Facade\Flasher;
use Livewire\Component;
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
    $contest = ModelsContestType::find($this->contestIdToDelete);
    
    if ($contest) {
        if (filter_var($contest->cancellable, FILTER_VALIDATE_BOOLEAN)) {
            $contest->is_deleted = 1;
            $contest->save();  
            Flasher::success('Contest cancelled successfully.');
        } else {
            Flasher::error('This contest cannot be cancelled.');
        }
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
        $contestTypes = ModelsContestType::query();
        $contestTypes = $contestTypes->paginate(env('PER_PAGE_RECORDS', 10))->withPath(request()->url());
        return view('livewire.cricket.contest-type', [
            'contestTypes' => $contestTypes
        ]);
    }
}
