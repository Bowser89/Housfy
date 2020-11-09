<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Repository\OfficeRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class CacheOffices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The offices collection to persist in cache.
     */
    private Collection $offices;

    /**
     * Creates a new job instance.
     */
    public function __construct(Collection $offices)
    {
        $this->offices = $offices;
    }

    /**
     * Executes the job.
     */
    public function handle(): void
    {
        info('Job Processed: Storing offices in cache.');
        Cache::rememberForever(OfficeRepository::CACHED_OFFICES_KEY, function () {
            return $this->offices;
        });
    }
}
