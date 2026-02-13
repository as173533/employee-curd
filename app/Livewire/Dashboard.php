<?php

namespace App\Livewire;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public function mount()
    {
        // dd('middleware passed');
    }
    public function render()
    {
        return view('livewire.dashboard');
    }
}
