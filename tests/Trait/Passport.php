<?php

namespace Tests\Trait;

use Tests\TestCase;

/**
 * Trait Passport
 *
 * @mixin TestCase
 */
trait Passport
{
    protected function issuePassportKeys(): void
    {
        $this->artisan('passport:keys');
    }

    protected function issuePassportPersonalToken(): void
    {
        $this->artisan("passport:client --personal --name=Kanban");
    }
}
