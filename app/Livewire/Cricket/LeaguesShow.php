<?php

namespace App\Livewire\Cricket;

use App\Models\League;
use Livewire\Component;
use Livewire\WithPagination;

class LeaguesShow extends Component
{
    use WithPagination;
    public $sortColumn = 'status'; 
    public $sortDirection = 'desc'; 

    public function toggleStatus($id)
    {
        $subCategory = League::findOrFail($id);
        $subCategory->status = !$subCategory->status;
        $subCategory->save();
    }
    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }

        $this->render();
    }
    public function render()
    {
        $leagues = League::orderBy($this->sortColumn, $this->sortDirection)->paginate(env('PER_PAGE_RECORDS',10));
        return view('livewire.cricket.leagues-show',[
            'leagues' => $leagues,
        ]);
    }
}
