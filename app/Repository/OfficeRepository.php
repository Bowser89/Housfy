<?php

declare(strict_types=1);

namespace App\Repository;

use App\Jobs\CacheOffices;
use App\Models\Office;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * OfficeRepository.
 *
 * Handles all the Office model persistent layer.
 */
class OfficeRepository implements RepositoryInterface
{
    /**
     * Cache key where the offices are stored.
     */
    const CACHED_OFFICES_KEY = 'offices';

    /**
     * {@inheritDoc}
     */
    public function all(): Collection
    {
        $cachedOffices = Cache::get(self::CACHED_OFFICES_KEY);

        if ($cachedOffices) {
            return $cachedOffices;
        }

        $offices = Office::all();

        // Dispatching cache offices job.
        CacheOffices::dispatch($offices);

        return $offices;
    }

    /**
     * {@inheritDoc}
     */
    public function find(int $officeId): ?Model
    {
       return Office::query()->find($officeId);
    }
}
