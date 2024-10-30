<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UpdatePassword extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function render()
    {
        return view('livewire.profile.update-password');
    }

    public function updatePassword()
    {
        $user = Auth::user();

        $validated = $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');

    }
}
