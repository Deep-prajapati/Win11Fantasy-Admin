<?php

namespace App\Livewire;

use App\Models\FootballLeague;
use App\Models\FootballMatch;
use App\Models\FootballSeason;
use Carbon\Carbon;
use Flasher\Laravel\Facade\Flasher;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class FootballMatches extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $match_id, $date, $status, $season,$league; //
    public $leagues = [];
    public $seasons = [];
    public $sortColumn = 'starting_at';
    public $sortDirection = 'desc';
    protected $queryString = ['match_id', 'date', 'status','league', 'season']; //,
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->leagues = FootballLeague::all();
        $this->date = Carbon::now()->toDateString(); 
        $this->updateSeasons();
    }
    public function updateSeasons(){
        $this->seasons = $this->league ? FootballSeason::where('league_id', $this->league)->get() : [];
    }
    public function updatedLeague($value)
    {
        if ($value) {
            $this->seasons = FootballSeason::where('league_id', $value)->get();
        } else {
            $this->seasons = [];
        }
        $this->season = '';
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

    public function render()
    {
        $matches = FootballMatch::query();

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
                $matches = $matches->where('is_upcomming', true);
                break;
            case 'live':
                $matches = $matches->where('is_live', true);
                break;
            case 'completed':
                $matches = $matches->where('is_completed', true);
                break;
            case 'cancelled':
                $matches = $matches->where('is_cancelled', true);
                break;
        }
        if ($this->match_id) {
            $matches = $matches->where('match_id', 'LIKE', $this->match_id . '%');
        }

        $matches = $matches->with('season','league')
            // ->orderBy('starting_at', 'desc')
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate(env('PER_PAGE_RECORDS', 10));

        return view('livewire.football-matches', [
            'matches' => $matches,
            'seasons' => $this->seasons
        ]);
    }
    public function clearFilters()
    {
        $this->reset(['match_id', 'date', 'status', 'league', 'season']);
        $this->resetPage();
        // $this->updateSeasons();
    }
    public function cancelMatch($match_id)
    {
        $match = FootballMatch::where([
            'match_id' => $match_id,
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
    public function restoreMatch($match_id)
    {
        $match = FootballMatch::where([
            'match_id' => $match_id,
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
}
