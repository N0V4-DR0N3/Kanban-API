<?php

namespace App\Reports;

use App\Models\User;
use App\Traits\InjectsReadonly;
use Illuminate\View\View;
use Spatie\Browsershot\Browsershot;
use Stringable;

abstract class Report implements Stringable
{
    use InjectsReadonly;

    // @phpstan-ignore-next-line
    public User $by;

    public function __construct()
    {
        $this->injectReadonly();

        // @phpstan-ignore-next-line
        if (!isset($this->by)) {
            $this->by = User::system();
        }
    }

    public function getName(): string
    {
        return $this->getViewName().' _ '.now()->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     * @phpstan-return view-string
     */
    abstract public function getViewName(): string;

    abstract public function getData(): array;

    protected function browsershotSetup(Browsershot $instance): Browsershot
    {
        return $instance
            ->format('A4')
            ->showBackground()
            ->waitUntilNetworkIdle();
    }

    final public function browsershot(): Browsershot
    {
        $instance = Browsershot::html($this->render())
            ->noSandbox()
            ->newHeadless()
            ->setChromePath(config('browsershot.chrome_path'))
            ->setContentUrl(config('app.internal_url'));

        return $this->browsershotSetup($instance);
    }

    final public function render(): View
    {
        return view($this->getViewName(), $this->getData());
    }

    final public function __toString(): string
    {
        return $this->render()->toHtml();
    }
}
