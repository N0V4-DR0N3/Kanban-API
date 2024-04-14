<?php

namespace App\Console\Commands\Base;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:service')]
class MakeServiceCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    protected $type = 'Service';

    protected function getNameInput(): string
    {
        $name = parent::getNameInput();

        return $this->trimNameType($name).$this->type;
    }

    protected function getStub(): string
    {
        return $this->laravel->basePath('stubs/service.stub');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Services';
    }

    protected function trimNameType(string $name): string
    {
        return str($name)->before($this->type);
    }
}
