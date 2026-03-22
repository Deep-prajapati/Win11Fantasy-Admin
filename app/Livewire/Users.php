<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Flasher\Laravel\Facade\Flasher;
use Livewire\WithoutUrlPagination;

class Users extends Component
{

    use WithPagination,WithoutUrlPagination;
    public $mobile_number, $name, $email, $status;

    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->reset(['mobile_number', 'name', 'status', 'email']);
        $this->resetPage();
    }

    public function updated($propertyName)
    {
        $this->resetPage();
    }
    public function unblockUser($user_id){
        $user = User::where(['id' => $user_id, 'role' => 2, 'is_banned' => true])->first();
        if (!$user) {
            Flasher::error('Invalid User details');
            return;
        }
        $user->is_banned = false;
        $user->update();
        Flasher::success('User unblocked successfully.');
        $this->dispatch('refreshComponent');
    }
    public function blockUser($user_id){
        $user = User::where(['id' => $user_id, 'role' => 2, 'is_banned' => false])->first();
        if (!$user) {
           Flasher::error('Invalid User details');
           return;
        }
        $user->is_banned = true;
        $user->update();
        Flasher::success('User blocked successfully.');
        $this->dispatch('refreshComponent');
    }
    public function render()
    {
        $users = User::where('role', 2)
            ->when($this->name, fn($query) => $query->where('name', 'LIKE', "{$this->name}%"))
            ->when($this->mobile_number, fn($query) => $query->where('mobile_number', 'LIKE', "{$this->mobile_number}%"))
            ->when($this->email, fn($query) => $query->where('email', 'LIKE', "{$this->email}%"))
            ->when($this->status === 'blocked', fn($query) => $query->where('is_banned', true))
            ->when($this->status === 'active', fn($query) => $query->where('is_banned', false))
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->orderBy('created_at','desc')
            ->with('account')
            ->paginate(env('PER_PAGE_RECORDS', 10));
        return view('livewire.users', [
            'users' => $users,
        ]);
    }
}
