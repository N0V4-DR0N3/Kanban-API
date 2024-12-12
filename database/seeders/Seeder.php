<?php

namespace Database\Seeders;

use App\Traits\InjectsReadonly;
use Illuminate\Database\Seeder as BaseSeeder;

abstract class Seeder extends BaseSeeder
{
    use InjectsReadonly;

    public function __construct()
    {
        $this->injectReadonly();
        $this->__setup();
    }

    public function __setup(): void
    {
    }

    abstract public function run(): void;
}
