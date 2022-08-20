<?php

namespace App\Http\Livewire;

use App\Models\DataProdi;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard', [
            'prodis' => DataProdi::all(),
        ]);
    }
}
