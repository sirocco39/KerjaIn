<?php

namespace App\View\Components\join_worker;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class join_template extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.join_worker.join_template');
    }
}
