<?php

namespace App\Providers;

use App\Rules\ValidEmail;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Validation\Rule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bootDB();
        $this->bootHttps();

        $this->bootBlade();
        $this->bootBlueprint();
        $this->bootBuilders();
        $this->bootHelpers();
        $this->bootRequest();
        $this->bootRules();
        $this->bootStr();
    }

    private function bootDB(): void
    {
        Schema::defaultStringLength(181);
    }

    private function bootHttps(): void
    {
        if (App::environment('production')) {
            URL::forceScheme('https');
        }
    }

    private function bootBlade(): void
    {
        Blade::directive('viteLocal', static function ($entrypoints) {
            $asset = Blade::compileString("@vite({$entrypoints})");

            if (Vite::isRunningHot()) {
                return $asset;
            }

            return str_replace(
                ['app(', '); ?>'],
                ['preg_replace(\'/(href|src)="([^"]*)(?=\/build)([^"]*)"/i\', \'$1="$3"\', app(', ')); ?>'],
                $asset,
            );
        });
    }

    private function bootBlueprint(): void
    {
        Blueprint::macro('primaryId', function (string $column = 'id') {
            /**
             * @var Blueprint $this
             */
            return $this->id($column)->primary();
        });

        Blueprint::macro('primaryUuid', function (string $column = 'id') {
            /**
             * @var Blueprint $this
             */
            return $this->uuid($column)->primary();
        });
    }

    private function bootBuilders(): void
    {
        Builder::macro('whereBuilder', function (callable $fn, ...$args) {
            /**
             * @var Builder $this
             */
            return $this->where(withBuilder($fn, ...$args));
        });

        Relation::macro('whereBuilder', function (callable $fn, ...$args) {
            /**
             * @var Builder $this
             * @phpstan-ignore-next-line
             */
            return $this->where(withBuilder($fn, ...$args));
        });

        Builder::macro('whereBuilderNot', function (callable $fn, ...$args) {
            /**
             * @var Builder $this
             */
            return $this->whereNot(withBuilder($fn, ...$args));
        });

        Relation::macro('whereBuilderNot', function (callable $fn, ...$args) {
            /**
             * @var Builder $this
             * @phpstan-ignore-next-line
             */
            return $this->whereNot(withBuilder($fn, ...$args));
        });
    }

    private function bootHelpers(): void
    {
        require_once base_path('bootstrap/helpers.php');
    }

    private function bootRequest(): void
    {
        Request::macro('mergeCloned', function (array $input) {
            /**
             * @var Request $this
             */
            return Request::createFrom($this)->merge($input);
        });
    }

    private function bootRules(): void
    {
        Rule::macro('validEmail', static fn () => new ValidEmail());
    }

    private function bootStr(): void
    {
        Str::macro('firstName', function (string $name) {
            return str($name)->words(1, '')->ucfirst();
        });

        Stringable::macro('firstName', function () {
            /**
             * @var Stringable $this
             */
            return $this->words(1, '')->ucfirst();
        });
    }
}
