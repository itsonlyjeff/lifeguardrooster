<?php

namespace App\Livewire\Profile;

use App\Rules\ValidIban;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UpdateIban extends Component
{
    public string $iban;

    public function mount(): void
    {
        $this->iban = Auth::user()->iban;
    }

    public function updateIban(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'iban' => ['required', 'string', new ValidIban()],
        ]);

        $user->fill([
            'iban' => $validated['iban'],
        ])->save();

        $this->dispatch('iban-updated', name: $user->iban);
    }

    public function render()
    {
        return view('livewire.profile.update-iban');
    }
}
