<?php

namespace App\Livewire\Cricket;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\DefaultContest as ModelDefaultContest;
use Livewire\WithoutUrlPagination;
use Flasher\Laravel\Facade\Flasher;

class DefaultContest extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $listeners = ['refreshComponent' => '$refresh'];
    public $contestIdToDelete;

    public function confirmDelete($contestId)
    {
        $this->contestIdToDelete = $contestId;
        sweetalert()
            ->showDenyButton()
            ->info('Are you sure you want to delete this contest.?');
    }

    #[On('sweetalert:confirmed')]
public function onConfirmed(array $payload): void
{
    $contest = ModelDefaultContest::find($this->contestIdToDelete);

    if ($contest) {
        if (filter_var($contest->cancellation, FILTER_VALIDATE_BOOLEAN)) {
            $contest->is_deleted = 1; // Custom soft delete
            $contest->save();
            Flasher::success('Contest deleted successfully.');
        } else {
            Flasher::error('This contest cannot be deleted because it is not cancellable.');
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
        $contests = ModelDefaultContest::query();
        $contests->with('contestType');
        $contests = $contests->paginate(env('PER_PAGE_RECORDS', 10))->withPath(request()->url());
        return view('livewire.cricket.default-contest', [
            'contests' => $contests
        ]);
    }
}
