<?php

namespace App\Reports\Example;

use App\Reports\Report;
use App\Services\UserService;

final class ExampleReport extends Report
{
    /*
     * Using `parent::__construct()`, readonly dependencies are auto-injected.
     */
    readonly protected UserService $users;

    public function __construct(
        public int $units,
    ) {
        parent::__construct();
    }

    public function getViewName(): string
    {
        return 'reports.examples';
    }

    public function getData(): array
    {
        $units = $this->units;
        $namespace = $this->users::class;

        return compact('namespace', 'units');
    }
}
