<?php

namespace App\Livewire\Dashboard;

use App\Models\Categories;

use Livewire\Component;

class IncludePopUp extends Component
{
    public $categories;

    public $category_id = 0;

    public function render()
    {
        $this->categories = Categories::where('type', 'entrada')->get();

        return view('livewire.dashboard.include-pop-up');
    }

}
