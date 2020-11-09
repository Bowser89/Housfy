<?php

declare(strict_types=1);

namespace App\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * RepositoryInterface.
 */
interface RepositoryInterface
{
    /**
     * Retrieves all the model records.
     */
    public function all(): Collection;

    /**
     * Retrieves a model by its id.
     */
    public function find(int $id): ?Model;
}
