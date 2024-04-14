<?php

namespace App\Console\Commands\Base;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:enum')]
class MakeEnumCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:enum';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new enum';

    protected $type = 'Enum';

    protected function getNameInput(): string
    {
        $name = parent::getNameInput();

        return $this->trimNameType($name);
    }

    protected function getStub(): string
    {
        return $this->laravel->basePath('stubs/enum.stub');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Enums';
    }

    protected function trimNameType(string $name): string
    {
        return str($name)->before($this->type);
    }
}
