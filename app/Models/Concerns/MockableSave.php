<?php

namespace App\Models\Concerns;

trait MockableSave
{
    protected bool $mockSave = false;

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function mockSave(bool $value = true): static
    {
        $this->mockSave = $value;

        return $this;
    }

    // @phpstan-ignore-next-line
    public function save(array $options = []): bool {
        if ($this->mockSave) {
            return true;
        }

        return parent::save($options);
    }
}
