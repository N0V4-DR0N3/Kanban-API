<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

final class ReportHeader extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public string $tz,
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.report-header');
    }
}
