<?php

namespace App\Livewire;

use Livewire\Component;

class TesteInicial extends Component
{

    public $inputTeste;
    public $yn='false';
    public $eu;

    public function render()
    {
        return view('livewire.teste-inicial');
    }

    public function teste()
    {
        dd($this->inputTeste);
    }

}
