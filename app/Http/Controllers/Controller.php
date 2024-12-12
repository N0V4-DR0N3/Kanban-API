<?php

namespace App\Http\Controllers;

use App\Data\WrappedData;
use App\Enums\Contracts\Perm;
use App\Models\User;
use App\Reports\Report;
use App\Traits\InjectsReadonly;
use App\Utils\Perms;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\ControllerMiddlewareOptions;
use Illuminate\Support\Carbon;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use InjectsReadonly;
    use ValidatesRequests;

    final public function __construct()
    {
        $this->injectReadonly();
        $this->__setup();
    }

    public function __setup(): void
    {
    }

    /**
     * @template TType
     *
     * @param TType $data
     *
     * @return WrappedData<TType>
     */
    public function wrapped(mixed $data): WrappedData
    {
        return new WrappedData($data);
    }

    /**
     * @param mixed $content
     * @param int $status
     * @param array<string, string> $headers
     *
     * @return Response
     */
    public function pdf(mixed $content, int $status = 200, array $headers = []): Response
    {
        if ($content instanceof Report) {
            $content = $content->browsershot()->pdf();
        }

        return response($content, $status, [
            'Cache-control' => 'no-store',
            'Content-type' => 'application/pdf',
            ...$headers,
        ]);
    }

    protected function withPerms(Perm ...$perms): ControllerMiddlewareOptions
    {
        $permsValues = Perms::values(...$perms);

        return $this->middleware(
            middleware: implode(':', ['permissions', implode('|', $permsValues)]),
        );
    }

    protected function resolveUser(): ?User
    {
        return request()->user();
    }

    protected function resolveUserId(): ?string
    {
        return $this->resolveUser()?->id;
    }

    protected function resolveDate(string $key, ?Carbon $default): ?Carbon
    {
        return request()->date($key) ?? $default;
    }

    /**
     * @param ?Carbon $from
     * @param ?Carbon $to
     * @param string $fromKey
     * @param string $toKey
     *
     * @return array{?Carbon, ?Carbon}
     */
    protected function resolveDateRange(?Carbon $from, ?Carbon $to, string $fromKey = 'from', string $toKey = 'to'): array
    {
        $lower = $this->resolveDate($fromKey, $from);
        $upper = $this->resolveDate($toKey, $to);

        if (($lower && $upper) && $lower->isAfter($upper)) {
            [$lower, $upper] = [$upper, $lower];
        }

        return [$lower, $upper];
    }
}
