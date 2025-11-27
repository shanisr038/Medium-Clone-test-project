<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class CategoryTabs extends Component
{
    public $categories;

    /**
     * Create a new component instance.
     */
    public function __construct($categories)
    {
        $this->categories = $categories;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.category-tabs');
    }
}
