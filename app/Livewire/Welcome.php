<?php

namespace App\Livewire;

use App\Models\OcorrenciaDevolucao;
use Livewire\Component;
use Carbon\Carbon;
use Livewire\WithFileUploads;

class Welcome extends Component
{
    use WithFileUploads;

    public function render()
    {
        return view('livewire.welcome')->layout('components.layouts.applogin');;
    }
}
