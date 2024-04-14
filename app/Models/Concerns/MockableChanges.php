<?php

namespace App\Models\Concerns;

trait MockableChanges
{
    protected bool $mockChanges = false;

    /**
     * @var array<string, mixed>
     */
    protected array $mockedChanges = [];

    /**
     * @param array<string, mixed>|false $changes
     *
     * @return $this
     */
    public function mockChanges(array|false $changes): static
    {
        $this->mockChanges = $changes !== false;
        $this->mockedChanges = $changes ?: [];

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getChanges(): array
    {
        if ($this->mockChanges) {
            return $this->mockedChanges;
        }

        return parent::getChanges();
    }
}
