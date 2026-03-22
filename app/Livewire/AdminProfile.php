<?php

namespace App\Livewire;

use Livewire\Component;
use App\Helpers\FileHelper;
use Livewire\WithFileUploads;
use Flasher\Laravel\Facade\Flasher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminProfile extends Component
{
    use WithFileUploads;

    public $user;
    public $name, $email, $mobile_number, $image;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->mobile_number = $this->user->mobile_number;
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => 'image|max:1024', // Max 800KB
        ]);
        $imagePath = $this->image->store('user/profile', 'public');
        $this->user->update(['image' => 'storage/' . $imagePath]);
        Flasher::success('Profile picture updated successfully. ');
        $this->dispatch('refreshComponent');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ], [
            'current_password.required' => 'Current password is required.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.confirmed' => 'New password and confirmation do not match.',
        ]);
        if (!Hash::check($this->current_password, Auth::user()->password)) {
            Flasher::error('Current password is incorrect.');
            return;
        }
        $this->user->update([
            'password' => Hash::make($this->new_password)
        ]);
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        Flasher::success('Password updated successfully.');
    }
    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user->id,
            'mobile_number' => 'nullable|string|max:15',
        ]);

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
        ]);

        Flasher::success('Profile updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin-profile');
    }
}
