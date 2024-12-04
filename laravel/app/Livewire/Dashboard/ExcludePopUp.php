<?php

namespace App\Livewire\Dashboard;

use App\Models\Categories;
use Livewire\Component;

class ExcludePopUp extends Component
{
    public $categories;

    public $category_id = 0;

    public function render()
    {
        $this->categories = Categories::where('type', 'saida')->get();

        return view('livewire.dashboard.exclude-pop-up');
    }
}
