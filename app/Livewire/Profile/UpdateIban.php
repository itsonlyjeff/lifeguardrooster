<?php

namespace App\Livewire\Profile;

use App\Rules\ValidIban;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UpdateIban extends Component
{
    public string $iban;
    public string $iban_tnv;

    public function mount(): void
    {
        $this->iban = Auth::user()->iban;
        $this->iban_tnv = Auth::user()->iban_tnv ? Auth::user()->iban_tnv : '';
    }

    public function updateIban(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'iban' => ['required', 'string', new ValidIban()],
            'iban_tnv' => ['required', 'string'],
        ]);

        $user->fill([
            'iban' => $validated['iban'],
            'iban_tnv' => $validated['iban_tnv'],
        ])->save();

        $this->dispatch('iban-updated', name: $user->iban);
    }

    public function render()
    {
        return view('livewire.profile.update-iban');
    }
}
