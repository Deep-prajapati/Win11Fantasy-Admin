<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\League;
use App\Models\Season;
use App\Models\Fixture;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Flasher\Laravel\Facade\Flasher;


class CricketMatches extends Component
{
    use WithPagination,WithoutUrlPagination;
    public $match_id, $date, $status, $league, $season;
    public $leagues = [];
    public $seasons = [];
    public $sortColumn = 'starting_at';
    public $sortDirection = 'desc';
    protected $queryString = ['match_id', 'date', 'status', 'league', 'season'];
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->leagues = League::all();
        $this->date = Carbon::now()->toDateString(); 
        $this->updateSeasons();
    }
    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }
    private function updateSeasons()
    {
        $this->seasons = $this->league ? Season::where('league_id', $this->league)->get() : [];
    }

    public function updatedLeague($value)
    {
        if ($value) {
            $this->seasons = Season::where('league_id', $value)->get();
        } else {
            $this->seasons = [];
        }
        $this->season = '';
    }

    public function render()
    {
        $matches = Fixture::query();

        if ($this->league) {
            $matches = $matches->where('league_id', $this->league);
        }
        if ($this->season) {
            $matches = $matches->where('season_id', $this->season);
        }
        if ($this->date) {
            $matches = $matches->whereDate('starting_at', $this->date);
        }
        switch ($this->status) {
            case 'upcomming':
                $matches = $matches->Upcoming();
                break;
            case 'live':
                $matches = $matches->Live();
                break;
            case 'completed':
                $matches = $matches->where('is_completed', true);
                break;
            case 'cancelled':
                $matches = $matches->Cancelled();
                break;
        }
        if ($this->match_id) {
            $matches = $matches->where('fixture_id', 'LIKE', $this->match_id . '%');
        }

        $matches = $matches->with('season', 'league')
        // ->orderBy('starting_at', 'desc')
        ->orderBy($this->sortColumn, $this->sortDirection)
        ->paginate(env('PER_PAGE_RECORDS',10));

        return view('livewire.cricket-matches', [
            'matches' => $matches,
            'seasons' => $this->seasons
        ]);
    }
    public function cancelMatch($fixture_id)
    {
        $match = Fixture::where([
            'fixture_id' => $fixture_id,
            'is_live' => false,
            'is_cancelled' => false
        ])->first();

        if (!$match) {
            Flasher::success('Match cancelled successfully.');
            return;
        }

        $match->is_cancelled = true;
        $match->save();

        Flasher::success('Match cancelled successfully.');
        $this->dispatch('refreshComponent');
    }

    public function restoreMatch($fixture_id)
    {
        $match = Fixture::where([
            'fixture_id' => $fixture_id,
            'is_cancelled' => true
        ])->first();

        if (!$match) {
            Flasher::error('Match not found or is not cancelled.');
            return;
        }

        $match->is_cancelled = false;
        $match->save();

        Flasher::success('Match restored successfully.');

        $this->dispatch('refreshComponent');
    }

    // public function filterMatches()
    // {
    //     $this->resetPage();
    // }
    // public function updated($propertyName)
    // {
    //     if (in_array($propertyName, ['match_id', 'date', 'status', 'league', 'season'])) {
    //         $this->resetPage();
    //     }

    //     if ($propertyName === 'league') {
    //         $this->updateSeasons();
    //     }
    // }

    public function clearFilters()
    {
        $this->reset(['match_id', 'date', 'status', 'league', 'season']);
        $this->resetPage();
        // $this->updateSeasons();
    }
}
