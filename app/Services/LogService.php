<?php

namespace App\Services;

use App\Data\Log\InsertData;
use App\Models\Log;
use App\Repositories\LogRepository;
use App\Utils\RequestQueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use InvalidArgumentException;
use Throwable;

/**
 * @final
 *
 * TODO: continue
 */
class LogService extends Service
{
    readonly protected Log $model;
    readonly protected LogRepository $repository;

    /**
     * @return LengthAwarePaginator<Log>
     *
     * @throws InvalidArgumentException
     */
    public function search(Request $request, mixed ...$filters): LengthAwarePaginator
    {
        $request = $request->mergeCloned($filters);
        $query = $this->model::query();

        RequestQueryFilter::make($request, $query)
            ->id()
            ->id('user_id')
            ->enum('domain')
            ->enum('action')
            ->dateRange('created_at', 'created_from', 'created_until')
            ->ordered()
            ->where('search', function (Builder $query, ?string $v) {
                $query
                    ->where('logs.domain', 'LIKE', "%{$v}%")
                    ->orWhere('logs.action', 'LIKE', "%{$v}%")
                    ->orWhere('logs.description', 'LIKE', "%{$v}%")
                    ->orWhereHas('user', function (Builder $query) use ($v) {
                        $query
                            ->where('users.name', 'LIKE', "%{$v}%")
                            ->orWhere('users.email', 'LIKE', "%{$v}%");
                    });
            })->where('domains', function (Builder $query, ?string $v) {
                if (!$v) {
                    return;
                }
                $domains = explode(',', $v);

                $query->whereIn('logs.domain', $domains);
            })->where('excluded_domains', function (Builder $query, ?string $v) {
                $domains = explode(',', $v ?? '');

                $query->whereNotIn('logs.domain', $domains);
            })->apply();

        return $query->latest()->paginate(
            perPage: $request->per_page ?? 20,
        );
    }

    /**
     * @codeCoverageIgnore
     *
     * @throws Throwable
     */
    public function insert(InsertData $data): Log
    {
        return $this->repository->create($data);
    }
}
