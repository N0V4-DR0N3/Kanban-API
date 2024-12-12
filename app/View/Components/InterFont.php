<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class InterFont extends Component
{
    /**
     * @var string[]
     */
    public array $prefetch = [];

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $prefetch = ''
    ) {
        $this->prefetch = array_map(trim(...), explode(',', $prefetch));
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.inter-font');
    }
}
