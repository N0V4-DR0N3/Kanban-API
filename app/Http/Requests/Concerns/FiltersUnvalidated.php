<?php

namespace App\Http\Requests\Concerns;

trait FiltersUnvalidated
{
    protected function filterOutUnvalidated(): void
    {
        $this->getInputSource()->replace($this->validator->validated());
    }

    protected function passedValidation(): void
    {
        $this->filterOutUnvalidated();
    }
}
