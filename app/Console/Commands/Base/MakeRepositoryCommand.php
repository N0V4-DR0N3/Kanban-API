<?php

namespace App\Console\Commands\Base;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:repository')]
class MakeRepositoryCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new eloquent repository class';

    protected $type = 'Repository';

    protected function getNameInput(): string
    {
        $name = parent::getNameInput();

        return $this->trimNameType($name).$this->type;
    }

    protected function getStub(): string
    {
        return $this->laravel->basePath('stubs/repository.stub');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Repositories';
    }

    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the repository already exists'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Create a new model for the repository'],
        ];
    }

    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

        return $this->replaceModel($stub, $this->discoverModel());
    }

    protected function discoverModel(): string
    {
        return $this->option('model') ?: $this->trimNameType($this->getNameInput());
    }

    protected function trimNameType(string $name): string
    {
        return str($name)->before($this->type);
    }

    protected function replaceModel(string $stub, string $model): string
    {
        $modelClass = $this->parseModel($model);

        $replace = [
            '{{ namespacedModel }}' => $modelClass,
            '{{ model }}' => class_basename($modelClass),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
        ];

        return str_replace(array_keys($replace), array_values($replace), $stub);
    }

    protected function parseModel(string $model): string
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new \InvalidArgumentException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
    }

    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output): void
    {
        if ($this->isReservedName($this->getNameInput()) || $this->didReceiveOptions($input)) {
            return;
        }

        $model = $this->components->ask(
            'What model should this repository apply to?',
            'none',
        );

        if ($model && $model !== 'none') {
            $input->setOption('model', $this->trimNameType($model));
        }
    }
}
