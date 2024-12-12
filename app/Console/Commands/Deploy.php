<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

final class Deploy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy
                            {--logs : Should run with verbose logs}

                            {--no-composer : Should not run composer}

                            {--no-migrate : Should not migrate the database}
                            {--migrate-fresh : Should freshly migrate the database}
                            {--no-seed : Should not seed the database}

                            {--passport : Should setup passport}

                            {--no-bun : Should not rebuild JS dependencies}
                            {--no-optimize : Should not optimize for production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploys the application.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if (!$this->option('no-composer')) {
            $this->runComposer();
        }

        $this->createSymlinks();
        $this->clearCache();

        #region [Database]
        if ($this->option('migrate-fresh')) {
            $this->migrateDatabaseFresh();
        }
        else if (!$this->option('no-migrate')) {
            $this->migrateDatabase();
        }

        if (!$this->option('no-seed')) {
            $this->seedDatabase();
        }
        #endregion

        if ($this->option('passport')) {
            $this->issuePassportKeys();
            $this->issuePassportPersonalToken();
        }

        if (!$this->option('no-bun')) {
            $this->buildJs();
        }
        if (!$this->option('no-optimize')) {
            $this->optimizeLaravel();
        }

        return 0;
    }

    protected function runComposer(): void
    {
        $this->notify('[Composer] Installing dependencies');

        $this->shell('composer install --optimize-autoloader');
    }

    protected function createSymlinks(): void
    {
        $this->notify('[Laravel] Creating symlinks');

        $this->shell('php artisan storage:link');
    }

    protected function clearCache(): void
    {
        $this->notify('[Laravel] Updating caches');

        $this->shell('php artisan optimize:clear');
    }

    protected function migrateDatabaseFresh(): void
    {
        $this->notify('[Laravel] Migrating the database freshly');

        $this->shell('php artisan migrate:fresh --force --no-interaction') && $this->issuePassportPersonalToken();
    }

    protected function migrateDatabase(): void
    {
        $this->notify('[Laravel] Migrating the database');

        $this->shell('php artisan migrate --force --no-interaction');
    }

    protected function seedDatabase(): void
    {
        $this->notify('[Laravel] Seeding the database');

        $this->shell('php artisan db:seed --no-interaction');
    }

    protected function issuePassportKeys(): void
    {
        $this->notify('[Laravel] Issuing the Passport oauth keys');

        $this->shell('php artisan passport:keys');
    }

    protected function issuePassportPersonalToken(): void
    {
        $this->notify('[Laravel] Issuing the main Passport personal token');

        $appName = config('app.name');

        $this->shell("php artisan passport:client --personal --name=\"{$appName}\"");
    }

    protected function buildJs(): void
    {
        $this->notify('[Bun] Installing and building dependencies');

        $this->shell('bun install --frozen-lockfile');
        $this->shell('bun run build');
    }

    protected function optimizeLaravel(): void
    {
        $this->notify('[Laravel] Optimizing for production');

        $this->shell('php artisan optimize');
    }

    protected function notify(string $message): void
    {
        $this->components->info($message);
    }

    protected function shell(string $command): string|bool|null
    {
        if ($this->option('logs')) {
            return passthru($command);
        }

        return shell_exec($command);
    }
}
